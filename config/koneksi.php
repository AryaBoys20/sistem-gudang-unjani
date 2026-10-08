<?php
// ═══ KONEKSI DATABASE ═══
$host = "localhost";
$user = "root";
$pass = "";
$db   = "database_unjani";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8mb4");

// ═══ HELPER FUNCTIONS ═══
function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

function badgeStok($stok, $minimal) {
    if ($stok <= 0) return '<span class="badge bg-dark">Habis</span>';
    if ($stok <= $minimal) return '<span class="badge bg-danger">Kritis</span>';
    return '<span class="badge bg-success">Aman</span>';
}

function tglIndo($tgl) {
    if (empty($tgl)) return '-';
    return date('d/m/Y', strtotime($tgl));
}

function tglIndoPanjang($tgl) {
    if (empty($tgl)) return '-';
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $d = date('d', strtotime($tgl));
    $m = (int)date('m', strtotime($tgl));
    $y = date('Y', strtotime($tgl));
    return $d . ' ' . $bulan[$m] . ' ' . $y;
}
?>