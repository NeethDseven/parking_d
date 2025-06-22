/**
 * Script de correction simplifié pour les modales admin
 * Utilise Bootstrap natif avec corrections minimales
 */

(function() {
    'use strict';

    let isInitialized = false;

    // Initialisation unique
    function initOnce() {
        if (isInitialized) return;
        isInitialized = true;

        console.log('🔧 Correction simplifiée des modales admin');

        // Nettoyer d'abord
        cleanupAll();

        // Laisser Bootstrap gérer les modales naturellement
        setupMinimalFixes();

        // Masquer les breadcrumbs
        hideBreadcrumbs();
    }

    // Initialisation
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initOnce);
    } else {
        initOnce();
    }

    function cleanupAll() {
        // Supprimer tous les backdrops existants
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());

        // Nettoyer les classes du body
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';

        // Fermer toutes les modales ouvertes
        document.querySelectorAll('.modal.show').forEach(modal => {
            modal.classList.remove('show');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
        });
    }

    function setupMinimalFixes() {
        // Vérifier que Bootstrap est disponible
        if (typeof bootstrap === 'undefined') {
            console.error('❌ Bootstrap n\'est pas chargé');
            return;
        }

        console.log('✅ Bootstrap détecté, initialisation des modales');

        // S'assurer que toutes les modales ont les bons attributs
        document.querySelectorAll('.modal').forEach(modal => {
            if (!modal.hasAttribute('tabindex')) {
                modal.setAttribute('tabindex', '-1');
            }
            if (!modal.hasAttribute('role')) {
                modal.setAttribute('role', 'dialog');
            }
            if (!modal.hasAttribute('aria-hidden')) {
                modal.setAttribute('aria-hidden', 'true');
            }

            // Initialiser Bootstrap Modal si pas déjà fait
            if (!bootstrap.Modal.getInstance(modal)) {
                try {
                    new bootstrap.Modal(modal, {
                        backdrop: true,
                        keyboard: true,
                        focus: true
                    });
                    console.log(`✅ Modal Bootstrap initialisée: ${modal.id}`);
                } catch (error) {
                    console.error(`❌ Erreur initialisation modal ${modal.id}:`, error);
                }
            }
        });

        // Écouter uniquement les événements de nettoyage
        document.addEventListener('hidden.bs.modal', function(event) {
            setTimeout(() => {
                cleanupOrphanedBackdrops();
            }, 100);
        });

        // Écouter les erreurs d'ouverture
        document.addEventListener('show.bs.modal', function(event) {
            console.log(`🔓 Ouverture modal: ${event.target.id}`);
        });

        document.addEventListener('shown.bs.modal', function(event) {
            console.log(`✅ Modal ouverte: ${event.target.id}`);
        });
    }

    function cleanupOrphanedBackdrops() {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        const openModals = document.querySelectorAll('.modal.show');

        if (backdrops.length > 0 && openModals.length === 0) {
            backdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            console.log('🧹 Backdrops orphelins nettoyés');
        }
    }

    // Fonction de débogage simple
    window.debugModals = function() {
        console.log('🔍 État des modales:');
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            console.log(`Modal ${modal.id}:`, {
                visible: modal.classList.contains('show'),
                display: modal.style.display,
                classes: modal.className,
                hasBootstrapInstance: !!bootstrap.Modal.getInstance(modal)
            });
        });

        const backdrops = document.querySelectorAll('.modal-backdrop');
        console.log(`Backdrops: ${backdrops.length}`);
        console.log('Body modal-open:', document.body.classList.contains('modal-open'));
    };

    // Fonction pour forcer l'ouverture d'une modale
    window.forceOpenModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) {
            console.error(`❌ Modal ${modalId} non trouvée`);
            return;
        }

        console.log(`🚀 Forçage ouverture modal: ${modalId}`);

        // Nettoyer d'abord
        cleanupAll();

        // Essayer Bootstrap d'abord
        try {
            let bsModal = bootstrap.Modal.getInstance(modal);
            if (!bsModal) {
                bsModal = new bootstrap.Modal(modal);
            }
            bsModal.show();
            console.log(`✅ Modal ${modalId} ouverte avec Bootstrap`);
        } catch (error) {
            console.error(`❌ Erreur Bootstrap:`, error);

            // Fallback manuel
            modal.style.display = 'block';
            modal.classList.add('show');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');

            // Créer backdrop
            if (!document.querySelector('.modal-backdrop')) {
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.style.zIndex = '1050';
                document.body.appendChild(backdrop);

                backdrop.addEventListener('click', () => {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                    modal.setAttribute('aria-hidden', 'true');
                    backdrop.remove();
                    document.body.classList.remove('modal-open');
                });
            }

            console.log(`⚠️ Modal ${modalId} ouverte manuellement`);
        }
    };

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

    console.log('📋 Script simplifié de correction des modales admin chargé');
    console.log('💡 Utilisez debugModals() pour déboguer');

    // Activer l'observer des breadcrumbs
    observeBreadcrumbs();

    // Fonction globale pour masquer manuellement les breadcrumbs
    window.hideBreadcrumbs = hideBreadcrumbs;
})();
