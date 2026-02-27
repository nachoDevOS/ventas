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
<form action="{{ route('items-stock.store', ['id' => $item->id]) }}" class="form-submit" method="POST">
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
    <script src="{{ url('js/main.js') }}"></script>
    <script>
        var countPage = 10, timeout = null;

        $(document).ready(() => {
            list();
            listSales();

            $('#status-stock').change(function () { list(); });
            $('#input-search-stock').on('keyup input', function () {
                clearTimeout(timeout);
                timeout = setTimeout(() => list(), 800);
            });
            $('#select-paginate-stock').change(function () { countPage = $(this).val(); list(); });
            $('#select-paginate-direct-sales').change(function () { countPage = $(this).val(); listSales(); });

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
                url: `${url}?search=${search}&paginate=${countPage}&page=${page}&status=${status}`,
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
                url: `${url}?paginate=${countPage}&page=${page}`,
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
    </script>
@stop
