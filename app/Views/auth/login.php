<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#f8f9fa">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>Login - Sistem Distribusi Gadget</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-light: rgba(37, 99, 235, 0.08);
            --card-bg: rgba(255, 255, 255, 0.92);
            --text: #111827;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --border: rgba(0, 0, 0, 0.08);
            --border-focus: rgba(37, 99, 235, 0.4);
            --danger: #dc2626;
            --danger-bg: #fef2f2;
            --danger-border: #fecaca;
            --radius: 16px;
            --radius-sm: 10px;
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
        }

        html {
            height: 100%;
            -webkit-text-size-adjust: 100%;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f0f2f5;
            min-height: 100%;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            padding-top: calc(24px + var(--safe-top));
            padding-bottom: calc(24px + var(--safe-bottom));
            overflow: hidden;
            position: relative;
        }

        /* Mesh gradient background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: 
                radial-gradient(ellipse 70% 50% at 10% 15%, rgba(147, 197, 253, 0.5) 0%, transparent 60%),
                radial-gradient(ellipse 60% 60% at 85% 75%, rgba(167, 139, 250, 0.4) 0%, transparent 55%),
                radial-gradient(ellipse 50% 40% at 60% 10%, rgba(251, 191, 239, 0.35) 0%, transparent 50%),
                radial-gradient(ellipse 55% 55% at 25% 85%, rgba(165, 243, 252, 0.4) 0%, transparent 55%);
            animation: meshMove 15s ease-in-out infinite alternate;
            z-index: 0;
        }

        @keyframes meshMove {
            0% {
                background-position: 0% 0%, 100% 100%, 60% 10%, 25% 85%;
                filter: hue-rotate(0deg);
            }
            33% {
                filter: hue-rotate(5deg);
            }
            66% {
                filter: hue-rotate(-5deg);
            }
            100% {
                background-position: 15% 10%, 85% 85%, 55% 15%, 30% 80%;
                filter: hue-rotate(0deg);
            }
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.5s ease forwards;
            opacity: 0;
            transform: translateY(16px);
        }

        @keyframes fadeIn {
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: var(--radius);
            box-shadow: 
                0 2px 8px rgba(0, 0, 0, 0.06),
                0 12px 32px rgba(0, 0, 0, 0.04);
            padding: 32px 24px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .brand {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary), #4f46e5);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        .brand-icon i {
            font-size: 1.5rem;
            color: #fff;
        }

        .brand h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }

        .brand p {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .alert-error {
            background: var(--danger-bg);
            border: 1px solid var(--danger-border);
            border-radius: var(--radius-sm);
            padding: 12px 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .alert-error i {
            color: var(--danger);
            font-size: 1.1rem;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alert-error span {
            font-size: 0.875rem;
            color: #991b1b;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background: #f9fafb;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
        }

        .input-wrapper:focus-within {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .input-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            min-width: 44px;
            height: 48px;
            color: var(--text-muted);
            font-size: 1.1rem;
            transition: color 0.2s ease;
        }

        .input-wrapper:focus-within .input-icon {
            color: var(--primary);
        }

        .input-wrapper input {
            flex: 1;
            border: none;
            background: transparent;
            padding: 14px 14px 14px 0;
            font-size: 0.9375rem;
            font-family: 'Inter', sans-serif;
            color: var(--text);
            outline: none;
            -webkit-appearance: none;
            min-height: 48px;
        }

        .input-wrapper input::placeholder {
            color: var(--text-muted);
        }

        .input-hint {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .input-hint i {
            font-size: 0.85rem;
        }

        .btn-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            min-width: 44px;
            height: 48px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-size: 1.1rem;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            transition: color 0.2s ease;
        }

        .btn-toggle:hover {
            color: var(--text-secondary);
        }

        .btn-toggle:active {
            transform: scale(0.92);
        }

        .role-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            background: #f9fafb;
            border-radius: var(--radius-sm);
            border: 1.5px solid var(--border);
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            transition: all 0.2s ease;
            user-select: none;
            min-height: 48px;
        }

        .role-toggle:active {
            transform: scale(0.98);
        }

        .role-toggle.active {
            background: var(--primary-light);
            border-color: rgba(37, 99, 235, 0.3);
        }

        .role-toggle input[type="checkbox"] {
            display: none;
        }

        .toggle-switch {
            position: relative;
            width: 44px;
            min-width: 44px;
            height: 24px;
            background: #d1d5db;
            border-radius: 12px;
            transition: background 0.25s ease;
        }

        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 20px;
            height: 20px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.15);
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .role-toggle.active .toggle-switch {
            background: var(--primary);
        }

        .role-toggle.active .toggle-switch::after {
            transform: translateX(20px);
        }

        .toggle-content {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
        }

        .toggle-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text);
        }

        .toggle-desc {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .password-section {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease, opacity 0.25s ease, margin 0.25s ease;
            opacity: 0;
            margin-top: 0;
        }

        .password-section.show {
            max-height: 140px;
            opacity: 1;
            margin-top: 18px;
        }

        .btn-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, var(--primary), #4f46e5);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 0.9375rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
            min-height: 48px;
            margin-top: 24px;
        }

        .btn-submit:hover {
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
        }

        .btn-submit:active {
            transform: scale(0.98);
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
        }

        .btn-submit i {
            font-size: 1.1rem;
        }

        .btn-submit.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-submit.loading .btn-text {
            visibility: hidden;
        }

        .btn-submit.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2.5px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.8rem;
            color: #111827;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .login-footer .heart {
            color: #ef4444;
            display: inline-block;
            animation: heartbeat 1.5s ease-in-out infinite;
        }

        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            15% { transform: scale(1.15); }
            30% { transform: scale(1); }
            45% { transform: scale(1.1); }
            60% { transform: scale(1); }
        }

        @media (max-width: 380px) {
            body { padding: 16px; }
            .login-card { padding: 24px 20px; border-radius: 14px; }
            .brand h1 { font-size: 1.125rem; }
            .brand-icon { width: 50px; height: 50px; }
        }

        @media (min-width: 768px) {
            .login-card { padding: 36px 32px; }
        }

        @media (max-height: 500px) {
            body { justify-content: flex-start; padding-top: 10px; }
            .brand { margin-bottom: 16px; }
            .brand-icon { width: 44px; height: 44px; margin-bottom: 10px; }
            .brand-icon i { font-size: 1.25rem; }
            .brand h1 { font-size: 1rem; }
            .brand p { display: none; }
            .form-group { margin-bottom: 14px; }
            .btn-submit { margin-top: 16px; }
            .login-footer { margin-top: 12px; }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="brand">
            <div class="brand-icon">
                <i class="bi bi-phone"></i>
            </div>
            <h1>Sistem Distribusi Tas & Raincase</h1>
            <p>Silakan login untuk melanjutkan</p>
        </div>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert-error">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span><?= esc(session()->getFlashdata('error')) ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('auth/process') ?>" method="post" id="loginForm">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="npk">NPK / Username</label>
                <div class="input-wrapper">
                    <div class="input-icon">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <input
                        type="text"
                        id="npk"
                        name="npk"
                        required
                        autofocus
                        inputmode="numeric"
                        autocomplete="username"
                        placeholder="Masukkan NPK (7 digit)"
                    >
                </div>
                <div class="input-hint">
                    <i class="bi bi-info-circle"></i>
                    <span>Mandor: cukup masukkan NPK saja</span>
                </div>
            </div>

            <div class="form-group">
                <div class="role-toggle" id="adminToggle" role="switch" aria-checked="false" tabindex="0">
                    <div class="toggle-content">
                        <span class="toggle-title">Login sebagai Admin</span>
                        <span class="toggle-desc">Aktifkan jika Anda administrator</span>
                    </div>
                    <div class="toggle-switch"></div>
                    <input type="checkbox" id="isAdmin" name="isAdmin">
                </div>
            </div>

            <div class="password-section" id="passwordSection">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrapper">
                    <div class="input-icon">
                        <i class="bi bi-key-fill"></i>
                    </div>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        placeholder="Masukkan password"
                    >
                    <button type="button" class="btn-toggle" id="togglePwBtn" aria-label="Tampilkan password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <i class="bi bi-box-arrow-in-right"></i>
                <span class="btn-text">Masuk</span>
            </button>
        </form>
    </div>

    <div class="login-footer">
        <span>&copy; <?= date('Y') ?> Operasional Kebun</span>
        <span>Dibangun dengan <i class="bi bi-heart-fill heart"></i></span>
    </div>
</div>

<script>
(function() {
    'use strict';

    const adminToggle = document.getElementById('adminToggle');
    const isAdminCb = document.getElementById('isAdmin');
    const passwordSection = document.getElementById('passwordSection');
    const passwordInput = document.getElementById('password');
    const togglePwBtn = document.getElementById('togglePwBtn');
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    const npkInput = document.getElementById('npk');

    function setAdminMode(active) {
        if (active) {
            adminToggle.classList.add('active');
            isAdminCb.checked = true;
            adminToggle.setAttribute('aria-checked', 'true');
            passwordSection.classList.add('show');
            passwordInput.required = true;
            npkInput.inputMode = 'text';
            npkInput.placeholder = 'Masukkan Username';
            setTimeout(() => passwordInput.focus(), 300);
        } else {
            adminToggle.classList.remove('active');
            isAdminCb.checked = false;
            adminToggle.setAttribute('aria-checked', 'false');
            passwordSection.classList.remove('show');
            passwordInput.required = false;
            passwordInput.value = '';
            npkInput.inputMode = 'numeric';
            npkInput.placeholder = 'Masukkan NPK (7 digit)';
        }
    }

    function toggleAdmin() {
        const willBeActive = !adminToggle.classList.contains('active');
        setAdminMode(willBeActive);
    }

    adminToggle.addEventListener('click', toggleAdmin);
    adminToggle.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            toggleAdmin();
        }
    });

    let pwVisible = false;
    togglePwBtn.addEventListener('click', function() {
        pwVisible = !pwVisible;
        passwordInput.type = pwVisible ? 'text' : 'password';
        togglePwBtn.querySelector('i').className = pwVisible ? 'bi bi-eye-slash' : 'bi bi-eye';
    });

    loginForm.addEventListener('submit', function() {
        submitBtn.classList.add('loading');
    });

    npkInput.addEventListener('input', function() {
        if (!isAdminCb.checked) {
            var cleaned = this.value.replace(/[^0-9]/g, '').slice(0, 7);
            if (this.value !== cleaned && /[a-zA-Z]/.test(this.value)) {
                setAdminMode(true);
                return;
            }
            this.value = cleaned;
        }
    });

    document.querySelectorAll('input[type="text"], input[type="password"]').forEach(function(input) {
        input.addEventListener('focus', function() {
            setTimeout(function() {
                input.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300);
        });
    });
})();
</script>
</body>
</html>
