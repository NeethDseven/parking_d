<!-- Main content -->
<meta name="current-page" content="admin_dashboard">
<div class="content">
    <div class="container-fluid p-4">
        <!-- Mobile toggle -->
        <button class="btn btn-primary d-md-none mb-3" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <h1 class="mb-4">Tableau de bord</h1>

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

        <!-- Stats cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-primary text-white stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Utilisateurs</h6>
                                <h2 class="mb-0"><?php echo $userStats['total']; ?></h2>
                                <small><?php echo isset($userStats['new_this_month']) ? $userStats['new_this_month'] : 0; ?> nouveaux ce mois</small>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a href="<?php echo BASE_URL; ?>admin/users" class="text-white text-decoration-none">
                            <small>Voir détails</small>
                        </a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-success text-white stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Revenus</h6>
                                <h2 class="mb-0"><?php echo number_format($revenueStats['total'], 2); ?> €</h2>
                                <small><?php echo number_format(isset($revenueStats['this_month']) ? $revenueStats['this_month'] : 0, 2); ?> € ce mois</small>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-euro-sign"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a href="<?php echo BASE_URL; ?>admin/reservations" class="text-white text-decoration-none">
                            <small>Voir détails</small>
                        </a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-warning text-white stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Réservations</h6>
                                <h2 class="mb-0"><?php echo $reservationStats['total']; ?></h2>
                                <small><?php echo $reservationStats['today']; ?> aujourd'hui</small>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a href="<?php echo BASE_URL; ?>admin/reservations" class="text-white text-decoration-none">
                            <small>Voir détails</small>
                        </a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-info text-white stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Places disponibles</h6>
                                <h2 class="mb-0"><?php echo isset($placeStats['libre']) ? $placeStats['libre'] : 0; ?></h2>
                                <small>sur <?php echo $placeStats['total']; ?> places</small>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-car"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a href="<?php echo BASE_URL; ?>admin/places" class="text-white text-decoration-none">
                            <small>Voir détails</small>
                        </a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
        </div> <!-- Charts -->
        <div class="row mb-4">
            <div class="col-xl-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Revenus mensuels</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Répartition des places</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="placesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Répartition des abonnements</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="subscriptionsChart"></canvas>
                        </div>
                        <div class="mt-3 text-center small" id="subscriptionsLegend">
                            <!-- La légende sera générée par JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nouveaux graphiques pour places -->
        <div class="row mb-4">
            <div class="col-xl-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Statut des places</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="placeStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Répartition des places par type</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="placeTypeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-xl-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Réservations actives</h5>
                        <a href="<?php echo BASE_URL; ?>admin/reservations" class="btn btn-sm btn-primary">
                            Toutes les réservations
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (count($activeReservations) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Place</th>
                                            <th>Client</th>
                                            <th>Début</th>
                                            <th>Fin</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($activeReservations as $reservation): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($reservation['numero']); ?></td>
                                                <td>
                                                    <?php echo htmlspecialchars($reservation['nom']); ?>
                                                    <?php echo htmlspecialchars($reservation['prenom']); ?>
                                                </td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></td>
                                                <td>
                                                    <a href="<?php echo BASE_URL; ?>admin/viewReservation/<?php echo $reservation['id']; ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Aucune réservation active pour le moment
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Dernières actions</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($recentLogs) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Utilisateur</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentLogs as $log): ?>
                                            <tr>
                                                <td><?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?></td>
                                                <td>
                                                    <?php if ($log['nom'] && $log['prenom']): ?>
                                                        <?php echo htmlspecialchars($log['prenom']); ?>
                                                        <?php echo htmlspecialchars($log['nom']); ?>
                                                    <?php else: ?>
                                                        Invité
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($log['description']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Aucune action récente
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

                    try {
                        const chart = new Chart(canvas, config);
                        console.log(`✅ Graphique ${chartName} créé`);
                        return chart;
                    } catch (error) {
                        console.error(`❌ Erreur lors de la création du graphique ${chartName}:`, error);
                        return null;
                    }
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

                console.log('=== FIN INITIALISATION DASHBOARD ===');
            });
        </script>