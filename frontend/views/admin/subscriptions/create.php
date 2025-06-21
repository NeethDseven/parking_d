<?php
$title = 'Nouvel abonnement - ' . APP_NAME;
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
                <h1>Nouvel abonnement</h1>
                <a href="<?php echo BASE_URL; ?>subscription/admin" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                </a>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Détails de l'abonnement</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>subscription/create" method="post">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom de l'abonnement *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="price" class="form-label">Prix (€) *</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="duration_days" class="form-label">Durée (jours) *</label>
                                <input type="number" class="form-control" id="duration_days" name="duration_days" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <label for="free_minutes" class="form-label">Minutes gratuites</label>
                                <input type="number" class="form-control" id="free_minutes" name="free_minutes" min="0" value="0">
                            </div>
                            <div class="col-md-4">
                                <label for="discount_percent" class="form-label">Réduction (%)</label>
                                <input type="number" class="form-control" id="discount_percent" name="discount_percent" min="0" max="100" step="0.01" value="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">Actif</label>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Créer l'abonnement</button>
                            <a href="<?php echo BASE_URL; ?>subscription/admin" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php require_once FRONTEND_PATH . '/views/admin/templates/footer.php'; ?>