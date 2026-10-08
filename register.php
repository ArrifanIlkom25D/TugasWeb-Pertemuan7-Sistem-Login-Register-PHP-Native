<?php
require_once __DIR__ . '/config.php';
requireGuest();

$errors = [];
$nama   = '';
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        $errors[] = 'Sesi form tidak valid. Muat ulang halaman.';
    }

    // Sanitasi input
    $nama     = sanitize($_POST['nama'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';       // password tidak di-escape, cukup di-hash
    $konfirm  = $_POST['konfirmasi'] ?? '';

    // Validasi nama
    if ($nama === '') {
        $errors[] = 'Nama wajib diisi.';
    } elseif (mb_strlen($nama) < 3) {
        $errors[] = 'Nama minimal 3 karakter.';
    }

    // Validasi email dengan filter_var()
    $emailBersih = html_entity_decode($email, ENT_QUOTES, 'UTF-8');
    if ($emailBersih === '') {
        $errors[] = 'Email wajib diisi.';
    } elseif (!filter_var($emailBersih, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    // Validasi password
    if ($password === '') {
        $errors[] = 'Password wajib diisi.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password minimal 8 karakter.';
    } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        $errors[] = 'Password harus mengandung huruf dan angka.';
    }
    if ($password !== $konfirm) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    }

    // Cek duplikasi email
    if (empty($errors) && findUserByEmail($emailBersih) !== null) {
        $errors[] = 'Email sudah terdaftar. Gunakan email lain atau login.';
    }

    // Simpan ke users.json
    if (empty($errors)) {
        $users   = getUsers();
        $users[] = [
            'id'         => uniqid('u_', true),
            'nama'       => $nama,
            'email'      => $emailBersih,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if (saveUsers($users)) {
            setFlash('success', 'Registrasi berhasil! Silakan login.');
            redirect('login.php');
        }
        $errors[] = 'Gagal menyimpan data. Periksa izin folder data/.';
    }
}

$pageTitle = 'Daftar';
require __DIR__ . '/partials/header.php';
?>
<h1>Buat akun</h1>
<p class="sub">Daftar untuk mengakses dashboard.</p>

<?php if ($errors): ?>
    <div class="alert alert-error" role="alert">
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="register.php" novalidate>
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">

    <label for="nama">Nama lengkap</label>
    <input type="text" id="nama" name="nama" value="<?= $nama ?>" autocomplete="name" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?= $email ?>" autocomplete="email" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" autocomplete="new-password" required>
    <small>Minimal 8 karakter, kombinasi huruf dan angka.</small>

    <label for="konfirmasi">Konfirmasi password</label>
    <input type="password" id="konfirmasi" name="konfirmasi" autocomplete="new-password" required>

    <button type="submit">Daftar</button>
</form>

<p class="switch">Sudah punya akun? <a href="login.php">Login</a></p>
<?php require __DIR__ . '/partials/footer.php'; ?>
