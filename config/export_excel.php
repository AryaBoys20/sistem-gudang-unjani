<?php
require_once __DIR__ . '/koneksi.php';

$jenis  = $_GET['jenis'] ?? 'stok';
$dari   = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');

$judul_map = [
    'stok'   => 'Laporan Stok Barang',
    'masuk'  => 'Laporan Barang Masuk',
    'keluar' => 'Laporan Barang Keluar',
    'minim'  => 'Laporan Stok Minim'
];
$judul = $judul_map[$jenis] ?? 'Laporan';

$nama_file = "Laporan_" . ucfirst($jenis) . "_" . date('Ymd_His') . ".xls";

header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=\"$nama_file\"");
header("Pragma: no-cache");
header("Expires: 0");
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" 
      xmlns:x="urn:schemas-microsoft-com:office:excel" 
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="UTF-8">
    <style>
        table { border-collapse: collapse; }
        th { background: #1e3a8a; color: #fff; padding: 8px; border: 1px solid #000; }
        td { padding: 6px 8px; border: 1px solid #999; }
        .judul { font-size: 16pt; font-weight: bold; text-align: center; }
        .subjudul { font-size: 11pt; text-align: center; }
    </style>
</head>
<body>

<table width="100%" border="0">
    <tr><td colspan="8" class="judul">WMS UNJANI</td></tr>
    <tr><td colspan="8" align="center">Laboratorium Logistik & Rantai Pasok</td></tr>
    <tr><td colspan="8"></td></tr>
    <tr><td colspan="8" class="judul"><?= strtoupper($judul) ?></td></tr>
    <tr><td colspan="8" align="center">Periode: <?= tglIndo($dari) ?> s/d <?= tglIndo($sampai) ?></td></tr>
    <tr><td colspan="8"></td></tr>
</table>

<?php if ($jenis == 'stok'):
    $q = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori FROM barang b 
        LEFT JOIN kategori k ON b.kategori_id = k.id ORDER BY b.nama_barang");
    $total_nilai = 0;
?>
    <table border="1">
        <thead><tr>
            <th>No</th><th>Kode SKU</th><th>Nama Barang</th><th>Kategori</th>
            <th>Stok</th><th>Satuan</th><th>Harga</th><th>Subtotal</th>
        </tr></thead>
        <tbody>
        <?php $no=1; while ($r = mysqli_fetch_assoc($q)): 
            $subtotal = $r['stok'] * $r['harga'];
            $total_nilai += $subtotal;
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $r['kode_barang'] ?></td>
            <td><?= $r['nama_barang'] ?></td>
            <td><?= $r['nama_kategori'] ?? '-' ?></td>
            <td align="center"><?= $r['stok'] ?></td>
            <td align="center"><?= $r['satuan'] ?></td>
            <td align="right"><?= number_format($r['harga'], 0, ',', '.') ?></td>
            <td align="right"><?= number_format($subtotal, 0, ',', '.') ?></td>
        </tr>
        <?php endwhile; ?>
        <tr style="font-weight:bold;">
            <td colspan="7" align="right">TOTAL</td>
            <td align="right"><?= number_format($total_nilai, 0, ',', '.') ?></td>
        </tr>
        </tbody>
    </table>

<?php elseif ($jenis == 'masuk'):
    $q = mysqli_query($koneksi, "SELECT bm.*, b.kode_barang, b.nama_barang, b.satuan, s.nama_supplier 
        FROM barang_masuk bm 
        JOIN barang b ON bm.barang_id = b.id 
        LEFT JOIN supplier s ON bm.supplier_id = s.id 
        WHERE bm.tanggal BETWEEN '$dari' AND '$sampai' ORDER BY bm.tanggal ASC");
    $total = 0;
?>
    <table border="1">
        <thead><tr>
            <th>No</th><th>Tanggal</th><th>Kode SKU</th><th>Nama Barang</th>
            <th>Supplier</th><th>Jumlah</th><th>No PO</th><th>Keterangan</th>
        </tr></thead>
        <tbody>
        <?php $no=1; while ($r = mysqli_fetch_assoc($q)): 
            $total += $r['jumlah'];
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= tglIndo($r['tanggal']) ?></td>
            <td><?= $r['kode_barang'] ?></td>
            <td><?= $r['nama_barang'] ?></td>
            <td><?= $r['nama_supplier'] ?? '-' ?></td>
            <td align="center"><?= $r['jumlah'] ?> <?= $r['satuan'] ?></td>
            <td><?= $r['no_po'] ?: '-' ?></td>
            <td><?= $r['keterangan'] ?: '-' ?></td>
        </tr>
        <?php endwhile; ?>
        <tr style="font-weight:bold;">
            <td colspan="5" align="right">TOTAL</td>
            <td align="center"><?= $total ?></td>
            <td colspan="2"></td>
        </tr>
        </tbody>
    </table>

<?php elseif ($jenis == 'keluar'):
    $q = mysqli_query($koneksi, "SELECT bk.*, b.kode_barang, b.nama_barang, b.satuan 
        FROM barang_keluar bk 
        JOIN barang b ON bk.barang_id = b.id 
        WHERE bk.tanggal BETWEEN '$dari' AND '$sampai' ORDER BY bk.tanggal ASC");
    $total = 0;
?>
    <table border="1">
        <thead><tr>
            <th>No</th><th>Tanggal</th><th>Kode SKU</th><th>Nama Barang</th>
            <th>Tujuan</th><th>Jumlah</th><th>No SJ</th><th>Keterangan</th>
        </tr></thead>
        <tbody>
        <?php $no=1; while ($r = mysqli_fetch_assoc($q)): 
            $total += $r['jumlah'];
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= tglIndo($r['tanggal']) ?></td>
            <td><?= $r['kode_barang'] ?></td>
            <td><?= $r['nama_barang'] ?></td>
            <td><?= $r['tujuan'] ?: '-' ?></td>
            <td align="center"><?= $r['jumlah'] ?> <?= $r['satuan'] ?></td>
            <td><?= $r['no_surat_jalan'] ?: '-' ?></td>
            <td><?= $r['keterangan'] ?: '-' ?></td>
        </tr>
        <?php endwhile; ?>
        <tr style="font-weight:bold;">
            <td colspan="5" align="right">TOTAL</td>
            <td align="center"><?= $total ?></td>
            <td colspan="2"></td>
        </tr>
        </tbody>
    </table>

<?php elseif ($jenis == 'minim'):
    $q = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori, (b.stok_minimal - b.stok) AS kurang 
        FROM barang b 
        LEFT JOIN kategori k ON b.kategori_id = k.id 
        WHERE b.stok <= b.stok_minimal ORDER BY kurang DESC");
?>
    <table border="1">
        <thead><tr>
            <th>No</th><th>Kode SKU</th><th>Nama Barang</th><th>Kategori</th>
            <th>Stok</th><th>Minimal</th><th>Kurang</th><th>Rak</th>
        </tr></thead>
        <tbody>
        <?php $no=1; while ($r = mysqli_fetch_assoc($q)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $r['kode_barang'] ?></td>
            <td><?= $r['nama_barang'] ?></td>
            <td><?= $r['nama_kategori'] ?? '-' ?></td>
            <td align="center"><?= $r['stok'] ?></td>
            <td align="center"><?= $r['stok_minimal'] ?></td>
            <td align="center"><?= $r['kurang'] ?></td>
            <td align="center"><?= $r['lokasi_rak'] ?></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>

<table width="100%" border="0">
    <tr><td colspan="8"></td></tr>
    <tr><td colspan="8" align="center"><em>Dicetak: <?= date('d F Y, H:i') ?> WIB</em></td></tr>
</table>

</body>
</html>