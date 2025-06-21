<?php
$title = 'Utilisateurs abonnés - ' . APP_NAME;
$activeMenu = 'subscriptions'; // Pour mettre en surbrillance l'élément de menu actif
require_once FRONTEND_PATH . '/views/admin/templates/header.php';
?>

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
                <h1>Utilisateurs abonnés</h1>
                <a href="<?php echo BASE_URL; ?>subscription/admin" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la gestion des abonnements
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

            <!-- Liste des utilisateurs abonnés -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Utilisateurs avec abonnement actif</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($users)): ?>
                        <div class="alert alert-info">
                            <p class="mb-0">Aucun utilisateur n'a d'abonnement actif actuellement.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="subscribersTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Abonnement</th>
                                        <th>Prix</th>
                                        <th>Date de début</th>
                                        <th>Date de fin</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo htmlspecialchars($user['subscription_name']); ?></td>
                                            <td><?php echo number_format((float)$user['price'], 2); ?> €</td>
                                            <td><?php echo date('d/m/Y', strtotime($user['start_date'])); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($user['end_date'])); ?></td>
                                            <td>
                                                <?php if ($user['status'] === 'actif'): ?>
                                                    <span class="badge bg-success">Actif</span>
                                                <?php elseif ($user['status'] === 'résilié'): ?>
                                                    <span class="badge bg-warning">Résilié</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Expiré</span>
                                                <?php endif; ?>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Logique de gestion des tables maintenant dans unifiedAdminManager.js -->
        <meta name="current-page" content="admin_subscriptions">

        <?php require_once FRONTEND_PATH . '/views/admin/templates/footer.php'; ?>