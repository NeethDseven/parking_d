/**
 * ADMIN MANAGER UNIFIÉ
 * ===================
 * 
 * Consolide tous les composants d'administration :
 * - adminCharts.js
 * - adminDashboard.js
 * - adminPlaces.js
 * - adminReservations.js
 * - adminTarifs.js
 * - adminUserAdd.js
 * - adminUserEdit.js
 * - userManagement.js
 * - adminTarifManager.js
 * - adminPlaceAdd.js
 * - adminReservationView.js
 */

(function () {
    'use strict';

    if (window.UnifiedAdminManager) {
        console.log('⚠️ UnifiedAdminManager déjà chargé');
        return;
    }

    console.log('🚀 UnifiedAdminManager: Initialisation...');

    class UnifiedAdminManager {
        constructor() {
            this.charts = {};
            this.currentPage = this.detectCurrentPage();
            this.init();
        }

        init() {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.setup());
            } else {
                this.setup();
            }
        } setup() {
            this.setupCharts();
            this.setupForms();
            this.setupDataTables();
            this.setupModalHandlers();
            this.setupFormValidation(); // Correction du nom de méthode
            this.setupPageSpecificHandlers();
            this.setupAdminSidebar();

            console.log('✅ UnifiedAdminManager: Configuration terminée pour', this.currentPage);
        }

        detectCurrentPage() {
            const path = window.location.pathname;
            if (path.includes('/admin/reservations')) return 'reservations';
            if (path.includes('/admin/places')) return 'places';
            if (path.includes('/admin/users')) return 'users';
            if (path.includes('/admin/tarifs')) return 'tarifs';
            if (path.includes('/admin/subscriptions')) return 'subscriptions';
            if (path.includes('/admin')) return 'dashboard';
            return 'unknown';
        }        // ===========================================
        // GESTION DES GRAPHIQUES
        // ===========================================

        setupCharts() {
            if (!window.Chart) {
                console.warn('⚠️ Chart.js non disponible');
                return;
            }

            // Détruire les anciens graphiques si ils existent
            this.destroyExistingCharts();

            // Graphique des types de places
            this.setupTypeChart();

            // Graphique des réservations
            this.setupReservationChart();

            // Graphique des revenus
            this.setupRevenueChart();

            // Graphique des abonnements
            this.setupSubscriptionChart();
        }

        destroyExistingCharts() {
            // Détruire les anciens graphiques pour éviter les conflits
            const canvasIds = ['typeChart', 'reservationStatusChart', 'revenueChart', 'subscriptionChart'];

            canvasIds.forEach(canvasId => {
                const canvas = document.getElementById(canvasId);
                if (canvas) {
                    // Récupérer l'instance Chart.js si elle existe
                    const chartInstance = Chart.getChart(canvas);
                    if (chartInstance) {
                        chartInstance.destroy();
                    }
                }
            });
        }

        setupTypeChart() {
            const canvas = document.getElementById('typeChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            // Récupérer les données depuis les attributs data-*
            const data = this.getTypeChartData();

            this.charts.typeChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Standard', 'Handicapé', 'Électrique', 'Moto/Scooter', 'Vélo'],
                    datasets: [{
                        data: data,
                        backgroundColor: [
                            '#007bff', '#28a745', '#ffc107', '#fd7e14', '#6f42c1'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            console.log('✅ Graphique des types de places configuré');
        }

        getTypeChartData() {
            const data = [0, 0, 0, 0, 0];

            // Méthode 1: Depuis les spans cachés
            const counts = [
                'count-standard', 'count-handicape', 'count-electrique',
                'count-moto', 'count-velo'
            ];

            counts.forEach((id, index) => {
                const element = document.getElementById(id);
                if (element) {
                    data[index] = parseInt(element.textContent) || 0;
                }
            });

            // Méthode 2: Depuis les attributs data-*
            const canvas = document.getElementById('typeChart');
            if (canvas) {
                ['standard', 'handicape', 'electrique', 'moto', 'velo'].forEach((type, index) => {
                    const value = canvas.dataset[type];
                    if (value !== undefined) {
                        data[index] = parseInt(value) || 0;
                    }
                });
            }

            return data;
        }

        setupReservationChart() {
            const canvas = document.getElementById('reservationChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            this.charts.reservationChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.getLast7Days(),
                    datasets: [{
                        label: 'Réservations',
                        data: this.getReservationData(),
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            console.log('✅ Graphique des réservations configuré');
        }

        setupRevenueChart() {
            const canvas = document.getElementById('revenueChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            this.charts.revenueChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: this.getLast12Months(),
                    datasets: [{
                        label: 'Revenus (€)',
                        data: this.getRevenueData(),
                        backgroundColor: '#28a745'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            console.log('✅ Graphique des revenus configuré');
        }

        setupSubscriptionChart() {
            const canvas = document.getElementById('subscriptionChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            this.charts.subscriptionChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Basique', 'Premium', 'VIP'],
                    datasets: [{
                        data: this.getSubscriptionData(),
                        backgroundColor: ['#17a2b8', '#ffc107', '#dc3545']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            console.log('✅ Graphique des abonnements configuré');
        }

        // Méthodes utilitaires pour les données des graphiques
        getLast7Days() {
            const days = [];
            for (let i = 6; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                days.push(date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' }));
            }
            return days;
        }

        getLast12Months() {
            const months = [];
            const monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
            for (let i = 11; i >= 0; i--) {
                const date = new Date();
                date.setMonth(date.getMonth() - i);
                months.push(monthNames[date.getMonth()]);
            }
            return months;
        }

        getReservationData() {
            // Données factices - à remplacer par de vraies données depuis le serveur
            return [12, 19, 15, 8, 22, 18, 25];
        }

        getRevenueData() {
            // Données factices - à remplacer par de vraies données depuis le serveur
            return [1200, 1400, 1100, 1600, 1800, 1500, 2000, 1900, 2200, 2400, 2100, 2600];
        }

        getSubscriptionData() {
            // Récupérer depuis les attributs data-* ou spans
            const canvas = document.getElementById('subscriptionChart');
            if (canvas) {
                return [
                    parseInt(canvas.dataset.basique) || 0,
                    parseInt(canvas.dataset.premium) || 0,
                    parseInt(canvas.dataset.vip) || 0
                ];
            }
            return [45, 25, 15]; // Valeurs par défaut
        }

        // ===========================================
        // GESTION DES FORMULAIRES
        // ===========================================

        setupForms() {
            this.setupFormValidation();
            this.setupPasswordValidation();
            this.setupTarifForms();
            this.setupPlacesForms();
            this.setupUserForms();
        }

        setupFormValidation() {
            const forms = document.querySelectorAll('form');

            forms.forEach(form => {
                form.addEventListener('submit', (event) => {
                    if (!this.validateForm(form)) {
                        event.preventDefault();
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

            // Validation spécifique par type de formulaire
            if (form.querySelector('#password') && form.querySelector('#confirm_password')) {
                isValid = this.validatePasswords(form) && isValid;
            }

            if (form.querySelector('input[type="email"]')) {
                isValid = this.validateEmails(form) && isValid;
            }

            return isValid;
        }

        validatePasswords(form) {
            const password = form.querySelector('#password');
            const confirmPassword = form.querySelector('#confirm_password');

            if (!password || !confirmPassword) return true;

            if (password.value !== confirmPassword.value) {
                this.showFieldError(confirmPassword, 'Les mots de passe ne correspondent pas');
                return false;
            }

            if (password.value.length > 0 && password.value.length < 6) {
                this.showFieldError(password, 'Le mot de passe doit contenir au moins 6 caractères');
                return false;
            }

            this.clearFieldError(password);
            this.clearFieldError(confirmPassword);
            return true;
        }

        validateEmails(form) {
            const emailFields = form.querySelectorAll('input[type="email"]');
            let isValid = true;

            emailFields.forEach(field => {
                if (field.value && !this.isValidEmail(field.value)) {
                    this.showFieldError(field, 'Format d\'email invalide');
                    isValid = false;
                } else {
                    this.clearFieldError(field);
                }
            });

            return isValid;
        }

        isValidEmail(email) {
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

        setupPasswordValidation() {
            const passwordToggles = document.querySelectorAll('.toggle-password');

            passwordToggles.forEach(toggle => {
                toggle.addEventListener('click', () => {
                    const target = document.querySelector(toggle.dataset.target);
                    if (target) {
                        const type = target.getAttribute('type') === 'password' ? 'text' : 'password';
                        target.setAttribute('type', type);

                        const icon = toggle.querySelector('i');
                        if (icon) {
                            icon.classList.toggle('fa-eye');
                            icon.classList.toggle('fa-eye-slash');
                        }
                    }
                });
            });
        } setupTarifForms() {
            if (this.currentPage !== 'tarifs') return;

            // Attendre que les modals soient dans le DOM
            const initTarifHandlers = () => {
                // Configuration des gestionnaires pour les sélecteurs de type de place
                this.setupTypePlaceHandlers();

                // Configuration des gestionnaires de formulaires
                this.setupTarifFormSubmissions();
                // Boutons d'édition des tarifs
                const editButtons = document.querySelectorAll('.edit-tarif');
                editButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        const tarifData = {
                            id: button.dataset.id,
                            type: button.dataset.type,
                            hourlyPrice: button.dataset.prixHeure,
                            dailyPrice: button.dataset.prixJournee,
                            monthlyPrice: button.dataset.prixMois,
                            freeMinutes: button.dataset.freeMinutes || 0
                        };

                        this.fillEditTarifForm(tarifData);
                    });
                });

                // Boutons de suppression des tarifs
                const deleteButtons = document.querySelectorAll('.delete-tarif');
                deleteButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        const tarifId = button.dataset.id;
                        const tarifType = button.dataset.type;

                        if (confirm(`Êtes-vous sûr de vouloir supprimer le tarif "${tarifType}" ?`)) {
                            this.deleteTarif(tarifId);
                        }
                    });
                });
            };

            // Initialiser immédiatement si les éléments sont disponibles
            if (document.getElementById('add_type_place')) {
                initTarifHandlers();
            } else {
                // Sinon attendre que les modals soient ajoutés au DOM
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        mutation.addedNodes.forEach((node) => {
                            if (node.nodeType === 1 && (node.id === 'addTarifModal' || node.querySelector('#addTarifModal'))) {
                                initTarifHandlers();
                                observer.disconnect();
                            }
                        });
                    });
                });

                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });

                // Fallback: essayer après 2 secondes
                setTimeout(() => {
                    if (document.getElementById('add_type_place')) {
                        initTarifHandlers();
                        observer.disconnect();
                    }
                }, 2000);
            }
        } setupTypePlaceHandlers() {
            // Gestionnaire pour le formulaire d'ajout
            const addSelect = document.getElementById('add_type_place');
            if (addSelect && !addSelect.dataset.handlerAdded) {
                addSelect.addEventListener('change', () => this.toggleCustomType('add'));
                addSelect.dataset.handlerAdded = 'true';
            }

            // Gestionnaire pour le formulaire d'édition
            const editSelect = document.getElementById('edit_type_place');
            if (editSelect && !editSelect.dataset.handlerAdded) {
                editSelect.addEventListener('change', () => this.toggleCustomType('edit'));
                editSelect.dataset.handlerAdded = 'true';
            }

            // Gestionnaires pour les champs de saisie personnalisés
            const addCustomInput = document.getElementById('add_custom_type');
            if (addCustomInput && !addCustomInput.dataset.handlerAdded) {
                addCustomInput.addEventListener('input', function () {
                    const finalInput = document.getElementById('add_final_type');
                    if (finalInput) {
                        finalInput.value = this.value;
                    }
                });
                addCustomInput.dataset.handlerAdded = 'true';
            }

            const editCustomInput = document.getElementById('edit_custom_type');
            if (editCustomInput && !editCustomInput.dataset.handlerAdded) {
                editCustomInput.addEventListener('input', function () {
                    const finalInput = document.getElementById('edit_final_type');
                    if (finalInput) {
                        finalInput.value = this.value;
                    }
                });
                editCustomInput.dataset.handlerAdded = 'true';
            }
        } toggleCustomType(prefix) {
            const select = document.getElementById(prefix + '_type_place');
            const customContainer = document.getElementById(prefix + '_custom_type_container');
            const customInput = document.getElementById(prefix + '_custom_type');
            const finalInput = document.getElementById(prefix + '_final_type');

            if (!select || !customContainer || !customInput || !finalInput) return;

            if (select.value === 'autre') {
                customContainer.style.display = 'block';
                customInput.required = true;
                finalInput.value = '';
            } else {
                customContainer.style.display = 'none';
                customInput.required = false;
                customInput.value = '';
                finalInput.value = select.value;
            }
        } setupTarifFormSubmissions() {
            // Gérer la soumission du formulaire d'ajout
            const addForm = document.querySelector('#addTarifModal form');
            if (addForm && !addForm.dataset.handlerAdded) {
                addForm.addEventListener('submit', (e) => {
                    const select = document.getElementById('add_type_place');
                    const customInput = document.getElementById('add_custom_type');
                    const finalInput = document.getElementById('add_final_type');

                    if (select && finalInput) {
                        if (select.value === 'autre') {
                            // Pour un type personnalisé, utiliser la valeur du champ personnalisé
                            if (customInput && customInput.value.trim()) {
                                finalInput.value = customInput.value.trim();
                            } else {
                                e.preventDefault();
                                alert('Veuillez saisir un nom pour le nouveau type de place.');
                                return;
                            }
                        } else {
                            // Pour un type prédéfini, utiliser la valeur du select
                            finalInput.value = select.value;
                        }
                    }
                });
                addForm.dataset.handlerAdded = 'true';
            }

            // Gérer la soumission du formulaire d'édition
            const editForm = document.querySelector('#editTarifModal form');
            if (editForm && !editForm.dataset.handlerAdded) {
                editForm.addEventListener('submit', (e) => {
                    const select = document.getElementById('edit_type_place');
                    const customInput = document.getElementById('edit_custom_type');
                    const finalInput = document.getElementById('edit_final_type');

                    if (select && finalInput) {
                        if (select.value === 'autre') {
                            // Pour un type personnalisé, utiliser la valeur du champ personnalisé
                            if (customInput && customInput.value.trim()) {
                                finalInput.value = customInput.value.trim();
                            } else {
                                e.preventDefault();
                                alert('Veuillez saisir un nom pour le nouveau type de place.');
                                return;
                            }
                        } else {
                            // Pour un type prédéfini, utiliser la valeur du select
                            finalInput.value = select.value;
                        }
                    }
                });
                editForm.dataset.handlerAdded = 'true';
            }
        }

        fillEditTarifForm(data) {
            const form = document.querySelector('#editTarifModal form');
            if (!form) return;

            // Remplir les champs de base
            const tarifIdField = form.querySelector('#edit_tarif_id');
            const typeField = form.querySelector('#edit_type_place');
            const hourlyField = form.querySelector('#edit_prix_heure');
            const dailyField = form.querySelector('#edit_prix_jour');
            const monthlyField = form.querySelector('#edit_prix_mois');

            if (tarifIdField) tarifIdField.value = data.id;
            if (hourlyField) hourlyField.value = data.hourlyPrice;
            if (dailyField) dailyField.value = data.dailyPrice;
            if (monthlyField) monthlyField.value = data.monthlyPrice;

            // Gérer le type de place avec la logique personnalisée
            if (typeField) {
                // Vérifier si le type existe dans les options prédéfinies
                const option = Array.from(typeField.options).find(opt => opt.value === data.type);

                if (option) {
                    // Type prédéfini trouvé
                    typeField.value = data.type;
                    this.toggleCustomType('edit');
                } else {
                    // Type personnalisé
                    typeField.value = 'autre';
                    this.toggleCustomType('edit');

                    const customInput = document.getElementById('edit_custom_type');
                    const finalInput = document.getElementById('edit_final_type');

                    if (customInput) customInput.value = data.type;
                    if (finalInput) finalInput.value = data.type;
                }
            }
        }

        // ===========================================
        // GESTION DES PLACES
        // ===========================================

        setupPlacesForms() {
            if (this.currentPage !== 'places') return;

            // Création multiple de places
            const createMultipleCheckbox = document.getElementById('create_multiple');
            const multipleOptions = document.getElementById('multiple_options');

            if (createMultipleCheckbox && multipleOptions) {
                createMultipleCheckbox.addEventListener('change', function () {
                    multipleOptions.style.display = this.checked ? 'flex' : 'none';
                });
            }

            // Gestionnaire pour les boutons de suppression de places
            const deleteButtons = document.querySelectorAll('.delete-place-btn');
            deleteButtons.forEach(button => {
                if (!button.dataset.handlerAdded) {
                    button.addEventListener('click', () => {
                        const placeId = button.dataset.id;
                        const placeNumero = button.dataset.numero;

                        if (confirm(`Êtes-vous sûr de vouloir supprimer la place "${placeNumero}" ?`)) {
                            this.deletePlace(placeId);
                        }
                    });
                    button.dataset.handlerAdded = 'true';
                }
            });
        }

        deletePlace(placeId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `${document.querySelector('meta[name="base-url"]').content}admin/deletePlace/${placeId}`;

            document.body.appendChild(form);
            form.submit();
        }

        deleteTarif(tarifId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `${document.querySelector('meta[name="base-url"]').content}admin/deleteTarif/${tarifId}`;

            document.body.appendChild(form);
            form.submit();
        }

        // ===========================================
        // GESTION DES UTILISATEURS
        // ===========================================

        setupUserForms() {
            if (this.currentPage !== 'users') return;

            // Boutons d'édition des utilisateurs
            const editButtons = document.querySelectorAll('.edit-user');
            editButtons.forEach(button => {
                if (!button.dataset.handlerAdded) {
                    button.addEventListener('click', () => {
                        const userData = {
                            id: button.dataset.id,
                            nom: button.dataset.nom,
                            email: button.dataset.email,
                            telephone: button.dataset.telephone,
                            status: button.dataset.status
                        };

                        this.fillEditUserForm(userData);
                    });
                    button.dataset.handlerAdded = 'true';
                }
            });
            // Boutons de suppression des utilisateurs avec récupération d'informations
            const deleteButtons = document.querySelectorAll('.delete-user-btn');
            deleteButtons.forEach(button => {
                if (!button.dataset.handlerAdded) {
                    button.addEventListener('click', () => {
                        const userId = button.dataset.id;
                        const userName = button.dataset.name;

                        this.showDeleteUserModal(userId, userName);
                    });
                    button.dataset.handlerAdded = 'true';
                }
            });

            // Boutons de changement de statut
            const statusButtons = document.querySelectorAll('.change-status-btn');
            statusButtons.forEach(button => {
                if (!button.dataset.handlerAdded) {
                    button.addEventListener('click', () => {
                        const userId = button.dataset.id;
                        const userName = button.dataset.name;
                        const newStatus = button.dataset.status;

                        this.fillChangeStatusModal(userId, userName, newStatus);
                    });
                    button.dataset.handlerAdded = 'true';
                }
            });
        }

        fillEditUserForm(userData) {
            const editIdField = document.getElementById('edit_user_id');
            const editNomField = document.getElementById('edit_nom');
            const editEmailField = document.getElementById('edit_email');
            const editTelephoneField = document.getElementById('edit_telephone');

            if (editIdField) editIdField.value = userData.id;
            if (editNomField) editNomField.value = userData.nom;
            if (editEmailField) editEmailField.value = userData.email;
            if (editTelephoneField) editTelephoneField.value = userData.telephone || '';
        }

        deleteUser(userId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `${document.querySelector('meta[name="base-url"]').content}admin/deleteUser/${userId}`;

            document.body.appendChild(form);
            form.submit();
        }

        fillChangeStatusModal(userId, userName, newStatus) {
            const form = document.getElementById('changeStatusForm');
            const message = document.getElementById('statusConfirmMessage');
            const statusValue = document.getElementById('statusValue');

            if (form) form.action = `${document.querySelector('meta[name="base-url"]').content}admin/changeUserStatus/${userId}`;
            if (message) message.textContent = `Êtes-vous sûr de vouloir ${newStatus === 'actif' ? 'activer' : 'désactiver'} l'utilisateur "${userName}" ?`;
            if (statusValue) statusValue.value = newStatus;
        }

        /**
         * Affiche le modal de suppression d'utilisateur avec informations détaillées
         */
        async showDeleteUserModal(userId, userName) {
            const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));

            // Réinitialiser le modal
            this.resetDeleteUserModal();

            // Afficher les informations de base
            const userInfoElement = document.getElementById('deleteUserInfo');
            userInfoElement.innerHTML = `<strong>${userName}</strong> (ID: ${userId})`;

            // Afficher le modal avec un état de chargement
            modal.show();

            try {
                // Récupérer les informations détaillées de l'utilisateur
                const response = await fetch(
                    `${document.querySelector('meta[name="base-url"]').content}admin/getUserDeleteInfo/${userId}`,
                    {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    }
                );

                if (!response.ok) {
                    throw new Error('Erreur lors de la récupération des informations utilisateur');
                }

                const data = await response.json();

                if (data.success) {
                    this.populateDeleteUserModal(data, userId);
                } else {
                    this.showDeleteError(data.error || 'Erreur inconnue');
                }

            } catch (error) {
                console.error('Erreur lors de la récupération des informations:', error);
                this.showDeleteError('Impossible de récupérer les informations de l\'utilisateur');
            }
        }

        /**
         * Remplit le modal avec les informations détaillées de l'utilisateur
         */
        populateDeleteUserModal(data, userId) {
            const user = data.user;
            const reservations = data.reservations;

            // Mettre à jour les informations utilisateur
            const userInfoElement = document.getElementById('deleteUserInfo');
            userInfoElement.innerHTML = `
                <strong>${user.nom} ${user.prenom || ''}</strong><br>
                <small class="text-muted">Email: ${user.email} | ID: ${user.id}</small>
            `;

            // Afficher les informations sur les réservations
            if (reservations.total > 0) {
                const reservationInfoElement = document.getElementById('reservationInfo');
                const statusListElement = document.getElementById('reservationStatusList');
                const deleteInfoElement = document.getElementById('reservationDeleteInfo');

                reservationInfoElement.style.display = 'block';

                // Construire la liste des statuts
                let statusHtml = `<li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Total des réservations</strong>
                    <span class="badge bg-primary rounded-pill">${reservations.total}</span>
                </li>`;

                for (const [status, count] of Object.entries(reservations.status_counts)) {
                    if (count > 0) {
                        const badgeClass = this.getStatusBadgeClass(status);
                        const statusLabel = this.getStatusLabel(status);
                        statusHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                            ${statusLabel}
                            <span class="badge ${badgeClass} rounded-pill">${count}</span>
                        </li>`;
                    }
                }

                statusListElement.innerHTML = statusHtml;
                deleteInfoElement.textContent = `${reservations.total} réservation(s) et leurs données associées`;
            } else {
                document.getElementById('reservationInfo').style.display = 'none';
                document.getElementById('reservationDeleteInfo').textContent = 'Aucune réservation associée';
            }

            // Configurer le bouton de confirmation
            this.setupDeleteConfirmation(userId);
        }

        /**
         * Configure la confirmation de suppression
         */
        setupDeleteConfirmation(userId) {
            const confirmCheckbox = document.getElementById('confirmDeletion');
            const confirmBtn = document.getElementById('confirmDeleteBtn');

            // Gérer l'état du bouton selon la checkbox
            confirmCheckbox.addEventListener('change', () => {
                confirmBtn.disabled = !confirmCheckbox.checked;
            });

            // Configurer l'action du bouton
            confirmBtn.onclick = () => {
                if (confirmCheckbox.checked) {
                    this.forceDeleteUser(userId);
                }
            };
        }

        /**
         * Effectue la suppression forcée de l'utilisateur
         */
        forceDeleteUser(userId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `${document.querySelector('meta[name="base-url"]').content}admin/forceDeleteUser/${userId}`;

            document.body.appendChild(form);
            form.submit();
        }

        /**
         * Réinitialise le modal de suppression
         */
        resetDeleteUserModal() {
            document.getElementById('deleteUserInfo').innerHTML = '<strong>Chargement...</strong>';
            document.getElementById('reservationInfo').style.display = 'none';
            document.getElementById('reservationStatusList').innerHTML = '';
            document.getElementById('reservationDeleteInfo').textContent = 'Toutes les réservations associées';

            const confirmCheckbox = document.getElementById('confirmDeletion');
            const confirmBtn = document.getElementById('confirmDeleteBtn');

            confirmCheckbox.checked = false;
            confirmBtn.disabled = true;
        }

        /**
         * Affiche une erreur dans le modal
         */
        showDeleteError(message) {
            const userInfoElement = document.getElementById('deleteUserInfo');
            userInfoElement.innerHTML = `<span class="text-danger">Erreur: ${message}</span>`;
        }

        /**
         * Obtient la classe CSS pour un badge de statut
         */
        getStatusBadgeClass(status) {
            const classes = {
                'en_cours': 'bg-success',
                'confirmée': 'bg-info',
                'en_attente': 'bg-warning',
                'terminee': 'bg-secondary',
                'annulée': 'bg-danger',
                'expirée': 'bg-dark',
                'en_cours_immediat': 'bg-primary'
            };
            return classes[status] || 'bg-secondary';
        }

        /**
         * Obtient le libellé français pour un statut
         */
        getStatusLabel(status) {
            const labels = {
                'en_cours': 'En cours',
                'confirmée': 'Confirmées',
                'en_attente': 'En attente',
                'terminee': 'Terminées',
                'annulée': 'Annulées',
                'expirée': 'Expirées',
                'en_cours_immediat': 'En cours immédiat'
            };
            return labels[status] || status;
        }

        // ===========================================
        // GESTION DES DATATABLES
        // ===========================================

        setupDataTables() {
            if (typeof DataTable === 'undefined') return;

            const tables = document.querySelectorAll('.data-table');

            tables.forEach(table => {
                if (!table.dataset.initialized) {
                    new DataTable(table, {
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                        },
                        pageLength: 25,
                        responsive: true
                    });
                    table.dataset.initialized = 'true';
                }
            });
        }

        // ===========================================
        // GESTION DES MODALS
        // ===========================================

        setupModalHandlers() {
            // Boutons de confirmation
            const confirmButtons = document.querySelectorAll('[data-confirm]');
            confirmButtons.forEach(button => {
                button.addEventListener('click', (event) => {
                    const message = button.dataset.confirm || 'Êtes-vous sûr ?';
                    if (!confirm(message)) {
                        event.preventDefault();
                        return false;
                    }
                });
            });

            // Reset des modals
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.addEventListener('hidden.bs.modal', () => {
                    const form = modal.querySelector('form');
                    if (form) {
                        form.reset();
                        this.clearAllFieldErrors(form);
                    }
                });
            });
        }

        clearAllFieldErrors(form) {
            const invalidFields = form.querySelectorAll('.is-invalid');
            invalidFields.forEach(field => this.clearFieldError(field));
        }

        // ===========================================
        // MÉTHODES UTILITAIRES
        // ===========================================

        showAlert(message, type = 'info') {
            // Créer une alerte Bootstrap
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            // Ajouter au début du conteneur principal
            const container = document.querySelector('.container-fluid, .container');
            if (container) {
                container.insertBefore(alertDiv, container.firstChild);
            }

            // Supprimer automatiquement après 5 secondes
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        updateCharts() {
            Object.values(this.charts).forEach(chart => {
                if (chart && typeof chart.update === 'function') {
                    chart.update();
                }
            });
        }

        destroy() {
            // Détruire tous les graphiques
            Object.values(this.charts).forEach(chart => {
                if (chart && typeof chart.destroy === 'function') {
                    chart.destroy();
                }
            });

            this.charts = {};
        }

        /**
         * Configuration spécifique pour la page des abonnements admin
         */
        setupSubscriptionAdminHandlers() {
            const table = document.getElementById('subscribersTable');
            if (table) {
                console.log('🔧 Table des abonnés initialisée');
                // Fonctionnalités futures de tri/filtre peuvent être ajoutées ici
            }
        }

        /**
         * Gestion de la sidebar admin sur mobile
         */
        setupAdminSidebar() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', () => {
                    const sidebar = document.querySelector('.sidebar');
                    const content = document.querySelector('.content');

                    sidebar.classList.toggle('active');

                    // Ajuster le contenu en fonction de l'état de la sidebar
                    if (sidebar.classList.contains('active')) {
                        content.style.marginLeft = '0';
                    } else {
                        content.style.marginLeft = '250px';
                    }
                });
            }
        }

        /**
         * Configure les gestionnaires spécifiques selon la page admin active
         */
        setupPageSpecificHandlers() {
            const currentPage = document.querySelector('meta[name="current-page"]')?.content;

            switch (currentPage) {
                case 'admin_dashboard':
                    // Graphiques déjà configurés dans setupCharts()
                    break;

                case 'admin_users':
                    // Gestion des utilisateurs
                    break;

                case 'admin_subscriptions':
                    this.setupSubscriptionAdminHandlers();
                    break;

                case 'admin_places':
                    // Gestion des places
                    break;

                case 'admin_reservations':
                    // Gestion des réservations
                    break;
            }
        }
    }

    // Initialiser le gestionnaire unifié d'administration
    window.UnifiedAdminManager = UnifiedAdminManager;
    window.adminManager = new UnifiedAdminManager();

    // Nettoyage à la fermeture de la page
    window.addEventListener('beforeunload', () => {
        if (window.adminManager) {
            window.adminManager.destroy();
        }
    });

})();
