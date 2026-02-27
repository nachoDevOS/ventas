<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Entrega de fondos - {{ Voyager::setting('admin.title') }}</title>
    <?php $admin_favicon = Voyager::setting('admin.icon_image', ''); ?>
    @if ($admin_favicon == '')
        <link rel="shortcut icon" href="{{ asset('images/icon.png') }}" type="image/png">
    @else
        <link rel="shortcut icon" href="{{ Voyager::image($admin_favicon) }}" type="image/png">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: 'monospace', sans-serif;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }
        .receipt-wrapper {
            max-width: 800px;
            margin: 20px auto;
        }
        .receipt {
            border: 2px solid #000;
            padding: 15px;
            position: relative;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 14px;
        }
        .details-section {
            border-bottom: 1px solid #000;
            padding: 10px 0;
        }
        .details-section table {
            width: 100%;
            font-size: 14px;
        }
        .details-section .label {
            font-weight: bold;
            padding-right: 10px;
            white-space: nowrap;
        }
        .amount-section {
            border-bottom: 1px solid #000;
            padding: 10px 0;
            font-size: 16px;
            display: flex;
            justify-content: space-between;
        }
        .bill-breakdown {
            padding: 10px 0;
            border-bottom: 1px solid #000;
        }
        .bill-breakdown h3 {
            text-align: center;
            margin: 0 0 10px 0;
            font-size: 14px;
            text-transform: uppercase;
        }
        .bill-breakdown-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .bill-breakdown-table thead {
            border-bottom: 1px solid #000;
        }
        .bill-breakdown-table th {
            padding: 5px;
            text-align: center;
        }
        .bill-breakdown-table tbody td {
            padding: 3px 5px;
            text-align: right;
        }
        .bill-breakdown .bills {
            display: flex;
            justify-content: center;
            gap: 15px;
            font-size: 12px;
        }
        .signatures {
            padding: 20px 0;
            display: flex;
            justify-content: space-around;
        }
        .signature-box {
            text-align: center;
            font-size: 12px;
        }
        .signature-box .line {
            margin-top: 40px;
            border-bottom: 1px solid #000;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            padding-top: 10px;
        }
        .print-buttons {
            text-align: right;
            padding: 10px 0;
        }
        .btn-print {
            background-color: #000;
            color: #fff;
            border: 1px solid #000;
            padding: 5px 10px;
            font-family: 'monospace', sans-serif;
            cursor: pointer;
        }
        .btn-print:hover {
            background-color: #333;
        }
        .print-only {
            display: none;
        }
        @media print {
            body {
                background-color: #fff;
            }
            .receipt-wrapper {
                margin: 0;
                max-width: 100%;
            }
            .receipt {
                border: none;
            }
            .print-buttons {
                display: none;
            }
            .page-break {
                page-break-after: always;
            }
            .print-only {
                display: block;
            }
        }
    </style>
</head>
<body>
    @php
        $months = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $movement = $cashier->movements->where('type', 'Ingreso')->first();
        $opening_details = $movement->details->where('type', 'Apertura')->first();
        $delivered_by = $movement->user;
        $amount = $movement->amount;
    @endphp

    <div class="print-buttons hide-print">
        <button class="btn-print" onclick="window.close()">[ CERRAR ]</button>
        <button class="btn-print" onclick="window.print()">[ IMPRIMIR ]</button>
    </div>

    @for ($i = 0; $i < 2; $i++)
    <div class="receipt-wrapper @if ($i == 0) page-break @endif @if($i > 0) print-only @endif">
        <div class="receipt">
            <div class="header">
                <h2>{{ setting('admin.title') }}</h2>
                <p>COMPROBANTE DE ENTREGA DE FONDOS</p>
            </div>

            <div class="details-section">
                <table>
                    <tr>
                        <td class="label">FECHA:</td>
                        <td>{{ date('d/m/Y', strtotime($opening_details->created_at)) }}</td>
                        <td class="label">HORA:</td>
                        <td>{{ date('h:i:s a', strtotime($cashier->created_at)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">ID:</td>
                        <td colspan="3">{{ str_pad($cashier->id, 6, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td class="label">CAJERO(A):</td>
                        <td colspan="3">{{ $cashier->user->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">CONCEPTO:</td>
                        <td colspan="3">APERTURA DE CAJA</td>
                    </tr>
                </table>
            </div>

            <div class="amount-section">
                <span>MONTO TOTAL:</span>
                <span>{{ number_format($amount, 2, ',', '.') }} Bs.</span>
            </div>

            @if($opening_details->detailCashes->count() > 0)
            <div class="bill-breakdown">
                <h3>DESGLOSE</h3>
                <table class="bill-breakdown-table">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Cantidad</th>
                            <th style="text-align: center;">Valor (Bs.)</th>
                            <th style="text-align: right;">Subtotal (Bs.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($opening_details->detailCashes as $cash)
                            <tr>
                                <td style="text-align: center;">{{ intval($cash->quantity) }}</td>
                                <td style="text-align: center;">{{ $cash->cash_value >= 1 ? intval($cash->cash_value) : number_format($cash->cash_value, 2, ',', '.') }}</td>
                                <td>{{ number_format($cash->quantity * $cash->cash_value, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <div class="signatures">
                <div class="signature-box">
                    <div class="line"></div>
                    <p>{{ strtoupper($delivered_by->name) }}</p>
                    {{-- <p>C.I.: {{ $delivered_by->ci }}</p> --}}
                    <p>(ENTREGADO POR)</p>
                </div>
                <div class="signature-box">
                    <div class="line"></div>
                    <p>{{ strtoupper($cashier->user->name) }}</p>
                    {{-- <p>C.I.: {{ $cashier->user->ci }}</p> --}}
                    <p>(RECIBIDO POR)</p>
                </div>
            </div>

            <div class="footer">
                <p>Impreso por: {{ Auth::user()->name }} | {{ date('d/m/Y h:i:s a') }}</p>
                <div>
                    {!! QrCode::size(70)->generate('ID: ' . str_pad($cashier->id, 6, '0', STR_PAD_LEFT) . ' | Monto: ' . number_format($amount, 2, ',', '.') . ' Bs.') !!}
                </div>
            </div>
        </div>
    </div>
    @endfor

    <script>
        document.body.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') window.print();
            if (e.key === 'Escape') window.close();
        });
    </script>
</body>
</html>
