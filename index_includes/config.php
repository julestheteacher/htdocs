<?php
declare(strict_types=1);

/**
 * Auto-generated config.php.
 * Rotate password via admin_set_password.php; PASSWORD_EPOCH updates too.
 */

const SITE_NAME = 'Digital Access Portal';
const PASSWORD_HASH = '$argon2id$v=19$m=65536,t=4,p=1$YXltNkYxNk9ja0NsTUFEbA$rZ24YROra5qlpLNM60UgNByTywt9z7D0P3DFKNQaapI';
const PASSWORD_EPOCH = 1769439756;

const SESSION_NAME     = 'digital_access_sess';
const SESSION_LIFETIME = 60 * 60 * 2; // 2 hours

function init_session(): void {
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',
        'domain'   => '',
        'secure'   => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name(SESSION_NAME);
    session_start();
}