<?php
// 1) Auth gate
if (empty($_SESSION['authenticated'])) {
    deny_and_redirect(('/index_includes/user_login.php'), 2000, 'not authenticated');
}

// 2) Epoch gate (forces re-login after admin rotates password)
$currentEpoch = defined('PASSWORD_EPOCH') ? PASSWORD_EPOCH : 0;
$sessionEpoch = (int)($_SESSION['password_epoch_at_login'] ?? 0);
if ($sessionEpoch !== $currentEpoch) {
    // Optionally clear session to be safe
    $_SESSION = [];
    if (session_id() !== '') { session_destroy(); }

    deny_and_redirect(('/index_includes/user_login.php'), 2000, 'password rotation');
}

// 3) Timeout gate
if (isset($_SESSION['auth_issued_at']) && (time() - $_SESSION['auth_issued_at']) > SESSION_LIFETIME) {
    $_SESSION = [];
    if (session_id() !== '') { session_destroy(); }

    deny_and_redirect('/index_includes/logout.php', 2000, 'session expired');
}
?>