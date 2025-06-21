<!-- Main content -->
<meta name="current-page" content="admin_place_add">
<div class="content">
    <div class="container-fluid p-4">
        <!-- Mobile toggle -->
        <button class="btn btn-primary d-md-none mb-3" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin/dashboard">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin/places">Places</a></li>
                <li class="breadcrumb-item active">Ajouter une place</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Ajouter une nouvelle place de parking</h1>
            <div>
                <a href="<?php echo BASE_URL; ?>admin/places" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                </a>
            </div>
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

        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Informations de la place</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>admin/addPlace" method="post">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="numero" class="form-label">Numéro <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="numero" name="numero" value="<?php echo isset($_POST['numero']) ? htmlspecialchars($_POST['numero']) : ''; ?>" required>
                            <small class="form-text text-muted">Exemple: A01, B12, etc.</small>
                        </div>

                        <div class="col-md-6">
                            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="standard" <?php echo (isset($_POST['type']) && $_POST['type'] === 'standard') ? 'selected' : ''; ?>>Standard</option>
                                <option value="handicape" <?php echo (isset($_POST['type']) && $_POST['type'] === 'handicape') ? 'selected' : ''; ?>>Handicapé (PMR)</option>
                                <option value="electrique" <?php echo (isset($_POST['type']) && $_POST['type'] === 'electrique') ? 'selected' : ''; ?>>Électrique</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Statut initial</label>
                        <select class="form-select" id="status" name="status">
                            <option value="libre" <?php echo (!isset($_POST['status']) || $_POST['status'] === 'libre') ? 'selected' : ''; ?>>Libre</option>
                            <option value="maintenance" <?php echo (isset($_POST['status']) && $_POST['status'] === 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="create_multiple" name="create_multiple">
                            <label class="form-check-label" for="create_multiple">
                                Créer plusieurs places en série
                            </label>
                        </div>
                    </div>

                    <div id="multiple_options" class="row mb-3 hidden">
                        <div class="col-md-6">
                            <label for="prefix" class="form-label">Préfixe</label>
                            <input type="text" class="form-control" id="prefix" name="prefix" placeholder="Ex: A">
                            <small class="form-text text-muted">Préfixe pour les numéros des places</small>
                        </div>
                        <div class="col-md-6">
                            <label for="count" class="form-label">Nombre de places</label>
                            <input type="number" class="form-control" id="count" name="count" min="2" max="20" value="5">
                            <small class="form-text text-muted">Nombre de places à créer (max 20)</small>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Ajouter la place
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>