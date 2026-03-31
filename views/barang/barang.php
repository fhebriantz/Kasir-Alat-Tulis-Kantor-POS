<?php
require_once __DIR__ . '/../../controllers/BarangController.php';

$flash = handleBarangAction($pdo);
$barangList = getAllBarang($pdo);
$kategoriList = getAllKategori($pdo);

$edit = null;
$editVarian = [];
if (isset($_GET['edit'])) {
    $edit = getBarangById($pdo, (int)$_GET['edit']);
    if ($edit) $editVarian = getVarianByBarang($pdo, $edit['id']);
}

$showVarian = isset($_GET['varian']);
$varianBarang = null;
$varianList = [];
if ($showVarian) {
    $varianBarang = getBarangById($pdo, (int)$_GET['varian']);
    if ($varianBarang) $varianList = getVarianByBarang($pdo, $varianBarang['id']);
}
?>

<?php if ($flash): ?>
    <?php [$type, $msg] = explode('|', $flash, 2); ?>
    <div class="alert alert-<?= htmlspecialchars($type) ?> alert-dismissible fade show"><?= htmlspecialchars($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<?php if ($showVarian && $varianBarang): ?>
<!-- MODE: Kelola Varian -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-palette2"></i> Varian: <?= htmlspecialchars($varianBarang['nama']) ?></h4>
    <a href="index.php?page=barang" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="index.php?page=barang&varian=<?= $varianBarang['id'] ?>">
            <input type="hidden" name="action" value="save_varian">
            <input type="hidden" name="barang_id" value="<?= $varianBarang['id'] ?>">
            <table class="table table-bordered align-middle" id="tabelVarian">
                <thead class="table-light">
                    <tr><th>Nama Varian (Warna/Ukuran)</th><th width="120">Stok</th><th width="200">Barcode</th><th width="50"></th></tr>
                </thead>
                <tbody>
                    <?php if (empty($varianList)): ?>
                    <tr class="row-varian">
                        <td><input type="text" name="varian_nama[]" class="form-control" placeholder="Contoh: Merah" required></td>
                        <td><input type="number" name="varian_stok[]" class="form-control" value="0" min="0"></td>
                        <td><input type="text" name="varian_barcode[]" class="form-control" placeholder="Opsional"></td>
                        <td><button type="button" class="btn btn-sm btn-outline-danger btn-hapus-varian"><i class="bi bi-trash"></i></button></td>
                    </tr>
                    <?php else: foreach ($varianList as $v): ?>
                    <tr class="row-varian">
                        <td><input type="text" name="varian_nama[]" class="form-control" value="<?= htmlspecialchars($v['nama']) ?>" required></td>
                        <td><input type="number" name="varian_stok[]" class="form-control" value="<?= $v['stok'] ?>" min="0"></td>
                        <td><input type="text" name="varian_barcode[]" class="form-control" value="<?= htmlspecialchars($v['barcode'] ?? '') ?>"></td>
                        <td><button type="button" class="btn btn-sm btn-outline-danger btn-hapus-varian"><i class="bi bi-trash"></i></button></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
            <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="btnTambahVarian"><i class="bi bi-plus"></i> Tambah Varian</button>
            <div class="d-grid"><button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Simpan Varian</button></div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.getElementById('btnTambahVarian').addEventListener('click', function(){
        let row = '<tr class="row-varian"><td><input type="text" name="varian_nama[]" class="form-control" placeholder="Contoh: Biru" required></td><td><input type="number" name="varian_stok[]" class="form-control" value="0" min="0"></td><td><input type="text" name="varian_barcode[]" class="form-control" placeholder="Opsional"></td><td><button type="button" class="btn btn-sm btn-outline-danger btn-hapus-varian"><i class="bi bi-trash"></i></button></td></tr>';
        document.querySelector('#tabelVarian tbody').insertAdjacentHTML('beforeend', row);
    });
    document.addEventListener('click', function(e){
        if(e.target.closest('.btn-hapus-varian')){
            let rows = document.querySelectorAll('.row-varian');
            if(rows.length > 1) e.target.closest('tr').remove();
        }
    });
});
</script>

<?php else: ?>
<!-- MODE: CRUD Barang -->
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header text-white" style="background-color: <?= htmlspecialchars($settings['warna_header'] ?? '#6f42c1') ?>;">
                <i class="bi bi-<?= $edit ? 'pencil-square' : 'plus-circle' ?>"></i>
                <?= $edit ? 'Edit Barang' : 'Tambah Barang' ?>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?page=barang">
                    <input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>">
                    <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>

                    <div class="mb-2">
                        <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($edit['nama'] ?? '') ?>" placeholder="Contoh: Pulpen Pilot G2">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Kategori</label>
                        <select name="kategori_id" class="form-select">
                            <option value="">-- Pilih --</option>
                            <?php foreach ($kategoriList as $k): ?>
                                <option value="<?= $k['id'] ?>" <?= ($edit['kategori_id'] ?? '') == $k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <label class="form-label">Harga Eceran</label>
                            <div class="input-group"><span class="input-group-text">Rp</span>
                            <input type="number" name="harga_eceran" class="form-control" min="0" step="100" value="<?= $edit['harga_eceran'] ?? 0 ?>"></div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Harga Grosir</label>
                            <div class="input-group"><span class="input-group-text">Rp</span>
                            <input type="number" name="harga_grosir" class="form-control" min="0" step="100" value="<?= $edit['harga_grosir'] ?? 0 ?>"></div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Min. Qty Grosir</label>
                        <input type="number" name="min_grosir" class="form-control" min="1" value="<?= $edit['min_grosir'] ?? 12 ?>">
                        <div class="form-text">Jika qty ≥ angka ini, otomatis pakai harga grosir</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stok" class="form-control" min="0" value="<?= $edit['stok'] ?? 0 ?>">
                        </div>
                        <div class="col-4">
                            <label class="form-label">Min. Stok</label>
                            <input type="number" name="stok_minimum" class="form-control" min="0" value="<?= $edit['stok_minimum'] ?? 5 ?>">
                        </div>
                        <div class="col-4">
                            <label class="form-label">Satuan</label>
                            <select name="satuan" class="form-select">
                                <?php foreach (['pcs','pak','rim','lusin','box','set','roll','lembar'] as $s): ?>
                                    <option value="<?= $s ?>" <?= ($edit['satuan'] ?? 'pcs') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Barcode</label>
                        <input type="text" name="barcode" id="inputBarcode" class="form-control" value="<?= htmlspecialchars($edit['barcode'] ?? '') ?>" placeholder="Kosongkan = auto-generate">
                        <div id="barcodeWarning" class="text-danger small mt-1" style="display:none;"></div>
                        <div class="form-text">Jika dikosongkan, barcode unik akan di-generate otomatis (prefix LIA)</div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="hidden" name="has_varian" value="0">
                        <input type="checkbox" name="has_varian" value="1" class="form-check-input" id="chkVarian" <?= ($edit['has_varian'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="chkVarian">Barang ini punya varian (warna/ukuran)</label>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success"><i class="bi bi-<?= $edit ? 'check-lg' : 'plus-lg' ?>"></i> <?= $edit ? 'Simpan Perubahan' : 'Tambah Barang' ?></button>
                        <?php if ($edit): ?><a href="index.php?page=barang" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i> Batal</a><?php endif; ?>
                    </div>
                </form>

                <!-- Tambah Kategori Cepat -->
                <hr>
                <form method="POST" action="index.php?page=barang" class="d-flex gap-2">
                    <input type="hidden" name="action" value="tambah_kategori">
                    <input type="text" name="nama_kategori" class="form-control form-control-sm" placeholder="Kategori baru...">
                    <button type="submit" class="btn btn-outline-primary btn-sm text-nowrap"><i class="bi bi-plus"></i> Kategori</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: <?= htmlspecialchars($settings['warna_header'] ?? '#6f42c1') ?>;">
                <span><i class="bi bi-box-seam"></i> Data Barang</span>
                <span class="badge bg-light text-dark"><?= count($barangList) ?> item</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($barangList)): ?>
                    <div class="text-center text-muted py-5"><i class="bi bi-inbox" style="font-size: 3rem;"></i><p class="mt-2">Belum ada data barang.</p></div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle">
                            <thead class="table-light">
                                <tr><th width="35">#</th><th>Nama</th><th>Kategori</th><th class="text-end">Eceran</th><th class="text-end">Grosir</th><th class="text-center">Stok</th><th class="text-center" width="130">Aksi</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($barangList as $i => $b): ?>
                                <tr>
                                    <td class="text-muted"><?= $i+1 ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($b['nama']) ?></strong>
                                        <?php if ($b['has_varian']): ?><br><small class="text-primary"><i class="bi bi-palette2"></i> <?= $b['jumlah_varian'] ?> varian</small><?php endif; ?>
                                        <?php if ($b['barcode']): ?><br><small class="text-muted"><i class="bi bi-upc-scan"></i> <?= htmlspecialchars($b['barcode']) ?></small><?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($b['kategori_nama'] ?? '-') ?></span></td>
                                    <td class="text-end"><?= formatRp($b['harga_eceran']) ?></td>
                                    <td class="text-end"><?= $b['harga_grosir'] > 0 ? formatRp($b['harga_grosir']) . '<br><small class="text-muted">min ' . $b['min_grosir'] . '</small>' : '-' ?></td>
                                    <td class="text-center">
                                        <?php if ($b['stok'] <= 0): ?><span class="badge bg-dark"><?= $b['stok'] ?></span>
                                        <?php elseif ($b['stok'] <= $b['stok_minimum']): ?><span class="badge bg-danger"><?= $b['stok'] ?></span>
                                        <?php else: ?><span class="badge bg-success"><?= $b['stok'] ?></span><?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($b['has_varian']): ?>
                                            <a href="index.php?page=barang&varian=<?= $b['id'] ?>" class="btn btn-sm btn-outline-info" title="Varian"><i class="bi bi-palette2"></i></a>
                                        <?php endif; ?>
                                        <a href="index.php?page=barang&edit=<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <form method="POST" action="index.php?page=barang" class="d-inline" onsubmit="return confirm('Hapus barang ini?')">
                                            <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $b['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Cek duplikat barcode -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    var inputBarcode = document.getElementById('inputBarcode');
    var barcodeWarning = document.getElementById('barcodeWarning');
    if (!inputBarcode) return;

    var timer = null;
    var editId = <?= json_encode($edit['id'] ?? 0) ?>;

    inputBarcode.addEventListener('input', function () {
        clearTimeout(timer);
        var val = this.value.trim();
        barcodeWarning.style.display = 'none';
        inputBarcode.classList.remove('is-invalid');

        if (val === '') return;

        timer = setTimeout(function () {
            var url = 'api.php?action=check_barcode&barcode=' + encodeURIComponent(val);
            if (editId > 0) url += '&exclude_id=' + editId;

            fetch(url).then(function(r){ return r.json(); }).then(function(res) {
                if (res.exists) {
                    barcodeWarning.textContent = res.message;
                    barcodeWarning.style.display = 'block';
                    inputBarcode.classList.add('is-invalid');
                }
            });
        }, 400);
    });
});
</script>
