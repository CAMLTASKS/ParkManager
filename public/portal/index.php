<?php

require __DIR__ . '/auth.php';

$pdo = portal_pdo();
$config = portal_config();
$user = portal_require_login();
$isAdmin = ($user['role'] ?? '') === 'admin';

$view = $_GET['view'] ?? 'dashboard';
$allowedViews = ['dashboard', 'movimientos', 'activos', 'salidas', 'tarifas', 'graficas', 'usuarios'];
if (! in_array($view, $allowedViews, true) || ($view === 'usuarios' && ! $isAdmin)) {
    $view = 'dashboard';
}

$notice = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin) {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_user') {
        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $role = ($_POST['role'] ?? 'viewer') === 'admin' ? 'admin' : 'viewer';

        if ($name !== '' && $username !== '' && $password !== '') {
            try {
                $stmt = $pdo->prepare('
                    INSERT INTO portal_users (name, username, password_hash, role, active, created_at, updated_at)
                    VALUES (:name, :username, :password_hash, :role, 1, NOW(), NOW())
                ');
                $stmt->execute([
                    'name' => $name,
                    'username' => $username,
                    'password_hash' => hash('sha256', $password),
                    'role' => $role,
                ]);
                $notice = 'Usuario creado correctamente.';
            } catch (Throwable) {
                $notice = 'No se pudo crear el usuario. Revisa si el usuario ya existe.';
            }
        }
    }

    if ($action === 'toggle_user') {
        $id = (int) ($_POST['user_id'] ?? 0);
        if ($id > 0 && $id !== (int) $user['id']) {
            $stmt = $pdo->prepare('UPDATE portal_users SET active = IF(active = 1, 0, 1), updated_at = NOW() WHERE id = :id');
            $stmt->execute(['id' => $id]);
            $notice = 'Estado del usuario actualizado.';
        }
    }
}

$today = date('Y-m-d');
$dateFrom = $_GET['date_from'] ?? date('Y-m-d', strtotime('-6 days'));
$dateTo = $_GET['date_to'] ?? $today;
$vehicleType = $_GET['vehicle_type'] ?? '';
$status = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');
$page = max((int) ($_GET['page'] ?? 1), 1);
$perPage = 12;

$where = ['entry_time BETWEEN :date_from AND :date_to'];
$params = [
    'date_from' => $dateFrom . ' 00:00:00',
    'date_to' => $dateTo . ' 23:59:59',
];

if ($view === 'activos') {
    $where[] = "status = 'active'";
} elseif ($view === 'salidas') {
    $where[] = 'exit_time IS NOT NULL';
} elseif ($status !== '') {
    $where[] = 'status = :status';
    $params['status'] = $status;
}

if ($vehicleType !== '') {
    $where[] = 'vehicle_type = :vehicle_type';
    $params['vehicle_type'] = $vehicleType;
}

if ($search !== '') {
    $where[] = '(ticket_code LIKE :search OR plate LIKE :search OR barcode LIKE :search)';
    $params['search'] = '%' . strtoupper($search) . '%';
}

$whereSql = implode(' AND ', $where);

function portal_page_url(int $page): string
{
    $query = $_GET;
    $query['page'] = $page;
    return '?' . http_build_query($query);
}

function portal_status_label(string $status): string
{
    return match ($status) {
        'active' => 'Activo',
        'paid' => 'Pagado',
        'pending_payment' => 'Pendiente',
        default => ucfirst(str_replace('_', ' ', $status)),
    };
}

function portal_icon(string $name): string
{
    $icons = [
        'home' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 10.6 12 4l8 6.6V20a1 1 0 0 1-1 1h-5v-6h-4v6H5a1 1 0 0 1-1-1v-9.4Z"/></svg>',
        'list' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v2H4V6Zm0 5h16v2H4v-2Zm0 5h10v2H4v-2Z"/></svg>',
        'car' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.4 7 5 11H3v7h2v-2h14v2h2v-7h-2l-1.4-4H6.4Zm1.4 2h8.4l.8 2H7l.8-2ZM6 13.5A1.5 1.5 0 1 1 6 16a1.5 1.5 0 0 1 0-2.5Zm12 0A1.5 1.5 0 1 1 18 16a1.5 1.5 0 0 1 0-2.5Z"/></svg>',
        'cash' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v12H4V6Zm2 3v6h12V9H6Zm6 1a2 2 0 1 1 0 4 2 2 0 0 1 0-4Z"/></svg>',
        'tag' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12V4h8l10 10-7 7L3 12Zm5-5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"/></svg>',
        'chart' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19h16v2H4v-2Zm1-2V9h3v8H5Zm5 0V4h3v13h-3Zm5 0v-6h3v6h-3Z"/></svg>',
        'users' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm0 2c4 0 6 2 6 4.5V20H2v-2.5C2 15 4 13 8 13Zm8.5-1a3.5 3.5 0 1 1 0-7 3.5 3.5 0 0 1 0 7Zm.2 1c3.2.1 5.3 1.8 5.3 4.1V20h-6v-2.5c0-1.5-.5-3-1.6-4.1.6-.3 1.4-.4 2.3-.4Z"/></svg>',
    ];

    return $icons[$name] ?? $icons['home'];
}

$stmt = $pdo->prepare("
    SELECT
        COUNT(*) AS tickets,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_count,
        SUM(CASE WHEN status = 'pending_payment' OR payment_status = 'pending' THEN 1 ELSE 0 END) AS pending_count,
        SUM(CASE WHEN payment_status = 'paid' OR status = 'paid' THEN total ELSE 0 END) AS paid_income,
        SUM(CASE WHEN status = 'pending_payment' OR payment_status = 'pending' THEN total ELSE 0 END) AS pending_income,
        SUM(CASE WHEN exit_time IS NOT NULL THEN 1 ELSE 0 END) AS exits_count
    FROM portal_tickets
    WHERE $whereSql
");
$stmt->execute($params);
$stats = $stmt->fetch() ?: [];

$stmt = $pdo->prepare("
    SELECT DATE(entry_time) AS day, SUM(CASE WHEN payment_status = 'paid' OR status = 'paid' THEN total ELSE 0 END) AS income, COUNT(*) AS entries
    FROM portal_tickets
    WHERE $whereSql
    GROUP BY DATE(entry_time)
    ORDER BY day ASC
");
$stmt->execute($params);
$daily = $stmt->fetchAll();
$maxDaily = max(array_map(fn($row) => max((int) $row['income'], (int) $row['entries']), $daily ?: [['income' => 1, 'entries' => 1]]));

$stmt = $pdo->prepare("
    SELECT vehicle_type, COUNT(*) AS total, SUM(CASE WHEN payment_status = 'paid' OR status = 'paid' THEN total ELSE 0 END) AS income
    FROM portal_tickets
    WHERE $whereSql
    GROUP BY vehicle_type
    ORDER BY total DESC
");
$stmt->execute($params);
$vehicleMix = $stmt->fetchAll();
$maxMix = max(array_map(fn($row) => (int) $row['total'], $vehicleMix ?: [['total' => 1]]));

$stmt = $pdo->prepare("
    SELECT tariff_name, vehicle_type, COUNT(*) AS uses_count, SUM(CASE WHEN payment_status = 'paid' OR status = 'paid' THEN total ELSE 0 END) AS income
    FROM portal_tickets
    WHERE $whereSql
    GROUP BY tariff_name, vehicle_type
    ORDER BY income DESC, uses_count DESC
    LIMIT 12
");
$stmt->execute($params);
$tariffs = $stmt->fetchAll();

$countStmt = $pdo->prepare("SELECT COUNT(*) AS total FROM portal_tickets WHERE $whereSql");
$countStmt->execute($params);
$totalRows = (int) ($countStmt->fetch()['total'] ?? 0);
$totalPages = max((int) ceil($totalRows / $perPage), 1);
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare("
    SELECT *
    FROM portal_tickets
    WHERE $whereSql
    ORDER BY COALESCE(exit_time, entry_time, updated_at) DESC
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$tickets = $stmt->fetchAll();

$events = [];
if ($view === 'graficas') {
    $events = $pdo->query("
        SELECT event_type, ticket_code, event_time, created_at
        FROM portal_events
        ORDER BY created_at DESC
        LIMIT 60
    ")->fetchAll();
}

$stmt = $pdo->prepare("
    SELECT status, COUNT(*) AS total, SUM(total) AS income
    FROM portal_tickets
    WHERE $whereSql
    GROUP BY status
    ORDER BY total DESC
");
$stmt->execute($params);
$statusMix = $stmt->fetchAll();
$maxStatus = max(array_map(fn($row) => (int) $row['total'], $statusMix ?: [['total' => 1]]));

$users = [];
if ($view === 'usuarios' && $isAdmin) {
    $users = $pdo->query('SELECT * FROM portal_users ORDER BY active DESC, name ASC')->fetchAll();
}

$lastSync = $pdo->query('SELECT MAX(last_synced_at) AS last_sync FROM portal_tickets')->fetch()['last_sync'] ?? null;
$nav = [
    'dashboard' => ['label' => 'Inicio', 'icon' => '⌂'],
    'movimientos' => ['label' => 'Registros', 'icon' => '≡'],
    'activos' => ['label' => 'Activos', 'icon' => '●'],
    'salidas' => ['label' => 'Salidas', 'icon' => '$'],
    'tarifas' => ['label' => 'Tarifas', 'icon' => '%'],
    'eventos' => ['label' => 'Sync', 'icon' => '↻'],
];
if ($isAdmin) {
    $nav['usuarios'] = ['label' => 'Usuarios', 'icon' => '+'];
}

$nav = [
    'dashboard' => ['label' => 'Inicio', 'icon' => 'home'],
    'movimientos' => ['label' => 'Registros', 'icon' => 'list'],
    'activos' => ['label' => 'Activos', 'icon' => 'car'],
    'salidas' => ['label' => 'Salidas', 'icon' => 'cash'],
    'tarifas' => ['label' => 'Tarifas', 'icon' => 'tag'],
    'graficas' => ['label' => 'Graficas', 'icon' => 'chart'],
];
if ($isAdmin) {
    $nav['usuarios'] = ['label' => 'Usuarios', 'icon' => 'users'];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($config['portal_name']) ?> - Portal</title>
    <link rel="stylesheet" href="assets/portal.css">
</head>

<body>
    <div class="app-layout">
        <aside class="app-sidebar">
            <a class="brand" href="index.php">
                <div class="brand-mark">P</div>
                <div>
                    <h1><?= htmlspecialchars($config['portal_name']) ?></h1>
                    <p>Monitor movil</p>
                </div>
            </a>
            <nav class="side-nav">
                <?php foreach ($nav as $key => $item): ?>
                    <a href="?view=<?= $key ?>" class="<?= $view === $key ? 'active' : '' ?>">
                        <span class="nav-icon"><?= portal_icon($item['icon']) ?></span><?= htmlspecialchars($item['label']) ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            <div class="sidebar-user">
                <strong><?= htmlspecialchars($user['name']) ?></strong>u
                <span><?= htmlspecialchars($user['role']) ?></span>
                <a href="logout.php">Salir</a>
            </div>
        </aside>

        <main class="app-main">
            <header class="portal-topbar">
                <div>

                    <span class="top-kicker">Ultima sync: <?= portal_date($lastSync, 'Sin datos') ?></span>
                    <span style="text-align: left; color:red; margin-left:50px;"> <a href="logout.php">Salir</a>
                    </span>
                    <h2><?= htmlspecialchars($nav[$view]['label'] ?? 'Inicio') ?></h2>

                </div>
                <a class="button ghost" href="index.php?view=movimientos">Ver registros</a>
            </header>

            <?php if ($notice): ?>
                <div class="notice success"><?= htmlspecialchars($notice) ?></div>
            <?php endif; ?>

            <section class="filter-card">
                <form class="filter-grid" method="GET">
                    <input type="hidden" name="view" value="<?= htmlspecialchars($view) ?>">
                    <label><span>Desde</span><input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>"></label>
                    <label><span>Hasta</span><input type="date" name="date_to" value="<?= htmlspecialchars($dateTo) ?>"></label>
                    <label>
                        <span>Vehiculo</span>
                        <select name="vehicle_type">
                            <option value="">Todos</option>
                            <option value="moto" <?= $vehicleType === 'moto' ? 'selected' : '' ?>>Moto</option>
                            <option value="auto" <?= $vehicleType === 'auto' ? 'selected' : '' ?>>Auto</option>
                            <option value="bicicleta" <?= $vehicleType === 'bicicleta' ? 'selected' : '' ?>>Bicicleta</option>
                        </select>
                    </label>
                    <label>
                        <span>Estado</span>
                        <select name="status">
                            <option value="">Todos</option>
                            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Activo</option>
                            <option value="paid" <?= $status === 'paid' ? 'selected' : '' ?>>Pagado</option>
                            <option value="pending_payment" <?= $status === 'pending_payment' ? 'selected' : '' ?>>Pendiente</option>
                        </select>
                    </label>
                    <label><span>Buscar</span><input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Placa o ticket"></label>
                    <button class="button" type="submit">Filtrar</button>
                </form>
            </section>

            <section class="kpi-grid">
                <article class="kpi accent"><span>Ingresos</span><strong><?= portal_money($stats['paid_income'] ?? 0) ?></strong><small>Pagos confirmados</small></article>
                <article class="kpi"><span>Por cobrar</span><strong><?= portal_money($stats['pending_income'] ?? 0) ?></strong><small>Pendientes</small></article>
                <article class="kpi"><span>Activos</span><strong><?= (int) ($stats['active_count'] ?? 0) ?></strong><small>En parqueadero</small></article>
                <article class="kpi"><span>Movimientos</span><strong><?= (int) ($stats['tickets'] ?? 0) ?></strong><small>Periodo filtrado</small></article>
            </section>

            <?php if ($view === 'dashboard'): ?>
                <section class="portal-grid">
                    <article class="portal-card hero-panel">
                        <span class="hero-kicker">Resumen operativo</span>
                        <h3>Todo lo importante del parqueadero en una vista rapida.</h3>
                        <div class="hero-actions">
                            <a class="chip" href="?view=activos">Activos: <?= (int) ($stats['active_count'] ?? 0) ?></a>
                            <a class="chip" href="?view=salidas">Salidas: <?= (int) ($stats['exits_count'] ?? 0) ?></a>
                            <a class="chip" href="?view=graficas">Graficas y sync: <?= portal_date($lastSync, 'pendiente') ?></a>
                        </div>
                    </article>
                    <article class="portal-card">
                        <div class="card-head">
                            <h3>Vehiculos</h3><span><?= count($vehicleMix) ?> tipos</span>
                        </div>
                        <div class="mix-list">
                            <?php foreach ($vehicleMix as $row): ?>
                                <div class="mix-row">
                                    <span><?= htmlspecialchars(strtoupper($row['vehicle_type'])) ?></span>
                                    <strong><?= (int) $row['total'] ?></strong>
                                    <div class="meter"><i style="width: <?= max(((int) $row['total'] / max($maxMix, 1)) * 100, 4) ?>%"></i></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </article>
                </section>
            <?php endif; ?>

            <?php if (in_array($view, ['dashboard', 'tarifas', 'graficas'], true)): ?>
                <section class="portal-grid">
                    <article class="portal-card">
                        <div class="card-head">
                            <h3>Ingresos por dia</h3><span><?= htmlspecialchars($dateFrom) ?> / <?= htmlspecialchars($dateTo) ?></span>
                        </div>
                        <div class="bars">
                            <?php foreach ($daily as $row): ?>
                                <?php $height = max(((int) $row['income'] / max($maxDaily, 1)) * 170, 8); ?>
                                <div class="bar">
                                    <b><?= portal_money($row['income']) ?></b>
                                    <span style="height: <?= (int) $height ?>px"></span>
                                    <small><?= date('d/m', strtotime($row['day'])) ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </article>

                    <article class="portal-card">
                        <div class="card-head">
                            <h3>Tarifas principales</h3><span>Top 12</span>
                        </div>
                        <div class="mix-list">
                            <?php foreach ($tariffs as $row): ?>
                                <div class="mix-row">
                                    <span><?= htmlspecialchars($row['tariff_name']) ?><small><?= htmlspecialchars(strtoupper($row['vehicle_type'])) ?></small></span>
                                    <strong><?= portal_money($row['income']) ?></strong>
                                    <div class="meter"><i style="width: <?= min(max(((int) $row['uses_count'] / max((int) ($stats['tickets'] ?? 1), 1)) * 100, 6), 100) ?>%"></i></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </article>
                </section>
            <?php endif; ?>

            <?php if (in_array($view, ['movimientos', 'activos', 'salidas', 'dashboard'], true)): ?>
                <?php if (in_array($view, ['activos', 'salidas'], true)): ?>
                    <section class="portal-grid compact-view-grid">
                        <article class="portal-card view-feature-card <?= $view === 'activos' ? 'active-view' : 'exit-view' ?>">
                            <span class="hero-kicker"><?= $view === 'activos' ? 'Operacion activa' : 'Recaudo y salidas' ?></span>
                            <h3><?= $view === 'activos' ? 'Vehiculos actualmente dentro del parqueadero.' : 'Movimientos cerrados dentro del periodo.' ?></h3>
                            <strong><?= $view === 'activos' ? (int) ($stats['active_count'] ?? 0) : (int) ($stats['exits_count'] ?? 0) ?></strong>
                        </article>
                        <article class="portal-card">
                            <div class="card-head">
                                <h3><?= $view === 'activos' ? 'Tipos activos' : 'Estados de salida' ?></h3><span>Distribucion</span>
                            </div>
                            <div class="mix-list">
                                <?php foreach (($view === 'activos' ? $vehicleMix : $statusMix) as $row): ?>
                                    <?php $label = $view === 'activos' ? strtoupper($row['vehicle_type']) : portal_status_label($row['status']); ?>
                                    <?php $max = $view === 'activos' ? $maxMix : $maxStatus; ?>
                                    <div class="mix-row">
                                        <span><?= htmlspecialchars($label) ?></span>
                                        <strong><?= (int) $row['total'] ?></strong>
                                        <div class="meter"><i style="width: <?= max(((int) $row['total'] / max($max, 1)) * 100, 4) ?>%"></i></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </article>
                    </section>
                <?php endif; ?>
                <section class="portal-card">
                    <div class="card-head">
                        <div>
                            <h3>Registros sincronizados</h3>
                            <p><?= $totalRows ?> registros encontrados.</p>
                        </div>
                        <div class="view-tabs">
                            <a class="<?= $view === 'movimientos' ? 'active' : '' ?>" href="?view=movimientos">Todos</a>
                            <a class="<?= $view === 'activos' ? 'active' : '' ?>" href="?view=activos">Activos</a>
                            <a class="<?= $view === 'salidas' ? 'active' : '' ?>" href="?view=salidas">Salidas</a>
                        </div>
                    </div>
                    <div class="record-list">
                        <?php foreach ($tickets as $ticket): ?>
                            <article class="record-card">
                                <div>
                                    <span class="ticket-code"><?= htmlspecialchars($ticket['ticket_code']) ?></span>
                                    <strong><?= htmlspecialchars($ticket['plate']) ?></strong>
                                </div>
                                <span class="status <?= htmlspecialchars($ticket['status']) ?>"><?= portal_status_label($ticket['status']) ?></span>
                                <div><small>Tipo</small><b><?= htmlspecialchars(strtoupper($ticket['vehicle_type'])) ?></b></div>
                                <div><small>Entrada</small><b><?= portal_date($ticket['entry_time']) ?></b></div>
                                <div><small>Salida</small><b><?= portal_date($ticket['exit_time']) ?></b></div>
                                <div><small>Total</small><b><?= portal_money($ticket['total']) ?></b></div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    <div class="pagination">
                        <a class="<?= $page <= 1 ? 'disabled' : '' ?>" href="<?= $page <= 1 ? '#' : portal_page_url($page - 1) ?>">Anterior</a>
                        <span>Pagina <?= $page ?> de <?= $totalPages ?></span>
                        <a class="<?= $page >= $totalPages ? 'disabled' : '' ?>" href="<?= $page >= $totalPages ? '#' : portal_page_url($page + 1) ?>">Siguiente</a>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($view === 'graficas'): ?>
                <section class="portal-card">
                    <div class="card-head">
                        <h3>Panel de graficas</h3><span>Analisis visual</span>
                    </div>
                    <div class="analytics-grid">
                        <div class="analytics-panel">
                            <h4>Estados</h4>
                            <div class="mix-list">
                                <?php foreach ($statusMix as $row): ?>
                                    <div class="mix-row">
                                        <span><?= portal_status_label($row['status']) ?></span>
                                        <strong><?= (int) $row['total'] ?></strong>
                                        <div class="meter"><i style="width: <?= max(((int) $row['total'] / max($maxStatus, 1)) * 100, 4) ?>%"></i></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="analytics-panel">
                            <h4>Vehiculos</h4>
                            <div class="mini-donut-list">
                                <?php foreach ($vehicleMix as $row): ?>
                                    <div><span><?= htmlspecialchars(strtoupper($row['vehicle_type'])) ?></span><strong><?= (int) $row['total'] ?></strong><small><?= portal_money($row['income']) ?></small></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="analytics-panel wide">
                            <h4>Ultimos eventos recibidos</h4>
                            <div class="event-list">
                                <?php foreach ($events as $event): ?>
                                    <div class="event-row">
                                        <span><?= htmlspecialchars($event['event_type']) ?></span>
                                        <strong><?= htmlspecialchars($event['ticket_code']) ?></strong>
                                        <small><?= portal_date($event['created_at']) ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($view === 'usuarios' && $isAdmin): ?>
                <section class="portal-grid">
                    <article class="portal-card">
                        <div class="card-head">
                            <h3>Crear usuario</h3><span>Acceso portal</span>
                        </div>
                        <form method="POST" class="user-form">
                            <input type="hidden" name="action" value="create_user">
                            <label><span>Nombre</span><input type="text" name="name" required></label>
                            <label><span>Usuario</span><input type="text" name="username" required></label>
                            <label><span>Clave</span><input type="password" name="password" required></label>
                            <label><span>Rol</span><select name="role">
                                    <option value="viewer">Consulta</option>
                                    <option value="admin">Admin</option>
                                </select></label>
                            <button class="button button-full" type="submit">Guardar usuario</button>
                        </form>
                    </article>
                    <article class="portal-card">
                        <div class="card-head">
                            <h3>Usuarios</h3><span><?= count($users) ?> cuentas</span>
                        </div>
                        <div class="user-list">
                            <?php foreach ($users as $portalUser): ?>
                                <div class="user-row">
                                    <div><strong><?= htmlspecialchars($portalUser['name']) ?></strong><span><?= htmlspecialchars($portalUser['username']) ?> · <?= htmlspecialchars($portalUser['role']) ?></span></div>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="toggle_user">
                                        <input type="hidden" name="user_id" value="<?= (int) $portalUser['id'] ?>">
                                        <button class="mini-button" type="submit" <?= (int) $portalUser['id'] === (int) $user['id'] ? 'disabled' : '' ?>>
                                            <?= (int) $portalUser['active'] === 1 ? 'Activo' : 'Inactivo' ?>
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </article>
                </section>
            <?php endif; ?>
        </main>
    </div>

    <nav class="mobile-tabbar">
        <?php foreach ($nav as $key => $item): ?>
            <a href="?view=<?= $key ?>" class="<?= $view === $key ? 'active' : '' ?>"><span class="nav-icon"><?= portal_icon($item['icon']) ?></span><?= htmlspecialchars($item['label']) ?></a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-user">
        <strong><?= htmlspecialchars($user['name']) ?></strong>
        <span><?= htmlspecialchars($user['role']) ?></span>
        <a href="logout.php">Salir</a>
    </div>
</body>

</html>