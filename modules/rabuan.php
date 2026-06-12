<?php
require_once '../includes/auth_check.php';
require_once '../config/koneksi.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$error   = $_SESSION['error']   ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$daftar_rabuan = $pdo->query('SELECT * FROM rabuan ORDER BY tanggal DESC')->fetchAll();
?>

<div class="main">
    <div class="topbar">
        <span class="topbar-title">Rabuan</span>
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

        <!-- FORM TAMBAH RAPAT -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Tambah rapat Rabuan</span>
            </div>
            <form action="/siswatrack/actions/proses_rabuan.php" method="POST" enctype="multipart/form-data">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Tanggal rapat</label>
                        <input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label>Upload notulensi (PDF)</label>
                        <input type="file" name="file_notulensi" accept=".pdf">
                    </div>
                </div>
                <div class="form-group">
                    <label>Agenda rapat</label>
                    <textarea name="agenda" required placeholder="Tuliskan agenda rapat..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Simpan rapat</button>
            </form>
        </div>

        <!-- DAFTAR RAPAT -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Daftar rapat Rabuan</span>
                <span class="pill"><?= count($daftar_rabuan) ?> rapat</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:5%">No</th>
                            <th style="width:15%">Tanggal</th>
                            <th style="width:45%">Agenda</th>
                            <th style="width:20%">Notulensi</th>
                            <th style="width:15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($daftar_rabuan)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center;color:var(--text-3);padding:24px">
                                Belum ada data rapat
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($daftar_rabuan as $i => $r): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= date('d M Y', strtotime($r['tanggal'])) ?></td>
                            <td><?= htmlspecialchars($r['agenda']) ?></td>
                            <td>
                                <?php if ($r['file_notulensi']): ?>
                                    <a href="/siswatrack/uploads/notulensi/<?= $r['file_notulensi'] ?>" target="_blank" class="badge badge-info">
                                        Lihat PDF
                                    </a>
                                <?php else: ?>
                                    <span class="badge badge-warn">Belum upload</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/siswatrack/actions/proses_rabuan.php?hapus=<?= $r['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Hapus rapat ini?')">
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

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>