<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

$configPath = ($_SERVER['DOCUMENT_ROOT'].'/index_includes/config.php');
if (!file_exists($configPath)) {
    echo "<!doctype html><html><head><meta charset='utf-8'><title>Setup required</title></head><body>";
    echo "<h1>Setup required</h1>";
    echo "<p><code>config.php</code> not found. Please run <code>admin_set_password.php</code> first.</p>";
    echo "</body></html>";
    exit();
}

require $configPath;
init_session();

// $safeTo = '/index_includes/user_login.php';
// $delayMs = 2000;
 
function deny_and_redirect(string $to, int $delayMs = 2000, string $reason = ''){
  $safeTo = htmlspecialchars($to, ENT_QUOTES, 'UTF-8');
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Access Denied</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Fallback meta refresh for environments without JS -->
  <meta http-equiv="refresh" content="2;url=<?= $safeTo; ?>">
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 0; display:flex; min-height:100vh; align-items:center; justify-content:center; background:#fff; color:#111; }
    .card { max-width: 640px; margin: 2rem; padding: 1.25rem; border: 1px solid #ddd; border-radius: 8px; background: #fdfdfd; text-align:center; }
    .muted { color: #666; }
    a.btn { display:inline-block; margin-top: .75rem; padding: .45rem .8rem; text-decoration:none; border-radius:6px; background:#0b5ed7; color:#fff; }
    a.btn:hover { background:#0a53be; }
  </style>
  <script>
    // JS redirect after a short delay
    setTimeout(function() {
      window.location.href = "<?= $safeTo; ?>";
    }, {<?= $delayMs; ?>});
  </script>
</head>
<body>
  <div class="card">
    
    <p class="muted">You will be redirected to the login page shortly.</p>
    <p>.</p>
   <?= $safeTo; ?>
    <p>.</p>
  
  </div>
</body>
</html>
<?php
  exit();
  }
?>