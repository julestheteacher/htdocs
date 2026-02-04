<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

init_session();

require_once __DIR__ . '/utils.php';

// If already authenticated, redirect to protected page.
if (!empty($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    header('Location: ../index.php', true, 302);
    exit();
}

// Retrieve any flash message
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 2rem;}
    form {max-width: 420px;}
    .notice {color: #b00020; margin-bottom: 1rem;}
    label {display:block; margin: 0.5rem 0;}
    input[type="password"] {width:100%; padding:0.5rem;}
    button {padding:0.5rem 0.75rem;}
  </style>
</head>
<body>
  <h1><?= htmlspecialchars(SITE_NAME) ?></h1>
  <p>Please enter the current access password to continue.</p>



  <form action="authenticate.php" method="" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
    <label for="password">Access password</label>
    <input type="password" id="password" name="password" autocomplete="current-password" required>
    <button type="submit">Enter</button>
  </form>
</</body>
