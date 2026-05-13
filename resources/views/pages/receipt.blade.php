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
            --muted: #222;
            --line: #000;
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
            padding: 18px 10px;
        }

        .print-actions {
            width: min(100%, 420px);
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .print-actions a,
        .print-actions button {
            border: 1px solid #d0d7e2;
            border-radius: 12px;
            background: #fff;
            color: #17233d;
            padding: 11px 14px;
            font: 700 14px Arial, Helvetica, sans-serif;
            text-decoration: none;
            cursor: pointer;
        }

        .print-actions .whatsapp-action {
            border-color: #bbf7d0;
            background: #ecfdf5;
            color: #047857;
        }

        .print-actions button {
            background: #111;
            color: #fff;
            border-color: #111;
        }

        .receipt-paper {
            width: var(--paper-width);
            max-width: 100%;
            padding: 5mm 4mm 6mm;
            background: #fff;
            border-radius: 2mm;
            box-shadow: 0 18px 52px rgba(15, 23, 42, .18);
        }

        .cut-line {
            height: 0;
            border-top: 2px dashed #000;
            margin: 1mm 0 5mm;
        }

        .cut-line.bottom {
            margin: 5mm 0 1mm;
        }

        .receipt-header {
            display: grid;
            grid-template-columns: 18mm 1fr;
            gap: 4mm;
            align-items: center;
        }

        .receipt-logo {
            width: 18mm;
            height: 18mm;
            display: grid;
            place-items: center;
            border: 2px solid #000;
            border-radius: 50%;
            position: relative;
            font-size: 11mm;
            font-weight: 900;
            line-height: 1;
        }

        .receipt-logo::after {
            content: "";
            position: absolute;
            right: -1mm;
            bottom: 1mm;
            width: 8mm;
            height: 4mm;
            border: 1.6px solid #000;
            border-radius: 2mm 2mm 1mm 1mm;
            background: #fff;
        }

        .brand h1 {
            margin: 0 0 2mm;
            font-size: 5.4mm;
            line-height: 1.05;
            letter-spacing: .08mm;
            font-weight: 900;
            word-break: normal;
            overflow-wrap: normal;
        }

        .brand-row {
            display: grid;
            grid-template-columns: 7mm 1fr;
            gap: 2mm;
            align-items: center;
            margin-top: 1mm;
            color: var(--muted);
            font-size: 4.2mm;
            line-height: 1.15;
        }

        .brand-icon,
        .receipt-icon {
            width: 6mm;
            height: 6mm;
            display: grid;
            place-items: center;
            font-weight: 900;
            font-size: 4.4mm;
            line-height: 1;
        }

        .receipt-rule {
            border: 0;
            border-top: 2px solid var(--line);
            margin: 4mm 0;
        }

        .receipt-title {
            margin: 0 0 3mm;
            text-align: center;
            font-size: 6.1mm;
            font-weight: 900;
            letter-spacing: .25mm;
        }

        .receipt-row {
            display: grid;
            grid-template-columns: 9mm 1fr;
            gap: 3mm;
            align-items: center;
            padding: 3mm 0;
            border-bottom: 1.5px solid var(--line);
        }

        .receipt-row:last-child {
            border-bottom: 0;
        }

        .receipt-label {
            display: block;
            font-size: 4.5mm;
            font-weight: 900;
            line-height: 1.05;
        }

        .receipt-value {
            display: block;
            margin-top: 1mm;
            font-size: 4.8mm;
            line-height: 1.16;
            word-break: break-word;
        }

        .receipt-value.large {
            font-size: 7.2mm;
            font-weight: 900;
            letter-spacing: .15mm;
        }

        .receipt-date-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3mm;
        }

        .receipt-date-grid.single {
            grid-template-columns: 1fr;
        }

        .date-block {
            display: grid;
            gap: 1mm;
        }

        .date-block strong {
            font-size: 4.2mm;
            line-height: 1.05;
        }

        .date-block span {
            font-size: 4mm;
            line-height: 1.1;
        }

        .ticket-number {
            padding: 4mm 0 2mm;
            text-align: center;
        }

        .ticket-number span {
            display: block;
            font-size: 4.6mm;
            font-weight: 900;
        }

        .ticket-number strong {
            display: block;
            margin-top: 1mm;
            font-size: 7mm;
            font-weight: 900;
            letter-spacing: .2mm;
            word-break: break-word;
        }

        .barcode-wrap {
            padding: 0 2mm 3mm;
            text-align: center;
            border-bottom: 1.5px solid #000;
        }

        .ticket-number.no-barcode {
            padding-bottom: 4mm;
            border-bottom: 1.5px solid #000;
        }

        .receipt-barcode-svg {
            display: block;
            width: 100%;
            height: 20mm;
            margin: 0 auto 1mm;
        }

        .barcode-text {
            font-size: 4.5mm;
            letter-spacing: .25mm;
            font-weight: 700;
        }

        .total-box {
            display: grid;
            grid-template-columns: 20mm 1fr;
            gap: 3mm;
            align-items: center;
            margin: 3mm 0;
            padding: 3mm;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            background: #f3f3f3;
        }

        .total-box .money-icon {
            width: 14mm;
            height: 14mm;
            display: grid;
            place-items: center;
            border: 1.8px solid #000;
            border-radius: 50%;
            font-size: 9mm;
            font-weight: 900;
        }

        .total-copy {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1mm;
        }

        .total-copy span {
            font-size: 5mm;
            font-weight: 900;
            line-height: 1;
        }

        .total-copy strong {
            font-size: 9mm;
            font-weight: 900;
            line-height: 1;
        }

        .important {
            display: grid;
            grid-template-columns: 8mm 1fr;
            gap: 3mm;
            padding-top: 4mm;
        }

        .important-icon {
            width: 7mm;
            height: 7mm;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: #000;
            color: #fff;
            font-size: 5mm;
            font-weight: 900;
            font-family: Georgia, serif;
        }

        .important strong {
            display: block;
            margin-bottom: 1.5mm;
            font-size: 4.4mm;
            font-weight: 900;
        }

        .important ul {
            margin: 0;
            padding-left: 4.5mm;
            font-size: 3.6mm;
            line-height: 1.3;
        }

        .thanks {
            display: grid;
            grid-template-columns: 9mm 1fr;
            gap: 3mm;
            align-items: center;
            padding-top: 4mm;
        }

        .thanks-icon {
            width: 8mm;
            height: 8mm;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: #000;
            color: #fff;
            font-size: 5mm;
            font-weight: 900;
        }

        .thanks strong {
            display: block;
            font-size: 4.5mm;
            font-weight: 900;
        }

        .thanks span {
            display: block;
            margin-top: 1mm;
            font-size: 3.9mm;
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
                padding: 4mm 3.5mm 5mm;
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
        <div class="cut-line"></div>

        <header class="receipt-header">
            <div class="receipt-logo">P</div>
            <div class="brand">
                <h1>PARQUEADERO<br>DONDE RICHARD</h1>
                <div class="brand-row"><span class="brand-icon">T</span><span>Tel: {{ $receiptBusiness['phone'] }}</span></div>
                <div class="brand-row"><span class="brand-icon">U</span><span>Direccion: {{ $receiptBusiness['address'] }}</span></div>
            </div>
        </header>

        <hr class="receipt-rule">

        <h2 class="receipt-title">RECIBO DE {{ $receiptType === 'ingreso' ? 'INGRESO' : 'SALIDA' }}</h2>

        <section class="receipt-row">
            <span class="receipt-icon">A</span>
            <div>
                <span class="receipt-label">UBICACION VEHICULO:</span>
                <span class="receipt-value">{{ $formattedLocation }}</span>
            </div>
        </section>

        <section class="receipt-row">
            <span class="receipt-icon">#</span>
            <div>
                <span class="receipt-label">PLACA:</span>
                <span class="receipt-value large">{{ $ticket->plate }}</span>
            </div>
        </section>

        <section class="receipt-row">
            <span class="receipt-icon">L</span>
            <div>
                <span class="receipt-label">LOCKER:</span>
                <span class="receipt-value">{{ $ticket->uses_locker ? 'SI - LOCKER '.$ticket->locker_number : 'NO' }}</span>
            </div>
        </section>

        <section class="receipt-row">
            <span class="receipt-icon">F</span>
            <div class="receipt-date-grid {{ $receiptType === 'ingreso' ? 'single' : '' }}">
                <div class="date-block">
                    <strong>FECHA ENTRADA:</strong>
                    <span>{{ optional($ticket->entry_time)->format('d/m/Y') }} {{ optional($ticket->entry_time)->format('h:i A') }}</span>
                </div>
                @if ($receiptType === 'salida')
                    <div class="date-block">
                        <strong>FECHA SALIDA:</strong>
                        <span>{{ optional($ticket->exit_time)->format('d/m/Y') }} {{ optional($ticket->exit_time)->format('h:i A') }}</span>
                    </div>
                @endif
            </div>
        </section>

        @if ($receiptType === 'salida')
            <section class="receipt-row">
                <span class="receipt-icon">H</span>
                <div>
                    <span class="receipt-label">TIEMPO TOTAL:</span>
                    <span class="receipt-value">{{ $formattedDuration }}</span>
                </div>
            </section>

            <section class="receipt-row">
                <span class="receipt-icon">$</span>
                <div>
                    <span class="receipt-label">TARIFA:</span>
                    <span class="receipt-value">{{ strtoupper($summary['applied_tariff']) }} - {{ strtoupper($summary['pricing_label']) }}</span>
                </div>
            </section>

            @if ($summary['uses_locker'] ?? false)
                <section class="receipt-row">
                    <span class="receipt-icon">$</span>
                    <div>
                        <span class="receipt-label">VALOR LOCKER:</span>
                        <span class="receipt-value">{{ '$'.number_format($summary['locker_fee'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                </section>
            @endif

            <section class="total-box">
                <span class="money-icon">$</span>
                <div class="total-copy">
                    <span>VALOR TOTAL:</span>
                    <strong>{{ '$'.number_format($summary['total'], 0, ',', '.') }}</strong>
                </div>
            </section>
        @else
            <section class="receipt-row">
                <span class="receipt-icon">$</span>
                <div>
                    <span class="receipt-label">TARIFA:</span>
                    <span class="receipt-value">{{ strtoupper($ticket->tariffProfile?->name ?? 'SIN TARIFA') }}</span>
                </div>
            </section>

            @if ($ticket->uses_locker)
                <section class="receipt-row">
                    <span class="receipt-icon">$</span>
                    <div>
                        <span class="receipt-label">VALOR LOCKER:</span>
                        <span class="receipt-value">{{ '$'.number_format($ticket->locker_fee ?? 0, 0, ',', '.') }}</span>
                    </div>
                </section>
            @endif
        @endif

        <section class="ticket-number {{ $receiptType === 'salida' ? 'no-barcode' : '' }}">
            <span>TICKET N:</span>
            <strong>{{ $ticket->ticket_code }}</strong>
        </section>

        @if ($receiptType === 'ingreso')
            <section class="barcode-wrap">
                {!! $barcodeSvg !!}
                <div class="barcode-text">{{ $ticket->ticket_code }}</div>
            </section>
        @endif

        @if ($receiptType === 'ingreso')
            <section class="important">
                <span class="important-icon">i</span>
                <div>
                    <strong>IMPORTANTE</strong>
                    <ul>
                        <li>Conserve este recibo durante su estancia.</li>
                        <li>El parqueadero no se hace responsable por objetos dejados en el vehiculo.</li>
                        <li>Presente este ticket para la salida.</li>
                    </ul>
                </div>
            </section>
        @else
            <section class="thanks">
                <span class="thanks-icon">OK</span>
                <div>
                    <strong>GRACIAS POR SU VISITA!</strong>
                    <span>Lo esperamos nuevamente.</span>
                </div>
            </section>
        @endif

        <div class="cut-line bottom"></div>
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
