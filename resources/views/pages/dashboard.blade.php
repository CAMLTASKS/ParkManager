@extends('layouts.app')

@section('header_actions')
    <a href="{{ route('entry') }}" class="button button-outline">Registrar entrada</a>
    <a href="{{ route('manage') }}" class="button button-primary">Gestionar ahora</a>
@endsection

@section('content')
@php
    $maxRevenue = max($revenueTrend->max('value'), 1);
    $maxOccupancy = max($occupancyTrend->max('value'), 1);
    $maxMovement = max($movementTrend->max(fn($point) => max($point['entries'], $point['exits'])), 1);
    $vehicleTotal = max($vehicleMix->sum('value'), 1);
    $pendingPaymentsCount = $pendingPayments->count();
@endphp

<section class="ops-dashboard">
    <article class="ops-hero-panel">
        <div class="ops-hero-copy">
            <span class="ops-kicker">Vista operacional en tiempo real</span>
            <h2>Dashboard Principal</h2>
            <p>Control de entradas, salidas, ocupacion, recaudo y alertas desde una sola pantalla.</p>
        </div>
        <div class="ops-command-grid">
            <a href="{{ route('entry') }}" class="ops-command primary">
                <span class="action-icon">@include('partials.svg.car-enter')</span>
                <strong>Entrada rapida</strong>
                <small>Placa o codigo de ticket</small>
            </a>
            <a href="{{ route('manage') }}" class="ops-command">
                <span class="action-icon">@include('partials.svg.cashier')</span>
                <strong>Salida y cobro</strong>
                <small>Gestion del movimiento</small>
            </a>
            <button class="ops-command" type="button" data-open-modal="pendingPaymentsModal">
                <span class="action-icon">@include('partials.svg.wallet')</span>
                <strong>Pendientes</strong>
                <small>{{ $pendingPaymentsCount }} por revisar</small>
            </button>
            @if ($currentUser->isAdmin())
                <a href="{{ route('reports') }}" class="ops-command">
                    <span class="action-icon">@include('partials.svg.chart')</span>
                    <strong>Reportes</strong>
                    <small>Filtros y graficos</small>
                </a>
            @endif
        </div>
    </article>

    <div class="ops-top-grid">
        <article class="ops-card ops-sync-card">
            <div class="ops-card-title">
                <h3>Sincronizacion portal</h3>
                <span class="ops-mini-icon">@include('partials.svg.chart')</span>
            </div>
            <div class="sync-dashboard-grid">
                <div>
                    <span>Ultima sincronizacion</span>
                    <strong data-portal-sync-last>{{ $portalSync['lastSyncedAt'] ? $portalSync['lastSyncedAt']->format('d/m/Y h:i A') : 'Sin envios' }}</strong>
                </div>
                <div>
                    <span>Proxima sincronizacion</span>
                    <strong data-portal-sync-next>{{ $portalSync['isDue'] ? 'Lista ahora' : $portalSync['nextRunAt']->format('d/m/Y h:i A') }}</strong>
                </div>
                <div>
                    <span>Pendientes</span>
                    <strong data-portal-sync-pending>{{ $portalSync['pendingCount'] }}</strong>
                </div>
                <div>
                    <span>Fallidos</span>
                    <strong data-portal-sync-failed>{{ $portalSync['failedCount'] }}</strong>
                </div>
            </div>
            <div class="sync-dashboard-actions">
                <small>Intervalo configurado: cada {{ $portalSync['intervalMinutes'] }} min.</small>
                <button class="button button-outline" type="button" data-portal-sync-now>Sincronizar ahora</button>
            </div>
            @if ($portalSync['lastFailure'])
                <p class="sync-dashboard-error">{{ $portalSync['lastFailure'] }}</p>
            @endif
        </article>

        <article class="ops-card ops-vehicle-card">
            <div class="ops-card-title">
                <h3>Vehiculos activos</h3>
                <span class="ops-mini-icon">@include('partials.svg.car')</span>
            </div>
            <div class="ops-donut-wrap">
                <div class="ops-donut" style="--progress: {{ $capacityPercent }}%">
                    <strong>{{ $capacityUsed }}</strong>
                    <span>/{{ $capacity }}</span>
                </div>
                <div class="ops-legend">
                    @foreach ($vehicleMix as $mix)
                        <div>
                            <span><i style="background: {{ $mix['color'] }}"></i>{{ $mix['label'] }}</span>
                            <strong>{{ $mix['value'] }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>
            <small>Capacidad total en parqueadero: {{ $capacityUsed }}/{{ $capacity }}</small>
        </article>

        <article class="ops-card">
            <div class="ops-card-title">
                <h3>Entradas y salidas hoy</h3>
                <span class="ops-mini-icon">@include('partials.svg.arrow-in')</span>
            </div>
            <div class="ops-dual-stat">
                <div><strong>{{ $stats[1]['value'] }}</strong><span>Entradas</span></div>
                <div><strong>{{ $todayExitCount }}</strong><span>Salidas</span></div>
            </div>
            <div class="ops-movement-bars">
                @foreach ($movementTrend as $point)
                    <div>
                        <span class="in" style="height: {{ max(($point['entries'] / $maxMovement) * 74, 8) }}px"></span>
                        <span class="out" style="height: {{ max(($point['exits'] / $maxMovement) * 74, 8) }}px"></span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="ops-card ops-wide-card">
            <div class="ops-card-title">
                <h3>Ocupacion en tiempo real</h3>
                <span class="ops-mini-icon">@include('partials.svg.chart')</span>
            </div>
            <div class="ops-wave-chart">
                @foreach ($occupancyTrend as $point)
                    <span style="height: {{ max(($point['value'] / $maxOccupancy) * 92, 8) }}px">
                        <i>{{ $point['value'] }}</i>
                    </span>
                @endforeach
            </div>
            <div class="ops-chart-labels">
                @foreach ($occupancyTrend as $point)
                    <small>{{ $point['label'] }}</small>
                @endforeach
            </div>
        </article>

        <article class="ops-card ops-money-card">
            <div class="ops-card-title">
                <h3>Recaudo del dia</h3>
                <span class="ops-menu-dot">...</span>
            </div>
            <strong class="ops-money">{{ $financeStat ?? '$0' }}</strong>
            <p>Cobrado: {{ '$'.number_format($todayIncomeRaw, 0, ',', '.') }} - Pendiente: {{ '$'.number_format($pendingAmount, 0, ',', '.') }}</p>
            <div class="ops-progress">
                <span style="width: {{ $goalProgress }}%"></span>
            </div>
            <small>Meta estimada: {{ '$'.number_format($dailyTarget, 0, ',', '.') }}</small>
        </article>

        <article class="ops-card ops-locker-card">
            <div class="ops-card-title">
                <h3>Servicio de lockers</h3>
                <span class="ops-mini-icon">@include('partials.svg.wallet')</span>
            </div>
            <div class="locker-dashboard-grid">
                <div><span>Activos</span><strong>{{ $lockerStats['active'] }}</strong></div>
                <div><span>Usos hoy</span><strong>{{ $lockerStats['today'] }}</strong></div>
                <div><span>Recaudo locker</span><strong>{{ '$'.number_format($lockerStats['income'], 0, ',', '.') }}</strong></div>
                <div><span>Tarifa fija</span><strong>{{ '$'.number_format($lockerStats['fee'], 0, ',', '.') }}</strong></div>
            </div>
        </article>
    </div>

    <div class="ops-main-grid">
        <article class="ops-monitor">
            <h3>Monitor de operaciones criticas</h3>
            <div class="ops-monitor-row">
                <span>Tasa de entrada promedio</span>
                <strong>{{ $criticalStats['entryRate'] }} veh/hora</strong>
                <div class="ops-spark positive"></div>
            </div>
            <div class="ops-monitor-row">
                <span>Tasa de salida promedio</span>
                <strong>{{ $criticalStats['exitRate'] }} veh/hora</strong>
                <div class="ops-spark negative"></div>
            </div>
            <div class="ops-monitor-row">
                <span>Estancias largas</span>
                <strong>{{ $criticalStats['alertRate'] }}%</strong>
                <div class="ops-spark warning"></div>
            </div>
        </article>

        <article class="ops-card ops-income-flow">
            <div class="ops-card-title">
                <h3>Flujo de ingresos y capacidad</h3>
                <span class="ops-mini-icon">@include('partials.svg.wallet')</span>
            </div>
            <div class="ops-revenue-bars">
                @foreach ($revenueTrend as $point)
                    <div>
                        <span style="height: {{ max(($point['value'] / $maxRevenue) * 170, 8) }}px"></span>
                        <small>{{ $point['label'] }}</small>
                    </div>
                @endforeach
            </div>
        </article>
    </div>

    <div class="ops-bottom-grid">
        <article class="ops-card ops-table-card">
            <div class="card-heading">
                <div>
                    <h3>Vehiculos activos</h3>
                    <p>Listado para abrir salida o validar ubicacion.</p>
                </div>
                <a href="{{ route('manage') }}" class="button-chip">Abrir gestion</a>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Placa</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Sync</th>
                            <th>Ubicacion</th>
                            <th>Ingreso</th>
                            <th>Accion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activeTickets as $ticket)
                            <tr>
                                <td>{{ $ticket->ticket_code }}</td>
                                <td><span class="plate">{{ $ticket->plate }}</span></td>
                                <td>{{ strtoupper($ticket->vehicle_type) }}</td>
                                <td><span class="pill success">Entrado</span></td>
                                <td>@include('partials.sync-badge', ['ticket' => $ticket])</td>
                                <td>{{ $ticket->location_number ?: '-' }}</td>
                                <td>{{ optional($ticket->entry_time)->format('h:i A') }}</td>
                                <td><a href="{{ route('transaction.show', $ticket) }}" class="table-link">Cobrar</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="8">No hay vehiculos activos ahora mismo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <aside class="ops-side-stack">
            <article class="ops-card">
                <div class="ops-card-title">
                    <h3>Pagos pendientes</h3>
                    <span class="ops-menu-dot">...</span>
                </div>
                <strong class="ops-side-total">{{ '$'.number_format($pendingAmount, 0, ',', '.') }}</strong>
                <p>{{ $pendingPaymentsCount }} tickets pendientes por recaudo.</p>
                <button class="button button-outline button-block" type="button" data-open-modal="pendingPaymentsModal">Ver todos los cobros</button>
            </article>
            <article class="ops-card">
                <div class="ops-card-title">
                    <h3>Alertas operativas</h3>
                    <span class="ops-menu-dot">!</span>
                </div>
                <div class="alert-list">
                    @forelse ($alerts as $alert)
                        <div class="alert-item">{{ $alert }}</div>
                    @empty
                        <div class="alert-item success">Todo en orden. No hay novedades criticas.</div>
                    @endforelse
                </div>
            </article>
        </aside>
    </div>

    <article class="ops-card ops-recent-card">
        <div class="card-heading">
            <div>
                <h3>Movimientos recientes</h3>
                <p>Ultimas operaciones del turno.</p>
            </div>
        </div>
        <div class="ops-recent-list">
            @forelse ($recentMovements as $ticket)
                <a href="{{ route('transaction.show', $ticket) }}">
                    <span>{{ $ticket->ticket_code }}</span>
                    <strong>{{ $ticket->plate }}</strong>
                    <small>{{ strtoupper($ticket->vehicle_type) }} - {{ optional($ticket->updated_at)->format('h:i A') }}</small>
                    <b>{{ $ticket->payment ? '$'.number_format($ticket->payment->total, 0, ',', '.') : 'Activo' }}</b>
                </a>
            @empty
                <p class="empty-inline">Aun no hay movimientos recientes.</p>
            @endforelse
        </div>
    </article>
</section>

<div class="modal-backdrop" data-app-modal-id="pendingPaymentsModal">
    <div class="modal-card modal-card-lg">
        <button class="modal-close" type="button" data-close-app-modal>&times;</button>
        <span class="modal-kicker">PENDIENTES</span>
        <h3>Pagos pendientes</h3>
        <p>Desde aqui puedes revisar y entrar al detalle de cada cobro pendiente.</p>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Placa</th>
                        <th>Salida</th>
                        <th>Total</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendingPayments as $payment)
                        <tr>
                            <td>{{ $payment->ticket?->ticket_code }}</td>
                            <td>{{ $payment->ticket?->plate }}</td>
                            <td>{{ optional($payment->ticket?->exit_time)->format('d/m H:i') ?: 'Pendiente' }}</td>
                            <td>{{ '$'.number_format($payment->total, 0, ',', '.') }}</td>
                            <td><a class="table-link" href="{{ route('transaction.show', $payment->ticket) }}">Abrir</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No hay pagos pendientes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
