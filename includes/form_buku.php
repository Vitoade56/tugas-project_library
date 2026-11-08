<?php
/**
 * Field form buku (dipakai bersama oleh tambah_buku.php & edit_buku.php).
 * Variabel yang dibutuhkan: $data (array nilai form), $sampul_lama (string|null)
 */
$sampul_lama = $sampul_lama ?? null;
?>
<div class="row g-4">
    <div class="col-md-8">
        <div class="mb-3">
            <label for="judul_buku" class="form-label">Judul Buku <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="judul_buku" name="judul_buku" maxlength="150"
                   value="<?= e($data['judul_buku']) ?>" required>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="kategori" name="kategori" maxlength="50"
                 value="<?= e($data['kategori']) ?>" placeholder="Contoh: Novel, Sejarah, Akademik" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="penulis" class="form-label">Penulis <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="penulis" name="penulis" maxlength="100"
                       value="<?= e($data['penulis']) ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="tahun_terbit" class="form-label">Tahun Terbit <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit"
                       min="1000" max="<?= date('Y') + 1 ?>" value="<?= e($data['tahun_terbit']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="status_ketersediaan" class="form-label">Status Ketersediaan (Jumlah Stok) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="status_ketersediaan" name="status_ketersediaan" min="0"
                       value="<?= e($data['status_ketersediaan']) ?>" required>
                <div class="form-text">Stok &gt; 0 = <b>Tersedia</b>, stok 0 = <b>Dipinjam</b>.</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label d-block">Sampul Buku (.jpg / .png, maks 2 MB)</label>
        <div class="preview-box mb-2" id="previewBox">
            <?php if ($sampul_lama && is_file(UPLOAD_DIR . $sampul_lama)): ?>
                <img src="uploads/<?= e($sampul_lama) ?>" alt="Sampul saat ini">
            <?php else: ?>
                <i class="bi bi-image"></i>
            <?php endif; ?>
        </div>
        <input type="file" class="form-control" id="sampul" name="sampul" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
        <div class="text-danger small mt-1" id="pesanFile"></div>
        <?php if ($sampul_lama): ?>
            <div class="form-text">Kosongkan jika tidak ingin mengganti sampul.</div>
        <?php endif; ?>
    </div>
</div>
