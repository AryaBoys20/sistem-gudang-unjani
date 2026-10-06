CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100),
    role ENUM('admin','staff') DEFAULT 'staff'
);

INSERT INTO users VALUES
(1, 'admin', 'admin123', 'Administrator', 'admin');

CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    kode_zona VARCHAR(5),
    keterangan TEXT
);

INSERT INTO kategori VALUES
(1, 'Elektronik', 'A', 'Barang elektronik'),
(2, 'Komponen Logistik', 'B', 'Komponen gudang'),
(3, 'Alat Tulis & Kantor', 'C', 'ATK');

CREATE TABLE supplier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20)
);

INSERT INTO supplier VALUES
(1, 'PT Maju Jaya', 'Jl. Sudirman No.1', '021-1234567'),
(2, 'CV Sumber Rejeki', 'Jl. Ahmad Yani No.5', '022-7654321');

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

INSERT INTO barang VALUES
(1, 'LBL-002', 'Kertas Label Thermal A6', 3, 'Roll', 4, 5, 'Rak C-04', 25000),
(2, 'ELK-001', 'Mouse Logitech M100', 1, 'pcs', 15, 5, 'Rak A-01', 75000),
(3, 'ELK-002', 'Keyboard USB Standard', 1, 'pcs', 8, 5, 'Rak A-02', 120000),
(4, 'KMP-001', 'Kabel HDMI 2m', 2, 'pcs', 20, 5, 'Rak B-01', 35000),
(5, 'ATK-001', 'Pulpen Standard', 3, 'box', 30, 5, 'Rak C-01', 15000);

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