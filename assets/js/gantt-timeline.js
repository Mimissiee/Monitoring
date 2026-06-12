// =============================================
// GANTT CHART — TIMELINE KEGIATAN
// =============================================

function renderGantt(canvasId, data) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !data || data.length === 0) return;

    const ctx   = canvas.getContext('2d');
    const W     = canvas.width;
    const padL  = 160;
    const padR  = 20;
    const padT  = 40;
    const rowH  = 36;
    const H     = padT + data.length * rowH + 20;

    canvas.height = H;
    ctx.clearRect(0, 0, W, H);

    const warna = {
        rabuan:      '#6366F1',
        mentoring:   '#14B8A6',
        operasional: '#F59E0B',
        binjas:      '#A78BFA',
    };

    // Cari range tanggal
    let minDate = new Date(data[0].mulai);
    let maxDate = new Date(data[0].selesai);

    data.forEach(d => {
        const s = new Date(d.mulai);
        const e = new Date(d.selesai);
        if (s < minDate) minDate = s;
        if (e > maxDate) maxDate = e;
    });

    // Tambah buffer
    minDate.setDate(minDate.getDate() - 2);
    maxDate.setDate(maxDate.getDate() + 2);

    const totalDays = (maxDate - minDate) / (1000 * 60 * 60 * 24);
    const chartW    = W - padL - padR;

    function dateToX(dateStr) {
        const d    = new Date(dateStr);
        const days = (d - minDate) / (1000 * 60 * 60 * 24);
        return padL + (days / totalDays) * chartW;
    }

    // Header bulan
    ctx.fillStyle = '#F4F4F5';
    ctx.fillRect(padL, 0, chartW, padT);

    ctx.fillStyle  = '#52525B';
    ctx.font       = '500 11px sans-serif';
    ctx.textAlign  = 'center';

    const tempDate = new Date(minDate);
    let lastMonth  = -1;
    while (tempDate <= maxDate) {
        const x   = dateToX(tempDate.toISOString().split('T')[0]);
        const mon = tempDate.getMonth();

        if (mon !== lastMonth) {
            const bulanNama = tempDate.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
            ctx.fillStyle = '#18181B';
            ctx.font      = '600 11px sans-serif';
            ctx.fillText(bulanNama, x + 30, 16);
            lastMonth = mon;

            // Garis bulan
            ctx.beginPath();
            ctx.moveTo(x, 0);
            ctx.lineTo(x, H);
            ctx.strokeStyle = '#E4E4E7';
            ctx.lineWidth   = 1;
            ctx.stroke();
        }

        // Tanggal
        if (totalDays <= 60) {
            ctx.fillStyle = '#A1A1AA';
            ctx.font      = '10px sans-serif';
            ctx.fillText(tempDate.getDate(), x, 30);
        }

        tempDate.setDate(tempDate.getDate() + 1);
    }

    // Garis horizontal grid
    data.forEach((d, i) => {
        const y = padT + i * rowH;
        ctx.beginPath();
        ctx.moveTo(0, y + rowH);
        ctx.lineTo(W, y + rowH);
        ctx.strokeStyle = '#F4F4F5';
        ctx.lineWidth   = 1;
        ctx.stroke();
    });

    // Bars dan labels
    data.forEach((d, i) => {
        const y      = padT + i * rowH;
        const x1     = dateToX(d.mulai);
        const x2     = Math.max(dateToX(d.selesai), x1 + 6);
        const barH   = 20;
        const barY   = y + (rowH - barH) / 2;
        const color  = warna[d.jenis] || '#6366F1';
        const radius = 4;

        // Label kiri
        ctx.fillStyle  = '#18181B';
        ctx.font       = '500 12px sans-serif';
        ctx.textAlign  = 'right';
        const label    = d.judul.length > 18 ? d.judul.substring(0, 18) + '…' : d.judul;
        ctx.fillText(label, padL - 8, y + rowH / 2 + 4);

        // Bar
        ctx.beginPath();
        ctx.moveTo(x1 + radius, barY);
        ctx.lineTo(x2 - radius, barY);
        ctx.quadraticCurveTo(x2, barY, x2, barY + radius);
        ctx.lineTo(x2, barY + barH - radius);
        ctx.quadraticCurveTo(x2, barY + barH, x2 - radius, barY + barH);
        ctx.lineTo(x1 + radius, barY + barH);
        ctx.quadraticCurveTo(x1, barY + barH, x1, barY + barH - radius);
        ctx.lineTo(x1, barY + radius);
        ctx.quadraticCurveTo(x1, barY, x1 + radius, barY);
        ctx.closePath();
        ctx.fillStyle = color + 'CC';
        ctx.fill();
        ctx.strokeStyle = color;
        ctx.lineWidth   = 1.5;
        ctx.stroke();
    });

    // Garis hari ini
    const today   = new Date().toISOString().split('T')[0];
    const todayX  = dateToX(today);
    if (todayX >= padL && todayX <= W - padR) {
        ctx.beginPath();
        ctx.moveTo(todayX, 0);
        ctx.lineTo(todayX, H);
        ctx.strokeStyle = '#EF4444';
        ctx.lineWidth   = 1.5;
        ctx.setLineDash([4, 3]);
        ctx.stroke();
        ctx.setLineDash([]);

        ctx.fillStyle  = '#EF4444';
        ctx.font       = '500 10px sans-serif';
        ctx.textAlign  = 'center';
        ctx.fillText('Hari ini', todayX, H - 4);
    }
}

// =============================================
// FETCH DATA DARI API LALU RENDER
// =============================================
function loadGantt(canvasId) {
    fetch('/siswatrack/api/get_timeline.php')
        .then(res => res.json())
        .then(data => {
            if (data && Array.isArray(data)) {
                renderGantt(canvasId || 'chartGantt', data);
            }
        })
        .catch(() => console.error('Gagal mengambil data timeline'));
}