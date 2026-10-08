<?php
require_once 'config/koneksi.php';

require_once 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h4><i class="bi bi-pie-chart-fill text-success"></i> Laporan per Kategori</h4>
        <small>Analisis stok & nilai inventaris per kategori</small>
    </div>
    <button onclick="window.print()" class="btn btn-outline-primary">
        <i class="bi bi-printer-fill"></i> Cetak
    </button>
</div>

<?php
$q = mysqli_query($koneksi, "
    SELECT 
        k.id, k.nama_kategori, k.kode_zona,
        COUNT(b.id) AS jml_barang,
        IFNULL(SUM(b.stok), 0) AS total_stok,
        IFNULL(SUM(b.stok * b.harga), 0) AS total_nilai
    FROM kategori k 
    LEFT JOIN barang b ON b.kategori_id = k.id 
    GROUP BY k.id 
    ORDER BY k.nama_kategori
");

$grand_total_barang = 0;
$grand_total_stok = 0;
$grand_total_nilai = 0;
$rows = [];
while ($r = mysqli_fetch_assoc($q)) {
    $rows[] = $r;
    $grand_total_barang += $r['jml_barang'];
    $grand_total_stok += $r['total_stok'];
    $grand_total_nilai += $r['total_nilai'];
}
?>

<!-- SUMMARY -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card blue">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Kategori</div>
                    <div class="stat-value mt-3"><?= count($rows) ?></div>
                    <small class="text-muted">Kategori aktif</small>
                </div>
                <div class="stat-icon" style="background:#dbeafe;color:#2563eb;">
                    <i class="bi bi-tags-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card green">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Barang</div>
                    <div class="stat-value mt-3"><?= $grand_total_barang ?></div>
                    <small class="text-muted">SKU terdaftar</small>
                </div>
                <div class="stat-icon" style="background:#d1fae5;color:#10b981;">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card cyan">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Nilai</div>
                    <div class="stat-value mt-3" style="font-size:24px;"><?= rupiah($grand_total_nilai) ?></div>
                    <small class="text-muted">Nilai inventaris</small>
                </div>
                <div class="stat-icon" style="background:#cffafe;color:#06b6d4;">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DETAIL TABLE -->
<div class="panel">
    <div class="panel-title mb-3"><i class="bi bi-table text-primary"></i> Rincian per Kategori</div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:50px;">No</th>
                    <th>Kategori</th>
                    <th>Zona</th>
                    <th class="text-center">Jumlah Barang</th>
                    <th class="text-center">Total Stok</th>
                    <th class="text-end">Total Nilai</th>
                    <th style="width:200px;">Persentase</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $no = 1;
            $colors = ['#2563eb', '#10b981', '#06b6d4', '#f59e0b', '#8b5cf6'];
            foreach ($rows as $i => $r): 
                $persen = $grand_total_nilai > 0 ? ($r['total_nilai'] / $grand_total_nilai * 100) : 0;
                $color = $colors[$i % count($colors)];
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><strong><?= $r['nama_kategori'] ?></strong></td>
                <td><span class="badge" style="background:<?= $color ?>;color:#fff;">Zona <?= $r['kode_zona'] ?></span></td>
                <td class="text-center"><strong><?= $r['jml_barang'] ?></strong></td>
                <td class="text-center"><strong><?= $r['total_stok'] ?></strong></td>
                <td class="text-end"><strong><?= rupiah($r['total_nilai']) ?></strong></td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height:8px;">
                            <div class="progress-bar" style="width:<?= $persen ?>%;background:<?= $color ?>;"></div>
                        </div>
                        <small style="min-width:45px;text-align:right;"><?= number_format($persen, 1) ?>%</small>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background:linear-gradient(90deg,#f8fafc,#f0f9ff);font-weight:700;font-size:15px;">
                    <td colspan="3" class="text-end">GRAND TOTAL:</td>
                    <td class="text-center"><?= $grand_total_barang ?></td>
                    <td class="text-center"><?= $grand_total_stok ?></td>
                    <td class="text-end text-primary"><?= rupiah($grand_total_nilai) ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>