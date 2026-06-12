<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

$akun = [
    'siswa' => ['password' => 'siswa123', 'role' => 'admin',      'nama' => 'Siswa'],
    'admin' => ['password' => 'admin',    'role' => 'super_admin', 'nama' => 'Super Admin'],
];

if (isset($akun[$username]) && $akun[$username]['password'] === $password) {
    $_SESSION['user_id'] = $username;
    $_SESSION['nama']    = $akun[$username]['nama'];
    $_SESSION['role']    = $akun[$username]['role'];
    header('Location: ../modules/dashboard.php');
    exit;
}

$_SESSION['error'] = 'Username atau password salah.';
header('Location: ../login.php');
exit;