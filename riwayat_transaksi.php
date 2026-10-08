<?php
require_once 'config/koneksi.php';
$jenis_filter = $_GET['jenis'] ?? 'semua';
$dari   = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$search = mysqli_real_escape_string($koneksi, $_GET['search'] ?? '');
require_once 'includes/header.php';
?>
<div class="page-header">
    <div>
        <h4><i class="bi bi-clock-history text-warning"></i> Riwayat Transaksi</h4>
        <small>Semua aktivitas masuk & keluar gudang</small>
    </div>
    <button onclick="window.print()" class="btn btn-outline-primary"><i class="bi bi-printer-fill"></i> Cetak</button>
</div>

<div class="panel mb-3">
    <form method="GET" action="index.php" class="row g-2">
        <input type="hidden" name="page" value="riwayat">
        <div class="col-md-3">
            <label class="form-label">Jenis</label>
            <select name="jenis" class="form-select">
                <option value="semua" <?= $jenis_filter=='semua'?'selected':'' ?>>Semua</option>
                <option value="masuk" <?= $jenis_filter=='masuk'?'selected':'' ?>>Masuk</option>
                <option value="keluar" <?= $jenis_filter=='keluar'?'selected':'' ?>>Keluar</option>
            </select>
        </div>
        <div class="col-md-2"><label class="form-label">Dari</label><input type="date" name="dari" class="form-control" value="<?= $dari ?>"></div>
        <div class="col-md-2"><label class="form-label">Sampai</label><input type="date" name="sampai" class="form-control" value="<?= $sampai ?>"></div>
        <div class="col-md-3"><label class="form-label">Cari</label><input type="text" name="search" class="form-control" placeholder="Nama barang..." value="<?= htmlspecialchars($search) ?>"></div>
        <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button></div>
    </form>
</div>

<?php
$w1 = "WHERE bm.tanggal BETWEEN '$dari' AND '$sampai'";
$w2 = "WHERE bk.tanggal BETWEEN '$dari' AND '$sampai'";
if (!empty($search)) { $w1 .= " AND (b.nama_barang LIKE '%$search%' OR b.kode_barang LIKE '%$search%')"; $w2 .= " AND (b.nama_barang LIKE '%$search%' OR b.kode_barang LIKE '%$search%')"; }
$union = [];
if ($jenis_filter == 'semua' || $jenis_filter == 'masuk') $union[] = "SELECT bm.tanggal, 'Masuk' AS jenis, b.kode_barang, b.nama_barang, b.satuan, bm.jumlah, IFNULL(s.nama_supplier,'-') AS ket, IFNULL(bm.no_po,'-') AS ref FROM barang_masuk bm JOIN barang b ON bm.barang_id=b.id LEFT JOIN supplier s ON bm.supplier_id=s.id $w1";
if ($jenis_filter == 'semua' || $jenis_filter == 'keluar') $union[] = "SELECT bk.tanggal, 'Keluar' AS jenis, b.kode_barang, b.nama_barang, b.satuan, bk.jumlah, IFNULL(bk.tujuan,'-') AS ket, IFNULL(bk.no_surat_jalan,'-') AS ref FROM barang_keluar bk JOIN barang b ON bk.barang_id=b.id $w2";
$sql = "(" . implode(") UNION ALL (", $union) . ") ORDER BY tanggal DESC";
$q = mysqli_query($koneksi, $sql);
?>

<div class="panel">
    <div class="panel-title mb-3"><i class="bi bi-list-check text-primary"></i> Daftar Transaksi <span class="badge bg-primary ms-2"><?= mysqli_num_rows($q) ?></span></div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th style="width:50px;">No</th><th>Tanggal</th><th>Jenis</th><th>Kode</th><th>Nama Barang</th><th class="text-center">Jumlah</th><th>Keterangan</th><th>Referensi</th></tr></thead>
            <tbody>
            <?php $no=1;
            if (mysqli_num_rows($q) == 0): ?>
                <tr><td colspan="8" class="text-center text-muted py-5"><i class="bi bi-inbox" style="font-size:50px;opacity:0.3;"></i><div class="mt-2">Belum ada transaksi</div></td></tr>
            <?php else: while ($r = mysqli_fetch_assoc($q)): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= tglIndo($r['tanggal']) ?></td>
                <td><?php if ($r['jenis']=='Masuk'): ?><span class="badge bg-success"><i class="bi bi-arrow-down"></i> Masuk</span><?php else: ?><span class="badge bg-danger"><i class="bi bi-arrow-up"></i> Keluar</span><?php endif; ?></td>
                <td><code><?= $r['kode_barang'] ?></code></td>
                <td><strong><?= $r['nama_barang'] ?></strong></td>
                <td class="text-center"><?php if ($r['jenis']=='Masuk'): ?><strong class="text-success">+<?= $r['jumlah'] ?></strong><?php else: ?><strong class="text-danger">-<?= $r['jumlah'] ?></strong><?php endif; ?> <small class="text-muted"><?= $r['satuan'] ?></small></td>
                <td><?= $r['ket'] ?></td>
                <td><code><?= $r['ref'] ?></code></td>
            </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>