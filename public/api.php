<?php

require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

$pdo = getConnection();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'search_barang':
        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 1) { echo json_encode([]); break; }

        // Cari di barang tanpa varian + di varian
        $stmt = $pdo->prepare("
            SELECT b.id, b.nama, b.harga_eceran, b.harga_grosir, b.min_grosir, b.stok,
                   b.satuan, b.barcode, b.has_varian, NULL as varian_id, NULL as varian_nama
            FROM barang b
            WHERE b.has_varian = 0 AND (b.nama LIKE :q OR b.barcode LIKE :q)
            UNION ALL
            SELECT b.id, b.nama, b.harga_eceran, b.harga_grosir, b.min_grosir, v.stok,
                   b.satuan, v.barcode, b.has_varian, v.id as varian_id, v.nama as varian_nama
            FROM varian v
            JOIN barang b ON b.id = v.barang_id
            WHERE v.nama LIKE :q OR v.barcode LIKE :q OR b.nama LIKE :q
            ORDER BY nama ASC
            LIMIT 30
        ");
        $stmt->execute([':q' => "%{$q}%"]);
        $results = [];
        foreach ($stmt->fetchAll() as $r) {
            $label = $r['nama'];
            if ($r['varian_nama']) $label .= ' — ' . $r['varian_nama'];
            $results[] = [
                'id'           => $r['id'],
                'text'         => $label . ' | ' . formatRp($r['harga_eceran']) . ' (stok: ' . $r['stok'] . ')',
                'nama'         => $r['nama'],
                'varian_id'    => $r['varian_id'],
                'varian_nama'  => $r['varian_nama'],
                'harga_eceran' => $r['harga_eceran'],
                'harga_grosir' => $r['harga_grosir'],
                'min_grosir'   => $r['min_grosir'],
                'stok'         => $r['stok'],
                'satuan'       => $r['satuan'],
                'barcode'      => $r['barcode'],
            ];
        }
        echo json_encode(['results' => $results]);
        break;

    case 'simpan_transaksi':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $items = $input['items'] ?? [];
        $bayar = (float)($input['bayar'] ?? 0);

        if (empty($items)) { echo json_encode(['success' => false, 'message' => 'Keranjang kosong.']); break; }

        $totalHarga = 0;
        foreach ($items as $item) {
            $totalHarga += (float)$item['harga'] * (int)$item['jumlah'];
        }

        if ($bayar < $totalHarga) { echo json_encode(['success' => false, 'message' => 'Uang bayar kurang.']); break; }

        $kembalian = $bayar - $totalHarga;

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO transaksi (total_harga, bayar, kembalian) VALUES (?, ?, ?)");
            $stmt->execute([$totalHarga, $bayar, $kembalian]);
            $transaksiId = $pdo->lastInsertId();

            $stmtDetail = $pdo->prepare("INSERT INTO detail_transaksi (transaksi_id, barang_id, varian_id, nama_barang, nama_varian, harga, jumlah, tipe_harga, subtotal) VALUES (?,?,?,?,?,?,?,?,?)");

            foreach ($items as $item) {
                $jumlah = (int)$item['jumlah'];
                $harga = (float)$item['harga'];
                $subtotal = $harga * $jumlah;
                $varianId = $item['varian_id'] ?? null;

                $stmtDetail->execute([
                    $transaksiId, $item['id'], $varianId ?: null,
                    $item['nama'], $item['varian_nama'] ?? null,
                    $harga, $jumlah, $item['tipe_harga'] ?? 'eceran', $subtotal
                ]);

                // Kurangi stok
                if ($varianId) {
                    $stmtStok = $pdo->prepare("UPDATE varian SET stok = stok - ? WHERE id = ? AND stok >= ?");
                    $stmtStok->execute([$jumlah, $varianId, $jumlah]);
                    if ($stmtStok->rowCount() === 0) {
                        $pdo->rollBack();
                        echo json_encode(['success' => false, 'message' => "Stok varian \"{$item['nama']}\" tidak cukup."]);
                        exit;
                    }
                    // Sync total stok barang
                    $pdo->prepare("UPDATE barang SET stok = (SELECT COALESCE(SUM(stok),0) FROM varian WHERE barang_id = ?) WHERE id = ?")->execute([$item['id'], $item['id']]);
                } else {
                    $stmtStok = $pdo->prepare("UPDATE barang SET stok = stok - ?, updated_at = datetime('now','localtime') WHERE id = ? AND stok >= ?");
                    $stmtStok->execute([$jumlah, $item['id'], $jumlah]);
                    if ($stmtStok->rowCount() === 0) {
                        $pdo->rollBack();
                        echo json_encode(['success' => false, 'message' => "Stok \"{$item['nama']}\" tidak cukup."]);
                        exit;
                    }
                }
            }

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Transaksi berhasil.', 'data' => [
                'id' => $transaksiId, 'total' => $totalHarga, 'bayar' => $bayar, 'kembalian' => $kembalian
            ]]);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()]);
        }
        break;

    case 'reset_transaksi':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false]); break; }
        $pdo->exec("DELETE FROM detail_transaksi");
        $pdo->exec("DELETE FROM transaksi");
        $pdo->exec("DELETE FROM sqlite_sequence WHERE name IN ('transaksi','detail_transaksi')");
        echo json_encode(['success' => true, 'message' => 'Semua transaksi berhasil dihapus.']);
        break;

    case 'reset_all':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false]); break; }
        $pdo->exec("DELETE FROM detail_transaksi"); $pdo->exec("DELETE FROM transaksi");
        $pdo->exec("DELETE FROM varian"); $pdo->exec("DELETE FROM barang");
        $pdo->exec("DELETE FROM kategori"); $pdo->exec("DELETE FROM pengaturan");
        $pdo->exec("DELETE FROM sqlite_sequence");
        require_once __DIR__ . '/../config/init_db.php';
        initDatabase($pdo);
        echo json_encode(['success' => true, 'message' => 'Database berhasil direset.']);
        break;

    case 'check_barcode':
        $bc = trim($_GET['barcode'] ?? '');
        $excludeId = (int)($_GET['exclude_id'] ?? 0);
        if ($bc === '') { echo json_encode(['exists' => false]); break; }

        // Cek di tabel barang
        if ($excludeId > 0) {
            $stmt = $pdo->prepare("SELECT id, nama FROM barang WHERE barcode = ? AND id != ?");
            $stmt->execute([$bc, $excludeId]);
        } else {
            $stmt = $pdo->prepare("SELECT id, nama FROM barang WHERE barcode = ?");
            $stmt->execute([$bc]);
        }
        $found = $stmt->fetch();
        if ($found) {
            echo json_encode(['exists' => true, 'message' => 'Barcode sudah dipakai oleh: ' . $found['nama']]);
            break;
        }

        // Cek di tabel varian
        $stmt = $pdo->prepare("SELECT v.id, b.nama, v.nama as varian FROM varian v JOIN barang b ON b.id = v.barang_id WHERE v.barcode = ?");
        $stmt->execute([$bc]);
        $found = $stmt->fetch();
        if ($found) {
            echo json_encode(['exists' => true, 'message' => 'Barcode sudah dipakai oleh varian: ' . $found['nama'] . ' — ' . $found['varian']]);
            break;
        }

        echo json_encode(['exists' => false]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Aksi tidak dikenali.']);
}

function formatRp(float $n): string { return 'Rp ' . number_format($n, 0, ',', '.'); }
