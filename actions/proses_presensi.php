<?php
session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../modules/kehadiran.php');
    exit;
}

$jenis       = $_POST['jenis_kegiatan'] ?? '';
$referensi   = (int) ($_POST['referensi_id'] ?? 0);
$tanggal     = $_POST['tanggal'] ?? date('Y-m-d');
$status_list = $_POST['status'] ?? [];
$user_id     = $_SESSION['user_id'] ?? 'admin';

if (empty($jenis) || empty($referensi) || empty($status_list)) {
    $_SESSION['error'] = 'Data tidak lengkap.';
    header('Location: ../modules/kehadiran.php?jenis=' . $jenis);
    exit;
}

// Hapus data lama lalu insert ulang
$del = $pdo->prepare('DELETE FROM kehadiran WHERE jenis_kegiatan = ? AND referensi_id = ?');
$del->execute([$jenis, $referensi]);

$ins = $pdo->prepare('INSERT INTO kehadiran (siswa_id, jenis_kegiatan, referensi_id, status, tanggal, user_id) VALUES (?, ?, ?, ?, ?, ?)');

foreach ($status_list as $siswa_id => $status) {
    $allowed = ['hadir', 'izin', 'sakit', 'absen'];
    if (!in_array($status, $allowed)) continue;
    $ins->execute([(int)$siswa_id, $jenis, $referensi, $status, $tanggal, $user_id]);
}

$_SESSION['success'] = 'Kehadiran berhasil disimpan.';
header('Location: ../modules/kehadiran.php?jenis=' . $jenis . '&ref_id=' . $referensi);
exit;