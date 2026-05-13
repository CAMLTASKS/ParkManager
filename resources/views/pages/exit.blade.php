@extends('layouts.app')

@section('title', 'Gestión de Salida')
@section('subtitle', 'Localiza vehículo y liquida pago.')

@section('content')
<section class="card stack-lg">
    <div class="search-row">
        <input class="search-lg" type="text" placeholder="Ingrese placa o código de ticket">
        <span class="search-divider">o</span>
        <button class="button button-outline">Escanear QR / Código</button>
    </div>

    <div class="vehicle-card">
        <div class="vehicle-main">
            <div class="icon-box">🚗</div>
            <div>
                <span class="eyebrow">Vehículo Encontrado</span>
                <h2>XYZ-789</h2>
            </div>
        </div>
        <div class="vehicle-time">
            <span>Tiempo transcurrido</span>
            <strong>02:15 hrs</strong>
        </div>
    </div>

    <div class="details-grid">
        <div>
            <span class="eyebrow">Hora de Ingreso</span>
            <strong>08:30 AM Hoy</strong>
        </div>
        <div>
            <span class="eyebrow">Ticket ID</span>
            <strong>TKT-8492-A</strong>
        </div>
        <div>
            <span class="eyebrow">Tarifa Aplicada</span>
            <strong>Fracción (Minuto)</strong>
        </div>
        <div>
            <span class="eyebrow">Alertas y Condiciones</span>
            <div class="tags">
                <span class="pill warning">Convenio: Gimnasio</span>
                <span class="pill info">Cliente Frecuente</span>
            </div>
        </div>
    </div>

    <label class="checkbox-row">
        <input type="checkbox">
        <span>Marcar como Ticket Perdido (Aplica recargo)</span>
    </label>

    <div class="row-between align-center">
        <strong>Subtotal estimado: $12,500</strong>
        <a href="{{ route('payment') }}" class="button button-primary">Calcular y Liquidar Pago</a>
    </div>
</section>

<section class="card">
    <div class="card-heading">
        <div>
            <h3>Salidas Recientes</h3>
            <p>Movimiento más reciente del turno.</p>
        </div>
        <a href="#" class="table-link">Ver todas</a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Placa</th>
                <th>Hora Salida</th>
                <th>Duración</th>
                <th>Total Pagado</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($recentExits as $exit)
                <tr>
                    <td>{{ $exit['plate'] }}</td>
                    <td>{{ $exit['time'] }}</td>
                    <td>{{ $exit['duration'] }}</td>
                    <td>{{ $exit['paid'] }}</td>
                    <td><span class="pill success">{{ $exit['status'] }}</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</section>
@endsection
