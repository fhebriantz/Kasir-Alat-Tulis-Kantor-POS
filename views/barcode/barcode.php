<?php
require_once __DIR__ . '/../../controllers/BarangController.php';
$barangList = getAllBarang($pdo);

// Siapkan data untuk dropdown (termasuk varian)
$allItems = [];
foreach ($barangList as $b) {
    if ($b['has_varian']) {
        $varians = getVarianByBarang($pdo, $b['id']);
        foreach ($varians as $v) {
            $allItems[] = [
                'nama'    => $b['nama'] . ' — ' . $v['nama'],
                'barcode' => $v['barcode'] ?? '',
                'harga'   => $b['harga_eceran'],
            ];
        }
    } else {
        $allItems[] = [
            'nama'    => $b['nama'],
            'barcode' => $b['barcode'] ?? '',
            'harga'   => $b['harga_eceran'],
        ];
    }
}
?>

<h4 class="mb-3"><i class="bi bi-upc-scan"></i> Cetak Barcode</h4>

<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm">
            <div class="card-header text-white" style="background-color: <?= htmlspecialchars($settings['warna_header'] ?? '#6f42c1') ?>;">
                <i class="bi bi-gear"></i> Pilih Barang
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Barang / Varian</label>
                    <select id="selectBarang" class="form-select">
                        <option value="">-- Pilih barang --</option>
                        <?php foreach ($allItems as $item): ?>
                            <?php if ($item['barcode']): ?>
                            <option value="<?= htmlspecialchars($item['barcode']) ?>"
                                    data-nama="<?= htmlspecialchars($item['nama']) ?>"
                                    data-harga="<?= $item['harga'] ?>">
                                <?= htmlspecialchars($item['nama']) ?> (<?= htmlspecialchars($item['barcode']) ?>)
                            </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Kode Barcode</label>
                    <input type="text" id="inputBarcode" class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Label / Nama</label>
                    <input type="text" id="inputLabel" class="form-control" placeholder="Otomatis dari barang">
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Tampilkan Harga</label>
                        <input type="text" id="inputHarga" class="form-control" placeholder="Opsional">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Jumlah Cetak</label>
                        <input type="number" id="inputJumlah" class="form-control" value="1" min="1" max="100">
                    </div>
                </div>

                <div class="d-grid">
                    <button type="button" class="btn text-white" id="btnGenerate" style="background-color: <?= htmlspecialchars($settings['warna_header'] ?? '#6f42c1') ?>;">
                        <i class="bi bi-upc-scan"></i> Generate Preview
                    </button>
                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <i class="bi bi-info-circle"></i> Semua barang sudah punya barcode otomatis. Pilih barang, atur jumlah cetak, lalu klik Generate Preview.
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm">
            <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: <?= htmlspecialchars($settings['warna_header'] ?? '#6f42c1') ?>;">
                <span><i class="bi bi-printer"></i> Preview Cetak</span>
                <button class="btn btn-light btn-sm" id="btnCetak" disabled><i class="bi bi-printer"></i> Cetak</button>
            </div>
            <div class="card-body" id="previewArea">
                <div class="text-center text-muted py-5">
                    <i class="bi bi-upc-scan" style="font-size: 3rem;"></i>
                    <p class="mt-2">Pilih barang dan klik Generate Preview.</p>
                </div>
            </div>
        </div>
    </div>
</div>
