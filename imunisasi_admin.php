<?php
require_once 'auth.php';
require_once 'koneksi.php';
require_role('admin');
send_security_headers();

$status = (string) ($_GET['status'] ?? '');
$messages = [
    'added' => 'Data imunisasi berhasil ditambahkan.',
    'updated' => 'Data imunisasi berhasil diperbarui.',
    'deleted' => 'Data imunisasi berhasil dihapus.',
    'not_found' => 'Data imunisasi tidak ditemukan.',
    'delete_failed' => 'Data imunisasi gagal dihapus.',
];
$query = 'SELECT i.id, a.nik_anak, a.nama_anak, i.jenis_imunisasi, i.tanggal, i.keterangan, i.berat_badan, i.tinggi_badan, i.diagnosa FROM imunisasi i JOIN data_anak a ON i.anak_id = a.id ORDER BY i.tanggal DESC, i.id DESC';
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manajemen Imunisasi - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-emerald-50 min-h-screen text-gray-800">
  <header class="bg-white shadow-md px-6 py-4 flex flex-col sm:flex-row justify-between items-center sticky top-0 z-10">
    <h1 class="text-2xl font-bold text-emerald-700 mb-2 sm:mb-0">Manajemen Imunisasi</h1>
    <a href="admin.php" class="text-emerald-600 hover:text-emerald-800 flex items-center text-sm"><i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard</a>
  </header>
  <main class="px-4 sm:px-6 lg:px-8 py-6 max-w-screen-xl mx-auto">
    <?php if (isset($messages[$status])): ?><div class="mb-6 rounded-md border px-4 py-3 <?= in_array($status, ['not_found', 'delete_failed'], true) ? 'border-red-300 bg-red-50 text-red-700' : 'border-green-300 bg-green-50 text-green-700' ?>"><?= htmlspecialchars($messages[$status]) ?></div><?php endif; ?>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 bg-white border border-emerald-100 p-4 rounded-lg shadow-sm">
      <h2 class="text-xl sm:text-2xl font-semibold text-gray-700 mb-3 sm:mb-0">Daftar Imunisasi Anak</h2>
      <a href="tambah_imunisasi.php" class="bg-emerald-600 text-white px-5 py-2 rounded-md shadow hover:bg-emerald-700 flex items-center"><i class="fas fa-plus mr-2"></i>Tambah Imunisasi</a>
    </div>
    <div class="overflow-x-auto bg-white rounded-xl shadow border border-emerald-200">
      <table class="min-w-full table-auto text-sm">
        <thead class="bg-emerald-100 text-emerald-800 uppercase"><tr><th class="px-4 py-3 border-b">No</th><th class="px-4 py-3 border-b">NIK Anak</th><th class="px-4 py-3 border-b">Nama Anak</th><th class="px-4 py-3 border-b">Jenis</th><th class="px-4 py-3 border-b">Tanggal</th><th class="px-4 py-3 border-b">Berat (kg)</th><th class="px-4 py-3 border-b">Tinggi (cm)</th><th class="px-4 py-3 border-b">Diagnosa</th><th class="px-4 py-3 border-b">Keterangan</th><th class="px-4 py-3 border-b text-center">Aksi</th></tr></thead>
        <tbody><?php if ($result && mysqli_num_rows($result) > 0): ?><?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?><tr class="hover:bg-emerald-50 border-b border-emerald-50"><td class="px-4 py-2"><?= $no++ ?></td><td class="px-4 py-2"><?= htmlspecialchars($row['nik_anak']) ?></td><td class="px-4 py-2"><?= htmlspecialchars($row['nama_anak']) ?></td><td class="px-4 py-2"><?= htmlspecialchars($row['jenis_imunisasi']) ?></td><td class="px-4 py-2"><?= htmlspecialchars($row['tanggal']) ?></td><td class="px-4 py-2"><?= $row['berat_badan'] === null ? '-' : htmlspecialchars($row['berat_badan']) ?></td><td class="px-4 py-2"><?= $row['tinggi_badan'] === null ? '-' : htmlspecialchars($row['tinggi_badan']) ?></td><td class="px-4 py-2"><?= htmlspecialchars($row['diagnosa'] ?: '-') ?></td><td class="px-4 py-2"><?= htmlspecialchars($row['keterangan'] ?: '-') ?></td><td class="px-4 py-2 text-right whitespace-nowrap"><a href="edit_imunisasi.php?id=<?= (int) $row['id'] ?>" class="text-blue-600 hover:text-blue-800 mr-2">Edit</a><form method="POST" action="hapus_imunisasi.php" class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><button class="text-red-600 hover:text-red-800">Hapus</button></form></td></tr><?php endwhile; ?><?php else: ?><tr><td colspan="10" class="text-center px-4 py-8 text-gray-500 font-medium">Tidak ada data imunisasi ditemukan.</td></tr><?php endif; ?></tbody>
      </table>
    </div>
  </main>
</body>
</html>
