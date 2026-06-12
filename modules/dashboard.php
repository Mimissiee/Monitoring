<?php
require_once '../includes/auth_check.php';
require_once '../config/koneksi.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main">
    <div class="topbar">
        <span class="topbar-title">Dasbor</span>
        <div class="topbar-right">
            <span class="topbar-date"><?= date('l, d F Y') ?></span>
        </div>
    </div>

    <div class="content">

        <?php
        $error   = $_SESSION['error']   ?? '';
        $success = $_SESSION['success'] ?? '';
        unset($_SESSION['error'], $_SESSION['success']);

        $total_siswa     = $pdo->query('SELECT COUNT(*) FROM siswa WHERE status_aktif = 1')->fetchColumn();
        $total_mentoring = $pdo->query('SELECT COUNT(*) FROM mentoring WHERE MONTH(tanggal) = MONTH(NOW())')->fetchColumn();
        $ops_berjalan    = $pdo->query("SELECT COUNT(*) FROM operasional WHERE status = 'berjalan'")->fetchColumn();
        $total_kehadiran = $pdo->query("SELECT COUNT(*) FROM kehadiran WHERE status = 'hadir'")->fetchColumn();
        $total_presensi  = $pdo->query('SELECT COUNT(*) FROM kehadiran')->fetchColumn();
        $pct_kehadiran   = $total_presensi > 0 ? round(($total_kehadiran / $total_presensi) * 100) : 0;
        ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- METRIC CARDS -->
        <div class="metrics">
    <div class="metric-card">
        <div class="metric-label">Total siswa</div>
        <div class="metric-val"><?= $total_siswa ?></div>
        <div class="metric-divider"></div>
        <div class="metric-sub">Aktif di sistem</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Kehadiran</div>
        <div class="metric-val"><?= $pct_kehadiran ?>%</div>
        <div class="metric-divider"></div>
        <div class="metric-sub">Rata-rata semua kegiatan</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Operasional</div>
        <div class="metric-val"><?= $ops_berjalan ?></div>
        <div class="metric-divider"></div>
        <div class="metric-sub <?= $ops_berjalan > 0 ? 'warn' : '' ?>">
            <?= $ops_berjalan > 0 ? 'Sedang berjalan' : 'Tidak ada' ?>
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Mentoring</div>
        <div class="metric-val"><?= $total_mentoring ?></div>
        <div class="metric-divider"></div>
        <div class="metric-sub">Bulan <?= date('F') ?></div>
    </div>
</div>

        <!-- KEHADIRAN PER KEGIATAN -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Kehadiran per kegiatan</span>
                <span class="pill"><?= date('F Y') ?></span>
            </div>
            <?php
            $jenis_list = ['rabuan', 'mentoring', 'binjas'];
            $warna = [
                'rabuan'    => '#378ADD',
                'mentoring' => '#1D9E75',
                'binjas'    => '#7F77DD',
            ];
            foreach ($jenis_list as $jenis):
                $hadir = $pdo->prepare("SELECT COUNT(*) FROM kehadiran WHERE jenis_kegiatan = ? AND status = 'hadir' AND MONTH(tanggal) = MONTH(NOW())");
                $hadir->execute([$jenis]);
                $jml_hadir = $hadir->fetchColumn();

                $total = $pdo->prepare("SELECT COUNT(*) FROM kehadiran WHERE jenis_kegiatan = ? AND MONTH(tanggal) = MONTH(NOW())");
                $total->execute([$jenis]);
                $jml_total = $total->fetchColumn();

                $pct = $jml_total > 0 ? round(($jml_hadir / $jml_total) * 100) : 0;
            ?>
            <div class="bar-row">
                <span class="bar-label"><?= ucfirst($jenis) ?></span>
                <div class="bar-track">
                    <div class="bar-fill" style="width:<?= $pct ?>%;background:<?= $warna[$jenis] ?>"></div>
                </div>
                <span class="bar-pct"><?= $pct ?>%</span>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- KEGIATAN TERBARU -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Kegiatan terbaru</span>
                <span class="pill">Semua modul</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:25%">Kegiatan</th>
                            <th style="width:20%">Tanggal</th>
                            <th style="width:35%">Keterangan</th>
                            <th style="width:20%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $kegiatan = [];

                    $r = $pdo->query('SELECT "Rabuan" as jenis, tanggal, agenda as ket, "selesai" as status FROM rabuan ORDER BY tanggal DESC LIMIT 2');
                    foreach ($r as $row) $kegiatan[] = $row;

                    $m = $pdo->query('SELECT "Mentoring" as jenis, tanggal, judul_materi as ket, "selesai" as status FROM mentoring ORDER BY tanggal DESC LIMIT 2');
                    foreach ($m as $row) $kegiatan[] = $row;

                    $o = $pdo->query('SELECT "Operasional" as jenis, tanggal_mulai as tanggal, nama_kegiatan as ket, status FROM operasional ORDER BY tanggal_mulai DESC LIMIT 2');
                    foreach ($o as $row) $kegiatan[] = $row;

                    usort($kegiatan, fn($a, $b) => strtotime($b['tanggal']) - strtotime($a['tanggal']));
                    $kegiatan = array_slice($kegiatan, 0, 6);

                    $badge = [
                        'selesai'  => 'badge-ok',
                        'berjalan' => 'badge-warn',
                        'pra'      => 'badge-info',
                    ];

                    foreach ($kegiatan as $k):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($k['jenis']) ?></td>
                        <td><?= date('d M Y', strtotime($k['tanggal'])) ?></td>
                        <td><?= htmlspecialchars($k['ket']) ?></td>
                        <td>
                            <span class="badge <?= $badge[$k['status']] ?? 'badge-info' ?>">
                                <?= ucfirst($k['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($kegiatan)): ?>
                    <tr>
                        <td colspan="4" style="text-align:center;color:var(--text-hint);padding:20px">
                            Belum ada kegiatan
                        </td>
                    </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RADAR BINJAS -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Bina Jasmani — pilih siswa</span>
                <select id="pilih-siswa" class="pill" style="cursor:pointer">
                    <option value="">-- Pilih siswa --</option>
                    <?php
                    $siswa_list = $pdo->query('SELECT id, nama FROM siswa WHERE status_aktif = 1 ORDER BY nama');
                    foreach ($siswa_list as $s):
                    ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:flex;justify-content:center;padding:16px 0">
                <canvas id="chartBinjas" width="300" height="280"></canvas>
            </div>
        </div>

    </div>
</div>

<script src="../assets/js/radar-binjas.js"></script>
<script>
document.getElementById('pilih-siswa').addEventListener('change', function() {
    const siswaId = this.value;
    if (siswaId) renderRadar(siswaId);
});
</script>

<?php require_once '../includes/footer.php'; ?>