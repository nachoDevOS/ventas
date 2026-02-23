@extends('voyager::master')

@section('page_title', 'Ver Productos / Items')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body" style="padding: 0px">
                        <div class="col-md-6" style="padding: 0px; display: flex; align-items: center;">
                            <h1 class="page-title">
                                <i class="fa-solid fa-cart-shopping"></i> Productos / Items
                            </h1>
                        </div>
                        <div class="col-md-6 text-right" style="margin-top: 30px">
                            <a href="{{ route('voyager.items.index') }}" class="btn btn-warning btn-sm">
                                <i class="voyager-list"></i> <span>Volver</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Imagen</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                @php
                                    $image = $item->image ? Voyager::image($item->image) : asset('images/default.jpg');
                                @endphp
                                <img src="{{ $image }}" style="width: 100%; height: 250px; object-fit: cover; border-radius: 5px; border: 1px solid #eee;" alt="{{ $item->nameTrade }}">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="panel-heading" style="border-bottom:0;">
                                        <h3 class="panel-title">Laboratorio</h3>
                                    </div>
                                    <div class="panel-body" style="padding-top:0;">
                                        <p>{{ $item->laboratory?strtoupper($item->laboratory->name):'SN' }} </p>
                                    </div>
                                    <hr style="margin:0;">
                                </div>
                                <div class="col-md-4">
                                    <div class="panel-heading" style="border-bottom:0;">
                                        <h3 class="panel-title">Linea</h3>
                                    </div>
                                    <div class="panel-body" style="padding-top:0;">
                                        <p>{{ $item->line?strtoupper($item->line->name):'SN' }} </p>
                                    </div>
                                    <hr style="margin:0;">
                                </div>
                                <div class="col-md-4">
                                    <div class="panel-heading" style="border-bottom:0;">
                                        <h3 class="panel-title">Nombre Generico</h3>
                                    </div>
                                    <div class="panel-body" style="padding-top:0;">
                                        <p>{{ $item->nameGeneric }}</p>
                                    </div>
                                    <hr style="margin:0;">
                                </div>
                                <div class="col-md-4">
                                    <div class="panel-heading" style="border-bottom:0;">
                                        <h3 class="panel-title">Nombre Comercial</h3>
                                    </div>
                                    <div class="panel-body" style="padding-top:0;">
                                        <p>{{ $item->nameTrade }}</p>
                                    </div>
                                    <hr style="margin:0;">
                                </div>
                                <div class="col-md-4">
                                    <div class="panel-heading" style="border-bottom:0;">
                                        <h3 class="panel-title">Observación / Descripción</h3>
                                    </div>
                                    <div class="panel-body" style="padding-top:0;">
                                        <p>{{$item->observation??'Sin Detalles'}}</p>
                                    </div>
                                    <hr style="margin:0;">
                                </div>
                                <div class="col-md-4">
                                    <div class="panel-heading" style="border-bottom:0;">
                                        <h3 class="panel-title">Venta por Fracción</h3>
                                    </div>
                                    <div class="panel-body" style="padding-top:0;">
                                        @if ($item->fraction)
                                            <p>
                                                Cada unidad se puede vender en {{number_format($item->fractionQuantity, 2, ',', '.')}} {{strtoupper($item->fractionPresentation->name)}}.
                                                <br><small>El precio de venta por fracción se define al agregar stock.</small>
                                            </p>
                                        @else
                                            <p><label class="label label-danger">Venta sin Fracción</label></p>
                                        @endif
                                    </div>
                                    <hr style="margin:0;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <div class="panel panel-bordered">
                    <div class="panel-body" style="padding: 0px;">
                        <ul class="nav nav-pills nav-stacked">
                            <li class="active"><a data-toggle="tab" href="#tab-stock"><i class="fa-solid fa-boxes-stacked"></i> Inventario</a></li>
                            <li><a data-toggle="tab" href="#tab-dispensations"><i class="fa-solid fa-clock-rotate-left"></i> Dispensaciones</a></li>
                            <li><a data-toggle="tab" href="#tab-sales"><i class="fa-solid fa-cash-register"></i> Ventas Directas</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-10">
                <div class="tab-content">
                    <div id="tab-stock" class="tab-pane fade in active" style="padding-top: 0px">
                        <div class="panel panel-bordered">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h4>
                                            Detalles del Inventario
                                        </h4>
                                    </div>
                                    <div class="col-sm-6 text-right">
                                        @if (auth()->user()->hasPermission('browse_items'))
                                            <button class="btn btn-success"                                      
                                                data-target="#modal-register-stock" data-toggle="modal" data-toggle="modal" style="margin: 0px">
                                                <i class="fa-solid fa-plus"></i> Agregar                                  
                                            </button>       
                                        @endif                         
                                    </div>  
                                </div>

                                <div class="row">
                                    <div class="col-sm-7">
                                        <div class="dataTables_length" id="dataTable_length">
                                            <label>Mostrar <select id="select-paginate-stock" class="form-control input-sm">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select> registros</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-2" style="margin-bottom: 10px">
                                        <select id="status-stock" name="status-stock" class="form-control select2">
                                            <option value="" selected>Todos</option>
                                            <option value="1">Con Stock</option>
                                            <option value="0">Sin Stock</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3" style="margin-bottom: 0px">
                                        <input type="text" id="input-search-stock" placeholder="🔍 Buscar en lote..." class="form-control">
                                    </div>
                                </div>
                                <div class="row" id="div-results" style="min-height: 120px"></div>
                            </div>
                        </div>
                    </div>
                    <div id="tab-dispensations" class="tab-pane fade" style="padding-top: 0px">
                        <div class="panel panel-bordered">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <h4>
                                            <i class="fa-solid fa-clock-rotate-left"></i> Historial de Dispensaciones
                                        </h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-7">
                                        <div class="dataTables_length" id="dataTable_length">
                                            <label>Mostrar <select id="select-paginate-dispensations" class="form-control input-sm">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select> registros</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="div-results-dispensations" style="min-height: 120px"></div>
                            </div>
                        </div>
                    </div>
                    <div id="tab-sales" class="tab-pane fade" style="padding-top: 0px">
                        <div class="panel panel-bordered">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <h4>
                                            <i class="fa-solid fa-cash-register"></i> Historial de Ventas Directas
                                        </h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-7">
                                        <div class="dataTables_length" id="dataTable_length_direct_sales">
                                            <label>Mostrar <select id="select-paginate-direct-sales" class="form-control input-sm">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select> registros</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="div-results-direct-sales" style="min-height: 120px"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <form action="{{ route('items-stock.store', ['id' => $item->id]) }}" class="form-submit" method="POST">
        <div class="modal fade" data-backdrop="static" id="modal-register-stock" role="dialog">
            <div class="modal-dialog modal-lg modal-success">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" style="color: #ffffff !important"><i class="voyager-plus"></i> Registrar Stock</h4>
                    </div>
                    <div class="modal-body" style="background-color: #f5f5f5;">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default" style="box-shadow: none; border: 1px solid #ddd;">
                                    <div class="panel-body">
                                        <h5 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; font-weight: bold; color: #555;">
                                            <i class="fa-solid fa-box"></i> Datos del Lote
                                        </h5>
                                        <div class="row">
                                            <div class="form-group col-md-5">
                                                <label for="lote">Lote / Código</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa-solid fa-barcode"></i></span>
                                                    <input type="text" id="lote" name="lote" class="form-control" placeholder="Ej. L-2023-X">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="expirationDate">Fecha de Expiración</label>
                                                <input type="date" id="expirationDate" name="expirationDate" class="form-control" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="quantity">Cantidad</label>
                                                <input style="text-align: right" type="number" id="quantity" step="1" min="1" name="quantity" class="form-control" required placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default" style="box-shadow: none; border: 1px solid #ddd;">
                                    <div class="panel-body">
                                        <h5 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; font-weight: bold; color: #555;">
                                            <i class="fa-solid fa-money-bill-trend-up"></i> Precios y Costos
                                        </h5>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="pricePurchase">P. Compra (por Unidad)</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">Bs.</span>
                                                    <input style="text-align: right" type="number" id="pricePurchase" step="0.01" min="0" name="pricePurchase" class="form-control" required placeholder="0.00">
                                                </div>
                                            </div> 
                                            <div class="form-group col-md-6">
                                                <label for="priceSale">P. Venta (por Unidad)</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">Bs.</span>
                                                    <input style="text-align: right" type="number" id="priceSale" step="0.01" min="0.1" name="priceSale" class="form-control" required placeholder="0.00">
                                                </div>
                                                <div id="unit-profit-info" class="alert alert-info" style="padding: 5px 10px; margin-top: 5px; margin-bottom: 0; font-size: 12px; display: none;">
                                                    <!-- La información de la ganancia se mostrará aquí -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($item->fraction)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-warning" style="box-shadow: none; border: 1px solid #f39c12;">
                                    <div class="panel-heading" style="background-color: #fcf8e3; color: #8a6d3b; padding: 10px 15px; border-bottom: 1px solid #faebcc;">
                                        <h3 class="panel-title" style="font-size: 14px; font-weight: bold;">
                                            <i class="fa-solid fa-puzzle-piece"></i> Venta Fraccionada
                                        </h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-7">
                                                <p style="margin-top: 5px; font-size: 13px;">
                                                    Este producto se divide en <b>{{number_format($item->fractionQuantity, 2, ',', '.')}} {{strtoupper($item->fractionPresentation->name)}}</b>.
                                                    <br><small class="text-muted">Defina el precio por cada fracción.</small>
                                                </p>
                                            </div>
                                            <div class="form-group col-md-5">
                                                <label for="dispensedPrice">Precio por {{strtoupper($item->fractionPresentation->name)}}</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">Bs.</span>
                                                    <input style="text-align: right" type="number" id="dispensedPrice" step="0.1" min="0.1" name="dispensedPrice" class="form-control" required placeholder="0.00">
                                                </div>
                                                <div id="fraction-profit-info" class="alert alert-info" style="padding: 5px 10px; margin-top: 5px; margin-bottom: 0; font-size: 12px; display: none;">
                                                    <!-- La información de la ganancia por fracción se mostrará aquí -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="observation">Observación / Detalles</label>
                                    <textarea id="observation" name="observation" class="form-control" rows="2" placeholder="Observaciones opcionales..."></textarea>
                                </div>
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label><input type="checkbox" required> Confirmar registro de stock</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <input type="submit" class="btn btn-success btn-form-submit" value="Guardar">
                    </div>
                </div>
            </div>
        </div>

    </form>
    @include('partials.modal-delete')
    
@stop

@section('css')
    <style>

    </style>
@stop

@section('javascript')
    <script src="{{ url('js/main.js') }}"></script>


    <script>
        var countPage = 10, order = 'id', typeOrder = 'desc';
        var timeout = null;
        $(document).ready(() => {
            list();
            listDispensations();
            listSales();

            $('#status-stock').change(function(){
                list();
            });
   
            $('#input-search-stock').on('keyup', function(e){
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    list();
                }, 1000); // 1 segundo de espera
            });

            $('#select-paginate-stock').change(function(){
                countPage = $(this).val();
                list();
            });

            $('#input-search-stock').on('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    list();
                }, 2000); // retardo de 2 segundos cada vez que se escribe algo en el input
            });

            // __________________________________________________________________________
            $('#select-paginate-dispensations').change(function(){
                countPage = $(this).val();
                listDispensations();
            });

            // __________________________________________________________________________

            $('#select-paginate-direct-sales').change(function(){
                countPage = $(this).val();
                listSales();
            });

            // Validación de fecha de expiración
            $('#expirationDate').on('change', function(){
                let date = $(this).val();
                if(date){
                    let today = new Date();
                    today.setHours(0,0,0,0);
                    let parts = date.split('-');
                    let selectedDate = new Date(parts[0], parts[1]-1, parts[2]);
                    
                    if(selectedDate <= today){
                        toastr.warning('La fecha de expiración debe ser mayor a la fecha actual.', 'Advertencia');
                        $(this).val('');
                    }
                }
            });

            // Validación para que el precio de venta no sea menor al de compra
            $('#priceSale').on('change', function(){
                let purchasePrice = parseFloat($('#pricePurchase').val()) || 0;
                let salePrice = parseFloat($(this).val()) || 0;
                
                if(purchasePrice > 0 && salePrice > 0){
                    if(salePrice < purchasePrice){
                        toastr.error('El precio de venta genera pérdidas.', 'Error');
                        $(this).val('');
                    }
                }
            });
        });

        function list(page = 1){
            $('#div-results').loading({message: 'Cargando...'});
            let url = '{{ url("admin/items/".$item->id."/stock/ajax/list") }}';
            let search = $('#input-search-stock').val() ? $('#input-search-stock').val() : '';
            let status =$("#status-stock").val();        
            // alert(1)

            $.ajax({
                url: `${url}?search=${search}&paginate=${countPage}&page=${page}&status=${status}`,
                // url: `${url}?paginate=${countPage}&page=${page}&status=${status}`,

                type: 'get',
                
                success: function(result){
                    $("#div-results").html(result);
                    $('#div-results').loading('toggle');
                }
            });
        }

        function listDispensations(page = 1){
            $('#div-results-dispensations').loading({message: 'Cargando...'});
            let url = '{{ url("admin/items/".$item->id."/dispensations/ajax/list") }}';

            $.ajax({
                url: `${url}?paginate=${countPage}&page=${page}`,

                type: 'get',
                success: function(result){
                    $("#div-results-dispensations").html(result);
                    $('#div-results-dispensations').loading('toggle');
                }
            });
        }

        function listSales(page = 1){
            $('#div-results-direct-sales').loading({message: 'Cargando...'});
            let url = '{{ url("admin/items/".$item->id."/sales/ajax/list") }}';

            $.ajax({
                url: `${url}?paginate=${countPage}&page=${page}`,

                type: 'get',
                success: function(result){
                    $("#div-results-direct-sales").html(result);
                    $('#div-results-direct-sales').loading('toggle');
                }
            });
        }


        $(document).ready(function(){   
            $('.form-submit').submit(function(e){
                $('.btn-form-submit').attr('disabled', true);
                $('.btn-form-submit').val('Guardando...');
            });

            $('#delete_form').submit(function(e){
                $('.btn-form-delete').attr('disabled', true);
                $('.btn-form-delete').val('Eliminando...');
            });
        });

        function deleteItem(url){
            $('#delete_form').attr('action', url);
        }

        @if ($item->fraction)
            // Calcular ganancia de venta por fracción
            $(document).ready(function(){
                const fractionQuantity = {{ $item->fractionQuantity }};
                
                function calculateProfits() {
                    let purchasePrice = parseFloat($('#pricePurchase').val());
                    let dispensedPrice = parseFloat($('#dispensedPrice').val());
                    let salePrice = parseFloat($('#priceSale').val());

                    // Ganancia por Venta Unitaria
                    if (!isNaN(purchasePrice) && purchasePrice > 0 && !isNaN(salePrice) && salePrice > 0) {
                        let profit = salePrice - purchasePrice;
                        let profitPercentage = (profit / purchasePrice) * 100;
                        let infoHtml = `Ganancia: <b class="text-dark">${profit.toFixed(2)} Bs. (${profitPercentage.toFixed(2)}%)</b>`;
                        $('#unit-profit-info').html(infoHtml).show();
                    } else {
                        $('#unit-profit-info').hide();
                    }

                    // Ganancia por Venta Fraccionada
                    if (!isNaN(purchasePrice) && purchasePrice > 0 && !isNaN(dispensedPrice) && dispensedPrice > 0) {
                        let totalSale = fractionQuantity * dispensedPrice;
                        let profit = totalSale - purchasePrice;
                        let profitPercentage = (profit / purchasePrice) * 100;

                        let infoHtml = `
                            Venta Total: <b class="text-dark">${totalSale.toFixed(2)} Bs.</b> <br>
                            Ganancia: <b class="text-dark">${profit.toFixed(2)} Bs. (${profitPercentage.toFixed(2)}%)</b>
                        `;
                        
                        $('#fraction-profit-info').html(infoHtml).show();
                    } else {
                        $('#fraction-profit-info').hide();
                    }
                }

                $('#pricePurchase, #dispensedPrice, #priceSale').on('keyup change', function(){
                    calculateProfits();
                });

                // Validación para que el precio fraccionado no genere pérdidas
                $('#dispensedPrice').on('change', function(){
                    let purchasePrice = parseFloat($('#pricePurchase').val()) || 0;
                    let dispensedPrice = parseFloat($(this).val()) || 0;

                    if(purchasePrice > 0 && dispensedPrice > 0){
                        if((dispensedPrice * fractionQuantity) < purchasePrice){
                            toastr.error('El precio fraccionado genera pérdidas. Debe ser mayor para obtener ganancia.', 'Precio Inválido');
                            $(this).val('');
                            calculateProfits();
                        }
                    }
                });
            });
        @endif



    </script>
    
@stop