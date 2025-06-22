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

        <!-- Layout desktop optimisé : Stats en haut, puis grille 2x2 -->
        <div class="dashboard-desktop-layout">
            <!-- Section 1 : Stats principales en haut sur toute la largeur -->
            <div class="dashboard-stats-desktop">
                <div class="stat-card primary">
                    <div class="stat-card-content">
                        <div class="stat-card-header">
                            <h6>Utilisateurs total</h6>
                            <i class="fas fa-users"></i>
                        </div>
                        <h2><?php echo $userStats['total']; ?></h2>
                        <p class="stat-card-subtitle">Utilisateurs inscrits</p>
                    </div>
                </div>

                <div class="stat-card primary-light">
                    <div class="stat-card-content">
                        <div class="stat-card-header">
                            <h6>Nouveaux utilisateurs</h6>
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h2><?php echo isset($userStats['new_this_month']) ? $userStats['new_this_month'] : 0; ?></h2>
                        <p class="stat-card-subtitle">Ce mois-ci</p>
                    </div>
                </div>

                <div class="stat-card success">
                    <div class="stat-card-content">
                        <div class="stat-card-header">
                            <h6>Revenus</h6>
                            <i class="fas fa-euro-sign"></i>
                        </div>
                        <h2><?php echo number_format($revenueStats['total'], 2); ?> €</h2>
                        <p class="stat-card-subtitle"><?php echo number_format(isset($revenueStats['this_month']) ? $revenueStats['this_month'] : 0, 2); ?> € ce mois</p>
                    </div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-card-content">
                        <div class="stat-card-header">
                            <h6>Réservations</h6>
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h2><?php echo $reservationStats['total']; ?></h2>
                        <p class="stat-card-subtitle"><?php echo $reservationStats['today']; ?> aujourd'hui</p>
                    </div>
                </div>

                <div class="stat-card info">
                    <div class="stat-card-content">
                        <div class="stat-card-header">
                            <h6>Places libres</h6>
                            <i class="fas fa-car"></i>
                        </div>
                        <h2><?php echo isset($placeStats['libre']) ? $placeStats['libre'] : 0; ?></h2>
                        <p class="stat-card-subtitle">sur <?php echo $placeStats['total']; ?> places</p>
                    </div>
                </div>

                <div class="stat-card secondary">
                    <div class="stat-card-content">
                        <div class="stat-card-header">
                            <h6>Types de places</h6>
                            <i class="fas fa-th-large"></i>
                        </div>
                        <h2><?php echo count($placeTypeStats ?? []); ?></h2>
                        <p class="stat-card-subtitle">Types disponibles</p>
                    </div>
                </div>
            </div>

            <!-- Section 2 : Contenu principal en grille 2x2 -->
            <div class="dashboard-main-grid">
                <!-- Zone 1 : Graphiques principaux -->
                <div class="dashboard-charts-main">
                    <div class="chart-card featured">
                        <div class="chart-card-header">
                            <h5><i class="fas fa-chart-line"></i> Revenus mensuels</h5>
                        </div>
                        <div class="chart-card-body">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-card-header">
                            <h6><i class="fas fa-chart-pie"></i> Répartition des places</h6>
                        </div>
                        <div class="chart-card-body">
                            <canvas id="placesChart"></canvas>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-card-header">
                            <h6><i class="fas fa-chart-pie"></i> État des places</h6>
                        </div>
                        <div class="chart-card-body">
                            <canvas id="placeStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Zone 2 : Graphiques secondaires -->
                <div class="dashboard-charts-secondary">
                    <div class="chart-card">
                        <div class="chart-card-header">
                            <h6><i class="fas fa-chart-bar"></i> Réservations par statut</h6>
                        </div>
                        <div class="chart-card-body">
                            <canvas id="reservationStatusChart"></canvas>
                        </div>
                    </div>

                    <div class="chart-card detailed">
                        <div class="chart-card-header">
                            <h6><i class="fas fa-chart-donut"></i> Abonnements détaillés</h6>
                        </div>
                        <div class="chart-card-body">
                            <canvas id="subscriptionsChartDetailed"></canvas>
                        </div>
                        <div class="chart-card-legend" id="subscriptionsLegendDetailed">
                            <!-- La légende sera générée par JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Zone 3 : Informations et tableaux -->
                <div class="dashboard-info-section">
                    <div class="info-card">
                        <div class="info-card-header">
                            <h5><i class="fas fa-calendar-check"></i> Réservations actives</h5>
                            <a href="<?php echo BASE_URL; ?>admin/reservations" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-list"></i> Voir tout
                            </a>
                        </div>
                        <div class="info-card-body">
                            <?php if (count($activeReservations) > 0): ?>
                                <div class="reservations-list">
                                    <?php foreach (array_slice($activeReservations, 0, 5) as $reservation): ?>
                                        <div class="reservation-item">
                                            <div class="reservation-place">
                                                <span class="place-number"><?php echo htmlspecialchars($reservation['numero']); ?></span>
                                            </div>
                                            <div class="reservation-user">
                                                <strong><?php echo htmlspecialchars($reservation['nom']); ?></strong>
                                                <?php if (!empty($reservation['prenom'])): ?>
                                                    <?php echo htmlspecialchars($reservation['prenom']); ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="reservation-time">
                                                <i class="fas fa-clock"></i>
                                                <?php echo date('H:i', strtotime($reservation['date_debut'])); ?>
                                                -
                                                <?php echo date('H:i', strtotime($reservation['date_fin'])); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-info-circle"></i>
                                    <p>Aucune réservation active</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Activité récente -->
                    <div class="info-card">
                        <div class="info-card-header">
                            <h5><i class="fas fa-history"></i> Activité récente</h5>
                        </div>
                        <div class="info-card-body">
                            <?php if (count($recentLogs) > 0): ?>
                                <div class="activity-list">
                                    <?php foreach (array_slice($recentLogs, 0, 5) as $log): ?>
                                        <div class="activity-item">
                                            <div class="activity-time">
                                                <i class="fas fa-clock"></i>
                                                <?php echo date('H:i', strtotime($log['created_at'])); ?>
                                            </div>
                                            <div class="activity-content">
                                                <div class="activity-user">
                                                    <?php if ($log['nom'] && $log['prenom']): ?>
                                                        <strong><?php echo htmlspecialchars($log['prenom'] . ' ' . $log['nom']); ?></strong>
                                                    <?php else: ?>
                                                        <em>Invité</em>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="activity-description">
                                                    <?php echo htmlspecialchars($log['description']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-info-circle"></i>
                                    <p>Aucune activité récente</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
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

        <!-- Script d'initialisation - chargement explicite pour diagnostic -->
        <script src="<?php echo getBaseUrl(); ?>frontend/assets/js/pages/admin-dashboard.js?v=<?php echo time(); ?>"></script>



