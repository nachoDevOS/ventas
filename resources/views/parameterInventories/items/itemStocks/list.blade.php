<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover" style="font-size: 13px;">
            <thead>
                <tr>
                    <th style="text-align: center; width: 4%">N°</th>
                    <th style="text-align: center; width: 13%">Lote / Origen</th>
                    <th style="text-align: center; width: 12%">Expiración</th>
                    <th style="text-align: center; width: 11%">Detalle Compra</th>
                    <th style="text-align: center; width: 14%">Stock Actual</th>
                    <th style="text-align: center; width: 18%">Precios / Ganancia</th>
                    <th style="text-align: center;">Observación</th>
                    <th style="text-align: center; width: 8%">Estado</th>
                    <th style="text-align: center; width: 5%">Acción</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 1; @endphp
                @forelse ($data as $value)
                    @php
                        $item = $value->item;

                        // Stock con fracción
                        $stock_full_units = $value->stock;
                        $stock_fractions  = 0;
                        $is_fractioned    = $value->dispensed == 'Fraccionado' && $value->dispensedQuantity > 0;

                        if ($is_fractioned) {
                            $fractions_sold = $value->itemStockFractions->sum('quantity');
                            if ($fractions_sold > 0) {
                                $opened_units     = ceil($fractions_sold / $value->dispensedQuantity);
                                $stock_fractions  = ($opened_units * $value->dispensedQuantity) - $fractions_sold;
                                $stock_full_units = max(0, $value->stock - $opened_units);
                            }
                        }

                        $total_stock_in_fractions = ($stock_full_units * ($value->dispensedQuantity ?? 1)) + $stock_fractions;

                        // Expiración
                        $expiration = $value->expirationDate ? \Carbon\Carbon::parse($value->expirationDate)->startOfDay() : null;
                        $today      = \Carbon\Carbon::now()->startOfDay();
                        $diffDays   = $expiration ? $today->diffInDays($expiration, false) : null;
                        $settingExp = setting('items-productos.notificateExpiration');
                        $daysAlert  = is_numeric($settingExp) ? (int)$settingExp : 15;

                        // Margen de ganancia unidad
                        $marginUnit = ($value->pricePurchase > 0)
                            ? (($value->priceSale - $value->pricePurchase) / $value->pricePurchase) * 100
                            : null;

                        // Margen de ganancia fracción
                        $marginFrac = null;
                        if ($is_fractioned && $value->dispensedPrice > 0 && $value->pricePurchase > 0) {
                            $totalFracSale = $value->dispensedPrice * $value->dispensedQuantity;
                            $marginFrac    = (($totalFracSale - $value->pricePurchase) / $value->pricePurchase) * 100;
                        }
                    @endphp
                    <tr>
                        {{-- N° --}}
                        <td style="text-align: center; vertical-align: middle; color: #999;">{{ $i }}</td>

                        {{-- Lote / Origen --}}
                        <td style="vertical-align: middle;">
                            <b style="font-size: 13px;">{{ $value->lote ?? 'S/N' }}</b><br>
                            <small class="text-muted">
                                @if ($value->incomeDetail_id == null)
                                    <i class="voyager-pen"></i> Ingreso Manual
                                @else
                                    <i class="voyager-bag"></i> Ingreso por Compra
                                @endif
                            </small><br>
                            <small style="color: #aaa; font-size: 10px;">
                                <i class="fa-solid fa-calendar-plus"></i>
                                {{ \Carbon\Carbon::parse($value->created_at)->format('d/m/Y H:i') }}
                            </small>
                        </td>

                        {{-- Expiración --}}
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($expiration)
                                @if ($diffDays < 0)
                                    <span style="color: #e74c3c; font-weight: 700;">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        {{ $expiration->format('d/m/Y') }}
                                    </span><br>
                                    <span class="label label-danger" style="font-size: 9px;">
                                        Vencido hace {{ abs($diffDays) }} día(s)
                                    </span>
                                @elseif ($diffDays <= $daysAlert)
                                    <span style="color: #e67e22; font-weight: 700;">
                                        <i class="fa-solid fa-clock"></i>
                                        {{ $expiration->format('d/m/Y') }}
                                    </span><br>
                                    <span class="label label-warning" style="font-size: 9px;">
                                        Vence en {{ $diffDays }} día(s)
                                    </span>
                                @else
                                    <span style="color: #27ae60;">
                                        {{ $expiration->format('d/m/Y') }}
                                    </span><br>
                                    <small class="text-muted" style="font-size: 10px;">
                                        {{ $diffDays }} días restantes
                                    </small>
                                @endif
                            @else
                                <span class="label label-warning" style="font-size: 9px;">Sin fecha</span>
                            @endif
                        </td>

                        {{-- Detalle Compra --}}
                        <td style="text-align: right; vertical-align: middle;">
                            <small class="text-muted">Cant. inicial:</small><br>
                            <b>{{ number_format($value->quantity, 0) }}
                               {{ $item->presentation->name ?? 'Unid.' }}</b><br>
                            <small class="text-muted">P/U compra:</small><br>
                            <b style="color: #e74c3c;">Bs. {{ number_format($value->pricePurchase, 2, ',', '.') }}</b>
                        </td>

                        {{-- Stock Actual --}}
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($is_fractioned)
                                <div style="margin-bottom: 3px;">
                                    <span style="display: inline-block; background: #e8f5e9; color: #2e7d32;
                                                 border: 1px solid #a5d6a7; border-radius: 4px;
                                                 padding: 2px 8px; font-size: 11px; font-weight: 700;">
                                        {{ $stock_full_units }} {{ $item->presentation->name ?? 'Unid.' }}
                                    </span>
                                </div>
                                @if ($stock_fractions > 0)
                                    <span style="display: inline-block; background: #e3f2fd; color: #1565c0;
                                                 border: 1px solid #90caf9; border-radius: 4px;
                                                 padding: 2px 8px; font-size: 11px; font-weight: 700;">
                                        {{ $stock_fractions }} {{ $item->fractionPresentation->name ?? 'Frac.' }}
                                    </span>
                                @endif
                            @else
                                <span style="display: inline-block; background: #f5f5f5; color: #555;
                                             border: 1px solid #ddd; border-radius: 4px;
                                             padding: 2px 10px; font-size: 12px; font-weight: 700;">
                                    {{ number_format($value->stock, 0) }} {{ $item->presentation->name ?? 'Unid.' }}
                                </span>
                            @endif
                        </td>

                        {{-- Precios / Ganancia --}}
                        <td style="vertical-align: middle;">
                            <div style="display: flex; justify-content: space-between; align-items: center;
                                        background: #e8f5e9; border-radius: 4px; padding: 3px 7px; margin-bottom: 2px;">
                                <small style="color: #555;">{{ $item->presentation->name ?? 'Unid.' }}</small>
                                <b style="color: #2e7d32;">Bs. {{ number_format($value->priceSale, 2, ',', '.') }}</b>
                            </div>
                            @if ($marginUnit !== null)
                                <div style="text-align: right; font-size: 10px; margin-bottom: 4px;
                                            color: {{ $marginUnit >= 0 ? '#27ae60' : '#e74c3c' }};">
                                    {{ $marginUnit >= 0 ? '▲' : '▼' }} {{ number_format(abs($marginUnit), 1) }}% ganancia
                                </div>
                            @endif

                            @if ($is_fractioned && $value->dispensedPrice > 0)
                                <div style="display: flex; justify-content: space-between; align-items: center;
                                            background: #e3f2fd; border-radius: 4px; padding: 3px 7px; margin-bottom: 2px;">
                                    <small style="color: #555;">{{ $item->fractionPresentation->name ?? 'Frac.' }}</small>
                                    <b style="color: #1565c0;">Bs. {{ number_format($value->dispensedPrice, 2, ',', '.') }}</b>
                                </div>
                                @if ($marginFrac !== null)
                                    <div style="text-align: right; font-size: 10px;
                                                color: {{ $marginFrac >= 0 ? '#27ae60' : '#e74c3c' }};">
                                        {{ $marginFrac >= 0 ? '▲' : '▼' }} {{ number_format(abs($marginFrac), 1) }}% ganancia
                                    </div>
                                @endif
                            @else
                                <div style="text-align: center; color: #ccc; font-size: 11px; font-style: italic;">
                                    Sin precio fracción
                                </div>
                            @endif
                        </td>

                        {{-- Observación --}}
                        <td style="vertical-align: middle;">
                            @if ($value->observation)
                                <span style="font-size: 12px;">{{ $value->observation }}</span>
                            @else
                                <span class="text-muted" style="font-size: 11px; font-style: italic;">—</span>
                            @endif
                        </td>

                        {{-- Estado --}}
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($total_stock_in_fractions <= 0)
                                <span class="label label-danger" style="font-size: 10px;">Agotado</span>
                            @else
                                <span class="label label-success" style="font-size: 10px;">Disponible</span>
                            @endif
                        </td>

                        {{-- Acciones --}}
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($value->deleted_at)
                                <span style="color: #e74c3c; font-size: 11px;">Eliminado</span>
                            @else
                                @if ($value->quantity == $value->stock && $value->itemStockFractions->count() == 0)
                                    <a href="#"
                                       onclick="deleteItem('{{ route('items-stock.destroy', ['id' => $value->item_id, 'stock' => $value->id]) }}')"
                                       title="Eliminar" data-toggle="modal" data-target="#modal-delete"
                                       class="btn btn-sm btn-danger">
                                        <i class="voyager-trash"></i>
                                    </a>
                                @else
                                    <span data-toggle="tooltip"
                                          title="No se puede eliminar porque ya tiene ventas o dispensaciones asociadas.">
                                        <button class="btn btn-sm btn-danger" disabled>
                                            <i class="voyager-trash"></i>
                                        </button>
                                    </span>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @php $i++; @endphp
                @empty
                    <tr>
                        <td colspan="9">
                            <h5 class="text-center" style="margin: 40px 0; color: #bbb;">
                                <i class="fa-solid fa-box-open" style="font-size: 45px; opacity: 0.3;"></i>
                                <br><br>No hay stock registrado para este producto.
                            </h5>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-12">
    <div class="col-md-4" style="overflow-x: auto;">
        @if(count($data) > 0)
            <p class="text-muted" style="margin-top: 8px;">
                Mostrando del {{ $data->firstItem() }} al {{ $data->lastItem() }}
                de {{ $data->total() }} registros.
            </p>
        @endif
    </div>
    <div class="col-md-8" style="overflow-x: auto;">
        <nav class="text-right">{{ $data->links() }}</nav>
    </div>
</div>

<script>
    var page = "{{ request('page') }}";
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('.page-link').click(function (e) {
            e.preventDefault();
            let link = $(this).attr('href');
            if (link) {
                page = link.split('=')[1];
                list(page);
            }
        });
    });
</script>
