/**
 * Fonctions utilitaires globales et initialisation de base
 */
document.addEventListener('DOMContentLoaded', function () {
    // Initialiser les tooltips Bootstrap
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }

    // Initialiser les popovers Bootstrap
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    if (typeof bootstrap !== 'undefined' && bootstrap.Popover) {
        [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
    }

    // Auto-fermeture des alertes après un délai
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.querySelector('[data-bs-dismiss="alert"]')) {
                alert.querySelector('[data-bs-dismiss="alert"]').click();
            } else if (bootstrap && bootstrap.Alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } else {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
    });

    // Fonction utilitaire pour formater les nombres
    window.formatNumber = function (number, decimals = 2) {
        return parseFloat(number).toFixed(decimals).replace('.', ',');
    };

    // Fonction utilitaire pour mettre la première lettre en majuscule
    window.ucfirst = function (string) {
        if (!string) return '';
        return string.charAt(0).toUpperCase() + string.slice(1);
    };
});
