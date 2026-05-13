@extends('layouts.app')

@section('header_actions')
    <button class="button button-primary" type="button" data-open-modal="createUserModal">Nuevo usuario</button>
@endsection

@section('content')
<section class="card">
    <div class="card-heading">
        <div>
            <h3>Usuarios del sistema</h3>
            <p>Listado paginado de operarios y administradores con edicion modal.</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table data-table-clickable">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Turno</th>
                    <th>Estado</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="{{ $selectedUser && $selectedUser->id === $user->id ? 'table-row-active' : '' }}">
                        <td>{{ $user->name }}</td>
                        <td>{{ '@'.$user->username }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>{{ $user->shift_name ?: 'Sin turno' }}</td>
                        <td><span class="pill {{ $user->is_active ? 'success' : 'danger' }}">{{ $user->is_active ? 'Activo' : 'Bloqueado' }}</span></td>
                        <td><a href="{{ route('audit', ['user' => $user->id]) }}" class="button button-outline table-action-button">Editar</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @include('partials.simple-pagination', ['paginator' => $users, 'label' => 'Usuarios'])
</section>

<section class="card">
    <div class="card-heading">
        <div>
            <h3>Bitacora completa</h3>
            <p>Seguimiento paginado a cambios sobre tickets, pagos, tarifas y usuarios.</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Modulo</th>
                    <th>Accion</th>
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($auditLogs as $log)
                    <tr>
                        <td>{{ optional($log->logged_at)->format('d/m/Y H:i:s') }}</td>
                        <td>{{ $log->user?->username ?? 'sistema' }}</td>
                        <td>{{ $log->module }}</td>
                        <td>{{ $log->action }}</td>
                        <td>{{ $log->detail }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @include('partials.simple-pagination', ['paginator' => $auditLogs, 'label' => 'Bitacora'])
</section>

<div class="modal-backdrop" data-app-modal-id="createUserModal">
    <div class="modal-card modal-card-lg">
        <button class="modal-close" type="button" data-close-app-modal>&times;</button>
        <span class="modal-kicker">USUARIOS</span>
        <h3>Nuevo usuario</h3>
        <p>Registra operarios o administradores sin salir del listado.</p>
        @include('pages.partials.user-form', [
            'action' => route('audit.users.store'),
            'method' => 'POST',
            'userEdit' => null,
        ])
    </div>
</div>

@if ($selectedUser && request('user'))
    <div class="modal-backdrop is-visible" data-app-modal-id="editUserModal">
        <div class="modal-card modal-card-lg">
            <button class="modal-close" type="button" data-close-app-modal onclick="window.location='{{ route('audit') }}'">&times;</button>
            <span class="modal-kicker">EDITAR USUARIO</span>
            <h3>{{ $selectedUser->name }}</h3>
            <p>{{ ucfirst($selectedUser->role) }} · {{ $selectedUser->shift_name ?: 'Sin turno' }}</p>
            @include('pages.partials.user-form', [
                'action' => route('audit.users.update', $selectedUser),
                'method' => 'PUT',
                'userEdit' => $selectedUser,
            ])
        </div>
    </div>
@endif
@endsection
