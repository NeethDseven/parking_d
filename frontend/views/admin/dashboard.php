<!-- Main content -->
<meta name="current-page" content="admin_dashboard">
<div class="content">
    <div class="container-fluid">
        <!-- Header du dashboard style navbar -->
        <div class="dashboard-header">
            <h1 class="dashboard-title">
                <i class="fas fa-tachometer-alt"></i>
                Tableau de bord
            </h1>
            <p class="dashboard-subtitle">Vue d'ensemble de votre système de parking</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Layout ultra-compact : Stats + Graphiques + Tableaux en une seule ligne -->
        <div class="dashboard-ultra-compact-layout">
            <!-- Colonne 1 : Stats compactes -->
            <div class="dashboard-stats-compact">
                <div class="stat-mini primary">
                    <div class="stat-mini-content">
                        <h6>Utilisateurs</h6>
                        <h3><?php echo $userStats['total']; ?></h3>
                        <small><?php echo isset($userStats['new_this_month']) ? $userStats['new_this_month'] : 0; ?> nouveaux</small>
                    </div>
                    <div class="stat-mini-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>

                <div class="stat-mini success">
                    <div class="stat-mini-content">
                        <h6>Revenus</h6>
                        <h3><?php echo number_format($revenueStats['total'], 2); ?> €</h3>
                        <small><?php echo number_format(isset($revenueStats['this_month']) ? $revenueStats['this_month'] : 0, 2); ?> € ce mois</small>
                    </div>
                    <div class="stat-mini-icon">
                        <i class="fas fa-euro-sign"></i>
                    </div>
                </div>

                <div class="stat-mini warning">
                    <div class="stat-mini-content">
                        <h6>Réservations</h6>
                        <h3><?php echo $reservationStats['total']; ?></h3>
                        <small><?php echo $reservationStats['today']; ?> aujourd'hui</small>
                    </div>
                    <div class="stat-mini-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>

                <div class="stat-mini info">
                    <div class="stat-mini-content">
                        <h6>Places libres</h6>
                        <h3><?php echo isset($placeStats['libre']) ? $placeStats['libre'] : 0; ?></h3>
                        <small>sur <?php echo $placeStats['total']; ?> places</small>
                    </div>
                    <div class="stat-mini-icon">
                        <i class="fas fa-car"></i>
                    </div>
                </div>
            </div>

            <!-- Colonne 2 : Graphiques ultra-compacts -->
            <div class="dashboard-charts-ultra-compact">
                <div class="chart-mini">
                    <h6><i class="fas fa-chart-line"></i> Revenus</h6>
                    <div class="chart-mini-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <div class="chart-mini">
                    <h6><i class="fas fa-chart-pie"></i> Places</h6>
                    <div class="chart-mini-container">
                        <canvas id="placesChart"></canvas>
                    </div>
                </div>

                <div class="chart-mini">
                    <h6><i class="fas fa-chart-donut"></i> Abonnements</h6>
                    <div class="chart-mini-container">
                        <canvas id="subscriptionsChart"></canvas>
                    </div>
                </div>

                <div class="chart-mini">
                    <h6><i class="fas fa-chart-bar"></i> Statut</h6>
                    <div class="chart-mini-container">
                        <canvas id="placeStatusChart"></canvas>
                    </div>
                </div>

                <div class="chart-mini">
                    <h6><i class="fas fa-chart-bar"></i> Types</h6>
                    <div class="chart-mini-container">
                        <canvas id="placeTypeChart"></canvas>
                    </div>
                </div>

                <div class="chart-mini chart-detailed">
                    <h6><i class="fas fa-chart-donut"></i> Abonnements détaillés</h6>
                    <div class="chart-mini-container">
                        <canvas id="subscriptionsChartDetailed"></canvas>
                    </div>
                    <div class="chart-mini-legend" id="subscriptionsLegendDetailed">
                        <!-- La légende sera générée par JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Colonne 3 : Tableaux compacts -->
            <div class="dashboard-tables-compact">
                <div class="table-mini">
                    <div class="table-mini-header">
                        <h6><i class="fas fa-calendar-check"></i> Réservations actives</h6>
                        <a href="<?php echo BASE_URL; ?>admin/reservations" class="btn-mini">
                            <i class="fas fa-list"></i>
                        </a>
                    </div>
                    <div class="table-mini-body">
                        <?php if (count($activeReservations) > 0): ?>
                            <div class="table-mini-scroll">
                                <?php foreach (array_slice($activeReservations, 0, 3) as $reservation): ?>
                                    <div class="table-mini-row">
                                        <div class="table-mini-cell">
                                            <strong><?php echo htmlspecialchars($reservation['numero']); ?></strong>
                                        </div>
                                        <div class="table-mini-cell">
                                            <?php echo htmlspecialchars($reservation['nom']); ?>
                                        </div>
                                        <div class="table-mini-cell">
                                            <?php echo date('H:i', strtotime($reservation['date_debut'])); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="table-mini-empty">
                                <i class="fas fa-info-circle"></i>
                                Aucune réservation
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="table-mini">
                    <div class="table-mini-header">
                        <h6><i class="fas fa-history"></i> Dernières actions</h6>
                    </div>
                    <div class="table-mini-body">
                        <?php if (count($recentLogs) > 0): ?>
                            <div class="table-mini-scroll">
                                <?php foreach (array_slice($recentLogs, 0, 3) as $log): ?>
                                    <div class="table-mini-row">
                                        <div class="table-mini-cell">
                                            <?php echo date('H:i', strtotime($log['created_at'])); ?>
                                        </div>
                                        <div class="table-mini-cell">
                                            <?php if ($log['nom'] && $log['prenom']): ?>
                                                <?php echo htmlspecialchars($log['prenom']); ?>
                                            <?php else: ?>
                                                <em>Invité</em>
                                            <?php endif; ?>
                                        </div>
                                        <div class="table-mini-cell">
                                            <?php echo substr(htmlspecialchars($log['description']), 0, 20); ?>...
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="table-mini-empty">
                                <i class="fas fa-info-circle"></i>
                                Aucune action
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>






        <!-- Données pour les graphiques -->
        <div id="chart-data" class="d-none"
            data-place-types='<?php echo json_encode($placeTypeStats ?? []); ?>'
            data-place-status='<?php echo json_encode($placeStats ?? []); ?>'
            data-subscription-stats='<?php echo json_encode($subscriptionStats ?? []); ?>'
            data-reservation-status='<?php echo json_encode($reservationByStatus ?? []); ?>'
            data-revenue-stats='<?php echo json_encode([
                                    'day' => $revenueStats["day"] ?? 0,
                                    'week' => $revenueStats["week"] ?? 0,
                                    'month' => $revenueStats["this_month"] ?? 0,
                                    'year' => $revenueStats["year"] ?? 0,
                                    'total' => $revenueStats["total"] ?? 0
                                ]); ?>'>
        </div>

        <!-- Scripts pour les graphiques -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Script d'initialisation des graphiques -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('=== INITIALISATION DASHBOARD ===');

                // Données depuis PHP
                const placeTypeStats = <?php echo json_encode($placeTypeStats ?? []); ?>;
                const placeStats = <?php echo json_encode($placeStats ?? []); ?>;
                const subscriptionStats = <?php echo json_encode($subscriptionStats ?? []); ?>;
                const revenueStats = <?php echo json_encode($revenueStats ?? []); ?>;
                const reservationByStatus = <?php echo json_encode($reservationByStatus ?? []); ?>;

                console.log('placeTypeStats:', placeTypeStats);
                console.log('placeStats:', placeStats);
                console.log('subscriptionStats:', subscriptionStats);
                console.log('revenueStats:', revenueStats);
                console.log('reservationByStatus:', reservationByStatus);
                // Couleurs pour les graphiques
                const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69'];

                // Fonction utilitaire pour les labels
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

                // Fonction utilitaire pour créer un graphique de manière sécurisée
                function createSafeChart(canvas, config, chartName) {
                    if (!canvas) {
                        console.log(`⚠️ Canvas ${chartName} non disponible`);
                        return null;
                    }

                    // Détruire le graphique existant s'il existe
                    const existingChart = Chart.getChart(canvas);
                    if (existingChart) {
                        console.log(`🗑️ Destruction du graphique ${chartName} existant`);
                        existingChart.destroy();
                    }

                    // Normaliser la taille du canvas
                    normalizeCanvasSize(canvas);

                    try {
                        // Configuration par défaut pour tous les graphiques
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
                                            font: {
                                                size: 11
                                            },
                                            boxWidth: 12,
                                            boxHeight: 12
                                        }
                                    }
                                },
                                // Configuration spéciale pour les graphiques en macaron
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

                // Fonction pour normaliser la taille des canvas
                function normalizeCanvasSize(canvas) {
                    // Supprimer les attributs de style inline qui peuvent interférer
                    canvas.removeAttribute('style');
                    canvas.style.width = '100%';
                    canvas.style.height = '100%';
                    canvas.style.maxWidth = '100%';
                    canvas.style.maxHeight = '280px';
                }

                function getStatusLabel(status) {
                    const labels = {
                        'libre': 'Libre',
                        'occupe': 'Occupé',
                        'maintenance': 'Maintenance'
                    };
                    return labels[status] || status;
                }

                // Graphique des types de places
                setTimeout(() => {
                    const placesCanvas = document.getElementById('placesChart');
                    if (placesCanvas && placeTypeStats) {
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
                            // Détruire le graphique existant s'il existe
                            const existingChart = Chart.getChart(placesCanvas);
                            if (existingChart) {
                                console.log('🗑️ Destruction du graphique de places existant');
                                existingChart.destroy();
                            }

                            try {
                                new Chart(placesCanvas, {
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
                                                position: 'bottom',
                                            }
                                        }
                                    }
                                });
                                console.log('✅ Graphique des places créé');
                            } catch (error) {
                                console.error('❌ Erreur lors de la création du graphique de places:', error);
                            }
                            console.log('✅ Graphique des places créé');
                        }
                    }
                }, 100);

                // Graphique du statut des places
                setTimeout(() => {
                    const statusCanvas = document.getElementById('placeStatusChart');
                    if (statusCanvas && placeStats && placeStats.by_status) {
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
                            // Détruire le graphique existant s'il existe
                            const existingChart = Chart.getChart(statusCanvas);
                            if (existingChart) {
                                console.log('🗑️ Destruction du graphique de statut existant');
                                existingChart.destroy();
                            }

                            try {
                                new Chart(statusCanvas, {
                                    type: 'pie',
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
                                                position: 'bottom',
                                            }
                                        }
                                    }
                                });
                                console.log('✅ Graphique du statut des places créé');
                            } catch (error) {
                                console.error('❌ Erreur lors de la création du graphique de statut:', error);
                            }
                        } else {
                            console.log('⚠️ Aucune donnée de statut à afficher');
                        }
                    } else {
                        console.log('⚠️ Canvas statusChart non disponible');
                    }
                }, 200);

                // Graphique des types de places (deuxième canvas)
                setTimeout(() => {
                    const typeCanvas = document.getElementById('placeTypeChart');
                    if (typeCanvas && placeTypeStats) {
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
                            // Détruire le graphique existant s'il existe
                            const existingChart = Chart.getChart(typeCanvas);
                            if (existingChart) {
                                console.log('🗑️ Destruction du graphique de types existant');
                                existingChart.destroy();
                            }

                            try {
                                new Chart(typeCanvas, {
                                    type: 'bar',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            label: 'Nombre de places',
                                            data: values,
                                            backgroundColor: backgroundColors,
                                            borderColor: backgroundColors,
                                            borderWidth: 1
                                        }],
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                display: false
                                            }
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
                                });
                                console.log('✅ Graphique des types de places (bar) créé');
                            } catch (error) {
                                console.error('❌ Erreur lors de la création du graphique de types:', error);
                            }
                        } else {
                            console.log('⚠️ Aucune donnée de type à afficher');
                        }
                    } else {
                        console.log('⚠️ Canvas typeChart non disponible');
                    }
                }, 300);
                // Graphique des abonnements
                setTimeout(() => {
                    const subscriptionsCanvas = document.getElementById('subscriptionsChart');
                    if (subscriptionsCanvas && subscriptionStats && subscriptionStats.length > 0) {
                        const labels = [];
                        const values = [];
                        const backgroundColors = [];

                        let colorIndex = 0;
                        subscriptionStats.forEach(sub => {
                            const count = parseInt(sub.count || sub.active_count || 0);
                            const name = sub.name || sub.nom || 'Inconnu';

                            if (count >= 0) { // Afficher même les 0 pour montrer tous les types
                                labels.push(name);
                                values.push(count);
                                backgroundColors.push(colors[colorIndex % colors.length]);
                                colorIndex++;
                            }
                        });
                        if (labels.length > 0) {
                            // Détruire le graphique existant s'il existe
                            const existingChart = Chart.getChart(subscriptionsCanvas);
                            if (existingChart) {
                                console.log('🗑️ Destruction du graphique d\'abonnements existant');
                                existingChart.destroy();
                            }

                            try {
                                new Chart(subscriptionsCanvas, {
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
                                                position: 'bottom',
                                            }
                                        }
                                    }
                                });
                                console.log('✅ Graphique des abonnements créé avec', labels.length, 'éléments');
                            } catch (error) {
                                console.error('❌ Erreur lors de la création du graphique d\'abonnements:', error);
                            }
                        } else {
                            subscriptionsCanvas.parentNode.innerHTML = '<div class="alert alert-info text-center">Aucun abonnement disponible</div>';
                            console.log('⚠️ Aucune donnée d\'abonnement à afficher');
                        }
                    } else {
                        console.log('⚠️ Canvas subscriptionsChart ou données non disponibles');
                        console.log('Canvas:', !!subscriptionsCanvas);
                        console.log('Données:', subscriptionStats);
                    }
                }, 400); // Graphique des revenus
                setTimeout(() => {
                    const revenueCanvas = document.getElementById('revenueChart');
                    if (revenueCanvas && revenueStats) {
                        // Détruire le graphique existant s'il existe (méthode robuste)
                        try {
                            if (window.revenueChartInstance && typeof window.revenueChartInstance.destroy === 'function') {
                                console.log('🗑️ Destruction du graphique de revenus existant');
                                window.revenueChartInstance.destroy();
                            }
                        } catch (error) {
                            console.log('⚠️ Erreur lors de la destruction du graphique de revenus:', error);
                        } finally {
                            window.revenueChartInstance = null;
                        }

                        // Vérifier que le canvas n'est pas déjà utilisé
                        const existingChart = Chart.getChart(revenueCanvas);
                        if (existingChart) {
                            console.log('🗑️ Destruction forcée du graphique via Chart.getChart');
                            existingChart.destroy();
                        }

                        const labels = ['Jour', 'Semaine', 'Mois', 'Année'];
                        const values = [
                            revenueStats.day || 0,
                            revenueStats.week || 0,
                            revenueStats.month || revenueStats.this_month || 0,
                            revenueStats.year || 0
                        ];

                        try {
                            window.revenueChartInstance = new Chart(revenueCanvas, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Revenus (€)',
                                        data: values,
                                        backgroundColor: '#1cc88a',
                                        borderColor: '#1cc88a',
                                        borderWidth: 1
                                    }],
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
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
                            });
                            console.log('✅ Graphique des revenus créé');
                        } catch (error) {
                            console.error('❌ Erreur lors de la création du graphique de revenus:', error);
                        }
                    } else {
                        console.log('⚠️ Canvas revenueChart ou données non disponibles');
                    }
                }, 500);

                // Graphique détaillé des abonnements
                setTimeout(() => {
                    const subscriptionsDetailedCanvas = document.getElementById('subscriptionsChartDetailed');
                    if (subscriptionsDetailedCanvas && subscriptionStats && subscriptionStats.length > 0) {
                        const labels = [];
                        const values = [];
                        const backgroundColors = [];

                        let colorIndex = 0;
                        subscriptionStats.forEach(sub => {
                            const count = parseInt(sub.count || sub.active_count || 0);
                            const name = sub.name || sub.nom || 'Inconnu';

                            if (count >= 0) { // Afficher même les 0 pour montrer tous les types
                                labels.push(name);
                                values.push(count);
                                backgroundColors.push(colors[colorIndex % colors.length]);
                                colorIndex++;
                            }
                        });

                        if (labels.length > 0) {
                            // Détruire le graphique existant s'il existe
                            const existingChart = Chart.getChart(subscriptionsDetailedCanvas);
                            if (existingChart) {
                                console.log('🗑️ Destruction du graphique d\'abonnements détaillé existant');
                                existingChart.destroy();
                            }

                            try {
                                new Chart(subscriptionsDetailedCanvas, {
                                    type: 'doughnut',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            data: values,
                                            backgroundColor: backgroundColors,
                                            hoverBackgroundColor: backgroundColors,
                                            hoverBorderColor: "rgba(234, 236, 244, 1)",
                                            borderWidth: 2,
                                            borderColor: '#fff'
                                        }],
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                position: 'bottom',
                                                labels: {
                                                    usePointStyle: true,
                                                    padding: 15,
                                                    font: {
                                                        size: 12
                                                    }
                                                }
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        return context.label + ': ' + context.parsed + ' abonnements';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                                console.log('✅ Graphique détaillé des abonnements créé avec', labels.length, 'éléments');
                            } catch (error) {
                                console.error('❌ Erreur lors de la création du graphique détaillé d\'abonnements:', error);
                            }
                        } else {
                            subscriptionsDetailedCanvas.parentNode.innerHTML = '<div class="alert alert-info text-center">Aucun abonnement disponible</div>';
                            console.log('⚠️ Aucune donnée d\'abonnement détaillé à afficher');
                        }
                    } else {
                        console.log('⚠️ Canvas subscriptionsChartDetailed ou données non disponibles');
                    }
                }, 600);

                console.log('=== FIN INITIALISATION DASHBOARD ===');
            });
        </script>