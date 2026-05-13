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

$ticket = $payload['ticket'] ?? null;
if (! is_array($ticket) || empty($ticket['ticket_code'])) {
    portal_json_response(['ok' => false, 'message' => 'Falta ticket_code.'], 422);
}

$pdo = portal_pdo();
$now = date('Y-m-d H:i:s');

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
