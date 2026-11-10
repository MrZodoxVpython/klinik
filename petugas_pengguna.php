<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pengguna') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';
$petugas = mysqli_query($conn, "SELECT * FROM petugas ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Petugas - Posyandu Bina Cita</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100 text-gray-800 min-h-screen">
    <header class="bg-white shadow-lg px-6 py-4 flex flex-col sm:flex-row justify-between items-center sticky top-0 z-10">
        <div class="flex items-center mb-3 sm:mb-0">
            <svg class="h-10 w-10 text-green-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h2a2 2 0 002-2V7a2 2 0 00-2-2h-2V3a1 1 0 00-1-1H7a1 1 0 00-1 1v2H4a2 2 0 00-2 2v11a2 2 0 002 2h2v-3a2 2 0 012-2h8a2 2 0 012 2v3z" /></svg>
            <h1 class="text-2xl font-bold text-green-800">Daftar Petugas Posyandu</h1>
        </div>
        <a href="index.php" class="bg-green-500 text-white px-5 py-2 rounded-full hover:bg-green-600 transition duration-300 ease-in-out transform hover:scale-105 flex items-center"><i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda</a>
    </header>
    <main class="p-6 md:p-10">
        <p class="mb-6 text-lg text-gray-700 text-center">Berikut adalah daftar petugas aktif yang siap melayani di Posyandu Bina Cita:</p>
        <div class="overflow-x-auto bg-white rounded-xl shadow-2xl border border-green-200">
            <table class="min-w-full text-sm table-auto divide-y divide-gray-200">
                <thead class="bg-green-600 text-white">
                    <tr><th class="px-6 py-3 text-left font-semibold uppercase tracking-wider rounded-tl-xl">#</th><th class="px-6 py-3 text-left font-semibold uppercase tracking-wider">Nama</th><th class="px-6 py-3 text-left font-semibold uppercase tracking-wider">Jabatan</th><th class="px-6 py-3 text-left font-semibold uppercase tracking-wider rounded-tr-xl">Kontak</th></tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (mysqli_num_rows($petugas) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($petugas)): ?>
                        <tr class="hover:bg-green-50 transition duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900"><?= $no++ ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700"><?= htmlspecialchars($row['nama']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700"><?= htmlspecialchars($row['jabatan']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700"><a href="tel:<?= htmlspecialchars($row['kontak']) ?>" class="text-blue-600 hover:underline"><?= htmlspecialchars($row['kontak']) ?></a></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data petugas yang tersedia.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    <footer class="bg-white border-t border-green-200 py-6 mt-10"><div class="text-center text-sm text-gray-600">&copy; <?= date('Y') ?> Posyandu Bina Cita. Semua Hak Dilindungi.</div></footer>
</body>
</html>
