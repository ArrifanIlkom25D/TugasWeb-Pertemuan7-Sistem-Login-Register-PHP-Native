# Sistem Login & Register — PHP Native (JSON)

## Struktur file
```
login-system/
├── index.php          # redirect ke login/dashboard
├── config.php         # session, helper JSON, sanitasi, CSRF, flash
├── register.php       # form + proses registrasi
├── login.php          # form + proses login
├── dashboard.php      # halaman terproteksi
├── logout.php         # session_destroy()
├── style.css
├── partials/
│   ├── header.php
│   └── footer.php
└── data/
    ├── users.json     # penyimpanan user
    └── .htaccess      # blokir akses langsung (Apache)
```

## Cara menjalankan
Butuh PHP 8.0+.

**Opsi 1 — built-in server (paling mudah)**
```
cd login-system
php -S localhost:8000
```
Buka http://localhost:8000

**Opsi 2 — XAMPP**
Salin folder `login-system` ke `C:\xampp\htdocs\`, start Apache, buka
http://localhost/login-system/

## Alur uji
1. Buka /register.php, daftar akun baru.
2. Daftar lagi dengan email yang sama -> muncul error duplikasi.
3. Login -> masuk dashboard.
4. Buka /dashboard.php tanpa login -> diarahkan ke login.
5. Klik Logout -> session dihancurkan.
6. Cek data/users.json -> password berupa hash ($2y$...).
