/**
 * SERVICE ADMIN CONSOLIDÉ
 * Fusionne : adminChartService, adminDataService, adminUIService
 */

// Protection contre le double chargement
if (typeof window.CoreAdminService !== 'undefined') {
    console.log('CoreAdminService déjà défini');
} else {

    class CoreAdminService {
        constructor() {
            // Configuration des graphiques
            this.colors = [
                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                '#5a5c69', '#2e59d9', '#17a673', '#2c9faf', '#f8f9fc'
            ];
            this.charts = new Map();

            // Données admin
            this.data = {};

            this.init();
        }

        init() {
            console.log('CoreAdminService initialisé');
            app.registerService('admin', this);

            // Auto-initialisation pour les pages admin
            if (this.isAdminPage()) {
                setTimeout(() => this.setupAdminFeatures(), 500);
            }
        }

        isAdminPage() {
            return window.location.pathname.includes('/admin/');
        }

        // ===========================================
        // GESTION DES GRAPHIQUES
        // ===========================================

        getChartColor(index) {
            return this.colors[index % this.colors.length];
        }

        async waitForChartJs() {
            return new Promise((resolve) => {
                if (typeof Chart !== 'undefined') {
                    resolve();
                    return;
                }

                const checkChart = () => {
                    if (typeof Chart !== 'undefined') {
                        resolve();
                    } else {
                        setTimeout(checkChart, 100);
                    }
                };
                checkChart();
            });
        }

        // ===========================================
        // INITIALISATION COMPLÈTE
        // ===========================================

        setupAdminFeatures() {
            this.setupTooltips();
            this.setupConfirmButtons();
            this.setupSidebarToggle();
            this.initializeChartsFromPageData();
        }

        setupTooltips() {
            // Initialiser les tooltips Bootstrap
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        setupConfirmButtons() {
            // Ajouter des confirmations pour les actions dangereuses
            document.querySelectorAll('[data-confirm]').forEach(button => {
                button.addEventListener('click', (e) => {
                    const message = button.dataset.confirm || 'Êtes-vous sûr ?';
                    if (!confirm(message)) {
                        e.preventDefault();
                    }
                });
            });
        }

        setupSidebarToggle() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', () => {
                    document.getElementById('accordionSidebar').classList.toggle('toggled');
                });
            }
        }

        async initializeChartsFromPageData() {
            // Détecter la page actuelle
            const currentPageMeta = document.querySelector('meta[name="current-page"]');
            const currentPage = currentPageMeta ? currentPageMeta.getAttribute('content') : '';

            console.log('Page détectée:', currentPage);

            // Dashboard spécifique
            if (currentPage.includes('dashboard')) {
                await this.initializeDashboardCharts();
                return;
            }
        }

        // ===========================================
        // GRAPHIQUES SPÉCIFIQUES AU DASHBOARD
        // ===========================================

        async initializeDashboardCharts() {
            await this.waitForChartJs();

            const chartDataElement = document.getElementById('chart-data');
            if (!chartDataElement) {
                console.warn('Élément chart-data non trouvé');
                return;
            } try {
                // Données depuis les attributs data
                const placeTypesData = JSON.parse(chartDataElement.dataset.placeTypes || '{}');
                const placeStatusData = JSON.parse(chartDataElement.dataset.placeStatus || '{}');
                const subscriptionData = JSON.parse(chartDataElement.dataset.subscriptionStats || '[]');
                const revenueData = JSON.parse(chartDataElement.dataset.revenueStats || '{}');

                console.log('=== DEBUG GRAPHIQUES DASHBOARD ===');
                console.log('Données placeTypes:', placeTypesData);
                console.log('Données placeStatus:', placeStatusData);
                console.log('Données subscriptions:', subscriptionData);
                console.log('Données revenue:', revenueData);
                console.log('Element chart-data datasets:', chartDataElement.dataset);

                // Vérifier si les canvas existent
                console.log('Canvas placesChart existe:', !!document.getElementById('placesChart'));
                console.log('Canvas subscriptionsChart existe:', !!document.getElementById('subscriptionsChart'));
                console.log('Canvas revenueChart existe:', !!document.getElementById('revenueChart'));
                console.log('Canvas placeStatusChart existe:', !!document.getElementById('placeStatusChart'));
                console.log('Canvas placeTypeChart existe:', !!document.getElementById('placeTypeChart'));
                console.log('==================================');

                // Créer tous les graphiques
                await this.createPlacesChart(placeTypesData);
                await this.createSubscriptionsChart(subscriptionData);
                await this.createRevenueChart(revenueData);
                await this.createPlaceStatusChart(placeStatusData);
                await this.createPlaceTypeChart(placeTypesData);

            } catch (error) {
                console.error('Erreur lors de l\'initialisation des graphiques du dashboard:', error);
            }
        }

        async createPlacesChart(data) {
            const canvas = document.getElementById('placesChart');
            if (!canvas || !data) return;

            const labels = [];
            const values = [];
            const backgroundColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];

            // Utiliser les données by_type si disponibles, sinon utiliser les données directes
            const typeInfo = data.by_type || data;

            for (const [type, count] of Object.entries(typeInfo)) {
                if (count > 0) {
                    labels.push(this.getTypeLabel(type));
                    values.push(count);
                }
            }

            if (labels.length === 0) {
                canvas.parentNode.innerHTML = '<div class="alert alert-info text-center">Aucune donnée de répartition disponible</div>';
                return;
            }

            new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: backgroundColors,
                        hoverBackgroundColor: backgroundColors,
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    cutout: '60%'
                },
            });
        }

        async createRevenueChart(data) {
            const canvas = document.getElementById('revenueChart');
            if (!canvas || !data) return;

            new Chart(canvas, {
                type: 'line',
                data: {
                    labels: ['Aujourd\'hui', '7 derniers jours', '30 derniers jours', 'Cette année', 'Total'],
                    datasets: [{
                        label: 'Revenus (€)',
                        data: [data.day || 0, data.week || 0, data.month || 0, data.year || 0, data.total || 0],
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 2,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return value + ' €';
                                }
                            }
                        }
                    }
                }
            });
        }

        async createPlaceStatusChart(data) {
            const canvas = document.getElementById('placeStatusChart');
            if (!canvas || !data) return;

            const statusLabels = [];
            const statusData = [];
            const statusColors = ['#28a745', '#dc3545', '#ffc107', '#6c757d'];

            // Utiliser les données by_status si disponibles, sinon utiliser les données directes
            const statusInfo = data.by_status || data;

            if (statusInfo.libre !== undefined && statusInfo.libre > 0) {
                statusLabels.push('Libres');
                statusData.push(statusInfo.libre);
            }

            if (statusInfo.occupe !== undefined && statusInfo.occupe > 0) {
                statusLabels.push('Occupées');
                statusData.push(statusInfo.occupe);
            }

            if (statusInfo.maintenance !== undefined && statusInfo.maintenance > 0) {
                statusLabels.push('Maintenance');
                statusData.push(statusInfo.maintenance);
            }

            // Si aucune donnée disponible, afficher un message
            if (statusLabels.length === 0) {
                canvas.parentNode.innerHTML = '<div class="alert alert-info text-center">Aucune donnée de statut disponible</div>';
                return;
            }

            new Chart(canvas, {
                type: 'pie',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: statusColors.slice(0, statusLabels.length),
                        hoverBackgroundColor: statusColors.slice(0, statusLabels.length),
                        borderWidth: 2,
                        borderColor: '#fff'
                    }],
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
        }

        async createPlaceTypeChart(data) {
            const canvas = document.getElementById('placeTypeChart');
            if (!canvas || !data) return;

            const typeLabels = [];
            const typeData = [];
            const typeColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'];

            // Utiliser les données by_type si disponibles, sinon utiliser les données directes
            const typeInfo = data.by_type || data;

            for (const [type, count] of Object.entries(typeInfo)) {
                if (count > 0) {
                    typeLabels.push(this.getTypeLabel(type));
                    typeData.push(count);
                }
            }

            // Si aucune donnée disponible, afficher un message
            if (typeLabels.length === 0) {
                canvas.parentNode.innerHTML = '<div class="alert alert-info text-center">Aucune donnée de type disponible</div>';
                return;
            }

            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: typeLabels,
                    datasets: [{
                        label: 'Nombre de places',
                        data: typeData,
                        backgroundColor: typeColors.slice(0, typeLabels.length),
                        borderColor: typeColors.slice(0, typeLabels.length),
                        borderWidth: 1
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        async createSubscriptionsChart(data) {
            const canvas = document.getElementById('subscriptionsChart');
            if (!canvas || !data || data.length === 0) {
                if (canvas) {
                    canvas.parentNode.innerHTML = '<div class="alert alert-info text-center">Aucune donnée d\'abonnement disponible</div>';
                }
                return;
            }

            const labels = [];
            const values = [];
            const backgroundColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'];

            data.forEach((subscription, index) => {
                if (subscription.count > 0) {
                    labels.push(subscription.name);
                    values.push(subscription.count);
                }
            });

            if (labels.length === 0) {
                canvas.parentNode.innerHTML = '<div class="alert alert-info text-center">Aucune donnée d\'abonnement disponible</div>';
                return;
            }

            new Chart(canvas, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: backgroundColors.slice(0, labels.length),
                        hoverBackgroundColor: backgroundColors.slice(0, labels.length),
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                },
            });

            // Créer la légende personnalisée
            this.createSubscriptionsLegend(labels, backgroundColors);
        }

        createSubscriptionsLegend(labels, colors) {
            const legendContainer = document.getElementById('subscriptionsLegend');
            if (!legendContainer) return;

            let legendHtml = '';
            labels.forEach((label, index) => {
                legendHtml += `
                <span class="me-2 mb-1 d-inline-block">
                    <i class="fas fa-circle" style="color: ${colors[index]}"></i> 
                    ${label}
                </span>
            `;
            });

            legendContainer.innerHTML = legendHtml;
        }

        getTypeLabel(type) {
            const typeMapping = {
                'standard': 'Standard',
                'handicape': 'PMR',
                'electrique': 'Électrique',
                'moto/scooter': 'Moto/Scooter',
                'velo': 'Vélo'
            };
            return typeMapping[type] || type;
        }
    }

    // Export pour le système de modules
    window.CoreAdminService = CoreAdminService;

    // Création de l'instance
    new CoreAdminService();

} // Fermeture de la condition if
