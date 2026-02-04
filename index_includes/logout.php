<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

$configPath = __DIR__ . '/config.php';
if (file_exists($configPath)) {
    require $configPath;
    init_session();
} else {
    // Fallback if config doesn't exist yet
    session_start();
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires'  => time() - 42000,
        'path'     => $params['path'] ?? '/',
        'domain'   => $params['domain'] ?? '',
        'secure'   => $params['secure'] ?? false,
        'httponly' => $params['httponly'] ?? true,
        'samesite' => $params['samesite'] ?? 'Lax',
    ]);
}
session_destroy();

header('Location: user_login.php', true, 302);
exit
?>