
@extends('voyager::master')

@section('page_title', 'Ver Productos en Ventas')

@section('page_header')
    <h1 class="page-title">
        <i class="fa-solid fa-cart-shopping"></i> Ventas &nbsp;
        <a href="{{ route('sales.index') }}" class="btn btn-warning">
            <i class="voyager-list"></i> <span class="hidden-xs hidden-sm">Volver a la lista</span>
        </a> 
        {{-- <button class="btn btn-danger" onclick="window.print()">
            <i class="voyager-polaroid"></i> <span class="hidden-xs hidden-sm">Imprimir Ticket</span>
        </button> --}}
    </h1>
@stop

@section('content')
    <div id="sale-details" class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h4 class="details-title">Detalles del Cliente</h4>
                                        <dl class="dl-horizontal">
                                            <dt>Cliente:</dt>
                                            <dd>{{ $sale->person_id ? $sale->person->first_name . ' ' . $sale->person->middle_name . ' ' . $sale->person->paternal_surname . ' ' . $sale->person->maternal_surname : 'Sin Cliente' }}</dd>
                                            <dt>CI/NIT:</dt>
                                            <dd>{{ $sale->person_id ? $sale->person->ci : 'S/N' }}</dd>
                                            <dt>Dirección:</dt>
                                            <dd>{{ $sale->person_id ? ($sale->person->address ?: 'No registrada') : 'No registrada' }}</dd>
                                        </dl>
                                    </div>
                                    <div class="col-sm-6">
                                        <h4 class="details-title">Detalles de la Venta</h4>
                                        <dl class="dl-horizontal">
                                            <dt>Tipo:</dt>
                                            <dd>Para {{ $sale->typeSale }}</dd>
                                            <dt>Fecha:</dt>
                                            <dd>{{ date('d/m/Y h:i:s a', strtotime($sale->dateSale)) }}</dd>
                                            <dt>Observación:</dt>
                                            <dd>{{ $sale->observation ?: 'Ninguna' }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="summary-card-container">
                                    <div class="summary-card">
                                        <i class="voyager-wallet"></i>
                                        <p>Total Venta</p>
                                        <h2>Bs. {{ number_format($sale->amount, 2, ',', '.') }}</h2>
                                    </div>
                                    <div class="summary-card">
                                        <i class="voyager-dollar"></i>
                                        <p>Monto Recibido</p>
                                        <h2>Bs. {{ number_format($sale->amountReceived, 2, ',', '.') }}</h2>
                                    </div>
                                    <div class="summary-card">
                                        <i class="voyager-refresh"></i>
                                        <p>Cambio</p>
                                        <h2>Bs. {{ number_format($sale->amountChange, 2, ',', '.') }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-list"></i> Detalles de la Venta</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">N&deg;</th>
                                        <th>Artículo</th>
                                        <th style="text-align: center; width: 12%">Tipo</th>
                                        <th style="text-align: right; width: 10%">Precio</th>
                                        <th style="text-align: center; width: 10%">Cantidad</th>
                                        <th style="text-align: right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                        $amountTotal = 0;
                                    @endphp
                                    @forelse ($sale->saleDetails as $value)
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>{{strtoupper($value->itemStock->item->nameGeneric)}} {{ $value->itemStock->item->nameTrade? '  |  '.strtoupper($value->itemStock->item->nameTrade):null }}</td>
                                            <td style="text-align: center">
                                                @if ($value->dispensed == 'Entero')
                                                    Unidades
                                                @else
                                                    Fracciones
                                                @endif
                                            </td>
                                            <td style="text-align: right">
                                                {{ number_format($value->price, 2, ',', '.') }}
                                            </td>
                                            <td style="text-align: center">
                                                {{ number_format($value->quantity, 2, ',', '.') }}
                                                @if ($value->dispensed == 'Entero')
                                                    {{$value->itemStock->item->presentation->name}}
                                                @else
                                                    {{$value->itemStock->item->fractionPresentation->name}}
                                                @endif
                                            </td>
                                            <td style="text-align: right; font-weight: bold;">{{ number_format($value->amount, 2, ',', '.') }}</td>
                                        </tr>
                                        @php
                                            $i++;
                                            $amountTotal += $value->amount;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No hay detalles registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="total-row">
                                        <td colspan="5" style="text-align: right;">TOTAL Bs.</td>
                                        <td style="text-align: right; font-weight: bold;">{{ number_format($amountTotal, 2, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-credit-cards"></i> Métodos de Pago</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Método de Pago</th>
                                        <th style="text-align: right">Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $amountTotalPayment = 0;
                                    @endphp
                                    @forelse ($sale->saleTransactions as $value)
                                        <tr>
                                            <td>{{ $value->paymentType }}</td>
                                            <td style="text-align: right">{{ number_format($value->amount, 2, ',', '.') }}</td>
                                        </tr>
                                        @php
                                            $amountTotalPayment += $value->amount;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center">No hay pagos registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="total-row">
                                        <td style="text-align: right;">TOTAL</td>
                                        <td style="text-align: right; font-weight: bold;">Bs. {{ number_format($amountTotalPayment, 2, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


  
    {{-- @include('partials.modal-delete') --}}
    
@stop

@section('css')
<style>
    .panel-body .details-title {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 10px;
        margin-top: 0;
    }
    .dl-horizontal dt {
        font-weight: normal;
        color: #777;
        white-space: normal;
    }
    .dl-horizontal dd {
        margin-left: 120px; /* Ajusta según sea necesario */
        font-weight: 600;
        color: #333;
    }
    .summary-card-container {
        display: flex;
        justify-content: space-around;
        height: 100%;
        align-items: center;
    }
    .summary-card {
        background-color: #f9f9f9;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        flex: 1;
        margin: 0 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .summary-card i {
        font-size: 24px;
        color: #3498db;
        margin-bottom: 10px;
    }
    .summary-card p {
        margin: 0;
        color: #777;
        font-size: 12px;
    }
    .summary-card h2 {
        margin: 5px 0 0 0;
        font-size: 22px;
        font-weight: 600;
        color: #333;
    }
    .panel-heading .panel-title {
        font-size: 18px;
        font-weight: 400;
    }
    .table tfoot .total-row {
        background-color: #f9f9f9;
        font-size: 1.1em;
    }

    /* Estilos para impresión */
    @media print {
        body * {
            visibility: hidden;
        }
        #sale-details, #sale-details * {
            visibility: visible;
        }
        #sale-details {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .page-title, .page-content.read .row:first-child .col-md-12 > .panel .panel-body .row .col-md-8 {
            width: 100% !important;
        }
        .page-content.read .row:first-child .col-md-12 > .panel .panel-body .row .col-md-4 {
            display: none;
        }
        .page-title a, .page-title button {
            display: none;
        }
        .panel {
            border: none !important;
            box-shadow: none !important;
        }
        .table {
            font-size: 10px;
        }
        .dl-horizontal dd {
            font-size: 12px;
        }
    }
</style>
@stop

@section('javascript')
@stop
