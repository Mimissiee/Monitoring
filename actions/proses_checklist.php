<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die('Akses ditolak! Silakan login terlebih dahulu.');
}

require_once '../config/koneksi.php';

$redirect = '../modules/operasional/pasca_ops.php';

// HAPUS ALAT
if (isset($_GET['hapus'])) {
    $id     = (int) $_GET['hapus'];
    $ops_id = (int) ($_GET['ops_id'] ?? 0);
    $pdo->prepare('DELETE FROM checklist_alat WHERE id = ?')->execute([$id]);
    $_SESSION['success'] = 'Alat berhasil dihapus.';
    header('Location: ' . $redirect);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $redirect);
    exit;
}

$aksi = $_POST['aksi'] ?? 'tambah';

// UPDATE KONDISI
if ($aksi === 'update') {
    $kondisi_list = $_POST['kondisi'] ?? [];
    $allowed      = ['layak', 'tidak_layak', 'butuh_perbaikan'];

    $upd = $pdo->prepare('UPDATE checklist_alat SET kondisi = ? WHERE id = ?');
    foreach ($kondisi_list as $id => $kondisi) {
        if (in_array($kondisi, $allowed)) {
            $upd->execute([$kondisi, (int)$id]);
        }
    }

    $_SESSION['success'] = 'Kondisi alat berhasil disimpan.';
    header('Location: ' . $redirect);
    exit;
}

// TAMBAH ALAT
$ops_id     = (int) ($_POST['operasional_id'] ?? 0);
$nama_alat  = trim($_POST['nama_alat']        ?? '');
$jenis      = $_POST['jenis']                 ?? 'regu';
$kondisi    = $_POST['kondisi']               ?? 'layak';
$keterangan = trim($_POST['keterangan']       ?? '');

if (empty($nama_alat) || !$ops_id) {
    $_SESSION['error'] = 'Nama alat wajib diisi.';
    header('Location: ' . $redirect);
    exit;
}

$allowed_kondisi = ['layak', 'tidak_layak', 'butuh_perbaikan'];
if (!in_array($kondisi, $allowed_kondisi)) $kondisi = 'layak';

$pdo->prepare('INSERT INTO checklist_alat (operasional_id, nama_alat, jenis, kondisi, keterangan) VALUES (?, ?, ?, ?, ?)')
    ->execute([$ops_id, $nama_alat, $jenis, $kondisi, $keterangan]);

$_SESSION['success'] = 'Alat berhasil ditambahkan ke checklist.';
header('Location: ' . $redirect);
exit;