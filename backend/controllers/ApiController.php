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
     * Génère le HTML pour une carte de place de parking (identique à la page PHP)
     */
    private function generatePlaceCardHtml($place, $tarif)
    {
        // Définir les images selon le type de place (identique à places.php)
        $placeImage = '';
        if ($place['type'] === 'standard') {
            // Images pour les places standard
            $standardImages = [
                'standar.jpg',
                'standar1.jpg',
                'standar2.jpg',
                'standar3.jpg',
                'standar4.jpg',
                'standar5.jpg',
                'standar6.webp',
                'standar7.jpg',
                'standar8.jpg',
                'standar9.jpg',
                'standar10.jpg',
                'standar11.webp',
                'standar12.webp',
                'standard1.webp',
                'standard2.webp',
                'standard3.webp'
            ];
            $randomIndex = array_rand($standardImages);
            $placeImage = BASE_URL . 'frontend/assets/img/' . $standardImages[$randomIndex];
        } elseif ($place['type'] === 'handicape') {
            // Images pour les places PMR/handicapé
            $handicapImages = [
                'pmr.jpg',
                'pmr1.jpg',
                'pmr1.webp',
                'pmr2.jpg',
                'pmr3.jpg',
                'pmr4.jpg',
                'pmr5.jpg',
                'pmr6.jpg',
                'pmr7.jpg',
                'pmr8.jpg',
                'pmr9.webp'
            ];
            $selectedHandicapImage = $handicapImages[$place['id'] % count($handicapImages)];
            $placeImage = BASE_URL . 'frontend/assets/img/' . $selectedHandicapImage;
        } elseif ($place['type'] === 'moto/scooter') {
            // Images pour les places moto/scooter
            $motoImages = [
                'moto.jpg',
                'moto1.jpg',
                'moto2.jpg',
                'moto3.jpg',
                'moto4.jpg'
            ];
            $randomIndex = array_rand($motoImages);
            $placeImage = BASE_URL . 'frontend/assets/img/' . $motoImages[$randomIndex];
        } elseif ($place['type'] === 'velo') {
            // Images pour les places vélo
            $bikeImages = [
                'velo.jpg',
                'velo1.webp',
                'velo2.jpg',
                'velo4.jpg',
                'velo5.jpg',
                'velo6.webp',
                'velo7.jpg'
            ];
            $randomIndex = array_rand($bikeImages);
            $placeImage = BASE_URL . 'frontend/assets/img/' . $bikeImages[$randomIndex];
        } elseif ($place['type'] === 'electrique') {
            // Images pour les places électriques (elec1 à elec6)
            $electricImages = [
                'elec1.webp',
                'elec2.webp',
                'elec3.webp',
                'elec4.webp',
                'elec5.webp',
                'elec6.webp'
            ];
            $randomIndex = array_rand($electricImages);
            $placeImage = BASE_URL . 'frontend/assets/img/' . $electricImages[$randomIndex];
        }

        // Formater le prix
        $tarifFloat = floatval($tarif);

        // Générer le HTML identique à places.php (nouvelle structure 3x2 grid)
        $html = '
                    <div class="place-card-item animate-on-scroll fade-in"
                        data-type="' . htmlspecialchars($place['type']) . '"
                        data-card-index="0">
                        <div class="card h-100 shadow-sm hover-effect">
                            <div class="place-card-image" style="background-image: url(\'' . $placeImage . '\');">
                                <div class="card-header bg-transparent border-0">
                                    <span class="badge ' .
                                        ($place['type'] === 'handicape' ? 'bg-warning text-dark' :
                                         ($place['type'] === 'electrique' ? 'bg-success text-white' :
                                          ($place['type'] === 'moto/scooter' ? 'bg-secondary text-white' :
                                           ($place['type'] === 'velo' ? 'bg-info text-white' : 'bg-secondary text-white')))) . '">
                                        Place ' . htmlspecialchars($place['numero']);

        // Ajouter l'icône selon le type
        switch($place['type']) {
            case 'handicape':
                $html .= '<i class="fas fa-wheelchair ms-1"></i>';
                break;
            case 'electrique':
                $html .= '<i class="fas fa-charging-station ms-1"></i>';
                break;
            case 'moto/scooter':
                $html .= '<i class="fas fa-motorcycle ms-1"></i>';
                break;
            case 'velo':
                $html .= '<i class="fas fa-bicycle ms-1"></i>';
                break;
            default:
                $html .= '<i class="fas fa-car ms-1"></i>';
                break;
        }

        $html .= '
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Type: ' . ucfirst($place['type']) . '</h5>
                                <p class="card-text">';

        // Statut de la place
        if ($place['status'] === 'libre') {
            $html .= '<span class="badge bg-success"><i class="fas fa-check me-1"></i> Disponible</span>';
        } else {
            $html .= '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Occupée</span>';
        }

        $html .= '</p>
                                <p class="card-text">
                                    <strong>Tarif:</strong> ' . number_format($tarifFloat, 2) . ' € / heure
                                </p>';

        // Affichage du statut d'occupation
        if ($place['status'] === 'occupe') {
            $html .= '<p class="text-warning small"><i class="fas fa-clock me-1"></i>
                                                Temporairement occupée
                                            </p>';
        } else {
            $html .= '<p class="text-success small"><i class="fas fa-clock me-1"></i> Tous les créneaux sont disponibles</p>';
        }

        // Bouton de réservation (toujours présent)
        $html .= '<button class="btn-reserve" data-bs-toggle="modal" data-bs-target="#reservationModal"
                                    data-place-id="' . $place['id'] . '"
                                    data-place-numero="' . htmlspecialchars($place['numero']) . '"
                                    data-place-type="' . htmlspecialchars($place['type']) . '"
                                    data-place-tarif="' . $tarifFloat . '">
                                    <i class="fas fa-calendar-check me-2"></i> Réserver
                                </button>
                                <form action="' . BASE_URL . 'reservation/reserveImmediate" method="post" class="mt-2">
                                    <input type="hidden" name="place_id" value="' . $place['id'] . '">
                                    <button type="submit" class="btn-reserve-immediate">
                                        <i class="fas fa-stopwatch me-2"></i> Réserver immédiatement
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>';

        return $html;
    }    /**
     * Génère la pagination HTML pour les pages de places avec support du filtrage par type
     */
    private function generateApiPaginationHtml($current_page, $total_pages, $type = null)
    {
        if ($total_pages <= 1) {
            return '';
        }

        // Attribut data-type pour les liens AJAX
        $typeAttr = $type ? ' data-type="' . htmlspecialchars($type) . '"' : '';
        $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';

        // Bouton "Précédent"
        $prevDisabled = ($current_page <= 1) ? 'disabled' : '';
        $html .= '<li class="page-item ' . $prevDisabled . '">';
        if ($current_page <= 1) {
            $html .= '<span class="page-link">Précédent</span>';
        } else {
            $html .= '<a class="page-link ajax-page-link" href="#" data-page="' . ($current_page - 1) . '"' . $typeAttr . '>Précédent</a>';
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
            $html .= '<a class="page-link ajax-page-link" href="#" data-page="' . $i . '"' . $typeAttr . '>' . $i . '</a>';
            $html .= '</li>';
        }

        // Bouton "Suivant"
        $nextDisabled = ($current_page >= $total_pages) ? 'disabled' : '';
        $html .= '<li class="page-item ' . $nextDisabled . '">';
        if ($current_page >= $total_pages) {
            $html .= '<span class="page-link">Suivant</span>';
        } else {
            $html .= '<a class="page-link ajax-page-link" href="#" data-page="' . ($current_page + 1) . '"' . $typeAttr . '>Suivant</a>';
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
