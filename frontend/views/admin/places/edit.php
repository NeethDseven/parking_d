<!-- Main content -->
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
                <li class="breadcrumb-item active">Modifier une place</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Modifier la place <?php echo htmlspecialchars($place['numero']); ?></h1>
            <div>
                <a href="<?php echo BASE_URL; ?>admin/places" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                </a>
            </div>
        </div>

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
                <form action="<?php echo BASE_URL; ?>admin/updatePlace" method="post">
                    <!-- Champ caché pour l'ID de la place -->
                    <input type="hidden" name="id" value="<?php echo $place['id']; ?>">

                    <div class="mb-3">
                        <label for="numero" class="form-label">Numéro <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="numero" name="numero" value="<?php echo htmlspecialchars($place['numero']); ?>" required>
                        <small class="form-text text-muted">Exemple: A01, B12, etc.</small>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type <span class="text-danger">*</span></label> <select class="form-select" id="type" name="type" required>
                            <option value="standard" <?php echo $place['type'] === 'standard' ? 'selected' : ''; ?>>Standard</option>
                            <option value="handicape" <?php echo $place['type'] === 'handicape' ? 'selected' : ''; ?>>Handicapé (PMR)</option>
                            <option value="electrique" <?php echo $place['type'] === 'electrique' ? 'selected' : ''; ?>>Électrique</option>
                            <option value="moto/scooter" <?php echo $place['type'] === 'moto/scooter' ? 'selected' : ''; ?>>Moto/Scooter</option>
                            <option value="velo" <?php echo $place['type'] === 'velo' ? 'selected' : ''; ?>>Vélo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="libre" <?php echo $place['status'] === 'libre' ? 'selected' : ''; ?>>Libre</option>
                            <option value="occupe" <?php echo $place['status'] === 'occupe' ? 'selected' : ''; ?>>Occupé</option>
                            <option value="maintenance" <?php echo $place['status'] === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>