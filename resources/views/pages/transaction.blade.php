@extends('layouts.app')

@section('header_actions')
    <a href="{{ route('entry') }}" class="button button-muted">Volver a entrada</a>
    <a href="{{ route('tickets.print', ['ticket' => $ticket, 'type' => 'ingreso', 'return_to' => 'transaction']) }}" class="button button-outline">Imprimir ingreso</a>
    <a href="{{ $whatsappReceiptUrls['ingreso'] }}" target="_blank" rel="noopener" class="button button-whatsapp">WhatsApp ingreso</a>
    @if ($ticket->exit_time || $ticket->payment)
        <a href="{{ route('tickets.print', ['ticket' => $ticket, 'type' => 'salida', 'return_to' => 'transaction']) }}" class="button button-outline">Imprimir salida</a>
        <a href="{{ $whatsappReceiptUrls['salida'] }}" target="_blank" rel="noopener" class="button button-whatsapp">WhatsApp salida</a>
    @endif
    <span class="button-chip">{{ $ticket->ticket_code }}</span>
@endsection

@section('content')
<section class="transaction-overview-grid">
    <article class="card stack-md transaction-primary-card">
        <div class="card-heading">
            <div>
                <h3>Salida del ticket</h3>
                <p>Resumen claro del vehiculo, tiempo y valor a cobrar.</p>
            </div>
            <span class="pill {{ $ticket->status === 'paid' ? 'success' : ($ticket->status === 'pending_payment' ? 'warning' : 'info') }}">
                {{ $ticket->status === 'paid' ? 'Pagado' : ($ticket->status === 'pending_payment' ? 'Pendiente' : 'Activo') }}
            </span>
        </div>

        <div class="transaction-ticket-hero">
            <span class="transaction-ticket-icon">@include('partials.svg.car')</span>
            <div>
                <span class="eyebrow">Vehiculo encontrado</span>
                <strong>{{ $ticket->plate }}</strong>
                <small>{{ $ticket->tariffProfile?->name ?? 'Sin tarifa' }} - Ubicacion {{ $ticket->location_number ?: '-' }}</small>
            </div>
            <div class="transaction-ticket-time">
                <span class="eyebrow">Tiempo transcurrido</span>
                <strong>{{ $summary['minutes'] }} min</strong>
            </div>
        </div>

        <div class="vehicle-location-banner">
            <span>Ubicacion del vehiculo</span>
            <strong>{{ $ticket->location_number ?: 'Sin ubicacion' }}</strong>
        </div>

        <div class="transaction-alert-strip">
            <span class="transaction-alert-pill">@include('partials.svg.clock') {{ $summary['minutes'] }} min totales</span>
            <span class="transaction-alert-pill">@include('partials.svg.wallet') {{ '$'.number_format($summary['total'], 0, ',', '.') }}</span>
            <span class="transaction-alert-pill">@include('partials.svg.chart') {{ $ticket->tariffProfile?->name ?? 'Sin tarifa' }}</span>
        </div>

        <div class="transaction-data-grid">
            <div class="info-list compact">
                <div><span>Ingreso</span><strong>{{ optional($ticket->entry_time)->format('d/m/Y h:i A') }}</strong></div>
                <div><span>Salida</span><strong>{{ optional($ticket->exit_time)->format('d/m/Y h:i A') ?: 'Pendiente' }}</strong></div>
                <div><span>Codigo</span><strong>{{ $ticket->barcode }}</strong></div>
                <div><span>Sincronizacion portal</span><strong>@include('partials.sync-badge', ['ticket' => $ticket])</strong></div>
                <div><span>Locker</span><strong>{{ $ticket->uses_locker ? 'Si - '.$ticket->locker_number : 'No' }}</strong></div>
                <div><span>Cliente</span><strong>{{ $ticket->customer_name ?: 'No registrado' }}</strong></div>
            </div>
            <div class="liquid-breakdown liquid-breakdown-emphasis">
                <div><span>Tarifa aplicada</span><strong>{{ $summary['applied_tariff'] }}</strong></div>
                <div><span>Regla de cobro</span><strong>{{ $summary['pricing_label'] }}</strong></div>
                <div><span>Minutos cobrables</span><strong>{{ $summary['billable_minutes'] }}</strong></div>
                <div><span>Parqueo</span><strong>{{ '$'.number_format($summary['parking_subtotal'] ?? $summary['subtotal'], 0, ',', '.') }}</strong></div>
                <div><span>Locker</span><strong>{{ ($summary['uses_locker'] ?? false) ? '$'.number_format($summary['locker_fee'] ?? 0, 0, ',', '.') : 'No' }}</strong></div>
                <div><span>Subtotal</span><strong>{{ '$'.number_format($summary['subtotal'], 0, ',', '.') }}</strong></div>
                <div><span>Recargo</span><strong>{{ '$'.number_format($summary['surcharge'], 0, ',', '.') }}</strong></div>
                <div><span>Impuesto</span><strong>{{ '$'.number_format($summary['tax'], 0, ',', '.') }}</strong></div>
                <div><span>Total</span><strong>{{ '$'.number_format($summary['total'], 0, ',', '.') }}</strong></div>
            </div>
        </div>
    </article>

    <article class="card stack-md transaction-payment-card">
        <div class="card-heading">
            <div>
                <h3>Liquidar pago</h3>
                <p>Selecciona el metodo y confirma la salida.</p>
            </div>
        </div>

        @if (in_array($ticket->status, ['active', 'pending_payment'], true))
            <form method="POST" action="{{ route('manage.exit.close') }}" class="stack-md" data-loading-form>
                @csrf
                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">

                <div class="payment-choice-grid payment-choice-grid-enhanced">
                    <label class="choice-tile payment-tile active">
                        <input type="radio" name="payment_method" value="efectivo" checked>
                        <span class="payment-icon">@include('partials.svg.wallet')</span>
                        <strong>Efectivo</strong>
                        <small>Cobro en caja</small>
                    </label>
                    <label class="choice-tile payment-tile">
                        <input type="radio" name="payment_method" value="nequi">
                        <span class="payment-icon payment-icon-nequi">@include('partials.svg.cashier')</span>
                        <strong>Nequi</strong>
                        <small>Transferencia</small>
                    </label>
                    <label class="choice-tile payment-tile">
                        <input type="radio" name="payment_method" value="pending">
                        <span class="payment-icon payment-icon-pending">@include('partials.svg.clock')</span>
                        <strong>Pendiente</strong>
                        <small>Cobrar despues</small>
                    </label>
                </div>

                <label class="field">
                    <span>Valor recibido</span>
                    <input type="number" min="0" name="received_amount" value="{{ $summary['total'] }}">
                </label>

                <label class="field transaction-check">
                    <span>Marcar ticket perdido</span>
                    <input type="checkbox" name="mark_lost_ticket" value="1" @checked($ticket->is_lost_ticket)>
                </label>

                <label class="field">
                    <span>Nota de salida</span>
                    <textarea name="notes" rows="3" placeholder="Observacion del cierre, recaudo o novedad."></textarea>
                </label>

                <button class="button button-primary button-block transaction-submit" type="submit">
                    {{ $ticket->status === 'pending_payment' ? 'Actualizar salida' : 'Confirmar salida' }}
                </button>
                <button class="button button-whatsapp button-block transaction-submit" type="submit" name="send_whatsapp" value="1">
                    @include('partials.svg.whatsapp') Guardar y enviar por WhatsApp
                </button>
            </form>
        @else
            <div class="empty-inline">Este ticket ya esta cerrado y no requiere nuevas acciones.</div>
        @endif

        <div class="info-list compact">
            <div><span>Metodo pago</span><strong>{{ strtoupper($ticket->payment?->method ?? 'N/A') }}</strong></div>
            <div><span>Estado pago</span><strong>{{ $ticket->payment?->status ?? 'Sin pago' }}</strong></div>
            <div><span>Operario entrada</span><strong>{{ $ticket->creator?->username ?? 'sistema' }}</strong></div>
            <div><span>Operario cierre</span><strong>{{ $ticket->closer?->username ?? 'pendiente' }}</strong></div>
        </div>
    </article>
</section>

@if ($ticket->payment?->status === 'pending')
<section class="card">
    <div class="card-heading">
        <div>
            <h3>Regularizar pago pendiente</h3>
            <p>Confirma el recaudo del ticket pendiente.</p>
        </div>
    </div>
    <form method="POST" action="{{ route('manage.pending.settle', $ticket->payment) }}" class="field-grid" data-loading-form>
        @csrf
        <label class="field">
            <span>Metodo</span>
            <select name="payment_method">
                <option value="efectivo">Efectivo</option>
                <option value="nequi">Nequi</option>
            </select>
        </label>
        <label class="field">
            <span>Valor recibido</span>
            <input type="number" min="0" name="received_amount" value="{{ $ticket->payment->total }}">
        </label>
        <button class="button button-outline" type="submit">Regularizar pago pendiente</button>
    </form>
</section>
@endif

<section class="card">
    <div class="card-heading">
        <div>
            <h3>Auditoria asociada</h3>
            <p>Eventos y cambios relacionados con este ticket.</p>
        </div>
    </div>
    <div class="timeline">
        @forelse ($ticket->audits as $audit)
            <div class="timeline-item">
                <strong>{{ $audit->action }}</strong>
                <span>{{ optional($audit->logged_at)->format('d/m/Y H:i') }} - {{ $audit->user?->username ?? 'sistema' }}</span>
                <p>{{ $audit->detail }}</p>
            </div>
        @empty
            <div class="empty-inline">No hay auditoria registrada para este ticket.</div>
        @endforelse
    </div>
</section>
@endsection
