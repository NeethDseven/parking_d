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
            if (!this.showFeesCheckbox.checked) {
                this.tarifsInfo.style.display = 'none';
                console.log('✅ Grille tarifaire cachée par défaut');
            } else {
                this.tarifsInfo.style.display = 'block';
                console.log('✅ Grille tarifaire affichée selon checkbox');
            }

            // Gestionnaire d'événement pour la checkbox
            this.showFeesCheckbox.addEventListener('change', (e) => {
                if (e.target.checked) {
                    this.tarifsInfo.style.display = 'block';
                    console.log('📋 Grille tarifaire affichée');
                } else {
                    this.tarifsInfo.style.display = 'none';
                    console.log('📋 Grille tarifaire cachée');
                }
            });
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
     * Méthode pour rafraîchir l'affichage des places
     */
    refreshPlacesDisplay() {
        console.log('🔄 Rafraîchissement de l'affichage des places');
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
if (window.location.pathname.includes('/places')) {
    const placesManager = new PlacesManager();
    
    // Exposer l'instance pour utilisation par d'autres scripts
    window.placesManager = placesManager;
}

// Export pour utilisation dans d'autres modules si nécessaire
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PlacesManager;
}
