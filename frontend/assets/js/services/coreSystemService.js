/**
 * SERVICE SYSTÈME CONSOLIDÉ
 * Fusionne : appStateService, languageService, notificationService, reservationTimerService, immediateReservationService
 */

// Protection contre le double chargement
if (typeof window.CoreSystemService !== 'undefined') {
    console.log('CoreSystemService déjà défini');
} else {

    class CoreSystemService {
        constructor() {
            // État de l'application
            this.state = {
                user: null,
                currentPage: '',
                filters: {
                    places: {
                        type: 'all',
                        search: '',
                        sort: 'numero-asc'
                    }
                },
                reservations: {
                    current: [],
                    history: []
                },
                notifications: {
                    unread: 0,
                    items: []
                },
                ui: {
                    sidebarOpen: false,
                    darkMode: this.loadDarkModePreference(),
                    language: this.loadLanguagePreference()
                }
            };

            // Gestion des langues
            this.currentLanguage = 'fr';
            this.translations = {};

            // Notifications
            this.notifications = [];
            this.unreadCount = 0;
            this.baseUrl = document.querySelector('meta[name="base-url"]')?.content || '/';

            // Timers
            this.timers = new Map();
            this.reservationTrackerInstance = null;

            // Réservations immédiates
            this.immediateTimers = {};

            this.listeners = new Map();
            this.init();
        }

        init() {
            // CoreSystemService initialisé silencieusement

            app.registerService('system', this);
            app.registerService('state', this);
            app.registerService('language', this);
            app.registerService('notification', this);
            app.registerService('reservationTimer', this);
            app.registerService('immediateReservation', this);

            this.loadInitialState();
            this.setupEventListeners();
            this.loadCurrentLanguage();
            this.loadNotifications();
            this.setupReservationTracking();
            this.checkAndUpdateTimers();

            // Auto-initialisation des timers
            setTimeout(() => this.initializeTimersFromPage(), 500);
            setInterval(() => this.checkAndUpdateTimers(), 10000);
        }

        // ===========================================
        // ÉTAT DE L'APPLICATION (ex-appStateService)
        // ===========================================

        loadInitialState() {
            this.loadUserData();
            this.loadPageState();
            this.loadUIPreferences();
        }

        loadUserData() {
            const userDataElement = document.querySelector('meta[name="user-data"]');
            if (userDataElement) {
                try {
                    this.state.user = JSON.parse(userDataElement.content);
                } catch (e) {
                    console.error('Erreur lors du chargement des données utilisateur:', e);
                }
            }
        }

        loadPageState() {
            const pageElement = document.querySelector('meta[name="current-page"]');
            if (pageElement) {
                this.state.currentPage = pageElement.content;
            }
        }

        loadUIPreferences() {
            const darkMode = this.loadDarkModePreference();
            const language = this.loadLanguagePreference();
            const sidebarState = localStorage.getItem('sidebarState');

            this.state.ui = {
                ...this.state.ui,
                darkMode,
                language,
                sidebarOpen: sidebarState === 'open'
            };
        }

        loadDarkModePreference() {
            const saved = localStorage.getItem('darkMode');
            if (saved !== null) {
                return saved === 'true';
            }
            return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        }

        loadLanguagePreference() {
            return localStorage.getItem('language') ||
                navigator.language.split('-')[0] ||
                'fr';
        }

        setupEventListeners() {
            window.matchMedia('(prefers-color-scheme: dark)').addListener((e) => {
                if (localStorage.getItem('darkMode') === null) {
                    this.updateState('ui.darkMode', e.matches);
                }
            });

            window.addEventListener('storage', (e) => {
                if (e.key === 'darkMode') {
                    this.updateState('ui.darkMode', e.newValue === 'true');
                } else if (e.key === 'language') {
                    this.updateState('ui.language', e.newValue);
                }
            });
        }

        updateState(path, value) {
            const keys = path.split('.');
            let current = this.state;

            for (let i = 0; i < keys.length - 1; i++) {
                if (!current[keys[i]]) {
                    current[keys[i]] = {};
                }
                current = current[keys[i]];
            }

            current[keys[keys.length - 1]] = value;

            this.persistStateChanges(path, value);
            this.notifyListeners(path, value);
        }

        persistStateChanges(path, value) {
            switch (path) {
                case 'ui.darkMode':
                    localStorage.setItem('darkMode', value);
                    this.applyDarkMode(value);
                    break;
                case 'ui.language':
                    localStorage.setItem('language', value);
                    break;
                case 'ui.sidebarOpen':
                    localStorage.setItem('sidebarState', value ? 'open' : 'closed');
                    break;
            }
        }

        applyUIPreferences() {
            this.applyDarkMode(this.state.ui.darkMode);
            document.documentElement.lang = this.state.ui.language;
        }

        applyDarkMode(isDark) {
            if (isDark) {
                document.documentElement.classList.add('dark-mode');
            } else {
                document.documentElement.classList.remove('dark-mode');
            }
        }

        subscribe(path, callback) {
            if (!this.listeners.has(path)) {
                this.listeners.set(path, new Set());
            }
            this.listeners.get(path).add(callback);

            return () => {
                const callbacks = this.listeners.get(path);
                if (callbacks) {
                    callbacks.delete(callback);
                }
            };
        }

        notifyListeners(path, value) {
            const callbacks = this.listeners.get(path);
            if (callbacks) {
                callbacks.forEach(callback => callback(value));
            }

            const parts = path.split('.');
            while (parts.length > 1) {
                parts.pop();
                const parentPath = parts.join('.');
                const parentCallbacks = this.listeners.get(parentPath);
                if (parentCallbacks) {
                    let currentValue = this.state;
                    for (const part of parts) {
                        currentValue = currentValue[part];
                    }
                    parentCallbacks.forEach(callback => callback(currentValue));
                }
            }
        }

        getState(path) {
            const keys = path.split('.');
            let current = this.state;

            for (const key of keys) {
                if (current[key] === undefined) {
                    return undefined;
                }
                current = current[key];
            }

            return current;
        }

        // ===========================================
        // LANGUES (ex-languageService)
        // ===========================================

        loadCurrentLanguage() {
            this.currentLanguage = this.getState('ui.language');
            this.loadTranslations(this.currentLanguage);
        }

        async loadTranslations(lang) {
            try {
                const response = await fetch(`${this.baseUrl}frontend/assets/locales/${lang}.json`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                this.translations[lang] = await response.json();
                this.updateUI();
            } catch (e) {
                console.error(`Erreur lors du chargement des traductions pour ${lang}:`, e);
                if (lang !== 'fr') {
                    this.loadTranslations('fr');
                }
            }
        }

        async setLanguage(lang) {
            if (this.currentLanguage === lang) return;

            if (!this.translations[lang]) {
                await this.loadTranslations(lang);
            }

            this.currentLanguage = lang;
            document.documentElement.lang = lang;

            this.updateState('ui.language', lang);
            this.updateUI();
        }

        translate(key, params = {}) {
            let text = this.getTranslationByKey(key);

            if (!text) return key;

            Object.entries(params).forEach(([param, value]) => {
                text = text.replace(`{${param}}`, value);
            });

            return text;
        }

        getTranslationByKey(key) {
            const keys = key.split('.');
            let current = this.translations[this.currentLanguage];

            for (const k of keys) {
                if (!current || current[k] === undefined) {
                    return null;
                }
                current = current[k];
            }

            return current || null;
        }

        updateUI() {
            document.querySelectorAll('[data-i18n]').forEach(element => {
                const key = element.getAttribute('data-i18n');
                const translation = this.translate(key);

                if (translation) {
                    const attrs = element.getAttribute('data-i18n-attr');
                    if (attrs) {
                        attrs.split(',').forEach(attr => {
                            element.setAttribute(attr, translation);
                        });
                    } else {
                        element.textContent = translation;
                    }
                }
            });

            document.querySelectorAll('[data-i18n-placeholder]').forEach(element => {
                const key = element.getAttribute('data-i18n-placeholder');
                const translation = this.translate(key);
                if (translation) {
                    element.setAttribute('placeholder', translation);
                }
            });
        }

        // ===========================================
        // NOTIFICATIONS (ex-notificationService)
        // ===========================================

        loadNotifications() {
            const notifBadge = document.querySelector('.notification-badge');
            if (notifBadge) {
                const count = parseInt(notifBadge.textContent) || 0;
                this.unreadCount = count;
            }

            const notificationItems = document.querySelectorAll('.notification-item');
            if (notificationItems.length) {
                notificationItems.forEach(item => {
                    item.addEventListener('click', () => {
                        const notifId = item.getAttribute('data-notification-id');
                        if (!notifId) return;

                        this.markAsRead(notifId, item);
                    });
                });
            }
        }

        markAsRead(notifId, element) {
            fetch(`${this.baseUrl}api/markNotificationRead/${notifId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        element.classList.remove('fw-bold');
                        this.updateUnreadCount(-1);
                    }
                })
                .catch(error => console.error('Error marking notification as read:', error));
        }

        updateUnreadCount(change) {
            this.unreadCount += change;
            if (this.unreadCount < 0) this.unreadCount = 0;

            const unreadBadge = document.getElementById('notificationsDropdown')?.querySelector('.badge');
            if (unreadBadge) {
                unreadBadge.textContent = this.unreadCount > 0 ? this.unreadCount : '';
                unreadBadge.style.display = this.unreadCount > 0 ? '' : 'none';
            }
        }

        showToast(message, type = 'info') {
            const uiService = app.getService('ui');
            if (uiService) {
                uiService.showToast(message, type);
            }
        }

        // ===========================================
        // TIMERS RÉSERVATION (ex-reservationTimerService)
        // ===========================================

        updateAllTimers() {
            try {
                this.timers.forEach((timerData, timerId) => {
                    this.updateTimer(timerId);
                });
            } catch (error) {
                console.error('Erreur lors de la mise à jour des timers:', error);
            }
        }

        updateTimer(timerId) {
            const timerData = this.timers.get(timerId);
            if (!timerData) return;

            const timerElement = document.getElementById(timerId);
            if (!timerElement) {
                this.stopTimer(timerId);
                return;
            }

            const startTime = new Date(timerData.startTime);
            const now = new Date();
            const elapsed = Math.floor((now - startTime) / 1000);

            const hours = Math.floor(elapsed / 3600);
            const minutes = Math.floor((elapsed % 3600) / 60);
            const seconds = elapsed % 60;

            const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            timerElement.textContent = timeString;

            if (timerData.tarif && timerData.costElementId) {
                this.updateCost(timerData.costElementId, elapsed, timerData.tarif);
            }
        }

        updateCost(costElementId, elapsedSeconds, tarifHoraire) {
            const costElement = document.getElementById(costElementId);
            if (!costElement) return;

            const hoursElapsed = elapsedSeconds / 3600;
            const estimatedCost = hoursElapsed * parseFloat(tarifHoraire);

            costElement.textContent = `${estimatedCost.toFixed(2)} €`;
        }

        startTimer(timerId, startTime, tarif = null, costElementId = null) {
            const timerData = {
                startTime: startTime,
                tarif: tarif,
                costElementId: costElementId,
                intervalId: null
            };

            this.timers.set(timerId, timerData);
            this.updateTimer(timerId);

            const intervalId = setInterval(() => {
                this.updateTimer(timerId);
            }, 1000);

            timerData.intervalId = intervalId;

            console.log('Timer démarré:', timerId);
        }

        stopTimer(timerId) {
            const timerData = this.timers.get(timerId);
            if (timerData && timerData.intervalId) {
                clearInterval(timerData.intervalId);
            }

            this.timers.delete(timerId);
            console.log('Timer arrêté:', timerId);
        }

        initializeTimersFromPage() {
            const timerElements = document.querySelectorAll('[data-start-time]');

            timerElements.forEach(element => {
                const timerId = element.id;
                const startTime = element.getAttribute('data-start-time');
                const tarif = element.getAttribute('data-tarif');
                const costElementId = element.getAttribute('data-cost-element');

                if (timerId && startTime && !this.timers.has(timerId)) {
                    this.startTimer(timerId, startTime, tarif, costElementId);
                }
            });
        }

        checkAndUpdateTimers() {
            const timerElements = document.querySelectorAll('[data-start-time]');
            if (timerElements.length > 0) {
                this.initializeTimersFromPage();
                this.updateAllTimers();
            }
        }

        cleanup() {
            this.timers.forEach((timerData, timerId) => {
                this.stopTimer(timerId);
            });
        }

        // ===========================================
        // RÉSERVATIONS IMMÉDIATES (ex-immediateReservationService)
        // ===========================================

        setupReservationTracking() {
            const reservationElement = document.querySelector('.immediate-reservation[data-reservation-id]');
            if (reservationElement) {
                const reservationId = reservationElement.getAttribute('data-reservation-id');
                this.startStatusCheck(reservationId);
                this.setupCountdown(reservationElement);
                this.setupExtendButton(reservationElement, reservationId);
                this.setupEndButton(reservationElement, reservationId);
            }
        }

        startStatusCheck(reservationId, interval = 30000) {
            if (this.immediateTimers[`status_${reservationId}`]) {
                clearInterval(this.immediateTimers[`status_${reservationId}`]);
            }

            this.immediateTimers[`status_${reservationId}`] = setInterval(() => {
                this.checkReservationStatus(reservationId);
            }, interval);
        }

        checkReservationStatus(reservationId) {
            fetch(`${this.baseUrl}api/checkReservationStatus/${reservationId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'terminée' || data.status === 'annulée') {
                        window.location.reload();
                    }
                })
                .catch(error => console.error('Erreur lors de la vérification du statut:', error));
        }

        setupCountdown(element) {
            const endTime = element.getAttribute('data-end-time');
            if (!endTime) return;

            const countdownElement = element.querySelector('.countdown');
            if (!countdownElement) return;

            const updateCountdown = () => {
                const now = new Date().getTime();
                const end = new Date(endTime).getTime();
                const diff = end - now;

                if (diff <= 0) {
                    countdownElement.textContent = 'Terminé';
                    return;
                }

                const hours = Math.floor(diff / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                countdownElement.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            };

            updateCountdown();
            setInterval(updateCountdown, 1000);
        }

        setupExtendButton(element, reservationId) {
            const extendBtn = element.querySelector('.extend-reservation-btn');
            if (extendBtn) {
                extendBtn.addEventListener('click', () => {
                    this.extendReservation(reservationId);
                });
            }
        }

        setupEndButton(element, reservationId) {
            const endBtn = element.querySelector('.end-reservation-btn');
            if (endBtn) {
                endBtn.addEventListener('click', () => {
                    this.endReservation(reservationId);
                });
            }
        }

        extendReservation(reservationId) {
            if (confirm('Voulez-vous prolonger cette réservation d\'une heure ?')) {
                fetch(`${this.baseUrl}api/extendReservation/${reservationId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.showToast('Réservation prolongée avec succès!', 'success');
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            this.showToast('Erreur lors de la prolongation: ' + data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la prolongation:', error);
                        this.showToast('Erreur lors de la prolongation', 'error');
                    });
            }
        }

        endReservation(reservationId) {
            if (confirm('Voulez-vous terminer cette réservation maintenant ?')) {
                fetch(`${this.baseUrl}api/endReservation/${reservationId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.showToast('Réservation terminée avec succès!', 'success');
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            this.showToast('Erreur lors de la fin de réservation: ' + data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la fin de réservation:', error);
                        this.showToast('Erreur lors de la fin de réservation', 'error');
                    });
            }
        }
    }

    // Export pour le système de modules
    window.CoreSystemService = CoreSystemService;

    // Création de l'instance
    new CoreSystemService();

}
