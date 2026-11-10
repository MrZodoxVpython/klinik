<?php
require_once 'auth.php';
require_once 'koneksi.php';
require_role('admin');
send_security_headers();

function childExists(mysqli $conn, int $anakId): bool
{
    $stmt = mysqli_prepare($conn, 'SELECT id FROM data_anak WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $anakId);
    mysqli_stmt_execute($stmt);
    $exists = mysqli_num_rows(mysqli_stmt_get_result($stmt)) === 1;
    mysqli_stmt_close($stmt);
    return $exists;
}

function optionalMeasurement(string $value, float $maximum): float|false|null
{
    if ($value === '') {
        return null;
    }
    if (!is_numeric($value) || (float) $value <= 0 || (float) $value > $maximum) {
        return false;
    }
    return (float) $value;
}

$errorMessage = '';
$anakId = 0;
$jenis = $tgl = $diagnosa = $ket = '';
$berat = $tinggi = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $anakId = filter_var($_POST['anak_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0;
    $jenis = trim((string) ($_POST['jenis_imunisasi'] ?? ''));
    $tgl = (string) ($_POST['tanggal'] ?? '');
    $berat = trim((string) ($_POST['berat_badan'] ?? ''));
    $tinggi = trim((string) ($_POST['tinggi_badan'] ?? ''));
    $diagnosa = trim((string) ($_POST['diagnosa'] ?? ''));
    $ket = trim((string) ($_POST['keterangan'] ?? ''));
    $beratValue = optionalMeasurement($berat, 99.99);
    $tinggiValue = optionalMeasurement($tinggi, 999.99);

    if (!$anakId || !childExists($conn, $anakId)) {
        $errorMessage = 'Pilih anak yang valid.';
    } elseif ($jenis === '' || strlen($jenis) > 100) {
        $errorMessage = 'Jenis imunisasi wajib diisi, maksimal 100 karakter.';
    } elseif (!is_valid_date($tgl) || $tgl > date('Y-m-d')) {
        $errorMessage = 'Tanggal imunisasi tidak valid atau berada di masa depan.';
    } elseif ($beratValue === false || $tinggiValue === false) {
        $errorMessage = 'Berat atau tinggi badan tidak valid.';
    } elseif (strlen($diagnosa) > 255) {
        $errorMessage = 'Diagnosa maksimal 255 karakter.';
    } else {
        $stmt = mysqli_prepare($conn, 'INSERT INTO imunisasi (anak_id, jenis_imunisasi, tanggal, berat_badan, tinggi_badan, diagnosa, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'issddss', $anakId, $jenis, $tgl, $beratValue, $tinggiValue, $diagnosa, $ket);
        $saved = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if ($saved) {
            redirect('imunisasi_admin.php?status=added');
        }
        $errorMessage = 'Data imunisasi gagal disimpan.';
    }
}

$anakResult = mysqli_query($conn, 'SELECT id, nik_anak, nama_anak FROM data_anak ORDER BY nama_anak ASC');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Imunisasi - Admin Posyandu Bina Cita</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100 min-h-screen flex items-center justify-center py-10 px-4">
    <div class="bg-white p-8 sm:p-10 rounded-xl shadow-2xl w-full max-w-lg border border-green-200">
        <h1 class="text-3xl font-extrabold text-green-800 mb-6 text-center">Tambah Data Imunisasi</h1>
        <?php if ($errorMessage !== ''): ?><div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6"><?= htmlspecialchars($errorMessage) ?></div><?php endif; ?>
        <form method="POST" class="space-y-5">
            <?= csrf_field() ?>
            <div><label for="anak_id" class="block text-sm font-semibold text-gray-700 mb-1">Pilih Anak</label><select name="anak_id" id="anak_id" required class="block w-full px-4 py-2 border rounded-lg"><option value="">-- Pilih Anak --</option><?php while ($anak = mysqli_fetch_assoc($anakResult)): ?><option value="<?= (int) $anak['id'] ?>" <?= $anakId === (int) $anak['id'] ? 'selected' : '' ?>><?= htmlspecialchars($anak['nik_anak']) ?> — <?= htmlspecialchars($anak['nama_anak']) ?></option><?php endwhile; ?></select></div>
            <div><label for="jenis_imunisasi" class="block text-sm font-semibold text-gray-700 mb-1">Jenis Imunisasi</label><input type="text" name="jenis_imunisasi" id="jenis_imunisasi" maxlength="100" value="<?= htmlspecialchars($jenis) ?>" required class="block w-full px-4 py-2 border rounded-lg"></div>
            <div><label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Imunisasi</label><input type="date" name="tanggal" id="tanggal" max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($tgl) ?>" required class="block w-full px-4 py-2 border rounded-lg"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4"><div><label for="berat_badan" class="block text-sm font-semibold text-gray-700 mb-1">Berat Badan (kg)</label><input type="number" step="0.01" min="0.01" max="99.99" name="berat_badan" id="berat_badan" value="<?= htmlspecialchars($berat) ?>" class="block w-full px-4 py-2 border rounded-lg"></div><div><label for="tinggi_badan" class="block text-sm font-semibold text-gray-700 mb-1">Tinggi Badan (cm)</label><input type="number" step="0.01" min="0.01" max="999.99" name="tinggi_badan" id="tinggi_badan" value="<?= htmlspecialchars($tinggi) ?>" class="block w-full px-4 py-2 border rounded-lg"></div></div>
            <div><label for="diagnosa" class="block text-sm font-semibold text-gray-700 mb-1">Diagnosa</label><input type="text" name="diagnosa" id="diagnosa" maxlength="255" value="<?= htmlspecialchars($diagnosa) ?>" class="block w-full px-4 py-2 border rounded-lg"></div>
            <div><label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label><textarea name="keterangan" id="keterangan" rows="3" class="block w-full px-4 py-2 border rounded-lg"><?= htmlspecialchars($ket) ?></textarea></div>
            <div class="flex justify-between items-center pt-2"><button type="submit" class="bg-green-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-green-700"><i class="fas fa-save mr-2"></i>Simpan Data</button><a href="imunisasi_admin.php" class="text-gray-600 hover:text-green-800">Kembali</a></div>
        </form>
    </div>
</body>
</html>
