<?php

require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    portal_json_response(['ok' => false, 'message' => 'Metodo no permitido.'], 405);
}

$raw = file_get_contents('php://input') ?: '';
$payload = json_decode($raw, true);

if (! is_array($payload)) {
    portal_json_response(['ok' => false, 'message' => 'JSON invalido.'], 422);
}

$config = portal_config();
$expectedToken = (string) ($config['sync_token'] ?? '');
$receivedToken = (string) ($_SERVER['HTTP_X_PORTAL_TOKEN'] ?? ($payload['token'] ?? ''));

if ($expectedToken !== '' && ! hash_equals($expectedToken, $receivedToken)) {
    portal_json_response(['ok' => false, 'message' => 'Token invalido.'], 401);
}

$pdo = portal_pdo();
$now = date('Y-m-d H:i:s');

$monthly = $payload['monthly_membership'] ?? null;
if (is_array($monthly) && ! empty($monthly['source_membership_id'])) {
    portal_ensure_monthly_tables($pdo);

    $fields = [
        'source_membership_id' => (string) $monthly['source_membership_id'],
        'plate' => (string) ($monthly['plate'] ?? ''),
        'customer_name' => (string) ($monthly['customer_name'] ?? ''),
        'vehicle_type' => (string) ($monthly['vehicle_type'] ?? ''),
        'vehicle_brand' => $monthly['vehicle_brand'] ?? null,
        'phone' => $monthly['phone'] ?? null,
        'site_name' => (string) ($monthly['site_name'] ?? 'Principal'),
        'tariff_name' => (string) ($monthly['tariff_name'] ?? 'Sin tarifa'),
        'tariff_amount' => (int) ($monthly['tariff_amount'] ?? 0),
        'starts_at' => $monthly['starts_at'] ?? null,
        'next_payment_date' => $monthly['next_payment_date'] ?? null,
        'status' => (string) ($monthly['status'] ?? 'active'),
        'days_overdue' => (int) ($monthly['days_overdue'] ?? 0),
        'last_payment_code' => $monthly['last_payment_code'] ?? null,
        'last_payment_amount' => (int) ($monthly['last_payment_amount'] ?? 0),
        'last_paid_at' => $monthly['last_paid_at'] ?? null,
        'last_activity_type' => $monthly['last_activity_type'] ?? null,
        'last_activity_at' => $monthly['last_activity_at'] ?? null,
        'notes' => $monthly['notes'] ?? null,
        'last_synced_at' => $monthly['synced_at'] ?? $now,
        'updated_at' => $now,
        'created_at' => $now,
    ];

    $stmt = $pdo->prepare("
        INSERT INTO portal_monthly_memberships (
            source_membership_id, plate, customer_name, vehicle_type, vehicle_brand, phone,
            site_name, tariff_name, tariff_amount, starts_at, next_payment_date, status,
            days_overdue, last_payment_code, last_payment_amount, last_paid_at,
            last_activity_type, last_activity_at, notes, last_synced_at, created_at, updated_at
        ) VALUES (
            :source_membership_id, :plate, :customer_name, :vehicle_type, :vehicle_brand, :phone,
            :site_name, :tariff_name, :tariff_amount, :starts_at, :next_payment_date, :status,
            :days_overdue, :last_payment_code, :last_payment_amount, :last_paid_at,
            :last_activity_type, :last_activity_at, :notes, :last_synced_at, :created_at, :updated_at
        )
        ON DUPLICATE KEY UPDATE
            plate = VALUES(plate),
            customer_name = VALUES(customer_name),
            vehicle_type = VALUES(vehicle_type),
            vehicle_brand = VALUES(vehicle_brand),
            phone = VALUES(phone),
            site_name = VALUES(site_name),
            tariff_name = VALUES(tariff_name),
            tariff_amount = VALUES(tariff_amount),
            starts_at = VALUES(starts_at),
            next_payment_date = VALUES(next_payment_date),
            status = VALUES(status),
            days_overdue = VALUES(days_overdue),
            last_payment_code = VALUES(last_payment_code),
            last_payment_amount = VALUES(last_payment_amount),
            last_paid_at = VALUES(last_paid_at),
            last_activity_type = VALUES(last_activity_type),
            last_activity_at = VALUES(last_activity_at),
            notes = VALUES(notes),
            last_synced_at = VALUES(last_synced_at),
            updated_at = VALUES(updated_at)
    ");
    $stmt->execute($fields);

    $payment = $payload['monthly_payment'] ?? null;
    if (is_array($payment) && ! empty($payment['source_payment_id'])) {
        $stmt = $pdo->prepare("
            INSERT INTO portal_monthly_payments (
                source_payment_id, source_membership_id, receipt_code, method, amount,
                period_start, period_end, paid_at, created_at, updated_at
            ) VALUES (
                :source_payment_id, :source_membership_id, :receipt_code, :method, :amount,
                :period_start, :period_end, :paid_at, :created_at, :updated_at
            )
            ON DUPLICATE KEY UPDATE
                receipt_code = VALUES(receipt_code),
                method = VALUES(method),
                amount = VALUES(amount),
                period_start = VALUES(period_start),
                period_end = VALUES(period_end),
                paid_at = VALUES(paid_at),
                updated_at = VALUES(updated_at)
        ");
        $stmt->execute([
            'source_payment_id' => (string) $payment['source_payment_id'],
            'source_membership_id' => $fields['source_membership_id'],
            'receipt_code' => (string) ($payment['receipt_code'] ?? ''),
            'method' => (string) ($payment['method'] ?? ''),
            'amount' => (int) ($payment['amount'] ?? 0),
            'period_start' => $payment['period_start'] ?? null,
            'period_end' => $payment['period_end'] ?? null,
            'paid_at' => $payment['paid_at'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    $stmt = $pdo->prepare("
        INSERT INTO portal_events (event_type, ticket_code, payload, event_time, created_at)
        VALUES (:event_type, :ticket_code, :payload, :event_time, :created_at)
    ");
    $stmt->execute([
        'event_type' => (string) ($payload['event_type'] ?? 'monthly_sync'),
        'ticket_code' => 'MENS-' . $fields['source_membership_id'],
        'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE),
        'event_time' => $payload['event_time'] ?? $now,
        'created_at' => $now,
    ]);

    portal_json_response(['ok' => true, 'message' => 'Mensualidad sincronizada', 'membership' => $fields['source_membership_id']]);
}

$ticket = $payload['ticket'] ?? null;
if (! is_array($ticket) || empty($ticket['ticket_code'])) {
    portal_json_response(['ok' => false, 'message' => 'Falta ticket_code.'], 422);
}

$fields = [
    'source_ticket_id' => (string) ($ticket['source_ticket_id'] ?? ''),
    'ticket_code' => (string) $ticket['ticket_code'],
    'barcode' => (string) ($ticket['barcode'] ?? $ticket['ticket_code']),
    'plate' => (string) ($ticket['plate'] ?? ''),
    'vehicle_type' => (string) ($ticket['vehicle_type'] ?? ''),
    'status' => (string) ($ticket['status'] ?? ''),
    'location_number' => $ticket['location_number'] ?? null,
    'site_name' => (string) ($ticket['site_name'] ?? 'Principal'),
    'tariff_name' => (string) ($ticket['tariff_name'] ?? 'Sin tarifa'),
    'tariff_type' => (string) ($ticket['tariff_type'] ?? 'normal'),
    'entry_time' => $ticket['entry_time'] ?? null,
    'exit_time' => $ticket['exit_time'] ?? null,
    'payment_method' => $ticket['payment_method'] ?? null,
    'payment_status' => $ticket['payment_status'] ?? null,
    'paid_at' => $ticket['paid_at'] ?? null,
    'subtotal' => (int) ($ticket['subtotal'] ?? 0),
    'discount' => (int) ($ticket['discount'] ?? 0),
    'surcharge' => (int) ($ticket['surcharge'] ?? 0),
    'tax' => (int) ($ticket['tax'] ?? 0),
    'total' => (int) ($ticket['total'] ?? 0),
    'duration_minutes' => (int) ($ticket['duration_minutes'] ?? 0),
    'last_synced_at' => $ticket['synced_at'] ?? $now,
    'updated_at' => $now,
];

$sql = "
    INSERT INTO portal_tickets (
        source_ticket_id, ticket_code, barcode, plate, vehicle_type, status, location_number,
        site_name, tariff_name, tariff_type, entry_time, exit_time, payment_method, payment_status,
        paid_at, subtotal, discount, surcharge, tax, total, duration_minutes, last_synced_at,
        created_at, updated_at
    ) VALUES (
        :source_ticket_id, :ticket_code, :barcode, :plate, :vehicle_type, :status, :location_number,
        :site_name, :tariff_name, :tariff_type, :entry_time, :exit_time, :payment_method, :payment_status,
        :paid_at, :subtotal, :discount, :surcharge, :tax, :total, :duration_minutes, :last_synced_at,
        :created_at, :updated_at
    )
    ON DUPLICATE KEY UPDATE
        source_ticket_id = VALUES(source_ticket_id),
        barcode = VALUES(barcode),
        plate = VALUES(plate),
        vehicle_type = VALUES(vehicle_type),
        status = VALUES(status),
        location_number = VALUES(location_number),
        site_name = VALUES(site_name),
        tariff_name = VALUES(tariff_name),
        tariff_type = VALUES(tariff_type),
        entry_time = VALUES(entry_time),
        exit_time = VALUES(exit_time),
        payment_method = VALUES(payment_method),
        payment_status = VALUES(payment_status),
        paid_at = VALUES(paid_at),
        subtotal = VALUES(subtotal),
        discount = VALUES(discount),
        surcharge = VALUES(surcharge),
        tax = VALUES(tax),
        total = VALUES(total),
        duration_minutes = VALUES(duration_minutes),
        last_synced_at = VALUES(last_synced_at),
        updated_at = VALUES(updated_at)
";

$fields['created_at'] = $now;
$stmt = $pdo->prepare($sql);
$stmt->execute($fields);

$event = [
    'event_type' => (string) ($payload['event_type'] ?? 'sync'),
    'ticket_code' => $fields['ticket_code'],
    'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE),
    'event_time' => $payload['event_time'] ?? $now,
    'created_at' => $now,
];

$stmt = $pdo->prepare("
    INSERT INTO portal_events (event_type, ticket_code, payload, event_time, created_at)
    VALUES (:event_type, :ticket_code, :payload, :event_time, :created_at)
");
$stmt->execute($event);

portal_json_response(['ok' => true, 'message' => 'Sincronizado', 'ticket_code' => $fields['ticket_code']]);

function portal_ensure_monthly_tables(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS portal_monthly_memberships (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            source_membership_id VARCHAR(80) NOT NULL,
            plate VARCHAR(30) NOT NULL,
            customer_name VARCHAR(160) NOT NULL,
            vehicle_type VARCHAR(40) NOT NULL,
            vehicle_brand VARCHAR(100) NULL,
            phone VARCHAR(40) NULL,
            site_name VARCHAR(160) NULL,
            tariff_name VARCHAR(160) NULL,
            tariff_amount INT NOT NULL DEFAULT 0,
            starts_at DATE NULL,
            next_payment_date DATE NULL,
            status VARCHAR(40) NOT NULL DEFAULT 'active',
            days_overdue INT NOT NULL DEFAULT 0,
            last_payment_code VARCHAR(80) NULL,
            last_payment_amount INT NOT NULL DEFAULT 0,
            last_paid_at DATETIME NULL,
            last_activity_type VARCHAR(40) NULL,
            last_activity_at DATETIME NULL,
            notes TEXT NULL,
            last_synced_at DATETIME NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            UNIQUE KEY portal_monthly_source_unique (source_membership_id),
            KEY portal_monthly_plate_index (plate),
            KEY portal_monthly_status_index (status),
            KEY portal_monthly_next_payment_index (next_payment_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS portal_monthly_payments (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            source_payment_id VARCHAR(80) NOT NULL,
            source_membership_id VARCHAR(80) NOT NULL,
            receipt_code VARCHAR(80) NOT NULL,
            method VARCHAR(40) NOT NULL,
            amount INT NOT NULL DEFAULT 0,
            period_start DATE NULL,
            period_end DATE NULL,
            paid_at DATETIME NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            UNIQUE KEY portal_monthly_payment_source_unique (source_payment_id),
            KEY portal_monthly_payment_membership_index (source_membership_id),
            KEY portal_monthly_payment_paid_at_index (paid_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}
