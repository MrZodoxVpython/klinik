<?php
require_once 'auth.php';
require_once 'koneksi.php';
require_role('admin');
send_security_headers();

function formatTanggalIndo(string $tanggal): string
{
    if ($tanggal === '' || $tanggal === '0000-00-00') {
        return '-';
    }
    $bulan = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
    $pecah = explode('-', $tanggal);
    return count($pecah) === 3 ? $pecah[2] . ' ' . ($bulan[$pecah[1]] ?? '') . ' ' . $pecah[0] : '-';
}

function parentExists(mysqli $conn, int $userId): bool
{
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE id = ? AND role = 'pengguna'");
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $exists = mysqli_num_rows($result) === 1;
    mysqli_stmt_close($stmt);
    return $exists;
}

$successMessage = isset($_GET['status']) && $_GET['status'] === 'deleted' ? 'Data anak berhasil dihapus.' : '';
$errorMessage = '';
$editMode = false;
$id = 0;
$nikAnak = $namaAnak = $tglLahir = $jenisKelamin = $namaIbu = $namaAyah = $alamat = '';
$userId = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'delete') {
        $deleteId = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($deleteId === false) {
            $errorMessage = 'Data anak tidak valid.';
        } else {
            $stmt = mysqli_prepare($conn, 'DELETE FROM data_anak WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'i', $deleteId);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                redirect('data_anak_admin.php?status=deleted');
            }
            $errorMessage = mysqli_errno($conn) === 1451
                ? 'Data anak tidak dapat dihapus karena masih memiliki riwayat imunisasi.'
                : 'Data anak gagal dihapus. Silakan coba kembali.';
            mysqli_stmt_close($stmt);
        }
    }

    if ($action === 'save') {
        $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) ?: 0;
        $nikAnak = trim((string) ($_POST['nik_anak'] ?? ''));
        $namaAnak = trim((string) ($_POST['nama_anak'] ?? ''));
        $tglLahir = (string) ($_POST['tgl_lahir'] ?? '');
        $jenisKelamin = (string) ($_POST['jenis_kelamin'] ?? '');
        $namaIbu = trim((string) ($_POST['nama_ibu'] ?? ''));
        $namaAyah = trim((string) ($_POST['nama_ayah'] ?? ''));
        $alamat = trim((string) ($_POST['alamat'] ?? ''));
        $userId = filter_var($_POST['user_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0;

        if (!preg_match('/^\d{16}$/', $nikAnak)) {
            $errorMessage = 'NIK anak harus terdiri dari tepat 16 angka.';
        } elseif ($namaAnak === '' || $namaIbu === '' || $alamat === '') {
            $errorMessage = 'Nama anak, nama ibu, dan alamat wajib diisi.';
        } elseif (!is_valid_date($tglLahir) || $tglLahir > date('Y-m-d')) {
            $errorMessage = 'Tanggal lahir tidak valid atau berada di masa depan.';
        } elseif (!in_array($jenisKelamin, ['Laki-laki', 'Perempuan'], true)) {
            $errorMessage = 'Jenis kelamin tidak valid.';
        } elseif (!$userId || !parentExists($conn, $userId)) {
            $errorMessage = 'Pilih akun orang tua yang valid.';
        } elseif ($id > 0) {
            $stmt = mysqli_prepare($conn, 'UPDATE data_anak SET nik_anak = ?, nama_anak = ?, tgl_lahir = ?, jenis_kelamin = ?, nama_ibu = ?, nama_ayah = ?, alamat = ?, user_id = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'sssssssii', $nikAnak, $namaAnak, $tglLahir, $jenisKelamin, $namaIbu, $namaAyah, $alamat, $userId, $id);
            $saved = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            if ($saved) {
                $successMessage = 'Data anak berhasil diperbarui.';
                $editMode = false;
                $id = 0;
                $nikAnak = $namaAnak = $tglLahir = $jenisKelamin = $namaIbu = $namaAyah = $alamat = '';
                $userId = 0;
            } else {
                $errorMessage = mysqli_errno($conn) === 1062 ? 'NIK anak sudah terdaftar.' : 'Data anak gagal diperbarui.';
                $editMode = true;
            }
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO data_anak (nik_anak, nama_anak, tgl_lahir, jenis_kelamin, nama_ibu, nama_ayah, alamat, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'sssssssi', $nikAnak, $namaAnak, $tglLahir, $jenisKelamin, $namaIbu, $namaAyah, $alamat, $userId);
            $saved = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            if ($saved) {
                $successMessage = 'Data anak berhasil ditambahkan.';
                $nikAnak = $namaAnak = $tglLahir = $jenisKelamin = $namaIbu = $namaAyah = $alamat = '';
                $userId = 0;
            } else {
                $errorMessage = mysqli_errno($conn) === 1062 ? 'NIK anak sudah terdaftar.' : 'Data anak gagal ditambahkan.';
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit'])) {
    $editId = filter_var($_GET['edit'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($editId === false) {
        $errorMessage = 'Data anak tidak valid.';
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT * FROM data_anak WHERE id = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 'i', $editId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        if ($row) {
            $id = (int) $row['id'];
            $nikAnak = $row['nik_anak'];
            $namaAnak = $row['nama_anak'];
            $tglLahir = $row['tgl_lahir'];
            $jenisKelamin = $row['jenis_kelamin'] ?? '';
            $namaIbu = $row['nama_ibu'];
            $namaAyah = $row['nama_ayah'];
            $alamat = $row['alamat'];
            $userId = (int) $row['user_id'];
            $editMode = true;
        } else {
            $errorMessage = 'Data anak tidak ditemukan.';
        }
    }
}

$parents = mysqli_query($conn, "SELECT id, nama, email FROM users WHERE role = 'pengguna' ORDER BY nama ASC");
$data = mysqli_query($conn, 'SELECT da.*, u.nama AS nama_pengguna FROM data_anak da LEFT JOIN users u ON u.id = da.user_id ORDER BY da.nama_anak ASC');
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
        @media screen and (max-width: 768px) { table { border: 0; } table thead { display: none; } table tr { display: block; margin-bottom: .825em; border: 1px solid #e2e8f0; border-radius: .75rem; background: #fff; } table td { display: block; text-align: right; border-bottom: 1px dotted #cbd5e0; padding: .625rem 1rem .625rem 50%; position: relative; } table td::before { content: attr(data-label); position: absolute; left: 0; width: 45%; padding-left: 1rem; font-weight: 600; text-align: left; color: #4a5568; } }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100 min-h-screen text-gray-800">
    <header class="bg-white shadow-lg px-6 py-4 flex flex-col sm:flex-row justify-between items-center sticky top-0 z-10">
        <h1 class="text-2xl font-bold text-green-800">Manajemen Data Anak</h1>
        <a href="admin.php" class="bg-green-500 text-white px-5 py-2 rounded-full hover:bg-green-600 transition flex items-center"><i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard</a>
    </header>
    <main class="p-6 md:p-10 max-w-7xl mx-auto">
        <?php if ($successMessage !== ''): ?><div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6"><strong>Sukses!</strong> <?= htmlspecialchars($successMessage) ?></div><?php endif; ?>
        <?php if ($errorMessage !== ''): ?><div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6"><strong>Gagal!</strong> <?= htmlspecialchars($errorMessage) ?></div><?php endif; ?>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <form method="POST" class="p-6 bg-gray-50">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= $id ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div><label class="block text-sm font-medium mb-1">NIK Anak</label><input type="text" name="nik_anak" value="<?= htmlspecialchars($nikAnak) ?>" inputmode="numeric" pattern="[0-9]{16}" maxlength="16" required class="w-full px-3 py-2 border rounded"></div>
                    <div><label class="block text-sm font-medium mb-1">Nama Anak</label><input type="text" name="nama_anak" value="<?= htmlspecialchars($namaAnak) ?>" required class="w-full px-3 py-2 border rounded"></div>
                    <div><label class="block text-sm font-medium mb-1">Tanggal Lahir</label><input type="date" name="tgl_lahir" max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($tglLahir) ?>" required class="w-full px-3 py-2 border rounded"></div>
                    <div><label class="block text-sm font-medium mb-1">Jenis Kelamin</label><select name="jenis_kelamin" required class="w-full px-3 py-2 border rounded"><option value="">Pilih</option><option value="Laki-laki" <?= $jenisKelamin === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option><option value="Perempuan" <?= $jenisKelamin === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option></select></div>
                    <div><label class="block text-sm font-medium mb-1">Akun Orang Tua</label><select name="user_id" required class="w-full px-3 py-2 border rounded"><option value="">Pilih akun orang tua</option><?php while ($parent = mysqli_fetch_assoc($parents)): ?><option value="<?= (int) $parent['id'] ?>" <?= $userId === (int) $parent['id'] ? 'selected' : '' ?>><?= htmlspecialchars($parent['nama']) ?> — <?= htmlspecialchars($parent['email']) ?></option><?php endwhile; ?></select></div>
                    <div><label class="block text-sm font-medium mb-1">Nama Ibu</label><input type="text" name="nama_ibu" value="<?= htmlspecialchars($namaIbu) ?>" required class="w-full px-3 py-2 border rounded"></div>
                    <div><label class="block text-sm font-medium mb-1">Nama Ayah</label><input type="text" name="nama_ayah" value="<?= htmlspecialchars($namaAyah) ?>" class="w-full px-3 py-2 border rounded"></div>
                    <div class="md:col-span-2"><label class="block text-sm font-medium mb-1">Alamat</label><textarea name="alamat" rows="3" required class="w-full px-3 py-2 border rounded"><?= htmlspecialchars($alamat) ?></textarea></div>
                </div>
                <div class="mt-4 flex justify-end gap-2"><?php if ($editMode): ?><a href="data_anak_admin.php" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">Batal</a><?php endif; ?><button class="<?= $editMode ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-600 hover:bg-green-700' ?> text-white px-4 py-2 rounded"><?= $editMode ? 'Update' : 'Tambah' ?></button></div>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg overflow-x-auto mt-6">
            <table class="min-w-full text-sm"><thead class="bg-green-600 text-white"><tr><th class="px-4 py-3 text-left">No</th><th class="px-4 py-3 text-left">NIK</th><th class="px-4 py-3 text-left">Nama Anak</th><th class="px-4 py-3 text-left">Tanggal Lahir</th><th class="px-4 py-3 text-left">JK</th><th class="px-4 py-3 text-left">Orang Tua</th><th class="px-4 py-3 text-left">Alamat</th><th class="px-4 py-3 text-center">Aksi</th></tr></thead>
                <tbody class="divide-y divide-gray-200"><?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?><tr class="hover:bg-gray-50"><td data-label="No" class="px-4 py-2"><?= $no++ ?></td><td data-label="NIK" class="px-4 py-2"><?= htmlspecialchars($row['nik_anak']) ?></td><td data-label="Nama" class="px-4 py-2"><?= htmlspecialchars($row['nama_anak']) ?></td><td data-label="Tanggal Lahir" class="px-4 py-2"><?= formatTanggalIndo($row['tgl_lahir']) ?></td><td data-label="JK" class="px-4 py-2"><?= htmlspecialchars($row['jenis_kelamin'] ?? '-') ?></td><td data-label="Akun Orang Tua" class="px-4 py-2"><?= htmlspecialchars($row['nama_pengguna'] ?? 'Belum terhubung') ?></td><td data-label="Alamat" class="px-4 py-2"><?= htmlspecialchars($row['alamat']) ?></td><td data-label="Aksi" class="px-4 py-2 text-center"><a href="?edit=<?= (int) $row['id'] ?>" class="text-yellow-600 hover:text-yellow-800 mr-3">Edit</a><form method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><button class="text-red-600 hover:text-red-800">Hapus</button></form></td></tr><?php endwhile; ?><?php if (mysqli_num_rows($data) === 0): ?><tr><td colspan="8" class="text-center px-4 py-6 text-gray-500">Belum ada data anak.</td></tr><?php endif; ?></tbody>
            </table>
        </div>
    </main>
</body>
</html>
