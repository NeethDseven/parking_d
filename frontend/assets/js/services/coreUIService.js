/**
 * SERVICE UI CONSOLIDÉ
 * Fusionne : uiService, profileTabService, clipboardService, cacheService
 */

// Protection contre le double chargement
if (typeof window.CoreUIService !== 'undefined') {
    console.log('CoreUIService déjà défini');
} else {

    // Vérification de compatibilité ES6
    try {
        // Test simple pour vérifier le support des classes
        eval('class TestES6 {}');
    } catch (e) {
        console.error('❌ Votre navigateur ne supporte pas les classes ES6. Veuillez utiliser un navigateur plus récent.');
        window.CoreUIService = null;
        return;
    }

    class CoreUIService {
        constructor() {
            // Configuration tabs
            this.validTabs = ['informations', 'reservations', 'subscriptions', 'notifications'];

            this.init();
        }

        init() {
            // CoreUIService initialisé silencieusement
            app.registerService('ui', this);

            this.setupGlobalListeners();
            this.setupClipboardButtons();
            this.refreshCacheOnce();

            // Initialiser les tabs si on est sur une page de profil
            if (document.getElementById('profileTabs')) {
                this.initProfileTabs();
            }
        }

        // ===========================================
        // UI GÉNÉRAL (ex-uiService)
        // ===========================================

        setupGlobalListeners() {
            // Gestion générique des formulaires
            document.addEventListener('submit', (e) => {
                const form = e.target;
                if (form.dataset.confirmMessage) {
                    if (!confirm(form.dataset.confirmMessage)) {
                        e.preventDefault();
                    }
                }
            });

            // Gestion des dropdowns de réservation immédiate
            this.setupReservationDropdowns();

            this.initTooltips();
            this.initAlerts();
        }

        setupReservationDropdowns() {
            const dropdowns = document.querySelectorAll('.dropdown-toggle');
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('show.bs.dropdown', () => {
                    // Forcer la mise à jour des éléments du dropdown quand il est ouvert
                    if (window.reservationTrackerInstance && typeof window.reservationTrackerInstance.updateAllTimers === 'function') {
                        setTimeout(() => {
                            window.reservationTrackerInstance.updateAllTimers();
                        }, 50);
                    }
                });
            });
        }

        initTooltips() {
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            }
        }

        initAlerts() {
            const alerts = document.querySelectorAll('.alert.alert-success');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 300);
                    }
                }, 5000);
            });
        }

        showToast(message, type = 'info') {
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '1055';
                document.body.appendChild(toastContainer);
            }

            const toastElement = document.createElement('div');
            toastElement.className = `toast align-items-center text-white bg-${this.getToastColorClass(type)} border-0`;
            toastElement.setAttribute('role', 'alert');
            toastElement.setAttribute('aria-live', 'assertive');
            toastElement.setAttribute('aria-atomic', 'true');

            toastElement.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
            </div>
        `;

            toastContainer.appendChild(toastElement);

            if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                const toast = new bootstrap.Toast(toastElement, { delay: 4000 });
                toast.show();

                toastElement.addEventListener('hidden.bs.toast', () => {
                    toastElement.remove();
                });
            } else {
                setTimeout(() => {
                    toastElement.style.opacity = '0';
                    setTimeout(() => toastElement.remove(), 300);
                }, 4000);
            }
        }

        getToastColorClass(type) {
            switch (type) {
                case 'success': return 'success';
                case 'error': return 'danger';
                case 'warning': return 'warning';
                case 'info': return 'info';
                default: return 'primary';
            }
        }

        getSafeElement(selector, container = document) {
            try {
                return container.querySelector(selector);
            } catch (e) {
                console.error('Erreur sélecteur:', selector, e);
                return null;
            }
        }    // ===========================================
        // PROFILE TABS (ex-profileTabService)
        // ===========================================

        initProfileTabs() {
            // Gérer le hash au chargement de la page
            this.handleHash();

            // Retry après un délai pour s'assurer que le DOM est prêt
            setTimeout(() => this.handleHash(), 100);

            // Écouter les changements de hash
            window.addEventListener('hashchange', () => this.handleHash());

            // Écouter les clics sur les onglets
            document.querySelectorAll('#profileTabs .nav-link').forEach(tab => {
                tab.addEventListener('click', (e) => {
                    const tabId = e.target.id.replace('-tab', '');
                    this.updateUrlHash(tabId);
                });
            });

            console.log('Service des onglets de profil initialisé');
        }

        activateTab(tabId) {
            if (!this.validTabs.includes(tabId)) {
                console.warn('ID d\'onglet non valide:', tabId);
                return false;
            }

            // Désactiver tous les onglets
            document.querySelectorAll('#profileTabs .nav-link').forEach(tab => {
                tab.classList.remove('active');
                tab.setAttribute('aria-selected', 'false');
            });

            document.querySelectorAll('.tab-content .tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });

            // Activer l'onglet demandé
            const tabButton = document.getElementById(tabId + '-tab');
            const tabPane = document.getElementById(tabId);

            if (tabButton && tabPane) {
                tabButton.classList.add('active');
                tabButton.setAttribute('aria-selected', 'true');
                tabPane.classList.add('show', 'active');
                return true;
            }

            console.error('Éléments d\'onglet non trouvés pour:', tabId);
            return false;
        }

        handleHash() {
            const hash = window.location.hash.substring(1);
            console.log('Hash détecté:', hash);

            if (hash && this.validTabs.includes(hash)) {
                return this.activateTab(hash);
            }

            return false;
        }

        updateUrlHash(tabId) {
            if (this.validTabs.includes(tabId)) {
                window.history.replaceState(null, null, '#' + tabId);
            }
        }

        getActiveTab() {
            const activeTab = document.querySelector('#profileTabs .nav-link.active');
            if (activeTab) {
                return activeTab.id.replace('-tab', '');
            }
            return null;
        }

        updateTabCounts() {
            // Mettre à jour le compteur de notifications
            const notificationCount = document.querySelectorAll('#notifications .notification-item').length;
            const notificationBadge = document.querySelector('#notifications-tab .badge');
            if (notificationBadge && notificationCount > 0) {
                notificationBadge.textContent = notificationCount;
            }

            // Mettre à jour le compteur de réservations
            const reservationCount = document.querySelectorAll('#reservations .reservation-item').length;
            const reservationBadge = document.querySelector('#reservations-tab .badge');
            if (reservationBadge && reservationCount > 0) {
                reservationBadge.textContent = reservationCount;
            }
        }

        // ===========================================
        // CLIPBOARD (ex-clipboardService)
        // ===========================================

        setupClipboardButtons() {
            const copyBtns = document.querySelectorAll('.copy-btn');
            copyBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.copyToClipboard(btn);
                });
            });
        }

        async copyToClipboard(button) {
            const text = button.getAttribute('data-clipboard-text');
            if (!text) return false;

            try {
                await navigator.clipboard.writeText(text);
                this.showCopySuccess(button);
                return true;
            } catch (error) {
                console.error('Erreur lors de la copie dans le presse-papiers:', error);
                this.showCopyError(button);
                return false;
            }
        }

        async copyText(text) {
            try {
                await navigator.clipboard.writeText(text);
                this.showToast('Texte copié dans le presse-papiers!', 'success');
                return true;
            } catch (error) {
                console.error('Erreur lors de la copie dans le presse-papiers:', error);
                return false;
            }
        }

        showCopySuccess(button) {
            const originalHTML = button.innerHTML;
            const originalClass = button.className;

            button.innerHTML = '<i class="fas fa-check me-1"></i> Copié!';
            button.classList.add('btn-success');
            if (button.classList.contains('btn-outline-primary')) {
                button.classList.replace('btn-outline-primary', 'btn-success');
            } else if (button.classList.contains('btn-primary')) {
                button.classList.replace('btn-primary', 'btn-success');
            }

            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.className = originalClass;
            }, 2000);
        }

        showCopyError(button) {
            const originalHTML = button.innerHTML;
            const originalClass = button.className;

            button.innerHTML = '<i class="fas fa-times me-1"></i> Erreur!';
            button.classList.add('btn-danger');
            if (button.classList.contains('btn-outline-primary')) {
                button.classList.replace('btn-outline-primary', 'btn-danger');
            } else if (button.classList.contains('btn-primary')) {
                button.classList.replace('btn-primary', 'btn-danger');
            }

            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.className = originalClass;
            }, 2000);
        }

        // ===========================================
        // CACHE (ex-cacheService)
        // ===========================================

        refreshCacheOnce() {
            const cacheVersion = new Date().getTime();
            localStorage.setItem('cacheVersion', cacheVersion);

            if (!sessionStorage.getItem('cacheRefreshed')) {
                this.reloadStylesheets(cacheVersion);
                console.log('Cache des ressources CSS rafraîchi');
                sessionStorage.setItem('cacheRefreshed', 'true');

                if (!localStorage.getItem('initialRefresh')) {
                    localStorage.setItem('initialRefresh', 'done');
                    setTimeout(() => {
                        window.location.reload(true);
                    }, 100);
                }
            }
        }

        reloadStylesheets(version) {
            const links = document.getElementsByTagName('link');
            for (let i = 0; i < links.length; i++) {
                if (links[i].rel === 'stylesheet') {
                    const href = links[i].href.split('?')[0];
                    links[i].href = href + '?v=' + version;
                }
            }
        }

        // ===========================================
        // MÉTHODES UTILITAIRES
        // ===========================================

        showAlert(message, type = 'info') {
            this.showToast(message, type);
        }

        // Alias pour compatibilité
        showNotification(message, type = 'info') {
            this.showToast(message, type);
        }
    }

    // Export pour le système de modules
    window.CoreUIService = CoreUIService;

    // Création de l'instance
    new CoreUIService();

}
