<?php
/**
 * Mandor Head Partial
 * Required vars: $page_title (string)
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0c4a6e">
    <title><?= esc($page_title ?? 'Mandor') ?> - Distribusi Gadget</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Sidebar */
            --sidebar-bg: #0c4a6e;
            --sidebar-width: 250px;
            --sidebar-hover: rgba(255,255,255,0.08);
            --sidebar-active-bg: rgba(56,189,248,0.15);
            --sidebar-active-color: #7dd3fc;
            --sidebar-text: #bae6fd;
            --sidebar-text-hover: #e0f2fe;
            --sidebar-border: rgba(255,255,255,0.08);
            /* Content */
            --content-bg: #f1f5f9;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
            --card-shadow-hover: 0 4px 12px rgba(0,0,0,0.08);
            --card-radius: 16px;
            /* Colors */
            --primary: #0284c7;
            --primary-soft: rgba(2,132,199,0.1);
            --success: #10b981;
            --success-soft: rgba(16,185,129,0.1);
            --warning: #f59e0b;
            --warning-soft: rgba(245,158,11,0.1);
            --danger: #ef4444;
            --danger-soft: rgba(239,68,68,0.1);
            --info: #06b6d4;
            --info-soft: rgba(6,182,212,0.1);
            /* Text */
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
        }

        * { box-sizing: border-box; }

        html { height: 100%; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--content-bg);
            margin: 0;
            padding: 0;
            min-height: 100%;
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
        }

        /* ======== SIDEBAR ======== */
        .sidebar {
            background: var(--sidebar-bg);
            height: 100vh;
            height: 100dvh;
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            overflow-x: hidden;
            border-right: 1px solid var(--sidebar-border);
        }

        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

        .sidebar-brand {
            padding: 24px 20px 20px;
            border-bottom: 1px solid var(--sidebar-border);
        }

        .sidebar-brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .sidebar-brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(14,165,233,0.3);
        }

        .sidebar-brand-icon i { color: #fff; font-size: 1.15rem; }

        .sidebar-brand-text h6 {
            color: #e0f2fe;
            font-weight: 700;
            font-size: 0.9rem;
            margin: 0;
            letter-spacing: -0.01em;
        }

        .sidebar-brand-text span {
            color: var(--sidebar-text);
            font-size: 0.7rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .sidebar-section {
            padding: 16px 12px 8px;
        }

        .sidebar-section-title {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(186,230,253,0.4);
            padding: 0 12px;
            margin-bottom: 8px;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding: 0 12px;
        }

        .sidebar-nav .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            border-radius: 10px;
            transition: all 0.2s ease;
            position: relative;
        }

        .sidebar-nav .nav-item:hover {
            background: var(--sidebar-hover);
            color: var(--sidebar-text-hover);
        }

        .sidebar-nav .nav-item.active {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-active-color);
        }

        .sidebar-nav .nav-item.active::before {
            content: '';
            position: absolute;
            left: -12px;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 24px;
            background: var(--sidebar-active-color);
            border-radius: 0 3px 3px 0;
        }

        .sidebar-nav .nav-item i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding: 16px 12px 20px;
            border-top: 1px solid var(--sidebar-border);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            margin-bottom: 12px;
        }

        .sidebar-user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-weight: 700;
            font-size: 0.8rem;
            color: #fff;
        }

        .sidebar-user-info h6 {
            color: #e0f2fe;
            font-weight: 600;
            font-size: 0.8rem;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 130px;
        }

        .sidebar-user-info span {
            color: var(--sidebar-text);
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .sidebar-user-info .status-dot {
            width: 6px;
            height: 6px;
            background: var(--success);
            border-radius: 50%;
            display: inline-block;
        }

        .sidebar-logout {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            color: #fca5a5;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .sidebar-logout:hover {
            background: rgba(239,68,68,0.1);
            color: #fecaca;
        }

        /* ======== SIDEBAR OVERLAY ======== */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 1035;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* ======== MOBILE TOPBAR ======== */
        .mobile-topbar {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--card-border);
            z-index: 1030;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
        }

        .mobile-topbar .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .mobile-topbar .btn-sidebar-toggle {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 1px solid var(--card-border);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-primary);
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.2s ease;
            -webkit-tap-highlight-color: transparent;
        }

        .mobile-topbar .btn-sidebar-toggle:active {
            transform: scale(0.93);
            background: var(--content-bg);
        }

        .mobile-topbar .topbar-title {
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text-primary);
        }

        .mobile-topbar .topbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .mobile-topbar .topbar-avatar {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.75rem;
            color: #fff;
        }

        /* ======== CONTENT AREA ======== */
        .content {
            margin-left: var(--sidebar-width);
            padding: 28px 32px 40px;
            min-height: 100vh;
            min-height: 100dvh;
        }

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-header h4 {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--text-primary);
            margin: 0;
            letter-spacing: -0.02em;
        }

        .page-header .header-sub {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 400;
            margin-top: 2px;
        }

        /* ======== STAT CARDS ======== */
        .stat-card {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 20px;
            border: 1px solid var(--card-border);
            box-shadow: var(--card-shadow);
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: var(--card-radius) var(--card-radius) 0 0;
        }

        .stat-card:hover {
            box-shadow: var(--card-shadow-hover);
            transform: translateY(-2px);
        }

        .stat-card .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 14px;
        }

        .stat-card .stat-label {
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1;
            letter-spacing: -0.02em;
        }

        .stat-card.primary::before { background: var(--primary); }
        .stat-card.primary .stat-icon { background: var(--primary-soft); color: var(--primary); }
        .stat-card.success::before { background: var(--success); }
        .stat-card.success .stat-icon { background: var(--success-soft); color: var(--success); }
        .stat-card.warning::before { background: var(--warning); }
        .stat-card.warning .stat-icon { background: var(--warning-soft); color: var(--warning); }
        .stat-card.danger::before { background: var(--danger); }
        .stat-card.danger .stat-icon { background: var(--danger-soft); color: var(--danger); }

        /* ======== DATA CARDS ======== */
        .data-card {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            border: 1px solid var(--card-border);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .data-card .data-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            border-bottom: 1px solid var(--card-border);
            flex-wrap: wrap;
            gap: 12px;
        }

        .data-card .data-card-header h5,
        .data-card .data-card-header h6 {
            font-weight: 700;
            font-size: 1rem;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .data-card .data-card-body {
            padding: 20px 24px;
        }

        /* ======== TABLE ======== */
        .table { margin-bottom: 0; }
        .table thead th {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            background: #f8fafc;
            border-bottom: 1px solid var(--card-border);
            padding: 14px 16px;
            white-space: nowrap;
        }
        .table tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
            color: var(--text-primary);
        }
        .table tbody tr:hover { background: #f8fafc; }
        .table tbody tr:last-child td { border-bottom: none; }

        /* ======== BADGES ======== */
        .badge-soft-primary { background: var(--primary-soft); color: var(--primary); font-weight: 600; }
        .badge-soft-success { background: var(--success-soft); color: var(--success); font-weight: 600; }
        .badge-soft-warning { background: var(--warning-soft); color: var(--warning); font-weight: 600; }
        .badge-soft-danger  { background: var(--danger-soft);  color: var(--danger);  font-weight: 600; }

        .badge {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* ======== BUTTONS ======== */
        .btn { font-family: 'Inter', sans-serif; font-weight: 600; border-radius: 10px; transition: all 0.2s ease; }
        .btn:active { transform: scale(0.96); }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: #0369a1; border-color: #0369a1; }

        /* ======== FORM ======== */
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid var(--card-border);
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-soft);
        }

        /* ======== EMPTY STATE ======== */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
        }
        .empty-state-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: var(--primary-soft);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 16px;
        }
        .empty-state p {
            color: var(--text-muted);
            font-size: 0.9rem;
            max-width: 300px;
            margin: 0 auto;
        }

        /* ======== ANIMATIONS ======== */
        .content > * {
            animation: contentFadeIn 0.4s ease forwards;
            opacity: 0;
        }
        .content > *:nth-child(1) { animation-delay: 0.05s; }
        .content > *:nth-child(2) { animation-delay: 0.1s; }
        .content > *:nth-child(3) { animation-delay: 0.15s; }
        .content > *:nth-child(4) { animation-delay: 0.2s; }
        .content > *:nth-child(5) { animation-delay: 0.25s; }

        @keyframes contentFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ======== RESPONSIVE ======== */
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .content { margin-left: 0; padding: 80px 16px 32px; }
            .mobile-topbar { display: flex; }
            .page-header h4 { font-size: 1.25rem; }
        }

        @media (max-width: 576px) {
            .stat-card .stat-value { font-size: 1.35rem; }
            .stat-card { padding: 16px; }
            .data-card .data-card-header { padding: 14px 16px; }
            .data-card .data-card-body { padding: 16px; }
        }

        /* ======== MISC ======== */
        h4, h5, h6 { font-weight: 700; color: var(--text-primary); }
        .font-monospace { font-family: 'JetBrains Mono', 'Fira Code', monospace; }
        .card { border-radius: var(--card-radius); border: 1px solid var(--card-border); }
        .modal-content { border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
    </style>
</head>
<body>
