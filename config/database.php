<?php

define('DB_PATH', __DIR__ . '/../database/kasir_atk.db');

function getConnection(): PDO
{
    $isNew = !file_exists(DB_PATH);

    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA journal_mode = WAL');
    $pdo->exec('PRAGMA foreign_keys = ON');

    if ($isNew) {
        require_once __DIR__ . '/init_db.php';
        initDatabase($pdo);
    }

    return $pdo;
}
