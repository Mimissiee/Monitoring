<?php
require_once '../../includes/auth_check.php';
require_once '../../config/koneksi.php';
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

$error   = $_SESSION['error']   ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$daftar_ops = $pdo->query("SELECT * FROM operasional WHERE status = 'selesai' ORDER BY tanggal_mulai DESC")->fetchAll();
$semua_ops  = $pdo->query("SELECT * FROM operasional ORDER BY tanggal_mulai DESC")->fetchAll();
?>

<div class="main">
    <div class="topbar">
        <span class="topbar-title">Operasional — Pasca Operasional</span>
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
               style="padding:8px 18px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;background:var(--surface);color:var(--text-2);border:1px solid var(--border)">
               Operasional
            </a>
            <a href="/siswatrack/modules/operasional/pasca_ops.php"
               style="padding:8px 18px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;background:var(--indigo);color:#fff;border:1px solid var(--indigo)">
               Pasca Operasional
            </a>
        </div>

        <?php foreach ($semua_ops as $ops): ?>
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

            <!-- FORM TAMBAH ALAT -->
            <div style="margin-bottom:16px">
                <div style="font-size:12px;font-weight:600;color:var(--text-2);margin-bottom:10px">Tambah alat untuk dicek</div>
                <form action="/siswatrack/actions/proses_checklist.php" method="POST"
                      style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                    <input type="hidden" name="operasional_id" value="<?= $ops['id'] ?>">
                    <input type="text" name="nama_alat" required placeholder="Nama alat"
                           style="flex:1;min-width:160px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px">
                    <select name="jenis" style="padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;background:var(--surface)">
                        <option value="regu">Regu</option>
                        <option value="pribadi">Pribadi</option>
                    </select>
                    <select name="kondisi" style="padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;background:var(--surface)">
                        <option value="layak">Layak</option>
                        <option value="tidak_layak">Tidak layak</option>
                        <option value="butuh_perbaikan">Butuh perbaikan</option>
                    </select>
                    <input type="text" name="keterangan" placeholder="Keterangan (opsional)"
                           style="flex:1;min-width:140px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px">
                    <button type="submit" class="btn btn-primary btn-sm">Tambah</button>
                </form>
            </div>

            <!-- CHECKLIST ALAT -->
            <?php
            $checklist = $pdo->prepare('SELECT * FROM checklist_alat WHERE operasional_id = ? ORDER BY jenis, nama_alat');
            $checklist->execute([$ops['id']]);
            $list_alat = $checklist->fetchAll();

            $layak         = count(array_filter($list_alat, fn($a) => $a['kondisi'] === 'layak'));
            $tidak_layak   = count(array_filter($list_alat, fn($a) => $a['kondisi'] === 'tidak_layak'));
            $butuh_perbaikan = count(array_filter($list_alat, fn($a) => $a['kondisi'] === 'butuh_perbaikan'));
            ?>

            <?php if (!empty($list_alat)): ?>
            <!-- Ringkasan kondisi -->
            <div style="display:flex;gap:10px;margin-bottom:14px">
                <span class="badge badge-ok">Layak: <?= $layak ?></span>
                <span class="badge badge-danger">Tidak layak: <?= $tidak_layak ?></span>
                <span class="badge badge-warn">Butuh perbaikan: <?= $butuh_perbaikan ?></span>
            </div>

            <form action="/siswatrack/actions/proses_checklist.php" method="POST">
                <input type="hidden" name="aksi" value="update">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:5%">No</th>
                                <th style="width:30%">Nama alat</th>
                                <th style="width:12%">Jenis</th>
                                <th style="width:25%">Kondisi</th>
                                <th style="width:20%">Keterangan</th>
                                <th style="width:8%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list_alat as $i => $alat): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td style="font-weight:500"><?= htmlspecialchars($alat['nama_alat']) ?></td>
                            <td><span class="badge <?= $alat['jenis'] === 'regu' ? 'badge-info' : 'badge-purple' ?>"><?= ucfirst($alat['jenis']) ?></span></td>
                            <td>
                                <select name="kondisi[<?= $alat['id'] ?>]"
                                        style="padding:6px 10px;border:1px solid var(--border);border-radius:6px;font-size:12px;background:var(--surface);width:100%">
                                    <option value="layak"           <?= $alat['kondisi'] === 'layak'            ? 'selected' : '' ?>>Layak</option>
                                    <option value="tidak_layak"     <?= $alat['kondisi'] === 'tidak_layak'      ? 'selected' : '' ?>>Tidak layak</option>
                                    <option value="butuh_perbaikan" <?= $alat['kondisi'] === 'butuh_perbaikan'  ? 'selected' : '' ?>>Butuh perbaikan</option>
                                </select>
                            </td>
                            <td style="font-size:12px;color:var(--text-2)"><?= htmlspecialchars($alat['keterangan'] ?? '-') ?></td>
                            <td>
                                <a href="/siswatrack/actions/proses_checklist.php?hapus=<?= $alat['id'] ?>&ops_id=<?= $ops['id'] ?>"
                                   onclick="return confirm('Hapus alat ini?')"
                                   class="btn btn-danger btn-sm">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="margin-top:12px">Simpan kondisi alat</button>
            </form>
            <?php else: ?>
                <p style="font-size:13px;color:var(--text-3)">Belum ada alat ditambahkan untuk dicek.</p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <?php if (empty($semua_ops)): ?>
        <div class="card" style="text-align:center;padding:40px;color:var(--text-3)">
            Belum ada kegiatan operasional.
        </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>