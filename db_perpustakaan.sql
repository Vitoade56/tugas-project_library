-- ============================================================
-- Database : data_perpus  (sesuai database yang sudah dibuat)
-- Aplikasi : Mini Digital Library (PHP Native + MySQL)
-- Catatan  : File ini AMAN diimport ke database yang sudah ada:
--            tabel tb_buku TIDAK dihapus/ditimpa (IF NOT EXISTS),
--            hanya menambah tabel tb_user untuk login.
-- ============================================================

CREATE DATABASE IF NOT EXISTS `data_perpus`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `data_perpus`;

-- ------------------------------------------------------------
-- Tabel tb_buku (sudah ada di database Anda)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tb_buku` (
  `id`                  INT NOT NULL AUTO_INCREMENT,
  `judul_buku`          VARCHAR(150) NOT NULL,
  `kategori`            VARCHAR(50)  NOT NULL,
  `penulis`             VARCHAR(100) NOT NULL,
  `tahun_terbit`        INT          NOT NULL,
  `status_ketersediaan` INT          NOT NULL DEFAULT 0 COMMENT 'jumlah stok: >0 = Tersedia, 0 = Dipinjam',
  `sampul`              VARCHAR(255) DEFAULT NULL COMMENT 'nama file di folder uploads/',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabel tb_user (baru, untuk autentikasi)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tb_user` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`     VARCHAR(50)  NOT NULL,
  `password`     VARCHAR(255) NOT NULL COMMENT 'hash bcrypt (password_hash)',
  `nama_lengkap` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Akun default -> username: admin | password: admin123
INSERT IGNORE INTO `tb_user` (`username`, `password`, `nama_lengkap`) VALUES
('admin', '$2y$10$5ChWO.qTpIWEbkH4tCzT6.WbJ2Q3gZjAXmFg1hwfaIMPPKDVqUpDO', 'Administrator Perpustakaan');
