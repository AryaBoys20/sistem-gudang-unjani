<?php
require_once 'config/koneksi.php';

$page  = $_GET['page'] ?? 'beranda';
$msg   = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';

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
<?php elseif (!empty($error)): ?>
<div class="alert alert-danger alert-dismissible fade show" id="notifAlert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Error!</strong> <?= htmlspecialchars($error) ?>
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
        <span class="hero-badge"> SISTEM PERGUDANGAN </span>
        <h1>Sistem Manajemen Gudang</h1>
        <p>Pantau sirkulasi barang masuk dan keluar secara real-time untuk operasional gudang yang efektif dan akurat.</p>
    </div>
    
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card blue">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Barang</div>
                        <div class="stat-value mt-3"><?= $total_barang ?></div>
                        <small class="text-muted d-block mt-2">Master SKU</small>
                    </div>
                    <div class="stat-icon" style="background:#dbeafe;color:#2563eb;"><i class="bi bi-box-seam-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card green">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Masuk Hari Ini</div>
                        <div class="stat-value mt-3 text-success"><?= $masuk_hari ?></div>
                        <small class="text-muted d-block mt-2">Total Inbound</small>
                    </div>
                    <div class="stat-icon" style="background:#d1fae5;color:#10b981;"><i class="bi bi-arrow-down-circle-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card yellow">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Keluar Hari Ini</div>
                        <div class="stat-value mt-3" style="color:#f59e0b;"><?= $keluar_hari ?></div>
                        <small class="text-muted d-block mt-2">Total Outbound</small>
                    </div>
                    <div class="stat-icon" style="background:#fef3c7;color:#f59e0b;"><i class="bi bi-arrow-up-circle-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card cyan">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Stok</div>
                        <div class="stat-value mt-3" style="color:#06b6d4;"><?= $total_stok ?></div>
                        <small class="text-muted d-block mt-2">Akumulasi Unit</small>
                    </div>
                    <div class="stat-icon" style="background:#cffafe;color:#06b6d4;"><i class="bi bi-stack"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="panel mb-4">
        <div class="panel-title"><i class="bi bi-graph-up-arrow text-primary"></i> Tren Mutasi Stok 7 Hari Terakhir</div>
        <div class="panel-subtitle">Perbandingan arus barang masuk dan keluar</div>
        <div style="height: 280px; position: relative;"><canvas id="chartMutasi"></canvas></div>
    </div>
    
    <div class="panel">
        <div class="panel-title"><i class="bi bi-exclamation-triangle-fill text-danger"></i> Peringatan Stok Minimis</div>
        <div class="table-responsive mt-3">
            <table class="table align-middle mb-0">
                <thead><tr><th>BARANG</th><th>KODE SKU</th><th>SISA STOK</th><th>STATUS</th><th>AKSI</th></tr></thead>
                <tbody>
                <?php
                $q = mysqli_query($koneksi, "SELECT * FROM barang WHERE stok <= stok_minimal ORDER BY stok ASC");
                if (mysqli_num_rows($q) == 0): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Semua stok aman!</td></tr>
                <?php else: while ($r = mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td><strong><?= $r['nama_barang'] ?></strong></td>
                    <td><code><?= $r['kode_barang'] ?></code></td>
                    <td><strong class="text-danger"><?= $r['stok'] ?> <?= $r['satuan'] ?></strong></td>
                    <td><?= badgeStok($r['stok'], $r['stok_minimal']) ?></td>
                    <td><a href="index.php?page=masuk" class="btn btn-sm btn-outline-success">Restock</a></td>
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
            { label: 'Barang Masuk', data: <?= json_encode($data_masuk) ?>, backgroundColor: 'rgba(37,99,235,0.85)', borderRadius: 8 },
            { label: 'Barang Keluar', data: <?= json_encode($data_keluar) ?>, backgroundColor: 'rgba(245,158,11,0.85)', borderRadius: 8 }
        ]},
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top', align: 'end' } } }
    });
    </script>

<?php // ═══════════════════════════════════════
// DATA BARANG (Hanya Tampil & Hapus)
// ═══════════════════════════════════════
elseif ($page == 'barang'):
    $search = mysqli_real_escape_string($koneksi, $_GET['search'] ?? '');
    $where = !empty($search) ? "WHERE b.nama_barang LIKE '%$search%' OR b.kode_barang LIKE '%$search%'" : '';
?>
    <div class="page-header mb-3">
        <div><h4><i class="bi bi-box-seam text-primary"></i> Data Barang</h4></div>
    </div>
    <div class="panel mb-3">
        <form method="GET" action="index.php" class="row g-2">
            <input type="hidden" name="page" value="barang">
            <div class="col-md-9"><input type="text" name="search" class="form-control" placeholder="🔍 Cari nama atau kode barang..." value="<?= htmlspecialchars($search) ?>"></div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search"></i> Cari</button>
                <a href="index.php?page=barang" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>
    <div class="panel">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>NO</th><th>KODE</th><th>NAMA BARANG</th><th>KATEGORI</th><th>STOK</th><th>RAK</th><th>AKSI</th></tr></thead>
                <tbody>
                <?php
                $no = 1;
                $q = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori FROM barang b LEFT JOIN kategori k ON b.kategori_id=k.id $where ORDER BY b.id DESC");
                if (mysqli_num_rows($q) == 0): ?>
                    <tr><td colspan="7" class="text-center text-muted py-5">Belum ada data barang</td></tr>
                <?php else: while ($r = mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><code><?= $r['kode_barang'] ?></code></td>
                    <td><strong><?= $r['nama_barang'] ?></strong></td>
                    <td><?= $r['nama_kategori'] ?? '-' ?></td>
                    <td><strong><?= $r['stok'] ?></strong> <?= $r['satuan'] ?></td>
                    <td><?= $r['lokasi_rak'] ?></td>
                    <td>
                        <a href="config/proses.php?action=hapus_barang&id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin hapus?')"><i class="bi bi-trash-fill"></i></a>
                    </td>
                </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php // ═══════════════════════════════════════
// BARANG MASUK
// ═══════════════════════════════════════
elseif ($page == 'masuk'):
    $search = mysqli_real_escape_string($koneksi, $_GET['search'] ?? '');
    $where = !empty($search) ? "WHERE b.nama_barang LIKE '%$search%' OR b.kode_barang LIKE '%$search%'" : '';
?>
    <div class="page-header">
        <div><h4><i class="bi bi-arrow-down-circle-fill text-success"></i> Barang Masuk</h4></div>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalMasuk"><i class="bi bi-plus-circle"></i> Tambah Barang Masuk</button>
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
                <thead><tr><th>NO</th><th>TANGGAL</th><th>BARANG</th><th>JUMLAH MASUK</th><th>AKSI</th></tr></thead>
                <tbody>
                <?php $no=1;
                $q = mysqli_query($koneksi, "SELECT bm.*, b.nama_barang, b.satuan FROM barang_masuk bm JOIN barang b ON bm.barang_id=b.id $where ORDER BY bm.id DESC");
                if (mysqli_num_rows($q) == 0): ?>
                    <tr><td colspan="5" class="text-center text-muted py-5">Belum ada transaksi</td></tr>
                <?php else: while($r=mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= tglIndo($r['tanggal']) ?></td>
                    <td><strong><?= $r['nama_barang'] ?></strong></td>
                    <td><span class="badge bg-success">+<?= $r['jumlah'] ?> <?= $r['satuan'] ?></span></td>
                    <td><a href="config/proses.php?action=hapus_masuk&id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash-fill"></i></a></td>
                </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- MODAL MASUK (Super Simpel) -->
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
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Opsi <span class="text-danger">*</span></label>
                            <select name="id_barang" id="selectBarang" class="form-select" required onchange="toggleForm(this.value)">
                                <option value="baru" style="color: #0d6efd; font-weight: 600;">
                                    + Daftarkan Barang Baru
                                </option>
                                <optgroup label="Barang yang Sudah Ada (Tambah Stok)">
                                    <?php $b=mysqli_query($koneksi,"SELECT * FROM barang ORDER BY nama_barang"); while($bb=mysqli_fetch_assoc($b)): ?>
                                    <option value="<?= $bb['id'] ?>"><?= $bb['kode_barang'] ?> - <?= $bb['nama_barang'] ?> (Sisa: <?= $bb['stok'] ?>)</option>
                                    <?php endwhile; ?>
                                </optgroup>
                            </select>
                        </div>

                        <!-- FORM BARANG BARU -->
                        <div id="formBarangBaru" style="display: none; background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 16px;">
                            <h6 class="text-dark mb-3 fw-bold" style="font-size: 14px;"><i class="bi bi-file-earmark-plus"></i> Formulir Barang Baru</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
                                    <input type="text" name="kode_baru" id="inputKode" class="form-control" placeholder="Contoh: JRG-007">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Satuan</label>
                                    <input type="text" name="satuan_baru" class="form-control" value="pcs">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" name="nama_baru" id="inputNama" class="form-control" placeholder="Contoh: Kabel LAN 5m">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori_baru" class="form-select">
                                    <?php $k=mysqli_query($koneksi,"SELECT * FROM kategori"); while($kk=mysqli_fetch_assoc($k)): ?>
                                    <option value="<?= $kk['id'] ?>"><?= $kk['nama_kategori'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <!-- FORM JUMLAH STOK (Selalu Muncul) -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Jumlah Stok Masuk <span class="text-danger">*</span></label>
                                <input type="number" name="jumlah" id="inputJumlah" class="form-control" min="1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SCRIPT MUNCULIN FORM -->
    <script>
    function toggleForm(val) {
        var formBaru = document.getElementById('formBarangBaru');
        var inputKode = document.getElementById('inputKode');
        var inputNama = document.getElementById('inputNama');

        if (val === 'baru') {
            formBaru.style.display = 'block';
            inputKode.required = true;
            inputNama.required = true;
        } else {
            formBaru.style.display = 'none';
            inputKode.required = false;
            inputNama.required = false;
        }
    }
    
    // Set default saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        toggleForm('baru'); // Memunculkan form barang baru secara default
    });
    </script>

<?php // ═══════════════════════════════════════
// BARANG KELUAR
// ═══════════════════════════════════════
elseif ($page == 'keluar'):
    $search = mysqli_real_escape_string($koneksi, $_GET['search'] ?? '');
    $where = !empty($search) ? "WHERE b.nama_barang LIKE '%$search%' OR b.kode_barang LIKE '%$search%'" : '';
?>
    <div class="page-header">
        <div><h4><i class="bi bi-arrow-up-circle-fill text-danger"></i> Barang Keluar</h4></div>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalKeluar"><i class="bi bi-plus-circle"></i> Tambah Barang Keluar</button>
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
                <thead><tr><th>NO</th><th>TANGGAL</th><th>BARANG</th><th>PEMINJAM</th><th>JUMLAH</th><th>AKSI</th></tr></thead>
                <tbody>
                <?php $no=1;
                $q = mysqli_query($koneksi, "SELECT bk.*, b.nama_barang, b.satuan FROM barang_keluar bk JOIN barang b ON bk.barang_id=b.id $where ORDER BY bk.id DESC");
                if (mysqli_num_rows($q) == 0): ?>
                    <tr><td colspan="6" class="text-center text-muted py-5">Belum ada transaksi</td></tr>
                <?php else: while($r=mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= tglIndo($r['tanggal']) ?></td>
                    <td><strong><?= $r['nama_barang'] ?></strong></td>
                    <td><strong><?= $r['nama_peminjam'] ?></strong><br><small><?= $r['no_identitas'] ?></small></td>
                    <td><span class="badge bg-danger">-<?= $r['jumlah'] ?> <?= $r['satuan'] ?></span></td>
                    <td><a href="config/proses.php?action=hapus_keluar&id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash-fill"></i></a></td>
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
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Nama Peminjam <span class="text-danger">*</span></label><input type="text" name="nama_peminjam" class="form-control" placeholder="Budi" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">No Identitas <span class="text-danger">*</span></label><input type="text" name="no_identitas" class="form-control" placeholder="210123" required></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php // ═══════════════════════════════════════
// LAPORAN & LAINNYA
// ═══════════════════════════════════════
elseif ($page == 'kartu_stok'):
    require 'kartu_stok.php'; exit;
elseif ($page == 'riwayat'):
    require 'riwayat_transaksi.php'; exit;
elseif ($page == 'laporan_kat'):
    require 'laporan_kategori.php'; exit;
elseif ($page == 'laporan'):
?>
    <div class="page-header">
        <div><h4><i class="bi bi-file-earmark-bar-graph-fill text-primary"></i> Laporan</h4></div>
        <div>
            <a href="config/export_excel.php?jenis=stok" class="btn btn-success"><i class="bi bi-file-earmark-excel-fill"></i> Export Excel</a>
            <button type="button" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#modalCetak"><i class="bi bi-printer-fill"></i> Cetak</button>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>