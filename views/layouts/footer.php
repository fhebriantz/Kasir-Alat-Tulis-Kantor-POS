</div>
<footer class="text-center text-muted py-3 mt-4 border-top" style="font-size: 0.85rem;">
    <strong>Lia's Laci</strong> v1.0.0 &mdash; Kasir Toko ATK &copy; <?= date('Y') ?> Lutfi Febrianto
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="js/app.js"></script>
<?php if ($page === 'kasir'): ?>
<script src="js/kasir.js"></script>
<?php endif; ?>
<?php if ($page === 'barcode'): ?>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script src="js/barcode.js"></script>
<?php endif; ?>
<script>
(function(){
    var f = document.querySelector('footer');
    if (!f || f.innerHTML.indexOf("Lia's Laci") === -1) {
        document.body.innerHTML = '<div style="text-align:center;padding:50px;"><h1 style="color:red;">Aplikasi Tidak Sah</h1></div>';
    }
})();
</script>
</body>
</html>
