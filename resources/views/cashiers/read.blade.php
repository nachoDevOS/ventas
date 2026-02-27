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
        .stats-card.danger { border-left-color: #e74c3c; }
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
        .stats-info p { margin: 0; color: #888; font-size: 12px; text-transform: uppercase; font-weight: 600; }

        .bg-primary-light { background: #e3f2fd; color: #1565c0; }
        .bg-success-light { background: #e8f5e9; color: #2e7d32; }
        .bg-danger-light { background: #ffebee; color: #c62828; }
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
        .nav-tabs-custom { border-bottom: 1px solid #eee; }
        .nav-tabs-custom > li > a {
            border: none;
            color: #777;
            font-weight: 500;
            padding: 15px 20px;
            transition: all 0.3s;
        }
        .nav-tabs-custom > li.active > a {
            border: none;
            border-bottom: 3px solid #22a7f0;
            color: #22a7f0;
            background: transparent;
        }
        .nav-tabs-custom > li > a:hover { background: #f9f9f9; color: #22a7f0; }
        
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
            $total_cash_global = 0;
            $total_qr_global = 0;
            $total_deleted_global = 0;

            $collections = [
                ['items' => $cashier->sales, 'transaction' => 'saleTransactions'],
                ['items' => $cashier->anamnesisForms, 'transaction' => 'anamnesisTransactions'],
                ['items' => $cashier->vaccinationRecords, 'transaction' => 'vaccinationTransactions'],
                ['items' => $cashier->dewormings ?? [], 'transaction' => 'dewormingTransactions'],
                ['items' => $cashier->hairSalons ?? [], 'transaction' => 'hairSalonTransactions'],
                ['items' => $cashier->homeServices ?? [], 'transaction' => 'homeServiceTransactions'],
                ['items' => $cashier->euthanasias ?? [], 'transaction' => 'euthanasiaTransactions'],
            ];

            foreach ($collections as $collection) {
                foreach ($collection['items'] as $item) {
                    if ($item->deleted_at == null) {
                        $total_qr_global += $item->{$collection['transaction']}->where('paymentType', 'Qr')->sum('amount');
                        $total_cash_global += $item->{$collection['transaction']}->where('paymentType', 'Efectivo')->sum('amount');
                    } else {
                        $total_deleted_global += $item->amount;
                    }
                }
            }

            $total_expense_global = 0;
            $total_expense_cash = 0;
            $total_expense_qr = 0;

            foreach ($cashier->expenses as $item) {
                if ($item->deleted_at == null) {
                    $total_expense_global += $item->amount;
                    $total_expense_cash += $item->expenseTransactions->where('paymentType', 'Efectivo')->sum('amount');
                    $total_expense_qr += $item->expenseTransactions->where('paymentType', 'Qr')->sum('amount');
                }
            }
            foreach ($cashier->advancePayments as $item) {
                if ($item->deleted_at == null) {
                    $total_expense_global += $item->amount;
                    $total_expense_cash += $item->advancePaymentTransactions->where('paymentType', 'Efectivo')->sum('amount');
                    $total_expense_qr += $item->advancePaymentTransactions->where('paymentType', 'Qr')->sum('amount');
                }
            }
            foreach ($cashier->paymentSheets as $item) {
                if ($item->deleted_at == null) {
                    $total_expense_global += $item->amount;
                    $total_expense_cash += $item->paymentSheetTransactions->where('paymentType', 'Efectivo')->sum('amount');
                    $total_expense_qr += $item->paymentSheetTransactions->where('paymentType', 'Qr')->sum('amount');
                }
            }

            $cashierInput_global = 0;
            foreach ($cashier->movements as $item) {
                if ($item->type == 'Ingreso' && $item->deleted_at == null) {
                    $cashierInput_global += $item->amount;
                }
            }
        @endphp

        <div class="row">
            <!-- Stats Cards -->
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
                        <i class="voyager-qr-code"></i>
                    </div>
                    <div class="stats-info">
                        <h3>{{ number_format($total_qr_global - $total_expense_qr, 2, ',', '.') }}</h3>
                        <p>Total QR/Transf.</p>
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
            <!-- Left Column: Transactions -->
            <div class="col-md-8">
                <div class="panel panel-custom">
                    <div class="panel-heading" style="border-bottom:0;">
                        <ul class="nav nav-tabs nav-tabs-custom">
                            <li class="active"><a data-toggle="tab" href="#sales"><i class="voyager-bag"></i> Ventas</a></li>
                            <li><a data-toggle="tab" href="#anamnesis"><i class="voyager-logbook"></i> Anamnesis</a></li>
                            <li><a data-toggle="tab" href="#vaccination"><i class="voyager-droplet"></i> Vacunas</a></li>
                            <li><a data-toggle="tab" href="#deworming"><i class="voyager-pill"></i> Desparasitación</a></li>
                            <li><a data-toggle="tab" href="#hairsalon"><i class="voyager-scissors"></i> Peluquería</a></li>
                            <li><a data-toggle="tab" href="#homeservice"><i class="voyager-truck"></i> Domicilio</a></li>
                            <li><a data-toggle="tab" href="#euthanasia"><i class="voyager-heart"></i> Eutanasia</a></li>
                        </ul>
                    </div>
                    <div class="panel-body p-0">
                        <div class="tab-content">
                            <!-- Sales Tab -->
                            <div id="sales" class="tab-pane fade in active">
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-hover table-custom">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; width: 5%">N&deg;</th>
                                        <th style="text-align: center; width: 10%">Código Venta</th>
                                        <th style="text-align: center">Cliente</th>
                                        <th style="text-align: center; width: 18%">Fecha</th>
                                        
                                        <th style="text-align: center; width: 10%">Pago Qr</th>
                                        <th style="text-align: center; width: 10%">Pago Efectivo</th>
                                        <th style="text-align: center; width: 10%">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $count = 1;
                                        $total_movements = 0;
                                        $total_movements_qr = 0;
                                        $total_movements_efectivo = 0;
                                        $total_movements_deleted = 0;
                                    @endphp
                                    @forelse ($cashier->sales->sortByDesc('created_at') as $item)
                                        <tr
                                            @if ($item->deleted_at) style="text-decoration: line-through; color: red;" @endif>
                                            <td style="text-align: center; font-size: 11px">{{ $count }}</td>

                                            <td style="; text-align: center">
                                                @if ($item->deleted_at == null && $cashier->status == 'Abierta')
                                                    <a href="#"
                                                        onclick="deleteItem('{{ route('sales.destroy', ['sale' => $item->id]) }}')"
                                                        title="Eliminar" data-toggle="modal" data-target="#modal-delete"
                                                        class="btn btn-sm btn-danger delete">
                                                        <i class="voyager-trash"></i>
                                                    </a>
                                                    <br>
                                                @endif
                                                <a href="{{ route('sales.show', ['sale' => $item->id]) }}" target="_blank" title="Ver Venta">
                                                    {{ $item->id }}
                                                </a>
                                            </td>
                                            <td style="font-size: 11px">
                                                @if ($item->person)
                                                    {{ strtoupper($item->person->first_name) }}
                                                    {{ $item->person->middle_name ? strtoupper($item->person->middle_name) : '' }}
                                                    {{ strtoupper($item->person->paternal_surname) }}
                                                    {{ strtoupper($item->person->maternal_surname) }}
                                                @else
                                                    Sin Datos
                                                @endif
                                            </td>
                                            <td style="text-align: center; font-size: 11px">
                                                {{ date('d/m/Y h:i a', strtotime($item->dateSale)) }}
                                            </td>

                                            @php
                                                $pagoQr = $item->saleTransactions
                                                    ->where('paymentType', 'Qr')
                                                    ->sum('amount');
                                                $pagoEfectivo = $item->saleTransactions
                                                    ->where('paymentType', 'Efectivo')
                                                    ->sum('amount');
                                                if ($item->deleted_at == null) {
                                                    $total_movements_qr += $pagoQr;
                                                    $total_movements_efectivo += $pagoEfectivo;

                                                    $total_movements += $pagoQr + $pagoEfectivo;
                                                } else {
                                                    $total_movements_deleted += $item->amount;
                                                }
                                            @endphp
                                            <td class="text-right">{{ number_format($pagoQr, 2, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($pagoEfectivo, 2, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($item->amount, 2, ',', '.') }}</td>


                                        </tr>
                                        @php
                                            $count++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td colspan="7">
                                                <h5 class="text-center" style="margin-top: 50px">
                                                    <img src="{{ asset('images/empty.png') }}" width="120px" alt="" style="opacity: 0.8">
                                                    <br><br>
                                                    No hay resultados
                                                </h5>
                                            </td>
                                        </tr>
                                    @endempty
                                    <tr>
                                        <td colspan="6" class="text-right"><span class="text-danger"><b>TOTAL
                                                    ANULADO</b></span></td>
                                        <td class="text-right"><b
                                                class="text-danger">{{ number_format($total_movements_deleted, 2, ',', '.') }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-right"><b>TOTAL COBROS</b></td>
                                        <td class="text-right">
                                            <b>{{ number_format($total_movements, 2, ',', '.') }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-right"><b>TOTAL QR/TRANSFERENCIA</b></td>
                                        <td class="text-right">
                                            <b>{{ number_format($total_movements_qr, 2, ',', '.') }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-right"><b>TOTAL EFECTIVO</b></td>
                                        <td class="text-right">
                                            <b>{{ number_format($total_movements_efectivo, 2, ',', '.') }}</b>
                                        </td>
                                    </tr>
                            </tbody>
                        </table>
                                </div>
                            </div>
                            
                            <!-- Anamnesis Tab -->
                            <div id="anamnesis" class="tab-pane fade">
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-hover table-custom">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center; width: 2%">N&deg;</th>
                                                <th style="text-align: center; width: 5%">Código</th>
                                                <th style="text-align: center; width: 10%">Mascota</th>
                                                <th style="text-align: center">Problema</th>
                                                <th style="text-align: center; width: 8%">Fecha</th>                                                
                                                <th style="text-align: center; width: 5%">QR</th>
                                                <th style="text-align: center; width: 5%">Efectivo</th>
                                                <th style="text-align: center; width: 5%">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $count = 1;
                                                $total_movements = 0;
                                                $total_movements_qr = 0;
                                                $total_movements_efectivo = 0;
                                                $total_movements_deleted = 0;
                                            @endphp
                                            @forelse ($cashier->anamnesisForms->sortByDesc('created_at') as $item)
                                                <tr @if ($item->deleted_at) style="text-decoration: line-through; color: red;" @endif>
                                                    <td style="text-align: center;">{{ $count }}</td>
                                                    <td style="text-align: center">
                                                        @if ($item->deleted_at == null && $cashier->status == 'Abierta')
                                                            <a href="#" onclick="deleteItem('{{ route('voyager.pets.history.destroy', ['anamnesis' => $item->id]) }}')" title="Eliminar" data-toggle="modal" data-target="#modal-delete" class="btn btn-sm btn-danger delete">
                                                                <i class="voyager-trash"></i>
                                                            </a>
                                                            <br>
                                                        @endif
                                                        <a href="{{ route('voyager.pets.history.show', ['history' => $item->id]) }}" target="_blank" title="Ver">{{ $item->id }}</a>
                                                    </td>
                                                    <td>{{ strtoupper($item->pet->name) }}</td>
                                                    <td>{{ strtoupper($item->main_problem) }}</td>
                                                    <td style="text-align: center;">{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</td>
                                                    @php
                                                        $pagoQr = $item->anamnesisTransactions->where('paymentType', 'Qr')->sum('amount');
                                                        $pagoEfectivo = $item->anamnesisTransactions->where('paymentType', 'Efectivo')->sum('amount');
                                                        if ($item->deleted_at == null) {
                                                            $total_movements_qr += $pagoQr;
                                                            $total_movements_efectivo += $pagoEfectivo;
                                                            $total_movements += $pagoQr + $pagoEfectivo;
                                                        } else {
                                                            $total_movements_deleted += $item->amount;
                                                        }
                                                    @endphp
                                                    <td class="text-right">{{ number_format($pagoQr, 2, ',', '.') }}</td>
                                                    <td class="text-right">{{ number_format($pagoEfectivo, 2, ',', '.') }}</td>
                                                    <td class="text-right">{{ number_format($item->amount, 2, ',', '.') }}</td>
                                                </tr>
                                                @php $count++; @endphp
                                            @empty
                                                <tr><td colspan="8" class="text-center">No hay registros</td></tr>
                                            @endforelse
                                            <!-- Totals rows (simplified) -->
                                            <tr>
                                                <td colspan="7" class="text-right"><b>TOTAL</b></td>
                                                <td class="text-right"><b>{{ number_format($total_movements, 2, ',', '.') }}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Vaccination Tab -->
                            <div id="vaccination" class="tab-pane fade">
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-hover table-custom">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center; width: 2%">N&deg;</th>
                                                <th style="text-align: center; width: 5%">Código</th>
                                                <th style="text-align: center; width: 10%">Mascota</th>
                                                <th style="text-align: center;">Detalle</th>
                                                <th style="text-align: center; width: 8%">Fecha</th>
                                                <th style="text-align: center; width: 5%">QR</th>
                                                <th style="text-align: center; width: 5%">Efectivo</th>
                                                <th style="text-align: center; width: 5%">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $count = 1;
                                                $total_movements = 0;
                                            @endphp
                                            @forelse ($cashier->vaccinationRecords->sortByDesc('created_at') as $item)
                                                <tr @if ($item->deleted_at) style="text-decoration: line-through; color: red;" @endif>
                                                    <td style="text-align: center;">{{ $count }}</td>
                                                    <td style="text-align: center">
                                                        @if ($item->deleted_at == null && $cashier->status == 'Abierta')
                                                            <a href="#" onclick="deleteItem('{{ route('voyager.pets.vaccinationrecords.destroy', ['vaccine' => $item->id]) }}')" title="Eliminar" data-toggle="modal" data-target="#modal-delete" class="btn btn-sm btn-danger delete"><i class="voyager-trash"></i></a><br>
                                                        @endif
                                                        <a href="{{ route('voyager.pets.vaccinationrecords.show', ['vaccine' => $item->id]) }}" target="_blank">{{ $item->id }}</a>
                                                    </td>
                                                    <td>{{ strtoupper($item->pet->name) }}</td>
                                                    <td>{{ $item->vaccine }}</td>
                                                    <td style="text-align: center;">{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</td>
                                                    @php
                                                        $pagoQr = $item->vaccinationTransactions->where('paymentType', 'Qr')->sum('amount');
                                                        $pagoEfectivo = $item->vaccinationTransactions->where('paymentType', 'Efectivo')->sum('amount');
                                                        if ($item->deleted_at == null) {
                                                            $total_movements += $pagoQr + $pagoEfectivo;
                                                        }
                                                    @endphp
                                                    <td class="text-right">{{ number_format($pagoQr, 2, ',', '.') }}</td>
                                                    <td class="text-right">{{ number_format($pagoEfectivo, 2, ',', '.') }}</td>
                                                    <td class="text-right">{{ number_format($item->amount, 2, ',', '.') }}</td>
                                                </tr>
                                                @php $count++; @endphp
                                            @empty
                                                <tr><td colspan="8" class="text-center">No hay registros</td></tr>
                                            @endforelse
                                            <tr>
                                                <td colspan="7" class="text-right"><b>TOTAL</b></td>
                                                <td class="text-right"><b>{{ number_format($total_movements, 2, ',', '.') }}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Deworming Tab -->
                            <div id="deworming" class="tab-pane fade">
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-hover table-custom">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center; width: 2%">N&deg;</th>
                                                <th style="text-align: center; width: 5%">Código</th>
                                                <th style="text-align: center; width: 10%">Mascota</th>
                                                <th style="text-align: center;">Detalle</th>
                                                <th style="text-align: center; width: 8%">Fecha</th>
                                                <th style="text-align: center; width: 5%">QR</th>
                                                <th style="text-align: center; width: 5%">Efectivo</th>
                                                <th style="text-align: center; width: 5%">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $count = 1; $total_movements = 0; @endphp
                                            @forelse ($cashier->dewormings->sortByDesc('created_at') ?? [] as $item)
                                                <tr @if ($item->deleted_at) style="text-decoration: line-through; color: red;" @endif>
                                                    <td style="text-align: center;">{{ $count }}</td>
                                                    <td style="text-align: center">
                                                        @if ($item->deleted_at == null && $cashier->status == 'Abierta')
                                                            <a href="#" onclick="deleteItem('{{ route('voyager.pets.dewormings.destroy', ['deworming' => $item->id]) }}')" title="Eliminar" data-toggle="modal" data-target="#modal-delete" class="btn btn-sm btn-danger delete"><i class="voyager-trash"></i></a><br>
                                                        @endif
                                                        <a href="{{ route('voyager.pets.dewormings.show', ['deworming' => $item->id]) }}" target="_blank">{{ $item->id }}</a>
                                                    </td>
                                                    <td>{{ strtoupper($item->pet->name ?? 'Sin Nombre') }}</td>
                                                    <td>{{$item->deworming}}</td>
                                                    <td style="text-align: center;">{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</td>
                                                    @php
                                                        $pagoQr = $item->dewormingTransactions->where('paymentType', 'Qr')->sum('amount');
                                                        $pagoEfectivo = $item->dewormingTransactions->where('paymentType', 'Efectivo')->sum('amount');
                                                        if ($item->deleted_at == null) { $total_movements += $pagoQr + $pagoEfectivo; }
                                                    @endphp
                                                    <td class="text-right">{{ number_format($pagoQr, 2, ',', '.') }}</td>
                                                    <td class="text-right">{{ number_format($pagoEfectivo, 2, ',', '.') }}</td>
                                                    <td class="text-right">{{ number_format($item->amount, 2, ',', '.') }}</td>
                                                </tr>
                                                @php $count++; @endphp
                                            @empty
                                                <tr><td colspan="8" class="text-center">No hay registros</td></tr>
                                            @endforelse
                                            <tr>
                                                <td colspan="7" class="text-right"><b>TOTAL</b></td>
                                                <td class="text-right"><b>{{ number_format($total_movements, 2, ',', '.') }}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- HairSalon Tab -->
                            <div id="hairsalon" class="tab-pane fade">
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-hover table-custom">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center; width: 2%">N&deg;</th>
                                                <th style="text-align: center; width: 5%">Código</th>
                                                <th style="text-align: center; width: 10%">Mascota</th>
                                                <th style="text-align: center; width: 8%">Tipo</th>
                                                <th style="text-align: center;">Detalle</th>
                                                <th style="text-align: center; width: 8%">Fecha</th>
                                                <th style="text-align: center; width: 5%">QR</th>
                                                <th style="text-align: center; width: 5%">Efectivo</th>
                                                <th style="text-align: center; width: 5%">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $count = 1; $total_movements = 0; @endphp
                                            @forelse ($cashier->hairSalons->sortByDesc('created_at') ?? [] as $item)
                                                <tr @if ($item->deleted_at) style="text-decoration: line-through; color: red;" @endif>
                                                    <td style="text-align: center;">{{ $count }}</td>
                                                    <td style="text-align: center">
                                                        @if ($item->deleted_at == null && $cashier->status == 'Abierta')
                                                            <a href="#" onclick="deleteItem('{{ route('voyager.pets.hairsalons.destroy', ['hairsalon' => $item->id]) }}')" title="Eliminar" data-toggle="modal" data-target="#modal-delete" class="btn btn-sm btn-danger delete"><i class="voyager-trash"></i></a><br>
                                                        @endif
                                                        <a href="{{ route('voyager.pets.hairsalons.show', ['hairsalon' => $item->id]) }}" target="_blank">{{ $item->id }}</a>
                                                    </td>
                                                    <td>{{ strtoupper($item->pet->name ?? 'Sin Nombre') }}</td>
                                                    <td>{{ strtoupper($item->type) }}</td>
                                                    <td>{{ $item->observation }}</td>
                                                    <td style="text-align: center;">{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</td>
                                                    @php
                                                        $pagoQr = $item->hairSalonTransactions->where('paymentType', 'Qr')->sum('amount');
                                                        $pagoEfectivo = $item->hairSalonTransactions->where('paymentType', 'Efectivo')->sum('amount');
                                                        if ($item->deleted_at == null) { $total_movements += $pagoQr + $pagoEfectivo; }
                                                    @endphp
                                                    <td class="text-right">{{ number_format($pagoQr, 2, ',', '.') }}</td>
                                                    <td class="text-right">{{ number_format($pagoEfectivo, 2, ',', '.') }}</td>
                                                    <td class="text-right">{{ number_format($item->amount, 2, ',', '.') }}</td>
                                                </tr>
                                                @php $count++; @endphp
                                            @empty
                                                <tr><td colspan="9" class="text-center">No hay registros</td></tr>
                                            @endforelse
                                            <tr>
                                                <td colspan="8" class="text-right"><b>TOTAL</b></td>
                                                <td class="text-right"><b>{{ number_format($total_movements, 2, ',', '.') }}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- HomeService Tab -->
                            <div id="homeservice" class="tab-pane fade">
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-hover table-custom">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center; width: 2%">N&deg;</th>
                                                <th style="text-align: center; width: 5%">Código</th>
                                                <th style="text-align: center; width: 15%">Cliente</th>
                                                <th style="text-align: center;">Dirección</th>
                                                <th style="text-align: center;">Detalle</th>
                                                <th style="text-align: center; width: 8%">Fecha</th>
                                                <th style="text-align: center; width: 5%">QR</th>
                                                <th style="text-align: center; width: 5%">Efectivo</th>
                                                <th style="text-align: center; width: 5%">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $count = 1; $total_movements = 0; @endphp
                                            @forelse ($cashier->homeServices->sortByDesc('created_at') ?? [] as $item)
                                                <tr @if ($item->deleted_at) style="text-decoration: line-through; color: red;" @endif>
                                                    <td style="text-align: center;">{{ $count }}</td>
                                                    <td style="text-align: center">
                                                        @if ($item->deleted_at == null && $cashier->status == 'Abierta')
                                                            <a href="#" onclick="deleteItem('{{ route('homeservices.destroy', ['id' => $item->id]) }}')" title="Eliminar" data-toggle="modal" data-target="#modal-delete" class="btn btn-sm btn-danger delete"><i class="voyager-trash"></i></a><br>
                                                        @endif
                                                        <a href="{{ route('homeservices.show', ['id' => $item->id]) }}" target="_blank">{{ $item->id }}</a>
                                                    </td>
                                                    <td>
                                                        @if ($item->person)
                                                            {{ strtoupper($item->person->first_name) }} {{ strtoupper($item->person->paternal_surname) }}
                                                        @else
                                                            Sin Datos
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->address }}</td>
                                                    <td>{{ $item->observation }}</td>
                                                    <td style="text-align: center;">{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</td>
                                                    @php
                                                        $pagoQr = $item->homeServiceTransactions->where('paymentType', 'Qr')->sum('amount');
                                                        $pagoEfectivo = $item->homeServiceTransactions->where('paymentType', 'Efectivo')->sum('amount');
                                                        if ($item->deleted_at == null) { $total_movements += $pagoQr + $pagoEfectivo; }
                                                    @endphp
                                                    <td class="text-right">{{ number_format($pagoQr, 2, ',', '.') }}</td>
                                                    <td class="text-right">{{ number_format($pagoEfectivo, 2, ',', '.') }}</td>
                                                    <td class="text-right">{{ number_format($item->amount, 2, ',', '.') }}</td>
                                                </tr>
                                                @php $count++; @endphp
                                            @empty
                                                <tr><td colspan="9" class="text-center">No hay registros</td></tr>
                                            @endforelse
                                            <tr>
                                                <td colspan="8" class="text-right"><b>TOTAL</b></td>
                                                <td class="text-right"><b>{{ number_format($total_movements, 2, ',', '.') }}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Euthanasia Tab -->
                            <div id="euthanasia" class="tab-pane fade">
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-hover table-custom">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center; width: 2%">N&deg;</th>
                                                <th style="text-align: center; width: 5%">Código</th>
                                                <th style="text-align: center; width: 10%">Mascota</th>
                                                <th style="text-align: center;">Detalle</th>
                                                <th style="text-align: center; width: 8%">Fecha</th>
                                                <th style="text-align: center; width: 5%">QR</th>
                                                <th style="text-align: center; width: 5%">Efectivo</th>
                                                <th style="text-align: center; width: 5%">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $count = 1; $total_movements = 0; @endphp
                                            @forelse ($cashier->euthanasias->sortByDesc('created_at') ?? [] as $item)
                                                <tr @if ($item->deleted_at) style="text-decoration: line-through; color: red;" @endif>
                                                    <td style="text-align: center;">{{ $count }}</td>
                                                    <td style="text-align: center">
                                                        @if ($item->deleted_at == null && $cashier->status == 'Abierta')
                                                            <a href="#" onclick="deleteItem('{{ route('voyager.pets.euthanasia.destroy', ['id' => $item->id]) }}')" title="Eliminar" data-toggle="modal" data-target="#modal-delete" class="btn btn-sm btn-danger delete"><i class="voyager-trash"></i></a><br>
                                                        @endif
                                                        <a href="{{ route('voyager.pets.show', ['id' => $item->pet_id]) }}" target="_blank">{{ $item->id }}</a>
                                                    </td>
                                                    <td>{{ strtoupper($item->pet->name ?? 'Sin Nombre') }}</td>
                                                    <td>{{ $item->observation }}</td>
                                                    <td style="text-align: center;">{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</td>
                                                    @php
                                                        $pagoQr = $item->euthanasiaTransactions->where('paymentType', 'Qr')->sum('amount');
                                                        $pagoEfectivo = $item->euthanasiaTransactions->where('paymentType', 'Efectivo')->sum('amount');
                                                        if ($item->deleted_at == null) { $total_movements += $pagoQr + $pagoEfectivo; }
                                                    @endphp
                                                    <td class="text-right">{{ number_format($pagoQr, 2, ',', '.') }}</td>
                                                    <td class="text-right">{{ number_format($pagoEfectivo, 2, ',', '.') }}</td>
                                                    <td class="text-right">{{ number_format($item->amount, 2, ',', '.') }}</td>
                                                </tr>
                                                @php $count++; @endphp
                                            @empty
                                                <tr><td colspan="8" class="text-center">No hay registros</td></tr>
                                            @endforelse
                                            <tr>
                                                <td colspan="7" class="text-right"><b>TOTAL</b></td>
                                                <td class="text-right"><b>{{ number_format($total_movements, 2, ',', '.') }}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Info & Management -->
            <div class="col-md-4">
                <!-- General Info -->
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title">Información General</h3>
                    </div>
                    <div class="panel-body">
                        <ul class="list-group list-group-flush" style="margin-bottom: 0;">
                            <li class="list-group-item">
                                <strong>Cajero:</strong> <span class="pull-right">{{ $cashier->user->name }}</span>
                            </li>
                            <li class="list-group-item">
                                <strong>Estado:</strong> 
                                <span class="pull-right label label-{{ $cashier->status == 'Abierta' ? 'success' : ($cashier->status == 'Cerrada' ? 'danger' : 'warning') }}" style="font-size: 100%;">
                                    {{ $cashier->status }}
                                </span>
                            </li>
                            <li class="list-group-item">
                                <strong>Apertura:</strong> <span class="pull-right">{{ date('d/m/Y H:i', strtotime($cashier->created_at)) }}</span>
                            </li>
                            @if($cashier->closed_at)
                            <li class="list-group-item">
                                <strong>Cierre:</strong> <span class="pull-right">{{ date('d/m/Y H:i', strtotime($cashier->closed_at)) }}</span>
                            </li>
                            @endif
                            <li class="list-group-item">
                                <strong>Descripción:</strong> <br>
                                <small>{{ $cashier->title }}</small>
                            </li>
                            @if($cashier->observations)
                            <li class="list-group-item">
                                <strong>Observaciones:</strong> <br>
                                <small>{{ $cashier->observations }}</small>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Inputs (Abonos) -->
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-double-up text-success"></i> Ingresos (Abonos)</h3>
                    </div>
                    <div class="panel-body p-0">
                        <div class="table-responsive" style="max-height: 300px;">
                            <table class="table table-hover table-custom">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">N&deg;</th>
                                        <th style="text-align: center">Detalle</th>
                                        <th style="text-align: right; width: 12%">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $count = 1;
                                        $cashierInput = 0;
                                    @endphp
                                    @forelse ($cashier->movements->where('type', 'Ingreso')->where('deleted_at', null) as $item)
                                        <tr>
                                            <td>{{ $count }}</td>
                                            <td>
                                                {{ $item->description }} <br>
                                                <small>{{ $item->user->name }} - {{ date('d/m/Y h:i a', strtotime($item->created_at)) }}</small>
                                                @if ($item->transferCashier_id)
                                                    <label class="label label-info">Trasferencia</label>
                                                @endif
                                            </td>
                                            <td style="text-align: right">{{ number_format($item->amount, 2, ',', '.') }}</td>
                                        </tr>
                                        @php
                                            $cashierInput += $item->amount;
                                            $count++;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="3">No hay datos</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" style="text-align: right"><b>TOTAL</b></td>
                                        <td style="text-align: right"><b>{{ number_format($cashierInput, 2, ',', '.') }}</b></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Expenses -->
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-double-down text-danger"></i> Gastos</h3>
                    </div>
                    <div class="panel-body p-0">
                        <div class="table-responsive" style="max-height: 300px;">
                            <table class="table table-hover table-custom">
                                <thead>
                                    <tr>
                                        <th style="width: 50px"></th>
                                        <th>Detalle</th>
                                        <th style="width: 10%;">Precio</th>
                                        <th style="width: 10%;">Cant.</th>
                                        <th style="text-align: right; width: 12%">Total</th>
                                        <th class="text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total_expense = 0;
                                        $total_expense_deleted = 0;
                                        
                                        $merged_expenses = collect();

                                        // Gastos
                                        foreach($cashier->expenses as $item){
                                            $item->setAttribute('expense_type', 'expense');
                                            $item->setAttribute('expense_label', 'Gasto');
                                            $item->setAttribute('route_delete', route('expenses.destroy', ['expense' => $item->id]));
                                            $item->setAttribute('transactions', $item->expenseTransactions);
                                            $item->setAttribute('detail_description', $item->observation);
                                            $merged_expenses->push($item);
                                        }

                                        // Adelantos
                                        foreach($cashier->advancePayments as $item){
                                            $item->setAttribute('expense_type', 'advance');
                                            $item->setAttribute('expense_label', 'Adelanto');
                                            $item->setAttribute('price', $item->amount);
                                            $item->setAttribute('quantity', 1);
                                            $item->setAttribute('route_delete', route('workers.advancepayment.destroy', ['advance' => $item->id]));
                                            $item->setAttribute('transactions', $item->advancePaymentTransactions);
                                            $workerName = $item->worker ? ($item->worker->first_name . ' ' . $item->worker->paternal_surname) : 'Sin Trabajador';
                                            $item->setAttribute('detail_description', 'Adelanto: ' . $workerName . ' - ' . $item->observation);
                                            $merged_expenses->push($item);
                                        }

                                        // Planillas
                                        foreach($cashier->paymentSheets as $item){
                                            $item->setAttribute('expense_type', 'sheet');
                                            $item->setAttribute('expense_label', 'Planilla');
                                            $item->setAttribute('price', $item->amount);
                                            $item->setAttribute('quantity', 1);
                                            $item->setAttribute('route_delete', route('paymentsheets.destroy', ['id' => $item->id]));
                                            $item->setAttribute('transactions', $item->paymentSheetTransactions);
                                            $workerName = $item->worker ? ($item->worker->first_name . ' ' . $item->worker->paternal_surname) : 'Sin Trabajador';
                                            $item->setAttribute('detail_description', 'Planilla: ' . $workerName . ' - ' . $item->observation);
                                            $merged_expenses->push($item);
                                        }
                                        
                                        $merged_expenses = $merged_expenses->sortByDesc('created_at');
                                    @endphp
                                    @forelse ($merged_expenses as $item)
                                        <tr class="tr-main" data-id="expense-{{ $item->expense_type }}-{{ $item->id }}" style="cursor: pointer; @if ($item->deleted_at) text-decoration: line-through; color: red; @endif">
                                            <td style="text-align: center; vertical-align: middle"><i class="voyager-angle-down icon-toggle" id="icon-expense-{{ $item->expense_type }}-{{ $item->id }}"></i></td>
                                            <td style="vertical-align: middle">
                                                <label class="label label-{{ $item->expense_type == 'expense' ? 'default' : ($item->expense_type == 'advance' ? 'warning' : 'primary') }}">{{ $item->expense_label }}</label>
                                                {{ \Illuminate\Support\Str::limit($item->detail_description, 40) }} <br>
                                                <small>{{ date('d/m/Y h:i a', strtotime($item->created_at)) }}</small>
                                            </td>
                                            <td class="text-right" style="vertical-align: middle">{{ number_format($item->price, 2, ',', '.') }}</td>
                                            <td class="text-right" style="vertical-align: middle">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                                            <td class="text-right" style="vertical-align: middle">{{ number_format($item->amount, 2, ',', '.') }}</td>
                                            <td class="no-sort no-click bread-actions text-right" style="vertical-align: middle">
                                                @if ($item->deleted_at == null && $cashier->status == 'Abierta')
                                                    <a href="#" title="Eliminar" class="btn btn-sm btn-danger delete" data-toggle="modal" data-target="#modal-delete" onclick="deleteItem('{{ $item->route_delete }}')">
                                                        <i class="voyager-trash"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr id="tr-details-expense-{{ $item->expense_type }}-{{ $item->id }}" style="display: none; background-color: #f5f5f5;">
                                            <td colspan="6" style="padding: 20px;">
                                                <div class="row">
                                                    <div class="col-md-12" style="margin-bottom: 10px;"><label class="label label-default">Detalle completo:</label> <p>{{ $item->detail_description }}</p></div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted">Registrado por:</small>
                                                        <h5>{{ $item->register->name ?? 'No especificado' }}</h5>
                                                        <b>{{date('d/m/Y h:m:s a', strtotime($item->created_at))}}</b> <br>
                                                        <small>{{\Carbon\Carbon::parse($item->created_at)->diffForHumans()}}</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted">Desglose de Pago:</small>
                                                        <table style="width: 100%; font-size: 12px;">
                                                            @php
                                                                $pago_efectivo = $item->transactions->where('paymentType', 'Efectivo')->sum('amount');
                                                                $pago_qr = $item->transactions->where('paymentType', 'Qr')->sum('amount');
                                                            @endphp
                                                            <tr><td>Efectivo:</td><td class="text-right">Bs. {{ number_format($pago_efectivo, 2) }}</td></tr>
                                                            <tr><td>QR/Transferencia:</td><td class="text-right">Bs. {{ number_format($pago_qr, 2) }}</td></tr>
                                                            <tr style="border-top: 1px solid #ccc; font-weight: bold;"><td>Total:</td><td class="text-right">Bs. {{ number_format($item->amount, 2) }}</td></tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @php
                                            if($item->deleted_at==null) { $total_expense += $item->amount; }
                                            else { $total_expense_deleted += $item->amount; }
                                        @endphp
                                    @empty
                                        <tr>
                                            <td style="text-align: center" colspan="6">No hay datos</td>
                                        </tr>
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

            </div>
        </div>
    </div>

    @include('partials.modal-delete')




@stop

@section('javascript')
<script>

    function deleteItem(url) {
        $('#delete_form').attr('action', url);
    }

    $(document).ready(function() {
        $('.btn-delete').click(function() {
            let loan_id = $(this).data('id');
            $(`#form-delete input[name="loan_id"]`).val(loan_id);
        });

        $('table').on('click', '.tr-main td', function(e){
            if($(this).hasClass('bread-actions') || $(this).find('a').length > 0 || $(this).find('button').length > 0) return;
            let tr = $(this).parent();
            let id = tr.data('id');
            if (id) {
                $(`#tr-details-${id}`).fadeToggle();
                $(`#icon-${id}`).toggleClass('voyager-angle-down voyager-angle-up');
            }
        });
    });
</script>
@stop
