<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
$query = "SELECT i.id, a.nik_anak, a.nama_anak, i.jenis_imunisasi, i.tanggal, i.keterangan, i.berat_badan, i.tinggi_badan, i.diagnosa
          FROM imunisasi i
          JOIN data_anak a ON i.id_anak = a.id
          ORDER BY i.tanggal DESC";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manajemen Imunisasi - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet"/>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-emerald-50 min-h-screen text-gray-800">
  <header class="bg-white shadow-md px-6 py-4 flex flex-col sm:flex-row justify-between items-center sticky top-0 z-10">
    <h1 class="text-2xl font-bold text-emerald-700 mb-2 sm:mb-0">Manajemen Imunisasi</h1>
    <a href="admin.php" class="text-emerald-600 hover:text-emerald-800 flex items-center text-sm"><i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard</a>
  </header>
  <main class="px-4 sm:px-6 lg:px-8 py-6 max-w-screen-xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 bg-white border border-emerald-100 p-4 rounded-lg shadow-sm">
      <h2 class="text-xl sm:text-2xl font-semibold text-gray-700 mb-3 sm:mb-0">Daftar Imunisasi Anak</h2>
      <a href="tambah_imunisasi.php" class="bg-emerald-600 text-white px-5 py-2 rounded-md shadow hover:bg-emerald-700 flex items-center"><i class="fas fa-plus mr-2"></i> Tambah Imunisasi</a>
    </div>
    <div class="overflow-x-auto bg-white rounded-xl shadow border border-emerald-200">
      <table class="min-w-full table-auto text-sm">
        <thead class="bg-emerald-100 text-emerald-800 uppercase">
          <tr>
            <th class="px-4 py-3 border-b border-emerald-200">No</th>
            <th class="px-4 py-3 border-b border-emerald-200">NIK Anak</th>
            <th class="px-4 py-3 border-b border-emerald-200">Nama Anak</th>
            <th class="px-4 py-3 border-b border-emerald-200">Jenis</th>
            <th class="px-4 py-3 border-b border-emerald-200">Tanggal</th>
            <th class="px-4 py-3 border-b border-emerald-200">Berat (kg)</th>
            <th class="px-4 py-3 border-b border-emerald-200">Tinggi (cm)</th>
            <th class="px-4 py-3 border-b border-emerald-200">Diagnosa</th>
            <th class="px-4 py-3 border-b border-emerald-200">Keterangan</th>
            <th class="px-4 py-3 border-b border-emerald-200 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
          <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
            <tr class="hover:bg-emerald-50 border-b border-emerald-50">
              <td class="px-4 py-2"><?= $no++ ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['nik_anak']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['nama_anak']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['jenis_imunisasi']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['tanggal']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['berat_badan']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['tinggi_badan']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['diagnosa']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['keterangan']) ?></td>
              <td class="px-4 py-2 text-right space-x-2">
                <a href="edit_imunisasi.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i> Edit</a>
                <a href="hapus_imunisasi.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i> Hapus</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="10" class="text-center px-4 py-8 text-gray-500 font-medium"><i class="fas fa-info-circle mr-1"></i> Tidak ada data imunisasi ditemukan.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>
