<?php $page_title = 'Edit Gadget Dikirim'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu ?? 'pengiriman_gadget']) ?>

<div class="content">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4><i class="bi bi-pencil-square me-2"></i>Edit Gadget Dikirim</h4>
            <div class="header-sub">Mengubah data gadget pada BASTE: <strong><?= esc($baste['no_baste']) ?></strong></div>
        </div>
        <div>
            <a href="<?= base_url('pengiriman-gadget/detail/' . $baste['id']) ?>" class="btn btn-outline-secondary btn-action">
                <i class="bi bi-arrow-left me-1"></i> Batal Kembali
            </a>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <div class="data-card p-4 mx-auto" style="max-width: 600px;">
        <form action="<?= base_url('pengiriman-gadget/update-item/' . $item['id']) ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="imei" class="form-label fw-bold">IMEI</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="imei" name="imei" value="<?= esc($item['imei']) ?>" required>
                    <button type="button" class="btn btn-outline-secondary" id="btn-check-imei">Cek IMEI</button>
                </div>
                <div id="imei-info" class="form-text mt-2 text-primary fw-medium"></div>
            </div>
            
            <div class="mb-4">
                <label for="kerusakan" class="form-label fw-bold">Kerusakan</label>
                <textarea class="form-control" id="kerusakan" name="kerusakan" rows="3" required><?= esc($item['kerusakan']) ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-save me-1"></i> Simpan Perubahan
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnCheck = document.getElementById('btn-check-imei');
    const imeiInput = document.getElementById('imei');
    const imeiInfo = document.getElementById('imei-info');

    btnCheck.addEventListener('click', function() {
        const imei = imeiInput.value.trim();
        if (!imei) {
            alert('Masukkan IMEI terlebih dahulu');
            return;
        }

        btnCheck.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        btnCheck.disabled = true;

        const formData = new FormData();
        formData.append('imei', imei);

        fetch('<?= base_url('pengiriman-gadget/check-imei') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            btnCheck.innerHTML = 'Cek IMEI';
            btnCheck.disabled = false;
            
            if(data.status === 'success') {
                const g = data.data;
                imeiInfo.innerHTML = `
                    <i class="bi bi-check-circle-fill text-success me-1"></i> Sesuai: 
                    ${g.nama_pengguna ?? '-'} (${g.npk_pengguna ?? '-'}) | ${g.aplikasi ?? '-'} | PT. ${g.pt ?? '-'}
                `;
                imeiInfo.className = 'form-text mt-2 text-success fw-medium';
            } else {
                imeiInfo.innerHTML = `<i class="bi bi-exclamation-circle-fill text-danger me-1"></i> ${data.message}`;
                imeiInfo.className = 'form-text mt-2 text-danger fw-medium';
            }
        })
        .catch(error => {
            btnCheck.innerHTML = 'Cek IMEI';
            btnCheck.disabled = false;
            imeiInfo.innerHTML = `<i class="bi bi-x-circle-fill text-danger me-1"></i> Terjadi kesalahan jaringan.`;
            imeiInfo.className = 'form-text mt-2 text-danger fw-medium';
        });
    });
});
</script>

<?= view('partials/admin_footer') ?>
