<?php

/**
 * Fonctions globales disponibles partout dans l'application
 */

/**
 * Récupère la réservation immédiate active pour l'utilisateur connecté
 * @return array|null Informations sur la réservation immédiate active
 */
function getActiveImmediateReservation()
{
    static $activeReservation = null;
    static $placeInfo = null;
    static $tarifHoraire = null;
    static $alreadyChecked = false;

    // Si déjà vérifié, retourner les résultats en cache
    if ($alreadyChecked) {
        return [
            'reservation' => $activeReservation,
            'place' => $placeInfo,
            'tarifHoraire' => $tarifHoraire
        ];
    }

    // Si l'utilisateur est connecté
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

/**
 * Détermine si la requête actuelle est une requête AJAX
 * @return bool True si c'est une requête AJAX, false sinon
 */
function isAjaxRequest()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Envoie une réponse JSON et termine l'exécution du script
 * @param array $data Données à encoder en JSON
 * @param int $status Code de statut HTTP
 */
function sendJsonResponse($data, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Normalise une URL en évitant les doublons de chemins
 * @param string $path Le chemin à ajouter au BASE_URL
 * @return string L'URL complète normalisée
 */
function normalizeUrl($path)
{
    $baseUrl = rtrim(BASE_URL, '/');
    $path = ltrim($path, '/');
    return $baseUrl . '/' . $path;
}

/**
 * Génère une URL sécurisée avec le bon préfixe
 * @param string $path Le chemin relatif
 * @return string L'URL complète
 */
function buildUrl($path = '')
{
    $baseUrl = defined('BASE_URL') ? BASE_URL : '/projet/parking_d/';
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}
