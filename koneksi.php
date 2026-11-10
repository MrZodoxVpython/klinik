<?php
$configFile = __DIR__ . '/config.local.php';

if (!is_file($configFile)) {
    http_response_code(500);
    exit('Konfigurasi database belum tersedia. Salin config.example.php menjadi config.local.php.');
}

$databaseConfig = require $configFile;
if (!is_array($databaseConfig) || !isset($databaseConfig['host'], $databaseConfig['user'], $databaseConfig['password'], $databaseConfig['database'])) {
    http_response_code(500);
    exit('Konfigurasi database tidak valid.');
}

$conn = mysqli_connect(
    $databaseConfig['host'],
    $databaseConfig['user'],
    $databaseConfig['password'],
    $databaseConfig['database']
);

if (!$conn) {
    error_log('Koneksi database Posyandu gagal: ' . mysqli_connect_error());
    http_response_code(500);
    exit('Layanan sedang tidak tersedia. Silakan coba beberapa saat lagi.');
}

mysqli_set_charset($conn, 'utf8mb4');
