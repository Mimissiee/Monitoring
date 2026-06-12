<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiswaTrack</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sand-1: #FAFAF8;
            --sand-2: #F5F4F0;
            --sand-3: #ECEAE4;
            --sand-4: #D8D5CC;
            --sand-5: #B8B4A8;
            --sage:   #7C9A8A;
            --sage-lt:#EEF3F0;
            --sage-dk:#4A6B5A;
            --clay:   #C4956A;
            --clay-lt:#F5EDE4;
            --clay-dk:#8B6040;
            --slate:  #6B7A8D;
            --slate-lt:#EEF0F3;
            --slate-dk:#3D4F63;
            --rose:   #C4828A;
            --rose-lt:#F5ECED;
            --rose-dk:#8B4B52;
            --ink:    #1A1A18;
            --ink-2:  #4A4A46;
            --ink-3:  #8A8A84;
            --ink-4:  #C4C2BC;
            --bg:     #F2F1ED;
            --surface:#FFFFFF;
            --border: #E4E2DC;
            --border-2:#CCCAC4;
        }

        html, body { height: 100%; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 14px;
            background: var(--bg);
            color: var(--ink);
            line-height: 1.6;
        }

        /* ===== LAYOUT ===== */
        .wrapper {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            height: 100vh;
            overflow-y: auto;
            background: var(--bg);
        }

        .content {
            padding: 28px 32px;
            flex: 1;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 224px;
            min-width: 224px;
            background: #111110;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow-y: auto;
            flex-shrink: 0;
        }

        .sidebar-logo {
            padding: 22px 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }

        .logo-title {
            display: block;
            font-weight: 600;
            font-size: 15px;
            color: #F5F4F0;
            letter-spacing: -0.2px;
        }

        .logo-sub {
            display: block;
            font-size: 11px;
            color: rgba(255,255,255,0.28);
            margin-top: 2px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 10px 0;
        }

        .nav-section {
            font-size: 10px;
            color: rgba(255,255,255,0.2);
            padding: 14px 18px 4px;
            letter-spacing: 0.1em;
            font-weight: 600;
            text-transform: uppercase;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 18px;
            font-size: 13px;
            color: rgba(255,255,255,0.42);
            text-decoration: none;
            transition: all .12s;
            margin: 1px 8px;
            border-radius: 5px;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.78);
        }

        .nav-item.active {
            background: rgba(255,255,255,0.09);
            color: #F5F4F0;
            font-weight: 500;
        }

        .nav-icon {
            font-size: 13px;
            width: 18px;
            text-align: center;
            flex-shrink: 0;
            opacity: 0.7;
        }

        .nav-item.active .nav-icon { opacity: 1; }

        .sidebar-bottom {
            padding: 14px 12px;
            border-top: 1px solid rgba(255,255,255,0.07);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 10px 8px;
            border-radius: 6px;
            background: rgba(255,255,255,0.04);
            margin-bottom: 8px;
        }

        .user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 5px;
            background: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.7);
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .user-info { flex: 1; min-width: 0; }

        .user-nama {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: rgba(255,255,255,0.75);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            display: block;
            font-size: 10px;
            color: rgba(255,255,255,0.28);
            text-transform: capitalize;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 8px 12px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 5px;
            color: rgba(255,255,255,0.4);
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all .12s;
            font-family: inherit;
        }

        .btn-logout:hover {
            background: rgba(196,130,138,0.15);
            border-color: rgba(196,130,138,0.3);
            color: #E8A0A8;
        }

        /* ===== TOPBAR ===== */
        .topbar {
            height: 50px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .topbar-title {
            font-weight: 600;
            font-size: 14px;
            color: var(--ink);
            letter-spacing: -0.1px;
        }

        .topbar-date {
            font-size: 12px;
            color: var(--ink-3);
            background: var(--sand-2);
            padding: 4px 12px;
            border-radius: 4px;
            border: 1px solid var(--border);
        }

        /* ===== METRICS ===== */
        .metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .metric-card {
            background: var(--surface);
            border-radius: 6px;
            padding: 16px 18px;
            border: 1px solid var(--border);
        }

        .metric-label {
            font-size: 11px;
            color: var(--ink-3);
            font-weight: 500;
            letter-spacing: 0.04em;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .metric-val {
            font-size: 26px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        .metric-divider {
            width: 20px;
            height: 2px;
            background: var(--sand-3);
            border-radius: 2px;
            margin-bottom: 6px;
        }

        .metric-sub {
            font-size: 11px;
            color: var(--sage-dk);
        }

        .metric-sub.warn { color: var(--clay-dk); }

        /* ===== CARD ===== */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 20px 24px;
            margin-bottom: 16px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
            letter-spacing: -0.1px;
        }

        /* ===== GRID ===== */
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        /* ===== BADGE ===== */
        .badge {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 3px;
            font-weight: 500;
            display: inline-block;
        }

        .badge-ok     { background: var(--sage-lt);  color: var(--sage-dk);  border: 1px solid #C8DDD4; }
        .badge-warn   { background: var(--clay-lt);  color: var(--clay-dk);  border: 1px solid #E0CBAE; }
        .badge-danger { background: var(--rose-lt);  color: var(--rose-dk);  border: 1px solid #E0C0C4; }
        .badge-info   { background: var(--slate-lt); color: var(--slate-dk); border: 1px solid #C4CCD4; }
        .badge-purple { background: var(--sand-2);   color: var(--ink-2);    border: 1px solid var(--border); }

        .pill {
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 3px;
            background: var(--sand-2);
            color: var(--ink-3);
            border: 1px solid var(--border);
            font-weight: 500;
        }

        /* ===== TABLE ===== */
        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            table-layout: fixed;
        }

        thead th {
            text-align: left;
            padding: 9px 12px;
            font-weight: 500;
            font-size: 11px;
            color: var(--ink-3);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-bottom: 1px solid var(--border);
            background: var(--sand-1);
        }

        tbody td {
            padding: 11px 12px;
            border-bottom: 1px solid var(--border);
            color: var(--ink);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: var(--sand-1); }

        /* ===== FORM ===== */
        .form-group { margin-bottom: 16px; }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: var(--ink-2);
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 9px 12px;
            font-size: 13px;
            font-family: inherit;
            border: 1px solid var(--border-2);
            border-radius: 5px;
            outline: none;
            color: var(--ink);
            background: var(--surface);
            transition: border-color .12s, box-shadow .12s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--sage);
            box-shadow: 0 0 0 3px rgba(124,154,138,0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 90px;
            line-height: 1.6;
        }

        /* ===== BUTTON ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 500;
            border-radius: 5px;
            border: 1px solid var(--border-2);
            cursor: pointer;
            background: var(--surface);
            color: var(--ink-2);
            transition: all .12s;
            text-decoration: none;
            font-family: inherit;
        }

        .btn:hover {
            background: var(--sand-2);
            border-color: var(--sand-4);
            color: var(--ink);
        }

        .btn-primary {
            background: var(--ink);
            color: #F5F4F0;
            border-color: var(--ink);
        }

        .btn-primary:hover {
            background: #2C2C2A;
            border-color: #2C2C2A;
            color: #F5F4F0;
        }

        .btn-danger {
            background: var(--rose-lt);
            color: var(--rose-dk);
            border-color: #E0C0C4;
        }

        .btn-danger:hover {
            background: var(--rose);
            color: #fff;
            border-color: var(--rose);
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        /* ===== ALERT ===== */
        .alert {
            padding: 11px 16px;
            border-radius: 5px;
            font-size: 13px;
            margin-bottom: 20px;
            border-left: 3px solid;
            font-weight: 500;
        }

        .alert-success { background: var(--sage-lt);  color: var(--sage-dk);  border-color: var(--sage); }
        .alert-error   { background: var(--rose-lt);  color: var(--rose-dk);  border-color: var(--rose); }
        .alert-warning { background: var(--clay-lt);  color: var(--clay-dk);  border-color: var(--clay); }

        /* ===== BAR CHART ===== */
        .bar-row { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .bar-label { width: 80px; font-size: 12px; color: var(--ink-2); flex-shrink: 0; font-weight: 500; }
        .bar-track { flex: 1; height: 6px; background: var(--sand-3); border-radius: 3px; overflow: hidden; }
        .bar-fill { height: 100%; border-radius: 3px; transition: width .4s ease; }
        .bar-pct { width: 36px; text-align: right; font-size: 12px; font-weight: 600; color: var(--ink); }

        /* ===== CHECKLIST ===== */
        .checklist-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
        }
        .checklist-item:last-child { border-bottom: none; }
        .check-name { flex: 1; color: var(--ink); }

        .divider { border: none; border-top: 1px solid var(--border); margin: 16px 0; }

        a { color: var(--sage-dk); text-decoration: none; }
        a:hover { color: var(--sage); }

        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--sand-4); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--sand-5); }
    </style>
</head>
<body>
<div class="wrapper">