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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center gap-3" href="index.php">
            <div class="brand-logo">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <div>
                <div class="fw-bold lh-1" style="font-size:17px;letter-spacing:-0.3px;">
                    WMS UNJANI 
                    <span class="badge ms-2" style="background:linear-gradient(135deg,#3b82f6,#2563eb);font-size:9px;padding:4px 8px;vertical-align:middle;letter-spacing:0.8px;">LAB LOGISTIK</span>
                </div>
                <small style="font-size:11px;opacity:0.65;">Sistem Informasi Manajemen Pergudangan</small>
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
                        <i class="bi bi-arrow-down-circle-fill"></i> Masuk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current=='keluar'?'active':'' ?>" href="index.php?page=keluar">
                        <i class="bi bi-arrow-up-circle-fill"></i> Keluar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current=='laporan'?'active':'' ?>" href="index.php?page=laporan">
                        <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= in_array($current,['kartu_stok','riwayat','laporan_kat'])?'active':'' ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-grid-3x3-gap-fill"></i> Lainnya
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end modern-dropdown">
                        <li><h6 class="dropdown-header">📊 ANALISIS</h6></li>
                        <li><a class="dropdown-item" href="index.php?page=kartu_stok"><i class="bi bi-file-earmark-text text-primary"></i> Kartu Stok</a></li>
                        <li><a class="dropdown-item" href="index.php?page=riwayat"><i class="bi bi-clock-history text-warning"></i> Riwayat Transaksi</a></li>
                        <li><a class="dropdown-item" href="index.php?page=laporan_kat"><i class="bi bi-pie-chart-fill text-success"></i> Laporan Kategori</a></li>
                    </ul>
                </li>
            </ul>
            
            <div class="d-flex align-items-center gap-2">
                <span class="badge status-badge">
                    <i class="bi bi-circle-fill me-1" style="font-size:7px;vertical-align:middle;color:#10b981;"></i> Gudang Utama
                </span>
            </div>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container-fluid main-content">