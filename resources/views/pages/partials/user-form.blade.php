<form method="POST" action="{{ $action }}" class="stack-md" data-loading-form>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif
    <div class="field-grid">
        <label class="field"><span>Nombre</span><input type="text" name="name" value="{{ old('name', $userEdit?->name) }}" required></label>
        <label class="field"><span>Usuario</span><input type="text" name="username" value="{{ old('username', $userEdit?->username) }}" required></label>
        <label class="field"><span>Email</span><input type="email" name="email" value="{{ old('email', $userEdit?->email) }}" required></label>
        <label class="field">
            <span>Rol</span>
            <select name="role">
                <option value="operario" @selected(old('role', $userEdit?->role) === 'operario')>Operario</option>
                <option value="admin" @selected(old('role', $userEdit?->role) === 'admin')>Admin</option>
            </select>
        </label>
        <label class="field"><span>Turno</span><input type="text" name="shift_name" value="{{ old('shift_name', $userEdit?->shift_name) }}"></label>
        <label class="field"><span>{{ $method === 'POST' ? 'Contrasena' : 'Nueva contrasena' }}</span><input type="password" name="password" {{ $method === 'POST' ? 'required' : '' }}></label>
        <label class="field check-card"><span>Activo</span><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $userEdit?->is_active ?? true))></label>
    </div>
    <button class="button button-primary" type="submit">{{ $method === 'POST' ? 'Crear usuario' : 'Guardar usuario' }}</button>
</form>
