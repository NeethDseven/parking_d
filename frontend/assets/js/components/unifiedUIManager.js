/**
* UI MANAGER UNIFIÉ
* ================
*
* Consolide tous les composants d'interface utilisateur :
* - authForms.js
* - profileForms.js
* - profileTabs.js
* - navbarComponent.js
* - carousel.js
* - animationManager.js
* - placesFilter.js
* - placeCards.js
* - paymentComponent.js
* - paymentForms.js
* - faqSearch.js
* - errorPage.js
* - jobModals.js
* - debugComponent.js
*/
(function() {
'use strict';
if (window.UnifiedUIManager) {
console.log('⚠️ UnifiedUIManager déjà chargé');
return;
}        console.log(' UnifiedUIManager: Initialisation...');    class UnifiedUIManager {
constructor() {
this.currentPage = this.detectCurrentPage();
this.carouselInstance = null;
this.isLoadingPage = false; // Protection contre les appels multiples
this.init();
}
init() {
if (document.readyState === 'loading') {
document.addEventListener('DOMContentLoaded', () => this.setup());
} else {
this.setup();
}
}        setup() {
this.setupNavbar();
this.setupAuthentication();
this.setupProfile();
this.setupCarousel();
this.setupAnimations();
this.setupPlaces();
this.setupPayment();
this.setupFAQ();
this.setupForms();
// Configuration terminée silencieusement
}
detectCurrentPage() {
const path = window.location.pathname;
if (path.includes('/auth/')) return 'auth';
if (path.includes('/profile')) return 'profile';
if (path.includes('/places')) return 'places';
if (path.includes('/payment')) return 'payment';
if (path.includes('/faq')) return 'faq';
if (path.includes('/careers')) return 'careers';
if (path === '/' || path.includes('/home')) return 'home';
return 'other';
}
// ===========================================
// GESTION DE LA NAVBAR
// ===========================================
setupNavbar() {
const navbar = document.querySelector('.navbar');
if (navbar) {
navbar.style.backgroundColor = '#444444';
}
// Identifier la page actuelle
const pageName = document.querySelector('meta[name="current-page"]')?.content || '';
if (document.body && pageName) {
document.body.setAttribute('data-page', pageName);
}
}
// ===========================================
// AUTHENTIFICATION
// ===========================================
setupAuthentication() {
if (this.currentPage !== 'auth') return;
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
if (loginForm) {
loginForm.addEventListener('submit', (e) => this.handleLoginSubmit(e));
}
if (registerForm) {
registerForm.addEventListener('submit', (e) => this.handleRegisterSubmit(e));
}
// Boutons de visibilité du mot de passe
this.setupPasswordToggles();
}
handleLoginSubmit(e) {
const form = e.target;
const email = form.querySelector('#email, [name="email"]');
const password = form.querySelector('#password, [name="password"]');
if (!this.validateEmail(email.value)) {
e.preventDefault();
this.showFieldError(email, 'Format d\'email invalide');
return false;
}
if (!password.value) {
e.preventDefault();
this.showFieldError(password, 'Mot de passe requis');
return false;
}
return true;
}
handleRegisterSubmit(e) {
const form = e.target;
const password = form.querySelector('#password');
const confirmPassword = form.querySelector('#confirm_password');
// Validation du mot de passe
if (password && confirmPassword) {
if (password.value !== confirmPassword.value) {
e.preventDefault();
this.showFieldError(confirmPassword, 'Les mots de passe ne correspondent pas');
return false;
}
if (password.value.length < 6) {
e.preventDefault();
this.showFieldError(password, 'Le mot de passe doit contenir au moins 6 caractères');
return false;
}
}
return true;
}
setupPasswordToggles() {
const toggleButtons = document.querySelectorAll('.toggle-password');
toggleButtons.forEach(button => {
button.addEventListener('click', () => {
const targetSelector = button.dataset.target;
const target = document.querySelector(targetSelector);
if (target) {
const type = target.getAttribute('type') === 'password' ? 'text' : 'password';
target.setAttribute('type', type);
const icon = button.querySelector('i');
if (icon) {
icon.classList.toggle('fa-eye');
icon.classList.toggle('fa-eye-slash');
}
}
});
});
}
// ===========================================
// PROFIL UTILISATEUR
// ===========================================
setupProfile() {
if (this.currentPage !== 'profile') return;
// Gestion des onglets basés sur l'URL
this.handleProfileTabs();
window.addEventListener('hashchange', () => this.handleProfileTabs());
// Formulaire de mise à jour du profil
const updateProfileForm = document.getElementById('updateProfileForm');
if (updateProfileForm) {
updateProfileForm.addEventListener('submit', (e) => this.handleProfileUpdate(e));
}
// Formulaire de changement de mot de passe
const changePasswordForm = document.getElementById('changePasswordForm');
if (changePasswordForm) {
changePasswordForm.addEventListener('submit', (e) => this.handlePasswordChange(e));
}
}
handleProfileTabs() {
const hash = window.location.hash.replace('#', '');
if (hash) {
const tabButton = document.querySelector(`[data-bs-target="#${hash}"]`);
if (tabButton) {
const tab = new bootstrap.Tab(tabButton);
tab.show();
}
}
}
handleProfileUpdate(e) {
const form = e.target;
const email = form.querySelector('[name="email"]');
if (email && !this.validateEmail(email.value)) {
e.preventDefault();
this.showFieldError(email, 'Format d\'email invalide');
return false;
}
return true;
}
handlePasswordChange(e) {
const form = e.target;
const newPassword = form.querySelector('[name="new_password"]');
const confirmPassword = form.querySelector('[name="confirm_password"]');
if (newPassword.value !== confirmPassword.value) {
e.preventDefault();
this.showFieldError(confirmPassword, 'Les mots de passe ne correspondent pas');
return false;
}
if (newPassword.value.length < 6) {
e.preventDefault();
this.showFieldError(newPassword, 'Le mot de passe doit contenir au moins 6 caractères');
return false;
}
return true;
}
// ===========================================
// CARROUSEL (PAGE D'ACCUEIL)
// ===========================================
setupCarousel() {
if (this.currentPage !== 'home') return;
const carousel = document.querySelector('.home-carousel');
if (!carousel) return;
// Initialiser le carrousel Bootstrap
this.carouselInstance = new bootstrap.Carousel(carousel, {
interval: 5000,
keyboard: true,
pause: 'hover',
ride: 'carousel'
});
// Contrôles tactiles
this.setupCarouselTouchControls(carousel);
}
setupCarouselTouchControls(carousel) {
let startX = 0;
carousel.addEventListener('touchstart', (e) => {
startX = e.touches[0].clientX;
});
carousel.addEventListener('touchend', (e) => {
const endX = e.changedTouches[0].clientX;
const diff = startX - endX;
if (Math.abs(diff) > 50) {
if (diff > 0) {
this.carouselInstance.next();
} else {
this.carouselInstance.prev();
}
}
});
}
// ===========================================
// ANIMATIONS
// ===========================================
setupAnimations() {
const sections = document.querySelectorAll('.container > div, .card, .feature-item');
sections.forEach(section => {
section.classList.add('animate-on-scroll');
});
window.addEventListener('scroll', () => this.animateOnScroll());
this.animateOnScroll(); // Vérification initiale
}
animateOnScroll() {
const elements = document.querySelectorAll('.animate-on-scroll');
elements.forEach(element => {
if (this.isElementInViewport(element)) {
element.classList.add('animated');
}
});
}
isElementInViewport(el) {
const rect = el.getBoundingClientRect();
return (
rect.top >= 0 &&
rect.left >= 0 &&
rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
rect.right <= (window.innerWidth || document.documentElement.clientWidth)
);
}
// ===========================================
// PLACES
// ===========================================
setupPlaces() {
if (this.currentPage !== 'places') return;
this.setupPlacesFilter();
this.setupPlacesCards();
this.setupPagination();
}        setupPlacesFilter() {
const typeFilter = document.getElementById('type-filter');
if (typeFilter) {
// Supprimer l'ancien gestionnaire s'il existe pour éviter les doublons
typeFilter.removeEventListener('change', this.handleTypeFilterChange);
// Ajouter le nouveau gestionnaire
this.handleTypeFilterChange = () => {
// Utiliser le système de pagination AJAX intégré pour filtrer côté serveur
const type = typeFilter.value === 'all' ? '' : typeFilter.value;
// Utiliser la pagination intégrée
if (this.loadPageContent) {
this.loadPageContent(1, type);
} else {
// Fallback: filtrage local (moins optimal)
this.filterPlacesByType(typeFilter.value);
}
};
typeFilter.addEventListener('change', this.handleTypeFilterChange);
}
const showFeesCheckbox = document.getElementById('show-fees');
if (showFeesCheckbox) {
// Supprimer l'ancien gestionnaire s'il existe
showFeesCheckbox.removeEventListener('change', this.handleFeesCheckboxChange);
// Créer le nouveau gestionnaire
this.handleFeesCheckboxChange = () => {
const tarifsInfo = document.querySelector('.tarifs-info');
if (tarifsInfo) {
tarifsInfo.classList.toggle('hidden', !showFeesCheckbox.checked);
}
};
showFeesCheckbox.addEventListener('change', this.handleFeesCheckboxChange);
// Vérifier l'état initial de la checkbox au chargement/rechargement
const tarifsInfo = document.querySelector('.tarifs-info');
if (tarifsInfo) {
tarifsInfo.classList.toggle('hidden', !showFeesCheckbox.checked);
}
}
}
filterPlacesByType(type) {
const placeCards = document.querySelectorAll('.place-card');
let visibleCount = 0;
placeCards.forEach(card => {
const cardType = card.dataset.type;
const shouldShow = type === 'all' || cardType === type;
card.style.display = shouldShow ? 'block' : 'none';
if (shouldShow) visibleCount++;
});
// Mettre à jour le compteur
const availableCount = document.getElementById('available-count');
if (availableCount) {
availableCount.textContent = visibleCount;
}
}        setupPlacesCards() {
// S'assurer que le container des places a les bonnes classes
const placesContainer = document.querySelector('#places-container');
if (placesContainer) {
this.ensurePlacesContainerClasses(placesContainer);
}

const cards = document.querySelectorAll('.place-card');
// Animation d'apparition
cards.forEach((card, index) => {
setTimeout(() => {
card.classList.add('fade-in');
}, index * 100);
});
// Égaliser les hauteurs
this.equalizeCardHeights();
window.addEventListener('resize', () => this.equalizeCardHeights());
// S'assurer que les boutons de réservation immédiate sont correctement configurés
this.setupImmediateReservationButtons();
}        setupImmediateReservationButtons() {
const immediateButtons = document.querySelectorAll('.btn-reserve-immediate');
console.log(` setupImmediateReservationButtons: ${immediateButtons.length} boutons trouvés`);
immediateButtons.forEach((button, index) => {
// Ajouter des attributs pour améliorer l'accessibilité et le style
if (!button.hasAttribute('data-immediate-setup')) {
console.log(` Configuration du bouton ${index + 1}`);
button.setAttribute('data-immediate-setup', 'true');
button.setAttribute('title', 'Réserver cette place immédiatement');
// S'assurer que le bouton a les bonnes classes
if (!button.classList.contains('btn-reserve-immediate')) {
button.classList.add('btn-reserve-immediate');
}
// Forcer les styles pour s'assurer qu'ils sont visibles
button.style.cssText = `
background: linear-gradient(135deg, #ff6b6b, #ee5a24) !important;
color: white !important;
border: none !important;
padding: 8px 16px !important;
border-radius: 25px !important;
transition: all 0.3s ease !important;
font-size: 0.9rem !important;
font-weight: 500 !important;
box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3) !important;
width: 100% !important;
display: block !important;
text-align: center !important;
text-decoration: none !important;
cursor: pointer !important;
`;
// Vérifier le formulaire parent
const form = button.closest('form');
const placeId = form?.querySelector('input[name="place_id"]')?.value;
console.log(`  - Bouton ${index + 1} pour place ID: ${placeId}`);
// Ajouter un gestionnaire hover
button.addEventListener('mouseenter', function() {
this.style.transform = 'translateY(-2px)';
this.style.boxShadow = '0 4px 12px rgba(255, 107, 107, 0.4)';
});
button.addEventListener('mouseleave', function() {
this.style.transform = 'translateY(0)';
this.style.boxShadow = '0 2px 8px rgba(255, 107, 107, 0.3)';
});
}
});
console.log(`✅ Configuration terminée pour ${immediateButtons.length} boutons`);
}
equalizeCardHeights() {
const cards = document.querySelectorAll('.place-card .card-body');
let maxHeight = 0;
// Reset des hauteurs
cards.forEach(card => {
card.style.height = 'auto';
});
// Trouver la hauteur maximale
cards.forEach(card => {
const height = card.offsetHeight;
if (height > maxHeight) {
maxHeight = height;
}
});
// Appliquer la hauteur maximale
cards.forEach(card => {
card.style.height = maxHeight + 'px';
});
}
// ===========================================
// PAIEMENT
// ===========================================
setupPayment() {
if (this.currentPage !== 'payment') return;
const paymentModes = document.querySelectorAll('input[name="mode_paiement"]');
const detailsBoxes = document.querySelectorAll('.payment-details');
paymentModes.forEach(mode => {
mode.addEventListener('change', function() {
detailsBoxes.forEach(box => box.style.display = 'none');
if (this.checked) {
const detailsBox = document.getElementById(`${this.value}-details`);
if (detailsBox) {
detailsBox.style.display = 'block';
}
}
});
});
// Validation des cartes de crédit
const cardNumberInput = document.getElementById('card_number');
if (cardNumberInput) {
cardNumberInput.addEventListener('input', (e) => {
// Formatage automatique du numéro de carte
let value = e.target.value.replace(/\s/g, '').replace(/\D/g, '');
value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
e.target.value = value;
});
}
}
// ===========================================
// FAQ
// ===========================================
setupFAQ() {
if (this.currentPage !== 'faq') return;
const searchInput = document.getElementById('faq-search-input');
if (searchInput) {
searchInput.addEventListener('input', (e) => {
this.searchFAQ(e.target.value);
});
}
// Boutons d'expansion/contraction des catégories
const categoryTogglers = document.querySelectorAll('.category-toggler');
categoryTogglers.forEach(toggler => {
toggler.addEventListener('click', () => {
const target = document.querySelector(toggler.dataset.target);
if (target) {
target.classList.toggle('show');
}
});
});
}
searchFAQ(searchTerm) {
const items = document.querySelectorAll('.accordion-item');
const term = searchTerm.toLowerCase().trim();
if (!term) {
items.forEach(item => item.style.display = 'block');
return;
}
items.forEach(item => {
const question = item.querySelector('.accordion-button').textContent.toLowerCase();
const answer = item.querySelector('.accordion-body').textContent.toLowerCase();
const matches = question.includes(term) || answer.includes(term);
item.style.display = matches ? 'block' : 'none';
});
}
// ===========================================
// VALIDATION ET UTILITAIRES
// ===========================================
setupForms() {
const forms = document.querySelectorAll('form:not([data-no-validation])');
forms.forEach(form => {
form.addEventListener('submit', (e) => {
if (!this.validateForm(form)) {
e.preventDefault();
return false;
}
});
});
}
validateForm(form) {
const requiredFields = form.querySelectorAll('[required]');
let isValid = true;
requiredFields.forEach(field => {
if (!field.value.trim()) {
this.showFieldError(field, 'Ce champ est requis');
isValid = false;
} else {
this.clearFieldError(field);
}
});
// Validation des emails
const emailFields = form.querySelectorAll('input[type="email"]');
emailFields.forEach(field => {
if (field.value && !this.validateEmail(field.value)) {
this.showFieldError(field, 'Format d\'email invalide');
isValid = false;
}
});
return isValid;
}
validateEmail(email) {
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
return emailRegex.test(email);
}
showFieldError(field, message) {
this.clearFieldError(field);
field.classList.add('is-invalid');
const errorDiv = document.createElement('div');
errorDiv.className = 'invalid-feedback';
errorDiv.textContent = message;
field.parentNode.appendChild(errorDiv);
}
clearFieldError(field) {
field.classList.remove('is-invalid');
const errorDiv = field.parentNode.querySelector('.invalid-feedback');
if (errorDiv) {
errorDiv.remove();
}
}
// ===========================================
// MÉTHODES UTILITAIRES
// ===========================================
showAlert(message, type = 'info') {
const alertDiv = document.createElement('div');
alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
alertDiv.innerHTML = `
${message}
<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
`;
const container = document.querySelector('.container-fluid, .container');
if (container) {
container.insertBefore(alertDiv, container.firstChild);
}
setTimeout(() => {
if (alertDiv.parentNode) {
alertDiv.remove();
}
}, 5000);
}        destroy() {
if (this.carouselInstance) {
this.carouselInstance.dispose();
}
}        /**
* Réinitialise les gestionnaires d'événements après un chargement AJAX
*/
reinitialize() {
console.log(' UnifiedUIManager: Réinitialisation après AJAX...');
// Réinitialiser les composants spécifiques à la page
if (this.currentPage === 'places') {
this.setupPlacesFilter();
this.setupPlacesCards();
// Restaurer l'état de la checkbox "Afficher les tarifs"
this.restoreFeesDisplayState();
}
// Réinitialiser les animations pour les nouveaux éléments
this.setupAnimations();
// Réinitialiser les tooltips Bootstrap
this.initTooltips();
console.log('✅ UnifiedUIManager: Réinitialisé');
}
/**
* Initialise les tooltips Bootstrap pour les nouveaux éléments
*/
initTooltips() {
// Supprimer les anciens tooltips pour éviter les conflits
const existingTooltips = document.querySelectorAll('.tooltip');
existingTooltips.forEach(tooltip => tooltip.remove());
// Initialiser les nouveaux tooltips
if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
tooltipElements.forEach(element => {
// Vérifier s'il y a déjà une instance de tooltip
const existingTooltip = bootstrap.Tooltip.getInstance(element);
if (existingTooltip) {
existingTooltip.dispose();
}
new bootstrap.Tooltip(element);
});
}
}
/**
* Restaure l'état d'affichage des tarifs après AJAX
*/
restoreFeesDisplayState() {
const showFeesCheckbox = document.getElementById('show-fees');
const tarifsInfo = document.querySelector('.tarifs-info');
if (showFeesCheckbox && tarifsInfo) {
// Appliquer l'état actuel de la checkbox
tarifsInfo.classList.toggle('hidden', !showFeesCheckbox.checked);
}
}
// ===========================================
// PAGINATION AJAX
// ===========================================
setupPagination() {
if (this.currentPage !== 'places') return;
// Configuration de la pagination
this.paginationConfig = {
paginationContainerSelector: '#pagination-container',
paginationLinkSelector: '.ajax-page-link, .page-link',
placesContainerSelector: '#places-container',
loadingSpinnerSelector: '#loading-spinner',
apiEndpoint: 'api/getPlacesPage',
retryAttempts: 2,
retryDelay: 1000
};
this.setupPaginationHandlers();
this.observePaginationChanges();
this.setupBrowserNavigation();
}        setupPaginationHandlers() {
const paginationContainer = document.querySelector(this.paginationConfig.paginationContainerSelector);
if (!paginationContainer) {
console.warn('[Pagination] Container non trouvé:', this.paginationConfig.paginationContainerSelector);
return false;
}
console.log('[Pagination] Configuration des gestionnaires...');
// Gestionnaire d'événements pour tout le conteneur (délégation d'événements)
paginationContainer.removeEventListener('click', this.handlePaginationClick.bind(this));
paginationContainer.addEventListener('click', this.handlePaginationClick.bind(this));
// Rendre les liens visuellement cliquables
const paginationLinks = paginationContainer.querySelectorAll(this.paginationConfig.paginationLinkSelector);
console.log('[Pagination] Liens trouvés:', paginationLinks.length, 'avec sélecteur:', this.paginationConfig.paginationLinkSelector);
paginationLinks.forEach(link => {
if (!link.hasAttribute('data-pagination-applied')) {
link.style.cursor = 'pointer';
link.setAttribute('data-pagination-applied', 'true');
link.setAttribute('role', 'button');
link.setAttribute('tabindex', '0');
link.classList.add('pagination-enhanced');
// Gestion des événements clavier (accessibilité)
link.addEventListener('keydown', function (e) {
if (e.key === 'Enter' || e.key === ' ') {
e.preventDefault();
this.click();
}
});
}
});
return true;
}        handlePaginationClick(event) {
console.log('[Pagination] Clic détecté sur:', event.target);
// Trouver l'élément de lien cliqué (ou son parent)
let target = event.target;
let depth = 0;
let paginationLink = null;
// Chercher jusqu'à 3 niveaux de profondeur
while (target && depth < 3) {
if (target.matches && target.matches(this.paginationConfig.paginationLinkSelector)) {
paginationLink = target;
console.log('[Pagination] Lien de pagination trouvé:', paginationLink);
break;
}
target = target.parentElement;
depth++;
}
// Si un lien de pagination a été trouvé
if (paginationLink) {
event.preventDefault();
event.stopPropagation();
// Obtenir les données de pagination
const page = paginationLink.getAttribute('data-page');
console.log('[Pagination] Page demandée:', page);
if (!page) return;
// Obtenir le type optionnel
let type = paginationLink.getAttribute('data-type') || '';
// Si le type est vide, vérifier le filtre actuel
if (!type || type === 'all') {
const typeFilter = document.getElementById('type-filter');
if (typeFilter && typeFilter.value !== 'all') {
type = typeFilter.value;
} else {
type = '';
}
}
// Charger la page
this.loadPageContent(page, type);
return false;
}
}        loadPageContent(page, type, retryCount = 0) {
// Protection contre les appels multiples simultanés
if (this.isLoadingPage) {
console.log('[Pagination] Appel déjà en cours, ignoré');
return;
}
this.isLoadingPage = true;
// Si aucun type n'est fourni, vérifier le filtre actuel
if (!type) {
const typeFilter = document.getElementById('type-filter');
if (typeFilter && typeFilter.value !== 'all') {
type = typeFilter.value;
}
}
console.log(`[Pagination] Chargement page ${page}, type: ${type || 'tous'}`);
// Afficher l'indicateur de chargement
this.showPaginationLoading(true);            // Construire l'URL de l'API
const baseUrl = this.getBaseUrl();
let apiUrl = `${baseUrl}${this.paginationConfig.apiEndpoint}?page=${page}`;
// Seulement ajouter le type s'il est défini et différent de 'all' ou vide
if (type && type !== 'all' && type !== '') {
apiUrl += `&type=${encodeURIComponent(type)}`;
}
// Mettre à jour le filtre de type si présent
const typeFilter = document.getElementById('type-filter');
if (typeFilter && type) {
typeFilter.value = type;
}
// Configuration du timeout
const controller = new AbortController();
const timeoutId = setTimeout(() => controller.abort(), 15000);
// Requête AJAX
fetch(apiUrl, { signal: controller.signal })
.then(response => {
clearTimeout(timeoutId);
if (!response.ok) {
throw new Error(`Erreur HTTP: ${response.status}`);
}
return response.json();
})
.then(data => {
// Vérifier si la réponse est valide
if (!data || typeof data !== 'object') {
throw new Error('Format de réponse invalide');
}
// Traiter les données
this.updatePageContent(data);

// Mettre à jour l'URL du navigateur
this.updateBrowserUrl(page, type);

// Réinitialiser les gestionnaires après la mise à jour du contenu
setTimeout(() => {
this.setupPaginationHandlers();
this.reinitializeAfterAjax();
}, 100);
})
.catch(error => {
console.error('[Pagination] Erreur:', error);
// Nouvelle tentative en cas d'échec
if (retryCount < this.paginationConfig.retryAttempts) {
setTimeout(() => {
this.loadPageContent(page, type, retryCount + 1);
}, this.paginationConfig.retryDelay * (retryCount + 1));
} else {
this.showPaginationError('Impossible de charger les places. Veuillez réessayer.');
this.isLoadingPage = false; // Libérer le verrou
}
this.showPaginationLoading(false);                });
}
updatePageContent(data) {
if (!data.success) {
this.showPaginationError(data.error || 'Une erreur est survenue lors du chargement des données.');
return;
}
// Mettre à jour le conteneur de places
const placesContainer = document.querySelector(this.paginationConfig.placesContainerSelector);
if (placesContainer && data.places_html) {
placesContainer.innerHTML = data.places_html;

// S'assurer que le container garde ses classes Bootstrap essentielles
this.ensurePlacesContainerClasses(placesContainer);

// Mettre à jour les attributs de données
if (data.current_page) {
placesContainer.dataset.currentPage = data.current_page;
}
}
// Mettre à jour la pagination
const paginationContainer = document.querySelector(this.paginationConfig.paginationContainerSelector);
if (paginationContainer && data.pagination_html) {
paginationContainer.innerHTML = data.pagination_html;
}
// Mettre à jour les compteurs
this.updatePlacesCounters(data);            // Animer les cartes de places
setTimeout(() => this.enhancePlaceCards(), 200);
// Terminer le chargement
this.showPaginationLoading(false);
// Libérer le verrou de chargement
this.isLoadingPage = false;
}
updatePlacesCounters(data) {
const availableCount = document.getElementById('available-count');
const totalCount = document.getElementById('total-count');
const progressBar = document.getElementById('places-progress');
if (availableCount && data.available_count !== undefined) {
availableCount.textContent = data.available_count;
}
if (totalCount && data.total_count !== undefined) {
totalCount.textContent = data.total_count;
}
if (progressBar && data.available_count !== undefined && data.total_count !== undefined) {
const percentage = data.total_count > 0 ? (data.available_count / data.total_count) * 100 : 0;
progressBar.style.width = `${percentage}%`;
progressBar.setAttribute('aria-valuenow', data.available_count);
progressBar.setAttribute('aria-valuemax', data.total_count);
}
}
ensurePlacesContainerClasses(placesContainer) {
// S'assurer que le container a les classes Bootstrap nécessaires
const requiredClasses = ['row', 'justify-content-center'];

requiredClasses.forEach(className => {
if (!placesContainer.classList.contains(className)) {
placesContainer.classList.add(className);
console.log(`✅ Classe '${className}' ajoutée au container des places`);
}
});

// S'assurer que le style flex est correct
placesContainer.style.display = 'flex';
placesContainer.style.flexWrap = 'wrap';
console.log('✅ Styles flex appliqués au container des places');
}

enhancePlaceCards() {
// Animation CSS basique pour les nouvelles cartes
const placeCards = document.querySelectorAll('.place-card');
placeCards.forEach((card, index) => {
card.style.opacity = '0';
card.style.transform = 'translateY(20px)';
setTimeout(() => {
card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
card.style.opacity = '1';
card.style.transform = 'translateY(0)';
}, index * 100);
});
// Égaliser les hauteurs des nouvelles cartes
this.equalizeCardHeights();
}
showPaginationLoading(isLoading) {
const spinner = document.querySelector(this.paginationConfig.loadingSpinnerSelector);
if (spinner) {
if (isLoading) {
spinner.classList.remove('hidden');
} else {
spinner.classList.add('hidden');
}
}
// Ajout/suppression de la classe de chargement sur le conteneur de pagination
const paginationContainer = document.querySelector(this.paginationConfig.paginationContainerSelector);
if (paginationContainer) {
if (isLoading) {
paginationContainer.classList.add('loading');
} else {
paginationContainer.classList.remove('loading');
}
}
}
showPaginationError(message) {
const placesContainer = document.querySelector(this.paginationConfig.placesContainerSelector);
if (placesContainer) {
placesContainer.innerHTML = `
<div class="col-12">
<div class="alert alert-danger">
<i class="fas fa-exclamation-triangle me-2"></i>
${message}
<button class="btn btn-sm btn-outline-danger ms-3" onclick="location.reload()">
<i class="fas fa-sync-alt me-1"></i> Actualiser
</button>
</div>
</div>
`;
}
}
observePaginationChanges() {
// Utiliser MutationObserver pour détecter les changements dans le DOM
try {
const observer = new MutationObserver(mutations => {
const shouldReapply = mutations.some(mutation => {
for (const node of mutation.addedNodes) {
if (node.nodeType === 1) { // Element node
if (node.matches && (node.matches(this.paginationConfig.paginationContainerSelector) ||
node.querySelector(this.paginationConfig.paginationContainerSelector))) {
return true;
}
if (node.matches && (node.matches(this.paginationConfig.paginationLinkSelector) ||
node.querySelector(this.paginationConfig.paginationLinkSelector))) {
return true;
}
}
}
return false;
});
if (shouldReapply) {
setTimeout(() => this.setupPaginationHandlers(), 50);
}
});
// Observer le document entier pour les changements
observer.observe(document.body, {
childList: true,
subtree: true
});
} catch (e) {
console.error('[Pagination] Erreur lors de la création de l\'observateur:', e);
// Fallback en cas d'erreur avec MutationObserver
setInterval(() => this.setupPaginationHandlers(), 2000);
}
}
setupBrowserNavigation() {
// Gérer les boutons Précédent/Suivant du navigateur
window.addEventListener('popstate', (event) => {
if (event.state && this.currentPage === 'places') {
const { page, type } = event.state;
console.log(`🔙 Navigation navigateur: page ${page}, type ${type}`);
this.loadPageContent(page || 1, type || '');
}
});
}

updateBrowserUrl(page, type) {
console.log(`🔗 updateBrowserUrl appelée avec: page=${page}, type="${type}"`);

// Construire la nouvelle URL
const baseUrl = this.getBaseUrl();
let newUrl = `${baseUrl}home/places`;

// Ajouter les paramètres de page et type si nécessaire
const params = new URLSearchParams();
if (page && page > 1) {
params.append('page', page);
console.log(`✅ Paramètre page ajouté: ${page}`);
}
if (type && type !== 'all' && type !== '') {
params.append('type', type);
console.log(`✅ Paramètre type ajouté: ${type}`);
} else {
console.log(`❌ Type ignoré: "${type}" (all=${type === 'all'}, empty=${type === ''})`);
}

// Ajouter les paramètres à l'URL si ils existent
if (params.toString()) {
newUrl += '?' + params.toString();
console.log(`🔗 URL avec paramètres: ${newUrl}`);
} else {
console.log(`🔗 URL sans paramètres: ${newUrl}`);
}

// Mettre à jour l'URL du navigateur sans recharger la page
if (window.history && window.history.pushState) {
window.history.pushState({ page, type }, '', newUrl);
console.log(`🔗 URL mise à jour: ${newUrl}`);
} else {
console.warn('⚠️ History API non supportée');
}
}

getBaseUrl() {
// Essayer d'obtenir l'URL de base depuis la balise meta
const metaBaseUrl = document.querySelector('meta[name="base-url"]');
if (metaBaseUrl) {
return metaBaseUrl.getAttribute('content');
}
// Fallback: extraire l'URL de base du chemin actuel
const currentPath = window.location.pathname;
const pathParts = currentPath.split('/');
// Trouver l'index de 'projet' ou 'parking_d'
let projectIndex = pathParts.findIndex(part => part === 'projet' || part === 'parking_d');
if (projectIndex !== -1) {
return '/' + pathParts.slice(1, projectIndex + 2).join('/') + '/';
}            // Si tout échoue, utiliser le chemin relatif
return '/';
}
reinitializeAfterAjax() {
console.log(' Réinitialisation après AJAX...');
// Réinitialiser les composants UI d'abord
this.setupPlacesFilter();
this.setupPlacesCards(); // Ajouter la réinitialisation des cartes de places
this.restoreFeesDisplayState();
this.setupAnimations();
this.initTooltips();
// Réinitialiser les gestionnaires de réservation avec une vérification robuste
setTimeout(() => {
if (window.reservationManager && typeof window.reservationManager.reinitialize === 'function') {
console.log('♻️ Réinitialisation du gestionnaire de réservation...');
try {
window.reservationManager.reinitialize();
} catch (error) {
console.error('❌ Erreur lors de la réinitialisation du gestionnaire de réservation:', error);
}
} else {
console.warn('⚠️ reservationManager non disponible, tentative de réinitialisation manuelle...');
this.fallbackReservationSetup();
}
}, 200); // Délai pour permettre au DOM de se stabiliser
console.log('✅ Réinitialisation terminée');
}
/**
* Configuration de secours pour les boutons de réservation si le gestionnaire principal n'est pas disponible
*/
fallbackReservationSetup() {
console.log(' Configuration de secours des boutons de réservation...');
// S'assurer que les gestionnaires d'événements sont en place pour les boutons de réservation immédiate
const immediateButtons = document.querySelectorAll('.btn-reserve-immediate');
console.log(` Configuration de secours pour ${immediateButtons.length} boutons de réservation immédiate`);
immediateButtons.forEach((button, index) => {
if (!button.hasAttribute('data-fallback-setup')) {
button.setAttribute('data-fallback-setup', 'true');
// Vérifier que le bouton a les bons styles
if (!button.style.background) {
button.style.cssText = `
background: linear-gradient(135deg, #ff6b6b, #ee5a24) !important;
color: white !important;
border: none !important;
padding: 8px 16px !important;
border-radius: 25px !important;
transition: all 0.3s ease !important;
font-size: 0.9rem !important;
font-weight: 500 !important;
box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3) !important;
width: 100% !important;
`;
}
console.log(`  - Bouton ${index + 1} configuré en mode fallback`);
}            });
}
}
// Initialiser le gestionnaire unifié d'interface utilisateur// Exporter la classe globalement
window.UnifiedUIManager = UnifiedUIManager;
// Créer l'instance seulement si elle n'existe pas déjà
if (!window.uiManager) {
window.uiManager = new UnifiedUIManager();
}
// Exposer l'API de pagination pour compatibilité avec l'ancien système
window.directClickPagination = {
loadPage: window.uiManager.loadPageContent?.bind(window.uiManager),
setupEventListeners: window.uiManager.setupPaginationHandlers?.bind(window.uiManager),
updatePlaces: window.uiManager.updatePageContent?.bind(window.uiManager)
};
// Nettoyage à la fermeture de la page
window.addEventListener('beforeunload', () => {
if (window.uiManager) {
window.uiManager.destroy();
}
});
})();
