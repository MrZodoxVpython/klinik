<?php
// Koneksi database untuk E-Posyandu Bina Cita
$host = "localhost";
$user = "benjamin";
$pass = "wickman";
$db   = "posyandu_db";

// Membuat koneksi ke database
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>