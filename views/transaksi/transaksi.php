<?php
$filterTanggal = $_GET['tanggal'] ?? date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM transaksi WHERE DATE(tanggal) = :tgl ORDER BY id DESC");
$stmt->execute([':tgl' => $filterTanggal]);
$transaksiList = $stmt->fetchAll();

$stmtR = $pdo->prepare("SELECT COUNT(*) as total_trx, COALESCE(SUM(total_harga),0) as omzet FROM transaksi WHERE DATE(tanggal) = :tgl");
$stmtR->execute([':tgl' => $filterTanggal]);
$ringkasan = $stmtR->fetch();
?>

<h4 class="mb-3"><i class="bi bi-receipt"></i> Riwayat Transaksi</h4>

<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row align-items-center g-2">
            <input type="hidden" name="page" value="transaksi">
            <div class="col-auto"><label class="form-label mb-0 fw-bold">Tanggal:</label></div>
            <div class="col-auto"><input type="date" name="tanggal" class="form-control form-control-sm" value="<?= htmlspecialchars($filterTanggal) ?>"></div>
            <div class="col-auto"><button type="submit" class="btn btn-sm text-white" style="background-color: <?= htmlspecialchars($settings['warna_header']) ?>;"><i class="bi bi-search"></i> Tampilkan</button></div>
            <div class="col-auto"><a href="index.php?page=transaksi&tanggal=<?= date('Y-m-d') ?>" class="btn btn-outline-secondary btn-sm">Hari Ini</a></div>
        </form>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6 mb-2">
        <div class="card shadow-sm border-0 bg-primary text-white"><div class="card-body py-3 d-flex justify-content-between align-items-center">
            <div><small>Total Transaksi</small><h4 class="mb-0"><?= $ringkasan['total_trx'] ?></h4></div>
            <i class="bi bi-cart-check" style="font-size: 2rem; opacity: 0.5;"></i>
        </div></div>
    </div>
    <div class="col-md-6 mb-2">
        <div class="card shadow-sm border-0 bg-success text-white"><div class="card-body py-3 d-flex justify-content-between align-items-center">
            <div><small>Omzet</small><h4 class="mb-0"><?= formatRp($ringkasan['omzet']) ?></h4></div>
            <i class="bi bi-cash-stack" style="font-size: 2rem; opacity: 0.5;"></i>
        </div></div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($transaksiList)): ?>
            <div class="text-center text-muted py-5"><i class="bi bi-inbox" style="font-size:3rem;"></i><p class="mt-2">Belum ada transaksi.</p></div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light"><tr><th width="60">#</th><th>Waktu</th><th>Item</th><th class="text-end">Total</th><th class="text-end">Bayar</th><th class="text-end">Kembali</th><th class="text-center" width="80">Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($transaksiList as $trx):
                            $sd = $pdo->prepare("SELECT * FROM detail_transaksi WHERE transaksi_id = ? ORDER BY id");
                            $sd->execute([$trx['id']]); $details = $sd->fetchAll(); ?>
                        <tr data-bs-toggle="collapse" data-bs-target="#det-<?= $trx['id'] ?>" style="cursor:pointer;">
                            <td class="fw-bold">#<?= str_pad($trx['id'],4,'0',STR_PAD_LEFT) ?></td>
                            <td><?= date('H:i:s', strtotime($trx['tanggal'])) ?></td>
                            <td><span class="badge bg-secondary"><?= count($details) ?> item</span></td>
                            <td class="text-end fw-bold"><?= formatRp($trx['total_harga']) ?></td>
                            <td class="text-end"><?= formatRp($trx['bayar']) ?></td>
                            <td class="text-end"><?= formatRp($trx['kembalian']) ?></td>
                            <td class="text-center"><a href="struk.php?id=<?= $trx['id'] ?>" target="_blank" class="btn btn-sm btn-outline-success" onclick="event.stopPropagation();"><i class="bi bi-printer"></i></a></td>
                        </tr>
                        <tr class="collapse" id="det-<?= $trx['id'] ?>">
                            <td colspan="7" class="p-0 border-0"><div class="bg-light p-3">
                                <table class="table table-sm table-bordered mb-0 bg-white">
                                    <thead class="table-secondary"><tr><th>Barang</th><th>Varian</th><th class="text-center">Tipe</th><th class="text-end">Harga</th><th class="text-center">Qty</th><th class="text-end">Subtotal</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($details as $d): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($d['nama_barang']) ?></td>
                                            <td><?= $d['nama_varian'] ? htmlspecialchars($d['nama_varian']) : '-' ?></td>
                                            <td class="text-center"><span class="badge <?= $d['tipe_harga'] === 'grosir' ? 'badge-grosir' : 'badge-eceran' ?> text-white"><?= ucfirst($d['tipe_harga']) ?></span></td>
                                            <td class="text-end"><?= formatRp($d['harga']) ?></td>
                                            <td class="text-center"><?= $d['jumlah'] ?></td>
                                            <td class="text-end"><?= formatRp($d['subtotal']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
