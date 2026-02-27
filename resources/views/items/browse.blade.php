@extends('voyager::master')

@section('page_title', 'Viendo Items')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body" style="padding: 0px">
                        <div class="col-md-7" style="padding: 0px">
                            <h1 class="page-title">
                                <i class="fa-solid fa-cart-shopping"></i> Productos / Items
                            </h1>
                        </div>
                        <div class="col-md-5 text-right" style="margin-top: 30px">
                            @if ($lowStockCount > 0)
                            <button id="btn-stock-bajo" onclick="filterStockBajo()" class="btn btn-danger"
                                    title="Ver productos con stock por debajo del mínimo">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span>Stock Bajo</span>
                                <span class="badge" style="background:#fff; color:#c0392b; font-weight:700;">
                                    {{ $lowStockCount }}
                                </span>
                            </button>
                            @endif
                            <a href="{{ route('items.expiry') }}" class="btn btn-warning">
                                <i class="fa-solid fa-triangle-exclamation"></i> <span>Vencimientos</span>
                            </a>
                            @if (auth()->user()->hasPermission('add_people'))
                            <a href="{{ route('voyager.items.create') }}" class="btn btn-success">
                                <i class="voyager-plus"></i> <span>Crear</span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        {{-- Alerta banner de stock bajo --}}
                        @if ($lowStockCount > 0)
                        <div id="alert-stock-bajo" class="alert alert-danger" style="margin-bottom:15px; cursor:pointer; border-left: 5px solid #c0392b;"
                             onclick="filterStockBajo()">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <strong>{{ $lowStockCount }} producto(s)</strong> tienen stock por debajo del mínimo configurado.
                            <span style="float:right; font-size:12px; margin-top:2px;">
                                <i class="fa-solid fa-filter"></i> Clic para filtrar
                            </span>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-sm-2">
                                <div class="dataTables_length" id="dataTable_length">
                                    <label>Mostrar <select id="select-paginate" class="form-control input-sm">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select> registros</label>
                                </div>
                            </div>
                            <div class="col-sm-2" style="margin-bottom: 10px">
                                <select id="category" name="category" class="form-control select2">
                                    <option value="" selected>Todas las categorías</option>
                                    @foreach ($categories as $item)
                                        <option value="{{$item->category_id}}">{{$item->category->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-2" style="margin-bottom: 10px">
                                <select id="laboratory" name="laboratory" class="form-control select2">
                                    <option value="" selected>Todos los laboratorios</option>
                                    @foreach ($laboratories as $item)
                                        <option value="{{$item->laboratory_id}}">{{$item->laboratory->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2" style="margin-bottom: 10px">
                                <select id="status" name="status" class="form-control select2">
                                    <option value="" selected>Todos</option>
                                    <option value="1">Con Stock</option>
                                    <option value="0">Sin Stock</option>
                                </select>
                            </div>

                            <div class="col-sm-4" style="margin-bottom: 10px">
                                <input type="text" id="input-search" placeholder="🔍 Buscar..." class="form-control">
                            </div>
                        </div>
                        <div class="row" id="div-results" style="min-height: 120px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    @include('partials.modal-delete')


@stop

@section('css')
    <style>
        #alert-stock-bajo:hover {
            opacity: 0.85;
        }
        #btn-stock-bajo.active-filter {
            box-shadow: 0 0 0 3px rgba(192,57,43,0.4);
        }
    </style>
@stop

@section('javascript')
    <script src="{{ url('js/main.js') }}"></script>
    <script>
        var countPage = 10, order = 'id', typeOrder = 'desc';
        var timeout = null;
        var stockBajoFilter = 0; // 0 = todos, 1 = solo stock bajo

        $(document).ready(() => {
            list();
            $('#category').change(function(){ list(); });
            $('#laboratory').change(function(){ list(); });
            $('#status').change(function(){ list(); });

            $('#input-search').on('keyup', function(e){
                if(e.keyCode == 13) {
                    clearTimeout(timeout);
                    list();
                }
            });

            $('#select-paginate').change(function(){
                countPage = $(this).val();
                list();
            });

            $('#input-search').on('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    list();
                }, 2000);
            });
        });

        function filterStockBajo() {
            stockBajoFilter = stockBajoFilter == 1 ? 0 : 1;
            $('#btn-stock-bajo').toggleClass('active-filter', stockBajoFilter == 1);
            if (stockBajoFilter == 1) {
                $('#btn-stock-bajo').html('<i class="fa-solid fa-circle-exclamation"></i> Stock Bajo <span class="badge" style="background:#fff;color:#c0392b;font-weight:700;">{{ $lowStockCount }}</span>');
                $('#alert-stock-bajo').hide();
            } else {
                $('#alert-stock-bajo').show();
            }
            list();
        }

        function list(page = 1){
            $('#div-results').loading({message: 'Cargando...'});
            let url = '{{ url("admin/items/ajax/list") }}';
            let search = $('#input-search').val() || '';
            let laboratory = $("#laboratory").val();
            let category = $("#category").val();
            let status = $("#status").val();

            $.ajax({
                url: `${url}?search=${search}&paginate=${countPage}&page=${page}&laboratory=${laboratory}&category=${category}&status=${status}&stockBajo=${stockBajoFilter}`,
                type: 'get',
                success: function(result){
                    $("#div-results").html(result);
                    $('#div-results').loading('toggle');
                }
            });
        }

        function deleteItem(url){
            $('#delete_form').attr('action', url);
        }
    </script>
@stop
