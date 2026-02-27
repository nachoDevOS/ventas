@extends('voyager::master')

@section('page_title', 'Cierre de caja')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body" style="padding: 0px">
                        <div class="col-md-8" style="padding: 0px">
                            <h1 class="page-title">
                                <i class="voyager-lock"></i> Cierre de caja
                            </h1>
                        </div>
                        <div class="col-md-4" style="margin-top: 30px">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="row">
                        @php
                            $amountCashier = ($globalFuntion_cashierMoney['cashierIn'] + $globalFuntion_cashierMoney['paymentEfectivo']) - $globalFuntion_cashierMoney['paymentEfectivoEgreso'];

                        @endphp
                        <div class="col-md-6" style="height: 650px; overflow-y: auto">
                            <form name="form_close" class="form-edit-add" id="form-close" action="{{ route('cashiers.close.store', ['cashier' => $cashier->id]) }}" method="post">
                                @csrf
                                <input type="hidden" name="amount_cashier" value="{{ $amountCashier }}">
                                <input type="hidden" name="amount_real">
                                <table id="dataStyle" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Corte</th>
                                            <th>Cantidad</th>
                                            <th style="width: 200px">Sub Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="lista_cortes"></tbody>
                                </table>

                                {{-- confirm modal --}}
                                <div class="modal modal-danger fade" tabindex="-1" id="close_modal" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close btn-cancel" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title"><i class="voyager-lock"></i> Confirme que desea cerrar la caja?</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>Esta acción cerrará la caja y no podrá realizar modificaciones posteriores</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-danger btn-submit">Sí, cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 div-details" style="padding-top: 10px">
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Dinero Asignado a caja por el Administrador</p>
                                </div>
                                <div class="col-md-6">
                                    <h3 class="text-right" style="padding-right: 20px">{{ number_format($globalFuntion_cashierMoney['cashierIn'], 2, ',', '.') }}</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Ingresos por cobros en efectivo</p>
                                </div>
                                <div class="col-md-6">
                                    <h3 class="text-right" style="padding-right: 20px">{{number_format($globalFuntion_cashierMoney['paymentEfectivo'], 2, ',', '.') }}</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Ingresos por cobros en Qr</p>
                                </div>
                                <div class="col-md-6">
                                    <h3 class="text-right" style="padding-right: 20px">{{ number_format($globalFuntion_cashierMoney['paymentQr'], 2, ',', '.') }}</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Egreso por Efectivo</p>
                                </div>
                                <div class="col-md-6">
                                    <h3 class="text-right" style="padding-right: 20px">{{ number_format($globalFuntion_cashierMoney['paymentEfectivoEgreso'], 2, ',', '.') }}</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Egreso por Qr</p>
                                </div>
                                <div class="col-md-6">
                                    <h3 class="text-right" style="padding-right: 20px">{{ number_format($globalFuntion_cashierMoney['paymentQrEgreso'], 2, ',', '.') }}</h3>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Total</p>
                                </div>
                                <div class="col-md-6">
                                    <h3 class="text-right" style="padding-right: 20px">{{ number_format($amountCashier, 2, ',', '.') }}</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Monto a entregar en efectivo</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="panel-heading" style="border-bottom:0;">
                                        <h3 class="text-right" style="padding-right: 20px" id="label-total">0,00</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Monto sobrante</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="panel-heading" style="border-bottom:0;">
                                        <h3 class="text-right" style="padding-right: 20px" id="label-plus_amount">0,00</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Monto faltante</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="panel-heading" style="border-bottom:0;">
                                        <h3 class="text-right" style="padding-right: 20px" id="label-missing_amount">0,00</h3>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-danger btn-block btn-confirm" data-toggle="modal" data-target="#close_modal">Cerrar caja <i class="voyager-lock"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .div-details .col-md-6{
            margin-bottom: 0px
        }
    </style>
@stop

@section('javascript')

    <script>
        const APP_URL = '{{ url('') }}';
    </script>
    <script src="{{ asset('js/cash_value.js') }}"></script>
    <script src="{{ asset('js/btn-submit.js') }}"></script>    

    <script>
        $(document).ready(function() {
            window.addEventListener("keypress", function(event){
                if (event.keyCode == 13){
                    event.preventDefault();
                }
            }, false);


            
            $('.input-corte').keyup(function(){
                getMissingAmount()
            });
            $('.input-corte').change(function(){
                getMissingAmount()
            });
        });

        function getMissingAmount(){
            let total = parseFloat("{{ $amountCashier }}");
            let total_cashier = parseFloat($('#label-total').text());
            let missing_amount = total - total_cashier;
            let plus_amount = total_cashier - total;

            $('#form-close input[name="amount_real"]').val(total_cashier);

            $('#label-missing_amount').text(missing_amount > 0 ? missing_amount.toFixed(2) : 0);
            $('#label-plus_amount').text(plus_amount > 0 ? plus_amount.toFixed(2) : 0);
            if(missing_amount > 0){
                $('#label-missing_amount').addClass('text-danger');
            }else{
                $('#label-missing_amount').removeClass('text-danger');                
            }
            // if(total == total_cashier){
            //     $('.btn-confirm').removeAttr('disabled');
            // }else{
            //     $('.btn-confirm').attr('disabled', 'disabled');
            // }
            plus_amount > 0 ? $('#label-plus_amount').addClass('text-success') : $('#label-plus_amount').removeClass('text-success');
        }
    </script>
@stop
