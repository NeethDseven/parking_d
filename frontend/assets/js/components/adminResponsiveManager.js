/**
 * Gestionnaire responsive unifié pour l'administration
 * Corrige les problèmes de sidebar et de responsive design
 */

(function() {
    'use strict';

    let isInitialized = false;

    function initAdminResponsiveManager() {
        if (isInitialized) return;
        isInitialized = true;

        console.log('🔧 Initialisation du gestionnaire responsive admin');

        setupSidebarManager();
        setupResponsiveLayout();
        setupViewportFix();
    }

    /**
     * Gestion unifiée de la sidebar
     */
    function setupSidebarManager() {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const overlay = createOverlay();

        if (!sidebar || !mainContent || !sidebarToggle) {
            console.warn('⚠️ Éléments sidebar non trouvés:');
            console.warn('- Sidebar:', sidebar);
            console.warn('- MainContent:', mainContent);
            console.warn('- SidebarToggle:', sidebarToggle);
            return;
        }

        // Éléments sidebar trouvés et configurés

        // État initial correct
        resetSidebarState();

        // Gestion du toggle
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Gestion du toggle selon la taille d'écran

            if (window.innerWidth < 992) {
                toggleMobileSidebar();
            }
            // Mode desktop : toggle ignoré
        });

        // Fermeture par clic sur overlay
        overlay.addEventListener('click', function() {
            closeMobileSidebar();
        });

        // Gestion du redimensionnement
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                handleResize();
            }, 100);
        });

        // Fermeture par échap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && window.innerWidth < 992) {
                closeMobileSidebar();
            }
        });

        function createOverlay() {
            let overlay = document.querySelector('.sidebar-overlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.className = 'sidebar-overlay';
                document.body.appendChild(overlay);
            }
            return overlay;
        }

        function toggleMobileSidebar() {
            const isOpen = sidebar.classList.contains('show');
            
            if (isOpen) {
                closeMobileSidebar();
            } else {
                openMobileSidebar();
            }
        }

        function openMobileSidebar() {
            sidebar.classList.add('show');
            // Forcer le style inline pour override le CSS
            sidebar.style.setProperty('transform', 'translateX(0)', 'important');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';

            // Mettre à jour l'icône
            const icon = sidebarToggle.querySelector('i');
            if (icon) {
                icon.className = 'fas fa-times';
            }
        }

        function closeMobileSidebar() {
            sidebar.classList.remove('show');
            // Forcer le style inline pour override le CSS
            sidebar.style.setProperty('transform', 'translateX(-100%)', 'important');
            overlay.classList.remove('show');
            document.body.style.overflow = '';

            // Remettre l'icône hamburger
            const icon = sidebarToggle.querySelector('i');
            if (icon) {
                icon.className = 'fas fa-bars';
            }
        }

        function resetSidebarState() {
            if (window.innerWidth >= 992) {
                // Desktop : sidebar visible, pas d'overlay
                sidebar.classList.remove('show');
                sidebar.style.removeProperty('transform'); // Reset transform
                overlay.classList.remove('show');
                document.body.style.overflow = '';
                mainContent.style.marginLeft = '';

                const icon = sidebarToggle.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-bars';
                }
            } else {
                // Mobile : sidebar cachée
                sidebar.classList.remove('show');
                sidebar.style.setProperty('transform', 'translateX(-100%)', 'important'); // Force cachée
                overlay.classList.remove('show');
                document.body.style.overflow = '';
                mainContent.style.marginLeft = '0';
            }
        }

        function handleResize() {
            resetSidebarState();
            
            // Réinitialiser la disposition du dashboard si nécessaire
            const dashboardLayout = document.querySelector('.dashboard-ultra-compact-layout');
            if (dashboardLayout) {
                dashboardLayout.style.transform = '';
                dashboardLayout.style.transition = '';
            }
        }

        console.log('✅ Sidebar manager configuré');
    }

    /**
     * Gestion du layout responsive
     */
    function setupResponsiveLayout() {
        // Correction des modales en responsive
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.addEventListener('shown.bs.modal', function() {
                if (window.innerWidth < 768) {
                    modal.style.paddingRight = '0';
                    document.body.style.paddingRight = '0';
                }
            });
        });

        // Correction des tableaux en responsive
        const tables = document.querySelectorAll('.admin-table');
        tables.forEach(table => {
            if (!table.closest('.table-responsive')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'table-responsive';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }
        });

        // Layout responsive configuré
    }

    /**
     * Correction du viewport pour mobile
     */
    function setupViewportFix() {
        // Correction du viewport meta tag si nécessaire
        let viewport = document.querySelector('meta[name="viewport"]');
        if (!viewport) {
            viewport = document.createElement('meta');
            viewport.name = 'viewport';
            document.head.appendChild(viewport);
        }
        viewport.content = 'width=device-width, initial-scale=1, shrink-to-fit=no';

        // Correction de la hauteur sur mobile
        function setVH() {
            const vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        }

        setVH();
        window.addEventListener('resize', setVH);
        window.addEventListener('orientationchange', setVH);

        // Correction du zoom sur iOS
        document.addEventListener('touchstart', function() {}, { passive: true });

        console.log('✅ Viewport fix configuré');
    }

    // Initialiser selon l'état du DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminResponsiveManager);
    } else {
        initAdminResponsiveManager();
    }

    // Réinitialiser si nécessaire (navigation AJAX)
    window.reinitAdminResponsive = initAdminResponsiveManager;

})();
