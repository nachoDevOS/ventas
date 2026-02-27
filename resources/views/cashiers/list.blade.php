<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-hover">
            <thead>
                <tr>
                    <th style="text-align: center">Id</th>
                    <th style="text-align: center">Usuario</th>
                    <th style="text-align: center">Nombre</th>
                    <th style="text-align: center">Estado</th>
                    <th style="text-align: center">Apertura</th>
                    <th style="text-align: center">Cierre</th>
                    <th style="text-align: center">Detalles de cierre</th>
                    <th style="text-align: right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cashier as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td style="width: 200pt;">
                            @php
                                $image = $item->user->avatar ? asset('storage/' . $item->user->avatar) : asset('images/default.jpg');
                            @endphp
                            <div style="display: flex; align-items: center;">
                                <img src="{{ $image }}" alt="{{ $item->user->name }}" style="width: 40px; height: 40px; border-radius: 20px; margin-right: 10px; object-fit: cover;">
                                <div>
                                    <b>{{ strtoupper($item->user->name) }}</b><br>
                                    <small>{{ $item->user->role->display_name }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">{{ strtoupper($item->title) }}
                            <br>                            
                            <label class="label label-info" style="background-color: #5bc0de;"><i class="voyager-shop"></i> {{$item->sale}}</label>

                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($item->status == 'Abierta')
                                <label class="label label-success" style="padding: 5px 10px; font-size: 12px;"><i class="voyager-unlock"></i> Abierta</label>
                            @endif
                            @if ($item->status == 'Cerrada')
                                <label class="label label-danger" style="padding: 5px 10px; font-size: 12px;"><i class="voyager-lock"></i> Cerrada</label>
                            @endif

                            @if ($item->status == 'Cierre Pendiente')
                                <label class="label label-primary" style="padding: 5px 10px; font-size: 12px;"><i class="voyager-watch"></i> Cierre Pendiente</label>
                            @endif

                            @if ($item->status == 'Apertura Pendiente')
                                <label class="label label-warning" style="padding: 5px 10px; font-size: 12px;"><i class="voyager-key"></i> Apertura Pendiente</label>
                            @endif

                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <b style="font-size: 14px;">Bs. {{ number_format($item->amountOpening, 2, ',', '.') }}</b><br>
                            <small>{{ date('d/m/Y', strtotime($item->created_at)) }}</small><br>
                            <small>{{ date('h:i:s a', strtotime($item->created_at)) }}</small>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($item->closed_at)
                                <b>{{ date('d/m/Y', strtotime($item->closed_at)) }}</b><br>
                                <small>{{ date('h:i:s a', strtotime($item->closed_at)) }}</small>
                            @else
                                <small>--</small>
                            @endif
                        </td>
                        <td style="vertical-align: middle;">
                            {{-- @php
                                $cashierIn = $item->movements->where('type', 'ingreso')->where('deleted_at', NULL)->where('status', 'Aceptado')->sum('amount');
                                $cashierOut =0;

                                $paymentEfectivo = $item->sales->where('deleted_at', NULL)
                                    ->flatMap(function($sale) {
                                        return $sale->saleTransactions->where('paymentType', 'Efectivo')->pluck('amount');
                                    })
                                    ->sum();

                                $paymentQr = $item->sales->where('deleted_at', NULL)
                                    ->flatMap(function($sale) {
                                        return $sale->saleTransactions->where('paymentType', 'Qr')->pluck('amount');
                                    })
                                    ->sum();
                                $amountCashier = ($cashierIn + $paymentEfectivo) - $cashierOut;
                            @endphp --}}
                            @if ($item->status=='Cerrada')
                                <small>Monto de cierre:</small> <b>Bs. {{ number_format($item->amountClosed, 2, ',', '.') }}</b><br>
                                <small>Monto faltante:</small> <b class="@if($item->amountMissing > 0) text-danger @endif">Bs. {{ number_format($item->amountMissing, 2, ',', '.') }}</b><br>
                                <small>Monto Sobrante:</small> <b class="@if($item->amountLeftover > 0) text-success @endif">Bs. {{ number_format($item->amountLeftover, 2, ',', '.') }}</b><br>
                            @endif
                        </td>
                        <td style="text-align: right; vertical-align: middle;">
                            <div class="btn-group" style="margin-right: 3px">
                                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                    Mas <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu" style="left: -90px !important">
                                    {{-- @php
                                        dump($item->movements->first());
                                    @endphp --}}
                                    {{-- @foreach ($item->vault_details as $aux) --}}
                                        <li><a href="#" onclick="openWindow({{ $item->id }})"
                                                style="color: blue" data-toggle="modal" title="Imprimir Comprobante"><i
                                                    class="fa-solid fa-print"></i>
                                                Imprimir Comporbante de Apertura</a>
                                        </li>
                                    {{-- @endforeach --}}
                                    @if ($item->status == 'Cerrada')
                                        <li><a href="#" onclick="closeWindow({{ $item->id }})"
                                                style="color: red" data-toggle="modal"
                                                title="Imprimir Comprobante de Cierre"><i class="fa-solid fa-print"></i>
                                                Imprimir Comprobante de Cierre
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            @if (auth()->user()->hasPermission('read_cashiers'))
                                <a href="{{ route('cashiers.show', ['cashier' => $item->id]) }}" title="Editar"
                                    class="btn btn-sm btn-warning">
                                    <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">Ver</span>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <h5 class="text-center" style="margin-top: 50px">
                                <img src="{{ asset('images/empty.png') }}" width="120px" alt=""
                                    style="opacity: 0.8">
                                <br><br>
                                No hay resultados
                            </h5>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-12">
    <div class="col-md-4" style="overflow-x:auto">
        @if (count($cashier) > 0)
            <p class="text-muted">Mostrando del {{ $cashier->firstItem() }} al {{ $cashier->lastItem() }} de
                {{ $cashier->total() }} registros.</p>
        @endif
    </div>
    <div class="col-md-8" style="overflow-x:auto">
        <nav class="text-right">
            {{ $cashier->links() }}
        </nav>
    </div>
</div>

<script>
    var page = "{{ request('page') }}";
    $(document).ready(function() {

        $('.page-link').click(function(e) {
            e.preventDefault();
            let link = $(this).attr('href');
            if (link) {
                page = link.split('=')[1];
                list(page);
            }
        });

        $('.btn-agregar-gasto').click(function() {
            let cashier_id = $(this).data('cashier_id');
            $('#form-agregar-gasto input[name="cashier_id"]').val(cashier_id);
        });
    });
</script>
