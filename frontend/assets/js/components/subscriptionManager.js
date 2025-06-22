/**
 * Gestionnaire des abonnements
 * Gère l'affichage et les interactions liées aux abonnements utilisateur
 */

class SubscriptionManager {
    constructor() {
        this.currentPage = this.detectCurrentPage();
        this.init();
    }

    detectCurrentPage() {
        const path = window.location.pathname;
        if (path.includes('/subscription')) return 'subscription';
        if (path.includes('/profile')) return 'profile';
        return 'other';
    }

    init() {
        console.log('🎫 SubscriptionManager: Initialisation...');
        
        if (this.currentPage === 'subscription') {
            this.setupSubscriptionPage();
        } else if (this.currentPage === 'profile') {
            this.setupProfileSubscriptionSection();
        }
        
        // Charger les avantages d'abonnement pour toutes les pages
        this.loadSubscriptionBenefits();
        
        console.log('✅ SubscriptionManager: Initialisé');
    }

    setupSubscriptionPage() {
        console.log('📄 Configuration page abonnements...');
        
        // Gestionnaires pour les boutons d'abonnement
        const subscriptionButtons = document.querySelectorAll('.btn-subscribe');
        subscriptionButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                this.handleSubscriptionClick(e);
            });
        });

        // Gestionnaires pour les modals de confirmation
        this.setupSubscriptionModals();
    }

    setupProfileSubscriptionSection() {
        console.log('👤 Configuration section abonnement du profil...');
        
        // Gestionnaires pour les actions d'abonnement dans le profil
        const cancelButton = document.getElementById('cancel-subscription-btn');
        if (cancelButton) {
            cancelButton.addEventListener('click', (e) => {
                this.handleCancelSubscription(e);
            });
        }

        const upgradeButtons = document.querySelectorAll('.btn-upgrade');
        upgradeButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                this.handleUpgradeSubscription(e);
            });
        });
    }

    setupSubscriptionModals() {
        // Configuration des modals de confirmation d'abonnement
        const confirmButtons = document.querySelectorAll('.confirm-subscription');
        confirmButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                this.confirmSubscription(e);
            });
        });
    }

    handleSubscriptionClick(event) {
        const button = event.target.closest('.btn-subscribe');
        const subscriptionType = button.dataset.subscriptionType;
        const price = button.dataset.price;
        
        console.log(`🎫 Abonnement sélectionné: ${subscriptionType} - ${price}€`);
        
        // Afficher la modal de confirmation
        this.showSubscriptionModal(subscriptionType, price);
    }

    showSubscriptionModal(type, price) {
        const modal = document.getElementById('subscription-confirmation-modal');
        if (modal) {
            // Mettre à jour le contenu de la modal
            const typeElement = modal.querySelector('.subscription-type');
            const priceElement = modal.querySelector('.subscription-price');
            
            if (typeElement) typeElement.textContent = type;
            if (priceElement) priceElement.textContent = price + '€';
            
            // Afficher la modal
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
        }
    }

    confirmSubscription(event) {
        const button = event.target;
        const form = button.closest('form');
        
        if (form) {
            // Désactiver le bouton pendant le traitement
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Traitement...';
            
            // Soumettre le formulaire
            form.submit();
        }
    }

    handleCancelSubscription(event) {
        event.preventDefault();
        
        if (confirm('Êtes-vous sûr de vouloir annuler votre abonnement ?')) {
            // Logique d'annulation d'abonnement
            console.log('🚫 Annulation d\'abonnement demandée');
            
            // Rediriger vers la page d'annulation ou soumettre un formulaire
            const cancelForm = document.getElementById('cancel-subscription-form');
            if (cancelForm) {
                cancelForm.submit();
            }
        }
    }

    handleUpgradeSubscription(event) {
        const button = event.target.closest('.btn-upgrade');
        const newType = button.dataset.upgradeType;
        
        console.log(`⬆️ Upgrade vers: ${newType}`);
        
        // Rediriger vers la page d'abonnements avec le type pré-sélectionné
        window.location.href = `${this.getBaseUrl()}subscription?upgrade=${newType}`;
    }

    loadSubscriptionBenefits() {
        // Charger les avantages d'abonnement depuis les données de la page
        const benefitsElement = document.getElementById('subscription-benefits');
        if (benefitsElement) {
            const freeMinutes = parseInt(benefitsElement.dataset.freeMinutes) || 0;
            const discountPercent = parseInt(benefitsElement.dataset.discountPercent) || 0;
            const subscriptionName = benefitsElement.dataset.subscriptionName || '';
            
            this.subscriptionBenefits = {
                freeMinutes,
                discountPercent,
                subscriptionName
            };
            
            console.log('🎁 Avantages d\'abonnement chargés:', this.subscriptionBenefits);
            
            // Exposer globalement pour les autres composants
            window.loadSubscriptionBenefits = () => this.subscriptionBenefits;
        } else {
            // Pas d'abonnement actif
            this.subscriptionBenefits = null;
            window.loadSubscriptionBenefits = () => null;
        }
    }

    getBaseUrl() {
        // Obtenir l'URL de base
        const metaBaseUrl = document.querySelector('meta[name="base-url"]');
        if (metaBaseUrl) {
            return metaBaseUrl.getAttribute('content');
        }
        
        // Fallback
        const currentPath = window.location.pathname;
        const pathParts = currentPath.split('/');
        let projectIndex = pathParts.findIndex(part => part === 'projet' || part === 'parking_d');
        if (projectIndex !== -1) {
            return '/' + pathParts.slice(1, projectIndex + 2).join('/') + '/';
        }
        
        return '/';
    }

    destroy() {
        // Nettoyage si nécessaire
        console.log('🧹 SubscriptionManager: Nettoyage...');
    }
}

// Initialiser le gestionnaire d'abonnements
document.addEventListener('DOMContentLoaded', function() {
    if (!window.subscriptionManager) {
        window.subscriptionManager = new SubscriptionManager();
    }
});

// Exporter pour utilisation globale
window.SubscriptionManager = SubscriptionManager;
