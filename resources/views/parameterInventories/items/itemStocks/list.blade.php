<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width:5px">N&deg;</th>
                    <th style="text-align: center; width:15%">Lote / Origen</th>
                    <th style="text-align: center; width:10%">Fecha de Expiración</th>
                    <th style="text-align: center; width:10%">Detalle de Compra</th>
                    <th style="text-align: center; width:15%">Stock Actual</th>
                    <th style="text-align: center; width:15%">Precios de Venta</th>
                    <th style="text-align: center">Observaciones</th>                     
                    <th style="text-align: center; width:10%">Estado</th>
                    <th style="text-align: center; width:5%">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i=1;
                @endphp 
                @forelse ($data as $value)
                    @php
                        // $value es ItemStock
                        $item = $value->item;

                        // Calcular stock disponible para mostrar
                        $stock_full_units = $value->stock;
                        $stock_fractions = 0;
                        $is_fractioned = $value->dispensed == 'Fraccionado' && $value->dispensedQuantity > 0;

                        if ($is_fractioned) {
                            $fractions_sold = $value->itemStockFractions->sum('quantity');
                            if ($fractions_sold > 0) {
                                $opened_units = ceil($fractions_sold / $value->dispensedQuantity);
                                $stock_fractions = ($opened_units * $value->dispensedQuantity) - $fractions_sold;
                                $stock_full_units = max(0, $value->stock - $opened_units);
                            }
                        }
                        
                        $total_stock_in_fractions = ($stock_full_units * ($value->dispensedQuantity ?? 1)) + $stock_fractions;
                    @endphp
                    <tr>
                        <td>{{ $i }}</td>
                        <td>
                            <strong>{{$value->lote ?? 'SN'}}</strong><br>
                            @if ($value->incomeDetail_id == null)
                                <small><i class="voyager-pen"></i> Ingreso Manual</small>
                            @else
                                <small><i class="voyager-bag"></i> Ingreso por Compra</small>
                            @endif
                        </td>
                        <td style="text-align: center">
                            @if ($value->expirationDate)
                                @php
                                    $expiration = \Carbon\Carbon::parse($value->expirationDate)->startOfDay();
                                    $today = \Carbon\Carbon::now()->startOfDay();
                                    $diffDays = $today->diffInDays($expiration, false);
                                    $settingExpiration = setting('items-productos.notificateExpiration');
                                    $daysExpiration = is_numeric($settingExpiration) ? (int)$settingExpiration : 15;
                                @endphp
                                @if ($diffDays < 0)
                                    <span style="color: #e74c3c; font-weight: bold" data-toggle="tooltip" title="Vencido">
                                        {{ date('d/m/Y', strtotime($value->expirationDate))}}
                                    </span>
                                @elseif ($diffDays <= $daysExpiration)
                                    <span style="color: #e67e22; font-weight: bold" data-toggle="tooltip" title="Por vencer">
                                        {{ date('d/m/Y', strtotime($value->expirationDate))}}
                                    </span>
                                @else
                                    {{ date('d/m/Y', strtotime($value->expirationDate))}}
                                @endif
                            @else
                                <label class="label label-warning">Sin Fecha de Expiración</label>
                            @endif
                        </td>
                        <td style="text-align: right">
                            <small>Cant. Inicial:</small> <strong>{{number_format($value->quantity, 0)}}</strong><br>
                            <small>P/U Compra:</small> <strong>Bs. {{number_format($value->pricePurchase, 2, ',', '.')}}</strong>
                        </td>
                        <td style="text-align: center">
                            @if ($is_fractioned)
                                <span class="label label-success" style="display: block; margin-bottom: 3px; font-size:11px;">{{$stock_full_units}} {{$item->presentation->name ?? 'Unid.'}}</span>
                                @if ($stock_fractions > 0)
                                    <span class="label label-info" style="display: block; font-size:11px;">{{$stock_fractions}} {{$item->fractionPresentation->name ?? 'Frac.'}}</span>
                                @endif
                            @else
                                <span class="label label-default" style="display: block; font-size:11px;">{{number_format($value->stock, 0)}} {{$item->presentation->name ?? 'Unid.'}}</span>
                            @endif
                        </td>
                        <td style="text-align: right">
                            <span class="label label-primary" style="display: block; margin-bottom: 2px; font-size:11px;">
                                {{ number_format($value->priceSale, 2, ',', '.') }} / {{$item->presentation->name ?? 'Unid.'}}
                            </span>
                            @if ($is_fractioned && $value->dispensedPrice > 0)
                                <span class="label label-info" style="display: block; font-size:11px;">
                                    {{ number_format($value->dispensedPrice, 2, ',', '.') }} / {{$item->fractionPresentation->name ?? 'Frac.'}}
                                </span>
                            @else
                                <span class="label label-default" style="display: block; font-size:11px;">
                                    --
                                </span>
                            @endif
                        </td>
                        <td>{{$value->observation ?? 'Ninguna'}}</td>
                        <td style="text-align: center">
                            @if ($total_stock_in_fractions <= 0)
                                <label class="label label-danger">Agotado</label> 
                            @else
                                <label class="label label-success">Disponible</label> 
                            @endif
                        </td>
                        <td style="text-align: center">
                            @if ($value->deleted_at)
                                <span style="color: red">Eliminado</span>
                            @else
                                @if ($value->quantity == $value->stock && $value->itemStockFractions->count() == 0)
                                    <a href="#" onclick="deleteItem('{{ route('items-stock.destroy', ['id' => $value->item_id, 'stock'=>$value->id]) }}')" title="Eliminar" data-toggle="modal" data-target="#modal-delete" class="btn btn-sm btn-danger delete">
                                        <i class="voyager-trash"></i>
                                    </a>                         
                                @else
                                    <span data-toggle="tooltip" title="No se puede eliminar porque ya tiene ventas o dispensaciones asociadas.">
                                        <button class="btn btn-sm btn-danger" disabled><i class="voyager-trash"></i></button>
                                    </span>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @php
                        $i++;
                    @endphp
                @empty
                    <tr>
                        <td colspan="9">
                            <h5 class="text-center" style="margin-top: 50px">
                                {{-- <img src="{{ asset('images/empty.png') }}" width="120px" alt="" style="opacity: 0.8"> --}}
                                <i class="fa-solid fa-box-open" style="font-size: 50px;"></i>
                                
                                <br><br>
                                No hay stock disponible para este producto.
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
        $('[data-toggle="tooltip"]').tooltip();
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