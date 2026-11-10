<?php
/** Shared session, authorization, and CSRF helpers. */

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function send_security_headers(): void
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function require_login(): void
{
    start_secure_session();
    if (empty($_SESSION['user_id'])) {
        redirect('login.php');
    }
}

function require_role(string $role): void
{
    require_login();
    if (($_SESSION['role'] ?? null) !== $role) {
        redirect('login.php');
    }
}

function csrf_token(): string
{
    start_secure_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function verify_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Metode permintaan tidak diizinkan.');
    }

    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!is_string($submittedToken) || !hash_equals(csrf_token(), $submittedToken)) {
        http_response_code(403);
        exit('Permintaan tidak valid. Silakan muat ulang halaman lalu coba lagi.');
    }
}

function is_valid_date(string $value): bool
{
    $date = DateTime::createFromFormat('Y-m-d', $value);
    return $date !== false && $date->format('Y-m-d') === $value;
}

function user_destination(string $candidate): string
{
    $allowed = [
        'data_anak_pengguna.php',
        'imunisasi_pengguna.php',
        'jadwal_pengguna.php',
        'edukasi_ibu.php',
        'petugas_pengguna.php',
    ];

    return in_array($candidate, $allowed, true) ? $candidate : 'index.php';
}
