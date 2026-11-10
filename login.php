<?php
session_start();
include 'koneksi.php';

$error = "";
$success_message = "";

// Ambil pesan sukses dari register
if (isset($_GET['status']) && $_GET['status'] === 'registered') {
    $success_message = "Akun berhasil dibuat! Silakan login.";
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Email dan Password wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, nama, password, role FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($data && password_verify($password, $data['password'])) {
            $role = $data['role'];

            if ($role === 'admin' && !preg_match('/^\d+\.kader\.[a-zA-Z]+@gmail\.com$/', $email)) {
                $error = "Format email admin tidak sesuai. Contoh: 01.kader.nama@gmail.com";
            } else {
                $_SESSION['user_id'] = $data['id'];
                $_SESSION['nama']    = $data['nama'];
                $_SESSION['role']    = $role;

                if ($role === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            }
        } else {
            $error = "Email atau password salah!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login - Posyandu Bina Cita</title>
    <link rel="SHORTCUT ICON" href="https://storage.pusdokkes.polri.go.id/pusdokkes/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100 flex items-center justify-center min-h-screen py-10 px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-8 sm:p-10 rounded-xl shadow-2xl w-full max-w-md border border-green-200">
        <div class="flex justify-center mb-6">
            <img src="https://1.bp.blogspot.com/-2b8VcGpaYYk/Uk3-rt6R8rI/AAAAAAAACSE/HkFPCtCWoL4/s400-rj-v1-c0Xffffff-e30/K3.png"
                 alt="Logo Posyandu"
                 class="w-24 h-24 object-cover rounded-full border-4 border-green-400 shadow-md" />
        </div>
        <h2 class="text-3xl font-extrabold text-green-800 mb-4 text-center">Selamat Datang</h2>
        <p class="text-center text-gray-600 mb-6">Masuk ke akun Anda untuk melanjutkan</p>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-6 text-sm" role="alert">
                <strong class="font-bold mr-1">Gagal!</strong>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php elseif (!empty($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-6 text-sm" role="alert">
                <strong class="font-bold mr-1">Sukses!</strong>
                <span><?= htmlspecialchars($success_message) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5" novalidate>
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email:</label>
                <input type="email" id="email" name="email" placeholder="Masukkan email Anda" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 text-gray-900 placeholder-gray-400" />
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password:</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 text-gray-900 placeholder-gray-400" />
            </div>

            <button type="submit"
                class="w-full bg-green-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition duration-300 ease-in-out transform hover:scale-105 flex items-center justify-center">
                <i class="fas fa-sign-in-alt mr-2"></i> Masuk
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Belum punya akun? <a href="register.php" class="text-green-600 font-semibold hover:text-green-800 transition duration-300">Daftar di sini</a>
        </p>
    </div>
</body>
</html>