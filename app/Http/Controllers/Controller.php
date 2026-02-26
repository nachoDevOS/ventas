<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Models\ItemStock;
use App\Models\ItemStockFraction;
use App\Models\SaleDetail;
use DateTime;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function custom_authorize($permission){
        if(!Auth::user()->hasPermission($permission)){
            abort(403, 'THIS ACTIO UNAUTHORIZED.');
        }
    }

    // Para poder validar si existe el producto y la cantidad en stock para poder dispensar
    public function stockValidate($request, $model = null)
    {
        // =================================================================================
        // 1. Bucle de validación de stock ANTES de iniciar la transacción
        // =================================================================================
        foreach ($request->products as $key => $value) {
            $itemStock = ItemStock::with(['item', 'itemStockFractions'])->findOrFail($value['id']);
            // return $itemStock;
            $quantity_unit = $value['quantity_unit'] ?? 0; // Cantidad de unidades enteras
            $quantity_fraction = $value['quantity_fraction'] ?? 0; // Cantidad de fracciones
            // return $quantity_unit;
            // return $quantity_fraction;

            // Si no se vende nada de este producto, saltar
            if ($quantity_unit == 0 && $quantity_fraction == 0) {
                continue;
            }

            $total_stock_in_fractions = $itemStock->stock * ($itemStock->dispensedQuantity ?? 1); //obtenemos 
            // return $total_stock_in_fractions;

            // Lógica para stock real si es fraccionado
            if ($itemStock->dispensed === 'Fraccionado' && $itemStock->dispensedQuantity > 0) {
                $fractions_sold = $itemStock->itemStockFractions->sum('quantity'); //obtengo el total de fracciones vendidas
                // return $fractions_sold;

                if ($fractions_sold > 0) {
                    $opened_units = ceil($fractions_sold / $itemStock->dispensedQuantity); // Unidades completas que se han tenido que "abrir" para vender por fracción
                    // return $opened_units;

                    // Fracciones restantes de la última unidad abierta
                    $remaining_fractions_in_opened_units = ($opened_units * $itemStock->dispensedQuantity) - $fractions_sold; 
                    // return $remaining_fractions_in_opened_units;
            
                    // Si se vendió una unidad completa en fracciones, las restantes son 0, no la cantidad total. Usamos max() para evitar valores negativos.
                    $full_units = max(0, $itemStock->stock - $opened_units);  //obtengo el valor total entero de productos enteros
                    // return $itemStock->stock;
                    // return $full_units;
                    
                    //Obtenemos el total en fracciones de productos enteros
                    $total_stock_in_fractions = ($full_units * $itemStock->dispensedQuantity) + $remaining_fractions_in_opened_units;
                    // return $total_stock_in_fractions;
                }
            }

            // Si estamos editando, sumamos el stock que ya tiene asignado el registro actual para simular su devolución
            if ($model) {
                $heldDispensations = $model->dispensions->where('itemStock_id', $itemStock->id);
                foreach ($heldDispensations as $dispensation) {
                    if ($dispensation->dispensed == 'Entero') {
                        $total_stock_in_fractions += $dispensation->quantity * ($itemStock->dispensedQuantity ?: 1);
                    } else {
                        $total_stock_in_fractions += $dispensation->quantity;
                    }
                }
            }
            
            $requested_fractions = ($quantity_unit * ($itemStock->dispensedQuantity ?? 1)) + $quantity_fraction;
            if ($requested_fractions > $total_stock_in_fractions) {
                return redirect()->back()->withInput()->with(['message' => 'Stock insuficiente para el producto: ' . $itemStock->item->nameGeneric, 'alert-type' => 'error']);
            }
        }
        return null;
        // =================================================================================
        // Fin de la validación
        // =================================================================================
    }

    // Para eliminacion y actualizacion o editar
    public function destroyDispensation($dispensions)
    {
        foreach ($dispensions as $detail) {
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
                    ->get()
                    ->sum('quantity');
               


                $itemStock->update([
                    'stock' => $itemStock->quantity
                ]);
                $itemStock->decrement('stock', ($itemStockUnit));
                $detail->delete();   
            }
        }
    }

    
}
