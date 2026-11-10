<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pengguna') {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$nama_pengguna = $_SESSION['nama'];
$anak_result = mysqli_query($conn, "SELECT nama_anak FROM data_anak WHERE user_id = '$user_id' ORDER BY nama_anak ASC");
$anak_nama = [];
while ($row = mysqli_fetch_assoc($anak_result)) { $anak_nama[] = $row['nama_anak']; }
$jadwal_result = mysqli_query($conn, "SELECT * FROM jadwal_posyandu ORDER BY tanggal DESC, waktu DESC LIMIT 1");
$jadwal = mysqli_fetch_assoc($jadwal_result);
function formatTanggal($tanggal) {
    $hari = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
    $bulan = ['January'=>'Januari','February'=>'Februari','March'=>'Maret','April'=>'April','May'=>'Mei','June'=>'Juni','July'=>'Juli','August'=>'Agustus','September'=>'September','October'=>'Oktober','November'=>'November','December'=>'Desember'];
    $timestamp = strtotime($tanggal);
    $hariIndo = $hari[date('l', $timestamp)];
    $bulanIndo = $bulan[date('F', $timestamp)];
    return "$hariIndo, " . date('d', $timestamp) . " $bulanIndo " . date('Y', $timestamp);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Posyandu - Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100 min-h-screen flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-8 sm:p-10 rounded-xl shadow-2xl w-full max-w-lg border border-green-200 text-center">
        <div class="flex justify-center mb-6">
            <svg class="h-20 w-20 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
        </div>
        <h2 class="text-3xl font-extrabold text-green-800 mb-6">Jadwal Posyandu Anda</h2>
        <?php if ($jadwal && count($anak_nama) > 0): ?>
        <div class="text-lg leading-relaxed text-gray-700 space-y-4">
            <p>Halo <strong class="font-extrabold text-green-700 text-xl"><?= htmlspecialchars($nama_pengguna) ?></strong>,<br>Kami punya kabar penting untuk Anda!</p>
            <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-inner">
                <p class="font-bold text-xl mb-2">🗓️ Jadwal Posyandu Berikutnya:</p>
                <p class="text-2xl font-bold"><?= formatTanggal($jadwal['tanggal']) ?></p>
                <p class="text-xl mt-1">Pukul <span class="font-bold"><?= substr($jadwal['waktu'], 0, 5) ?> WIB</span> - selesai</p>
                <p class="mt-3">Di <strong><span class="font-bold text-green-700"><?= htmlspecialchars($jadwal['tempat']) ?></span></strong></p>
            </div>
            <p class="mt-4">Jangan lupa ajak anak Anda: <br><span class="font-extrabold text-green-700 text-xl block mt-2"><?= implode(', ', $anak_nama) ?></span></p>
            <p class="text-green-700 font-semibold italic mt-4">"Kesehatan anak adalah prioritas utama. Mari bersama menjaga tumbuh kembang mereka!"</p>
        </div>
        <?php elseif (count($anak_nama) == 0): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md relative text-left" role="alert">
            <strong class="font-bold mr-1">Perhatian!</strong><span class="block sm:inline">Anda belum memiliki data anak terdaftar. Silakan <a href="tambah_data_anak_pengguna.php" class="text-red-800 hover:underline font-semibold">tambahkan data anak Anda</a> untuk mendapatkan informasi jadwal.</span>
        </div>
        <?php else: ?>
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-md relative text-left" role="alert">
            <strong class="font-bold mr-1">Informasi:</strong><span class="block sm:inline">Belum ada jadwal Posyandu terbaru yang ditentukan oleh admin. Mohon bersabar dan cek kembali nanti.</span>
        </div>
        <?php endif; ?>
        <a href="index.php" class="inline-block mt-8 bg-green-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition duration-300 ease-in-out transform hover:scale-105 flex items-center justify-center mx-auto max-w-max"><i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda</a>
    </div>
</body>
</html>
