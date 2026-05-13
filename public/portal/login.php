<?php

require __DIR__ . '/auth.php';

if (portal_current_user()) {
    header('Location: index.php');
    exit;
}

$config = portal_config();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (portal_login($_POST['username'] ?? '', $_POST['password'] ?? '')) {
        header('Location: index.php');
        exit;
    }

    $error = 'Usuario o clave incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingreso - <?= htmlspecialchars($config['portal_name']) ?></title>
    <link rel="stylesheet" href="assets/portal.css">
</head>
<body class="portal-login-body">
    <main class="login-shell">
        <section class="login-card">
            <div class="login-brand">
                <div class="brand-mark">P</div>
                <div>
                    <h1><?= htmlspecialchars($config['portal_name']) ?></h1>
                    <p>Portal movil de monitoreo</p>
                </div>
            </div>

            <div class="login-copy">
                <span>Acceso privado</span>
                <h2>Consulta ventas, ocupacion y movimientos desde el celular.</h2>
            </div>

            <?php if ($error): ?>
                <div class="notice error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <label>
                    <span>Usuario</span>
                    <input type="text" name="username" autocomplete="username" autofocus required>
                </label>
                <label>
                    <span>Clave</span>
                    <input type="password" name="password" autocomplete="current-password" required>
                </label>
                <button class="button button-full" type="submit">Entrar al portal</button>
            </form>
        </section>
    </main>
</body>
</html>
