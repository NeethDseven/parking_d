/**
 * ADMIN MANAGER UNIFIÉ
 * ===================
 * 
 * Consolide tous les composants d'administration :
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
        // GESTION DES FORMULAIRES
        // ===========================================



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
            // Configuration des tableaux admin avec tri simple
            this.setupAdminTableSorting();

            // Configuration des modales
            this.setupModalsFix();

            // DataTables pour les tableaux spéciaux (si disponible)
            if (typeof DataTable !== 'undefined') {
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
        }

        /**
         * Configuration du tri pour les tableaux admin
         */
        setupAdminTableSorting() {
            const adminTables = document.querySelectorAll('.admin-table');

            adminTables.forEach(table => {
                if (table.dataset.sortingInitialized) return;

                const headers = table.querySelectorAll('thead th');

                headers.forEach((header, index) => {
                    // Ignorer la colonne Actions
                    if (header.textContent.toLowerCase().includes('action')) return;

                    // Ajouter les styles et icônes de tri
                    header.style.cursor = 'pointer';
                    header.style.userSelect = 'none';
                    header.style.position = 'relative';

                    // Ajouter l'icône de tri
                    const sortIcon = document.createElement('i');
                    sortIcon.className = 'fas fa-sort ms-2';
                    sortIcon.style.opacity = '0.5';
                    header.appendChild(sortIcon);

                    // Ajouter l'événement de clic
                    header.addEventListener('click', () => {
                        this.sortTable(table, index, header);
                    });
                });

                table.dataset.sortingInitialized = 'true';
            });
        }

        /**
         * Fonction de tri des tableaux
         */
        sortTable(table, columnIndex, header) {
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));

            // Déterminer la direction du tri
            const currentSort = header.dataset.sort || 'none';
            const newSort = currentSort === 'asc' ? 'desc' : 'asc';

            // Réinitialiser tous les headers
            table.querySelectorAll('thead th').forEach(th => {
                const icon = th.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-sort ms-2';
                    icon.style.opacity = '0.5';
                }
                th.dataset.sort = 'none';
            });

            // Mettre à jour le header actuel
            header.dataset.sort = newSort;
            const icon = header.querySelector('i');
            if (icon) {
                icon.className = `fas fa-sort-${newSort === 'asc' ? 'up' : 'down'} ms-2`;
                icon.style.opacity = '1';
            }

            // Trier les lignes
            rows.sort((a, b) => {
                const aCell = a.cells[columnIndex];
                const bCell = b.cells[columnIndex];

                if (!aCell || !bCell) return 0;

                let aValue = this.getCellValue(aCell);
                let bValue = this.getCellValue(bCell);

                // Tri numérique si possible
                const aNum = parseFloat(aValue);
                const bNum = parseFloat(bValue);

                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return newSort === 'asc' ? aNum - bNum : bNum - aNum;
                }

                // Tri alphabétique
                aValue = aValue.toLowerCase();
                bValue = bValue.toLowerCase();

                if (newSort === 'asc') {
                    return aValue.localeCompare(bValue, 'fr');
                } else {
                    return bValue.localeCompare(aValue, 'fr');
                }
            });

            // Réorganiser les lignes dans le DOM
            rows.forEach(row => tbody.appendChild(row));

            // Animation subtile
            tbody.style.opacity = '0.7';
            setTimeout(() => {
                tbody.style.opacity = '1';
            }, 150);
        }

        /**
         * Extraire la valeur de tri d'une cellule
         */
        getCellValue(cell) {
            // Chercher d'abord un attribut data-sort
            if (cell.dataset.sort) {
                return cell.dataset.sort;
            }

            // Extraire le texte en ignorant les badges et icônes
            let text = cell.textContent || cell.innerText || '';

            // Nettoyer le texte
            text = text.trim();

            // Gérer les prix (enlever € et espaces)
            if (text.includes('€')) {
                text = text.replace(/[€\s]/g, '').replace(',', '.');
            }

            // Gérer les dates (format dd/mm/yyyy)
            if (text.match(/^\d{2}\/\d{2}\/\d{4}/)) {
                const parts = text.split('/');
                if (parts.length >= 3) {
                    return `${parts[2]}-${parts[1]}-${parts[0]}`;
                }
            }

            return text;
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

        destroy() {
            // Nettoyage des gestionnaires d'événements si nécessaire
            console.log('🧹 UnifiedAdminManager: Nettoyage terminé');
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
         * Gestion de la sidebar admin sur mobile - DÉSACTIVÉ
         * Géré par adminResponsiveManager.js
         */
        setupAdminSidebar() {
            // Désactivé pour éviter les conflits avec adminResponsiveManager.js
            console.log('📝 Sidebar gérée par adminResponsiveManager.js');
        }

        /**
         * Configuration des modales - désactivée temporairement
         */
        setupModalsFix() {
            // Désactivé pour éviter les conflits avec le script simplifié
            console.log('🚫 setupModalsFix désactivé - utilisation du script simplifié');
            return;

            // Écouter la fermeture des modales
            document.addEventListener('hidden.bs.modal', (event) => {
                // Nettoyer les z-index personnalisés
                const modal = event.target;
                modal.style.zIndex = '';

                // Nettoyer les backdrops orphelins
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => {
                    if (!document.querySelector('.modal.show')) {
                        backdrop.remove();
                    }
                });
            });

            // Correction spécifique pour les modales existantes
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                // Ajouter des attributs pour Bootstrap
                if (!modal.hasAttribute('tabindex')) {
                    modal.setAttribute('tabindex', '-1');
                }

                // S'assurer que la modale a les bonnes classes
                if (!modal.classList.contains('fade')) {
                    modal.classList.add('fade');
                }

                // Forcer le positionnement
                modal.style.position = 'fixed';
                modal.style.top = '0';
                modal.style.left = '0';
                modal.style.width = '100%';
                modal.style.height = '100%';
            });

            // Correction pour les boutons qui ouvrent les modales
            const modalTriggers = document.querySelectorAll('[data-bs-toggle="modal"]');
            modalTriggers.forEach(trigger => {
                trigger.addEventListener('click', (e) => {
                    // Petit délai pour s'assurer que Bootstrap a initialisé la modale
                    setTimeout(() => {
                        const targetModal = document.querySelector(trigger.getAttribute('data-bs-target'));
                        if (targetModal) {
                            targetModal.style.zIndex = '1055';
                            targetModal.style.display = 'block';
                        }
                    }, 50);
                });
            });
        }

        /**
         * Configure les gestionnaires spécifiques selon la page admin active
         */
        setupPageSpecificHandlers() {
            const currentPage = document.querySelector('meta[name="current-page"]')?.content;

            switch (currentPage) {
                case 'admin_dashboard':
                    // Dashboard configuré
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
