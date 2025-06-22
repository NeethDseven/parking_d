/* Script pour la gestion de la sidebar admin responsive */

document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            const isMobileOrTablet = window.innerWidth < 992;

            /* Fonctionnalité uniquement sur mobile/tablette */
            if (isMobileOrTablet) {
                sidebar.classList.toggle('show');
            }
        });

        /* Gérer le redimensionnement de la fenêtre */
        window.addEventListener('resize', function() {
            const isMobileOrTablet = window.innerWidth < 992;

            if (!isMobileOrTablet) {
                /* Retour au desktop : nettoyer les classes mobile */
                sidebar.classList.remove('show');
            }
        });
    }
});
