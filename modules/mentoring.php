<?php
require_once '../includes/auth_check.php';
require_once '../config/koneksi.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$error   = $_SESSION['error']   ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$daftar = $pdo->query('SELECT * FROM mentoring ORDER BY tanggal DESC')->fetchAll();
?>

<div class="main">
    <div class="topbar">
        <span class="topbar-title">Mentoring</span>
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

        <!-- FORM TAMBAH -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Tambah sesi mentoring</span>
            </div>
            <form action="/siswatrack/actions/proses_mentoring.php" method="POST" enctype="multipart/form-data">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Judul materi</label>
                        <input type="text" name="judul_materi" required placeholder="Contoh: Navigasi Medan">
                    </div>
                    <div class="form-group">
                        <label>Nama pengisi / tutor</label>
                        <input type="text" name="pengisi" required placeholder="Nama lengkap pengisi">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label>Tanggal sesi</label>
                        <input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label>Upload bahan ajar (PDF)</label>
                        <input type="file" name="file_bahan_ajar" accept=".pdf">
                    </div>
                </div>
                <div class="form-group">
                    <label>Kebutuhan logistik</label>
                    <textarea name="kebutuhan_logistik" placeholder="Contoh: Kompas 24 buah, proyektor, whiteboard..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Simpan sesi</button>
            </form>
        </div>

        <!-- DAFTAR SESI -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Daftar sesi mentoring</span>
                <span class="pill"><?= count($daftar) ?> sesi</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:5%">No</th>
                            <th style="width:15%">Tanggal</th>
                            <th style="width:25%">Judul materi</th>
                            <th style="width:20%">Pengisi</th>
                            <th style="width:20%">Bahan ajar</th>
                            <th style="width:15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($daftar)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;color:var(--text-3);padding:24px">
                                Belum ada data sesi mentoring
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($daftar as $i => $m): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= date('d M Y', strtotime($m['tanggal'])) ?></td>
                            <td><?= htmlspecialchars($m['judul_materi']) ?></td>
                            <td><?= htmlspecialchars($m['pengisi']) ?></td>
                            <td>
                                <?php if ($m['file_bahan_ajar']): ?>
                                    <a href="/siswatrack/uploads/materi/<?= $m['file_bahan_ajar'] ?>" target="_blank" class="badge badge-info">
                                        Lihat PDF
                                    </a>
                                <?php else: ?>
                                    <span class="badge badge-warn">Belum upload</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/siswatrack/actions/proses_mentoring.php?hapus=<?= $m['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Hapus sesi ini?')">
                                   Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- DETAIL LOGISTIK -->
        <?php if (!empty($daftar)): ?>
        <div class="card">
            <div class="card-header">
                <span class="card-title">Kebutuhan logistik sesi terbaru</span>
                <span class="pill"><?= htmlspecialchars($daftar[0]['judul_materi']) ?></span>
            </div>
            <?php if ($daftar[0]['kebutuhan_logistik']): ?>
                <p style="font-size:13px;color:var(--text-2);line-height:1.8;white-space:pre-line">
                    <?= htmlspecialchars($daftar[0]['kebutuhan_logistik']) ?>
                </p>
            <?php else: ?>
                <p style="font-size:13px;color:var(--text-3)">Tidak ada catatan logistik.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>