<?php
// Dashboard: Read (tampil data) + akses menu CRUD
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/helper.php';
wajib_login();

// ---- Pencarian & filter (metode GET) ----
$cari     = trim($_GET['cari'] ?? '');
$kategori = trim($_GET['kategori'] ?? '');

$sql    = 'SELECT * FROM tb_buku WHERE 1=1';
$tipe   = '';
$params = [];

if ($cari !== '') {
    $sql     .= ' AND (judul_buku LIKE ? OR penulis LIKE ?)';
    $like     = '%' . $cari . '%';
    $params[] = $like;
    $params[] = $like;
    $tipe    .= 'ss';
}
if ($kategori !== '') {
    $sql     .= ' AND kategori = ?';
    $params[] = $kategori;
    $tipe    .= 's';
}
$sql .= ' ORDER BY id DESC';

$stmt = $koneksi->prepare($sql);
if ($params) {
    $stmt->bind_param($tipe, ...$params);
}
$stmt->execute();
$hasil = $stmt->get_result();
$daftar_buku = $hasil->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---- Statistik ----
$stat = $koneksi->query('SELECT COUNT(*) AS total,
                                SUM(status_ketersediaan > 0) AS tersedia,
                                SUM(status_ketersediaan = 0) AS dipinjam
                         FROM tb_buku')->fetch_assoc();

$judul_halaman = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card stat-1"><div class="card-body d-flex justify-content-between align-items-center">
            <div><div class="small">Total Buku</div><div class="fs-2 fw-semibold"><?= (int) $stat['total'] ?></div></div>
            <i class="bi bi-book icon"></i>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card stat-2"><div class="card-body d-flex justify-content-between align-items-center">
            <div><div class="small">Tersedia</div><div class="fs-2 fw-semibold"><?= (int) $stat['tersedia'] ?></div></div>
            <i class="bi bi-check-circle icon"></i>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card stat-3"><div class="card-body d-flex justify-content-between align-items-center">
            <div><div class="small">Dipinjam</div><div class="fs-2 fw-semibold"><?= (int) $stat['dipinjam'] ?></div></div>
            <i class="bi bi-arrow-left-right icon"></i>
        </div></div>
    </div>
</div>

<div class="card card-table">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-semibold"><i class="bi bi-collection me-2"></i>Daftar Buku</h5>
            <a href="tambah_buku.php" class="btn btn-brand"><i class="bi bi-plus-lg me-1"></i>Tambah Buku</a>
        </div>

        <form method="GET" action="dashboard.php" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="text" name="cari" class="form-control" placeholder="Cari judul atau penulis..." value="<?= e($cari) ?>">
            </div>
            <div class="col-md-4">
                <select name="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    <?php foreach (daftar_kategori() as $k): ?>
                        <option value="<?= e($k) ?>" <?= $kategori === $k ? 'selected' : '' ?>><?= e($k) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-outline-primary flex-fill" type="submit"><i class="bi bi-search"></i></button>
                <a href="dashboard.php" class="btn btn-outline-secondary flex-fill" title="Reset"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Sampul</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Penulis</th>
                        <th>Tahun</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($daftar_buku) === 0): ?>
                    <tr><td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>Belum ada data buku.
                    </td></tr>
                <?php endif; ?>

                <?php foreach ($daftar_buku as $no => $b): ?>
                    <?php $status = label_status($b['status_ketersediaan']); ?>
                    <tr>
                        <td><?= $no + 1 ?></td>
                        <td>
                            <?php if ($b['sampul'] && is_file(UPLOAD_DIR . $b['sampul'])): ?>
                                <img src="uploads/<?= e($b['sampul']) ?>" class="cover-thumb" alt="Sampul <?= e($b['judul_buku']) ?>">
                            <?php else: ?>
                                <span class="cover-placeholder"><i class="bi bi-image"></i></span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-medium"><?= e($b['judul_buku']) ?></td>
                        <td><span class="badge text-bg-light border"><?= e($b['kategori']) ?></span></td>
                        <td><?= e($b['penulis']) ?></td>
                        <td><?= e($b['tahun_terbit']) ?></td>
                        <td><?= (int) $b['status_ketersediaan'] ?></td>
                        <td><span class="badge text-bg-<?= $status['kelas'] ?>"><?= $status['label'] ?></span></td>
                        <td class="text-center text-nowrap">
                            <a href="edit_buku.php?id=<?= (int) $b['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="hapus_buku.php" method="POST" class="d-inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $b['id'] ?>">
                                <button type="button" class="btn btn-sm btn-danger btn-hapus"
                                        data-judul="<?= e($b['judul_buku']) ?>" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
