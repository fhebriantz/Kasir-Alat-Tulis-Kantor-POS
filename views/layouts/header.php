<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($settings['nama_toko'] ?? "Lia's Laci") ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<?php $warnaHeader = htmlspecialchars($settings['warna_header'] ?? '#6f42c1'); ?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: <?= $warnaHeader ?>;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php?page=dashboard">
            <?php if (!empty($settings['logo_toko'])): ?>
                <img src="uploads/<?= htmlspecialchars($settings['logo_toko']) ?>" alt="Logo" style="max-height: 30px;">
            <?php else: ?>
                <i class="bi bi-pencil-square"></i>
            <?php endif; ?>
            <?= htmlspecialchars($settings['nama_toko'] ?? "Lia's Laci") ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link <?= $page === 'dashboard' ? 'active' : '' ?>" href="index.php?page=dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link <?= $page === 'kasir' ? 'active' : '' ?>" href="index.php?page=kasir"><i class="bi bi-calculator"></i> Kasir</a></li>
                <li class="nav-item"><a class="nav-link <?= $page === 'barang' ? 'active' : '' ?>" href="index.php?page=barang"><i class="bi bi-box-seam"></i> Barang</a></li>
                <li class="nav-item"><a class="nav-link <?= $page === 'transaksi' ? 'active' : '' ?>" href="index.php?page=transaksi"><i class="bi bi-receipt"></i> Riwayat</a></li>
                <li class="nav-item"><a class="nav-link <?= $page === 'laporan' ? 'active' : '' ?>" href="index.php?page=laporan"><i class="bi bi-graph-up"></i> Laporan</a></li>
                <li class="nav-item"><a class="nav-link <?= $page === 'barcode' ? 'active' : '' ?>" href="index.php?page=barcode"><i class="bi bi-upc-scan"></i> Barcode</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link <?= $page === 'pengaturan' ? 'active' : '' ?>" href="index.php?page=pengaturan"><i class="bi bi-gear"></i> Pengaturan</a></li>
                <li class="nav-item"><a class="nav-link <?= $page === 'bantuan' ? 'active' : '' ?>" href="index.php?page=bantuan"><i class="bi bi-question-circle"></i> Bantuan</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container-fluid mt-3">
