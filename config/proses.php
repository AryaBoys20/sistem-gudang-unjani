<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/koneksi.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ═══ BARANG MASUK (Daftar Baru & Tambah Stok) ═══
if ($action == 'barang_masuk') {
    $id_barang = $_POST['id_barang'] ?? '';
    $jumlah    = (int)($_POST['jumlah'] ?? 0);
    $tgl       = $_POST['tanggal'] ?? date('Y-m-d');

    // Jika mendaftarkan barang baru
    if ($id_barang == 'baru') {
        $kode_baru = mysqli_real_escape_string($koneksi, trim($_POST['kode_baru'] ?? ''));
        $nama_baru = mysqli_real_escape_string($koneksi, trim($_POST['nama_baru'] ?? ''));
        $kat_baru  = (int)($_POST['kategori_baru'] ?? 0);
        $sat_baru  = mysqli_real_escape_string($koneksi, trim($_POST['satuan_baru'] ?? 'pcs'));
        
        // Simpan ke Data Barang (Stok awal 0)
        $sql = "INSERT INTO barang (kode_barang, nama_barang, kategori_id, satuan, stok) VALUES ('$kode_baru', '$nama_baru', '$kat_baru', '$sat_baru', 0)";
        mysqli_query($koneksi, $sql);
        
        // Ambil ID barang baru
        $id_barang = mysqli_insert_id($koneksi);
    }

    // Simpan transaksi masuk & Tambah stok
    if ($jumlah > 0 && $id_barang > 0) {
        mysqli_query($koneksi, "INSERT INTO barang_masuk (barang_id, jumlah, tanggal) VALUES ('$id_barang', '$jumlah', '$tgl')");
        mysqli_query($koneksi, "UPDATE barang SET stok = stok + $jumlah WHERE id='$id_barang'");
        header("Location: ../index.php?page=masuk&msg=sukses");
    } else {
        header("Location: ../index.php?page=masuk&error=jumlah");
    }
    exit;
}

// ═══ BARANG KELUAR ═══
if ($action == 'barang_keluar') {
    $id_barang = (int)($_POST['id_barang'] ?? 0);
    $jumlah    = (int)($_POST['jumlah'] ?? 0);
    $tgl       = $_POST['tanggal'] ?? date('Y-m-d');
    $nama_pem  = mysqli_real_escape_string($koneksi, trim($_POST['nama_peminjam'] ?? ''));
    $no_id     = mysqli_real_escape_string($koneksi, trim($_POST['no_identitas'] ?? ''));
    
    $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT stok FROM barang WHERE id='$id_barang'"));
    
    if ($cek && $cek['stok'] >= $jumlah && $jumlah > 0) {
        mysqli_query($koneksi, "INSERT INTO barang_keluar (barang_id, jumlah, tanggal, nama_peminjam, no_identitas) VALUES ('$id_barang','$jumlah','$tgl','$nama_pem','$no_id')");
        mysqli_query($koneksi, "UPDATE barang SET stok = stok - $jumlah WHERE id='$id_barang'");
        header("Location: ../index.php?page=keluar&msg=sukses"); exit;
    } else {
        header("Location: ../index.php?page=keluar&error=stok"); exit;
    }
}

// ═══ HAPUS DATA ═══
if ($action == 'hapus_barang') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) mysqli_query($koneksi, "DELETE FROM barang WHERE id='$id'");
    header("Location: ../index.php?page=barang&msg=hapus"); exit;
}

if ($action == 'hapus_masuk') {
    $id = (int)($_GET['id'] ?? 0);
    $r = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM barang_masuk WHERE id='$id'"));
    if ($r) {
        mysqli_query($koneksi, "UPDATE barang SET stok = stok - {$r['jumlah']} WHERE id='{$r['barang_id']}'");
        mysqli_query($koneksi, "DELETE FROM barang_masuk WHERE id='$id'");
    }
    header("Location: ../index.php?page=masuk&msg=hapus"); exit;
}

if ($action == 'hapus_keluar') {
    $id = (int)($_GET['id'] ?? 0);
    $r = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM barang_keluar WHERE id='$id'"));
    if ($r) {
        mysqli_query($koneksi, "UPDATE barang SET stok = stok + {$r['jumlah']} WHERE id='{$r['barang_id']}'");
        mysqli_query($koneksi, "DELETE FROM barang_keluar WHERE id='$id'");
    }
    header("Location: ../index.php?page=keluar&msg=hapus"); exit;
}

header("Location: ../index.php"); exit;
?>