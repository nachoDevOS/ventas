<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ItemStock;
use App\Models\ItemStockFraction;
use App\Models\SaleDetail;
use App\Models\SaleTransaction;
use App\Models\Transaction;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->custom_authorize('browse_sales');
        return view('sales.browse');
    }

    public function list()
    {
        $this->custom_authorize('browse_sales');

        $search = request('search') ?? null;
        $paginate = request('paginate') ?? 10;
        // $status = request('status') ?? null;
        // $typeSale = request('typeSale') ?? null;

        $data = Sale::with([
            'person',
            'register',
            'saleDetails' => function ($q) {
                $q->where('deleted_at', null);
            },
            'saleDetails.itemStock.item',
            'saleTransactions' => function ($q) {
                $q->where('deleted_at', null);
            },
        ])
            ->where(function ($query) use ($search) {
                $query
                    ->OrWhereRaw($search ? "id = '$search'" : 1)
                    ->OrWhereRaw($search ? "code like '%$search%'" : 1);
            })
            ->where('deleted_at', null)
            // ->whereRaw($typeSale ? "typeSale = '$typeSale'" : 1)
            // ->whereRaw($status ? "status = '$status'" : 1)
            ->orderBy('id', 'DESC')
            ->paginate($paginate);            

        return view('sales.list', compact('data'));
    }

    public function create()
    {
        // $branches = Branch::where('deleted_at', null)->get();
        $cashier = $this->cashier(null, 'user_id = "' . Auth::user()->id . '"', 'status = "Abierta"');
        $user = Auth::user();
        $this->custom_authorize('add_sales');
        return view('sales.edit-add', compact('cashier'));
    }

    public function edit(Sale $sale)
    {
        $this->custom_authorize('edit_sales');
        $cashier = $this->cashier(null, 'user_id = "' . Auth::user()->id . '"', 'status = "Abierta"');

        $sale = Sale::with([
                'person',
                'register',
                'saleTransactions',
                'saleDetails' => function ($q) {
                    $q->where('deleted_at', null);
                },
                'saleDetails.itemStock'=>function($q){
                    $q->where('deleted_at', null);
                },
                'saleDetails.itemStock.item.category',
                'saleDetails.itemStock.item.presentation',
                'saleDetails.itemStock.item.laboratory',
                'saleDetails.itemStock.item.brand',
                'saleDetails.itemStock.itemStockFractions'=>function($q){
                    $q->where('deleted_at', null);
                },
                'saleDetails.itemStockFraction'=>function($q){
                    $q->where('deleted_at', null);
                },

                
            ])
            ->where('id', $sale->id)
            ->first();

        // return $sale;
        return view('sales.edit-add', compact('sale', 'cashier'));
    }
    
    public function generarNumeroFactura($typeSale)
    {
        $prefix = $typeSale != 'Proforma' ? 'VTA-' : 'PRO-';
        $fecha = now()->format('Ymd');
        $count = Sale::withTrashed()
            // ->where('typeSale', $typeSale)
            ->whereRaw($typeSale != 'Proforma' ? 'typeSale != "Proforma"' : 'typeSale = "Proforma"')
            ->whereDate('created_at', today())
            ->count();

        return $prefix . $fecha . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $this->custom_authorize('add_sales');
        if(!$request->products)
        {
            return redirect()
                ->route('sales.create')
                ->with(['message' => 'No se encontraron productos.', 'alert-type' => 'error']);
        }

        $amountItems =0;
        if ($request->products) {
            $val = $this->stockValidate($request);
            if($val){
                return $val;
            }
            foreach ($request->products as $key => $value){
                if($value['quantity_unit'] > 0 && $value['price_unit'] > 0){
                    $amountItems = $amountItems + ($value['quantity_unit'] * $value['price_unit']);
                }
                if(isset($value['quantity_fraction']) && isset($value['price_fraction']))
                {
                    if($value['quantity_fraction'] > 0 && $value['price_fraction'] > 0){
                        $amountItems = $amountItems + ($value['quantity_fraction'] * $value['price_fraction']);
                    }
                }
            }
        } 
        
        $amountTotal = $amountItems;
        $amount_cash = $request->amount_cash ? $request->amount_cash : 0;
        $amount_qr = $request->amount_qr ? $request->amount_qr : 0;

        if(($amount_cash + $amount_qr) < $amountTotal)
        {
            return redirect()
                ->route('sales.create')
                ->with(['message' => 'Monto Incorrecto.', 'alert-type' => 'error']);
        }

        $cashier = $this->cashier(null,'user_id = "'.Auth::user()->id.'"', 'status = "Abierta"');
        if (!$cashier) {
            return redirect()
                ->route('sales.index')
                ->with(['message' => 'Usted no cuenta con caja abierta.', 'alert-type' => 'warning']);
        }

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'status' => 'Completado',
            ]);
            $sale = Sale::create([
                'person_id' => $request->person_id,
                'cashier_id' => $cashier->id,

                'code' => $this->generarNumeroFactura($request->typeSale),
                'typeSale' => $request->typeSale,
                'amountReceived' => $request->amountReceived,
                'amountChange' => $request->payment_type == 'Efectivo'? $request->amountReceived-$amountTotal : 0,
                'amount' => $amountTotal ?? 0,
                'observation' => $request->observation,
                'dateSale' => Carbon::now(),
                'status' => $request->typeSale == 'Venta al Contado' ? 'Pagado' : (($amount_cash+$amount_qr) >= $amountTotal?'Pagado':'Pendiente'),
            ]);

            if ($request->payment_type == 'Efectivo' || $request->payment_type == 'Efectivo y Qr') {
                SaleTransaction::create([
                    'sale_id' => $sale->id,
                    'transaction_id' => $transaction->id,
                    'amount' => $amountTotal - $amount_qr,
                    'paymentType' => 'Efectivo',
                ]);
            }
            if ($request->payment_type == 'Qr' || $request->payment_type == 'Efectivo y Qr') {
                SaleTransaction::create([
                    'sale_id' => $sale->id,
                    'transaction_id' => $transaction->id,
                    'amount' => $amount_qr,
                    'paymentType' => 'Qr',
                ]);
            }

            // =================================================================================
            // 2. Bucle de creación de detalles y descuento de stock
            // =================================================================================
            foreach ($request->products as $key => $value) {
                $quantity_unit = $value['quantity_unit'] ?? 0;
                $quantity_fraction = $value['quantity_fraction'] ?? 0;

                // Si no se vende nada de este producto, saltar
                if ($quantity_unit == 0 && $quantity_fraction == 0) {
                    continue;
                }

                $itemStock = ItemStock::with(['item.presentation', 'itemStockFractions'])
                        ->where('id', $value['id'])
                        ->first();                        

                // Lógica para venta de unidades enteras
                if ($quantity_unit > 0) {
                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'itemStock_id' => $itemStock->id,
                        'dispensed' => 'Entero',
                        'presentation_id' => $itemStock->item->presentation_id,
                        'pricePurchase' => $itemStock->pricePurchase,
                        'price' => $value['price_unit'],
                        'quantity' => $quantity_unit,
                        'amount' => $value['price_unit'] * $quantity_unit,
                    ]);

                    $itemStock->decrement('stock', $quantity_unit);
                }

                // return $request;

                // Lógica para venta de fracciones
                if ($quantity_fraction > 0) {
                    // Obtener el total de fracciones vendidas ANTES de la venta actual
                    $fractions_sold_before = $itemStock->itemStockFractions()->where('deleted_at', null)->sum('quantity');
                    // return $fractions_sold_before;
                        
                    // Calcular las unidades "abiertas" después de esta venta
                    $fractions_sold_after = $fractions_sold_before + $quantity_fraction;
                    // return $fractions_sold_after;
                    // $opened_units_after = $fractions_sold_after / $itemStock->dispensedQuantity;                        
                    $opened_units_after = $fractions_sold_after / ($itemStock->dispensedQuantity ?: 1);
                        
                    if($opened_units_after > $itemStock->stock){
                        DB::rollBack();
                        return  redirect()->route('sales.index')->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error'])->withInput();
                    }
    
                    // Si la venta de esta fracción provocó que se completara y "abriera" una nueva unidad, descontamos del stock principal
                    if ($itemStock->stock == $opened_units_after) {
                        $itemStock->decrement('stock', $opened_units_after);
                    }


                    $itemStockFraction = ItemStockFraction::create([
                        'itemStock_id' => $itemStock->id,
                        'quantity' => $quantity_fraction,
                        'price' => $value['price_fraction'],
                        'amount' => $value['price_fraction'] * $quantity_fraction,
                    ]);
                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'itemStock_id' => $itemStock->id,
                        'itemStockFraction_id' => $itemStockFraction->id,
                        'dispensed' => 'Fraccionado',
                        'presentation_id' => $itemStock->item->fractionPresentation_id,
                        'pricePurchase' => $itemStock->pricePurchase,
                        'price' => $value['price_fraction'],
                        'quantity' => $quantity_fraction,
                        'amount' => $value['price_fraction'] * $quantity_fraction,
                    ]);
                }
            }

            // =================================================================================

            DB::commit();
            return redirect()
                ->route('sales.index')
                ->with(['message' => 'Registrado exitosamente.', 'alert-type' => 'success']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()
                ->route('sales.index')
                ->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error']);
        }
    }

    public function update(Request $request, Sale $sale)
    {
        $this->custom_authorize('edit_sales');
        if(!$request->products)
        {
            return redirect()
                ->route('sales.index')
                ->with(['message' => 'No se encontraron productos.', 'alert-type' => 'error']);
        }

        $amountItems =0;
        if ($request->products) {
            $sale->load('saleDetails');
            $sale->setRelation('dispensions', $sale->saleDetails);
            $val = $this->stockValidate($request, $sale);
            if($val){
                return $val;
            }
            foreach ($request->products as $key => $value){
                if($value['quantity_unit'] > 0 && $value['price_unit'] > 0){
                    $amountItems = $amountItems + ($value['quantity_unit'] * $value['price_unit']);
                }
                if(isset($value['quantity_fraction']) && isset($value['price_fraction']))
                {
                    if($value['quantity_fraction'] > 0 && $value['price_fraction'] > 0){
                        $amountItems = $amountItems + ($value['quantity_fraction'] * $value['price_fraction']);
                    }
                }
            }
        } 
        $amountTotal = $amountItems;

        $amount_cash = $request->amount_cash ? $request->amount_cash : 0;
        $amount_qr = $request->amount_qr ? $request->amount_qr : 0;

        if (($amount_cash + $amount_qr) < $amountTotal) {
            return redirect()
                ->route('sales.edit', ['sale' => $sale->id])
                ->with(['message' => 'Monto Incorrecto.', 'alert-type' => 'error']);
        }

        $cashier = $this->cashier(null,'user_id = "'.Auth::user()->id.'"', 'status = "Abierta"');
        if (!$cashier) {
            return redirect()
                ->route('sales.index')
                ->with(['message' => 'Usted no cuenta con caja abierta.', 'alert-type' => 'warning']);
        }
        if($cashier->id != $sale->cashier_id){
            return redirect()
                ->route('sales.index')
                ->with(['message' => 'No puede modificar ventas de otra caja.', 'alert-type' => 'warning']);
        }
       
        DB::beginTransaction();
        try {
            // Eliminar transacciones de pago antiguas
            foreach ($sale->saleTransactions as $saleTransaction) {
                if ($saleTransaction->transaction) {
                    $saleTransaction->transaction->delete();
                }
                $saleTransaction->delete();
            }

            $sale = Sale::with(['saleTransactions', 'saleDetails'])
                ->where('id', $sale->id)
                ->first();


            // 1. Restaurar stock y eliminar detalles existentes
            // Es más seguro eliminar y recrear los detalles para manejar correctamente 
            // los cambios entre unidades y fracciones.
            $this->destroyDispensation($sale->saleDetails);


            $sale->update([
                'person_id' => $request->person_id,
                'amountReceived' => $request->amountReceived,
                'amountChange' => $request->payment_type == 'Efectivo' ? $request->amountReceived - $amountTotal : 0, // Ajustar si es necesario

                'amount' => $amountTotal,
                'observation' => $request->observation,
                'status' => $request->typeSale == 'Venta al Contado' ? 'Pagado' : (($amount_cash + $amount_qr) >= $amountTotal ? 'Pagado' : 'Pendiente'),
            ]);

            // 2. Crear nuevos detalles (Lógica idéntica a store)
            foreach ($request->products as $key => $value) {
                $quantity_unit = $value['quantity_unit'] ?? 0;

                $quantity_fraction = $value['quantity_fraction'] ?? 0;

                if ($quantity_unit == 0 && $quantity_fraction == 0) {
                    continue;
                }

                $itemStock = ItemStock::with(['item.presentation', 'itemStockFractions'])
                        ->where('id', $value['id'])
                        ->first();

                // Venta de Unidades
                if ($quantity_unit > 0) {
                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'itemStock_id' => $itemStock->id,
                        'dispensed' => 'Entero',
                        'presentation_id' => $itemStock->item->presentation_id,
                        'pricePurchase' => $itemStock->pricePurchase,
                        'price' => $value['price_unit'],
                        'quantity' => $quantity_unit,
                        'amount' => $value['price_unit'] * $quantity_unit,
                    ]);

                    $itemStock->decrement('stock', $quantity_unit);
                }


                // Venta de Fracciones
                if ($quantity_fraction > 0) {
                    // Lógica para descontar stock si las fracciones completan una unidad
                    $fractions_sold_before = $itemStock->itemStockFractions()->where('deleted_at', null)->sum('quantity');
                    // return $fractions_sold_before;
           
                    $fractions_sold_after = $fractions_sold_before + $quantity_fraction;
                    // return $fractions_sold_after;
                        
                    $opened_units_after = $fractions_sold_after / ($itemStock->dispensedQuantity ?: 1);
                    // return $opened_units_after;
                        
                    // return $itemStock->stock;
                    if($opened_units_after > $itemStock->stock){
                        DB::rollBack();
                        return  redirect()
                                ->route('sales.index')
                                ->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error']);
                    }
                    if ($itemStock->stock == $opened_units_after) {
                        $itemStock->decrement('stock', $opened_units_after);
                    }
                    $itemStockFraction = ItemStockFraction::create([
                        'itemStock_id' => $itemStock->id,
                        'quantity' => $quantity_fraction,
                        'price' => $value['price_fraction'],
                        'amount' => $value['price_fraction'] * $quantity_fraction,
                    ]);

                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'itemStock_id' => $itemStock->id,
                        'itemStockFraction_id' => $itemStockFraction->id,
                        'dispensed' => 'Fraccionado',
                        'presentation_id' => $itemStock->item->fractionPresentation_id,
                        'pricePurchase' => $itemStock->pricePurchase,
                        'price' => $value['price_fraction'],
                        'quantity' => $quantity_fraction,
                        'amount' => $value['price_fraction'] * $quantity_fraction,
                    ]);
                }

                
            }

            // Crear nuevas transacciones de pago
            $transaction = Transaction::create([
                'status' => 'Completado',
            ]);

            if ($request->payment_type == 'Efectivo' || $request->payment_type == 'Efectivo y Qr') {
                SaleTransaction::create([
                    'sale_id' => $sale->id,
                    'transaction_id' => $transaction->id,
                    'amount' => $amountTotal - $amount_qr,
                    'paymentType' => 'Efectivo',
                ]);
            }
            if ($request->payment_type == 'Qr' || $request->payment_type == 'Efectivo y Qr') {
                SaleTransaction::create([
                    'sale_id' => $sale->id,
                    'transaction_id' => $transaction->id,
                    'amount' => $amount_qr,
                    'paymentType' => 'Qr',
                ]);
            }
            DB::commit();
            return redirect()->route('sales.index')->with(['message' => 'Venta actualizada exitosamente.', 'alert-type' => 'success']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('sales.edit', ['sale' => $sale->id])->with(['message' => 'Ocurrió un error al actualizar: ' . $e->getMessage(), 'alert-type' => 'error']);
        }
    }

    public function prinf($id)
    {
        $sale = Sale::with([
            'person',
            'register',
            'branch',
            'saleDetails' => function ($q) {
                $q->where('deleted_at', null);
            },
        ])
            ->where('id', $id)
            ->where('deleted_at', null)
            ->first();
        if ($sale->typeSale != 'Proforma') {
            $transaction = SaleTransaction::with(['transaction'])
                ->where('sale_id', $sale->id)
                ->first();
            return view('sales.prinfSale', compact('sale', 'transaction'));
        } else {
            return view('sales.prinfProforma', compact('sale'));
        }
    }

    public function destroy($id)
    {
        $sale = Sale::with([
                'saleDetails' => function ($q) {
                    $q->where('deleted_at', null);
                },
                'saleDetails.itemStockFraction' => function ($q) {
                    $q->where('deleted_at', null);
                }
            ])
            ->where('id', $id)
            ->where('deleted_at', null)
            ->first();

        $cashier = $this->cashier(null,'user_id = "'.Auth::user()->id.'"', 'status = "Abierta"');
        if (!$cashier) {
            return redirect()
                ->route('sales.index')
                ->with(['message' => 'Usted no cuenta con caja abierta.', 'alert-type' => 'warning']);
        }
        if($cashier->id != $sale->cashier_id){
            return redirect()
                ->route('sales.index')
                ->with(['message' => 'No puede eliminar ventas de otra caja.', 'alert-type' => 'warning']);
        }

        DB::beginTransaction();
        try {

            foreach ($sale->saleDetails as $detail) {
                $itemStock = ItemStock::findOrFail($detail->itemStock_id);

                if ($detail->dispensed == 'Entero') {
                    $itemStock->increment('stock', $detail->quantity);
                    $detail->delete();
                } 
                elseif ($detail->dispensed == 'Fraccionado' && $detail->itemStockFraction) {
                        // Si es fracción, eliminamos el registro de fracción (soft delete)
                    $detail->itemStockFraction->delete();

                    $itemStockUnit = SaleDetail::with(['sale'])
                        ->whereHas('sale', function ($q) {
                            $q->where('deleted_at', null);
                        })
                        ->where('itemStock_id',$detail->itemStock_id)
                        ->where('dispensed','Entero')
                        ->where('deleted_at', null)
                        ->get()->sum('quantity');

                    $dispensationAnamnesisForm = Dispensation::with(['anamnesisForm'])
                        ->whereHas('anamnesisForm', function ($q) {
                            $q->where('deleted_at', null);
                        })
                        ->where('itemStock_id',$detail->itemStock_id)
                        ->where('dispensed','Entero')
                        ->where('deleted_at', null)
                        ->get()
                        ->sum('quantity');

                    $dispensationVaccinationRecord = Dispensation::with(['vaccinationRecord'])
                        ->whereHas('vaccinationRecord', function ($q) {
                            $q->where('deleted_at', null);
                        })
                        ->where('itemStock_id',$detail->itemStock_id)
                        ->where('dispensed','Entero')
                        ->where('deleted_at', null)
                        ->get()
                        ->sum('quantity');

                    $dispensationDeworming = Dispensation::with(['deworming'])
                        ->whereHas('deworming', function ($q) {
                            $q->where('deleted_at', null);
                        })
                        ->where('itemStock_id',$detail->itemStock_id)
                        ->where('dispensed','Entero')
                        ->where('deleted_at', null)
                        ->get()
                        ->sum('quantity');

                    $itemStock->update([
                        'stock' => $itemStock->quantity
                    ]);
                    $itemStock->decrement('stock', ($itemStockUnit+$dispensationAnamnesisForm+$dispensationVaccinationRecord+$dispensationDeworming));
                    $detail->delete();   
                }
            }


            $sale->delete();
            DB::commit();
            return redirect()->back()->with(['message' => 'Eliminado exitosamente.', 'alert-type' => 'success'])->withInput();
            
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error'])->withInput();

        }
    }


    public function show($id)
    {
        $sale = Sale::with([
            'person',
            'register',
            'saleTransactions',
            'saleDetails' => function ($q) {
                $q->where('deleted_at', null);
            },
            'saleDetails.itemStock.item' => function ($q) {
                $q->withTrashed();
            },
        ])
            ->where('id', $id)
            ->withTrashed()
            ->first();

        return view('sales.read', compact('sale'));
    }
}
