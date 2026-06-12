// =============================================
// SISWATRACK — Global Script
// =============================================

// Jalankan setelah DOM siap
document.addEventListener('DOMContentLoaded', function () {
    initAlertAutoDismiss();
    initConfirmDelete();
    initActiveNav();
    showFlashFromURL();
});

// =============================================
// 1. AUTO DISMISS ALERT
// =============================================
function initAlertAutoDismiss() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity    = '0';
            setTimeout(function () {
                alert.remove();
            }, 500);
        }, 4000);
    });
}

// =============================================
// 2. KONFIRMASI HAPUS
// =============================================
function initConfirmDelete() {
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const pesan = el.getAttribute('data-confirm') || 'Yakin ingin menghapus data ini?';
            if (!confirm(pesan)) {
                e.preventDefault();
            }
        });
    });
}

// =============================================
// 3. TANDAI NAV AKTIF
// =============================================
function initActiveNav() {
    const path     = window.location.pathname;
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(function (item) {
        const href = item.getAttribute('href');
        if (href && path.includes(href.split('/').pop().replace('.php', ''))) {
            item.classList.add('active');
        }
    });
}

// =============================================
// 4. FLASH MESSAGE DARI URL PARAMETER
// =============================================
function showFlashFromURL() {
    const params  = new URLSearchParams(window.location.search);
    const success = params.get('success');
    const error   = params.get('error');

    if (success) showToast(decodeURIComponent(success), 'success');
    if (error)   showToast(decodeURIComponent(error),   'error');
}

// =============================================
// 5. TOAST NOTIFICATION
// =============================================
function showToast(pesan, tipe) {
    const warna = {
        success: { bg: 'var(--green-lt)',  color: 'var(--green-dk)',  border: 'var(--green)' },
        error:   { bg: 'var(--red-lt)',    color: 'var(--red-dk)',    border: 'var(--red)' },
        warning: { bg: 'var(--amber-lt)',  color: 'var(--amber-dk)',  border: 'var(--amber)' },
        info:    { bg: 'var(--indigo-lt)', color: 'var(--indigo-dk)', border: 'var(--indigo)' },
    };

    const w = warna[tipe] || warna.info;

    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        padding: 12px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        font-family: sans-serif;
        background: ${w.bg};
        color: ${w.color};
        border-left: 4px solid ${w.border};
        max-width: 320px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: opacity 0.4s ease;
        opacity: 0;
    `;
    toast.textContent = pesan;
    document.body.appendChild(toast);

    setTimeout(() => toast.style.opacity = '1', 50);
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 400);
    }, 4000);
}

// =============================================
// 6. TOGGLE SIDEBAR (mobile)
// =============================================
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.style.display = sidebar.style.display === 'none' ? 'flex' : 'none';
    }
}

// =============================================
// 7. FORMAT ANGKA
// =============================================
function formatAngka(n) {
    return new Intl.NumberFormat('id-ID').format(n);
}

// =============================================
// 8. FORMAT TANGGAL
// =============================================
function formatTanggal(dateStr) {
    const bulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    const d = new Date(dateStr);
    return d.getDate() + ' ' + bulan[d.getMonth()] + ' ' + d.getFullYear();
}

// =============================================
// 9. KONFIRMASI SEBELUM LEAVE PAGE
// =============================================
function initFormDirty() {
    const forms = document.querySelectorAll('form[data-dirty]');
    forms.forEach(function (form) {
        let isDirty = false;
        form.addEventListener('input', () => isDirty = true);
        window.addEventListener('beforeunload', function (e) {
            if (isDirty) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
        form.addEventListener('submit', () => isDirty = false);
    });
}

// =============================================
// 10. TANDAI SEMUA RADIO HADIR
// =============================================
function tandaiSemuaHadir() {
    document.querySelectorAll('input[type="radio"][value="hadir"]').forEach(function (r) {
        r.checked = true;
    });
}