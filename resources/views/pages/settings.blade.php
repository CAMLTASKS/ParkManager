@extends('layouts.app')

@section('header_actions')
<button class="button button-primary" type="button" data-open-modal="createTariffModal">Agregar tarifa</button>
@endsection

@section('content')
<section class="card locker-settings-card">
    <div class="card-heading">
        <div>
            <h3>Servicio de locker</h3>
            <p>Configura el valor fijo que se suma al total cuando una entrada usa locker.</p>
        </div>
        <span class="pill info">Valor fijo</span>
    </div>
    <form method="POST" action="{{ route('settings.locker.update') }}" class="field-grid" data-loading-form>
        @csrf
        <label class="field">
            <span>Tarifa fija locker</span>
            <input type="number" min="0" name="locker_fee" value="{{ old('locker_fee', $currentSite?->locker_fee ?? 0) }}" required>
        </label>
        <div class="locker-settings-preview">
            <span>Valor actual</span>
            <strong>{{ '$'.number_format($currentSite?->locker_fee ?? 0, 0, ',', '.') }}</strong>
        </div>
        <button class="button button-primary" type="submit">Guardar locker</button>
    </form>
</section>

<section class="card">
    <div class="card-heading">
        <div>
            <h3>Listado de tarifas</h3>
            <p>Administra las tres reglas de cobro: minuto, tarifa plena y convenio.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table data-table-clickable">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Vehiculo</th>
                    <th>Regla</th>
                    <th>Valor</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tariffs as $tariff)
                <tr class="{{ $selectedTariff && $selectedTariff->id === $tariff->id ? 'table-row-active' : '' }}">

                    <td>{{ $tariff->name }}</td>

                    <td>{{ strtoupper($tariff->vehicle_type) }}</td>

                    <td>
                        @if($tariff->tariff_type === 'plena')
                            Despues de {{ $tariff->threshold_minutes }} min
                        @elseif($tariff->tariff_type === 'convenio')
                            Cada {{ $tariff->max_minutes }} min
                        @elseif($tariff->tariff_type === 'mensualidad')
                            Mensual
                        @else
                            Por minuto
                        @endif
                    </td>

                    <td>
                        @if($tariff->tariff_type === 'plena')
                            {{ '$'.number_format($tariff->full_rate ?? 0, 0, ',', '.') }}
                        @else
                            {{ '$'.number_format($tariff->unit_rate ?? 0, 0, ',', '.') }}
                        @endif
                    </td>

                    <!-- 🔥 NUEVO -->
                    <td>
                        <span class="pill pill-type {{ $tariff->tariff_type }}">
                            {{ $tariff->tariff_type === 'normal' ? 'Minuto' : ucfirst($tariff->tariff_type) }}
                        </span>

                        @if($tariff->tariff_type === 'plena')
                        <small>Valor unico: {{ '$'.number_format($tariff->full_rate ?? 0, 0, ',', '.') }}</small>
                        @endif

                        @if($tariff->tariff_type === 'convenio')
                        <small>{{ $tariff->max_minutes }} min por convenio</small>
                        @endif

                        @if($tariff->tariff_type === 'mensualidad')
                        <small>No aparece en entrada</small>
                        @endif
                    </td>

                    <td>
                        <span class="pill {{ $tariff->active ? 'success' : 'danger' }}">
                            {{ $tariff->active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </td>

                    <!-- 🔥 ACCIONES -->
                    <td class="table-actions">
                        <a href="{{ route('settings', ['tariff' => $tariff->id]) }}"
                            class="button button-outline table-action-button">
                            Editar
                        </a>

                        <!-- 🔥 ELIMINAR -->
                        <form method="POST"
                            action="{{ route('settings.tariff.delete', $tariff) }}"
                            onsubmit="return confirm('¿Eliminar esta tarifa?')"
                            style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button class="button button-danger table-action-button">
                                Eliminar
                            </button>
                        </form>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @include('partials.simple-pagination', ['paginator' => $tariffs, 'label' => 'Tarifas'])
</section>

<section class="card">
    <div class="card-heading">
        <div>
            <h3>Ultimos cambios</h3>
            <p>Auditoria de configuraciones recientes.</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Modulo</th>
                    <th>Accion</th>
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentAudits as $audit)
                <tr>
                    <td>{{ optional($audit->logged_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ strtoupper($audit->module) }}</td>
                    <td>{{ $audit->action }}</td>
                    <td>{{ $audit->detail }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<div class="modal-backdrop" data-app-modal-id="createTariffModal">
    <div class="modal-card modal-card-lg">
        <button class="modal-close" type="button" data-close-app-modal>&times;</button>
        <span class="modal-kicker">TARIFAS</span>
        <h3>Agregar tarifa</h3>
        <p>Configura cobro por minuto, plena con umbral, convenio o mensualidad.</p>
        @include('pages.partials.tariff-form', [
        'action' => route('settings.tariff.store'),
        'method' => 'POST',
        'tariff' => null,
        'chargeUnitOptions' => $chargeUnitOptions,
        ])
    </div>
</div>

@if ($selectedTariff && request('tariff'))
<div class="modal-backdrop is-visible" data-app-modal-id="editTariffModal">
    <div class="modal-card modal-card-lg">
        <button class="modal-close" type="button" data-close-app-modal onclick="window.location='{{ route('settings') }}'">&times;</button>
        <span class="modal-kicker">EDITAR TARIFA</span>
        <h3>{{ $selectedTariff->name }}</h3>
        <p>
            {{ match($selectedTariff->tariff_type) {
        'normal' => 'Cobra un valor por cada minuto',
        'plena' => 'Tarifa que se activa después de un umbral',
        'convenio' => 'Cobra un valor fijo por cada bloque de tiempo',
        'mensualidad' => 'Valor fijo mensual para clientes registrados',
        default => ''
    } }}
        </p>
        @include('pages.partials.tariff-form', [
        'action' => route('settings.tariff.update', $selectedTariff),
        'method' => 'PUT',
        'tariff' => $selectedTariff,
        'chargeUnitOptions' => $chargeUnitOptions,
        ])
    </div>
</div>
@endif
@endsection
