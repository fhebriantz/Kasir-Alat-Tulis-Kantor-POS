<?php
require_once __DIR__ . '/../../controllers/BarangController.php';

$totalBarang = count(getAllBarang($pdo));

$stmtTrx = $pdo->query("SELECT COUNT(*) as total, COALESCE(SUM(total_harga),0) as omzet FROM transaksi");
$statTrx = $stmtTrx->fetch();

$stmtHariIni = $pdo->query("SELECT COUNT(*) as total, COALESCE(SUM(total_harga),0) as omzet FROM transaksi WHERE DATE(tanggal) = DATE('now','localtime')");
$statHariIni = $stmtHariIni->fetch();

// Stok menipis: stok <= stok_minimum
$stmtRendah = $pdo->query("SELECT b.*, k.nama as kategori_nama FROM barang b LEFT JOIN kategori k ON k.id = b.kategori_id WHERE b.stok <= b.stok_minimum ORDER BY b.stok ASC LIMIT 15");
$stokRendah = $stmtRendah->fetchAll();
?>

<h4 class="mb-3"><i class="bi bi-speedometer2"></i> Dashboard</h4>

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-0 bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="card-title mb-1">Total Barang</h6><h3 class="mb-0"><?= $totalBarang ?></h3></div>
                    <i class="bi bi-box-seam" style="font-size: 2.5rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-0 bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="card-title mb-1">Transaksi Hari Ini</h6><h3 class="mb-0"><?= $statHariIni['total'] ?></h3></div>
                    <i class="bi bi-cart-check" style="font-size: 2.5rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-0 bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="card-title mb-1">Omzet Hari Ini</h6><h5 class="mb-0"><?= formatRp($statHariIni['omzet']) ?></h5></div>
                    <i class="bi bi-cash-stack" style="font-size: 2.5rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <a href="#stokRendah" class="text-decoration-none" <?= count($stokRendah) > 0 ? '' : 'style="pointer-events:none;"' ?>>
            <div class="card shadow-sm border-0 bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="card-title mb-1">Stok Menipis</h6><h3 class="mb-0 <?= count($stokRendah) > 0 ? 'text-danger' : '' ?>"><?= count($stokRendah) ?></h3></div>
                        <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Menu Cepat -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <a href="index.php?page=kasir" class="text-decoration-none">
            <div class="card shadow-sm border-0 h-100"><div class="card-body text-center py-4">
                <i class="bi bi-calculator text-primary" style="font-size: 3rem;"></i>
                <h5 class="mt-2 text-dark">Mulai Transaksi</h5>
                <p class="text-muted mb-0">Buka kasir untuk memulai penjualan</p>
            </div></div>
        </a>
    </div>
    <div class="col-md-4 mb-3">
        <a href="index.php?page=barang" class="text-decoration-none">
            <div class="card shadow-sm border-0 h-100"><div class="card-body text-center py-4">
                <i class="bi bi-box-seam text-primary" style="font-size: 3rem;"></i>
                <h5 class="mt-2 text-dark">Kelola Barang</h5>
                <p class="text-muted mb-0">Tambah, edit barang & varian</p>
            </div></div>
        </a>
    </div>
    <div class="col-md-4 mb-3">
        <a href="index.php?page=barcode" class="text-decoration-none">
            <div class="card shadow-sm border-0 h-100"><div class="card-body text-center py-4">
                <i class="bi bi-upc-scan text-primary" style="font-size: 3rem;"></i>
                <h5 class="mt-2 text-dark">Generate Barcode</h5>
                <p class="text-muted mb-0">Buat & cetak barcode custom</p>
            </div></div>
        </a>
    </div>
</div>

<!-- Peringatan Stok Menipis -->
<?php if (!empty($stokRendah)): ?>
<div class="card shadow-sm border-danger mb-4" id="stokRendah">
    <div class="card-header bg-danger text-white">
        <i class="bi bi-exclamation-triangle"></i> Peringatan Stok Menipis (≤ batas minimum)
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr><th>Nama Barang</th><th>Kategori</th><th class="text-center">Stok</th><th class="text-center">Minimum</th><th>Satuan</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($stokRendah as $b): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($b['nama']) ?></strong></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($b['kategori_nama'] ?? '-') ?></span></td>
                        <td class="text-center"><span class="badge <?= $b['stok'] <= 0 ? 'bg-dark' : 'bg-danger' ?>"><?= $b['stok'] ?></span></td>
                        <td class="text-center"><?= $b['stok_minimum'] ?></td>
                        <td><?= ucfirst($b['satuan']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
