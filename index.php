<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$nama = $is_logged_in ? $_SESSION['nama'] : null;
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Beranda - E-Posyandu Bina Cita</title>
  <link rel="SHORTCUT ICON" href="https://storage.pusdokkes.polri.go.id/pusdokkes/logo.png">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    html, body { margin: 0; padding: 0; overflow-x: hidden; background-color: #f0fdf4; }
    .navbar-blur { backdrop-filter: blur(12px); background-color: rgba(255, 255, 255, 0.75); }
    .slide-wrapper { position: relative; width: 100%; height: auto; aspect-ratio: 1 / 1; }
    @media (min-width: 768px) { .slide-wrapper { aspect-ratio: unset; height: calc(100vh - 64px); } }
    .slide-image { width: 100%; height: 100%; object-fit: cover; object-position: top; position: absolute; top: 0; left: 0; }
    .slide-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; text-align: center; color: white; }
    .menu-card { transition: all 0.3s ease; }
    .menu-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    .swiper-pagination-bullet { background: #ccc; opacity: 1; }
    .swiper-pagination-bullet-active { background: #10b981; }
  </style>
</head>
<body class="text-gray-800">
<header class="fixed top-0 left-0 right-0 z-50 navbar-blur shadow-md h-16">
  <div class="flex justify-between items-center h-full px-4 sm:px-8">
    <div class="flex items-center gap-3">
      <img src="https://1.bp.blogspot.com/-2b8VcGpaYYk/Uk3-rt6R8rI/AAAAAAAACSE/HkFPCtCWoL4/s400-rj-v1-c0Xffffff-e30/K3.png" alt="Logo" class="w-10 h-10 rounded-full border-2 border-green-600 shadow-sm" />
      <span class="text-lg font-bold text-green-800">Posyandu Bina Cita</span>
    </div>
    <?php if ($is_logged_in && $role === 'admin'): ?>
      <a href="admin.php" class="bg-green-600 text-white px-4 py-2 rounded-full hover:bg-green-700 transition-all text-sm"><i class="fas fa-user-shield mr-2"></i> Dashboard Admin</a>
    <?php elseif ($is_logged_in): ?>
      <a href="logout.php" class="bg-green-600 text-white px-4 py-2 rounded-full hover:bg-green-700 transition-all text-sm"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
    <?php else: ?>
      <a href="login.php" class="bg-green-600 text-white px-4 py-2 rounded-full hover:bg-green-700 transition-all text-sm"><i class="fas fa-sign-in-alt mr-2"></i> Login</a>
    <?php endif; ?>
  </div>
</header>
<div class="mt-16">
  <div class="swiper swiper-container">
    <div class="swiper-wrapper">
      <div class="swiper-slide"><div class="slide-wrapper"><img src="assets/slide1.jpg" alt="Slide 1" class="slide-image"><div class="slide-overlay text-xl sm:text-3xl font-bold">Mewujudkan Generasi Sehat & Cerdas</div></div></div>
      <div class="swiper-slide"><div class="slide-wrapper"><img src="assets/slide2.jpg" alt="Slide 2" class="slide-image"><div class="slide-overlay text-xl sm:text-3xl font-bold">Imunisasi Lengkap untuk Perlindungan Optimal</div></div></div>
      <div class="swiper-slide"><div class="slide-wrapper"><img src="assets/slide3.jpg" alt="Slide 3" class="slide-image"><div class="slide-overlay text-xl sm:text-3xl font-bold">Pantau Tumbuh Kembang Buah Hati Anda</div></div></div>
    </div>
    <div class="swiper-pagination"></div>
  </div>
</div>
<section class="text-center my-16 px-4">
  <h2 class="text-3xl sm:text-5xl font-extrabold text-green-800 mb-4">Selamat Datang di Sistem Informasi Posyandu</h2>
  <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">Platform terpadu untuk memudahkan orang tua balita dalam mengakses informasi dan layanan Posyandu Bina Cita.</p>
</section>
<br>
<section class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 px-4 max-w-7xl mx-auto mb-10 sm:mb-20">
  <a href="<?= $is_logged_in ? 'data_anak_pengguna.php' : 'login.php?redirect=data_anak_pengguna.php' ?>" class="menu-card bg-white p-6 sm:p-10 rounded-2xl shadow-xl text-center text-green-700 font-semibold text-base sm:text-xl border border-green-200"><i class="fas fa-child text-3xl sm:text-4xl mb-2 sm:mb-4 text-green-600"></i><br>Data Anak & Orang Tua</a>
  <a href="<?= $is_logged_in ? 'imunisasi_pengguna.php' : 'login.php?redirect=imunisasi_pengguna.php' ?>" class="menu-card bg-white p-6 sm:p-10 rounded-2xl shadow-xl text-center text-green-700 font-semibold text-base sm:text-xl border border-green-200"><i class="fas fa-syringe text-3xl sm:text-4xl mb-2 sm:mb-4 text-green-600"></i><br>Imunisasi</a>
  <a href="<?= $is_logged_in ? 'jadwal_pengguna.php' : 'login.php?redirect=jadwal_pengguna.php' ?>" class="menu-card bg-white p-6 sm:p-10 rounded-2xl shadow-xl text-center text-green-700 font-semibold text-base sm:text-xl border border-green-200"><i class="fas fa-calendar-alt text-3xl sm:text-4xl mb-2 sm:mb-4 text-green-600"></i><br>Jadwal Posyandu</a>
  <a href="<?= $is_logged_in ? 'edukasi_ibu.php' : 'login.php?redirect=edukasi_ibu.php' ?>" class="menu-card bg-white p-6 sm:p-10 rounded-2xl shadow-xl text-center text-green-700 font-semibold text-base sm:text-xl border border-green-200"><i class="fas fa-book-reader text-3xl sm:text-4xl mb-2 sm:mb-4 text-green-600"></i><br>Edukasi Ibu</a>
</section>
<footer class="mt-16 text-center text-sm text-gray-600 py-8 border-t border-green-200 bg-white">&copy; 2025 Posyandu Bina Cita. Semua hak dilindungi.</footer>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
  const swiper = new Swiper('.swiper-container', { loop: true, autoplay: { delay: 5000, disableOnInteraction: false }, pagination: { el: '.swiper-pagination', clickable: true } });
</script>
</body>
</html>
