@extends('layouts.app')

@section('header_actions')
<button class="button button-outline" type="button" data-open-modal="appParametersModal">Parametros</button>
<form method="POST" action="{{ route('settings.backup') }}" data-loading-form>
    @csrf
    <button class="button button-outline" type="submit">Backup BD</button>
</form>
<button class="button button-primary" type="button" data-open-modal="createTariffModal">Agregar tarifa</button>
@endsection

@section('content')
<section class="settings-highlight-grid">
    <article class="settings-highlight">
        <span>Impresion</span>
        <strong>{{ trim($appSettings['printer_name'] ?? '') !== '' ? 'Directa' : 'Navegador' }}</strong>
        <small>{{ trim($appSettings['printer_name'] ?? '') !== '' ? $appSettings['printer_name'] : 'Sin impresora configurada' }}</small>
    </article>
    <article class="settings-highlight">
        <span>Recibo</span>
        <strong>{{ $appSettings['receipt_width_mm'] ?? 80 }}mm</strong>
        <small>{{ $appSettings['receipt_copies'] ?? 1 }} copia(s)</small>
    </article>
    <article class="settings-highlight">
        <span>Backup</span>
        <strong>{{ $appSettings['backup_retention_days'] ?? 30 }} dias</strong>
        <small>Retencion sugerida</small>
    </article>
</section>

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
            <p>Administra las reglas de cobro: minuto, tarifa plena, convenio y mensualidad.</p>
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
                    <td class="table-actions">
                        <a href="{{ route('settings', ['tariff' => $tariff->id]) }}" class="button button-outline table-action-button">
                            Editar
                        </a>
                        <form method="POST" action="{{ route('settings.tariff.delete', $tariff) }}" onsubmit="return confirm('Eliminar esta tarifa?')" style="display:inline">
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

<div class="modal-backdrop" data-app-modal-id="appParametersModal">
    <div class="modal-card modal-card-lg">
        <button class="modal-close" type="button" data-close-app-modal>&times;</button>
        <span class="modal-kicker">PARAMETROS</span>
        <h3>Impresion y datos generales</h3>
        <p>Si configuras nombre de impresora, el sistema intentara imprimir directo sin abrir la ventana del navegador. Si lo dejas vacio, se usa la ventana de impresion normal.</p>

        <form method="POST" action="{{ route('settings.parameters.update') }}" class="stack-md" data-loading-form>
            @csrf
            <div class="field-grid">
                <label class="field field-span-2">
                    <span>Nombre de impresora Windows</span>
                    <input type="text" name="printer_name" value="{{ old('printer_name', $appSettings['printer_name'] ?? '') }}" placeholder="Ej: POS-80C, EPSON TM-T20, Caja Principal">
                </label>
                <label class="field">
                    <span>Ancho recibo</span>
                    <select name="receipt_width_mm" required>
                        <option value="80" @selected((string) old('receipt_width_mm', $appSettings['receipt_width_mm'] ?? '80') === '80')>80 mm</option>
                        <option value="58" @selected((string) old('receipt_width_mm', $appSettings['receipt_width_mm'] ?? '80') === '58')>58 mm</option>
                    </select>
                </label>
                <label class="field">
                    <span>Copias impresion directa</span>
                    <input type="number" min="1" max="3" name="receipt_copies" value="{{ old('receipt_copies', $appSettings['receipt_copies'] ?? 1) }}" required>
                </label>
                <label class="field">
                    <span>Volver despues de imprimir</span>
                    <input type="number" min="1" max="30" name="auto_return_seconds" value="{{ old('auto_return_seconds', $appSettings['auto_return_seconds'] ?? 3) }}" required>
                </label>
                <label class="field">
                    <span>Retencion backup dias</span>
                    <input type="number" min="1" max="365" name="backup_retention_days" value="{{ old('backup_retention_days', $appSettings['backup_retention_days'] ?? 30) }}" required>
                </label>
                <label class="field field-span-2">
                    <span>Nombre negocio</span>
                    <input type="text" name="business_name" value="{{ old('business_name', $appSettings['business_name'] ?? 'PARQUEADERO DONDE RICHARD') }}" required>
                </label>
                <label class="field">
                    <span>Regimen</span>
                    <input type="text" name="business_regime" value="{{ old('business_regime', $appSettings['business_regime'] ?? 'Regimen simplificado') }}" required>
                </label>
                <label class="field">
                    <span>NIT</span>
                    <input type="text" name="business_nit" value="{{ old('business_nit', $appSettings['business_nit'] ?? 'NIT 7662483-1') }}" required>
                </label>
                <label class="field">
                    <span>Direccion</span>
                    <input type="text" name="business_address" value="{{ old('business_address', $appSettings['business_address'] ?? 'Cra 71a #8 - 43 sur') }}" required>
                </label>
                <label class="field">
                    <span>Telefono</span>
                    <input type="text" name="business_phone" value="{{ old('business_phone', $appSettings['business_phone'] ?? '3237902525') }}" required>
                </label>
            </div>
            <button class="button button-primary" type="submit">Guardar parametros</button>
        </form>
    </div>
</div>

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
                'plena' => 'Tarifa que se activa despues de un umbral',
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
