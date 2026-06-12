<?php
session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../modules/binjas.php');
    exit;
}

$siswa_id    = (int) ($_POST['siswa_id']     ?? 0);
$tanggal     = $_POST['tanggal']             ?? '';
$pushup      = (int) ($_POST['pushup']       ?? 0);
$situp       = (int) ($_POST['situp']        ?? 0);
$lari_detik  = (int) ($_POST['lari_detik']   ?? 0);
$pullup      = (int) ($_POST['pullup']       ?? 0);
$renang      = (int) ($_POST['renang_detik'] ?? 0);
$user_id     = $_SESSION['user_id']          ?? 'admin';

if (!$siswa_id || empty($tanggal)) {
    $_SESSION['error'] = 'Siswa dan tanggal wajib diisi.';
    header('Location: ../modules/binjas.php');
    exit;
}

$stmt = $pdo->prepare('
    INSERT INTO binjas (siswa_id, tanggal, pushup, situp, lari_detik, pullup, renang_detik, user_id)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
');
$stmt->execute([$siswa_id, $tanggal, $pushup, $situp, $lari_detik, $pullup, $renang, $user_id]);

$_SESSION['success'] = 'Nilai Bina Jasmani berhasil disimpan.';
header('Location: ../modules/binjas.php');
exit;