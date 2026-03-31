<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#1a1a2e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Login - Sistem Distribusi Gadget</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary: #4f6ef7;
            --primary-dark: #3b5de7;
            --primary-light: rgba(79, 110, 247, 0.12);
            --primary-glow: rgba(79, 110, 247, 0.35);
            --bg-dark: #0f0f23;
            --bg-card: rgba(255, 255, 255, 0.97);
            --text-dark: #1a1a2e;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --border-color: #e5e7eb;
            --danger: #ef4444;
            --danger-bg: #fef2f2;
            --success: #10b981;
            --radius-lg: 20px;
            --radius-md: 14px;
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
            background: var(--bg-dark);
            min-height: 100%;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            padding-top: calc(20px + var(--safe-top));
            padding-bottom: calc(20px + var(--safe-bottom));
            overflow-x: hidden;
            position: relative;
        }

        /* Animated gradient background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(ellipse at 20% 20%, rgba(79, 110, 247, 0.25) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(168, 85, 247, 0.2) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(59, 93, 231, 0.1) 0%, transparent 70%);
            z-index: 0;
            animation: bgPulse 8s ease-in-out infinite alternate;
        }

        @keyframes bgPulse {
            0% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        /* Floating orbs for visual depth */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(60px);
            z-index: 0;
            pointer-events: none;
        }
        .orb-1 {
            width: 200px; height: 200px;
            background: rgba(79, 110, 247, 0.3);
            top: -50px; left: -50px;
            animation: float1 6s ease-in-out infinite;
        }
        .orb-2 {
            width: 150px; height: 150px;
            background: rgba(168, 85, 247, 0.25);
            bottom: -30px; right: -30px;
            animation: float2 7s ease-in-out infinite;
        }
        .orb-3 {
            width: 100px; height: 100px;
            background: rgba(16, 185, 129, 0.2);
            top: 30%; right: 10%;
            animation: float3 5s ease-in-out infinite;
        }

        @keyframes float1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, 20px) scale(1.1); }
        }
        @keyframes float2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-20px, -30px) scale(1.05); }
        }
        @keyframes float3 {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-15px, 15px); }
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            z-index: 1;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes slideUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            box-shadow: 
                0 4px 6px rgba(0, 0, 0, 0.05),
                0 20px 50px rgba(0, 0, 0, 0.12);
            padding: 32px 24px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        /* Subtle top gradient accent */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), #a855f7, var(--success));
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        }

        /* Header */
        .login-header {
            text-align: center;
            margin-bottom: 28px;
            padding-top: 8px;
        }

        .login-icon {
            width: 68px;
            height: 68px;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--primary), #6366f1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 8px 24px var(--primary-glow);
            animation: iconPulse 3s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { box-shadow: 0 8px 24px var(--primary-glow); }
            50% { box-shadow: 0 8px 32px rgba(79, 110, 247, 0.5); }
        }

        .login-icon i {
            font-size: 1.75rem;
            color: #fff;
        }

        .login-header h1 {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }

        .login-header p {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin: 0;
        }

        /* Alert */
        .alert-error {
            background: var(--danger-bg);
            border: 1px solid rgba(239, 68, 68, 0.15);
            border-radius: var(--radius-sm);
            padding: 14px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            animation: shakeIn 0.4s ease;
        }

        @keyframes shakeIn {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-6px); }
            40% { transform: translateX(6px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
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

        /* Form */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background: #f9fafb;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
            overflow: hidden;
        }

        .input-wrapper:focus-within {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px var(--primary-light);
        }

        .input-wrapper .input-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            min-width: 52px;
            height: 54px;
            color: var(--text-muted);
            font-size: 1.15rem;
            transition: color 0.2s ease;
        }

        .input-wrapper:focus-within .input-icon {
            color: var(--primary);
        }

        .input-wrapper input {
            flex: 1;
            border: none;
            background: transparent;
            padding: 16px 16px 16px 0;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            outline: none;
            -webkit-appearance: none;
            min-height: 54px;
        }

        .input-wrapper input::placeholder {
            color: var(--text-muted);
            font-size: 0.9rem;
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

        /* Password toggle button */
        .btn-toggle-pw {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            min-width: 50px;
            height: 54px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-size: 1.2rem;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            transition: color 0.2s ease;
        }

        .btn-toggle-pw:active {
            color: var(--primary);
        }

        /* Admin Toggle Switch */
        .admin-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            background: #f9fafb;
            border-radius: var(--radius-md);
            border: 2px solid var(--border-color);
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            transition: all 0.2s ease;
            user-select: none;
            min-height: 54px;
        }

        .admin-toggle:active {
            transform: scale(0.98);
        }

        .admin-toggle.active {
            background: rgba(168, 85, 247, 0.06);
            border-color: rgba(168, 85, 247, 0.3);
        }

        .admin-toggle input[type="checkbox"] {
            display: none;
        }

        .toggle-switch {
            position: relative;
            width: 48px;
            min-width: 48px;
            height: 28px;
            background: #d1d5db;
            border-radius: 14px;
            transition: background 0.3s ease;
        }

        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 22px;
            height: 22px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .admin-toggle.active .toggle-switch {
            background: #a855f7;
        }

        .admin-toggle.active .toggle-switch::after {
            transform: translateX(20px);
        }

        .toggle-label {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
        }

        .toggle-label-text {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .toggle-label-hint {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .toggle-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(168, 85, 247, 0.1);
            color: #a855f7;
            font-size: 1rem;
            flex-shrink: 0;
        }

        /* Password Section */
        .password-section {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.16, 1, 0.3, 1), 
                        opacity 0.3s ease,
                        margin 0.3s ease;
            opacity: 0;
            margin-top: 0;
        }

        .password-section.show {
            max-height: 150px;
            opacity: 1;
            margin-top: 20px;
        }

        /* Submit Button */
        .btn-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 16px 24px;
            background: linear-gradient(135deg, var(--primary), #6366f1);
            color: #fff;
            border: none;
            border-radius: var(--radius-md);
            font-size: 1rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            transition: all 0.2s ease;
            box-shadow: 0 4px 16px var(--primary-glow);
            min-height: 56px;
            margin-top: 24px;
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: left 0.5s ease;
        }

        .btn-submit:active {
            transform: scale(0.97);
            box-shadow: 0 2px 8px var(--primary-glow);
        }

        .btn-submit:active::before {
            left: 100%;
        }

        .btn-submit i {
            font-size: 1.15rem;
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.35);
            letter-spacing: 0.02em;
        }

        /* Responsive adjustments */
        @media (max-width: 380px) {
            body {
                padding: 16px;
            }
            .login-card {
                padding: 24px 20px;
                border-radius: 16px;
            }
            .login-header h1 {
                font-size: 1.2rem;
            }
            .login-icon {
                width: 60px;
                height: 60px;
                border-radius: 16px;
            }
            .login-icon i {
                font-size: 1.5rem;
            }
        }

        @media (min-width: 768px) {
            .login-card {
                padding: 40px 36px;
            }
            .login-header h1 {
                font-size: 1.5rem;
            }
        }

        /* Keyboard open adjustment - prevent content from being pushed off screen */
        @media (max-height: 500px) {
            body {
                justify-content: flex-start;
                padding-top: 10px;
            }
            .login-header {
                margin-bottom: 16px;
            }
            .login-icon {
                width: 48px;
                height: 48px;
                margin-bottom: 10px;
            }
            .login-icon i {
                font-size: 1.25rem;
            }
            .login-header h1 {
                font-size: 1.1rem;
            }
            .login-header p {
                display: none;
            }
            .form-group {
                margin-bottom: 14px;
            }
            .btn-submit {
                margin-top: 16px;
                min-height: 48px;
                padding: 12px 24px;
            }
            .login-footer {
                margin-top: 12px;
            }
        }

        /* Haptic feedback visual */
        @keyframes tapFeedback {
            0% { transform: scale(1); }
            50% { transform: scale(0.96); }
            100% { transform: scale(1); }
        }

        /* Loading state for button */
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
            width: 22px;
            height: 22px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<!-- Floating orbs for visual depth -->
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

<div class="login-wrapper">
    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <div class="login-icon">
                <i class="bi bi-phone"></i>
            </div>
            <h1>Sistem Distribusi Tas & Raincase Gadget</h1>
            <p>Silakan login untuk melanjutkan</p>
        </div>

        <!-- Error Alert -->
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert-error">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span><?= esc(session()->getFlashdata('error')) ?></span>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="<?= base_url('auth/process') ?>" method="post" id="loginForm">
            <?= csrf_field() ?>

            <!-- NPK Input -->
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

            <!-- Admin Toggle -->
            <div class="form-group">
                <div class="admin-toggle" id="adminToggle" role="switch" aria-checked="false" tabindex="0">
                    <div class="toggle-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <div class="toggle-label">
                        <span class="toggle-label-text">Login sebagai Admin</span>
                        <span class="toggle-label-hint">Aktifkan jika Anda administrator</span>
                    </div>
                    <div class="toggle-switch"></div>
                    <input type="checkbox" id="isAdmin" name="isAdmin">
                </div>
            </div>

            <!-- Password Section -->
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
                    <button type="button" class="btn-toggle-pw" id="togglePwBtn" aria-label="Tampilkan password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-submit" id="submitBtn">
                <i class="bi bi-box-arrow-in-right"></i>
                <span class="btn-text">Masuk</span>
            </button>
        </form>
    </div>

    <div class="login-footer">
        &copy; <?= date('Y') ?> Operasional Kebun
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

    // Set admin mode ON or OFF
    function setAdminMode(active) {
        if (active) {
            adminToggle.classList.add('active');
            isAdminCb.checked = true;
            adminToggle.setAttribute('aria-checked', 'true');
            passwordSection.classList.add('show');
            passwordInput.required = true;
            // Switch to text keyboard for admin username
            npkInput.inputMode = 'text';
            npkInput.placeholder = 'Masukkan Username';
            // Delay focus to allow animation
            setTimeout(() => passwordInput.focus(), 300);
        } else {
            adminToggle.classList.remove('active');
            isAdminCb.checked = false;
            adminToggle.setAttribute('aria-checked', 'false');
            passwordSection.classList.remove('show');
            passwordInput.required = false;
            passwordInput.value = '';
            // Switch back to numeric keyboard for NPK
            npkInput.inputMode = 'numeric';
            npkInput.placeholder = 'Masukkan NPK (7 digit)';
        }
    }

    // Admin toggle click handler
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

    // Password visibility toggle
    let pwVisible = false;
    togglePwBtn.addEventListener('click', function() {
        pwVisible = !pwVisible;
        passwordInput.type = pwVisible ? 'text' : 'password';
        togglePwBtn.querySelector('i').className = pwVisible ? 'bi bi-eye-slash' : 'bi bi-eye';
        togglePwBtn.setAttribute('aria-label', pwVisible ? 'Sembunyikan password' : 'Tampilkan password');
    });

    // Form submit with loading state
    loginForm.addEventListener('submit', function() {
        submitBtn.classList.add('loading');
    });

    // NPK input: limit to 7 digits for mandor, allow text for admin
    npkInput.addEventListener('input', function() {
        if (!isAdminCb.checked) {
            // Only numeric for mandor, max 7 digits
            var cleaned = this.value.replace(/[^0-9]/g, '').slice(0, 7);
            // If user typed letters, auto-switch to admin mode
            if (this.value !== cleaned && /[a-zA-Z]/.test(this.value)) {
                setAdminMode(true);
                // Don't clean the value, let them type admin username
                return;
            }
            this.value = cleaned;
        }
    });

    // Prevent zoom on focus (iOS)
    document.querySelectorAll('input[type="text"], input[type="password"]').forEach(function(input) {
        input.addEventListener('focus', function() {
            // Scroll into view smoothly on mobile
            setTimeout(function() {
                input.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300);
        });
    });
})();
</script>
</body>
</html>
