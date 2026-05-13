@extends('layouts.app')

@section('header_actions')
    <button class="button button-outline" type="button" data-open-modal="entriesModal">Entradas</button>
    <button class="button button-outline" type="button" data-open-modal="exitsModal">Salidas</button>
@endsection

@section('content')
@php
    $maxDaily = max(collect($dailyData)->max('value'), 1);
    $maxHour = max(collect($hourlyData)->max('value'), 1);
    $maxGrouped = max($groupedRows->max('entries') ?? 1, 1);
@endphp

<section class="reports-command">
    <article class="card reports-filter-card">
        <div class="card-heading">
            <div>
                <h3>Filtros del reporte</h3>
                <p>Los indicadores, graficos y listado se actualizan con estos filtros.</p>
            </div>
        </div>
        <form method="GET" action="{{ route('reports') }}" class="report-filter-grid">
            <label class="field"><span>Fecha inicial</span><input type="date" name="date_from" value="{{ $filters['date_from'] ?? now()->format('Y-m-d') }}"></label>
            <label class="field"><span>Fecha final</span><input type="date" name="date_to" value="{{ $filters['date_to'] ?? now()->format('Y-m-d') }}"></label>
            <label class="field">
                <span>Vehiculo</span>
                <select name="vehicle_type">
                    <option value="">Todos</option>
                    <option value="moto" @selected(($filters['vehicle_type'] ?? '') === 'moto')>Moto</option>
                    <option value="auto" @selected(($filters['vehicle_type'] ?? '') === 'auto')>Auto</option>
                    <option value="bicicleta" @selected(($filters['vehicle_type'] ?? '') === 'bicicleta')>Bicicleta</option>
                </select>
            </label>
            <label class="field">
                <span>Metodo pago</span>
                <select name="payment_method">
                    <option value="">Todos</option>
                    <option value="efectivo" @selected(($filters['payment_method'] ?? '') === 'efectivo')>Efectivo</option>
                    <option value="nequi" @selected(($filters['payment_method'] ?? '') === 'nequi')>Nequi</option>
                </select>
            </label>
            <label class="field">
                <span>Estado</span>
                <select name="status">
                    <option value="">Todos</option>
                    <option value="paid" @selected(($filters['status'] ?? '') === 'paid')>Pagado</option>
                    <option value="pending_payment" @selected(($filters['status'] ?? '') === 'pending_payment')>Pendiente</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Activo</option>
                </select>
            </label>
            <label class="field">
                <span>Agrupar por</span>
                <select name="group_by">
                    <option value="dia" @selected($groupBy === 'dia')>Dia</option>
                    <option value="vehiculo" @selected($groupBy === 'vehiculo')>Vehiculo</option>
                </select>
            </label>
            <label class="field report-search-field"><span>Buscar placa o ticket</span><input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="ABC123 o PK-..."></label>
            <button class="button button-primary" type="submit">Filtrar</button>
            <a href="{{ route('reports') }}" class="button button-muted">Limpiar</a>
        </form>
    </article>
</section>

<section class="report-stat-strip">
    <article class="report-stat-card income"><span>Ingresos</span><strong>{{ $reportStats['income'] }}</strong><small>Pagos confirmados</small></article>
    <article class="report-stat-card"><span>Transacciones</span><strong>{{ $reportStats['transactions'] }}</strong><small>Tickets liquidados</small></article>
    <article class="report-stat-card"><span>Entradas</span><strong>{{ $reportStats['entries'] }}</strong><small>Movimientos filtrados</small></article>
    <article class="report-stat-card"><span>Salidas</span><strong>{{ $reportStats['exits'] }}</strong><small>Tickets con salida</small></article>
    <article class="report-stat-card warning"><span>Pendientes</span><strong>{{ $reportStats['pending'] }}</strong><small>Cobros por recaudar</small></article>
    <article class="report-stat-card"><span>Vehiculos</span><strong>{{ $reportStats['vehicles'] }}</strong><small>Registros analizados</small></article>
</section>

<section class="reports-visual-grid">
    <article class="card report-chart-card report-main-chart">
        <div class="card-heading">
            <div>
                <h3>Tendencia de ingresos</h3>
                <p>Recaudo confirmado dentro del rango filtrado.</p>
            </div>
        </div>
        <div class="report-bars">
            @foreach ($dailyData as $bar)
                <div>
                    <span style="height: {{ max(($bar['value'] / $maxDaily) * 210, 8) }}px"></span>
                    <strong>{{ '$'.number_format($bar['value'], 0, ',', '.') }}</strong>
                    <small>{{ $bar['label'] }}</small>
                </div>
            @endforeach
        </div>
    </article>

    <article class="card report-chart-card">
        <div class="card-heading"><div><h3>Composicion</h3><p>Vehiculos y metodos de pago.</p></div></div>
        <div class="report-mix-grid">
            <div class="legend vertical">
                @foreach ($vehicleMix as $mix)
                    <div class="legend-row"><span><i class="{{ $mix['class'] }}"></i>{{ $mix['label'] }}</span><strong>{{ $mix['value'] }}</strong></div>
                @endforeach
            </div>
            <div class="legend vertical">
                @foreach ($paymentMix as $mix)
                    <div class="legend-row"><span><i class="{{ $mix['class'] }}"></i>{{ $mix['label'] }}</span><strong>{{ $mix['value'] }}</strong></div>
                @endforeach
            </div>
        </div>
    </article>

    <article class="card report-chart-card report-hour-chart">
        <div class="card-heading"><div><h3>Comparativo horario</h3><p>Recaudo por franja.</p></div></div>
        <div class="report-hourline">
            @foreach ($hourlyData as $point)
                <div>
                    <span style="height: {{ max(($point['value'] / $maxHour) * 145, 8) }}px"></span>
                    <small>{{ $point['label'] }}</small>
                </div>
            @endforeach
        </div>
    </article>
</section>

<section class="reports-split-grid">
    <article class="card report-group-card">
        <div class="card-heading">
            <div>
                <h3>Agrupado por {{ $groupBy === 'vehiculo' ? 'vehiculo' : 'dia' }}</h3>
                <p>Resumen operativo de los registros filtrados.</p>
            </div>
        </div>
        <div class="report-group-list">
            @forelse ($groupedRows as $row)
                <div class="report-group-row">
                    <div>
                        <strong>{{ $row['label'] }}</strong>
                        <span>{{ $row['entries'] }} entradas - {{ $row['exits'] }} salidas</span>
                    </div>
                    <div class="report-group-bar"><span style="width: {{ max(($row['entries'] / $maxGrouped) * 100, 4) }}%"></span></div>
                    <small>{{ '$'.number_format($row['income'], 0, ',', '.') }} - {{ $row['active'] }} activos - {{ $row['pending'] }} pendientes</small>
                </div>
            @empty
                <p class="empty-inline">No hay registros para agrupar con estos filtros.</p>
            @endforelse
        </div>
    </article>

    <article class="card report-list-card">
        <div class="card-heading">
            <div>
                <h3>Listado de vehiculos</h3>
                <p>Resultados paginados del filtro actual.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Placa</th>
                        <th>Entrada / salida</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $ticket)
                        <tr>
                            <td>{{ $ticket->ticket_code }}</td>
                            <td><span class="plate">{{ $ticket->plate }}</span></td>
                            <td>{{ optional($ticket->entry_time)->format('d/m H:i') }} / {{ optional($ticket->exit_time)->format('H:i') ?: '--' }}</td>
                            <td>{{ strtoupper($ticket->vehicle_type) }}</td>
                            <td><span class="pill {{ $ticket->status === 'paid' ? 'success' : ($ticket->status === 'pending_payment' ? 'warning' : 'info') }}">{{ $ticket->status }}</span></td>
                            <td>{{ '$'.number_format($ticket->payment?->total ?? 0, 0, ',', '.') }}</td>
                            <td><a class="table-link" href="{{ route('transaction.show', $ticket) }}">Ver</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No hay registros con los filtros actuales.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('partials.simple-pagination', ['paginator' => $transactions, 'label' => 'Registros filtrados'])
    </article>
</section>

<div class="modal-backdrop" data-app-modal-id="entriesModal">
    <div class="modal-card modal-card-lg">
        <button class="modal-close" type="button" data-close-app-modal>&times;</button>
        <span class="modal-kicker">ENTRADAS</span>
        <h3>Ultimas entradas filtradas</h3>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Ticket</th><th>Placa</th><th>Ingreso</th><th>Tipo</th></tr></thead>
                <tbody>
                    @forelse ($entriesToday as $ticket)
                        <tr><td>{{ $ticket->ticket_code }}</td><td>{{ $ticket->plate }}</td><td>{{ optional($ticket->entry_time)->format('d/m H:i') }}</td><td>{{ strtoupper($ticket->vehicle_type) }}</td></tr>
                    @empty
                        <tr><td colspan="4">No hay entradas con estos filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-backdrop" data-app-modal-id="exitsModal">
    <div class="modal-card modal-card-lg">
        <button class="modal-close" type="button" data-close-app-modal>&times;</button>
        <span class="modal-kicker">SALIDAS</span>
        <h3>Ultimas salidas filtradas</h3>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Ticket</th><th>Placa</th><th>Salida</th><th>Total</th></tr></thead>
                <tbody>
                    @forelse ($exitsToday as $ticket)
                        <tr><td>{{ $ticket->ticket_code }}</td><td>{{ $ticket->plate }}</td><td>{{ optional($ticket->exit_time)->format('d/m H:i') }}</td><td>{{ '$'.number_format($ticket->payment?->total ?? 0, 0, ',', '.') }}</td></tr>
                    @empty
                        <tr><td colspan="4">No hay salidas con estos filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
