<?php

// Contrôleur d'administration - Gestion centralisée des utilisateurs, places, réservations et tarifs
class AdminController extends BaseController
{
    private $userModel;
    private $reservationModel;
    private $placeModel;
    private $homeModel;
    private $logModel;
    private $subscriptionModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();

        // Initialise tous les modèles nécessaires
        $this->userModel = new UserModel();
        $this->reservationModel = new ReservationModel();
        $this->placeModel = new PlaceModel();
        $this->homeModel = new HomeModel();
        $this->logModel = new LogModel();
        $this->subscriptionModel = new SubscriptionModel();
    }

    // Tableau de bord d'administration avec statistiques
    public function dashboard()
    {
        $placeStats = $this->getPlaceStatistics();
        $reservationStats = $this->getReservationStatistics();

        $data = $this->setActiveMenu('dashboard') + [
            'title' => 'Tableau de bord - Administration',
            'userStats' => $this->getUserStatistics(),
            'placeStats' => $placeStats,
            'placeTypeStats' => $this->placeModel->countByType(),
            'reservationStats' => $reservationStats,
            'reservationByStatus' => $reservationStats['by_status'],
            'subscriptionStats' => $this->subscriptionModel->getSubscriptionStats(),
            'revenueStats' => $this->getRevenueStatistics(),
            'activeReservations' => $this->reservationModel->getActiveReservations(),
            'recentLogs' => $this->logModel->getAllLogs(10)
        ];

        $this->renderView('admin/dashboard', $data, 'admin');
    }

    // ====== GESTION DES UTILISATEURS ======

    // Liste des utilisateurs avec pagination et filtres
    public function users($page = 1)
    {
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Récupère les filtres de l'URL
        $roleFilter = $_GET['role'] ?? null;
        $statusFilter = $_GET['status'] ?? null;
        $sortFilter = $_GET['sort'] ?? 'created_at_desc';

        // Applique les filtres si nécessaire
        if ($roleFilter || $statusFilter || $sortFilter !== 'created_at_desc') {
            $users = $this->userModel->getFilteredUsers($roleFilter, $statusFilter, $sortFilter, $offset, $limit);
            $totalCount = $this->userModel->countFilteredUsers($roleFilter, $statusFilter);
        } else {
            $users = $this->userModel->getUsersPaginated($offset, $limit);
            $totalCount = $this->userModel->countUsers();
        }

        $data = $this->setActiveMenu('users') + [
            'title' => 'Gestion des utilisateurs - Administration',
            'users' => $users,
            'currentPage' => $page,
            'totalPages' => ceil($totalCount / $limit),
            'totalUsers' => $this->userModel->countUsers(),
            'newUsers' => $this->userModel->countNewUsersThisMonth(),
            'activeUsers' => $this->userModel->countActiveUsersLastMonth(),
            'role' => $roleFilter,
            'status' => $statusFilter,
            'sort' => $sortFilter
        ];

        $this->renderView('admin/users/index', $data, 'admin');
    }

    // Formulaire d'ajout d'utilisateur
    public function addUserForm()
    {
        $data = $this->setActiveMenu('users') + [
            'title' => 'Ajouter un utilisateur - Administration'
        ];

        $this->renderView('admin/users/add', $data, 'admin');
    }

    // Traitement d'ajout d'utilisateur avec validation
    public function addUser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'admin/addUserForm');
            return;
        }

        $validated = $this->validateRequiredFields([
            'nom',
            'prenom',
            'email',
            'password',
            'confirm_password'
        ]);

        if (!$validated) {
            return;
        }

        // Valide l'email et la correspondance des mots de passe
        if (!$this->isValidEmail($validated['email'])) {
            $this->redirectWithError(BASE_URL . 'admin/addUserForm', 'L\'adresse email est invalide.');
            return;
        }

        if ($validated['password'] !== $validated['confirm_password']) {
            $this->redirectWithError(BASE_URL . 'admin/addUserForm', 'Les mots de passe ne correspondent pas.');
            return;
        }

        // Vérifie l'unicité de l'email
        if ($this->userModel->getUserByEmail($validated['email'])) {
            $this->redirectWithError(BASE_URL . 'admin/addUserForm', 'Un utilisateur avec cette adresse email existe déjà.');
            return;
        }

        // Crée l'utilisateur
        $userData = [
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'password' => password_hash($validated['password'], PASSWORD_DEFAULT),
            'role' => $_POST['role'] ?? 'user',
            'telephone' => $_POST['telephone'] ?? null
        ];

        $userId = $this->userModel->addUser($userData);

        if ($userId) {
            $this->redirectWithSuccess(BASE_URL . 'admin/users', 'Utilisateur ajouté avec succès.');
        } else {
            $this->redirectWithError(BASE_URL . 'admin/addUserForm', 'Une erreur est survenue lors de l\'ajout de l\'utilisateur.');
        }
    }

    // Formulaire d'édition d'utilisateur
    public function editUser($userId = null)
    {
        $user = $this->validateAndGetUser($userId);
        if (!$user) return;

        $data = $this->setActiveMenu('users') + [
            'title' => 'Modifier un utilisateur - Administration',
            'user' => $user
        ];

        $this->renderView('admin/users/edit', $data, 'admin');
    }

    // Mise à jour d'utilisateur avec validation
    public function updateUser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'admin/users');
            return;
        }

        $userId = intval($_POST['id'] ?? 0);
        if (!$userId) {
            $this->redirectWithError(BASE_URL . 'admin/users', 'ID utilisateur invalide.');
            return;
        }

        $validated = $this->validateRequiredFields(['nom', 'prenom', 'email']);
        if (!$validated) return;

        if (!$this->isValidEmail($validated['email'])) {
            $this->redirectWithError(BASE_URL . 'admin/editUser/' . $userId, 'L\'adresse email est invalide.');
            return;
        }

        // Vérifie l'unicité de l'email pour un autre utilisateur
        $existingUser = $this->userModel->getUserByEmail($validated['email']);
        if ($existingUser && $existingUser['id'] != $userId) {
            $this->redirectWithError(BASE_URL . 'admin/editUser/' . $userId, 'Un autre utilisateur utilise déjà cette adresse email.');
            return;
        }

        $data = $validated + [
            'role' => $_POST['role'] ?? 'user',
            'telephone' => $_POST['telephone'] ?? null,
            'status' => $_POST['status'] ?? 'active'
        ];

        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        if ($this->userModel->updateUser($userId, $data)) {
            $this->redirectWithSuccess(BASE_URL . 'admin/users', 'L\'utilisateur a été modifié avec succès.');
        } else {
            $this->redirectWithError(BASE_URL . 'admin/editUser/' . $userId, 'Une erreur est survenue lors de la mise à jour de l\'utilisateur.');
        }
    }

    // Suppression d'utilisateur avec vérifications
    public function deleteUser($id = null)
    {
        if (!$this->validateUserDeletion($id)) return;

        if ($this->userModel->deleteUser($id)) {
            $this->redirectWithSuccess(BASE_URL . 'admin/users', 'Utilisateur supprimé avec succès.');
        } else {
            $this->redirectWithError(BASE_URL . 'admin/users', 'Impossible de supprimer cet utilisateur. Il possède probablement des réservations actives.');
        }
    }

    /**
     * Active/désactive un utilisateur
     */
    public function toggleUserStatus($userId = null)
    {
        if (!$userId) {
            $this->redirectWithError(BASE_URL . 'admin/users', 'ID utilisateur manquant.');
            return;
        }

        // Empêcher de désactiver son propre compte
        if ($userId == $_SESSION['user']['id']) {
            $this->redirectWithError(BASE_URL . 'admin/users', 'Vous ne pouvez pas modifier le statut de votre propre compte.');
            return;
        }

        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            $this->redirectWithError(BASE_URL . 'admin/users', 'Utilisateur non trouvé.');
            return;
        }

        $newStatus = ($user['status'] === 'active') ? 'inactive' : 'active';

        if ($this->userModel->updateUserStatusOnly($userId, $newStatus)) {
            $message = ($newStatus === 'active') ? 'Utilisateur activé avec succès.' : 'Utilisateur désactivé avec succès.';
            $this->redirectWithSuccess(BASE_URL . 'admin/users', $message);
        } else {
            $this->redirectWithError(BASE_URL . 'admin/users', 'Une erreur est survenue lors de la modification du statut.');
        }
    }

    /**
     * Supprime un utilisateur avec toutes ses réservations (avec confirmation)
     */
    public function forceDeleteUser($id = null)
    {
        if (!$id) {
            $this->redirectWithError(BASE_URL . 'admin/users', 'ID utilisateur manquant.');
            return;
        }

        // Empêcher la suppression de son propre compte
        if ($id == $_SESSION['user']['id']) {
            $this->redirectWithError(BASE_URL . 'admin/users', 'Vous ne pouvez pas supprimer votre propre compte.');
            return;
        }

        // Vérifier que l'utilisateur existe
        $user = $this->userModel->getUserById($id);
        if (!$user) {
            $this->redirectWithError(BASE_URL . 'admin/users', 'Utilisateur non trouvé.');
            return;
        }

        // Compter les réservations de l'utilisateur
        $reservations = $this->userModel->getUserReservations($id);
        $reservationCount = count($reservations);

        if ($this->userModel->deleteUserWithReservations($id)) {
            $message = "Utilisateur supprimé avec succès.";
            if ($reservationCount > 0) {
                $message .= " $reservationCount réservation(s) ont également été supprimée(s).";
            }
            $this->redirectWithSuccess(BASE_URL . 'admin/users', $message);
        } else {
            $this->redirectWithError(BASE_URL . 'admin/users', 'Erreur lors de la suppression de l\'utilisateur.');
        }
    }

    /**
     * Récupère les informations d'un utilisateur et ses réservations pour confirmation de suppression
     */
    public function getUserDeleteInfo($id = null)
    {
        if (!$id) {
            $this->jsonResponse(['success' => false, 'error' => 'ID utilisateur manquant.'], 400);
            return;
        }

        $user = $this->userModel->getUserById($id);
        if (!$user) {
            $this->jsonResponse(['success' => false, 'error' => 'Utilisateur non trouvé.'], 404);
            return;
        }

        $reservations = $this->userModel->getUserReservations($id);
        $reservationCount = count($reservations);

        // Compter les réservations par statut
        $statusCounts = [
            'en_cours' => 0,
            'confirmée' => 0,
            'en_attente' => 0,
            'terminee' => 0,
            'annulée' => 0
        ];

        foreach ($reservations as $reservation) {
            $status = $reservation['status'];
            if (isset($statusCounts[$status])) {
                $statusCounts[$status]++;
            }
        }

        $this->jsonResponse([
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom'] ?? '',
                'email' => $user['email']
            ],
            'reservations' => [
                'total' => $reservationCount,
                'status_counts' => $statusCounts,
                'details' => array_slice($reservations, 0, 5) // Limiter à 5 pour la preview
            ]
        ]);
    }

    // ====== GESTION DES RÉSERVATIONS ======

    /**
     * Gestion des réservations avec pagination
     */
    public function reservations($page = 1)
    {
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Récupérer les filtres de l'URL
        $statusFilter = $_GET['status'] ?? null;
        $dateFilter = $_GET['date'] ?? null;

        // Récupérer les statistiques et préparer les données pour les graphiques
        $reservationStats = $this->getReservationStatistics();
        $reservationByPlaceType = $this->reservationModel->countReservationsByPlaceType();

        // Appliquer les filtres
        $reservations = $this->reservationModel->getReservationsPaginated($offset, $limit, $statusFilter, $dateFilter);
        $totalCount = $this->reservationModel->countReservations($statusFilter, $dateFilter);

        $data = $this->setActiveMenu('reservations') + [
            'title' => 'Gestion des réservations - Administration',
            'reservations' => $reservations,
            'currentPage' => $page,
            'totalPages' => ceil($totalCount / $limit),
            'stats' => $reservationStats,
            'reservationByPlaceType' => $reservationByPlaceType,
            'currentStatusFilter' => $statusFilter,
            'currentDateFilter' => $dateFilter
        ];
        $this->renderView('admin/reservations/index', $data, 'admin');
    }

    /**
     * Détails d'une réservation
     */ public function viewReservation($id = null)
    {
        if (!$id) {
            $this->redirectWithError(BASE_URL . 'admin/reservations', 'ID de réservation manquant.');
            return;
        }

        $reservation = $this->reservationModel->getReservationById($id);
        if (!$reservation) {
            $this->redirectWithError(BASE_URL . 'admin/reservations', 'Réservation non trouvée.');
            return;
        }

        // Récupérer les informations de l'utilisateur si ce n'est pas une réservation invité
        $user = null;
        if ($reservation['user_id'] > 0) {
            $user = $this->userModel->getUserById($reservation['user_id']);
        }

        $data = $this->setActiveMenu('reservations') + [
            'title' => 'Détails de la réservation #' . $id . ' - Administration',
            'reservation' => $reservation,
            'user' => $user
        ];

        $this->renderView('admin/reservations/view', $data, 'admin');
    }

    /**
     * Annulation d'une réservation
     */
    public function cancelReservation($id = null)
    {
        if (!$id) {
            $this->redirectWithError(BASE_URL . 'admin/reservations', 'ID de réservation manquant.');
            return;
        }

        $reservation = $this->reservationModel->getReservationById($id);
        if (!$reservation) {
            $this->redirectWithError(BASE_URL . 'admin/reservations', 'Réservation non trouvée.');
            return;
        }

        if (in_array($reservation['status'], ['annulee', 'annulée', 'terminee', 'terminée'])) {
            $this->redirectWithError(BASE_URL . 'admin/reservations', 'Cette réservation ne peut pas être annulée.');
            return;
        }

        if ($this->reservationModel->cancelReservation($id)) {
            // Notifier l'utilisateur si ce n'est pas une réservation invité
            if ($reservation['user_id'] !== null) {
                $this->userModel->createNotification(
                    $reservation['user_id'],
                    'Réservation annulée',
                    'Votre réservation #' . $id . ' a été annulée par l\'administration.',
                    'warning'
                );
            }

            $this->redirectWithSuccess(BASE_URL . 'admin/reservations', 'La réservation a été annulée avec succès.');
        } else {
            $this->redirectWithError(BASE_URL . 'admin/reservations', 'Une erreur est survenue lors de l\'annulation de la réservation.');
        }
    }

    // ====== GESTION DES PLACES ======

    /**
     * Liste des places avec pagination et filtres
     */
    public function places($page = 1)
    {
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Récupérer les filtres de l'URL
        $typeFilter = $_GET['type'] ?? null;
        $statusFilter = $_GET['status'] ?? null;

        // Obtenir les statistiques des places
        $placeStats = $this->getPlaceStatistics();

        // Récupérer les places avec filtres
        if ($typeFilter || $statusFilter) {
            $places = $this->placeModel->getFilteredPlaces($typeFilter, $statusFilter, $offset, $limit);
            $totalCount = $this->placeModel->countFilteredPlaces($typeFilter, $statusFilter);
        } else {
            $places = $this->placeModel->getPlacesPaginated($offset, $limit);
            $totalCount = $this->placeModel->countPlaces();
        }

        // S'assurer que les données pour les graphiques sont bien structurées
        $data = $this->setActiveMenu('places') + [
            'title' => 'Gestion des places - Administration',
            'places' => $places,
            'currentPage' => $page,
            'totalPages' => ceil($totalCount / $limit),
            'stats' => $placeStats,
            // Pour les graphiques
            'placeStats' => $placeStats, // Données complètes
            'typeStats' => $placeStats['by_type'], // Spécifiquement les types de places
            // Filtres actuels
            'type' => $typeFilter,
            'status' => $statusFilter
        ];

        $this->renderView('admin/places/index', $data, 'admin');
    }

    /**
     * Ajout d'une place
     */
    public function addPlace()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $data = $this->setActiveMenu('places') + [
                'title' => 'Ajouter une place - Administration'
            ];

            $this->renderView('admin/places/add', $data, 'admin');
            return;
        }

        $validated = $this->validateRequiredFields(['numero', 'type']);
        if (!$validated) {
            $this->redirectWithError(BASE_URL . 'admin/addPlace', 'Tous les champs obligatoires doivent être remplis.');
            return;
        }

        $validated['status'] = $_POST['status'] ?? 'libre';

        if ($this->placeModel->createPlace($validated)) {
            $this->redirectWithSuccess(BASE_URL . 'admin/places', 'La place a été ajoutée avec succès.');
        } else {
            $this->redirectWithError(BASE_URL . 'admin/addPlace', 'Une erreur est survenue lors de l\'ajout de la place. Vérifiez que le numéro n\'est pas déjà utilisé.');
        }
    }

    /**
     * Édition d'une place
     */
    public function editPlace($placeId = null)
    {
        if (!$placeId) {
            $this->redirectWithError(BASE_URL . 'admin/places', 'ID de place manquant.');
            return;
        }

        $place = $this->placeModel->getById($placeId);
        if (!$place) {
            $this->redirectWithError(BASE_URL . 'admin/places', 'Place non trouvée.');
            return;
        }

        $data = $this->setActiveMenu('places') + [
            'title' => 'Modifier la place #' . $place['numero'] . ' - Administration',
            'place' => $place
        ];

        $this->renderView('admin/places/edit', $data, 'admin');
    }

    /**
     * Mise à jour d'une place
     */
    public function updatePlace()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'admin/places');
            return;
        }

        $placeId = intval($_POST['id'] ?? 0);
        if (!$placeId) {
            $this->redirectWithError(BASE_URL . 'admin/places', 'ID de place invalide.');
            return;
        }

        $validated = $this->validateRequiredFields(['numero', 'type', 'status']);
        if (!$validated) {
            $this->redirectWithError(BASE_URL . 'admin/editPlace/' . $placeId, 'Tous les champs obligatoires doivent être remplis.');
            return;
        }

        if ($this->placeModel->updatePlace($placeId, $validated)) {
            $this->redirectWithSuccess(BASE_URL . 'admin/places', 'La place a été mise à jour avec succès.');
        } else {
            $this->redirectWithError(BASE_URL . 'admin/editPlace/' . $placeId, 'Une erreur est survenue lors de la mise à jour de la place. Vérifiez que le numéro n\'est pas déjà utilisé par une autre place.');
        }
    }

    /**
     * Suppression d'une place
     */
    public function deletePlace($placeId = null)
    {
        if (!$placeId) {
            $this->redirectWithError(BASE_URL . 'admin/places', 'ID de place manquant.');
            return;
        }

        if ($this->placeModel->deletePlace($placeId)) {
            $this->redirectWithSuccess(BASE_URL . 'admin/places', 'La place a été supprimée avec succès.');
        } else {
            $this->redirectWithError(BASE_URL . 'admin/places', 'Cette place ne peut pas être supprimée car elle est associée à des réservations. Utilisez la fonction de maintenance pour la désactiver temporairement.');
        }
    }

    // ====== GESTION DES TARIFS ======

    /**
     * Gestion des tarifs
     */    public function tarifs()
    {
        // Récupérer la répartition des places par type
        $placesByType = $this->placeModel->countByType();

        $data = $this->setActiveMenu('tarifs') + [
            'title' => 'Gestion des tarifs - Administration',
            'tarifs' => $this->homeModel->getTarifs(),
            'tarifsHistory' => $this->homeModel->getTarifsHistory(20),
            'placesByType' => $placesByType
        ];

        $this->renderView('admin/tarifs/index', $data, 'admin');
    }

    /**
     * Mise à jour d'un tarif
     */
    public function updateTarif()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'admin/tarifs');
            return;
        }

        $tarifId = intval($_POST['id'] ?? 0);
        if (!$tarifId) {
            $this->redirectWithError(BASE_URL . 'admin/tarifs', 'ID de tarif invalide.');
            return;
        }

        $validated = $this->validateRequiredFields(['prix_heure', 'prix_journee', 'prix_mois']);
        if (!$validated) {
            $this->redirectWithError(BASE_URL . 'admin/tarifs', 'Tous les champs obligatoires doivent être remplis.');
            return;
        }

        // Convertir les valeurs numériques
        $validated['prix_heure'] = floatval(str_replace(',', '.', $validated['prix_heure']));
        $validated['prix_journee'] = floatval(str_replace(',', '.', $validated['prix_journee']));
        $validated['prix_mois'] = floatval(str_replace(',', '.', $validated['prix_mois']));
        $validated['free_minutes'] = intval($_POST['free_minutes'] ?? 0);

        if ($this->homeModel->updateTarif($tarifId, $validated)) {
            $this->redirectWithSuccess(BASE_URL . 'admin/tarifs', 'Le tarif a été mis à jour avec succès.');
        } else {
            $this->redirectWithError(BASE_URL . 'admin/tarifs', 'Une erreur est survenue lors de la mise à jour du tarif.');
        }
    }

    /**
     * Ajout d'un nouveau tarif
     */
    public function addTarif()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'admin/tarifs');
            return;
        }

        $validated = $this->validateRequiredFields(['type_place', 'prix_heure', 'prix_journee', 'prix_mois']);
        if (!$validated) {
            $this->redirectWithError(BASE_URL . 'admin/tarifs', 'Tous les champs obligatoires doivent être remplis.');
            return;
        }

        // Vérifier si un tarif existe déjà pour ce type de place
        $existingTarif = $this->homeModel->getTarifByType($validated['type_place']);
        if ($existingTarif) {
            $this->redirectWithError(BASE_URL . 'admin/tarifs', 'Un tarif existe déjà pour ce type de place.');
            return;
        }

        // Convertir les valeurs numériques
        $validated['prix_heure'] = floatval(str_replace(',', '.', $validated['prix_heure']));
        $validated['prix_journee'] = floatval(str_replace(',', '.', $validated['prix_journee']));
        $validated['prix_mois'] = floatval(str_replace(',', '.', $validated['prix_mois']));
        $validated['free_minutes'] = intval($_POST['free_minutes'] ?? 0);        // Validation des valeurs
        if ($validated['prix_heure'] <= 0 || $validated['prix_journee'] <= 0 || $validated['prix_mois'] <= 0) {
            $this->redirectWithError(BASE_URL . 'admin/tarifs', 'Les prix doivent être supérieurs à 0.');
            return;
        }

        if ($this->homeModel->addTarif(
            $validated['type_place'],
            $validated['prix_heure'],
            $validated['prix_journee'],
            $validated['prix_mois'],
            $validated['free_minutes']
        )) {
            $this->redirectWithSuccess(BASE_URL . 'admin/tarifs', 'Le tarif a été ajouté avec succès.');
        } else {
            $this->redirectWithError(BASE_URL . 'admin/tarifs', 'Une erreur est survenue lors de l\'ajout du tarif.');
        }
    }

    /**
     * Supprimer un tarif
     */
    public function deleteTarif($tarifId = null)
    {
        if (!$tarifId) {
            $this->redirectWithError(BASE_URL . 'admin/tarifs', 'ID du tarif manquant.');
            return;
        }

        // Vérifier si le tarif existe
        $tarif = $this->homeModel->getTarifById($tarifId);
        if (!$tarif) {
            $this->redirectWithError(BASE_URL . 'admin/tarifs', 'Tarif introuvable.');
            return;
        }

        // Vérifier s'il y a des réservations liées à ce tarif
        $reservationsCount = $this->homeModel->countReservationsByTarifType($tarif['type_place']);
        if ($reservationsCount > 0) {
            $this->redirectWithError(
                BASE_URL . 'admin/tarifs',
                'Impossible de supprimer ce tarif car il est utilisé par ' . $reservationsCount . ' réservation(s).'
            );
            return;
        }

        // Supprimer le tarif
        if ($this->homeModel->deleteTarif($tarifId)) {
            $this->redirectWithSuccess(BASE_URL . 'admin/tarifs', 'Le tarif a été supprimé avec succès.');
        } else {
            $this->redirectWithError(BASE_URL . 'admin/tarifs', 'Une erreur est survenue lors de la suppression du tarif.');
        }
    }

    // ====== MÉTHODES STATISTIQUES CONSOLIDÉES ======

    /**
     * Statistiques des utilisateurs
     */    /**
     * Statistiques des utilisateurs
     * @return array Tableau associatif des statistiques utilisateurs
     */
    private function getUserStatistics()
    {
        return [
            'total' => $this->userModel->countUsers(),
            'new_this_month' => $this->userModel->countNewUsersThisMonth(),
            'active_month' => $this->userModel->countActiveUsersLastMonth()
        ];
    }

    /**
     * Statistiques des places
     */    /**
     * Statistiques des places
     * @return array Tableau associatif des statistiques de places
     */
    private function getPlaceStatistics()
    {
        $byStatus = $this->placeModel->getPlacesByStatus();
        return [
            'total' => $this->placeModel->countTotal(),
            'by_status' => $byStatus,
            'by_type' => $this->placeModel->countByType(),
            // Ajouter directement les clés pour l'accès facile dans les vues
            'libre' => isset($byStatus['libre']) ? $byStatus['libre'] : 0,
            'occupe' => isset($byStatus['occupe']) ? $byStatus['occupe'] : 0,
            'maintenance' => isset($byStatus['maintenance']) ? $byStatus['maintenance'] : 0
        ];
    }

    /**
     * Statistiques des réservations
     */
    private function getReservationStatistics()
    {
        return [
            'total' => $this->reservationModel->countReservations(),
            'today' => $this->reservationModel->countTodayReservations(),
            'month' => $this->reservationModel->countMonthReservations(),
            'by_status' => $this->reservationModel->countByStatus()
        ];
    }
    /**
     * Change le statut d'un utilisateur (actif/inactif)
     */
    public function changeUserStatus($id = null)
    {
        if (!$id) {
            $this->redirectWithError(BASE_URL . 'admin/users', 'ID utilisateur manquant.');
            return;
        }

        // Empêcher la modification de son propre compte
        if ($id == $_SESSION['user']['id']) {
            $this->redirectWithError(BASE_URL . 'admin/users', 'Vous ne pouvez pas modifier votre propre statut.');
            return;
        }

        // Vérifier que l'utilisateur existe
        $user = $this->userModel->getUserById($id);
        if (!$user) {
            $this->redirectWithError(BASE_URL . 'admin/users', 'Utilisateur non trouvé.');
            return;
        }

        // Déterminer le nouveau statut
        $newStatus = ($user['status'] === 'actif') ? 'inactif' : 'actif';

        // Mettre à jour le statut
        $result = $this->userModel->updateUserStatus($id, $newStatus);

        if ($result) {
            // Journaliser l'action
            $this->logModel->addLog(
                $_SESSION['user']['id'],
                'user_status_change',
                "Statut utilisateur {$user['email']} changé vers {$newStatus}"
            );

            $this->redirectWithSuccess(
                BASE_URL . 'admin/users',
                "Statut de l'utilisateur {$user['nom']} {$user['prenom']} changé vers '{$newStatus}'."
            );
        } else {
            $this->redirectWithError(BASE_URL . 'admin/users', 'Erreur lors du changement de statut.');
        }
    }

    // Statistiques des revenus par période
    private function getRevenueStatistics()
    {
        return [
            'day' => $this->reservationModel->calculateRevenue('day'),
            'week' => $this->reservationModel->calculateRevenue('week'),
            'this_month' => $this->reservationModel->calculateRevenue('month'),
            'year' => $this->reservationModel->calculateRevenue('year'),
            'total' => $this->reservationModel->calculateRevenue('total')
        ];
    }

    // ====== MÉTHODES UTILITAIRES ======

    // Valide et récupère un utilisateur par ID
    private function validateAndGetUser($userId)
    {
        if (!$userId) {
            $this->redirectWithError(BASE_URL . 'admin/users', 'L\'ID utilisateur est requis.');
            return false;
        }

        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            $this->redirectWithError(BASE_URL . 'admin/users', 'Utilisateur non trouvé.');
            return false;
        }

        return $user;
    }

    // Valide les conditions de suppression d'un utilisateur
    private function validateUserDeletion($id)
    {
        if (!$id) {
            $this->redirectWithError(BASE_URL . 'admin/users', 'ID utilisateur manquant.');
            return false;
        }

        // Empêche la suppression de son propre compte
        if ($id == $_SESSION['user']['id']) {
            $this->redirectWithError(BASE_URL . 'admin/users', 'Vous ne pouvez pas supprimer votre propre compte.');
            return false;
        }

        return true;
    }
}
