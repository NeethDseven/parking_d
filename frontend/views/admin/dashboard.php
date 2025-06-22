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

        <!-- Script d'initialisation - chargement explicite pour diagnostic -->
        <script src="<?php echo getBaseUrl(); ?>frontend/assets/js/pages/admin-dashboard.js?v=<?php echo time(); ?>"></script>



