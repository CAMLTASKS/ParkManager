<?php

require_once __DIR__ . '/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('parkmanager_portal');
    session_start();
}

function portal_current_user(): ?array
{
    if (empty($_SESSION['portal_user_id'])) {
        return null;
    }

    $stmt = portal_pdo()->prepare('SELECT * FROM portal_users WHERE id = :id AND active = 1 LIMIT 1');
    $stmt->execute(['id' => (int) $_SESSION['portal_user_id']]);
    $user = $stmt->fetch();

    if (! $user) {
        unset($_SESSION['portal_user_id']);
        return null;
    }

    return $user;
}

function portal_require_login(): array
{
    $user = portal_current_user();
    if (! $user) {
        header('Location: login.php');
        exit;
    }

    return $user;
}

function portal_require_admin(array $user): void
{
    if (($user['role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('No autorizado.');
    }
}

function portal_login(string $username, string $password): bool
{
    $stmt = portal_pdo()->prepare('SELECT * FROM portal_users WHERE username = :username AND active = 1 LIMIT 1');
    $stmt->execute(['username' => trim($username)]);
    $user = $stmt->fetch();

    if (! $user || ! hash_equals((string) $user['password_hash'], hash('sha256', $password))) {
        return false;
    }

    $_SESSION['portal_user_id'] = (int) $user['id'];
    $stmt = portal_pdo()->prepare('UPDATE portal_users SET last_login_at = NOW(), updated_at = NOW() WHERE id = :id');
    $stmt->execute(['id' => (int) $user['id']]);

    return true;
}

function portal_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
