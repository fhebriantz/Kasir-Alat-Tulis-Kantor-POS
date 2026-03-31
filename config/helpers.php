<?php

function getSemuaPengaturan(PDO $pdo): array
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS pengaturan (kunci TEXT PRIMARY KEY, nilai TEXT NOT NULL DEFAULT '')");

    $defaults = [
        'nama_toko'    => "Lia's Laci",
        'alamat_toko'  => 'Jl. Contoh Alamat No. 123',
        'telepon_toko' => '08xx-xxxx-xxxx',
        'footer_struk' => 'Barang yang sudah dibeli tidak dapat ditukar/dikembalikan',
        'warna_header' => '#6f42c1',
        'logo_toko'    => '',
    ];

    $stmt = $pdo->query("SELECT kunci, nilai FROM pengaturan");
    $settings = $defaults;
    foreach ($stmt->fetchAll() as $row) {
        $settings[$row['kunci']] = $row['nilai'];
    }
    return $settings;
}

function getPengaturan(PDO $pdo, string $kunci, string $default = ''): string
{
    $all = getSemuaPengaturan($pdo);
    return $all[$kunci] ?? $default;
}

function formatRp(float $angka): string
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
