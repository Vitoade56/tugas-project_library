<?php
// Query UPDATE + penggantian file sampul
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/helper.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    set_flash('danger', 'ID buku tidak valid.');
    redirect('dashboard.php');
}
if (!csrf_valid()) {
    set_flash('danger', 'Sesi form tidak valid. Silakan coba lagi.');
    redirect('edit_buku.php?id=' . $id);
}

// Ambil data lama (untuk mengetahui nama file sampul lama)
$stmt = $koneksi->prepare('SELECT sampul FROM tb_buku WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$lama = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$lama) {
    set_flash('danger', 'Data buku tidak ditemukan.');
    redirect('dashboard.php');
}

$data  = ambil_input_buku();
$error = validasi_buku($data);

// ---- Upload sampul baru (opsional) ----
$sampulBaru = null;
$ada_file   = isset($_FILES['sampul']) && $_FILES['sampul']['error'] !== UPLOAD_ERR_NO_FILE;

if ($ada_file && !$error) {
    [$sampulBaru, $errUpload] = upload_sampul($_FILES['sampul']);
    if ($errUpload) {
        $error[] = $errUpload;
    }
} elseif ($ada_file && $_FILES['sampul']['size'] > MAX_UKURAN_FILE) {
    $error[] = 'Ukuran file melebihi batas maksimal 2 MB.';
}

if ($error) {
    $_SESSION['old'] = $data;
    set_flash('danger', implode(' ', $error));
    redirect('edit_buku.php?id=' . $id);
}

// ---- UPDATE ----
$namaSampul = $sampulBaru ?? $lama['sampul']; // pakai file lama jika tidak ada upload baru
$tahun = (int) $data['tahun_terbit'];
$stok  = (int) $data['status_ketersediaan'];

try {
    $stmt = $koneksi->prepare('UPDATE tb_buku SET judul_buku = ?, kategori = ?, penulis = ?, tahun_terbit = ?, status_ketersediaan = ?, sampul = ? WHERE id = ?');
    $stmt->bind_param('sssiisi', $data['judul_buku'], $data['kategori'], $data['penulis'], $tahun, $stok, $namaSampul, $id);
    $stmt->execute();
    $stmt->close();
} catch (mysqli_sql_exception $ex) {
    hapus_sampul($sampulBaru); // batalkan file baru jika query gagal
    $_SESSION['old'] = $data;
    set_flash('danger', 'Gagal memperbarui data di database.');
    redirect('edit_buku.php?id=' . $id);
}

// Query sukses -> hapus file sampul lama dari server (jika diganti)
if ($sampulBaru !== null && $lama['sampul']) {
    hapus_sampul($lama['sampul']);
}

set_flash('success', 'Data buku "' . $data['judul_buku'] . '" berhasil diperbarui.');
redirect('dashboard.php');
