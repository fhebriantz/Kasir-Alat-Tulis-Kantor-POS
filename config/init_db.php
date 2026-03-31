<?php

function initDatabase(PDO $pdo): void
{
    // Kategori barang
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS kategori (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nama TEXT NOT NULL UNIQUE
        )
    ");

    // Barang utama
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS barang (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nama TEXT NOT NULL,
            kategori_id INTEGER,
            harga_eceran REAL NOT NULL DEFAULT 0,
            harga_grosir REAL NOT NULL DEFAULT 0,
            min_grosir INTEGER NOT NULL DEFAULT 12,
            stok INTEGER NOT NULL DEFAULT 0,
            stok_minimum INTEGER NOT NULL DEFAULT 5,
            satuan TEXT NOT NULL DEFAULT 'pcs',
            barcode TEXT UNIQUE,
            has_varian INTEGER NOT NULL DEFAULT 0,
            created_at TEXT DEFAULT (datetime('now','localtime')),
            updated_at TEXT DEFAULT (datetime('now','localtime')),
            FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL
        )
    ");

    // Varian barang (warna, ukuran, dll)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS varian (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            barang_id INTEGER NOT NULL,
            nama TEXT NOT NULL,
            stok INTEGER NOT NULL DEFAULT 0,
            barcode TEXT UNIQUE,
            FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE
        )
    ");

    // Transaksi
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS transaksi (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            tanggal TEXT DEFAULT (datetime('now','localtime')),
            total_harga REAL NOT NULL DEFAULT 0,
            bayar REAL NOT NULL DEFAULT 0,
            kembalian REAL NOT NULL DEFAULT 0
        )
    ");

    // Detail transaksi
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS detail_transaksi (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            transaksi_id INTEGER NOT NULL,
            barang_id INTEGER NOT NULL,
            varian_id INTEGER,
            nama_barang TEXT NOT NULL,
            nama_varian TEXT,
            harga REAL NOT NULL,
            jumlah INTEGER NOT NULL DEFAULT 1,
            tipe_harga TEXT DEFAULT 'eceran',
            subtotal REAL NOT NULL DEFAULT 0,
            FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
            FOREIGN KEY (barang_id) REFERENCES barang(id)
        )
    ");

    // Pengaturan
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS pengaturan (
            kunci TEXT PRIMARY KEY,
            nilai TEXT NOT NULL DEFAULT ''
        )
    ");

    // Default settings
    $defaults = [
        'nama_toko'    => "Lia's Laci",
        'alamat_toko'  => 'Jl. Contoh Alamat No. 123',
        'telepon_toko' => '08xx-xxxx-xxxx',
        'footer_struk' => 'Barang yang sudah dibeli tidak dapat ditukar/dikembalikan',
        'warna_header' => '#6f42c1',
        'logo_toko'    => '',
    ];
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO pengaturan (kunci, nilai) VALUES (:k, :v)");
    foreach ($defaults as $k => $v) {
        $stmt->execute([':k' => $k, ':v' => $v]);
    }

    // Default kategori ATK
    $kategoris = ['Alat Tulis', 'Kertas', 'Buku', 'Perlengkapan Kantor', 'Aksesoris', 'Lain-lain'];
    $stmtKat = $pdo->prepare("INSERT OR IGNORE INTO kategori (nama) VALUES (:nama)");
    foreach ($kategoris as $k) {
        $stmtKat->execute([':nama' => $k]);
    }

    // Index
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_barang_barcode ON barang(barcode)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_varian_barcode ON varian(barcode)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_varian_barang ON varian(barang_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_detail_transaksi ON detail_transaksi(transaksi_id)");
}
