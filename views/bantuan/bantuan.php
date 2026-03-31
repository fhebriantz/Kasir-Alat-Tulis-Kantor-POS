<?php $versi = '1.0.0'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-question-circle"></i> Bantuan & Panduan</h4>
    <span class="badge fs-6 text-white" style="background-color:<?= htmlspecialchars($settings['warna_header']) ?>">v<?= $versi ?></span>
</div>
<div class="row">
    <div class="col-lg-3 mb-4">
        <div class="card shadow-sm sticky-top" style="top:70px;">
            <div class="card-header text-white" style="background-color:<?= htmlspecialchars($settings['warna_header']) ?>"><i class="bi bi-list-ul"></i> Daftar Isi</div>
            <div class="list-group list-group-flush">
                <a href="#mulai" class="list-group-item list-group-item-action"><i class="bi bi-lightning"></i> Mulai Cepat</a>
                <a href="#kasir" class="list-group-item list-group-item-action"><i class="bi bi-calculator"></i> Kasir</a>
                <a href="#harga" class="list-group-item list-group-item-action"><i class="bi bi-tags"></i> Harga Bertingkat</a>
                <a href="#varian" class="list-group-item list-group-item-action"><i class="bi bi-palette2"></i> Varian</a>
                <a href="#barcode" class="list-group-item list-group-item-action"><i class="bi bi-upc-scan"></i> Barcode</a>
                <a href="#reset" class="list-group-item list-group-item-action text-danger"><i class="bi bi-arrow-counterclockwise"></i> Reset Database</a>
                <a href="#teknis" class="list-group-item list-group-item-action"><i class="bi bi-code-slash"></i> Info Teknis</a>
            </div>
        </div>
    </div>
    <div class="col-lg-9 mb-4">

        <div class="card shadow-sm mb-3" id="mulai">
            <div class="card-header text-white" style="background-color:<?= htmlspecialchars($settings['warna_header']) ?>"><i class="bi bi-lightning"></i> Mulai Cepat</div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3"><div class="border rounded p-3 h-100"><div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 text-white" style="width:40px;height:40px;background-color:<?= htmlspecialchars($settings['warna_header']) ?>">1</div><h6>Tambah Barang</h6><small class="text-muted">Buka menu Barang, isi form, klik Tambah</small></div></div>
                    <div class="col-md-3 mb-3"><div class="border rounded p-3 h-100"><div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 text-white" style="width:40px;height:40px;background-color:<?= htmlspecialchars($settings['warna_header']) ?>">2</div><h6>Buka Kasir</h6><small class="text-muted">Cari barang, masukkan ke keranjang</small></div></div>
                    <div class="col-md-3 mb-3"><div class="border rounded p-3 h-100"><div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 text-white" style="width:40px;height:40px;background-color:<?= htmlspecialchars($settings['warna_header']) ?>">3</div><h6>Bayar & Simpan</h6><small class="text-muted">Input uang bayar, simpan transaksi</small></div></div>
                    <div class="col-md-3 mb-3"><div class="border rounded p-3 h-100"><div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 text-white" style="width:40px;height:40px;background-color:<?= htmlspecialchars($settings['warna_header']) ?>">4</div><h6>Cetak Struk</h6><small class="text-muted">Struk otomatis terbuka</small></div></div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3" id="kasir">
            <div class="card-header text-white" style="background-color:<?= htmlspecialchars($settings['warna_header']) ?>"><i class="bi bi-calculator"></i> Halaman Kasir</div>
            <div class="card-body">
                <ol>
                    <li>Ketik nama barang atau scan barcode di kolom pencarian</li>
                    <li>Pilih barang — otomatis masuk keranjang</li>
                    <li>Atur qty — harga otomatis switch eceran/grosir berdasarkan jumlah</li>
                    <li>Input uang bayar, kembalian otomatis terhitung</li>
                    <li>Klik <strong>Simpan Transaksi</strong></li>
                </ol>
                <div class="alert alert-info mb-0"><i class="bi bi-info-circle"></i> Stok berkurang otomatis. Jika barang punya varian, stok per varian yang berkurang.</div>
            </div>
        </div>

        <div class="card shadow-sm mb-3" id="harga">
            <div class="card-header text-white" style="background-color:<?= htmlspecialchars($settings['warna_header']) ?>"><i class="bi bi-tags"></i> Harga Bertingkat (Eceran & Grosir)</div>
            <div class="card-body">
                <p>Setiap barang bisa punya 2 harga:</p>
                <ul>
                    <li><span class="badge badge-eceran text-white">Eceran</span> — harga normal untuk pembelian satuan</li>
                    <li><span class="badge badge-grosir text-white">Grosir</span> — harga diskon otomatis jika qty ≥ <strong>Min. Qty Grosir</strong></li>
                </ul>
                <p>Contoh: Pulpen harga eceran Rp 5.000, grosir Rp 4.000 (min 12 pcs). Jika kasir input qty 12, otomatis pakai harga Rp 4.000.</p>
                <div class="alert alert-warning mb-0"><i class="bi bi-exclamation-triangle"></i> Jika harga grosir dikosongkan (0), barang hanya punya harga eceran.</div>
            </div>
        </div>

        <div class="card shadow-sm mb-3" id="varian">
            <div class="card-header text-white" style="background-color:<?= htmlspecialchars($settings['warna_header']) ?>"><i class="bi bi-palette2"></i> Manajemen Varian</div>
            <div class="card-body">
                <p>Untuk barang yang punya warna/ukuran berbeda:</p>
                <ol>
                    <li>Saat tambah barang, centang <strong>"Barang ini punya varian"</strong></li>
                    <li>Setelah disimpan, klik tombol <i class="bi bi-palette2 text-info"></i> di tabel barang</li>
                    <li>Tambahkan varian (contoh: Merah, Biru, A4, A3) dengan stok masing-masing</li>
                    <li>Setiap varian bisa punya barcode sendiri</li>
                </ol>
                <div class="alert alert-info mb-0"><i class="bi bi-info-circle"></i> Total stok barang = jumlah stok semua varian. Di kasir, pencarian menampilkan per varian.</div>
            </div>
        </div>

        <div class="card shadow-sm mb-3" id="barcode">
            <div class="card-header text-white" style="background-color:<?= htmlspecialchars($settings['warna_header']) ?>"><i class="bi bi-upc-scan"></i> Generate & Cetak Barcode</div>
            <div class="card-body">
                <ul>
                    <li>Buka menu <strong>Barcode</strong></li>
                    <li>Pilih barang atau input kode manual</li>
                    <li>Klik <strong>Generate Kode Random</strong> untuk barang tanpa barcode pabrik</li>
                    <li>Pilih jumlah cetak dan format (CODE128, EAN-13, EAN-8)</li>
                    <li>Klik <strong>Cetak</strong> untuk print label barcode</li>
                </ul>
                <div class="alert alert-info mb-0"><i class="bi bi-info-circle"></i> Kode random menggunakan prefix "LIA" + timestamp. Cocok untuk pernak-pernik tanpa barcode pabrik.</div>
            </div>
        </div>

        <div class="card shadow-sm mb-3 border-danger" id="reset">
            <div class="card-header bg-danger text-white"><i class="bi bi-exclamation-triangle"></i> Reset Database</div>
            <div class="card-body">
                <p><strong>Reset Transaksi</strong> — hapus semua transaksi, barang & pengaturan tetap.</p>
                <p><strong>Reset Seluruh Database</strong> — hapus semua data, kembali ke default.</p>
                <button type="button" class="btn btn-outline-danger" id="btnResetTrx"><i class="bi bi-trash"></i> Reset Transaksi</button>
                <button type="button" class="btn btn-outline-dark ms-2" id="btnResetAll"><i class="bi bi-arrow-counterclockwise"></i> Reset Semua</button>
            </div>
        </div>

        <div class="card shadow-sm mb-3" id="teknis">
            <div class="card-header bg-secondary text-white"><i class="bi bi-code-slash"></i> Informasi Teknis</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><td><strong>Nama Aplikasi</strong></td><td><strong>Lia's Laci</strong></td></tr>
                    <tr><td><strong>Versi</strong></td><td>v<?= $versi ?></td></tr>
                    <tr><td><strong>Tech Stack</strong></td><td>PHP Native + SQLite + Bootstrap 5 + jQuery</td></tr>
                    <tr><td><strong>Database</strong></td><td>SQLite (<code>database/kasir_atk.db</code>)</td></tr>
                    <tr><td><strong>Struk</strong></td><td>58mm thermal printer</td></tr>
                    <tr><td><strong>Barcode</strong></td><td>JsBarcode (CODE128, EAN-13, EAN-8)</td></tr>
                </table>
                <div class="alert alert-secondary mt-3 mb-0"><i class="bi bi-shield-lock"></i> <strong>Lia's Laci</strong> adalah nama resmi aplikasi ini. Tidak boleh diubah oleh siapapun.</div>
            </div>
        </div>

    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.getElementById('btnResetTrx').addEventListener('click', function(){
        if(!confirm('YAKIN hapus semua transaksi?')) return;
        fetch('api.php?action=reset_transaksi',{method:'POST'}).then(r=>r.json()).then(res=>{alert(res.message);if(res.success)location.reload();});
    });
    document.getElementById('btnResetAll').addEventListener('click', function(){
        if(!confirm('HAPUS SEMUA DATA?')) return; if(!confirm('TERAKHIR KALI. Yakin?')) return;
        fetch('api.php?action=reset_all',{method:'POST'}).then(r=>r.json()).then(res=>{alert(res.message);if(res.success)location.href='index.php';});
    });
});
</script>
