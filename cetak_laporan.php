<?php
require_once 'config/koneksi.php';

$jenis   = $_GET['jenis'] ?? 'stok';
$dari    = $_GET['dari'] ?? date('Y-m-01');
$sampai  = $_GET['sampai'] ?? date('Y-m-d');

$judul_map = [
    'stok'   => 'LAPORAN STOK BARANG',
    'masuk'  => 'LAPORAN BARANG MASUK',
    'keluar' => 'LAPORAN BARANG KELUAR',
    'minim'  => 'LAPORAN STOK MINIM (PERLU RESTOCK)'
];
$judul = $judul_map[$jenis] ?? 'LAPORAN';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $judul ?> - WMS UNJANI</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            margin: 0;
            padding: 20px 30px;
            background: #fff;
        }
        
        .kop {
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .kop-logo {
            width: 80px;
            height: 80px;
            background: #1e3a8a;
            color: #fff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }
        .kop-text h1 { font-size: 18pt; font-weight: bold; margin: 0; letter-spacing: 1px; }
        .kop-text h2 { font-size: 14pt; font-weight: normal; margin: 3px 0; }
        .kop-text p { font-size: 10pt; margin: 3px 0 0 0; color: #333; }
        
        .judul { text-align: center; margin-bottom: 20px; }
        .judul h3 {
            font-size: 15pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0 0 5px 0;
            letter-spacing: 1px;
        }
        .judul p { font-size: 11pt; margin: 0; color: #333; }
        
        .info-periode {
            font-size: 11pt;
            margin-bottom: 15px;
            padding: 10px 15px;
            background: #f0f0f0;
            border-left: 4px solid #1e3a8a;
        }
        .info-periode strong { color: #1e3a8a; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
            margin-bottom: 15px;
        }
        table thead { background: #1e3a8a; color: #fff; }
        table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #000;
            font-size: 10.5pt;
        }
        table td { padding: 8px; border: 1px solid #666; }
        table tbody tr:nth-child(even) { background: #f5f5f5; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 9.5pt;
            border-radius: 3px;
            font-weight: bold;
        }
        .badge-aman { background: #d1fae5; color: #065f46; }
        .badge-kritis { background: #fef3c7; color: #92400e; }
        .badge-habis { background: #fee2e2; color: #991b1b; }
        
        .summary {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            justify-content: flex-end;
        }
        .summary-box {
            padding: 10px 20px;
            background: #f0f4f8;
            border-left: 4px solid #1e3a8a;
            border-radius: 5px;
        }
        .summary-box .label { font-size: 10pt; color: #666; text-transform: uppercase; }
        .summary-box .value { font-size: 14pt; font-weight: bold; color: #1e3a8a; }
        
        .ttd { margin-top: 40px; display: flex; justify-content: space-between; }
        .ttd-box { text-align: center; width: 220px; }
        .ttd-box p { margin: 0 0 60px 0; font-size: 11pt; }
        .ttd-box .line { border-top: 1px solid #000; padding-top: 5px; font-weight: bold; font-size: 11pt; }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #999;
            font-size: 9pt;
            color: #666;
            text-align: center;
        }
        
        .toolbar {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        .toolbar button,
        .toolbar a {
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: 'Segoe UI', sans-serif;
            transition: all 0.2s;
        }
        .btn-print { background: #2563eb; color: #fff; box-shadow: 0 4px 12px rgba(37,99,235,0.4); }
        .btn-print:hover { background: #1d4ed8; transform: translateY(-2px); }
        .btn-back { background: #64748b; color: #fff; }
        .btn-back:hover { background: #475569; transform: translateY(-2px); }
        
        @media print {
            .toolbar { display: none; }
            body { padding: 0; margin: 15mm; }
            table thead { background: #1e3a8a !important; color: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            table tbody tr:nth-child(even) { background: #f5f5f5 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .info-periode { background: #f0f0f0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .badge-aman, .badge-kritis, .badge-habis { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        
        @page { size: A4; margin: 15mm; }
    </style>
</head>
<body>

<div class="toolbar">
    <a href="index.php?page=laporan" class="btn-back">← Kembali</a>
    <button onclick="window.print()" class="btn-print">🖨️ Cetak / Save PDF</button>
</div>

<!-- KOP -->
<div class="kop">
    <div class="kop-logo">📦</div>
    <div class="kop-text">
        <h1>Sistem Gudang UNJANI</h1>
        <h2>Laboratorium Logistik & Rantai Pasok</h2>
        <p>Sistem Informasi Manajemen Pergudangan</p>
        <p>Jl. Contoh Alamat No. 1, Cimahi, Jawa Barat | Telp: (022) 123-4567</p>
    </div>
</div>

<!-- JUDUL -->
<div class="judul">
    <h3><?= $judul ?></h3>
    <p>Periode: <?= tglIndo($dari) ?> s/d <?= tglIndo($sampai) ?></p>
</div>

<div class="info-periode">
    <strong>📅 Tanggal Cetak:</strong> <?= date('d F Y, H:i') ?> WIB
    &nbsp;&nbsp;|&nbsp;&nbsp;
    <strong>Total Data:</strong> 
    <?php
    switch ($jenis) {
        case 'stok':   $q = mysqli_query($koneksi, "SELECT COUNT(*) jml FROM barang"); break;
        case 'masuk':  $q = mysqli_query($koneksi, "SELECT COUNT(*) jml FROM barang_masuk WHERE tanggal BETWEEN '$dari' AND '$sampai'"); break;
        case 'keluar': $q = mysqli_query($koneksi, "SELECT COUNT(*) jml FROM barang_keluar WHERE tanggal BETWEEN '$dari' AND '$sampai'"); break;
        case 'minim':  $q = mysqli_query($koneksi, "SELECT COUNT(*) jml FROM barang WHERE stok <= stok_minimal"); break;
        default:       $q = mysqli_query($koneksi, "SELECT 0 jml"); break;
    }
    echo mysqli_fetch_assoc($q)['jml'];
    ?> baris
</div>


<?php // ═══ LAPORAN STOK ═══
if ($jenis == 'stok'):
    $q = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori FROM barang b 
        LEFT JOIN kategori k ON b.kategori_id = k.id ORDER BY b.nama_barang");
    $total_nilai = 0;
?>
    <table>
        <thead>
            <tr>
                <th style="width:40px;" class="text-center">No</th>
                <th>Kode SKU</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th class="text-center">Stok</th>
                <th class="text-center">Satuan</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Subtotal</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
        <?php $no=1; while ($r = mysqli_fetch_assoc($q)): 
            $subtotal = $r['stok'] * $r['harga'];
            $total_nilai += $subtotal;
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td><strong><?= $r['kode_barang'] ?></strong></td>
            <td><?= $r['nama_barang'] ?></td>
            <td><?= $r['nama_kategori'] ?? '-' ?></td>
            <td class="text-center text-bold"><?= $r['stok'] ?></td>
            <td class="text-center"><?= $r['satuan'] ?></td>
            <td class="text-right"><?= rupiah($r['harga']) ?></td>
            <td class="text-right"><?= rupiah($subtotal) ?></td>
            <td class="text-center">
                <?php 
                if ($r['stok'] <= 0) echo '<span class="badge badge-habis">HABIS</span>';
                elseif ($r['stok'] <= $r['stok_minimal']) echo '<span class="badge badge-kritis">KRITIS</span>';
                else echo '<span class="badge badge-aman">AMAN</span>';
                ?>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <div class="summary">
        <div class="summary-box">
            <div class="label">Total Nilai Inventaris</div>
            <div class="value"><?= rupiah($total_nilai) ?></div>
        </div>
    </div>


<?php // ═══ LAPORAN MASUK ═══
elseif ($jenis == 'masuk'):
    $q = mysqli_query($koneksi, "SELECT bm.*, b.kode_barang, b.nama_barang, b.satuan, s.nama_supplier 
        FROM barang_masuk bm 
        JOIN barang b ON bm.barang_id = b.id 
        LEFT JOIN supplier s ON bm.supplier_id = s.id 
        WHERE bm.tanggal BETWEEN '$dari' AND '$sampai'
        ORDER BY bm.tanggal ASC, bm.id ASC");
    $total_qty = 0;
?>
    <table>
        <thead>
            <tr>
                <th style="width:40px;" class="text-center">No</th>
                <th class="text-center">Tanggal</th>
                <th>Kode SKU</th>
                <th>Nama Barang</th>
                <th>Supplier</th>
                <th class="text-center">Jumlah</th>
                <th>No PO</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
        <?php $no=1; while ($r = mysqli_fetch_assoc($q)): 
            $total_qty += $r['jumlah'];
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center"><?= tglIndo($r['tanggal']) ?></td>
            <td><strong><?= $r['kode_barang'] ?></strong></td>
            <td><?= $r['nama_barang'] ?></td>
            <td><?= $r['nama_supplier'] ?? '-' ?></td>
            <td class="text-center text-bold">+<?= $r['jumlah'] ?> <?= $r['satuan'] ?></td>
            <td><?= $r['no_po'] ?: '-' ?></td>
            <td><?= $r['keterangan'] ?: '-' ?></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <div class="summary">
        <div class="summary-box">
            <div class="label">Total Barang Masuk</div>
            <div class="value"><?= number_format($total_qty, 0, ',', '.') ?> unit</div>
        </div>
    </div>


<?php // ═══ LAPORAN KELUAR ═══
elseif ($jenis == 'keluar'):
    $q = mysqli_query($koneksi, "SELECT bk.*, b.kode_barang, b.nama_barang, b.satuan 
        FROM barang_keluar bk 
        JOIN barang b ON bk.barang_id = b.id 
        WHERE bk.tanggal BETWEEN '$dari' AND '$sampai'
        ORDER BY bk.tanggal ASC, bk.id ASC");
    $total_qty = 0;
?>
    <table>
        <thead>
            <tr>
                <th style="width:40px;" class="text-center">No</th>
                <th class="text-center">Tanggal</th>
                <th>Kode SKU</th>
                <th>Nama Barang</th>
                <th>Tujuan</th>
                <th class="text-center">Jumlah</th>
                <th>No SJ</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
        <?php $no=1; while ($r = mysqli_fetch_assoc($q)): 
            $total_qty += $r['jumlah'];
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center"><?= tglIndo($r['tanggal']) ?></td>
            <td><strong><?= $r['kode_barang'] ?></strong></td>
            <td><?= $r['nama_barang'] ?></td>
            <td><?= $r['tujuan'] ?: '-' ?></td>
            <td class="text-center text-bold">-<?= $r['jumlah'] ?> <?= $r['satuan'] ?></td>
            <td><?= $r['no_surat_jalan'] ?: '-' ?></td>
            <td><?= $r['keterangan'] ?: '-' ?></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <div class="summary">
        <div class="summary-box">
            <div class="label">Total Barang Keluar</div>
            <div class="value"><?= number_format($total_qty, 0, ',', '.') ?> unit</div>
        </div>
    </div>


<?php // ═══ LAPORAN MINIM ═══
elseif ($jenis == 'minim'):
    $q = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori, (b.stok_minimal - b.stok) AS kurang 
        FROM barang b 
        LEFT JOIN kategori k ON b.kategori_id = k.id 
        WHERE b.stok <= b.stok_minimal 
        ORDER BY kurang DESC");
?>
    <table>
        <thead>
            <tr>
                <th style="width:40px;" class="text-center">No</th>
                <th>Kode SKU</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th class="text-center">Stok Saat Ini</th>
                <th class="text-center">Stok Minimal</th>
                <th class="text-center">Kurang</th>
                <th class="text-center">Lokasi Rak</th>
                <th class="text-center">Prioritas</th>
            </tr>
        </thead>
        <tbody>
        <?php $no=1; while ($r = mysqli_fetch_assoc($q)): 
            $prioritas = $r['kurang'] >= 10 ? 'TINGGI' : ($r['kurang'] >= 5 ? 'SEDANG' : 'RENDAH');
            $badge_prio = $prioritas == 'TINGGI' ? 'badge-habis' : ($prioritas == 'SEDANG' ? 'badge-kritis' : 'badge-aman');
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td><strong><?= $r['kode_barang'] ?></strong></td>
            <td><?= $r['nama_barang'] ?></td>
            <td><?= $r['nama_kategori'] ?? '-' ?></td>
            <td class="text-center text-bold"><?= $r['stok'] ?> <?= $r['satuan'] ?></td>
            <td class="text-center"><?= $r['stok_minimal'] ?> <?= $r['satuan'] ?></td>
            <td class="text-center text-bold">-<?= $r['kurang'] ?></td>
            <td class="text-center"><?= $r['lokasi_rak'] ?></td>
            <td class="text-center"><span class="badge <?= $badge_prio ?>"><?= $prioritas ?></span></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>


<!-- TANDA TANGAN -->
<div class="ttd">
    <div class="ttd-box">
        <p>Dibuat oleh,</p>
        <div class="line">Staff Gudang</div>
    </div>
    <div class="ttd-box">
        <p>Disetujui oleh,</p>
        <div class="line">Kepala Lab Logistik</div>
    </div>
</div>

<!-- FOOTER -->
<div class="footer">
    Dokumen ini dicetak secara otomatis dari Sistem Informasi Manajemen Pergudangan WMS UNJANI<br>
    Dicetak pada: <?= date('d F Y, H:i:s') ?> WIB
</div>
</body>
</html>