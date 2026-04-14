<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Input Data Gadget</title>
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
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            background: #ffffff;
            color: var(--text);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
        }

        /* Top Bar */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            padding: 1rem 1.25rem;
        }

        .topbar-content {
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .topbar-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-secondary);
            border-radius: 10px;
            color: var(--primary);
        }

        .topbar-title {
            flex: 1;
        }

        .topbar-title h1 {
            font-size: 1rem;
            font-weight: 600;
            line-height: 1.2;
            color: var(--text);
        }

        .topbar-title p {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.125rem;
        }

        /* Main Content */
        .main {
            max-width: 600px;
            margin: 0 auto;
            padding: 1.5rem 1.25rem 3rem;
        }

        /* Alerts */
        .alert-box {
            padding: 0.875rem 1rem;
            border-radius: var(--radius);
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            animation: slideDown 0.2s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-box.success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-box.error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-box i {
            font-size: 1.125rem;
            flex-shrink: 0;
            margin-top: 0.0625rem;
        }

        /* Form */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-label i {
            font-size: 1rem;
        }

        .form-label .required {
            color: var(--danger);
            margin-left: 0.125rem;
        }

        .form-input {
            width: 100%;
            padding: 0.8125rem 1rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            font-size: 0.9375rem;
            color: var(--text);
            background: var(--bg);
            transition: all 0.15s ease;
            -webkit-appearance: none;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgb(37 99 235 / 0.1);
        }

        .form-input::placeholder {
            color: var(--text-muted);
        }

        .form-select {
            width: 100%;
            padding: 0.8125rem 1rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            font-size: 0.9375rem;
            color: var(--text);
            background: var(--bg);
            transition: all 0.15s ease;
            cursor: pointer;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            padding-right: 2.5rem;
        }

        .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgb(37 99 235 / 0.1);
        }

        .input-counter {
            text-align: right;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.375rem;
            font-weight: 500;
        }

        .input-counter.complete {
            color: var(--success);
            font-weight: 600;
        }

        .input-hint {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.375rem;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 0.9375rem 1.25rem;
            background: var(--text);
            color: #fff;
            border: none;
            border-radius: var(--radius);
            font-size: 0.9375rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.15s ease;
            margin-top: 1.5rem;
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-submit.loading {
            pointer-events: none;
        }

        .btn-submit.loading i {
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 2rem 1.25rem;
            color: var(--text-muted);
            font-size: 0.75rem;
        }

        .footer strong {
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Modal Styling */
        .modal-content {
            border: none;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .modal-header {
            background: var(--bg);
            border-bottom: 1px solid var(--border);
            padding: 1.25rem;
        }

        .modal-title {
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modal-body {
            padding: 1.25rem;
        }

        .modal-footer {
            border-top: 1px solid var(--border);
            padding: 1rem 1.25rem 1.25rem;
            gap: 0.625rem;
        }

        /* Validation Data Display */
        .validation-section {
            margin-bottom: 1.25rem;
        }

        .validation-section:last-child {
            margin-bottom: 0;
        }

        .validation-label {
            font-size: 0.6875rem;
            font-weight: 500;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.375rem;
        }

        .validation-value {
            font-size: 1.0625rem;
            font-weight: 500;
            color: var(--text);
        }

        .validation-value.imei {
            font-size: 1.375rem;
            font-family: 'SF Mono', 'Fira Code', monospace;
            letter-spacing: 0.05em;
            color: var(--text);
        }

        .validation-divider {
            height: 1px;
            background: var(--border);
            margin: 1.25rem 0;
        }

        /* Validation Alert */
        .validation-alert {
            padding: 1rem;
            border-radius: var(--radius);
            margin-top: 1rem;
        }

        .validation-alert.success {
            background: #f0fdf4;
            border: 1.5px solid #bbf7d0;
        }

        .validation-alert.warning {
            background: #fffbeb;
            border: 1.5px solid #fef3c7;
        }

        .validation-alert.error {
            background: #fef2f2;
            border: 1.5px solid #fecaca;
        }

        .validation-alert-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .validation-alert.success .validation-alert-header {
            color: #166534;
        }

        .validation-alert.warning .validation-alert-header {
            color: #92400e;
        }

        .validation-alert.error .validation-alert-header {
            color: #991b1b;
        }

        .validation-alert-body {
            font-size: 0.875rem;
            line-height: 1.6;
        }

        .validation-alert.success .validation-alert-body {
            color: #166534;
        }

        .validation-alert.warning .validation-alert-body {
            color: #92400e;
        }

        .validation-alert.error .validation-alert-body {
            color: #991b1b;
        }

        .owner-info-card {
            background: #fff;
            padding: 0.875rem;
            border-radius: 8px;
            margin-top: 0.75rem;
        }

        .owner-info-card .row {
            font-size: 0.8125rem;
            margin-bottom: 0.375rem;
        }

        .owner-info-card .row:last-child {
            margin-bottom: 0;
        }

        .owner-info-card .label {
            color: var(--text-secondary);
        }

        .owner-info-card .value {
            font-weight: 500;
            color: var(--text);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.625rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-badge.success {
            background: #dcfce7;
            color: #166534;
        }

        .status-badge.danger {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Modal Buttons */
        .btn-modal {
            flex: 1;
            padding: 0.875rem 1rem;
            border-radius: var(--radius);
            font-size: 0.9375rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
            cursor: pointer;
            transition: all 0.15s ease;
            border: none;
        }

        .btn-modal:active {
            transform: scale(0.98);
        }

        .btn-modal.primary {
            background: var(--text);
            color: #fff;
        }

        .btn-modal.warning {
            background: var(--warning);
            color: #fff;
        }

        .btn-modal.success {
            background: var(--success);
            color: #fff;
        }

        .btn-modal.danger {
            background: var(--danger);
            color: #fff;
        }

        .btn-modal.secondary {
            background: var(--bg-secondary);
            color: var(--text);
            border: 1.5px solid var(--border);
        }
    </style>
</head>
<body>

<!-- Top Bar -->
<div class="topbar">
    <div class="topbar-content">
        <div class="topbar-icon">
            <i class="bi bi-phone"></i>
        </div>
        <div class="topbar-title">
            <h1>Data Gadget</h1>
            <p>Masukkan data gadget Anda</p>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="main">
    <!-- Alerts -->
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert-box success">
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

    <!-- Form -->
    <form id="form-input-gadget" action="<?= base_url('public/input-gadget') ?>" method="post">
        <?= csrf_field() ?>

        <!-- NPK -->
        <div class="form-group">
            <label class="form-label">
                <i class="bi bi-person-badge"></i>
                NPK
                <span class="required">*</span>
            </label>
            <input 
                type="text" 
                name="npk" 
                class="form-input" 
                required 
                placeholder="Masukkan NPK Anda" 
                value="<?= old('npk') ?>"
                inputmode="numeric"
                autocomplete="off"
            >
        </div>

        <!-- Aplikasi -->
        <div class="form-group">
            <label class="form-label">
                <i class="bi bi-grid"></i>
                Aplikasi
                <span class="required">*</span>
            </label>
            <select name="aplikasi" class="form-select" required>
                <option value="">Pilih aplikasi</option>
                <?php foreach($applications as $app): ?>
                    <option value="<?= esc($app) ?>" <?= old('aplikasi') == $app ? 'selected' : '' ?>><?= esc($app) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- IMEI -->
        <div class="form-group">
            <label class="form-label">
                <i class="bi bi-upc-scan"></i>
                IMEI
                <span class="required">*</span>
            </label>
            <input 
                type="text" 
                name="imei" 
                id="input-imei"
                class="form-input" 
                required 
                placeholder="15 digit IMEI" 
                minlength="15" 
                maxlength="15" 
                value="<?= old('imei') ?>"
                inputmode="numeric"
                pattern="[0-9]{15}"
                autocomplete="off"
            >
            <div class="input-counter" id="imei-counter">0/15</div>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn-submit" id="btn-submit">
            <i class="bi bi-arrow-right"></i>
            Simpan Data Aset
        </button>
    </form>
</div>

<!-- Footer -->
<div class="footer">
    Dibangun dengan <i class="bi bi-heart-fill" style="color: #ef4444;"></i> Cinta
</div>

<!-- Instruction Modal -->
<div class="modal fade" id="instructionModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-info-circle"></i>
                    Instruksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?= nl2br(esc($popup_instruction)) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal primary" data-bs-dismiss="modal">
                    Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Validation Modal -->
<div class="modal fade" id="validationModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-clipboard-check"></i>
                    Konfirmasi Data
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="validation-content">
                <!-- Dynamic content -->
            </div>
            <div class="modal-footer" id="validation-actions">
                <!-- Dynamic buttons -->
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Show instruction modal on load
    <?php if(!session()->getFlashdata('success')): ?>
    var myModal = new bootstrap.Modal(document.getElementById('instructionModal'));
    myModal.show();
    <?php endif; ?>

    // IMEI Counter
    $('#input-imei').on('input', function() {
        var val = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(val);
        
        var len = val.length;
        var counter = $('#imei-counter');
        counter.text(len + '/15');
        
        if(len === 15) {
            counter.addClass('complete');
        } else {
            counter.removeClass('complete');
        }
    });

    // NPK - Numbers only
    $('input[name="npk"]').on('input', function() {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });

    // Form Submit
    $('#form-input-gadget').on('submit', function(e) {
        e.preventDefault();
        
        var npk = $('input[name="npk"]').val().trim();
        var aplikasi = $('select[name="aplikasi"]').val();
        var imei = $('#input-imei').val().trim();
        
        if(!npk || !aplikasi || !imei) {
            alert('Semua field wajib diisi.');
            return;
        }
        
        if(imei.length !== 15) {
            alert('IMEI harus 15 digit.');
            return;
        }
        
        // Validate
        $.ajax({
            url: '<?= base_url('public/validate-imei') ?>',
            type: 'POST',
            data: { imei: imei, npk: npk },
            dataType: 'json',
            beforeSend: function() {
                $('#btn-submit').addClass('loading').prop('disabled', true);
                $('#btn-submit i').removeClass('bi-arrow-right').addClass('bi-hourglass-split');
            },
            success: function(res) {
                $('#btn-submit').removeClass('loading').prop('disabled', false);
                $('#btn-submit i').removeClass('bi-hourglass-split').addClass('bi-arrow-right');
                showValidation(npk, aplikasi, imei, res);
            },
            error: function() {
                $('#btn-submit').removeClass('loading').prop('disabled', false);
                $('#btn-submit i').removeClass('bi-hourglass-split').addClass('bi-arrow-right');
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        });
    });

    // Show Validation
    function showValidation(npk, aplikasi, imei, res) {
        var content = '';
        var actions = '';
        var canSave = false;
        
        // Build content
        content += '<div class="validation-section">';
        content += '<div class="validation-label">NPK</div>';
        content += '<div class="validation-value">' + npk + '</div>';
        content += '</div>';
        
        content += '<div class="validation-divider"></div>';
        
        content += '<div class="validation-section">';
        content += '<div class="validation-label">Aplikasi</div>';
        content += '<div class="validation-value">' + aplikasi + '</div>';
        content += '</div>';
        
        content += '<div class="validation-divider"></div>';
        
        content += '<div class="validation-section">';
        content += '<div class="validation-label">IMEI</div>';
        content += '<div class="validation-value imei">' + imei + '</div>';
        content += '</div>';
        
        // Status
        var alertHtml = '';
        if(res.status === 'not_registered') {
            alertHtml = '<div class="validation-alert error">';
            alertHtml += '<div class="validation-alert-header">';
            alertHtml += '<i class="bi bi-x-circle"></i>';
            alertHtml += '<span>IMEI Tidak Terdaftar</span>';
            alertHtml += '</div>';
            alertHtml += '<div class="validation-alert-body">';
            alertHtml += 'IMEI ini tidak ditemukan di Master Gadget.';
            alertHtml += '</div>';
            alertHtml += '</div>';
            canSave = false;
        } else if(res.status === 'owned_by_other') {
            alertHtml = '<div class="validation-alert warning">';
            alertHtml += '<div class="validation-alert-header">';
            alertHtml += '<i class="bi bi-exclamation-triangle"></i>';
            alertHtml += '<span>IMEI Tidak Cocok</span>';
            alertHtml += '</div>';
            alertHtml += '<div class="validation-alert-body">';
            alertHtml += 'IMEI terdaftar atas nama:';
            alertHtml += '<div class="owner-info-card">';
            alertHtml += '<div class="row"><span class="label">Nama:</span> <span class="value">' + res.owner_name + '</span></div>';
            alertHtml += '<div class="row"><span class="label">NPK:</span> <span class="value">' + res.owner_npk + '</span></div>';
            alertHtml += '<div class="row"><span class="label">Status:</span> <span class="value"><span class="status-badge danger">' + res.status_desc + '</span></span></div>';
            alertHtml += '</div>';
            alertHtml += '</div>';
            alertHtml += '</div>';
            canSave = true;
        } else if(res.status === 'matched') {
            alertHtml = '<div class="validation-alert success">';
            alertHtml += '<div class="validation-alert-header">';
            alertHtml += '<i class="bi bi-check-circle"></i>';
            alertHtml += '<span>IMEI Cocok' + (res.match_type === 'fuzzy' ? ' (NPK Mirip)' : '') + '</span>';
            alertHtml += '</div>';
            alertHtml += '<div class="validation-alert-body">';
            alertHtml += 'IMEI terdaftar dan cocok.';
            if(res.match_type === 'fuzzy') {
                alertHtml += '<br><small><i class="bi bi-info-circle"></i> NPK Anda mirip dengan data di Master Gadget.</small>';
            }
            alertHtml += '<div class="owner-info-card">';
            alertHtml += '<div class="row"><span class="label">Nama:</span> <span class="value">' + res.owner_name + '</span></div>';
            alertHtml += '<div class="row"><span class="label">NPK:</span> <span class="value">' + res.owner_npk + '</span></div>';
            alertHtml += '<div class="row"><span class="label">Status:</span> <span class="value"><span class="status-badge success">' + res.status_desc + '</span></span></div>';
            alertHtml += '</div>';
            alertHtml += '</div>';
            alertHtml += '</div>';
            canSave = true;
        }
        
        content += alertHtml;
        $('#validation-content').html(content);
        
        // Buttons
        if(canSave) {
            var btnClass = res.status === 'matched' ? 'success' : 'warning';
            var btnText = res.status === 'matched' ? 'Simpan Data' : 'Lanjutkan Simpan';
            actions += '<button type="button" class="btn-modal ' + btnClass + '" id="btn-confirm">';
            actions += '<i class="bi bi-check"></i>' + btnText;
            actions += '</button>';
            actions += '<button type="button" class="btn-modal secondary" data-bs-dismiss="modal">';
            actions += '<i class="bi bi-x"></i>Batal';
            actions += '</button>';
        } else {
            actions += '<button type="button" class="btn-modal danger" data-bs-dismiss="modal">';
            actions += '<i class="bi bi-x"></i>Tutup';
            actions += '</button>';
            actions += '<button type="button" class="btn-modal secondary" id="btn-back">';
            actions += '<i class="bi bi-arrow-left"></i>Kembali';
            actions += '</button>';
        }
        
        $('#validation-actions').html(actions);
        
        // Bind events
        $('#btn-confirm').on('click', function() {
            $('#form-input-gadget').data('confirmed', true);
            bootstrap.Modal.getInstance(document.getElementById('validationModal')).hide();
            setTimeout(function() {
                $('#form-input-gadget').off('submit').submit();
            }, 300);
        });
        
        $('#btn-back').on('click', function() {
            $('#input-imei').focus().select();
        });
        
        // Show
        new bootstrap.Modal(document.getElementById('validationModal')).show();
    }

    // Auto focus
    <?php if(!session()->getFlashdata('success')): ?>
    setTimeout(function() { $('input[name="npk"]').focus(); }, 500);
    <?php endif; ?>
});
</script>

</body>
</html>
