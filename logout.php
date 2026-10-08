<?php
require_once __DIR__ . '/config.php';

// Kosongkan data session, hapus cookie, lalu hancurkan session
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

session_destroy();

// Mulai session baru hanya untuk menampilkan pesan sukses
session_start();
setFlash('success', 'Kamu sudah logout.');
redirect('login.php');
