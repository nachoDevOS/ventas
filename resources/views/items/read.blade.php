@extends('voyager::master')

@section('page_title', 'Ver Producto')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body" style="padding: 0px">
                        <div class="col-md-8" style="padding: 0px; display: flex; align-items: center;">
                            <h1 class="page-title">
                                <i class="fa-solid fa-box-open"></i> Productos / Items
                            </h1>
                        </div>
                        <div class="col-md-4 text-right" style="margin-top: 30px">
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

    {{-- ══════════════════════════════════════════════════
         CABECERA DEL PRODUCTO
    ══════════════════════════════════════════════════ --}}
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered" style="margin-bottom: 10px;">
                <div class="panel-body" style="padding: 10px 15px;">
                    <div class="row">

                        {{-- Imagen + Nombre --}}
                        <div class="col-md-2" style="text-align: center; padding-top: 4px;">
                            @php
                                $image = $item->image ? Voyager::image($item->image) : asset('images/default.jpg');
                            @endphp
                            <img src="{{ $image }}"
                                 style="width: 180px; height: 180px; object-fit: cover;
                                        border-radius: 6px; border: 2px solid #eee;"
                                 alt="{{ $item->nameTrade ?? $item->nameGeneric }}">
                            <div style="margin-top: 5px;">
                                <div style="font-weight: 700; color: #2c3e50; font-size: 16px; line-height: 1.3;">
                                    {{ strtoupper($item->nameGeneric) }}
                                </div>
                                @if($item->nameTrade)
                                    <div style="font-size: 14px; color: #000000; margin-top: 2px;">
                                        {{ $item->nameTrade }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Info principal --}}
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="item-info-card">
                                        <span class="item-info-label">Laboratorio</span>
                                        <span class="item-info-value">{{ $item->laboratory ? strtoupper($item->laboratory->name) : '—' }}</span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="item-info-card">
                                        <span class="item-info-label">Línea</span>
                                        <span class="item-info-value">{{ $item->line ? strtoupper($item->line->name) : '—' }}</span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="item-info-card">
                                        <span class="item-info-label">Estado</span>
                                        <span class="item-info-value">
                                            @if($item->status)
                                                <span class="label label-success" style="font-size: 10px;">Activo</span>
                                            @else
                                                <span class="label label-danger" style="font-size: 10px;">Inactivo</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                {{-- Fracción --}}
                                @if ($item->fraction)
                                <div class="col-md-6">
                                    <div class="item-info-card item-fraction-card">
                                        <span class="item-info-label">
                                            <i class="fa-solid fa-puzzle-piece"></i> Venta por Fracción
                                            <span class="label label-info" style="font-size: 8px; margin-left: 3px; vertical-align: middle;">Habilitada</span>
                                        </span>
                                        <div style="margin-top: 5px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                            <div style="text-align: center; background: #fff; border: 1px solid #c8e6c9; border-radius: 5px; padding: 4px 12px; min-width: 80px;">
                                                <div style="font-size: 10px; color: #888; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px;">1 Unidad</div>
                                                <div style="font-size: 13px; font-weight: 700; color: #2e7d32;">
                                                    {{ $item->presentation ? strtoupper($item->presentation->name) : '—' }}
                                                </div>
                                            </div>
                                            <div style="font-size: 16px; color: #aaa;">→</div>
                                            <div style="text-align: center; background: #fff; border: 1px solid #90caf9; border-radius: 5px; padding: 4px 12px; min-width: 80px;">
                                                <div style="font-size: 10px; color: #888; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px;">Contiene</div>
                                                <div style="font-size: 13px; font-weight: 700; color: #1565c0;">
                                                    {{ number_format($item->fractionQuantity, 0, ',', '.') }}
                                                    {{ strtoupper($item->fractionPresentation->name) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div style="margin-top: 4px; font-size: 10px; color: #777;">
                                            <i class="fa-solid fa-circle-info"></i>
                                            Vender por unidad <b>({{ $item->presentation ? strtoupper($item->presentation->name) : '—' }})</b>
                                            o por fracción <b>({{ strtoupper($item->fractionPresentation->name) }})</b>.
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="col-md-3">
                                    <div class="item-info-card">
                                        <span class="item-info-label">
                                            <i class="fa-solid fa-puzzle-piece"></i> Venta por Fracción
                                        </span>
                                        <span class="item-info-value">
                                            <span class="label label-default" style="font-size: 10px;">Sin fracción</span>
                                        </span>
                                    </div>
                                </div>
                                @endif

                                <div class="col-md-6" style="margin-top: 2px;">
                                    <div class="item-info-card" style="background: #fffde7; border-color: #ffe082;">
                                        <span class="item-info-label">
                                            <i class="fa-solid fa-note-sticky"></i> Descripción / Observación
                                        </span>
                                        <span class="item-info-value" style="color: #7a6000; font-weight: 400; white-space: pre-line;">
                                            {{ $item->observation ?: '—' }}
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         STOCK MÍNIMO
    ══════════════════════════════════════════════════ --}}
    @php
        $totalStockItem = $item->itemStocks()->where('deleted_at', null)->sum('stock');
        $isBelowMin = $item->stockMinimum && $item->stockMinimum > 0 && $totalStockItem < $item->stockMinimum;
    @endphp
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered" style="margin-bottom: 10px;
                 {{ $isBelowMin ? 'border-color: #e74c3c;' : '' }}">
                <div class="panel-body" style="padding: 10px 15px;">
                    <div class="row" style="display: flex; align-items: center;">

                        {{-- Ícono + título --}}
                        <div class="col-md-1 text-center" style="padding-top: 4px;">
                            <i class="fa-solid fa-gauge-high" style="font-size: 28px;
                               color: {{ $isBelowMin ? '#c0392b' : '#27ae60' }};"></i>
                        </div>

                        {{-- Info actual --}}
                        <div class="col-md-4">
                            <div style="font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 0.5px;">
                                Stock mínimo configurado
                            </div>
                            <div style="font-size: 18px; font-weight: 700;
                                        color: {{ $isBelowMin ? '#c0392b' : '#2c3e50' }};">
                                {{ $item->stockMinimum ? number_format($item->stockMinimum, 0, ',', '.') . ' ' . ($item->presentation->name ?? 'Unid.') : '—  Sin configurar' }}
                            </div>
                            @if ($item->stockMinimum)
                                @if ($isBelowMin)
                                    <span style="color:#c0392b; font-size:12px;">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                        Stock actual ({{ $totalStockItem }}) está por debajo del mínimo
                                    </span>
                                @else
                                    <span style="color:#27ae60; font-size:12px;">
                                        <i class="fa-solid fa-circle-check"></i>
                                        Stock actual ({{ $totalStockItem }}) sobre el mínimo
                                    </span>
                                @endif
                            @else
                                <span style="color:#aaa; font-size:12px;">
                                    <i class="fa-solid fa-circle-info"></i>
                                    Configure un mínimo para recibir alertas
                                </span>
                            @endif
                        </div>

                        {{-- Barra de progreso --}}
                        @if ($item->stockMinimum > 0)
                        <div class="col-md-4" style="padding-top: 4px;">
                            @php
                                $pct = min(100, ($totalStockItem / $item->stockMinimum) * 100);
                                $barColor = $pct < 50 ? '#e74c3c' : ($pct < 100 ? '#f39c12' : '#27ae60');
                            @endphp
                            <div style="font-size: 10px; color:#888; margin-bottom: 4px;">
                                {{ number_format($pct, 0) }}% del mínimo
                            </div>
                            <div style="background:#eee; border-radius: 4px; height: 8px; overflow:hidden;">
                                <div style="width: {{ $pct }}%; background: {{ $barColor }}; height: 100%; border-radius: 4px; transition: width 0.4s;"></div>
                            </div>
                        </div>
                        @else
                        <div class="col-md-4"></div>
                        @endif

                        {{-- Formulario para editar --}}
                        @if (auth()->user()->hasPermission('edit_items'))
                        <div class="col-md-3 text-right">
                            <form action="{{ route('items.update.minimum', ['id' => $item->id]) }}" method="POST"
                                  style="display: flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                @csrf
                                @method('PATCH')
                                <div class="input-group" style="max-width: 160px;">
                                    <input type="number" name="stockMinimum" class="form-control input-sm"
                                           value="{{ $item->stockMinimum ?? '' }}"
                                           min="0" step="1" placeholder="0"
                                           style="text-align: right;">
                                    <span class="input-group-addon" style="font-size: 11px;">
                                        {{ $item->presentation->name ?? 'Unid.' }}
                                    </span>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                </button>
                            </form>
                            <div style="font-size: 10px; color: #aaa; margin-top: 4px; text-align: right;">
                                Dejar vacío para quitar la alerta
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         TABS: Inventario + Ventas
    ══════════════════════════════════════════════════ --}}
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-body" style="padding: 0;">

                    <ul class="nav nav-tabs" style="padding: 0 15px; background: #f8f9fa; border-bottom: 1px solid #ddd; margin: 0;">
                        <li class="active">
                            <a data-toggle="tab" href="#tab-stock" style="font-size: 13px; font-weight: 600;">
                                <i class="fa-solid fa-boxes-stacked"></i> Inventario
                            </a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#tab-sales" style="font-size: 13px; font-weight: 600;">
                                <i class="fa-solid fa-cash-register"></i> Ventas
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" style="padding: 15px;">

                        {{-- ── Tab Inventario ── --}}
                        <div id="tab-stock" class="tab-pane fade in active">
                            <div class="row" style="margin-bottom: 10px;">
                                <div class="col-sm-6">
                                    <h4 style="margin: 0; font-size: 15px; font-weight: 600;">
                                        <i class="fa-solid fa-warehouse"></i> Stock del Producto
                                    </h4>
                                </div>
                                <div class="col-sm-6 text-right">
                                    @if (auth()->user()->hasPermission('browse_items'))
                                        <button class="btn btn-success btn-sm"
                                                data-target="#modal-register-stock" data-toggle="modal">
                                            <i class="fa-solid fa-plus"></i> Agregar Stock
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 10px;">
                                <div class="col-sm-6">
                                    <label style="font-weight: normal;">
                                        Mostrar
                                        <select id="select-paginate-stock" class="form-control input-sm" style="display:inline-block; width:auto;">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        registros
                                    </label>
                                </div>
                                <div class="col-sm-2">
                                    <select id="status-stock" name="status-stock" class="form-control input-sm">
                                        <option value="" selected>Todos</option>
                                        <option value="1">Con Stock</option>
                                        <option value="0">Sin Stock</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" id="input-search-stock"
                                           placeholder="🔍 Buscar en lote..." class="form-control input-sm">
                                </div>
                            </div>
                            <div class="row" id="div-results" style="min-height: 120px;"></div>
                        </div>

                        {{-- ── Tab Ventas ── --}}
                        <div id="tab-sales" class="tab-pane fade">
                            <div class="row" style="margin-bottom: 10px;">
                                <div class="col-sm-6">
                                    <h4 style="margin: 0; font-size: 15px; font-weight: 600;">
                                        <i class="fa-solid fa-receipt"></i> Historial de Ventas
                                    </h4>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <label style="font-weight: normal;">
                                        Mostrar
                                        <select id="select-paginate-direct-sales" class="form-control input-sm" style="display:inline-block; width:auto;">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        registros
                                    </label>
                                </div>
                            </div>
                            <div class="row" id="div-results-direct-sales" style="min-height: 120px;"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Modal Registrar Stock --}}
<form action="{{ route('items-stock.store', ['id' => $item->id]) }}" class="form-edit-add form-submit" method="POST">
    <div class="modal fade" data-backdrop="static" id="modal-register-stock" role="dialog">
        <div class="modal-dialog modal-lg modal-success">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title" style="color:#fff;">
                        <i class="voyager-plus"></i> Registrar Stock
                    </h4>
                </div>
                <div class="modal-body" style="background-color: #f5f5f5;">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default" style="box-shadow:none; border:1px solid #ddd;">
                                <div class="panel-body">
                                    <h5 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:10px; margin-bottom:15px; font-weight:bold; color:#555;">
                                        <i class="fa-solid fa-box"></i> Datos del Lote
                                    </h5>
                                    <div class="row">
                                        <div class="form-group col-md-5">
                                            <label>Lote / Código</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa-solid fa-barcode"></i></span>
                                                <input type="text" id="lote" name="lote" class="form-control" placeholder="Ej. L-2023-X">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Fecha de Expiración</label>
                                            <input type="date" id="expirationDate" name="expirationDate" class="form-control"
                                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                        </div>
                                        @if ($item->fraction)
                                        {{-- Item fraccionado: campo unidades + fracciones adicionales --}}
                                        <div class="form-group col-md-2">
                                            <label>{{ strtoupper($item->presentation->name ?? 'Unidades') }}</label>
                                            <input style="text-align:right" type="number" id="quantity"
                                                   step="1" min="0" name="quantity" class="form-control"
                                                   value="0" placeholder="0" oninput="updateStockSummary()">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>{{ strtoupper($item->fractionPresentation->name ?? 'Fracciones') }} adicionales</label>
                                            <input style="text-align:right" type="number" id="extraFractions"
                                                   step="1" min="0" name="extraFractions" class="form-control"
                                                   value="0" placeholder="0" oninput="updateStockSummary()">
                                        </div>
                                        <div class="col-md-2" style="padding-top: 26px;">
                                            <div id="stock-summary" style="background:#f0fdf4; border:1px solid #c8e6c9;
                                                                            border-radius:5px; padding:6px 8px; font-size:12px;">
                                                Total: <b id="summary-text" style="color:#1565c0;">—</b>
                                            </div>
                                        </div>
                                        @else
                                        {{-- Item sin fracción: campo único --}}
                                        <div class="form-group col-md-3">
                                            <label>Cantidad</label>
                                            <input style="text-align:right" type="number" id="quantity"
                                                   step="1" min="1" name="quantity" class="form-control" required placeholder="0">
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default" style="box-shadow:none; border:1px solid #ddd;">
                                <div class="panel-body">
                                    <h5 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:10px; margin-bottom:15px; font-weight:bold; color:#555;">
                                        <i class="fa-solid fa-money-bill-trend-up"></i> Precios y Costos
                                    </h5>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label>P. Compra (por Unidad)</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">Bs.</span>
                                                <input style="text-align:right" type="number" id="pricePurchase"
                                                       step="0.01" min="0" name="pricePurchase" class="form-control" required placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>P. Venta (por Unidad)</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">Bs.</span>
                                                <input style="text-align:right" type="number" id="priceSale"
                                                       step="0.01" min="0.1" name="priceSale" class="form-control" required placeholder="0.00">
                                            </div>
                                            <div id="unit-profit-info" class="alert alert-info"
                                                 style="padding:5px 10px; margin-top:5px; margin-bottom:0; font-size:12px; display:none;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($item->fraction)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-warning" style="box-shadow:none; border:1px solid #f39c12;">
                                <div class="panel-heading" style="background:#fcf8e3; color:#8a6d3b; padding:10px 15px; border-bottom:1px solid #faebcc;">
                                    <h3 class="panel-title" style="font-size:14px; font-weight:bold;">
                                        <i class="fa-solid fa-puzzle-piece"></i> Venta Fraccionada
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <p style="margin-top:5px; font-size:13px;">
                                                Este producto se divide en
                                                <b>{{ number_format($item->fractionQuantity, 2, ',', '.') }}
                                                {{ strtoupper($item->fractionPresentation->name) }}</b>.
                                                <br><small class="text-muted">Defina el precio por cada fracción.</small>
                                            </p>
                                        </div>
                                        <div class="form-group col-md-5">
                                            <label>Precio por {{ strtoupper($item->fractionPresentation->name) }}</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">Bs.</span>
                                                <input style="text-align:right" type="number" id="dispensedPrice"
                                                       step="0.1" min="0.1" name="dispensedPrice" class="form-control" required placeholder="0.00">
                                            </div>
                                            <div id="fraction-profit-info" class="alert alert-info"
                                                 style="padding:5px 10px; margin-top:5px; margin-bottom:0; font-size:12px; display:none;"></div>
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
                                <label>Observación / Detalles</label>
                                <textarea id="observation" name="observation" class="form-control"
                                          rows="2" placeholder="Observaciones opcionales..."></textarea>
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
                    <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">Cancelar</button>
                    <input type="submit" class="btn btn-success btn-form-submit btn-submit" value="Guardar">
                </div>
            </div>
        </div>
    </div>
</form>

{{-- ══════════════════════════════════════════════════
     Modal Egreso de Stock (se rellena vía JS por fila)
══════════════════════════════════════════════════ --}}
<form id="form-egress-stock" action="" method="POST">
    @csrf
    <div class="modal fade" data-backdrop="static" id="modal-egress-stock" role="dialog">
        <div class="modal-dialog modal-lg modal-warning">
            <div class="modal-content">
                <div class="modal-header" style="background:#c0392b; border-radius:3px 3px 0 0;">
                    <button type="button" class="close" data-dismiss="modal"><span style="color:#fff;">&times;</span></button>
                    <h4 class="modal-title" style="color:#fff;">
                        <i class="fa-solid fa-arrow-up-from-bracket"></i> Egreso de Stock
                    </h4>
                </div>
                <div class="modal-body" style="background:#f5f5f5;">

                    {{-- Info del lote --}}
                    <div style="background:#fff3cd; border:1px solid #ffc107; border-radius:5px;
                                padding:12px 15px; margin-bottom:15px;">
                        <div style="font-size:10px; color:#856404; text-transform:uppercase;
                                    font-weight:700; letter-spacing:0.5px; margin-bottom:4px;">
                            <i class="fa-solid fa-layer-group"></i> Lote seleccionado
                        </div>
                        <div style="font-size:15px; font-weight:700; color:#2c3e50;" id="egress-lote-name">—</div>
                        <div style="font-size:12px; color:#666; margin-top:4px;">
                            <i class="fa-solid fa-box" style="color:#27ae60;"></i>
                            Disponible: <b id="egress-available-text" style="color:#27ae60;">—</b>
                        </div>
                    </div>

                    {{-- Cantidad a egresar --}}
                    <div class="panel panel-default" style="box-shadow:none; border:1px solid #ddd;">
                        <div class="panel-body">
                            <h5 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:8px;
                                       margin-bottom:12px; font-weight:bold; color:#555;">
                                <i class="fa-solid fa-minus"></i> Cantidad a Egresar
                            </h5>
                            <div class="row">
                                @if ($item->fraction)
                                <div class="form-group col-md-3">
                                    <label>{{ strtoupper($item->presentation->name ?? 'Unidades') }}</label>
                                    <input style="text-align:right" type="number" id="egress-quantity"
                                           name="quantity" step="1" min="0" value="0"
                                           class="form-control" placeholder="0"
                                           oninput="updateEgressSummary()">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>{{ strtoupper($item->fractionPresentation->name ?? 'Fracciones') }} adicionales</label>
                                    <input style="text-align:right" type="number" id="egress-extra-fractions"
                                           name="extraFractions" step="1" min="0" value="0"
                                           class="form-control" placeholder="0"
                                           oninput="updateEgressSummary()">
                                </div>
                                @else
                                <div class="form-group col-md-4">
                                    <label>Cantidad</label>
                                    <input style="text-align:right" type="number" id="egress-quantity"
                                           name="quantity" step="1" min="1" value=""
                                           class="form-control" placeholder="0"
                                           oninput="updateEgressSummary()">
                                </div>
                                @endif
                                <div class="col-md-4" style="padding-top:26px;">
                                    <div style="background:#fef2f2; border:1px solid #fca5a5;
                                                border-radius:5px; padding:6px 10px; font-size:12px;">
                                        Total a sacar: <b id="egress-summary-text" style="color:#c0392b;">—</b>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Razón del egreso --}}
                    <div class="panel panel-default" style="box-shadow:none; border:1px solid #e74c3c;">
                        <div class="panel-heading" style="background:#fdf2f2; color:#922b21;
                                                          padding:10px 15px; border-bottom:1px solid #f1948a;">
                            <h3 class="panel-title" style="font-size:14px; font-weight:bold;">
                                <i class="fa-solid fa-clipboard-question"></i> Razón del Egreso
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group" style="margin-bottom:10px;">
                                <label>Motivo <span class="text-danger">*</span></label>
                                <textarea id="egress-reason" name="reason" class="form-control" rows="2" required
                                          placeholder="Ej: Producto vencido, daño físico, ajuste de inventario, robo..."></textarea>
                                <small class="text-muted">Se registrará en la observación del lote para trazabilidad.</small>
                            </div>
                            <div class="checkbox" style="margin:0;">
                                <label>
                                    <input type="checkbox" id="egress-confirm"> Confirmar egreso de stock
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" id="egress-submit-btn" class="btn btn-danger" disabled>
                        <i class="fa-solid fa-arrow-up-from-bracket"></i> Registrar Egreso
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@include('partials.modal-delete')
@stop

@section('css')
<style>
    .item-info-card {
        background: #f8f9fa;
        border: 1px solid #eee;
        border-radius: 5px;
        padding: 5px 10px;
        margin-bottom: 6px;
    }
    .item-info-label {
        display: block;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #999;
        font-weight: 600;
        margin-bottom: 2px;
    }
    .item-info-value {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #333;
    }
    .item-fraction-card {
        background: linear-gradient(135deg, #f0fdf4 0%, #eff6ff 100%);
        border: 1px solid #c8e6c9 !important;
    }
    .nav-tabs > li > a { border-radius: 0; padding: 12px 18px; }
    .nav-tabs > li.active > a { border-bottom: 3px solid #3498db; color: #3498db; }
</style>
@stop

@section('javascript')
    <script src="{{ asset('js/btn-submit.js') }}"></script>
    <script src="{{ asset('js/input-numberBlock.js') }}"></script>
    <script src="{{ url('js/main.js') }}"></script>
    <script>
        var countPageStock = 10, countPageSales = 10, timeout = null;

        $(document).ready(() => {
            list();
            listSales();

            $('#status-stock').change(function () { list(); });
            $('#input-search-stock').on('keyup input', function () {
                clearTimeout(timeout);
                timeout = setTimeout(() => list(), 800);
            });
            $('#select-paginate-stock').change(function () { countPageStock = $(this).val(); list(); });
            $('#select-paginate-direct-sales').change(function () { countPageSales = $(this).val(); listSales(); });

            $('#expirationDate').on('change', function () {
                let date = $(this).val();
                if (date) {
                    let today = new Date(); today.setHours(0,0,0,0);
                    let parts = date.split('-');
                    let selected = new Date(parts[0], parts[1]-1, parts[2]);
                    if (selected <= today) {
                        toastr.warning('La fecha de expiración debe ser mayor a la fecha actual.', 'Advertencia');
                        $(this).val('');
                    }
                }
            });

            $('#priceSale').on('change', function () {
                let purchase = parseFloat($('#pricePurchase').val()) || 0;
                let sale     = parseFloat($(this).val()) || 0;
                if (purchase > 0 && sale > 0 && sale < purchase) {
                    toastr.error('El precio de venta genera pérdidas.', 'Error');
                    $(this).val('');
                }
            });

            @if ($item->fraction)
            updateStockSummary();
            $('.form-submit').submit(function (e) {
                let units  = parseInt($('#quantity').val()) || 0;
                let fracs  = parseInt($('#extraFractions').val()) || 0;
                if ((units + fracs) <= 0) {
                    e.preventDefault();
                    toastr.error('Debe ingresar al menos una unidad o fracción.', 'Cantidad inválida');
                    return false;
                }
                $('.btn-form-submit').attr('disabled', true).val('Guardando...');
            });
        @else
        $('.form-submit').submit(function () {
                $('.btn-form-submit').attr('disabled', true).val('Guardando...');
            });
        @endif
            $('#delete_form').submit(function () {
                $('.btn-form-delete').attr('disabled', true).val('Eliminando...');
            });
        });

        function list(page = 1) {
            $('#div-results').loading({ message: 'Cargando...' });
            let url    = '{{ url("admin/items/".$item->id."/stock/ajax/list") }}';
            let search = $('#input-search-stock').val() || '';
            let status = $('#status-stock').val();
            $.ajax({
                url: `${url}?search=${search}&paginate=${countPageStock}&page=${page}&status=${status}`,
                type: 'get',
                success: function (result) {
                    $('#div-results').html(result);
                    $('#div-results').loading('toggle');
                }
            });
        }

        @if ($item->fraction)
        function updateStockSummary() {
            const fracQty  = {{ $item->fractionQuantity }};
            const unitName = '{{ strtoupper($item->presentation->name ?? "Unid.") }}';
            const fracName = '{{ strtoupper($item->fractionPresentation->name ?? "Frac.") }}';
            const units    = parseInt($('#quantity').val()) || 0;
            const fracs    = parseInt($('#extraFractions').val()) || 0;
            const total    = (units * fracQty) + fracs;

            if (total <= 0) {
                $('#summary-text').text('—');
                return;
            }

            const storedUnits = Math.ceil(total / fracQty);
            const remaining   = (storedUnits * fracQty) - total;
            let text = '';
            const fullUnits = Math.floor(total / fracQty);
            const leftOver  = total % fracQty;

            if (fullUnits > 0 && leftOver > 0) {
                text = `${fullUnits} ${unitName} + ${leftOver} ${fracName}`;
            } else if (fullUnits > 0) {
                text = `${fullUnits} ${unitName}`;
            } else {
                text = `${leftOver} ${fracName}`;
            }
            $('#summary-text').text(text);
        }
        @endif

        function listSales(page = 1) {
            $('#div-results-direct-sales').loading({ message: 'Cargando...' });
            let url = '{{ url("admin/items/".$item->id."/sales/ajax/list") }}';
            $.ajax({
                url: `${url}?paginate=${countPageSales}&page=${page}`,
                type: 'get',
                success: function (result) {
                    $('#div-results-direct-sales').html(result);
                    $('#div-results-direct-sales').loading('toggle');
                }
            });
        }

        function deleteItem(url) { $('#delete_form').attr('action', url); }

        @if ($item->fraction)
        $(document).ready(function () {
            const fractionQuantity = {{ $item->fractionQuantity }};
            function calculateProfits() {
                let purchase  = parseFloat($('#pricePurchase').val());
                let dispensed = parseFloat($('#dispensedPrice').val());
                let sale      = parseFloat($('#priceSale').val());
                if (!isNaN(purchase) && purchase > 0 && !isNaN(sale) && sale > 0) {
                    let profit = sale - purchase, pct = (profit / purchase) * 100;
                    $('#unit-profit-info').html(`Ganancia: <b>${profit.toFixed(2)} Bs. (${pct.toFixed(2)}%)</b>`).show();
                } else { $('#unit-profit-info').hide(); }
                if (!isNaN(purchase) && purchase > 0 && !isNaN(dispensed) && dispensed > 0) {
                    let total = fractionQuantity * dispensed, profit = total - purchase, pct = (profit / purchase) * 100;
                    $('#fraction-profit-info').html(`Venta Total: <b>${total.toFixed(2)} Bs.</b><br>Ganancia: <b>${profit.toFixed(2)} Bs. (${pct.toFixed(2)}%)</b>`).show();
                } else { $('#fraction-profit-info').hide(); }
            }
            $('#pricePurchase, #dispensedPrice, #priceSale').on('keyup change', calculateProfits);
            $('#dispensedPrice').on('change', function () {
                let purchase = parseFloat($('#pricePurchase').val()) || 0;
                let dispensed = parseFloat($(this).val()) || 0;
                if (purchase > 0 && dispensed > 0 && (dispensed * fractionQuantity) < purchase) {
                    toastr.error('El precio fraccionado genera pérdidas.', 'Precio Inválido');
                    $(this).val(''); calculateProfits();
                }
            });
        });
        @endif

        // ── Egreso de Stock ──────────────────────────────────────────────────
        @php
            $egressIsFrac  = $item->fraction ? 'true' : 'false';
            $egressFracQty = $item->fractionQuantity ?? 1;
            $egressUnit    = strtoupper($item->presentation->name ?? 'Unid.');
            $egressFrac    = $item->fraction ? strtoupper($item->fractionPresentation->name ?? 'Frac.') : '';
        @endphp
        const egressIsFraction = {{ $egressIsFrac }};
        const egressFracQty    = {{ $egressFracQty }};
        const egressUnitName   = '{{ $egressUnit }}';
        const egressFracName   = '{{ $egressFrac }}';
        const egressBaseUrl    = '{{ url("admin/items/".$item->id."/stock") }}';

        // Stock actual del lote seleccionado
        let egressLotData = { fullUnits: 0, extraFracs: 0, totalFracs: 0, stock: 0 };

        // Clic en botón de egreso de cualquier fila (delegación — la lista es AJAX)
        $(document).on('click', '.btn-egress-lot', function () {
            const btn = $(this);
            egressLotData = {
                fullUnits  : parseInt(btn.data('full-units'))  || 0,
                extraFracs : parseInt(btn.data('extra-fracs')) || 0,
                totalFracs : parseInt(btn.data('total-fracs')) || 0,
                stock      : parseInt(btn.data('stock'))       || 0,
            };

            // Rellenar info del lote en el modal
            const lote = btn.data('lote') || 'Sin lote';
            $('#egress-lote-name').text(lote);

            let availText;
            if (egressIsFraction) {
                if (egressLotData.fullUnits > 0 && egressLotData.extraFracs > 0)
                    availText = `${egressLotData.fullUnits} ${egressUnitName} + ${egressLotData.extraFracs} ${egressFracName}`;
                else if (egressLotData.fullUnits > 0)
                    availText = `${egressLotData.fullUnits} ${egressUnitName}`;
                else
                    availText = `${egressLotData.extraFracs} ${egressFracName}`;
            } else {
                availText = `${egressLotData.stock} ${egressUnitName}`;
            }
            $('#egress-available-text').text(availText);

            // Actualizar action del form
            const stockId = btn.data('stock-id');
            $('#form-egress-stock').attr('action', `${egressBaseUrl}/${stockId}/egress`);

            // Reset campos
            $('#egress-quantity').val(0);
            if (egressIsFraction) $('#egress-extra-fractions').val(0);
            $('#egress-reason').val('');
            $('#egress-confirm').prop('checked', false);
            $('#egress-summary-text').text('—');
            $('#egress-submit-btn').prop('disabled', true);

            $('#modal-egress-stock').modal('show');
        });

        function updateEgressSummary() {
            if (egressIsFraction) {
                const units = parseInt($('#egress-quantity').val()) || 0;
                const fracs = parseInt($('#egress-extra-fractions').val()) || 0;
                const total = (units * egressFracQty) + fracs;

                if (total <= 0) { $('#egress-summary-text').text('—'); checkEgressReady(); return; }

                if (total > egressLotData.totalFracs) {
                    $('#egress-summary-text').html(`<span style="color:#e74c3c;">⚠ Supera el disponible (${egressLotData.totalFracs} ${egressFracName})</span>`);
                    checkEgressReady(); return;
                }

                const fullU = Math.floor(total / egressFracQty);
                const remF  = total % egressFracQty;
                let text = '';
                if (fullU > 0 && remF > 0) text = `${fullU} ${egressUnitName} + ${remF} ${egressFracName}`;
                else if (fullU > 0)         text = `${fullU} ${egressUnitName}`;
                else                        text = `${remF} ${egressFracName}`;
                $('#egress-summary-text').text(text);
            } else {
                const qty = parseInt($('#egress-quantity').val()) || 0;
                if (qty <= 0) { $('#egress-summary-text').text('—'); checkEgressReady(); return; }

                if (qty > egressLotData.stock) {
                    $('#egress-summary-text').html(`<span style="color:#e74c3c;">⚠ Supera el disponible (${egressLotData.stock} ${egressUnitName})</span>`);
                    checkEgressReady(); return;
                }
                $('#egress-summary-text').text(`${qty} ${egressUnitName}`);
            }
            checkEgressReady();
        }

        function checkEgressReady() {
            const hasReason  = $('#egress-reason').val().trim().length > 0;
            const hasConfirm = $('#egress-confirm').is(':checked');
            const noError    = $('#egress-summary-text span').length === 0 && $('#egress-summary-text').text() !== '—';
            $('#egress-submit-btn').prop('disabled', !(hasReason && hasConfirm && noError));
        }

        $('#egress-reason, #egress-confirm').on('change keyup', checkEgressReady);

        // Reset al cerrar modal
        $('#modal-egress-stock').on('hidden.bs.modal', function () {
            $('#egress-quantity').val(0);
            if (egressIsFraction) $('#egress-extra-fractions').val(0);
            $('#egress-reason').val('');
            $('#egress-confirm').prop('checked', false);
            $('#egress-summary-text').text('—');
            $('#egress-submit-btn').prop('disabled', true);
        });

        // Bloquear doble submit
        $('#form-egress-stock').submit(function () {
            $('#egress-submit-btn').prop('disabled', true)
                .html('<i class="fa-solid fa-spinner fa-spin"></i> Procesando...');
        });
    </script>
@stop
