<?php

/**
 * Component pour afficher le badge de statut d'une réservation
 * Compatible avec toutes les variations de statut utilisées dans le système
 */

// Récupérer le statut depuis l'une des clés possibles - utiliser une variable locale
$status = '';
if (isset($reservation['statut']) && !empty($reservation['statut'])) {
    $status = $reservation['statut'];
} elseif (isset($reservation['status']) && !empty($reservation['status'])) {
    $status = $reservation['status'];
}

// S'assurer que le statut est normalisé (sans accents et en minuscules)
$status = str_replace(['é', 'è', 'ê'], 'e', strtolower($status));

// Mise à jour automatique du statut en fonction des dates et heures actuelles
$now = time();
$dateDebut = isset($reservation['date_debut']) ? strtotime($reservation['date_debut']) : 0;
$dateFin = isset($reservation['date_fin']) ? strtotime($reservation['date_fin']) : 0;

// Détermination du statut basée sur les règles métier
// Ne marquer comme annulée que si explicitement indiqué dans le statut ou les autres conditions sont remplies
// Ne pas utiliser la date_fin nulle comme indicateur d'annulation, car cela peut être trompeur

// Vérifier explicitement si la réservation est annulée
if (is_string($status) && strpos($status, 'annul') !== false) {
    $status = 'annulee';
}
// Réservation expirée
elseif (is_string($status) && strpos($status, 'expir') !== false) {
    $status = 'expiree';
}
// Réservation terminée avec statut explicite
elseif (is_string($status) && strpos($status, 'termin') !== false) {
    $status = 'terminee';
}
// Réservation terminée implicitement par les dates
elseif ($dateFin > 86400 && $dateFin <= $now) {
    $status = 'terminee';
}
// Réservation confirmée qui a débuté mais n'est pas terminée
elseif ((strpos($status, 'confirm') !== false) && $dateDebut <= $now && ($dateFin > $now || $dateFin <= 86400)) {
    $status = 'en_cours';
}
// Réservation en cours explicite - CONSERVER CE STATUT
elseif (strpos($status, 'en_cours') !== false) {
    // Ne pas modifier le statut, assurer qu'il reste en cours
    $status = 'en_cours';
}
// Réservation immédiate spécifique - CONSERVER CE STATUT
elseif (strpos($status, 'immediat') !== false) {
    $status = 'en_cours_immediat';
}
// Si le statut est toujours vide ou inconnu, définir un statut par défaut
elseif (empty($status) || $status === 'inconnu') {
    // Déduire le statut en fonction des dates
    if ($dateDebut <= $now && ($dateFin > $now || $dateFin <= 86400)) {
        $status = 'en_cours';
    } elseif ($dateFin <= $now && $dateFin > 86400) {
        $status = 'terminee';
    } else {
        $status = 'confirmee';  // Par défaut on suppose que c'est confirmée
    }
}
?>
<span class="status-badge badge <?php
                                switch ($status) {
                                    case 'en_attente':
                                        echo 'bg-warning text-dark';
                                        break;
                                    case 'confirmée':
                                    case 'confirmee':
                                        echo 'bg-success';
                                        break;
                                    case 'en_cours':
                                        echo 'bg-info';
                                        break;
                                    case 'en_cours_immediat':
                                        echo 'bg-warning';
                                        break;
                                    case 'terminée':
                                    case 'terminee':
                                        echo 'bg-secondary';
                                        break;
                                    case 'annulée':
                                    case 'annulee':
                                        echo 'bg-danger';
                                        break;
                                    case 'expiré':
                                    case 'expire':
                                    case 'expirée':
                                    case 'expiree':
                                        echo 'bg-dark';
                                        break;
                                    default:
                                        echo 'bg-secondary';
                                }
                                ?>"> <?php
                                        switch ($status) {
                                            case 'en_attente':
                                                echo '<i class="fas fa-clock me-1"></i>En attente';
                                                break;
                                            case 'confirmée':
                                            case 'confirmee':
                                                echo '<i class="fas fa-check me-1"></i>Confirmée';
                                                break;
                                            case 'en_cours':
                                                echo '<i class="fas fa-play me-1"></i>En cours';
                                                break;
                                            case 'en_cours_immediat':
                                                echo '<i class="fas fa-stopwatch me-1"></i>Chrono en cours';
                                                break;
                                            case 'terminée':
                                            case 'terminee':
                                                echo '<i class="fas fa-flag-checkered me-1"></i>Terminée';
                                                break;
                                            case 'annulée':
                                            case 'annulee':
                                                echo '<i class="fas fa-times me-1"></i>Annulée';
                                                break;
                                            case 'expiré':
                                            case 'expire':
                                            case 'expirée':
                                            case 'expiree':
                                                echo '<i class="fas fa-hourglass-end me-1"></i>Expirée';
                                                break;
                                            default:
                                                echo '<i class="fas fa-question-circle me-1"></i>' . ucfirst($status);
                                        }
                                        ?>
</span>