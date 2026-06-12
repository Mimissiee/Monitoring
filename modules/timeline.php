<?php
require_once '../includes/auth_check.php';
require_once '../config/koneksi.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$error   = $_SESSION['error']   ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

// Ambil semua data untuk timeline
$rabuan    = $pdo->query('SELECT id, tanggal as mulai, tanggal as selesai, agenda as judul, "rabuan" as jenis FROM rabuan ORDER BY tanggal')->fetchAll();
$mentoring = $pdo->query('SELECT id, tanggal as mulai, tanggal as selesai, judul_materi as judul, "mentoring" as jenis FROM mentoring ORDER BY tanggal')->fetchAll();
$operasional = $pdo->query('SELECT id, tanggal_mulai as mulai, tanggal_selesai as selesai, nama_kegiatan as judul, "operasional" as jenis FROM operasional ORDER BY tanggal_mulai')->fetchAll();
$binjas    = $pdo->query('SELECT id, tanggal as mulai, tanggal as selesai, CONCAT("Bina Jasmani - ", tanggal) as judul, "binjas" as jenis FROM binjas GROUP BY tanggal ORDER BY tanggal')->fetchAll();

$semua = array_merge($rabuan, $mentoring, $operasional, $binjas);
usort($semua, fn($a, $b) => strtotime($a['mulai']) - strtotime($b['mulai']));
?>

<div class="main">
    <div class="topbar">
        <span class="topbar-title">Timeline Kegiatan</span>
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

        <!-- LEGEND -->
        <div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap">
            <?php
            $legend = [
                'rabuan'      => ['label' => 'Rabuan',       'color' => '#6366F1'],
                'mentoring'   => ['label' => 'Mentoring',    'color' => '#14B8A6'],
                'operasional' => ['label' => 'Operasional',  'color' => '#F59E0B'],
                'binjas'      => ['label' => 'Bina Jasmani', 'color' => '#A78BFA'],
            ];
            foreach ($legend as $key => $l):
            ?>
            <div style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:500;color:var(--text-2)">
                <span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:<?= $l['color'] ?>"></span>
                <?= $l['label'] ?>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- FILTER BULAN -->
        <div class="card" style="margin-bottom:16px">
            <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                <span style="font-size:12px;font-weight:600;color:var(--text-2)">Filter bulan:</span>
                <?php
                $bulan_list = [];
                foreach ($semua as $item) {
                    $bln = date('Y-m', strtotime($item['mulai']));
                    if (!in_array($bln, $bulan_list)) $bulan_list[] = $bln;
                }
                sort($bulan_list);
                ?>
                <button onclick="filterBulan('semua')" id="btn-semua"
                    class="btn btn-sm btn-primary">Semua</button>
                <?php foreach ($bulan_list as $bln): ?>
                <button onclick="filterBulan('<?= $bln ?>')" id="btn-<?= $bln ?>"
                    class="btn btn-sm">
                    <?= date('M Y', strtotime($bln . '-01')) ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- TIMELINE LIST -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Semua kegiatan</span>
                <span class="pill" id="jumlah-item"><?= count($semua) ?> kegiatan</span>
            </div>

            <?php if (empty($semua)): ?>
                <p style="text-align:center;color:var(--text-3);padding:40px">
                    Belum ada data kegiatan.
                </p>
            <?php else: ?>

            <div id="timeline-list">
                <?php foreach ($semua as $item):
                    $warna = $legend[$item['jenis']]['color'] ?? '#6366F1';
                    $label = $legend[$item['jenis']]['label'] ?? ucfirst($item['jenis']);
                    $bulan = date('Y-m', strtotime($item['mulai']));
                    $sama  = $item['mulai'] === $item['selesai'];
                ?>
                <div class="timeline-item" data-bulan="<?= $bulan ?>"
                     style="display:flex;gap:16px;padding:14px 0;border-bottom:1px solid var(--border);align-items:flex-start">

                    <!-- Tanggal -->
                    <div style="width:80px;flex-shrink:0;text-align:center">
                        <div style="font-size:22px;font-weight:800;color:<?= $warna ?>;line-height:1">
                            <?= date('d', strtotime($item['mulai'])) ?>
                        </div>
                        <div style="font-size:11px;color:var(--text-3);font-weight:500">
                            <?= date('M Y', strtotime($item['mulai'])) ?>
                        </div>
                    </div>

                    <!-- Garis -->
                    <div style="display:flex;flex-direction:column;align-items:center;padding-top:4px">
                        <div style="width:10px;height:10px;border-radius:50%;background:<?= $warna ?>;flex-shrink:0"></div>
                        <div style="width:2px;flex:1;background:<?= $warna ?>;opacity:0.2;min-height:30px;margin-top:4px"></div>
                    </div>

                    <!-- Konten -->
                    <div style="flex:1;min-width:0">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap">
                            <span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:20px;color:#fff;background:<?= $warna ?>">
                                <?= $label ?>
                            </span>
                            <?php if (!$sama): ?>
                            <span style="font-size:11px;color:var(--text-3)">
                                s/d <?= date('d M Y', strtotime($item['selesai'])) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <div style="font-size:14px;font-weight:600;color:var(--text);margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            <?= htmlspecialchars($item['judul']) ?>
                        </div>
                        <div style="font-size:12px;color:var(--text-3)">
                            <?= $sama
                                ? date('l, d F Y', strtotime($item['mulai']))
                                : date('d F', strtotime($item['mulai'])) . ' — ' . date('d F Y', strtotime($item['selesai']))
                            ?>
                        </div>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>

            <?php endif; ?>
        </div>

        <!-- RINGKASAN PER MODUL -->
        <div class="grid-2">
            <?php foreach ($legend as $key => $l): ?>
            <?php
            $data_modul = array_filter($semua, fn($i) => $i['jenis'] === $key);
            $jumlah     = count($data_modul);
            ?>
            <div class="card" style="margin-bottom:0">
                <div style="display:flex;align-items:center;gap:12px">
                    <div style="width:40px;height:40px;border-radius:10px;background:<?= $l['color'] ?>22;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <div style="width:16px;height:16px;border-radius:4px;background:<?= $l['color'] ?>"></div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:var(--text-3);font-weight:500"><?= $l['label'] ?></div>
                        <div style="font-size:22px;font-weight:800;color:var(--text);line-height:1.2"><?= $jumlah ?></div>
                        <div style="font-size:11px;color:var(--text-3)">kegiatan tercatat</div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>

<script>
function filterBulan(bulan) {
    const items   = document.querySelectorAll('.timeline-item');
    const buttons = document.querySelectorAll('[id^="btn-"]');
    let tampil    = 0;

    buttons.forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.style.background = '';
        btn.style.color      = '';
        btn.style.borderColor = '';
    });

    const activeBtn = document.getElementById('btn-' + bulan);
    if (activeBtn) activeBtn.classList.add('btn-primary');

    items.forEach(item => {
        if (bulan === 'semua' || item.dataset.bulan === bulan) {
            item.style.display = 'flex';
            tampil++;
        } else {
            item.style.display = 'none';
        }
    });

    document.getElementById('jumlah-item').textContent = tampil + ' kegiatan';
}
</script>

<?php require_once '../includes/footer.php'; ?>