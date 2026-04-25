<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0f172a">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --bg: #ffffff;
            --bg-secondary: #f8fafc;
            --border: #e2e8f0;
            --text: #0f172a;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --primary: #2563eb;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --radius: 12px;
            --radius-lg: 16px;
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #ffffff; color: var(--text); min-height: 100vh; }
        .topbar { position: sticky; top: 0; z-index: 100; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid var(--border); padding: 1rem 1.25rem; }
        .topbar-content { max-width: 600px; margin: 0 auto; display: flex; align-items: center; gap: 0.75rem; }
        .topbar-icon { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary); border-radius: 10px; color: var(--primary); }
        .topbar-title h1 { font-size: 1rem; font-weight: 600; margin: 0; }
        .topbar-title p { font-size: 0.75rem; color: var(--text-muted); margin: 0; }
        
        .main { max-width: 600px; margin: 0 auto; padding: 1.5rem 1.25rem 3rem; }
        .alert-box { padding: 0.875rem 1rem; border-radius: var(--radius); margin-bottom: 1.25rem; font-size: 0.875rem; display: flex; align-items: flex-start; gap: 0.625rem; border: 1px solid #bbf7d0; background: #f0fdf4; color: #166534; }
        .alert-box.error { background: #fef2f2; color: #991b1b; border-color: #fecaca; }

        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; font-weight: 500; color: var(--text-secondary); margin-bottom: 0.5rem; text-transform: uppercase; }
        .form-input, .form-select { width: 100%; padding: 0.8125rem 1rem; border: 1.5px solid var(--border); border-radius: var(--radius); font-size: 0.9375rem; transition: all 0.15s ease; }
        .form-input:focus, .form-select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgb(37 99 235 / 0.1); }
        
        .btn-submit { width: 100%; padding: 0.9375rem 1.25rem; background: var(--text); color: #fff; border: none; border-radius: var(--radius); font-size: 0.9375rem; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 0.5rem; cursor: pointer; margin-top: 1.5rem; }
        .btn-submit.loading { opacity: 0.7; pointer-events: none; }
        
        .input-counter { text-align: right; font-size: 0.75rem; color: var(--text-muted); margin-top: 0.375rem; }
        .input-counter.complete { color: var(--success); font-weight: 600; }

        .modal-content { border: none; border-radius: var(--radius-lg); box-shadow: var(--shadow-md); }
        .validation-section { margin-bottom: 1rem; }
        .validation-label { font-size: 0.6875rem; font-weight: 500; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem; }
        .validation-value { font-size: 1rem; font-weight: 600; }
        .validation-divider { height: 1px; background: var(--border); margin: 1rem 0; }
        
        .owner-info-card { background: var(--bg-secondary); padding: 0.875rem; border-radius: 8px; margin-top: 0.5rem; font-size: 0.8125rem; }
        .btn-modal { flex: 1; padding: 0.875rem 1rem; border-radius: var(--radius); font-size: 0.9375rem; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 0.375rem; border: none; }
        .btn-modal.primary { background: var(--text); color: #fff; }
        .btn-modal.secondary { background: var(--bg-secondary); color: var(--text); border: 1.5px solid var(--border); }
    </style>
</head>
<body>

<div class="topbar">
    <div class="topbar-content">
        <div class="topbar-icon"><i class="bi bi-person-check"></i></div>
        <div class="topbar-title">
            <h1><?= $title ?></h1>
            <p>Khusus Karyawan Jabatan <?= $type ?></p>
        </div>
    </div>
</div>

<div class="main">
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert-box">
            <i class="bi bi-check-circle"></i>
            <div><?= session()->getFlashdata('success') ?></div>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert-box error">
            <i class="bi bi-exclamation-circle"></i>
            <div><?= session()->getFlashdata('error') ?></div>
        </div>
    <?php endif; ?>

    <form id="form-input-karyawan" action="<?= base_url('public/save-karyawan-gadget') ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="type" value="<?= $type ?>">

        <div class="form-group">
            <label class="form-label"><i class="bi bi-person-badge"></i> NIK Karyawan <span class="text-danger">*</span></label>
            <input type="text" name="npk" id="input-nik" class="form-input" required placeholder="Masukkan NIK Anda" inputmode="numeric" autocomplete="off">
        </div>

        <div class="form-group">
            <label class="form-label"><i class="bi bi-grid"></i> Aplikasi <span class="text-danger">*</span></label>
            <select name="aplikasi" class="form-select" required>
                <option value="">Pilih aplikasi</option>
                <?php foreach($applications as $app): ?>
                    <option value="<?= esc($app) ?>"><?= esc($app) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label"><i class="bi bi-upc-scan"></i> IMEI Gadget <span class="text-danger">*</span></label>
            <input type="text" name="imei" id="input-imei" class="form-input" required placeholder="15 digit IMEI" minlength="15" maxlength="15" inputmode="numeric" pattern="[0-9]{15}" autocomplete="off">
            <div class="input-counter" id="imei-counter">0/15</div>
        </div>

        <button type="submit" class="btn-submit" id="btn-submit">
            <i class="bi bi-arrow-right"></i> Simpan Data Aset
        </button>
    </form>
</div>

<div class="footer text-center text-muted small pb-4">
    Astra Agro Lestari &copy; <?= date('Y') ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    $('#input-imei').on('input', function() {
        var val = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(val);
        var len = val.length;
        $('#imei-counter').text(len + '/15').toggleClass('complete', len === 15);
    });

    $('#form-input-karyawan').on('submit', function(e) {
        if ($(this).data('verified')) {
            return; // Allow form submission
        }
        
        e.preventDefault();
        var nik = $('#input-nik').val().trim();
        var app = $('select[name="aplikasi"]').val();
        var imei = $('#input-imei').val().trim();
        
        if(!nik || !app || imei.length !== 15) return alert('Lengkapi data dengan benar (IMEI harus 15 digit).');

        $('#btn-submit').addClass('loading').prop('disabled', true);

        // Hanya verifikasi apakah NIK terdaftar di sistem sesuai jabatan
        $.get('<?= base_url('public/check-nik') ?>', { nik: nik, type: '<?= $type ?>' }, function(nikRes) {
            $('#btn-submit').removeClass('loading').prop('disabled', false);
            
            if(nikRes.status !== 'success') {
                return alert(nikRes.message);
            }

            // Jika NIK valid, langsung submit form tanpa modal konfirmasi
            $('#form-input-karyawan').data('verified', true).submit();
        }).fail(function() {
            $('#btn-submit').removeClass('loading').prop('disabled', false);
            alert('Terjadi kesalahan jaringan saat verifikasi NIK.');
        });
    });
});
</script>
</body>
</html>
