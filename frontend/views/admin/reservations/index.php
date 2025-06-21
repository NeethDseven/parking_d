<!-- Main content -->
<meta name="current-page" content="admin_reservations">
<div class="content">
    <div class="container-fluid p-4">
        <!-- Mobile toggle -->
        <button class="btn btn-primary d-md-none mb-3" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestion des réservations</h1>
            <div>
                <a href="<?php echo BASE_URL; ?>admin/dashboard" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour au tableau de bord
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
        <div class="card mb-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Liste des réservations</h5>
                    <div class="d-flex gap-2"> <!-- Dropdown de filtrage -->
                        <div class="dropdown">
                            <button class="btn <?php echo (isset($currentStatusFilter) || isset($currentDateFilter)) ? 'btn-primary' : 'btn-outline-secondary'; ?> dropdown-toggle btn-sm" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
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
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
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
                                            <td><?php echo $reservation['id']; ?></td>
                                            <td>
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
                                            <td>
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
                                            <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></td>
                                            <td><?php echo number_format($reservation['montant_total'], 2); ?> €</td>
                                            <td>
                                                <?php                                                // Préparation et inclusion du composant pour afficher le badge de statut
                                                $reservation['statut'] = $reservation['status']; // Compatibilité avec le component
                                                include FRONTEND_PATH . '/views/components/reservation_status_badge.php';
                                                ?>
                                            </td>
                                            <td class="reservation-actions">
                                                <?php include FRONTEND_PATH . '/views/components/reservation_actions.php'; ?>

                                                <!-- Actions spécifiques admin -->
                                                <a href="<?php echo BASE_URL; ?>admin/viewReservation/<?php echo $reservation['id']; ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
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
                <div class="card-footer bg-white">
                    <?php if (isset($totalPages) && $totalPages > 1): ?>
                        <nav aria-label="Pagination">
                            <ul class="pagination justify-content-center mb-0">
                                <?php if ($currentPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo BASE_URL; ?>admin/reservations/<?php echo $currentPage - 1; ?>" aria-label="Précédent">
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
                                    echo '<li class="page-item"><a class="page-link" href="' . BASE_URL . 'admin/reservations/1">1</a></li>';
                                    if ($startPage > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }

                                for ($i = $startPage; $i <= $endPage; $i++):
                                ?>
                                    <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo BASE_URL; ?>admin/reservations/<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor;

                                if ($endPage < $totalPages) {
                                    if ($endPage < $totalPages - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="' . BASE_URL . 'admin/reservations/' . $totalPages . '">' . $totalPages . '</a></li>';
                                }
                                ?>

                                <?php if ($currentPage < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo BASE_URL; ?>admin/reservations/<?php echo $currentPage + 1; ?>" aria-label="Suivant">
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
        </div>
    </div>
</div>