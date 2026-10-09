<?php
require_once 'config/koneksi.php';

$barang_id = (int)($_GET['id'] ?? 0);
$dari      = $_GET['dari'] ?? date('Y-m-01');
$sampai    = $_GET['sampai'] ?? date('Y-m-d');

$barang = null;
if ($barang_id > 0) {
    $barang = mysqli_fetch_assoc(mysqli_query($koneksi, "
        SELECT b.*, k.nama_kategori 
        FROM barang b 
        LEFT JOIN kategori k ON b.kategori_id = k.id 
        WHERE b.id='$barang_id'
    "));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Stok - WMS UNJANI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-box-seam-fill"></i> WMS UNJANI
        </a>
        <a href="index.php" class="btn btn-sm btn-outline-light">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</nav>

<div class="container-fluid main-content">
    <div class="page-header">
        <div>
            <h4><i class="bi bi-file-earmark-text text-primary"></i> Kartu Stok Barang</h4>
            <small>Riwayat transaksi per barang - Lab Logistik UNJANI</small>
        </div>
        <?php if ($barang): ?>
        <button onclick="window.print()" class="btn btn-primary">                                                                                                                                                                                                                   
            <i class="bi bi-printer-fill"></i> Cetak
        </button>
        <?php endif; ?>
    </div>
    
    <div class="panel mb-3 no-print">
        <form method="GET" action="kartu_stok.php" class="row g-2">
            <div class="col-md-4">
                <label class="form-label">Pilih Barang</label>
                <select name="id" class="form-select" required>
                    <option value="">- Pilih Barang -</option>
                    <?php 
                    $q_b = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY nama_barang");
                    while ($b = mysqli_fetch_assoc($q_b)): 
                    ?>
                    <option value="<?= $b['id'] ?>" <?= $barang_id == $b['id'] ? 'selected' : '' ?>>
                        <?= $b['kode_barang'] ?> - <?= $b['nama_barang'] ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="<?= $dari ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="<?= $sampai ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>
    
    <?php if ($barang): ?>
    <div class="panel mb-3">
        <div class="row">
            <div class="col-md-3"><small class="text-muted">Kode SKU</small><div><strong><code><?= $barang['kode_barang'] ?></code></strong></div></div>
            <div class="col-md-3"><small class="text-muted">Nama Barang</small><div><strong><?= $barang['nama_barang'] ?></strong></div></div>
            <div class="col-md-2"><small class="text-muted">Kategori</small><div><strong><?= $barang['nama_kategori'] ?? '-' ?></strong></div></div>
            <div class="col-md-2"><small class="text-muted">Stok Saat Ini</small><div><strong style="font-size:20px;color:#2563eb;"><?= $barang['stok'] ?> <?= $barang['satuan'] ?></strong></div></div>
            <div class="col-md-2"><small class="text-muted">Lokasi</small><div><strong><?= $barang['lokasi_rak'] ?></strong></div></div>
        </div>
    </div>
    
    <div class="panel">
        <div class="panel-title mb-3"><i class="bi bi-clock-history text-primary"></i> Riwayat Transaksi</div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:50px;">No</th><th>Tanggal</th><th>Jenis</th>
                        <th>Keterangan</th><th class="text-center">Masuk</th>
                        <th class="text-center">Keluar</th><th class="text-center">Sisa</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $q = mysqli_query($koneksi, "
                    (SELECT bm.tanggal, 'Masuk' AS jenis, 
                        CONCAT('Dari: ', IFNULL(s.nama_supplier, '-'), ' | PO: ', IFNULL(bm.no_po, '-')) AS ket,
                        bm.jumlah AS masuk, 0 AS keluar
                     FROM barang_masuk bm 
                     LEFT JOIN supplier s ON bm.supplier_id = s.id
                     WHERE bm.barang_id = '$barang_id' 
                       AND bm.tanggal BETWEEN '$dari' AND '$sampai')
                    UNION ALL
                    (SELECT bk.tanggal, 'Keluar' AS jenis,
                        CONCAT('Ke: ', IFNULL(bk.tujuan, '-'), ' | SJ: ', IFNULL(bk.no_surat_jalan, '-')) AS ket,
                        0 AS masuk, bk.jumlah AS keluar
                     FROM barang_keluar bk 
                     WHERE bk.barang_id = '$barang_id' 
                       AND bk.tanggal BETWEEN '$dari' AND '$sampai')
                    ORDER BY tanggal ASC
                ");
                
                $sum_in = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT IFNULL(SUM(jumlah),0) t FROM barang_masuk WHERE barang_id='$barang_id'"))['t'];
                $sum_out = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT IFNULL(SUM(jumlah),0) t FROM barang_keluar WHERE barang_id='$barang_id'"))['t'];
                $stok_awal = $barang['stok'] - $sum_in + $sum_out;
                
                $no = 1;
                $running = $stok_awal;
                ?>
                <tr style="background:#f8fafc;">
                    <td>-</td>
                    <td colspan="3"><em>Stok Awal Periode</em></td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center"><strong><?= $stok_awal ?></strong></td>
                </tr>
                <?php if (mysqli_num_rows($q) == 0): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size:30px;"></i>
                        <div>Tidak ada transaksi di periode ini</div>
                    </td></tr>
                <?php else: while ($r = mysqli_fetch_assoc($q)): 
                    $running += $r['masuk'] - $r['keluar'];
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= tglIndo($r['tanggal']) ?></td>
                    <td><?php if ($r['jenis'] == 'Masuk'): ?>
                        <span class="badge bg-success">Masuk</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Keluar</span>
                    <?php endif; ?></td>
                    <td><?= $r['ket'] ?></td>
                    <td class="text-center"><?= $r['masuk'] > 0 ? '<strong class="text-success">+' . $r['masuk'] . '</strong>' : '-' ?></td>
                    <td class="text-center"><?= $r['keluar'] > 0 ? '<strong class="text-danger">-' . $r['keluar'] . '</strong>' : '-' ?></td>
                    <td class="text-center"><strong><?= $running ?></strong></td>
                </tr>
                <?php endwhile; endif; ?>
                <tr style="background:#f0f9ff;font-weight:bold;">
                    <td colspan="4" class="text-end">Stok Akhir:</td>
                    <td class="text-center text-success">+<?= $sum_in ?></td>
                    <td class="text-center text-danger">-<?= $sum_out ?></td>
                    <td class="text-center"><?= $barang['stok'] ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="panel text-center py-5">
        <i class="bi bi-file-earmark-text text-muted" style="font-size:60px;"></i>
        <h5 class="mt-3">Pilih Barang Terlebih Dahulu</h5>
        <p class="text-muted">Pilih barang di atas untuk melihat kartu stok</p>
    </div>
    <?php endif; ?>
</div>

<footer><strong>&copy; <?= date('Y') ?> WMS UNJANI</strong> — Sistem Informasi Manajemen Pergudangan</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<style>@media print { .no-print, .navbar, footer { display: none !important; } }</style>
</body>
</html>