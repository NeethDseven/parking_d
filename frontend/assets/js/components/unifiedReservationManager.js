/**
 * RESERVATION MANAGER UNIFIÉ
 * =========================
 *
 * Consolide tous les composants de réservation pour éliminer les redondances
 */

(function() {
    'use strict';

    /* Protection contre le double chargement */
    if (window.UnifiedReservationManager) {
        console.log('⚠️ UnifiedReservationManager déjà chargé');
        return;
    }

    console.log('🚀 UnifiedReservationManager: Initialisation...');

    class UnifiedReservationManager {
        constructor() {
            this.reservationModal = null;
            this.placeIdInput = null;
            this.formSubmitted = false;
            this.subscriptionBenefits = null;
            this.currentPlaceData = null;
            this.trackerInterval = null;
            
            this.init();
        }

        init() {
            // Attendre que le DOM soit prêt
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.setup());
            } else {
                this.setup();
            }
        }        setup() {
            this.setupModal();            this.setupFormHandlers();
            this.setupButtonHandlers();
            this.setupAlertHandlers();
            this.setupReservationTracking();
            this.loadSubscriptionBenefits();
            
            // Méthodes spécifiques maintenant intégrées
            // this.setupPageSpecificHandlers(); // Intégré dans les autres méthodes
            // this.setupProfileHandlers(); // Intégré dans setupFormHandlers
            
            // Exposer les fonctions globales nécessaires
            this.exposeGlobalFunctions();
            
            // Configuration terminée silencieusement
        }

        // ===========================================
        // GESTION DE LA MODAL
        // ===========================================

        setupModal() {
            // Attendre que la modal soit disponible
            const waitForModal = () => {
                this.reservationModal = document.getElementById('reservationModal');
                if (!this.reservationModal) {
                    setTimeout(waitForModal, 100);
                    return;
                }

                // Événements de la modal
                this.reservationModal.addEventListener('show.bs.modal', (event) => {
                    this.handleModalShow(event);
                });

                this.reservationModal.addEventListener('shown.bs.modal', (event) => {
                    this.handleModalShown(event);
                });
            };

            waitForModal();
        }

        handleModalShow(event) {
            this.formSubmitted = false;

            if (event.relatedTarget) {
                const button = event.relatedTarget;

                const placeData = {
                    id: button.getAttribute('data-place-id'),
                    numero: button.getAttribute('data-place-numero'),
                    type: button.getAttribute('data-place-type'),
                    tarif: parseFloat(button.getAttribute('data-place-tarif')) || 2.0
                };

                this.currentPlaceData = placeData;
                this.reservationModal._placeData = placeData;
                this.reservationModal._relatedTarget = button;

                this.updateModalFields(placeData);
                this.setPlaceId(placeData.id);

                console.log('📖 Modal ouverte pour place:', placeData);
            }
        }

        handleModalShown(event) {
            // Validation finale que le place_id est bien défini
            setTimeout(() => {
                this.ensurePlaceIdSet();
                this.updateTrackedElements();
            }, 200);
        }

        updateModalFields(placeData) {
            if (!placeData.id) return;

            // Mettre à jour les informations affichées pour les utilisateurs connectés
            const tarifInfo = document.getElementById('tarif-info');
            if (tarifInfo) {
                tarifInfo.textContent = `Tarif: ${placeData.tarif.toFixed(2)} € / heure`;
                tarifInfo.dataset.tarif = placeData.tarif;
            }

            // Mettre à jour les informations affichées pour les invités
            const guestTarifInfo = document.getElementById('guest-tarif-info');
            if (guestTarifInfo) {
                guestTarifInfo.textContent = `Tarif: ${placeData.tarif.toFixed(2)} € / heure`;
                guestTarifInfo.dataset.tarif = placeData.tarif;
            }

            // Calculer le prix initial
            this.updatePriceCalculation();
        }

        // ===========================================
        // GESTION DU PLACE_ID
        // ===========================================

        setPlaceId(id) {
            if (!id) return;

            console.log('🔧 Définition place_id:', id);

            // Rechercher ou créer le champ place_id pour le formulaire utilisateur connecté
            let placeIdInput = document.getElementById('place_id');

            if (!placeIdInput) {
                const form = document.getElementById('reservation-form');
                if (form) {
                    placeIdInput = document.createElement('input');
                    placeIdInput.type = 'hidden';
                    placeIdInput.name = 'place_id';
                    placeIdInput.id = 'place_id';
                    form.appendChild(placeIdInput);
                }
            }

            if (placeIdInput) {
                placeIdInput.value = id;
                this.placeIdInput = placeIdInput;
            }

            // Rechercher ou créer le champ place_id pour le formulaire invité
            let guestPlaceIdInput = document.getElementById('guest_place_id');

            if (!guestPlaceIdInput) {
                const guestForm = document.getElementById('guest-reservation-form');
                if (guestForm) {
                    guestPlaceIdInput = document.createElement('input');
                    guestPlaceIdInput.type = 'hidden';
                    guestPlaceIdInput.name = 'place_id';
                    guestPlaceIdInput.id = 'guest_place_id';
                    guestForm.appendChild(guestPlaceIdInput);
                }
            }

            if (guestPlaceIdInput) {
                guestPlaceIdInput.value = id;
                this.guestPlaceIdInput = guestPlaceIdInput;
            }

            // Backup global
            window._lastSelectedPlaceId = id;
        }

        ensurePlaceIdSet() {
            let placeIdInput = this.placeIdInput || document.getElementById('place_id');
            let guestPlaceIdInput = this.guestPlaceIdInput || document.getElementById('guest_place_id');

            if ((!placeIdInput || !placeIdInput.value) && (!guestPlaceIdInput || !guestPlaceIdInput.value)) {
                // Récupérer depuis les données de la modal
                if (this.currentPlaceData && this.currentPlaceData.id) {
                    this.setPlaceId(this.currentPlaceData.id);
                }
                // Ou depuis le backup global
                else if (window._lastSelectedPlaceId) {
                    this.setPlaceId(window._lastSelectedPlaceId);
                }
            }
        }

        validatePlaceId() {
            this.ensurePlaceIdSet();
            
            const placeIdInput = document.getElementById('place_id');
            if (!placeIdInput || !placeIdInput.value) {
                console.error('❌ place_id manquant');
                alert("Erreur: Place non sélectionnée. Veuillez fermer cette fenêtre et cliquer à nouveau sur 'Réserver' pour une place.");
                return false;
            }
            
            // place_id validé
            return true;
        }        // ===========================================
        // GESTION DES FORMULAIRES
        // ===========================================
        
        setupFormHandlers() {
            const reservationForm = document.getElementById('reservation-form');
            if (!reservationForm) return;

            // Créer un gestionnaire unique pour éviter les conflits
            const submitHandler = (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.handleFormSubmit(event);
            };

            // Supprimer les anciens gestionnaires et attributs
            reservationForm.removeAttribute('onsubmit');
            
            // Cloner le formulaire pour supprimer tous les anciens event listeners
            const newForm = reservationForm.cloneNode(true);
            newForm.removeAttribute('onsubmit'); // S'assurer qu'il est supprimé du clone aussi
            reservationForm.parentNode.replaceChild(newForm, reservationForm);

            // Ajouter notre gestionnaire au nouveau formulaire
            newForm.addEventListener('submit', submitHandler);

            // Réinitialiser la référence et la configuration
            this.setupPriceCalculation(newForm);
            console.log('✅ Gestionnaire de formulaire configuré (AJAX)');

            // Gérer aussi le formulaire invité s'il existe
            this.setupGuestFormHandlers();
        }

        setupGuestFormHandlers() {
            const guestForm = document.getElementById('guest-reservation-form');
            if (!guestForm) return;

            // Créer un gestionnaire unique pour éviter les conflits
            const submitHandler = (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.handleGuestFormSubmit(event);
            };

            // Supprimer les anciens gestionnaires
            const newGuestForm = guestForm.cloneNode(true);
            guestForm.parentNode.replaceChild(newGuestForm, guestForm);

            // Ajouter notre gestionnaire au nouveau formulaire
            newGuestForm.addEventListener('submit', submitHandler);

            // Réinitialiser la configuration
            this.setupGuestPriceCalculation(newGuestForm);
        }

        async handleFormSubmit(event) {
            if (this.formSubmitted) return false;
            
            console.log('🚀 Soumission de formulaire');
            
            const submitButton = event.target.querySelector('button[type="submit"]');
            this.setButtonLoading(submitButton, true);
            
            try {
                // Validation du place_id
                if (!this.validatePlaceId()) {
                    this.formSubmitted = false;
                    return false;
                }
                
                // Validation des autres champs
                if (!this.validateFormFields(event.target)) {
                    this.formSubmitted = false;
                    return false;
                }
                
                // Debug: afficher les données du formulaire
                const formData = new FormData(event.target);
                console.log('📋 Données du formulaire:');
                for (let [key, value] of formData.entries()) {
                    console.log(`  ${key}: ${value}`);
                }
                
                // Marquer comme soumis
                this.formSubmitted = true;
                
                // Envoyer la requête AJAX au lieu d'une soumission classique
                const response = await fetch(event.target.action, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                console.log('📡 Réponse du serveur:', result);
                
                if (result.success) {
                    console.log('✅ Réservation créée avec succès, redirection vers:', result.redirect_url);
                    
                    // Fermer la modal si elle existe
                    if (this.reservationModal) {
                        const modalInstance = bootstrap.Modal.getInstance(this.reservationModal);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    }
                    
                    // Rediriger vers la page de paiement
                    window.location.href = result.redirect_url;
                    return true;
                } else {
                    // Afficher l'erreur
                    console.error('❌ Erreur:', result.error);
                    alert('Erreur: ' + (result.error || 'Une erreur est survenue lors de la création de la réservation.'));
                    this.formSubmitted = false;
                    return false;
                }
                
            } catch (error) {
                console.error('❌ Erreur lors de la soumission:', error);                alert('Une erreur est survenue lors de la soumission. Veuillez réessayer.');
                this.formSubmitted = false;
                return false;
            } finally {
                this.setButtonLoading(submitButton, false);
            }
        }

        async handleGuestFormSubmit(event) {
            if (this.formSubmitted) return false;

            console.log('🚀 Soumission de formulaire invité');

            const submitButton = event.target.querySelector('button[type="submit"]');
            this.setButtonLoading(submitButton, true);

            try {
                // Validation du place_id pour invité
                if (!this.validateGuestPlaceId()) {
                    this.formSubmitted = false;
                    return false;
                }

                // Validation des champs invité
                if (!this.validateGuestFormFields(event.target)) {
                    this.formSubmitted = false;
                    return false;
                }

                // Debug: afficher les données du formulaire
                const formData = new FormData(event.target);
                console.log('📋 Données du formulaire invité:');
                for (let [key, value] of formData.entries()) {
                    console.log(`  ${key}: ${value}`);
                }

                // Marquer comme soumis
                this.formSubmitted = true;

                // Envoyer la requête AJAX
                const response = await fetch(event.target.action, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                console.log('📡 Réponse du serveur (invité):', result);

                if (result.success) {
                    console.log('✅ Réservation invité créée avec succès, redirection vers:', result.redirect_url);

                    // Fermer la modal si elle existe
                    if (this.reservationModal) {
                        const modalInstance = bootstrap.Modal.getInstance(this.reservationModal);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    }

                    // Rediriger vers la page de paiement
                    window.location.href = result.redirect_url;
                    return true;
                } else {
                    // Afficher l'erreur
                    console.error('❌ Erreur:', result.error);
                    alert('Erreur: ' + (result.error || 'Une erreur est survenue lors de la création de la réservation.'));
                    this.formSubmitted = false;
                    return false;
                }

            } catch (error) {
                console.error('❌ Erreur lors de la soumission invité:', error);
                alert('Une erreur est survenue lors de la soumission. Veuillez réessayer.');
                this.formSubmitted = false;
                return false;
            } finally {
                this.setButtonLoading(submitButton, false);
            }
        }

        validateFormFields(form) {
            const dateDebut = form.querySelector('#date_debut');
            const dureeHeures = form.querySelector('#duree_heures');
            const dureeMinutes = form.querySelector('#duree_minutes');
            const dureeHidden = form.querySelector('#duree');
            
            if (!dateDebut || !dateDebut.value) {
                alert('Veuillez sélectionner une date et heure de début');
                return false;
            }
            
            const heures = parseInt(dureeHeures?.value || 0);
            const minutes = parseInt(dureeMinutes?.value || 0);
            const totalMinutes = (heures * 60) + minutes;
            
            if (totalMinutes < 15) {
                alert('La durée minimum est de 15 minutes');
                return false;
            }
            
            // Vérifier que le champ de durée cachée est bien défini
            if (!dureeHidden || !dureeHidden.value) {
                console.warn('⚠️ Champ durée manquant, calcul automatique...');
                const durationInHours = totalMinutes / 60;
                if (dureeHidden) {
                    dureeHidden.value = durationInHours.toFixed(2);
                } else {
                    // Créer le champ s'il n'existe pas
                    const hiddenDuree = document.createElement('input');
                    hiddenDuree.type = 'hidden';
                    hiddenDuree.name = 'duree';
                    hiddenDuree.id = 'duree';
                    hiddenDuree.value = durationInHours.toFixed(2);
                    form.appendChild(hiddenDuree);
                }
            }
            
            console.log('✅ Validation des champs réussie:', {
                dateDebut: dateDebut.value,
                totalMinutes: totalMinutes,
                duree: form.querySelector('#duree')?.value
            });
            
            return true;
        }

        validateGuestPlaceId() {
            this.ensurePlaceIdSet();

            const guestPlaceIdInput = document.getElementById('guest_place_id');

            if (!guestPlaceIdInput || !guestPlaceIdInput.value) {
                alert("Erreur: Place non sélectionnée. Veuillez fermer cette fenêtre et cliquer à nouveau sur 'Réserver' pour une place.");
                return false;
            }

            return true;
        }

        validateGuestFormFields(form) {
            const guestName = form.querySelector('#guest_name');
            const guestEmail = form.querySelector('#guest_email');
            const dateDebut = form.querySelector('#guest_date_debut');
            const dureeHeures = form.querySelector('#guest_duree_heures');
            const dureeMinutes = form.querySelector('#guest_duree_minutes');
            const dureeHidden = form.querySelector('#guest_duree');

            // Validation des champs obligatoires
            if (!guestName || !guestName.value.trim()) {
                alert('Veuillez saisir votre nom complet');
                return false;
            }

            if (!guestEmail || !guestEmail.value.trim()) {
                alert('Veuillez saisir votre adresse email');
                return false;
            }

            // Validation de l'email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(guestEmail.value.trim())) {
                alert('Veuillez saisir une adresse email valide');
                return false;
            }

            if (!dateDebut || !dateDebut.value) {
                alert('Veuillez sélectionner une date et heure de début');
                return false;
            }

            const heures = parseInt(dureeHeures?.value || 0);
            const minutes = parseInt(dureeMinutes?.value || 0);
            const totalMinutes = (heures * 60) + minutes;

            if (totalMinutes < 15) {
                alert('La durée minimum est de 15 minutes');
                return false;
            }

            // Vérifier que le champ de durée cachée est bien défini
            if (!dureeHidden || !dureeHidden.value) {
                console.warn('⚠️ Champ durée invité manquant, calcul automatique...');
                const durationInHours = totalMinutes / 60;
                if (dureeHidden) {
                    dureeHidden.value = durationInHours.toFixed(2);
                } else {
                    // Créer le champ s'il n'existe pas
                    const hiddenDuree = document.createElement('input');
                    hiddenDuree.type = 'hidden';
                    hiddenDuree.name = 'duree';
                    hiddenDuree.id = 'guest_duree';
                    hiddenDuree.value = durationInHours.toFixed(2);
                    form.appendChild(hiddenDuree);
                }
            }

            console.log('✅ Validation des champs invité réussie:', {
                guestName: guestName.value,
                guestEmail: guestEmail.value,
                dateDebut: dateDebut.value,
                totalMinutes: totalMinutes,
                duree: form.querySelector('#guest_duree')?.value
            });

            return true;
        }

        setButtonLoading(button, loading) {
            if (!button) return;
            
            if (loading) {
                button.disabled = true;
                button.dataset.originalText = button.innerHTML;
                button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Traitement...';
            } else {
                setTimeout(() => {
                    button.disabled = false;
                    if (button.dataset.originalText) {
                        button.innerHTML = button.dataset.originalText;
                        delete button.dataset.originalText;
                    }
                }, 100);
            }
        }

        // ===========================================
        // CALCUL DE PRIX
        // ===========================================

        setupPriceCalculation(form) {
            const heuresInput = form.querySelector('#duree_heures');
            const minutesSelect = form.querySelector('#duree_minutes');
            
            if (heuresInput) {
                heuresInput.addEventListener('input', () => this.updatePriceCalculation());
                heuresInput.addEventListener('change', () => this.updatePriceCalculation());
            }
            
            if (minutesSelect) {
                minutesSelect.addEventListener('change', () => this.updatePriceCalculation());
            }
        }

        setupGuestPriceCalculation(form) {
            const heuresInput = form.querySelector('#guest_duree_heures');
            const minutesSelect = form.querySelector('#guest_duree_minutes');

            if (heuresInput) {
                heuresInput.addEventListener('input', () => this.updateGuestPriceCalculation());
                heuresInput.addEventListener('change', () => this.updateGuestPriceCalculation());
            }

            if (minutesSelect) {
                minutesSelect.addEventListener('change', () => this.updateGuestPriceCalculation());
            }
        }

        updateGuestPriceCalculation() {
            const heuresInput = document.getElementById('guest_duree_heures');
            const minutesSelect = document.getElementById('guest_duree_minutes');
            const totalElement = document.getElementById('guest-montant-total');
            const dureeHidden = document.getElementById('guest_duree');

            if (!heuresInput || !minutesSelect || !totalElement) return;

            const heures = parseInt(heuresInput.value || 0);
            const minutes = parseInt(minutesSelect.value || 0);
            const totalMinutes = (heures * 60) + minutes;
            const durationInHours = totalMinutes / 60;

            // Mettre à jour le champ caché de durée
            if (!dureeHidden) {
                // Créer le champ s'il n'existe pas
                const guestForm = document.getElementById('guest-reservation-form');
                if (guestForm) {
                    const hiddenDuree = document.createElement('input');
                    hiddenDuree.type = 'hidden';
                    hiddenDuree.name = 'duree';
                    hiddenDuree.id = 'guest_duree';
                    hiddenDuree.value = durationInHours.toFixed(2);
                    guestForm.appendChild(hiddenDuree);
                }
            } else {
                dureeHidden.value = durationInHours.toFixed(2);
            }

            // Récupérer le tarif
            const tarifInfo = document.getElementById('guest-tarif-info');
            let tarif = 2.0;
            if (tarifInfo && tarifInfo.dataset.tarif) {
                tarif = parseFloat(tarifInfo.dataset.tarif);
            }

            // Calculer le prix (les invités n'ont pas d'avantages d'abonnement)
            const finalTotal = (totalMinutes / 60) * tarif;
            totalElement.textContent = `${finalTotal.toFixed(2)} €`;
        }

        updatePriceCalculation() {
            const heuresInput = document.getElementById('duree_heures');
            const minutesSelect = document.getElementById('duree_minutes');
            const totalElement = document.getElementById('montant-total');
            const dureeHidden = document.getElementById('duree');
            
            if (!heuresInput || !minutesSelect || !totalElement) return;
            
            const heures = parseInt(heuresInput.value || 0);
            const minutes = parseInt(minutesSelect.value || 0);
            const totalMinutes = (heures * 60) + minutes;
            const durationInHours = totalMinutes / 60;
            
            // Mettre à jour le champ caché de durée
            if (dureeHidden) {
                dureeHidden.value = durationInHours.toFixed(2);
            }
            
            // Récupérer le tarif
            const tarifInfo = document.getElementById('tarif-info');
            let tarif = 2.0;
            if (tarifInfo && tarifInfo.dataset.tarif) {
                tarif = parseFloat(tarifInfo.dataset.tarif);
            }
            
            // Calculer le prix avec les avantages d'abonnement
            const finalTotal = this.calculatePriceWithBenefits(totalMinutes, tarif);
            totalElement.textContent = `${finalTotal.toFixed(2)} €`;
            
            this.updateSubscriptionBenefitsDisplay(totalMinutes, durationInHours * tarif, finalTotal);
        }

        calculatePriceWithBenefits(totalMinutes, hourlyRate) {
            if (!this.subscriptionBenefits) {
                return (totalMinutes / 60) * hourlyRate;
            }
            
            const { freeMinutes, discountPercent } = this.subscriptionBenefits;
            let billableMinutes = totalMinutes;
            
            // Appliquer les minutes gratuites
            if (freeMinutes > 0) {
                billableMinutes = Math.max(0, totalMinutes - freeMinutes);            }
            
            let amount = (billableMinutes / 60) * hourlyRate;
            
            // Appliquer la réduction
            if (discountPercent > 0) {
                amount = amount * (1 - discountPercent / 100);
            }
            
            return Math.max(0, amount);
        }

        // ===========================================
        // GESTION DES BOUTONS
        // ===========================================

        setupButtonHandlers() {
            document.addEventListener('click', (event) => {
                const button = event.target.closest('[data-bs-toggle="modal"][data-place-id]');
                if (button) {
                    const placeId = button.getAttribute('data-place-id');
                    console.log('👆 Bouton cliqué - Place ID:', placeId);
                    
                    // Stocker immédiatement
                    window._lastSelectedPlaceId = placeId;
                    
                    // Essayer de définir le champ
                    setTimeout(() => {
                        this.setPlaceId(placeId);
                    }, 100);
                }
            });
            
            // Gestionnaire pour les boutons de réservation immédiate
            document.addEventListener('click', (event) => {
                const immediateButton = event.target.closest('.btn-reserve-immediate');
                if (immediateButton) {
                    console.log('👆 Bouton réservation immédiate cliqué');
                    // Ne pas intercepter - laisser le formulaire se soumettre normalement
                    // Le contrôleur gérera la redirection automatiquement
                }
            });
            
            // Gestionnaire pour les formulaires de réservation immédiate
            document.addEventListener('submit', (event) => {
                const form = event.target;
                if (form.action && form.action.includes('reserveImmediate')) {
                    console.log('📤 Soumission formulaire réservation immédiate');
                    
                    // Ajouter un header pour indiquer que c'est AJAX si nécessaire
                    // Mais pour l'instant, on laisse passer en mode normal
                    // pour que le contrôleur fasse la redirection directe
                }
            });
            
            // Gestionnaires de boutons configurés
        }

        // ===========================================
        // GESTION DES ALERTES - DÉSACTIVÉE
        // ===========================================

        setupAlertHandlers() {
            // Fonctionnalité d'alertes désactivée
            console.log('🚫 Gestion des alertes désactivée');
        }

        getReservationFormData() {
            const form = document.getElementById('reservation-form');
            if (!form) return null;
            
            const placeId = form.querySelector('#place_id')?.value;
            const dateDebut = form.querySelector('#date_debut')?.value;
            const duree = form.querySelector('#duree')?.value;
            
            if (!placeId || !dateDebut || !duree) return null;
            
            return { placeId, dateDebut, duree };
        }

        // ===========================================
        // SUIVI DES RÉSERVATIONS
        // ===========================================

        setupReservationTracking() {
            // Démarrer le suivi si des éléments trackés sont présents
            this.updateTrackedElements();
            
            // Mettre à jour périodiquement
            if (this.trackerInterval) clearInterval(this.trackerInterval);
            this.trackerInterval = setInterval(() => {
                this.updateTrackedElements();
            }, 1000);
        }

        updateTrackedElements() {
            const trackedElements = document.querySelectorAll('[data-start-time]');
            
            trackedElements.forEach(element => {
                const startTime = new Date(element.dataset.startTime);
                const tarif = parseFloat(element.dataset.tarif || 2.0);
                
                this.updateElementTimer(element, startTime, tarif);
            });
        }        updateElementTimer(element, startTime, tarif) {
            const now = new Date();
            const elapsedMs = now - startTime;
            
            if (elapsedMs < 0) return; // La réservation n'a pas encore commencé
            
            const elapsedMinutes = Math.floor(elapsedMs / 60000);
            const elapsedHours = Math.floor(elapsedMinutes / 60);
            const remainingMinutes = elapsedMinutes % 60;
            const elapsedSeconds = Math.floor((elapsedMs % 60000) / 1000);
            
            const cost = (elapsedMinutes / 60) * tarif;
            
            // Format d'affichage : HH:MM:SS
            const timeString = String(elapsedHours).padStart(2, '0') + ':' + 
                             String(remainingMinutes).padStart(2, '0') + ':' + 
                             String(elapsedSeconds).padStart(2, '0');
            
            // Mettre à jour les éléments d'affichage
            const durationTargets = element.querySelectorAll('[data-target="duration"]');
            const costTargets = element.querySelectorAll('[data-target="cost"]');
            
            durationTargets.forEach(target => {
                target.textContent = timeString;
            });
            
            costTargets.forEach(target => {
                target.textContent = `${cost.toFixed(2)} €`;
            });
        }

        // ===========================================
        // AVANTAGES D'ABONNEMENT
        // ===========================================

        loadSubscriptionBenefits() {
            if (typeof loadSubscriptionBenefits === 'function') {
                try {
                    this.subscriptionBenefits = loadSubscriptionBenefits();
                    // Avantages d'abonnement chargés
                } catch (error) {
                    console.warn('⚠️ Erreur lors du chargement des avantages:', error);
                }
            }
        }

        updateSubscriptionBenefitsDisplay(totalMinutes, originalAmount, finalAmount) {
            const benefitsContainer = document.getElementById('subscription-benefits-details');
            if (!benefitsContainer || !this.subscriptionBenefits) return;
            
            const { freeMinutes, discountPercent, subscriptionName } = this.subscriptionBenefits;
            let content = `<h6><i class="fas fa-star me-2"></i>Avantages "${subscriptionName}"</h6>`;
            let hasAdvantages = false;
            
            // Minutes gratuites
            if (freeMinutes > 0 && totalMinutes > freeMinutes) {
                content += `<div class="small mb-1">
                    <i class="fas fa-clock text-success me-1"></i>
                    ${freeMinutes} minutes gratuites appliquées
                </div>`;
                hasAdvantages = true;
            }
            
            // Réduction
            if (discountPercent > 0 && originalAmount !== finalAmount) {
                const savings = originalAmount - finalAmount;
                content += `<div class="small mb-1">
                    <i class="fas fa-percentage text-success me-1"></i>
                    Réduction de ${discountPercent}%: -${savings.toFixed(2)} €
                </div>`;
                hasAdvantages = true;
            }
            
            if (hasAdvantages) {
                benefitsContainer.innerHTML = content;
                benefitsContainer.style.display = 'block';
            } else {
                benefitsContainer.style.display = 'none';
            }
        }

        // ===========================================
        // FONCTIONS GLOBALES
        // ===========================================

        exposeGlobalFunctions() {
            // Fonction de validation pour compatibilité
            window.validateReservationForm = () => {
                return this.validatePlaceId();
            };
            
            // Autres fonctions utiles
            window.updateReservationPrice = () => {
                this.updatePriceCalculation();
            };
            
            // Fonctions globales exposées
        }

        // ===========================================
        // NETTOYAGE
        // ===========================================

        destroy() {
            if (this.trackerInterval) {
                clearInterval(this.trackerInterval);
            }
        }

        /**
         * Réinitialise les gestionnaires d'événements après un chargement AJAX
         */
        reinitialize() {
            console.log('🔄 UnifiedReservationManager: Réinitialisation après AJAX...');
            
            // Réinitialiser les gestionnaires de boutons (délégation d'événements - pas besoin de refaire)
            // Les événements délégués fonctionnent automatiquement avec le nouveau contenu
            
            // Recharger les avantages d'abonnement si nécessaire
            this.loadSubscriptionBenefits();
            
            // Réactiver les gestionnaires spécifiques qui pourraient avoir besoin de réinitialisation
            this.setupFormHandlers();
            this.setupGuestFormHandlers();

            console.log('✅ UnifiedReservationManager: Réinitialisé');
        }
    }    // Initialiser le gestionnaire unifié
    window.UnifiedReservationManager = UnifiedReservationManager;
    window.reservationManager = new UnifiedReservationManager();
    
    // Exposer aussi via window.app si disponible
    if (window.app) {
        window.app.reservationManager = window.reservationManager;
    } else {
        // Si window.app n'est pas encore disponible, l'attacher dès qu'il le sera
        Object.defineProperty(window, 'app', {
            get: function() { return this._app; },
            set: function(value) { 
                this._app = value; 
                if (value && window.reservationManager) {
                    value.reservationManager = window.reservationManager;
                }
            }
        });
    }
    
    // Nettoyage à la fermeture de la page
    window.addEventListener('beforeunload', () => {
        if (window.reservationManager) {
            window.reservationManager.destroy();
        }
    });

})();
