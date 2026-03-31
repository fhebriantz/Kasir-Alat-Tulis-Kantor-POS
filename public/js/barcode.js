$(document).ready(function () {

    $('#selectBarang').on('change', function () {
        var opt = $(this).find(':selected');
        if (!opt.val()) {
            $('#inputBarcode').val('');
            $('#inputLabel').val('');
            $('#inputHarga').val('');
            return;
        }
        $('#inputBarcode').val(opt.val());
        $('#inputLabel').val(opt.data('nama') || '');
        var harga = parseInt(opt.data('harga')) || 0;
        $('#inputHarga').val(harga > 0 ? 'Rp ' + new Intl.NumberFormat('id-ID').format(harga) : '');
    });

    $('#btnGenerate').on('click', function () {
        var code = $('#inputBarcode').val().trim();
        var label = $('#inputLabel').val().trim();
        var harga = $('#inputHarga').val().trim();
        var jumlah = parseInt($('#inputJumlah').val()) || 1;

        if (!code) { alert('Pilih barang terlebih dahulu.'); return; }

        var $area = $('#previewArea').empty();
        var html = '<div class="d-flex flex-wrap gap-2 justify-content-center" id="barcodeContainer">';

        for (var i = 0; i < jumlah; i++) {
            html += '<div class="barcode-item text-center border p-2" style="width: 200px;">';
            if (label) html += '<div style="font-size: 10px; font-weight: bold; margin-bottom: 2px;">' + esc(label) + '</div>';
            html += '<svg class="barcode-svg" data-code="' + esc(code) + '"></svg>';
            if (harga) html += '<div style="font-size: 11px; font-weight: bold; margin-top: 2px;">' + esc(harga) + '</div>';
            html += '</div>';
        }
        html += '</div>';
        $area.html(html);

        try {
            $area.find('.barcode-svg').each(function () {
                JsBarcode(this, $(this).data('code'), {
                    format: 'CODE128', width: 1.5, height: 40, fontSize: 12, margin: 2, displayValue: true
                });
            });
            $('#btnCetak').prop('disabled', false);
        } catch (e) {
            $area.html('<div class="alert alert-danger">Gagal generate barcode. Kode mungkin tidak valid.</div>');
            $('#btnCetak').prop('disabled', true);
        }
    });

    $('#btnCetak').on('click', function () {
        var content = document.getElementById('barcodeContainer').innerHTML;
        var win = window.open('', '_blank', 'width=600,height=400');
        win.document.write('<html><head><title>Cetak Barcode</title><style>'
            + 'body { font-family: sans-serif; margin: 10px; }'
            + '.barcode-item { display: inline-block; text-align: center; border: 1px dashed #ccc; padding: 5px; margin: 3px; }'
            + '@media print { .barcode-item { border: 1px dashed #ccc; page-break-inside: avoid; } }'
            + '</style></head><body>'
            + '<div style="display:flex;flex-wrap:wrap;gap:5px;justify-content:center;">' + content + '</div>'
            + '<script>window.onload=function(){window.print();}<\/script>'
            + '</body></html>');
        win.document.close();
    });

    function esc(t) { var d = document.createElement('div'); d.appendChild(document.createTextNode(t)); return d.innerHTML; }
});
