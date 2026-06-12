<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die('Akses ditolak! Silakan login terlebih dahulu.');
}

require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../modules/operasional/ops.php');
    exit;
}

$ops_id = (int) ($_POST['operasional_id'] ?? 0);

if (!$ops_id || empty($_FILES['file_laporan']['name'])) {
    $_SESSION['error'] = 'File laporan wajib dipilih.';
    header('Location: ../modules/operasional/ops.php');
    exit;
}

$file     = $_FILES['file_laporan'];
$ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if ($ekstensi !== 'pdf') {
    $_SESSION['error'] = 'File harus berformat PDF.';
    header('Location: ../modules/operasional/ops.php');
    exit;
}

if ($file['size'] > 5 * 1024 * 1024) {
    $_SESSION['error'] = 'Ukuran file maksimal 5MB.';
    header('Location: ../modules/operasional/ops.php');
    exit;
}

$nama_file = time() . '_laporan_' . $ops_id . '.pdf';
move_uploaded_file($file['tmp_name'], '../uploads/operasional/' . $nama_file);

$pdo->prepare('UPDATE operasional SET file_laporan = ? WHERE id = ?')
    ->execute([$nama_file, $ops_id]);

$_SESSION['success'] = 'Laporan berhasil diupload.';
header('Location: ../modules/operasional/ops.php');
exit;