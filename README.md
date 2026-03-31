# Lia's Laci — Kasir Toko ATK

Aplikasi Point of Sale (POS) untuk toko alat tulis kantor (ATK). Berbasis web lokal offline, bisa dijalankan sebagai aplikasi desktop via PHP Desktop.

## Tech Stack

- PHP Native (tanpa framework)
- SQLite (database file-based)
- Bootstrap 5 + Bootstrap Icons
- jQuery + Select2
- JsBarcode (generate & cetak barcode)

## Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| Harga Bertingkat | Eceran & grosir otomatis switch berdasarkan qty |
| Manajemen Varian | Warna/ukuran per barang, stok & barcode per varian |
| Barcode Auto-Generate | Otomatis jika kosong (prefix LIA), cek duplikat realtime |
| Cetak Barcode | Pilih barang → preview → cetak label barcode |
| Kategori | Alat Tulis, Kertas, Buku, dll + tambah cepat |
| Stok Minimum Custom | Batas minimum per barang, peringatan di dashboard |
| Kasir (POS) | Select2 AJAX, harga otomatis, keranjang, hitung kembalian |
| Cetak Struk | Thermal 58mm, data toko dinamis, auto-print |
| Riwayat | Filter tanggal, detail item + varian + tipe harga |
| Laporan Bulanan | Omzet harian, barang terlaris |
| Pengaturan | Nama toko, logo, warna, footer struk |
| Bantuan | Panduan in-app, reset database |

## Struktur Direktori

```
kasir-atk/
├── config/                  # Konfigurasi
│   ├── database.php         # Koneksi PDO SQLite
│   ├── helpers.php          # Helper functions
│   └── init_db.php          # Auto-create tabel + default
├── controllers/             # Logic CRUD
├── views/                   # Tampilan (PHP + HTML)
│   ├── layouts/             # Header & footer
│   ├── dashboard/           # Statistik & peringatan stok
│   ├── kasir/               # Halaman POS
│   ├── barang/              # CRUD barang + varian
│   ├── barcode/             # Generate & cetak barcode
│   ├── transaksi/           # Riwayat transaksi
│   ├── laporan/             # Laporan bulanan
│   ├── pengaturan/          # Settings toko
│   └── bantuan/             # Halaman bantuan
├── public/                  # Document root
│   ├── index.php            # Router
│   ├── api.php              # AJAX endpoint
│   ├── struk.php            # Cetak struk thermal
│   ├── css/style.css
│   ├── js/app.js
│   ├── js/kasir.js
│   ├── js/barcode.js
│   └── uploads/             # Logo toko
├── database/                # SQLite (auto-generated)
├── build.sh                 # Build script
├── phpdesktop-settings.json
├── LICENSE
└── .gitignore
```

## Cara Menjalankan (Development)

### Prasyarat

```bash
sudo apt install php-cli php-sqlite3
```

### Jalankan

```bash
cd public
php -S localhost:8000
```

Buka http://localhost:8000 — database otomatis ter-generate.

## Build ke PHP Desktop (.exe)

### 1. Download PHP Desktop

Download dari https://github.com/cztomczak/phpdesktop/releases — pilih versi Chrome.

### 2. Ekstrak ke project

```bash
# Ekstrak isi ZIP ke folder phpdesktop/
# Pastikan phpdesktop/phpdesktop-chrome.exe ada
```

### 3. Cek SQLite extension

Buka `phpdesktop/php/php.ini`, pastikan aktif (tanpa `;`):

```ini
extension=php_pdo_sqlite.dll
extension=php_sqlite3.dll
```

### 4. Build

```bash
chmod +x build.sh
./build.sh
```

### 5. Distribusi

```bash
cd dist
zip -r LiasLaci.zip LiasLaci/
```

Kirim ZIP ke client → extract → double-click `phpdesktop-chrome.exe`.

## Reset Database

### Reset total

Hapus file `database/kasir_atk.db` → buka aplikasi → database baru otomatis dibuat.

### Reset via aplikasi

Menu **Bantuan** → scroll ke Reset Database:
- **Reset Transaksi** — hapus transaksi saja
- **Reset Semua** — hapus semua data, kembali ke default

## Git Workflow

```bash
git add -A
git commit -m "deskripsi perubahan"
git push origin main

# Tag release
git tag -a v1.0.0 -m "Release versi 1.0.0"
git push origin main --tags
```

## Lisensi

Lia's Laci © 2026 Lutfi Febrianto. Nama aplikasi tidak boleh diubah.
