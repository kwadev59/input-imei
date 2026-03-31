<?php
/**
 * Admin Footer Partial
 * Includes Bootstrap JS bundle + sidebar toggle script
 */
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
    'use strict';

    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle = document.getElementById('sidebarToggle');

    function openSidebar() {
        sidebar.classList.add('show');
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    if (toggle) {
        toggle.addEventListener('click', function() {
            if (sidebar.classList.contains('show')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Close sidebar on nav click (mobile)
    document.querySelectorAll('.sidebar-nav .nav-item').forEach(function(item) {
        item.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                closeSidebar();
            }
        });
    });

    // Close sidebar when resizing to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            closeSidebar();
        }
    });

    // Desktop specific toggle
    const desktopToggle = document.getElementById('desktopSidebarToggle');
    const content = document.querySelector('.content');

    // Restore desktop sidebar state from local storage
    if (window.innerWidth >= 992 && localStorage.getItem('sidebar_minimized') === 'true') {
        sidebar.classList.add('minimized');
        if(content) content.classList.add('minimized');
    }

    if (desktopToggle) {
        desktopToggle.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('minimized');
            if(content) content.classList.toggle('minimized');
            
            // Save state
            localStorage.setItem('sidebar_minimized', sidebar.classList.contains('minimized'));
        });
    }

    // Auto-dismiss alerts after 5s
    document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 5000);
    });
})();
</script>
</body>
</html>
