// =============================================
// RADAR CHART — BINA JASMANI
// =============================================

const standarDefault = {
    pushup:       40,
    situp:        50,
    lari_detik:   750,
    pullup:       10,
    renang_detik: 900,
};

function renderRadar(data, canvasId, standar) {
    canvasId = canvasId || 'chartBinjas';
    standar  = standar  || standarDefault;

    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const W   = canvas.width;
    const H   = canvas.height;
    const cx  = W / 2;
    const cy  = H / 2 + 8;
    const R   = Math.min(W, H) / 2 - 44;

    ctx.clearRect(0, 0, W, H);

    const labels = ['Push-up', 'Sit-up', 'Lari', 'Pull-up', 'Renang'];
    const keys   = ['pushup', 'situp', 'lari_detik', 'pullup', 'renang_detik'];
    const N      = labels.length;

    // Normalisasi nilai
    function normalize(key, val) {
        if (!val || val === 0) return 0;
        if (key === 'lari_detik' || key === 'renang_detik') {
            return Math.min(standar[key] / val, 1.4);
        }
        return Math.min(val / standar[key], 1.4);
    }

    const siswaVals   = keys.map(k => normalize(k, parseFloat(data[k]) || 0));
    const standarVals = keys.map(() => 1.0);

    function getPoint(i, val) {
        const angle = (Math.PI * 2 * i / N) - Math.PI / 2;
        return {
            x: cx + R * val * Math.cos(angle),
            y: cy + R * val * Math.sin(angle),
        };
    }

    // Grid rings
    const gridColor = getComputedStyle(document.documentElement)
        .getPropertyValue('--border').trim() || '#E4E4E7';

    [0.25, 0.5, 0.75, 1.0].forEach(scale => {
        ctx.beginPath();
        for (let i = 0; i < N; i++) {
            const p = getPoint(i, scale);
            i === 0 ? ctx.moveTo(p.x, p.y) : ctx.lineTo(p.x, p.y);
        }
        ctx.closePath();
        ctx.strokeStyle = gridColor;
        ctx.lineWidth   = 0.8;
        ctx.stroke();
    });

    // Axis lines
    for (let i = 0; i < N; i++) {
        const p = getPoint(i, 1.0);
        ctx.beginPath();
        ctx.moveTo(cx, cy);
        ctx.lineTo(p.x, p.y);
        ctx.strokeStyle = gridColor;
        ctx.lineWidth   = 0.8;
        ctx.stroke();
    }

    // Standar polygon (amber dashed)
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

    // Siswa polygon (indigo filled)
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

    // Dots pada titik siswa
    siswaVals.forEach((v, i) => {
        const p = getPoint(i, v);
        ctx.beginPath();
        ctx.arc(p.x, p.y, 3.5, 0, Math.PI * 2);
        ctx.fillStyle = '#6366F1';
        ctx.fill();
    });

    // Labels
    ctx.fillStyle  = '#52525B';
    ctx.font       = '500 11px -apple-system, sans-serif';
    ctx.textAlign  = 'center';
    labels.forEach((label, i) => {
        const p = getPoint(i, 1.42);
        ctx.fillText(label, p.x, p.y + 4);
    });

    // Nilai di tengah (nama siswa jika ada)
    if (data.nama) {
        ctx.fillStyle  = '#18181B';
        ctx.font       = '600 12px -apple-system, sans-serif';
        ctx.textAlign  = 'center';
        ctx.fillText(data.nama, cx, cy + R + 30);
    }
}

// =============================================
// FETCH DATA DARI API LALU RENDER
// =============================================
function renderRadarByID(siswaId, canvasId, standar) {
    if (!siswaId) return;
    fetch('/siswatrack/api/get_binjas.php?siswa_id=' + siswaId)
        .then(res => res.json())
        .then(data => {
            if (data && data.pushup !== undefined) {
                renderRadar(data, canvasId, standar);
            } else {
                const canvas = document.getElementById(canvasId || 'chartBinjas');
                if (canvas) {
                    const ctx = canvas.getContext('2d');
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    ctx.fillStyle  = '#A1A1AA';
                    ctx.font       = '13px sans-serif';
                    ctx.textAlign  = 'center';
                    ctx.fillText('Belum ada data nilai', canvas.width / 2, canvas.height / 2);
                }
            }
        })
        .catch(() => console.error('Gagal mengambil data binjas'));
}