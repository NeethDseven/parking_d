<!-- Main content -->
<meta name="current-page" content="admin_reservation_view">
<div class="content">
    <div class="container-fluid p-4">
        <!-- Mobile toggle -->
        <button class="btn btn-primary d-md-none mb-3" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin/dashboard">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin/reservations">Réservations</a></li>
                <li class="breadcrumb-item active">Détails réservation #<?php echo $reservation['id']; ?></li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Détails de la réservation #<?php echo $reservation['id']; ?></h1>
            <div>
                <?php if ($reservation['status'] !== 'annulée'): ?>
                    <a href="<?php echo BASE_URL; ?>admin/cancelReservation/<?php echo $reservation['id']; ?>"
                        class="btn btn-danger me-2"
                        data-confirm="Êtes-vous sûr de vouloir annuler cette réservation ?">
                        <i class="fas fa-times me-2"></i>Annuler la réservation
                    </a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>admin/reservations" class="btn btn-outline-secondary">
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

        <div class="row">
            <!-- Informations de réservation -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Informations de réservation</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">ID de réservation</h6>
                                <p><?php echo $reservation['id']; ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">Status</h6>
                                <p>
                                    <span class="badge <?php
                                                        echo $reservation['status'] === 'confirmée' ? 'bg-success' : ($reservation['status'] === 'annulée' ? 'bg-danger' : 'bg-warning');
                                                        ?>">
                                        <?php echo $reservation['status']; ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">Date de début</h6>
                                <p><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">Date de fin</h6>
                                <p><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">Montant total</h6>
                                <p class="fw-bold"><?php echo number_format($reservation['montant_total'], 2); ?> €</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">Date de création</h6>
                                <p><?php echo date('d/m/Y H:i', strtotime($reservation['created_at'])); ?></p>
                            </div>
                            <?php if ($reservation['code_acces']): ?>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted">Code d'accès</h6>
                                    <p class="bg-light p-2 rounded"><?php echo $reservation['code_acces']; ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Informations de paiement -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Informations de paiement</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($payment) && $payment): ?>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted">ID de paiement</h6>
                                    <p><?php echo $payment['id']; ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted">Montant</h6>
                                    <p><?php echo number_format($payment['montant'], 2); ?> €</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted">Mode de paiement</h6>
                                    <p>
                                        <?php if ($payment['mode_paiement'] === 'carte'): ?>
                                            <i class="far fa-credit-card me-1"></i> Carte bancaire
                                        <?php elseif ($payment['mode_paiement'] === 'paypal'): ?>
                                            <i class="fab fa-paypal me-1"></i> PayPal
                                        <?php elseif ($payment['mode_paiement'] === 'virement'): ?>
                                            <i class="fas fa-university me-1"></i> Virement bancaire
                                        <?php else: ?>
                                            Non spécifié
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted">Statut du paiement</h6>
                                    <p>
                                        <span class="badge <?php
                                                            echo $payment['status'] === 'valide' ? 'bg-success' : ($payment['status'] === 'refuse' ? 'bg-danger' : ($payment['status'] === 'annule' ? 'bg-warning' : 'bg-info'));
                                                            ?>">
                                            <?php echo $payment['status']; ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted">Date du paiement</h6>
                                    <p><?php echo date('d/m/Y H:i', strtotime($payment['date_paiement'])); ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i> Aucun paiement associé à cette réservation
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Informations client et place -->
            <div class="col-md-4">
                <!-- Informations client -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Informations client</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($reservation['user_id'] > 0): ?>
                            <p>
                                <a href="<?php echo BASE_URL; ?>admin/editUser/<?php echo $reservation['user_id']; ?>" class="text-decoration-none fw-bold">
                                    <?php echo htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?>
                                </a>
                            </p>
                            <p><i class="fas fa-envelope me-2"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                            <?php if (!empty($user['telephone'])): ?>
                                <p><i class="fas fa-phone me-2"></i> <?php echo htmlspecialchars($user['telephone']); ?></p>
                            <?php endif; ?>
                            <hr>
                            <a href="<?php echo BASE_URL; ?>admin/editUser/<?php echo $reservation['user_id']; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-user me-1"></i> Voir le profil
                            </a>
                        <?php else: ?>
                            <!-- Client invité -->
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-user-clock me-2"></i> Réservation par invité
                            </div>
                            <p><strong>Nom:</strong> <?php echo htmlspecialchars($user['nom']); ?></p>
                            <p><i class="fas fa-envelope me-2"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                            <?php if (!empty($user['telephone'])): ?>
                                <p><i class="fas fa-phone me-2"></i> <?php echo htmlspecialchars($user['telephone']); ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Informations place -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Informations place</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <span class="badge p-2 <?php
                                                        echo $reservation['type'] == 'standard' ? 'bg-secondary' : ($reservation['type'] == 'handicape' ? 'bg-primary' : 'bg-success');
                                                        ?>" class="font-size-15">
                                    <?php
                                    if ($reservation['type'] == 'handicape') {
                                        echo '<i class="fas fa-wheelchair"></i>';
                                    } elseif ($reservation['type'] == 'electrique') {
                                        echo '<i class="fas fa-charging-station"></i>';
                                    } else {
                                        echo '<i class="fas fa-car"></i>';
                                    }
                                    ?>
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-0">Place <?php echo $reservation['numero']; ?></h4>
                                <p class="text-muted mb-0">Type: <?php echo ucfirst($reservation['type']); ?></p>
                            </div>
                        </div>
                        <p>
                            <span class="badge <?php
                                                echo $reservation['place_status'] === 'libre' ? 'bg-success' : ($reservation['place_status'] === 'maintenance' ? 'bg-warning' : 'bg-danger');
                                                ?>">
                                <?php echo $reservation['place_status']; ?>
                            </span>
                        </p>
                        <a href="<?php echo BASE_URL; ?>admin/places" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-th-list me-1"></i> Voir toutes les places
                        </a>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <?php if ($reservation['status'] !== 'annulée'): ?>
                                <a href="<?php echo BASE_URL; ?>admin/cancelReservation/<?php echo $reservation['id']; ?>"
                                    class="btn btn-danger"
                                    data-confirm="Êtes-vous sûr de vouloir annuler cette réservation ?">
                                    <i class="fas fa-times me-2"></i>Annuler la réservation
                                </a>
                            <?php endif; ?>

                            <a href="javascript:window.print();" class="btn btn-outline-dark">
                                <i class="fas fa-print me-2"></i>Imprimer les détails
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>