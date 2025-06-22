/* Script de navigation pour gérer les liens vers les sections du profil et les notifications */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        try {
            /* Gère les liens vers des sections spécifiques du profil */
            document.querySelectorAll('a[data-section]').forEach(link => {
                link.addEventListener('click', function(e) {
                    const section = this.getAttribute('data-section');
                    const currentUrl = new URL(this.href);

                    /* Ajoute le hash de la section à l'URL */
                    currentUrl.hash = section;

                    /* Si on est déjà sur la page profil, active directement l'onglet */
                    if (window.location.pathname.includes('auth/profile')) {
                        e.preventDefault();

                        /* Fonction pour activer l'onglet directement */
                        function activateTab(tabId) {
                            /* Désactive tous les onglets */
                            document.querySelectorAll('#profileTabs .nav-link').forEach(tab => {
                                tab.classList.remove('active');
                                tab.setAttribute('aria-selected', 'false');
                            });
                            
                            document.querySelectorAll('.tab-content .tab-pane').forEach(pane => {
                                pane.classList.remove('show', 'active');
                            });
                            
                            /* Active l'onglet demandé */
                            const tabButton = document.getElementById(tabId + '-tab');
                            const tabPane = document.getElementById(tabId);

                            if (tabButton && tabPane) {
                                tabButton.classList.add('active');
                                tabButton.setAttribute('aria-selected', 'true');
                                tabPane.classList.add('show', 'active');

                                /* Met à jour l'URL */
                                window.history.replaceState(null, null, '#' + tabId);
                                console.log('Onglet activé:', tabId);
                                return true;
                            }
                            return false;
                        }

                        /* Essaie d'activer l'onglet avec un délai pour s'assurer que le DOM est prêt */
                        setTimeout(() => {
                            /* Essaie différentes méthodes d'activation par ordre de priorité */
                            if (window.activateProfileTab && typeof window.activateProfileTab === 'function') {
                                window.activateProfileTab(section);
                            } else if (window.app && window.app.coreUI && typeof window.app.coreUI.activateTab === 'function') {
                                window.app.coreUI.activateTab(section);
                            } else if (typeof activateTab === 'function') {
                                activateTab(section);
                            } else {
                                /* Fallback: activation manuelle */
                                console.log('Utilisation du fallback pour activer l\'onglet:', section);
                                const tabButton = document.getElementById(section + '-tab');
                                if (tabButton) {
                                    tabButton.click();
                                }
                            }
                        }, 100);
                    } else {
                        /* Redirige vers la page profil avec le hash */
                        this.href = currentUrl.toString();
                    }
                });
            });

            /* Gère les clics sur les notifications */
            document.querySelectorAll('a[data-notification-id]').forEach(link => {
                link.addEventListener('click', function(e) {
                    const notificationId = this.getAttribute('data-notification-id');

                    /* Marque la notification comme lue */
                    if (notificationId) {
                        const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';

                        fetch(baseUrl + 'notification/markAsRead', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: 'notification_id=' + encodeURIComponent(notificationId)
                        }).then(response => {
                            if (response.ok) {
                                /* Supprime le style "non lu" */
                                this.classList.remove('fw-bold');
                            }
                        }).catch(error => {
                            console.warn('Erreur lors du marquage de la notification:', error);
                        });
                    }
                });
            });
            
            console.log('Navigation header initialisée');
        } catch (error) {
            console.error('Erreur lors de l\'initialisation de la navigation header:', error);
        }
    });
})();
