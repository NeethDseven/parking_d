<?php
$title = 'Administration des abonnements - ' . APP_NAME;
$activeMenu = 'subscriptions'; // Pour mettre en surbrillance l'élément de menu actif
require_once FRONTEND_PATH . '/views/admin/templates/header.php';
?>

<meta name="current-page" content="admin_subscriptions">
<div class="wrapper d-flex align-items-stretch">
    <!-- Sidebar -->
    <?php require_once FRONTEND_PATH . '/views/admin/templates/sidebar.php'; ?>

    <!-- Content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Container principal uniforme -->
            <div class="admin-page-container">

                <!-- Header de page uniforme -->
                <div class="admin-page-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="admin-page-title">
                                <i class="fas fa-crown"></i>
                                Gestion des abonnements
                            </h1>
                            <p class="text-muted mb-0">Gérez les formules d'abonnement et suivez les revenus</p>
                        </div>
                        <div class="admin-page-actions">
                            <a href="<?php echo BASE_URL; ?>subscription/create" class="admin-btn admin-btn-primary">
                                <i class="fas fa-plus"></i>
                                Nouvel abonnement
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Alertes style uniforme -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="admin-content-card">
                        <div class="admin-content-card-body">
                            <div class="dashboard-alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <?php echo $_SESSION['success']; ?>
                            </div>
                        </div>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="admin-content-card">
                        <div class="admin-content-card-body">
                            <div class="dashboard-alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i>
                                <?php echo $_SESSION['error']; ?>
                            </div>
                        </div>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Stats Cards style uniforme -->
                <div class="dashboard-stats-row">
                    <div class="dashboard-stat-card primary">
                        <div class="dashboard-stat-header">
                            <div class="dashboard-stat-content">
                                <h6>Abonnements actifs</h6>
                                <h2><?php echo $activeCount; ?></h2>
                                <small>Utilisateurs abonnés</small>
                            </div>
                            <div class="dashboard-stat-icon primary">
                                <i class="fas fa-crown"></i>
                            </div>
                        </div>
                        <div class="dashboard-stat-footer">
                            <a href="<?php echo BASE_URL; ?>subscription/users">
                                <i class="fas fa-arrow-right"></i>
                                Voir les abonnés
                            </a>
                        </div>
                    </div>

                    <div class="dashboard-stat-card success">
                        <div class="dashboard-stat-header">
                            <div class="dashboard-stat-content">
                                <h6>Revenus du jour</h6>
                                <h2><?php echo number_format($revenue['today'], 2); ?> €</h2>
                                <small>Aujourd'hui</small>
                            </div>
                            <div class="dashboard-stat-icon success">
                                <i class="fas fa-euro-sign"></i>
                            </div>
                        </div>
                        <div class="dashboard-stat-footer">
                            <a href="#subscriptionsList">
                                <i class="fas fa-arrow-right"></i>
                                Voir détails
                            </a>
                        </div>
                    </div>

                    <div class="dashboard-stat-card info">
                        <div class="dashboard-stat-header">
                            <div class="dashboard-stat-content">
                                <h6>Revenus du mois</h6>
                                <h2><?php echo number_format($revenue['month'], 2); ?> €</h2>
                                <small>Ce mois-ci</small>
                            </div>
                            <div class="dashboard-stat-icon info">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                        <div class="dashboard-stat-footer">
                            <a href="#subscriptionsList">
                                <i class="fas fa-arrow-right"></i>
                                Voir graphiques
                            </a>
                        </div>
                    </div>

                    <div class="dashboard-stat-card warning">
                        <div class="dashboard-stat-header">
                            <div class="dashboard-stat-content">
                                <h6>Revenus totaux</h6>
                                <h2><?php echo number_format($revenue['total'], 2); ?> €</h2>
                                <small>Depuis le début</small>
                            </div>
                            <div class="dashboard-stat-icon warning">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                        <div class="dashboard-stat-footer">
                            <a href="#subscriptionsList">
                                <i class="fas fa-arrow-right"></i>
                                Historique complet
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Layout en grille optimisé -->
                <div class="admin-dashboard-layout">
                    <!-- Section principale - Liste des abonnements -->
                    <div class="admin-main-section">
                        <div class="admin-content-card" id="subscriptionsList">
                            <div class="admin-content-card-header">
                                <h3 class="admin-content-card-title">
                                    <i class="fas fa-list me-2"></i>
                                    Abonnements disponibles
                                </h3>
                                <div>
                                    <a href="<?php echo BASE_URL; ?>subscription/users" class="admin-btn admin-btn-outline admin-btn-sm">
                                        <i class="fas fa-users"></i>
                                        Voir les abonnés
                                    </a>
                                </div>
                            </div>
                    <div class="admin-content-card-body">
                        <div class="admin-table-wrapper">
                            <table class="admin-table">
                                <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Prix</th>
                                            <th>Durée (jours)</th>
                                            <th>Minutes gratuites</th>
                                            <th>Réduction</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($subscriptions as $subscription): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($subscription['name']); ?></td>
                                                <td><?php echo number_format($subscription['price'], 2); ?> €</td>
                                                <td><?php echo $subscription['duration_days']; ?></td>
                                                <td><?php echo $subscription['free_minutes']; ?> min</td>
                                                <td><?php echo $subscription['discount_percent']; ?>%</td>
                                                <td>
                                                    <?php if ($subscription['is_active']): ?>
                                                        <span class="badge bg-success">Actif</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Inactif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <a href="<?php echo BASE_URL; ?>subscription/update/<?php echo $subscription['id']; ?>" class="admin-btn-icon edit" title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="admin-btn-icon delete" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $subscription['id']; ?>" title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>

                                                    <!-- Modal de suppression -->
                                                    <div class="modal fade" id="deleteModal<?php echo $subscription['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $subscription['id']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $subscription['id']; ?>">Confirmer la suppression</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Êtes-vous sûr de vouloir supprimer l'abonnement "<?php echo htmlspecialchars($subscription['name']); ?>" ?</p>
                                                                    <p class="text-danger">Si des utilisateurs ont souscrit à cet abonnement, il sera désactivé au lieu d'être supprimé.</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                    <a href="<?php echo BASE_URL; ?>subscription/delete/<?php echo $subscription['id']; ?>" class="btn btn-danger">Supprimer</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    </div> <!-- Fin admin-main-section -->



                        <!-- Statistiques complémentaires -->
                        <div class="admin-content-card mt-3">
                            <div class="admin-content-card-header">
                                <h5 class="admin-content-card-title">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Informations rapides
                                </h5>
                            </div>
                            <div class="admin-content-card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                            <span class="text-muted">Total abonnements</span>
                                            <strong><?php echo count($subscriptions); ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                            <span class="text-muted">Abonnements actifs</span>
                                            <strong><?php echo count(array_filter($subscriptions, function($s) { return $s['is_active']; })); ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                            <span class="text-muted">Prix moyen</span>
                                            <strong><?php
                                                $prices = array_column($subscriptions, 'price');
                                                echo count($prices) > 0 ? number_format(array_sum($prices) / count($prices), 2) : '0.00';
                                            ?> €</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- Fin admin-sidebar-section -->

                </div> <!-- Fin admin-dashboard-layout -->

            </div> <!-- Fin admin-page-container -->
        </div>

        <!-- Script pour le graphique des abonnements déplacé vers le dashboard -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Page des abonnements chargée - Graphique déplacé vers le dashboard');
            });
        </script>

        <?php require_once FRONTEND_PATH . '/views/admin/templates/footer.php'; ?>