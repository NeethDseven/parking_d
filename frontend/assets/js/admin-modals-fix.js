/* Gestionnaire unifié des modales admin et masquage des breadcrumbs */

(function() {
    'use strict';

    let isInitialized = false;

    /* Initialise une seule fois pour éviter les doublons */
    function initOnce() {
        if (isInitialized) return;
        isInitialized = true;

        console.log('🔧 Gestionnaire unifié des modales admin');

        /* Attend Bootstrap avant d'initialiser */
        if (typeof bootstrap === 'undefined') {
            setTimeout(initOnce, 100);
            return;
        }

        cleanupAll();
        setupModals();
        setupEventListeners();
        hideBreadcrumbs();
        addGlobalFunctions();
    }

    /* Lance l'initialisation selon l'état du DOM */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initOnce);
    } else {
        initOnce();
    }

    function cleanupAll() {
        // Supprime tous les backdrops orphelins
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());

        // Nettoie les classes du body
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';

        // Ferme toutes les modales ouvertes
        document.querySelectorAll('.modal.show').forEach(modal => {
            modal.classList.remove('show');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
        });
    }

    function setupModals() {
        const modals = document.querySelectorAll('.modal');

        modals.forEach(modal => {
            // Configure les attributs requis pour l'accessibilité
            modal.setAttribute('tabindex', '-1');
            modal.setAttribute('role', 'dialog');
            modal.setAttribute('aria-hidden', 'true');

            // Nettoie les styles inline qui peuvent causer des problèmes
            modal.style.position = '';
            modal.style.top = '';
            modal.style.left = '';
            modal.style.width = '';
            modal.style.height = '';
            modal.style.zIndex = '';

            // Initialise Bootstrap Modal si pas déjà fait
            if (!bootstrap.Modal.getInstance(modal)) {
                try {
                    new bootstrap.Modal(modal, {
                        backdrop: true,
                        keyboard: true,
                        focus: true
                    });
                    console.log(`✅ Modal configurée: ${modal.id}`);
                } catch (error) {
                    console.error(`❌ Erreur initialisation modal ${modal.id}:`, error);
                }
            }
        });
    }

    function setupEventListeners() {
        // Nettoyage après fermeture
        document.addEventListener('hidden.bs.modal', function(event) {
            setTimeout(() => {
                cleanupOrphanedBackdrops();
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

        // Gestion des clics sur les triggers
        document.addEventListener('click', function(event) {
            const trigger = event.target.closest('[data-bs-toggle="modal"]');
            if (trigger) {
                event.preventDefault();
                event.stopPropagation();

                const targetId = trigger.getAttribute('data-bs-target');
                const modal = document.querySelector(targetId);

                if (modal) {
                    openModal(modal);
                }
            }
        });
    }

    function openModal(modal) {
        // Nettoyer avant d'ouvrir
        cleanupOrphanedBackdrops();

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
                openModalManually(modal);
            }
        }, 10);
    }

    function cleanupOrphanedBackdrops() {
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
        cleanupOrphanedBackdrops();

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
        /* Fonction de nettoyage pour les modales orphelines */
        window.cleanupModals = function() {
            document.querySelectorAll('.modal.show').forEach(modal => {
                closeModalManually(modal);
            });
            cleanupOrphanedBackdrops();
        };


    }

    // Fonction pour masquer tous les breadcrumbs
    function hideBreadcrumbs() {
        console.log('🚫 Masquage des breadcrumbs...');

        // Sélecteurs pour tous les types de breadcrumbs possibles
        const breadcrumbSelectors = [
            '.breadcrumb',
            'ol.breadcrumb',
            '.breadcrumb-container',
            'nav[aria-label="breadcrumb"]',
            '.admin-breadcrumb',
            '.breadcrumb-wrapper',
            '.breadcrumb-section',
            '.page-breadcrumb'
        ];

        breadcrumbSelectors.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(element => {
                element.style.display = 'none';
                element.style.visibility = 'hidden';
                element.style.height = '0';
                element.style.margin = '0';
                element.style.padding = '0';
                element.style.overflow = 'hidden';
                element.setAttribute('aria-hidden', 'true');

                console.log(`✅ Breadcrumb masqué: ${selector}`);
            });
        });

        // Masquer aussi les conteneurs parents qui ne contiennent que des breadcrumbs
        const navElements = document.querySelectorAll('nav');
        navElements.forEach(nav => {
            const breadcrumb = nav.querySelector('.breadcrumb, ol.breadcrumb');
            if (breadcrumb && nav.children.length === 1) {
                nav.style.display = 'none';
                console.log('✅ Container nav de breadcrumb masqué');
            }
        });

        console.log('🎯 Masquage des breadcrumbs terminé');
    }

    // Observer pour masquer les breadcrumbs ajoutés dynamiquement
    function observeBreadcrumbs() {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        // Vérifier si le nœud ajouté est un breadcrumb
                        if (node.classList && (
                            node.classList.contains('breadcrumb') ||
                            node.tagName === 'OL' && node.classList.contains('breadcrumb') ||
                            node.getAttribute('aria-label') === 'breadcrumb'
                        )) {
                            console.log('🆕 Nouveau breadcrumb détecté, masquage...');
                            node.style.display = 'none';
                            node.style.visibility = 'hidden';
                        }

                        // Vérifier les enfants du nœud ajouté
                        const childBreadcrumbs = node.querySelectorAll && node.querySelectorAll('.breadcrumb, ol.breadcrumb, nav[aria-label="breadcrumb"]');
                        if (childBreadcrumbs && childBreadcrumbs.length > 0) {
                            childBreadcrumbs.forEach(breadcrumb => {
                                breadcrumb.style.display = 'none';
                                breadcrumb.style.visibility = 'hidden';
                                console.log('🆕 Breadcrumb enfant masqué');
                            });
                        }
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        console.log('👁️ Observer des breadcrumbs activé');
    }

    // Réessayer après un délai si Bootstrap n'est pas encore chargé
    setTimeout(initOnce, 500);

    // Activer l'observer des breadcrumbs
    observeBreadcrumbs();

    // Fonction globale pour masquer manuellement les breadcrumbs
    window.hideBreadcrumbs = hideBreadcrumbs;

    console.log('📋 Gestionnaire unifié des modales admin chargé');
})();
