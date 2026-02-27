@extends('voyager::master')

@section('page_title', 'Gestión de Vencimientos')

@section('css')
<style>
    .stats-card {
        background: #fff;
        border-radius: 8px;
        padding: 18px 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        border-left: 4px solid transparent;
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .stats-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .stats-card.active { box-shadow: 0 0 0 2px currentColor; }
    .stats-card.expired  { border-left-color: #e74c3c; }
    .stats-card.critical { border-left-color: #f39c12; }
    .stats-card.warning  { border-left-color: #3498db; }
    .stats-card.ok       { border-left-color: #2ecc71; }

    .stats-icon {
        width: 48px; height: 48px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; margin-right: 14px; flex-shrink: 0;
    }
    .stats-info h3 { margin: 0; font-size: 26px; font-weight: bold; color: #333; }
    .stats-info p  { margin: 0; font-size: 11px; color: #888; text-transform: uppercase; font-weight: 600; }

    .bg-expired-icon  { background: #ffebee; color: #c62828; }
    .bg-critical-icon { background: #fff8e1; color: #e65100; }
    .bg-warning-icon  { background: #e3f2fd; color: #1565c0; }
    .bg-ok-icon       { background: #e8f5e9; color: #2e7d32; }

    .filter-btn { margin-right: 5px; margin-bottom: 8px; }
    .filter-btn.active { box-shadow: inset 0 3px 5px rgba(0,0,0,0.15); }

    .expiry-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: bold;
        white-space: nowrap;
    }
    .badge-expired  { background: #ffebee; color: #c62828; border: 1px solid #ef9a9a; }
    .badge-critical { background: #fff3e0; color: #e65100; border: 1px solid #ffcc80; }
    .badge-warning  { background: #e3f2fd; color: #1565c0; border: 1px solid #90caf9; }
    .badge-ok       { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }

    tr.row-expired  { background-color: #fff5f5 !important; }
    tr.row-critical { background-color: #fffdf0 !important; }
    tr.row-warning  { background-color: #f0f7ff !important; }
</style>
@stop

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="fa fa-calendar-times-o" style="color: #e74c3c;"></i>
            Gestión de Vencimientos
            <small>Control de fechas de expiración del inventario</small>
        </h1>
        <div class="pull-right" style="margin-top: 20px;">
            <a href="{{ route('voyager.items.index') }}" class="btn btn-default btn-sm">
                <i class="voyager-list"></i> Volver a Productos
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="page-content container-fluid">

    {{-- ── Stats Cards ──────────────────────────────────────────────────────── --}}
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="stats-card expired" onclick="filterTable('expired')" id="card-expired">
                <div class="stats-icon bg-expired-icon">
                    <i class="fa fa-times-circle"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ $counts['expired'] }}</h3>
                    <p>Vencidos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stats-card critical" onclick="filterTable('critical')" id="card-critical">
                <div class="stats-icon bg-critical-icon">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ $counts['critical'] }}</h3>
                    <p>Vencen en ≤ 30 días</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stats-card warning" onclick="filterTable('warning')" id="card-warning">
                <div class="stats-icon bg-warning-icon">
                    <i class="fa fa-clock-o"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ $counts['warning'] }}</h3>
                    <p>Vencen en 31–90 días</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stats-card ok" onclick="filterTable('ok')" id="card-ok">
                <div class="stats-icon bg-ok-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ $counts['ok'] }}</h3>
                    <p>Vigentes (&gt; 90 días)</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tabla ──────────────────────────────────────────────────────────── --}}
    <div class="panel" style="border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-radius: 8px;">
        <div class="panel-heading" style="background: #fff; border-bottom: 1px solid #f0f0f0; border-radius: 8px 8px 0 0; padding: 14px 20px; display: flex; align-items: center; flex-wrap: wrap; gap: 8px;">
            <strong style="margin-right: 10px; color: #555;"><i class="fa fa-filter"></i> Filtrar:</strong>
            <button class="btn btn-default btn-sm filter-btn active" onclick="filterTable('all')" id="btn-all">
                Todos ({{ $stocks->count() }})
            </button>
            <button class="btn btn-danger btn-sm filter-btn" onclick="filterTable('expired')" id="btn-expired">
                <i class="fa fa-times-circle"></i> Vencidos ({{ $counts['expired'] }})
            </button>
            <button class="btn btn-warning btn-sm filter-btn" onclick="filterTable('critical')" id="btn-critical">
                <i class="fa fa-exclamation-triangle"></i> ≤ 30 días ({{ $counts['critical'] }})
            </button>
            <button class="btn btn-info btn-sm filter-btn" onclick="filterTable('warning')" id="btn-warning">
                <i class="fa fa-clock-o"></i> 31–90 días ({{ $counts['warning'] }})
            </button>
            <button class="btn btn-success btn-sm filter-btn" onclick="filterTable('ok')" id="btn-ok">
                <i class="fa fa-check-circle"></i> Vigentes ({{ $counts['ok'] }})
            </button>

            {{-- Buscador --}}
            <div class="input-group" style="width: 220px; margin-left: auto;">
                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                <input type="text" id="search-expiry" class="form-control input-sm" placeholder="Buscar producto…" oninput="searchTable()">
            </div>
        </div>

        <div class="panel-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table table-hover" id="expiry-table" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th style="background: #f8f9fa; font-size: 12px; text-transform: uppercase; color: #555; padding: 10px 14px;">Producto</th>
                            <th style="background: #f8f9fa; font-size: 12px; text-transform: uppercase; color: #555; text-align: center; width: 100px;">Lote</th>
                            <th style="background: #f8f9fa; font-size: 12px; text-transform: uppercase; color: #555; text-align: center; width: 130px;">Vencimiento</th>
                            <th style="background: #f8f9fa; font-size: 12px; text-transform: uppercase; color: #555; text-align: center; width: 80px;">Stock</th>
                            <th style="background: #f8f9fa; font-size: 12px; text-transform: uppercase; color: #555; text-align: right; width: 110px;">P. Venta</th>
                            <th style="background: #f8f9fa; font-size: 12px; text-transform: uppercase; color: #555; text-align: center; width: 160px;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stocks as $s)
                            @php
                                if ($s->daysLeft < 0) {
                                    $rowClass   = 'row-expired';
                                    $badgeClass = 'badge-expired';
                                    $badgeText  = 'Vencido hace ' . abs((int)$s->daysLeft) . ' día(s)';
                                    $dataFilter = 'expired';
                                } elseif ($s->daysLeft <= 30) {
                                    $rowClass   = 'row-critical';
                                    $badgeClass = 'badge-critical';
                                    $badgeText  = 'Vence en ' . (int)$s->daysLeft . ' día(s)';
                                    $dataFilter = 'critical';
                                } elseif ($s->daysLeft <= 90) {
                                    $rowClass   = 'row-warning';
                                    $badgeClass = 'badge-warning';
                                    $badgeText  = 'Vence en ' . (int)$s->daysLeft . ' días';
                                    $dataFilter = 'warning';
                                } else {
                                    $rowClass   = '';
                                    $badgeClass = 'badge-ok';
                                    $badgeText  = 'Vigente — ' . (int)$s->daysLeft . ' días';
                                    $dataFilter = 'ok';
                                }
                            @endphp
                            <tr class="{{ $rowClass }}" data-filter="{{ $dataFilter }}" data-name="{{ strtolower($s->item->nameGeneric ?? '') }}">
                                <td style="vertical-align: middle; padding: 10px 14px;">
                                    <b style="font-size: 13px;">{{ $s->item->nameGeneric ?? '—' }}</b>
                                    @if ($s->item->nameTrade ?? false)
                                        <br><small class="text-muted">{{ $s->item->nameTrade }}</small>
                                    @endif
                                    @if ($s->dispensed === 'Fraccionado')
                                        <span class="label label-info" style="font-size: 10px; margin-left: 4px;">Fracc.</span>
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    @if ($s->lote)
                                        <span style="font-size: 12px; font-family: monospace; background: #f5f5f5; padding: 2px 6px; border-radius: 4px;">{{ $s->lote }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <b style="font-size: 13px;">{{ \Carbon\Carbon::parse($s->expirationDate)->format('d/m/Y') }}</b>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <b style="font-size: 14px;">{{ number_format($s->stock, 2) }}</b>
                                    @if ($s->dispensed === 'Fraccionado')
                                        <br><small class="text-muted" style="font-size: 10px;">uds. fracc.</small>
                                    @endif
                                </td>
                                <td style="text-align: right; vertical-align: middle;">
                                    <b>Bs. {{ number_format($s->priceSale, 2) }}</b>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <span class="expiry-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <h5 class="text-center" style="margin: 40px 0; color: #bbb;">
                                        <i class="fa fa-check-circle" style="font-size: 40px; display: block; margin-bottom: 10px; color: #2ecc71;"></i>
                                        No hay productos con fecha de vencimiento registrada
                                    </h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Conteo visible --}}
        <div class="panel-footer" style="background: #fafafa; border-top: 1px solid #f0f0f0; padding: 8px 20px; font-size: 12px; color: #888; border-radius: 0 0 8px 8px;">
            <span id="visible-count">Mostrando {{ $stocks->count() }} de {{ $stocks->count() }} registros</span>
        </div>
    </div>

</div>
@stop

@section('javascript')
<script>
    let currentFilter = 'all';

    function filterTable(filter) {
        currentFilter = filter;

        // Actualizar botones
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        const btn = document.getElementById('btn-' + filter);
        if (btn) btn.classList.add('active');

        // Actualizar cards
        document.querySelectorAll('.stats-card').forEach(c => c.style.opacity = '0.5');
        const card = document.getElementById('card-' + filter);
        if (card) card.style.opacity = '1';
        if (filter === 'all') {
            document.querySelectorAll('.stats-card').forEach(c => c.style.opacity = '1');
        }

        applyFilter();
    }

    function searchTable() {
        applyFilter();
    }

    function applyFilter() {
        const search  = document.getElementById('search-expiry').value.toLowerCase().trim();
        const rows    = document.querySelectorAll('#expiry-table tbody tr[data-filter]');
        let visible   = 0;

        rows.forEach(row => {
            const matchFilter = currentFilter === 'all' || row.dataset.filter === currentFilter;
            const matchSearch = !search || row.dataset.name.includes(search);
            if (matchFilter && matchSearch) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('visible-count').textContent =
            'Mostrando ' + visible + ' de {{ $stocks->count() }} registros';
    }
</script>
@stop
