<?php
session_start();

// Cek login manual tanpa auth_check
if (!isset($_SESSION['user_id'])) {
    die('Akses ditolak! Silakan login terlebih dahulu.');
}

require_once '../config/koneksi.php';

$redirect = '../modules/operasional/pra_ops.php';

// HAPUS OPERASIONAL
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $pdo->prepare('DELETE FROM operasional WHERE id = ?')->execute([$id]);
    $_SESSION['success'] = 'Operasional berhasil dihapus.';
    header('Location: ' . $redirect);
    exit;
}

// HAPUS PERBEKALAN
if (isset($_GET['hapus_perbekalan'])) {
    $id     = (int) $_GET['hapus_perbekalan'];
    $pdo->prepare('DELETE FROM operasional_perbekalan WHERE id = ?')->execute([$id]);
    $_SESSION['success'] = 'Item perbekalan dihapus.';
    header('Location: ' . $redirect);
    exit;
}

// UBAH STATUS
if (isset($_GET['ubah_status'])) {
    $id      = (int) $_GET['ubah_status'];
    $status  = $_GET['status'] ?? 'pra';
    $allowed = ['pra', 'berjalan', 'selesai'];
    if (in_array($status, $allowed)) {
        $pdo->prepare('UPDATE operasional SET status = ? WHERE id = ?')->execute([$status, $id]);
        $_SESSION['success'] = 'Status operasional diperbarui.';
    }
    header('Location: ../modules/operasional/ops.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $redirect);
    exit;
}

$aksi    = $_POST['aksi']       ?? '';
$user_id = 1; // pakai id 1 karena login masih dummy

// BUAT OPERASIONAL BARU
if ($aksi === 'buat') {
    $nama            = trim($_POST['nama_kegiatan']   ?? '');
    $tanggal_mulai   = $_POST['tanggal_mulai']        ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai']      ?? '';

    if (empty($nama) || empty($tanggal_mulai) || empty($tanggal_selesai)) {
        $_SESSION['error'] = 'Semua field wajib diisi.';
        header('Location: ' . $redirect);
        exit;
    }

    $pdo->prepare('INSERT INTO operasional (nama_kegiatan, tanggal_mulai, tanggal_selesai, status, user_id) VALUES (?, ?, ?, "pra", ?)')
        ->execute([$nama, $tanggal_mulai, $tanggal_selesai, $user_id]);

    $_SESSION['success'] = 'Operasional berhasil dibuat.';
    header('Location: ' . $redirect);
    exit;
}

// SIMPAN PESERTA
if ($aksi === 'peserta') {
    $ops_id      = (int) ($_POST['operasional_id'] ?? 0);
    $status_list = $_POST['status'] ?? [];

    $pdo->prepare('DELETE FROM operasional_peserta WHERE operasional_id = ?')->execute([$ops_id]);

    $ins = $pdo->prepare('INSERT INTO operasional_peserta (operasional_id, siswa_id, status_kehadiran) VALUES (?, ?, ?)');
    foreach ($status_list as $siswa_id => $status) {
        $allowed = ['hadir', 'izin', 'sakit', 'absen'];
        if (!in_array($status, $allowed)) continue;
        $ins->execute([$ops_id, (int)$siswa_id, $status]);
    }

    $_SESSION['success'] = 'Data peserta berhasil disimpan.';
    header('Location: ' . $redirect);
    exit;
}

// TAMBAH PERBEKALAN
if ($aksi === 'perbekalan') {
    $ops_id     = (int) ($_POST['operasional_id'] ?? 0);
    $nama_item  = trim($_POST['nama_item']        ?? '');
    $jenis      = $_POST['jenis']                 ?? 'regu';
    $keterangan = trim($_POST['keterangan']       ?? '');

    if (empty($nama_item)) {
        $_SESSION['error'] = 'Nama item wajib diisi.';
        header('Location: ' . $redirect);
        exit;
    }

    $pdo->prepare('INSERT INTO operasional_perbekalan (operasional_id, nama_item, jenis, keterangan) VALUES (?, ?, ?, ?)')
        ->execute([$ops_id, $nama_item, $jenis, $keterangan]);

    $_SESSION['success'] = 'Perbekalan berhasil ditambahkan.';
    header('Location: ' . $redirect);
    exit;
}

header('Location: ' . $redirect);
exit;