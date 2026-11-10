<?php
require_once 'auth.php';
require_once 'koneksi.php';
require_role('admin');
send_security_headers();

$errorMessage = '';
$successMessage = isset($_GET['status']) && $_GET['status'] === 'deleted' ? 'Data petugas berhasil dihapus.' : '';
$id = 0;
$nama = $jabatan = $kontak = '';
$editMode = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'delete') {
        $deleteId = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($deleteId === false) {
            $errorMessage = 'Data petugas tidak valid.';
        } else {
            $stmt = mysqli_prepare($conn, 'DELETE FROM petugas WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'i', $deleteId);
            $deleted = mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) === 1;
            mysqli_stmt_close($stmt);
            if ($deleted) {
                redirect('petugas_admin.php?status=deleted');
            }
            $errorMessage = 'Data petugas gagal dihapus.';
        }
    }
    if ($action === 'save') {
        $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) ?: 0;
        $editMode = $id > 0;
        $nama = trim((string) ($_POST['nama'] ?? ''));
        $jabatan = trim((string) ($_POST['jabatan'] ?? ''));
        $kontak = trim((string) ($_POST['kontak'] ?? ''));
        if ($nama === '' || strlen($nama) > 100 || $jabatan === '' || strlen($jabatan) > 100 || strlen($kontak) > 50) {
            $errorMessage = 'Nama dan jabatan wajib diisi, dengan panjang data yang valid.';
        } else {
            if ($id > 0) {
                $stmt = mysqli_prepare($conn, 'UPDATE petugas SET nama = ?, jabatan = ?, kontak = ? WHERE id = ?');
                mysqli_stmt_bind_param($stmt, 'sssi', $nama, $jabatan, $kontak, $id);
            } else {
                $stmt = mysqli_prepare($conn, 'INSERT INTO petugas (nama, jabatan, kontak) VALUES (?, ?, ?)');
                mysqli_stmt_bind_param($stmt, 'sss', $nama, $jabatan, $kontak);
            }
            $saved = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            if ($saved) {
                $successMessage = $id > 0 ? 'Data petugas berhasil diperbarui.' : 'Data petugas berhasil ditambahkan.';
                $id = 0;
                $nama = $jabatan = $kontak = '';
                $editMode = false;
            } else {
                $errorMessage = 'Data petugas gagal disimpan.';
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit'])) {
    $editId = filter_var($_GET['edit'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($editId === false) {
        $errorMessage = 'Data petugas tidak valid.';
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT * FROM petugas WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $editId);
        mysqli_stmt_execute($stmt);
        $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        if ($data) {
            $id = (int) $data['id'];
            $nama = $data['nama'];
            $jabatan = $data['jabatan'];
            $kontak = $data['kontak'];
            $editMode = true;
        } else {
            $errorMessage = 'Data petugas tidak ditemukan.';
        }
    }
}

$petugas = mysqli_query($conn, 'SELECT * FROM petugas ORDER BY nama ASC');
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Kelola Petugas Posyandu</title><link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"></head>
<body class="bg-green-50 min-h-screen text-gray-800">
  <main class="max-w-5xl mx-auto p-6"><div class="flex justify-between items-center mb-6"><h1 class="text-2xl font-bold text-green-800">Kelola Petugas Posyandu</h1><a href="admin.php" class="text-green-700 hover:underline">Kembali ke Dashboard</a></div>
    <?php if ($successMessage !== ''): ?><div class="mb-5 rounded bg-green-100 p-3 text-green-700"><?= htmlspecialchars($successMessage) ?></div><?php endif; ?>
    <?php if ($errorMessage !== ''): ?><div class="mb-5 rounded bg-red-100 p-3 text-red-700"><?= htmlspecialchars($errorMessage) ?></div><?php endif; ?>
    <form method="POST" class="mb-8 rounded-lg bg-white p-6 shadow"><h2 class="mb-4 text-lg font-semibold"><?= $editMode ? 'Edit Petugas' : 'Tambah Petugas' ?></h2><?= csrf_field() ?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= $id ?>"><div class="grid gap-4 md:grid-cols-3"><div><label class="mb-1 block text-sm font-medium">Nama</label><input name="nama" maxlength="100" required value="<?= htmlspecialchars($nama) ?>" class="w-full rounded border px-3 py-2"></div><div><label class="mb-1 block text-sm font-medium">Jabatan</label><input name="jabatan" maxlength="100" required value="<?= htmlspecialchars($jabatan) ?>" class="w-full rounded border px-3 py-2"></div><div><label class="mb-1 block text-sm font-medium">Kontak</label><input name="kontak" maxlength="50" value="<?= htmlspecialchars($kontak) ?>" class="w-full rounded border px-3 py-2"></div></div><div class="mt-4"><button class="rounded bg-green-600 px-5 py-2 text-white hover:bg-green-700"><?= $editMode ? 'Update' : 'Simpan' ?></button><?php if ($editMode): ?><a href="petugas_admin.php" class="ml-3 text-sm text-green-700 hover:underline">Batal</a><?php endif; ?></div></form>
    <div class="overflow-x-auto rounded-lg bg-white shadow"><table class="min-w-full text-sm"><thead class="bg-green-600 text-white"><tr><th class="px-4 py-3 text-left">Nama</th><th class="px-4 py-3 text-left">Jabatan</th><th class="px-4 py-3 text-left">Kontak</th><th class="px-4 py-3 text-right">Aksi</th></tr></thead><tbody><?php while ($row = mysqli_fetch_assoc($petugas)): ?><tr class="border-b"><td class="px-4 py-3"><?= htmlspecialchars($row['nama']) ?></td><td class="px-4 py-3"><?= htmlspecialchars($row['jabatan']) ?></td><td class="px-4 py-3"><?= htmlspecialchars($row['kontak']) ?></td><td class="px-4 py-3 text-right"><a href="?edit=<?= (int) $row['id'] ?>" class="mr-3 text-blue-600 hover:underline">Edit</a><form method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus petugas ini?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><button class="text-red-600 hover:underline">Hapus</button></form></td></tr><?php endwhile; ?></tbody></table></div>
  </main>
</body>
</html>
