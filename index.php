
<?php
require_once 'config/koneksi.php';

$page  = $_GET['page'] ?? 'beranda';
$msg   = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';

// Halaman mandiri: jangan muat header utama lebih dulu
if ($page === 'kartu_stok') {
    require 'kartu_stok.php';
    exit;
}

require_once 'includes/header.php';
?>
<!-- NOTIFIKASI -->
<?php if ($msg == 'sukses'): ?>
<div class="alert alert-success alert-dismissible fade show" id="notifAlert">
    <i class="bi bi-check-circle-fill me-2"></i> <strong>Berhasil!</strong> Data telah disimpan.
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php elseif ($msg == 'edit'): ?>
<div class="alert alert-info alert-dismissible fade show" id="notifAlert">
    <i class="bi bi-pencil-fill me-2"></i> <strong>Update!</strong> Data telah diperbarui.
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php elseif ($msg == 'hapus'): ?>
<div class="alert alert-warning alert-dismissible fade show" id="notifAlert">
    <i class="bi bi-trash-fill me-2"></i> <strong>Terhapus!</strong> Data telah dihapus.
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php elseif ($error == 'stok'): ?>
<div class="alert alert-danger alert-dismissible fade show" id="notifAlert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Stok tidak cukup!</strong> Transaksi dibatalkan.
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php elseif ($error == 'jumlah'): ?>
<div class="alert alert-danger alert-dismissible fade show" id="notifAlert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Jumlah tidak valid!</strong> Harus lebih dari 0.
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
?>

    <div class="hero-banner">
        <span class="hero-badge">LABORATORIUM LOGISTIK UNJANI</span>
        <h1>Sistem Manajemen Gudang UNJANI</h1>
        <p>Kelola data persediaan serta pantau pergerakan barang masuk dan keluar gudang.</p>
    </div>
    
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card blue">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Jenis Barang</div>
                        <div class="stat-value mt-3"><?= $total_barang ?></div>
                        <small class="text-muted d-block mt-2"><i class="bi bi-box"></i> Jenis barang terdaftar</small>
                    </div>
                    <div class="stat-icon" style="background:#dbeafe;color:#2563eb;"><i class="bi bi-box-seam-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card green">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Masuk (Hari Ini)</div>
                        <div class="stat-value mt-3 text-success"><?= $masuk_hari ?></div>
                        <small class="text-muted d-block mt-2"><i class="bi bi-arrow-down-circle"></i>  Jumlah barang masuk</small>
                    </div>
                    <div class="stat-icon" style="background:#d1fae5;color:#10b981;"><i class="bi bi-arrow-down-circle-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card yellow">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Keluar (Hari Ini)</div>
                        <div class="stat-value mt-3" style="color:#f59e0b;"><?= $keluar_hari ?></div>
                        <small class="text-muted d-block mt-2"><i class="bi bi-arrow-up-circle"></i>  Jumlah barang keluar</small>
                    </div>
                    <div class="stat-icon" style="background:#fef3c7;color:#f59e0b;"><i class="bi bi-arrow-up-circle-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card cyan">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Volume Stok</div>
                        <div class="stat-value mt-3" style="color:#06b6d4;"><?= $total_stok ?></div>
                        <small class="text-muted d-block mt-2"><i class="bi bi-stack"></i> jumlah seluruh Stock</small>
                    </div>
                    <div class="stat-icon" style="background:#cffafe;color:#06b6d4;"><i class="bi bi-stack"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="panel">
                <div class="panel-title"><i class="bi bi-graph-up-arrow text-primary"></i> Tren Mutasi Stok</div>
                <div class="panel-subtitle">Perbandingan arus barang masuk dan keluar 7 hari terakhir</div>
                <div style="height: 300px; position: relative;"><canvas id="chartMutasi"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-title"><i class="bi bi-pie-chart-fill text-primary"></i> Kapasitas Gudang</div>
                <div class="panel-subtitle">Estimasi beban area penyimpanan rak</div>
                <?php
                $zona = mysqli_query($koneksi, "SELECT k.nama_kategori, k.kode_zona, COUNT(b.id) as jml_barang, IFNULL(SUM(b.stok),0) as total_stok FROM kategori k LEFT JOIN barang b ON b.kategori_id = k.id GROUP BY k.id ORDER BY k.kode_zona");
                $warna_map = ['A'=>'#2563eb','B'=>'#10b981','C'=>'#06b6d4','D'=>'#f59e0b'];
                while ($z = mysqli_fetch_assoc($zona)):
                    $persen = min(100, $z['jml_barang'] * 15);
                    $w = $warna_map[$z['kode_zona']] ?? '#6b7280';
                ?>
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold" style="font-size:13.5px;">
                            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:<?= $w ?>;margin-right:6px;"></span>
                            Zona <?= $z['kode_zona'] ?> <span class="text-muted" style="font-weight:400;font-size:12.5px;">(<?= $z['nama_kategori'] ?>)</span>
                        </span>
                        <strong style="color:<?= $w ?>;font-size:15px;"><?= $persen ?>%</strong>
                    </div>
                    <div class="progress"><div class="progress-bar" style="width:<?= $persen ?>%;background:<?= $w ?>;"></div></div>
                    <small class="text-muted d-block mt-1" style="font-size:12px;"><?= $z['jml_barang'] ?> jenis · <?= $z['total_stok'] ?> unit</small>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    
    <!-- TOP 5 & STATISTIK CEPAT -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-title"><i class="bi bi-trophy-fill text-warning"></i> Top 5 Barang Sering Keluar</div>
                <div class="panel-subtitle">Berdasarkan total frekuensi keluar</div>
                <?php
                $top5 = mysqli_query($koneksi, "
                    SELECT b.nama_barang, b.kode_barang, SUM(bk.jumlah) AS total
                    FROM barang_keluar bk 
                    JOIN barang b ON bk.barang_id = b.id 
                    GROUP BY b.id 
                    ORDER BY total DESC 
                    LIMIT 5
                ");
                $max_val = 0;
                $top_data = [];
                while ($t = mysqli_fetch_assoc($top5)) {
                    $top_data[] = $t;
                    if ($t['total'] > $max_val) $max_val = $t['total'];
                }
                if (count($top_data) == 0): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-bar-chart" style="font-size:40px;opacity:0.3;"></i>
                        <div class="mt-2">Belum ada data transaksi keluar</div>
                    </div>
                <?php else:
                    $rank = 1;
                    $badges = ['🥇', '🥈', '🥉', '4️⃣', '5️⃣'];
                    foreach ($top_data as $t):
                        $persen = $max_val > 0 ? ($t['total'] / $max_val * 100) : 0;
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span style="font-size:13.5px;font-weight:600;">
                            <?= $badges[$rank-1] ?> <?= $t['nama_barang'] ?>
                            <small class="text-muted">(<?= $t['kode_barang'] ?>)</small>
                        </span>
                        <strong style="color:#2563eb;"><?= $t['total'] ?> unit</strong>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar" style="width:<?= $persen ?>%;background:linear-gradient(90deg,#3b82f6,#1d4ed8);"></div>
                    </div>
                </div>
                <?php 
                    $rank++;
                    endforeach;
                endif; ?>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-title"><i class="bi bi-info-circle-fill text-info"></i> Statistik Cepat</div>
                <div class="panel-subtitle">Ringkasan kondisi gudang</div>
                <?php
                $stok_kritis = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM barang WHERE stok <= stok_minimal"))['c'];
                $stok_habis = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM barang WHERE stok = 0"))['c'];
                $total_transaksi = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT (SELECT COUNT(*) FROM barang_masuk) + (SELECT COUNT(*) FROM barang_keluar) c"))['c'];
                $nilai_inventaris = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT IFNULL(SUM(stok * harga), 0) t FROM barang"))['t'];
                ?>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3" style="background:#fef3c7;border-radius:10px;border-left:4px solid #f59e0b;">
                            <small class="text-muted d-block">Stok Kritis</small>
                            <strong style="font-size:24px;color:#d97706;"><?= $stok_kritis ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3" style="background:#fee2e2;border-radius:10px;border-left:4px solid #ef4444;">
                            <small class="text-muted d-block">Stok Habis</small>
                            <strong style="font-size:24px;color:#dc2626;"><?= $stok_habis ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3" style="background:#dbeafe;border-radius:10px;border-left:4px solid #2563eb;">
                            <small class="text-muted d-block">Total Transaksi</small>
                            <strong style="font-size:24px;color:#1e40af;"><?= $total_transaksi ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3" style="background:#d1fae5;border-radius:10px;border-left:4px solid #10b981;">
                            <small class="text-muted d-block">Nilai Inventaris</small>
                            <strong style="font-size:16px;color:#065f46;"><?= rupiah($nilai_inventaris) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="panel mb-4">
        <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
            <div>
                <div class="panel-title"><i class="bi bi-exclamation-triangle-fill text-danger"></i> Peringatan Stok Minimis</div>
                <div class="panel-subtitle mb-0">Barang berikut perlu diperiksa dan diisi kembali.</div>
            </div>
            <a href="index.php?page=laporan" class="btn btn-sm btn-outline-primary">Lihat Semua <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>BARANG</th><th>KODE SKU</th><th>LOKASI RAK</th><th>SISA STOK</th><th>STATUS</th><th>AKSI</th></tr></thead>
                <tbody>
                <?php
                $q = mysqli_query($koneksi, "SELECT * FROM barang WHERE stok <= stok_minimal ORDER BY stok ASC");
                if (mysqli_num_rows($q) == 0): ?>
                    <tr><td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-check-circle-fill text-success" style="font-size:40px;"></i>
                        <div class="mt-2 fw-semibold">Semua stok aman!</div>
                        <small>Tidak ada barang yang perlu di-restock</small>
                    </td></tr>
                <?php else: while ($r = mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td><strong><?= $r['nama_barang'] ?></strong></td>
                    <td><code><?= $r['kode_barang'] ?></code></td>
                    <td><?= $r['lokasi_rak'] ?></td>
                    <td><strong class="text-danger"><?= $r['stok'] ?> <?= $r['satuan'] ?></strong></td>
                    <td><?= badgeStok($r['stok'], $r['stok_minimal']) ?></td>
                    <td><a href="index.php?page=masuk" class="btn btn-sm btn-outline-success"><i class="bi bi-plus-circle"></i> Restock</a></td>
                </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    <?php
    $hari_indo = ['Sun'=>'Min','Mon'=>'Sen','Tue'=>'Sel','Wed'=>'Rab','Thu'=>'Kam','Fri'=>'Jum','Sat'=>'Sab'];
    $labels = []; $data_masuk = []; $data_keluar = [];
    for ($i = 6; $i >= 0; $i--) {
        $tgl = date('Y-m-d', strtotime("-$i days"));
        $labels[] = $hari_indo[date('D', strtotime($tgl))];
        $data_masuk[] = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT IFNULL(SUM(jumlah),0) jml FROM barang_masuk WHERE tanggal='$tgl'"))['jml'];
        $data_keluar[] = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT IFNULL(SUM(jumlah),0) jml FROM barang_keluar WHERE tanggal='$tgl'"))['jml'];
    }
    ?>
    new Chart(document.getElementById('chartMutasi'), {
        type: 'bar',
        data: { labels: <?= json_encode($labels) ?>, datasets: [
            { label: 'Barang Masuk', data: <?= json_encode($data_masuk) ?>, backgroundColor: 'rgba(37,99,235,0.85)', borderRadius: 8, barPercentage: 0.7 },
            { label: 'Barang Keluar', data: <?= json_encode($data_keluar) ?>, backgroundColor: 'rgba(245,158,11,0.85)', borderRadius: 8, barPercentage: 0.7 }
        ]},
        options: { responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'top', align: 'end', labels: { usePointStyle: true, padding: 16 } } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#f1f5f9', drawBorder: false } }, x: { grid: { display: false } } }
        }
    });
    </script>


<?php // ═══════════════════════════════════════
// DATA BARANG
// ═══════════════════════════════════════
elseif ($page == 'barang'):
    $edit = null;
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $edit_id = (int)$_GET['edit'];
        $edit_q = mysqli_query($koneksi, "SELECT * FROM barang WHERE id='$edit_id'");
        if ($edit_q && mysqli_num_rows($edit_q) > 0) $edit = mysqli_fetch_assoc($edit_q);
    }
    $search = mysqli_real_escape_string($koneksi, $_GET['search'] ?? '');
    $filter_kat = (int)($_GET['filter_kat'] ?? 0);
    $where = [];
    if (!empty($search)) $where[] = "(b.nama_barang LIKE '%$search%' OR b.kode_barang LIKE '%$search%')";
    if ($filter_kat > 0) $where[] = "b.kategori_id='$filter_kat'";
    $where_sql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';
?>
    <div class="page-header">
        <div><h4><i class="bi bi-box-seam text-primary"></i> Data Barang</h4><small>Kelola master data barang gudang</small></div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBarang"><i class="bi bi-plus-circle"></i> Tambah Barang</button>
    </div>
    <div class="panel mb-3">
        <form method="GET" action="index.php" class="row g-2">
            <input type="hidden" name="page" value="barang">
            <div class="col-md-5"><input type="text" name="search" class="form-control" placeholder="🔍 Cari nama atau kode barang..." value="<?= htmlspecialchars($search) ?>"></div>
            <div class="col-md-4"><select name="filter_kat" class="form-select">
                <option value="0">Semua Kategori</option>
                <?php $kat_q = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori"); while ($k = mysqli_fetch_assoc($kat_q)): ?>
                <option value="<?= $k['id'] ?>" <?= $filter_kat == $k['id'] ? 'selected' : '' ?>><?= $k['nama_kategori'] ?></option>
                <?php endwhile; ?>
            </select></div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search"></i> Cari</button>
                <a href="index.php?page=barang" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>
    <div class="panel">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th style="width:60px;">NO</th><th>KODE</th><th>NAMA BARANG</th><th>KATEGORI</th><th>STOK</th><th>RAK</th><th>HARGA</th><th style="width:120px;">AKSI</th></tr></thead>
                <tbody>
                <?php
                $no = 1;
                $q = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori FROM barang b LEFT JOIN kategori k ON b.kategori_id=k.id $where_sql ORDER BY b.id DESC");
                if (mysqli_num_rows($q) == 0): ?>
                    <tr><td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-inbox" style="font-size:40px;color:#cbd5e1;"></i>
                        <div class="mt-2"><?= (!empty($search) || $filter_kat > 0) ? 'Tidak ada barang yang cocok' : 'Belum ada data barang' ?></div>
                    </td></tr>
                <?php else: while ($r = mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><code><?= $r['kode_barang'] ?></code></td>
                    <td><strong><?= $r['nama_barang'] ?></strong></td>
                    <td><?= $r['nama_kategori'] ?? '-' ?></td>
                    <td><strong><?= $r['stok'] ?></strong> <?= $r['satuan'] ?> <?= badgeStok($r['stok'], $r['stok_minimal']) ?></td>
                    <td><?= $r['lokasi_rak'] ?></td>
                    <td><?= rupiah($r['harga']) ?></td>
                    <td>
                        <a href="index.php?page=barang&edit=<?= $r['id'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                        <a href="config/proses.php?action=hapus_barang&id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin hapus <?= $r['nama_barang'] ?>?')"><i class="bi bi-trash-fill"></i></a>
                    </td>
                </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="modalBarang" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="config/proses.php">
                    <input type="hidden" name="action" value="simpan_barang">
                    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
                    <div class="modal-header" style="background:linear-gradient(135deg,#2563eb,#1e40af);color:#fff;">
                        <h5><i class="bi bi-box-seam"></i> <?= $edit ? 'Edit Barang' : 'Tambah Barang' ?></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Kode Barang <span class="text-danger">*</span></label><input type="text" name="kode_barang" class="form-control" value="<?= htmlspecialchars($edit['kode_barang'] ?? '') ?>" placeholder="Contoh: BRG-001" required></div>
                        <div class="mb-3"><label class="form-label">Nama Barang <span class="text-danger">*</span></label><input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($edit['nama_barang'] ?? '') ?>" placeholder="Contoh: Mouse Logitech" required></div>
                        <div class="mb-3"><label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_id" class="form-select" required>
                                <option value="">- Pilih Kategori -</option>
                                <?php $k = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori"); while ($kk = mysqli_fetch_assoc($k)): ?>
                                <option value="<?= $kk['id'] ?>" <?= ($edit && $edit['kategori_id']==$kk['id'])?'selected':'' ?>><?= $kk['nama_kategori'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Satuan</label><input type="text" name="satuan" class="form-control" value="<?= htmlspecialchars($edit['satuan'] ?? 'pcs') ?>" placeholder="pcs / box / roll"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Stok</label><input type="number" name="stok" class="form-control" value="<?= $edit['stok'] ?? 0 ?>" min="0"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Stok Minimal</label><input type="number" name="stok_minimal" class="form-control" value="<?= $edit['stok_minimal'] ?? 5 ?>" min="0"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Lokasi Rak</label><input type="text" name="lokasi_rak" class="form-control" value="<?= htmlspecialchars($edit['lokasi_rak'] ?? '') ?>" placeholder="Contoh: Rak A-01"></div>
                        </div>
                        <div class="mb-3"><label class="form-label">Harga (Rp)</label><input type="number" name="harga" class="form-control" value="<?= $edit['harga'] ?? 0 ?>" min="0"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle-fill"></i> <?= $edit ? 'Update' : 'Simpan' ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php if ($edit): ?>
    <script>document.addEventListener('DOMContentLoaded', function() { new bootstrap.Modal(document.getElementById('modalBarang')).show(); });</script>
    <?php endif; ?>


<?php // ═══════════════════════════════════════
// KATEGORI
// ═══════════════════════════════════════
elseif ($page == 'kategori'):
    $edit = null;
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $edit_id = (int)$_GET['edit'];
        $edit_q = mysqli_query($koneksi, "SELECT * FROM kategori WHERE id='$edit_id'");
        if ($edit_q && mysqli_num_rows($edit_q) > 0) $edit = mysqli_fetch_assoc($edit_q);
    }
?>
    <div class="page-header">
        <div><h4><i class="bi bi-tags-fill text-primary"></i> Kategori Barang</h4><small>Kelola kategori dan zona gudang</small></div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalKategori"><i class="bi bi-plus-circle"></i> Tambah Kategori</button>
    </div>
    <div class="panel">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th style="width:60px;">NO</th><th>NAMA KATEGORI</th><th>ZONA</th><th>KETERANGAN</th><th style="width:120px;">AKSI</th></tr></thead>
                <tbody>
                <?php $no=1; $q=mysqli_query($koneksi,"SELECT * FROM kategori ORDER BY id");
                while($r=mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= $r['nama_kategori'] ?></strong></td>
                    <td><span class="badge bg-primary">Zona <?= $r['kode_zona'] ?></span></td>
                    <td><?= $r['keterangan'] ?></td>
                    <td>
                        <a href="index.php?page=kategori&edit=<?= $r['id'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                        <a href="config/proses.php?action=hapus_kategori&id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus kategori ini?')"><i class="bi bi-trash-fill"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="modalKategori" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="config/proses.php">
                    <input type="hidden" name="action" value="simpan_kategori">
                    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
                    <div class="modal-header" style="background:linear-gradient(135deg,#2563eb,#1e40af);color:#fff;">
                        <h5><i class="bi bi-tag-fill"></i> <?= $edit ? 'Edit' : 'Tambah' ?> Kategori</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Nama Kategori <span class="text-danger">*</span></label><input type="text" name="nama_kategori" class="form-control" value="<?= htmlspecialchars($edit['nama_kategori'] ?? '') ?>" required></div>
                        <div class="mb-3"><label class="form-label">Kode Zona <span class="text-danger">*</span></label><input type="text" name="kode_zona" class="form-control" value="<?= htmlspecialchars($edit['kode_zona'] ?? '') ?>" maxlength="2" required></div>
                        <div class="mb-3"><label class="form-label">Keterangan</label><textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($edit['keterangan'] ?? '') ?></textarea></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle-fill"></i> <?= $edit ? 'Update' : 'Simpan' ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php if ($edit): ?>
    <script>document.addEventListener('DOMContentLoaded', function() { new bootstrap.Modal(document.getElementById('modalKategori')).show(); });</script>
    <?php endif; ?>


<?php // ═══════════════════════════════════════
// SUPPLIER
// ═══════════════════════════════════════
elseif ($page == 'supplier'):
    $edit = null;
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $edit_id = (int)$_GET['edit'];
        $edit_q = mysqli_query($koneksi, "SELECT * FROM supplier WHERE id='$edit_id'");
        if ($edit_q && mysqli_num_rows($edit_q) > 0) $edit = mysqli_fetch_assoc($edit_q);
    }
?>
    <div class="page-header">
        <div><h4><i class="bi bi-truck text-primary"></i> Data Supplier</h4><small>Kelola data pemasok barang</small></div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSupplier"><i class="bi bi-plus-circle"></i> Tambah Supplier</button>
    </div>
    <div class="panel">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th style="width:60px;">NO</th><th>NAMA SUPPLIER</th><th>ALAMAT</th><th>TELEPON</th><th style="width:120px;">AKSI</th></tr></thead>
                <tbody>
                <?php $no=1; $q=mysqli_query($koneksi,"SELECT * FROM supplier ORDER BY id");
                while($r=mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= $r['nama_supplier'] ?></strong></td>
                    <td><?= $r['alamat'] ?></td>
                    <td><?= $r['telepon'] ?></td>
                    <td>
                        <a href="index.php?page=supplier&edit=<?= $r['id'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                        <a href="config/proses.php?action=hapus_supplier&id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus supplier ini?')"><i class="bi bi-trash-fill"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="modalSupplier" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="config/proses.php">
                    <input type="hidden" name="action" value="simpan_supplier">
                    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
                    <div class="modal-header" style="background:linear-gradient(135deg,#2563eb,#1e40af);color:#fff;">
                        <h5><i class="bi bi-truck"></i> <?= $edit ? 'Edit' : 'Tambah' ?> Supplier</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Nama Supplier <span class="text-danger">*</span></label><input type="text" name="nama_supplier" class="form-control" value="<?= htmlspecialchars($edit['nama_supplier'] ?? '') ?>" required></div>
                        <div class="mb-3"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($edit['alamat'] ?? '') ?></textarea></div>
                        <div class="mb-3"><label class="form-label">Telepon</label><input type="text" name="telepon" class="form-control" value="<?= htmlspecialchars($edit['telepon'] ?? '') ?>"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle-fill"></i> <?= $edit ? 'Update' : 'Simpan' ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php if ($edit): ?>
    <script>document.addEventListener('DOMContentLoaded', function() { new bootstrap.Modal(document.getElementById('modalSupplier')).show(); });</script>
    <?php endif; ?>


<?php // ═══════════════════════════════════════
// BARANG MASUK
// ═══════════════════════════════════════
elseif ($page == 'masuk'):
    $search = mysqli_real_escape_string($koneksi, $_GET['search'] ?? '');
    $where = !empty($search) ? "WHERE b.nama_barang LIKE '%$search%' OR b.kode_barang LIKE '%$search%'" : '';
?>
    <div class="page-header">
        <div><h4><i class="bi bi-arrow-down-circle-fill text-success"></i> Barang Masuk</h4><small>Catat transaksi barang masuk ke gudang</small></div>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalMasuk"><i class="bi bi-plus-circle"></i> Tambah Barang Masuk</button>
    </div>
    <div class="panel mb-3">
        <form method="GET" action="index.php" class="row g-2">
            <input type="hidden" name="page" value="masuk">
            <div class="col-md-9"><input type="text" name="search" class="form-control" placeholder="🔍 Cari barang..." value="<?= htmlspecialchars($search) ?>"></div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search"></i> Cari</button>
                <a href="index.php?page=masuk" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>
    <div class="panel">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th style="width:60px;">NO</th><th>TANGGAL</th><th>BARANG</th><th>SUPPLIER</th><th>JUMLAH</th><th>NO PO</th><th style="width:100px;">AKSI</th></tr></thead>
                <tbody>
                <?php $no=1;
                $q = mysqli_query($koneksi, "SELECT bm.*, b.nama_barang, b.satuan, s.nama_supplier FROM barang_masuk bm JOIN barang b ON bm.barang_id=b.id LEFT JOIN supplier s ON bm.supplier_id=s.id $where ORDER BY bm.id DESC");
                if (mysqli_num_rows($q) == 0): ?>
                    <tr><td colspan="7" class="text-center text-muted py-5"><i class="bi bi-inbox" style="font-size:40px;color:#cbd5e1;"></i><div class="mt-2">Belum ada transaksi barang masuk</div></td></tr>
                <?php else: while($r=mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= tglIndo($r['tanggal']) ?></td>
                    <td><strong><?= $r['nama_barang'] ?></strong></td>
                    <td><?= $r['nama_supplier'] ?? '-' ?></td>
                    <td><span class="badge bg-success">+<?= $r['jumlah'] ?> <?= $r['satuan'] ?></span></td>
                    <td><code><?= $r['no_po'] ?: '-' ?></code></td>
                    <td><a href="config/proses.php?action=hapus_masuk&id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus transaksi? Stok akan dikurangi.')"><i class="bi bi-trash-fill"></i></a></td>
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
                    <div class="modal-header" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;">
                        <h5><i class="bi bi-arrow-down-circle-fill"></i> Barang Masuk</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Barang <span class="text-danger">*</span></label>
                            <select name="id_barang" class="form-select" required>
                                <option value="">- Pilih Barang -</option>
                                <?php $b=mysqli_query($koneksi,"SELECT * FROM barang ORDER BY nama_barang"); while($bb=mysqli_fetch_assoc($b)): ?>
                                <option value="<?= $bb['id'] ?>"><?= $bb['kode_barang'] ?> - <?= $bb['nama_barang'] ?> (Stok: <?= $bb['stok'] ?>)</option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label">Supplier</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">- Pilih Supplier -</option>
                                <?php $s=mysqli_query($koneksi,"SELECT * FROM supplier"); while($ss=mysqli_fetch_assoc($s)): ?>
                                <option value="<?= $ss['id'] ?>"><?= $ss['nama_supplier'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Jumlah <span class="text-danger">*</span></label><input type="number" name="jumlah" class="form-control" min="1" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Tanggal <span class="text-danger">*</span></label><input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
                        </div>
                        <div class="mb-3"><label class="form-label">No. PO</label><input type="text" name="no_po" class="form-control" placeholder="Opsional"></div>
                        <div class="mb-3"><label class="form-label">Keterangan</label><textarea name="keterangan" class="form-control" rows="2"></textarea></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success"><i class="bi bi-check-circle-fill"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


<?php // ═══════════════════════════════════════
// BARANG KELUAR
// ═══════════════════════════════════════
elseif ($page == 'keluar'):
    $search = mysqli_real_escape_string($koneksi, $_GET['search'] ?? '');
    $where = !empty($search) ? "WHERE b.nama_barang LIKE '%$search%' OR b.kode_barang LIKE '%$search%'" : '';
?>
    <div class="page-header">
        <div><h4><i class="bi bi-arrow-up-circle-fill text-danger"></i> Barang Keluar</h4><small>Catat transaksi barang keluar dari gudang</small></div>
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalKeluar"><i class="bi bi-plus-circle"></i> Tambah Barang Keluar</button>
    </div>
    <div class="panel mb-3">
        <form method="GET" action="index.php" class="row g-2">
            <input type="hidden" name="page" value="keluar">
            <div class="col-md-9"><input type="text" name="search" class="form-control" placeholder="🔍 Cari barang..." value="<?= htmlspecialchars($search) ?>"></div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search"></i> Cari</button>
                <a href="index.php?page=keluar" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>
    <div class="panel">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th style="width:60px;">NO</th><th>TANGGAL</th><th>BARANG</th><th>TUJUAN</th><th>JUMLAH</th><th>NO SJ</th><th style="width:100px;">AKSI</th></tr></thead>
                <tbody>
                <?php $no=1;
                $q = mysqli_query($koneksi, "SELECT bk.*, b.nama_barang, b.satuan FROM barang_keluar bk JOIN barang b ON bk.barang_id=b.id $where ORDER BY bk.id DESC");
                if (mysqli_num_rows($q) == 0): ?>
                    <tr><td colspan="7" class="text-center text-muted py-5"><i class="bi bi-inbox" style="font-size:40px;color:#cbd5e1;"></i><div class="mt-2">Belum ada transaksi barang keluar</div></td></tr>
                <?php else: while($r=mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= tglIndo($r['tanggal']) ?></td>
                    <td><strong><?= $r['nama_barang'] ?></strong></td>
                    <td><?= $r['tujuan'] ?: '-' ?></td>
                    <td><span class="badge bg-danger">-<?= $r['jumlah'] ?> <?= $r['satuan'] ?></span></td>
                    <td><code><?= $r['no_surat_jalan'] ?: '-' ?></code></td>
                    <td><a href="config/proses.php?action=hapus_keluar&id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus transaksi? Stok akan dikembalikan.')"><i class="bi bi-trash-fill"></i></a></td>
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
                    <div class="modal-header" style="background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;">
                        <h5><i class="bi bi-arrow-up-circle-fill"></i> Barang Keluar</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Barang <span class="text-danger">*</span></label>
                            <select name="id_barang" class="form-select" required>
                                <option value="">- Pilih Barang -</option>
                                <?php $b=mysqli_query($koneksi,"SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang"); while($bb=mysqli_fetch_assoc($b)): ?>
                                <option value="<?= $bb['id'] ?>"><?= $bb['kode_barang'] ?> - <?= $bb['nama_barang'] ?> (Stok: <?= $bb['stok'] ?>)</option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Jumlah <span class="text-danger">*</span></label><input type="number" name="jumlah" class="form-control" min="1" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Tanggal <span class="text-danger">*</span></label><input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
                        </div>
                        <div class="mb-3"><label class="form-label">Tujuan</label><input type="text" name="tujuan" class="form-control" placeholder="Contoh: Divisi IT / Lab A"></div>
                        <div class="mb-3"><label class="form-label">No. Surat Jalan</label><input type="text" name="no_surat_jalan" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">Keterangan</label><textarea name="keterangan" class="form-control" rows="2"></textarea></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger"><i class="bi bi-check-circle-fill"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


<?php 


// RIWAYAT TRANSAKSI
// ═══════════════════════════════════════
elseif ($page == 'riwayat'):
    require 'riwayat_transaksi.php';
    exit;

// ═══════════════════════════════════════
// LAPORAN KATEGORI
// ═══════════════════════════════════════
elseif ($page == 'laporan_kat'):
    require 'laporan_kategori.php';
    exit;

// ═══════════════════════════════════════
// LAPORAN
// ═══════════════════════════════════════
elseif ($page == 'laporan'):
?>
    <div class="page-header">
        <div>
            <h4><i class="bi bi-file-earmark-bar-graph-fill text-primary"></i> Laporan</h4>
            <small>Ringkasan data inventaris gudang</small>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCetak">
            <i class="bi bi-printer-fill"></i> Cetak Laporan
        </button>
    </div>
    
    <div class="modal fade" id="modalCetak" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="GET" action="cetak_laporan.php" target="_blank">
                    <div class="modal-header" style="background:linear-gradient(135deg,#2563eb,#1e40af);color:#fff;">
                        <h5><i class="bi bi-printer-fill"></i> Cetak Laporan</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Jenis Laporan</label>
                            <select name="jenis" class="form-select" required>
                                <option value="stok">Laporan Stok Barang</option>
                                <option value="masuk">Laporan Barang Masuk</option>
                                <option value="keluar">Laporan Barang Keluar</option>
                                <option value="minim">Laporan Stok Minim</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" name="dari" class="form-control" value="<?= date('Y-m-01') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" name="sampai" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-printer-fill"></i> Buka Halaman Cetak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-title"><i class="bi bi-stack text-primary"></i> Laporan Stok Barang</div>
                <div class="panel-subtitle">Daftar seluruh barang dan stok saat ini</div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>KODE</th><th>NAMA</th><th>STOK</th><th>STATUS</th></tr></thead>
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
                <div class="panel-title"><i class="bi bi-exclamation-triangle-fill text-danger"></i> Laporan Stok Minim</div>
                <div class="panel-subtitle">Barang yang perlu segera di-restock</div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>KODE</th><th>NAMA</th><th>STOK</th><th>KURANG</th></tr></thead>
                        <tbody>
                        <?php $q=mysqli_query($koneksi,"SELECT *, (stok_minimal-stok) as kurang FROM barang WHERE stok <= stok_minimal ORDER BY kurang DESC");
                        if (mysqli_num_rows($q) == 0): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">
                                <i class="bi bi-check-circle-fill text-success" style="font-size:28px;"></i>
                                <div class="mt-2">Semua stok aman</div>
                            </td></tr>
                        <?php else: while($r=mysqli_fetch_assoc($q)): ?>
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
                <div class="panel-title"><i class="bi bi-arrow-down-circle-fill text-success"></i> Riwayat Barang Masuk</div>
                <div class="panel-subtitle">10 transaksi terakhir</div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>TANGGAL</th><th>BARANG</th><th>JUMLAH</th></tr></thead>
                        <tbody>
                        <?php $q=mysqli_query($koneksi,"SELECT bm.*, b.nama_barang FROM barang_masuk bm JOIN barang b ON bm.barang_id=b.id ORDER BY bm.id DESC LIMIT 10");
                        if (mysqli_num_rows($q) == 0): ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data</td></tr>
                        <?php else: while($r=mysqli_fetch_assoc($q)): ?>
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
                <div class="panel-title"><i class="bi bi-arrow-up-circle-fill text-danger"></i> Riwayat Barang Keluar</div>
                <div class="panel-subtitle">10 transaksi terakhir</div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>TANGGAL</th><th>BARANG</th><th>JUMLAH</th></tr></thead>
                        <tbody>
                        <?php $q=mysqli_query($koneksi,"SELECT bk.*, b.nama_barang FROM barang_keluar bk JOIN barang b ON bk.barang_id=b.id ORDER BY bk.id DESC LIMIT 10");
                        if (mysqli_num_rows($q) == 0): ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data</td></tr>
                        <?php else: while($r=mysqli_fetch_assoc($q)): ?>
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

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>