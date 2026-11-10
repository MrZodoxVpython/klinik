<?php
require_once 'auth.php';
require_once 'koneksi.php';
require_role('admin');
send_security_headers();

function hariIndo(string $tanggal): string
{
    $hari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
    return $hari[date('l', strtotime($tanggal))] ?? '';
}

function validTime(string $waktu): bool
{
    return (bool) preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $waktu);
}

$status = (string) ($_GET['status'] ?? '');
$successMessage = $status === 'deleted' ? 'Jadwal berhasil dihapus.' : '';
$errorMessage = '';
$editData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'delete') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) {
            $errorMessage = 'Jadwal tidak valid.';
        } else {
            $stmt = mysqli_prepare($conn, 'DELETE FROM jadwal_posyandu WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'i', $id);
            $deleted = mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) === 1;
            mysqli_stmt_close($stmt);
            if ($deleted) {
                redirect('jadwal_admin.php?status=deleted');
            }
            $errorMessage = 'Jadwal gagal dihapus.';
        }
    }

    if ($action === 'save') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $tanggal = (string) ($_POST['tanggal'] ?? '');
        $waktu = (string) ($_POST['waktu'] ?? '');
        $tempat = trim((string) ($_POST['tempat'] ?? ''));
        $keterangan = trim((string) ($_POST['keterangan'] ?? ''));
        $editData = ['id' => $id ?: '', 'tanggal' => $tanggal, 'waktu' => $waktu, 'tempat' => $tempat, 'keterangan' => $keterangan];

        if (!is_valid_date($tanggal) || $tanggal < date('Y-m-d')) {
            $errorMessage = 'Tanggal jadwal harus valid dan tidak boleh sudah lewat.';
        } elseif (!validTime($waktu)) {
            $errorMessage = 'Jam mulai tidak valid.';
        } elseif ($tempat === '' || strlen($tempat) > 100) {
            $errorMessage = 'Tempat wajib diisi, maksimal 100 karakter.';
        } else {
            if ($id !== false) {
                $stmt = mysqli_prepare($conn, 'UPDATE jadwal_posyandu SET tanggal = ?, waktu = ?, tempat = ?, keterangan = ? WHERE id = ?');
                mysqli_stmt_bind_param($stmt, 'ssssi', $tanggal, $waktu, $tempat, $keterangan, $id);
                $saved = mysqli_stmt_execute($stmt);
            } else {
                $stmt = mysqli_prepare($conn, 'INSERT INTO jadwal_posyandu (tanggal, waktu, tempat, keterangan) VALUES (?, ?, ?, ?)');
                mysqli_stmt_bind_param($stmt, 'ssss', $tanggal, $waktu, $tempat, $keterangan);
                $saved = mysqli_stmt_execute($stmt);
            }
            mysqli_stmt_close($stmt);
            if ($saved) {
                $successMessage = $id !== false ? 'Jadwal berhasil diperbarui.' : 'Jadwal berhasil ditambahkan.';
                $editData = null;
            } else {
                $errorMessage = 'Jadwal gagal disimpan.';
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit'])) {
    $editId = filter_var($_GET['edit'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($editId === false) {
        $errorMessage = 'Jadwal tidak valid.';
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT * FROM jadwal_posyandu WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $editId);
        mysqli_stmt_execute($stmt);
        $editData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        if (!$editData) {
            $errorMessage = 'Jadwal tidak ditemukan.';
        }
    }
}

$jadwal = mysqli_query($conn, 'SELECT * FROM jadwal_posyandu ORDER BY tanggal ASC, waktu ASC');
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Kelola Jadwal Posyandu</title><link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"></head>
<body class="bg-green-50 min-h-screen">
  <div class="max-w-4xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6"><h1 class="text-2xl font-bold text-green-700"><?= $editData ? 'Edit Jadwal' : 'Tambah Jadwal Posyandu' ?></h1><a href="admin.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded">Kembali ke Dashboard</a></div>
    <?php if ($successMessage !== ''): ?><div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= htmlspecialchars($successMessage) ?></div><?php endif; ?>
    <?php if ($errorMessage !== ''): ?><div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($errorMessage) ?></div><?php endif; ?>
    <form method="POST" class="bg-white p-6 rounded shadow mb-8">
      <?= csrf_field() ?><input type="hidden" name="action" value="save"><?php if ($editData): ?><input type="hidden" name="id" value="<?= (int) $editData['id'] ?>"><?php endif; ?>
      <label class="block mb-2 font-medium">Tanggal Posyandu</label><input type="date" name="tanggal" min="<?= date('Y-m-d') ?>" required value="<?= htmlspecialchars($editData['tanggal'] ?? '') ?>" class="w-full mb-4 border rounded px-4 py-2">
      <label class="block mb-2 font-medium">Jam Mulai</label><input type="time" name="waktu" required value="<?= htmlspecialchars(isset($editData['waktu']) ? substr($editData['waktu'], 0, 5) : '') ?>" class="w-full mb-4 border rounded px-4 py-2">
      <label class="block mb-2 font-medium">Lokasi</label><input type="text" name="tempat" maxlength="100" required value="<?= htmlspecialchars($editData['tempat'] ?? '') ?>" class="w-full mb-4 border rounded px-4 py-2">
      <label class="block mb-2 font-medium">Keterangan</label><textarea name="keterangan" rows="3" class="w-full mb-4 border rounded px-4 py-2"><?= htmlspecialchars($editData['keterangan'] ?? '') ?></textarea>
      <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700"><?= $editData ? 'Update' : 'Simpan Jadwal' ?></button><?php if ($editData): ?><a href="jadwal_admin.php" class="ml-4 text-sm text-green-600 hover:underline">Batal Edit</a><?php endif; ?>
    </form>
    <h2 class="text-xl font-semibold text-green-800 mb-2">Daftar Jadwal Posyandu</h2>
    <ul class="bg-white rounded shadow divide-y"><?php while ($row = mysqli_fetch_assoc($jadwal)): ?><li class="p-4 flex justify-between items-center gap-4"><div><strong><?= hariIndo($row['tanggal']) ?>, <?= date('d-m-Y', strtotime($row['tanggal'])) ?> — <?= htmlspecialchars(substr($row['waktu'], 0, 5)) ?> WIB</strong><br>Lokasi: <?= htmlspecialchars($row['tempat']) ?><br><small class="text-gray-500"><?= htmlspecialchars($row['keterangan']) ?></small></div><div class="space-x-2 whitespace-nowrap"><a href="jadwal_admin.php?edit=<?= (int) $row['id'] ?>" class="text-blue-600 hover:underline">Edit</a><form method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><button class="text-red-600 hover:underline">Hapus</button></form></div></li><?php endwhile; ?></ul>
  </div>
</body>
</html>
