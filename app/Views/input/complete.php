<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#10b981">
    <title>Selesai - Sistem Distribusi Gadget</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
            background-color: #f1f5f9; 
            display: flex; 
            text-align: center; 
            justify-content: center; 
            min-height: 100vh;
            min-height: 100dvh;
            align-items: center;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
        }
        .done-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            max-width: 400px;
            width: 100%;
            overflow: hidden;
            animation: cardEntry 0.5s ease forwards;
        }
        @keyframes cardEntry {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .check-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(16,185,129,0.1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            animation: popIn 0.4s ease 0.2s forwards;
            transform: scale(0);
        }
        @keyframes popIn {
            0% { transform: scale(0); }
            70% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .btn { 
            font-family: 'Inter', sans-serif; 
            font-weight: 600; 
            border-radius: 12px; 
            transition: all 0.2s ease; 
        }
        .btn:active { transform: scale(0.96); }
    </style>
</head>
<body>
    <div class="done-card">
        <div style="padding: 48px 32px 32px;">
            <div class="check-circle">
                <i class="bi bi-check-lg" style="font-size: 2rem; color: #10b981;"></i>
            </div>
            <h4 class="fw-bold mb-2" style="color: #0f172a;">Input Selesai 🎉</h4>
            <p class="text-muted mb-4" style="font-size: 0.9rem;">Semua pekerja di Afdeling telah berhasil diproses.</p>
            <div class="d-grid">
                <a href="<?= base_url('auth/logout') ?>" class="btn btn-danger btn-lg py-3 fw-bold" style="font-size: 1rem;">
                    <i class="bi bi-box-arrow-left me-1"></i> Keluar / Logout
                </a>
            </div>
        </div>
        <div style="padding: 16px; border-top: 1px solid #e2e8f0; background: #fafbfc;">
            <small class="text-muted">Terima kasih, <strong><?= esc($nama_mandor) ?></strong> 👋</small>
        </div>
    </div>
</body>
</html>
