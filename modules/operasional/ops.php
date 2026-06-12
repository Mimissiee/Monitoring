<?php
require_once '../../includes/auth_check.php';
require_once '../../config/koneksi.php';
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

$error   = $_SESSION['error']   ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$daftar_ops = $pdo->query("SELECT * FROM operasional ORDER BY tanggal_mulai DESC")->fetchAll();
?>

<div class="main">
    <div class="topbar">
        <span class="topbar-title">Operasional — Pelaksanaan</span>
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

        <!-- TAB FASE -->
        <div style="display:flex;gap:8px;margin-bottom:20px">
            <a href="/siswatrack/modules/operasional/pra_ops.php"
               style="padding:8px 18px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;background:var(--surface);color:var(--text-2);border:1px solid var(--border)">
               Pra Operasional
            </a>
            <a href="/siswatrack/modules/operasional/ops.php"
               style="padding:8px 18px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;background:var(--indigo);color:#fff;border:1px solid var(--indigo)">
               Operasional
            </a>
            <a href="/siswatrack/modules/operasional/pasca_ops.php"
               style="padding:8px 18px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;background:var(--surface);color:var(--text-2);border:1px solid var(--border)">
               Pasca Operasional
            </a>
        </div>

        <?php if (empty($daftar_ops)): ?>
        <div class="card" style="text-align:center;padding:40px;color:var(--text-3)">
            Belum ada kegiatan operasional. Buat dulu di halaman Pra Operasional.
        </div>
        <?php endif; ?>

        <?php foreach ($daftar_ops as $ops): ?>
        <div class="card">
            <div class="card-header">
                <div>
                    <span class="card-title"><?= htmlspecialchars($ops['nama_kegiatan']) ?></span>
                    <div style="font-size:12px;color:var(--text-3);margin-top:2px">
                        <?= date('d M Y', strtotime($ops['tanggal_mulai'])) ?> —
                        <?= date('d M Y', strtotime($ops['tanggal_selesai'])) ?>
                    </div>
                </div>
                <span class="badge <?= $ops['status'] === 'selesai' ? 'badge-ok' : ($ops['status'] === 'berjalan' ? 'badge-warn' : 'badge-info') ?>">
                    <?= ucfirst($ops['status']) ?>
                </span>
            </div>

            <!-- UBAH STATUS -->
            <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap">
                <?php foreach (['pra' => 'Pra', 'berjalan' => 'Berjalan', 'selesai' => 'Selesai'] as $val => $label): ?>
                <a href="/siswatrack/actions/proses_operasional.php?ubah_status=<?= $ops['id'] ?>&status=<?= $val ?>"
                   class="btn btn-sm <?= $ops['status'] === $val ? 'btn-primary' : '' ?>">
                   <?= $label ?>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- UPLOAD LAPORAN -->
            <div style="background:var(--gray-1);border-radius:10px;padding:16px;margin-bottom:16px">
                <div style="font-size:12px;font-weight:600;color:var(--text-2);margin-bottom:10px">
                    Upload laporan hasil kegiatan (PDF)
                </div>
                <?php if ($ops['file_laporan']): ?>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                        <span class="badge badge-ok">Sudah upload</span>
                        <a href="/siswatrack/uploads/operasional/<?= $ops['file_laporan'] ?>" target="_blank" class="btn btn-sm">
                            Lihat laporan
                        </a>
                    </div>
                <?php endif; ?>
                <form action="/siswatrack/actions/proses_upload.php" method="POST" enctype="multipart/form-data"
                      style="display:flex;gap:8px;align-items:center">
                    <input type="hidden" name="operasional_id" value="<?= $ops['id'] ?>">
                    <input type="file" name="file_laporan" accept=".pdf" required
                           style="font-size:13px;flex:1">
                    <button type="submit" class="btn btn-primary btn-sm">Upload PDF</button>
                </form>
            </div>

            <!-- RINGKASAN PESERTA -->
            <?php
            $peserta = $pdo->prepare('
                SELECT s.nama, s.regu, op.status_kehadiran
                FROM operasional_peserta op
                JOIN siswa s ON s.id = op.siswa_id
                WHERE op.operasional_id = ?
                ORDER BY s.regu, s.nama
            ');
            $peserta->execute([$ops['id']]);
            $list_peserta = $peserta->fetchAll();
            $jml_hadir = count(array_filter($list_peserta, fn($p) => $p['status_kehadiran'] === 'hadir'));
            ?>

            <?php if (!empty($list_peserta)): ?>
            <div>
                <div style="font-size:12px;font-weight:600;color:var(--text-2);margin-bottom:10px">
                    Peserta (<?= $jml_hadir ?>/<?= count($list_peserta) ?> hadir)
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:5%">No</th>
                                <th style="width:50%">Nama</th>
                                <th style="width:20%">Regu</th>
                                <th style="width:25%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list_peserta as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td style="font-weight:500"><?= htmlspecialchars($p['nama']) ?></td>
                            <td><?= $p['regu'] ?></td>
                            <td>
                                <span class="badge <?= $p['status_kehadiran'] === 'hadir' ? 'badge-ok' : ($p['status_kehadiran'] === 'izin' ? 'badge-info' : ($p['status_kehadiran'] === 'sakit' ? 'badge-warn' : 'badge-danger')) ?>">
                                    <?= ucfirst($p['status_kehadiran']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>