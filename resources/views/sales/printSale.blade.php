<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Venta - {{Voyager::setting('admin.title') }}</title>
    <style>
        :root {
            --primary: #1a3e72;
            --accent: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --success: #2a9d8f;
        }
        
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');
        
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            color: var(--dark);
            line-height: 1.4;
            background-color: #f1f5f9;
            font-size: 14px;
        }
        
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary) 0%, #2a4a7e 100%);
            color: white;
            padding: 15px 30px;
            position: relative;
            overflow: hidden;
        }
        
        .header::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--accent);
        }
        
        .logo-header {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        
        .logo {
            height: 60px;
            margin-right: 15px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 3px 0;
        }
        
        .company-tagline {
            font-size: 12px;
            opacity: 0.9;
            margin: 0;
            font-weight: 400;
        }
        
        .document-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 30px;
            background: var(--light);
            border-bottom: 1px solid #e9ecef;
        }
        
        .document-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary);
            margin: 0;
        }
        
        .document-meta {
            text-align: right;
        }
        
        .document-number {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 3px;
        }
        
        .document-date {
            font-size: 12px;
            color: var(--gray);
        }
        
        .sale-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            padding: 15px 30px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-box {
            margin-bottom: 10px;
        }
        
        .info-label {
            font-size: 12px;
            color: var(--gray);
            margin-bottom: 2px;
            display: block;
        }
        
        .info-value {
            font-weight: 500;
            font-size: 13px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            padding: 0 30px;
            margin-top: 15px;
        }
        
        .section-title::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e9ecef;
            margin-left: 10px;
        }
        
        .products-section {
            padding: 0 30px;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 15px;
            font-size: 13px;
        }
        
        .products-table thead {
            background: var(--primary);
            color: white;
        }
        
        .products-table th {
            padding: 8px 10px;
            text-align: left;
            font-weight: 500;
        }
        
        .products-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .products-table tr:last-child td {
            border-bottom: none;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .payment-method {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            background: #e8f4fd;
            border-radius: 4px;
            font-size: 13px;
            margin-top: 3px;
        }
        
        .payment-icon {
            color: var(--primary);
            font-size: 16px;
        }
        
        .totals-container {
            margin-left: auto;
            width: 250px;
            border-top: 2px solid var(--primary);
            margin-bottom: 15px;
            font-size: 13px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
        }
        
        .total-label {
            font-weight: 500;
        }
        
        .grand-total {
            font-weight: 700;
            font-size: 15px;
            color: var(--primary);
        }
        
        .signature-section {
            display: flex;
            justify-content: space-between;
            padding: 20px 30px;
            border-top: 1px solid #e9ecef;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
            font-size: 13px;
        }
        
        .signature-line {
            border-top: 1px solid var(--gray);
            margin: 20px auto 8px;
            width: 80%;
        }
        
        .footer {
            background: var(--primary);
            color: white;
            padding: 12px 30px;
            text-align: center;
            font-size: 12px;
        }
        
        .footer p {
            margin: 3px 0;
            opacity: 0.9;
        }
        
        .thank-you {
            font-weight: 500;
            margin-bottom: 5px !important;
        }
        
        .decorative-border {
            height: 3px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 50%, var(--primary) 100%);
        }
        
        /* Estilos para los botones */
        .button-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            padding: 15px 0;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        
        .print-button, .new-sale-button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .print-button {
            background-color: var(--primary);
            color: white;
        }
        
        .print-button:hover {
            background-color: #142f5c;
        }
        
        .new-sale-button {
            background-color: var(--success);
            color: white;
        }
        
        .new-sale-button:hover {
            background-color: #21867a;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
        }
        
        .paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .pending {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        
        .notes {
            padding: 10px 30px;
            font-size: 12px;
        }
        
        @media print {
            .button-container {
                display: none;
            }
            
            body {
                background: none;
                font-size: 12px;
            }
            
            .container {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
                border-radius: 0;
            }
            
            .header, .footer {
                padding: 10px 20px;
            }
            
            .products-table {
                font-size: 11px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-header">
                <?php $admin_favicon = Voyager::setting('admin.icon_image', ''); ?>
                @if($admin_favicon == '')
                    <img src="{{ asset('images/icon.png')}}" alt="{{Voyager::setting('admin.title') }}" class="logo">
                @else
                    <img src="{{ Voyager::image($admin_favicon) }}" alt="{{Voyager::setting('admin.title') }}" class="logo">
                @endif
                <div class="company-info">
                    <h1 class="company-name">{{strtoupper(Voyager::setting('admin.title')) }}</h1>
                    <p class="company-tagline">Especialistas en repuestos para motos</p>
                </div>
            </div>
        </div>
        
        <div class="decorative-border"></div>
        
        <div class="document-head">
            <h2 class="document-title">COMPROBANTE DE VENTA <span class="status-badge paid">PAGADO</span></h2>
            <div class="document-meta">
                <div class="document-number">N°: {{$sale->code}}</div>
                <div class="document-date">Fecha: {{date('d/m/Y h:i a', strtotime($sale->dateSale))}}</div>
            </div>
        </div>
        
        <div class="sale-info">
            <div>
                <div class="info-box">
                    <span class="info-label">Cliente</span>
                    <span class="info-value">{{$sale->person ? $sale->person->first_name.' '.$sale->person->paternal_surname.' '.$sale->person->maternal_surname : 'Cliente ocasional'}}</span>
                </div>
                <div class="info-box">
                    <span class="info-label">Documento</span>
                    <span class="info-value">{{$sale->person ? $sale->person->ci : 'Sin documento'}}</span>
                </div>
            </div>
            <div>
                <div class="info-box">
                    <span class="info-label">Vendedor</span>
                    <span class="info-value">{{Auth::user()->name}}</span>
                </div>
                <div class="info-box">
                    <span class="info-label">Método de Pago</span>
                    <div class="payment-method">
                        @if ($transaction->transaction->type=='Efectivo')
                            <i class="fas fa-money-bill-wave payment-icon"></i>
                            <span>Efectivo</span>
                        @else
                            <i class="fa-solid fa-qrcode"></i>
                            <span>Qr</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section-title">
            <i class="fas fa-list-ol" style="margin-right: 8px;"></i>
            Detalle de productos
        </div>
        
        <div class="products-section">
            <table class="products-table">
                <thead>
                    <tr>
                        <th width="5%">N°</th>
                        <th width="50%">Descripción</th>
                        <th width="10%" class="text-center">Cant.</th>
                        <th width="15%" class="text-right">P. Unit.</th>
                        <th width="20%" class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                        $total = 0;
                    @endphp
                    @foreach($sale->saleDetails as $item)
                    <tr>
                        <td>{{$i}}</td>
                        <td>{{$item->name}}</td>
                        <td class="text-center">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                        <td class="text-right">Bs. {{ number_format($item->price, 2, ',', '.') }}</td>
                        <td class="text-right">Bs. {{ number_format($item->amount, 2, ',', '.') }}</td>
                    </tr>
                    @php
                        $i++;
                        $total += $item->amount;
                    @endphp
                    @endforeach
                </tbody>
            </table>
            
            <div class="totals-container">
                <div class="total-row grand-total">
                    <span>TOTAL:</span>
                    <span>Bs. {{ number_format($total, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
        
        {{-- <div class="notes">
            <p><strong>Nota:</strong> Todos los repuestos tienen garantía de 3 meses contra defectos de fabricación.</p>
        </div> --}}
        
        {{-- <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <p>Cliente</p>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <p>Vendedor</p>
                <p><strong>{{Auth::user()->name}}</strong></p>
            </div>
        </div> --}}
        
        <div class="footer">
            <p class="thank-you">¡Gracias por su preferencia!</p>
            <p>Documento válido como comprobante de pago</p>
        </div>
        
        <div class="button-container">
            <button class="print-button" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir
            </button>
            <button class="new-sale-button" onclick="window.close()">
                <i class="fas fa-times-circle"></i> Cerrar
            </button>
        </div>
    </div>
    
    <script>
        // Configuración para impresión
        window.addEventListener('beforeprint', function() {
            console.log('Preparando para imprimir...');
        });

        // Auto-impresión al cargar (opcional)
        // window.addEventListener('load', function() {
        //     window.print();
        // });
    </script>
</body>
</html>