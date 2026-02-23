<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="text-align: center; width: 3%">ID</th>
                    <th style="text-align: center; width: 40%">Item</th>
                    <th style="text-align: center; width: 15%"></th>
                    <th style="text-align: center">Descripción</th>
                    <th style="text-align: center; width: 5%">Estado</th>
                    <th style="text-align: center; width: 10%">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    @php
                        $image = asset('images/default.jpg');
                        if($item->image){
                            $image = asset('storage/' . str_replace('.avif', '', $item->image) . '-cropped.webp');
                        }
                        $stock = $item->itemStocks->sum('stock');
                    @endphp
                    <tr>
                        <td style="text-align: center">{{ $item->id }}</td>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <img src="{{ $image }}" alt="{{ $item->name }}"
                                    class="image-expandable"
                                    style="width: 70px; height: 70px; border-radius: 8px; margin-right: 10px; object-fit: cover; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <div>
                                    <strong style="font-size: 12px">{{ strtoupper($item->nameGeneric) }} {{ $item->nameTrade? '  |  '.strtoupper($item->nameTrade):null }}</strong> <br>
                                    <div style="font-size: 10px; color: #555; margin-top: 5px;">
                                        <span>CATEGORÍA:</span> {{ $item->category?strtoupper($item->category->name):'SN' }} <br>
                                        <span>PRESENTACIÓN:</span> {{ $item->presentation?strtoupper($item->presentation->name):'SN' }} <br>
                                        <span>LABORATORIO:</span> {{ $item->laboratory?strtoupper($item->laboratory->name):'SN' }} <br>
                                        <span>Linea:</span> {{ $item->line?strtoupper($item->line->name):'SN' }} 
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="text-align: center">
                            @if ($item->fraction)
                                <strong style="font-size: 12px">{{number_format($item->fractionQuantity, 2, ',', '.')}} {{$item->fractionPresentation->name}} </strong>
                            @else
                                <label class="label label-danger">Venta sin Fracción</label>
                            @endif
                        </td>
                            
                        <td> 
                            <strong style="font-size: 12px">{{$item->observation}}</strong>
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th style="font-size: 12px; padding: 2px 5px; text-align: center">Lote</th>
                                        <th style="font-size: 12px; padding: 2px 5px; text-align: center">P. de Compra</th>
                                        <th style="font-size: 12px; padding: 2px 5px; text-align: center">Stock</th>
                                        <th style="font-size: 12px; padding: 2px 5px; text-align: center">P. de Venta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($item->itemStocks as $itemStock)
                                        @php
                                            // Default stock display for non-fractioned items
                                            $stockText = '<span  style="font-size:11px;">'.number_format($itemStock->stock, 0).' '.($item->presentation->name ?? 'Unid.').'</span>';

                                            // If item is fractioned, calculate and format detailed stock
                                            if ($itemStock->dispensed == 'Fraccionado' && $itemStock->dispensedQuantity > 0) {
                                                $fractions_sold = $itemStock->itemStockFractions->sum('quantity');
                                                
                                                $full_units = $itemStock->stock;
                                                $remaining_fractions = 0;

                                                if ($fractions_sold > 0) {
                                                    $opened_units = ceil($fractions_sold / $itemStock->dispensedQuantity);
                                                    $remaining_fractions = ($opened_units * $itemStock->dispensedQuantity) - $fractions_sold;
                                                    $full_units = max(0, $itemStock->stock - $opened_units);
                                                }

                                                $unitName = $item->presentation ? $item->presentation->name : 'Unid.';
                                                $fractionName = $item->fractionPresentation ? $item->fractionPresentation->name : 'Frac.';

                                                $stockText = '<span style="margin-bottom: 3px; display: inline-block; width: 100%; text-align: right; font-size:11px;">'.$full_units.' '.$unitName.'</span>';
                                                if ($remaining_fractions > 0) {
                                                    $stockText .= '<br><span style="display: inline-block; width: 100%; text-align: right; font-size:11px;">'.$remaining_fractions.' '.$fractionName.'</span>';
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td style="font-size: 12px; text-align: center">{{ $itemStock->lote }}</td>
                                            <td style="font-size: 12px; text-align: right">{{ number_format($itemStock->pricePurchase, 2, ',', '.') }}</td>
                                            <td style="font-size: 12px; text-align: right">{!! $stockText !!}</td>
                                            <td style="font-size: 12px; text-align: right">
                                                <span style="display: block; margin-bottom: 2px; font-size:12px;">
                                                    {{ number_format($itemStock->priceSale, 2, ',', '.') }} / {{ $item->presentation->name ?? 'Unid.' }}
                                                </span>
                                                @if ($itemStock->dispensed == 'Fraccionado' && $itemStock->dispensedPrice > 0)
                                                    <span style="display: block; font-size:12px;">
                                                        {{ number_format($itemStock->dispensedPrice, 2, ',', '.') }} / {{ $item->fractionPresentation->name ?? 'Frac.' }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">
                                                <h5 class="text-center" style="margin-top: 5px">
                                                    <img src="{{ asset('images/emptyBox.png') }}" width="30px" alt="" style="opacity: 0.8">
                                                    <br>
                                                    Sin stocks
                                                </h5>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </td>
                
                        <td style="text-align: center">
                            @if ($item->status==1)  
                                <label class="label label-success">Activo</label>
                            @else
                                <label class="label label-warning">Inactivo</label>
                            @endif    
                            {{-- <br>
                            @if ($stock == 0)
                                <label class="label label-danger">Agotado</label>
                            @elseif ($stock <= 5)
                                <label class="label label-warning">{{ $stock }}</label>
                            @else
                                <label class="label label-success">{{ $stock }}</label>
                            @endif                     --}}
                        </td>
                        <td class="no-sort no-click bread-actions text-right">
                            @if (auth()->user()->hasPermission('read_items'))
                                <a href="{{ route('voyager.items.show', ['id' => $item->id]) }}" title="Ver" class="btn btn-sm btn-warning view">
                                    <i class="voyager-eye"></i> 
                                </a>
                            @endif
                            @if (auth()->user()->hasPermission('edit_items'))
                                <a href="{{ route('voyager.items.edit', ['id' => $item->id]) }}" title="Editar" class="btn btn-sm btn-primary edit">
                                    <i class="voyager-edit"></i>
                                </a>
                            @endif
                            @if (auth()->user()->hasPermission('delete_items'))
                                <a href="#" onclick="deleteItem('{{ route('voyager.items.destroy', ['id' => $item->id]) }}')" title="Eliminar" data-toggle="modal" data-target="#modal-delete" class="btn btn-sm btn-danger delete">
                                    <i class="voyager-trash"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <h5 class="text-center" style="margin-top: 50px">
                                <img src="{{ asset('images/empty.png') }}" width="120px" alt="" style="opacity: 0.8">
                                <br><br>
                                No hay resultados
                            </h5>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-12">
    <div class="col-md-4" style="overflow-x:auto">
        @if(count($data)>0)
            <p class="text-muted">Mostrando del {{$data->firstItem()}} al {{$data->lastItem()}} de {{$data->total()}} registros.</p>
        @endif
    </div>
    <div class="col-md-8" style="overflow-x:auto">
        <nav class="text-right">
            {{ $data->links() }}
        </nav>
    </div>
</div>

<script>
   
   var page = "{{ request('page') }}";
    $(document).ready(function(){
        $('.page-link').click(function(e){
            e.preventDefault();
            let link = $(this).attr('href');
            if(link){
                page = link.split('=')[1];
                list(page);
            }
        });
    });
</script>