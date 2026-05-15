<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }} - {{ $ticket->ticket_code }}</title>
    <style>
        :root {
            --paper-width: 72mm;
            --ink: #000;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #eef2f7;
            color: var(--ink);
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            display: grid;
            justify-items: center;
            min-height: 100vh;
            padding: 14px 8px;
        }

        .print-actions {
            width: min(100%, 420px);
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .print-actions a,
        .print-actions button {
            border: 1px solid #d0d7e2;
            border-radius: 8px;
            background: #fff;
            color: #17233d;
            padding: 9px 11px;
            font: 700 13px Arial, Helvetica, sans-serif;
            text-decoration: none;
            cursor: pointer;
        }

        .print-actions .whatsapp-action {
            border-color: #bbf7d0;
            background: #ecfdf5;
            color: #047857;
        }

        .receipt-paper {
            width: var(--paper-width);
            max-width: 100%;
            padding: 3mm 3.5mm 4mm;
            background: #fff;
            border-radius: 2mm;
            box-shadow: 0 18px 52px rgba(15, 23, 42, .18);
            font-size: 3.2mm;
            line-height: 1.2;
        }

        .center {
            text-align: center;
        }

        .business-name {
            margin: 0 0 1mm;
            font-size: 4.2mm;
            line-height: 1.05;
            font-weight: 900;
        }

        .business-line,
        .footer-line {
            margin: .4mm 0;
            font-size: 3.1mm;
        }

        .rule {
            margin: 2mm 0;
            text-align: center;
            font: 700 3.1mm/1 Arial, Helvetica, sans-serif;
            letter-spacing: .15mm;
        }

        .plate {
            margin: 1mm 0 1.5mm;
            text-align: center;
            font-size: 8mm;
            line-height: 1;
            font-weight: 900;
            letter-spacing: 0;
            word-break: break-word;
        }

        .rows {
            display: grid;
            gap: .9mm;
        }

        .receipt-row {
            display: grid;
            grid-template-columns: 21mm 1fr;
            gap: 2mm;
            align-items: baseline;
        }

        .receipt-row span {
            font-weight: 900;
            text-transform: uppercase;
        }

        .receipt-row strong {
            font-size: 3.4mm;
            line-height: 1.15;
            word-break: break-word;
        }

        .payment-total {
            margin-top: 1.5mm;
            text-align: center;
        }

        .payment-total span {
            display: block;
            font-size: 3.4mm;
            font-weight: 900;
        }

        .payment-total strong {
            display: block;
            margin-top: .6mm;
            font-size: 8mm;
            line-height: 1;
            font-weight: 900;
        }

        .barcode-wrap {
            text-align: center;
        }

        .receipt-barcode-svg {
            display: block;
            width: 100%;
            height: 31mm;
            margin: 0 auto .7mm;
        }

        .barcode-text {
            font-size: 3.8mm;
            line-height: 1;
            font-weight: 900;
            letter-spacing: .15mm;
        }

        .footer {
            text-align: center;
        }

        @page {
            size: 80mm auto;
            margin: 0;
        }

        @media print {
            html,
            body {
                width: 80mm;
                min-height: auto;
                padding: 0;
                background: #fff;
            }

            body {
                display: block;
            }

            .print-actions {
                display: none;
            }

            .receipt-paper {
                width: 72mm;
                max-width: none;
                margin: 0 auto;
                padding: 3mm 3.5mm 4mm;
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <div class="print-actions">
        <a href="{{ route('tickets.print', ['ticket' => $ticket, 'type' => $receiptType, 'return_to' => 'transaction']) }}">Imprimir directo</a>
        <a href="{{ $whatsappReceiptUrl }}" target="_blank" rel="noopener" class="whatsapp-action">Enviar WhatsApp</a>
        <a href="{{ route('entry') }}">Volver a entrada</a>
        <a href="{{ route('transaction.show', $ticket) }}">Ver ticket</a>
    </div>

    <main class="receipt-paper">
        <header class="center">
            <h1 class="business-name">{{ $receiptBusiness['name'] }}</h1>
            <p class="business-line">{{ $receiptBusiness['regime'] }}</p>
            <p class="business-line">{{ $receiptBusiness['nit'] }}</p>
            <p class="business-line">{{ $receiptBusiness['address'] }}</p>
            <p class="business-line">{{ $receiptBusiness['phone'] }}</p>
        </header>

        <div class="rule">------------</div>

        <section>
            <div class="plate">{{ $ticket->plate }}</div>
            <div class="rows">
                <div class="receipt-row"><span>Fecha</span><strong>{{ optional($ticket->entry_time)->format('d/m/Y') }}</strong></div>
                <div class="receipt-row"><span>Hora</span><strong>{{ optional($ticket->entry_time)->format('h:i A') }}</strong></div>
                <div class="receipt-row"><span>Ubicacion</span><strong>{{ $formattedLocation }}</strong></div>
                <div class="receipt-row"><span>Locker</span><strong>{{ $ticket->uses_locker ? 'SI - LOCKER '.$ticket->locker_number : 'NO' }}</strong></div>
            </div>
        </section>

        @if ($receiptType === 'salida')
            <div class="rule">------------</div>
            <section class="rows">
                <div class="receipt-row"><span>Salida</span><strong>{{ optional($ticket->exit_time)->format('d/m/Y h:i A') }}</strong></div>
                <div class="receipt-row"><span>Tiempo</span><strong>{{ $formattedDuration }}</strong></div>
                <div class="receipt-row"><span>Tipo pago</span><strong>{{ strtoupper($ticket->payment?->method ?? 'N/A') }}</strong></div>
            </section>
            <section class="payment-total">
                <span>VALOR A PAGAR</span>
                <strong>{{ '$'.number_format($summary['total'], 0, ',', '.') }}</strong>
            </section>
            <div class="rule">------------</div>
        @else
            <div class="rule">------------</div>
            <section class="barcode-wrap">
                {!! $barcodeSvg !!}
                <div class="barcode-text">{{ $barcodeValue }}</div>
            </section>
            <div class="rule">------------</div>
        @endif

        <footer class="footer">
            @if ($receiptType === 'ingreso')
                <p class="footer-line">Horario lunes a sabado 5:30am a 10:30pm</p>
                <p class="footer-line">Domingos y festivos 7am a 10pm</p>
            @else
                <p class="footer-line">Gracias por su visita</p>
            @endif
            <p class="footer-line">Software por ingedevsolutions</p>
        </footer>
    </main>

    <script>
        const autoPrint = @json($autoPrint);
        const autoReturn = @json($autoReturn);
        const autoClose = @json($autoClose ?? false);
        const returnUrl = @json($returnUrl ?? route('entry'));
        let returning = false;

        if (autoPrint) {
            window.addEventListener('load', () => {
                setTimeout(() => window.print(), 250);
            });
        }

        window.addEventListener('afterprint', () => {
            if (autoClose) {
                setTimeout(() => window.close(), 500);
                return;
            }

            if (!autoReturn || returning) {
                return;
            }

            returning = true;
            setTimeout(() => {
                window.location.href = returnUrl;
            }, 700);
        });
    </script>
</body>
</html>
