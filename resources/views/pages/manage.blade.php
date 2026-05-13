@extends('layouts.app')

@section('header_actions')
    <a href="{{ route('entry') }}" class="button button-primary manage-header-entry">Registrar entrada</a>
    <button class="button button-outline manage-header-pending" type="button" data-open-modal="managePendingModal">Pagos pendientes</button>
@endsection

@section('content')
<section class="manage-command">
    <article class="card manage-command-card">
        <div class="manage-command-copy">
            <small>Centro operativo</small>
            <h3>Busca un ticket y completa la operacion en segundos</h3>
            <p>Desde aqui controlas entradas, encuentras tickets activos y abres el detalle de salida con toda la informacion organizada.</p>
        </div>

        <form method="GET" action="{{ route('manage') }}" class="manage-command-form" data-loading-form>
            <label class="field">
                <span>Busqueda principal</span>
                <span class="manage-search-input-wrap">
                    <input type="text" name="lookup" placeholder="Placa, ticket o codigo de barras" autofocus aria-describedby="manageSearchEnterTip">
                    <span class="manage-search-tooltip" id="manageSearchEnterTip" role="tooltip">Enter para buscar</span>
                </span>
            </label>
            <button class="button button-primary" type="submit">Buscar ticket</button>
        </form>

        <div class="manage-command-actions">
            <a href="{{ route('entry') }}" class="quick-action quick-strong manage-primary-action">
                <span class="action-icon">@include('partials.svg.car-enter')</span>
                <strong>Registrar entrada</strong>
                <span>Ingreso agil con seleccion visual</span>
            </a>
            <button type="button" class="quick-action alt manage-pending-action"  data-open-modal="managePendingModal">
                <span class="action-icon">@include('partials.svg.wallet')</span>
                <strong>Ver pendientes</strong>
                <span>Liquidaciones por regularizar</span>
            </button>
        </div>
    </article>
</section>

<section class="stats-grid dashboard-stats-grid manage-stats-grid">
    <article class="card stat-card stat-card-premium manage-stat-card manage-stat-active"><div class="stat-card-head"><span>Activos</span><span class="stat-card-icon">@include('partials.svg.car')</span></div><strong>{{ $overviewStats['active'] }}</strong><small>Vehiculos en parqueadero</small></article>
    <article class="card stat-card stat-card-premium manage-stat-card manage-stat-pending"><div class="stat-card-head"><span>Pendientes</span><span class="stat-card-icon">@include('partials.svg.wallet')</span></div><strong>{{ $overviewStats['pending'] }}</strong><small>Pagos por recaudar</small></article>
    <article class="card stat-card stat-card-premium manage-stat-card manage-stat-entry"><div class="stat-card-head"><span>Entradas hoy</span><span class="stat-card-icon">@include('partials.svg.arrow-in')</span></div><strong>{{ $overviewStats['todayEntries'] }}</strong><small>Tickets generados</small></article>
    <article class="card stat-card stat-card-premium manage-stat-card manage-stat-exit"><div class="stat-card-head"><span>Salidas hoy</span><span class="stat-card-icon">@include('partials.svg.arrow-out')</span></div><strong>{{ $overviewStats['todayClosed'] }}</strong><small>Movimientos cerrados</small></article>
</section>

<section class="manage-operations-section">
    <article class="card stack-md manage-operations-card manage-active-board">
        <div class="manage-section-head">
            <div>
                <h3>Tickets activos</h3>
                <p>Haz clic en la accion para ir a la salida del ticket.</p>
            </div>
            <span class="manage-section-count">{{ $overviewStats['active'] }} activos</span>
        </div>

        <div class="active-ticket-list">
            @forelse ($activeTickets as $ticket)
                <a href="{{ route('transaction.show', $ticket) }}" class="active-ticket-item">
                    <span class="ticket-status-dot"></span>
                    <div class="ticket-main-data">
                        <span>{{ $ticket->ticket_code }}</span>
                        <strong>{{ $ticket->plate }}</strong>
                    </div>
                    <div class="ticket-meta-block">
                        <span>Tipo</span>
                        <strong>{{ strtoupper($ticket->vehicle_type) }}</strong>
                    </div>
                    <div class="ticket-meta-block">
                        <span>Tarifa</span>
                        <strong>{{ $ticket->tariffProfile?->name ?? 'Sin tarifa' }}</strong>
                    </div>
                    <div class="ticket-meta-block">
                        <span>Ubicacion</span>
                        <strong>{{ $ticket->location_number ?: '-' }}</strong>
                    </div>
                    <div class="ticket-meta-block">
                        <span>Ingreso</span>
                        <strong>{{ optional($ticket->entry_time)->format('d/m H:i') }}</strong>
                    </div>
                    <div class="ticket-meta-block ticket-sync-block">
                        <span>Portal</span>
                        @include('partials.sync-badge', ['ticket' => $ticket])
                    </div>
                    <span class="ticket-open-action">Abrir salida</span>
                </a>
            @empty
                <div class="manage-empty-state">
                    <span class="empty-state-icon">@include('partials.svg.car')</span>
                    <strong>Sin tickets activos</strong>
                    <p>Cuando registres una entrada aparecera aqui lista para abrir su salida.</p>
                    <a href="{{ route('entry') }}" class="button button-primary">Registrar entrada</a>
                </div>
            @endforelse
        </div>
        @include('partials.simple-pagination', ['paginator' => $activeTickets, 'label' => 'Tickets activos'])
    </article>

    <article class="card stack-md manage-operations-card manage-movement-board">
        <div class="manage-section-head">
            <div>
                <h3>Ultimos movimientos</h3>
                <p>Tickets pagados o pendientes recientemente.</p>
            </div>
            <span class="manage-section-count">Recientes</span>
        </div>

        <div class="movement-list">
            @forelse ($recentClosedTickets as $ticket)
                <a href="{{ route('transaction.show', $ticket) }}" class="movement-item">
                    <div class="movement-ticket">
                        <span>{{ $ticket->ticket_code }}</span>
                        <strong>{{ $ticket->plate }}</strong>
                    </div>
                    <span class="pill {{ $ticket->status === 'paid' ? 'success' : 'warning' }}">{{ $ticket->status === 'paid' ? 'Pagado' : 'Pendiente' }}</span>
                    @include('partials.sync-badge', ['ticket' => $ticket])
                    <strong class="movement-total">{{ '$'.number_format($ticket->payment?->total ?? 0, 0, ',', '.') }}</strong>
                    <span class="movement-open">Abrir</span>
                </a>
            @empty
                <div class="manage-empty-state compact">
                    <strong>Sin movimientos recientes</strong>
                    <p>Los tickets cerrados o pendientes se mostraran aqui.</p>
                </div>
            @endforelse
        </div>
        @include('partials.simple-pagination', ['paginator' => $recentClosedTickets, 'label' => 'Ultimos movimientos'])
    </article>
</section>

<div class="modal-backdrop" data-app-modal-id="managePendingModal">
    <div class="modal-card modal-card-lg pending-modal-card">
        <button class="modal-close" type="button" data-close-app-modal>&times;</button>
        <span class="modal-kicker">PAGOS PENDIENTES</span>
        <h3>Liquidaciones por regularizar</h3>
        <p>Selecciona el ticket pendiente y completa su pago desde el detalle.</p>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Placa</th>
                        <th>Total</th>
                        <th>Metodo</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendingPayments as $payment)
                        <tr>
                            <td>{{ $payment->ticket?->ticket_code }}</td>
                            <td>{{ $payment->ticket?->plate }}</td>
                            <td>{{ '$'.number_format($payment->total, 0, ',', '.') }}</td>
                            <td>{{ strtoupper($payment->method) }}</td>
                            <td><a href="{{ route('transaction.show', $payment->ticket) }}" class="table-link">Abrir</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No hay pagos pendientes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('partials.simple-pagination', ['paginator' => $pendingPayments, 'label' => 'Pagos pendientes'])
    </div>
</div>
@endsection
