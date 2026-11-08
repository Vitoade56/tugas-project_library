<?php
// Form Tambah Data Buku
require_once __DIR__ . '/config/helper.php';
wajib_login();

// Pulihkan input lama jika validasi gagal di proses_tambah.php
$data = $_SESSION['old'] ?? ['judul_buku' => '', 'kategori' => '', 'penulis' => '', 'tahun_terbit' => date('Y'), 'status_ketersediaan' => 1];
unset($_SESSION['old']);

$judul_halaman = 'Tambah Buku';
require __DIR__ . '/includes/header.php';
?>

<div class="card form-card">
    <div class="card-body p-4">
        <h5 class="fw-semibold mb-4"><i class="bi bi-plus-circle me-2"></i>Tambah Buku Baru</h5>
        <form action="proses_tambah.php" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <?php require __DIR__ . '/includes/form_buku.php'; ?>
            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-brand"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="dashboard.php" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
