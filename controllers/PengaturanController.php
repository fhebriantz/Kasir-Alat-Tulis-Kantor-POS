<?php

require_once __DIR__ . '/../config/helpers.php';

function handlePengaturanAction(PDO $pdo): ?string
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return null;
    $action = $_POST['action'] ?? '';

    try {
        switch ($action) {
            case 'simpan_umum':
                $fields = ['nama_toko', 'alamat_toko', 'telepon_toko', 'footer_struk', 'warna_header'];
                $stmt = $pdo->prepare("INSERT OR REPLACE INTO pengaturan (kunci, nilai) VALUES (:k, :v)");
                foreach ($fields as $f) {
                    $stmt->execute([':k' => $f, ':v' => trim($_POST[$f] ?? '')]);
                }
                return 'success|Pengaturan berhasil disimpan.';

            case 'upload_logo':
                if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) return 'danger|Gagal mengupload file.';
                $file = $_FILES['logo'];
                if (!in_array($file['type'], ['image/png','image/jpeg','image/gif','image/webp'])) return 'danger|Format tidak didukung.';
                if ($file['size'] > 2 * 1024 * 1024) return 'danger|Maks 2MB.';

                $uploadDir = __DIR__ . '/../public/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                $logoLama = getPengaturan($pdo, 'logo_toko');
                if ($logoLama && file_exists($uploadDir . $logoLama)) unlink($uploadDir . $logoLama);

                $filename = 'logo_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
                move_uploaded_file($file['tmp_name'], $uploadDir . $filename);

                $pdo->prepare("INSERT OR REPLACE INTO pengaturan (kunci, nilai) VALUES ('logo_toko', ?)")->execute([$filename]);
                return 'success|Logo berhasil diupload.';

            case 'hapus_logo':
                $logoLama = getPengaturan($pdo, 'logo_toko');
                $uploadDir = __DIR__ . '/../public/uploads/';
                if ($logoLama && file_exists($uploadDir . $logoLama)) unlink($uploadDir . $logoLama);
                $pdo->prepare("INSERT OR REPLACE INTO pengaturan (kunci, nilai) VALUES ('logo_toko', '')")->execute();
                return 'success|Logo berhasil dihapus.';

            default: return 'danger|Aksi tidak dikenali.';
        }
    } catch (Exception $e) {
        return 'danger|Error: ' . $e->getMessage();
    }
}
