$(document).ready(function () {
    let keranjang = [];

    function fmt(n) { return new Intl.NumberFormat('id-ID').format(n); }

    $('#cariBarang').select2({
        placeholder: 'Ketik nama barang atau scan barcode...',
        allowClear: true, minimumInputLength: 1,
        ajax: {
            url: 'api.php?action=search_barang', dataType: 'json', delay: 300,
            data: function (p) { return { q: p.term }; },
            processResults: function (d) { return { results: d.results }; },
            cache: true
        },
        templateResult: function (item) {
            if (!item.id) return item.text;
            let label = '<strong>' + item.nama + '</strong>';
            if (item.varian_nama) label += ' <span class="text-muted">— ' + item.varian_nama + '</span>';
            label += '<br><small class="text-muted">Eceran: Rp ' + fmt(item.harga_eceran);
            if (item.harga_grosir > 0) label += ' | Grosir: Rp ' + fmt(item.harga_grosir) + ' (min ' + item.min_grosir + ')';
            label += ' · Stok: ' + item.stok + '</small>';
            return $('<div>').html(label);
        },
        templateSelection: function (item) {
            let n = item.nama || item.text;
            if (item.varian_nama) n += ' — ' + item.varian_nama;
            return n;
        }
    });

    $('#cariBarang').on('select2:select', function (e) {
        tambahKeKeranjang(e.params.data);
        $(this).val(null).trigger('change');
    });

    function tambahKeKeranjang(item) {
        let key = item.id + '_' + (item.varian_id || '0');
        let existing = keranjang.find(k => k.key === key);
        if (existing) {
            if (existing.jumlah + 1 > item.stok) { alert('Stok "' + item.nama + '" tidak cukup!'); return; }
            existing.jumlah += 1;
            hitungHargaItem(existing);
        } else {
            if (item.stok <= 0) { alert('Stok "' + item.nama + '" habis!'); return; }
            let entry = {
                key: key, id: item.id, nama: item.nama,
                varian_id: item.varian_id, varian_nama: item.varian_nama,
                harga_eceran: parseFloat(item.harga_eceran),
                harga_grosir: parseFloat(item.harga_grosir),
                min_grosir: parseInt(item.min_grosir),
                stok: parseInt(item.stok), satuan: item.satuan, jumlah: 1,
                harga: parseFloat(item.harga_eceran), tipe_harga: 'eceran'
            };
            hitungHargaItem(entry);
            keranjang.push(entry);
        }
        renderKeranjang();
    }

    function hitungHargaItem(item) {
        if (item.harga_grosir > 0 && item.jumlah >= item.min_grosir) {
            item.harga = item.harga_grosir;
            item.tipe_harga = 'grosir';
        } else {
            item.harga = item.harga_eceran;
            item.tipe_harga = 'eceran';
        }
    }

    function renderKeranjang() {
        let $body = $('#bodyKeranjang').empty();
        if (keranjang.length === 0) {
            $body.html('<tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-cart-x" style="font-size:2rem;"></i><p class="mb-0 mt-1">Keranjang masih kosong</p></td></tr>');
            $('#badgeTotal').text('0 item');
            hitungTotal(); return;
        }
        keranjang.forEach(function (item, i) {
            let sub = item.harga * item.jumlah;
            let nama = escapeHtml(item.nama);
            if (item.varian_nama) nama += '<br><small class="text-muted">' + escapeHtml(item.varian_nama) + '</small>';
            let badge = item.tipe_harga === 'grosir'
                ? '<span class="badge badge-grosir text-white">Grosir</span>'
                : '<span class="badge badge-eceran text-white">Eceran</span>';

            $body.append('<tr>' +
                '<td class="text-muted">' + (i+1) + '</td>' +
                '<td>' + nama + '</td>' +
                '<td class="text-center">' + badge + '</td>' +
                '<td class="text-end">Rp ' + fmt(item.harga) + '</td>' +
                '<td class="text-center"><div class="input-group input-group-sm justify-content-center">' +
                    '<button class="btn btn-outline-secondary btn-qty-minus" data-i="'+i+'" type="button">-</button>' +
                    '<input type="number" class="form-control text-center input-qty" value="'+item.jumlah+'" min="1" max="'+item.stok+'" data-i="'+i+'" style="max-width:55px;">' +
                    '<button class="btn btn-outline-secondary btn-qty-plus" data-i="'+i+'" type="button">+</button>' +
                '</div></td>' +
                '<td class="text-end fw-bold">Rp ' + fmt(sub) + '</td>' +
                '<td><button class="btn btn-sm btn-outline-danger btn-hapus" data-i="'+i+'"><i class="bi bi-trash"></i></button></td></tr>');
        });
        let total = keranjang.reduce(function(s,i){return s+i.jumlah;},0);
        $('#badgeTotal').text(total + ' item');
        hitungTotal();
    }

    $(document).on('click', '.btn-qty-minus', function () {
        let i = $(this).data('i');
        if (keranjang[i].jumlah > 1) { keranjang[i].jumlah--; hitungHargaItem(keranjang[i]); renderKeranjang(); }
    });
    $(document).on('click', '.btn-qty-plus', function () {
        let i = $(this).data('i');
        if (keranjang[i].jumlah < keranjang[i].stok) { keranjang[i].jumlah++; hitungHargaItem(keranjang[i]); renderKeranjang(); }
        else alert('Stok maksimal: ' + keranjang[i].stok);
    });
    $(document).on('change', '.input-qty', function () {
        let i = $(this).data('i'), v = Math.max(1, Math.min(parseInt($(this).val())||1, keranjang[i].stok));
        keranjang[i].jumlah = v; hitungHargaItem(keranjang[i]); renderKeranjang();
    });
    $(document).on('click', '.btn-hapus', function () { keranjang.splice($(this).data('i'),1); renderKeranjang(); });

    function getTotal() { return keranjang.reduce(function(s,i){return s+(i.harga*i.jumlah);},0); }

    function hitungTotal() {
        $('#totalBelanja').val(fmt(getTotal()));
        let bayar = parseInt($('#uangBayar').val())||0, kem = bayar - getTotal();
        if (bayar > 0 && kem < 0) {
            $('#kembalian').val('-' + fmt(Math.abs(kem))).removeClass('text-success').addClass('text-danger');
        } else {
            $('#kembalian').val(fmt(Math.max(0,kem))).removeClass('text-danger').addClass('text-success');
        }
        $('#btnSimpan').prop('disabled', !(keranjang.length > 0 && bayar >= getTotal() && getTotal() > 0));
    }

    $('#uangBayar').on('input', hitungTotal);

    $(document).on('click', '.btn-uang-pas', function () {
        let n = $(this).data('nominal');
        $('#uangBayar').val(n === 'uang-pas' ? getTotal() : parseInt(n));
        hitungTotal();
    });

    $('#btnSimpan').on('click', function () {
        if (keranjang.length === 0) { alert('Keranjang kosong!'); return; }
        let bayar = parseInt($('#uangBayar').val())||0;
        if (bayar < getTotal()) { alert('Uang bayar kurang!'); return; }

        let $btn = $(this).prop('disabled',true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
        $.ajax({
            url: 'api.php?action=simpan_transaksi', method: 'POST', contentType: 'application/json',
            data: JSON.stringify({ items: keranjang, bayar: bayar }),
            success: function (res) {
                if (res.success) {
                    window.open('struk.php?id=' + res.data.id, '_blank', 'width=420,height=600,scrollbars=yes');
                    $('#suksesTotal').text('Rp ' + fmt(res.data.total));
                    $('#suksesBayar').text('Rp ' + fmt(res.data.bayar));
                    $('#suksesKembalian').text('Rp ' + fmt(res.data.kembalian));
                    $('#modalSukses').modal('show');
                } else alert('Gagal: ' + res.message);
            },
            error: function () { alert('Kesalahan jaringan.'); },
            complete: function () { $btn.prop('disabled',false).html('<i class="bi bi-check-circle"></i> Simpan Transaksi'); }
        });
    });

    function resetKasir() {
        keranjang = []; renderKeranjang();
        $('#uangBayar').val(0); $('#kembalian').val('0').removeClass('text-danger').addClass('text-success');
        $('#cariBarang').val(null).trigger('change');
    }
    $('#btnReset').on('click', function () { if (keranjang.length === 0 || confirm('Bersihkan keranjang?')) resetKasir(); });
    $('#btnTransaksiBaru').on('click', function () { $('#modalSukses').modal('hide'); resetKasir(); });

    function escapeHtml(t) { let d=document.createElement('div'); d.appendChild(document.createTextNode(t)); return d.innerHTML; }
});
