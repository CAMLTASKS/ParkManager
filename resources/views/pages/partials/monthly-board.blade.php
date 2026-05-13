@php
    $tabs = [
        'all' => 'Todos',
        'due' => 'Por vencer',
        'overdue' => 'Vencidas',
        'paid' => 'Pagadas',
        'active' => 'Activas',
        'cancelled' => 'Canceladas',
    ];
    $selectedLabel = $tabs[$filters['status']] ?? 'Todos';
@endphp

<section class="monthly-board-layout">
    <article class="card monthly-filter-panel">
        <div class="card-heading">
            <div>
                <h3>Vistas</h3>
                <p>{{ $selectedLabel }} en pantalla.</p>
            </div>
        </div>
        <div class="monthly-filter-list">
            @foreach ($tabs as $key => $label)
                @php
                    $tabCount = match($key) {
                        'active' => $monthlyStats['active'],
                        'overdue' => $monthlyStats['overdue'],
                        'due' => $monthlyStats['due'],
                        'cancelled' => $monthlyStats['cancelled'],
                        default => $monthlyStats['all'],
                    };
                @endphp
                <a data-monthly-filter class="{{ $filters['status'] === $key ? 'active' : '' }}" href="{{ route('monthly.index', ['status' => $key, 'search' => $filters['search']]) }}" data-status="{{ $key }}">
                    <span>{{ $label }}</span>
                    <strong>{{ $tabCount }}</strong>
                </a>
            @endforeach
        </div>
    </article>

    <article class="card monthly-vehicles-panel">
        <div class="card-heading">
            <div>
                <h3>Vehiculos mensuales</h3>
                <p>{{ $memberships->total() }} registros encontrados.</p>
            </div>
            <span class="pill info">{{ $selectedLabel }}</span>
        </div>

        <div class="monthly-card-grid">
            @forelse ($memberships as $membership)
                @php
                    $status = $membership->currentStatus();
                    $latestActivity = $membership->activities->first();
                    $statusLabel = $status === 'active' ? 'Al dia' : ($status === 'overdue' ? 'Vencida' : 'Cancelada');
                    $statusClass = $status === 'active' ? 'success' : ($status === 'overdue' ? 'warning' : 'danger');
                    $whatsappPhone = preg_replace('/\D+/', '', (string) $membership->phone);
                    $whatsappText = 'Hola '.$membership->customer_name.', tu mensualidad del parqueadero para la placa '.$membership->plate.' esta '.($status === 'overdue' ? 'vencida con '.$membership->daysOverdue().' dias pendientes.' : 'proxima a pago el '.optional($membership->next_payment_date)->format('d/m/Y')).'.';
                @endphp
                <article class="monthly-vehicle-card {{ $status }} {{ $selectedMembership && $selectedMembership->id === $membership->id ? 'active-card' : '' }}">
                    <div class="monthly-card-top">
                        <span class="pill {{ $statusClass }}">{{ $statusLabel }}</span>
                        <strong>{{ $membership->plate }}</strong>
                    </div>
                    <div class="monthly-owner">
                        <span>{{ $membership->customer_name }}</span>
                        <small>{{ $membership->phone ?: 'Sin telefono' }}</small>
                    </div>
                    <div class="monthly-card-facts">
                        <div><span>Vehiculo</span><strong>{{ strtoupper($membership->vehicle_type) }}</strong></div>
                        <div><span>Marca</span><strong>{{ $membership->vehicle_brand ?: 'Sin marca' }}</strong></div>
                        <div><span>Prox pago</span><strong>{{ optional($membership->next_payment_date)->format('d/m/Y') }}</strong></div>
                        <div><span>Tarifa</span><strong>{{ '$'.number_format($membership->tariffProfile?->unit_rate ?? 0, 0, ',', '.') }}</strong></div>
                    </div>
                    @if ($status === 'overdue')
                        <div class="monthly-warning-strip">{{ $membership->daysOverdue() }} dias vencidos</div>
                    @elseif ($latestActivity)
                        <div class="monthly-activity-strip">{{ ucfirst($latestActivity->event_type) }} {{ $latestActivity->occurred_at->format('d/m H:i') }}</div>
                    @else
                        <div class="monthly-activity-strip muted">Sin actividad registrada</div>
                    @endif
                    <div class="monthly-card-actions">
                        <a data-monthly-detail href="{{ route('monthly.index', ['membership' => $membership->id, 'status' => $filters['status'], 'search' => $filters['search']]) }}" data-membership="{{ $membership->id }}" class="button button-primary">Gestionar</a>
                        @if ($whatsappPhone)
                            <a href="https://api.whatsapp.com/send?phone={{ $whatsappPhone }}&text={{ rawurlencode($whatsappText) }}" target="_blank" rel="noopener" class="button button-whatsapp">@include('partials.svg.whatsapp') Notificar</a>
                        @endif
                    </div>
                </article>
            @empty
                <div class="empty-inline">No hay mensualidades para esta vista.</div>
            @endforelse
        </div>
        @include('partials.simple-pagination', ['paginator' => $memberships, 'label' => 'Mensualidades'])
    </article>
</section>
