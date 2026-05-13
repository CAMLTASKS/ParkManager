<form method="POST" action="{{ $action }}" class="stack-md" data-loading-form>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="field-grid">

        <label class="field">
            <span>Nombre</span>
            <input type="text" name="name" value="{{ old('name', $tariff?->name) }}" required>
        </label>

        <label class="field">
            <span>Tipo vehiculo</span>
            <select name="vehicle_type" required>
                <option value="moto" @selected(old('vehicle_type', $tariff?->vehicle_type) === 'moto')>Moto</option>
                <option value="auto" @selected(old('vehicle_type', $tariff?->vehicle_type) === 'auto')>Automovil</option>
                <option value="bicicleta" @selected(old('vehicle_type', $tariff?->vehicle_type) === 'bicicleta')>Bicicleta</option>
            </select>
        </label>

        <!-- 🔥 NUEVO: tipo de tarifa -->
        <label class="field">
            <span>Tipo de tarifa</span>
            <select name="type" required data-tariff-type-select>
                <option value="normal" @selected(old('type', $tariff?->tariff_type) === 'normal')>Minuto</option>
                <option value="plena" @selected(old('type', $tariff?->tariff_type) === 'plena')>Tarifa plena</option>
                <option value="convenio" @selected(old('type', $tariff?->tariff_type) === 'convenio')>Convenio</option>
            </select>
        </label>

        <input type="hidden" name="charge_unit" value="minute">
        <input type="hidden" name="charge_interval" value="1">

        <label class="field" data-tariff-field="normal convenio">
            <span data-rate-label>Valor por minuto</span>
            <input type="number" min="0" name="unit_rate"
                   value="{{ old('unit_rate', $tariff?->unit_rate ?? 0) }}">
        </label>

        <!-- 🔵 SOLO PLENA -->
        <label class="field" data-tariff-field="plena">
            <span>Despues de cuantos minutos aplica plena</span>
            <input type="number" min="0" name="threshold_minutes"
                   value="{{ old('threshold_minutes', $tariff?->threshold_minutes) }}"
                   placeholder="Ej: 240">
        </label>

        <!-- 🔵 PLENA Y CONVENIO -->
        <label class="field" data-tariff-field="plena">
            <span>Valor tarifa plena</span>
            <input type="number" min="0" name="full_rate"
                   value="{{ old('full_rate', $tariff?->full_rate) }}"
                   placeholder="Ej: 8000">
        </label>

        <label class="field" data-tariff-field="convenio">
            <span>Tiempo que cubre un convenio (minutos)</span>
            <input type="number" min="1" name="max_minutes"
                   value="{{ old('max_minutes', $tariff?->max_minutes) }}"
                   placeholder="Ej: 720 (12 horas)">
        </label>

        <input type="hidden" name="daily_cap" value="0">

        <label class="field">
            <span>Gracia ingreso</span>
            <input type="number" min="0" name="grace_entry_minutes"
                   value="{{ old('grace_entry_minutes', $tariff?->grace_entry_minutes ?? 0) }}" required>
        </label>

        <label class="field">
            <span>Gracia salida</span>
            <input type="number" min="0" name="grace_exit_minutes"
                   value="{{ old('grace_exit_minutes', $tariff?->grace_exit_minutes ?? 0) }}" required>
        </label>

        <label class="field">
            <span>Ticket perdido</span>
            <input type="number" min="0" name="lost_ticket_fee"
                   value="{{ old('lost_ticket_fee', $tariff?->lost_ticket_fee ?? 0) }}" required>
        </label>

        <label class="field">
            <span>Impuesto %</span>
            <input type="number" step="0.01" min="0" name="tax_percentage"
                   value="{{ old('tax_percentage', $tariff?->tax_percentage ?? 0) }}" required>
        </label>

        <label class="field check-card">
            <span>Activa</span>
            <input type="checkbox" name="active" value="1"
                   @checked(old('active', $tariff?->active ?? true))>
        </label>

    </div>

    <button class="button button-primary" type="submit">
        {{ $method === 'POST' ? 'Crear tarifa' : 'Guardar tarifa' }}
    </button>
</form>

<script>
    document.querySelectorAll('[data-tariff-type-select]').forEach((select) => {
        const form = select.closest('form');
        const updateTariffFields = () => {
            const type = select.value;

            form.querySelectorAll('[data-tariff-field]').forEach((field) => {
                const isVisible = field.dataset.tariffField.split(' ').includes(type);
                field.style.display = isVisible ? '' : 'none';
                field.querySelectorAll('input, select, textarea').forEach((input) => {
                    input.required = isVisible && ['unit_rate', 'threshold_minutes', 'full_rate', 'max_minutes'].includes(input.name);
                });
            });

            const rateLabel = form.querySelector('[data-rate-label]');
            if (rateLabel) {
                rateLabel.textContent = type === 'convenio' ? 'Valor del convenio' : 'Valor por minuto';
            }
        };

        select.addEventListener('change', updateTariffFields);
        updateTariffFields();
    });
</script>
