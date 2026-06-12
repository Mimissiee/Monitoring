<?php
$halaman = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-logo">
        <span class="logo-title">SiswaTrack</span>
        <span class="logo-sub">Monitoring Kegiatan</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Utama</div>
        <a href="/siswatrack/modules/dashboard.php"
           class="nav-item <?= $halaman === 'dashboard.php' ? 'active' : '' ?>">
            <span class="nav-icon">▪</span> Dasbor
        </a>
        <a href="/siswatrack/modules/timeline.php"
           class="nav-item <?= $halaman === 'timeline.php' ? 'active' : '' ?>">
            <span class="nav-icon">▪</span> Timeline
        </a>

        <div class="nav-section">Modul</div>
        <a href="/siswatrack/modules/rabuan.php"
           class="nav-item <?= $halaman === 'rabuan.php' ? 'active' : '' ?>">
            <span class="nav-icon">▪</span> Rabuan
        </a>
        <a href="/siswatrack/modules/mentoring.php"
           class="nav-item <?= $halaman === 'mentoring.php' ? 'active' : '' ?>">
            <span class="nav-icon">▪</span> Mentoring
        </a>
        <a href="/siswatrack/modules/operasional/pra_ops.php"
           class="nav-item <?= in_array($halaman, ['pra_ops.php','ops.php','pasca_ops.php']) ? 'active' : '' ?>">
            <span class="nav-icon">▪</span> Operasional
        </a>
        <a href="/siswatrack/modules/binjas.php"
           class="nav-item <?= $halaman === 'binjas.php' ? 'active' : '' ?>">
            <span class="nav-icon">▪</span> Bina Jasmani
        </a>
        <a href="/siswatrack/modules/kehadiran.php"
           class="nav-item <?= $halaman === 'kehadiran.php' ? 'active' : '' ?>">
            <span class="nav-icon">▪</span> Kehadiran
        </a>
    </nav>

    <div class="sidebar-bottom">
        <div class="sidebar-user">
            <div class="user-avatar">
                <?= strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 2)) ?>
            </div>
            <div class="user-info">
                <span class="user-nama"><?= htmlspecialchars($_SESSION['nama'] ?? '') ?></span>
                <span class="user-role"><?= htmlspecialchars($_SESSION['role'] ?? '') ?></span>
            </div>
        </div>
        <a href="/siswatrack/logout.php" class="btn-logout">
            Keluar dari akun
        </a>
    </div>
</div>