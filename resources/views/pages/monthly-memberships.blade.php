@extends('layouts.app')

@section('header_actions')
<button class="button button-primary" type="button" data-open-modal="createMonthlyModal">Nueva mensualidad</button>
<a href="{{ route('settings') }}" class="button button-outline">Tarifas mensualidad</a>
@endsection

@section('content')
<section class="manage-command monthly-command" data-monthly-page data-monthly-url="{{ route('monthly.data') }}">
    <article class="card manage-command-card monthly-command-card">
        <div class="manage-command-copy">
            <small>MENSUALIDADES</small>
            <h3>Control visual de clientes mensuales, pagos y movimientos</h3>
            <p>Busca una placa, revisa vencimientos y abre el detalle para registrar pagos o consultar actividad del mes.</p>
        </div>

        <form method="GET" action="{{ route('monthly.index') }}" class="manage-command-form monthly-command-form" data-monthly-search-form>
            <input type="hidden" name="status" value="{{ $filters['status'] }}">
            <label class="field">
                <span>Busqueda principal</span>
                <span class="manage-search-input-wrap">
                    <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Placa, cliente o telefono" autofocus>
                    <span class="manage-search-tooltip" role="tooltip">Enter para buscar</span>
                </span>
            </label>
            <button class="button button-primary" type="submit">Buscar mensualidad</button>
        </form>

        <div class="manage-command-actions monthly-command-actions">
            <a href="{{ route('monthly.index', ['status' => 'due']) }}" data-monthly-filter data-status="due" class="quick-action quick-strong manage-primary-action">
                <span class="action-icon">@include('partials.svg.clock')</span>
                <strong>{{ $monthlyStats['due'] }} por vencer</strong>
                <span>Proximos 5 dias</span>
            </a>
            <a href="{{ route('monthly.index', ['status' => 'overdue']) }}" data-monthly-filter data-status="overdue" class="quick-action alt manage-pending-action">
                <span class="action-icon">@include('partials.svg.wallet')</span>
                <strong>{{ $monthlyStats['overdue'] }} vencidas</strong>
                <span>Requieren notificacion</span>
            </a>
        </div>
    </article>
</section>

<section class="stats-grid dashboard-stats-grid manage-stats-grid monthly-stats-grid">
    <article class="card stat-card stat-card-premium manage-stat-card manage-stat-active"><div class="stat-card-head"><span>Activas</span><span class="stat-card-icon">@include('partials.svg.car')</span></div><strong>{{ $monthlyStats['active'] }}</strong><small>Mensualidades al dia</small></article>
    <article class="card stat-card stat-card-premium manage-stat-card manage-stat-pending"><div class="stat-card-head"><span>Vencidas</span><span class="stat-card-icon">@include('partials.svg.clock')</span></div><strong>{{ $monthlyStats['overdue'] }}</strong><small>Pendientes por pagar</small></article>
    <article class="card stat-card stat-card-premium manage-stat-card manage-stat-entry"><div class="stat-card-head"><span>Por vencer</span><span class="stat-card-icon">@include('partials.svg.chart')</span></div><strong>{{ $monthlyStats['due'] }}</strong><small>Ventana de 5 dias</small></article>
    <article class="card stat-card stat-card-premium manage-stat-card manage-stat-exit"><div class="stat-card-head"><span>Pagos hoy</span><span class="stat-card-icon">@include('partials.svg.wallet')</span></div><strong>{{ '$'.number_format($monthlyStats['paid'], 0, ',', '.') }}</strong><small>Recaudo mensual</small></article>
</section>

<div id="monthlyBoard">
    @include('pages.partials.monthly-board')
</div>

<div class="modal-backdrop" data-app-modal-id="createMonthlyModal">
    <div class="modal-card modal-card-lg monthly-modal-card">
        <button class="modal-close" type="button" data-close-app-modal>&times;</button>
        <span class="modal-kicker">MENSUALIDADES</span>
        <h3>Nueva mensualidad</h3>
        <p>Registra los datos del cliente y el vehiculo.</p>
        @include('pages.partials.monthly-form', [
            'action' => route('monthly.store'),
            'method' => 'POST',
            'membership' => null,
            'monthlyTariffs' => $monthlyTariffs,
        ])
    </div>
</div>

<div id="monthlyModalSlot">
    @include('pages.partials.monthly-detail-modal')
</div>
@endsection
