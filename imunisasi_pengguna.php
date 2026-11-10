<?php
require_once 'auth.php';
require_once 'koneksi.php';
require_role('pengguna');
send_security_headers();
$user_id = $_SESSION['user_id'];
$query = "
  SELECT a.nik_anak, a.nama_anak, i.jenis_imunisasi, i.tanggal, i.keterangan, i.berat_badan, i.tinggi_badan, i.diagnosa
  FROM data_anak a
  JOIN imunisasi i ON a.id = i.anak_id
  WHERE a.user_id = ?
  ORDER BY i.tanggal DESC
";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Riwayat Imunisasi Anak</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-green-50 min-h-screen p-6 text-gray-800">
  <header class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-bold text-green-700">💉 Riwayat Imunisasi Anak</h1>
    <a href="index.php" class="text-green-600 hover:underline">← Kembali</a>
  </header>
  <div class="overflow-auto bg-white rounded shadow border">
    <table class="min-w-full text-sm text-left">
      <thead class="bg-green-100 text-green-800">
        <tr><th class="px-4 py-2">NIK Anak</th><th class="px-4 py-2">Nama Anak</th><th class="px-4 py-2">Jenis Imunisasi</th><th class="px-4 py-2">Tanggal</th><th class="px-4 py-2">Berat</th><th class="px-4 py-2">Tinggi</th><th class="px-4 py-2">Diagnosa</th><th class="px-4 py-2">Keterangan</th></tr>
      </thead>
      <tbody class="divide-y divide-green-100">
        <?php if (mysqli_num_rows($result) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr class="hover:bg-green-50">
              <td class="px-4 py-2"><?= htmlspecialchars($row['nik_anak']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['nama_anak']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['jenis_imunisasi']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['tanggal']) ?></td>
              <td class="px-4 py-2"><?= $row['berat_badan'] === null ? '-' : htmlspecialchars($row['berat_badan']) ?> kg</td>
              <td class="px-4 py-2"><?= $row['tinggi_badan'] === null ? '-' : htmlspecialchars($row['tinggi_badan']) ?> cm</td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['diagnosa'] ?: '-') ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['keterangan']) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="8" class="text-center text-gray-500 px-4 py-4">Belum ada data imunisasi.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
