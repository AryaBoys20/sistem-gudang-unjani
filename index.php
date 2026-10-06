<?php
require_once 'config/koneksi.php';

$page  = $_GET['page'] ?? 'beranda';
$msg   = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';

require_once 'includes/header.php';
?>

<!-- ═══ NOTIFIKASI ═══ -->
<?php if ($msg == 'sukses'): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle-fill me-2"></i> <strong>Berhasil!</strong> Data telah disimpan.
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php elseif ($msg == 'hapus'): ?>
<div class="alert alert-warning alert-dismissible fade show">
    <i class="bi bi-trash-fill me-2"></i> <strong>Terhapus!</strong> Data telah dihapus dari sistem.
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php elseif ($error == 'stok'): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Stok tidak cukup!</strong> Transaksi dibatalkan.
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php elseif ($error == 'jumlah'): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Jumlah tidak valid!</strong> Harus lebih dari 0.
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php elseif ($error && $error != 'stok' && $error != 'jumlah'): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Error:</strong> <?= htmlspecialchars(urldecode($error)) ?>
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>


<?php // ═══════════════════════════════════════
// BERANDA / DASHBOARD
// ═══════════════════════════════════════
if ($page == 'beranda'):
    $total_barang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) jml FROM barang"))['jml'];
    $masuk_hari   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT IFNULL(SUM(jumlah),0) jml FROM barang_masuk WHERE tanggal=CURDATE()"))['jml'];
    $keluar_hari  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT IFNULL(SUM(jumlah),0) jml FROM barang_keluar WHERE tanggal=CURDATE()"))['jml'];
    $total_stok   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT IFNULL(SUM(stok),0) jml FROM barang"))['jml'];
    $stok_kritis  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) jml FROM barang WHERE stok <= stok_minimal"))['jml'];
?>

    <!-- HERO BANNER -->
    <div class="hero-banner">
        <span class="hero-badge">LABORATORIUM LOGISTIK & RANTAI PASOK</span>
        <h1>Sistem Manajemen Gudang Terpadu</h1>
        <p>Pantau sirkulasi barang masuk, keluar, dan kapasitas rak simpan secara real-time untuk operasional gudang yang efektif dan akurat.</p>
    </div>
    
    <!-- STAT CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card blue">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Jenis Barang</div>
                        <div class="stat-value mt-3"><?= $total_barang ?></div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-box"></i> Master SKU
                        </small>
                    </div>
                    <div class="stat-icon" style="background: #dbeafe; color: #2563eb;">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card green">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Masuk (Hari Ini)</div>
                        <div class="stat-value mt-3 text-success"><?= $masuk_hari ?></div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-arrow-down-circle"></i> Total Inbound
                        </small>
                    </div>
                    <div class="stat-icon" style="background: #d1fae5; color: #10b981;">
                        <i class="bi bi-arrow-down-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card yellow">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Keluar (Hari Ini)</div>
                        <div class="stat-value mt-3" style="color: #f59e0b;"><?= $keluar_hari ?></div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-arrow-up-circle"></i> Total Outbound
                        </small>
                    </div>
                    <div class="stat-icon" style="background: #fef3c7; color: #f59e0b;">
                        <i class="bi bi-arrow-up-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card cyan">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Volume Stok</div>
                        <div class="stat-value mt-3" style="color: #06b6d4;"><?= $total_stok ?></div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-stack"></i> Akumulasi Unit
                        </small>
                    </div>
                    <div class="stat-icon" style="background: #cffafe; color: #06b6d4;">
                        <i class="bi bi-stack"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- GRAFIK + ZONA -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="panel">
                <div class="panel-title">
                    <i class="bi bi-graph-up-arrow text-primary"></i> Tren Mutasi Stok
                </div>
                <div class="panel-subtitle">Perbandingan arus barang masuk dan keluar 7 hari terakhir</div>
                <div style="height: 300px; position: relative;">
                    <canvas id="chartMutasi"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-title">
                    <i class="bi bi-pie-chart-fill text-primary"></i> Kapasitas Gudang
                </div>
                <div class="panel-subtitle">Estimasi beban area penyimpanan rak</div>
                <?php
                $zona = mysqli_query($koneksi, "
                    SELECT k.nama_kategori, k.kode_zona, 
                        COUNT(b.id) as jml_barang,
                        IFNULL(SUM(b.stok),0) as total_stok
                    FROM kategori k 
                    LEFT JOIN barang b ON b.kategori_id = k.id 
                    GROUP BY k.id 
                    ORDER BY k.kode_zona");
                $warna_map = [
                    'A' => ['bg' => '#2563eb'],
                    'B' => ['bg' => '#10b981'],
                    'C' => ['bg' => '#06b6d4'],
                    'D' => ['bg' => '#f59e0b']
                ];
                while ($z = mysqli_fetch_assoc($zona)):
                    $persen = min(100, $z['jml_barang'] * 15);
                    $w = $warna_map[$z['kode_zona']] ?? ['bg' => '#6b7280'];
                ?>
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold" style="font-size: 13.5px;">
                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: <?= $w['bg'] ?>; margin-right: 6px;"></span>
                            Zona <?= $z['kode_zona'] ?>
                            <span class="text-muted" style="font-weight: 400; font-size: 12.5px;">(<?= $z['nama_kategori'] ?>)</span>
                        </span>
                        <strong style="color: <?= $w['bg'] ?>; font-size: 15px;"><?= $persen ?>%</strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?= $persen ?>%; background: <?= $w['bg'] ?>;"></div>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 12px;">
                        <?= $z['jml_barang'] ?> jenis barang · <?= $z['total_stok'] ?> unit stok
                    </small>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    
    <!-- PERINGATAN STOK MINIM -->
    <div class="panel mb-4">
        <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
            <div>
                <div class="panel-title">
                    <i class="bi bi-exclamation-triangle-fill text-danger"></i> 
                    Peringatan Stok Minimis
                </div>
                <div class="panel-subtitle mb-0">Perlu re-stock barang berikut untuk hindari out-of-stock</div>
            </div>
            <a href="index.php?page=laporan" class="btn btn-sm btn-outline-primary">
                Lihat Semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>BARANG</th>
                        <th>KODE SKU</th>
                        <th>LOKASI RAK</th>
                        <th>SISA STOK</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $q = mysqli_query($koneksi, "SELECT * FROM barang WHERE stok <= stok_minimal ORDER BY stok ASC");
                if (mysqli_num_rows($q) == 0): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 40px;"></i>
                            <div class="mt-2 fw-semibold">Semua stok aman!</div>
                            <small>Tidak ada barang yang perlu di-restock saat ini</small>
                        </td>
                    </tr>
                <?php else:
                while ($r = mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td><strong><?= $r['nama_barang'] ?></strong></td>
                    <td><code><?= $r['kode_barang'] ?></code></td>
                    <td><?= $r['lokasi_rak'] ?></td>
                    <td><strong class="text-danger"><?= $r['stok'] ?> <?= $r['satuan'] ?></strong></td>
                    <td><?= badgeStok($r['stok'], $r['stok_minimal']) ?></td>
                    <td>
                        <a href="index.php?page=masuk" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-plus-circle"></i> Restock
                        </a>
                    </td>
                </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- CHART.JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    <?php
    $hari_indo = ['Sun'=>'Min','Mon'=>'Sen','Tue'=>'Sel','Wed'=>'Rab','Thu'=>'Kam','Fri'=>'Jum','Sat'=>'Sab'];
    $labels = []; $data_masuk = []; $data_keluar = [];
    for ($i = 6; $i >= 0; $i--) {
        $tgl = date('Y-m-d', strtotime("-$i days"));
        $labels[] = $hari_indo[date('D', strtotime($tgl))];
        $m = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT IFNULL(SUM(jumlah),0) jml FROM barang_masuk WHERE tanggal='$tgl'"))['jml'];
        $k = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT IFNULL(SUM(jumlah),0) jml FROM barang_keluar WHERE tanggal='$tgl'"))['jml'];
        $data_masuk[] = (int)$m;
        $data_keluar[] = (int)$k;
    }
    ?>
    new Chart(document.getElementById('chartMutasi'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [
                { 
                    label: 'Barang Masuk', 
                    data: <?= json_encode($data_masuk) ?>, 
                    backgroundColor: 'rgba(37, 99, 235, 0.85)',
                    hoverBackgroundColor: '#2563eb',
                    borderRadius: 8,
                    barPercentage: 0.7
                },
                { 
                    label: 'Barang Keluar', 
                    data: <?= json_encode($data_keluar) ?>, 
                    backgroundColor: 'rgba(245, 158, 11, 0.85)',
                    hoverBackgroundColor: '#f59e0b',
                    borderRadius: 8,
                    barPercentage: 0.7
                }
            ]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            plugins: { 
                legend: { 
                    position: 'top',
                    align: 'end',
                    labels: { 
                        usePointStyle: true, 
                        padding: 16, 
                        font: { size: 13, weight: '600' },
                        boxWidth: 10,
                        boxHeight: 10
                    }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 14,
                    titleFont: { size: 14, weight: '700' },
                    bodyFont: { size: 13 },
                    cornerRadius: 8,
                    displayColors: true
                }
            }, 
            scales: { 
                y: { 
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0, color: '#94a3b8', font: { size: 12 } },
                    grid: { color: '#f1f5f9', drawBorder: false }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { size: 13, weight: '600' } }
                }
            } 
        }
    });
    </script>
    

<?php // ═══════════════════════════════════════
// DATA BARANG
// ═══════════════════════════════════════
elseif ($page == 'barang'):
?>
    <div class="page-header">
        <div>
            <h4><i class="bi bi-box-seam text-primary"></i> Data Barang</h4>
            <small>Kelola master data barang gudang</small>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBarang">
            <i class="bi bi-plus-circle"></i> Tambah Barang
        </button>
    </div>
    
    <div class="panel">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="width: 60px;">NO</th>
                        <th>KODE</th>
                        <th>NAMA BARANG</th>
                        <th>KATEGORI</th>
                        <th>STOK</th>
                        <th>RAK</th>
                        <th>HARGA</th>
                        <th style="width: 100px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                $q = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori FROM barang b 
                    LEFT JOIN kategori k ON b.kategori_id=k.id ORDER BY b.id DESC");
                if (mysqli_num_rows($q) == 0): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-inbox" style="font-size: 40px; color: #cbd5e1;"></i>
                            <div class="mt-2">Belum ada data barang</div>
                        </td>
                    </tr>
                <?php else:
                while ($r = mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><code><?= $r['kode_barang'] ?></code></td>
                    <td><strong><?= $r['nama_barang'] ?></strong></td>
                    <td><?= $r['nama_kategori'] ?? '-' ?></td>
                    <td>
                        <strong><?= $r['stok'] ?></strong> <?= $r['satuan'] ?> 
                        <?= badgeStok($r['stok'], $r['stok_minimal']) ?>
                    </td>
                    <td><?= $r['lokasi_rak'] ?></td>
                    <td><?= rupiah($r['harga']) ?></td>
                    <td>
                        <a href="?page=barang&edit=<?= $r['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <a href="config/proses.php?action=hapus_barang&id=<?= $r['id'] ?>" 
                           class="btn btn-sm btn-danger" title="Hapus"
                           onclick="return confirm('Yakin hapus barang ini?')">
                            <i class="bi bi-trash-fill"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php
    $edit = null;
    if (isset($_GET['edit'])) {
        $edit = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM barang WHERE id='{$_GET['edit']}'"));
    }
    ?>
    <div class="modal fade" id="modalBarang" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="config/proses.php">
                    <input type="hidden" name="action" value="simpan_barang">
                    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
                    <div class="modal-header" style="background: linear-gradient(135deg, #2563eb, #1e40af); color: #fff;">
                        <h5><i class="bi bi-box-seam"></i> <?= $edit?'Edit':'Tambah' ?> Barang</h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
                            <input type="text" name="kode_barang" class="form-control" 
                                   value="<?= $edit['kode_barang'] ?? '' ?>" 
                                   placeholder="Contoh: BRG-001" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="nama_barang" class="form-control" 
                                   value="<?= $edit['nama_barang'] ?? '' ?>" 
                                   placeholder="Contoh: Mouse Logitech" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_id" class="form-select" required>
                                <option value="">- Pilih Kategori -</option>
                                <?php
                                $k = mysqli_query($koneksi, "SELECT * FROM kategori");
                                while ($kk = mysqli_fetch_assoc($k)):
                                ?>
                                <option value="<?= $kk['id'] ?>" 
                                    <?= ($edit && $edit['kategori_id']==$kk['id'])?'selected':'' ?>>
                                    <?= $kk['nama_kategori'] ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Satuan</label>
                                <input type="text" name="satuan" class="form-control" 
                                       value="<?= $edit['satuan'] ?? 'pcs' ?>" 
                                       placeholder="pcs / box / roll">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok Awal</label>
                                <input type="number" name="stok" class="form-control" 
                                       value="<?= $edit['stok'] ?? 0 ?>" min="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok Minimal</label>
                                <input type="number" name="stok_minimal" class="form-control" 
                                       value="<?= $edit['stok_minimal'] ?? 5 ?>" min="0">
                                <small class="text-muted">Batas peringatan restock</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lokasi Rak</label>
                                <input type="text" name="lokasi_rak" class="form-control" 
                                       value="<?= $edit['lokasi_rak'] ?? '' ?>" 
                                       placeholder="Contoh: Rak A-01">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga (Rp)</label>
                            <input type="number" name="harga" class="form-control" 
                                   value="<?= $edit['harga'] ?? 0 ?>" min="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle-fill"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php if ($edit): ?>
    <script>new bootstrap.Modal(document.getElementById('modalBarang')).show();</script>
    <?php endif; ?>


<?php // ═══════════════════════════════════════
// KATEGORI
// ═══════════════════════════════════════
elseif ($page == 'kategori'):
?>
    <div class="page-header">
        <div>
            <h4><i class="bi bi-tags-fill text-primary"></i> Kategori Barang</h4>
            <small>Kelola kategori dan zona gudang</small>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalKategori">
            <i class="bi bi-plus-circle"></i> Tambah Kategori
        </button>
    </div>
    <div class="panel">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 60px;">NO</th>
                        <th>NAMA KATEGORI</th>
                        <th>ZONA</th>
                        <th>KETERANGAN</th>
                        <th style="width: 100px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no=1; $q=mysqli_query($koneksi,"SELECT * FROM kategori ORDER BY id");
                while($r=mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= $r['nama_kategori'] ?></strong></td>
                    <td><span class="badge bg-primary">Zona <?= $r['kode_zona'] ?></span></td>
                    <td><?= $r['keterangan'] ?></td>
                    <td>
                        <a href="config/proses.php?action=hapus_kategori&id=<?= $r['id'] ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Yakin hapus kategori ini?')">
                            <i class="bi bi-trash-fill"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="modalKategori">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="config/proses.php">
                    <input type="hidden" name="action" value="simpan_kategori">
                    <div class="modal-header" style="background: linear-gradient(135deg, #2563eb, #1e40af); color: #fff;">
                        <h5><i class="bi bi-tag-fill"></i> Tambah Kategori</h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" class="form-control" 
                                   placeholder="Contoh: Elektronik" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kode Zona <span class="text-danger">*</span></label>
                            <input type="text" name="kode_zona" class="form-control" 
                                   placeholder="A / B / C" maxlength="2" required>
                            <small class="text-muted">Kode untuk denah gudang</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3" 
                                      placeholder="Opsional"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle-fill"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


<?php // ═══════════════════════════════════════
// SUPPLIER
// ═══════════════════════════════════════
elseif ($page == 'supplier'):
?>
    <div class="page-header">
        <div>
            <h4><i class="bi bi-truck text-primary"></i> Data Supplier</h4>
            <small>Kelola data pemasok barang</small>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSupplier">
            <i class="bi bi-plus-circle"></i> Tambah Supplier
        </button>
    </div>
    <div class="panel">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 60px;">NO</th>
                        <th>NAMA SUPPLIER</th>
                        <th>ALAMAT</th>
                        <th>TELEPON</th>
                        <th style="width: 100px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no=1; $q=mysqli_query($koneksi,"SELECT * FROM supplier ORDER BY id");
                while($r=mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= $r['nama_supplier'] ?></strong></td>
                    <td><?= $r['alamat'] ?></td>
                    <td><?= $r['telepon'] ?></td>
                    <td>
                        <a href="config/proses.php?action=hapus_supplier&id=<?= $r['id'] ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Yakin hapus supplier ini?')">
                            <i class="bi bi-trash-fill"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="modalSupplier">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="config/proses.php">
                    <input type="hidden" name="action" value="simpan_supplier">
                    <div class="modal-header" style="background: linear-gradient(135deg, #2563eb, #1e40af); color: #fff;">
                        <h5><i class="bi bi-truck"></i> Tambah Supplier</h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                            <input type="text" name="nama_supplier" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="telepon" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle-fill"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


<?php // ═══════════════════════════════════════
// BARANG MASUK
// ═══════════════════════════════════════
elseif ($page == 'masuk'):
?>
    <div class="page-header">
        <div>
            <h4><i class="bi bi-arrow-down-circle-fill text-success"></i> Barang Masuk</h4>
            <small>Catat transaksi barang masuk ke gudang</small>
        </div>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalMasuk">
            <i class="bi bi-plus-circle"></i> Tambah Barang Masuk
        </button>
    </div>
    <div class="panel">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 60px;">NO</th>
                        <th>TANGGAL</th>
                        <th>BARANG</th>
                        <th>SUPPLIER</th>
                        <th>JUMLAH</th>
                        <th>NO PO</th>
                        <th style="width: 100px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no=1;
                $q = mysqli_query($koneksi, "SELECT bm.*, b.nama_barang, b.satuan, s.nama_supplier 
                    FROM barang_masuk bm 
                    JOIN barang b ON bm.barang_id=b.id 
                    LEFT JOIN supplier s ON bm.supplier_id=s.id 
                    ORDER BY bm.id DESC");
                if (mysqli_num_rows($q) == 0): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox" style="font-size: 40px; color: #cbd5e1;"></i>
                            <div class="mt-2">Belum ada transaksi barang masuk</div>
                        </td>
                    </tr>
                <?php else:
                while($r=mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= tglIndo($r['tanggal']) ?></td>
                    <td><strong><?= $r['nama_barang'] ?></strong></td>
                    <td><?= $r['nama_supplier'] ?? '-' ?></td>
                    <td><span class="badge bg-success">+<?= $r['jumlah'] ?> <?= $r['satuan'] ?></span></td>
                    <td><code><?= $r['no_po'] ?: '-' ?></code></td>
                    <td>
                        <a href="config/proses.php?action=hapus_masuk&id=<?= $r['id'] ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Hapus transaksi? Stok akan dikurangi.')">
                            <i class="bi bi-trash-fill"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="modalMasuk">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="config/proses.php">
                    <input type="hidden" name="action" value="barang_masuk">
                    <div class="modal-header" style="background: linear-gradient(135deg, #10b981, #059669); color: #fff;">
                        <h5><i class="bi bi-arrow-down-circle-fill"></i> Barang Masuk</h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Barang <span class="text-danger">*</span></label>
                            <select name="id_barang" class="form-select" required>
                                <option value="">- Pilih Barang -</option>
                                <?php $b=mysqli_query($koneksi,"SELECT * FROM barang ORDER BY nama_barang");
                                while($bb=mysqli_fetch_assoc($b)): ?>
                                <option value="<?= $bb['id'] ?>">
                                    <?= $bb['kode_barang'] ?> - <?= $bb['nama_barang'] ?> 
                                    (Stok: <?= $bb['stok'] ?>)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Supplier</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">- Pilih Supplier -</option>
                                <?php $s=mysqli_query($koneksi,"SELECT * FROM supplier");
                                while($ss=mysqli_fetch_assoc($s)): ?>
                                <option value="<?= $ss['id'] ?>"><?= $ss['nama_supplier'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                                <input type="number" name="jumlah" class="form-control" min="1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" 
                                       value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. PO</label>
                            <input type="text" name="no_po" class="form-control" placeholder="Opsional">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Opsional"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle-fill"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


<?php // ═══════════════════════════════════════
// BARANG KELUAR
// ═══════════════════════════════════════
elseif ($page == 'keluar'):
?>
    <div class="page-header">
        <div>
            <h4><i class="bi bi-arrow-up-circle-fill text-danger"></i> Barang Keluar</h4>
            <small>Catat transaksi barang keluar dari gudang</small>
        </div>
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalKeluar">
            <i class="bi bi-plus-circle"></i> Tambah Barang Keluar
        </button>
    </div>
    <div class="panel">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 60px;">NO</th>
                        <th>TANGGAL</th>
                        <th>BARANG</th>
                        <th>TUJUAN</th>
                        <th>JUMLAH</th>
                        <th>NO SJ</th>
                        <th style="width: 100px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no=1;
                $q = mysqli_query($koneksi, "SELECT bk.*, b.nama_barang, b.satuan 
                    FROM barang_keluar bk 
                    JOIN barang b ON bk.barang_id=b.id 
                    ORDER BY bk.id DESC");
                if (mysqli_num_rows($q) == 0): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox" style="font-size: 40px; color: #cbd5e1;"></i>
                            <div class="mt-2">Belum ada transaksi barang keluar</div>
                        </td>
                    </tr>
                <?php else:
                while($r=mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= tglIndo($r['tanggal']) ?></td>
                    <td><strong><?= $r['nama_barang'] ?></strong></td>
                    <td><?= $r['tujuan'] ?: '-' ?></td>
                    <td><span class="badge bg-danger">-<?= $r['jumlah'] ?> <?= $r['satuan'] ?></span></td>
                    <td><code><?= $r['no_surat_jalan'] ?: '-' ?></code></td>
                    <td>
                        <a href="config/proses.php?action=hapus_keluar&id=<?= $r['id'] ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Hapus transaksi? Stok akan dikembalikan.')">
                            <i class="bi bi-trash-fill"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="modalKeluar">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="config/proses.php">
                    <input type="hidden" name="action" value="barang_keluar">
                    <div class="modal-header" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff;">
                        <h5><i class="bi bi-arrow-up-circle-fill"></i> Barang Keluar</h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Barang <span class="text-danger">*</span></label>
                            <select name="id_barang" class="form-select" required>
                                <option value="">- Pilih Barang -</option>
                                <?php $b=mysqli_query($koneksi,"SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang");
                                while($bb=mysqli_fetch_assoc($b)): ?>
                                <option value="<?= $bb['id'] ?>">
                                    <?= $bb['kode_barang'] ?> - <?= $bb['nama_barang'] ?> 
                                    (Stok: <?= $bb['stok'] ?>)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                                <input type="number" name="jumlah" class="form-control" min="1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" 
                                       value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tujuan</label>
                            <input type="text" name="tujuan" class="form-control" 
                                   placeholder="Contoh: Divisi IT / Lab A">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Surat Jalan</label>
                            <input type="text" name="no_surat_jalan" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-check-circle-fill"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


<?php // ═══════════════════════════════════════
// LAPORAN
// ═══════════════════════════════════════
elseif ($page == 'laporan'):
?>
    <div class="page-header">
        <div>
            <h4><i class="bi bi-file-earmark-bar-graph-fill text-primary"></i> Laporan</h4>
            <small>Ringkasan data inventaris gudang</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-primary" onclick="cetak('stok')">
                <i class="bi bi-printer-fill"></i> Cetak Stok
            </button>
            <button class="btn btn-success" onclick="cetak('masuk')">
                <i class="bi bi-printer-fill"></i> Cetak Masuk
            </button>
            <button class="btn btn-danger" onclick="cetak('keluar')">
                <i class="bi bi-printer-fill"></i> Cetak Keluar
            </button>
            <button class="btn btn-warning" onclick="cetak('minim')">
                <i class="bi bi-printer-fill"></i> Cetak Minim
            </button>
        </div>
    </div>
    
    <!-- SUMMARY CARDS -->
    <?php
    $total_sku      = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) jml FROM barang"))['jml'];
    $total_masuk    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT IFNULL(SUM(jumlah),0) jml FROM barang_masuk"))['jml'];
    $total_keluar   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT IFNULL(SUM(jumlah),0) jml FROM barang_keluar"))['jml'];
    $total_kritis   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) jml FROM barang WHERE stok <= stok_minimal"))['jml'];
    ?>
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card blue">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total SKU</div>
                        <div class="stat-value mt-3"><?= $total_sku ?></div>
                        <small class="text-muted d-block mt-2"><i class="bi bi-box"></i> Jenis Barang</small>
                    </div>
                    <div class="stat-icon" style="background:#dbeafe; color:#2563eb;">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card green">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Inbound</div>
                        <div class="stat-value mt-3" style="color:#10b981;"><?= $total_masuk ?></div>
                        <small class="text-muted d-block mt-2"><i class="bi bi-arrow-down-circle"></i> Barang Masuk</small>
                    </div>
                    <div class="stat-icon" style="background:#d1fae5; color:#10b981;">
                        <i class="bi bi-arrow-down-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card yellow">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Outbound</div>
                        <div class="stat-value mt-3" style="color:#f59e0b;"><?= $total_keluar ?></div>
                        <small class="text-muted d-block mt-2"><i class="bi bi-arrow-up-circle"></i> Barang Keluar</small>
                    </div>
                    <div class="stat-icon" style="background:#fef3c7; color:#f59e0b;">
                        <i class="bi bi-arrow-up-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card cyan">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Stok Kritis</div>
                        <div class="stat-value mt-3" style="color:#ef4444;"><?= $total_kritis ?></div>
                        <small class="text-muted d-block mt-2"><i class="bi bi-exclamation-triangle"></i> Perlu Restock</small>
                    </div>
                    <div class="stat-icon" style="background:#fee2e2; color:#ef4444;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="panel">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="panel-title">
                            <i class="bi bi-stack text-primary"></i> Laporan Stok Barang
                        </div>
                        <div class="panel-subtitle mb-0">Daftar seluruh barang dan stok saat ini</div>
                    </div>
                    <button class="btn btn-sm btn-primary" onclick="cetak('stok')">
                        <i class="bi bi-printer-fill"></i> Cetak
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>KODE</th>
                                <th>NAMA</th>
                                <th>STOK</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $q=mysqli_query($koneksi,"SELECT * FROM barang ORDER BY stok ASC");
                        while($r=mysqli_fetch_assoc($q)): ?>
                        <tr>
                            <td><code><?= $r['kode_barang'] ?></code></td>
                            <td><?= $r['nama_barang'] ?></td>
                            <td><strong><?= $r['stok'] ?></strong> <?= $r['satuan'] ?></td>
                            <td><?= badgeStok($r['stok'], $r['stok_minimal']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="panel">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="panel-title">
                            <i class="bi bi-exclamation-triangle-fill text-danger"></i> Laporan Stok Minim
                        </div>
                        <div class="panel-subtitle mb-0">Barang yang perlu segera di-restock</div>
                    </div>
                    <button class="btn btn-sm btn-warning" onclick="cetak('minim')">
                        <i class="bi bi-printer-fill"></i> Cetak
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>KODE</th>
                                <th>NAMA</th>
                                <th>STOK</th>
                                <th>KURANG</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $q=mysqli_query($koneksi,"SELECT *, (stok_minimal-stok) as kurang FROM barang WHERE stok <= stok_minimal ORDER BY kurang DESC");
                        if (mysqli_num_rows($q) == 0): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 28px;"></i>
                                    <div class="mt-2">Semua stok aman</div>
                                </td>
                            </tr>
                        <?php else:
                        while($r=mysqli_fetch_assoc($q)): ?>
                        <tr>
                            <td><code><?= $r['kode_barang'] ?></code></td>
                            <td><?= $r['nama_barang'] ?></td>
                            <td><span class="badge bg-danger"><?= $r['stok'] ?></span></td>
                            <td><strong class="text-danger">-<?= $r['kurang'] ?></strong></td>
                        </tr>
                        <?php endwhile; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="panel">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="panel-title">
                            <i class="bi bi-arrow-down-circle-fill text-success"></i> Riwayat Barang Masuk
                        </div>
                        <div class="panel-subtitle mb-0">10 transaksi terakhir</div>
                    </div>
                    <button class="btn btn-sm btn-success" onclick="cetak('masuk')">
                        <i class="bi bi-printer-fill"></i> Cetak
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>TANGGAL</th>
                                <th>BARANG</th>
                                <th>JUMLAH</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $q=mysqli_query($koneksi,"SELECT bm.*, b.nama_barang FROM barang_masuk bm 
                            JOIN barang b ON bm.barang_id=b.id ORDER BY bm.id DESC LIMIT 10");
                        if (mysqli_num_rows($q) == 0): ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data</td></tr>
                        <?php else:
                        while($r=mysqli_fetch_assoc($q)): ?>
                        <tr>
                            <td><?= tglIndo($r['tanggal']) ?></td>
                            <td><?= $r['nama_barang'] ?></td>
                            <td><span class="badge bg-success">+<?= $r['jumlah'] ?></span></td>
                        </tr>
                        <?php endwhile; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="panel">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="panel-title">
                            <i class="bi bi-arrow-up-circle-fill text-danger"></i> Riwayat Barang Keluar
                        </div>
                        <div class="panel-subtitle mb-0">10 transaksi terakhir</div>
                    </div>
                    <button class="btn btn-sm btn-danger" onclick="cetak('keluar')">
                        <i class="bi bi-printer-fill"></i> Cetak
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>TANGGAL</th>
                                <th>BARANG</th>
                                <th>JUMLAH</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $q=mysqli_query($koneksi,"SELECT bk.*, b.nama_barang FROM barang_keluar bk 
                            JOIN barang b ON bk.barang_id=b.id ORDER BY bk.id DESC LIMIT 10");
                        if (mysqli_num_rows($q) == 0): ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data</td></tr>
                        <?php else:
                        while($r=mysqli_fetch_assoc($q)): ?>
                        <tr>
                            <td><?= tglIndo($r['tanggal']) ?></td>
                            <td><?= $r['nama_barang'] ?></td>
                            <td><span class="badge bg-danger">-<?= $r['jumlah'] ?></span></td>
                        </tr>
                        <?php endwhile; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ MODAL CETAK ═══ -->
    <div class="modal fade" id="modalCetak" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="GET" action="cetak_laporan.php" target="_blank">
                    <input type="hidden" name="jenis" id="cetak_jenis" value="stok">
                    <div class="modal-header" style="background: linear-gradient(135deg, #2563eb, #1e40af); color: #fff;">
                        <h5><i class="bi bi-printer-fill"></i> Cetak Laporan</h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info py-2 mb-3" style="font-size:13px;">
                            <i class="bi bi-info-circle-fill"></i> Pilih periode lalu klik <strong>Buka Halaman Cetak</strong>. Halaman akan terbuka di tab baru dan siap di-print / save as PDF.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Laporan</label>
                            <input type="text" id="cetak_jenis_label" class="form-control" readonly>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" name="dari" class="form-control" 
                                       value="<?= date('Y-m-01') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" name="sampai" class="form-control" 
                                       value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-lightbulb"></i> 
                            Periode hanya berlaku untuk laporan <strong>Masuk</strong> dan <strong>Keluar</strong>.
                        </small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-printer-fill"></i> Buka Halaman Cetak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
    function cetak(jenis) {
        const label = {
            'stok':   'Laporan Stok Barang',
            'masuk':  'Laporan Barang Masuk',
            'keluar': 'Laporan Barang Keluar',
            'minim':  'Laporan Stok Minim'
        };
        document.getElementById('cetak_jenis').value = jenis;
        document.getElementById('cetak_jenis_label').value = label[jenis];
        new bootstrap.Modal(document.getElementById('modalCetak')).show();
    }
    </script>

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>