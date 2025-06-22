<!-- Sidebar -->
<div class="sidebar">
    <div class="py-4 px-3">
        <h5 class="d-flex align-items-center"> <img src="<?php echo BASE_URL; ?>frontend/assets/img/logo.webp" alt="ParkMe In Logo" height="40" class="me-2 sidebar-logo-filter">
            <span><?php echo APP_NAME; ?></span>
        </h5>
        <hr class="bg-light">
        <p class="text-light">Administration</p>
    </div>

    <ul class="nav flex-column">
        <!-- Section utilisateur en haut -->
        <li class="nav-item">
            <div class="user-section">
                <div class="user-info">
                    <i class="fas fa-user-circle me-2"></i>
                    <span><?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom']) : 'Admin'; ?></span>
                </div>
            </div>
        </li>

        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>admin/dashboard" class="nav-link <?php echo isset($activeMenu) && $activeMenu === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>admin/reservations" class="nav-link <?php echo isset($activeMenu) && $activeMenu === 'reservations' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check me-2"></i> Réservations
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>admin/users" class="nav-link <?php echo isset($activeMenu) && $activeMenu === 'users' ? 'active' : ''; ?>">
                <i class="fas fa-users me-2"></i> Utilisateurs
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>admin/places" class="nav-link <?php echo isset($activeMenu) && $activeMenu === 'places' ? 'active' : ''; ?>">
                <i class="fas fa-car me-2"></i> Places
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>admin/tarifs" class="nav-link <?php echo isset($activeMenu) && $activeMenu === 'tarifs' ? 'active' : ''; ?>">
                <i class="fas fa-tag me-2"></i> Tarifs
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>subscription/admin" class="nav-link <?php echo isset($activeMenu) && $activeMenu === 'subscriptions' ? 'active' : ''; ?>">
                <i class="fas fa-id-card me-2"></i> Abonnements
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>admin/contact" class="nav-link <?php echo isset($activeMenu) && $activeMenu === 'contact' ? 'active' : ''; ?>">
                <i class="fas fa-envelope me-2"></i> Messages de Contact
                <?php
                // Afficher le badge de nouveaux messages si disponible
                if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
                    try {
                        $contactModel = new ContactModel();
                        $newMessagesCount = $contactModel->countMessages('nouveau');
                        if ($newMessagesCount > 0) {
                            echo '<span class="badge bg-danger rounded-pill ms-1">' . $newMessagesCount . '</span>';
                        }
                    } catch (Exception $e) {
                        // Ignorer les erreurs silencieusement
                    }
                }
                ?>
            </a>
        </li>

        <!-- Séparateur -->
        <li class="nav-item mt-4">
            <hr class="sidebar-divider">
        </li>

        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>" class="nav-link text-info">
                <i class="fas fa-home me-2"></i> Retour au site
            </a>
        </li>

        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>auth/logout" class="nav-link text-danger">
                <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
            </a>
        </li>
    </ul>
</div>