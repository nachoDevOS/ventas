<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\ItemStockFraction;
use App\Models\EgresDetail;
use App\Models\Egres;
use Illuminate\Support\Facades\DB;

class EgresController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, $id, $stockId)
    {
        $this->custom_authorize('edit_items');

        $item      = Item::with(['presentation', 'fractionPresentation'])->findOrFail($id);
        $itemStock = ItemStock::with(['itemStockFractions'])
            ->where('id', $stockId)
            ->where('item_id', $id)
            ->whereNull('deleted_at')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $reason = trim($request->reason ?? '');

            $egress = Egres::create([
                'reason'     => $reason,
                'dateEgress' => now()->toDateString(),
                'status'     => 'Activo',
            ]);

            if ($item->fraction && $item->fractionQuantity > 0) {
                $wholeUnits     = max(0, intval($request->quantity ?? 0));
                $extraFractions = max(0, floatval($request->extraFractions ?? 0));

                if ($wholeUnits == 0 && $extraFractions == 0) {
                    DB::rollback();
                    return redirect()->back()
                        ->with(['message' => 'Debe ingresar al menos una unidad o fracción.', 'alert-type' => 'error']);
                }

                $usedFractions      = $itemStock->itemStockFractions->sum('quantity');
                $availableFractions = ($itemStock->stock * $item->fractionQuantity) - $usedFractions;
                $totalRequested     = ($wholeUnits * $item->fractionQuantity) + $extraFractions;

                if ($totalRequested > $availableFractions) {
                    DB::rollback();
                    return redirect()->back()
                        ->with(['message' => 'La cantidad supera el stock disponible del lote (' . $availableFractions . ' ' . ($item->fractionPresentation->name ?? 'fracciones') . ').', 'alert-type' => 'error']);
                }

                if ($wholeUnits > 0) {
                    $itemStock->decrement('stock', $wholeUnits);
                    $this->autoZeroStock($itemStock, $item->fractionQuantity);

                    EgresDetail::create([
                        'egres_id'        => $egress->id,
                        'itemStock_id'    => $itemStock->id,
                        'pricePurchase'   => $itemStock->pricePurchase,
                        'presentation_id' => $item->presentation_id,
                        'price'           => $itemStock->priceSale,
                        'quantity'        => $wholeUnits,
                        'amount'          => 0,
                        'status'          => 1,
                    ]);
                }

                if ($extraFractions > 0) {
                    $fraction = ItemStockFraction::create([
                        'itemStock_id' => $itemStock->id,
                        'quantity'     => $extraFractions,
                        'price'        => 0,
                        'amount'       => 0,
                    ]);

                    EgresDetail::create([
                        'egres_id'             => $egress->id,
                        'itemStock_id'         => $itemStock->id,
                        'itemStockFraction_id' => $fraction->id,
                        'pricePurchase'        => $itemStock->pricePurchase,
                        'presentation_id'      => $item->fractionPresentation_id,
                        'price'                => $itemStock->dispensedPrice,
                        'quantity'             => $extraFractions,
                        'amount'               => 0,
                        'status'               => 1,
                    ]);
                    $this->autoZeroStock($itemStock, $item->fractionQuantity);
                }

            } else {
                $quantity = max(0, floatval($request->quantity ?? 0));

                if ($quantity <= 0) {
                    DB::rollback();
                    return redirect()->back()
                        ->with(['message' => 'Debe ingresar una cantidad mayor a 0.', 'alert-type' => 'error']);
                }

                if ($quantity > $itemStock->stock) {
                    DB::rollback();
                    return redirect()->back()
                        ->with(['message' => 'La cantidad supera el stock disponible del lote (' . $itemStock->stock . ' unidades).', 'alert-type' => 'error']);
                }

                $itemStock->decrement('stock', $quantity);

                EgresDetail::create([
                    'egres_id'        => $egress->id,
                    'itemStock_id'    => $itemStock->id,
                    'pricePurchase'   => $itemStock->pricePurchase,
                    'presentation_id' => $item->presentation_id,
                    'price'           => $itemStock->priceSale,
                    'quantity'        => $quantity,
                    'amount'          => 0,
                    'status'          => 1,
                ]);
            }

            DB::commit();
            return redirect()->route('voyager.items.show', ['id' => $id])
                ->with(['message' => 'Egreso registrado exitosamente.', 'alert-type' => 'success']);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('voyager.items.show', ['id' => $id])
                ->with(['message' => 'Error al registrar egreso: ' . $e->getMessage(), 'alert-type' => 'error']);
        }
    }

    public function destroy(Request $request, $id, $stockId, $egressId)
    {
        $this->custom_authorize('edit_items');

        $egressHeader = Egres::with(['details' => function ($q) {
            $q->whereNull('deleted_at');
        }])->findOrFail($egressId);

        DB::beginTransaction();
        try {
            // Eliminar cabecera primero para que destroyDispensation la excluya del conteo
            $egressHeader->delete();

            $this->destroyDispensation($egressHeader->details);

            DB::commit();
            return redirect()->route('voyager.items.show', ['id' => $id])
                ->with(['message' => 'Egreso eliminado y stock restaurado correctamente.', 'alert-type' => 'success']);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('voyager.items.show', ['id' => $id])
                ->with(['message' => 'Error al eliminar egreso: ' . $e->getMessage(), 'alert-type' => 'error']);
        }
    }
}
