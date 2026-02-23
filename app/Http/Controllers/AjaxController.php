<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemStock;
use Illuminate\Http\Request;
use App\Models\Person;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function personList(){
        $q = request('q');
        $data = Person::OrWhereRaw($q ? "ci like '%$q%'" : 1)
                        ->OrWhereRaw($q ? "phone like '%$q%'" : 1)
                        ->OrWhereRaw($q ? "first_name like '%$q%'" : 1)
                        ->OrWhereRaw($q ? "middle_name like '%$q%'" : 1)
                        ->OrWhereRaw($q ? "paternal_surname like '%$q%'" : 1)
                        ->OrWhereRaw($q ? "maternal_surname like '%$q%'" : 1)
                        ->orWhere(function ($subQ) use ($q) {
                            $subQ->whereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, '')) like ?", ["%$q%"])
                                ->orWhereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(paternal_surname, ''), ' ', COALESCE(maternal_surname, '')) like ?", ["%$q%"])
                                ->orWhereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(paternal_surname, ''), ' ', COALESCE(maternal_surname, '')) like ?", ["%$q%"]);
                        })
                        ->where('deleted_at', null)
                        ->get();
        return response()->json($data);
    }

    public function personStore(Request $request){
        $person = Person::withTrashed()->where('ci', $request->ci)->first();
        if ($person) {
            return response()->json(['error' => 'El CI ya se encuentra registrado a nombre de: ' . $person->first_name . ' ' . $person->paternal_surname]);
        }
        DB::beginTransaction();
        try {
            $person =Person::create($request->all());
            DB::commit();
            return response()->json(['person' => $person]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['error' => 'Ocurrió un error al guardar...']);
        }
    }


    // Para obtener el stock en tipo real Select "Ventas"
    public function itemStockList(){
        $search = request('q');
        
        // $user = Auth::user();
        $data = ItemStock::with(['item', 'item.line', 'item.category', 'item.presentation', 'item.fractionPresentation', 'itemStockFractions'=>function($q){
                $q->where('deleted_at', null);
            }])
            ->Where(function($query) use ($search){
                if($search){
                    $query->whereHas('item', function($query) use($search){
                        $query->whereRaw($search ? 'nameGeneric like "%'.$search.'%"' : 1)
                            ->orWhereRaw($search ? 'nameTrade like "%'.$search.'%"' : 1)
                            ->OrWhereRaw($search ? "observation like '%$search%'" : 1);
                    })
                    ->OrwhereHas('item.line', function($query) use($search){
                        $query->whereRaw($search ? 'name like "%'.$search.'%"' : 1);
                    })
                    ->OrwhereHas('item.category', function($query) use($search){
                        $query->whereRaw($search ? 'name like "%'.$search.'%"' : 1);
                    })
                    ->OrwhereHas('item.presentation', function($query) use($search){
                        $query->whereRaw($search ? 'name like "%'.$search.'%"' : 1);
                    })
                    ->OrWhereRaw($search ? "id like '%$search%'" : 1)
                    ->OrWhereRaw($search ? "lote like '%$search%'" : 1);
                }
            })
            ->where('deleted_at', null)
            ->where('stock', '>', 0)
            ->get();

        // Calcular stock real para productos fraccionados
        foreach ($data as $itemStock) {
            if ($itemStock->dispensed === 'Fraccionado' && $itemStock->dispensedQuantity > 0) {
                $fractions_sold = $itemStock->itemStockFractions->sum('quantity');
                
                if ($fractions_sold > 0) {
                    // Unidades completas que se han tenido que "abrir" para vender por fracción
                    $opened_units = ceil($fractions_sold / $itemStock->dispensedQuantity);
                    
                    // Fracciones restantes de la última unidad abierta
                    // $remaining_fractions = $itemStock->dispensedQuantity - ($fractions_sold % $itemStock->dispensedQuantity);
                    $remaining_fractions = $itemStock->dispensedQuantity - fmod($fractions_sold, $itemStock->dispensedQuantity);
                    // Si se vendió una unidad completa en fracciones, las restantes son 0, no la cantidad total
                    if ($remaining_fractions == $itemStock->dispensedQuantity) {
                        $remaining_fractions = 0;
                    }

                    $itemStock->stock_details = [
                        'full_units' => max(0, $itemStock->stock - $opened_units),
                        'remaining_fractions' => $remaining_fractions
                    ];
                }
            }
        }

        return response()->json($data);
    }


    // Para Income Para buscar los Item
    public function itemList(){
        $search = request('q');
        $data = Item::with(['line', 'laboratory', 'category', 'presentation', 'fractionPresentation'])
            ->Where(function($query) use ($search){
                if($search){
                    $query->whereHas('line', function($query) use($search){
                        $query->whereRaw($search ? 'name like "%'.$search.'%"' : 1);
                    })
                    ->OrwhereHas('category', function($query) use($search){
                        $query->whereRaw($search ? 'name like "%'.$search.'%"' : 1);
                    })
                    ->OrwhereHas('laboratory', function($query) use($search){
                        $query->whereRaw($search ? 'name like "%'.$search.'%"' : 1);
                    })
                    ->OrWhereRaw($search ? "id like '%$search%'" : 1)
                    ->OrWhereRaw($search ? "nameGeneric like '%$search%'" : 1)
                    ->OrWhereRaw($search ? "nameTrade like '%$search%'" : 1)
                    ->OrWhereRaw($search ? "observation like '%$search%'" : 1);
                }
            })
            ->where('deleted_at', null)
            ->where('status', 1)
            ->get();
        return response()->json($data);
    }
}
