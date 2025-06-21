/**
 * SERVICE DATA CONSOLIDÉ
 * Fusionne : dateTimeService, validationService, reservationService, reservationAvailabilityService
 */

// Protection contre le double chargement
if (typeof window.CoreDataService !== 'undefined') {
    console.log('CoreDataService déjà défini');
} else {

    class CoreDataService {
        constructor() {
            this.baseUrl = document.querySelector('meta[name="base-url"]')?.content || '/';
            this.reservedSlots = [];

            this.init();
        }

        init() {
            // CoreDataService initialisé silencieusement
            app.registerService('data', this);

            // Initialiser l'intervalle de mise à jour des statuts si on est sur la page de profil
            if (document.getElementById('reservations') && document.querySelector('.reservations-table')) {
                this.statusUpdateInterval = setInterval(() => this.updateAllReservationStatuses(), 60000);
                this.updateAllReservationStatuses();
            }
        }

        // ===========================================
        // DATE & TIME (ex-dateTimeService)
        // ===========================================

        parseDateTime(dateTimeString) {
            if (!dateTimeString) return null;

            try {
                const [datePart, timePart] = dateTimeString.split(' ');
                const [day, month, year] = datePart.split('/').map(Number);
                const [hours, minutes] = timePart.split(':').map(Number);

                return new Date(year, month - 1, day, hours, minutes);
            } catch (e) {
                console.error('Erreur de parsing de date/heure:', e);
                return new Date();
            }
        }

        formatDate(date, includeTime = false) {
            if (!date) return '';

            const options = {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            };

            if (includeTime) {
                options.hour = '2-digit';
                options.minute = '2-digit';
            }

            return date.toLocaleDateString('fr-FR', options);
        }

        formatDateTimeForInput(date) {
            if (!date) return '';

            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');

            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }

        calculateDuration(start, end) {
            if (!start || !end) return { hours: 0, minutes: 0 };

            const diff = end - start;
            const totalMinutes = Math.floor(diff / (1000 * 60));

            return {
                hours: Math.floor(totalMinutes / 60),
                minutes: totalMinutes % 60
            };
        }

        calculatePrice(start, end, hourlyRate) {
            if (!start || !end || !hourlyRate) return 0;

            const diff = end - start;
            const hours = diff / (1000 * 60 * 60);

            // Arrondir au quart d'heure supérieur
            const roundedHours = Math.ceil(hours * 4) / 4;

            return roundedHours * hourlyRate;
        }

        // ===========================================
        // VALIDATION (ex-validationService)
        // ===========================================

        validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(String(email).toLowerCase());
        }

        validateName(name) {
            return name && name.trim().length >= 2;
        }

        validatePhone(phone) {
            const re = /^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/;
            return re.test(phone.trim());
        }

        validatePassword(password, options = {}) {
            const minLength = options.minLength || 8;
            const requireUppercase = options.requireUppercase !== false;
            const requireLowercase = options.requireLowercase !== false;
            const requireNumber = options.requireNumber !== false;
            const requireSpecial = options.requireSpecial !== false;

            let isValid = true;
            let message = '';

            if (password.length < minLength) {
                isValid = false;
                message = `Le mot de passe doit contenir au moins ${minLength} caractères.`;
            }

            if (requireUppercase && !/[A-Z]/.test(password)) {
                isValid = false;
                message = 'Le mot de passe doit contenir au moins une majuscule.';
            }

            if (requireLowercase && !/[a-z]/.test(password)) {
                isValid = false;
                message = 'Le mot de passe doit contenir au moins une minuscule.';
            }

            if (requireNumber && !/\d/.test(password)) {
                isValid = false;
                message = 'Le mot de passe doit contenir au moins un chiffre.';
            }

            if (requireSpecial && !/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                isValid = false;
                message = 'Le mot de passe doit contenir au moins un caractère spécial.';
            }

            return { isValid, message };
        }

        applyFieldValidator(field, validatorFn, errorMessage, options = {}) {
            const validateField = () => {
                const isValid = validatorFn(field.value);
                this.updateFieldValidationUI(field, isValid, errorMessage);
                return isValid;
            };

            // Validation en temps réel
            field.addEventListener('blur', validateField);
            if (options.realTime) {
                field.addEventListener('input', validateField);
            }

            return validateField;
        }

        updateFieldValidationUI(field, isValid, errorMessage) {
            field.classList.remove('is-valid', 'is-invalid');

            let feedbackElement = field.parentNode.querySelector('.invalid-feedback, .valid-feedback');
            if (feedbackElement) {
                feedbackElement.remove();
            }

            if (isValid) {
                field.classList.add('is-valid');
            } else {
                field.classList.add('is-invalid');

                feedbackElement = document.createElement('div');
                feedbackElement.className = 'invalid-feedback';
                feedbackElement.textContent = errorMessage;
                field.parentNode.appendChild(feedbackElement);
            }
        }

        validateForm(form) {
            let isValid = true;
            const inputs = form.querySelectorAll('input, select, textarea');

            inputs.forEach(input => {
                if (input.hasAttribute('required') && !input.value.trim()) {
                    this.updateFieldValidationUI(input, false, 'Ce champ est obligatoire.');
                    isValid = false;
                } else if (input.type === 'email' && input.value && !this.validateEmail(input.value)) {
                    this.updateFieldValidationUI(input, false, 'Veuillez entrer une adresse email valide.');
                    isValid = false;
                } else if (input.type === 'tel' && input.value && !this.validatePhone(input.value)) {
                    this.updateFieldValidationUI(input, false, 'Veuillez entrer un numéro de téléphone valide.');
                    isValid = false;
                } else {
                    this.updateFieldValidationUI(input, true, '');
                }
            });

            return isValid;
        }

        // ===========================================
        // RÉSERVATIONS (ex-reservationService)
        // ===========================================

        checkTimeSlotConflicts(start, end) {
            if (!this.reservedSlots.length) return false;

            return this.reservedSlots.some(slot => {
                const slotStart = new Date(slot.start);
                const slotEnd = new Date(slot.end);

                return (start < slotEnd && end > slotStart);
            });
        }

        updateAllReservationStatuses() {
            const reservationRows = document.querySelectorAll('.reservations-table tbody tr');
            const now = new Date();

            reservationRows.forEach(row => this.updateReservationStatus(row, now));
        }

        updateReservationStatus(row, now) {
            const reservationId = row.getAttribute('data-reservation-id');
            const status = row.getAttribute('data-status');
            const startDate = new Date(row.getAttribute('data-date-debut'));
            const endDate = row.getAttribute('data-date-fin') ? new Date(row.getAttribute('data-date-fin')) : null;

            const statusCell = row.querySelector('td.reservation-status');
            const actionsCell = row.querySelector('td.reservation-actions');

            if (!statusCell || !actionsCell) return;

            if (status === 'en_attente' && startDate <= now) {
                this.updateStatusDisplay(statusCell, 'en_cours');
                row.setAttribute('data-status', 'en_cours');
            } else if (status === 'en_cours' && endDate && endDate <= now) {
                this.updateStatusDisplay(statusCell, 'terminée');
                row.setAttribute('data-status', 'terminée');
            }
        }

        updateStatusDisplay(cell, newStatus) {
            const badge = cell.querySelector('.badge');
            if (!badge) return;

            // Supprimer toutes les classes de statut
            badge.classList.remove('bg-warning', 'bg-primary', 'bg-success', 'bg-secondary', 'bg-danger');

            // Appliquer le nouveau statut
            const statusConfig = {
                'en_attente': { class: 'bg-warning', text: 'En attente' },
                'en_cours': { class: 'bg-primary', text: 'En cours' },
                'confirmée': { class: 'bg-success', text: 'Confirmée' },
                'terminée': { class: 'bg-secondary', text: 'Terminée' },
                'annulée': { class: 'bg-danger', text: 'Annulée' }
            };

            const config = statusConfig[newStatus];
            if (config) {
                badge.classList.add(config.class);
                badge.textContent = config.text;
            }
        }

        calculateNewStatus(reservationData, now) {
            const startDate = new Date(reservationData.date_debut);
            const endDate = reservationData.date_fin ? new Date(reservationData.date_fin) : null;

            if (now < startDate) {
                return 'en_attente';
            } else if (now >= startDate && (!endDate || now < endDate)) {
                return 'en_cours';
            } else if (endDate && now >= endDate) {
                return 'terminée';
            }

            return reservationData.status;
        }

        // ===========================================
        // DISPONIBILITÉ (ex-reservationAvailabilityService)
        // ===========================================

        async checkAvailability(placeId, dateDebut, duree) {
            try {
                const url = `${this.baseUrl}api/checkAvailability`;
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        place_id: placeId,
                        date_debut: dateDebut,
                        duree: duree
                    })
                });

                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }

                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Erreur lors de la vérification de disponibilité:', error);
                throw error;
            }
        }

        // ===========================================
        // MÉTHODES UTILITAIRES
        // ===========================================

        formatCurrency(value) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR'
            }).format(value);
        }

        ucfirst(string) {
            if (!string) return '';
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        formatDateTimeInputValue(date) {
            if (!date) date = new Date();

            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');

            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }

        getFutureDate(hoursToAdd = 1) {
            const date = new Date();
            date.setHours(date.getHours() + hoursToAdd);
            date.setMinutes(0); // Arrondir à l'heure
            return date;
        }

        // Compatibilité avec les anciens noms de services
        $(selector, context = document) {
            return context.querySelector(selector);
        }

        $$(selector, context = document) {
            return context.querySelectorAll(selector);
        }
    }

    // Export pour le système de modules
    window.CoreDataService = CoreDataService;

    // Création de l'instance
    new CoreDataService();

}
