@extends('voyager::master')

@section('page_title', 'Confimar cierre de caja')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body" style="padding: 0px">
                        <div class="col-md-8" style="padding: 0px">
                            <h1 class="page-title">
                                <i class="voyager-lock"></i> Confimar cierre de caja
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
                            $cashierIn = $cashier->movements->where('type', 'Ingreso')->where('deleted_at', NULL)->where('status', 'Aceptado')->sum('amount');
                            $cashierOut = $cashier->expenses->where('deleted_at', null)->sum('amount');

                            $paymentEfectivoSale = $cashier->sales->where('deleted_at', null)
                                ->flatMap(function($q) {
                                    return $q->saleTransactions->where('paymentType', 'Efectivo')->pluck('amount');
                                })                
                                ->sum();

                            $paymentEfectivo = $paymentEfectivoSale;


                            // #####################################
                            $paymentQrSale = $cashier->sales->where('deleted_at', null)
                                ->flatMap(function($q) {
                                    return $q->saleTransactions->where('paymentType', 'Qr')->pluck('amount');
                                })
                                ->sum();

                            $paymentQr = $paymentQrSale;
                            


                            //#######################################################       EGRESO       ################################

                            $paymentEfectivoExpenses = $cashier->expenses->where('deleted_at', null)
                                ->flatMap(function($q) {
                                    return $q->expenseTransactions->where('paymentType', 'Efectivo')->pluck('amount');
                                })                
                                ->sum();

                            $paymentEfectivoEgreso = $paymentEfectivoExpenses;

                            // Qr.....................................................................

                            $paymentQrExpenses = $cashier->expenses->where('deleted_at', null)
                                ->flatMap(function($q) {
                                    return $q->expenseTransactions->where('paymentType', 'Qr')->pluck('amount');
                                })                
                                ->sum();

                            $paymentQrEgreso = $paymentQrExpenses;

                            
                            
                            $amountCashier = ($cashierIn + $paymentEfectivo) - $paymentEfectivoEgreso;
                        @endphp
                        <div class="col-md-6">
                            <form name="form_close" class="form-edit-add" action="{{ route('cashiers.confirm_close.store', ['cashier' => $cashier->id]) }}" method="post">
                                @csrf
                                <table id="dataStyle" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Corte</th>
                                            <th>Cantidad</th>
                                            <th>Sub Total</th>
                                        </tr>
                                    </thead>
                                    @php
                                        // $cash = ['200', '100', '50', '20', '10', '5', '2', '1', '0.5', '0.2', '0.1'];
                                        $cash = ['200', '100', '50', '20', '10', '5', '2', '1', '0.5'];
        
                                    @endphp
                                    <tbody>
                                        @foreach ($cash as $item)
                                        <tr>
                                            <td><h4 style="margin: 0px"><img src=" {{ url('images/cash/'.$item.'.jpg') }} " alt="{{ $item }} Bs." width="70px"> {{ $item }} Bs. </h4></td>
                                            <td>
                                                @php                                                    
                                                    // 1. Encontrar el detalle de cierre
                                                    $close_detail = $cashier->details->where('type', 'Cierre')->first();
                                                    // 2. Encontrar el corte de billete específico dentro de los detalles del cierre
                                                    $cash_detail = $close_detail ? $close_detail->detailCashes->where('cash_value', $item)->first() : null;
                                                    $quantity = $cash_detail ? $cash_detail->quantity : 0;
                                                @endphp
                                                {{ $quantity }}
                                            </td>
                                            <td>
                                                {{ number_format($quantity * $item, 2, ',', '.') }}
                                                <input type="hidden" name="cash_value[]" value="{{ $item }}">
                                                <input type="hidden" name="quantity[]" value="{{ $quantity }}">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                {{-- confirm modal --}}
                                <div class="modal modal-danger fade" id="close_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
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
                        <div class="col-md-6 div-details" style="padding-top: 20px">
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Dinero Asignado a caja por el Administrador</p>
                                </div>
                                <div class="col-md-6">
                                    <h3 class="text-right" style="padding-right: 20px">{{ number_format($cashierIn, 2, ',', '.') }}</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Ingresos por cobros en efectivo</p>
                                </div>
                                <div class="col-md-6">
                                    <h3 class="text-right" style="padding-right: 20px">{{ number_format($paymentEfectivo, 2, ',', '.') }}</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Ingresos por cobros en Qr</p>
                                </div>
                                <div class="col-md-6">
                                    <h3 class="text-right" style="padding-right: 20px">{{ number_format($paymentQr, 2, ',', '.') }}</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Egreso por Efectivo</p>
                                </div>
                                <div class="col-md-6">
                                    <h3 class="text-right" style="padding-right: 20px">{{ number_format($paymentEfectivoEgreso, 2, ',', '.') }}</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Egreso por Efectivo</p>
                                </div>
                                <div class="col-md-6">
                                    <h3 class="text-right" style="padding-right: 20px">{{ number_format($paymentQrEgreso, 2, ',', '.') }}</h3>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Total en Caja</p>
                                </div>
                                <div class="col-md-6">
                                    <h3 class="text-right" style="padding-right: 20px">{{ number_format($amountCashier, 2, ',', '.') }}</h3>
                                </div>
                            </div>
                            <hr>
                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Total entregado</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="panel-heading" style="border-bottom:0;">
                                        <h3 class="text-right" style="padding-right: 20px">{{ number_format($cashier->amountClosed, 2, ',', '.') }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Monto Sobrante</p>
                                </div>
                                <div class="col-md-6">
                                    <h3 class="text-right" style="padding-right: 20px; @if($cashier->amountLeftover > 0) color: #28a745 !important; @endif">{{ number_format($cashier->amountLeftover, 2, ',', '.') }}</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Monto Faltante</p>
                                </div>
                                <div class="col-md-6">
                                    <h3 class="text-right" style="padding-right: 20px; @if($cashier->amountMissing > 0) color: #dc3545 !important; @endif">{{ number_format($cashier->amountMissing, 2, ',', '.') }}</h3>
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

        });
    </script>
@stop
