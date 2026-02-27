@extends('voyager::master')

@section('page_header')

    @php
        $meses = [
            '',
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre',
        ];
    @endphp

    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body" style="overflow: visible;">
                        <div class="row">
                            <div class="col-md-8">
                                <h2>Hola, {{ Auth::user()->name }}</h2>
                                <p class="text-muted">Resumen de rendimiento -
                                    {{ date('d') . ' de ' . $meses[intval(date('m'))] . ' ' . date('Y') }}</p>
                            </div>
                            <div class="col-md-4 text-right">
                                {{-- <a href="{{ route('proformas.index') }}" class="btn btn-danger" style="margin-right: 10px;">
                                    <i class="fa-solid fa-file-invoice-dollar"></i> Proforma
                                </a> --}}
                                <div class="btn-group">                              
                                    <div id="status" style="display: inline-block; margin-right: 10px;">
                                        <span>Obteniendo estado...</span>
                                    </div>
                                </div>
                                {{-- <div class="btn-group">
                                    <button type="button" class="btn btn-primary" id="filter-button">
                                        <i class="voyager-refresh"></i> Todo
                                    </button>
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu" id="filter-menu">
                                        <li><a href="#" data-range="Todo">Todo</a></li>
                                        <li><a href="#" data-range="Desayuno">Desayuno</a></li>
                                        <li><a href="#" data-range="Almuerzo">Almuerzo</a></li>
                                        <li><a href="#" data-range="Cena">Cena</a></li>
                                    </ul>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    
    <div class="page-content container-fluid">
        @include('voyager::alerts')
        @include('voyager::dimmers')

        <!-- KPI Cards -->
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('sales.index') }}" class="panel panel-bordered dashboard-kpi" style="display: block; color: inherit; text-decoration: none;">
                    <div class="panel-body">
                        <div class="kpi-icon" style="background-color: rgba(80,227,194,0.12);">
                            <i class="fa-solid fa-hand-holding-dollar" style="color: #27ae60;"></i>
                        </div>
                        <div class="kpi-content">
                            <p class="kpi-label">Ventas del Día</p>
                            <h3 class="kpi-value">Bs. {{ number_format($global_index['amountDaytotal'], 2, ',', '.') }}</h3>
                            <small class="text-muted">{{ $global_index['saleDaytotalCount'] }} {{ $global_index['saleDaytotalCount'] == 1 ? 'transacción' : 'transacciones' }}</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="panel panel-bordered dashboard-kpi">
                    <div class="panel-body">
                        <div class="kpi-icon">
                            <i class="fa-regular fa-bell"></i>
                        </div>
                        <div class="kpi-content">
                            <p class="kpi-label">Recordatorios de Hoy</p>
                            <h3 class="kpi-value">{{ $global_index['reminder'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('voyager.people.index') }}" class="panel panel-bordered dashboard-kpi" style="display: block; color: inherit; text-decoration: none;">
                    <div class="panel-body">
                        <div class="kpi-icon">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div class="kpi-content">
                            <p class="kpi-label">Clientes</p>
                            <h3 class="kpi-value">{{ $global_index['customer'] }}</h3>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="panel panel-bordered dashboard-kpi">
                    <div class="panel-body">
                        <div class="kpi-icon" style="background-color: rgba(255,193,7,0.12);">
                            <i class="fa-solid fa-cake-candles" style="color: #e67e22;"></i>
                        </div>
                        <div class="kpi-content">
                            <p class="kpi-label">Cumpleaños de Hoy</p>
                            <h3 class="kpi-value">{{ $global_index['todayBirthdaysCount'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($globalFuntion_cashier)
            @if ($globalFuntion_cashier->status == 'Abierta' || $globalFuntion_cashier->status == 'Apertura Pendiente')

                @if ($globalFuntion_cashier->status == 'Abierta')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-bordered">
                                <div class="panel-body">
                              

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h2 id="h2"><i class="fa-solid fa-wallet"></i>
                                                {{ $globalFuntion_cashier->title }}</h2>
                                        </div>
                                        @if ($globalFuntion_cashier->status == 'Abierta')
                                            <div class="col-md-6 text-right">
                                                <a href="#" data-toggle="modal" data-target="#modal-create-expense"
                                                    title="Agregar Gastos" class="btn btn-success">Gastos <i
                                                        class="fa-solid fa-money-bill-transfer"></i></a>
                                                {{-- <a  href="#" data-toggle="modal" data-target="#modal_transfer_moneyCashier" title="Transferir Dinero" class="btn btn-success">Traspaso <i class="fa-solid fa-money-bill-transfer"></i></a> --}}

                                                <a href="{{ route('cashiers.close', ['cashier' => $globalFuntion_cashier->id]) }}"
                                                    class="btn btn-danger">Cerrar Caja <i class="voyager-lock"></i></a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row" style="margin-top: 30px;">
                                        <div class="col-md-7">
                                            <div class="row">
                                                <div class="col-xs-4">
                                                    <div class="panel panel-bordered" style="border: 1px solid #f1f1f1; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                                                        <div class="panel-body text-center" style="padding: 15px 5px;">
                                                            <small class="text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Asignado</small>
                                                            <h4 style="margin: 5px 0 0; font-weight: 700;">{{ number_format($globalFuntion_cashierMoney['cashierIn'], 2, ',', '.') }} <small>Bs.</small></h4>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-4">
                                                    <div class="panel panel-bordered" style="border: 1px solid #f1f1f1; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                                                        <div class="panel-body text-center" style="padding: 15px 5px;">
                                                            <small class="text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Efectivo</small>
                                                            <h4 class="text-success" style="margin: 5px 0 0; font-weight: 700;">{{ number_format($globalFuntion_cashierMoney['amountEfectivoCashier'], 2, ',', '.') }} <small>Bs.</small></h4>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-4">
                                                    <div class="panel panel-bordered" style="border: 1px solid #f1f1f1; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                                                        <div class="panel-body text-center" style="padding: 15px 5px;">
                                                            <small class="text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Qr</small>
                                                            <h4 class="text-info" style="margin: 5px 0 0; font-weight: 700;">{{ number_format($globalFuntion_cashierMoney['amountQrCashier'], 2, ',', '.') }} <small>Bs.</small></h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div style="margin-top: 20px;">
                                                <h5 class="text-muted" style="margin-bottom: 15px; font-weight: 600; text-transform: uppercase; font-size: 12px;">Detalle de Movimientos</h5>
                                                <table class="table table-hover">
                                                    <tbody>
                                                        <tr>
                                                            <td style="border-top: 1px solid #f1f1f1;"><i class="fa-solid fa-arrow-trend-up text-success" style="margin-right: 10px;"></i> Ingreso Efectivo</td>
                                                            <td class="text-right" style="border-top: 1px solid #f1f1f1;"><strong>{{ number_format($globalFuntion_cashierMoney['paymentEfectivoIngreso'], 2, ',', '.') }}</strong> <small>Bs.</small></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="border-top: 1px solid #f1f1f1;"><i class="fa-solid fa-qrcode text-info" style="margin-right: 10px;"></i> Ingreso Qr</td>
                                                            <td class="text-right" style="border-top: 1px solid #f1f1f1;"><strong>{{ number_format($globalFuntion_cashierMoney['paymentQrIngreso'], 2, ',', '.') }}</strong> <small>Bs.</small></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="border-top: 1px solid #f1f1f1;"><i class="fa-solid fa-arrow-trend-down text-danger" style="margin-right: 10px;"></i> Egreso Efectivo</td>
                                                            <td class="text-right" style="border-top: 1px solid #f1f1f1;"><strong>{{ number_format($globalFuntion_cashierMoney['paymentEfectivoEgreso'], 2, ',', '.') }}</strong> <small>Bs.</small></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="border-top: 1px solid #f1f1f1;"><i class="fa-solid fa-qrcode text-danger" style="margin-right: 10px;"></i> Egreso Qr</td>
                                                            <td class="text-right" style="border-top: 1px solid #f1f1f1;"><strong>{{ number_format($globalFuntion_cashierMoney['paymentQrEgreso'], 2, ',', '.') }}</strong> <small>Bs.</small></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div style="height: 300px; position: relative;">
                                                <canvas id="myChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('partials.modal-registerExpense')


                @else
                    <div class="row" id="rowCashierOpen">
                        <div class="col-md-12">
                            <div class="panel panel-bordered">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h2 id="h2"><i class="fa-solid fa-wallet"></i>
                                                {{ $globalFuntion_cashier->title }}</h2>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6" style="margin-top: 50px">
                                            <table class="table table-hover" id="dataTable">
                                                <thead>
                                                    <tr>
                                                        <th>Corte</th>
                                                        <th>Cantidad</th>
                                                        <th>Sub Total</th>
                                                    </tr>
                                                </thead>
                                                @php
                                                    $cash = [
                                                        '200',
                                                        '100',
                                                        '50',
                                                        '20',
                                                        '10',
                                                        '5',
                                                        '2',
                                                        '1',
                                                        '0.5',
                                                        // '0.2',
                                                        // '0.1',
                                                    ];
                                                    $total = 0;
                                                @endphp
                                                <tbody>
                                                    @foreach ($cash as $item)
                                                        <tr>
                                                            <td>
                                                                <h4 style="margin: 0px"><img
                                                                        src=" {{ url('images/cash/' . $item . '.jpg') }} "
                                                                        alt="{{ $item }} Bs." width="70px">
                                                                    {{ $item }} Bs. </h4>
                                                            </td>
                                                            <td>
                                                                {{-- @php
                                                                    $details = null;
                                                                    if ($globalFuntion_cashier->vault_detail) {
                                                                        $details = $globalFuntion_cashier->vault_detail->cash
                                                                            ->where('cash_value', $item)
                                                                            ->first();
                                                                    }
                                                                @endphp
                                                                {{ $details ? $details->quantity : 0 }} --}}



                                                                @php                                                    
                                                                    // 1. Encontrar el detalle de cierre
                                                                    $open_detail = $globalFuntion_cashier->details->where('type', 'Apertura')->first();
                                                                    // 2. Encontrar el corte de billete específico dentro de los detalles del cierre
                                                                    $cash_detail = $open_detail ? $open_detail->detailCashes->where('cash_value', $item)->first() : null;
                                                                    $quantity = $cash_detail ? $cash_detail->quantity : 0;
                                                                @endphp
                                                                {{ $quantity }}
                                                            </td>
                                                            <td>
                                                                {{ number_format($quantity * $item, 2, ',', '.') }}
                                                                <input type="hidden" name="cash_value[]" value="{{ $item }}">
                                                                <input type="hidden" name="quantity[]" value="{{ $quantity }}">
                                                            </td>
                                                            @php
                                                                $total += $quantity * $item;
                                                            @endphp
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <br>
                                            <div class="alert alert-info">
                                                <strong>Información:</strong>
                                                <p>Si la cantidad de de cortes de billetes coincide con la cantidad
                                                    entregada por parte del administrador(a) de vóbeda, acepta la apertura
                                                    de caja, caso contrario puedes rechazar la apertura.</p>
                                            </div>
                                            <br>
                                            <h2 id="h3" class="text-right">Total en caja: Bs.
                                                {{ number_format($total, 2, ',', '.') }} </h2>
                                            <br>
                                            <div class="text-right">
                                                <button type="button" data-toggle="modal"
                                                    data-target="#refuse_cashier-modal" class="btn btn-danger">Rechazar 
                                                    apertura <i class="voyager-x"></i></button>
                                                <button type="button" data-toggle="modal" data-target="#open_cashier-modal"
                                                    class="btn btn-success">Aceptar apertura <i
                                                        class="voyager-key"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Aceptar apertura de caja --}}
                    <form class="form-edit-add" action="{{ route('cashiers.change.status', ['cashier' => $globalFuntion_cashier->id]) }}"
                        method="post">
                        @csrf
                        <input type="hidden" name="status" value="Abierta">
                        <div class="modal fade" tabindex="-1" id="open_cashier-modal" role="dialog">
                            <div class="modal-dialog modal-success">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span
                                                aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><i class="fa-solid fa-wallet"></i> Aceptar apertura de caja
                                        </h4>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-muted"></p>
                                        <small>Esta a punto de aceptar que posee todos los cortes de billetes descritos en
                                            la lista, ¿Desea continuar?</small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-success btn-submit">Si, aceptar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Rechazar apertura de caja --}}
                    <form class="form-edit-add" action="{{ route('cashiers.change.status', ['cashier' => $globalFuntion_cashier->id]) }}"
                        method="post">
                        @csrf
                        <input type="hidden" name="status" value="Cerrada">
                        <div class="modal modal-danger fade" tabindex="-1" id="refuse_cashier-modal" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><i class="fa-solid fa-wallet"></i> Rechazar apertura de
                                            caja</h4>
                                    </div>
                                    <div class="modal-body">
                                        <small>Esta a punto de rechazar la apertura de caja, ¿Desea continuar?</small>
                                        <p class="text-muted"></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-danger btn-submit">Si, rechazar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-bordered">
                            <div class="panel-body text-center">
                                <h2>Tienes una caja esperando por confimación de cierre</h2>
                                <a href="#" style="margin: 0px" data-toggle="modal"
                                    data-target="#cashier-revert-modal" class="btn btn-success"><i
                                        class="voyager-key"></i> Reabrir caja</a>
                                <a href="{{ route('cashiers.print', $globalFuntion_cashier->id) }}" style="margin: 0px"
                                    class="btn btn-danger" target="_blank"><i class="fa fa-print"></i> Imprimir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <form class="form-edit-add" action="{{ route('cashiers.close.revert', ['cashier' => $globalFuntion_cashier->id]) }}" method="post">
                    @csrf
                    <div class="modal fade" tabindex="-1" id="cashier-revert-modal" role="dialog">
                        <div class="modal-dialog modal-success">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span
                                            aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title"><i class="voyager-key"></i> Reabrir Caja</h4>
                                </div>
                                <div class="modal-body">
                                    <p class="text-muted">Si reabre la caja deberá realizar el arqueo nuevamente, ¿Desea
                                        continuar?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-success btn-submit">Si, reabrir</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            @endif
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-bordered">
                        <div class="panel-body">
                            <h1 class="text-center">No tienes caja abierta</h1>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body" style="padding: 0;">
                        <ul class="nav nav-tabs" style="padding: 15px 15px 0;">
                            <li class="active"><a data-toggle="tab" href="#stats_tab">Estadísticas</a></li>
                            <li><a data-toggle="tab" href="#birthdays_tab">Próximos Cumpleaños</a></li>
                            <li><a data-toggle="tab" href="#reminders_tab">Recordatorios</a></li>
                        </ul>
                        <div class="tab-content" style="padding: 15px;">
                            <div id="stats_tab" class="tab-pane fade in active">
                                <div class="row">
                                    <!-- Gráfico de productos más vendidos -->
                                    <div class="col-md-6">
                                        <div class="panel panel-bordered">
                                            <div class="panel-heading">
                                                <div class="panel-title-container">
                                                    <h3 class="panel-title">5 Productos Más Vendidos del Día</h3>
                                                    <div class="chart-controls">
                                                        <select class="form-control chart-type-selector" data-chart="topProductosChart">
                                                            <option value="doughnut" selected>Dona</option>
                                                            <option value="bar">Barras</option>
                                                        </select>
                                                        <button class="btn btn-sm btn-default chart-export" data-chart="topProductosChart" title="Descargar">
                                                            <i class="voyager-download"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel-body">
                                                <div class="chart-container">
                                                    <canvas id="topProductosChart" height="250"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Gráfico de ventas por día de la semana -->
                                    <div class="col-md-6">
                                        <div class="panel panel-bordered">
                                            <div class="panel-heading">
                                                <div class="panel-title-container">
                                                    <h3 class="panel-title">Ventas por Día de la Semana</h3>
                                                    <div class="chart-controls">
                                                        <select class="form-control chart-type-selector" data-chart="ventasDiasChart">
                                                            <option value="bar" selected>Barras</option>
                                                            <option value="line">Líneas</option>
                                                        </select>
                                                        <button class="btn btn-sm btn-default chart-export" data-chart="ventasDiasChart" title="Descargar">
                                                            <i class="voyager-download"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel-body">
                                                <div class="chart-container">
                                                    <canvas id="ventasDiasChart" height="250"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- Gráfico de ventas mensuales -->
                                    <div class="col-md-6">
                                        <div class="panel panel-bordered">
                                            <div class="panel-heading">
                                                <div class="panel-title-container">
                                                    <h3 class="panel-title">Ventas Mensuales</h3>
                                                    <div class="chart-controls">
                                                        <select class="form-control chart-type-selector" data-chart="ventasMensualesChart">
                                                            <option value="bar" selected>Barras</option>
                                                            <option value="line">Líneas</option>
                                                        </select>
                                                        <button class="btn btn-sm btn-default chart-export" data-chart="ventasMensualesChart" title="Descargar">
                                                            <i class="voyager-download"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel-body">
                                                <div class="chart-container">
                                                    <canvas id="ventasMensualesChart" height="250"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Gráfico de método de pago del día -->
                                    <div class="col-md-6">
                                        <div class="panel panel-bordered">
                                            <div class="panel-heading">
                                                <div class="panel-title-container">
                                                    <h3 class="panel-title">Método de Pago del Día</h3>
                                                    <div class="chart-controls">
                                                        <select class="form-control chart-type-selector" data-chart="tipoPagoChart">
                                                            <option value="doughnut" selected>Dona</option>
                                                            <option value="bar">Barras</option>
                                                        </select>
                                                        <button class="btn btn-sm btn-default chart-export" data-chart="tipoPagoChart" title="Descargar">
                                                            <i class="voyager-download"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel-body">
                                                <div class="chart-container">
                                                    <canvas id="tipoPagoChart" height="250"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="birthdays_tab" class="tab-pane fade">
                                @php
                                    $upcomingBirthdays = $global_index['upcomingBirthdays'] ?? [];
                                    $todayMD = \Carbon\Carbon::today()->format('m-d');
                                @endphp
                                @if(count($upcomingBirthdays) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover" style="margin-bottom: 0;">
                                            <thead>
                                                <tr>
                                                    <th>Cliente</th>
                                                    <th>Cumpleaños</th>
                                                    <th>Días restantes</th>
                                                    <th>Teléfono</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($upcomingBirthdays as $bday)
                                                    @php
                                                        $birthDate  = \Carbon\Carbon::parse($bday->birth_date);
                                                        $isToday    = $birthDate->format('m-d') === $todayMD;
                                                        $nextBday   = \Carbon\Carbon::parse($bday->next_birthday);
                                                        $daysUntil  = (int) \Carbon\Carbon::today()->diffInDays($nextBday);
                                                        $fullName   = trim(implode(' ', array_filter([
                                                            $bday->first_name,
                                                            $bday->paternal_surname,
                                                            $bday->maternal_surname,
                                                        ])));
                                                        $age = $birthDate->age;
                                                    @endphp
                                                    <tr style="{{ $isToday ? 'background-color: #fffde7;' : '' }}">
                                                        <td>
                                                            <strong>{{ $fullName }}</strong>
                                                            @if($isToday)
                                                                <span class="label label-warning" style="margin-left: 6px;">
                                                                    <i class="fa-solid fa-cake-candles"></i> ¡Hoy cumple {{ $age }} años!
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $birthDate->format('d \d\e M') }}</td>
                                                        <td>
                                                            @if($isToday)
                                                                <span class="label label-warning">¡Hoy!</span>
                                                            @elseif($daysUntil <= 7)
                                                                <span class="label label-info">{{ $daysUntil }} {{ $daysUntil === 1 ? 'día' : 'días' }}</span>
                                                            @else
                                                                <span class="text-muted">{{ $daysUntil }} días</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($bday->phone)
                                                                <a href="tel:{{ $bday->phone }}" style="color: inherit;">
                                                                    <i class="fa-solid fa-phone" style="margin-right:4px; color: #27ae60;"></i>{{ $bday->phone }}
                                                                </a>
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center" style="padding: 50px 0; color: #ccc;">
                                        <i class="fa-solid fa-cake-candles" style="font-size: 3rem;"></i>
                                        <p style="margin-top: 15px; font-size: 1rem;">No hay próximos cumpleaños registrados.</p>
                                    </div>
                                @endif
                            </div>
                            <div id="reminders_tab" class="tab-pane fade">
                                @if($global_index['reminder'] > 0)
                                    <p class="text-muted">Tienes {{ $global_index['reminder'] }} recordatorio(s) para hoy.</p>
                                @else
                                    <div class="text-center" style="padding: 50px 0; color: #ccc;">
                                        <i class="fa-regular fa-bell" style="font-size: 3rem;"></i>
                                        <p style="margin-top: 15px; font-size: 1rem;">No tienes recordatorios para hoy.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


        {{-- Modal QR --}}
    <div class="modal modal-success fade" tabindex="-1" id="qr_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-lock"></i> Iniciar sesión</h4>
                </div>
                <div class="modal-body">
                    <div class="col-md-12 text-center">
                        <img alt="Código QR" id="qr_code">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a90e2; /* Un azul más suave y moderno */
            --secondary-color: #50e3c2; /* Un toque de menta fresca */
            --text-color: #333;
            --text-color-light: #777;
            --background-color: #f4f7f6; /* Un fondo ligeramente gris */
            --panel-bg-color: #ffffff;
            --border-color: #e8e8e8;
            --shadow-color: rgba(0, 0, 0, 0.05);
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
        }

        .page-content.container-fluid {
            background-color: transparent;
        }

        .panel {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 12px var(--shadow-color);
            background-color: var(--panel-bg-color);
        }

        .panel-bordered > .panel-heading, .panel-bordered > .panel-body {
            border-color: var(--border-color);
        }

        .panel-body {
            padding: 25px;
        }
        
        /* Header */
        .page-content .panel-body h2 {
            font-weight: 400;
            color: var(--text-color);
        }
        .page-content .panel-body .text-muted {
            color: var(--text-color-light) !important;
            font-size: 1rem;
        }

        /* KPI Cards */
        .dashboard-kpi {
            transition: all 0.3s ease-in-out;
            border-radius: 12px;
            background: var(--panel-bg-color);
        }

        .dashboard-kpi:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .dashboard-kpi .panel-body {
            display: flex;
            align-items: center;
            padding: 20px;
        }

        .kpi-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-right: 20px;
            width: 60px;
            height: 60px;
            line-height: 60px;
            text-align: center;
            border-radius: 50%;
            background-color: rgba(74, 144, 226, 0.1);
        }

        .kpi-content {
            flex-grow: 1;
        }

        .kpi-value {
            font-size: 2rem;
            font-weight: 500;
            margin: 0;
            color: var(--text-color);
        }

        .kpi-label {
            font-size: 0.9rem;
            color: var(--text-color-light);
            margin: 0;
        }

        /* Chart Panels */
        .panel-title {
            font-weight: 400;
            font-size: 1.2rem;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: #357abd;
            border-color: #357abd;
        }
        .btn-success {
            border-radius: 8px;
        }
        .btn-danger {
            border-radius: 8px;
        }

        /* Cashier section */
        #h2 {
            font-weight: 400;
        }
        #h2 .fa-wallet {
            color: var(--primary-color);
        }
        
        table tr td {
            padding: 10px 5px !important;
        }

        table small {
            font-size: 1rem;
            color: var(--text-color-light);
        }
        table h4 {
            font-weight: 400;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dashboard-kpi .panel-body {
                flex-direction: column;
                text-align: center;
            }
            .kpi-icon {
                margin-right: 0;
                margin-bottom: 15px;
            }
        }
        
        /* Nuevos estilos para funcionalidades adicionales */
        .panel-title-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .chart-controls {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        .chart-controls select {
            width: auto;
            display: inline-block;
        }
        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }
    </style>
@stop

@section('javascript')
    <script src="{{ asset('js/qrious.js') }}"></script>
    <script src="{{ asset('js/input-numberBlock.js') }}"></script>
    {{-- Socket.io --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.4.0/socket.io.js" integrity="sha512-nYuHvSAhY5lFZ4ixSViOwsEKFvlxHMU2NHts1ILuJgOS6ptUmAGt/0i5czIgMOahKZ6JN84YFDA+mCdky7dD8A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function () {
            const serverUrl = "{{ setting('whatsapp.servidores') }}";
            const sessionId = "{{ setting('whatsapp.session') }}";
            let isWhatsappOnline = false; // Variable para rastrear el estado

            // Si no hay servidor configurado, no hacemos nada.
            if (!serverUrl || !sessionId) return;

            // --- Helper Functions ---
            function updateStatus(status, message) {
                let html = '';
                switch (status) {
                    case 'online':
                        html = '<button class="btn btn-success">WhatsApp en línea</button>';
                        isWhatsappOnline = true;
                        break;
                    case 'offline':
                        html = `<button type="button" class="btn btn-danger btn-offline" onclick="login()">${message || 'WhatsApp Fuera de línea'}</button>`;
                        break;
                    case 'loading':
                        html = '<span>Iniciando sesión...</span>';
                        break;
                    case 'server_offline':
                        html = '<b class="text-danger">Servidor fuera de línea</b>';
                        isWhatsappOnline = false;
                        break;
                    default:
                        html = '<span>Obteniendo estado...</span>';
                }
                $('#status').html(html);
            }

            function handleFetchError(error, context) {
                updateStatus('server_offline');
                console.error(`Error en ${context}:`, error);
                toastr.error('No se pudo conectar con el servidor de WhatsApp.', 'Error de Conexión');
            }

            // --- Socket.io Event Listeners ---
            const socket = io(serverUrl);

            socket.on('connect_error', (err) => {
                handleFetchError(err, 'socket connect_error');
                updateStatus('server_offline');
            });

            socket.on('login', data => {
                updateStatus('online');
                $('#qr_modal').modal('hide');
                toastr.success('La sesión de WhatsApp se ha iniciado correctamente.', 'Conectado');
            });

            socket.on('qr', data => {
                // Solo mostrar el QR si no estamos ya en línea
                if (!isWhatsappOnline) {
                    $('#qr_modal').modal('show');
                    new QRious({
                        element: document.querySelector("#qr_code"),
                        value: data.qr,
                        size: 450,
                        backgroundAlpha: 0,
                        foreground: "#000000",
                        level: "H",
                    });
                }
            });

            socket.on('logout', data => {
                updateStatus('offline', 'Sesión finalizada');
                toastr.warning('La sesión de WhatsApp ha finalizado.', 'Desconectado');
            });

            socket.on('disconnected', data => {
                $('#qr_modal').modal('hide');
                updateStatus('offline', 'Sesión finalizada');
                console.log('Socket disconnected:', data);
                toastr.error('Se perdió la conexión con el servidor de WhatsApp.', 'Desconectado');
            });

            // --- Initial Status Check ---
            async function checkInitialStatus() {
                try {
                    const response = await fetch(`${serverUrl}/status?id=${sessionId}`);
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const res = await response.json();

                    if (res.success) {
                        if (res.status == 1) {
                            updateStatus('online');
                        } else {
                            updateStatus('offline');
                        }
                    } else {
                        console.warn('El servidor respondió, pero no se pudo obtener el estado.');
                    }
                } catch (error) {
                    handleFetchError(error, 'checkInitialStatus');
                }
            }

            checkInitialStatus();

            // --- Global function for login button ---
            window.login = async function() {
                $('#status').html('<span>Iniciando sesión...</span>');
                try {
                    const response = await fetch(`${serverUrl}/login?id=${sessionId}`);
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const res = await response.json();
                    if (!res.success) {
                        console.error('Error al intentar iniciar sesión:', res);
                    }
                } catch (error) {
                    console.error('Error en login():', error);
                    $('#status').html('<b class="text-danger">Error al conectar</b>');
                }
            }
        });
    </script>


{{-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ --}}
    <!-- Incluir Chart.js -->

    <script src="{{ asset('js/btn-submit.js') }}"></script>  

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @if ($globalFuntion_cashier)
        @if ($globalFuntion_cashier->status == 'Abierta')
            <script>
                $(document).ready(function() {
                    const data = {
                        labels: [
                            'Ingreso en Efectivo y Qr, Dinero Asignado',
                            'Egresos en Efectivo y Qr',
                        ],
                        datasets: [{
                            label: 'Bs.',
                            data: [
                                "{{ $globalFuntion_cashierMoney['paymentEfectivoIngreso'] + $globalFuntion_cashierMoney['paymentQrIngreso'] }}", // Ventas Efectivo
            
                                "{{ $globalFuntion_cashierMoney['paymentEfectivoEgreso'] + $globalFuntion_cashierMoney['paymentQrEgreso'] }}", // Gastos
                            ],
                            backgroundColor: [
                                'rgb(60, 179, 113)',
                                'rgb(229, 57, 53)'
                            ],
                            hoverOffset: 4
                        }]
                    };
                    const config = {
                        type: 'doughnut',
                        data: data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                }
                            }
                        }
                    };
                    var myChart = new Chart(
                        document.getElementById('myChart'),
                        config
                    );

                    $('.btn-agregar-gasto').click(function() {
                        let cashier_id = $(this).data('cashier_id');
                        $('#form-agregar-gasto input[name="cashier_id"]').val(cashier_id);
                    });
                });
            </script>
        @endif
    @endif

    <script>
        $(document).ready(function() {
            // 1. Declarar las variables de los gráficos aquí para que sean accesibles en todo el script
            let ventasMensualesChart, topProductosChart, ventasDiasChart, tipoPagoChart;


            // --- Preparación de datos iniciales para los gráficos ---
            const monthData = @json($global_index['monthInteractive']);
            const ventasMensualesData = {
                labels: monthData.map(item => item.month.substring(0, 3) + '-' + item.year),
                datasets: [{
                    label: 'Ventas',
                    data: monthData.map(item => item.amount),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',

                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                }]
            };

            // Datos para el gráfico de productos más vendidos
            const productTop5Day = @json($global_index['productTop5Day']);

            const topProductosData = {
                labels: productTop5Day.map(item => item.name),
                datasets: [{
                    label: 'Unidades Vendidas',
                    data: productTop5Day.map(item => item.total_quantity),
                    backgroundColor: [
                        '#E83410', // Rojo vibrante
                        '#36A2EB', // Azul brillante
                        '#FFCE56', // Amarillo soleado
                        '#4BC0C0', // Turquesa
                        '#9966FF'  // Púrpura
                    ],
                    borderColor: [
                        '#E83410',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF'
                    ],
                    borderWidth: 1
                }]
            };

            // Datos para el gráfico de método de pago del día
            const paymentBreakdown = @json($global_index['paymentBreakdown']);
            const tipoPagoData = {
                labels: paymentBreakdown.length
                    ? paymentBreakdown.map(item => item.type === 'Efectivo' ? 'Efectivo' : 'QR')
                    : ['Sin ventas'],
                datasets: [{
                    label: 'Bs.',
                    data: paymentBreakdown.length
                        ? paymentBreakdown.map(item => item.amount)
                        : [0],
                    backgroundColor: ['#27ae60', '#3498db', '#f39c12'],
                    borderColor:     ['#27ae60', '#3498db', '#f39c12'],
                    borderWidth: 1
                }]
            };

            // Datos para el gráfico de ventas por día de la semana
            $weekDays = @json($global_index['weekDays']);
            const ventasDiasData = {
                labels: $weekDays.map(item => item.name + ' (' + item.dateInverso + ')'),

                datasets: [{
                    label: 'Total de Ventas',
                    data: $weekDays.map(item => item.amount),
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            };

            const comparacionAnualData = {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                datasets: [{
                        label: '2022',
                        data: [100000, 150000, 130000, 160000, 190000, 210000, 230000, 200000, 220000,
                            240000, 260000, 280000
                        ],
                        borderColor: 'rgba(201, 203, 207, 1)',
                        backgroundColor: 'rgba(201, 203, 207, 0.2)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: '2023',
                        data: [120000, 190000, 150000, 180000, 210000, 230000, 250000, 220000, 240000,
                            260000, 280000, 300000
                        ],
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }
                ]
            };

            // Configuración común para los gráficos
            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            };

            const pieChartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            };

            // Lógica para el filtro del dashboard
            $('#filter-menu a').on('click', function(e) {
                e.preventDefault();
                var range = $(this).data('range');
                $('#filter-button').html('<i class="voyager-refresh"></i> ' + range);
                
                // Muestra un loader mientras se cargan los datos
                $('#voyager-loader').fadeIn();

                // Petición AJAX para obtener los nuevos datos
                $.ajax({
                    url: '{{ url('admin/dashboard-data') }}/' + range,
                    type: 'GET',
                    success: function(data) {
                        updateDashboard(data);
                        $('#voyager-loader').fadeOut();
                    },
                    error: function(error) {
                        console.error("Error al cargar los datos:", error);
                        toastr.error('No se pudieron actualizar los datos del dashboard.');
                        $('#voyager-loader').fadeOut();
                    }
                });
            });

            // Función para actualizar todos los componentes del dashboard
            function updateDashboard(data) {
                // --- Actualizar KPIs ---
                let amountDaytotal = data.amountDaytotal;
                let saleDaytotal = data.saleDaytotal;
                let ticketPromedio = saleDaytotal > 0 ? (amountDaytotal / saleDaytotal) : 0;

                // Formatear números a 2 decimales con coma
                const formatNumber = (num) => {
                    let value = parseFloat(num) || 0;
                    return value.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }

                $('.dashboard-kpi').eq(0).find('.kpi-value').text('Bs. ' + formatNumber(amountDaytotal));
                $('.dashboard-kpi').eq(1).find('.kpi-value').text(saleDaytotal);
                $('.dashboard-kpi').eq(2).find('.kpi-value').text('Bs. ' + formatNumber(ticketPromedio));
                $('.dashboard-kpi').eq(3).find('.kpi-value').text(data.customer);

                // --- Actualizar Gráficos ---

                // Gráfico de Ventas Mensuales
                ventasMensualesChart.data.datasets[0].data = data.monthInteractive.map(item => item.amount);
                ventasMensualesChart.update();

                // Gráfico de Top 5 Productos
                topProductosChart.data.labels = data.productTop5Day.map(item => item.name);
                topProductosChart.data.datasets[0].data = data.productTop5Day.map(item => item.total_quantity);
                topProductosChart.update();

                // Gráfico de Ventas por Día de la Semana
                ventasDiasChart.data.datasets[0].data = data.weekDays.map(item => item.amount);
                ventasDiasChart.update();

                toastr.success('Dashboard actualizado para: ' + $('#filter-button').text().trim());
            }

            // 2. Inicializar los gráficos UNA SOLA VEZ, asignándolos a las variables declaradas arriba
            ventasMensualesChart = new Chart(document.getElementById('ventasMensualesChart'), {
                type: 'bar',
                data: ventasMensualesData,
                options: chartOptions
            });

            topProductosChart = new Chart(document.getElementById('topProductosChart'), {
                type: 'doughnut',
                data: topProductosData,
                options: pieChartOptions
            });

            ventasDiasChart = new Chart(document.getElementById('ventasDiasChart'), {
                type: 'bar',
                data: ventasDiasData,
                options: chartOptions
            });

            tipoPagoChart = new Chart(document.getElementById('tipoPagoChart'), {
                type: 'doughnut',
                data: tipoPagoData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: ctx => {
                                    const val = ctx.parsed || 0;
                                    return ' Bs. ' + val.toLocaleString('es-ES', { minimumFractionDigits: 2 });
                                }
                            }
                        }
                    }
                }
            });

            // Cambiar tipo de gráfico
            $('.chart-type-selector').change(function() {
                const chartId = $(this).data('chart');
                const newType = $(this).val();
                
                let chart;
                if (chartId === 'ventasMensualesChart') {
                    chart = ventasMensualesChart;
                } else if (chartId === 'ventasDiasChart') {
                    chart = ventasDiasChart;
                } else if (chartId === 'topProductosChart') {
                    chart = topProductosChart;
                } else if (chartId === 'tipoPagoChart') {
                    chart = tipoPagoChart;
                }

                if (chart) {
                    chart.config.type = newType;
                    chart.update();
                }
            });

            // Exportar gráfico
            $('.chart-export').click(function() {
                const chartId = $(this).data('chart');
                const canvas = document.getElementById(chartId);
                const url = canvas.toDataURL('image/png');
                
                const link = document.createElement('a');
                link.download = `${chartId}.png`;
                link.href = url;
                link.click();
            });

        });
    </script>
    
    

@stop