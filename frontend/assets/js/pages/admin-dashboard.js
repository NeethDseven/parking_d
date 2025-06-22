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
    const reservationByStatus = JSON.parse(chartDataElement.dataset.reservationStatus || '{}');

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

    /* Crée le graphique de l'état des places (libre/occupées/en maintenance) */
    function createPlaceStatusChart() {
        const canvas = document.getElementById('placeStatusChart');
        if (!canvas || !placeStats) return;

        // Données spécifiques pour libre/occupées/en maintenance
        const data = {
            'Libres': placeStats.libre || 0,
            'Occupées': placeStats.occupee || 0,
            'En maintenance': placeStats.maintenance || 0
        };

        const labels = Object.keys(data);
        const values = Object.values(data);
        const backgroundColors = ['#28a745', '#dc3545', '#ffc107']; // Vert, Rouge, Orange

        createSafeChart(canvas, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: backgroundColors,
                    hoverBackgroundColor: backgroundColors,
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                    borderWidth: 2
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        }, 'placeStatus');
    }



    /* Crée le graphique des réservations par statut */
    function createReservationStatusChart() {
        const canvas = document.getElementById('reservationStatusChart');
        console.log('🔍 Canvas reservationStatusChart:', canvas);
        console.log('🔍 Données reservationByStatus:', reservationByStatus);
        if (!canvas || !reservationByStatus) return;

        // Données des réservations par statut (avec les vraies clés de la DB)
        const data = {
            'Confirmées': (reservationByStatus['confirmée'] || reservationByStatus.confirmee || 0),
            'En attente': reservationByStatus.en_attente || 0,
            'Annulées': (reservationByStatus['annulée'] || reservationByStatus.annulee || 0),
            'Terminées': reservationByStatus.terminee || 0,
            'En cours': reservationByStatus.en_cours || 0,
            'Expirées': (reservationByStatus['expirée'] || reservationByStatus.expiree || 0),
            'En cours immédiat': reservationByStatus.en_cours_immediat || 0,
            'En attente paiement': reservationByStatus.en_attente_paiement || 0
        };

        // Filtrer les données pour ne garder que celles avec des valeurs > 0
        const filteredData = {};
        Object.entries(data).forEach(([key, value]) => {
            if (value > 0) {
                filteredData[key] = value;
            }
        });

        const labels = Object.keys(filteredData);
        const values = Object.values(filteredData);
        const colorMap = {
            'Confirmées': '#28a745',              // Vert
            'En attente': '#ffc107',              // Orange
            'Annulées': '#dc3545',                // Rouge
            'Terminées': '#6c757d',               // Gris
            'En cours': '#17a2b8',                // Bleu
            'Expirées': '#343a40',                // Gris foncé
            'En cours immédiat': '#fd7e14',       // Orange vif
            'En attente paiement': '#20c997'      // Vert-bleu
        };
        const backgroundColors = labels.map(label => colorMap[label] || '#6c757d');

        // Ne créer le graphique que s'il y a des données
        if (labels.length === 0) {
            canvas.parentElement.innerHTML = '<div class="empty-chart"><i class="fas fa-info-circle"></i><p>Aucune réservation trouvée</p></div>';
            return;
        }

        // Afficher un message informatif si un seul statut
        if (labels.length === 1) {
            console.log(`ℹ️ Graphique réservations: Un seul statut trouvé (${labels[0]}: ${values[0]})`);
        }

        createSafeChart(canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nombre de réservations',
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
        }, 'reservationStatus');
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
    setTimeout(createReservationStatusChart, 300);
    setTimeout(createSubscriptionsChartDetailed, 400);
    setTimeout(createRevenueChart, 500);

    console.log('✅ Dashboard initialisé');
});
