<?php
// Query INSERT + operasi upload file sampul
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/helper.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('tambah_buku.php');
}
if (!csrf_valid()) {
    set_flash('danger', 'Sesi form tidak valid. Silakan coba lagi.');
    redirect('tambah_buku.php');
}

$data  = ambil_input_buku();
$error = validasi_buku($data);

// ---- Proses upload (wajib untuk data baru) ----
$namaSampul = null;
$ada_file   = isset($_FILES['sampul']) && $_FILES['sampul']['error'] !== UPLOAD_ERR_NO_FILE;

if (!$ada_file) {
    $error[] = 'Sampul buku wajib diupload.';
} elseif (!$error) {
    [$namaSampul, $errUpload] = upload_sampul($_FILES['sampul']);
    if ($errUpload) {
        $error[] = $errUpload;
    }
} else {
    // Ada error teks: tetap validasi file agar pesan error lengkap tanpa menyimpan
    $cek = $_FILES['sampul'];
    if ($cek['error'] === UPLOAD_ERR_OK && $cek['size'] > MAX_UKURAN_FILE) {
        $error[] = 'Ukuran file melebihi batas maksimal 2 MB.';
    }
}

if ($error) {
    $_SESSION['old'] = $data;
    set_flash('danger', implode(' ', $error));
    redirect('tambah_buku.php');
}

// ---- INSERT (prepared statement) ----
try {
    $stmt = $koneksi->prepare('INSERT INTO tb_buku (judul_buku, kategori, penulis, tahun_terbit, status_ketersediaan, sampul) VALUES (?, ?, ?, ?, ?, ?)');
    $tahun = (int) $data['tahun_terbit'];
    $stok  = (int) $data['status_ketersediaan'];
    $stmt->bind_param('sssiis', $data['judul_buku'], $data['kategori'], $data['penulis'], $tahun, $stok, $namaSampul);
    $stmt->execute();
    $stmt->close();
} catch (mysqli_sql_exception $ex) {
    hapus_sampul($namaSampul); // rollback file jika query gagal
    $_SESSION['old'] = $data;
    set_flash('danger', 'Gagal menyimpan data ke database.');
    redirect('tambah_buku.php');
}

set_flash('success', 'Buku "' . $data['judul_buku'] . '" berhasil ditambahkan.');
redirect('dashboard.php');
