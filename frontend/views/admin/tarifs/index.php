<!-- Main content -->
<meta name="current-page" content="admin_tarifs">
<div class="content">
    <div class="container-fluid p-4">
        <!-- Mobile toggle -->
        <button class="btn btn-primary d-md-none mb-3" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestion des tarifs</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTarifModal">
                <i class="fas fa-plus"></i> Ajouter un tarif
            </button>
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

        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Liste des tarifs</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Type de place</th>
                                <th>Prix à l'heure</th>
                                <th>Prix à la journée</th>
                                <th>Prix au mois</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($tarifs) && count($tarifs) > 0): ?>
                                <?php foreach ($tarifs as $tarif): ?>
                                    <tr>
                                        <td><?php echo $tarif['id']; ?></td>
                                        <td>
                                            <?php echo getTypePlaceBadge($tarif['type_place']); ?>
                                        </td>
                                        <td><?php echo number_format($tarif['prix_heure'], 2); ?> €</td>
                                        <td><?php echo number_format($tarif['prix_journee'], 2); ?> €</td>
                                        <td><?php echo number_format($tarif['prix_mois'], 2); ?> €</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info edit-tarif"
                                                    data-id="<?php echo $tarif['id']; ?>"
                                                    data-type="<?php echo $tarif['type_place']; ?>"
                                                    data-prix-heure="<?php echo $tarif['prix_heure']; ?>"
                                                    data-prix-journee="<?php echo $tarif['prix_journee']; ?>"
                                                    data-prix-mois="<?php echo $tarif['prix_mois']; ?>"
                                                    data-free-minutes="<?php echo $tarif['free_minutes'] ?? 0; ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editTarifModal">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger delete-tarif"
                                                    data-id="<?php echo $tarif['id']; ?>"
                                                    data-type="<?php echo $tarif['type_place']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">Aucun tarif trouvé</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Historique des modifications -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Historique des modifications</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Utilisateur</th>
                                        <th>Type de place</th>
                                        <th>Action</th>
                                        <th>Ancien prix</th>
                                        <th>Nouveau prix</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($historique) && count($historique) > 0): ?>
                                        <?php foreach ($historique as $item): ?>
                                            <?php
                                            // Parser la description pour extraire les informations
                                            $actionText = '';
                                            $ancienPrix = '-';
                                            $nouveauPrix = '-';
                                            $typePlaceDisplay = '';

                                            if (strpos($item['description'], 'Type:') !== false) {
                                                preg_match('/Type:\s*([^,]+)/', $item['description'], $typeMatches);
                                                if (isset($typeMatches[1])) {
                                                    $typePlace = trim($typeMatches[1]);
                                                    // Extraire le type avant la flèche pour les modifications
                                                    if (strpos($typePlace, ' -> ') !== false) {
                                                        $typePlace = explode(' -> ', $typePlace)[0];
                                                    }
                                                    $typePlaceDisplay = $typePlace;
                                                }
                                            }

                                            if ($item['action'] === 'modification_tarif') {
                                                $actionText = 'Modification';
                                                // Extraire les prix avant et après
                                                if (preg_match('/Prix\/h:\s*([\d.]+)€\s*->\s*([\d.]+)€/', $item['description'], $matches)) {
                                                    $ancienPrix = $matches[1];
                                                    $nouveauPrix = $matches[2];
                                                }
                                            } elseif ($item['action'] === 'ajout_tarif') {
                                                $actionText = 'Ajout';
                                                if (preg_match('/Prix\/h:\s*([\d.]+)€/', $item['description'], $matches)) {
                                                    $nouveauPrix = $matches[1];
                                                }
                                            } elseif ($item['action'] === 'suppression_tarif') {
                                                $actionText = 'Suppression';
                                                if (preg_match('/Prix\/h:\s*([\d.]+)€/', $item['description'], $matches)) {
                                                    $ancienPrix = $matches[1];
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></td>
                                                <td><?php echo htmlspecialchars($item['prenom'] . ' ' . $item['nom']); ?></td>
                                                <td>
                                                    <?php echo getTypePlaceBadge($typePlaceDisplay); ?>
                                                </td>
                                                <td>
                                                    <?php if ($item['action'] === 'modification_tarif'): ?>
                                                        <span class="badge bg-warning">Modification</span>
                                                    <?php elseif ($item['action'] === 'ajout_tarif'): ?>
                                                        <span class="badge bg-success">Ajout</span>
                                                    <?php elseif ($item['action'] === 'suppression_tarif'): ?>
                                                        <span class="badge bg-danger">Suppression</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $ancienPrix !== '-' ? number_format($ancienPrix, 2) . ' €' : '-'; ?></td>
                                                <td><?php echo $nouveauPrix !== '-' ? number_format($nouveauPrix, 2) . ' €' : '-'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4">Aucun historique disponible</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'ajout de tarif -->
<div class="modal fade" id="addTarifModal" tabindex="-1" aria-labelledby="addTarifModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo BASE_URL; ?>admin/addTarif" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTarifModalLabel">Ajouter un nouveau tarif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_type_place" class="form-label">Type de place</label>
                        <select class="form-select" id="add_type_place" name="type_place_select">
                            <option value="">Sélectionner un type</option>
                            <option value="standard">Standard</option>
                            <option value="handicape">Handicapé</option>
                            <option value="electrique">Électrique</option>
                            <option value="moto/scooter">Moto/Scooter</option>
                            <option value="velo">Vélo</option>
                            <option value="autre">🆕 Créer un nouveau type</option>
                        </select>
                    </div>

                    <!-- Champ pour nouveau type de place -->
                    <div class="mb-3" id="add_custom_type_container" style="display: none;">
                        <label for="add_custom_type" class="form-label">
                            <i class="fas fa-plus-circle text-primary"></i> Nouveau type de place
                        </label>
                        <input type="text" class="form-control" id="add_custom_type" name="custom_type_place"
                            placeholder="Ex: Premium, VIP, Camion, etc.">
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Créez un nouveau type personnalisé (évitez les espaces, utilisez des tirets si nécessaire)
                        </small>
                    </div>

                    <!-- Champ caché pour le type final -->
                    <input type="hidden" id="add_final_type" name="type_place">

                    <div class="mb-3">
                        <label for="add_free_minutes" class="form-label">Minutes gratuites</label>
                        <input type="number" class="form-control" id="add_free_minutes" name="free_minutes" min="0" value="0">
                    </div>
                    <div class="mb-3">
                        <label for="add_prix_heure" class="form-label">Prix à l'heure (€)</label>
                        <input type="number" class="form-control" id="add_prix_heure" name="prix_heure" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_prix_journee" class="form-label">Prix à la journée (€)</label>
                        <input type="number" class="form-control" id="add_prix_journee" name="prix_journee" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_prix_mois" class="form-label">Prix au mois (€)</label>
                        <input type="number" class="form-control" id="add_prix_mois" name="prix_mois" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Ajouter le tarif</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de modification de tarif -->
<div class="modal fade" id="editTarifModal" tabindex="-1" aria-labelledby="editTarifModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo BASE_URL; ?>admin/updateTarif" method="POST">
                <input type="hidden" id="edit_tarif_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTarifModalLabel">Modifier le tarif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_type_place" class="form-label">Type de place</label>
                        <select class="form-select" id="edit_type_place" name="type_place_select">
                            <option value="standard">Standard</option>
                            <option value="handicape">Handicapé</option>
                            <option value="electrique">Électrique</option>
                            <option value="moto/scooter">Moto/Scooter</option>
                            <option value="velo">Vélo</option>
                            <option value="autre">🆕 Créer un nouveau type</option>
                        </select>
                    </div>

                    <!-- Champ pour nouveau type de place -->
                    <div class="mb-3" id="edit_custom_type_container" style="display: none;">
                        <label for="edit_custom_type" class="form-label">
                            <i class="fas fa-plus-circle text-primary"></i> Nouveau type de place
                        </label>
                        <input type="text" class="form-control" id="edit_custom_type" name="custom_type_place"
                            placeholder="Ex: Premium, VIP, Camion, etc.">
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Créez un nouveau type personnalisé (évitez les espaces, utilisez des tirets si nécessaire)
                        </small>
                    </div>

                    <!-- Champ caché pour le type final -->
                    <input type="hidden" id="edit_final_type" name="type_place">

                    <div class="mb-3">
                        <label for="edit_free_minutes" class="form-label">Minutes gratuites</label>
                        <input type="number" class="form-control" id="edit_free_minutes" name="free_minutes" min="0">
                    </div>
                    <div class="mb-3">
                        <label for="edit_prix_heure" class="form-label">Prix à l'heure (€)</label>
                        <input type="number" class="form-control" id="edit_prix_heure" name="prix_heure" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_prix_journee" class="form-label">Prix à la journée (€)</label>
                        <input type="number" class="form-control" id="edit_prix_journee" name="prix_journee" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_prix_mois" class="form-label">Prix au mois (€)</label>
                        <input type="number" class="form-control" id="edit_prix_mois" name="prix_mois" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteTarifModal" tabindex="-1" aria-labelledby="deleteTarifModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo BASE_URL; ?>admin/deleteTarif" method="POST">
                <input type="hidden" id="delete_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTarifModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer ce tarif ?</p>
                    <p><strong>Type:</strong> <span id="delete_type_display"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </div>
            </form>
        </div>
    </div>
</div>



<?php
// Inclure le helper pour les badges de type de place
require_once BACKEND_PATH . '/helpers/place_type_helper.php';
?>