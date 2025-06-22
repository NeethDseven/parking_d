<!-- Main content -->
<meta name="current-page" content="admin_places">
<div class="content">
    <div class="container-fluid">
        <!-- Container principal uniforme -->
        <div class="admin-page-container">

            <!-- Header de page uniforme -->
            <div class="admin-page-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="admin-page-title">
                            <i class="fas fa-car"></i>
                            Gestion des places
                        </h1>
                        <p class="text-muted mb-0">Gérez les places de parking et leur disponibilité</p>
                    </div>
                    <div class="admin-page-actions">
                        <button type="button" class="admin-btn admin-btn-primary" data-bs-toggle="modal" data-bs-target="#addPlaceModal">
                            <i class="fas fa-plus"></i>
                            Ajouter une place
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

            <?php if (isset($_SESSION['warning'])): ?>
                <div class="admin-content-card">
                    <div class="admin-content-card-body">
                        <div class="dashboard-alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php echo $_SESSION['warning']; ?>
                            <div class="mt-3">
                                <a href="<?php echo BASE_URL; ?>admin/deletePlace/<?php echo $_SESSION['confirm_delete']['id']; ?>?force=1" class="admin-btn admin-btn-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Oui, supprimer tout
                                </a>
                                <a href="<?php echo BASE_URL; ?>admin/places" class="admin-btn admin-btn-secondary">
                                    Annuler
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                unset($_SESSION['warning']);
                unset($_SESSION['confirm_delete']);
                ?>
            <?php endif; ?>

            <!-- Stats Cards style uniforme -->
            <div class="dashboard-stats-row">
                <div class="dashboard-stat-card primary">
                    <div class="dashboard-stat-header">
                        <div class="dashboard-stat-content">
                            <h6>Places totales</h6>
                            <h2><?php echo $stats['total']; ?></h2>
                            <small>Toutes les places</small>
                        </div>
                        <div class="dashboard-stat-icon primary">
                            <i class="fas fa-car"></i>
                        </div>
                    </div>
                    <div class="dashboard-stat-footer">
                        <a href="#placesList">
                            <i class="fas fa-arrow-right"></i>
                            Voir la liste
                        </a>
                    </div>
                </div>

                <div class="dashboard-stat-card success">
                    <div class="dashboard-stat-header">
                        <div class="dashboard-stat-content">
                            <h6>Places libres</h6>
                            <h2><?php echo isset($stats['libre']) ? $stats['libre'] : 0; ?></h2>
                            <small>Disponibles maintenant</small>
                        </div>
                        <div class="dashboard-stat-icon success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="dashboard-stat-footer">
                        <a href="?status=libre">
                            <i class="fas fa-arrow-right"></i>
                            Voir libres
                        </a>
                    </div>
                </div>

                <div class="dashboard-stat-card warning">
                    <div class="dashboard-stat-header">
                        <div class="dashboard-stat-content">
                            <h6>Places occupées</h6>
                            <h2><?php echo isset($stats['occupe']) ? $stats['occupe'] : 0; ?></h2>
                            <small>En cours d'utilisation</small>
                        </div>
                        <div class="dashboard-stat-icon warning">
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                    <div class="dashboard-stat-footer">
                        <a href="?status=occupe">
                            <i class="fas fa-arrow-right"></i>
                            Voir occupées
                        </a>
                    </div>
                </div>

                <div class="dashboard-stat-card info">
                    <div class="dashboard-stat-header">
                        <div class="dashboard-stat-content">
                            <h6>En maintenance</h6>
                            <h2><?php echo isset($stats['maintenance']) ? $stats['maintenance'] : 0; ?></h2>
                            <small>Hors service</small>
                        </div>
                        <div class="dashboard-stat-icon info">
                            <i class="fas fa-tools"></i>
                        </div>
                    </div>
                    <div class="dashboard-stat-footer">
                        <a href="?status=maintenance">
                            <i class="fas fa-arrow-right"></i>
                            Voir maintenance
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filtres style uniforme -->
            <div class="admin-content-card">
                <div class="admin-content-card-header">
                    <h3 class="admin-content-card-title">
                        <i class="fas fa-filter me-2"></i>
                        Filtres et recherche
                    </h3>
                </div>
                <div class="admin-content-card-body">
                    <form method="GET" action="<?php echo BASE_URL; ?>admin/places" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="filterType" class="form-label">Type de place</label>
                            <select id="filterType" name="type" class="form-select">
                                <option value="">Tous les types</option>
                                <option value="standard" <?php echo isset($_GET['type']) && $_GET['type'] == 'standard' ? 'selected' : ''; ?>>Standard</option>
                                <option value="handicape" <?php echo isset($_GET['type']) && $_GET['type'] == 'handicape' ? 'selected' : ''; ?>>Handicapé</option>
                                <option value="electrique" <?php echo isset($_GET['type']) && $_GET['type'] == 'electrique' ? 'selected' : ''; ?>>Électrique</option>
                                <option value="moto/scooter" <?php echo isset($_GET['type']) && $_GET['type'] == 'moto/scooter' ? 'selected' : ''; ?>>Moto/Scooter</option>
                                <option value="velo" <?php echo isset($_GET['type']) && $_GET['type'] == 'velo' ? 'selected' : ''; ?>>Vélo</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="filterStatus" class="form-label">Statut</label>
                            <select id="filterStatus" name="status" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="libre" <?php echo isset($_GET['status']) && $_GET['status'] == 'libre' ? 'selected' : ''; ?>>Libre</option>
                                <option value="occupe" <?php echo isset($_GET['status']) && $_GET['status'] == 'occupe' ? 'selected' : ''; ?>>Occupé</option>
                                <option value="maintenance" <?php echo isset($_GET['status']) && $_GET['status'] == 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="admin-btn admin-btn-primary w-100">
                                <i class="fas fa-filter"></i>
                                Filtrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des places style uniforme -->
            <div class="admin-content-card" id="placesList">
                <div class="admin-content-card-header">
                    <h3 class="admin-content-card-title">
                        <i class="fas fa-list me-2"></i>
                        Liste des places
                    </h3>
                    <div>
                        <a href="<?php echo BASE_URL; ?>admin/exportPlaces" class="admin-btn admin-btn-outline admin-btn-sm">
                            <i class="fas fa-download"></i>
                            Exporter
                        </a>
                    </div>
                </div>
                <div class="admin-content-card-body">
                    <div class="admin-table-wrapper">
                        <table class="admin-table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Numéro</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($places) && !empty($places)): ?>
                                <?php foreach ($places as $place): ?>
                                    <tr>
                                        <td data-sort="<?php echo $place['id']; ?>"><?php echo $place['id']; ?></td>
                                        <td data-sort="<?php echo htmlspecialchars($place['numero']); ?>"><?php echo htmlspecialchars($place['numero']); ?></td>
                                        <td data-sort="<?php echo $place['type']; ?>">
                                            <?php switch ($place['type']) {
                                                case 'standard': ?>
                                                    <span class="badge bg-secondary">Standard</span>
                                                <?php break;
                                                case 'handicape': ?>
                                                    <span class="badge bg-primary"><i class="fas fa-wheelchair me-1"></i> PMR</span>
                                                <?php break;
                                                case 'electrique': ?>
                                                    <span class="badge bg-success"><i class="fas fa-charging-station me-1"></i> Électrique</span> <?php break;
                                                                                                                                                case 'moto/scooter': ?>
                                                    <span class="badge bg-warning"><i class="fas fa-motorcycle me-1"></i> Moto/Scooter</span>
                                                <?php break;
                                                                                                                                                case 'velo': ?>
                                                    <span class="badge bg-info"><i class="fas fa-bicycle me-1"></i> Vélo</span>
                                                <?php break;
                                                                                                                                                default: ?>
                                                    <span class="badge bg-dark"><?= !empty($place['type']) ? ucfirst(htmlspecialchars($place['type'])) : 'Non défini' ?></span>
                                            <?php } ?>
                                        </td>
                                        <td data-sort="<?php echo $place['status']; ?>">
                                            <?php if ($place['status'] === 'libre'): ?>
                                                <span class="badge bg-success">Libre</span>
                                            <?php elseif ($place['status'] === 'occupe'): ?>
                                                <span class="badge bg-danger">Occupé</span>
                                            <?php elseif ($place['status'] === 'maintenance'): ?>
                                                <span class="badge bg-warning text-dark">Maintenance</span>
                                            <?php endif; ?>
                                        </td>
                                        <td data-sort="<?php echo date('Y-m-d H:i:s', strtotime($place['created_at'])); ?>"><?php echo date('d/m/Y H:i', strtotime($place['created_at'])); ?></td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="<?php echo BASE_URL; ?>admin/editPlace/<?php echo $place['id']; ?>" class="admin-btn-icon edit" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="admin-btn-icon delete delete-place-btn"
                                                    data-id="<?php echo $place['id']; ?>"
                                                    data-numero="<?php echo htmlspecialchars($place['numero']); ?>"
                                                    data-base-url="<?php echo BASE_URL; ?>"
                                                    title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal Edition -->
                                    <div class="modal fade" id="editPlaceModal<?php echo $place['id']; ?>" tabindex="-1" aria-labelledby="editPlaceModalLabel<?php echo $place['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editPlaceModalLabel<?php echo $place['id']; ?>">Modifier la place <?php echo $place['numero']; ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="<?php echo BASE_URL; ?>admin/updatePlace/<?php echo $place['id']; ?>" method="post">
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="editNumero<?php echo $place['id']; ?>" class="form-label">Numéro</label>
                                                            <input type="text" class="form-control" id="editNumero<?php echo $place['id']; ?>" name="numero" value="<?php echo htmlspecialchars($place['numero']); ?>" required>
                                                        </div>
                                                        <div class="mb-3"> <label for="editType<?php echo $place['id']; ?>" class="form-label">Type</label>
                                                            <select class="form-select" id="editType<?php echo $place['id']; ?>" name="type" required>
                                                                <option value="standard" <?php echo $place['type'] === 'standard' ? 'selected' : ''; ?>>Standard</option>
                                                                <option value="handicape" <?php echo $place['type'] === 'handicape' ? 'selected' : ''; ?>>Handicapé</option>
                                                                <option value="electrique" <?php echo $place['type'] === 'electrique' ? 'selected' : ''; ?>>Électrique</option>
                                                                <option value="moto/scooter" <?php echo $place['type'] === 'moto/scooter' ? 'selected' : ''; ?>>Moto/Scooter</option>
                                                                <option value="velo" <?php echo $place['type'] === 'velo' ? 'selected' : ''; ?>>Vélo</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="editStatus<?php echo $place['id']; ?>" class="form-label">Statut</label>
                                                            <select class="form-select" id="editStatus<?php echo $place['id']; ?>" name="status" required>
                                                                <option value="libre" <?php echo $place['status'] === 'libre' ? 'selected' : ''; ?>>Libre</option>
                                                                <option value="occupe" <?php echo $place['status'] === 'occupe' ? 'selected' : ''; ?>>Occupé</option>
                                                                <option value="maintenance" <?php echo $place['status'] === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Suppression -->
                                    <div class="modal fade" id="deletePlaceModal<?php echo $place['id']; ?>" tabindex="-1" aria-labelledby="deletePlaceModalLabel<?php echo $place['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deletePlaceModalLabel<?php echo $place['id']; ?>">Supprimer la place</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Êtes-vous sûr de vouloir supprimer la place <strong><?php echo htmlspecialchars($place['numero']); ?></strong> ?</p>
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        Cette action est irréversible. Les réservations associées à cette place ne seront pas supprimées.
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <a href="<?php echo BASE_URL; ?>admin/deletePlace/<?php echo $place['id']; ?>" class="btn btn-danger">Supprimer</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Changement de statut -->
                                    <div class="modal fade" id="statusPlaceModal<?php echo $place['id']; ?>" tabindex="-1" aria-labelledby="statusPlaceModalLabel<?php echo $place['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="statusPlaceModalLabel<?php echo $place['id']; ?>">Changer le statut de la place <?php echo $place['numero']; ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Statut actuel :
                                                        <?php if ($place['status'] === 'libre'): ?>
                                                            <span class="badge bg-success">Libre</span>
                                                        <?php elseif ($place['status'] === 'occupe'): ?>
                                                            <span class="badge bg-warning">Occupé</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Maintenance</span>
                                                        <?php endif; ?>
                                                    </p>

                                                    <form action="<?php echo BASE_URL; ?>admin/updatePlaceStatus/<?php echo $place['id']; ?>" method="post">
                                                        <div class="mb-3">
                                                            <label for="newStatus<?php echo $place['id']; ?>" class="form-label">Nouveau statut</label>
                                                            <select class="form-select" id="newStatus<?php echo $place['id']; ?>" name="status" required>
                                                                <option value="libre" <?php echo $place['status'] === 'libre' ? 'selected' : ''; ?>>Libre</option>
                                                                <option value="occupe" <?php echo $place['status'] === 'occupe' ? 'selected' : ''; ?>>Occupé</option>
                                                                <option value="maintenance" <?php echo $place['status'] === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="statusNote<?php echo $place['id']; ?>" class="form-label">Note (optionnelle)</label>
                                                            <textarea class="form-control" id="statusNote<?php echo $place['id']; ?>" name="note" rows="3"></textarea>
                                                        </div>

                                                        <div class="alert alert-info">
                                                            <i class="fas fa-info-circle me-2"></i>
                                                            <strong>Note:</strong> Le changement de statut peut affecter les réservations en cours.
                                                        </div>

                                                        <div class="d-grid">
                                                            <button type="submit" class="btn btn-primary">Changer le statut</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">Aucune place trouvée</td>
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
                            <?php
                            // Variables pour les paramètres d'URL
                            $queryParams = [];
                            if (!empty($type)) $queryParams['type'] = $type;
                            if (!empty($status)) $queryParams['status'] = $status;
                            $queryString = !empty($queryParams) ? '?' . http_build_query($queryParams) : '';
                            ?>

                            <!-- Bouton "Précédent" -->
                            <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link ajax-page-link pagination-enhanced" href="<?php echo ($currentPage > 1) ? BASE_URL . 'admin/places/' . ($currentPage - 1) . $queryString : 'javascript:void(0)'; ?>" data-page="<?php echo ($currentPage > 1) ? ($currentPage - 1) : 1; ?>" aria-label="Précédent" data-pagination-applied="true" role="button" tabindex="0" style="cursor: pointer;">
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
                                    <a class="page-link ajax-page-link pagination-enhanced" href="<?php echo BASE_URL; ?>admin/places/1<?php echo $queryString; ?>" data-page="1" data-pagination-applied="true" role="button" tabindex="0" style="cursor: pointer;">1</a>
                                </li>
                                <?php if ($currentPage > $range + 2): ?>
                                    <li class="page-item disabled"><span class="page-link pagination-enhanced" data-pagination-applied="true" role="button" tabindex="0" style="cursor: pointer;">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- Pages autour de la page courante -->
                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                                    <a class="page-link ajax-page-link pagination-enhanced" href="<?php echo BASE_URL; ?>admin/places/<?php echo $i . $queryString; ?>" data-page="<?php echo $i; ?>" data-pagination-applied="true" role="button" tabindex="0" style="cursor: pointer;"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Dernière page toujours visible -->
                            <?php if ($currentPage < $totalPages - $range): ?>
                                <?php if ($currentPage < $totalPages - $range - 1): ?>
                                    <li class="page-item disabled"><span class="page-link pagination-enhanced" data-pagination-applied="true" role="button" tabindex="0" style="cursor: pointer;">...</span></li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link ajax-page-link pagination-enhanced" href="<?php echo BASE_URL; ?>admin/places/<?php echo $totalPages . $queryString; ?>" data-page="<?php echo $totalPages; ?>" data-pagination-applied="true" role="button" tabindex="0" style="cursor: pointer;"><?php echo $totalPages; ?></a>
                                </li>
                            <?php endif; ?>

                            <!-- Bouton "Suivant" -->
                            <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link ajax-page-link pagination-enhanced" href="<?php echo ($currentPage < $totalPages) ? BASE_URL . 'admin/places/' . ($currentPage + 1) . $queryString : 'javascript:void(0)'; ?>" data-page="<?php echo ($currentPage < $totalPages) ? ($currentPage + 1) : $totalPages; ?>" aria-label="Suivant" data-pagination-applied="true" role="button" tabindex="0" style="cursor: pointer;">
                                    <span aria-hidden="true">»</span>
                                    <span class="visually-hidden">Suivant</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
                </div>
            </div>

            <!-- Données pour le service de graphiques -->
            <div id="chart-data" class="d-none"
                data-places-stats="true"
                data-standard="<?php echo isset($typeStats['standard']) ? $typeStats['standard'] : 0; ?>"
                data-handicape="<?php echo isset($typeStats['handicape']) ? $typeStats['handicape'] : 0; ?>"
                data-electrique="<?php echo isset($typeStats['electrique']) ? $typeStats['electrique'] : 0; ?>"
                data-moto="<?php echo isset($typeStats['moto/scooter']) ? $typeStats['moto/scooter'] : 0; ?>"
                data-velo="<?php echo isset($typeStats['velo']) ? $typeStats['velo'] : 0; ?>">
            </div>

        </div> <!-- Fin admin-page-container -->
    </div>
</div>

<!-- Modal Ajouter une place -->
<div class="modal fade" id="addPlaceModal" tabindex="-1" aria-labelledby="addPlaceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPlaceModalLabel">Ajouter une place</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>admin/addPlace" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="addNumero" class="form-label">Numéro <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="addNumero" name="numero" required>
                    </div>
                    <div class="mb-3">
                        <label for="addType" class="form-label">Type</label>
                        <select class="form-select" id="addType" name="type" required>
                            <option value="standard">Standard</option>
                            <option value="handicape">Handicapé</option>
                            <option value="electrique">Électrique</option>
                            <option value="moto">Moto</option>
                            <option value="velo">Vélo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="addStatus" class="form-label">Statut initial</label>
                        <select class="form-select" id="addStatus" name="status" required>
                            <option value="libre">Libre</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>

                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" id="create_multiple" name="create_multiple">
                        <label class="form-check-label" for="create_multiple">
                            Créer plusieurs places en série
                        </label>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>