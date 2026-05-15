@if ($selectedMembership)
@php
    $activityMonthValue = $activityMonth ?? now()->format('Y-m');
    $monthStart = \Illuminate\Support\Carbon::createFromFormat('Y-m', $activityMonthValue)->startOfMonth();
    $monthEnd = $monthStart->copy()->endOfMonth();
    $monthlyActivities = $selectedMembership->activities
        ->filter(fn($activity) => $activity->occurred_at && $activity->occurred_at->betweenIncluded($monthStart, $monthEnd))
        ->sortByDesc('occurred_at');
@endphp
<div class="modal-backdrop is-visible" data-app-modal-id="monthlyDetailModal">
    <div class="modal-card modal-card-lg monthly-modal-card">
        <button class="modal-close" type="button" data-close-app-modal>&times;</button>
        <span class="modal-kicker">MENSUALIDAD</span>
        <h3>{{ $selectedMembership->plate }} - {{ $selectedMembership->customer_name }}</h3>
        <p>Estado: {{ $selectedMembership->currentStatus() === 'overdue' ? 'Vencida '.$selectedMembership->daysOverdue().' dias' : ucfirst($selectedMembership->currentStatus()) }}</p>

        <section class="monthly-detail-grid">
            <article class="monthly-panel">
                <h4>Datos del cliente</h4>
                @include('pages.partials.monthly-form', [
                    'action' => route('monthly.update', $selectedMembership),
                    'method' => 'PUT',
                    'membership' => $selectedMembership,
                    'monthlyTariffs' => $monthlyTariffs,
                ])
                @if ($selectedMembership->status !== 'cancelled')
                    <form method="POST" action="{{ route('monthly.cancel', $selectedMembership) }}" onsubmit="return confirm('Cancelar esta mensualidad?')" data-loading-form>
                        @csrf
                        <button class="button button-danger" type="submit">Cancelar mensualidad</button>
                    </form>
                @endif
            </article>

            <article class="monthly-panel">
                <div class="monthly-payment-box">
                    <h4>Pago mensualidad</h4>
                    <form method="POST" action="{{ route('monthly.pay', $selectedMembership) }}" class="stack-md" data-loading-form>
                        @csrf
                        <label class="field">
                            <span>Metodo</span>
                            <select name="method">
                                <option value="efectivo">Efectivo</option>
                                <option value="nequi">Nequi</option>
                            </select>
                        </label>
                        <label class="field">
                            <span>Valor a pagar</span>
                            <input type="number" min="0" name="amount" value="{{ old('amount', $selectedMembership->tariffProfile?->unit_rate ?? 0) }}" required>
                        </label>
                        <label class="field">
                            <span>Periodo desde</span>
                            <input type="date" name="period_start" value="{{ old('period_start', optional($selectedMembership->next_payment_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                        </label>
                        <button class="button button-primary" type="submit">Registrar pago y recibo</button>
                    </form>
                </div>

                <button class="button button-outline" type="button" data-open-modal="monthlyActivityModal">Ver actividad del mes</button>

                <div class="monthly-side-lists">
                    <section>
                        <h4>Pagos recientes</h4>
                        <div class="info-list compact">
                            @forelse ($selectedMembership->payments->sortByDesc('paid_at')->take(6) as $payment)
                                <div><span>{{ $payment->receipt_code }}</span><strong>{{ '$'.number_format($payment->amount, 0, ',', '.') }} - {{ $payment->paid_at->format('d/m/Y') }}</strong></div>
                            @empty
                                <div><span>Sin pagos</span><strong>Pendiente</strong></div>
                            @endforelse
                        </div>
                    </section>
                </div>
            </article>
        </section>
    </div>
</div>

<div class="modal-backdrop" data-app-modal-id="monthlyActivityModal">
    <div class="modal-card modal-card-lg monthly-modal-card">
        <button class="modal-close" type="button" data-close-app-modal>&times;</button>
        <span class="modal-kicker">ACTIVIDAD DEL MES</span>
        <h3>{{ $selectedMembership->plate }} - {{ $monthStart->format('m/Y') }}</h3>
        <form method="GET" action="{{ route('monthly.index') }}" class="field-grid monthly-activity-month-form" data-monthly-activity-form>
            <input type="hidden" name="membership" value="{{ $selectedMembership->id }}">
            <input type="hidden" name="status" value="{{ $filters['status'] }}">
            <input type="hidden" name="search" value="{{ $filters['search'] }}">
            <label class="field">
                <span>Mes</span>
                <input type="month" name="activity_month" value="{{ $activityMonthValue }}">
            </label>
            <button class="button button-outline" type="submit">Actualizar mes</button>
        </form>
        <div class="timeline monthly-activity-list">
            @forelse ($monthlyActivities as $activity)
                <div class="timeline-item">
                    <strong>{{ ucfirst($activity->event_type) }} {{ $activity->ticket_code }}</strong>
                    <span>{{ $activity->occurred_at->format('d/m/Y H:i') }}</span>
                    <p>{{ $activity->notes }}</p>
                </div>
            @empty
                <div class="empty-inline">Sin actividad en este mes.</div>
            @endforelse
        </div>
    </div>
</div>
@endif
