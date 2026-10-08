-- ═══ DATABASE WMS UNJANI ═══
-- Sistem Informasi Manajemen Pergudangan
-- Laboratorium Logistik UNJANI
-- Tanpa Login / Tanpa User

-- ═══════════════════════════════════════════
-- KATEGORI
-- ═══════════════════════════════════════════
CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    kode_zona VARCHAR(5),
    keterangan TEXT
);

INSERT INTO kategori (nama_kategori, kode_zona, keterangan) VALUES
('Elektronik', 'A', 'Barang elektronik'),
('Komponen Logistik', 'B', 'Komponen gudang'),
('Alat Tulis & Kantor', 'C', 'ATK');

-- ═══════════════════════════════════════════
-- SUPPLIER
-- ═══════════════════════════════════════════
CREATE TABLE supplier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20)
);

INSERT INTO supplier (nama_supplier, alamat, telepon) VALUES
('PT Maju Jaya', 'Jl. Sudirman No.1', '021-1234567'),
('CV Sumber Rejeki', 'Jl. Ahmad Yani No.5', '022-7654321');

-- ═══════════════════════════════════════════
-- BARANG
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

INSERT INTO barang (kode_barang, nama_barang, kategori_id, satuan, stok, stok_minimal, lokasi_rak, harga) VALUES
('ELK-001', 'Komputer PC Desktop', 1, 'unit', 20, 5, 'Rak A-01', 5500000),
('ELK-002', 'Laptop Asus VivoBook', 1, 'unit', 10, 3, 'Rak A-02', 7500000),
('ELK-003', 'Monitor LED 21.5 inch', 1, 'unit', 25, 5, 'Rak A-03', 1500000),
('ELK-004', 'Proyektor Epson', 1, 'unit', 5, 2, 'Rak A-04', 4500000),
('ELK-005', 'Keyboard USB Standard', 1, 'pcs', 30, 10, 'Rak A-05', 120000),
('ELK-006', 'Mouse Optik Logitech', 1, 'pcs', 40, 10, 'Rak A-06', 75000),
('ELK-007', 'Printer Canon MP287', 1, 'unit', 8, 2, 'Rak A-07', 1850000),
('ELK-008', 'Scanner Epson L3110', 1, 'unit', 4, 1, 'Rak A-08', 2500000),
('ELK-009', 'Speaker Aktif', 1, 'unit', 12, 3, 'Rak A-09', 250000),
('ELK-010', 'Headset Gaming', 1, 'pcs', 15, 5, 'Rak A-10', 350000),
('ELK-011', 'Kabel HDMI 2m', 1, 'pcs', 30, 10, 'Rak A-11', 35000),
('ELK-012', 'Flashdisk 32GB', 1, 'pcs', 20, 5, 'Rak A-12', 85000),
('KMP-001', 'RAM DDR4 8GB', 2, 'pcs', 15, 3, 'Rak B-01', 480000),
('KMP-002', 'SSD 256GB SATA', 2, 'pcs', 10, 3, 'Rak B-02', 550000),
('KMP-003', 'Power Supply 500W', 2, 'unit', 8, 2, 'Rak B-03', 450000),
('KMP-004', 'Thermal Paste', 2, 'tube', 20, 5, 'Rak B-04', 35000),
('KMP-005', 'Kipas CPU Cooler', 2, 'pcs', 12, 3, 'Rak B-05', 150000),
('KMP-006', 'Motherboard B460M', 2, 'unit', 6, 2, 'Rak B-06', 1250000),
('KMP-007', 'Kabel SATA Data', 2, 'pcs', 30, 10, 'Rak B-07', 15000),
('KMP-008', 'Kabel Power CPU', 2, 'pcs', 25, 10, 'Rak B-08', 25000),
('ATK-001', 'Kertas A4 80gr', 3, 'rim', 50, 10, 'Rak C-01', 55000),
('ATK-002', 'Kertas F4 70gr', 3, 'rim', 40, 10, 'Rak C-02', 50000),
('ATK-003', 'Pulpen Standard', 3, 'box', 30, 5, 'Rak C-03', 15000),
('ATK-004', 'Spidol Whiteboard', 3, 'pcs', 25, 5, 'Rak C-04', 12000),
('ATK-005', 'Penghapus Whiteboard', 3, 'pcs', 15, 5, 'Rak C-05', 8000),
('ATK-006', 'Penggaris 30cm', 3, 'pcs', 20, 5, 'Rak C-06', 5000),
('ATK-007', 'Gunting Kertas', 3, 'pcs', 10, 3, 'Rak C-07', 15000),
('ATK-008', 'Lem Kertas Stick', 3, 'pcs', 25, 5, 'Rak C-08', 7000),
('ATK-009', 'Stapler Medium', 3, 'unit', 8, 2, 'Rak C-09', 35000),
('ATK-010', 'Isi Staples No.10', 3, 'box', 30, 10, 'Rak C-10', 5000);

-- ═══════════════════════════════════════════
-- BARANG MASUK
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

-- ═══════════════════════════════════════════
-- BARANG KELUAR
-- ═══════════════════════════════════════════
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