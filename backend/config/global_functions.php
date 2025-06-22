<?php

// Fonctions globales disponibles partout dans l'application

// Récupère la réservation immédiate active avec cache pour éviter les requêtes multiples
function getActiveImmediateReservation()
{
    static $activeReservation = null;
    static $placeInfo = null;
    static $tarifHoraire = null;
    static $alreadyChecked = false;

    // Utilise le cache si déjà vérifié
    if ($alreadyChecked) {
        return [
            'reservation' => $activeReservation,
            'place' => $placeInfo,
            'tarifHoraire' => $tarifHoraire
        ];
    }

    // Vérifie seulement si utilisateur connecté
    if (isset($_SESSION['user'])) {
        $reservationModel = new ReservationModel();
        $activeReservation = $reservationModel->getActiveImmediateReservation($_SESSION['user']['id']);

        if ($activeReservation) {
            $placeModel = new PlaceModel();
            $placeInfo = $placeModel->getById($activeReservation['place_id']);
            $tarifs = $placeModel->getAllTarifs();
            $tarifHoraire = isset($tarifs[$placeInfo['type']]['prix_heure']) ? $tarifs[$placeInfo['type']]['prix_heure'] : 0;
        }
    }

    $alreadyChecked = true;

    return [
        'reservation' => $activeReservation,
        'place' => $placeInfo,
        'tarifHoraire' => $tarifHoraire
    ];
}

// Détecte les requêtes AJAX
function isAjaxRequest()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Envoie une réponse JSON et termine l'exécution
function sendJsonResponse($data, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Normalise une URL pour éviter les doublons de chemins
function normalizeUrl($path)
{
    $baseUrl = rtrim(BASE_URL, '/');
    $path = ltrim($path, '/');
    return $baseUrl . '/' . $path;
}

// Génère une URL sécurisée avec fallback
function buildUrl($path = '')
{
    $baseUrl = defined('BASE_URL') ? BASE_URL : '/projet/parking_d/';
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}
