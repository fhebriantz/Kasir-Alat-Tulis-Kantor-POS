<?php
require_once __DIR__ . '/../../controllers/PengaturanController.php';
$flash = handlePengaturanAction($pdo);
$settings = getSemuaPengaturan($pdo);
?>
<?php if ($flash): ?><?php [$type,$msg]=explode('|',$flash,2); ?>
<div class="alert alert-<?= htmlspecialchars($type) ?> alert-dismissible fade show"><?= htmlspecialchars($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<h4 class="mb-3"><i class="bi bi-gear"></i> Pengaturan Toko</h4>
<div class="row">
    <div class="col-lg-7 mb-4">
        <form method="POST" action="index.php?page=pengaturan">
            <input type="hidden" name="action" value="simpan_umum">
            <div class="card shadow-sm mb-3">
                <div class="card-header text-white" style="background-color:<?= htmlspecialchars($settings['warna_header']) ?>"><i class="bi bi-shop"></i> Informasi Toko</div>
                <div class="card-body">
                    <div class="mb-3"><label class="form-label">Nama Toko *</label><input type="text" name="nama_toko" class="form-control" required value="<?= htmlspecialchars($settings['nama_toko']) ?>"></div>
                    <div class="mb-3"><label class="form-label">Alamat</label><input type="text" name="alamat_toko" class="form-control" value="<?= htmlspecialchars($settings['alamat_toko']) ?>"></div>
                    <div class="mb-3"><label class="form-label">Telepon</label><input type="text" name="telepon_toko" class="form-control" value="<?= htmlspecialchars($settings['telepon_toko']) ?>"></div>
                </div>
            </div>
            <div class="card shadow-sm mb-3">
                <div class="card-header text-white" style="background-color:<?= htmlspecialchars($settings['warna_header']) ?>"><i class="bi bi-printer"></i> Layout Struk</div>
                <div class="card-body"><div class="mb-3"><label class="form-label">Footer Struk</label><textarea name="footer_struk" class="form-control" rows="2"><?= htmlspecialchars($settings['footer_struk']) ?></textarea></div></div>
            </div>
            <div class="card shadow-sm mb-3">
                <div class="card-header text-white" style="background-color:<?= htmlspecialchars($settings['warna_header']) ?>"><i class="bi bi-palette"></i> Tampilan</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Warna Header</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="color" name="warna_header" class="form-control form-control-color" id="inputWarna" value="<?= htmlspecialchars($settings['warna_header']) ?>">
                            <input type="text" class="form-control" style="max-width:120px;" id="inputWarnaText" value="<?= htmlspecialchars($settings['warna_header']) ?>" readonly>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <?php foreach (['#6f42c1'=>'Ungu','#198754'=>'Hijau','#0d6efd'=>'Biru','#dc3545'=>'Merah','#fd7e14'=>'Oranye','#d63384'=>'Pink','#20c997'=>'Teal','#212529'=>'Hitam'] as $hex=>$n): ?>
                        <button type="button" class="btn btn-sm border btn-preset-warna" data-warna="<?= $hex ?>" style="background:<?= $hex ?>;width:36px;height:36px;" title="<?= $n ?>"></button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-lg w-100 text-white" style="background-color:<?= htmlspecialchars($settings['warna_header']) ?>"><i class="bi bi-check-lg"></i> Simpan Pengaturan</button>
        </form>
    </div>
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm">
            <div class="card-header text-white" style="background-color:<?= htmlspecialchars($settings['warna_header']) ?>"><i class="bi bi-image"></i> Logo Toko</div>
            <div class="card-body text-center">
                <div class="mb-3 p-3 bg-light rounded">
                    <?php if (!empty($settings['logo_toko'])): ?>
                        <img src="uploads/<?= htmlspecialchars($settings['logo_toko']) ?>" class="img-fluid" style="max-height:150px;" id="previewLogo">
                    <?php else: ?>
                        <div id="previewLogo" class="text-muted py-4"><i class="bi bi-image" style="font-size:4rem;"></i><p class="mb-0 mt-2">Belum ada logo</p></div>
                    <?php endif; ?>
                </div>
                <form method="POST" action="index.php?page=pengaturan" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="upload_logo">
                    <div class="mb-3"><input type="file" name="logo" class="form-control" accept="image/*"><div class="form-text">PNG, JPG, GIF, WebP. Maks 2MB.</div></div>
                    <button type="submit" class="btn w-100 text-white" style="background-color:<?= htmlspecialchars($settings['warna_header']) ?>"><i class="bi bi-upload"></i> Upload Logo</button>
                </form>
                <?php if (!empty($settings['logo_toko'])): ?><hr>
                <form method="POST" action="index.php?page=pengaturan" onsubmit="return confirm('Hapus logo?')">
                    <input type="hidden" name="action" value="hapus_logo">
                    <button type="submit" class="btn btn-outline-danger w-100"><i class="bi bi-trash"></i> Hapus Logo</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const iw=document.getElementById('inputWarna'), it=document.getElementById('inputWarnaText');
    iw.addEventListener('input', function(){ it.value=this.value; });
    document.querySelectorAll('.btn-preset-warna').forEach(b => b.addEventListener('click', function(){ iw.value=this.dataset.warna; it.value=this.dataset.warna; }));
});
</script>
