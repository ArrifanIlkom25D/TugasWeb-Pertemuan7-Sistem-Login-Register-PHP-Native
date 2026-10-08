<?php
require_once __DIR__ . '/config.php';
requireGuest();

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        $errors[] = 'Sesi form tidak valid. Muat ulang halaman.';
    }

    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $emailBersih = html_entity_decode($email, ENT_QUOTES, 'UTF-8');

    if ($emailBersih === '' || $password === '') {
        $errors[] = 'Email dan password wajib diisi.';
    } elseif (!filter_var($emailBersih, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    if (empty($errors)) {
        $user = findUserByEmail($emailBersih);

        // Pesan error sengaja digabung agar tidak membocorkan email mana yang terdaftar
        if ($user === null || !password_verify($password, $user['password'])) {
            $errors[] = 'Email atau password salah.';
        } else {
            session_regenerate_id(true);   // cegah session fixation
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_nama']  = $user['nama'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['login_time'] = time();

            setFlash('success', 'Login berhasil. Selamat datang, ' . $user['nama'] . '!');
            redirect('dashboard.php');
        }
    }
}

$flash     = getFlash();
$pageTitle = 'Login';
require __DIR__ . '/partials/header.php';
?>
<h1>Masuk</h1>
<p class="sub">Login untuk membuka dashboard.</p>

<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>

<?php if ($errors): ?>
    <div class="alert alert-error" role="alert">
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="login.php" novalidate>
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?= $email ?>" autocomplete="email" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" autocomplete="current-password" required>

    <button type="submit">Login</button>
</form>

<p class="switch">Belum punya akun? <a href="register.php">Daftar</a></p>
<?php require __DIR__ . '/partials/footer.php'; ?>
