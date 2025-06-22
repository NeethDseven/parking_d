/* Script pour le dashboard admin - Gestion des graphiques */

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== INITIALISATION DASHBOARD ===');

    /* Vérification de Chart.js */
    if (typeof Chart === 'undefined') {
        console.error('❌ Chart.js non disponible');
        return;
    }
    console.log('✅ Chart.js disponible');

    /* Récupère les données depuis les attributs data */
    const chartDataElement = document.getElementById('chart-data');
    if (!chartDataElement) {
        console.error('❌ Élément chart-data non trouvé');
        return;
    }
    console.log('✅ Élément chart-data trouvé');

    /* Parse les données JSON */
    const placeTypeStats = JSON.parse(chartDataElement.dataset.placeTypes || '{}');
    const placeStats = JSON.parse(chartDataElement.dataset.placeStatus || '{}');
    const subscriptionStats = JSON.parse(chartDataElement.dataset.subscriptionStats || '[]');
    const revenueStats = JSON.parse(chartDataElement.dataset.revenueStats || '{}');
    const reservationByStatus = JSON.parse(chartDataElement.dataset.reservationByStatus || '{}');

    /* Couleurs pour les graphiques */
    const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69'];

    /* Utilitaires pour les labels */
    function getTypeLabel(type) {
        const labels = {
            'standard': 'Standard',
            'handicape': 'Handicapé',
            'electrique': 'Électrique',
            'moto/scooter': 'Moto/Scooter',
            'velo': 'Vélo'
        };
        return labels[type] || type;
    }

    function getStatusLabel(status) {
        const labels = {
            'libre': 'Libre',
            'occupe': 'Occupé',
            'maintenance': 'Maintenance'
        };
        return labels[status] || status;
    }

    /* Crée un graphique de manière sécurisée */
    function createSafeChart(canvas, config, chartName) {
        if (!canvas) {
            console.log(`⚠️ Canvas ${chartName} non disponible`);
            return null;
        }

        /* Détruit le graphique existant s'il existe */
        const existingChart = Chart.getChart(canvas);
        if (existingChart) {
            console.log(`🗑️ Destruction du graphique ${chartName} existant`);
            existingChart.destroy();
        }

        /* Normalise la taille du canvas */
        normalizeCanvasSize(canvas);

        try {
            /* Configuration par défaut pour tous les graphiques */
            const defaultConfig = {
                ...config,
                options: {
                    ...config.options,
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        ...config.options?.plugins,
                        legend: {
                            ...config.options?.plugins?.legend,
                            position: 'bottom',
                            labels: {
                                ...config.options?.plugins?.legend?.labels,
                                usePointStyle: true,
                                padding: 10,
                                font: { size: 11 },
                                boxWidth: 12,
                                boxHeight: 12
                            }
                        }
                    },
                    /* Configuration spéciale pour les graphiques en macaron */
                    ...(config.type === 'doughnut' || config.type === 'pie' ? {
                        cutout: config.type === 'doughnut' ? '60%' : 0,
                        radius: '80%'
                    } : {})
                }
            };

            const chart = new Chart(canvas, defaultConfig);
            console.log(`✅ Graphique ${chartName} créé`);
            return chart;
        } catch (error) {
            console.error(`❌ Erreur lors de la création du graphique ${chartName}:`, error);
            return null;
        }
    }

    /* Normalise la taille des canvas */
    function normalizeCanvasSize(canvas) {
        canvas.removeAttribute('style');
        canvas.style.width = '100%';
        canvas.style.height = '100%';
        canvas.style.maxWidth = '100%';
        canvas.style.maxHeight = '280px';
    }

    /* Crée le graphique des types de places */
    function createPlacesChart() {
        const canvas = document.getElementById('placesChart');
        console.log('🔍 Canvas placesChart:', canvas);
        console.log('🔍 Données placeTypeStats:', placeTypeStats);
        if (!canvas || !placeTypeStats) return;

        const labels = [];
        const values = [];
        const backgroundColors = [];

        let colorIndex = 0;
        for (const [type, count] of Object.entries(placeTypeStats)) {
            if (count > 0) {
                labels.push(getTypeLabel(type));
                values.push(count);
                backgroundColors.push(colors[colorIndex % colors.length]);
                colorIndex++;
            }
        }

        if (labels.length > 0) {
            createSafeChart(canvas, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: backgroundColors,
                        hoverBackgroundColor: backgroundColors,
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }]
                }
            }, 'places');
        }
    }

    /* Crée le graphique du statut des places */
    function createPlaceStatusChart() {
        const canvas = document.getElementById('placeStatusChart');
        if (!canvas || !placeStats?.by_status) return;

        const labels = [];
        const values = [];
        const backgroundColors = [];

        let colorIndex = 0;
        for (const [status, count] of Object.entries(placeStats.by_status)) {
            if (count > 0) {
                labels.push(getStatusLabel(status));
                values.push(count);
                backgroundColors.push(colors[colorIndex % colors.length]);
                colorIndex++;
            }
        }

        if (labels.length > 0) {
            createSafeChart(canvas, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: backgroundColors,
                        hoverBackgroundColor: backgroundColors,
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }]
                }
            }, 'placeStatus');
        }
    }

    /* Crée le graphique des abonnements */
    function createSubscriptionsChart() {
        const canvas = document.getElementById('subscriptionsChart');
        if (!canvas || !subscriptionStats?.length) return;

        const labels = [];
        const values = [];
        const backgroundColors = [];

        let colorIndex = 0;
        subscriptionStats.forEach(sub => {
            const count = parseInt(sub.count || sub.active_count || 0);
            const name = sub.name || sub.nom || 'Inconnu';

            if (count >= 0) {
                labels.push(name);
                values.push(count);
                backgroundColors.push(colors[colorIndex % colors.length]);
                colorIndex++;
            }
        });

        if (labels.length > 0) {
            createSafeChart(canvas, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: backgroundColors,
                        hoverBackgroundColor: backgroundColors,
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }]
                }
            }, 'subscriptions');
        }
    }

    /* Crée le graphique des types de places */
    function createPlaceTypeChart() {
        const canvas = document.getElementById('placeTypeChart');
        console.log('🔍 Canvas placeTypeChart:', canvas);
        console.log('🔍 Données placeTypeStats:', placeTypeStats);
        if (!canvas || !placeTypeStats) return;

        const labels = [];
        const values = [];
        const backgroundColors = [];

        let colorIndex = 0;
        for (const [type, count] of Object.entries(placeTypeStats)) {
            if (count > 0) {
                labels.push(getTypeLabel(type));
                values.push(count);
                backgroundColors.push(colors[colorIndex % colors.length]);
                colorIndex++;
            }
        }

        if (labels.length > 0) {
            createSafeChart(canvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Nombre de places',
                        data: values,
                        backgroundColor: backgroundColors,
                        borderColor: backgroundColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            }, 'placeType');
        }
    }

    /* Crée le graphique des revenus */
    function createRevenueChart() {
        const canvas = document.getElementById('revenueChart');
        if (!canvas || !revenueStats) return;

        const labels = ['Jour', 'Semaine', 'Mois', 'Année'];
        const values = [
            revenueStats.day || 0,
            revenueStats.week || 0,
            revenueStats.month || revenueStats.this_month || 0,
            revenueStats.year || 0
        ];

        createSafeChart(canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenus (€)',
                    data: values,
                    backgroundColor: '#1cc88a',
                    borderColor: '#1cc88a',
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(2) + ' €';
                            }
                        }
                    }
                }
            }
        }, 'revenue');
    }

    /* Crée le graphique des abonnements détaillés */
    function createSubscriptionsChartDetailed() {
        const canvas = document.getElementById('subscriptionsChartDetailed');
        console.log('🔍 Canvas subscriptionsChartDetailed:', canvas);
        console.log('🔍 Données subscriptionStats:', subscriptionStats);
        if (!canvas || !subscriptionStats?.length) return;

        const labels = [];
        const values = [];
        const backgroundColors = [];

        let colorIndex = 0;
        subscriptionStats.forEach(sub => {
            const count = parseInt(sub.count || sub.active_count || 0);
            const name = sub.name || sub.nom || 'Inconnu';

            if (count >= 0) {
                labels.push(name);
                values.push(count);
                backgroundColors.push(colors[colorIndex % colors.length]);
                colorIndex++;
            }
        });

        if (labels.length > 0) {
            createSafeChart(canvas, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: backgroundColors,
                        hoverBackgroundColor: backgroundColors,
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false // Légende personnalisée
                        }
                    }
                }
            }, 'subscriptionsDetailed');

            /* Créer la légende personnalisée */
            const legendContainer = document.getElementById('subscriptionsLegendDetailed');
            if (legendContainer) {
                let legendHtml = '';
                labels.forEach((label, index) => {
                    legendHtml += `
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: ${backgroundColors[index]}"></span>
                            <span class="legend-label">${label}: ${values[index]}</span>
                        </div>
                    `;
                });
                legendContainer.innerHTML = legendHtml;
            }
        }
    }

    /* Initialise tous les graphiques avec des délais pour éviter les conflits */
    setTimeout(createPlacesChart, 100);
    setTimeout(createPlaceStatusChart, 200);
    setTimeout(createSubscriptionsChart, 300);
    setTimeout(createPlaceTypeChart, 400);
    setTimeout(createSubscriptionsChartDetailed, 500);
    setTimeout(createRevenueChart, 600);

    console.log('✅ Dashboard initialisé');
});
