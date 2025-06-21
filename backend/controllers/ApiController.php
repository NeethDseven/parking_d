<?php
/**
 * Contrôleur API consolidé et optimisé
 * Gère toutes les requêtes AJAX et endpoints API du système
 */
class ApiController extends BaseController
{
    private $userModel;
    private $homeModel;
    private $placeModel;
    private $reservationModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->homeModel = new HomeModel();
        $this->placeModel = new PlaceModel();
        $this->reservationModel = new ReservationModel();
        
        // Définir l'en-tête de réponse comme JSON
        header('Content-Type: application/json');
    }
    
    /**
     * Marque une notification comme lue
     */
    public function markNotificationRead($id = null)
    {
        if (!$this->isAuthenticated()) {
            $this->jsonError('Vous devez être connecté pour effectuer cette action.', 401);
        }

        if (!$id) {
            $this->jsonError('ID de notification non spécifié.', 400);
        }

        $result = $this->userModel->markNotificationAsRead($id);

        if ($result) {
            $this->jsonResponse(['success' => true]);
        } else {
            $this->jsonError('Impossible de marquer la notification comme lue.');
        }
    }

    /**
     * Récupère toutes les places disponibles
     */
    public function getPlacesDisponibles()
    {
        $places = $this->homeModel->getAvailablePlaces();
        $this->jsonResponse([
            'success' => true, 
            'places' => $places, 
            'count' => count($places)
        ]);
    }

    /**
     * Récupère les places par type
     */
    public function getPlaceParType($type = null)
    {
        if (!$type) {
            $this->jsonError('Type de place non spécifié.', 400);
        }

        $places = $this->homeModel->getPlacesByType($type);
        $this->jsonResponse([
            'success' => true, 
            'places' => $places, 
            'count' => count($places)
        ]);
    }

    /**
     * Endpoint pour récupérer les places disponibles avec pagination
     * Utilisé pour l'affichage AJAX des places
     */
    public function getPlacesPage()
    {
        // Récupérer les paramètres
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $type = isset($_GET['type']) && $_GET['type'] !== 'all' ? $_GET['type'] : null;
        $items_per_page = 6;
        $offset = ($page - 1) * $items_per_page;

        // Récupérer les places disponibles avec pagination
        if ($type) {
            $places = $this->homeModel->getAvailablePlacesByType($type, $page, $items_per_page);
            $total_places_available = $this->homeModel->countAvailablePlacesByType($type);
        } else {
            $places = $this->homeModel->getAvailablePlaces($page, $items_per_page);
            $total_places_available = $this->homeModel->countAvailablePlaces();
        }
          // Récupérer les tarifs pour les places
        $tarifsData = $this->homeModel->getTarifs();
        $tarifs = array_column($tarifsData, null, 'type_place');

        // Nombre total de places (tous types confondus) - pour l'affichage général
        $total_places = count($this->homeModel->getAllPlaces());

        // Calculer le nombre de pages basé sur les places filtrées/disponibles
        $total_pages = ceil($total_places_available / $items_per_page);
        
        // Générer le HTML pour les places
        $placesHtml = '';
        foreach ($places as $place) {
            $placeType = $place['type'];
            $tarifHeure = isset($tarifs[$placeType]) ? $tarifs[$placeType]['prix_heure'] : 'N/A';
            $placesHtml .= $this->generatePlaceCardHtml($place, $tarifHeure);
        }

        // Générer le HTML pour la pagination AJAX
        $paginationHtml = $this->generateApiPaginationHtml($page, $total_pages, $type);

        // Renvoyer les données JSON
        $this->jsonResponse([
            'success' => true,
            'total_count' => $total_places,
            'available_count' => $total_places_available,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'places_html' => $placesHtml,
            'pagination_html' => $paginationHtml
        ]);
    }

    /**
     * Génère le HTML pour une carte de place de parking
     */
    private function generatePlaceCardHtml($place, $tarif)
    {
        // Définir les classes et styles selon le type de place
        $typeClass = '';
        $badgeClass = '';

        if ($place['type'] === 'handicape') {
            $badgeClass = 'bg-primary';
        } elseif ($place['type'] === 'electrique') {
            $badgeClass = 'bg-success';
        } else {
            $badgeClass = 'bg-light text-dark';
        }

        // Définir les images aléatoires pour les types de place
        $standardImages = [
            'standard1.webp',
            'standard2.webp',
            'standard3.webp'
        ];
        
        $electricImages = [
            'elec1.webp',
            'elec2.webp',
            'elec3.webp',
            'elec4.webp',
            'elec5.webp'
        ];
        
        $randomIndex = array_rand($standardImages);
        $randomIndexElec = array_rand($electricImages);

        // Définir l'image selon le type de place
        $placeImage = '';
        if ($place['type'] === 'standard') {
            $placeImage = BASE_URL . 'frontend/assets/img/' . $standardImages[$randomIndex];
        } elseif ($place['type'] === 'handicape') {
            $placeImage = BASE_URL . 'frontend/assets/img/pmr1.webp';
        } elseif ($place['type'] === 'electrique') {
            $placeImage = BASE_URL . 'frontend/assets/img/' . $electricImages[$randomIndexElec];
        } elseif ($place['type'] === 'moto' || $place['type'] === 'moto/scooter') {
            $placeImage = BASE_URL . 'frontend/assets/img/parking-propre.webp';
        } elseif ($place['type'] === 'velo') {
            $placeImage = BASE_URL . 'frontend/assets/img/1200x680_velo-stationnement-125.webp';
        }

        // Icône selon le type
        $iconHtml = '';
        if ($place['type'] === 'handicape') {
            $iconHtml = '<i class="fas fa-wheelchair ms-1"></i>';
        } elseif ($place['type'] === 'electrique') {
            $iconHtml = '<i class="fas fa-charging-station ms-1"></i>';
        } elseif ($place['type'] === 'moto' || $place['type'] === 'moto/scooter') {
            $iconHtml = '<i class="fas fa-motorcycle ms-1"></i>';
        } elseif ($place['type'] === 'velo') {
            $iconHtml = '<i class="fas fa-bicycle ms-1"></i>';
        }

        // Formater le prix
        $tarifFloat = floatval($tarif);
        $tarifFormatted = number_format($tarifFloat, 2, ',', ' ') . ' €';
        
        // Générer le HTML de la carte
        $html = '
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 place-card shadow-sm">
                <div class="badge ' . $badgeClass . ' position-absolute top-0 end-0 m-2 py-2 px-3">
                    ' . ucfirst($place['type']) . ' ' . $iconHtml . '
                </div>
                <img src="' . $placeImage . '" alt="Place ' . $place['numero'] . '" class="card-img-top">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0">Place N°' . $place['numero'] . '</h5>
                        <span class="badge ' . ($place['status'] === 'libre' ? 'bg-success' : 'bg-danger') . '">' 
                            . ucfirst($place['status']) . 
                        '</span>
                    </div>                    <p class="card-text">Tarif horaire: <strong>' . $tarifFormatted . '</strong></p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary reserve-btn" 
                                data-bs-toggle="modal" 
                                data-bs-target="#reservationModal" 
                                data-place-id="' . $place['id'] . '" 
                                data-place-numero="' . $place['numero'] . '" 
                                data-place-type="' . $place['type'] . '" 
                                data-place-tarif="' . $tarifFloat . '"
                                ' . ($place['status'] !== 'libre' ? 'disabled' : '') . '>
                            <i class="fas fa-calendar-check me-2"></i>' . ($place['status'] === 'libre' ? 'Réserver' : 'Non disponible') . '
                        </button>';
        
        // Ajouter le bouton de réservation immédiate seulement si la place est libre
        if ($place['status'] === 'libre') {
            $html .= '
                        <form action="' . BASE_URL . 'reservation/reserveImmediate" method="post" class="mt-2">
                            <input type="hidden" name="place_id" value="' . $place['id'] . '">
                            <button type="submit" class="btn-reserve-immediate">
                                <i class="fas fa-stopwatch me-2"></i> Réserver immédiatement
                            </button>
                        </form>';
        }
        
        $html .= '
                    </div>
                </div>
            </div>
        </div>';
        
        return $html;
    }    /**
     * Génère la pagination HTML pour les pages de places
     */
    private function generateApiPaginationHtml($current_page, $total_pages, $type = null)
    {
        if ($total_pages <= 1) {
            return '';
        }
        
        $typeParam = $type ? '&type=' . $type : '';
        $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
          // Bouton "Précédent"
        $prevDisabled = ($current_page <= 1) ? 'disabled' : '';
        $html .= '<li class="page-item ' . $prevDisabled . '">';
        if ($current_page <= 1) {
            $html .= '<span class="page-link">Précédent</span>';
        } else {
            $html .= '<a class="page-link ajax-page-link" href="#" data-page="' . ($current_page - 1) . '">Précédent</a>';
        }
        $html .= '</li>';
        
        // Pages numériques
        $start = max(1, $current_page - 2);
        $end = min($total_pages, $start + 4);
        
        if ($end - $start < 4) {
            $start = max(1, $end - 4);
        }
        
        for ($i = $start; $i <= $end; $i++) {
            $activeClass = ($i == $current_page) ? 'active' : '';
            $html .= '<li class="page-item ' . $activeClass . '">';
            $html .= '<a class="page-link ajax-page-link" href="#" data-page="' . $i . '">' . $i . '</a>';
            $html .= '</li>';
        }
          // Bouton "Suivant"
        $nextDisabled = ($current_page >= $total_pages) ? 'disabled' : '';
        $html .= '<li class="page-item ' . $nextDisabled . '">';
        if ($current_page >= $total_pages) {
            $html .= '<span class="page-link">Suivant</span>';
        } else {
            $html .= '<a class="page-link ajax-page-link" href="#" data-page="' . ($current_page + 1) . '">Suivant</a>';
        }
        $html .= '</li>';
        
        $html .= '</ul></nav>';
        return $html;
    }

    /**
     * Renvoie les informations de tarif pour un type de place
     */
    public function getTarifInfo()
    {
        $type = $_GET['type'] ?? null;
        
        if (!$type) {
            $this->jsonError('Type de place non spécifié', 400);
        }
        
        $tarif = $this->homeModel->getTarifByType($type);
        
        if (!$tarif) {
            $this->jsonError('Tarif non trouvé pour ce type de place', 404);
        }
        
        $this->jsonResponse([
            'success' => true,
            'tarif' => $tarif
        ]);
    }

    /**
     * Vérifie le statut d'une réservation
     */
    public function checkReservationStatus($reservationId = null)
    {
        if (!$reservationId) {
            $this->jsonError('ID de réservation non spécifié', 400);
        }
        
        $reservation = $this->reservationModel->getReservationById($reservationId);
        
        if (!$reservation) {
            $this->jsonError('Réservation non trouvée', 404);
        }
        
        // Vérifier si l'utilisateur a le droit d'accéder à cette réservation
        $isOwner = isset($_SESSION['user']) && $_SESSION['user']['id'] == $reservation['user_id'];
        $isGuest = isset($_SESSION['guest_token']) && $_SESSION['guest_token'] == $reservation['guest_token'];
        $isAdmin = isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin';
        
        if (!$isOwner && !$isGuest && !$isAdmin) {
            $this->jsonError('Vous n\'avez pas l\'autorisation d\'accéder à cette réservation', 403);
        }
        
        $this->jsonResponse([
            'success' => true,
            'status' => $reservation['status'],
            'details' => [
                'date_debut' => $reservation['date_debut'],
                'date_fin' => $reservation['date_fin'],
                'montant_total' => $reservation['montant_total'],
                'code_acces' => $reservation['code_acces'],
                'place_numero' => isset($reservation['place_numero']) ? $reservation['place_numero'] : null
            ]
        ]);
    }

    /**
     * Vérifie la disponibilité d'une place pour un créneau spécifique
     */
    public function checkAvailability()
    {
        $placeId = intval($_GET['place_id'] ?? 0);
        $dateDebut = $_GET['date_debut'] ?? null;
        $dateFin = $_GET['date_fin'] ?? null;
        
        if (!$placeId || !$dateDebut) {
            $this->jsonError('Paramètres manquants', 400);
        }
        
        // Si date_fin n'est pas spécifiée, utiliser date_debut + 1 heure par défaut
        if (!$dateFin) {
            $dateObj = new DateTime($dateDebut);
            $dateObj->modify('+1 hour');
            $dateFin = $dateObj->format('Y-m-d H:i:s');
        }
        
        $isAvailable = $this->reservationModel->isPlaceAvailableForTimeSlot($placeId, $dateDebut, $dateFin);
        
        $this->jsonResponse([
            'success' => true,
            'available' => $isAvailable,
            'place_id' => $placeId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ]);
    }
}
