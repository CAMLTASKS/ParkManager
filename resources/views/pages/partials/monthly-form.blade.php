<form method="POST" action="{{ $action }}" class="stack-md" data-loading-form>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="field-grid">
        <label class="field">
            <span>Nombre</span>
            <input type="text" name="customer_name" value="{{ old('customer_name', $membership?->customer_name) }}" required>
        </label>
        <label class="field">
            <span>Placa</span>
            <input type="text" name="plate" value="{{ old('plate', $membership?->plate) }}" required>
        </label>
        <label class="field">
            <span>Vehiculo</span>
            <select name="vehicle_type" required>
                <option value="moto" @selected(old('vehicle_type', $membership?->vehicle_type) === 'moto')>Moto</option>
                <option value="auto" @selected(old('vehicle_type', $membership?->vehicle_type) === 'auto')>Automovil</option>
                <option value="bicicleta" @selected(old('vehicle_type', $membership?->vehicle_type) === 'bicicleta')>Bicicleta</option>
            </select>
        </label>
        <label class="field">
            <span>Marca de la moto</span>
            <input type="text" name="vehicle_brand" value="{{ old('vehicle_brand', $membership?->vehicle_brand) }}">
        </label>
        <label class="field">
            <span>Telefono</span>
            <input type="text" name="phone" value="{{ old('phone', $membership?->phone) }}">
        </label>
        <label class="field">
            <span>Tarifa mensualidad</span>
            <select name="tariff_profile_id" required>
                @forelse ($monthlyTariffs as $tariff)
                    <option value="{{ $tariff->id }}" @selected((string) old('tariff_profile_id', $membership?->tariff_profile_id) === (string) $tariff->id)>
                        {{ $tariff->name }} - {{ strtoupper($tariff->vehicle_type) }} - {{ '$'.number_format($tariff->unit_rate, 0, ',', '.') }}
                    </option>
                @empty
                    <option value="">Primero crea una tarifa tipo mensualidad</option>
                @endforelse
            </select>
        </label>
        <label class="field">
            <span>Fecha de inicio</span>
            <input type="date" name="starts_at" value="{{ old('starts_at', optional($membership?->starts_at)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
        </label>
        <label class="field">
            <span>Fecha de pago</span>
            <input type="date" name="next_payment_date" value="{{ old('next_payment_date', optional($membership?->next_payment_date)->format('Y-m-d') ?? now()->addMonth()->format('Y-m-d')) }}" required>
        </label>
        @if ($membership)
            <label class="field">
                <span>Estado</span>
                <select name="status">
                    <option value="active" @selected(old('status', $membership->status) === 'active')>Activa</option>
                    <option value="cancelled" @selected(old('status', $membership->status) === 'cancelled')>Cancelada</option>
                </select>
            </label>
        @endif
        <label class="field field-span-2">
            <span>Observaciones</span>
            <textarea name="notes" rows="3">{{ old('notes', $membership?->notes) }}</textarea>
        </label>
    </div>

    <button class="button button-primary" type="submit" @disabled($monthlyTariffs->isEmpty())>
        {{ $method === 'POST' ? 'Crear mensualidad' : 'Guardar mensualidad' }}
    </button>
</form>
