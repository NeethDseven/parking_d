<!DOCTYPE html>
<?php
// Helper pour éviter les erreurs BASE_URL
function getBaseUrl()
{
    return defined('BASE_URL') ? BASE_URL : '/projet/parking_d/';
}
?>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?php echo getBaseUrl(); ?>">
    <meta name="base-url" content="<?php echo getBaseUrl(); ?>">
    <meta name="description" content="<?php echo isset($description) ? $description : 'ParkMe In - Votre solution de stationnement intelligente'; ?>">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- Data-page pour chargement conditionnel des scripts -->
    <meta name="current-page" content="<?php echo isset($active_page) ? $active_page : 'home'; ?>">

    <?php if (isset($_SESSION['user'])): ?>
        <meta name="user-data" content="<?php echo htmlspecialchars(json_encode([
                                            'id' => $_SESSION['user']['id'],
                                            'email' => $_SESSION['user']['email'],
                                            'nom' => $_SESSION['user']['nom'],
                                            'prenom' => $_SESSION['user']['prenom'],
                                            'role' => $_SESSION['user']['role']
                                        ])); ?>">
    <?php endif; ?>    <title><?php echo isset($title) ? $title : 'ParkMe In'; ?></title>

    <!-- Système de journalisation chargé en premier -->
    <script src="<?php echo getBaseUrl(); ?>frontend/assets/js/core/logger.js?v=<?php echo time(); ?>"></script>
    <!-- Système de chargement principal -->
    <script src="<?php echo getBaseUrl(); ?>frontend/assets/js/core/app.js?v=<?php echo time(); ?>"></script>
    <!-- Script de navigation pour les liens et notifications -->
    <script src="<?php echo getBaseUrl(); ?>frontend/assets/js/navigation.js?v=<?php echo time(); ?>"></script>

    <link rel="icon" type="image/webp" href="<?php echo getBaseUrl(); ?>frontend/assets/img/logo.webp">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Styles principaux -->
    <link href="<?php echo getBaseUrl(); ?>frontend/assets/css/variables.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="<?php echo getBaseUrl(); ?>frontend/assets/css/components.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="<?php echo getBaseUrl(); ?>frontend/assets/css/app.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="<?php echo getBaseUrl(); ?>frontend/assets/css/pages.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="<?php echo getBaseUrl(); ?>frontend/assets/css/profile.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>

<body data-page="<?php echo isset($active_page) ? $active_page : 'home'; ?>"
      data-user-logged-in="<?php echo isset($_SESSION['user']) ? 'true' : 'false'; ?>">
    <!-- Navigation principale -->
    <nav class="navbar navbar-expand-lg navbar-dark"
        class="admin-navbar
                --bs-navbar-color: rgba(255, 255, 255, 0.85);
                --bs-navbar-hover-color: var(--accent-color);
                --bs-navbar-active-color: var(--accent-color);">
        <div class="container-fluid px-3">
            <!-- Logo à gauche -->
            <a class="navbar-brand d-flex align-items-center" href="<?php echo getBaseUrl(); ?>">
                <div class="logo-container me-2">
                    <img src="<?php echo getBaseUrl(); ?>frontend/assets/img/logo.webp" alt="ParkMe In Logo" class="logo-img logo-filter">
                </div>
                <span>Park<strong>Me</strong> <span class="brand-accent">In</span></span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Menu principal étendu -->
                <ul class="navbar-nav me-auto flex-grow-1 justify-content-evenly">
                    <li class="nav-item">
                        <a class="nav-link <?php echo empty($_GET['url']) ? 'active' : ''; ?>" href="<?php echo getBaseUrl(); ?>">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isset($_GET['url']) && $_GET['url'] == 'home/places' ? 'active' : ''; ?>" href="<?php echo getBaseUrl(); ?>home/places">Places disponibles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isset($_GET['url']) && $_GET['url'] == 'subscription' ? 'active' : ''; ?>" href="<?php echo getBaseUrl(); ?>subscription">Abonnements</a>
                    </li>                    <?php if (isset($_SESSION['user'])): ?> <li class="nav-item">
                            <a class="nav-link" href="<?php echo getBaseUrl(); ?>auth/profile#reservations">Mes réservations</a>
                        </li>

                        <!-- Affichage réservation immédiate active -->
                        <?php
                        $activeReservationData = getActiveImmediateReservation();
                        if ($activeReservationData['reservation']):
                            $activeImmediateReservation = $activeReservationData['reservation'];
                            $placeActive = $activeReservationData['place'];
                            $tarifHoraireActive = $activeReservationData['tarifHoraire'];
                        ?>
                            <li class="nav-item dropdown">                                <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-start-time="<?php echo $activeImmediateReservation['date_debut']; ?>" data-tarif="<?php echo $tarifHoraireActive; ?>">
                                    <i class="fas fa-stopwatch text-warning me-1"></i>
                                    Réservation en cours <span id="header-timer" class="badge bg-warning text-dark" data-target="duration">00:00:00</span>
                                </a><div class="dropdown-menu p-3 dropdown-menu-wide" data-start-time="<?php echo $activeImmediateReservation['date_debut']; ?>" data-tarif="<?php echo $tarifHoraireActive; ?>">
                                    <h6 class="dropdown-header">Réservation Immédiate</h6>
                                    <p class="mb-1"><strong>Place:</strong> <?php echo $placeActive['numero']; ?> (<?php echo ucfirst($placeActive['type']); ?>)</p>
                                    <p class="mb-1"><strong>Depuis:</strong> <?php echo date('d/m/Y H:i', strtotime($activeImmediateReservation['date_debut'])); ?></p>                                    <div class="d-flex justify-content-between mb-1">
                                        <span><strong>Temps écoulé:</strong></span>
                                        <span id="header-duration" data-target="duration">--:--:--</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span><strong>Coût estimé:</strong></span>
                                        <span id="header-cost" data-target="cost">0.00 €</span>
                                    </div><div class="d-grid">                                        <a href="<?php echo getBaseUrl(); ?>reservation/immediate/<?php echo $activeImmediateReservation['id']; ?>" class="btn btn-info btn-sm mb-2">
                                            <i class="fas fa-info-circle"></i> Détails
                                        </a>
                                        <form action="<?php echo getBaseUrl(); ?>reservation/endImmediate" method="post">
                                            <input type="hidden" name="reservation_id" value="<?php echo $activeImmediateReservation['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Êtes-vous sûr de vouloir terminer cette réservation ? Vous devrez procéder au paiement pour quitter le parking.')">
                                                <i class="fas fa-stop-circle"></i> Terminer et payer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        <?php endif; ?>

                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo isset($_GET['url']) && $_GET['url'] == 'admin/dashboard' ? 'active' : ''; ?>" href="<?php echo getBaseUrl(); ?>admin/dashboard">Administration</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isset($_GET['url']) && $_GET['url'] == 'home/faq' ? 'active' : ''; ?>" href="<?php echo getBaseUrl(); ?>home/faq">FAQ</a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user'])): ?>
                        <!-- Notifications -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                <?php if (isset($unread_notifications) && $unread_notifications > 0): ?>
                                    <span class="badge bg-danger rounded-pill"><?php echo $unread_notifications; ?></span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                                <li>
                                    <h6 class="dropdown-header">Notifications</h6>
                                </li>
                                <?php if (isset($notifications) && !empty($notifications)): ?>
                                    <?php foreach (array_slice($notifications, 0, 5) as $notification): ?>                                        <li>
                                            <a class="dropdown-item notification-item <?php echo $notification['lu'] ? '' : 'fw-bold'; ?>" href="<?php echo getBaseUrl(); ?>auth/profile#notifications" data-notification-id="<?php echo $notification['id']; ?>">
                                                <small class="d-block text-truncate text-truncate-250">
                                                    <?php echo htmlspecialchars($notification['titre']); ?>
                                                </small>
                                                <small class="text-muted"><?php echo date('d/m H:i', strtotime($notification['created_at'])); ?></small>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item text-center" href="<?php echo getBaseUrl(); ?>auth/profile#notifications">Voir toutes</a></li>
                                <?php else: ?>
                                    <li><span class="dropdown-item">Aucune notification</span></li>
                                <?php endif; ?>
                            </ul>
                        </li>

                        <!-- User Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i>
                                <?php echo htmlspecialchars($_SESSION['user']['prenom']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="<?php echo getBaseUrl(); ?>auth/profile"><i class="fas fa-user-circle me-2"></i>Mon profil</a></li>
                                <li><a class="dropdown-item" href="<?php echo getBaseUrl(); ?>auth/profile#reservations"><i class="fas fa-ticket-alt me-2"></i>Mes réservations</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?php echo getBaseUrl(); ?>auth/logout"><i class="fas fa-sign-out-alt me-2"></i> Déconnexion</a></li>
                            </ul>
                        </li> <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isset($_GET['url']) && $_GET['url'] == 'home/reservationTracking' ? 'active' : ''; ?>" href="<?php echo getBaseUrl(); ?>home/reservationTracking">
                                <i class="fas fa-ticket-alt me-1"></i> Réservation
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isset($_GET['url']) && $_GET['url'] == 'auth/login' ? 'active' : ''; ?>" href="<?php echo getBaseUrl(); ?>auth/login">
                                <i class="fas fa-sign-in-alt me-1"></i> Connexion
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isset($_GET['url']) && $_GET['url'] == 'auth/register' ? 'active' : ''; ?>" href="<?php echo getBaseUrl(); ?>auth/register">
                                <i class="fas fa-user-plus me-1"></i> Inscription
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>    <!-- Bouton Retour à l'accueil -->

    <!-- Flash Messages -->
    <div class="container mt-3">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $_SESSION['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Content Container -->
    <main class="py-4">
        <!-- Script de navigation chargé automatiquement via navigation.js -->