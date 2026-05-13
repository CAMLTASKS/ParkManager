<?php

function portal_config(): array
{
    static $config = null;

    if ($config === null) {
        $config = require __DIR__ . '/config.php';
    }

    return $config;
}

function portal_pdo(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = portal_config()['db'];
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $config['host'],
        $config['port'],
        $config['database'],
        $config['charset']
    );

    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

function portal_money(int|float|null $value): string
{
    $symbol = portal_config()['currency_symbol'] ?? '$';

    return $symbol . number_format((float) ($value ?? 0), 0, ',', '.');
}

function portal_date(?string $value, string $fallback = '-'): string
{
    if (! $value) {
        return $fallback;
    }

    $timestamp = strtotime($value);

    return $timestamp ? date('d/m/Y h:i A', $timestamp) : $fallback;
}

function portal_json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}
