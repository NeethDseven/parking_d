<!-- Main content -->
<meta name="current-page" content="admin_users">
<div class="content">
    <div class="container-fluid">
        <!-- Container principal uniforme -->
        <div class="admin-page-container">

            <!-- Header de page uniforme -->
            <div class="admin-page-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="admin-page-title">
                            <i class="fas fa-users"></i>
                            Gestion des utilisateurs
                        </h1>
                        <p class="text-muted mb-0">Gérez les comptes utilisateurs et leurs permissions</p>
                    </div>
                    <div class="admin-page-actions">
                        <button class="admin-btn admin-btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-user-plus"></i>
                            Ajouter un utilisateur
                        </button>
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
                            <h6>Utilisateurs totaux</h6>
                            <h2><?php echo $totalUsers; ?></h2>
                            <small>Comptes enregistrés</small>
                        </div>
                        <div class="dashboard-stat-icon primary">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="dashboard-stat-footer">
                        <a href="#usersList">
                            <i class="fas fa-arrow-right"></i>
                            Voir la liste
                        </a>
                    </div>
                </div>

                <div class="dashboard-stat-card success">
                    <div class="dashboard-stat-header">
                        <div class="dashboard-stat-content">
                            <h6>Nouveaux ce mois</h6>
                            <h2><?php echo $newUsers; ?></h2>
                            <small>Inscriptions récentes</small>
                        </div>
                        <div class="dashboard-stat-icon success">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                    <div class="dashboard-stat-footer">
                        <a href="#usersList">
                            <i class="fas fa-arrow-right"></i>
                            Voir détails
                        </a>
                    </div>
                </div>

                <div class="dashboard-stat-card info">
                    <div class="dashboard-stat-header">
                        <div class="dashboard-stat-content">
                            <h6>Utilisateurs actifs</h6>
                            <h2><?php echo $activeUsers; ?></h2>
                            <small>Comptes actifs</small>
                        </div>
                        <div class="dashboard-stat-icon info">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                    <div class="dashboard-stat-footer">
                        <a href="#usersList">
                            <i class="fas fa-arrow-right"></i>
                            Voir actifs
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filtres et tri style uniforme -->
            <div class="admin-content-card">
                <div class="admin-content-card-header">
                    <h3 class="admin-content-card-title">
                        <i class="fas fa-filter me-2"></i>
                        Filtres et tri
                    </h3>
                </div>
                <div class="admin-content-card-body">
                    <form method="GET" action="<?php echo BASE_URL; ?>admin/users" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="filterRole" class="form-label">Rôle</label>
                            <select id="filterRole" name="role" class="form-select">
                                <option value="">Tous les rôles</option>
                                <option value="admin" <?php echo isset($_GET['role']) && $_GET['role'] == 'admin' ? 'selected' : ''; ?>>Administrateur</option>
                                <option value="user" <?php echo isset($_GET['role']) && $_GET['role'] == 'user' ? 'selected' : ''; ?>>Utilisateur</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filterStatus" class="form-label">Statut</label>
                            <select id="filterStatus" name="status" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="actif" <?php echo isset($_GET['status']) && $_GET['status'] == 'actif' ? 'selected' : ''; ?>>Actif</option>
                                <option value="inactif" <?php echo isset($_GET['status']) && $_GET['status'] == 'inactif' ? 'selected' : ''; ?>>Inactif</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="sortBy" class="form-label">Trier par</label>
                            <select id="sortBy" name="sort" class="form-select">
                                <option value="created_at_desc" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'created_at_desc' ? 'selected' : ''; ?>>Plus récent</option>
                                <option value="created_at_asc" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'created_at_asc' ? 'selected' : ''; ?>>Plus ancien</option>
                                <option value="nom_asc" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'nom_asc' ? 'selected' : ''; ?>>Nom A-Z</option>
                                <option value="nom_desc" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'nom_desc' ? 'selected' : ''; ?>>Nom Z-A</option>
                                <option value="email_asc" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'email_asc' ? 'selected' : ''; ?>>Email A-Z</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="admin-btn admin-btn-primary w-100">
                                <i class="fas fa-filter"></i>
                                Appliquer
                            </button>
                        </div>
                    </form>
                </div>
            </div>



            <!-- Liste des utilisateurs style uniforme -->
            <div class="admin-content-card" id="usersList">
                <div class="admin-content-card-header">
                    <h3 class="admin-content-card-title">
                        <i class="fas fa-list me-2"></i>
                        Liste des utilisateurs
                    </h3>
                </div>
                <div class="admin-content-card-body">
                    <div class="admin-table-wrapper">
                        <table class="admin-table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th>Date d'inscription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($users) && count($users) > 0): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-light text-dark me-2">
                                                    <span><?php echo strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)); ?></span>
                                                </div>
                                                <div>
                                                    <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-sort="<?php echo htmlspecialchars($user['email']); ?>"><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td data-sort="<?php echo $user['role']; ?>">
                                            <?php if ($user['role'] === 'admin'): ?>
                                                <span class="badge bg-primary">Admin</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Utilisateur</span>
                                            <?php endif; ?>
                                        </td>
                                        <td data-sort="<?php echo $user['status'] ?? 'actif'; ?>">
                                            <?php
                                            $status = $user['status'] ?? 'actif'; // Utiliser 'actif' comme valeur par défaut si status n'est pas défini
                                            if ($status === 'actif'):
                                            ?>
                                                <span class="badge bg-success">Actif</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td data-sort="<?php echo date('Y-m-d', strtotime($user['created_at'])); ?>"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="<?php echo BASE_URL; ?>admin/editUser/<?php echo $user['id']; ?>" class="admin-btn-icon edit" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <?php if ($user['id'] != 1): // Ne pas permettre d'afficher le bouton pour l'admin principal
                                                ?>
                                                    <?php
                                                    $status = $user['status'] ?? 'actif';
                                                    if ($status === 'actif'):
                                                    ?>
                                                        <button type="button" class="admin-btn-icon status change-status-btn"
                                                            data-id="<?php echo $user['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?>"
                                                            data-status="inactif"
                                                            data-bs-toggle="modal" data-bs-target="#changeStatusModal"
                                                            title="Désactiver">
                                                            <i class="fas fa-user-slash"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="admin-btn-icon success change-status-btn"
                                                            data-id="<?php echo $user['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?>"
                                                            data-status="actif"
                                                            data-bs-toggle="modal" data-bs-target="#changeStatusModal"
                                                            title="Activer">
                                                            <i class="fas fa-user-check"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?php if (strpos($user['email'], 'deleted_') === 0): ?>
                                                    <!-- Utilisateur déjà désactivé - ajouter un bouton de suppression forcée -->
                                                    <a href="<?php echo BASE_URL; ?>admin/forceDeleteUser/<?php echo $user['id']; ?>"
                                                        class="admin-btn-icon delete"
                                                        onclick="return confirm('Attention! Cette action supprimera définitivement l\'utilisateur et ne peut pas être annulée. Continuer?');"
                                                        title="Supprimer définitivement">
                                                        <i class="fas fa-skull-crossbones"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <!-- Utilisateur normal - bouton standard de suppression -->
                                                    <button type="button" class="admin-btn-icon delete delete-user-btn"
                                                        data-id="<?php echo $user['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?>"
                                                        title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">Aucun utilisateur trouvé</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer avec pagination style uniforme -->
                <div class="admin-content-card-header" style="border-top: 1px solid #e9ecef; border-bottom: none;">
                <?php if (isset($totalPages) && $totalPages > 1): ?>
                    <nav aria-label="Pagination">
                        <ul class="pagination justify-content-center mb-0">
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo BASE_URL; ?>admin/users/<?php echo $currentPage - 1; ?>" aria-label="Précédent">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" aria-label="Précédent">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($startPage + 4, $totalPages);

                            if ($startPage > 1) {
                                echo '<li class="page-item"><a class="page-link" href="' . BASE_URL . 'admin/users/1">1</a></li>';
                                if ($startPage > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                            }

                            for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                                <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo BASE_URL; ?>admin/users/<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor;

                            if ($endPage < $totalPages) {
                                if ($endPage < $totalPages - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="' . BASE_URL . 'admin/users/' . $totalPages . '">' . $totalPages . '</a></li>';
                            }
                            ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo BASE_URL; ?>admin/users/<?php echo $currentPage + 1; ?>" aria-label="Suivant">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" aria-label="Suivant">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
                </div>
            </div>

        </div> <!-- Fin admin-page-container -->
    </div>
</div>

<!-- Modal Ajouter un utilisateur -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Ajouter un utilisateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>admin/addUser" method="post">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="col">
                            <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="prenom" name="prenom" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone">
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col">
                            <label for="confirm_password" class="form-label">Confirmer <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Rôle</label>
                        <select class="form-select" id="role" name="role">
                            <option value="user" selected>Utilisateur</option>
                            <option value="admin">Administrateur</option>
                        </select>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="notifications_active" name="notifications_active" value="1" checked>
                        <label class="form-check-label" for="notifications_active">Notifications actives</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour changer le statut -->
<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeStatusModalLabel">Changer le statut de l'utilisateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="changeStatusForm" action="" method="post">
                <div class="modal-body">
                    <p id="statusConfirmMessage">Êtes-vous sûr de vouloir changer le statut de cet utilisateur ?</p>
                    <input type="hidden" name="status" id="statusValue" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Confirmer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de suppression d'utilisateur avec informations détaillées -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteUserModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmation de suppression d'utilisateur
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Informations de base de l'utilisateur -->
                <div class="alert alert-warning" role="alert">
                    <h6><i class="fas fa-user me-2"></i>Utilisateur à supprimer :</h6>
                    <p id="deleteUserInfo" class="mb-0"><strong>Chargement...</strong></p>
                </div>

                <!-- Informations sur les réservations -->
                <div id="reservationInfo" style="display: none;">
                    <h6><i class="fas fa-calendar-alt me-2"></i>Réservations associées :</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush" id="reservationStatusList">
                                <!-- Sera rempli dynamiquement -->
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <small><i class="fas fa-info-circle me-1"></i>
                                    La suppression de cet utilisateur entraînera également la suppression de toutes ses réservations et données associées.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message de confirmation -->
                <div class="alert alert-danger mt-3" role="alert">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Attention !</h6>
                    <p class="mb-2">Cette action est <strong>irréversible</strong> et va supprimer :</p>
                    <ul class="mb-2">
                        <li>Le compte utilisateur</li>
                        <li id="reservationDeleteInfo">Toutes les réservations associées</li>
                        <li>Toutes les données liées (notifications, alertes, etc.)</li>
                    </ul>
                    <p class="mb-0"><strong>Êtes-vous absolument certain de vouloir continuer ?</strong></p>
                </div>

                <!-- Checkbox de confirmation -->
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="confirmDeletion">
                    <label class="form-check-label" for="confirmDeletion">
                        Je comprends que cette action est irréversible et je souhaite supprimer définitivement cet utilisateur et toutes ses données.
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                    <i class="fas fa-trash me-2"></i>Supprimer définitivement
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Styles gérés par la structure CSS optimisée -->
<meta name="current-page" content="admin_users">