-- ═══════════════════════════════════════════
-- DATABASE WMS LAB FSI (Fakultas Sains & Informatika)
-- Siap Import (Data Lengkap + Riwayat)
-- ═══════════════════════════════════════════

-- 1. Matikan Foreign Key sementara agar tabel bisa dihapus
SET FOREIGN_KEY_CHECKS = 0;

-- 2. Hapus tabel lama (Jika ada)
DROP TABLE IF EXISTS barang_keluar;
DROP TABLE IF EXISTS barang_masuk;
DROP TABLE IF EXISTS barang;
DROP TABLE IF EXISTS supplier;
DROP TABLE IF EXISTS kategori;

-- 3. Nyalakan kembali Foreign Key
SET FOREIGN_KEY_CHECKS = 1;

-- ═══════════════════════════════════════════
-- BUAT TABEL KATEGORI & SUPPLIER
-- ═══════════════════════════════════════════
CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    kode_zona VARCHAR(5),
    keterangan TEXT
);

INSERT INTO kategori (id, nama_kategori, kode_zona, keterangan) VALUES
(1, 'Perangkat Komputer', 'A', 'PC, Monitor, Proyektor, dan Aksesoris'),
(2, 'Perangkat Jaringan', 'B', 'Router, Switch, Kabel UTP, Alat Jaringan'),
(3, 'Alat Tulis & Administrasi', 'C', 'Kebutuhan ATK Asisten dan Dosen');

CREATE TABLE supplier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20)
);

INSERT INTO supplier (id, nama_supplier, alamat, telepon) VALUES
(1, 'PT Maju Jaya Komputer', 'Jl. Sudirman No.1', '021-1234567'),
(2, 'CV Sumber Jaringan', 'Jl. Ahmad Yani No.5', '022-7654321');

-- ═══════════════════════════════════════════
-- BUAT TABEL BARANG (14 Barang Lab FSI)
-- ═══════════════════════════════════════════
CREATE TABLE barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_barang VARCHAR(20) UNIQUE NOT NULL,
    nama_barang VARCHAR(100) NOT NULL,
    kategori_id INT,
    satuan VARCHAR(20) DEFAULT 'pcs',
    stok INT DEFAULT 0,
    stok_minimal INT DEFAULT 5,
    lokasi_rak VARCHAR(20),
    harga DECIMAL(15,2) DEFAULT 0,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL
);

INSERT INTO barang (id, kode_barang, nama_barang, kategori_id, satuan, stok, stok_minimal, lokasi_rak, harga) VALUES
-- Rak A: Komputer
(1, 'ELK-001', 'Komputer PC Desktop', 1, 'unit', 20, 5, 'Rak A-01', 5500000),
(2, 'ELK-002', 'Monitor LED 21.5 inch', 1, 'unit', 25, 5, 'Rak A-02', 1500000),
(3, 'ELK-003', 'Proyektor Epson', 1, 'unit', 5, 2, 'Rak A-03', 4500000),
(4, 'ELK-004', 'Keyboard USB Standard', 1, 'pcs', 30, 10, 'Rak A-04', 120000),
(5, 'ELK-005', 'Mouse Optik Logitech', 1, 'pcs', 30, 10, 'Rak A-05', 75000),

-- Rak B: Jaringan
(6, 'JRG-001', 'Router Mikrotik RB951', 2, 'unit', 10, 3, 'Rak B-01', 850000),
(7, 'JRG-002', 'Switch Hub 16 Port', 2, 'unit', 5, 2, 'Rak B-02', 600000),
(8, 'JRG-003', 'Kabel UTP Cat6 (Roll)', 2, 'roll', 4, 1, 'Rak B-03', 1200000),
(9, 'JRG-004', 'Konektor RJ45 (Box)', 2, 'box', 6, 2, 'Rak B-04', 150000),
(10, 'JRG-005', 'Crimping Tool RJ45', 2, 'pcs', 10, 3, 'Rak B-05', 85000),
(11, 'JRG-006', 'Modul Praktikum Algoritma', 2, 'buku', 40, 10, 'Rak B-06', 35000),

-- Rak C: ATK
(12, 'ATK-001', 'Kertas A4 80gr', 3, 'rim', 50, 10, 'Rak C-01', 55000),
(13, 'ATK-002', 'Pulpen Standard', 3, 'box', 30, 5, 'Rak C-02', 15000),
(14, 'ATK-003', 'Spidol Whiteboard', 3, 'pcs', 25, 5, 'Rak C-03', 12000);

-- ═══════════════════════════════════════════
-- BUAT TABEL BARANG MASUK & KELUAR
-- ═══════════════════════════════════════════
CREATE TABLE barang_masuk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barang_id INT NOT NULL,
    supplier_id INT,
    jumlah INT NOT NULL,
    tanggal DATE NOT NULL,
    no_po VARCHAR(50),
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES supplier(id) ON DELETE SET NULL
);

CREATE TABLE barang_keluar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barang_id INT NOT NULL,
    jumlah INT NOT NULL,
    tanggal DATE NOT NULL,
    tujuan VARCHAR(100),
    no_surat_jalan VARCHAR(50),
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE
);

-- ═══════════════════════════════════════════
-- ISI RIWAYAT BARANG MASUK
-- ═══════════════════════════════════════════
INSERT INTO barang_masuk (barang_id, supplier_id, jumlah, tanggal, no_po, keterangan) VALUES
(1, 1, 20, '2026-10-01', 'PO-2026-001', 'Pengadaan PC Lab Baru'),
(2, 1, 25, '2026-10-01', 'PO-2026-001', 'Pengadaan Monitor Lab'),
(3, 1, 5,  '2026-10-02', 'PO-2026-002', 'Pembelian Proyektor'),
(6, 2, 10, '2026-10-03', 'PO-2026-003', 'Pembelian Router Mikrotik'),
(7, 2, 5,  '2026-10-03', 'PO-2026-003', 'Pembelian Switch Hub'),
(8, 2, 4,  '2026-10-04', 'PO-2026-004', 'Kabel UTP untuk Lab'),
(11, 2, 40, '2026-10-05', 'PO-2026-005', 'Cetak Modul Praktikum'),
(12, 1, 50, '2026-10-05', 'PO-2026-006', 'Kertas A4');

-- ═══════════════════════════════════════════
-- ISI RIWAYAT BARANG KELUAR (Peminjaman)
-- ═══════════════════════════════════════════
INSERT INTO barang_keluar (barang_id, jumlah, tanggal, tujuan, no_surat_jalan, keterangan) VALUES
(3, 1, '2026-10-10', 'Budi (NIM 210123)', 'SJ-2026-001', 'Pinjam Proyektor Seminar'),
(6, 2, '2026-10-12', 'Asisten Lab (Andi)', 'SJ-2026-003', 'Pinjam Router Praktikum'),
(8, 1, '2026-10-12', 'Asisten Lab (Andi)', 'SJ-2026-003', 'Kabel UTP Praktikum'),
(11, 10, '2026-10-13', 'Mahasiswa Kelas A', 'SJ-2026-004', 'Distribusi Modul'),
(12, 5, '2026-10-14', 'Bagian Administrasi', 'SJ-2026-005', 'Print Laporan');

-- ═══════════════════════════════════════════
-- SINKRONISASI STOK (Update otomatis stok berkurang)
-- ═══════════════════════════════════════════
UPDATE barang b
LEFT JOIN (
    SELECT barang_id, SUM(jumlah) as total_keluar 
    FROM barang_keluar 
    GROUP BY barang_id
) bk ON b.id = bk.barang_id
SET b.stok = b.stok - IFNULL(bk.total_keluar, 0);