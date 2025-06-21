<?php

/**
 * Composant pour afficher les actions disponibles pour une réservation
 * selon son statut
 * 
 * @param array $reservation La réservation
 */

// Récupérer le statut depuis l'une des clés possibles
$currentStatus = '';
if (isset($reservation['statut']) && !empty($reservation['statut'])) {
    $currentStatus = $reservation['statut'];
} elseif (isset($reservation['status']) && !empty($reservation['status'])) {
    $currentStatus = $reservation['status'];
}

// Mise à jour automatique du statut côté serveur
$now = time();
$dateDebut = isset($reservation['date_debut']) ? strtotime($reservation['date_debut']) : 0;
$dateFin = isset($reservation['date_fin']) ? strtotime($reservation['date_fin']) : 0;

// Standardisation des valeurs du statut (enlever accents et utiliser la version sans accent)
$currentStatus = str_replace(['é', 'è', 'ê'], 'e', strtolower($currentStatus));

// Si la date de fin est vide ou 01/01/1970, c'est probablement annulée
if ($dateFin <= 86400) { // 86400 = 1 jour en secondes depuis epoch
    $currentStatus = 'annulee';
}
// Réservation avec "annulée" dans le statut
elseif (is_string($currentStatus) && strpos($currentStatus, 'annul') !== false) {
    $currentStatus = 'annulee';
}
// Réservation expirée
elseif (is_string($currentStatus) && strpos($currentStatus, 'expir') !== false) {
    $currentStatus = 'expiree';
}
// Réservation terminée avec statut explicite ou implicite
elseif (is_string($currentStatus) && (strpos($currentStatus, 'termin') !== false || $dateFin <= $now)) {
    $currentStatus = 'terminee';
}
// Réservation confirmée qui a débuté mais n'est pas terminée
elseif ((strpos($currentStatus, 'confirm') !== false) && $dateDebut <= $now && $dateFin > $now) {
    $currentStatus = 'en_cours';
}
// Réservation en cours explicite
elseif (strpos($currentStatus, 'en_cours') !== false) {
    $currentStatus = 'en_cours';
}
// Si le statut est toujours vide ou inconnu, définir un statut par défaut
elseif (empty($currentStatus) || $currentStatus === 'inconnu') {
    // Déduire le statut en fonction des dates
    if ($dateDebut <= $now && $dateFin > $now) {
        $currentStatus = 'en_cours';
    } elseif ($dateFin <= $now) {
        $currentStatus = 'terminee';
    } else {
        $currentStatus = 'confirmee';  // Par défaut on suppose que c'est confirmée
    }
}
?>
<!-- Début du div pour les actions - avec classe explicite -->
<div class="reservation-actions-container">
    <?php if ($currentStatus === 'en_cours_immediat'): ?>
        <a href="<?php echo BASE_URL; ?>reservation/immediate/<?php echo $reservation['id']; ?>" class="btn btn-sm btn-primary action-button">
            <i class="fas fa-stopwatch me-1"></i> Voir chrono
        </a>
        <form action="<?php echo BASE_URL; ?>reservation/endImmediate" method="post" class="d-inline">
            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
            <button type="submit" class="btn btn-sm btn-danger action-button" onclick="return confirm('Êtes-vous sûr de vouloir terminer cette réservation ? Vous devrez procéder au paiement pour quitter le parking.')">
                <i class="fas fa-stop-circle me-1"></i> Terminer
            </button>
        </form>

    <?php elseif ($currentStatus === 'confirmee' || $currentStatus === 'confirmée'): ?> <?php if ($dateDebut > $now): // Réservation future 
                                                                                        ?>
            <a href="<?php echo BASE_URL; ?>reservation/cancel/<?php echo $reservation['id']; ?>" class="btn btn-sm btn-danger action-button"
                onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                <i class="fas fa-times me-1"></i> Annuler
            </a>
        <?php endif; ?>
        <!-- Toujours afficher le bouton détails pour les réservations confirmées -->
        <a href="<?php echo BASE_URL; ?>reservation/confirmation/<?php echo $reservation['id']; ?>" class="btn btn-sm btn-info action-button">
            <i class="fas fa-info-circle me-1"></i> Détails
        </a>

    <?php elseif ($currentStatus === 'en_cours'): ?>
        <a href="<?php echo BASE_URL; ?>reservation/confirmation/<?php echo $reservation['id']; ?>" class="btn btn-sm btn-info action-button">
            <i class="fas fa-info-circle me-1"></i> Détails
        </a>

    <?php elseif ($currentStatus === 'en_attente'): ?> <div class="btn-group btn-group-sm">
            <a href="<?php echo BASE_URL; ?>reservation/payment/<?php echo $reservation['id']; ?>" class="btn btn-success action-button">
                <i class="fas fa-credit-card me-1"></i> Payer </a>
            <a href="<?php echo BASE_URL; ?>reservation/cancel/<?php echo $reservation['id']; ?>" class="btn btn-danger action-button"
                onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                <i class="fas fa-times"></i>
            </a>
        </div>
        <div class="small text-muted mt-1">
            <i class="fas fa-clock me-1"></i> Expire dans:
            <strong id="countdown-<?php echo $reservation['id']; ?>"
                data-expires="<?php echo isset($reservation['date_creation']) ? strtotime($reservation['date_creation']) + 900 : time() + 900; ?>"
                data-reservation-id="<?php echo $reservation['id']; ?>">
                Calcul...
            </strong>
        </div>
    <?php elseif ($currentStatus === 'terminee' || $currentStatus === 'terminée'): ?>
        <?php if (isset($reservation['code_sortie']) && !empty($reservation['code_sortie'])): ?>
            <button type="button" class="btn btn-sm btn-success action-button" data-bs-toggle="modal" data-bs-target="#codeModal<?php echo $reservation['id']; ?>">
                <i class="fas fa-key me-1"></i> Voir codes
            </button>
        <?php endif; ?>
        <a href="<?php echo BASE_URL; ?>reservation/confirmation/<?php echo $reservation['id']; ?>" class="btn btn-sm btn-info action-button">
            <i class="fas fa-info-circle me-1"></i> Détails
        </a>

    <?php elseif ($currentStatus === 'annulee' || $currentStatus === 'annulée' || $currentStatus === 'expiree' || $currentStatus === 'expirée'): ?>
        <a href="<?php echo BASE_URL; ?>reservation/confirmation/<?php echo $reservation['id']; ?>" class="btn btn-sm btn-info action-button">
            <i class="fas fa-info-circle me-1"></i> Détails
        </a>
    <?php else: ?>
        <!-- Fallback pour tout autre statut - toujours afficher le bouton détails -->
        <a href="<?php echo BASE_URL; ?>reservation/confirmation/<?php echo $reservation['id']; ?>" class="btn btn-sm btn-info action-button">
            <i class="fas fa-info-circle me-1"></i> Détails
        </a>
    <?php endif; ?>
</div>
<!-- Fin du div pour les actions -->