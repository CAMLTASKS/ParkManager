@extends('layouts.app')

@section('title', 'Confirmación de Salida')
@section('subtitle', 'Resumen y comprobante de transacción.')

@section('content')
<section class="success-banner">
    <div class="success-icon">✓</div>
    <h2>Salida Registrada</h2>
    <p>El vehículo puede retirarse de las instalaciones.</p>
</section>

<section class="confirmation-grid">
    <div class="card stack-md">
        <h3>Detalle de Estadía</h3>
        <div class="info-list">
            <div><span>Placa</span><strong>XYZ-789</strong></div>
            <div><span>Entrada</span><strong>12 Oct 2023, 08:30 AM</strong></div>
            <div><span>Salida</span><strong>12 Oct 2023, 10:45 AM</strong></div>
            <div><span>Tiempo Total</span><strong>2 hrs 15 min</strong></div>
            <div><span>Operador</span><strong>{{ $operator['name'] }}</strong></div>
        </div>
    </div>

    <div class="card stack-md">
        <div class="card-heading">
            <div>
                <h3>Desglose de Pago</h3>
                <p>Recibo #4829</p>
            </div>
        </div>
        <div class="info-list">
            <div><span>Subtotal</span><strong>$12,150</strong></div>
            <div><span>Dcto. Convenio</span><strong>-$1,215</strong></div>
            <div><span>Recargo</span><strong>+$5,000</strong></div>
            <div><span>IVA (19%)</span><strong>$3,027</strong></div>
            <div><span>Total Pagado</span><strong class="accent">$18,962</strong></div>
            <div><span>Método</span><strong>Efectivo</strong></div>
            <div><span>Cambio</span><strong>$1,038</strong></div>
        </div>
    </div>
</section>

<section class="actions-inline">
    <button class="button button-primary">Imprimir Comprobante</button>
    <button class="button button-muted">Reimprimir Entrada</button>
    <a href="{{ route('dashboard') }}" class="button button-dark">Finalizar</a>
</section>
@endsection
