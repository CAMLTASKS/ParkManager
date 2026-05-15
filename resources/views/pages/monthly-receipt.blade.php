<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recibo mensualidad - {{ $payment->receipt_code }}</title>
    <style>
        body { margin: 0; padding: 14px 8px; background: #eef2f7; color: #000; font-family: Arial, Helvetica, sans-serif; display: grid; justify-items: center; }
        .actions { display: flex; gap: 8px; margin-bottom: 12px; flex-wrap: wrap; justify-content: center; }
        .actions a, .actions button { border: 1px solid #d0d7e2; border-radius: 8px; background: #fff; color: #17233d; padding: 9px 11px; font: 700 13px Arial; text-decoration: none; cursor: pointer; }
        .paper { width: 72mm; max-width: 100%; background: #fff; padding: 3mm 3.5mm 4mm; border-radius: 2mm; box-shadow: 0 18px 52px rgba(15,23,42,.18); font-size: 3.2mm; line-height: 1.22; }
        .center { text-align: center; }
        h1 { margin: 0 0 1mm; font-size: 4.2mm; line-height: 1.05; }
        p { margin: .4mm 0; }
        .rule { margin: 2mm 0; text-align: center; font-weight: 900; }
        .plate { margin: 1mm 0 1.5mm; text-align: center; font-size: 8mm; line-height: 1; font-weight: 900; }
        .row { display: grid; grid-template-columns: 24mm 1fr; gap: 2mm; margin: .9mm 0; }
        .row span { font-weight: 900; text-transform: uppercase; }
        .total { margin-top: 2mm; text-align: center; }
        .total span { display: block; font-weight: 900; }
        .total strong { display: block; margin-top: .8mm; font-size: 8mm; line-height: 1; }
        @page { size: 80mm auto; margin: 0; }
        @media print { body { width: 80mm; padding: 0; background: #fff; display: block; } .actions { display: none; } .paper { width: 72mm; margin: 0 auto; box-shadow: none; border-radius: 0; } }
    </style>
</head>
<body>
    <div class="actions">
        <button type="button" onclick="window.print()">Imprimir</button>
        <a href="{{ route('monthly.index', ['membership' => $membership->id]) }}">Volver</a>
    </div>
    <main class="paper">
        <header class="center">
            <h1>{{ $receiptBusiness['name'] }}</h1>
            <p>{{ $receiptBusiness['regime'] }}</p>
            <p>{{ $receiptBusiness['nit'] }}</p>
            <p>{{ $receiptBusiness['address'] }}</p>
            <p>{{ $receiptBusiness['phone'] }}</p>
        </header>
        <div class="rule">------------</div>
        <p class="center"><strong>RECIBO PAGO MENSUALIDAD</strong></p>
        <p class="center">{{ $payment->receipt_code }}</p>
        <div class="plate">{{ $membership->plate }}</div>
        <div class="row"><span>Nombre</span><strong>{{ $membership->customer_name }}</strong></div>
        <div class="row"><span>Vehiculo</span><strong>{{ strtoupper($membership->vehicle_type) }} {{ $membership->vehicle_brand }}</strong></div>
        <div class="row"><span>Metodo</span><strong>{{ strtoupper($payment->method) }}</strong></div>
        <div class="row"><span>Pago</span><strong>{{ $payment->paid_at->format('d/m/Y h:i A') }}</strong></div>
        <div class="row"><span>Periodo</span><strong>{{ $payment->period_start->format('d/m/Y') }} - {{ $payment->period_end->format('d/m/Y') }}</strong></div>
        <div class="row"><span>Prox pago</span><strong>{{ $membership->next_payment_date->format('d/m/Y') }}</strong></div>
        <section class="total">
            <span>VALOR PAGADO</span>
            <strong>{{ '$'.number_format($payment->amount, 0, ',', '.') }}</strong>
        </section>
        <div class="rule">------------</div>
        <p class="center">Software por ingedevsolutions</p>
    </main>
</body>
</html>
