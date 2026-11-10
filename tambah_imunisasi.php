<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
$error_message = '';
$success_message = '';
$anak_result = mysqli_query($conn, "SELECT id, nik_anak, nama_anak FROM data_anak ORDER BY nama_anak ASC");
if (!$anak_result) {
    $error_message = "Gagal mengambil data anak: " . mysqli_error($conn);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $anak_id = isset($_POST['anak_id']) ? intval($_POST['anak_id']) : 0;
    $jenis   = isset($_POST['jenis_imunisasi']) ? trim($_POST['jenis_imunisasi']) : '';
    $tgl     = isset($_POST['tanggal']) ? trim($_POST['tanggal']) : '';
    $ket     = isset($_POST['keterangan']) ? trim($_POST['keterangan']) : '';
    if (empty($anak_id) || empty($jenis) || empty($tgl)) {
        $error_message = "Semua kolom wajib diisi kecuali Keterangan.";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO imunisasi (anak_id, jenis_imunisasi, tanggal, keterangan) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            $error_message = "Gagal menyiapkan statement: " . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($stmt, "isss", $anak_id, $jenis, $tgl, $ket);
            $sukses = mysqli_stmt_execute($stmt);
            if ($sukses) {
                header("Location: imunisasi_admin.php?status=added");
                exit;
            } else {
                $error_message = "Gagal menyimpan data imunisasi: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
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
<body class="bg-gradient-to-br from-green-50 to-green-100 min-h-screen flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-8 sm:p-10 rounded-xl shadow-2xl w-full max-w-lg border border-green-200">
        <div class="flex justify-center mb-6">
            <svg class="h-16 w-16 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.007 12.007 0 002.944 12c0 2.871.675 5.55 1.945 7.917v0C7.294 20.354 9.582 21 12 21c2.418 0 4.706-.646 6.111-2.083v0C21.325 17.55 22 14.871 22 12c0-3.097-.665-6.012-1.882-8.616z" />
            </svg>
        </div>
        <h2 class="text-3xl font-extrabold text-green-800 mb-4 text-center">Tambah Data Imunisasi</h2>
        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md relative mb-6 text-sm" role="alert">
                <strong class="font-bold mr-1">Error!</strong><span class="block sm:inline"><?= htmlspecialchars($error_message) ?></span>
            </div>
        <?php endif; ?>
        <form method="POST" class="space-y-5">
            <div>
                <label for="anak_id" class="block text-sm font-semibold text-gray-700 mb-1">Pilih Anak:</label>
                <select name="anak_id" id="anak_id" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 text-gray-900">
                    <option value="">-- Pilih Anak --</option>
                    <?php
                    if ($anak_result && mysqli_num_rows($anak_result) > 0) {
                        mysqli_data_seek($anak_result, 0);
                        while ($anak = mysqli_fetch_assoc($anak_result)):
                    ?>
                        <option value="<?= $anak['id'] ?>"><?= htmlspecialchars($anak['nik_anak']) ?> - <?= htmlspecialchars($anak['nama_anak']) ?></option>
                    <?php
                        endwhile;
                    } else {
                        echo '<option value="" disabled>Tidak ada data anak ditemukan</option>';
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="jenis_imunisasi" class="block text-sm font-semibold text-gray-700 mb-1">Jenis Imunisasi:</label>
                <input type="text" name="jenis_imunisasi" id="jenis_imunisasi" placeholder="Contoh: BCG, Polio, DPT-HB-Hib" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 text-gray-900 placeholder-gray-400" />
            </div>
            <div>
                <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Imunisasi:</label>
                <input type="date" name="tanggal" id="tanggal" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 text-gray-900" />
            </div>
            <div>
                <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-1">Keterangan (Opsional):</label>
                <textarea name="keterangan" id="keterangan" placeholder="Catatan tambahan mengenai imunisasi" rows="3" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 text-gray-900 placeholder-gray-400"></textarea>
            </div>
            <div class="flex justify-between items-center mt-6">
                <button type="submit" class="bg-green-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition duration-300 ease-in-out transform hover:scale-105 flex items-center justify-center">
                    <i class="fas fa-save mr-2"></i> Simpan Data
                </button>
                <a href="imunisasi_admin.php" class="text-gray-600 hover:text-green-800 transition duration-300 flex items-center"><i class="fas fa-arrow-left mr-2"></i> Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>
