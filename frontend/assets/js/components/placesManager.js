/**
 * Places Manager - Gestion spécifique de la page des places de parking
 * Complément au UnifiedUIManager pour les fonctionnalités spécifiques
 * Se concentre uniquement sur la logique fonctionnelle, pas le CSS
 */

class PlacesManager {
    constructor() {
        this.loadingSpinner = null;
        this.tarifsInfo = null;
        this.showFeesCheckbox = null;
        this.isInitialized = false;
        this.init();
    }

    /**
     * Initialisation du gestionnaire des places
     * Vérifie si UnifiedUIManager existe pour éviter les conflits
     */
    init() {
        // Attendre que le DOM soit prêt
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initializeManager());
        } else {
            this.initializeManager();
        }
    }

    /**
     * Initialisation effective du gestionnaire
     */
    initializeManager() {
        // Éviter la double initialisation
        if (this.isInitialized) return;
        
        console.log('🔧 PlacesManager - Initialisation complémentaire...');
        
        this.initElements();
        this.initTarifsToggle();
        this.initLoadingSpinner();
        this.setupUtilityFunctions();
        this.setupAutoRefresh();

        this.isInitialized = true;
        console.log('✅ PlacesManager initialisé avec succès');
    }

    /**
     * Initialisation des éléments DOM
     */
    initElements() {
        this.loadingSpinner = document.getElementById('loading-spinner');
        this.tarifsInfo = document.querySelector('.tarifs-info');
        this.showFeesCheckbox = document.getElementById('show-fees');
    }

    /**
     * Gestion de l'affichage/masquage de la grille tarifaire
     */
    initTarifsToggle() {
        if (this.tarifsInfo && this.showFeesCheckbox) {
            // État initial de la grille tarifaire
            this.updateTarifsDisplay(!this.showFeesCheckbox.checked);
            console.log('✅ État initial grille tarifaire configuré');

            // Gestionnaire d'événement pour la checkbox
            this.showFeesCheckbox.addEventListener('change', (e) => {
                this.updateTarifsDisplay(!e.target.checked);
                console.log(`📋 Grille tarifaire ${e.target.checked ? 'affichée' : 'cachée'}`);
            });
        }
    }

    /**
     * Met à jour l'affichage de la grille tarifaire
     * @param {boolean} hide - true pour cacher, false pour afficher
     */
    updateTarifsDisplay(hide) {
        if (!this.tarifsInfo) return;

        if (hide) {
            // Cacher la grille
            this.tarifsInfo.style.display = 'none';
            this.tarifsInfo.classList.add('hidden');
        } else {
            // Afficher la grille
            this.tarifsInfo.style.display = 'block';
            this.tarifsInfo.classList.remove('hidden');
        }
    }

    /**
     * Initialisation du spinner de chargement
     */
    initLoadingSpinner() {
        if (this.loadingSpinner) {
            // S'assurer que le spinner est caché par défaut
            this.loadingSpinner.classList.add('d-none');
            this.loadingSpinner.style.display = 'none';
            console.log('✅ Spinner de chargement caché par défaut');
        }
    }

    /**
     * Configuration des fonctions utilitaires globales
     * Seulement si elles n'existent pas déjà (éviter les conflits)
     */
    setupUtilityFunctions() {
        // Vérifier si les fonctions existent déjà (créées par UnifiedUIManager)
        if (!window.showAjaxSpinner) {
            // Fonction pour afficher le spinner lors des requêtes AJAX
            window.showAjaxSpinner = () => {
                if (this.loadingSpinner) {
                    this.loadingSpinner.classList.remove('d-none');
                    this.loadingSpinner.style.display = 'block';
                    console.log('🔄 Spinner AJAX affiché');
                }
            };
        }

        if (!window.hideAjaxSpinner) {
            // Fonction pour cacher le spinner
            window.hideAjaxSpinner = () => {
                if (this.loadingSpinner) {
                    this.loadingSpinner.classList.add('d-none');
                    this.loadingSpinner.style.display = 'none';
                    console.log('✅ Spinner AJAX caché');
                }
            };
        }

        console.log('✅ Fonctions utilitaires AJAX configurées');
    }

    /**
     * Configuration de la mise à jour automatique des créneaux
     */
    setupAutoRefresh() {
        // Vérifier les changements de statut toutes les 30 secondes
        this.refreshInterval = setInterval(() => {
            this.checkForUpdates();
        }, 30000);

        console.log('✅ Auto-refresh des créneaux configuré (30s)');
    }

    /**
     * Vérifie s'il y a des mises à jour de statut des places
     */
    async checkForUpdates() {
        try {
            const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '/';
            const response = await fetch(`${baseUrl}api/getPlacesStatus`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success && data.hasChanges) {
                    console.log('🔄 Changements détectés, actualisation des créneaux...');
                    this.refreshPlacesData();
                }
            }
        } catch (error) {
            console.warn('⚠️ Erreur lors de la vérification des mises à jour:', error);
        }
    }

    /**
     * Actualise les données des places sans recharger la page
     */
    async refreshPlacesData() {
        try {
            const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '/';
            const currentUrl = new URL(window.location);
            const params = new URLSearchParams(currentUrl.search);

            // Ajouter un paramètre pour indiquer que c'est une requête AJAX
            params.set('ajax', '1');

            const response = await fetch(`${baseUrl}home/places?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const html = await response.text();

                // Extraire et remplacer seulement la section des places
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newPlacesContainer = doc.querySelector('#places-container');
                const currentPlacesContainer = document.querySelector('#places-container');

                if (newPlacesContainer && currentPlacesContainer) {
                    currentPlacesContainer.innerHTML = newPlacesContainer.innerHTML;
                    console.log('✅ Créneaux mis à jour automatiquement');

                    // Réinitialiser les événements après la mise à jour
                    if (window.app && window.app.uiManager) {
                        window.app.uiManager.setupPlacesCards();
                    }
                }
            }
        } catch (error) {
            console.warn('⚠️ Erreur lors de l\'actualisation des places:', error);
        }
    }

    /**
     * Méthode pour rafraîchir l'affichage des places
     */
    refreshPlacesDisplay() {
        console.log('🔄 Rafraîchissement de l\'affichage des places');
        // Le CSS Grid gère automatiquement la disposition
        // Réinitialiser les éléments si nécessaire
        this.initElements();
        this.initTarifsToggle();
    }

    /**
     * Méthode pour réinitialiser après un chargement AJAX
     */
    reinitializeAfterAjax() {
        console.log('🔄 Réinitialisation PlacesManager après AJAX');
        this.refreshPlacesDisplay();
    }

    /**
     * Méthode pour gérer les filtres de places
     */
    handlePlaceFilters() {
        // Logique pour les filtres spécifiques si nécessaire
        console.log('🔍 Gestion des filtres de places');
    }
}

// Initialisation automatique seulement si on est sur la page des places
if (window.location.pathname.includes('/home/places')) {
    const placesManager = new PlacesManager();

    // Exposer l'instance pour utilisation par d'autres scripts
    window.placesManager = placesManager;
}

// Export pour utilisation dans d'autres modules si nécessaire
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PlacesManager;
}
