# 📚 Mini Digital Library

Aplikasi web **Sistem Informasi Perpustakaan Digital Mini** untuk mengelola data buku (CRUD) dengan autentikasi, session & cookie, serta upload sampul buku.
Dibuat dengan **PHP Native** (tanpa framework), **MySQL (mysqli)**, **Bootstrap 5**, dan **SweetAlert2**.

> Tugas Terstruktur — Mata Kuliah **Pemrograman Web** — Program Studi **Sistem Informasi**
> Nama: `<isi nama>` | NIM: `<isi NIM>` | Kelas: `<2C>`

- 🎥 **Video Demo (YouTube):** `<tempel link video di sini>`

---

## ✨ Fitur

| Modul | Fitur |
|---|---|
| **Autentikasi** | Login (POST), password di-hash (`password_hash`/`password_verify`), session, proteksi halaman, CSRF token |
| **Cookie** | "Remember Me" menyimpan username di cookie 30 hari (HttpOnly) |
| **Logout** | Menghapus session & cookie, redirect ke halaman login |
| **CRUD Buku** | Tambah, tampil (tabel + pencarian & filter kategori via GET), edit, hapus |
| **Upload Sampul** | Validasi ekstensi (.jpg/.png), MIME asli, ukuran maks **2 MB**, nama file acak; saat edit file lama diganti, saat hapus file fisik ikut di-`unlink` |
| **Logika Bisnis** | Stok > 0 → **Tersedia**, stok 0 → **Dipinjam** (if-else pada `label_status()`) |
| **Keamanan** | Prepared statement (anti SQL Injection), `htmlspecialchars` (anti XSS), hapus hanya via POST + CSRF |

## 🗂️ Struktur Project

```
tugas-project_library/
├── config/
│   ├── koneksi.php          # Koneksi database MySQL (mysqli)
│   └── helper.php           # Session, flash, CSRF, label status, validasi & upload file
├── includes/
│   ├── header.php           # Navbar + flash message (halaman setelah login)
│   ├── footer.php           # Script JS & footer
│   └── form_buku.php        # Field form bersama (tambah & edit)
├── assets/
│   ├── css/style.css
    ├── img
│   └── js/app.js            # Konfirmasi hapus, preview gambar, toggle password
├── uploads/                 # Folder penampung file sampul buku
├── index.php                # Halaman Login
├── proses_login.php         # Verifikasi autentikasi & Session/Cookie
├── logout.php               # Hapus Session & Cookie
├── dashboard.php            # Tampil Data (Read) & akses menu
├── tambah_buku.php          # Form Tambah Data
├── proses_tambah.php        # Query Insert & upload file
├── edit_buku.php            # Form Edit Data
├── proses_edit.php          # Query Update & penggantian file
├── hapus_buku.php           # Query Delete & unlink file gambar
└── db_perpustakaan.sql      # Ekspor database
```

## ⚙️ Cara Menjalankan

1. Install **XAMPP / Laragon** (PHP 8+, MySQL/MariaDB). Jalankan Apache & MySQL.
2. Clone repositori ke folder web server:
   ```bash
   git clone <url-repositori> C:/xampp/htdocs/project_perpustakaan
   ```
3. Buka **phpMyAdmin** → **Import** → pilih `db_perpustakaan.sql`. Database yang dipakai: `data_perpus` (tabel `tb_buku` dan `tb_user`).
4. Sesuaikan kredensial di `config/koneksi.php` bila perlu (default: user `root`, password kosong).
5. Pastikan folder `uploads/` dapat ditulis (Linux/Mac: `chmod 755 uploads`).
6. Buka `http://localhost/project_perpustakaan/`.

**Akun demo:** username `admin` — password `admin123`

## 🗄️ Struktur Tabel Database

### `tb_user`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT UNSIGNED, PK, AUTO_INCREMENT | |
| username | VARCHAR(50), UNIQUE | |
| password | VARCHAR(255) | hash bcrypt |
| nama_lengkap | VARCHAR(100) | |

### `tb_buku`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT, PK, AUTO_INCREMENT | |
| judul_buku | VARCHAR(150) | |
| kategori | VARCHAR(50) | |
| penulis | VARCHAR(100) | |
| tahun_terbit | INT | |
| status_ketersediaan | INT | jumlah stok: > 0 = Tersedia, 0 = Dipinjam |
| sampul | VARCHAR(255) NULL | nama file di `uploads/` |

## 📸 Screenshot

> Ganti placeholder di bawah dengan screenshot asli (simpan di folder `screenshots/`).

| Halaman | Screenshot |
|---|---|
| Login | ![Login](screenshots/01-login.png) |
| Login gagal / validasi | ![Login Gagal](screenshots/02-login-gagal.png) |
| Dashboard (Read) | ![Dashboard](screenshots/03-dashboard.png) |
| Tambah Buku (Create) | ![Tambah](screenshots/04-tambah.png) |
| Validasi Upload (file > 2MB / bukan gambar) | ![Validasi Upload](screenshots/05-validasi-upload.png) |
| Edit Buku (Update) | ![Edit](screenshots/06-edit.png) |
| Konfirmasi Hapus (Delete) | ![Hapus](screenshots/07-hapus.png) |
| Pencarian & Filter | ![Cari](screenshots/08-cari.png) |

## 👤 Pembuat
`<Nama>` — `<NIM>`
