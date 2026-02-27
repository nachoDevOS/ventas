<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cashier;
use App\Models\CashierDetail;
use App\Models\CashierDetailCash;
use App\Models\CashierMovement;
use App\Models\User;
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
            // return redirect()
            //     ->route('cashiers.index')
            //     ->with(['message' => 'El usuario seleccionado tiene una caja que no ha sido cerrada.', 'alert-type' => 'warning']);
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
            // $this->logError($th, $request);
            return redirect()->route('cashiers.index')->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error']);
        }
    }
}
