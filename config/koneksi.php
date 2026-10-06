<?php
// DEBUG MODE
error_reporting(E_ALL);
ini_set('display_errors', 1);

// KONEKSI DATABASE
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_gudang";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8mb4");

// HELPER FUNCTIONS
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
?>