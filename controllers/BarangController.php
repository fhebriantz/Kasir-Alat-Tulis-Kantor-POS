<?php

function getAllBarang(PDO $pdo): array
{
    return $pdo->query("
        SELECT b.*, k.nama as kategori_nama,
            (SELECT COUNT(*) FROM varian WHERE barang_id = b.id) as jumlah_varian
        FROM barang b
        LEFT JOIN kategori k ON k.id = b.kategori_id
        ORDER BY b.id DESC
    ")->fetchAll();
}

function getBarangById(PDO $pdo, int $id): array|false
{
    $stmt = $pdo->prepare("
        SELECT b.*, k.nama as kategori_nama
        FROM barang b LEFT JOIN kategori k ON k.id = b.kategori_id
        WHERE b.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getVarianByBarang(PDO $pdo, int $barangId): array
{
    $stmt = $pdo->prepare("SELECT * FROM varian WHERE barang_id = ? ORDER BY nama");
    $stmt->execute([$barangId]);
    return $stmt->fetchAll();
}

function getAllKategori(PDO $pdo): array
{
    return $pdo->query("SELECT * FROM kategori ORDER BY nama")->fetchAll();
}

function generateBarcode(PDO $pdo): string
{
    do {
        $code = 'LIA' . date('ymd') . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM barang WHERE barcode = ? UNION ALL SELECT COUNT(*) FROM varian WHERE barcode = ?");
        $stmt->execute([$code, $code]);
        $exists = false;
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $count) {
            if ($count > 0) { $exists = true; break; }
        }
    } while ($exists);
    return $code;
}

function createBarang(PDO $pdo, array $data): int
{
    $barcode = trim($data['barcode'] ?? '');
    if ($barcode === '') {
        $barcode = generateBarcode($pdo);
    }

    $stmt = $pdo->prepare("
        INSERT INTO barang (nama, kategori_id, harga_eceran, harga_grosir, min_grosir, stok, stok_minimum, satuan, barcode, has_varian)
        VALUES (:nama, :kat, :eceran, :grosir, :min_grosir, :stok, :stok_min, :satuan, :barcode, :has_varian)
    ");
    $stmt->execute([
        ':nama'       => trim($data['nama']),
        ':kat'        => ((int)($data['kategori_id'] ?? 0)) ?: null,
        ':eceran'     => (float)($data['harga_eceran'] ?? 0),
        ':grosir'     => (float)($data['harga_grosir'] ?? 0),
        ':min_grosir' => (int)($data['min_grosir'] ?? 12),
        ':stok'       => (int)($data['stok'] ?? 0),
        ':stok_min'   => (int)($data['stok_minimum'] ?? 5),
        ':satuan'     => trim($data['satuan'] ?? 'pcs'),
        ':barcode'    => $barcode,
        ':has_varian' => (int)($data['has_varian'] ?? 0),
    ]);
    return (int) $pdo->lastInsertId();
}

function updateBarang(PDO $pdo, int $id, array $data): bool
{
    $stmt = $pdo->prepare("
        UPDATE barang SET nama=:nama, kategori_id=:kat, harga_eceran=:eceran, harga_grosir=:grosir,
            min_grosir=:min_grosir, stok=:stok, stok_minimum=:stok_min, satuan=:satuan, barcode=:barcode,
            has_varian=:has_varian, updated_at=datetime('now','localtime')
        WHERE id=:id
    ");
    return $stmt->execute([
        ':id'         => $id,
        ':nama'       => trim($data['nama']),
        ':kat'        => ((int)($data['kategori_id'] ?? 0)) ?: null,
        ':eceran'     => (float)($data['harga_eceran'] ?? 0),
        ':grosir'     => (float)($data['harga_grosir'] ?? 0),
        ':min_grosir' => (int)($data['min_grosir'] ?? 12),
        ':stok'       => (int)($data['stok'] ?? 0),
        ':stok_min'   => (int)($data['stok_minimum'] ?? 5),
        ':satuan'     => trim($data['satuan'] ?? 'pcs'),
        ':barcode'    => trim($data['barcode'] ?? '') ?: null,
        ':has_varian' => (int)($data['has_varian'] ?? 0),
    ]);
}

function deleteBarang(PDO $pdo, int $id): bool
{
    $stmt = $pdo->prepare("DELETE FROM barang WHERE id = ?");
    return $stmt->execute([$id]);
}

// Varian CRUD
function createVarian(PDO $pdo, int $barangId, string $nama, int $stok, ?string $barcode): bool
{
    $bc = trim($barcode ?? '');
    if ($bc === '') {
        $bc = generateBarcode($pdo);
    }
    $stmt = $pdo->prepare("INSERT INTO varian (barang_id, nama, stok, barcode) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$barangId, trim($nama), $stok, $bc]);
}

function updateVarian(PDO $pdo, int $id, string $nama, int $stok, ?string $barcode): bool
{
    $stmt = $pdo->prepare("UPDATE varian SET nama=?, stok=?, barcode=? WHERE id=?");
    return $stmt->execute([trim($nama), $stok, trim($barcode) ?: null, $id]);
}

function deleteVarian(PDO $pdo, int $id): bool
{
    $stmt = $pdo->prepare("DELETE FROM varian WHERE id = ?");
    return $stmt->execute([$id]);
}

// Kategori
function createKategori(PDO $pdo, string $nama): bool
{
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO kategori (nama) VALUES (?)");
    return $stmt->execute([trim($nama)]);
}

function handleBarangAction(PDO $pdo): ?string
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return null;
    $action = $_POST['action'] ?? '';

    try {
        switch ($action) {
            case 'create':
                if (empty(trim($_POST['nama'] ?? ''))) return 'danger|Nama barang wajib diisi.';
                $id = createBarang($pdo, $_POST);
                // Simpan varian jika ada
                if (!empty($_POST['varian_nama']) && is_array($_POST['varian_nama'])) {
                    foreach ($_POST['varian_nama'] as $i => $vNama) {
                        if (trim($vNama) === '') continue;
                        createVarian($pdo, $id, $vNama, (int)($_POST['varian_stok'][$i] ?? 0), $_POST['varian_barcode'][$i] ?? '');
                    }
                    // Update total stok barang dari varian
                    updateStokFromVarian($pdo, $id);
                }
                return 'success|Barang berhasil ditambahkan.';

            case 'update':
                $id = (int)($_POST['id'] ?? 0);
                if ($id <= 0 || empty(trim($_POST['nama'] ?? ''))) return 'danger|Data tidak valid.';
                updateBarang($pdo, $id, $_POST);
                return 'success|Barang berhasil diperbarui.';

            case 'delete':
                $id = (int)($_POST['id'] ?? 0);
                if ($id <= 0) return 'danger|Data tidak valid.';
                deleteBarang($pdo, $id);
                return 'success|Barang berhasil dihapus.';

            case 'save_varian':
                $barangId = (int)($_POST['barang_id'] ?? 0);
                if ($barangId <= 0) return 'danger|Data tidak valid.';
                // Hapus varian lama, insert ulang
                $pdo->prepare("DELETE FROM varian WHERE barang_id = ?")->execute([$barangId]);
                if (!empty($_POST['varian_nama']) && is_array($_POST['varian_nama'])) {
                    foreach ($_POST['varian_nama'] as $i => $vNama) {
                        if (trim($vNama) === '') continue;
                        createVarian($pdo, $barangId, $vNama, (int)($_POST['varian_stok'][$i] ?? 0), $_POST['varian_barcode'][$i] ?? '');
                    }
                }
                updateStokFromVarian($pdo, $barangId);
                return 'success|Varian berhasil disimpan.';

            case 'tambah_kategori':
                $nama = trim($_POST['nama_kategori'] ?? '');
                if ($nama === '') return 'danger|Nama kategori wajib diisi.';
                createKategori($pdo, $nama);
                return 'success|Kategori berhasil ditambahkan.';

            default:
                return 'danger|Aksi tidak dikenali.';
        }
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'UNIQUE constraint failed')) {
            return 'danger|Barcode sudah digunakan oleh barang/varian lain.';
        }
        return 'danger|Terjadi kesalahan: ' . $e->getMessage();
    }
}

function updateStokFromVarian(PDO $pdo, int $barangId): void
{
    $stmt = $pdo->prepare("UPDATE barang SET stok = (SELECT COALESCE(SUM(stok),0) FROM varian WHERE barang_id = ?) WHERE id = ? AND has_varian = 1");
    $stmt->execute([$barangId, $barangId]);
}
