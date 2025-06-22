/**
 * Gestionnaire de la navbar principale
 * Gère uniquement la fermeture automatique, laisse Bootstrap gérer le toggle
 */

(function() {
    'use strict';

    let isInitialized = false;

    function initNavbarManager() {
        if (isInitialized) return;
        isInitialized = true;

        console.log('🔧 Initialisation du gestionnaire de navbar (fermeture auto uniquement)');

        // Attendre que le DOM soit prêt
        setTimeout(function() {
            setupAutoClose();
        }, 500);
    }



    /**
     * Configure la fermeture automatique de la navbar
     */
    function setupAutoClose() {
        const navbarCollapse = document.querySelector('#navbarNav');
        const navbarToggler = document.querySelector('.navbar-toggler');

        if (!navbarCollapse || !navbarToggler) return;

        // Fermer la navbar quand on clique sur un lien (mobile uniquement)
        const navLinks = navbarCollapse.querySelectorAll('.nav-link:not(.dropdown-toggle)');
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Vérifier si on est en mode mobile et que la navbar est ouverte
                if (window.innerWidth < 992 && navbarCollapse.classList.contains('show')) {
                    // Utiliser Bootstrap natif pour fermer
                    navbarToggler.click();
                }
            });
        });

        // Fermer la navbar lors du redimensionnement vers desktop
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                if (window.innerWidth >= 992 && navbarCollapse.classList.contains('show')) {
                    navbarToggler.click();
                }
            }, 100);
        });

        console.log('✅ Fermeture automatique navbar configurée');
    }

    // Initialiser selon l'état du DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNavbarManager);
    } else {
        initNavbarManager();
    }

    // Réinitialiser si nécessaire (navigation AJAX)
    window.reinitNavbar = initNavbarManager;

})();
