<?php
/**
 * config.php
 * Fungsi bersama: session, penyimpanan JSON, sanitasi, flash message, CSRF.
 */

declare(strict_types=1);

// --- Session aman ---
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,      // cookie tidak bisa dibaca JavaScript
        'samesite' => 'Lax',
    ]);
    session_start();
}

define('USERS_FILE', __DIR__ . '/data/users.json');

/* ---------- Sanitasi ---------- */

/** Sanitasi input teks (hapus spasi tepi + htmlspecialchars). */
function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/** Escape output ke HTML. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/* ---------- Penyimpanan JSON ---------- */

/** Baca semua user dari users.json. */
function getUsers(): array
{
    if (!file_exists(USERS_FILE)) {
        file_put_contents(USERS_FILE, '[]');
    }
    $data = json_decode((string) file_get_contents(USERS_FILE), true);
    return is_array($data) ? $data : [];
}

/** Simpan array user ke users.json (dengan file lock). */
function saveUsers(array $users): bool
{
    $json = json_encode(array_values($users), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents(USERS_FILE, $json, LOCK_EX) !== false;
}

/** Cari user berdasarkan email (case-insensitive). */
function findUserByEmail(string $email): ?array
{
    foreach (getUsers() as $user) {
        if (strcasecmp($user['email'], $email) === 0) {
            return $user;
        }
    }
    return null;
}

/* ---------- Auth helper ---------- */

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

/** Redirect ke login jika belum login. */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlash('error', 'Silakan login terlebih dahulu.');
        redirect('login.php');
    }
}

/** Redirect ke dashboard jika sudah login (untuk halaman login/register). */
function requireGuest(): void
{
    if (isLoggedIn()) {
        redirect('dashboard.php');
    }
}

function redirect(string $url): never
{
    header("Location: $url");
    exit;
}

/* ---------- Flash message ---------- */

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/* ---------- CSRF ---------- */

function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verifyCsrf(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}
