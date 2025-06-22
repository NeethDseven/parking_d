<?php

/**
 * Contrôleur de page d'accueil consolidé et optimisé
 * Gère toutes les pages publiques du site
 */
class HomeController extends BaseController
{
    private $homeModel;
    private $reservationModel;
    private $placeModel;

    public function __construct()
    {
        parent::__construct();
        $this->homeModel = new HomeModel();
        $this->reservationModel = new ReservationModel();
        $this->placeModel = new PlaceModel();
    }
    public function index()
    {
        // Récupérer les horaires d'ouverture
        $horaires = $this->homeModel->getHorairesFormatted();

        // Récupérer les statistiques de base
        $stats = $this->homeModel->getStatistiques();

        $data = [
            'title' => 'Accueil - ' . APP_NAME,
            'description' => 'Bienvenue sur ' . APP_NAME . ', votre solution de stationnement intelligente.',
            'horaires' => $horaires,
            'stats' => $stats,
            'active_page' => 'home'
        ];

        $this->renderView('home/index', $data);
    }

    public function places()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $type = isset($_GET['type']) ? $_GET['type'] : null;
        $items_per_page = 6;

        // Récupérer les places disponibles (avec pagination)
        if ($type && $type !== 'all') {
            $places = $this->homeModel->getAvailablePlacesByType($type, $page, $items_per_page);
            $total_places_available = $this->homeModel->countAvailablePlacesByType($type);
        } else {
            $places = $this->homeModel->getAvailablePlaces($page, $items_per_page);
            $total_places_available = $this->homeModel->countAvailablePlaces();
        }        // Récupérer les tarifs et les indexer par type
        $tarifsData = $this->homeModel->getTarifs();
        $tarifs = array_column($tarifsData, null, 'type_place');

        // Récupérer tous les créneaux réservés pour les places affichées
        $reservedTimeSlots = $this->homeModel->getAllReservedTimeSlots();

        // Récupérer les informations sur l'occupation actuelle des places
        $currentOccupation = $this->homeModel->getCurrentOccupationInfo();

        // Nombre total de places (tous types confondus)
        $total_places = count($this->homeModel->getAllPlaces());        // Calculer le nombre de pages
        $total_pages = ceil($total_places_available / $items_per_page);        // Récupérer les avantages d'abonnement de l'utilisateur si connecté
        $userSubscriptionBenefits = null;
        if (isset($_SESSION['user'])) {
            $subscriptionModel = new SubscriptionModel();
            $userSubscriptions = $subscriptionModel->getUserActiveSubscriptions($_SESSION['user']['id']);

            if (!empty($userSubscriptions)) {
                $userSubscriptionBenefits = [
                    'free_minutes' => $userSubscriptions[0]['free_minutes'],
                    'discount_percent' => $userSubscriptions[0]['discount_percent'],
                    'subscription_name' => $userSubscriptions[0]['name']
                ];
            }
        }

        // Assurons-nous que tous les paramètres nécessaires sont définis
        $data = [
            'title' => 'Places disponibles - ' . APP_NAME,
            'description' => 'Consultez les places disponibles dans notre parking.',
            'places' => $places,
            'tarifs' => $tarifs,
            'reservedTimeSlots' => $reservedTimeSlots,
            'currentOccupation' => $currentOccupation,
            'total_places' => $total_places,
            'total_places_available' => $total_places_available,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'items_per_page' => $items_per_page,
            'selected_type' => $type,
            'pagination_url' => BASE_URL . 'home/places',
            'url_params' => $type && $type !== 'all' ? ['type' => $type] : [],
            'active_page' => 'places',
            'userSubscriptionBenefits' => $userSubscriptionBenefits
        ];

        // Charger la vue - le chargement initial utilise le rendu traditionnel, les interactions suivantes utiliseront AJAX
        $this->renderView('home/places', $data);
    }
    public function about()
    {
        $data = [
            'title' => 'À propos - ' . APP_NAME,
            'description' => 'En savoir plus sur ' . APP_NAME,
            'active_page' => 'about'
        ];

        $this->renderView('home/about', $data);
    }
    public function contact()
    {
        $data = [
            'title' => 'Contact - ' . APP_NAME,
            'description' => 'Contactez-nous pour toute question',
            'active_page' => 'contact'
        ];

        // Traitement du formulaire de contact
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Valider les champs requis
            $requiredFields = ['nom', 'email', 'sujet', 'message', 'rgpd'];
            $validated = $this->validateRequiredFields($requiredFields);

            if ($validated) {
                // Créer le modèle de contact
                $contactModel = new ContactModel();

                // Sauvegarder le message en base de données
                $messageId = $contactModel->createMessage(
                    $validated['nom'],
                    $validated['email'],
                    $validated['sujet'],
                    $validated['message']
                );

                if ($messageId) {
                    $data['success'] = 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.';

                    // Log de l'action
                    $logModel = new LogModel();
                    $logModel->addLog(null, 'contact_message', 'Nouveau message de contact reçu de ' . $validated['email']);
                } else {
                    $data['error'] = 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer.';
                }
            } else {
                $data['error'] = 'Veuillez remplir tous les champs obligatoires.';
            }
        }

        $this->renderView('home/contact', $data);
    }

    public function reservationTracking()
    {
        $data = [
            'title' => 'Suivi de votre réservation - ' . APP_NAME,
            'description' => 'Retrouvez les détails de votre réservation'
        ];

        $this->renderView('home/reservationTracking', $data);
    }

    public function findReservation()
    {
        // Initialiser le modèle de réservation
        $reservationModel = new ReservationModel();
        $reservation = null;

        // Recherche par code de suivi (token)
        if (isset($_POST['track_by_code']) && !empty($_POST['tracking_code'])) {
            $token = trim($_POST['tracking_code']);
            $reservation = $reservationModel->getReservationByGuestToken($token);

            if ($reservation) {
                // Rediriger vers la page de suivi avec le token
                header('Location: ' . BASE_URL . 'reservation/trackReservation/' . $token);
                exit;
            }

            $_SESSION['error'] = 'Aucune réservation trouvée avec ce code de suivi.';
            header('Location: ' . BASE_URL . 'home/reservationTracking');
            exit;
        }

        // Recherche par email
        if (isset($_POST['track_by_email']) && !empty($_POST['email'])) {
            $email = trim($_POST['email']);

            // Récupérer toutes les réservations associées à cet email
            $reservations = $reservationModel->getReservationsByGuestEmail($email);

            if (count($reservations) > 0) {
                // S'il n'y a qu'une seule réservation, rediriger directement
                if (count($reservations) === 1) {
                    header('Location: ' . BASE_URL . 'reservation/trackReservation/' . $reservations[0]['guest_token']);
                    exit;
                }

                // Sinon, afficher la liste des réservations
                $data = [
                    'title' => 'Vos réservations - ' . APP_NAME,
                    'description' => 'Liste de vos réservations',
                    'reservations' => $reservations
                ];

                $this->renderView('home/reservationList', $data);
                return;
            }

            $_SESSION['error'] = 'Aucune réservation trouvée avec cette adresse email.';
            header('Location: ' . BASE_URL . 'home/reservationTracking');
            exit;
        }

        // Si nous arrivons ici, c'est qu'aucune action valide n'a été effectuée
        $_SESSION['error'] = 'Veuillez fournir un code de suivi ou une adresse email.';
        header('Location: ' . BASE_URL . 'home/reservationTracking');
        exit;
    }

    /**
     * Affiche la page FAQ
     */
    public function faq()
    {
        $data = [
            'title' => 'Foire aux questions - ' . APP_NAME,
            'description' => 'Consultez les questions fréquemment posées sur ' . APP_NAME
        ];

        $this->renderView('home/faq', $data);
    }

    public function terms()
    {
        $data = [
            'title' => 'Conditions Générales d\'Utilisation - ' . APP_NAME,
            'description' => 'Consultez les conditions générales d\'utilisation de ' . APP_NAME
        ];

        $this->renderView('home/terms', $data);
    }

    public function privacy()
    {
        $data = [
            'title' => 'Politique de Confidentialité - ' . APP_NAME,
            'description' => 'Consultez notre politique de confidentialité et de protection des données'
        ];

        $this->renderView('home/privacy', $data);
    }

    /**
     * Affiche la page des offres d'emploi (carrières)
     */
    public function careers()
    {
        $data = [
            'title' => 'Carrières - ' . APP_NAME,
            'description' => 'Rejoignez notre équipe ! Découvrez nos offres d\'emploi et opportunités de carrière chez ' . APP_NAME
        ];

        $this->renderView('home/careers', $data);
    }


}
