<?php
// Form Edit Data Buku
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/helper.php';
wajib_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    set_flash('danger', 'ID buku tidak valid.');
    redirect('dashboard.php');
}

$stmt = $koneksi->prepare('SELECT * FROM tb_buku WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$buku = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$buku) {
    set_flash('danger', 'Data buku tidak ditemukan.');
    redirect('dashboard.php');
}

// Jika validasi gagal sebelumnya, tampilkan kembali input pengguna
$data = $_SESSION['old'] ?? $buku;
unset($_SESSION['old']);
$sampul_lama = $buku['sampul'];

$judul_halaman = 'Edit Buku';
require __DIR__ . '/includes/header.php';
?>

<div class="card form-card">
    <div class="card-body p-4">
        <h5 class="fw-semibold mb-4"><i class="bi bi-pencil-square me-2"></i>Edit Data Buku</h5>
        <form action="proses_edit.php" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $buku['id'] ?>">
            <?php require __DIR__ . '/includes/form_buku.php'; ?>
            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-brand"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
                <a href="dashboard.php" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
