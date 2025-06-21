<?php

/**
 * Affiche un badge avec le statut de la réservation
 * @param string $status Le statut de la réservation
 * @deprecated Utilisez le composant reservation_status_badge.php à la place
 */
function displayStatusBadge($status)
{
    // Créer un tableau temporaire avec le statut pour pouvoir utiliser le composant
    $reservation = ['statut' => $status];    // Inclure le composant pour afficher le badge    include_once FRONTEND_PATH . '/views/components/reservation_status_badge.php';
}
