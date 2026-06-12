<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: modules/dashboard.php');
    exit;
}

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SiswaTrack</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: sans-serif;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-card {
            background: #ffffff;
            border: 0.5px solid #e0e0e0;
            border-radius: 12px;
            padding: 2rem 2.5rem;
            width: 100%;
            max-width: 400px;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .login-logo h1 {
            font-size: 22px;
            font-weight: 500;
            color: #1a1a1a;
        }

        .login-logo p {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            color: #374151;
            margin-bottom: 6px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            border: 0.5px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            color: #1a1a1a;
            background: #fff;
            transition: border-color .15s;
        }

        .form-group input:focus {
            border-color: #378ADD;
            box-shadow: 0 0 0 3px rgba(55,138,221,0.1);
        }

        .btn-login {
            width: 100%;
            padding: 10px;
            background: #378ADD;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 0.5rem;
            transition: background .15s;
        }

        .btn-login:hover {
            background: #185FA5;
        }

        .alert-error {
            background: #FCEBEB;
            color: #791F1F;
            border: 0.5px solid #F09595;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 1rem;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-logo">
        <h1>SiswaTrack</h1>
        <p>Sistem Monitoring Kegiatan Siswa</p>
    </div>

    <?php if ($error): ?>
        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="actions/proses_login.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                placeholder="Masukkan username"
                required
                autocomplete="username"
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Masukkan password"
                required
                autocomplete="current-password"
            >
        </div>

        <button type="submit" class="btn-login">Masuk</button>
    </form>

    <div class="login-footer">
        SiswaTrack &copy; <?= date('Y') ?>
    </div>
</div>

</body>
</html>