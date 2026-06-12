// =============================================
// BAR CHART — KEHADIRAN
// =============================================

function renderBarKehadiran(canvasId, data) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const ctx  = canvas.getContext('2d');
    const W    = canvas.width;
    const H    = canvas.height;
    const padL = 60;
    const padR = 20;
    const padT = 20;
    const padB = 40;

    ctx.clearRect(0, 0, W, H);

    const chartW = W - padL - padR;
    const chartH = H - padT - padB;

    const warna = {
        rabuan:    '#6366F1',
        mentoring: '#14B8A6',
        binjas:    '#A78BFA',
    };

    const labels = data.map(d => d.label);
    const values = data.map(d => parseFloat(d.pct) || 0);
    const N      = labels.length;

    if (N === 0) return;

    const barW   = Math.min((chartW / N) * 0.55, 60);
    const gap    = chartW / N;

    // Grid lines
    const gridLevels = [0, 25, 50, 75, 100];
    ctx.strokeStyle = '#E4E4E7';
    ctx.lineWidth   = 0.8;
    ctx.fillStyle   = '#A1A1AA';
    ctx.font        = '11px sans-serif';
    ctx.textAlign   = 'right';

    gridLevels.forEach(level => {
        const y = padT + chartH - (level / 100) * chartH;
        ctx.beginPath();
        ctx.moveTo(padL, y);
        ctx.lineTo(padL + chartW, y);
        ctx.stroke();
        ctx.fillText(level + '%', padL - 6, y + 4);
    });

    // Bars
    data.forEach((d, i) => {
        const pct   = parseFloat(d.pct) || 0;
        const x     = padL + i * gap + (gap - barW) / 2;
        const barH  = (pct / 100) * chartH;
        const y     = padT + chartH - barH;
        const color = warna[d.key] || '#6366F1';

        // Bar dengan rounded top
        const radius = 6;
        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + barW - radius, y);
        ctx.quadraticCurveTo(x + barW, y, x + barW, y + radius);
        ctx.lineTo(x + barW, y + barH);
        ctx.lineTo(x, y + barH);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.closePath();
        ctx.fillStyle = color;
        ctx.fill();

        // Nilai di atas bar
        ctx.fillStyle  = '#18181B';
        ctx.font       = '600 12px sans-serif';
        ctx.textAlign  = 'center';
        ctx.fillText(Math.round(pct) + '%', x + barW / 2, y - 6);

        // Label bawah
        ctx.fillStyle = '#52525B';
        ctx.font      = '11px sans-serif';
        ctx.fillText(d.label, x + barW / 2, padT + chartH + 18);
    });
}

// =============================================
// FETCH DATA DARI API LALU RENDER
// =============================================
function loadBarKehadiran(canvasId) {
    fetch('/siswatrack/api/get_kehadiran.php')
        .then(res => res.json())
        .then(data => {
            if (data && Array.isArray(data)) {
                renderBarKehadiran(canvasId || 'chartKehadiran', data);
            }
        })
        .catch(() => console.error('Gagal mengambil data kehadiran'));
}