<?php
require_once 'auth.php';
require_once 'koneksi.php';
require_role('admin');
verify_csrf();

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    redirect('imunisasi_admin.php?status=not_found');
}

$stmt = mysqli_prepare($conn, 'DELETE FROM imunisasi WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
$deleted = mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) === 1;
mysqli_stmt_close($stmt);

redirect('imunisasi_admin.php?status=' . ($deleted ? 'deleted' : 'delete_failed'));
