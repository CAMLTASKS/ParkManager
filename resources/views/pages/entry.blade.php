@extends('layouts.app')

@section('header_actions')
<a href="{{ route('manage') }}" class="button button-muted">Volver a gestion</a>
@endsection

@section('content')
<section class="entry-layout entry-create-focus">
    <article class="card stack-md entry-hero">
        <div class="card-heading">
            <div>
                <h3>Registrar entrada</h3>
                <p>Captura rapida con seleccion visual para que el operario trabaje mas facil.</p>
            </div>
        </div>

        <div class="ticket-badge">
            <span class="eyebrow">Siguiente ticket</span>
            <strong>{{ $nextTicketCode }}</strong>
        </div>
        <div class="entry-inline-tip">
            <span>Enter</span>
            <p>Escribe la placa o codigo del ticket y presiona Enter para abrir salida si ya existe.</p>
        </div>

        @if ($activeTicket)
        <div class="entry-alert">
            <strong>La placa ya tiene un ticket activo o pendiente.</strong>
            <span>Para evitar duplicados, continua desde la salida de ese ticket.</span>
            <a href="{{ route('transaction.show', $activeTicket) }}" class="button button-primary">Abrir ticket activo</a>
        </div>
        @endif
    </article>

    <article class="card stack-md entry-form-card">
        <form method="POST" action="{{ route('manage.entry.store') }}" class="stack-md" data-loading-form>
            @csrf
            @php
                $selectedVehicleType = old('vehicle_type', $prefillTicket?->vehicle_type ?? $defaultVehicleType);
                $defaultTariff = $tariffs->firstWhere('vehicle_type', $selectedVehicleType) ?? $tariffs->first();
                $selectedTariffId = (string) old('tariff_profile_id', $prefillTicket?->tariff_profile_id ?? $defaultTariff?->id);
            @endphp
            <div class="entry-form-header">
                <div>
                    <span class="eyebrow">Nuevo ingreso</span>
                    <h3>Captura del vehiculo</h3>
                </div>
                <div class="entry-form-clock">
                    <span>@include('partials.svg.clock')</span>
                    <strong>{{ now()->format('h:i A') }}</strong>
                    <small>{{ now()->format('d/m/Y') }}</small>
                </div>
            </div>

            <div class="field-grid">
                <label class="field field-span-2 field-spotlight">
                    <span>Placa o codigo de ticket</span>
                    <span class="entry-plate-input-wrap">
                        <input type="text" name="plate" value="{{ old('plate', $prefillTicket?->plate ?? $plateLookup) }}" placeholder="ABC123 o PK-260508-0001" data-entry-prefill-route="{{ route('entry') }}" autocomplete="off" autofocus required aria-describedby="entryPlateEnterTip">
                        <span class="entry-enter-tooltip" id="entryPlateEnterTip" role="tooltip">Enter busca placa o ticket</span>
                    </span>
                </label>

                <div class="entry-workspace field-span-2">
                    <div class="entry-left-column">
                        <div class="select-card-group">
                            <span class="select-card-title">Tipo de vehiculo</span>
                            <div class="visual-choice-grid">
                                <label class="visual-choice-card {{ $selectedVehicleType === 'moto' ? 'active' : '' }}">
                                    <input type="radio" name="vehicle_type" value="moto" @checked($selectedVehicleType === 'moto')>
                                    <span class="action-icon">@include('partials.svg.car-enter')</span>
                                    <strong>Moto</strong>
                                    <small>Ingreso agil predeterminado</small>
                                </label>
                                <!-- <label class="visual-choice-card {{ $selectedVehicleType === 'auto' ? 'active' : '' }}">
                                    <input type="radio" name="vehicle_type" value="auto" @checked($selectedVehicleType === 'auto')>
                                    <span class="action-icon">@include('partials.svg.car')</span>
                                    <strong>Automovil</strong>
                                    <small>Parqueo general</small>
                                </label> -->
                                <label class="visual-choice-card {{ $selectedVehicleType === 'bicicleta' ? 'active' : '' }}">
                                    <input type="radio" name="vehicle_type" value="bicicleta" @checked($selectedVehicleType === 'bicicleta')>
                                    <span class="action-icon">@include('partials.svg.bike')</span>
                                    <strong>Bicicleta</strong>
                                    <small>Ingreso liviano</small>
                                </label>
                            </div>
                        </div>

                        <div class="tariff-picker" data-tariff-picker>
                            <div class="tariff-picker-head">
                                <div>
                                    <span class="select-card-title">Tarifa</span>
                                    <p>Elige la tarifa activa para este ingreso.</p>
                                </div>
                                <span class="tariff-picker-hint">Se filtra por tipo</span>
                            </div>
                            <select name="tariff_profile_id" class="tariff-native-select" data-tariff-select>
                                @foreach ($tariffs as $tariff)
                                <option value="{{ $tariff->id }}" data-vehicle-type="{{ $tariff->vehicle_type }}" @selected($selectedTariffId === (string) $tariff->id)>
                                    {{ $tariff->name }} - {{ ucfirst($tariff->vehicle_type) }}
                                </option>
                                @endforeach
                            </select>
                            <div class="tariff-choice-grid">
                                @foreach ($tariffs as $tariff)
                                    @php
                                        $rate = $tariff->tariff_type === 'plena'
                                            ? ($tariff->full_rate ?: $tariff->unit_rate)
                                            : $tariff->unit_rate;
                                        $mode = match($tariff->tariff_type) {
                                            'plena' => 'Plena',
                                            'convenio' => 'Convenio',
                                            default => 'Minuto',
                                        };
                                        $detail = match($tariff->tariff_type) {
                                            'plena' => 'Desde ' . ($tariff->threshold_minutes ?? 0) . ' min',
                                            'convenio' => 'Cada ' . ($tariff->max_minutes ?? 0) . ' min',
                                            default => 'Cobro por minuto',
                                        };
                                    @endphp
                                    <label class="tariff-choice-card {{ $selectedTariffId === (string) $tariff->id ? 'active' : '' }}" data-tariff-card data-vehicle-type="{{ $tariff->vehicle_type }}">
                                        <input type="radio" name="tariff_profile_choice" value="{{ $tariff->id }}" @checked($selectedTariffId === (string) $tariff->id)>
                                        <span class="tariff-card-top">
                                            <span class="tariff-type-badge">{{ ucfirst($tariff->vehicle_type) }}</span>
                                            <span class="tariff-mode-badge">{{ $mode }}</span>
                                        </span>
                                        <strong>{{ $tariff->name }}</strong>
                                        <span class="tariff-price">{{ '$'.number_format($rate, 0, ',', '.') }}</span>
                                        <small>{{ $detail }}</small>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="entry-right-column">
                        <div class="locker-entry-card">
                            <label class="field check-card">
                                <span>Servicio de locker</span>
                                <input type="checkbox" name="uses_locker" value="1" @checked(old('uses_locker', $prefillTicket?->uses_locker)) data-locker-toggle>
                            </label>
                            <label class="field" data-locker-number-field>
                                <span>Numero de locker</span>
                                <input type="text" name="locker_number" value="{{ old('locker_number', $prefillTicket?->locker_number) }}" placeholder="Ej: L-12">
                            </label>
                            <div class="locker-fee-preview">
                                <span>Tarifa fija locker</span>
                                <strong>{{ '$'.number_format($lockerFee ?? 0, 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        <div class="entry-side-inputs">
                            <label class="field">
                                <span>Ubicacion</span>
                                <input type="number" min="1" name="location_number" value="{{ old('location_number', $prefillTicket?->location_number) }}" required>
                            </label>

                            <label class="field">
                                <span>Nombre del cliente</span>
                                <input type="text" name="customer_name" value="{{ old('customer_name', $prefillTicket?->customer_name) }}">
                            </label>

                            <label class="field">
                                <span>Telefono</span>
                                <input type="text" name="customer_phone" value="{{ old('customer_phone', $prefillTicket?->customer_phone) }}">
                            </label>

                            <label class="field field-span-2">
                                <span>Observaciones</span>
                                <textarea name="notes" rows="4" placeholder="Comentario operativo o estado del vehiculo.">{{ old('notes') }}</textarea>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="entry-actions-bar">
                <a href="{{ route('manage') }}" class="button button-muted">Cancelar</a>
                <button class="button button-outline" type="submit" name="print_mode" value="0">Guardar sin imprimir</button>
                <button class="button button-primary" type="submit">Guardar entrada</button>
                <button class="button button-whatsapp" type="submit" name="send_whatsapp" value="1">@include('partials.svg.whatsapp') Guardar y enviar por WhatsApp</button>
            </div>
        </form>
    </article>
</section>
@endsection
