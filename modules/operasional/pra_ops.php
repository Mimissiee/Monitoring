<?php
require_once '../../includes/auth_check.php';
require_once '../../config/koneksi.php';
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

$error   = $_SESSION['error']   ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$daftar_siswa = $pdo->query('SELECT * FROM siswa WHERE status_aktif = 1 ORDER BY regu, nama')->fetchAll();
$daftar_ops   = $pdo->query("SELECT * FROM operasional ORDER BY tanggal_mulai DESC")->fetchAll();
?>

<div class="main">
    <div class="topbar">
        <span class="topbar-title">Operasional — Pra Operasional</span>
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
               style="padding:8px 18px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;background:var(--indigo);color:#fff;border:1px solid var(--indigo)">
               Pra Operasional
            </a>
            <a href="/siswatrack/modules/operasional/ops.php"
               style="padding:8px 18px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;background:var(--surface);color:var(--text-2);border:1px solid var(--border)">
               Operasional
            </a>
            <a href="/siswatrack/modules/operasional/pasca_ops.php"
               style="padding:8px 18px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;background:var(--surface);color:var(--text-2);border:1px solid var(--border)">
               Pasca Operasional
            </a>
        </div>

        <!-- FORM BUAT OPERASIONAL BARU -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Buat kegiatan operasional baru</span>
            </div>
            <form action="/siswatrack/actions/proses_operasional.php" method="POST">
                <input type="hidden" name="aksi" value="buat">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Nama kegiatan</label>
                        <input type="text" name="nama_kegiatan" required placeholder="Contoh: Pendakian Gunung Ciremai">
                    </div>
                    <div class="form-group">
                        <label>Tanggal mulai</label>
                        <input type="date" name="tanggal_mulai" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label>Tanggal selesai</label>
                        <input type="date" name="tanggal_selesai" required value="<?= date('Y-m-d', strtotime('+3 days')) ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Buat operasional</button>
            </form>
        </div>

        <!-- DAFTAR OPERASIONAL -->
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

            <!-- PESERTA -->
            <div style="margin-bottom:16px">
                <div style="font-size:12px;font-weight:600;color:var(--text-2);margin-bottom:10px">Data peserta</div>
                <form action="/siswatrack/actions/proses_operasional.php" method="POST">
                    <input type="hidden" name="aksi"          value="peserta">
                    <input type="hidden" name="operasional_id" value="<?= $ops['id'] ?>">

                    <?php
                    $peserta_ada = $pdo->prepare('SELECT siswa_id, status_kehadiran FROM operasional_peserta WHERE operasional_id = ?');
                    $peserta_ada->execute([$ops['id']]);
                    $existing_peserta = [];
                    foreach ($peserta_ada->fetchAll() as $p) {
                        $existing_peserta[$p['siswa_id']] = $p['status_kehadiran'];
                    }
                    ?>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:5%">No</th>
                                    <th style="width:30%">Nama</th>
                                    <th style="width:10%">Regu</th>
                                    <th style="width:15%;text-align:center">Ikut</th>
                                    <th style="width:15%;text-align:center">Izin</th>
                                    <th style="width:15%;text-align:center">Sakit</th>
                                    <th style="width:10%;text-align:center">Absen</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($daftar_siswa as $i => $s):
                                $st = $existing_peserta[$s['id']] ?? 'hadir';
                            ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td style="font-weight:500"><?= htmlspecialchars($s['nama']) ?></td>
                                <td><?= $s['regu'] ?></td>
                                <?php foreach (['hadir', 'izin', 'sakit', 'absen'] as $opt): ?>
                                <td style="text-align:center">
                                    <input type="radio" name="status[<?= $s['id'] ?>]" value="<?= $opt ?>"
                                           <?= $st === $opt ? 'checked' : '' ?>
                                           style="width:16px;height:16px;accent-color:var(--indigo);cursor:pointer">
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="margin-top:12px">Simpan peserta</button>
                </form>
            </div>

            <!-- PERBEKALAN -->
            <div>
                <div style="font-size:12px;font-weight:600;color:var(--text-2);margin-bottom:10px">Perbekalan & peralatan</div>
                <form action="/siswatrack/actions/proses_operasional.php" method="POST" style="display:flex;gap:8px;margin-bottom:10px">
                    <input type="hidden" name="aksi"           value="perbekalan">
                    <input type="hidden" name="operasional_id" value="<?= $ops['id'] ?>">
                    <input type="text" name="nama_item" placeholder="Nama item" required style="flex:1;padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px">
                    <select name="jenis" style="padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;background:var(--surface)">
                        <option value="regu">Regu</option>
                        <option value="pribadi">Pribadi</option>
                    </select>
                    <input type="text" name="keterangan" placeholder="Keterangan" style="flex:1;padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px">
                    <button type="submit" class="btn btn-primary btn-sm">Tambah</button>
                </form>

                <?php
                $perbekalan = $pdo->prepare('SELECT * FROM operasional_perbekalan WHERE operasional_id = ? ORDER BY jenis, nama_item');
                $perbekalan->execute([$ops['id']]);
                $list_perbekalan = $perbekalan->fetchAll();
                ?>

                <?php if (!empty($list_perbekalan)): ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:5%">No</th>
                                <th style="width:40%">Nama item</th>
                                <th style="width:15%">Jenis</th>
                                <th style="width:30%">Keterangan</th>
                                <th style="width:10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list_perbekalan as $i => $pb): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($pb['nama_item']) ?></td>
                            <td><span class="badge <?= $pb['jenis'] === 'regu' ? 'badge-info' : 'badge-purple' ?>"><?= ucfirst($pb['jenis']) ?></span></td>
                            <td><?= htmlspecialchars($pb['keterangan'] ?? '-') ?></td>
                            <td>
                                <a href="/siswatrack/actions/proses_operasional.php?hapus_perbekalan=<?= $pb['id'] ?>&ops_id=<?= $ops['id'] ?>"
                                   onclick="return confirm('Hapus item ini?')"
                                   class="btn btn-danger btn-sm">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p style="font-size:13px;color:var(--text-3)">Belum ada perbekalan ditambahkan.</p>
                <?php endif; ?>
            </div>

            <!-- HAPUS OPERASIONAL -->
            <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border)">
                <a href="/siswatrack/actions/proses_operasional.php?hapus=<?= $ops['id'] ?>"
                   onclick="return confirm('Hapus seluruh data operasional ini?')"
                   class="btn btn-danger btn-sm">Hapus operasional</a>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($daftar_ops)): ?>
        <div class="card" style="text-align:center;padding:40px;color:var(--text-3)">
            Belum ada kegiatan operasional. Buat kegiatan baru di atas.
        </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>