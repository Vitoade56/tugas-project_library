<?php
// Query DELETE + unlink file gambar sampul
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/helper.php';
wajib_login();

// Penghapusan hanya lewat POST + token CSRF (tidak bisa dipicu lewat link biasa)
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid()) {
    set_flash('danger', 'Permintaan hapus tidak valid.');
    redirect('dashboard.php');
}

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    set_flash('danger', 'ID buku tidak valid.');
    redirect('dashboard.php');
}

// Ambil nama file sampul sebelum record dihapus
$stmt = $koneksi->prepare('SELECT judul_buku, sampul FROM tb_buku WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$buku = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$buku) {
    set_flash('danger', 'Data buku tidak ditemukan.');
    redirect('dashboard.php');
}

try {
    $stmt = $koneksi->prepare('DELETE FROM tb_buku WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
} catch (mysqli_sql_exception $ex) {
    set_flash('danger', 'Gagal menghapus data dari database.');
    redirect('dashboard.php');
}

// Hapus berkas gambar dari folder uploads/
hapus_sampul($buku['sampul']);

set_flash('success', 'Buku "' . $buku['judul_buku'] . '" beserta file sampulnya berhasil dihapus.');
redirect('dashboard.php');
