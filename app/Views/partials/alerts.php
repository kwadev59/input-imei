<?php
/**
 * Flash Alert Partial
 * Shows success/error/warning flash messages with consistent styling
 */
?>
<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i><?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <?php if(is_array(session()->getFlashdata('errors'))): ?>
            <ul class="mb-0 ps-3">
            <?php foreach(session()->getFlashdata('errors') as $e): ?>
                <li><?= esc($e) ?></li>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <?= esc(session()->getFlashdata('errors')) ?>
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(session()->getFlashdata('warning')): ?>
    <div class="alert alert-warning alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
        <i class="bi bi-info-circle me-2"></i><?= esc(session()->getFlashdata('warning')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
