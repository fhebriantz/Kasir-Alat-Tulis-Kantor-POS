<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

$pdo = getConnection();
$settings = getSemuaPengaturan($pdo);

$page = $_GET['page'] ?? 'dashboard';

$allowedPages = ['dashboard', 'kasir', 'barang', 'transaksi', 'laporan', 'barcode', 'pengaturan', 'bantuan'];
if (!in_array($page, $allowedPages)) {
    $page = 'dashboard';
}

$viewMap = [
    'dashboard'   => 'dashboard/dashboard',
    'kasir'       => 'kasir/kasir',
    'barang'      => 'barang/barang',
    'transaksi'   => 'transaksi/transaksi',
    'laporan'     => 'laporan/laporan',
    'barcode'     => 'barcode/barcode',
    'pengaturan'  => 'pengaturan/pengaturan',
    'bantuan'     => 'bantuan/bantuan',
];

include __DIR__ . '/../views/layouts/header.php';
include __DIR__ . "/../views/{$viewMap[$page]}.php";
include __DIR__ . '/../views/layouts/footer.php';
