/**
 * GESTIONNAIRE DE SCRIPTS UNIFIÉ
 * ==============================
 * 
 * Ce script détermine automatiquement quels gestionnaires charger
 * selon la page actuelle pour optimiser les performances
 */

(function () {
    'use strict';

    // ScriptManager: Initialisation du gestionnaire de scripts unifié

    class ScriptManager {
        constructor() {
            this.loadedScripts = new Set();
            this.isInitialized = false;

            // Définir BASE_URL depuis la méta base-url
            this.initializeBaseUrl();
        }

        initializeBaseUrl() {
            const baseUrlMeta = document.querySelector('meta[name="base-url"]');
            if (baseUrlMeta && !window.BASE_URL) {
                window.BASE_URL = baseUrlMeta.getAttribute('content');
            }
        }

        init() {
            // Page détectée: ${this.currentPage}
            this.loadRequiredScripts();
        }

        getBaseUrl() {
            // Récupérer l'URL de base depuis les métadonnées ou détecter automatiquement
            const metaBase = document.querySelector('meta[name="base-url"]');
            if (metaBase) {
                return metaBase.content;
            }

            // Déduction automatique
            const path = window.location.pathname;
            const segments = path.split('/').filter(s => s);

            // Rechercher 'parking_d' dans le chemin
            const projectIndex = segments.findIndex(s => s === 'parking_d');
            if (projectIndex !== -1) {
                const basePath = '/' + segments.slice(0, projectIndex + 1).join('/') + '/';
                return window.location.origin + basePath;
            }

            return window.location.origin + '/';
        }

        detectCurrentPage() {
            const path = window.location.pathname.toLowerCase();

            // Pages d'administration
            if (path.includes('/admin/')) {
                if (path.includes('/admin/reservations')) return 'admin-reservations';
                if (path.includes('/admin/places')) return 'admin-places';
                if (path.includes('/admin/users')) return 'admin-users';
                if (path.includes('/admin/tarifs')) return 'admin-tarifs';
                if (path.includes('/admin/subscriptions')) return 'admin-subscriptions';
                return 'admin-dashboard';
            }

            // Pages utilisateur
            if (path.includes('/places')) return 'places';
            if (path.includes('/auth/') || path.includes('/login') || path.includes('/register')) return 'auth';
            if (path.includes('/profile')) return 'profile';
            if (path.includes('/payment')) return 'payment';
            if (path.includes('/faq')) return 'faq';
            if (path.includes('/careers')) return 'careers';
            if (path === '/' || path.includes('/home') || path.endsWith('parking_d/')) return 'home';

            return 'general';
        }
        async loadRequiredScripts() {
            const scriptsToLoad = this.getRequiredScripts();

            // Scripts à charger pour ${this.currentPage}: scriptsToLoad

            for (const script of scriptsToLoad) {
                await this.loadScript(script);
            }

            // Tous les scripts requis sont chargés
        }
        getRequiredScripts() {
            const scripts = [];

            // Services consolidés - chargés dynamiquement selon le besoin
            this.loadCoreServices();

            // Scripts UI unifiés pour toutes les pages
            scripts.push('unifiedUIManager.js');

            // Scripts spécifiques par page
            switch (this.currentPage) {
                case 'places':
                    scripts.push('unifiedReservationManager.js');
                    break;

                case 'admin-dashboard':
                case 'admin-reservations':
                case 'admin-places':
                case 'admin-users':
                case 'admin-tarifs':
                case 'admin-subscriptions':
                    scripts.push('unifiedAdminManager.js');
                    scripts.push('unifiedReservationManager.js'); // Pour les modals de réservation
                    break;

                case 'profile':
                    scripts.push('unifiedReservationManager.js'); // Pour l'historique des réservations
                    break;

                case 'home':
                case 'auth':
                case 'payment':
                case 'faq':
                case 'careers':
                case 'general':
                default:
                    // Seuls les scripts UI communs
                    break;
            }

            return scripts;
        }

        // Charge les services core selon le contexte
        loadCoreServices() {
            // Services système (toujours nécessaire)
            this.loadCoreService('coreSystemService.js');

            // Services UI (pour toutes les pages)
            this.loadCoreService('coreUIService.js');

            // Services data (pour les formulaires/validations)
            this.loadCoreService('coreDataService.js');

            // Services admin (seulement pour les pages admin)
            if (this.currentPage.startsWith('admin-')) {
                this.loadCoreService('coreAdminService.js');
            }
        }

        // Charge un service core spécifique
        async loadCoreService(serviceName) {
            const servicePath = `${this.baseUrl}frontend/assets/js/services/${serviceName}`;

            if (this.loadedScripts.has(servicePath)) {
                return true;
            }

            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = servicePath;
                script.async = true;
                script.onload = () => {
                    // Service core chargé: ${serviceName}
                    this.loadedScripts.add(servicePath);
                    resolve(true);
                };

                script.onerror = () => {
                    // Erreur de chargement du service: ${serviceName}
                    reject(new Error(`Failed to load ${serviceName}`));
                };

                document.head.appendChild(script);
            });
        }
        async loadScript(scriptName) {
            const scriptPath = `${this.baseUrl}frontend/assets/js/components/${scriptName}`;

            if (this.loadedScripts.has(scriptPath)) {
                // Script déjà chargé: ${scriptName}
                return true;
            }

            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = scriptPath;
                script.async = true;

                script.onload = () => {
                    // Script chargé: ${scriptName}
                    this.loadedScripts.add(scriptPath);
                    resolve(true);
                };

                script.onerror = () => {
                    // Erreur de chargement: ${scriptName}
                    reject(new Error(`Failed to load ${scriptName}`));
                };

                document.head.appendChild(script);
            });
        }
        // Méthode publique pour charger des scripts supplémentaires
        async loadAdditionalScript(scriptName) {
            // Chargement de script supplémentaire: ${scriptName}
            return await this.loadScript(scriptName);
        }

        // Méthode publique pour vérifier si un script est chargé
        isScriptLoaded(scriptName) {
            const scriptPath = `${this.baseUrl}frontend/assets/js/components/${scriptName}`;
            return this.loadedScripts.has(scriptPath);
        }

        // Informations de debug
        getLoadedScripts() {
            return Array.from(this.loadedScripts);
        }
    }

    // Initialiser le gestionnaire de scripts
    window.ScriptManager = ScriptManager;
    window.scriptManager = new ScriptManager();

    // Exposer des fonctions utiles globalement
    window.loadScript = (scriptName) => window.scriptManager.loadAdditionalScript(scriptName);
    window.isScriptLoaded = (scriptName) => window.scriptManager.isScriptLoaded(scriptName);

})();
