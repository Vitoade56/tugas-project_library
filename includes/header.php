<?php
/** Header bersama (hanya untuk halaman setelah login). Butuh $judul_halaman. */
require_once __DIR__ . '/../config/helper.php';
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul_halaman ?? 'Mini Digital Library') ?> | Mini Digital Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-brand-custom shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="dashboard.php"><i class="bi bi-journal-bookmark-fill me-2"></i>Mini Digital Library</a>
        <div class="ms-auto d-flex align-items-center text-white">
            <span class="me-3 d-none d-sm-inline"><i class="bi bi-person-circle me-1"></i><?= e($_SESSION['nama_lengkap'] ?? '') ?></span>
            <a href="logout.php" class="btn btn-sm btn-light"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
        </div>
    </div>
</nav>
<main class="container py-4">
<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['tipe']) ?> alert-dismissible fade show" role="alert">
        <?= e($flash['pesan']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>
