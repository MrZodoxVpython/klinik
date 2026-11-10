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

$id = intval($_GET['id']);
mysqli_query($conn, "DELETE FROM imunisasi WHERE id = '$id'");

header("Location: imunisasi_admin.php?status=deleted");
exit;
?>