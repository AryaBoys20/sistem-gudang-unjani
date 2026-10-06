<?php
require_once __DIR__ . '/../config/koneksi.php';
$current = $_GET['page'] ?? 'beranda';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WMS UNJANI - Sistem Informasi Manajemen Pergudangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center gap-3" href="index.php">
            <div style="width:44px;height:44px;background:linear-gradient(135deg,#2563eb,#1e40af);border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(37,99,235,0.4);">
                <i class="bi bi-box-seam-fill" style="font-size:22px;color:#fff;"></i>
            </div>
            <div>
                <div class="fw-bold lh-1" style="font-size:17px;">
                    WMS UNJANI 
                    <span class="badge ms-2" style="background:#2563eb;font-size:9.5px;padding:4px 8px;vertical-align:middle;letter-spacing:0.8px;">LAB LOGISTIK</span>
                </div>
                <small style="font-size:11px;opacity:0.7;">Sistem Informasi Manajemen Pergudangan</small>
            </div>
        </a>
        
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $current=='beranda'?'active':'' ?>" href="index.php">
                        <i class="bi bi-house-door-fill"></i> Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= in_array($current,['barang','kategori','supplier'])?'active':'' ?>" href="index.php?page=barang">
                        <i class="bi bi-box-seam"></i> Data Barang
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current=='masuk'?'active':'' ?>" href="index.php?page=masuk">
                        <i class="bi bi-arrow-down-circle-fill"></i> Barang Masuk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current=='keluar'?'active':'' ?>" href="index.php?page=keluar">
                        <i class="bi bi-arrow-up-circle-fill"></i> Barang Keluar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current=='laporan'?'active':'' ?>" href="index.php?page=laporan">
                        <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan
                    </a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center gap-2">
                <span class="badge" style="background:rgba(16,185,129,0.15);color:#34d399;border:1px solid rgba(16,185,129,0.3);padding:7px 14px;font-size:12px;font-weight:600;">
                    <i class="bi bi-circle-fill me-1" style="font-size:7px;vertical-align:middle;"></i> Gudang Utama
                </span>
            </div>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container-fluid main-content">