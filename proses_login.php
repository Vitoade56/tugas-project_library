<?php
// Verifikasi autentikasi + pembuatan Session & Cookie
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/helper.php';

// Hanya terima metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

if (!csrf_valid()) {
    set_flash('danger', 'Sesi form tidak valid. Silakan coba lagi.');
    redirect('index.php');
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

if ($username === '' || $password === '') {
    set_flash('danger', 'Username dan password wajib diisi.');
    redirect('index.php');
}

// Prepared statement -> aman dari SQL Injection
$stmt = $koneksi->prepare('SELECT id, username, password, nama_lengkap FROM tb_user WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($user && password_verify($password, $user['password'])) {
    // Login berhasil: buat ID session baru (cegah session fixation)
    session_regenerate_id(true);
    $_SESSION['user_id']      = $user['id'];
    $_SESSION['username']     = $user['username'];
    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];

    // Fitur Remember Me: simpan username di cookie selama 30 hari
    if ($remember) {
        setcookie(COOKIE_REMEMBER, $user['username'], [
            'expires'  => time() + (30 * 24 * 60 * 60),
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    } else {
        // Jika tidak dicentang, hapus cookie lama bila ada
        setcookie(COOKIE_REMEMBER, '', time() - 3600, '/');
    }

    set_flash('success', 'Selamat datang, ' . $user['nama_lengkap'] . '!');
    redirect('dashboard.php');
}

set_flash('danger', 'Username atau password salah.');
redirect('index.php');
