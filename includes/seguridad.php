
<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');

    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
        'path' => '/'
    ]);

    session_start();
}

if (empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/csrf.php';

function exigir_rol(string ...$roles): void
{
    if (!in_array($_SESSION['admin_rol'] ?? '', $roles, true)) {
        http_response_code(403);
        exit('No tienes permisos para realizar esta operación.');
    }
}
