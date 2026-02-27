<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover" style="font-size: 13px;">
            <thead>
                <tr>
                    <th style="text-align: center; width: 9%">Venta</th>
                    <th style="text-align: center;">Cliente</th>
                    <th style="text-align: center; width: 9%">Tipo</th>
                    <th style="text-align: center; width: 10%">Cantidad</th>
                    <th style="text-align: center; width: 9%">Precio</th>
                    <th style="text-align: center; width: 9%">Descuento</th>
                    <th style="text-align: center; width: 9%">Subtotal</th>
                    <th style="text-align: center; width: 14%">Fecha</th>
                    <th style="text-align: center; width: 8%">Pago</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $det)
                <tr>
                    {{-- Venta --}}
                    <td style="text-align: center; vertical-align: middle;">
                        <b style="font-size: 12px; color: #555;">#{{ $det->sale->id }}</b><br>
                        @if($det->sale->invoiceNumber)
                            <small class="text-muted" style="font-size: 10px;">{{ $det->sale->invoiceNumber }}</small><br>
                        @endif
                        @if ($det->sale->status == 'Pagado')
                            <span class="label label-success" style="font-size: 9px; padding: 2px 6px;">
                                <i class="voyager-check"></i> Pagado
                            </span>
                        @else
                            <span class="label label-warning" style="font-size: 9px; padding: 2px 6px;">
                                <i class="voyager-watch"></i> Pendiente
                            </span>
                        @endif
                    </td>

                    {{-- Cliente --}}
                    <td style="vertical-align: middle;">
                        @if($det->sale->person)
                            <b style="font-size: 13px;">
                                {{ strtoupper($det->sale->person->first_name) }}
                                {{ $det->sale->person->middle_name ? strtoupper($det->sale->person->middle_name).' ' : '' }}{{ strtoupper($det->sale->person->paternal_surname) }}
                                {{ strtoupper($det->sale->person->maternal_surname) }}
                            </b><br>
                            <small class="text-muted">
                                <i class="fa-solid fa-id-card" style="font-size: 9px;"></i>
                                {{ $det->sale->person->ci }}
                            </small>
                        @else
                            <span class="text-muted"><i class="fa-solid fa-user-slash"></i> Sin Cliente</span>
                        @endif
                    </td>

                    {{-- Tipo (Unidad / Fracción) --}}
                    <td style="text-align: center; vertical-align: middle;">
                        @if($det->dispensed == 'Entero')
                            <span style="font-size: 10px; padding: 2px 8px; border-radius: 8px;
                                         background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7;">
                                <i class="fa-solid fa-cube"></i> Unidad
                            </span>
                        @else
                            <span style="font-size: 10px; padding: 2px 8px; border-radius: 8px;
                                         background: #e3f2fd; color: #1565c0; border: 1px solid #90caf9;">
                                <i class="fa-solid fa-cubes-stacked"></i> Fracción
                            </span>
                        @endif
                    </td>

                    {{-- Cantidad --}}
                    <td style="text-align: center; vertical-align: middle;">
                        <b>{{ number_format($det->quantity, 2, ',', '.') }}</b>
                    </td>

                    {{-- Precio --}}
                    <td style="text-align: right; vertical-align: middle;">
                        Bs {{ number_format($det->price, 2, ',', '.') }}
                    </td>

                    {{-- Descuento --}}
                    <td style="text-align: right; vertical-align: middle;">
                        @if(($det->discount ?? 0) > 0)
                            <span style="color: #e53935;">-Bs {{ number_format($det->discount, 2, ',', '.') }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Subtotal --}}
                    <td style="text-align: right; vertical-align: middle; font-weight: 700;">
                        Bs {{ number_format($det->amount, 2, ',', '.') }}
                    </td>

                    {{-- Fecha --}}
                    <td style="text-align: center; vertical-align: middle;">
                        <b style="font-size: 12px;">{{ date('d/m/Y', strtotime($det->sale->dateSale)) }}</b><br>
                        <small class="text-muted">{{ date('h:i a', strtotime($det->sale->dateSale)) }}</small><br>
                        <small class="text-info" style="font-size: 10px;">
                            {{ \Carbon\Carbon::parse($det->sale->dateSale)->diffForHumans() }}
                        </small>
                    </td>

                    {{-- Pago --}}
                    <td style="text-align: center; vertical-align: middle;">
                        @php
                            $paymentQr       = $det->sale->saleTransactions->where('paymentType', 'Qr')->sum('amount') > 0;
                            $paymentEfectivo = $det->sale->saleTransactions->where('paymentType', 'Efectivo')->sum('amount') > 0;
                        @endphp
                        @if ($paymentEfectivo && $paymentQr)
                            <span class="label" style="font-size: 9px; background:#5bc0de; color:#fff;">Mixto</span>
                        @elseif ($paymentEfectivo)
                            <span class="label label-success" style="font-size: 9px;">
                                <i class="fa-solid fa-money-bill-wave"></i>
                            </span>
                        @elseif ($paymentQr)
                            <span class="label label-primary" style="font-size: 9px;">
                                <i class="fa-solid fa-qrcode"></i>
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            <h5 class="text-center" style="margin-top: 40px; color: #aaa;">
                                <i class="fa-solid fa-cash-register" style="font-size: 40px; opacity: 0.3;"></i>
                                <br><br>No hay ventas registradas para este producto.
                            </h5>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-12">
    <div class="col-md-4" style="overflow-x: auto;">
        @if(count($data) > 0)
            <p class="text-muted" style="margin-top: 8px;">
                Mostrando del {{ $data->firstItem() }} al {{ $data->lastItem() }}
                de {{ $data->total() }} registros.
            </p>
        @endif
    </div>
    <div class="col-md-8" style="overflow-x: auto;">
        <nav class="text-right">{{ $data->links() }}</nav>
    </div>
</div>

<script>
    var pageSales = "{{ request('page') }}";
    $(document).ready(function () {
        $('.page-link').click(function (e) {
            e.preventDefault();
            let link = $(this).attr('href');
            if (link) {
                pageSales = link.split('=')[1];
                listSales(pageSales);
            }
        });
    });
</script>
