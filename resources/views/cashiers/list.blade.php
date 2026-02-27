<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-hover" style="margin-bottom: 0;">
            <thead>
                <tr>
                    <th style="width: 220px;">Cajero</th>
                    <th>Descripción</th>
                    <th style="text-align: center; width: 150px;">Estado</th>
                    <th style="text-align: center; width: 150px;">Apertura</th>
                    <th style="text-align: center; width: 150px;">Cierre</th>
                    <th style="width: 220px;">Resumen</th>
                    <th style="text-align: right; width: 130px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cashier as $item)
                    <tr>
                        {{-- ── Cajero ──────────────────────────────────────────── --}}
                        <td style="vertical-align: middle;">
                            @php
                                $image = $item->user->avatar
                                    ? asset('storage/' . $item->user->avatar)
                                    : asset('images/default.jpg');
                            @endphp
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="{{ $image }}" alt="{{ $item->user->name }}"
                                    style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid #e0e0e0; flex-shrink: 0;">
                                <div>
                                    <b style="font-size: 13px;">{{ strtoupper($item->user->name) }}</b><br>
                                    <small class="text-muted">{{ $item->user->role->display_name ?? '—' }}</small><br>
                                    <span style="font-size: 10px; color: #aaa;">#{{ $item->id }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- ── Descripción ─────────────────────────────────────── --}}
                        <td style="vertical-align: middle;">
                            <b style="font-size: 13px;">{{ strtoupper($item->title) }}</b>
                            @if ($item->sale)
                                <br>
                                <span class="label label-info" style="font-size: 11px; margin-top: 3px; display: inline-block;">
                                    <i class="voyager-shop"></i> {{ $item->sale }} venta(s)
                                </span>
                            @endif
                            @if ($item->observation)
                                <br><small class="text-muted" style="font-style: italic;">{{ \Illuminate\Support\Str::limit($item->observation, 40) }}</small>
                            @endif
                        </td>

                        {{-- ── Estado ──────────────────────────────────────────── --}}
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($item->status == 'Abierta')
                                <span class="label label-success" style="padding: 6px 12px; font-size: 12px; border-radius: 20px;">
                                    <i class="voyager-unlock"></i> Abierta
                                </span>
                            @elseif ($item->status == 'Cerrada')
                                <span class="label label-danger" style="padding: 6px 12px; font-size: 12px; border-radius: 20px;">
                                    <i class="voyager-lock"></i> Cerrada
                                </span>
                            @elseif ($item->status == 'Cierre Pendiente')
                                <span class="label label-primary" style="padding: 6px 12px; font-size: 12px; border-radius: 20px;">
                                    <i class="voyager-watch"></i> Cierre Pend.
                                </span>
                            @elseif ($item->status == 'Apertura Pendiente')
                                <span class="label label-warning" style="padding: 6px 12px; font-size: 12px; border-radius: 20px;">
                                    <i class="voyager-key"></i> Apertura Pend.
                                </span>
                            @endif
                        </td>

                        {{-- ── Apertura ─────────────────────────────────────────── --}}
                        <td style="text-align: center; vertical-align: middle;">
                            <b style="font-size: 14px; color: #27ae60;">Bs. {{ number_format($item->amountOpening, 2, ',', '.') }}</b><br>
                            <small class="text-muted">
                                <i class="fa fa-calendar"></i> {{ date('d/m/Y', strtotime($item->created_at)) }}<br>
                                <i class="fa fa-clock-o"></i> {{ date('H:i', strtotime($item->created_at)) }}
                            </small>
                        </td>

                        {{-- ── Cierre ───────────────────────────────────────────── --}}
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($item->closed_at)
                                <small class="text-muted">
                                    <i class="fa fa-calendar"></i> {{ date('d/m/Y', strtotime($item->closed_at)) }}<br>
                                    <i class="fa fa-clock-o"></i> {{ date('H:i', strtotime($item->closed_at)) }}
                                </small>
                            @else
                                <span class="text-muted" style="font-size: 18px;">—</span>
                            @endif
                        </td>

                        {{-- ── Resumen ──────────────────────────────────────────── --}}
                        <td style="vertical-align: middle; font-size: 12px;">
                            @if ($item->status == 'Cerrada')
                                <div style="line-height: 1.8;">
                                    <span class="text-muted">Monto de cierre:</span>
                                    <b style="float: right;">Bs. {{ number_format($item->amountClosed, 2, ',', '.') }}</b><br>
                                    <span class="text-muted">Faltante:</span>
                                    <b class="{{ $item->amountMissing > 0 ? 'text-danger' : 'text-muted' }}" style="float: right;">
                                        Bs. {{ number_format($item->amountMissing, 2, ',', '.') }}
                                    </b><br>
                                    <span class="text-muted">Sobrante:</span>
                                    <b class="{{ $item->amountLeftover > 0 ? 'text-success' : 'text-muted' }}" style="float: right;">
                                        Bs. {{ number_format($item->amountLeftover, 2, ',', '.') }}
                                    </b>
                                </div>
                            @elseif ($item->status == 'Abierta')
                                <span style="color: #27ae60; font-size: 12px;">
                                    <i class="fa fa-circle" style="font-size: 9px;"></i> En operación
                                </span>
                            @elseif ($item->status == 'Cierre Pendiente')
                                <span class="text-primary" style="font-size: 12px;">
                                    <i class="fa fa-hourglass-half"></i> Esperando confirmación de cierre
                                </span>
                            @elseif ($item->status == 'Apertura Pendiente')
                                <span class="text-warning" style="font-size: 12px;">
                                    <i class="fa fa-hourglass-start"></i> Esperando aceptación de apertura
                                </span>
                            @endif
                        </td>

                        {{-- ── Acciones ─────────────────────────────────────────── --}}
                        <td style="text-align: right; vertical-align: middle;">
                            @if (auth()->user()->hasPermission('read_cashiers'))
                                <a href="{{ route('cashiers.show', ['cashier' => $item->id]) }}"
                                    title="Ver detalle de caja" class="btn btn-sm btn-warning">
                                    <i class="voyager-eye"></i> Ver
                                </a>
                            @endif
                            <div class="btn-group" style="margin-left: 3px;">
                                <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" title="Más opciones">
                                    <i class="fa fa-print"></i> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li>
                                        <a href="#" onclick="openWindow({{ $item->id }})" title="Imprimir Comprobante de Apertura">
                                            <i class="fa-solid fa-print" style="color: #337ab7;"></i> Comprobante de Apertura
                                        </a>
                                    </li>
                                    @if ($item->status == 'Cerrada')
                                        <li>
                                            <a href="#" onclick="closeWindow({{ $item->id }})" title="Imprimir Comprobante de Cierre">
                                                <i class="fa-solid fa-print" style="color: #e74c3c;"></i> Comprobante de Cierre
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <h5 class="text-center" style="margin: 50px 0; color: #bbb;">
                                <img src="{{ asset('images/empty.png') }}" width="110px" alt=""
                                    style="opacity: 0.6; display: block; margin: 0 auto 12px;">
                                No hay cajas registradas
                            </h5>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-12" style="margin-top: 10px;">
    <div class="col-md-4" style="overflow-x: auto;">
        @if (count($cashier) > 0)
            <p class="text-muted" style="margin-top: 8px;">
                Mostrando del {{ $cashier->firstItem() }} al {{ $cashier->lastItem() }} de
                {{ $cashier->total() }} registros.
            </p>
        @endif
    </div>
    <div class="col-md-8" style="overflow-x: auto;">
        <nav class="text-right">
            {{ $cashier->links() }}
        </nav>
    </div>
</div>

<script>
    var page = "{{ request('page') }}";
    $(document).ready(function () {
        $('.page-link').click(function (e) {
            e.preventDefault();
            let link = $(this).attr('href');
            if (link) {
                page = link.split('=')[1];
                list(page);
            }
        });

        $('.btn-agregar-gasto').click(function () {
            let cashier_id = $(this).data('cashier_id');
            $('#form-agregar-gasto input[name="cashier_id"]').val(cashier_id);
        });
    });
</script>
