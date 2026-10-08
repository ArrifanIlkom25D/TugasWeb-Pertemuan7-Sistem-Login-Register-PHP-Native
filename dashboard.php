<?php
require_once __DIR__ . '/config.php';
requireLogin();   // proteksi halaman: redirect ke login jika belum login

$flash     = getFlash();
$pageTitle = 'Dashboard';
$since     = date('d M Y, H:i', $_SESSION['login_time'] ?? time());
require __DIR__ . '/partials/header.php';
?>
<h1>Halo, <?= e($_SESSION['user_nama']) ?></h1>
<p class="sub">Kamu berhasil login.</p>

<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>

<dl class="profile">
    <dt>Nama</dt>  <dd><?= e($_SESSION['user_nama']) ?></dd>
    <dt>Email</dt> <dd><?= e($_SESSION['user_email']) ?></dd>
    <dt>Login sejak</dt> <dd><?= e($since) ?></dd>
</dl>

<a class="btn-link" href="logout.php">Logout</a>
<?php require __DIR__ . '/partials/footer.php'; ?>
