<?php
// Halaman Login
require_once __DIR__ . '/config/helper.php';

// Jika sudah login, langsung ke dashboard
if (sudah_login()) {
    redirect('dashboard.php');
}

$flash = get_flash();
// Isi otomatis username dari cookie "Remember Me"
$username_cookie = $_COOKIE[COOKIE_REMEMBER] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Perpus Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="login-wrapper d-flex align-items-center justify-content-center p-3">
    <div class="card login-card w-100" style="max-width: 420px;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <span class="login-icon mb-3"><i class="bi bi-journal-bookmark-fill"></i></span>
                <h4 class="fw-semibold mb-1">Perpus Digital</h4>
                <p class="text-muted small mb-0">Silakan login untuk mengelola koleksi buku</p>
            </div>

            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['tipe']) ?> py-2 small"><?= e($flash['pesan']) ?></div>
            <?php endif; ?>

            <form action="proses_login.php" method="POST" autocomplete="off">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?= e($username_cookie) ?>" placeholder="Masukkan username" required autofocus>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Masukkan password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-label="Tampilkan password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                           <?= $username_cookie !== '' ? 'checked' : '' ?>>
                    <label class="form-check-label small" for="remember">Remember Me (ingat username saya)</label>
                </div>
                <button type="submit" class="btn btn-brand w-100 py-2"><i class="bi bi-box-arrow-in-right me-1"></i>Login</button>
            </form>

            <p class="text-center text-muted small mt-4 mb-0">Akun demo: <code>admin</code> / <code>admin123</code></p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
<?php require __DIR__ . '/includes/footer.php'; ?>

</html>
