<!-- Main content -->
<meta name="current-page" content="admin_reservations">
<div class="content">
    <div class="container-fluid">
        <!-- Container principal uniforme -->
        <div class="admin-page-container">

            <!-- Header de page uniforme -->
            <div class="admin-page-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="admin-page-title">
                            <i class="fas fa-calendar-check"></i>
                            Gestion des réservations
                        </h1>
                        <p class="text-muted mb-0">Gérez et suivez toutes les réservations de parking</p>
                    </div>
                    <div class="admin-page-actions">
                        <a href="<?php echo BASE_URL; ?>admin/dashboard" class="admin-btn admin-btn-outline">
                            <i class="fas fa-arrow-left"></i>
                            Retour au tableau de bord
                        </a>
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
            <!-- Contenu principal style uniforme -->
            <div class="admin-content-card">
                <div class="admin-content-card-header">
                    <h3 class="admin-content-card-title">
                        <i class="fas fa-list me-2"></i>
                        Liste des réservations
                    </h3>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="admin-btn <?php echo (isset($currentStatusFilter) || isset($currentDateFilter)) ? 'admin-btn-primary' : 'admin-btn-outline'; ?> admin-btn-sm dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-filter me-1"></i>
                                <?php
                                if (isset($currentStatusFilter)) {
                                    echo 'Filtré par: ';
                                    switch ($currentStatusFilter) {
                                        case 'en_attente':
                                            echo 'En attente';
                                            break;
                                        case 'confirmée':
                                            echo 'Confirmées';
                                            break;
                                        case 'en_cours':
                                            echo 'En cours';
                                            break;
                                        case 'en_cours_immediat':
                                            echo 'En cours immédiat';
                                            break;
                                        case 'en_attente_paiement':
                                            echo 'En attente paiement';
                                            break;
                                        case 'terminee':
                                            echo 'Terminées';
                                            break;
                                        case 'annulée':
                                            echo 'Annulées';
                                            break;
                                        case 'expirée':
                                            echo 'Expirées';
                                            break;
                                        default:
                                            echo $currentStatusFilter;
                                    }
                                } elseif (isset($currentDateFilter)) {
                                    echo 'Filtré par: ';
                                    switch ($currentDateFilter) {
                                        case 'today':
                                            echo 'Aujourd\'hui';
                                            break;
                                        case 'week':
                                            echo 'Cette semaine';
                                            break;
                                        case 'month':
                                            echo 'Ce mois';
                                            break;
                                        default:
                                            echo $currentDateFilter;
                                    }
                                } else {
                                    echo 'Filtrer';
                                }
                                ?>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                <li><a class="dropdown-item <?php echo (!isset($currentStatusFilter) && !isset($currentDateFilter)) ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/reservations">Toutes</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <h6 class="dropdown-header">Par statut</h6>
                                </li>
                                <li><a class="dropdown-item <?php echo (isset($currentStatusFilter) && $currentStatusFilter === 'en_attente') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/reservations?status=en_attente">En attente</a></li>
                                <li><a class="dropdown-item <?php echo (isset($currentStatusFilter) && $currentStatusFilter === 'confirmée') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/reservations?status=confirmée">Confirmées</a></li>
                                <li><a class="dropdown-item <?php echo (isset($currentStatusFilter) && $currentStatusFilter === 'en_cours') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/reservations?status=en_cours">En cours</a></li>
                                <li><a class="dropdown-item <?php echo (isset($currentStatusFilter) && $currentStatusFilter === 'en_cours_immediat') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/reservations?status=en_cours_immediat">En cours immédiat</a></li>
                                <li><a class="dropdown-item <?php echo (isset($currentStatusFilter) && $currentStatusFilter === 'en_attente_paiement') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/reservations?status=en_attente_paiement">En attente paiement</a></li>
                                <li><a class="dropdown-item <?php echo (isset($currentStatusFilter) && $currentStatusFilter === 'terminee') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/reservations?status=terminee">Terminées</a></li>
                                <li><a class="dropdown-item <?php echo (isset($currentStatusFilter) && $currentStatusFilter === 'annulée') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/reservations?status=annulée">Annulées</a></li>
                                <li><a class="dropdown-item <?php echo (isset($currentStatusFilter) && $currentStatusFilter === 'expirée') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/reservations?status=expirée">Expirées</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <h6 class="dropdown-header">Par période</h6>
                                </li>
                                <li><a class="dropdown-item <?php echo (isset($currentDateFilter) && $currentDateFilter === 'today') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/reservations?date=today">Aujourd'hui</a></li>
                                <li><a class="dropdown-item <?php echo (isset($currentDateFilter) && $currentDateFilter === 'week') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/reservations?date=week">Cette semaine</a></li>
                                <li><a class="dropdown-item <?php echo (isset($currentDateFilter) && $currentDateFilter === 'month') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/reservations?date=month">Ce mois</a></li>
                            </ul>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="admin-content-card-body">
                    <div class="admin-table-wrapper">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Place</th>
                                    <th>Client</th>
                                    <th>Début</th>
                                    <th>Fin</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($reservations) && count($reservations) > 0): ?>
                                    <?php foreach ($reservations as $reservation): ?>
                                        <tr>
                                            <td data-sort="<?php echo $reservation['id']; ?>"><?php echo $reservation['id']; ?></td>
                                            <td data-sort="<?php echo $reservation['place_numero']; ?>">
                                                <span class="badge bg-<?php echo $reservation['place_type'] == 'standard' ? 'secondary' : ($reservation['place_type'] == 'handicape' ? 'primary' : 'success'); ?> me-1">
                                                    <?php
                                                    if ($reservation['place_type'] == 'handicape') {
                                                        echo '<i class="fas fa-wheelchair"></i>';
                                                    } elseif ($reservation['place_type'] == 'electrique') {
                                                        echo '<i class="fas fa-charging-station"></i>';
                                                    } else {
                                                        echo '<i class="fas fa-car"></i>';
                                                    }
                                                    ?>
                                                </span>
                                                <?php echo $reservation['place_numero']; ?>
                                            </td>
                                            <td data-sort="<?php echo $reservation['user_id'] > 0 ? htmlspecialchars($reservation['nom'] . ' ' . $reservation['prenom']) : htmlspecialchars($reservation['guest_name'] ?? 'Invité'); ?>">
                                                <?php if ($reservation['user_id'] > 0): ?>
                                                    <a href="<?php echo BASE_URL; ?>admin/editUser/<?php echo $reservation['user_id']; ?>" data-bs-toggle="tooltip" title="Voir le profil">
                                                        <?php echo htmlspecialchars($reservation['nom'] . ' ' . $reservation['prenom']); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($reservation['guest_email']); ?>">
                                                        <i class="fas fa-user-clock me-1 text-muted"></i> <?php echo htmlspecialchars($reservation['guest_name'] ?? 'Invité'); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-sort="<?php echo date('Y-m-d H:i:s', strtotime($reservation['date_debut'])); ?>"><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                                            <td data-sort="<?php echo date('Y-m-d H:i:s', strtotime($reservation['date_fin'])); ?>"><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></td>
                                            <td data-sort="<?php echo $reservation['montant_total']; ?>"><?php echo number_format($reservation['montant_total'], 2); ?> €</td>
                                            <td>
                                                <?php                                                // Préparation et inclusion du composant pour afficher le badge de statut
                                                $reservation['statut'] = $reservation['status']; // Compatibilité avec le component
                                                include FRONTEND_PATH . '/views/components/reservation_status_badge.php';
                                                ?>
                                            </td>
                                            <td class="reservation-actions">
                                                <!-- Actions spécifiques admin -->
                                                <div class="d-flex gap-1">
                                                    <a href="<?php echo BASE_URL; ?>admin/viewReservation/<?php echo $reservation['id']; ?>" class="admin-btn-icon view" data-bs-toggle="tooltip" title="Voir détails">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    <?php if ($reservation['status'] !== 'annulée' && $reservation['status'] !== 'terminée'): ?>
                                                        <a href="<?php echo BASE_URL; ?>admin/cancelReservation/<?php echo $reservation['id']; ?>"
                                                           class="admin-btn-icon delete"
                                                           data-bs-toggle="tooltip"
                                                           title="Annuler la réservation"
                                                           onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">Aucune réservation trouvée</td>
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
                                <!-- Bouton "Précédent" -->
                                <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link ajax-page-link pagination-enhanced" href="<?php echo ($currentPage > 1) ? BASE_URL . 'admin/reservations/' . ($currentPage - 1) : 'javascript:void(0)'; ?>" data-page="<?php echo ($currentPage > 1) ? ($currentPage - 1) : 1; ?>" aria-label="Précédent" data-pagination-applied="true" role="button" tabindex="0" style="cursor: pointer;">
                                        <span aria-hidden="true">«</span>
                                        <span class="visually-hidden">Précédent</span>
                                    </a>
                                </li>

                                <?php
                                // Logique de pagination améliorée
                                $range = 2;
                                $startPage = max(1, $currentPage - $range);
                                $endPage = min($totalPages, $currentPage + $range);

                                // Première page toujours visible si on est loin
                                if ($currentPage > $range + 1): ?>
                                    <li class="page-item">
                                        <a class="page-link ajax-page-link pagination-enhanced" href="<?php echo BASE_URL; ?>admin/reservations/1" data-page="1" data-pagination-applied="true" role="button" tabindex="0" style="cursor: pointer;">1</a>
                                    </li>
                                    <?php if ($currentPage > $range + 2): ?>
                                        <li class="page-item disabled"><span class="page-link pagination-enhanced" data-pagination-applied="true" role="button" tabindex="0" style="cursor: pointer;">...</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <!-- Pages autour de la page courante -->
                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                                        <a class="page-link ajax-page-link pagination-enhanced" href="<?php echo BASE_URL; ?>admin/reservations/<?php echo $i; ?>" data-page="<?php echo $i; ?>" data-pagination-applied="true" role="button" tabindex="0" style="cursor: pointer;"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <!-- Dernière page toujours visible -->
                                <?php if ($currentPage < $totalPages - $range): ?>
                                    <?php if ($currentPage < $totalPages - $range - 1): ?>
                                        <li class="page-item disabled"><span class="page-link pagination-enhanced" data-pagination-applied="true" role="button" tabindex="0" style="cursor: pointer;">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link ajax-page-link pagination-enhanced" href="<?php echo BASE_URL; ?>admin/reservations/<?php echo $totalPages; ?>" data-page="<?php echo $totalPages; ?>" data-pagination-applied="true" role="button" tabindex="0" style="cursor: pointer;"><?php echo $totalPages; ?></a>
                                    </li>
                                <?php endif; ?>

                                <!-- Bouton "Suivant" -->
                                <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                                    <a class="page-link ajax-page-link pagination-enhanced" href="<?php echo ($currentPage < $totalPages) ? BASE_URL . 'admin/reservations/' . ($currentPage + 1) : 'javascript:void(0)'; ?>" data-page="<?php echo ($currentPage < $totalPages) ? ($currentPage + 1) : $totalPages; ?>" aria-label="Suivant" data-pagination-applied="true" role="button" tabindex="0" style="cursor: pointer;">
                                        <span aria-hidden="true">»</span>
                                        <span class="visually-hidden">Suivant</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>

        </div> <!-- Fin admin-page-container -->
    </div>
</div>