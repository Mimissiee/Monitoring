<?php
session_start();
require_once '../config/koneksi.php';

// HAPUS
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];

    $r = $pdo->prepare('SELECT file_notulensi FROM rabuan WHERE id = ?');
    $r->execute([$id]);
    $file = $r->fetchColumn();

    if ($file && file_exists('../uploads/notulensi/' . $file)) {
        unlink('../uploads/notulensi/' . $file);
    }

    $pdo->prepare('DELETE FROM rabuan WHERE id = ?')->execute([$id]);
    $_SESSION['success'] = 'Rapat berhasil dihapus.';
    header('Location: ../modules/rabuan.php');
    exit;
}

// TAMBAH
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../modules/rabuan.php');
    exit;
}

$tanggal = $_POST['tanggal'] ?? '';
$agenda  = trim($_POST['agenda'] ?? '');
$user_id = $_SESSION['user_id'] ?? 'admin';

if (empty($tanggal) || empty($agenda)) {
    $_SESSION['error'] = 'Tanggal dan agenda wajib diisi.';
    header('Location: ../modules/rabuan.php');
    exit;
}

// Upload file
$nama_file = null;
if (!empty($_FILES['file_notulensi']['name'])) {
    $file     = $_FILES['file_notulensi'];
    $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($ekstensi !== 'pdf') {
        $_SESSION['error'] = 'File harus berformat PDF.';
        header('Location: ../modules/rabuan.php');
        exit;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        $_SESSION['error'] = 'Ukuran file maksimal 5MB.';
        header('Location: ../modules/rabuan.php');
        exit;
    }

    $nama_file = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $file['name']);
    move_uploaded_file($file['tmp_name'], '../uploads/notulensi/' . $nama_file);
}

$stmt = $pdo->prepare('INSERT INTO rabuan (tanggal, agenda, file_notulensi, user_id) VALUES (?, ?, ?, ?)');
$stmt->execute([$tanggal, $agenda, $nama_file, $user_id]);

$_SESSION['success'] = 'Rapat berhasil disimpan.';
header('Location: ../modules/rabuan.php');
exit;