<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'koneksi.php';
$success = "";
$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama     = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    if (empty($nama) || empty($email) || empty($password)) { $error = "Semua kolom wajib diisi."; }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $error = "Format email tidak valid."; }
    elseif (substr($email, -10) !== '@gmail.com') { $error = "Email harus menggunakan domain @gmail.com."; }
    elseif (strlen($password) < 6) { $error = "Password minimal 6 karakter."; }
    else {
        $role = preg_match('/^\d+\.kader\..+@gmail\.com$/', $email) ? 'admin' : 'pengguna';
        $stmt_cek = mysqli_prepare($conn, "SELECT id FROM users WHERE email=?");
        if (!$stmt_cek) { $error = "Gagal mempersiapkan query SELECT: " . mysqli_error($conn); }
        else {
            mysqli_stmt_bind_param($stmt_cek, "s", $email);
            mysqli_stmt_execute($stmt_cek);
            mysqli_stmt_store_result($stmt_cek);
            if (mysqli_stmt_num_rows($stmt_cek) > 0) { $error = "Email sudah terdaftar!"; }
            else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($conn, "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
                if (!$stmt) { $error = "Gagal mempersiapkan query INSERT: " . mysqli_error($conn); }
                else {
                    mysqli_stmt_bind_param($stmt, "ssss", $nama, $email, $hashed, $role);
                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_close($stmt);
                        mysqli_stmt_close($stmt_cek);
                        mysqli_close($conn);
                        header("Location: login.php?status=registered");
                        exit;
                    } else { $error = "Gagal menyimpan data: " . mysqli_error($conn); }
                    mysqli_stmt_close($stmt);
                }
            }
            mysqli_stmt_close($stmt_cek);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Registrasi - Posyandu Bina Cita</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100 flex items-center justify-center min-h-screen py-10 px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-8 sm:p-10 rounded-xl shadow-2xl w-full max-w-md border border-green-200">
        <div class="flex justify-center mb-6">
            <svg class="h-16 w-16 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM12 10v2a4 4 0 00-4 4v2H6a2 2 0 01-2-2v-4a2 2 0 012-2h4z" />
            </svg>
        </div>
        <h2 class="text-3xl font-extrabold text-green-800 mb-4 text-center">Buat Akun Baru</h2>
        <p class="text-center text-gray-600 mb-8">Daftar sekarang untuk memulai perjalanan Anda dengan Posyandu Bina Cita</p>
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md relative mb-6 text-sm" role="alert">
                <strong class="font-bold mr-1">Ups!</strong><span class="block sm:inline"><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>
        <form method="POST" class="space-y-5">
            <div>
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-1">Username:</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username Anda" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 text-gray-900 placeholder-gray-400" />
            </div>
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email:</label>
                <input type="email" id="email" name="email" placeholder="contoh@gmail.com" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 text-gray-900 placeholder-gray-400" />
                <p class="mt-1 text-xs text-gray-500">Gunakan format email dengan @gmail.com. Untuk akun admin, gunakan format: [nomor].kader.[nama]@gmail.com</p>
            </div>
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password:</label>
                <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 text-gray-900 placeholder-gray-400" />
            </div>
            <button type="submit" class="w-full bg-green-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition duration-300 ease-in-out transform hover:scale-105 flex items-center justify-center">
                <i class="fas fa-user-plus mr-2"></i> Daftar Sekarang
            </button>
        </form>
        <p class="text-center text-sm text-gray-500 mt-6">
            Sudah punya akun? <a href="login.php" class="text-green-600 font-semibold hover:text-green-800 transition duration-300">Login di sini</a>
        </p>
    </div>
</body>
</html>
