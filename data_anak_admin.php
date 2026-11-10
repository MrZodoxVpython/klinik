<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
$success_message = "";
$error_message = "";
function formatTanggalIndo($tanggal) {
    if (empty($tanggal) || $tanggal === '0000-00-00') return '-';
    $bulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
    $pecah = explode('-', $tanggal);
    return $pecah[2] . ' ' . $bulan[$pecah[1]] . ' ' . $pecah[0];
}
$id = 0; $edit_mode = false;
$nik_anak = $nama_anak = $tanggal_lahir = $jenis_kelamin = $nama_ibu = $nama_ayah = $alamat = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $nik_anak = trim($_POST['nik_anak']);
    $nama_anak = trim($_POST['nama_anak']);
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? null;
    $nama_ibu = trim($_POST['nama_ibu']);
    $nama_ayah = trim($_POST['nama_ayah']);
    $alamat = trim($_POST['alamat']);
    if ($nik_anak === "" || $nama_anak === "" || $tanggal_lahir === "") {
        $error_message = "NIK, Nama Anak, dan Tanggal Lahir wajib diisi.";
    } else {
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, "UPDATE data_anak SET nik_anak=?, nama_anak=?, tanggal_lahir=?, jenis_kelamin=?, nama_ibu=?, nama_ayah=?, alamat=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sssssssi", $nik_anak, $nama_anak, $tanggal_lahir, $jenis_kelamin, $nama_ibu, $nama_ayah, $alamat, $id);
            if (mysqli_stmt_execute($stmt)) $success_message = "Data berhasil diperbarui!";
            else $error_message = "Gagal memperbarui data: " . mysqli_error($conn);
            mysqli_stmt_close($stmt);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO data_anak (nik_anak, nama_anak, tanggal_lahir, jenis_kelamin, nama_ibu, nama_ayah, alamat) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssssss", $nik_anak, $nama_anak, $tanggal_lahir, $jenis_kelamin, $nama_ibu, $nama_ayah, $alamat);
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Data berhasil ditambahkan!";
                $nik_anak = $nama_anak = $tanggal_lahir = $jenis_kelamin = $nama_ibu = $nama_ayah = $alamat = "";
            } else {
                $error_message = "Gagal menambahkan data: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM data_anak WHERE id = $edit_id LIMIT 1");
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $id = $row['id'];
        $nik_anak = $row['nik_anak'];
        $nama_anak = $row['nama_anak'];
        $tanggal_lahir = $row['tanggal_lahir'];
        $jenis_kelamin = $row['jenis_kelamin'];
        $nama_ibu = $row['nama_ibu'];
        $nama_ayah = $row['nama_ayah'];
        $alamat = $row['alamat'];
        $edit_mode = true;
    } else $error_message = "Data anak tidak ditemukan.";
}
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    if (mysqli_query($conn, "DELETE FROM data_anak WHERE id = $delete_id")) {
        header("Location: data_anak_admin.php?status=deleted");
        exit;
    } else {
        $error_message = "Gagal menghapus data: " . mysqli_error($conn);
    }
}
$query_data_anak = "SELECT * FROM data_anak ORDER BY nama_anak ASC";
$data = mysqli_query($conn, $query_data_anak);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Data Anak - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        @media screen and (max-width: 768px) {
            table { border: 0; }
            table thead { display: none; }
            table tr { display: block; margin-bottom: 0.825em; border: 1px solid #e2e8f0; border-radius: 0.75rem; background-color: #ffffff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
            table td { display: block; text-align: right; font-size: 0.9rem; border-bottom: 1px dotted #cbd5e0; padding: 0.625rem 1rem; position: relative; padding-left: 50%; }
            table td::before { content: attr(data-label); position: absolute; left: 0; width: 45%; padding-left: 1rem; font-weight: 600; text-align: left; color: #4a5568; }
            table td:last-child { border-bottom: 0; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100 min-h-screen text-gray-800">
    <header class="bg-white shadow-lg px-6 py-4 flex flex-col sm:flex-row justify-between items-center sticky top-0 z-10">
        <div class="flex items-center mb-3 sm:mb-0">
            <svg class="h-10 w-10 text-green-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.195-1.269-.545-1.782l-2.617-3.664M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.195-1.269.545-1.782l2.617-3.664m0 0L9.433 10.9A4.004 4.004 0 0111 6h2c1.455 0 2.723.738 3.486 1.89l.437.755M14.5 9.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" /></svg>
            <h1 class="text-2xl font-bold text-green-800">Manajemen Data Anak</h1>
        </div>
        <a href="admin.php" class="bg-green-500 text-white px-5 py-2 rounded-full hover:bg-green-600 transition duration-300 ease-in-out transform hover:scale-105 flex items-center"><i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard</a>
    </header>
    <main class="p-6 md:p-10 max-w-7xl mx-auto">
        <?php if (!empty($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-6 text-sm" role="alert"><strong class="font-bold mr-1">Sukses!</strong><span><?= htmlspecialchars($success_message) ?></span></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-6 text-sm" role="alert"><strong class="font-bold mr-1">Error!</strong><span><?= htmlspecialchars($error_message) ?></span></div>
        <?php endif; ?>
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <form method="POST" class="p-6 bg-gray-50">
                <input type="hidden" name="id" value="<?= $id ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">NIK Anak</label><input type="text" name="nik_anak" value="<?= htmlspecialchars($nik_anak) ?>" required class="w-full mt-1 px-3 py-2 border rounded"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Nama Anak</label><input type="text" name="nama_anak" value="<?= htmlspecialchars($nama_anak) ?>" required class="w-full mt-1 px-3 py-2 border rounded"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label><input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($tanggal_lahir) ?>" required class="w-full mt-1 px-3 py-2 border rounded"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label><select name="jenis_kelamin" class="w-full mt-1 px-3 py-2 border rounded"><option value="Laki-laki" <?= $jenis_kelamin == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option><option value="Perempuan" <?= $jenis_kelamin == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option></select></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu</label><input type="text" name="nama_ibu" value="<?= htmlspecialchars($nama_ibu) ?>" class="w-full mt-1 px-3 py-2 border rounded"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Nama Ayah</label><input type="text" name="nama_ayah" value="<?= htmlspecialchars($nama_ayah) ?>" class="w-full mt-1 px-3 py-2 border rounded"></div>
                    <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label><textarea name="alamat" rows="3" class="w-full mt-1 px-3 py-2 border rounded"><?= htmlspecialchars($alamat) ?></textarea></div>
                </div>
                <div class="mt-4 flex justify-end gap-2">
                    <?php if ($edit_mode): ?>
                        <a href="data_anak_admin.php" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">Batal</a>
                        <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Update</button>
                    <?php else: ?>
                        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Tambah</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <div class="bg-white shadow rounded-lg overflow-x-auto mt-6">
            <table class="min-w-full text-sm">
                <thead class="bg-green-600 text-white">
                    <tr><th class="px-4 py-3 text-left">No</th><th class="px-4 py-3 text-left">NIK</th><th class="px-4 py-3 text-left">Nama Anak</th><th class="px-4 py-3 text-left">Tgl. Lahir</th><th class="px-4 py-3 text-left">JK</th><th class="px-4 py-3 text-left">Ibu</th><th class="px-4 py-3 text-left">Ayah</th><th class="px-4 py-3 text-left">Alamat</th><th class="px-4 py-3 text-center">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2"><?= $no++ ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($row['nik_anak']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($row['nama_anak']) ?></td>
                        <td class="px-4 py-2"><?= formatTanggalIndo($row['tanggal_lahir']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($row['jenis_kelamin']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($row['nama_ibu']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($row['nama_ayah']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($row['alamat']) ?></td>
                        <td class="px-4 py-2 text-center space-x-2">
                            <a href="?edit=<?= $row['id'] ?>" class="text-yellow-600 hover:text-yellow-800"><i class="fas fa-edit"></i></a>
                            <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin hapus data ini?')" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; if (mysqli_num_rows($data) == 0): ?>
                    <tr><td colspan="9" class="text-center px-4 py-6 text-gray-500">Belum ada data anak.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    <footer class="text-center text-xs text-gray-500 mt-10">&copy; <?= date('Y') ?> Posyandu Bina Cita</footer>
</body>
</html>
