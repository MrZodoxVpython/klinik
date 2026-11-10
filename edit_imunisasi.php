<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
if (!isset($_GET['id'])) {
    die("ID tidak ditemukan.");
}
$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM imunisasi WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);
if (!$data) {
    die("Data tidak ditemukan.");
}
$anak_result = mysqli_query($conn, "SELECT id, nik_anak, nama_anak FROM data_anak ORDER BY nama_anak ASC");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $anak_id        = $_POST['anak_id'];
    $jenis          = $_POST['jenis_imunisasi'];
    $tgl            = $_POST['tanggal'];
    $berat_badan    = $_POST['berat_badan'];
    $tinggi_badan   = $_POST['tinggi_badan'];
    $diagnosa       = $_POST['diagnosa'];
    $ket            = $_POST['keterangan'];
    $stmt = mysqli_prepare($conn, "UPDATE imunisasi SET anak_id=?, jenis_imunisasi=?, tanggal=?, berat_badan=?, tinggi_badan=?, diagnosa=?, keterangan=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "issddssi", $anak_id, $jenis, $tgl, $berat_badan, $tinggi_badan, $diagnosa, $ket, $id);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: imunisasi_admin.php?status=updated");
    } else {
        header("Location: imunisasi_admin.php?status=error_update");
    }
    mysqli_stmt_close($stmt);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Imunisasi</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0fdf4; }
        input[type="text"], input[type="date"], input[type="number"], select, textarea {
            box-sizing: border-box; width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); font-size: 0.875rem; line-height: 1.25rem; color: #1f2937;
        }
        input[type="text"]:focus, input[type="date"]:focus, input[type="number"]:focus, select:focus, textarea:focus {
            outline: none; ring-width: 2px; ring-color: #10b981; border-color: #10b981;
        }
    </style>
</head>
<body class="bg-green-50 min-h-screen p-4 sm:p-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-green-700">Edit Data Imunisasi</h1>
                <a href="imunisasi_admin.php" class="text-green-600 hover:text-green-800 flex items-center"><i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar</a>
            </div>
            <form method="POST" class="space-y-6">
                <div>
                    <label for="anak_id" class="block text-sm font-medium text-gray-700 mb-1 required">Pilih Anak:</label>
                    <select name="anak_id" id="anak_id" required class="w-full mt-1 px-3 py-2 border rounded">
                        <?php
                        if ($anak_result && mysqli_num_rows($anak_result) > 0) {
                            while ($anak = mysqli_fetch_assoc($anak_result)):
                        ?>
                            <option value="<?= $anak['id'] ?>" <?= $data['anak_id'] == $anak['id'] ? 'selected' : '' ?>><?= htmlspecialchars($anak['nik_anak']) ?> - <?= htmlspecialchars($anak['nama_anak']) ?></option>
                        <?php
                            endwhile;
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label for="jenis_imunisasi" class="block text-sm font-medium text-gray-700 mb-1 required">Jenis Imunisasi:</label>
                    <input type="text" name="jenis_imunisasi" id="jenis_imunisasi" value="<?= htmlspecialchars($data['jenis_imunisasi']) ?>" required placeholder="Contoh: BCG, DPT-HB-Hib 1" />
                </div>
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1 required">Tanggal Imunisasi:</label>
                    <input type="date" name="tanggal" id="tanggal" value="<?= htmlspecialchars($data['tanggal']) ?>" required />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="berat_badan" class="block text-sm font-medium text-gray-700 mb-1 required">Berat Badan (kg):</label>
                        <input type="number" step="0.01" name="berat_badan" id="berat_badan" value="<?= htmlspecialchars($data['berat_badan']) ?>" required placeholder="Contoh: 7.5" />
                    </div>
                    <div>
                        <label for="tinggi_badan" class="block text-sm font-medium text-gray-700 mb-1 required">Tinggi Badan (cm):</label>
                        <input type="number" step="0.01" name="tinggi_badan" id="tinggi_badan" value="<?= htmlspecialchars($data['tinggi_badan']) ?>" required placeholder="Contoh: 68.2" />
                    </div>
                </div>
                <div>
                    <label for="diagnosa" class="block text-sm font-medium text-gray-700 mb-1">Diagnosa (Opsional):</label>
                    <input type="text" name="diagnosa" id="diagnosa" value="<?= htmlspecialchars($data['diagnosa']) ?>" placeholder="Contoh: Batuk ringan, Sehat" />
                </div>
                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan Tambahan (Opsional):</label>
                    <textarea name="keterangan" id="keterangan" rows="3" placeholder="Tambahkan catatan penting lainnya..."><?= htmlspecialchars($data['keterangan']) ?></textarea>
                </div>
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mt-6">
                    <button type="submit" class="w-full sm:w-auto bg-green-600 text-white px-8 py-2.5 rounded-lg font-semibold hover:bg-green-700 transition duration-300 ease-in-out transform hover:scale-105 flex items-center justify-center shadow-md"><i class="fas fa-save mr-2"></i> Update Data</button>
                    <a href="imunisasi_admin.php" class="w-full sm:w-auto text-center text-sm font-medium text-gray-600 hover:text-gray-800 hover:underline transition duration-300 ease-in-out py-2 flex items-center justify-center"><i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Imunisasi</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
