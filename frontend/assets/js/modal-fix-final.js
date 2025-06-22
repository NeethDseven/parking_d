/**
 * Solution finale pour les modales admin
 * Approche simple et robuste
 */

(function() {
    'use strict';

    let initialized = false;

    // Initialisation
    function init() {
        if (initialized) return;
        initialized = true;

        console.log('🔧 Initialisation correction modales admin');

        // Attendre que Bootstrap soit disponible
        if (typeof bootstrap === 'undefined') {
            console.log('⏳ Attente de Bootstrap...');
            setTimeout(init, 100);
            return;
        }

        setupModals();
        setupEventListeners();
        addGlobalFunctions();
    }

    function setupModals() {
        const modals = document.querySelectorAll('.modal');
        
        modals.forEach(modal => {
            // Attributs requis
            modal.setAttribute('tabindex', '-1');
            modal.setAttribute('role', 'dialog');
            modal.setAttribute('aria-hidden', 'true');

            // Nettoyer les styles inline problématiques
            modal.style.position = '';
            modal.style.top = '';
            modal.style.left = '';
            modal.style.width = '';
            modal.style.height = '';
            modal.style.zIndex = '';

            // Initialiser Bootstrap Modal
            if (!bootstrap.Modal.getInstance(modal)) {
                new bootstrap.Modal(modal, {
                    backdrop: true,
                    keyboard: true,
                    focus: true
                });
            }

            console.log(`✅ Modal configurée: ${modal.id}`);
        });
    }

    function setupEventListeners() {
        // Nettoyage après fermeture
        document.addEventListener('hidden.bs.modal', function(event) {
            setTimeout(() => {
                cleanupBackdrops();
            }, 100);
        });

        // Correction z-index lors des événements Bootstrap
        document.addEventListener('show.bs.modal', function(event) {
            const modal = event.target;
            console.log(`🔓 Ouverture: ${modal.id}`);

            // Forcer les z-index immédiatement
            modal.style.zIndex = '1055';

            const dialog = modal.querySelector('.modal-dialog');
            const content = modal.querySelector('.modal-content');

            if (dialog) {
                dialog.style.zIndex = '1056';
                dialog.style.position = 'relative';
            }

            if (content) {
                content.style.zIndex = '1057';
                content.style.position = 'relative';
            }
        });

        document.addEventListener('shown.bs.modal', function(event) {
            const modal = event.target;
            console.log(`✅ Ouverte: ${modal.id}`);

            // Double vérification des z-index après ouverture
            setTimeout(() => {
                modal.style.zIndex = '1055';

                const dialog = modal.querySelector('.modal-dialog');
                const content = modal.querySelector('.modal-content');

                if (dialog) {
                    dialog.style.zIndex = '1056';
                    dialog.style.position = 'relative';
                }

                if (content) {
                    content.style.zIndex = '1057';
                    content.style.position = 'relative';
                }

                // S'assurer que le backdrop est derrière
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.style.zIndex = '1050';
                }
            }, 50);
        });

        // Gestion des clics sur les triggers avec correction z-index
        document.addEventListener('click', function(event) {
            const trigger = event.target.closest('[data-bs-toggle="modal"]');
            if (trigger) {
                event.preventDefault();
                event.stopPropagation();

                const targetId = trigger.getAttribute('data-bs-target');
                const modal = document.querySelector(targetId);

                if (modal) {
                    // Nettoyer avant d'ouvrir
                    cleanupBackdrops();

                    // Forcer les z-index avant l'ouverture
                    modal.style.zIndex = '1055';

                    const dialog = modal.querySelector('.modal-dialog');
                    const content = modal.querySelector('.modal-content');

                    if (dialog) {
                        dialog.style.zIndex = '1056';
                        dialog.style.position = 'relative';
                    }

                    if (content) {
                        content.style.zIndex = '1057';
                        content.style.position = 'relative';
                    }

                    // Essayer Bootstrap d'abord
                    setTimeout(() => {
                        let bsModal = bootstrap.Modal.getInstance(modal);
                        if (!bsModal) {
                            bsModal = new bootstrap.Modal(modal);
                        }

                        try {
                            bsModal.show();

                            // Double vérification après ouverture
                            setTimeout(() => {
                                modal.style.zIndex = '1055';
                                if (dialog) dialog.style.zIndex = '1056';
                                if (content) content.style.zIndex = '1057';
                            }, 50);

                        } catch (error) {
                            console.error('Erreur ouverture modal:', error);
                            // Fallback manuel
                            openModalManually(modal);
                        }
                    }, 10);
                }
            }
        });
    }

    function cleanupBackdrops() {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        const openModals = document.querySelectorAll('.modal.show');
        
        if (backdrops.length > openModals.length) {
            backdrops.forEach((backdrop, index) => {
                if (index >= openModals.length) {
                    backdrop.remove();
                }
            });
        }

        // Nettoyer le body si aucune modal ouverte
        if (openModals.length === 0) {
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }
    }

    function openModalManually(modal) {
        console.log(`⚠️ Ouverture manuelle: ${modal.id}`);

        // Nettoyer d'abord
        cleanupBackdrops();

        // Ouvrir manuellement avec z-index forcé
        modal.style.display = 'block';
        modal.style.zIndex = '1055';
        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');

        // Forcer le z-index du dialog et content
        const dialog = modal.querySelector('.modal-dialog');
        const content = modal.querySelector('.modal-content');

        if (dialog) {
            dialog.style.zIndex = '1056';
            dialog.style.position = 'relative';
        }

        if (content) {
            content.style.zIndex = '1057';
            content.style.position = 'relative';
        }

        // Ajouter backdrop avec z-index correct
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.style.zIndex = '1050';
        document.body.appendChild(backdrop);

        // Ajouter classe au body
        document.body.classList.add('modal-open');

        // Fermeture sur backdrop
        backdrop.addEventListener('click', () => {
            closeModalManually(modal);
        });

        // Fermeture sur bouton close
        const closeButtons = modal.querySelectorAll('[data-bs-dismiss="modal"]');
        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                closeModalManually(modal);
            });
        });
    }

    function closeModalManually(modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
        
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        
        console.log(`🔒 Fermée manuellement: ${modal.id}`);
    }

    function addGlobalFunctions() {
        // Fonction de debug
        window.debugModals = function() {
            console.log('🔍 Debug modales:');
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                console.log(`${modal.id}:`, {
                    visible: modal.classList.contains('show'),
                    hasBootstrap: !!bootstrap.Modal.getInstance(modal)
                });
            });
        };

        // Fonction de nettoyage
        window.cleanupModals = function() {
            document.querySelectorAll('.modal.show').forEach(modal => {
                closeModalManually(modal);
            });
            cleanupBackdrops();
            console.log('🧹 Nettoyage effectué');
        };

        // Fonction de test
        window.testModal = function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                const bsModal = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);
                bsModal.show();
            }
        };
    }

    // Initialisation
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Réessayer après un délai si Bootstrap n'est pas encore chargé
    setTimeout(init, 500);

    console.log('📋 Script de correction finale des modales chargé');
})();
