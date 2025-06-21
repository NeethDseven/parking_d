<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">Modifier un tarif</h1>
        <a href="<?php echo BASE_URL; ?>admin/tarifs" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux tarifs
        </a>
    </div>

    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Modification du tarif #<?php echo $tarif['id']; ?></h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>admin/updateTarif" method="POST">
                        <input type="hidden" name="id" value="<?php echo $tarif['id']; ?>">

                        <div class="mb-3">
                            <label for="type_place" class="form-label">Type de place</label>
                            <select class="form-select" id="type_place" name="type_place" required>
                                <option value="standard" <?php echo $tarif['type_place'] === 'standard' ? 'selected' : ''; ?>>Standard</option>
                                <option value="handicape" <?php echo $tarif['type_place'] === 'handicape' ? 'selected' : ''; ?>>Handicapé</option>
                                <option value="electrique" <?php echo $tarif['type_place'] === 'electrique' ? 'selected' : ''; ?>>Électrique</option>
                                <option value="moto/scooter" <?php echo $tarif['type_place'] === 'moto/scooter' ? 'selected' : ''; ?>>Moto/Scooter</option>
                                <option value="velo" <?php echo $tarif['type_place'] === 'velo' ? 'selected' : ''; ?>>Vélo</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="free_minutes" class="form-label">Minutes gratuites</label>
                            <input type="number" class="form-control" id="free_minutes" name="free_minutes"
                                min="0" value="<?php echo $tarif['free_minutes'] ?? 0; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="prix_heure" class="form-label">Prix à l'heure (€)</label>
                            <input type="number" class="form-control" id="prix_heure" name="prix_heure"
                                step="0.01" min="0" value="<?php echo $tarif['prix_heure']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="prix_journee" class="form-label">Prix à la journée (€)</label>
                            <input type="number" class="form-control" id="prix_journee" name="prix_journee"
                                step="0.01" min="0" value="<?php echo $tarif['prix_journee']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="prix_mois" class="form-label">Prix au mois (€)</label>
                            <input type="number" class="form-control" id="prix_mois" name="prix_mois"
                                step="0.01" min="0" value="<?php echo $tarif['prix_mois']; ?>" required>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer les modifications
                            </button>
                            <a href="<?php echo BASE_URL; ?>admin/tarifs" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Informations</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">
                        <i class="fas fa-info-circle"></i>
                        Modifiez les tarifs avec précaution car cela peut affecter les futures réservations.
                    </p>
                    <p class="text-muted mb-0">
                        <i class="fas fa-clock"></i>
                        Les prix doivent être en euros (€).
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>