/**
 * Application principale - Système de chargement des scripts (Version Consolidée)
 */

// Éviter les chargements multiples avec un guard pattern complet
(function () {
    'use strict';

    // Vérification si l'app existe déjà
    if (window.App && window.app && typeof window.app.init === 'function') {
        // App déjà chargée, réutilisation de l'instance existante
        console.log('App déjà chargée, réutilisation de l\'instance existante');
        return; // Sort de la fonction IIFE
    }

    // Si window.app existe mais est corrompu, le supprimer
    if (window.app && typeof window.app.init !== 'function') {
        console.warn('Instance window.app corrompue détectée, réinitialisation...');
        delete window.app;
    }

    // Initialisation de l'application...

    class App {
        constructor() {
            console.log('🚀 Constructeur App appelé');
            this.baseUrl = document.querySelector('meta[name="base-url"]')?.content || '/';
            this.page = '';  // Sera défini dans init()
            this.loadedModules = new Set();
            this.components = {};
            this.services = {};
            console.log('✅ Constructeur App terminé');
        }

        /**
         * Initialise l'application avec l'architecture consolidée
         */
        async init() {
            console.log('🔥 Méthode init() appelée');
            // Récupérer la page active depuis la meta ou l'attribut data-page du body        
            this.page = document.querySelector('meta[name="current-page"]')?.content ||
                (document.body ? document.body.dataset.page : '') || '';

            console.log('📄 Page détectée:', this.page);

            // Application initialisée silencieusement
            window.logger?.silent('Application initialisée - Page active:', this.page);

            // Charger uniquement les gestionnaires unifiés (ARCHITECTURE CONSOLIDÉE)
            try {                // Gestionnaire unifié pour toutes les réservations (remplace tous les anciens composants)
                if (document.querySelector('meta[name="user-data"]') ||
                    this.page === 'places' ||
                    this.page === 'reservation') {
                    console.log('🔄 Chargement unifiedReservationManager...');
                    await this.loadModule('components/unifiedReservationManager');
                }

                // Gestionnaire UI unifié pour les pages avec interactions dynamiques
                if (this.page === 'places' || this.page.startsWith('admin_')) {
                    console.log('🔄 Chargement unifiedUIManager...');
                    await this.loadModule('components/unifiedUIManager');
                }

                // Script spécifique pour la page places
                if (this.page === 'places') {
                    console.log('🔄 Chargement script places...');
                    await this.loadModule('pages/places');
                }

                // Gestionnaires unifiés pour l'administration (remplacent tous les anciens composants admin)
                if (this.page.startsWith('admin_')) {
                    console.log('🔄 Chargement modules admin...');
                    await this.loadModule('components/unifiedAdminManager');

                    // Script spécifique pour le dashboard admin
                    if (this.page === 'admin_dashboard') {
                        console.log('🔄 Chargement script dashboard admin...');
                        await this.loadModule('pages/admin-dashboard');
                    }
                }

                // Charger les dépendances spécifiques à la page (VERSION CONSOLIDÉE)
                this.loadPageDependencies();

                console.log('✅ Initialisation terminée avec succès');
            } catch (error) {
                console.error('❌ Erreur lors de l\'initialisation:', error);
                window.logger?.error('Erreur lors de l\'initialisation de l\'application:', error);
            }
        }        /**
         * Charge les dépendances nécessaires pour la page actuelle
         * VERSION CONSOLIDÉE - Tous les composants ont été unifiés dans les gestionnaires
         */
        loadPageDependencies() {
            // ARCHITECTURE CONSOLIDÉE: Tous les composants ont été unifiés
            // - unifiedReservationManager: gère toutes les fonctionnalités de réservation, alertes, profil, abonnements
            // - unifiedAdminManager: gère toutes les fonctionnalités d'administration
            // - unifiedUIManager: gère l'interface utilisateur admin

            // Plus de chargements individuels nécessaires - tout est consolidé !
            window.logger?.silent(`Page ${this.page}: Utilisation de l'architecture consolidée`);

            // Validation post-chargement pour s'assurer que les gestionnaires sont opérationnels
            setTimeout(() => {
                this.validateLoadedComponents();
            }, 500);

            // Services globaux maintenant consolidés
            this.initializeBootstrapComponents();
        }

        /**
         * Valide que les composants chargés sont opérationnels
         */
        validateLoadedComponents() {
            if (this.page === 'places' && window.uiManager) {
                // Vérifier que l'UI Manager est bien initialisé pour la page places
                if (typeof window.uiManager.setupPlaces === 'function') {
                    console.log('✅ UI Manager validé pour la page places');
                } else {
                    console.warn('⚠ UI Manager non complètement initialisé');
                }

                // Vérifier l'API de pagination
                if (window.directClickPagination && window.directClickPagination.loadPage) {
                    console.log('✅ API de pagination disponible');
                } else {
                    console.warn('⚠ API de pagination non disponible');
                }
            }
        }

        /**
         * Initialise les composants Bootstrap
         */
        initializeBootstrapComponents() {
            // Initialiser les tooltips et popovers Bootstrap
            setTimeout(() => {
                if (window.bootstrap) {
                    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

                    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
                    [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
                }
            }, 500);
        }

        /**
         * Charge un module (service ou composant)
         * @param {string} moduleName - Le nom du module à charger
         * @returns {Promise} - Promise résolue quand le module est chargé
         */
        async loadModule(moduleName) {
            if (this.loadedModules.has(moduleName)) {
                // Module ${moduleName} déjà chargé
                return true;
            }

            try {
                const moduleUrl = `${this.baseUrl}frontend/assets/js/${moduleName}.js`;

                const response = await fetch(moduleUrl);
                if (!response.ok) {
                    throw new Error(`Erreur de chargement du module ${moduleName}`);
                }

                const script = document.createElement('script');
                script.src = moduleUrl;
                script.async = true;

                const loadPromise = new Promise((resolve, reject) => {
                    script.onload = () => {
                        this.loadedModules.add(moduleName);
                        resolve(true);
                    };
                    script.onerror = () => {
                        reject(new Error(`Erreur de chargement du module ${moduleName}`));
                    };
                });

                document.head.appendChild(script);
                return await loadPromise;

            } catch (error) {
                window.logger?.error(`Erreur lors du chargement du module ${moduleName}:`, error);
                throw error;
            }
        }

        /**
         * Enregistre un composant
         * @param {string} name - Le nom du composant
         * @param {Object} component - L'instance du composant
         */
        registerComponent(name, component) {
            this.components[name] = component;
            window.logger?.debug(`Composant enregistré: ${name}`);
        }

        /**
         * Enregistre un service
         * @param {string} name - Le nom du service
         * @param {Object} service - L'instance du service
         */
        registerService(name, service) {
            this.services[name] = service;
            window.logger?.debug(`Service enregistré: ${name}`);
        }

        /**
         * Récupère un composant
         * @param {string} name - Le nom du composant
         * @returns {Object} - L'instance du composant
         */
        getComponent(name) {
            return this.components[name];
        }

        /**
         * Récupère un service
         * @param {string} name - Le nom du service
         * @returns {Object} - L'instance du service
         */
        getService(name) {
            return this.services[name];
        }

        /**
         * Récupère la page actuelle
         * @returns {string} - Le nom de la page actuelle
         */
        getCurrentPage() {
            return this.page;
        }

        /**
         * Vérifie si un module est chargé
         * @param {string} moduleName - Le nom du module
         * @returns {boolean} - True si le module est chargé
         */
        isModuleLoaded(moduleName) {
            return this.loadedModules.has(moduleName);
        }
    }

    // Exporter globalement
    window.App = App;

    // Créer l'instance globale et initialiser
    try {
        console.log('🏗️ Création de l\'instance App...');
        window.app = new App();

        // Vérifier que l'instance a bien la méthode init
        if (typeof window.app.init !== 'function') {
            console.error('❌ Erreur: window.app.init n\'est pas une fonction', window.app);
            return;
        }

        console.log('✅ Instance App créée avec succès');

        // Initialiser quand le DOM est prêt
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                console.log('📄 DOM prêt, initialisation...');
                if (window.app && typeof window.app.init === 'function') {
                    window.app.init();
                } else {
                    console.error('❌ window.app ou window.app.init non disponible au moment de l\'initialisation');
                }
            });
        } else {
            console.log('📄 DOM déjà prêt, initialisation immédiate...');
            if (window.app && typeof window.app.init === 'function') {
                window.app.init();
            } else {
                console.error('❌ window.app ou window.app.init non disponible');
            }
        }
    } catch (error) {
        console.error('❌ Erreur lors de la création de l\'instance App:', error);
    }

})();
