<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proforma {{ $sale->invoiceNumber ?? $sale->id }} - {{ Voyager::setting('admin.title') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Arial&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            color: #111;
            font-size: 13px;
        }

        .page {
            max-width: 780px;
            margin: 20px auto;
            background: #fff;
            border: 1px solid #ccc;
            padding: 30px 35px 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        /* ── ENCABEZADO ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #111;
            padding-bottom: 14px;
            margin-bottom: 14px;
        }
        .company-block {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo {
            height: 60px;
        }
        .company-name {
            font-size: 17px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .company-sub {
            font-size: 11px;
            color: #555;
            margin-top: 3px;
        }

        /* Caja PROFORMA */
        .doc-box {
            border: 2px solid #111;
            text-align: center;
            min-width: 190px;
        }
        .doc-box-title {
            background: #111;
            color: #fff;
            font-weight: bold;
            font-size: 14px;
            letter-spacing: 2px;
            padding: 5px 12px;
        }
        .doc-box-body {
            padding: 8px 12px;
            font-size: 12px;
        }
        .doc-box-body .nro {
            font-size: 15px;
            font-weight: bold;
        }
        .doc-box-body .fecha {
            font-size: 11px;
            color: #555;
            margin-top: 3px;
        }
        .doc-box-body .validez {
            font-size: 11px;
            color: #333;
            margin-top: 2px;
            border-top: 1px solid #ddd;
            padding-top: 4px;
        }

        /* ── DATOS CLIENTE ── */
        .client-section {
            border: 1px solid #bbb;
            padding: 10px 14px;
            margin-bottom: 16px;
        }
        .client-section-title {
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
            color: #333;
        }
        .client-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 20px;
            font-size: 12px;
        }
        .client-row {
            display: flex;
            gap: 6px;
        }
        .client-row .lbl {
            font-weight: bold;
            min-width: 75px;
            color: #444;
            flex-shrink: 0;
        }

        /* ── TABLA PRODUCTOS ── */
        .products-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 12px;
        }
        .products-table th {
            background: #111;
            color: #fff;
            padding: 8px 10px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .products-table th.r { text-align: right; }
        .products-table th.c { text-align: center; }

        .products-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
        }
        .products-table tbody tr:nth-child(even) td {
            background: #fafafa;
        }
        .products-table td.r { text-align: right; }
        .products-table td.c { text-align: center; }

        /* ── TOTALES ── */
        .totals-wrap {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 16px;
        }
        .totals-table {
            width: 240px;
            font-size: 12px;
            border-collapse: collapse;
        }
        .totals-table tr td {
            padding: 4px 8px;
        }
        .totals-table tr td:last-child {
            text-align: right;
            font-weight: bold;
        }
        .totals-table tr.grand td {
            border-top: 2px solid #111;
            font-size: 14px;
            font-weight: bold;
            padding-top: 6px;
        }
        .totals-table tr.discount td {
            color: #c0392b;
        }

        /* ── OBSERVACIÓN ── */
        .obs-section {
            border: 1px dashed #bbb;
            padding: 8px 12px;
            margin-bottom: 16px;
            font-size: 12px;
        }
        .obs-section strong {
            display: block;
            margin-bottom: 3px;
            font-size: 11px;
            text-transform: uppercase;
            color: #555;
        }

        /* ── AVISO DE VIGENCIA ── */
        .validity-note {
            border: 1px solid #bbb;
            padding: 8px 12px;
            font-size: 11px;
            color: #444;
            margin-bottom: 20px;
            background: #fafafa;
        }

        /* ── FIRMAS ── */
        .signatures {
            display: flex;
            justify-content: space-between;
            gap: 40px;
            margin-top: 30px;
        }
        .sig-box {
            flex: 1;
            text-align: center;
            font-size: 11px;
        }
        .sig-line {
            border-top: 1px solid #555;
            margin: 35px auto 6px;
            width: 80%;
        }
        .sig-name {
            font-weight: bold;
            font-size: 12px;
        }
        .sig-label {
            color: #666;
        }

        /* ── FOOTER ── */
        .footer {
            border-top: 2px solid #111;
            margin-top: 20px;
            padding-top: 8px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .footer .disclaimer {
            font-style: italic;
            margin-top: 3px;
        }

        /* ── BOTONES ── */
        .btn-bar {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 16px 0 0;
        }
        .btn-bar button {
            padding: 8px 20px;
            border: 1px solid #555;
            border-radius: 4px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            background: #fff;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.15s;
        }
        .btn-print { background: #111 !important; color: #fff; border-color: #111 !important; }
        .btn-print:hover { background: #333 !important; }
        .btn-close:hover { background: #f0f0f0; }

        /* ── PRINT ── */
        @media print {
            body { background: none; }
            .page { box-shadow: none; margin: 0; border: none; padding: 20px 25px; max-width: 100%; }
            .btn-bar { display: none; }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ═══════════ ENCABEZADO ═══════════ --}}
    <div class="header">
        <div class="company-block">
            @php $icon = Voyager::setting('admin.icon_image', ''); @endphp
            @if($icon)
                <img src="{{ Voyager::image($icon) }}" alt="{{ Voyager::setting('admin.title') }}" class="logo">
            @else
                <img src="{{ asset('images/icon.png') }}" alt="{{ Voyager::setting('admin.title') }}" class="logo">
            @endif
            <div>
                <div class="company-name">{{ Voyager::setting('admin.title') }}</div>
                <div class="company-sub">Especialistas en repuestos para motos</div>
            </div>
        </div>

        <div class="doc-box">
            <div class="doc-box-title">PROFORMA</div>
            <div class="doc-box-body">
                <div class="nro">N° {{ $sale->invoiceNumber ?? 'PRO-' . $sale->id }}</div>
                <div class="fecha">
                    Fecha: {{ \Carbon\Carbon::parse($sale->dateSale)->format('d/m/Y') }}
                </div>
                <div class="validez">
                    Válida hasta: {{ \Carbon\Carbon::parse($sale->dateSale)->addDays(30)->format('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ DATOS DEL CLIENTE ═══════════ --}}
    <div class="client-section">
        <div class="client-section-title">Datos del cliente</div>
        <div class="client-grid">
            <div class="client-row">
                <span class="lbl">Señor(a):</span>
                <span>
                    @if($sale->person)
                        {{ $sale->person->first_name }}
                        {{ $sale->person->middle_name ? $sale->person->middle_name . ' ' : '' }}{{ $sale->person->paternal_surname }}
                        {{ $sale->person->maternal_surname }}
                    @else
                        —
                    @endif
                </span>
            </div>
            <div class="client-row">
                <span class="lbl">Elaborado por:</span>
                <span>{{ $sale->register->name ?? Auth::user()->name }}</span>
            </div>
            <div class="client-row">
                <span class="lbl">C.I. / NIT:</span>
                <span>{{ $sale->person->ci ?? '—' }}</span>
            </div>
            <div class="client-row">
                <span class="lbl">Teléfono:</span>
                <span>{{ $sale->person->phone ?? '—' }}</span>
            </div>
            @if($sale->person->address ?? null)
            <div class="client-row" style="grid-column: 1 / -1;">
                <span class="lbl">Dirección:</span>
                <span>{{ $sale->person->address }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- ═══════════ TABLA DE PRODUCTOS ═══════════ --}}
    <table class="products-table">
        <thead>
            <tr>
                <th style="width:4%;" class="c">N°</th>
                <th style="width:46%;">Descripción</th>
                <th style="width:12%;" class="c">Cantidad</th>
                <th style="width:14%;" class="r">P. Unitario</th>
                <th style="width:12%;" class="r">Descuento</th>
                <th style="width:12%;" class="r">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 1;
                $totalDescItems = 0;
            @endphp
            @forelse($sale->saleDetails as $detail)
            <tr>
                <td class="c" style="color:#999;">{{ $i }}</td>
                <td>
                    <strong>{{ strtoupper($detail->itemStock->item->nameGeneric) }}</strong>
                    @if($detail->itemStock->item->nameTrade)
                        <br><span style="color:#666; font-size:11px;">{{ $detail->itemStock->item->nameTrade }}</span>
                    @endif
                </td>
                <td class="c">
                    {{ number_format($detail->quantity, 2, ',', '.') }}
                    <span style="font-size:10px; color:#777;">
                        @if($detail->dispensed == 'Entero')
                            {{ $detail->itemStock->item->presentation->name ?? '' }}
                        @else
                            {{ $detail->itemStock->item->fractionPresentation->name ?? '' }}
                        @endif
                    </span>
                </td>
                <td class="r">Bs. {{ number_format($detail->price, 2, ',', '.') }}</td>
                <td class="r">
                    @if(($detail->discount ?? 0) > 0)
                        <span style="color:#c0392b;">Bs. {{ number_format($detail->discount, 2, ',', '.') }}</span>
                    @else
                        Bs. 0,00
                    @endif
                </td>
                <td class="r"><strong>Bs. {{ number_format($detail->amount, 2, ',', '.') }}</strong></td>
            </tr>
            @php
                $i++;
                $totalDescItems += ($detail->discount ?? 0);
            @endphp
            @empty
            <tr>
                <td colspan="6" style="text-align:center; padding:20px; color:#aaa;">
                    Sin productos registrados.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ═══════════ TOTALES ═══════════ --}}
    <div class="totals-wrap">
        <table class="totals-table">
            @if($totalDescItems > 0)
            <tr class="discount">
                <td>Descuento en productos:</td>
                <td>- Bs. {{ number_format($totalDescItems, 2, ',', '.') }}</td>
            </tr>
            @endif
            @if(($sale->general_discount ?? 0) > 0)
            <tr class="discount">
                <td>Descuento general:</td>
                <td>- Bs. {{ number_format($sale->general_discount, 2, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="grand">
                <td>TOTAL A PAGAR:</td>
                <td>Bs. {{ number_format($sale->amount, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    {{-- ═══════════ OBSERVACIÓN ═══════════ --}}
    @if($sale->observation)
    <div class="obs-section">
        <strong>Observación:</strong>
        {{ $sale->observation }}
    </div>
    @endif

    {{-- ═══════════ VIGENCIA ═══════════ --}}
    <div class="validity-note">
        <strong>Nota:</strong>
        La presente proforma tiene vigencia de <strong>30 días</strong> a partir del
        {{ \Carbon\Carbon::parse($sale->dateSale)->format('d/m/Y') }}.
        Los precios están sujetos a disponibilidad de stock.
    </div>

    {{-- ═══════════ FIRMAS ═══════════ --}}
    <div class="signatures">
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-name">
                @if($sale->person)
                    {{ $sale->person->first_name }} {{ $sale->person->paternal_surname }}
                @else
                    Cliente
                @endif
            </div>
            <div class="sig-label">Firma del Cliente</div>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-name">{{ $sale->register->name ?? Auth::user()->name }}</div>
            <div class="sig-label">Firma del Vendedor</div>
        </div>
    </div>

    {{-- ═══════════ FOOTER ═══════════ --}}
    <div class="footer">
        {{ Voyager::setting('admin.title') }}
        &mdash;
        Este documento es una cotización. No tiene validez fiscal ni constituye comprobante de pago.
    </div>

</div>

{{-- Botones fuera del área de impresión --}}
<div class="btn-bar">
    <button class="btn-print" onclick="window.print()">
        <i class="fa-solid fa-print"></i> Imprimir
    </button>
    <button class="btn-close" onclick="window.close()">
        <i class="fa-solid fa-xmark"></i> Cerrar
    </button>
</div>

</body>
</html>
