/**
 * Système de journalisation centralisé 
 * Permet de désactiver les logs en production tout en gardant le code inchangé
 */
class Logger {
    constructor() {
        // Mode silencieux par défaut en production - seuls les avertissements et erreurs sont affichés
        this.debugEnabled = false; // Logs de développement - désactivés par défaut
        this.infoEnabled = false;  // Logs informatifs - désactivés par défaut
        this.warnEnabled = true;   // Avertissements - toujours visibles
        this.errorEnabled = true;  // Erreurs - toujours visibles

        // Vérifier si le mode développement est activé (par exemple via un cookie ou localStorage)
        this.checkDebugMode();
    }

    /**
     * Vérifie si le mode développement est activé
     */
    checkDebugMode() {
        // Vérifier dans l'URL si debug=true
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('debug')) {
            this.debugEnabled = true;
            this.infoEnabled = true;
            this.saveDebugMode(true);
            return;
        }

        // Vérifier dans le localStorage
        const debugMode = localStorage.getItem('parkme_debug_mode');
        if (debugMode === 'true') {
            this.debugEnabled = true;
            this.infoEnabled = true;
        }
    }

    /**
     * Enregistre le mode debug dans le localStorage
     */
    saveDebugMode(enabled) {
        if (enabled) {
            localStorage.setItem('parkme_debug_mode', 'true');
        } else {
            localStorage.removeItem('parkme_debug_mode');
        }
    }

    /**
     * Active ou désactive le mode debug
     */
    setDebugMode(enabled) {
        this.debugEnabled = enabled;
        this.infoEnabled = enabled;
        this.saveDebugMode(enabled);
    }

    /**
     * Log de débogage - N'apparaît qu'en mode développement
     */
    debug(...args) {
        if (this.debugEnabled) {
            console.debug('[DEBUG]', ...args);
        }
    }

    /**
     * Log d'information - N'apparaît qu'en mode développement
     */
    info(...args) {
        if (this.infoEnabled) {
            console.info('[INFO]', ...args);
        }
    }

    /**
     * Log d'avertissement - Toujours visible
     */
    warn(...args) {
        if (this.warnEnabled) {
            console.warn('[WARN]', ...args);
        }
    }
    /**
   * Log d'erreur - Toujours visible
   */
    error(...args) {
        if (this.errorEnabled) {
            console.error('[ERROR]', ...args);
        }
    }

    /**
     * Méthode silencieuse - Ne log jamais même en mode debug
     */
    silent(...args) {
        // Ne fait rien
    }
}

// Créer une instance globale
window.logger = new Logger();

// Pour faciliter la migration du code existant, on peut ajouter ces méthodes
if (!console.silent) {
    console.silent = (...args) => { }; // Ne fait rien
}

if (!console.quietlog) {
    console.quietlog = (...args) => {
        window.logger.debug(...args);
    };
}

// Raccourcis pour les développeurs via la console du navigateur
window.enableDebug = function () {
    window.logger.setDebugMode(true);
    console.log('Mode debug activé - rafraîchissez la page');
};

window.disableDebug = function () {
    window.logger.setDebugMode(false);
    console.log('Mode debug désactivé - rafraîchissez la page');
};
