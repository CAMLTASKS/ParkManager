@extends('layouts.app')

@section('title', 'Liquidación de Pago')
@section('subtitle', 'Ticket TKT-8492-A · Placa XYZ-789')

@section('content')
<section class="payment-layout">
    <div class="card stack-lg">
        <div class="split-info">
            <div>
                <span class="eyebrow">Hora Ingreso</span>
                <strong>08:30 AM</strong>
                <small>12 Oct 2023</small>
            </div>
            <div>
                <span class="eyebrow">Hora Salida</span>
                <strong>10:45 AM</strong>
                <small>12 Oct 2023</small>
            </div>
            <div>
                <span class="eyebrow">Duración Total</span>
                <strong>02:15 hrs</strong>
            </div>
            <div>
                <span class="eyebrow">Tarifa Base</span>
                <strong>Fracción (Minuto)</strong>
                <small>$90 / min</small>
            </div>
        </div>

        <div class="stack-sm">
            <h3>Desglose de Cobro</h3>
            <div class="bill-line"><span>Subtotal (135 min)</span><strong>$12,150</strong></div>
            <div class="bill-line highlight"><span>Dcto. Convenio Gimnasio (10%)</span><strong>-$1,215</strong></div>
            <div class="bill-line warning"><span>Recargo Ticket Perdido</span><strong>+$5,000</strong></div>
            <div class="bill-line"><span>IVA (19%)</span><strong>$3,027</strong></div>
        </div>
    </div>

    <div class="card stack-lg">
        <div class="amount-box">
            <span>Total a Pagar</span>
            <strong>$18,962</strong>
            <small>Impuestos incluidos</small>
        </div>

        <div>
            <h3>Método de Pago</h3>
            <div class="choice-grid payments">
                <button class="choice active">Efectivo</button>
                <button class="choice">Tarjeta</button>
                <button class="choice">Transfer.</button>
            </div>
        </div>

        <label class="field">
            <span>Efectivo Recibido</span>
            <input type="text" value="$20,000">
        </label>

        <div class="change-box">
            <span>Cambio a devolver</span>
            <strong>$1,038</strong>
        </div>

        <a href="{{ route('confirmation') }}" class="button button-primary button-block">Confirmar Pago</a>
    </div>
</section>
@endsection
