<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

$configPath = ($_SERVER['DOCUMENT_ROOT'].'/index_includes/config.php');
if (!file_exists($configPath)) {
    echo "<!doctype html><html><head><meta charset='utf-8'><title>Setup required</title></head><body>";
    echo "<h1>Setup required</h1>";
    echo "<p><code>config.php</code> not found. Please ask an admin to run <code>admin_set_password.php</code> first.</p>";
    echo "</body></html>";
    print $configPath;
    exit();
}

require $configPath;
//print $configPath;
init_session();

// Do NOT auto-redirect when already authenticated
$already = !empty($_SESSION['authenticated']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = isset($_POST['password']) && is_string($_POST['password']) ? trim($_POST['password']) : '';

    if ($password !== '' && password_verify($password, PASSWORD_HASH)) {
        session_regenerate_id(true);
        $_SESSION['authenticated']           = true;
        $_SESSION['auth_issued_at']          = time();
        $_SESSION['auth_marker']             = bin2hex(random_bytes(16));
        $_SESSION['password_epoch_at_login'] = defined('PASSWORD_EPOCH') ? PASSWORD_EPOCH : 0;

        header('Location: /', true, 302);
        exit();
    }

    // Set flash and redirect back to GET
    $_SESSION['flash'] = 'Incorrect password. Please try again.';
    header('Location: /index_includes/user_login.php', true, 302);

    exit();
}

// GET: show page and flash (if any)
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars(SITE_NAME) ?> — Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root { --border:#ddd; --btn:#0b5ed7; --btnh:#0a53be; --white:#fff; --muted:#555; }
    body{font-family:system-ui;margin:0}
    header{display:flex;justify-content:space-between;align-items:center;padding:.75rem 1rem;border-bottom:1px solid var(--border);background:#fff}
    .brand{font-weight:700}
    .actions a{display:inline-block;padding:.45rem .8rem;border-radius:6px;background:var(--btn);color:var(--white);text-decoration:none}
    .actions a:hover{background:var(--btnh)}
    main{max-width:740px;margin:1.25rem auto;padding:0 1rem}
    .notice{color:#b00020;margin-bottom:1rem;}
    .muted{color:var(--muted)}
    form{max-width:420px}
    label{display:block;margin:.5rem 0}
    input[type="password"]{width:100%;padding:.5rem}
    button{padding:.5rem .75rem}
    .links a{margin-right:.5rem}
  </style>
</head>
<body>
  <header>
 



  </header>
  <main>
    <h1>Login</h1>

    <?php if ($already): ?>
      <p class="muted">You are already authenticated.</p>
      <p class="links">
        <a href="../">Home</a>
        <a href="../index_includes/logout.php">Log out</a>
      </p>
    <?php endif; ?>

    <?php if (!empty($flash)): ?>
      <div class="notice"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>
    
    <?php if (!$already): ?>
      <form action="" method="post" novalidate>
        <label for="password">Access password</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required>
        <p><button type="submit">Enter</button></p>
      </form>
    <?php endif; ?>
  </main>
</body>
