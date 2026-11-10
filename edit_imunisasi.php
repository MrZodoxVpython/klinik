<?php
require_once 'auth.php';
require_once 'koneksi.php';
require_role('admin');
send_security_headers();

function validOptionalMeasurement(string $value, float $maximum): float|false|null
{
    if ($value === '') {
        return null;
    }
    if (!is_numeric($value) || (float) $value <= 0 || (float) $value > $maximum) {
        return false;
    }
    return (float) $value;
}

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    redirect('imunisasi_admin.php?status=not_found');
}

$fetchStmt = mysqli_prepare($conn, 'SELECT * FROM imunisasi WHERE id = ? LIMIT 1');
mysqli_stmt_bind_param($fetchStmt, 'i', $id);
mysqli_stmt_execute($fetchStmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($fetchStmt));
mysqli_stmt_close($fetchStmt);
if (!$data) {
    redirect('imunisasi_admin.php?status=not_found');
}

$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $anakId = filter_var($_POST['anak_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0;
    $jenis = trim((string) ($_POST['jenis_imunisasi'] ?? ''));
    $tgl = (string) ($_POST['tanggal'] ?? '');
    $beratText = trim((string) ($_POST['berat_badan'] ?? ''));
    $tinggiText = trim((string) ($_POST['tinggi_badan'] ?? ''));
    $diagnosa = trim((string) ($_POST['diagnosa'] ?? ''));
    $ket = trim((string) ($_POST['keterangan'] ?? ''));
    $berat = validOptionalMeasurement($beratText, 99.99);
    $tinggi = validOptionalMeasurement($tinggiText, 999.99);

    $childStmt = mysqli_prepare($conn, 'SELECT id FROM data_anak WHERE id = ?');
    mysqli_stmt_bind_param($childStmt, 'i', $anakId);
    mysqli_stmt_execute($childStmt);
    $childExists = mysqli_num_rows(mysqli_stmt_get_result($childStmt)) === 1;
    mysqli_stmt_close($childStmt);

    if (!$childExists) {
        $errorMessage = 'Pilih anak yang valid.';
    } elseif ($jenis === '' || strlen($jenis) > 100) {
        $errorMessage = 'Jenis imunisasi wajib diisi, maksimal 100 karakter.';
    } elseif (!is_valid_date($tgl) || $tgl > date('Y-m-d')) {
        $errorMessage = 'Tanggal imunisasi tidak valid atau berada di masa depan.';
    } elseif ($berat === false || $tinggi === false) {
        $errorMessage = 'Berat atau tinggi badan tidak valid.';
    } elseif (strlen($diagnosa) > 255) {
        $errorMessage = 'Diagnosa maksimal 255 karakter.';
    } else {
        $stmt = mysqli_prepare($conn, 'UPDATE imunisasi SET anak_id = ?, jenis_imunisasi = ?, tanggal = ?, berat_badan = ?, tinggi_badan = ?, diagnosa = ?, keterangan = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'issddssi', $anakId, $jenis, $tgl, $berat, $tinggi, $diagnosa, $ket, $id);
        $saved = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if ($saved) {
            redirect('imunisasi_admin.php?status=updated');
        }
        $errorMessage = 'Data imunisasi gagal diperbarui.';
        $data = array_merge($data, ['anak_id' => $anakId, 'jenis_imunisasi' => $jenis, 'tanggal' => $tgl, 'berat_badan' => $beratText, 'tinggi_badan' => $tinggiText, 'diagnosa' => $diagnosa, 'keterangan' => $ket]);
    }
}

$anakResult = mysqli_query($conn, 'SELECT id, nik_anak, nama_anak FROM data_anak ORDER BY nama_anak ASC');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Imunisasi</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-green-50 min-h-screen p-4 sm:p-6">
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg p-6 sm:p-8">
        <div class="flex justify-between items-center mb-6"><h1 class="text-2xl font-bold text-green-700">Edit Data Imunisasi</h1><a href="imunisasi_admin.php" class="text-green-600 hover:text-green-800">Kembali</a></div>
        <?php if ($errorMessage !== ''): ?><div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6"><?= htmlspecialchars($errorMessage) ?></div><?php endif; ?>
        <form method="POST" class="space-y-6">
            <?= csrf_field() ?>
            <div><label for="anak_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Anak</label><select name="anak_id" id="anak_id" required class="w-full px-3 py-2 border rounded"><?php while ($anak = mysqli_fetch_assoc($anakResult)): ?><option value="<?= (int) $anak['id'] ?>" <?= (int) $data['anak_id'] === (int) $anak['id'] ? 'selected' : '' ?>><?= htmlspecialchars($anak['nik_anak']) ?> — <?= htmlspecialchars($anak['nama_anak']) ?></option><?php endwhile; ?></select></div>
            <div><label for="jenis_imunisasi" class="block text-sm font-medium text-gray-700 mb-1">Jenis Imunisasi</label><input type="text" name="jenis_imunisasi" id="jenis_imunisasi" maxlength="100" value="<?= htmlspecialchars($data['jenis_imunisasi']) ?>" required class="w-full px-3 py-2 border rounded"></div>
            <div><label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Imunisasi</label><input type="date" name="tanggal" id="tanggal" max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($data['tanggal']) ?>" required class="w-full px-3 py-2 border rounded"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4"><div><label for="berat_badan" class="block text-sm font-medium text-gray-700 mb-1">Berat Badan (kg)</label><input type="number" step="0.01" min="0.01" max="99.99" name="berat_badan" id="berat_badan" value="<?= htmlspecialchars((string) $data['berat_badan']) ?>" class="w-full px-3 py-2 border rounded"></div><div><label for="tinggi_badan" class="block text-sm font-medium text-gray-700 mb-1">Tinggi Badan (cm)</label><input type="number" step="0.01" min="0.01" max="999.99" name="tinggi_badan" id="tinggi_badan" value="<?= htmlspecialchars((string) $data['tinggi_badan']) ?>" class="w-full px-3 py-2 border rounded"></div></div>
            <div><label for="diagnosa" class="block text-sm font-medium text-gray-700 mb-1">Diagnosa</label><input type="text" name="diagnosa" id="diagnosa" maxlength="255" value="<?= htmlspecialchars((string) $data['diagnosa']) ?>" class="w-full px-3 py-2 border rounded"></div>
            <div><label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan Tambahan</label><textarea name="keterangan" id="keterangan" rows="3" class="w-full px-3 py-2 border rounded"><?= htmlspecialchars((string) $data['keterangan']) ?></textarea></div>
            <div class="flex justify-between"><button type="submit" class="bg-green-600 text-white px-8 py-2.5 rounded-lg font-semibold hover:bg-green-700"><i class="fas fa-save mr-2"></i>Update Data</button><a href="imunisasi_admin.php" class="text-gray-600 hover:underline py-2">Batal</a></div>
        </form>
    </div>
</body>
</html>
