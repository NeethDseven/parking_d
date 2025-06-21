<!-- Main content -->
<meta name="current-page" content="admin_user_edit">
<div class="content">
    <div class="container-fluid p-4">
        <!-- Mobile toggle -->
        <button class="btn btn-primary d-md-none mb-3" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin/dashboard">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin/users">Utilisateurs</a></li>
                <li class="breadcrumb-item active">Modifier un utilisateur</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Modifier l'utilisateur <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h1>
            <div>
                <a href="<?php echo BASE_URL; ?>admin/users" class="btn btn-outline-secondary">
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
                <h5 class="mb-0">Informations utilisateur</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>admin/updateUser" method="post">
                    <!-- Champ caché pour l'ID de l'utilisateur -->
                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        <small class="text-muted">L'email sera utilisé comme identifiant de connexion</small>
                    </div>

                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone" value="<?php echo htmlspecialchars($user['telephone'] ?? ''); ?>">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="text-muted">Laissez vide pour ne pas changer le mot de passe</small>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label">Confirmer mot de passe</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Rôle</label>
                        <select class="form-select" id="role" name="role">
                            <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>Utilisateur</option>
                            <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Administrateur</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="actif" <?php echo ($user['status'] === 'actif') ? 'selected' : ''; ?>>Actif</option>
                            <option value="inactif" <?php echo ($user['status'] === 'inactif') ? 'selected' : ''; ?>>Inactif</option>
                        </select>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="notifications_active" name="notifications_active" value="1" <?php echo ($user['notifications_active'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="notifications_active">Notifications actives</label>
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