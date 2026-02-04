<?php
declare(strict_types=1);

/**
 * admin_set_password.php
 * Admin page to set/rotate the site password.
 * - On GET: Always show the form (even if user is authenticated).
 * - On POST: Write new hash + bump epoch, invalidate opcache, then redirect to protected page.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

$configPath    = __DIR__ . '/config.php';
$protectedUrl  = 'index.php';

function makeConfig(string $hash, int $epoch): string {
    return <<<PHP
<?php
declare(strict_types=1);

/**
 * Auto-generated config.php.
 * Rotate password via admin_set_password.php; PASSWORD_EPOCH updates too.
 */

const SITE_NAME = 'Digital Access Portal';
const PASSWORD_HASH = '{$hash}';
const PASSWORD_EPOCH = {$epoch};

const SESSION_NAME     = 'digital_access_sess';
const SESSION_LIFETIME = 60 * 60 * 2; // 2 hours

function init_session(): void {
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',
        'domain'   => '',
        'secure'   => !empty(\$_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name(SESSION_NAME);
    session_start();
}
PHP;
}

function writeConfig(string $path, string $hash, int $epoch): void {
    $dir = dirname($path);
    if (!is_dir($dir)) {
        throw new RuntimeException("Directory does not exist: {$dir}");
    }

    if (file_exists($path)) {
        if (!is_writable($path)) {
            throw new RuntimeException("config.php exists but is not writable: {$path}");
        }
        $content = file_get_contents($path);
        if ($content === false) {
            throw new RuntimeException("Failed to read existing config.php");
        }

        $content = preg_replace(
            '/const\s+PASSWORD_HASH\s*=\s*[\'"][^\'"]*[\'"]\s*;/',
            "const PASSWORD_HASH = '{$hash}';",
            $content,
            1
        );

        if (preg_match('/const\s+PASSWORD_EPOCH\s*=\s*\d+\s*;/', $content)) {
            $content = preg_replace(
                '/const\s+PASSWORD_EPOCH\s*=\s*\d+\s*;/',
                "const PASSWORD_EPOCH = {$epoch};",
                $content,
                1
            );
        } else {
            $content .= "\nconst PASSWORD_EPOCH = {$epoch};\n";
        }

        if (file_put_contents($path, $content, LOCK_EX) === false) {
            throw new RuntimeException("Failed to write updated config.php");
        }
    } else {
        if (!is_writable($dir)) {
            throw new RuntimeException("Directory not writable: {$dir}");
        }
        $content = makeConfig($hash, $epoch);
        if (file_put_contents($path, $content, LOCK_EX) === false) {
            throw new RuntimeException("Failed to create config.php");
        }
    }

    if (function_exists('opcache_invalidate')) {
        @opcache_invalidate($path, true);
    }
    if (function_exists('opcache_reset')) {
        @opcache_reset();
    }
}

function makeHash(string $password): string {
    $hash = defined('PASSWORD_ARGON2ID')
      ? password_hash($password, PASSWORD_ARGON2ID)
      : password_hash($password, PASSWORD_DEFAULT);
    if ($hash === false) {
        throw new RuntimeException("Could not generate password hash.");
    }
    return $hash;
}

// If config exists, we can init session to render header info.
if (file_exists($configPath)) {
    require $configPath;
    init_session();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = isset($_POST['password']) && is_string($_POST['password'])
        ? trim($_POST['password'])
        : '';

    if ($password === '') {
        http_response_code(400);
        echo "<h1>Error</h1><p>Password cannot be empty.</p>";
        exit();
    }

    try {
        $hash  = makeHash($password);
        $epoch = time();
        writeConfig($configPath, $hash, $epoch);

        // Make sure session exists after a fresh config
        if (!function_exists('init_session')) {
            require $configPath;
        }
        init_session();
        session_regenerate_id(true);
        $_SESSION['authenticated']           = true;
        $_SESSION['auth_issued_at']          = time();
        $_SESSION['auth_marker']             = bin2hex(random_bytes(16));
        $_SESSION['password_epoch_at_login'] = $epoch;

        header("Location: {$protectedUrl}", true, 302);
        exit();
    } catch (Throwable $e) {
        http_response_code(500);
        echo "<h1>Config write failed</h1>";
        echo "<p>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
        exit();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin — Set/Rotate Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root { --border:#ddd; --btn:#0b5ed7; --btnh:#0a53be; --white:#fff; }
    body{font-family:system-ui;margin:0}
    header{display:flex;justify-content:space-between;align-items:center;padding:.75rem 1rem;border-bottom:1px solid var(--border);background:#fff}
    .brand{font-weight:700}
    .actions a{display:inline-block;padding:.45rem .8rem;border-radius:6px;background:var(--btn);color:var(--white);text-decoration:none}
    .actions a:hover{background:var(--btnh)}
    main{max-width:740px;margin:1.25rem auto;padding:0 1rem}
    form{max-width:480px}
    label{display:block;margin:.5rem 0}
    input{width:100%;padding:.5rem}
    button{padding:.5rem .75rem}
  </style>
</head>
<body>
  <header>
    <div class="brand"><?= isset($configPath) && file_exists($configPath) && defined('SITE_NAME') ? htmlspecialchars(SITE_NAME) : 'Digital Access Portal' ?></div>
    <nav class="actions">
      <a href="user_login.php">User Login</a>
      <a href="logout.php" style="margin-left:.5rem">Logout</a>
    </nav>
  </header>
  <main>
    <h1>Admin — Set/Rotate Password</h1>
    <p>Enter the new site password. This updates the stored hash and forces users to log in again.</p>

    <form method="post" action="">
      <label for="password">New password</label>
      <input type="password" id="password" name="password" autocomplete="new-password" required>
      <p><button type="submit">Save & go to protected page</button></p>
    </form>

    <h3>Tip</h3>
    <p>You can visit this page anytime, even when already logged in.</p>
  </main>
</body>
</html>
