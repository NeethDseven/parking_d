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
        <div class="container-fluid p-4">
            <!-- Mobile toggle -->
            <button class="btn btn-primary d-md-none mb-3" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Gestion des abonnements</h1>
                <a href="<?php echo BASE_URL; ?>subscription/create" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-2"></i>Nouvel abonnement
                </a>
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

            <!-- Statistiques des abonnements -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Abonnements actifs</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $activeCount; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Revenus aujourd'hui</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($revenue['today'], 2); ?> €</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Revenus ce mois</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($revenue['month'], 2); ?> €</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Revenus totaux</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($revenue['total'], 2); ?> €</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <!-- Liste des abonnements -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Abonnements disponibles</h6>
                            <a href="<?php echo BASE_URL; ?>subscription/users" class="btn btn-sm btn-info">
                                <i class="fas fa-users me-2"></i>Voir les utilisateurs abonnés
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="subscriptionsTable" width="100%" cellspacing="0">
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
                                                    <a href="<?php echo BASE_URL; ?>subscription/update/<?php echo $subscription['id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $subscription['id']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>

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
                </div>
            </div>
        </div>

        <?php require_once FRONTEND_PATH . '/views/admin/templates/footer.php'; ?>