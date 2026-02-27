@extends('voyager::master')

@section('page_title', 'Ver Caja')

@section('css')
    <style>
        .stats-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            transition: transform 0.2s;
            border-left: 4px solid transparent;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stats-card.primary { border-left-color: #22a7f0; }
        .stats-card.success { border-left-color: #2ecc71; }
        .stats-card.danger  { border-left-color: #e74c3c; }
        .stats-card.warning { border-left-color: #f39c12; }

        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .stats-info { flex-grow: 1; }
        .stats-info h3 { margin: 0; font-size: 24px; font-weight: bold; color: #333; }
        .stats-info p  { margin: 0; color: #888; font-size: 12px; text-transform: uppercase; font-weight: 600; }

        .bg-primary-light { background: #e3f2fd; color: #1565c0; }
        .bg-success-light { background: #e8f5e9; color: #2e7d32; }
        .bg-danger-light  { background: #ffebee; color: #c62828; }
        .bg-warning-light { background: #fff8e1; color: #f9a825; }

        .panel-custom {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-radius: 8px;
            background: #fff;
            margin-bottom: 20px;
        }
        .panel-custom .panel-heading {
            background: transparent;
            border-bottom: 1px solid #f1f1f1;
            padding: 15px 20px;
        }
        .panel-custom .panel-title {
            font-size: 16px;
            font-weight: 600;
            color: #444;
            margin: 0;
        }
        .table-custom { margin-bottom: 0; }
        .table-custom thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #eee;
            color: #555;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
        }
        .table-custom tbody td {
            vertical-align: middle;
            border-top: 1px solid #f1f1f1;
            font-size: 13px;
        }
        .list-group-item { border-color: #f1f1f1; padding: 12px 20px; }
    </style>
@stop

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-dollar"></i> Caja #{{ $cashier->id }} <small>{{ $cashier->title }}</small>
        </h1>
        <div class="pull-right" style="margin-top: 20px;">
            <a href="{{ route('cashiers.index') }}" class="btn btn-warning btn-sm">
                <span class="glyphicon glyphicon-list"></span>&nbsp; Volver
            </a>
            @if ($cashier->status == 'Cierre Pendiente')
                <a href="{{ route('cashiers.confirm_close', ['cashier' => $cashier->id]) }}" class="btn btn-info btn-sm">
                    <i class="voyager-lock"></i> Confirmar Cierre
                </a>
            @endif
            @if ($cashier->status == 'Cerrada')
                <a href="{{ route('cashiers.print', $cashier->id) }}" target="_blank" class="btn btn-danger btn-sm">
                    <i class="fa fa-print"></i> Imprimir
                </a>
            @endif
        </div>
    </div>
@stop

@section('content')
    <div class="page-content read container-fluid">
        @php
            // ── Totales de ventas ──────────────────────────────────────────────────
            $total_cash_global    = 0;
            $total_qr_global      = 0;
            $total_deleted_global = 0;

            foreach ($cashier->sales as $sale) {
                if ($sale->deleted_at == null) {
                    $total_qr_global   += $sale->saleTransactions->where('paymentType', 'Qr')->sum('amount');
                    $total_cash_global += $sale->saleTransactions->where('paymentType', 'Efectivo')->sum('amount');
                } else {
                    $total_deleted_global += $sale->amount;
                }
            }

            // ── Totales de gastos ──────────────────────────────────────────────────
            $total_expense_global = 0;
            $total_expense_cash   = 0;
            $total_expense_qr     = 0;

            foreach ($cashier->expenses as $expense) {
                if ($expense->deleted_at == null) {
                    $total_expense_global += $expense->amount;
                    $total_expense_cash   += $expense->expenseTransactions->where('paymentType', 'Efectivo')->sum('amount');
                    $total_expense_qr     += $expense->expenseTransactions->where('paymentType', 'Qr')->sum('amount');
                }
            }

            // ── Abonos / Ingresos a caja ───────────────────────────────────────────
            $cashierInput_global = 0;
            foreach ($cashier->movements as $mov) {
                if ($mov->type == 'Ingreso' && $mov->deleted_at == null) {
                    $cashierInput_global += $mov->amount;
                }
            }
        @endphp

        {{-- ── Stats Cards ──────────────────────────────────────────────────────── --}}
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="stats-card success">
                    <div class="stats-icon bg-success-light">
                        <i class="voyager-dollar"></i>
                    </div>
                    <div class="stats-info">
                        <h3>{{ number_format($cashierInput_global + $total_cash_global - $total_expense_cash, 2, ',', '.') }}</h3>
                        <p>Efectivo en Caja</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card primary">
                    <div class="stats-icon bg-primary-light">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <div class="stats-info">
                        <h3>{{ number_format($total_qr_global - $total_expense_qr, 2, ',', '.') }}</h3>
                        <p>Total QR / Transf.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card danger">
                    <div class="stats-icon bg-danger-light">
                        <i class="voyager-basket"></i>
                    </div>
                    <div class="stats-info">
                        <h3>{{ number_format($total_expense_global, 2, ',', '.') }}</h3>
                        <p>Total Gastos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card warning">
                    <div class="stats-icon bg-warning-light">
                        <i class="voyager-wallet"></i>
                    </div>
                    <div class="stats-info">
                        <h3>{{ number_format($cashierInput_global, 2, ',', '.') }}</h3>
                        <p>Dinero Abonado</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- ── Columna Izquierda: Ventas ─────────────────────────────────────── --}}
            <div class="col-md-8">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-bag"></i> Ventas de la Caja</h3>
                    </div>
                    <div class="panel-body" style="padding: 0;">
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover table-custom">
                                <thead>
                                    <tr>
                                        <th style="width: 30px;"></th>
                                        <th style="text-align: center; width: 100px;">Acciones</th>
                                        <th style="text-align: center;">Cliente</th>
                                        <th style="text-align: center; width: 16%">Fecha</th>
                                        <th style="text-align: center; width: 11%">QR</th>
                                        <th style="text-align: center; width: 11%">Efectivo</th>
                                        <th style="text-align: center; width: 11%">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total_ventas          = 0;
                                        $total_ventas_qr       = 0;
                                        $total_ventas_efectivo = 0;
                                        $total_ventas_anulado  = 0;
                                    @endphp
                                    @forelse ($cashier->sales->sortByDesc('created_at') as $item)
                                        @php
                                            $pagoQr       = $item->saleTransactions->where('paymentType', 'Qr')->sum('amount');
                                            $pagoEfectivo = $item->saleTransactions->where('paymentType', 'Efectivo')->sum('amount');
                                            if ($item->deleted_at == null) {
                                                $total_ventas_qr       += $pagoQr;
                                                $total_ventas_efectivo += $pagoEfectivo;
                                                $total_ventas          += $pagoQr + $pagoEfectivo;
                                            } else {
                                                $total_ventas_anulado += $item->amount;
                                            }
                                        @endphp
                                        {{-- Fila principal --}}
                                        <tr class="tr-main" data-id="sale-{{ $item->id }}"
                                            style="cursor: pointer; @if ($item->deleted_at) text-decoration: line-through; color: #e74c3c; @endif">
                                            <td style="text-align: center; vertical-align: middle;">
                                                <i class="voyager-angle-down icon-toggle" id="icon-sale-{{ $item->id }}"></i>
                                            </td>
                                            <td style="vertical-align: middle;" class="no-sort no-click bread-actions text-right" class="no-click">
                                                <a href="{{ route('sales.show', ['sale' => $item->id]) }}" target="_blank"
                                                    title="Ver venta #{{ $item->invoiceNumber ?? $item->id }}"
                                                    class="btn btn-info btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @if ($item->deleted_at == null && $cashier->status == 'Abierta')
                                                    <a href="#"
                                                        onclick="deleteItem('{{ route('sales.destroy', ['sale' => $item->id]) }}')"
                                                        title="Eliminar" data-toggle="modal" data-target="#modal-delete"
                                                        class="btn btn-danger btn-xs delete"
                                                        style="margin-left: 3px;">
                                                        <i class="voyager-trash"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td style="font-size: 11px; vertical-align: middle;">
                                                @if ($item->person)
                                                    {{ strtoupper($item->person->first_name) }}
                                                    {{ $item->person->middle_name ? strtoupper($item->person->middle_name) : '' }}
                                                    {{ strtoupper($item->person->paternal_surname) }}
                                                    {{ strtoupper($item->person->maternal_surname) }}
                                                @else
                                                    <span class="text-muted">Sin cliente</span>
                                                @endif
                                            </td>
                                            <td style="text-align: center; font-size: 11px; vertical-align: middle;">
                                                {{ date('d/m/Y H:i', strtotime($item->dateSale ?? $item->created_at)) }}
                                            </td>
                                            <td class="text-right" style="vertical-align: middle;">{{ number_format($pagoQr, 2, ',', '.') }}</td>
                                            <td class="text-right" style="vertical-align: middle;">{{ number_format($pagoEfectivo, 2, ',', '.') }}</td>
                                            <td class="text-right" style="vertical-align: middle; font-weight: bold;">{{ number_format($item->amount, 2, ',', '.') }}</td>
                                        </tr>
                                        {{-- Fila expandible con detalle --}}
                                        <tr id="tr-details-sale-{{ $item->id }}" style="display: none; background-color: #f8fbff;">
                                            <td colspan="7" style="padding: 14px 20px; border-top: none;">
                                                <div class="row">
                                                    {{-- Productos --}}
                                                    <div class="col-md-8">
                                                        <strong style="font-size: 12px; color: #555; display: block; margin-bottom: 8px;">
                                                            <i class="voyager-bag"></i> Productos vendidos
                                                        </strong>
                                                        <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
                                                            <thead>
                                                                <tr style="background: #dce8f5;">
                                                                    <th style="padding: 5px 8px; border: 1px solid #c8daf0;">Producto</th>
                                                                    <th style="padding: 5px 8px; border: 1px solid #c8daf0; text-align: center; width: 55px;">Cant.</th>
                                                                    <th style="padding: 5px 8px; border: 1px solid #c8daf0; text-align: right; width: 80px;">P.Unit.</th>
                                                                    <th style="padding: 5px 8px; border: 1px solid #c8daf0; text-align: right; width: 70px;">Dto.</th>
                                                                    <th style="padding: 5px 8px; border: 1px solid #c8daf0; text-align: right; width: 80px;">Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse ($item->saleDetails->where('deleted_at', null) as $detail)
                                                                    <tr>
                                                                        <td style="padding: 4px 8px; border: 1px solid #eee;">
                                                                            {{ $detail->itemStock->item->nameGeneric ?? '—' }}
                                                                            @if ($detail->dispensed === 'Fraccionado')
                                                                                <span class="label label-info" style="font-size: 10px; margin-left: 4px;">Fracc.</span>
                                                                            @endif
                                                                        </td>
                                                                        <td style="padding: 4px 8px; border: 1px solid #eee; text-align: center;">{{ $detail->quantity }}</td>
                                                                        <td style="padding: 4px 8px; border: 1px solid #eee; text-align: right;">Bs. {{ number_format($detail->price, 2) }}</td>
                                                                        <td style="padding: 4px 8px; border: 1px solid #eee; text-align: right;">
                                                                            @if ($detail->discount > 0)
                                                                                <span class="text-danger">-Bs. {{ number_format($detail->discount, 2) }}</span>
                                                                            @else
                                                                                <span class="text-muted">—</span>
                                                                            @endif
                                                                        </td>
                                                                        <td style="padding: 4px 8px; border: 1px solid #eee; text-align: right; font-weight: bold;">Bs. {{ number_format($detail->amount, 2) }}</td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="5" class="text-center text-muted" style="padding: 8px;">Sin detalle de productos</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    {{-- Pago y registro --}}
                                                    <div class="col-md-4">
                                                        <strong style="font-size: 12px; color: #555; display: block; margin-bottom: 8px;">
                                                            <i class="fa fa-credit-card"></i> Desglose de pago
                                                        </strong>
                                                        <table style="width: 100%; font-size: 12px;">
                                                            <tr>
                                                                <td><i class="fa-solid fa-money-bill-wave" style="color: #27ae60;"></i> Efectivo:</td>
                                                                <td class="text-right"><b>Bs. {{ number_format($pagoEfectivo, 2) }}</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td><i class="fa-solid fa-qrcode" style="color: #1565c0;"></i> QR / Transf.:</td>
                                                                <td class="text-right"><b>Bs. {{ number_format($pagoQr, 2) }}</b></td>
                                                            </tr>
                                                            @if (($item->general_discount ?? 0) > 0)
                                                                <tr>
                                                                    <td class="text-danger">Dto. General:</td>
                                                                    <td class="text-right text-danger">-Bs. {{ number_format($item->general_discount, 2) }}</td>
                                                                </tr>
                                                            @endif
                                                            <tr style="border-top: 2px solid #ddd;">
                                                                <td><b>Total cobrado:</b></td>
                                                                <td class="text-right"><b>Bs. {{ number_format($item->amount, 2) }}</b></td>
                                                            </tr>
                                                        </table>
                                                        @if ($item->register)
                                                            <div style="margin-top: 10px; padding-top: 8px; border-top: 1px solid #eee; font-size: 11px; color: #888;">
                                                                <i class="fa fa-user"></i> Registrado por: <b>{{ $item->register->name }}</b>
                                                            </div>
                                                        @endif
                                                        @if ($item->observation)
                                                            <div style="margin-top: 6px; font-size: 11px; color: #888; font-style: italic;">
                                                                <i class="fa fa-comment"></i> {{ $item->observation }}
                                                            </div>
                                                        @endif
                                                        @if ($item->deleted_at)
                                                            <div style="margin-top: 8px; padding: 6px 10px; background: #ffebee; border-radius: 4px; font-size: 11px; color: #c62828;">
                                                                <i class="voyager-trash"></i> Anulada — {{ $item->deleteObservation ?? 'sin motivo' }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7">
                                                <h5 class="text-center" style="margin: 40px 0; color: #bbb;">
                                                    <img src="{{ asset('images/empty.png') }}" width="100px" alt="" style="opacity: 0.6; display: block; margin: 0 auto 10px;"><br>
                                                    No hay ventas registradas
                                                </h5>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-right"><span class="text-danger"><b>TOTAL ANULADO</b></span></td>
                                        <td class="text-right"><b class="text-danger">{{ number_format($total_ventas_anulado, 2, ',', '.') }}</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-right"><b>TOTAL QR / TRANSFERENCIA</b></td>
                                        <td class="text-right"><b>{{ number_format($total_ventas_qr, 2, ',', '.') }}</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-right"><b>TOTAL EFECTIVO</b></td>
                                        <td class="text-right"><b>{{ number_format($total_ventas_efectivo, 2, ',', '.') }}</b></td>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td colspan="6" class="text-right"><b>TOTAL COBRADO</b></td>
                                        <td class="text-right"><b>{{ number_format($total_ventas, 2, ',', '.') }}</b></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Columna Derecha: Info + Ingresos + Gastos ───────────────────────── --}}
            <div class="col-md-4">

                {{-- Información General --}}
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title">Información General</h3>
                    </div>
                    <div class="panel-body" style="padding: 0;">
                        <ul class="list-group list-group-flush" style="margin-bottom: 0;">
                            <li class="list-group-item">
                                <strong>Cajero:</strong>
                                <span class="pull-right">{{ $cashier->user->name }}</span>
                            </li>
                            <li class="list-group-item">
                                <strong>Estado:</strong>
                                <span class="pull-right label label-{{ $cashier->status == 'Abierta' ? 'success' : ($cashier->status == 'Cerrada' ? 'danger' : 'warning') }}" style="font-size: 100%;">
                                    {{ $cashier->status }}
                                </span>
                            </li>
                            <li class="list-group-item">
                                <strong>Apertura:</strong>
                                <span class="pull-right">{{ date('d/m/Y H:i', strtotime($cashier->created_at)) }}</span>
                            </li>
                            @if ($cashier->closed_at)
                                <li class="list-group-item">
                                    <strong>Cierre:</strong>
                                    <span class="pull-right">{{ date('d/m/Y H:i', strtotime($cashier->closed_at)) }}</span>
                                </li>
                            @endif
                            <li class="list-group-item">
                                <strong>Descripción:</strong><br>
                                <small>{{ $cashier->title }}</small>
                            </li>
                            @if ($cashier->observation)
                                <li class="list-group-item">
                                    <strong>Observación:</strong><br>
                                    <small>{{ $cashier->observation }}</small>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                {{-- Ingresos (Abonos) --}}
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-double-up text-success"></i> Ingresos (Abonos)</h3>
                    </div>
                    <div class="panel-body" style="padding: 0;">
                        <div class="table-responsive" style="max-height: 260px; overflow-y: auto;">
                            <table class="table table-hover table-custom">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">N&deg;</th>
                                        <th>Detalle</th>
                                        <th style="text-align: right; width: 14%">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $count = 1; $cashierInput = 0; @endphp
                                    @forelse ($cashier->movements->where('type', 'Ingreso')->where('deleted_at', null) as $item)
                                        <tr>
                                            <td>{{ $count }}</td>
                                            <td>
                                                {{ $item->description }}<br>
                                                <small class="text-muted">{{ $item->user->name }} — {{ date('d/m/Y H:i', strtotime($item->created_at)) }}</small>
                                                @if ($item->transferCashier_id)
                                                    <span class="label label-info">Transferencia</span>
                                                @endif
                                            </td>
                                            <td class="text-right">{{ number_format($item->amount, 2, ',', '.') }}</td>
                                        </tr>
                                        @php $cashierInput += $item->amount; $count++; @endphp
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted">No hay abonos</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-right"><b>TOTAL</b></td>
                                        <td class="text-right"><b>{{ number_format($cashierInput, 2, ',', '.') }}</b></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Gastos --}}
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-double-down text-danger"></i> Gastos</h3>
                    </div>
                    <div class="panel-body" style="padding: 0;">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-hover table-custom">
                                <thead>
                                    <tr>
                                        <th style="width: 30px;"></th>
                                        <th>Detalle</th>
                                        <th style="text-align: right; width: 12%">Precio</th>
                                        <th style="text-align: right; width: 10%">Cant.</th>
                                        <th style="text-align: right; width: 12%">Total</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total_expense         = 0;
                                        $total_expense_deleted = 0;
                                    @endphp
                                    @forelse ($cashier->expenses->sortByDesc('created_at') as $item)
                                        <tr class="tr-main" data-id="expense-{{ $item->id }}"
                                            style="cursor: pointer; @if ($item->deleted_at) text-decoration: line-through; color: #e74c3c; @endif">
                                            <td style="text-align: center; vertical-align: middle;">
                                                <i class="voyager-angle-down icon-toggle" id="icon-expense-{{ $item->id }}"></i>
                                            </td>
                                            <td style="vertical-align: middle;">
                                                {{ \Illuminate\Support\Str::limit($item->observation, 45) }}<br>
                                                <small class="text-muted">{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</small>
                                            </td>
                                            <td class="text-right" style="vertical-align: middle;">{{ number_format($item->price, 2, ',', '.') }}</td>
                                            <td class="text-right" style="vertical-align: middle;">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                                            <td class="text-right" style="vertical-align: middle;">{{ number_format($item->amount, 2, ',', '.') }}</td>
                                            <td style="vertical-align: middle; text-align: right;">
                                                @if ($item->deleted_at == null && $cashier->status == 'Abierta')
                                                    <a href="#" title="Eliminar" class="btn btn-sm btn-danger delete"
                                                        data-toggle="modal" data-target="#modal-delete"
                                                        onclick="deleteItem('{{ route('expenses.destroy', ['expense' => $item->id]) }}')">
                                                        <i class="voyager-trash"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        {{-- Detalle expandible --}}
                                        <tr id="tr-details-expense-{{ $item->id }}" style="display: none; background-color: #fafafa;">
                                            <td colspan="6" style="padding: 15px 20px;">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <small class="text-muted">Registrado por:</small>
                                                        <p style="margin: 2px 0;"><b>{{ $item->register->name ?? '—' }}</b></p>
                                                        <small>{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted">Desglose de pago:</small>
                                                        @php
                                                            $ef = $item->expenseTransactions->where('paymentType', 'Efectivo')->sum('amount');
                                                            $qr = $item->expenseTransactions->where('paymentType', 'Qr')->sum('amount');
                                                        @endphp
                                                        <table style="width: 100%; font-size: 12px; margin-top: 4px;">
                                                            <tr><td>Efectivo:</td><td class="text-right">Bs. {{ number_format($ef, 2) }}</td></tr>
                                                            <tr><td>QR / Transf.:</td><td class="text-right">Bs. {{ number_format($qr, 2) }}</td></tr>
                                                            <tr style="border-top: 1px solid #ddd; font-weight: bold;">
                                                                <td>Total:</td><td class="text-right">Bs. {{ number_format($item->amount, 2) }}</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @php
                                            if ($item->deleted_at == null) { $total_expense += $item->amount; }
                                            else { $total_expense_deleted += $item->amount; }
                                        @endphp
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted">No hay gastos</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-right"><span class="text-danger"><b>TOTAL ANULADO</b></span></td>
                                        <td class="text-right"><b class="text-danger">{{ number_format($total_expense_deleted, 2, ',', '.') }}</b></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-right"><b>TOTAL GASTOS</b></td>
                                        <td class="text-right"><b>{{ number_format($total_expense, 2, ',', '.') }}</b></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>{{-- /col-md-4 --}}
        </div>{{-- /row --}}
    </div>

    @include('partials.modal-delete')
@stop

@section('javascript')
<script src="{{ asset('js/btn-submit.js') }}"></script>
<script src="{{ asset('js/input-numberBlock.js') }}"></script>
<script>
    function deleteItem(url) {
        $('#delete_form').attr('action', url);
    }

    $(document).ready(function () {
        $('table').on('click', '.tr-main td', function (e) {
            if ($(this).hasClass('no-click') || $(this).find('a, button').length > 0) return;
            let id = $(this).parent().data('id');
            if (id) {
                $(`#tr-details-${id}`).fadeToggle();
                $(`#icon-${id}`).toggleClass('voyager-angle-down voyager-angle-up');
            }
        });
    });
</script>
@stop
