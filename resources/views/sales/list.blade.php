<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="text-align: center; width: 11%">Nro / Tipo</th>
                    <th style="text-align: center;">Cliente</th>
                    <th style="text-align: center; width: 10%">Monto</th>
                    <th style="text-align: center;">Productos</th>
                    <th style="text-align: center; width: 14%">Fecha / Vendedor</th>
                    <th style="text-align: center; width: 10%">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)

                {{-- ══ FILA PRINCIPAL ══ --}}
                <tr class="sale-main-row" data-id="{{ $item->id }}" style="cursor: pointer;">

                    {{-- ── Nro / Tipo ── --}}
                    <td style="text-align: center; vertical-align: middle;">
                        <span class="toggle-icon" style="font-size: 10px; color: #aaa; margin-right: 3px;">
                            <i class="fa-solid fa-chevron-right"></i>
                        </span>
                        <b style="font-size: 13px; color: #555;">#{{ $item->id }}</b>
                        @if($item->invoiceNumber)
                            <br><small class="text-muted" style="font-size: 10px;">{{ $item->invoiceNumber }}</small>
                        @endif
                        <br>
                        @php
                            $typeSale = $item->typeSale ?? 'Venta al Contado';
                            if ($typeSale === 'Proforma') {
                                $tc = '#8a6d3b'; $tb = '#fcf8e3'; $tl = 'Proforma';
                            } elseif ($typeSale === 'Venta al Credito') {
                                $tc = '#31708f'; $tb = '#d9edf7'; $tl = 'Crédito';
                            } else {
                                $tc = '#3c763d'; $tb = '#dff0d8'; $tl = 'Contado';
                            }
                        @endphp
                        <span style="font-size: 9px; padding: 2px 8px; border-radius: 10px;
                                     background-color: {{ $tb }}; color: {{ $tc }};
                                     border: 1px solid {{ $tc }}; font-weight: 600;">
                            {{ $tl }}
                        </span>
                    </td>

                    {{-- ── Cliente ── --}}
                    <td style="vertical-align: middle;">
                        @if ($item->person)
                            @php
                                $image = asset('images/default.jpg');
                                if ($item->person->image) {
                                    $image = asset('storage/' . str_replace('.avif', '', $item->person->image) . '-cropped.webp');
                                }
                            @endphp
                            <div style="display: flex; align-items: center;">
                                <img src="{{ $image }}" alt="{{ $item->person->first_name }}"
                                     class="image-expandable"
                                     style="width: 46px; height: 46px; border-radius: 50%;
                                            margin-right: 10px; object-fit: cover;
                                            border: 2px solid #e8e8e8; flex-shrink: 0;">
                                <div>
                                    <b style="font-size: 14px; line-height: 1.3;">
                                        {{ strtoupper($item->person->first_name) }}
                                        {{ $item->person->middle_name ? strtoupper($item->person->middle_name).' ' : '' }}{{ strtoupper($item->person->paternal_surname) }}
                                        {{ strtoupper($item->person->maternal_surname) }}
                                    </b><br>
                                    <small class="text-muted">
                                        <i class="fa-solid fa-id-card" style="font-size: 9px;"></i>
                                        {{ $item->person->ci }}
                                    </small>
                                </div>
                            </div>
                        @else
                            <span class="text-muted">
                                <i class="fa-solid fa-user-slash"></i> Sin Cliente
                            </span>
                        @endif

                        @if($item->observation)
                            <div style="margin-top: 5px; padding: 3px 8px;
                                        background: #fffde7; border-left: 3px solid #ffc107;
                                        border-radius: 2px;">
                                <small style="font-size: 10px; color: #7a6000; font-style: italic;">
                                    <i class="fa-solid fa-note-sticky"></i> {{ $item->observation }}
                                </small>
                            </div>
                        @endif
                    </td>

                    {{-- ── Monto / Pago / Estado ── --}}
                    <td style="text-align: center; vertical-align: middle;">
                        <b style="font-size: 17px; color: #2c3e50;">
                            Bs {{ number_format($item->amount, 2, ',', '.') }}
                        </b>
                        @if(($item->general_discount ?? 0) > 0)
                            <br>
                            <small style="font-size: 10px; color: #e74c3c;">
                                <i class="fa-solid fa-tag"></i>
                                -Bs {{ number_format($item->general_discount, 2, ',', '.') }}
                            </small>
                        @endif
                        <br>
                        @php
                            $paymentQr       = $item->saleTransactions->where('paymentType', 'Qr')->sum('amount') > 0;
                            $paymentEfectivo = $item->saleTransactions->where('paymentType', 'Efectivo')->sum('amount') > 0;
                        @endphp
                        @if ($paymentEfectivo && $paymentQr)
                            <span class="label" style="font-size: 10px; background-color: #5bc0de; color:#fff;">
                                <i class="fa-solid fa-money-bill-1-wave"></i>
                                <i class="fa-solid fa-qrcode"></i> Mixto
                            </span>
                        @elseif ($paymentEfectivo)
                            <span class="label label-success" style="font-size: 10px;">
                                <i class="fa-solid fa-money-bill-1-wave"></i> Efectivo
                            </span>
                        @elseif ($paymentQr)
                            <span class="label label-primary" style="font-size: 10px;">
                                <i class="fa-solid fa-qrcode"></i> QR
                            </span>
                        @endif
                        <br>
                        @if ($item->status == 'Pagado')
                            <span class="label label-success"
                                  style="font-size: 10px; margin-top: 4px; display: inline-block; padding: 3px 8px;">
                                <i class="voyager-check"></i> Pagado
                            </span>
                        @elseif($item->status == 'Pendiente')
                            <span class="label label-warning"
                                  style="font-size: 10px; margin-top: 4px; display: inline-block; padding: 3px 8px;">
                                <i class="voyager-watch"></i> Pendiente
                            </span>
                        @endif
                    </td>

                    {{-- ── Productos (resumen) ── --}}
                    <td style="vertical-align: middle; padding: 8px;">
                        <table style="width: 100%;">
                            @foreach ($item->saleDetails->groupBy('itemStock_id') as $details)
                                @php
                                    $detail   = $details->first();
                                    $totalQty = $details->sum('quantity');
                                    $totalAmt = $details->sum('amount');
                                @endphp
                                <tr style="border-bottom: 1px dashed #f0f0f0;">
                                    <td style="padding: 3px 0;">
                                        <small style="font-size: 11px; color: #333;">
                                            {{ strtoupper($detail->itemStock->item->nameGeneric) }}
                                            @if($detail->itemStock->item->nameTrade)
                                                <span class="text-muted">
                                                    | {{ strtoupper($detail->itemStock->item->nameTrade) }}
                                                </span>
                                            @endif
                                        </small>
                                    </td>
                                    <td style="text-align: right; padding: 3px 0; white-space: nowrap;">
                                        <span style="font-size: 9px; background: #eeeeee; color: #555;
                                                     padding: 1px 5px; border-radius: 3px; font-weight: 600;">
                                            x{{ $totalQty }}
                                        </span>
                                        <small class="text-muted" style="font-size: 10px; margin-left: 2px;">
                                            Bs {{ number_format($totalAmt, 2, ',', '.') }}
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </td>

                    {{-- ── Fecha / Vendedor ── --}}
                    <td style="text-align: center; vertical-align: middle;">
                        <small class="text-muted">
                            <i class="fa-solid fa-user" style="color: #bbb;"></i>
                            {{ \Illuminate\Support\Str::words($item->register->name, 2, '') }}
                        </small><br>
                        <b style="font-size: 12px;">{{ date('d/m/Y', strtotime($item->dateSale)) }}</b><br>
                        <small class="text-muted">{{ date('h:i:s a', strtotime($item->dateSale)) }}</small><br>
                        <small class="text-info" style="font-size: 10px;">
                            {{ \Carbon\Carbon::parse($item->dateSale)->diffForHumans() }}
                        </small>
                    </td>

                    {{-- ── Acciones ── --}}
                    <td style="vertical-align: middle;" class="no-sort no-click bread-actions text-right">

                        @if (auth()->user()->hasPermission('read_sales'))
                            <a href="{{ route('sales.show', ['sale' => $item->id]) }}"
                               title="Ver" class="btn btn-sm btn-warning view">
                                <i class="voyager-eye"></i>
                            </a>
                            <a href="{{ route('sales.prinf', ['id' => $item->id]) }}"
                               title="Imprimir" target="_blank"
                               class="btn btn-sm btn-default">
                                <i class="fa-solid fa-print"></i>
                            </a>
                        @endif

                        @if (auth()->user()->hasPermission('edit_sales'))
                            <a href="{{ route('sales.edit', ['sale' => $item->id]) }}"
                               title="Editar" class="btn btn-sm btn-primary edit">
                                <i class="voyager-edit"></i>
                            </a>
                        @endif

                        @if (auth()->user()->hasPermission('delete_sales'))
                            <a href="#"
                               onclick="deleteItem('{{ route('sales.destroy', ['sale' => $item->id]) }}')"
                               title="Eliminar" data-toggle="modal" data-target="#modal-delete"
                               class="btn btn-sm btn-danger delete">
                                <i class="voyager-trash"></i>
                            </a>
                        @endif

                    </td>
                </tr>

                {{-- ══ FILA DETALLE (colapsable) ══ --}}
                <tr class="sale-detail-row" id="detail-{{ $item->id }}" style="display: none;">
                    <td colspan="6" style="padding: 0; background: #f8fafc; border-top: none;">
                        <div class="sale-detail-panel">

                            {{-- Tabla de productos --}}
                            <div class="sale-detail-products">
                                <p class="sale-detail-section-title">
                                    <i class="fa-solid fa-box-open"></i> Detalle de productos
                                </p>
                                <table class="table table-condensed" style="margin-bottom: 0; background: #fff; border-radius: 4px; overflow: hidden;">
                                    <thead>
                                        <tr style="background: #eef2f7; font-size: 11px; text-transform: uppercase; color: #666;">
                                            <th style="padding: 7px 10px;">#</th>
                                            <th style="padding: 7px 10px;">Artículo</th>
                                            <th style="padding: 7px 10px; text-align: center;">Tipo</th>
                                            <th style="padding: 7px 10px; text-align: center;">Cantidad</th>
                                            <th style="padding: 7px 10px; text-align: right;">Precio</th>
                                            <th style="padding: 7px 10px; text-align: right;">Descuento</th>
                                            <th style="padding: 7px 10px; text-align: right;">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $di = 1; @endphp
                                        @foreach ($item->saleDetails as $det)
                                            <tr style="font-size: 12px;">
                                                <td style="padding: 6px 10px; color: #aaa;">{{ $di++ }}</td>
                                                <td style="padding: 6px 10px;">
                                                    <b>{{ strtoupper($det->itemStock->item->nameGeneric) }}</b>
                                                    @if($det->itemStock->item->nameTrade)
                                                        <span class="text-muted"> | {{ strtoupper($det->itemStock->item->nameTrade) }}</span>
                                                    @endif
                                                </td>
                                                <td style="padding: 6px 10px; text-align: center;">
                                                    @if($det->dispensed == 'Entero')
                                                        <span style="font-size: 10px; padding: 1px 7px; border-radius: 8px; background:#e8f5e9; color:#2e7d32; border:1px solid #a5d6a7;">
                                                            Unidad
                                                        </span>
                                                    @else
                                                        <span style="font-size: 10px; padding: 1px 7px; border-radius: 8px; background:#e3f2fd; color:#1565c0; border:1px solid #90caf9;">
                                                            Fracción
                                                        </span>
                                                    @endif
                                                </td>
                                                <td style="padding: 6px 10px; text-align: center;">
                                                    <b>{{ number_format($det->quantity, 2, ',', '.') }}</b>
                                                    <small class="text-muted">
                                                        @if($det->dispensed == 'Entero')
                                                            {{ optional($det->itemStock->item->presentation)->name }}
                                                        @else
                                                            {{ optional($det->itemStock->item->fractionPresentation)->name }}
                                                        @endif
                                                    </small>
                                                </td>
                                                <td style="padding: 6px 10px; text-align: right;">
                                                    Bs {{ number_format($det->price, 2, ',', '.') }}
                                                </td>
                                                <td style="padding: 6px 10px; text-align: right;">
                                                    @if(($det->discount ?? 0) > 0)
                                                        <span style="color: #e53935;">-Bs {{ number_format($det->discount, 2, ',', '.') }}</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td style="padding: 6px 10px; text-align: right; font-weight: 700;">
                                                    Bs {{ number_format($det->amount, 2, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Resumen lateral --}}
                            <div class="sale-detail-summary">
                                {{-- Métodos de pago --}}
                                <p class="sale-detail-section-title">
                                    <i class="fa-solid fa-credit-card"></i> Método de pago
                                </p>
                                @foreach($item->saleTransactions as $trx)
                                    <div class="sale-detail-payment-row">
                                        <span>
                                            @if($trx->paymentType == 'Qr')
                                                <i class="fa-solid fa-qrcode"></i>
                                            @else
                                                <i class="fa-solid fa-money-bill-wave"></i>
                                            @endif
                                            {{ $trx->paymentType }}
                                        </span>
                                        <b>Bs {{ number_format($trx->amount, 2, ',', '.') }}</b>
                                    </div>
                                @endforeach

                                {{-- Totales --}}
                                <div style="margin-top: 12px; border-top: 1px solid #e0e0e0; padding-top: 10px;">
                                    @if(($item->general_discount ?? 0) > 0)
                                        <div class="sale-detail-total-row" style="color: #e53935;">
                                            <span><i class="fa-solid fa-tag"></i> Descuento general</span>
                                            <b>-Bs {{ number_format($item->general_discount, 2, ',', '.') }}</b>
                                        </div>
                                    @endif
                                    <div class="sale-detail-total-row sale-detail-grand-total">
                                        <span>TOTAL</span>
                                        <b>Bs {{ number_format($item->amount, 2, ',', '.') }}</b>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </td>
                </tr>

                @empty
                    <tr>
                        <td colspan="6">
                            <h5 class="text-center" style="margin-top: 50px;">
                                <img src="{{ asset('images/empty.png') }}" width="120px" alt=""
                                     style="opacity: 0.8; display: block; margin: 0 auto 15px;">
                                No hay resultados
                            </h5>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-12" style="margin-top: 5px;">
    <div class="col-md-4" style="overflow-x: auto;">
        @if(count($data) > 0)
            <p class="text-muted" style="margin-top: 8px;">
                Mostrando del {{ $data->firstItem() }} al {{ $data->lastItem() }}
                de {{ $data->total() }} registros.
            </p>
        @endif
    </div>
    <div class="col-md-8" style="overflow-x: auto;">
        <nav class="text-right">
            {{ $data->links() }}
        </nav>
    </div>
</div>

<style>
    /* ── Panel detalle ── */
    .sale-detail-panel {
        display: flex;
        gap: 0;
        padding: 14px 16px;
        border-top: 2px solid #d0e4f7;
        background: #f8fafc;
    }
    .sale-detail-products {
        flex: 1;
        padding-right: 16px;
        border-right: 1px solid #e0e8f0;
    }
    .sale-detail-summary {
        width: 220px;
        flex-shrink: 0;
        padding-left: 16px;
    }
    .sale-detail-section-title {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #888;
        font-weight: 600;
        margin: 0 0 8px 0;
    }
    .sale-detail-payment-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 8px;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        margin-bottom: 5px;
        font-size: 12px;
    }
    .sale-detail-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        padding: 3px 0;
    }
    .sale-detail-grand-total {
        font-size: 15px;
        font-weight: 700;
        color: #1a237e;
        margin-top: 6px;
        padding-top: 6px;
        border-top: 1px dashed #c5cae9;
    }
    /* Chevron animado */
    .sale-main-row.open .toggle-icon i {
        transform: rotate(90deg);
    }
    .toggle-icon i {
        transition: transform 0.2s ease;
        display: inline-block;
    }
    /* Highlight fila al expandir */
    .sale-main-row.open {
        background-color: #eef5ff !important;
    }
</style>

<script>
    async function handlePrintClick(element, typePrint, url, ip, port, print, title, sale, fallbackUrl) {
        const button = $(element);
        const icon = button.find('i');
        const originalIconClass = icon.attr('class');

        if (button.hasClass('disabled')) return;

        button.addClass('disabled');
        icon.removeClass(originalIconClass).addClass('fa-solid fa-spinner fa-spin');

        try {
            await printTicket(typePrint, url, ip, port, print, title, sale, fallbackUrl);
        } finally {
            setTimeout(() => {
                button.removeClass('disabled');
                icon.removeClass('fa-solid fa-spinner fa-spin').addClass(originalIconClass);
            }, 2000);
        }
    }

    var page = "{{ request('page') }}";

    $(document).ready(function () {
        // Toggle fila detalle al hacer clic en la fila principal
        // (excepto en los botones de acciones)
        $(document).on('click', '.sale-main-row', function (e) {
            if ($(e.target).closest('.bread-actions').length) return;

            var id = $(this).data('id');
            var $detailRow = $('#detail-' + id);
            var $mainRow   = $(this);

            if ($detailRow.is(':visible')) {
                $detailRow.slideUp(180);
                $mainRow.removeClass('open');
            } else {
                $detailRow.slideDown(220);
                $mainRow.addClass('open');
            }
        });

        // Paginación
        $('.page-link').click(function (e) {
            e.preventDefault();
            let link = $(this).attr('href');
            if (link) {
                page = link.split('=')[1];
                list(page);
            }
        });
    });
</script>
