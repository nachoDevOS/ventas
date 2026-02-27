@extends('voyager::master')

@section('page_title', 'Detalle de Venta')

@section('page_header')
    <h1 class="page-title">
        <i class="fa-solid fa-receipt"></i> Detalle de Venta
        <a href="{{ route('sales.index') }}" class="btn btn-warning">
            <i class="voyager-list"></i> <span class="hidden-xs hidden-sm">Volver a la lista</span>
        </a>
        <button class="btn btn-default" onclick="window.print()">
            <i class="fa-solid fa-print"></i> <span class="hidden-xs hidden-sm">Imprimir</span>
        </button>
    </h1>
@stop

@section('content')
<div id="sale-details" class="page-content read container-fluid">

    {{-- ══════════════════════════════════════════════════
         CABECERA: Número de factura + estado
    ══════════════════════════════════════════════════ --}}
    <div class="row">
        <div class="col-md-12">
            <div class="sale-header-bar">
                <div class="sale-header-left">
                    <span class="sale-invoice-number">
                        <i class="fa-solid fa-file-invoice"></i>
                        {{ $sale->invoiceNumber ?? '#' . $sale->id }}
                    </span>
                    @php
                        $typeSale = $sale->typeSale ?? 'Venta al Contado';
                        if ($typeSale === 'Proforma') {
                            $typeColor = '#8a6d3b'; $typeBg = '#fcf8e3'; $typeLabel = 'Proforma';
                        } elseif ($typeSale === 'Venta al Credito') {
                            $typeColor = '#31708f'; $typeBg = '#d9edf7'; $typeLabel = 'Venta al Crédito';
                        } else {
                            $typeColor = '#3c763d'; $typeBg = '#dff0d8'; $typeLabel = 'Venta al Contado';
                        }
                    @endphp
                    <span class="sale-type-badge" style="background-color:{{ $typeBg }}; color:{{ $typeColor }}; border-color:{{ $typeColor }};">
                        {{ $typeLabel }}
                    </span>
                </div>
                <div class="sale-header-right">
                    @if ($sale->status == 'Pagado')
                        <span class="sale-status-badge status-paid">
                            <i class="voyager-check"></i> Pagado
                        </span>
                    @else
                        <span class="sale-status-badge status-pending">
                            <i class="voyager-watch"></i> Pendiente
                        </span>
                    @endif
                    <span class="sale-date-label">
                        <i class="fa-solid fa-calendar-day"></i>
                        {{ date('d/m/Y — h:i:s a', strtotime($sale->dateSale)) }}
                    </span>
                    <span class="sale-register-label">
                        <i class="fa-solid fa-user-tie"></i>
                        {{ $sale->register->name ?? 'N/A' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         FILA: Info cliente + Resumen financiero
    ══════════════════════════════════════════════════ --}}
    <div class="row">
        {{-- Cliente --}}
        <div class="col-md-5">
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa-solid fa-user"></i> Cliente</h3>
                </div>
                <div class="panel-body">
                    @if ($sale->person)
                        @php
                            $image = asset('images/default.jpg');
                            if ($sale->person->image) {
                                $image = asset('storage/' . str_replace('.avif', '', $sale->person->image) . '-cropped.webp');
                            }
                        @endphp
                        <div style="display:flex; align-items:center; margin-bottom: 15px;">
                            <img src="{{ $image }}" alt="{{ $sale->person->first_name }}"
                                 style="width:70px; height:70px; border-radius:50%; object-fit:cover; border:3px solid #eee; margin-right:15px; flex-shrink:0;">
                            <div>
                                <h4 style="margin:0 0 4px 0; font-size:16px; font-weight:700;">
                                    {{ strtoupper($sale->person->first_name) }}
                                    {{ $sale->person->middle_name ? strtoupper($sale->person->middle_name) . ' ' : '' }}{{ strtoupper($sale->person->paternal_surname) }}
                                    {{ strtoupper($sale->person->maternal_surname) }}
                                </h4>
                                <span style="font-size:12px; color:#777;"><i class="fa-solid fa-id-card"></i> CI/NIT: <b>{{ $sale->person->ci }}</b></span>
                            </div>
                        </div>
                        <dl class="dl-horizontal" style="margin-bottom:0;">
                            <dt>Dirección:</dt>
                            <dd>{{ $sale->person->address ?: 'No registrada' }}</dd>
                            @if($sale->person->phone ?? null)
                                <dt>Teléfono:</dt>
                                <dd>{{ $sale->person->phone }}</dd>
                            @endif
                        </dl>
                    @else
                        <div class="text-center text-muted" style="padding: 20px 0;">
                            <i class="fa-solid fa-user-slash" style="font-size:40px; opacity:0.3;"></i>
                            <p style="margin-top:10px;">Sin cliente registrado</p>
                        </div>
                    @endif

                    @if($sale->observation)
                        <div class="sale-observation">
                            <i class="fa-solid fa-note-sticky"></i>
                            <span>{{ $sale->observation }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Resumen financiero --}}
        <div class="col-md-7">
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa-solid fa-coins"></i> Resumen Financiero</h3>
                </div>
                <div class="panel-body" style="padding: 20px;">
                    <div class="financial-grid">
                        <div class="financial-card card-total">
                            <div class="financial-card-icon"><i class="fa-solid fa-receipt"></i></div>
                            <div class="financial-card-body">
                                <p>Total Venta</p>
                                <h3>Bs {{ number_format($sale->amount, 2, ',', '.') }}</h3>
                            </div>
                        </div>
                        @if(($sale->general_discount ?? 0) > 0)
                        <div class="financial-card card-discount">
                            <div class="financial-card-icon"><i class="fa-solid fa-tag"></i></div>
                            <div class="financial-card-body">
                                <p>Descuento General</p>
                                <h3>- Bs {{ number_format($sale->general_discount, 2, ',', '.') }}</h3>
                            </div>
                        </div>
                        @endif
                        <div class="financial-card card-received">
                            <div class="financial-card-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                            <div class="financial-card-body">
                                <p>Monto Recibido</p>
                                <h3>Bs {{ number_format($sale->amountReceived, 2, ',', '.') }}</h3>
                            </div>
                        </div>
                        <div class="financial-card card-change">
                            <div class="financial-card-icon"><i class="fa-solid fa-rotate-left"></i></div>
                            <div class="financial-card-body">
                                <p>Cambio</p>
                                <h3>Bs {{ number_format($sale->amountChange, 2, ',', '.') }}</h3>
                            </div>
                        </div>
                    </div>

                    {{-- Métodos de pago --}}
                    <div style="margin-top: 15px;">
                        <p style="font-size:12px; color:#777; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px;">Métodos de Pago</p>
                        <div style="display:flex; gap:8px; flex-wrap:wrap;">
                            @forelse ($sale->saleTransactions as $trx)
                                <div class="payment-pill">
                                    @if($trx->paymentType == 'Qr')
                                        <i class="fa-solid fa-qrcode"></i>
                                    @else
                                        <i class="fa-solid fa-money-bill-wave"></i>
                                    @endif
                                    <span>{{ $trx->paymentType }}</span>
                                    <strong>Bs {{ number_format($trx->amount, 2, ',', '.') }}</strong>
                                </div>
                            @empty
                                <span class="text-muted" style="font-size:13px;">Sin transacciones registradas</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         TABLA DE PRODUCTOS
    ══════════════════════════════════════════════════ --}}
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa-solid fa-box-open"></i> Productos de la Venta</h3>
                </div>
                <div class="panel-body" style="padding:0;">
                    <div class="table-responsive">
                        <table class="table table-hover" style="margin-bottom:0;">
                            <thead class="products-thead">
                                <tr>
                                    <th style="width:4%; text-align:center;">#</th>
                                    <th>Artículo</th>
                                    <th style="text-align:center; width:10%;">Tipo</th>
                                    <th style="text-align:center; width:13%;">Cantidad</th>
                                    <th style="text-align:right; width:10%;">Precio Unit.</th>
                                    <th style="text-align:right; width:10%;">Descuento</th>
                                    <th style="text-align:right; width:11%;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; $amountTotal = 0; $discountTotal = 0; @endphp
                                @forelse ($sale->saleDetails as $value)
                                    <tr>
                                        <td style="text-align:center; color:#aaa; vertical-align:middle;">{{ $i }}</td>
                                        <td style="vertical-align:middle;">
                                            <b style="font-size:13px;">{{ strtoupper($value->itemStock->item->nameGeneric) }}</b>
                                            @if($value->itemStock->item->nameTrade)
                                                <span class="text-muted"> | {{ strtoupper($value->itemStock->item->nameTrade) }}</span>
                                            @endif
                                        </td>
                                        <td style="text-align:center; vertical-align:middle;">
                                            @if ($value->dispensed == 'Entero')
                                                <span class="label" style="background:#e8f5e9; color:#2e7d32; border:1px solid #a5d6a7; font-size:10px;">
                                                    <i class="fa-solid fa-cube"></i> Unidad
                                                </span>
                                            @else
                                                <span class="label" style="background:#e3f2fd; color:#1565c0; border:1px solid #90caf9; font-size:10px;">
                                                    <i class="fa-solid fa-cubes-stacked"></i> Fracción
                                                </span>
                                            @endif
                                        </td>
                                        <td style="text-align:center; vertical-align:middle;">
                                            <b>{{ number_format($value->quantity, 2, ',', '.') }}</b>
                                            <small class="text-muted">
                                                @if ($value->dispensed == 'Entero')
                                                    {{ $value->itemStock->item->presentation->name ?? '' }}
                                                @else
                                                    {{ $value->itemStock->item->fractionPresentation->name ?? '' }}
                                                @endif
                                            </small>
                                        </td>
                                        <td style="text-align:right; vertical-align:middle;">
                                            Bs {{ number_format($value->price, 2, ',', '.') }}
                                        </td>
                                        <td style="text-align:right; vertical-align:middle;">
                                            @if(($value->discount ?? 0) > 0)
                                                <span style="color:#e53935;">- Bs {{ number_format($value->discount, 2, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td style="text-align:right; vertical-align:middle; font-weight:700; font-size:14px;">
                                            Bs {{ number_format($value->amount, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                    @php
                                        $i++;
                                        $amountTotal += $value->amount;
                                        $discountTotal += ($value->discount ?? 0);
                                    @endphp
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center" style="padding:40px; color:#aaa;">
                                            <i class="fa-solid fa-box-open" style="font-size:30px;"></i>
                                            <p style="margin-top:8px;">No hay productos registrados.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                @if($discountTotal > 0)
                                <tr style="background:#fff8e1;">
                                    <td colspan="6" style="text-align:right; color:#e65100; font-size:13px;">
                                        Descuento en productos:
                                    </td>
                                    <td style="text-align:right; color:#e65100; font-weight:600;">
                                        - Bs {{ number_format($discountTotal, 2, ',', '.') }}
                                    </td>
                                </tr>
                                @endif
                                @if(($sale->general_discount ?? 0) > 0)
                                <tr style="background:#fff8e1;">
                                    <td colspan="6" style="text-align:right; color:#e65100; font-size:13px;">
                                        Descuento general:
                                    </td>
                                    <td style="text-align:right; color:#e65100; font-weight:600;">
                                        - Bs {{ number_format($sale->general_discount, 2, ',', '.') }}
                                    </td>
                                </tr>
                                @endif
                                <tr class="total-row">
                                    <td colspan="6" style="text-align:right; font-size:15px; font-weight:600;">
                                        TOTAL
                                    </td>
                                    <td style="text-align:right; font-size:18px; font-weight:700; color:#1a237e;">
                                        Bs {{ number_format($sale->amount, 2, ',', '.') }}
                                    </td>
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

@section('css')
<style>
    /* ── Barra de cabecera ── */
    .sale-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 14px 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .sale-header-left, .sale-header-right {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .sale-invoice-number {
        font-size: 20px;
        font-weight: 700;
        color: #2c3e50;
        letter-spacing: 0.5px;
    }
    .sale-type-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 12px;
        border: 1px solid;
    }
    .sale-status-badge {
        font-size: 12px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 4px;
        color: #fff;
    }
    .status-paid   { background-color: #27ae60; }
    .status-pending { background-color: #f39c12; }
    .sale-date-label, .sale-register-label {
        font-size: 12px;
        color: #666;
    }

    /* ── Grid financiero ── */
    .financial-grid {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .financial-card {
        flex: 1;
        min-width: 120px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-radius: 8px;
        padding: 12px 14px;
        border: 1px solid #e0e0e0;
    }
    .financial-card-icon {
        font-size: 22px;
        opacity: 0.7;
        flex-shrink: 0;
    }
    .financial-card-body p {
        margin: 0;
        font-size: 11px;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .financial-card-body h3 {
        margin: 2px 0 0 0;
        font-size: 18px;
        font-weight: 700;
    }
    .card-total    { background: #e8f5e9; }
    .card-total .financial-card-icon, .card-total h3 { color: #2e7d32; }
    .card-discount { background: #fff8e1; }
    .card-discount .financial-card-icon, .card-discount h3 { color: #e65100; }
    .card-received { background: #e3f2fd; }
    .card-received .financial-card-icon, .card-received h3 { color: #1565c0; }
    .card-change   { background: #f3e5f5; }
    .card-change .financial-card-icon, .card-change h3 { color: #6a1b9a; }

    /* ── Pastillas de pago ── */
    .payment-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f5f5f5;
        border: 1px solid #ddd;
        border-radius: 20px;
        padding: 5px 14px;
        font-size: 13px;
    }
    .payment-pill i { color: #555; }
    .payment-pill strong { color: #333; }

    /* ── Tabla productos ── */
    .products-thead th {
        background-color: #f5f7fa;
        color: #555;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border-bottom: 2px solid #e0e0e0;
        padding: 10px 12px;
    }
    .table > tbody > tr > td {
        padding: 10px 12px;
        vertical-align: middle;
    }
    .table > tfoot > tr.total-row > td {
        background-color: #f5f7fa;
        padding: 14px 12px;
        border-top: 2px solid #ddd;
    }

    /* ── DL horizontal ── */
    .dl-horizontal dt { font-weight: normal; color: #777; white-space: normal; }
    .dl-horizontal dd { margin-left: 100px; font-weight: 600; color: #333; }

    /* ── Observación ── */
    .sale-observation {
        margin-top: 12px;
        padding: 8px 12px;
        background: #fffde7;
        border-left: 3px solid #ffc107;
        border-radius: 2px;
        font-size: 13px;
        color: #7a6000;
        display: flex;
        gap: 8px;
        align-items: flex-start;
    }

    /* ── Panel titles ── */
    .panel-heading .panel-title { font-size: 15px; font-weight: 600; }

    /* ── Impresión ── */
    @media print {
        body * { visibility: hidden; }
        #sale-details, #sale-details * { visibility: visible; }
        #sale-details { position: absolute; left: 0; top: 0; width: 100%; }
        .sale-header-bar { box-shadow: none; }
        .panel { border: 1px solid #ccc !important; box-shadow: none !important; }
        .table { font-size: 11px; }
        .page-title, .breadcrumb-nav { display: none; }
    }
</style>
@stop

@section('javascript')
@stop
