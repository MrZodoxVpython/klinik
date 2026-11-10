<?php
require_once 'auth.php';
require_once 'koneksi.php';
require_role('admin');
send_security_headers();
$nama_admin = $_SESSION['nama'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin | Posyandu Bina Cita</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .navbar-blur { backdrop-filter: blur(12px); background-color: rgba(255, 255, 255, 0.7); }
        .menu-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .menu-card:hover { transform: translateY(-5px) scale(1.02); box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1); }
        .swiper-pagination-bullet { background-color: rgba(255, 255, 255, 0.6); }
        .swiper-pagination-bullet-active { background-color: #10B981; }
        .swiper-button-next, .swiper-button-prev { color: #10B981 !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100 text-gray-800">
<header class="fixed top-0 left-0 right-0 z-50 navbar-blur shadow-lg">
    <div class="flex flex-col sm:flex-row justify-between items-center px-4 sm:px-8 py-3">
        <div class="flex items-center gap-3 mb-3 sm:mb-0">
            <img src="https://i.imgur.com/9rjiL5N.jpg" alt="Logo Posyandu" class="w-12 h-12 rounded-full object-cover border-2 border-green-600 shadow-md" />
            <span class="text-xl font-bold text-green-800">Dashboard Kader Posyandu</span>
        </div>
        <div class="text-sm sm:text-base text-green-800 font-semibold flex items-center">
            <i class="fas fa-hand-sparkles text-yellow-500 mr-2"></i> Selamat datang,
            <span class="font-bold ml-1 mr-4"><?= htmlspecialchars($nama_admin) ?></span>
            <form method="POST" action="logout.php"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>"><button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-600 transition duration-300 ease-in-out transform hover:scale-105 flex items-center text-sm"><i class="fas fa-sign-out-alt mr-1"></i> Logout</button></form>
        </div>
    </div>
</header>
<section class="pt-20">
    <div class="swiper-container h-[400px] w-full rounded-b-lg shadow-xl overflow-hidden">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="https://i.imgur.com/tLmVvvy.jpg" class="w-full h-full object-cover" alt="Anak-anak Sehat 1"><div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center"><h2 class="text-white text-3xl sm:text-5xl font-extrabold text-center drop-shadow-lg p-4">Mewujudkan Generasi Sehat & Cerdas</h2></div></div>
            <div class="swiper-slide"><img src="https://imgur.com/96UKEBT.jpg" class="w-full h-full object-cover" alt="Imunisasi"><div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center"><h2 class="text-white text-3xl sm:text-5xl font-extrabold text-center drop-shadow-lg p-4">Imunisasi Lengkap untuk Perlindungan Optimal</h2></div></div>
            <div class="swiper-slide"><img src="https://imgur.com/V9famHy.jpg" class="w-full h-full object-cover" alt="Tumbuh Kembang"><div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center"><h2 class="text-white text-3xl sm:text-5xl font-extrabold text-center drop-shadow-lg p-4">Pantau Tumbuh Kembang Buah Hati Anda</h2></div></div>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</section>
<section class="text-center my-10 md:my-16 px-4">
    <h2 class="text-3xl sm:text-4xl font-extrabold text-green-700 mb-4">Selamat Datang di Dashboard Admin Posyandu Bina Cita</h2>
    <p class="text-lg text-gray-600 max-w-2xl mx-auto">Sebagai kader, Anda memiliki peran penting dalam mengelola data dan memastikan pelayanan kesehatan anak berjalan optimal.</p>
</section>
<section class="grid grid-cols-2 lg:grid-cols-4 gap-6 px-6 mb-16 max-w-7xl mx-auto">
    <a href="data_anak_admin.php" class="menu-card bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl text-center text-green-700 font-semibold flex flex-col items-center justify-center"><i class="fas fa-child text-5xl mb-3 text-blue-500"></i><span class="text-lg">Kelola Data Anak</span></a>
    <a href="imunisasi_admin.php" class="menu-card bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl text-center text-green-700 font-semibold flex flex-col items-center justify-center"><i class="fas fa-syringe text-5xl mb-3 text-red-500"></i><span class="text-lg">Data Imunisasi</span></a>
    <a href="jadwal_admin.php" class="menu-card bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl text-center text-green-700 font-semibold flex flex-col items-center justify-center"><i class="fas fa-calendar-alt text-5xl mb-3 text-purple-500"></i><span class="text-lg">Jadwal Posyandu</span></a>
    <a href="petugas_admin.php" class="menu-card bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl text-center text-green-700 font-semibold flex flex-col items-center justify-center"><i class="fas fa-user-nurse text-5xl mb-3 text-teal-500"></i><span class="text-lg">Kelola Petugas</span></a>
    <a href="laporan.php" class="menu-card bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl text-center text-green-700 font-semibold flex flex-col items-center justify-center"><i class="fas fa-chart-line text-5xl mb-3 text-indigo-500"></i><span class="text-lg">Lihat Laporan</span></a>
</section>
<footer class="bg-white border-t border-green-200 py-8"><div class="text-center text-sm text-gray-600">&copy; <?= date('Y') ?> Posyandu Bina Cita. Semua Hak Dilindungi.</div></footer>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new Swiper('.swiper-container', { loop: true, autoplay: { delay: 5000, disableOnInteraction: false }, pagination: { el: '.swiper-pagination', clickable: true }, navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' }, speed: 800, effect: 'fade', fadeEffect: { crossFade: true } });
    });
</script>
</body>
</html>
