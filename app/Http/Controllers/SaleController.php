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
use Illuminate\Support\Carbon;

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
        $search   = request('search')   ?? null;
        $paginate = request('paginate') ?? 10;
        $typeSale = in_array(request('typeSale'), ['Venta al Contado', 'Venta al Credito', 'Proforma'])
                    ? request('typeSale') : null;

        $data = Sale::with([
            'person',
            'register',
            'saleDetails' => function ($q) {
                $q->where('deleted_at', null);
            },
            'saleDetails.itemStock.item.presentation',
            'saleDetails.itemStock.item.fractionPresentation',
            'saleTransactions' => function ($q) {
                $q->where('deleted_at', null);
            },
        ])
            ->where(function ($query) use ($search) {
                $query
                    ->OrWhereRaw($search ? "id = '$search'" : 1)
                    ->OrWhereRaw($search ? "code like '%$search%'" : 1);
            })
            ->when($typeSale, function ($query) use ($typeSale) {
                $query->where('typeSale', $typeSale);
            })
            ->where('deleted_at', null)
            ->orderBy('id', 'DESC')
            ->paginate($paginate);

        return view('sales.list', compact('data'));
    }

    public function create()
    {
        $this->custom_authorize('add_sales');
        return view('sales.edit-add');
    }

    public function store(Request $request)
    {
        $this->custom_authorize('add_sales');
        $isProforma = $request->typeSale === 'Proforma';

        if(!$request->products)
        {
            return redirect()
                ->route('sales.create')
                ->with(['message' => 'No se encontraron productos.', 'alert-type' => 'error']);
        }

        $amountItems =0;
        if ($request->products) {
            if (!$isProforma) {
                $val = $this->stockValidate($request);
                if($val){
                    return $val;
                }
            }
            foreach ($request->products as $key => $value){
                if($value['quantity_unit'] > 0 && $value['price_unit'] > 0){
                    $discount_unit = isset($value['discount_unit']) ? floatval($value['discount_unit']) : 0;
                    $bruto_unit = $value['quantity_unit'] * $value['price_unit'];
                    if ($bruto_unit > 0 && $discount_unit >= $bruto_unit) {
                        $discount_unit = max(0, round($bruto_unit - 0.01, 2));
                    }
                    $amountItems = $amountItems + ($bruto_unit - $discount_unit);
                }
                if(isset($value['quantity_fraction']) && isset($value['price_fraction']))
                {
                    if($value['quantity_fraction'] > 0 && $value['price_fraction'] > 0){
                        $discount_fraction = isset($value['discount_fraction']) ? floatval($value['discount_fraction']) : 0;
                        $bruto_fraction = $value['quantity_fraction'] * $value['price_fraction'];
                        if ($bruto_fraction > 0 && $discount_fraction >= $bruto_fraction) {
                            $discount_fraction = max(0, round($bruto_fraction - 0.01, 2));
                        }
                        $amountItems = $amountItems + ($bruto_fraction - $discount_fraction);
                    }
                }
            }
        }

        $general_discount = floatval($request->general_discount ?? 0);
        if ($amountItems > 0 && $general_discount >= $amountItems) {
            $general_discount = max(0, round($amountItems - 0.01, 2));
        }
        $amountTotal = $amountItems - $general_discount;
        $amount_cash = $request->amount_cash ? $request->amount_cash : 0;
        $amount_qr = $request->amount_qr ? $request->amount_qr : 0;

        if(!$isProforma && ($amount_cash + $amount_qr) < $amountTotal)
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
            $sale = Sale::create([
                'person_id' => $request->person_id,
                'cashier_id' => $cashier->id,
                'invoiceNumber' => $this->generarNumeroFactura($request->typeSale),
                'typeSale' => $request->typeSale,
                'amountReceived' => $isProforma ? 0 : $request->amountReceived,
                'amountChange' => (!$isProforma && $request->payment_type == 'Efectivo') ? $request->amountReceived-$amountTotal : 0,
                'amount' => $amountTotal ?? 0,
                'general_discount' => $general_discount,
                'observation' => $request->observation,
                'dateSale' => Carbon::now(),
                'status' => $isProforma ? 'Pendiente' : ($request->typeSale == 'Venta al Contado' ? 'Pagado' : (($amount_cash+$amount_qr) >= $amountTotal?'Pagado':'Pendiente')),
            ]);

            if (!$isProforma) {
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
                    $discount_unit = isset($value['discount_unit']) ? floatval($value['discount_unit']) : 0;
                    $bruto_unit = $value['price_unit'] * $quantity_unit;
                    if ($bruto_unit > 0 && $discount_unit >= $bruto_unit) {
                        $discount_unit = max(0, round($bruto_unit - 0.01, 2));
                    }
                    $amount_unit = $bruto_unit - $discount_unit;
                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'itemStock_id' => $itemStock->id,
                        'dispensed' => 'Entero',
                        'presentation_id' => $itemStock->item->presentation_id,
                        'pricePurchase' => $itemStock->pricePurchase,
                        'price' => $value['price_unit'],
                        'quantity' => $quantity_unit,
                        'discount' => $discount_unit,
                        'amount' => $amount_unit,
                    ]);

                    if (!$isProforma) {
                        $itemStock->decrement('stock', $quantity_unit);
                        $this->autoZeroStock($itemStock, $itemStock->dispensedQuantity);
                    }
                }

                // Lógica para venta de fracciones
                if ($quantity_fraction > 0) {
                    $discount_fraction = isset($value['discount_fraction']) ? floatval($value['discount_fraction']) : 0;
                    $bruto_fraction = $value['price_fraction'] * $quantity_fraction;
                    if ($bruto_fraction > 0 && $discount_fraction >= $bruto_fraction) {
                        $discount_fraction = max(0, round($bruto_fraction - 0.01, 2));
                    }
                    $amount_fraction = $bruto_fraction - $discount_fraction;

                    if ($isProforma) {
                        // Proforma: solo registrar el detalle, sin tocar stock ni crear fracción
                        SaleDetail::create([
                            'sale_id' => $sale->id,
                            'itemStock_id' => $itemStock->id,
                            'dispensed' => 'Fraccionado',
                            'presentation_id' => $itemStock->item->fractionPresentation_id,
                            'pricePurchase' => $itemStock->pricePurchase,
                            'price' => $value['price_fraction'],
                            'quantity' => $quantity_fraction,
                            'discount' => $discount_fraction,
                            'amount' => $amount_fraction,
                        ]);
                    } else {
                        $fractions_sold_before = $itemStock->itemStockFractions()->where('deleted_at', null)->sum('quantity');
                        $fractions_sold_after  = $fractions_sold_before + $quantity_fraction;
                        $opened_units_after    = $fractions_sold_after / ($itemStock->dispensedQuantity ?: 1);

                        if ($opened_units_after > $itemStock->stock) {
                            DB::rollBack();
                            return redirect()->route('sales.index')->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error'])->withInput();
                        }

                        $itemStockFraction = ItemStockFraction::create([
                            'itemStock_id' => $itemStock->id,
                            'quantity' => $quantity_fraction,
                            'price' => $value['price_fraction'],
                            'amount' => $amount_fraction,
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
                            'discount' => $discount_fraction,
                            'amount' => $amount_fraction,
                        ]);
                        $this->autoZeroStock($itemStock, $itemStock->dispensedQuantity);
                    }
                }
            }

            // =================================================================================

            DB::commit();
            return redirect()
                ->route('sales.index')
                ->with(['message' => 'Registrado exitosamente.', 'alert-type' => 'success']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return 0;
            return redirect()
                ->route('sales.index')
                ->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error']);
        }
    }

    public function edit(Sale $sale)
    {
        $this->custom_authorize('edit_sales');
        // $cashier = $this->cashier(null, 'user_id = "' . Auth::user()->id . '"', 'status = "Abierta"');
        $cashier=null;

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
                'saleDetails.itemStock.item.line',
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

    

    public function update(Request $request, Sale $sale)
    {
        $this->custom_authorize('edit_sales');
        $isProforma = $sale->typeSale === 'Proforma';

        if(!$request->products)
        {
            return redirect()
                ->route('sales.index')
                ->with(['message' => 'No se encontraron productos.', 'alert-type' => 'error']);
        }

        $amountItems =0;
        if ($request->products) {
            if (!$isProforma) {
                $sale->load('saleDetails');
                $sale->setRelation('dispensions', $sale->saleDetails);
                $val = $this->stockValidate($request, $sale);
                if($val){
                    return $val;
                }
            }
            foreach ($request->products as $key => $value){
                if($value['quantity_unit'] > 0 && $value['price_unit'] > 0){
                    $discount_unit = isset($value['discount_unit']) ? floatval($value['discount_unit']) : 0;
                    $bruto_unit = $value['quantity_unit'] * $value['price_unit'];
                    if ($bruto_unit > 0 && $discount_unit >= $bruto_unit) {
                        $discount_unit = max(0, round($bruto_unit - 0.01, 2));
                    }
                    $amountItems = $amountItems + ($bruto_unit - $discount_unit);
                }
                if(isset($value['quantity_fraction']) && isset($value['price_fraction']))
                {
                    if($value['quantity_fraction'] > 0 && $value['price_fraction'] > 0){
                        $discount_fraction = isset($value['discount_fraction']) ? floatval($value['discount_fraction']) : 0;
                        $bruto_fraction = $value['quantity_fraction'] * $value['price_fraction'];
                        if ($bruto_fraction > 0 && $discount_fraction >= $bruto_fraction) {
                            $discount_fraction = max(0, round($bruto_fraction - 0.01, 2));
                        }
                        $amountItems = $amountItems + ($bruto_fraction - $discount_fraction);
                    }
                }
            }
        }
        $general_discount = floatval($request->general_discount ?? 0);
        if ($amountItems > 0 && $general_discount >= $amountItems) {
            $general_discount = max(0, round($amountItems - 0.01, 2));
        }
        $amountTotal = $amountItems - $general_discount;

        $amount_cash = $request->amount_cash ? $request->amount_cash : 0;
        $amount_qr = $request->amount_qr ? $request->amount_qr : 0;

        if (!$isProforma && ($amount_cash + $amount_qr) < $amountTotal) {
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
            if (!$isProforma) {
                // Eliminar transacciones de pago antiguas
                foreach ($sale->saleTransactions as $saleTransaction) {
                    if ($saleTransaction->transaction) {
                        $saleTransaction->transaction->delete();
                    }
                    $saleTransaction->delete();
                }
            }

            $sale = Sale::with(['saleTransactions', 'saleDetails'])
                ->where('id', $sale->id)
                ->first();

            // 1. Restaurar stock y eliminar detalles existentes
            if ($isProforma) {
                // Proforma: solo eliminar detalles sin restaurar stock
                foreach ($sale->saleDetails as $detail) {
                    $detail->delete();
                }
            } else {
                $this->destroyDispensation($sale->saleDetails);
            }


            $sale->update([
                'person_id' => $request->person_id,
                'amountReceived' => $isProforma ? 0 : $request->amountReceived,
                'amountChange' => (!$isProforma && $request->payment_type == 'Efectivo') ? $request->amountReceived - $amountTotal : 0,
                'amount' => $amountTotal,
                'general_discount' => $general_discount,
                'observation' => $request->observation,
                'status' => $isProforma ? 'Pendiente' : ($sale->typeSale == 'Venta al Contado' ? 'Pagado' : (($amount_cash + $amount_qr) >= $amountTotal ? 'Pagado' : 'Pendiente')),
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
                    $discount_unit = isset($value['discount_unit']) ? floatval($value['discount_unit']) : 0;
                    $bruto_unit = $value['price_unit'] * $quantity_unit;
                    if ($bruto_unit > 0 && $discount_unit >= $bruto_unit) {
                        $discount_unit = max(0, round($bruto_unit - 0.01, 2));
                    }
                    $amount_unit = $bruto_unit - $discount_unit;
                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'itemStock_id' => $itemStock->id,
                        'dispensed' => 'Entero',
                        'presentation_id' => $itemStock->item->presentation_id,
                        'pricePurchase' => $itemStock->pricePurchase,
                        'price' => $value['price_unit'],
                        'quantity' => $quantity_unit,
                        'discount' => $discount_unit,
                        'amount' => $amount_unit,
                    ]);

                    if (!$isProforma) {
                        $itemStock->decrement('stock', $quantity_unit);
                        $this->autoZeroStock($itemStock, $itemStock->dispensedQuantity);
                    }
                }

                // Venta de Fracciones
                if ($quantity_fraction > 0) {
                    $discount_fraction = isset($value['discount_fraction']) ? floatval($value['discount_fraction']) : 0;
                    $bruto_fraction = $value['price_fraction'] * $quantity_fraction;
                    if ($bruto_fraction > 0 && $discount_fraction >= $bruto_fraction) {
                        $discount_fraction = max(0, round($bruto_fraction - 0.01, 2));
                    }
                    $amount_fraction = $bruto_fraction - $discount_fraction;

                    if ($isProforma) {
                        SaleDetail::create([
                            'sale_id' => $sale->id,
                            'itemStock_id' => $itemStock->id,
                            'dispensed' => 'Fraccionado',
                            'presentation_id' => $itemStock->item->fractionPresentation_id,
                            'pricePurchase' => $itemStock->pricePurchase,
                            'price' => $value['price_fraction'],
                            'quantity' => $quantity_fraction,
                            'discount' => $discount_fraction,
                            'amount' => $amount_fraction,
                        ]);
                    } else {
                        $fractions_sold_before = $itemStock->itemStockFractions()->where('deleted_at', null)->sum('quantity');
                        $fractions_sold_after  = $fractions_sold_before + $quantity_fraction;
                        $opened_units_after    = $fractions_sold_after / ($itemStock->dispensedQuantity ?: 1);

                        if ($opened_units_after > $itemStock->stock) {
                            DB::rollBack();
                            return redirect()
                                    ->route('sales.index')
                                    ->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error']);
                        }
                        $itemStockFraction = ItemStockFraction::create([
                            'itemStock_id' => $itemStock->id,
                            'quantity' => $quantity_fraction,
                            'price' => $value['price_fraction'],
                            'amount' => $amount_fraction,
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
                            'discount' => $discount_fraction,
                            'amount' => $amount_fraction,
                        ]);
                        $this->autoZeroStock($itemStock, $itemStock->dispensedQuantity);
                    }
                }


            }

            if (!$isProforma) {
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
            'saleDetails' => function ($q) {
                $q->where('deleted_at', null)
                  ->with(['itemStock.item.presentation', 'itemStock.item.fractionPresentation']);
            },
        ])
            ->where('id', $id)
            ->where('deleted_at', null)
            ->first();
        if ($sale->typeSale != 'Proforma') {
            $transaction = SaleTransaction::with(['transaction'])
                ->where('sale_id', $sale->id)
                ->first();
            return view('sales.printSale', compact('sale', 'transaction'));
        } else {
            return view('sales.printProforma', compact('sale'));
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
            if ($sale->typeSale === 'Proforma') {
                foreach ($sale->saleDetails as $detail) {
                    $detail->delete();
                }
            } else {
                $this->destroyDispensation($sale->saleDetails);
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
