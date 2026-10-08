<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/koneksi.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ═══════════════════════════════════════════
// BARANG
// ═══════════════════════════════════════════
if ($action == 'simpan_barang') {
    $id       = $_POST['id'] ?? '';
    $kode     = mysqli_real_escape_string($koneksi, $_POST['kode_barang'] ?? '');
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama_barang'] ?? '');
    $kat      = (int)($_POST['kategori_id'] ?? 0);
    $satuan   = mysqli_real_escape_string($koneksi, $_POST['satuan'] ?? 'pcs');
    $stok     = (int)($_POST['stok'] ?? 0);
    $stok_min = (int)($_POST['stok_minimal'] ?? 5);
    $rak      = mysqli_real_escape_string($koneksi, $_POST['lokasi_rak'] ?? '');
    $harga    = (float)($_POST['harga'] ?? 0);
    
    if (!empty($id) && $id > 0) {
        // UPDATE
        $sql = "UPDATE barang SET 
            kode_barang='$kode', 
            nama_barang='$nama', 
            kategori_id='$kat', 
            satuan='$satuan', 
            stok='$stok', 
            stok_minimal='$stok_min', 
            lokasi_rak='$rak', 
            harga='$harga' 
            WHERE id='$id'";
        
        if (mysqli_query($koneksi, $sql)) {
            header("Location: ../index.php?page=barang&msg=edit&highlight=$id");
        } else {
            header("Location: ../index.php?page=barang&error=" . urlencode(mysqli_error($koneksi)));
        }
    } else {
        // INSERT
        $sql = "INSERT INTO barang 
            (kode_barang, nama_barang, kategori_id, satuan, stok, stok_minimal, lokasi_rak, harga) 
            VALUES ('$kode','$nama','$kat','$satuan','$stok','$stok_min','$rak','$harga')";
        
        if (mysqli_query($koneksi, $sql)) {
            $new_id = mysqli_insert_id($koneksi);
            header("Location: ../index.php?page=barang&msg=sukses&highlight=$new_id");
        } else {
            header("Location: ../index.php?page=barang&error=" . urlencode(mysqli_error($koneksi)));
        }
    }
    exit;
}

if ($action == 'hapus_barang') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) mysqli_query($koneksi, "DELETE FROM barang WHERE id='$id'");
    header("Location: ../index.php?page=barang&msg=hapus");
    exit;
}

// ═══════════════════════════════════════════
// KATEGORI
// ═══════════════════════════════════════════
if ($action == 'simpan_kategori') {
    $id   = $_POST['id'] ?? '';
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_kategori'] ?? '');
    $zona = mysqli_real_escape_string($koneksi, $_POST['kode_zona'] ?? '');
    $ket  = mysqli_real_escape_string($koneksi, $_POST['keterangan'] ?? '');
    
    if (!empty($id) && $id > 0) {
        $sql = "UPDATE kategori SET nama_kategori='$nama', kode_zona='$zona', keterangan='$ket' WHERE id='$id'";
        if (mysqli_query($koneksi, $sql)) {
            header("Location: ../index.php?page=kategori&msg=edit&highlight=$id");
        } else {
            header("Location: ../index.php?page=kategori&error=" . urlencode(mysqli_error($koneksi)));
        }
    } else {
        $sql = "INSERT INTO kategori (nama_kategori, kode_zona, keterangan) VALUES ('$nama','$zona','$ket')";
        if (mysqli_query($koneksi, $sql)) {
            $new_id = mysqli_insert_id($koneksi);
            header("Location: ../index.php?page=kategori&msg=sukses&highlight=$new_id");
        } else {
            header("Location: ../index.php?page=kategori&error=" . urlencode(mysqli_error($koneksi)));
        }
    }
    exit;
}

if ($action == 'hapus_kategori') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) mysqli_query($koneksi, "DELETE FROM kategori WHERE id='$id'");
    header("Location: ../index.php?page=kategori&msg=hapus");
    exit;
}

// ═══════════════════════════════════════════
// SUPPLIER
// ═══════════════════════════════════════════
if ($action == 'simpan_supplier') {
    $id     = $_POST['id'] ?? '';
    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama_supplier'] ?? '');
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat'] ?? '');
    $telp   = mysqli_real_escape_string($koneksi, $_POST['telepon'] ?? '');
    
    if (!empty($id) && $id > 0) {
        $sql = "UPDATE supplier SET nama_supplier='$nama', alamat='$alamat', telepon='$telp' WHERE id='$id'";
        if (mysqli_query($koneksi, $sql)) {
            header("Location: ../index.php?page=supplier&msg=edit&highlight=$id");
        } else {
            header("Location: ../index.php?page=supplier&error=" . urlencode(mysqli_error($koneksi)));
        }
    } else {
        $sql = "INSERT INTO supplier (nama_supplier, alamat, telepon) VALUES ('$nama','$alamat','$telp')";
        if (mysqli_query($koneksi, $sql)) {
            $new_id = mysqli_insert_id($koneksi);
            header("Location: ../index.php?page=supplier&msg=sukses&highlight=$new_id");
        } else {
            header("Location: ../index.php?page=supplier&error=" . urlencode(mysqli_error($koneksi)));
        }
    }
    exit;
}

if ($action == 'hapus_supplier') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) mysqli_query($koneksi, "DELETE FROM supplier WHERE id='$id'");
    header("Location: ../index.php?page=supplier&msg=hapus");
    exit;
}

// ═══════════════════════════════════════════
// BARANG MASUK
// ═══════════════════════════════════════════
if ($action == 'barang_masuk') {
    $id_barang = (int)($_POST['id_barang'] ?? 0);
    $supplier  = (int)($_POST['supplier_id'] ?? 0);
    $jumlah    = (int)($_POST['jumlah'] ?? 0);
    $tgl       = $_POST['tanggal'] ?? date('Y-m-d');
    $no_po     = mysqli_real_escape_string($koneksi, $_POST['no_po'] ?? '');
    $ket       = mysqli_real_escape_string($koneksi, $_POST['keterangan'] ?? '');
    
    if ($jumlah <= 0 || $id_barang <= 0) {
        header("Location: ../index.php?page=masuk&error=jumlah");
        exit;
    }
    
    $supplier_val = $supplier > 0 ? "'$supplier'" : "NULL";
    
    $q1 = mysqli_query($koneksi, "INSERT INTO barang_masuk 
        (barang_id, supplier_id, jumlah, tanggal, no_po, keterangan) 
        VALUES ('$id_barang', $supplier_val, '$jumlah', '$tgl', '$no_po', '$ket')");
    
    $q2 = mysqli_query($koneksi, "UPDATE barang SET stok = stok + $jumlah WHERE id='$id_barang'");
    
    if ($q1 && $q2) {
        header("Location: ../index.php?page=masuk&msg=sukses");
    } else {
        header("Location: ../index.php?page=masuk&error=" . urlencode(mysqli_error($koneksi)));
    }
    exit;
}

if ($action == 'hapus_masuk') {
    $id = (int)($_GET['id'] ?? 0);
    $r = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM barang_masuk WHERE id='$id'"));
    if ($r) {
        mysqli_query($koneksi, "UPDATE barang SET stok = stok - {$r['jumlah']} WHERE id='{$r['barang_id']}'");
        mysqli_query($koneksi, "DELETE FROM barang_masuk WHERE id='$id'");
    }
    header("Location: ../index.php?page=masuk&msg=hapus");
    exit;
}

// ═══════════════════════════════════════════
// BARANG KELUAR
// ═══════════════════════════════════════════
if ($action == 'barang_keluar') {
    $id_barang = (int)($_POST['id_barang'] ?? 0);
    $jumlah    = (int)($_POST['jumlah'] ?? 0);
    $tgl       = $_POST['tanggal'] ?? date('Y-m-d');
    $tujuan    = mysqli_real_escape_string($koneksi, $_POST['tujuan'] ?? '');
    $no_sj     = mysqli_real_escape_string($koneksi, $_POST['no_surat_jalan'] ?? '');
    $ket       = mysqli_real_escape_string($koneksi, $_POST['keterangan'] ?? '');
    
    if ($jumlah <= 0 || $id_barang <= 0) {
        header("Location: ../index.php?page=keluar&error=jumlah");
        exit;
    }
    
    $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT stok FROM barang WHERE id='$id_barang'"));
    
    if (!$cek || $cek['stok'] < $jumlah) {
        header("Location: ../index.php?page=keluar&error=stok");
        exit;
    }
    
    $q1 = mysqli_query($koneksi, "INSERT INTO barang_keluar 
        (barang_id, jumlah, tanggal, tujuan, no_surat_jalan, keterangan) 
        VALUES ('$id_barang','$jumlah','$tgl','$tujuan','$no_sj','$ket')");
    
    $q2 = mysqli_query($koneksi, "UPDATE barang SET stok = stok - $jumlah WHERE id='$id_barang'");
    
    if ($q1 && $q2) {
        header("Location: ../index.php?page=keluar&msg=sukses");
    } else {
        header("Location: ../index.php?page=keluar&error=" . urlencode(mysqli_error($koneksi)));
    }
    exit;
}

if ($action == 'hapus_keluar') {
    $id = (int)($_GET['id'] ?? 0);
    $r = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM barang_keluar WHERE id='$id'"));
    if ($r) {
        mysqli_query($koneksi, "UPDATE barang SET stok = stok + {$r['jumlah']} WHERE id='{$r['barang_id']}'");
        mysqli_query($koneksi, "DELETE FROM barang_keluar WHERE id='$id'");
    }
    header("Location: ../index.php?page=keluar&msg=hapus");
    exit;
}

header("Location: ../index.php");
exit;
?>