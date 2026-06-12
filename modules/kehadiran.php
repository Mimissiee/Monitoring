<?php
require_once '../includes/auth_check.php';
require_once '../config/koneksi.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$error   = $_SESSION['error']   ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$jenis_aktif = $_GET['jenis'] ?? 'rabuan';
$daftar_siswa = $pdo->query('SELECT * FROM siswa WHERE status_aktif = 1 ORDER BY nama')->fetchAll();

// Ambil referensi kegiatan sesuai jenis
$referensi = [];
if ($jenis_aktif === 'rabuan') {
    $referensi = $pdo->query('SELECT id, tanggal, agenda as judul FROM rabuan ORDER BY tanggal DESC')->fetchAll();
} elseif ($jenis_aktif === 'mentoring') {
    $referensi = $pdo->query('SELECT id, tanggal, judul_materi as judul FROM mentoring ORDER BY tanggal DESC')->fetchAll();
} elseif ($jenis_aktif === 'binjas') {
    $referensi = $pdo->query('SELECT id, tanggal, CONCAT("Binjas ", tanggal) as judul FROM binjas GROUP BY tanggal ORDER BY tanggal DESC')->fetchAll();
}

$ref_id_aktif = $_GET['ref_id'] ?? ($referensi[0]['id'] ?? null);

// Ambil data kehadiran yang sudah ada
$existing = [];
if ($ref_id_aktif) {
    $stmt = $pdo->prepare('SELECT siswa_id, status FROM kehadiran WHERE jenis_kegiatan = ? AND referensi_id = ?');
    $stmt->execute([$jenis_aktif, $ref_id_aktif]);
    foreach ($stmt->fetchAll() as $row) {
        $existing[$row['siswa_id']] = $row['status'];
    }
}

// Rekap kehadiran per siswa
$rekap = $pdo->query('
    SELECT s.nama, s.id,
        SUM(CASE WHEN k.status = "hadir" THEN 1 ELSE 0 END) as hadir,
        SUM(CASE WHEN k.status = "izin"  THEN 1 ELSE 0 END) as izin,
        SUM(CASE WHEN k.status = "sakit" THEN 1 ELSE 0 END) as sakit,
        SUM(CASE WHEN k.status = "absen" THEN 1 ELSE 0 END) as absen,
        COUNT(k.id) as total
    FROM siswa s
    LEFT JOIN kehadiran k ON k.siswa_id = s.id AND k.jenis_kegiatan = "' . $jenis_aktif . '"
    WHERE s.status_aktif = 1
    GROUP BY s.id, s.nama
    ORDER BY s.nama
')->fetchAll();
?>

<div class="main">
    <div class="topbar">
        <span class="topbar-title">Kehadiran</span>
        <div class="topbar-right">
            <span class="topbar-date"><?= date('l, d F Y') ?></span>
        </div>
    </div>

    <div class="content">

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- TAB JENIS KEGIATAN -->
        <div style="display:flex;gap:8px;margin-bottom:20px">
            <?php foreach (['rabuan' => 'Rabuan', 'mentoring' => 'Mentoring', 'binjas' => 'Bina Jasmani'] as $key => $label): ?>
            <a href="?jenis=<?= $key ?>"
               style="padding:8px 18px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:1px solid var(--border);
               <?= $jenis_aktif === $key ? 'background:var(--indigo);color:#fff;border-color:var(--indigo)' : 'background:var(--surface);color:var(--text-2)' ?>">
                <?= $label ?>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- FORM INPUT KEHADIRAN -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Input kehadiran — <?= ucfirst($jenis_aktif) ?></span>
            </div>

            <?php if (empty($referensi)): ?>
                <p style="font-size:13px;color:var(--text-3);padding:12px 0">
                    Belum ada data <?= $jenis_aktif ?> yang bisa dipilih.
                </p>
            <?php else: ?>

            <!-- Pilih sesi -->
            <div style="margin-bottom:16px">
                <label style="font-size:12px;font-weight:600;color:var(--text-2);display:block;margin-bottom:6px">Pilih sesi</label>
                <select onchange="window.location='?jenis=<?= $jenis_aktif ?>&ref_id='+this.value"
                        style="padding:9px 14px;border:1px solid var(--border);border-radius:8px;font-size:13px;width:100%;max-width:500px;background:var(--surface)">
                    <?php foreach ($referensi as $ref): ?>
                    <option value="<?= $ref['id'] ?>" <?= $ref['id'] == $ref_id_aktif ? 'selected' : '' ?>>
                        <?= date('d M Y', strtotime($ref['tanggal'])) ?> — <?= htmlspecialchars(substr($ref['judul'], 0, 60)) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <form action="/siswatrack/actions/proses_presensi.php" method="POST">
                <input type="hidden" name="jenis_kegiatan" value="<?= $jenis_aktif ?>">
                <input type="hidden" name="referensi_id"   value="<?= $ref_id_aktif ?>">
                <input type="hidden" name="tanggal"        value="<?= $referensi[0]['tanggal'] ?? date('Y-m-d') ?>">

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:5%">No</th>
                                <th style="width:40%">Nama siswa</th>
                                <th style="width:15%;text-align:center">Hadir</th>
                                <th style="width:15%;text-align:center">Izin</th>
                                <th style="width:15%;text-align:center">Sakit</th>
                                <th style="width:10%;text-align:center">Absen</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($daftar_siswa)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center;color:var(--text-3);padding:24px">
                                    Belum ada data siswa
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($daftar_siswa as $i => $s):
                                $status_skrg = $existing[$s['id']] ?? 'hadir';
                            ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td style="font-weight:500"><?= htmlspecialchars($s['nama']) ?></td>
                                <?php foreach (['hadir', 'izin', 'sakit', 'absen'] as $st): ?>
                                <td style="text-align:center">
                                    <input type="radio"
                                           name="status[<?= $s['id'] ?>]"
                                           value="<?= $st ?>"
                                           <?= $status_skrg === $st ? 'checked' : '' ?>
                                           style="width:16px;height:16px;accent-color:var(--indigo);cursor:pointer">
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($daftar_siswa)): ?>
                <div style="margin-top:16px;display:flex;gap:10px;align-items:center">
                    <button type="submit" class="btn btn-primary">Simpan kehadiran</button>
                    <button type="button" class="btn"
                        onclick="document.querySelectorAll('input[value=hadir]').forEach(r=>r.checked=true)">
                        Tandai semua hadir
                    </button>
                </div>
                <?php endif; ?>
            </form>

            <?php endif; ?>
        </div>

        <!-- REKAP KEHADIRAN -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Rekap kehadiran — <?= ucfirst($jenis_aktif) ?></span>
                <span class="pill"><?= count($rekap) ?> siswa</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:5%">No</th>
                            <th style="width:30%">Nama siswa</th>
                            <th style="width:12%;text-align:center">Hadir</th>
                            <th style="width:12%;text-align:center">Izin</th>
                            <th style="width:12%;text-align:center">Sakit</th>
                            <th style="width:12%;text-align:center">Absen</th>
                            <th style="width:17%;text-align:center">% Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($rekap)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center;color:var(--text-3);padding:24px">
                                Belum ada data kehadiran
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rekap as $i => $r):
                            $pct = $r['total'] > 0 ? round(($r['hadir'] / $r['total']) * 100) : 0;
                            $badge = $pct >= 80 ? 'badge-ok' : ($pct >= 60 ? 'badge-warn' : 'badge-danger');
                        ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td style="font-weight:500"><?= htmlspecialchars($r['nama']) ?></td>
                            <td style="text-align:center"><span class="badge badge-ok"><?= $r['hadir'] ?></span></td>
                            <td style="text-align:center"><span class="badge badge-info"><?= $r['izin'] ?></span></td>
                            <td style="text-align:center"><span class="badge badge-warn"><?= $r['sakit'] ?></span></td>
                            <td style="text-align:center"><span class="badge badge-danger"><?= $r['absen'] ?></span></td>
                            <td style="text-align:center"><span class="badge <?= $badge ?>"><?= $pct ?>%</span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>