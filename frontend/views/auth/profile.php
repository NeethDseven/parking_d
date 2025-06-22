<div class="container py-5">
    <h1 class="mb-4">Mon profil</h1>

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

    <!-- Navigation par onglets -->
    <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="informations-tab" data-bs-toggle="tab" data-bs-target="#informations" type="button" role="tab" aria-controls="informations" aria-selected="true">
                <i class="fas fa-user-circle me-2"></i>Mes informations
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reservations-tab" data-bs-toggle="tab" data-bs-target="#reservations" type="button" role="tab" aria-controls="reservations" aria-selected="false">
                <i class="fas fa-ticket-alt me-2"></i>Mes réservations
                <?php if (!empty($reservations)): ?>
                    <span class="badge rounded-pill bg-electric-blue"><?php echo count($reservations); ?></span>
                <?php endif; ?>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="subscriptions-tab" data-bs-toggle="tab" data-bs-target="#subscriptions" type="button" role="tab" aria-controls="subscriptions" aria-selected="false">
                <i class="fas fa-id-card me-2"></i>Mes abonnements
                <?php if (isset($user['is_subscribed']) && $user['is_subscribed']): ?>
                    <span class="badge rounded-pill bg-success">Actif</span>
                <?php endif; ?>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab" aria-controls="notifications" aria-selected="false">
                <i class="fas fa-bell me-2"></i>Notifications
                <?php if ($unread_notifications > 0): ?>
                    <span class="badge rounded-pill bg-danger"><?php echo $unread_notifications; ?></span>
                <?php endif; ?>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="profileTabsContent">
        <!-- Onglet Informations -->
        <div class="tab-pane fade show active" id="informations" role="tabpanel" aria-labelledby="informations-tab">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informations personnelles</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>auth/updateProfile" method="post">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" value="<?php echo htmlspecialchars($user['telephone']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                            <input type="password" class="form-control" id="password" name="password" minlength="6">
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6">
                            <div class="invalid-feedback" id="password-match-error">
                                Les mots de passe ne correspondent pas.
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="notifications_active" name="notifications_active" <?php echo $user['notifications_active'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="notifications_active">Recevoir des notifications par email</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Mettre à jour mon profil</button>
                    </form>
                </div>
            </div>
        </div>        <!-- Onglet Réservations -->
        <div class="tab-pane fade" id="reservations" role="tabpanel" aria-labelledby="reservations-tab">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Mes réservations</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($reservations)): ?>
                        <?php
                        // Configuration de la pagination
                        $reservationsPerPage = 7;
                        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $totalReservations = count($reservations);
                        $totalPages = ceil($totalReservations / $reservationsPerPage);
                        $startIndex = ($currentPage - 1) * $reservationsPerPage;
                        $paginatedReservations = array_slice($reservations, $startIndex, $reservationsPerPage);
                        ?>
                        
                        <div class="table-responsive">
                            <table class="table table-striped reservations-table">
                                <thead>
                                    <tr>
                                        <th>Place</th>
                                        <th>Date de début</th>
                                        <th>Date de fin</th>
                                        <th>Statut</th>
                                        <th>Montant</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($paginatedReservations as $reservation): ?>
                                        <tr data-reservation-id="<?php echo $reservation['id']; ?>"
                                            data-status="<?php echo $reservation['status']; ?>"
                                            data-date-debut="<?php echo $reservation['date_debut']; ?>"
                                            data-date-fin="<?php echo $reservation['date_fin'] ?? ''; ?>">
                                            <td><?php echo htmlspecialchars($reservation['place_numero']); ?> (<?php echo ucfirst($reservation['place_type']); ?>)</td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                                            <td>
                                                <?php if ($reservation['status'] === 'en_cours_immediat'): ?>
                                                    <em>En cours...</em>
                                                <?php else: ?>
                                                    <?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?>
                                                <?php endif; ?>
                                            </td>                                            <td>
                                                <?php
                                                // Force l'affichage du badge de statut en incluant directement le composant
                                                include FRONTEND_PATH . '/views/components/reservation_status_badge.php';
                                                ?>
                                            </td>
                                            <td><?php echo number_format($reservation['montant_total'], 2); ?> €</td>                                            <td>
                                                <?php
                                                // Inclusion du composant pour afficher les actions selon le statut
                                                include FRONTEND_PATH . '/views/components/reservation_actions.php';
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Pagination des réservations" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <!-- Bouton Précédent -->
                                    <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="<?php echo $currentPage > 1 ? '?page=' . ($currentPage - 1) . '#reservations' : '#'; ?>" aria-label="Précédent">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>

                                    <!-- Numéros de pages -->
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>#reservations"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <!-- Bouton Suivant -->
                                    <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="<?php echo $currentPage < $totalPages ? '?page=' . ($currentPage + 1) . '#reservations' : '#'; ?>" aria-label="Suivant">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>

                            <!-- Informations sur la pagination -->
                            <div class="text-center mt-3 text-muted">
                                <small>
                                    Affichage de <?php echo $startIndex + 1; ?> à <?php echo min($startIndex + $reservationsPerPage, $totalReservations); ?> 
                                    sur <?php echo $totalReservations; ?> réservations
                                </small>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <p class="mb-0">Vous n'avez pas encore effectué de réservation.</p>
                        </div>
                        <a href="<?php echo BASE_URL; ?>home/places" class="btn btn-primary">Réserver une place</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Onglet Abonnements -->
        <div class="tab-pane fade" id="subscriptions" role="tabpanel" aria-labelledby="subscriptions-tab">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Mes abonnements</h5>
                </div>
                <div class="card-body">
                    <?php 
                    // Inclure le modèle d'abonnement
                    require_once BACKEND_PATH . '/models/SubscriptionModel.php';
                    $subscriptionModel = new SubscriptionModel();
                    $userSubscriptions = $subscriptionModel->getUserActiveSubscriptions($_SESSION['user']['id']);
                    $subscriptionHistory = $subscriptionModel->getUserSubscriptionHistory($_SESSION['user']['id']);
                    ?>

                    <?php if (!empty($userSubscriptions)): ?>
                        <div class="alert alert-success mb-4">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h4 class="alert-heading mb-1">Abonnement actif</h4>
                                    <p class="mb-0">Vous bénéficiez actuellement des avantages de l'abonnement.</p>
                                </div>
                                <div class="ms-auto">
                                    <span class="badge bg-success p-2">Actif</span>
                                </div>
                            </div>
                        </div>

                        <?php foreach ($userSubscriptions as $subscription): ?>
                            <div class="card mb-4 border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><?php echo htmlspecialchars($subscription['name']); ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p><strong>Date de début :</strong> <?php echo date('d/m/Y', strtotime($subscription['start_date'])); ?></p>
                                            <p><strong>Date de fin :</strong> <?php echo date('d/m/Y', strtotime($subscription['end_date'])); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Minutes gratuites :</strong> <?php echo $subscription['free_minutes']; ?> min par réservation</p>
                                            <p><strong>Réduction :</strong> <?php echo $subscription['discount_percent']; ?>% sur toutes les réservations</p>
                                        </div>
                                    </div>
                                    <p><strong>Description :</strong> <?php echo htmlspecialchars($subscription['description']); ?></p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="text-muted">Prix : <?php echo number_format($subscription['price'], 2); ?> €</span>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelSubscriptionModal<?php echo $subscription['id']; ?>">
                                            <i class="fas fa-times-circle me-2"></i>Résilier
                                        </button>
                                    </div>                                </div>
                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>
                        <div class="alert alert-info mb-4">
                            <h4 class="alert-heading">Aucun abonnement actif</h4>
                            <p class="mb-0">Vous n'avez pas d'abonnement actif actuellement. Souscrivez à un abonnement pour bénéficier de nombreux avantages.</p>
                        </div>
                        <a href="<?php echo BASE_URL; ?>subscription" class="btn btn-primary mb-4">
                            <i class="fas fa-ticket-alt me-2"></i>Découvrir nos abonnements
                        </a>
                    <?php endif; ?>

                    <!-- Historique des abonnements -->
                    <?php if (!empty($subscriptionHistory) && count($subscriptionHistory) > count($userSubscriptions)): ?>
                        <h5 class="mt-4 mb-3">Historique des abonnements</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Abonnement</th>
                                        <th>Date de début</th>
                                        <th>Date de fin</th>
                                        <th>Statut</th>
                                        <th>Prix</th>
                                    </tr>
                                </thead>
                                <tbody>                                    <?php foreach ($subscriptionHistory as $history): 
                                        // Ne pas afficher les abonnements actifs déjà montrés au-dessus
                                        if ($history['status'] === 'actif') continue;
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($history['name']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($history['start_date'])); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($history['end_date'])); ?></td>
                                            <td>                                                <?php 
                                                switch($history['status']) {
                                                    case 'actif':
                                                        echo '<span class="badge bg-success">Actif</span>';
                                                        break;
                                                    case 'résilié':
                                                        echo '<span class="badge bg-warning">Résilié</span>';
                                                        break;
                                                    case 'expiré':
                                                        echo '<span class="badge bg-secondary">Expiré</span>';
                                                        break;
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo number_format($history['price'], 2); ?> €</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Onglet Notifications -->
        <div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notifications</h5>
                    <?php if (!empty($notifications)): ?>
                        <a href="<?php echo BASE_URL; ?>auth/markAllNotificationsRead" class="btn btn-sm btn-outline-primary">Tout marquer comme lu</a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (!empty($notifications)): ?>
                        <div class="list-group">
                            <?php foreach ($notifications as $notification): ?>
                                <a href="#" class="list-group-item list-group-item-action notification-item <?php echo !$notification['lu'] ? 'fw-bold' : ''; ?>" data-notification-id="<?php echo $notification['id']; ?>">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($notification['titre']); ?></h5>
                                        <small><?php echo date('d/m/Y H:i', strtotime($notification['created_at'])); ?></small>
                                    </div>
                                    <p class="mb-1"><?php echo nl2br(htmlspecialchars($notification['message'])); ?></p>
                                    <small class="text-muted">
                                        <?php
                                        switch ($notification['type']) {
                                            case 'reservation':
                                                echo '<span class="badge bg-success">Réservation</span>';
                                                break;
                                            case 'paiement':
                                                echo '<span class="badge bg-primary">Paiement</span>';
                                                break;
                                            case 'rappel':
                                                echo '<span class="badge bg-warning text-dark">Rappel</span>';
                                                break;
                                            default:
                                                echo '<span class="badge bg-secondary">Système</span>';
                                        }
                                        ?>
                                    </small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <p class="mb-0">Vous n'avez pas de notification.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Onglet Paramètres -->
            </div>
</div>

<!-- Script de validation pour les formulaires d'annulation -->

<!-- Styles gérés par la structure CSS optimisée -->
<meta name="current-page" content="profile">

<!-- Logique des onglets et modals maintenant dans unifiedReservationManager.js -->

<!-- Section des modals (en dehors des onglets pour éviter les conflits) -->
<?php if (!empty($reservations)): ?>
    <?php foreach ($reservations as $reservation): ?>
        <?php if (isset($reservation['code_sortie']) && !empty($reservation['code_sortie'])): ?>
            <!-- Modal pour afficher les codes -->
            <div class="modal fade" id="codeModal<?php echo $reservation['id']; ?>" tabindex="-1" aria-labelledby="codeModalLabel<?php echo $reservation['id']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="codeModalLabel<?php echo $reservation['id']; ?>">
                                <i class="fas fa-qrcode me-2"></i>Codes d'accès et de sortie
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Code d'entrée</h6>
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="mb-3">
                                                <div id="qr-entry-code-<?php echo $reservation['id']; ?>" class="qr-code-container mb-2">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Génération du QR code...</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="d-block p-2 border rounded bg-light fs-4 font-monospace" id="code-acces-<?php echo $reservation['id']; ?>">
                                                <?php echo isset($reservation['code_acces']) && !empty($reservation['code_acces']) ? $reservation['code_acces'] : 'Génération...'; ?>
                                            </span>
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="copy-acces-<?php echo $reservation['id']; ?>" onclick="copyToClipboard(document.getElementById('code-acces-<?php echo $reservation['id']; ?>').textContent.trim())" style="<?php echo (!isset($reservation['code_acces']) || empty($reservation['code_acces'])) ? 'display:none;' : ''; ?>">
                                                <i class="fas fa-copy me-1"></i>Copier
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0"><i class="fas fa-sign-out-alt me-2"></i>Code de sortie</h6>
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="mb-3">
                                                <div id="qr-exit-code-<?php echo $reservation['id']; ?>" class="qr-code-container mb-2">
                                                    <div class="spinner-border text-success" role="status">
                                                        <span class="visually-hidden">Génération du QR code...</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="d-block p-2 border rounded bg-light fs-4 font-monospace" id="code-sortie-<?php echo $reservation['id']; ?>">
                                                <?php echo isset($reservation['code_sortie']) && !empty($reservation['code_sortie']) ? $reservation['code_sortie'] : 'Génération...'; ?>
                                            </span>
                                            <button type="button" class="btn btn-sm btn-outline-success mt-2" id="copy-sortie-<?php echo $reservation['id']; ?>" onclick="copyToClipboard(document.getElementById('code-sortie-<?php echo $reservation['id']; ?>').textContent.trim())" style="<?php echo (!isset($reservation['code_sortie']) || empty($reservation['code_sortie'])) ? 'display:none;' : ''; ?>">
                                                <i class="fas fa-copy me-1"></i>Copier
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info mt-3 mb-0">
                                <i class="fas fa-info-circle me-2"></i> 
                                Ces codes vous permettent d'entrer et de sortir du parking. Vous pouvez scanner les QR codes ou saisir les codes manuellement.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($userSubscriptions)): ?>
    <?php foreach ($userSubscriptions as $subscription): ?>
        <!-- Modal de confirmation de résiliation -->
        <div class="modal fade" id="cancelSubscriptionModal<?php echo $subscription['id']; ?>" tabindex="-1" aria-labelledby="cancelSubscriptionModalLabel<?php echo $subscription['id']; ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="cancelSubscriptionModalLabel<?php echo $subscription['id']; ?>">
                            <i class="fas fa-exclamation-triangle me-2"></i>Confirmer la résiliation
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Attention : Vous êtes sur le point de résilier votre abonnement <?php echo htmlspecialchars($subscription['name']); ?>.
                        </div>
                        <p>La résiliation sera effective immédiatement, mais vous continuerez à bénéficier des avantages jusqu'au <?php echo date('d/m/Y', strtotime($subscription['end_date'])); ?>.</p>
                        <p>Êtes-vous sûr de vouloir résilier cet abonnement ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <a href="<?php echo BASE_URL; ?>subscription/cancel/<?php echo $subscription['id']; ?>" class="btn btn-danger">Confirmer la résiliation</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>


<!-- Styles chargés automatiquement via profile.css -->

<!-- JavaScript chargé automatiquement via profile.js -->

<!-- Gestion des onglets chargée automatiquement via profile.js -->

<!-- Script de secours pour l'activation des onglets -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 Script de secours pour les onglets du profil');

    // Fonction pour activer un onglet
    function forceActivateTab(tabId) {
        console.log('🎯 Activation forcée de l\'onglet:', tabId);

        // Désactiver tous les onglets
        document.querySelectorAll('#profileTabs .nav-link').forEach(tab => {
            tab.classList.remove('active');
            tab.setAttribute('aria-selected', 'false');
        });

        document.querySelectorAll('.tab-content .tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });

        // Activer l'onglet demandé
        const tabButton = document.getElementById(tabId + '-tab');
        const tabPane = document.getElementById(tabId);

        if (tabButton && tabPane) {
            tabButton.classList.add('active');
            tabButton.setAttribute('aria-selected', 'true');
            tabPane.classList.add('show', 'active');
            console.log('✅ Onglet activé:', tabId);
            return true;
        } else {
            console.warn('⚠️ Onglet non trouvé:', tabId);
            return false;
        }
    }

    // Vérifier l'ancre dans l'URL
    function checkUrlHash() {
        const hash = window.location.hash.substring(1);
        console.log('🔗 Hash détecté:', hash);

        if (hash === 'notifications' || hash === 'reservations' || hash === 'informations' || hash === 'subscriptions') {
            setTimeout(() => {
                forceActivateTab(hash);
            }, 100);
        }
    }

    // Vérifier immédiatement
    checkUrlHash();

    // Vérifier après un délai
    setTimeout(checkUrlHash, 500);

    // Écouter les changements de hash
    window.addEventListener('hashchange', checkUrlHash);

    console.log('✅ Script de secours initialisé');
});
</script>
