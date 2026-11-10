<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
$nama_admin = $_SESSION['nama'];
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
$query_laporan = "
    SELECT DISTINCT
        da.id,
        da.nik_anak,
        da.nama_anak,
        da.tgl_lahir,
        da.nama_ibu,
        da.nama_ayah,
        da.alamat
    FROM
        data_anak da
    INNER JOIN
        imunisasi i ON da.id = i.anak_id
";
$where_clauses = [];
$param_types = '';
$param_values = [];
if (!empty($filter_bulan)) {
    $where_clauses[] = "MONTH(i.tanggal) = ?";
    $param_types .= 'i';
    $param_values[] = $filter_bulan;
}
if (!empty($filter_tahun)) {
    $where_clauses[] = "YEAR(i.tanggal) = ?";
    $param_types .= 'i';
    $param_values[] = $filter_tahun;
}
if (!empty($where_clauses)) {
    $query_laporan .= " WHERE " . implode(" AND ", $where_clauses);
}
$query_laporan .= " ORDER BY da.nama_anak ASC";
$stmt = mysqli_prepare($conn, $query_laporan);
if ($stmt === false) {
    die("Query preparation failed: " . htmlspecialchars(mysqli_error($conn)));
}
if (!empty($param_values)) {
    mysqli_stmt_bind_param($stmt, $param_types, ...$param_values);
}
mysqli_stmt_execute($stmt);
$result_laporan = mysqli_stmt_get_result($stmt);
if (!$result_laporan) {
    die("Query execution failed: " . htmlspecialchars(mysqli_error($conn)));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Imunisasi Anak - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .navbar-blur { backdrop-filter: blur(12px); background-color: rgba(255, 255, 255, 0.7); }
        @media print {
            .no-print { display: none !important; }
            body { background-color: #fff !important; color: #000 !important; margin: 0; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">
<header class="bg-white shadow-lg px-6 py-4 flex flex-col sm:flex-row justify-between items-center sticky top-0 z-10">
    <div class="flex items-center mb-3 sm:mb-0">
        <svg class="h-10 w-10 text-green-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.007 12.007 0 002.944 12c0 2.871.675 5.55 1.945 7.917v0C7.294 20.354 9.582 21 12 21c2.418 0 4.706-.646 6.111-2.083v0C21.325 17.55 22 14.871 22 12c0-3.097-.665-6.012-1.882-8.616z" /></svg>
        <h1 class="text-2xl font-bold text-green-800">Laporan Data Imunisasi</h1>
    </div>
    <div class="flex flex-col sm:flex-row gap-4 mt-3 sm:mt-0">
        <a href="admin.php" class="bg-green-500 text-white px-5 py-2 rounded-full hover:bg-green-600 transition duration-300 ease-in-out transform hover:scale-105 flex items-center"><i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard</a>
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300 ease-in-out transform hover:scale-105 flex items-center no-print"><i class="fas fa-print mr-2"></i> Cetak Laporan</button>
    </div>
</header>
<main class="p-6 md:p-10 max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-2xl border border-green-200 p-6 mb-8 no-print">
        <h2 class="text-xl font-semibold text-green-800 mb-4">Filter Laporan</h2>
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                <select name="bulan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    <option value="">Semua Bulan</option>
                    <?php for ($b = 1; $b <= 12; $b++): ?>
                        <option value="<?= $b ?>" <?= $filter_bulan == $b ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $b, 1)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <input type="number" name="tahun" value="<?= htmlspecialchars($filter_tahun) ?>" placeholder="Tahun" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" min="2000" max="2100">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-green-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-700 transition">Tampilkan</button>
            </div>
        </form>
    </div>
    <div class="overflow-x-auto bg-white rounded-xl shadow-2xl border border-green-200">
        <table class="min-w-full text-sm table-auto divide-y divide-gray-200">
            <thead class="bg-green-600 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">NIK Anak</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Nama Anak</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Tgl. Lahir</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Nama Ibu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Nama Ayah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Alamat</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                $no = 1;
                if (mysqli_num_rows($result_laporan) > 0) {
                    while ($row = mysqli_fetch_assoc($result_laporan)) {
                ?>
                <tr class="hover:bg-green-50 transition duration-150 ease-in-out">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $no++ ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($row['nik_anak']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($row['nama_anak']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars(date('d F Y', strtotime($row['tgl_lahir']))) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($row['nama_ibu']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($row['nama_ayah']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($row['alamat']) ?></td>
                </tr>
                <?php
                    }
                } else {
                ?>
                <tr>
                    <td colspan="7" class="px-6 py-8 whitespace-nowrap text-sm text-gray-500 text-center font-semibold">
                        <i class="fas fa-info-circle mr-2"></i> Tidak ada data anak yang melakukan imunisasi pada periode ini.
                    </td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</main>
<footer class="bg-white border-t border-green-200 py-6 no-print"><div class="text-center text-sm text-gray-600">&copy; <?= date('Y') ?> Posyandu Bina Cita. Semua Hak Dilindungi.</div></footer>
</body>
</html>
