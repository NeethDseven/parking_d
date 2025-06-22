// Script pour la page profil - Gestion des QR codes et des onglets

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Initialisation de la page profil');
    
    // Génère les QR codes avec l'API QR Server
    function generateQRCode(text, containerId, color = '000000') {
        const container = document.getElementById(containerId);
        if (!container) return;

        // Nettoie le contenu existant
        container.innerHTML = '<div class="qr-placeholder"><i class="fas fa-spinner fa-spin"></i><br>Génération du QR code...</div>';

        // URL de l'API QR Server avec paramètres optimisés
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(text)}&color=${color}&bgcolor=ffffff&format=png&ecc=M`;

        // Crée l'image QR code
        const img = new Image();
        img.onload = function() {
            container.innerHTML = '';
            container.appendChild(img);
            console.log(`✅ QR code généré pour ${containerId}`);
        };
        
        img.onerror = function() {
            container.innerHTML = '<div class="qr-placeholder text-danger"><i class="fas fa-exclamation-triangle"></i><br>Erreur de génération</div>';
            console.error(`❌ Erreur génération QR code pour ${containerId}`);
        };
        
        img.src = qrUrl;
        img.alt = `QR Code pour ${text}`;
        img.className = 'img-fluid';
    }

    // Copie le texte dans le presse-papiers
    function copyToClipboard(text, buttonElement) {
        if (navigator.clipboard && window.isSecureContext) {
            // API moderne
            navigator.clipboard.writeText(text).then(() => {
                showCopySuccess(buttonElement);
            }).catch(err => {
                console.error('Erreur copie moderne:', err);
                fallbackCopy(text, buttonElement);
            });
        } else {
            // Fallback pour navigateurs plus anciens
            fallbackCopy(text, buttonElement);
        }
    }

    // Méthode de copie fallback
    function fallbackCopy(text, buttonElement) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            showCopySuccess(buttonElement);
        } catch (err) {
            console.error('Erreur copie fallback:', err);
            showCopyError(buttonElement);
        } finally {
            document.body.removeChild(textArea);
        }
    }

    // Affiche le succès de la copie
    function showCopySuccess(buttonElement) {
        const originalText = buttonElement.textContent;
        buttonElement.textContent = 'Copié !';
        buttonElement.classList.add('copied');
        
        setTimeout(() => {
            buttonElement.textContent = originalText;
            buttonElement.classList.remove('copied');
        }, 2000);
    }

    // Affiche l'erreur de copie
    function showCopyError(buttonElement) {
        const originalText = buttonElement.textContent;
        buttonElement.textContent = 'Erreur';
        buttonElement.style.background = 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)';
        
        setTimeout(() => {
            buttonElement.textContent = originalText;
            buttonElement.style.background = '';
        }, 2000);
    }

    // Génère les QR codes pour les réservations
    const reservationCards = document.querySelectorAll('[data-reservation-id]');
    reservationCards.forEach(card => {
        const reservationId = card.getAttribute('data-reservation-id');
        const qrContainer = card.querySelector('.qr-code-container');
        
        if (qrContainer && reservationId) {
            const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';
            const trackingUrl = `${baseUrl}home/reservationTracking?id=${reservationId}`;
            generateQRCode(trackingUrl, qrContainer.id);
        }
    });

    // Gestion des boutons de copie
    document.querySelectorAll('.copy-button').forEach(button => {
        button.addEventListener('click', function() {
            const textToCopy = this.getAttribute('data-copy');
            if (textToCopy) {
                copyToClipboard(textToCopy, this);
            }
        });
    });

    // Gestion des modals de codes QR
    document.querySelectorAll('[id^="codeModal"]').forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            const modalId = this.id;
            const reservationId = modalId.replace('codeModal', '');

            console.log(`🔄 Modal ouverte pour réservation ${reservationId}, génération des QR codes...`);

            // Récupérer les codes depuis les éléments de la modal
            const entryCodeElement = document.getElementById(`code-acces-${reservationId}`);
            const exitCodeElement = document.getElementById(`code-sortie-${reservationId}`);

            if (entryCodeElement && entryCodeElement.textContent.trim() !== 'Génération...') {
                const entryCode = entryCodeElement.textContent.trim();
                generateQRCode(entryCode, `qr-entry-code-${reservationId}`, '007bff');

                // Afficher le bouton de copie
                const copyEntryBtn = document.getElementById(`copy-acces-${reservationId}`);
                if (copyEntryBtn) copyEntryBtn.style.display = 'inline-block';
            }

            if (exitCodeElement && exitCodeElement.textContent.trim() !== 'Génération...') {
                const exitCode = exitCodeElement.textContent.trim();
                generateQRCode(exitCode, `qr-exit-code-${reservationId}`, '198754');

                // Afficher le bouton de copie
                const copyExitBtn = document.getElementById(`copy-sortie-${reservationId}`);
                if (copyExitBtn) copyExitBtn.style.display = 'inline-block';
            }
        });
    });

    console.log('✅ Gestionnaire de QR codes et copie initialisé');
});

// Fonction globale pour copier dans le presse-papiers (utilisée par les boutons onclick)
function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            console.log('✅ Code copié:', text);
            // Afficher une notification de succès
            showCopyNotification('Code copié !');
        }).catch(err => {
            console.error('❌ Erreur copie:', err);
            fallbackCopy(text);
        });
    } else {
        fallbackCopy(text);
    }
}

// Fonction de fallback pour la copie
function fallbackCopy(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        document.execCommand('copy');
        console.log('✅ Code copié (fallback):', text);
        showCopyNotification('Code copié !');
    } catch (err) {
        console.error('❌ Erreur copie fallback:', err);
        showCopyNotification('Erreur lors de la copie', 'error');
    }

    document.body.removeChild(textArea);
}

// Affiche une notification de copie
function showCopyNotification(message, type = 'success') {
    // Créer une notification temporaire
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
    notification.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 200px;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : 'exclamation-triangle'} me-2"></i>
        ${message}
    `;

    document.body.appendChild(notification);

    // Animation d'apparition
    setTimeout(() => notification.style.opacity = '1', 100);

    // Suppression automatique
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => document.body.removeChild(notification), 300);
    }, 2000);
}

// Gestion des onglets du profil
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 Initialisation de la gestion des onglets du profil...');
    
    // Active un onglet spécifique
    function activateProfileTab(tabId) {
        console.log(`🎯 Tentative d'activation de l'onglet: ${tabId}`);
        
        // Désactive tous les onglets
        document.querySelectorAll('#profileTabs .nav-link').forEach(tab => {
            tab.classList.remove('active');
            tab.setAttribute('aria-selected', 'false');
        });
        
        document.querySelectorAll('.tab-content .tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });
        
        // Active l'onglet demandé
        const tabButton = document.getElementById(tabId + '-tab');
        const tabPane = document.getElementById(tabId);
        
        if (tabButton && tabPane) {
            tabButton.classList.add('active');
            tabButton.setAttribute('aria-selected', 'true');
            tabPane.classList.add('show', 'active');
            
            // Met à jour l'URL
            window.history.replaceState(null, null, '#' + tabId);
            console.log(`✅ Onglet activé: ${tabId}`);
            return true;
        } else {
            console.warn(`⚠️ Onglet non trouvé: ${tabId}`);
            return false;
        }
    }

    // Expose la fonction globalement
    window.activateProfileTab = activateProfileTab;

    // Active l'onglet depuis l'URL au chargement
    function checkAndActivateTabFromUrl() {
        const hash = window.location.hash.substring(1);
        console.log('🔗 Hash détecté dans l\'URL:', hash);

        if (hash && (hash === 'notifications' || hash === 'reservations' || hash === 'informations')) {
            console.log('🎯 Activation de l\'onglet:', hash);
            activateProfileTab(hash);
            return true;
        }
        return false;
    }

    // Vérifier immédiatement
    checkAndActivateTabFromUrl();

    // Vérifier après un délai pour s'assurer que le DOM est prêt
    setTimeout(() => {
        if (!checkAndActivateTabFromUrl()) {
            console.log('ℹ️ Aucun hash valide, onglet par défaut actif');
        }
    }, 100);

    // Écouter les changements de hash
    window.addEventListener('hashchange', () => {
        console.log('🔄 Changement de hash détecté');
        checkAndActivateTabFromUrl();
    });

    // Gestion des liens vers les onglets
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href').substring(1);
            if (document.getElementById(targetId + '-tab')) {
                e.preventDefault();
                activateProfileTab(targetId);
            }
        });
    });

    console.log('✅ Gestion des onglets du profil initialisée');
});
