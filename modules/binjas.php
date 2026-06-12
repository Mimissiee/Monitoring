<?php
require_once '../includes/auth_check.php';
require_once '../config/koneksi.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$error   = $_SESSION['error']   ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$daftar_siswa = $pdo->query('SELECT * FROM siswa WHERE status_aktif = 1 ORDER BY nama')->fetchAll();
$standar      = $pdo->query('SELECT * FROM binjas_standar')->fetchAll();

$standar_map = [];
foreach ($standar as $s) {
    $standar_map[strtolower($s['nama_item'])] = $s['nilai_minimum'];
}

$semua_nilai = $pdo->query('
    SELECT b.*, s.nama, s.regu
    FROM binjas b
    JOIN siswa s ON s.id = b.siswa_id
    ORDER BY b.tanggal DESC, s.nama ASC
')->fetchAll();

$nilai_terbaru = [];
foreach ($semua_nilai as $n) {
    if (!isset($nilai_terbaru[$n['siswa_id']])) {
        $nilai_terbaru[$n['siswa_id']] = $n;
    }
}
?>

<div class="main">
    <div class="topbar">
        <span class="topbar-title">Bina Jasmani</span>
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

        <!-- STANDAR NILAI -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Standar nilai Bina Jasmani</span>
                <span class="pill">Acuan penilaian</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px">
                <?php
                $warna_standar = [
                    'Push-up'  => 'var(--indigo)',
                    'Sit-up'   => 'var(--teal)',
                    'Lari'     => 'var(--amber)',
                    'Pull-up'  => 'var(--violet)',
                    'Renang'   => '#14B8A6',
                ];
                foreach ($standar as $st):
                    $warna = $warna_standar[$st['nama_item']] ?? 'var(--indigo)';
                ?>
                <div style="background:var(--gray-1);border-radius:10px;padding:14px;border:1px solid var(--border);text-align:center">
                    <div style="font-size:11px;color:var(--text-3);margin-bottom:4px;font-weight:500"><?= htmlspecialchars($st['nama_item']) ?></div>
                    <div style="font-size:22px;font-weight:800;color:<?= $warna ?>">
                        <?= $st['nilai_minimum'] ?>
                    </div>
                    <div style="font-size:10px;color:var(--text-3);margin-top:2px"><?= htmlspecialchars($st['satuan']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="grid-2">

            <!-- FORM INPUT NILAI -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Input nilai latihan</span>
                </div>
                <form action="/siswatrack/actions/proses_binjas.php" method="POST">
                    <div class="form-group">
                        <label>Pilih siswa</label>
                        <select name="siswa_id" required>
                            <option value="">-- Pilih siswa --</option>
                            <?php foreach ($daftar_siswa as $s): ?>
                            <option value="<?= $s['id'] ?>">
                                <?= htmlspecialchars($s['nama']) ?> — <?= $s['regu'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal latihan</label>
                        <input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div class="form-group">
                            <label>Push-up (repetisi)</label>
                            <input type="number" name="pushup" min="0" max="200" placeholder="Min <?= $standar_map['push-up'] ?? 40 ?>">
                        </div>
                        <div class="form-group">
                            <label>Sit-up (repetisi)</label>
                            <input type="number" name="situp" min="0" max="200" placeholder="Min <?= $standar_map['sit-up'] ?? 50 ?>">
                        </div>
                        <div class="form-group">
                            <label>Lari (detik)</label>
                            <input type="number" name="lari_detik" min="0" placeholder="Maks <?= $standar_map['lari'] ?? 750 ?>">
                        </div>
                        <div class="form-group">
                            <label>Pull-up (repetisi)</label>
                            <input type="number" name="pullup" min="0" max="100" placeholder="Min <?= $standar_map['pull-up'] ?? 10 ?>">
                        </div>
                        <div class="form-group" style="grid-column:span 2">
                            <label>Renang (detik)</label>
                            <input type="number" name="renang_detik" min="0" placeholder="Maks <?= $standar_map['renang'] ?? 900 ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan nilai</button>
                </form>
            </div>

            <!-- RADAR CHART -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Radar chart individu</span>
                    <select id="pilih-siswa-radar" class="pill" style="cursor:pointer">
                        <option value="">-- Pilih siswa --</option>
                        <?php foreach ($daftar_siswa as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display:flex;justify-content:center;align-items:center;padding:16px 0;min-height:260px">
                    <canvas id="chartBinjas" width="280" height="260"></canvas>
                </div>
                <div id="radar-info" style="display:none;margin-top:8px">
                    <div style="display:flex;gap:16px;justify-content:center;font-size:12px">
                        <span style="display:flex;align-items:center;gap:4px">
                            <span style="display:inline-block;width:12px;height:3px;background:var(--indigo);border-radius:2px"></span>
                            Nilai siswa
                        </span>
                        <span style="display:flex;align-items:center;gap:4px">
                            <span style="display:inline-block;width:12px;height:3px;background:var(--amber);border-radius:2px;border:1px dashed var(--amber)"></span>
                            Standar
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <!-- TABEL REKAP NILAI -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Rekap nilai terbaru semua siswa</span>
                <span class="pill"><?= count($nilai_terbaru) ?> siswa</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:4%">No</th>
                            <th style="width:20%">Nama</th>
                            <th style="width:8%">Regu</th>
                            <th style="width:10%">Tanggal</th>
                            <th style="width:10%;text-align:center">Push-up</th>
                            <th style="width:10%;text-align:center">Sit-up</th>
                            <th style="width:10%;text-align:center">Lari (s)</th>
                            <th style="width:10%;text-align:center">Pull-up</th>
                            <th style="width:10%;text-align:center">Renang (s)</th>
                            <th style="width:8%;text-align:center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($nilai_terbaru)): ?>
                        <tr>
                            <td colspan="10" style="text-align:center;color:var(--text-3);padding:24px">
                                Belum ada data nilai
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($nilai_terbaru as $n):
                            $lulus_pushup  = $n['pushup']      >= ($standar_map['push-up'] ?? 40);
                            $lulus_situp   = $n['situp']       >= ($standar_map['sit-up']  ?? 50);
                            $lulus_lari    = $n['lari_detik']  <= ($standar_map['lari']    ?? 750);
                            $lulus_pullup  = $n['pullup']      >= ($standar_map['pull-up'] ?? 10);
                            $lulus_renang  = $n['renang_detik'] <= ($standar_map['renang'] ?? 900);
                            $total_lulus   = array_sum([$lulus_pushup, $lulus_situp, $lulus_lari, $lulus_pullup, $lulus_renang]);
                            $status_badge  = $total_lulus == 5 ? 'badge-ok' : ($total_lulus >= 3 ? 'badge-warn' : 'badge-danger');
                            $status_label  = $total_lulus == 5 ? 'Lulus' : ($total_lulus >= 3 ? 'Cukup' : 'Kurang');
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td style="font-weight:500"><?= htmlspecialchars($n['nama']) ?></td>
                            <td><?= htmlspecialchars($n['regu']) ?></td>
                            <td><?= date('d M Y', strtotime($n['tanggal'])) ?></td>
                            <td style="text-align:center">
                                <span class="badge <?= $lulus_pushup ? 'badge-ok' : 'badge-danger' ?>">
                                    <?= $n['pushup'] ?>
                                </span>
                            </td>
                            <td style="text-align:center">
                                <span class="badge <?= $lulus_situp ? 'badge-ok' : 'badge-danger' ?>">
                                    <?= $n['situp'] ?>
                                </span>
                            </td>
                            <td style="text-align:center">
                                <span class="badge <?= $lulus_lari ? 'badge-ok' : 'badge-danger' ?>">
                                    <?= $n['lari_detik'] ?>
                                </span>
                            </td>
                            <td style="text-align:center">
                                <span class="badge <?= $lulus_pullup ? 'badge-ok' : 'badge-danger' ?>">
                                    <?= $n['pullup'] ?>
                                </span>
                            </td>
                            <td style="text-align:center">
                                <span class="badge <?= $lulus_renang ? 'badge-ok' : 'badge-danger' ?>">
                                    <?= $n['renang_detik'] ?>
                                </span>
                            </td>
                            <td style="text-align:center">
                                <span class="badge <?= $status_badge ?>"><?= $status_label ?></span>
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

<script>
const standarNilai = {
    pushup:      <?= $standar_map['push-up'] ?? 40 ?>,
    situp:       <?= $standar_map['sit-up']  ?? 50 ?>,
    lari_detik:  <?= $standar_map['lari']    ?? 750 ?>,
    pullup:      <?= $standar_map['pull-up'] ?? 10 ?>,
    renang_detik:<?= $standar_map['renang']  ?? 900 ?>,
};

const nilaiDB = <?= json_encode(array_values($nilai_terbaru)) ?>;

document.getElementById('pilih-siswa-radar').addEventListener('change', function() {
    const siswaId = parseInt(this.value);
    if (!siswaId) return;
    const data = nilaiDB.find(n => parseInt(n.siswa_id) === siswaId);
    if (data) renderRadar(data);
});

function renderRadar(data) {
    const canvas = document.getElementById('chartBinjas');
    const ctx    = canvas.getContext('2d');
    const W = canvas.width;
    const H = canvas.height;
    const cx = W / 2;
    const cy = H / 2 + 10;
    const R  = Math.min(W, H) / 2 - 40;

    ctx.clearRect(0, 0, W, H);

    const labels = ['Push-up', 'Sit-up', 'Lari', 'Pull-up', 'Renang'];
    const keys   = ['pushup', 'situp', 'lari_detik', 'pullup', 'renang_detik'];
    const N      = labels.length;

    // Normalisasi nilai (lari & renang dibalik karena makin kecil makin baik)
    function normalize(key, val) {
        if (key === 'lari_detik') {
            return Math.min(standarNilai[key] / val, 1.4);
        }
        if (key === 'renang_detik') {
            return Math.min(standarNilai[key] / val, 1.4);
        }
        return Math.min(val / standarNilai[key], 1.4);
    }

    const siswaVals   = keys.map(k => normalize(k, data[k]));
    const standarVals = keys.map(() => 1.0);

    function getPoint(i, val) {
        const angle = (Math.PI * 2 * i / N) - Math.PI / 2;
        return {
            x: cx + R * val * Math.cos(angle),
            y: cy + R * val * Math.sin(angle),
        };
    }

    // Grid
    [0.25, 0.5, 0.75, 1.0].forEach(scale => {
        ctx.beginPath();
        for (let i = 0; i < N; i++) {
            const p = getPoint(i, scale);
            i === 0 ? ctx.moveTo(p.x, p.y) : ctx.lineTo(p.x, p.y);
        }
        ctx.closePath();
        ctx.strokeStyle = '#E4E4E7';
        ctx.lineWidth   = 0.8;
        ctx.stroke();
    });

    // Axes
    for (let i = 0; i < N; i++) {
        const p = getPoint(i, 1);
        ctx.beginPath();
        ctx.moveTo(cx, cy);
        ctx.lineTo(p.x, p.y);
        ctx.strokeStyle = '#E4E4E7';
        ctx.lineWidth   = 0.8;
        ctx.stroke();
    }

    // Standar polygon
    ctx.beginPath();
    standarVals.forEach((v, i) => {
        const p = getPoint(i, v);
        i === 0 ? ctx.moveTo(p.x, p.y) : ctx.lineTo(p.x, p.y);
    });
    ctx.closePath();
    ctx.strokeStyle = '#F59E0B';
    ctx.lineWidth   = 1.5;
    ctx.setLineDash([5, 3]);
    ctx.stroke();
    ctx.setLineDash([]);

    // Siswa polygon
    ctx.beginPath();
    siswaVals.forEach((v, i) => {
        const p = getPoint(i, v);
        i === 0 ? ctx.moveTo(p.x, p.y) : ctx.lineTo(p.x, p.y);
    });
    ctx.closePath();
    ctx.fillStyle   = 'rgba(99,102,241,0.15)';
    ctx.fill();
    ctx.strokeStyle = '#6366F1';
    ctx.lineWidth   = 2;
    ctx.stroke();

    // Labels
    ctx.fillStyle  = '#52525B';
    ctx.font       = '11px sans-serif';
    ctx.textAlign  = 'center';
    labels.forEach((label, i) => {
        const p = getPoint(i, 1.35);
        ctx.fillText(label, p.x, p.y);
    });

    document.getElementById('radar-info').style.display = 'block';
}
</script>

<?php require_once '../includes/footer.php'; ?>