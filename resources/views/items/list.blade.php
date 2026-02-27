<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover" style="font-size: 13px;">
            <thead>
                <tr>
                    <th style="text-align: center; width: 3%">ID</th>
                    <th style="width: 30%">Producto</th>
                    <th style="width: 38%">Inventario (Lotes)</th>
                    <th style="text-align: center; width: 16%">Fracción</th>
                    <th style="text-align: center; width: 6%">Estado</th>
                    <th style="text-align: center; width: 7%">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    @php
                        $image = asset('images/default.jpg');
                        if ($item->image) {
                            $image = asset('storage/' . str_replace('.avif', '', $item->image) . '-cropped.webp');
                        }
                        // Stock total
                        $totalStock = $item->itemStocks->sum('stock');
                        $hasStock   = $totalStock > 0;
                        $isBelowMin = $item->stockMinimum && $item->stockMinimum > 0 && $totalStock < $item->stockMinimum;
                    @endphp
                    <tr style="{{ $isBelowMin ? 'background: #fff8f8;' : '' }}">
                        {{-- ID --}}
                        <td style="text-align: center; vertical-align: middle; color: #999; font-size: 11px;">
                            {{ $item->id }}
                        </td>

                        {{-- Producto --}}
                        <td style="vertical-align: middle;">
                            <div style="display: flex; align-items: flex-start; gap: 10px;">
                                <img src="{{ $image }}" alt="{{ $item->nameGeneric }}"
                                     class="image-expandable"
                                     style="width: 65px; height: 65px; border-radius: 6px;
                                            object-fit: cover; flex-shrink: 0;
                                            box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <div>
                                    <div style="font-weight: 700; font-size: 13px; color: #2c3e50; line-height: 1.3;">
                                        {{ strtoupper($item->nameGeneric) }}
                                    </div>
                                    @if($item->nameTrade)
                                        <div style="font-size: 11px; color: #777; margin-bottom: 4px;">
                                            <i class="fa-solid fa-tag" style="font-size: 9px;"></i>
                                            {{ $item->nameTrade }}
                                        </div>
                                    @endif
                                    <div style="margin-top: 3px; line-height: 1.8;">
                                        @if($item->category)
                                            <span style="display:inline-block; background:#f0f0f0; color:#555;
                                                         border-radius:3px; padding:1px 6px; font-size:10px; margin-right:2px;">
                                                {{ strtoupper($item->category->name) }}
                                            </span>
                                        @endif
                                        @if($item->presentation)
                                            <span style="display:inline-block; background:#e3f2fd; color:#1565c0;
                                                         border-radius:3px; padding:1px 6px; font-size:10px; margin-right:2px;">
                                                {{ strtoupper($item->presentation->name) }}
                                            </span>
                                        @endif
                                        @if($item->laboratory)
                                            <span style="display:inline-block; background:#e8f5e9; color:#2e7d32;
                                                         border-radius:3px; padding:1px 6px; font-size:10px; margin-right:2px;">
                                                {{ strtoupper($item->laboratory->name) }}
                                            </span>
                                        @endif
                                        @if($item->line)
                                            <span style="display:inline-block; background:#fff3e0; color:#e65100;
                                                         border-radius:3px; padding:1px 6px; font-size:10px;">
                                                {{ strtoupper($item->line->name) }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($item->observation)
                                        <div style="margin-top: 4px; font-size: 11px; color: #888;
                                                    font-style: italic; border-left: 2px solid #ffe082; padding-left: 5px;">
                                            {{ Str::limit($item->observation, 60) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Inventario --}}
                        <td style="vertical-align: middle; padding: 6px; {{ $isBelowMin ? 'border-left: 3px solid #c0392b;' : '' }}">
                            {{-- Resumen total --}}
                            <div style="margin-bottom: 5px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <span>
                                    <span style="font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: 0.4px;">
                                        Stock total:
                                    </span>
                                    <span style="font-weight: 700; font-size: 12px;
                                                 color: {{ $isBelowMin ? '#c0392b' : ($hasStock ? '#27ae60' : '#e74c3c') }};">
                                        {{ $totalStock }} {{ $item->presentation->name ?? 'Unid.' }}
                                    </span>
                                </span>
                                @if ($isBelowMin)
                                    <span style="display:inline-flex; align-items:center; gap:4px;
                                                 background:#c0392b; color:#fff; border-radius:12px;
                                                 padding:2px 8px; font-size:10px; font-weight:600;">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                        Bajo mínimo ({{ number_format($item->stockMinimum, 0) }})
                                    </span>
                                @elseif ($item->stockMinimum > 0)
                                    <span style="display:inline-flex; align-items:center; gap:4px;
                                                 background:#eafaf1; color:#27ae60; border-radius:12px;
                                                 padding:2px 8px; font-size:10px; border:1px solid #a9dfbf;">
                                        <i class="fa-solid fa-shield-halved"></i>
                                        Mín: {{ number_format($item->stockMinimum, 0) }}
                                    </span>
                                @endif
                            </div>

                            @if($item->itemStocks->count() > 0)
                            <table style="width:100%; font-size: 11px; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f8f9fa;">
                                        <th style="padding: 2px 5px; border: 1px solid #eee; text-align:center;">Lote</th>
                                        <th style="padding: 2px 5px; border: 1px solid #eee; text-align:right;">P.Compra</th>
                                        <th style="padding: 2px 5px; border: 1px solid #eee; text-align:right;">Stock</th>
                                        <th style="padding: 2px 5px; border: 1px solid #eee; text-align:right;">P.Venta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($item->itemStocks as $itemStock)
                                        @php
                                            $is_frac   = $itemStock->dispensed == 'Fraccionado' && $itemStock->dispensedQuantity > 0;
                                            $full_u    = $itemStock->stock;
                                            $rem_frac  = 0;
                                            if ($is_frac) {
                                                $frac_sold = $itemStock->itemStockFractions->sum('quantity');
                                                if ($frac_sold > 0) {
                                                    $opened = ceil($frac_sold / $itemStock->dispensedQuantity);
                                                    $rem_frac = ($opened * $itemStock->dispensedQuantity) - $frac_sold;
                                                    $full_u   = max(0, $itemStock->stock - $opened);
                                                }
                                            }
                                            $rowBg = ($itemStock->stock <= 0) ? '#fff5f5' : '#fff';
                                        @endphp
                                        <tr style="background: {{ $rowBg }};">
                                            <td style="padding: 2px 5px; border: 1px solid #eee; text-align:center; color:#555;">
                                                {{ $itemStock->lote ?? 'S/N' }}
                                            </td>
                                            <td style="padding: 2px 5px; border: 1px solid #eee; text-align:right; color:#e74c3c;">
                                                Bs.{{ number_format($itemStock->pricePurchase, 2, ',', '.') }}
                                            </td>
                                            <td style="padding: 2px 5px; border: 1px solid #eee; text-align:right;">
                                                @if ($is_frac)
                                                    <span style="color:#2e7d32; font-weight:600;">{{ $full_u }} {{ $item->presentation->name ?? 'U' }}</span>
                                                    @if($rem_frac > 0)
                                                        <br><span style="color:#1565c0;">{{ $rem_frac }} {{ $item->fractionPresentation->name ?? 'F' }}</span>
                                                    @endif
                                                @else
                                                    <span style="font-weight:600;">{{ number_format($itemStock->stock, 0) }} {{ $item->presentation->name ?? 'U' }}</span>
                                                @endif
                                            </td>
                                            <td style="padding: 2px 5px; border: 1px solid #eee; text-align:right; color:#2e7d32;">
                                                Bs.{{ number_format($itemStock->priceSale, 2, ',', '.') }}<br>
                                                @if($is_frac && $itemStock->dispensedPrice > 0)
                                                    <span style="color:#1565c0; font-size:10px;">
                                                        Bs.{{ number_format($itemStock->dispensedPrice, 2, ',', '.') }}/{{ $item->fractionPresentation->name ?? 'Frac.' }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" style="text-align:center; padding:5px; color:#bbb; font-style:italic; border:1px solid #eee;">
                                                Sin lotes registrados
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @else
                                <div style="text-align:center; color:#bbb; font-size:11px; font-style:italic; padding: 8px 0;">
                                    <i class="fa-solid fa-box-open"></i> Sin stock
                                </div>
                            @endif
                        </td>

                        {{-- Fracción --}}
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($item->fraction)
                                <div style="background: linear-gradient(135deg,#f0fdf4,#eff6ff);
                                            border: 1px solid #c8e6c9; border-radius: 5px; padding: 6px 8px;">
                                    <div style="font-size: 9px; color:#888; text-transform:uppercase; letter-spacing:0.4px; margin-bottom:3px;">
                                        <i class="fa-solid fa-puzzle-piece"></i> Habilitada
                                    </div>
                                    <div style="display:flex; align-items:center; justify-content:center; gap:4px; flex-wrap:wrap;">
                                        <span style="font-size:11px; font-weight:700; color:#2e7d32;">
                                            {{ $item->presentation ? strtoupper($item->presentation->name) : '—' }}
                                        </span>
                                        <span style="color:#aaa; font-size:12px;">→</span>
                                        <span style="font-size:11px; font-weight:700; color:#1565c0;">
                                            {{ number_format($item->fractionQuantity, 0, ',', '.') }}
                                            {{ $item->fractionPresentation ? strtoupper($item->fractionPresentation->name) : '—' }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <span style="font-size:10px; background:#f5f5f5; color:#888;
                                             border:1px solid #ddd; border-radius:4px;
                                             padding:3px 8px; display:inline-block;">
                                    <i class="fa-solid fa-cube"></i> Sin fracción
                                </span>
                            @endif
                        </td>

                        {{-- Estado --}}
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($item->status == 1)
                                <span class="label label-success" style="font-size:10px;">Activo</span>
                            @else
                                <span class="label label-warning" style="font-size:10px;">Inactivo</span>
                            @endif
                        </td>

                        {{-- Acciones --}}
                        <td class="no-sort no-click bread-actions text-right" style="vertical-align: middle;">
                            @if (auth()->user()->hasPermission('read_items'))
                                <a href="{{ route('voyager.items.show', ['id' => $item->id]) }}"
                                   title="Ver" class="btn btn-sm btn-warning view">
                                    <i class="voyager-eye"></i>
                                </a>
                            @endif
                            @if (auth()->user()->hasPermission('edit_items'))
                                <a href="{{ route('voyager.items.edit', ['id' => $item->id]) }}"
                                   title="Editar" class="btn btn-sm btn-primary edit">
                                    <i class="voyager-edit"></i>
                                </a>
                            @endif
                            @if (auth()->user()->hasPermission('delete_items'))
                                <a href="#"
                                   onclick="deleteItem('{{ route('voyager.items.destroy', ['id' => $item->id]) }}')"
                                   title="Eliminar" data-toggle="modal" data-target="#modal-delete"
                                   class="btn btn-sm btn-danger delete">
                                    <i class="voyager-trash"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <h5 class="text-center" style="margin: 40px 0; color: #bbb;">
                                <img src="{{ asset('images/empty.png') }}" width="100px" alt="" style="opacity: 0.5"><br><br>
                                No hay resultados.
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
