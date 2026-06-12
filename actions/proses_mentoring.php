<?php
session_start();
require_once '../config/koneksi.php';

// HAPUS
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];

    $r = $pdo->prepare('SELECT file_bahan_ajar FROM mentoring WHERE id = ?');
    $r->execute([$id]);
    $file = $r->fetchColumn();

    if ($file && file_exists('../uploads/materi/' . $file)) {
        unlink('../uploads/materi/' . $file);
    }

    $pdo->prepare('DELETE FROM mentoring WHERE id = ?')->execute([$id]);
    $_SESSION['success'] = 'Sesi mentoring berhasil dihapus.';
    header('Location: ../modules/mentoring.php');
    exit;
}

// TAMBAH
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../modules/mentoring.php');
    exit;
}

$tanggal           = $_POST['tanggal']            ?? '';
$judul_materi      = trim($_POST['judul_materi']  ?? '');
$pengisi           = trim($_POST['pengisi']        ?? '');
$kebutuhan         = trim($_POST['kebutuhan_logistik'] ?? '');
$user_id           = $_SESSION['user_id']         ?? 'admin';

if (empty($tanggal) || empty($judul_materi) || empty($pengisi)) {
    $_SESSION['error'] = 'Tanggal, judul materi, dan pengisi wajib diisi.';
    header('Location: ../modules/mentoring.php');
    exit;
}

// Upload file
$nama_file = null;
if (!empty($_FILES['file_bahan_ajar']['name'])) {
    $file     = $_FILES['file_bahan_ajar'];
    $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($ekstensi !== 'pdf') {
        $_SESSION['error'] = 'File harus berformat PDF.';
        header('Location: ../modules/mentoring.php');
        exit;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        $_SESSION['error'] = 'Ukuran file maksimal 5MB.';
        header('Location: ../modules/mentoring.php');
        exit;
    }

    $nama_file = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $file['name']);
    move_uploaded_file($file['tmp_name'], '../uploads/materi/' . $nama_file);
}

$stmt = $pdo->prepare('INSERT INTO mentoring (tanggal, judul_materi, pengisi, file_bahan_ajar, kebutuhan_logistik, user_id) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->execute([$tanggal, $judul_materi, $pengisi, $nama_file, $kebutuhan, $user_id]);

$_SESSION['success'] = 'Sesi mentoring berhasil disimpan.';
header('Location: ../modules/mentoring.php');
exit;