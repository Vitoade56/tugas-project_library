<?php
// Hapus Session & Cookie secara aman, lalu kembali ke halaman login
require_once __DIR__ . '/config/helper.php';

// 1) Kosongkan seluruh data session
$_SESSION = [];

// 2) Hapus cookie session (PHPSESSID) di browser
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

// 3) Hapus cookie "Remember Me"
setcookie(COOKIE_REMEMBER, '', time() - 3600, '/');
unset($_COOKIE[COOKIE_REMEMBER]);

// 4) Hancurkan session di server
session_destroy();

// 5) Mulai session baru hanya untuk pesan flash
session_start();
set_flash('success', 'Anda telah berhasil logout.');
redirect('index.php');
