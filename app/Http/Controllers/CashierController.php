<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cashier;
use App\Models\CashierDetail;
use App\Models\CashierDetailCash;
use App\Models\CashierMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->custom_authorize('browse_cashiers');

        return view('cashiers.browse');
    }

    public function list()
    {
        $this->custom_authorize('browse_cashiers');

        $paginate = request('paginate') ?? 10;
        $search = request('search') ?? null;
        $status = request('status') ?? null;
        $cashier =  Cashier::where(function ($query) use ($search) {
                        if ($search) {
                            $query->whereHas('user', function ($query) use ($search) {
                                $query->whereRaw("name like '%$search%'");
                            })
                            ->OrWhereRaw($search ? "status like '%$search%'" : 1)
                            ->OrWhereRaw($search ? "sale like '%$search%'" : 1)
                            ->OrWhereRaw($search ? "title like '%$search%'" : 1);
                        }
                    })
                    ->where('deleted_at', null)
                    ->orderBy('id', 'DESC')
                    ->paginate($paginate);
            
        return view('cashiers.list', compact('cashier'));
    }

    public function create()
    {
        $this->custom_authorize('add_cashiers');
        $cashiers = User::where('role_id', '!=', 1)->where('status', 1)->get();
        return view('cashiers.edit-add', compact('cashiers'));
    }

    public function store(Request $request)
    {
        $this->custom_authorize('add_cashiers');
        $cashier = $this->cashier(null,'user_id = "'.$request->user_id.'"', '(status = "Abierta" or status = "Apertura Pendiente" or status = "Cierre Pendiente")');

        if ($cashier) {
            return redirect()->back()->withInput()->with(['message' => 'La persona seleccionada cuenta con una caja activa...', 'alert-type' => 'error']);
        }

        DB::beginTransaction();
        try {
            $cashier = Cashier::create([
                'vault_id' => $request->vault_id,
                'user_id' => $request->user_id,
                'title' => $request->title,
                'amountOpening' => $request->amount,
                'observation' => $request->observation,
                'status' => 'Apertura Pendiente',
            ]);

            $cashierMovement = CashierMovement::create([
                'cashier_id' => $cashier->id,
                'amount' => $request->amount ? $request->amount : 0,
                'observation' => 'Monto de apertura de caja.',
                'type' => 'Ingreso',
                'status' => 'Aceptado',
            ]);
            $detail = CashierDetail::create([
                'cashierMovement_id' => $cashierMovement->id,
                'cashier_id' => $cashier->id,
                'type' => 'Apertura',
            ]);

            for ($i=0; $i < count($request->cash_value); $i++) { 
                CashierDetailCash::create([
                    'cashierDetail_id' => $detail->id,
                    'cash_value' => $request->cash_value[$i],
                    'quantity' => $request->quantity[$i],
                ]);
            }
         
            DB::commit();
            return redirect()->route('cashiers.index')->with(['message' => 'Registrado exitosamente.', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('cashiers.index')->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error']);
        }
    }

    public function print_open($id){
        
        $cashier = Cashier::with(['user',
            'movements' => function($q){
                $q->where('deleted_at', NULL)
                ->with(['details.detailCashes']);
            }])
            ->where('id', $id)
        ->first();

      
        return view('cashiers.print-open', compact('cashier'));
    }

    public function show($id)
    {
        $cashier = Cashier::with([
                'user',
                'movements' => function ($q) {
                    $q->where('deleted_at', null)->with(['user']);
                },
                'sales' => function ($q) {
                    $q->with([
                        'person',
                        'register',
                        'saleTransactions' => function ($q) {
                            $q->where('deleted_at', null);
                        },
                        'saleDetails' => function ($q) {
                            $q->with(['itemStock.item']);
                        },
                    ]);
                },
                'expenses' => function ($q) {
                    $q->with(['expenseTransactions', 'register']);
                },
            ])
            ->where('id', $id)
            ->where('deleted_at', null)
            ->first();

        return view('cashiers.read', compact('cashier'));
    }

    //*** Para que los cajeros Acepte o rechase el dinero dado por Boveda o gerente
    public function change_status($id, Request $request){
        DB::beginTransaction();
        
        try {
            if($request->status == 'Abierta'){
                $message = 'Caja aceptada exitosamente.';
                Cashier::where('id', $id)->update([
                    'status' => $request->status,
                    'view' => Carbon::now()
                ]);
            }else{
                $message = 'Caja rechazada exitosamente.';
                $cashier = Cashier::with(['movements' => function($q){
                            $q->where('deleted_at', NULL);
                        },
                        'details' => function($q){
                            $q->where('deleted_at', NULL);
                        },
                        'details.detailCashes' => function($q){
                            $q->where('deleted_at', NULL);
                        }
                    ])
                ->where('id', $id)->first();

                foreach ($cashier->movements as $value) {
                    $value->update([
                        'deleted_at' => Carbon::now(),
                        'status' => 'Rechazado'
                    ]);
                }

                foreach ($cashier->details as $value) {
                    $value->update([
                        'deleted_at' => Carbon::now(),
                    ]);
                    foreach ($value->detailCashes as $item) {
                        $item->update([
                            'deleted_at' => Carbon::now()
                        ]);
                    }
                }               
                $cashier->update([
                    'status' => 'Rechazado',
                    'view' => Carbon::now(),
                    'deleted_at' => Carbon::now()
                ]);
            }

            DB::commit();
            return redirect()->route('voyager.dashboard')->with(['message' => $message, 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            // $this->logError($th, $request);
            return redirect()->route('voyager.dashboard')->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error']);
        }
    }

    //***para cerrar la caja el cajero vista 
    public function close($id)
    {
        $cashier = $this->cashier('id = "'.$id.'"', null, 'status = "Abierta"');        

        if (!$cashier) {
            return redirect()->route('voyager.dashboard')->with(['message' => 'La caja no se encuentra abierta.', 'alert-type' => 'warning']);
        }
        if (count($cashier->movements->where('deleted_at', null)->where('status', 'Pendiente'))>0) {
            return redirect()->route('voyager.dashboard')->with(['message' => 'La caja no puede ser cerrada, tiene transacciones pendiente.', 'alert-type' => 'warning']);
        }   
        return view('cashiers.close', compact('cashier'));
    }

    public function close_store(Request $request, $id){
        DB::beginTransaction();
        $cashier = Cashier::findOrFail($id);
        if($cashier->status != 'Abierta'){
            return redirect()->route('voyager.dashboard')->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error']);
        }
        try {
            $cashier->closed_at = Carbon::now();
            $cashier->status = 'Cierre Pendiente';
            $cashier->amountExpectedClosing = $request->amount_cashier;

            $cashier->amountClosed = $request->amount_real;

            $cashier->amountLeftover = $request->amount_cashier < $request->amount_real ? $request->amount_real - $request->amount_cashier : 0;
            $cashier->amountMissing = $request->amount_cashier > $request->amount_real ? $request->amount_cashier - $request->amount_real : 0;
            $cashier->save();

            $detail = CashierDetail::create([
                'cashier_id' => $cashier->id,
                'type' => 'Cierre',
            ]);

            for ($i = 0; $i < count($request->cash_value); $i++) {
                CashierDetailCash::create([
                    'cashierDetail_id' => $detail->id,
                    'cash_value' => $request->cash_value[$i],
                    'quantity' => $request->quantity[$i],
                ]);
            }     

            DB::commit();
            return redirect()->route('voyager.dashboard')->with(['message' => 'Caja cerrada exitosamente.', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            $this->logError($th, $request);
            return redirect()->route('voyager.dashboard')->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error']);
        }
    }
    public function close_revert(Request $request, $id)
    {
        $cashier = Cashier::with(['details'=>function($q){
            $q->where('deleted_at', NULL)
            ->where('type', 'Cierre')
            ->with(['detailCashes']);
        }])
        ->where('id', $id)
        ->first();

        if ($cashier->status != 'Cierre Pendiente') {
            return redirect()->route('voyager.dashboard')->with(['message' => 'La caja no se encuentra en cierre pendiente.', 'alert-type' => 'error']);
        }
        DB::beginTransaction();
        try {

            $cashier->closed_at = NULL;
            $cashier->status = 'Abierta';
            $cashier->amountExpectedClosing = null;
            $cashier->amountClosed = null;
            $cashier->amountMissing = null;
            $cashier->amountLeftover = null;
            $cashier->save();

            $cashier->details[0]->update([
                'deleted_at' => Carbon::now()
            ]);

            foreach ($cashier->details[0]->detailCashes as $item) {
                $item->update([
                    'deleted_at' => Carbon::now()
                ]);
            }
            

            DB::commit();
            return redirect()->route('voyager.dashboard')->with(['message' => 'Caja reabierta exitosamente.', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            $this->logError($th, $request);
            return redirect()->route('voyager.dashboard')->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error']);
        }
    }

    public function print($id){

        $cashier = $this->cashier('id = "'.$id.'"', null, null);        
        return view('cashiers.print-close-details', compact('cashier',));
    }

    public function confirm_close($id)
    {
        $cashier = $this->cashier('id = "'.$id.'"', null, null);    
        if($cashier->status == 'Cierre Pendiente'){
            return view('cashiers.confirm_close', compact('cashier'));
        }else{
            return redirect()->route('cashiers.index')->with(['message' => 'La caja ya no está abierta.', 'alert-type' => 'warning']);
        }
    }

    public function confirm_close_store(Request $request, $id)
    {
        // return $id;
        $cashier = Cashier::findOrFail($id);
        if($cashier->status != 'Cierre Pendiente'){
            return redirect()->route('cashiers.index')->with(['message' => 'La caja ya no está abierta.', 'alert-type' => 'warning']);
        }


        DB::beginTransaction();
        try {
            $cashier->status = 'Cerrada';
            $cashier->closeUser_id= Auth::user()->id;
            $cashier->save();
            

            DB::commit();
            return redirect()->route('cashiers.index')->with(['message' => 'Caja cerrada exitosamente.', 'alert-type' => 'success', 'id_cashier_close' => $id]);
        } catch (\Throwable $th) {
            DB::rollback();
            $this->logError($th, $request);
            return redirect()->route('cashiers.index')->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error']);
        }
    }

    public function print_close($id){
        $cashier = $this->cashier('id = "'.$id.'"', null, null);   
        return view('cashiers.print-close', compact('cashier'));
    }
}
