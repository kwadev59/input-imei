<?php
/**
 * Admin Sidebar Partial
 * Required vars: $active_menu (string) - one of: dashboard, mandor, karyawan, gadget, laporan
 */
$menu_groups = [
    'dashboard' => [
        'is_group' => false,
        'item' => ['icon' => 'bi-grid-1x2-fill', 'label' => 'Dashboard', 'url' => 'dashboard']
    ],
    'operasional' => [
        'is_group' => true,
        'icon' => 'bi-briefcase-fill',
        'label' => 'Operasional',
        'items' => [
            'buat_pengiriman'=> ['icon' => 'bi-truck',  'label' => 'Kirim Gadget', 'url' => 'pengiriman-gadget/draft'],
            'pengiriman_gadget' => ['icon' => 'bi-clock-history', 'label' => 'History Pengiriman', 'url' => 'pengiriman-gadget'],
            'rekon_gadget'=> ['icon' => 'bi-arrow-left-right',  'label' => 'Rekon Gadget', 'url' => 'rekon-gadget'],
        ]
    ],
    'master_data' => [
        'is_group' => true,
        'icon' => 'bi-database-fill',
        'label' => 'Data Master',
        'items' => [
            'mandor'   => ['icon' => 'bi-person-badge-fill', 'label' => 'List Mandor',   'url' => 'mandor'],
            'karyawan' => ['icon' => 'bi-people-fill',       'label' => 'Data Karyawan', 'url' => 'karyawan'],
            'real_karyawan' => ['icon' => 'bi-person-vcard-fill', 'label' => 'Real Karyawan', 'url' => 'real-karyawan'],
            'real_gadget'   => ['icon' => 'bi-router-fill',       'label' => 'Real Gadget', 'url' => 'real-gadget'],
            'gadget'   => ['icon' => 'bi-phone-fill',        'label' => 'Master Gadget', 'url' => 'gadget'],
        ]
    ],
    'laporan' => [
        'is_group' => true,
        'icon' => 'bi-file-earmark-text-fill',
        'label' => 'Laporan & Rekap',
        'items' => [
            'laporan'   => ['icon' => 'bi-file-text-fill', 'label' => 'Rekap Input', 'url' => 'laporan'],
            'rekap'     => ['icon' => 'bi-bar-chart-fill',    'label' => 'Rekap Afdeling', 'url' => 'rekap'],
            'rekap_mpp' => ['icon' => 'bi-briefcase-fill',    'label' => 'Rekap MPP', 'url' => 'rekap-mpp'],
            'gadget_dobel'=> ['icon' => 'bi-phone-vibrate-fill',  'label' => 'Gadget Dobel', 'url' => 'gadget-dobel'],
        ]
    ]
];

$userName = esc(session()->get('nama') ?? 'Admin');
$userInitials = strtoupper(substr($userName, 0, 2));
?>

<!-- Mobile Top Bar -->
<div class="mobile-topbar">
    <div class="topbar-left">
        <button class="btn-sidebar-toggle" id="sidebarToggle" aria-label="Menu">
            <i class="bi bi-list"></i>
        </button>
        <span class="topbar-title">Distribusi Gadget</span>
    </div>
    <div class="topbar-right">
        <div class="topbar-avatar"><?= $userInitials ?></div>
    </div>
</div>

<!-- Sidebar -->
<aside class="sidebar" id="adminSidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="sidebar-brand-logo">
                <div class="sidebar-brand-icon">
                    <i class="bi bi-phone"></i>
                </div>
                <div class="sidebar-brand-text">
                    <h6>Distribusi Gadget</h6>
                    <span>Admin Panel</span>
                </div>
            </div>
            
            <button class="d-none d-lg-flex" id="desktopSidebarToggle" title="Toggle Sidebar">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </div>

    <!-- Navigation -->
    <div class="sidebar-section">
        <div class="sidebar-section-title">Menu Utama</div>
        <nav class="sidebar-nav">
            <?php 
            $current_active = $active_menu ?? ''; 
            
            foreach($menu_groups as $groupId => $group): 
                if(!$group['is_group']): 
                    $isActive = ($current_active === $groupId);
            ?>
                    <a class="nav-item <?= $isActive ? 'active' : '' ?>" href="<?= base_url($group['item']['url']) ?>">
                        <i class="bi <?= $group['item']['icon'] ?>"></i>
                        <span class="flex-grow-1"><?= $group['item']['label'] ?></span>
                    </a>
                <?php else: 
                    $isActiveGroup = false;
                    if (isset($group['items'][$current_active])) {
                        $isActiveGroup = true;
                    }
                ?>
                    <div class="nav-item-group">
                        <a class="nav-item has-submenu <?= $isActiveGroup ? '' : 'collapsed' ?> <?= $isActiveGroup ? 'active' : '' ?>" data-bs-toggle="collapse" href="#submenu-<?= $groupId ?>" role="button" aria-expanded="<?= $isActiveGroup ? 'true' : 'false' ?>" aria-controls="submenu-<?= $groupId ?>">
                            <i class="bi <?= $group['icon'] ?>"></i>
                            <span class="flex-grow-1"><?= $group['label'] ?></span>
                            <i class="bi bi-chevron-down submenu-icon"></i>
                        </a>
                        <div class="collapse <?= $isActiveGroup ? 'show' : '' ?>" id="submenu-<?= $groupId ?>">
                            <div class="submenu-list">
                                <?php foreach($group['items'] as $key => $item): ?>
                                    <a class="submenu-item <?= $current_active === $key ? 'active' : '' ?>" href="<?= base_url($item['url']) ?>">
                                        <i class="bi <?= $item['icon'] ?>"></i>
                                        <span><?= $item['label'] ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Bottom Section -->
    <div class="sidebar-bottom">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar"><?= $userInitials ?></div>
            <div class="sidebar-user-info">
                <h6><?= $userName ?></h6>
                <span><span class="status-dot"></span> Online</span>
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
