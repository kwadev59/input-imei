<?php
/**
 * Mandor Sidebar Partial
 * Required vars: $active_menu (string) — 'dashboard' or 'create'
 */
$menus = [
    'dashboard' => ['icon' => 'bi-grid-1x2-fill',    'label' => 'Dashboard',       'url' => 'input'],
    'create'    => ['icon' => 'bi-plus-circle-fill',  'label' => 'Input Pengajuan', 'url' => 'input/create'],
];

$userName = esc(session()->get('nama') ?? 'Mandor');
$userInitials = strtoupper(substr($userName, 0, 2));
$tipeMandor = esc(session()->get('tipe_mandor') ?? '');
$afdeling = esc(session()->get('afdeling_id') ?? '');
$ptSite = esc(session()->get('pt_site') ?? '');
?>

<!-- Mobile Top Bar -->
<div class="mobile-topbar">
    <div class="topbar-left">
        <button class="btn-sidebar-toggle" id="sidebarToggle" aria-label="Menu">
            <i class="bi bi-list"></i>
        </button>
        <span class="topbar-title">Mandor Panel</span>
    </div>
    <div class="topbar-right">
        <div class="topbar-avatar"><?= $userInitials ?></div>
    </div>
</div>

<!-- Sidebar -->
<aside class="sidebar" id="mandorSidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="sidebar-brand-logo">
            <div class="sidebar-brand-icon">
                <i class="bi bi-phone"></i>
            </div>
            <div class="sidebar-brand-text">
                <h6>Mandor Panel</h6>
                <span>Distribusi Gadget</span>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="sidebar-section">
        <div class="sidebar-section-title">Menu</div>
        <nav class="sidebar-nav">
            <?php foreach($menus as $key => $menu): ?>
                <a class="nav-item <?= ($active_menu ?? '') === $key ? 'active' : '' ?>" href="<?= base_url($menu['url']) ?>">
                    <i class="bi <?= $menu['icon'] ?>"></i>
                    <span><?= $menu['label'] ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Bottom Section -->
    <div class="sidebar-bottom">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar"><?= $userInitials ?></div>
            <div class="sidebar-user-info">
                <h6><?= $userName ?></h6>
                <span>
                    <?= $tipeMandor ?>
                    <?php if($afdeling): ?> · <?= $afdeling ?><?php endif; ?>
                    <?php if($ptSite): ?> · <?= $ptSite ?><?php endif; ?>
                </span>
            </div>
        </div>
        <a class="sidebar-logout" href="<?= base_url('auth/logout') ?>">
            <i class="bi bi-box-arrow-left"></i>
            <span>Keluar</span>
        </a>
    </div>
</aside>

<!-- Sidebar overlay for mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
