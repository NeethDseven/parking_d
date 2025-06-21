<?php
// Vérifier que les variables nécessaires sont définies
if (!isset($reservation)) {
    header('Location: ' . buildUrl('auth/profile'));
    exit;
}

// Calculer la durée de la réservation
$dateDebut = new DateTime($reservation['date_debut']);
$dateFin = new DateTime($reservation['date_fin']);
$duree = $dateFin->diff($dateDebut);

// Formater la durée
if ($duree->h > 0) {
    $dureeText = $duree->h . 'h';
    if ($duree->i > 0) {
        $dureeText .= ' ' . $duree->i . 'min';
    }
} else {
    $dureeText = $duree->i . ' minutes';
}
?>

<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white text-center">
                    <h2><i class="fas fa-check-circle"></i> Réservation Terminée</h2>
                    <p class="mb-0">Merci d'avoir utilisé notre service de parking</p>
                </div>

                <div class="card-body p-4">
                    <!-- Résumé de la réservation -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">
                                        <i class="fas fa-info-circle"></i> Détails de la réservation
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Numéro :</strong></td>
                                            <td>#<?= str_pad($reservation['id'], 6, '0', STR_PAD_LEFT) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Place :</strong></td>
                                            <td><?= htmlspecialchars($reservation['place_numero'] ?? 'N/A') ?>
                                                (<?= ucfirst($reservation['place_type'] ?? 'Standard') ?>)</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Durée :</strong></td>
                                            <td><?= $dureeText ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Statut :</strong></td>
                                            <td>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check"></i> Terminée
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-left-info">
                                <div class="card-body">
                                    <h5 class="card-title text-info">
                                        <i class="fas fa-clock"></i> Horaires
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Arrivée :</strong></td>
                                            <td><?= $dateDebut->format('d/m/Y à H:i') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Départ :</strong></td>
                                            <td><?= $dateFin->format('d/m/Y à H:i') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Terminée le :</strong></td>
                                            <td><?= date('d/m/Y à H:i') ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations de paiement -->
                    <?php if (isset($payment) && $payment): ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-left-success">
                                    <div class="card-body">
                                        <h5 class="card-title text-success">
                                            <i class="fas fa-credit-card"></i> Paiement
                                        </h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>Montant payé :</strong><br>
                                                <span class="text-success h4"><?= number_format($payment['montant'], 2) ?> €</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Mode de paiement :</strong><br>
                                                <?= ucfirst($payment['mode_paiement'] ?? 'N/A') ?>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Statut :</strong><br>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check"></i> Validé
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Actions -->
                    <div class="row">
                        <div class="col-12 text-center">
                            <div class="btn-group" role="group">
                                <a href="<?= buildUrl('auth/profile') ?>" class="btn btn-primary">
                                    <i class="fas fa-user"></i> Retour au profil
                                </a>
                                <a href="<?= buildUrl('home') ?>" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Nouvelle réservation
                                </a>
                                <?php if (isset($payment) && $payment): ?>
                                    <a href="<?= buildUrl('reservation/invoice/' . $payment['id']) ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-file-pdf"></i> Télécharger la facture
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Évaluation optionnelle -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">
                                        <i class="fas fa-star text-warning"></i> Évaluez votre expérience
                                    </h6>
                                    <p class="text-muted">Comment s'est passée votre réservation ?</p>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-danger" onclick="submitRating(1)">
                                            <i class="fas fa-star"></i> Mauvais
                                        </button>
                                        <button class="btn btn-outline-warning" onclick="submitRating(3)">
                                            <i class="fas fa-star"></i> Moyen
                                        </button>
                                        <button class="btn btn-outline-success" onclick="submitRating(5)">
                                            <i class="fas fa-star"></i> Excellent
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function submitRating(rating) {
        // Vous pouvez implémenter ici l'envoi de l'évaluation
        fetch('<?= buildUrl("reservation/rate/" . $reservation["id"]) ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    rating: rating
                })
            })
            .then(() => {
                alert('Merci pour votre évaluation !');
                document.querySelector('.btn-group').innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Merci !</span>';
            })
            .catch(() => {
                alert('Erreur lors de l\'envoi de l\'évaluation');
            });
    }
</script>