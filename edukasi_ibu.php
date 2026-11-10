<?php
require_once 'auth.php';
require_login();
send_security_headers();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Halaman Edukasi Ibu - Posyandu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0fdf4; color: #374151; }
        header { background-color: #047857; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        header .container { display: flex; justify-content: space-between; align-items: center; }
        header h1 { font-size: 1.75rem; font-weight: 700; letter-spacing: -0.025em; }
        @media (min-width: 768px) { header h1 { font-size: 2.5rem; } }
        header p { font-size: 0.875rem; opacity: 0.9; }
        @media (min-width: 768px) { header p { font-size: 1rem; } }
        .back-home-btn { background-color: #059669; color: white; padding: 0.6rem 1.2rem; border-radius: 9999px; font-weight: 600; text-decoration: none; transition: background-color 0.3s ease, transform 0.2s ease; font-size: 0.875rem; }
        @media (min-width: 640px) { .back-home-btn { padding: 0.75rem 1.5rem; font-size: 1rem; } }
        .back-home-btn:hover { background-color: #047857; transform: translateY(-2px); }
        main { padding-top: 2rem; padding-bottom: 2rem; }
        @media (min-width: 640px) { main { padding-top: 3rem; padding-bottom: 3rem; } }
        .article-card { display: flex; flex-direction: column; height: 100%; transition: transform 0.3s ease, box-shadow 0.3s ease; border-radius: 0.75rem; overflow: hidden; background-color: #ffffff; box-shadow: 0 5px 10px rgba(0,0,0,0.1); }
        .article-card:hover { transform: translateY(-5px); box-shadow: 0 15px 20px rgba(0,0,0,0.15); }
        .article-card img { flex-shrink: 0; width: 100%; height: 200px; object-fit: cover; border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem; }
        .article-card .p-4 { flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; padding: 1.5rem; }
        .article-card h2 { font-size: 1.25rem; font-weight: 600; color: #047857; margin-bottom: 0.75rem; }
        .article-card p { font-size: 0.95rem; line-height: 1.6; color: #4b5563; margin-bottom: 1rem; }
        .article-card a { display: inline-block; margin-top: auto; color: #059669; font-weight: 600; text-decoration: none; transition: color 0.2s ease; }
        .article-card a:hover { text-decoration: underline; color: #047857; }
        footer { background-color: #e0f2f7; padding: 1.5rem; border-top: 1px solid #d1d5db; text-align: center; }
        footer p { color: #6b7280; font-size: 0.9rem; }
    </style>
</head>
<body class="bg-green-50">
  <header class="text-white py-6 shadow">
    <div class="container mx-auto px-4 flex items-center justify-between">
      <div><h1 class="font-bold">Edukasi Ibu - Posyandu Bina Cita</h1><p class="text-sm">Artikel kesehatan dan tumbuh kembang anak</p></div>
      <a href="index.php" class="back-home-btn"><i class="fas fa-home mr-2"></i> Kembali ke Halaman Utama</a>
    </div>
  </header>
  <main class="container mx-auto px-4 py-6 sm:py-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
        <div class="article-card">
            <img src="https://imgur.com/PNydUvi.jpg" alt="Pentingnya ASI Eksklusif" class="object-cover">
            <div class="p-4"><h2 class="font-bold">Pentingnya ASI Eksklusif</h2><p class="text-gray-700">ASI eksklusif sangat penting untuk meningkatkan imun dan perkembangan bayi secara optimal.</p><a href="https://upk.kemkes.go.id/new/ketahui-manfaat-asi-eksklusif-bagi-bayi-dan-ibu" target="_blank" rel="noopener noreferrer">Baca Selengkapnya <i class="fas fa-external-link-alt ml-1 text-xs"></i></a></div>
        </div>
        <div class="article-card">
            <img src="https://imgur.com/lZWI2Sm.jpg" alt="Gizi Anak" class="object-cover">
            <div class="p-4"><h2 class="font-bold">Gizi Seimbang untuk Balita</h2><p class="text-gray-700">Penuhi kebutuhan gizi harian anak agar tumbuh sehat dan aktif sejak usia dini.</p><a href="https://ayosehat.kemkes.go.id/list-perangkat-ajar/makan-gizi-seimbang" target="_blank" rel="noopener noreferrer">Baca Selengkapnya <i class="fas fa-external-link-alt ml-1 text-xs"></i></a></div>
        </div>
        <div class="article-card">
            <img src="https://imgur.com/L1Hk8i8.jpg" alt="Imunisasi" class="object-cover">
            <div class="p-4"><h2 class="font-bold">Imunisasi Lengkap untuk Perlindungan Optimal</h2><p class="text-gray-700">Kenali jadwal imunisasi lengkap dan manfaatnya untuk mencegah berbagai penyakit.</p><a href="https://primaku.com/berita/momdad--yuk-kenali-jenis-posyandu-beserta-manfaatnya-" target="_blank" rel="noopener noreferrer">Baca Selengkapnya <i class="fas fa-external-link-alt ml-1 text-xs"></i></a></div>
        </div>
        <div class="article-card">
            <img src="https://imgur.com/P7b0GKS.jpg" alt="Pola Asuh" class="object-cover">
            <div class="p-4"><h2 class="font-bold">Pola Asuh Positif</h2><p class="text-gray-700">Pola asuh yang hangat dan tegas akan membantu anak tumbuh percaya diri dan mandiri.</p><a href="https://hellosehat.com/parenting/anak-6-sampai-9-tahun/perkembangan-anak/pengasuhan-positif-parenting/" target="_blank" rel="noopener noreferrer">Baca Selengkapnya <i class="fas fa-external-link-alt ml-1 text-xs"></i></a></div>
        </div>
    </div>
  </main>
  <footer><p>&copy; 2025 Posyandu Bina Cita. Semua Hak Dilindungi.</p></footer>
</body>
</html>
