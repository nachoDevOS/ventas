@extends('voyager::master')

@section('page_title', 'Viendo Ventas')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body" style="padding: 0px">
                        <div class="col-md-8" style="padding: 0px">
                            <h1 class="page-title">
                                <i class="fa-solid fa-cart-plus"></i> Ventas
                            </h1>
                        </div>
                        <div class="col-md-4 text-right" style="margin-top: 30px">
                            @if (auth()->user()->hasPermission('add_sales'))
                            <a href="{{ route('sales.create') }}" class="btn btn-success">
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
                        {{-- Select oculto requerido por el JS --}}
                        <select id="typeSale" style="display: none;">
                            <option value="">— Todos los tipos —</option>
                            <option value="Venta al Contado">Venta al Contado</option>
                            <option value="Venta al Credito">Venta al Crédito</option>
                            <option value="Proforma">Proforma</option>
                        </select>

                        <div class="row">
                            <div class="col-sm-9">
                                <div class="dataTables_length" id="dataTable_length">
                                    <label>Mostrar <select id="select-paginate" class="form-control input-sm">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select> registros</label>
                                </div>
                            </div>
                            <div class="col-sm-3" style="margin-bottom: 10px">
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

    
    </style>
@stop

@section('javascript')
    <script src="{{ asset('js/btn-submit.js') }}"></script>    
    <script src="{{ url('js/main.js') }}"></script>
        
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> --}}
    <script>
        var countPage = 10, order = 'id', typeOrder = 'desc';
        $(document).ready(() => {
            list();
            $('#typeSale').change(function(){
                list();
            });
            
            $('#input-search').on('keyup', function(e){
                if(e.keyCode == 13) {
                    list();
                }
            });

            $('#select-paginate').change(function(){
                countPage = $(this).val();
               
                list();
            });
        });

        function list(page = 1){
            $('#div-results').loading({message: 'Cargando...'});

            let url = '{{ url("admin/sales/ajax/list") }}';
            let search = $('#input-search').val() ? $('#input-search').val() : '';
            let typeSale =$("#typeSale").val();

            $.ajax({
                url: `${url}?search=${search}&paginate=${countPage}&page=${page}&typeSale=${typeSale}`,

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