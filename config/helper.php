<?php
/**
 * Fungsi bantu: session, flash message, CSRF, proteksi halaman,
 * label status ketersediaan, serta validasi & penanganan upload file.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------- Konstanta ----------
const UPLOAD_DIR      = __DIR__ . '/../uploads/';
const MAX_UKURAN_FILE = 2 * 1024 * 1024; // 2 MB
const EKSTENSI_BOLEH  = ['jpg', 'jpeg', 'png'];
const MIME_BOLEH      = ['image/jpeg', 'image/png'];
const COOKIE_REMEMBER = 'remember_username';

// ---------- Umum ----------
function e($teks)
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

// ---------- Flash message ----------
function set_flash($tipe, $pesan)
{
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}

function get_flash()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ---------- Autentikasi ----------
function sudah_login()
{
    return isset($_SESSION['user_id']);
}

/** Dipanggil di setiap halaman yang wajib login. */
function wajib_login()
{
    if (!sudah_login()) {
        set_flash('warning', 'Silakan login terlebih dahulu untuk mengakses halaman tersebut.');
        redirect('index.php');
    }
}

// ---------- CSRF ----------
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_valid()
{
    $kirim = $_POST['csrf_token'] ?? '';
    return is_string($kirim) && hash_equals($_SESSION['csrf_token'] ?? '', $kirim);
}

// ---------- Logika bisnis: status ketersediaan ----------
/**
 * Menentukan label status berdasarkan stok (percabangan if-else).
 * Stok > 0 => "Tersedia", stok 0 => "Dipinjam".
 */
function label_status($stok)
{
    $stok = (int) $stok;
    if ($stok > 0) {
        return ['label' => 'Tersedia', 'kelas' => 'success'];
    } else {
        return ['label' => 'Dipinjam', 'kelas' => 'danger'];
    }
}

// ---------- Upload file ----------
/**
 * Validasi & simpan file sampul.
 * @return array [nama_file|null, pesan_error|null]
 */
function upload_sampul(array $file)
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return [null, 'Ukuran file melebihi batas maksimal 2 MB.'];
            case UPLOAD_ERR_PARTIAL:
                return [null, 'File hanya terupload sebagian. Silakan coba lagi.'];
            default:
                return [null, 'Terjadi kesalahan saat upload file (kode ' . $file['error'] . ').'];
        }
    }

    // 1) Validasi ukuran
    if ($file['size'] > MAX_UKURAN_FILE) {
        return [null, 'Ukuran file melebihi batas maksimal 2 MB.'];
    }

    // 2) Validasi ekstensi
    $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ekstensi, EKSTENSI_BOLEH, true)) {
        return [null, 'Format file tidak diizinkan. Hanya .jpg, .jpeg, atau .png.'];
    }

    // 3) Validasi tipe MIME sebenarnya (bukan hanya ekstensi)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!in_array($mime, MIME_BOLEH, true)) {
        return [null, 'Isi file bukan gambar JPG/PNG yang valid.'];
    }

    // 4) Pastikan benar-benar gambar
    if (@getimagesize($file['tmp_name']) === false) {
        return [null, 'File bukan gambar yang valid.'];
    }

    // 5) Nama acak agar unik & aman
    $namaBaru = 'sampul_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ekstensi;

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $namaBaru)) {
        return [null, 'Gagal menyimpan file ke folder uploads.'];
    }
    return [$namaBaru, null];
}

/** Hapus berkas sampul fisik dari folder uploads. */
function hapus_sampul($namaFile)
{
    if (!$namaFile) {
        return;
    }
    $path = UPLOAD_DIR . basename($namaFile); // basename() mencegah path traversal
    if (is_file($path)) {
        unlink($path);
    }
}

/** Validasi data teks buku. @return array daftar error */
function validasi_buku(array $d)
{
    $error = [];
    if ($d['judul_buku'] === '')    $error[] = 'Judul buku wajib diisi.';
    if ($d['kategori'] === '') $error[] = 'Kategori wajib diisi.';
    if ($d['penulis'] === '')  $error[] = 'Penulis wajib diisi.';
    if (!ctype_digit((string) $d['tahun_terbit']) || $d['tahun_terbit'] < 1000 || $d['tahun_terbit'] > (int) date('Y') + 1) {
        $error[] = 'Tahun terbit tidak valid.';
    }
    if (!ctype_digit((string) $d['status_ketersediaan'])) {
        $error[] = 'Stok harus berupa angka bulat (0 atau lebih).';
    }
    if (mb_strlen($d['judul_buku']) > 150) $error[] = 'Judul maksimal 150 karakter.';
    return $error;
}

/** Ambil & bersihkan input buku dari $_POST. */
function ambil_input_buku()
{
    return [
        'judul_buku'        => trim($_POST['judul_buku'] ?? ''),
        'kategori'     => trim($_POST['kategori'] ?? ''),
        'penulis'      => trim($_POST['penulis'] ?? ''),
        'tahun_terbit' => trim($_POST['tahun_terbit'] ?? ''),
        'status_ketersediaan'         => trim($_POST['status_ketersediaan'] ?? ''),
    ];
}

/** Daftar kategori (array) untuk dropdown. */
function daftar_kategori()
{
    return ['Novel', 'Sejarah', 'Filsafat', 'Dongeng', 'Akademik', 'Pemrograman', 'Sains', 'Lainnya'];
}
