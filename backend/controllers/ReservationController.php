<?php

/**
 * Contrôleur de réservation consolidé et optimisé
 * Gère toutes les opérations liées aux réservations: création, paiement, suivi, etc.
 */
class ReservationController extends BaseController
{
    private $reservationModel;
    private $userModel;
    private $placeModel;
    private $subscriptionModel;
    private $logModel;
    private $homeModel;
    public function __construct()
    {
        parent::__construct();
        $this->reservationModel = new ReservationModel();
        $this->userModel = new UserModel();
        $this->placeModel = new PlaceModel();
        $this->subscriptionModel = new SubscriptionModel();
        $this->logModel = new LogModel();
        $this->homeModel = new HomeModel();
    }
    /**
     * Crée une nouvelle réservation pour un utilisateur connecté
     */
    public function reserve()
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['success' => false, 'error' => 'Vous devez être connecté pour réserver.'], 401);
            return;
        }

        // Vérifier les données requises avec la validation AJAX
        $validated = $this->validateRequiredFieldsAjax(['place_id', 'date_debut', 'duree']);
        if (!$validated) {
            // Identifier le champ manquant
            $missingFields = [];
            foreach (['place_id', 'date_debut', 'duree'] as $field) {
                if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
                    $missingFields[] = $field;
                }
            }

            $this->jsonResponse([
                'success' => false,
                'error' => 'Champs obligatoires manquants : ' . implode(', ', $missingFields)
            ], 400);
            return;
        }

        // Récupérer et valider les données
        $placeId = intval($validated['place_id']);
        $dateDebut = $validated['date_debut'];
        $duree = floatval($validated['duree']);

        // Validation de la durée: min 15 minutes, max 24 heures
        if (!is_numeric($duree) || $duree <= 0) {
            $this->jsonResponse(['success' => false, 'error' => 'La durée doit être un nombre positif.'], 400);
            return;
        }

        // Convertir en minutes pour la validation
        $dureeMinutes = $duree * 60;
        if ($dureeMinutes < 15) {
            $this->jsonResponse(['success' => false, 'error' => 'La durée minimale de réservation est de 15 minutes.'], 400);
            return;
        }

        if ($duree > 24) {
            $this->jsonResponse(['success' => false, 'error' => 'La durée maximale de réservation est de 24 heures.'], 400);
            return;
        }

        // Vérifier si la place existe
        $place = $this->placeModel->getById($placeId);
        if (!$place) {
            $this->jsonResponse(['success' => false, 'error' => 'Cette place n\'existe pas.'], 404);
            return;
        }

        // Formater la date de début correctement (convertir du format HTML5 datetime-local)
        try {
            $dateDebutObj = new DateTime($dateDebut);
            $dateDebutFormatted = $dateDebutObj->format('Y-m-d H:i:s');

            // Calculer la date de fin
            $dateFinObj = clone $dateDebutObj;
            $dateFinObj->modify("+{$dureeMinutes} minutes");
            $dateFin = $dateFinObj->format('Y-m-d H:i:s');
        } catch (Exception) {
            $this->jsonResponse(['success' => false, 'error' => 'Format de date invalide.'], 400);
            return;
        }

        // Vérifier que la date de début est dans le futur (avec une tolérance de 5 minutes)
        $now = new DateTime();
        $toleranceTime = clone $now;
        $toleranceTime->modify('+5 minutes');

        if ($dateDebutObj < $toleranceTime) {
            $this->jsonResponse(['success' => false, 'error' => 'La date de début doit être d\'au moins 5 minutes dans le futur.'], 400);
            return;
        }

        // Vérifier la disponibilité de la place avec un message d'erreur détaillé
        $availability = $this->reservationModel->checkPlaceAvailabilityDetailed($placeId, $dateDebutFormatted, $dateFin);
        if (!$availability['available']) {
            $this->jsonResponse(['success' => false, 'error' => $availability['reason']], 409);
            return;
        }

        // Créer la réservation
        $reservationId = $this->reservationModel->createReservation(
            $_SESSION['user']['id'],
            $placeId,
            $dateDebutFormatted,
            $dateFin
        );

        if ($reservationId) {
            // Journaliser l'action
            $this->logModel->addLog($_SESSION['user']['id'], 'reservation_create', 'Nouvelle réservation créée: ' . $reservationId);

            // Récupérer la réservation créée pour déterminer la redirection
            $reservation = $this->reservationModel->getReservationById($reservationId);
            // Envoyer une notification de confirmation de réservation
            if ($reservation && $place) {
                $this->userModel->sendReservationReminderNotification(
                    $_SESSION['user']['id'],
                    $reservationId,
                    'Place ' . $place['numero'],
                    $reservation['date_debut']
                );
            }
            $redirectUrl = buildUrl('reservation/payment/' . $reservationId);

            // Si la réservation est déjà terminée (cas d'une réservation immédiate passée)
            if ($reservation && $reservation['status'] === 'terminée') {
                $redirectUrl = buildUrl('reservation/end/' . $reservationId);
            }
            // Si la réservation est gratuite (montant = 0), rediriger vers confirmation
            elseif ($reservation && floatval($reservation['montant_total']) == 0) {
                $redirectUrl = buildUrl('reservation/confirmation/' . $reservationId);
            }

            $this->jsonResponse([
                'success' => true,
                'reservation_id' => $reservationId,
                'montant' => number_format($reservation['montant_total'], 2),
                'redirect_url' => $redirectUrl
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la création de la réservation.'], 500);
        }
    }
    /**
     * Gère le paiement d'une réservation
     */
    public function payment($id = null)
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->isAuthenticated()) {
            $this->redirect('auth/login');
            return;
        }

        // Vérifier si l'ID de réservation est valide
        if (!$id) {
            $this->redirectWithError('auth/profile', 'ID de réservation non spécifié.');
            return;
        }

        // Récupérer la réservation
        $reservation = $this->reservationModel->getReservationById($id);

        // Vérifier si la réservation existe et appartient à l'utilisateur
        if (!$reservation || $reservation['user_id'] != $_SESSION['user']['id']) {
            $this->redirectWithError('auth/profile', 'Réservation non trouvée ou accès non autorisé.');
            return;
        }

        // Vérifier si la réservation n'est pas annulée ou expirée
        if (in_array($reservation['status'], ['annulée', 'annulee', 'expirée', 'expiree'])) {
            $this->redirectWithError('auth/profile', 'Cette réservation a été annulée et ne peut plus être payée.');
            return;
        }

        // Vérifier si la réservation n'est pas déjà payée
        $payment = $this->reservationModel->getPaymentByReservationId($id);
        if ($payment && $payment['status'] === 'valide') {
            $this->redirectWithSuccess('reservation/confirmation/' . $id, 'Cette réservation est déjà payée.');
            return;
        }

        $data = [
            'title' => 'Paiement de votre réservation - ' . APP_NAME,
            'description' => 'Finalisez votre réservation en effectuant le paiement',
            'active_page' => 'reservation',
            'reservation' => $reservation,
            'modes_payment' => ['carte', 'paypal', 'virement']
        ];

        // Vérifier si c'est le paiement d'une réservation immédiate
        $isImmediatePayment = isset($_SESSION['immediate_payment']) && $_SESSION['immediate_payment']['reservation_id'] == $id;
        if ($isImmediatePayment) {
            $data['immediate_payment'] = $_SESSION['immediate_payment'];
        }

        // Récupérer les avantages d'abonnement de l'utilisateur
        $activeSubscriptions = $this->subscriptionModel->getUserActiveSubscriptions($_SESSION['user']['id']);
        $subscriptionBenefits = null;

        if (!empty($activeSubscriptions)) {
            // Prendre le premier abonnement actif (le plus récent)
            $subscriptionBenefits = $activeSubscriptions[0];
        }

        $data['subscription_benefits'] = $subscriptionBenefits;

        // Recalculer le montant avec les avantages d'abonnement
        if ($subscriptionBenefits) {
            $originalAmount = $reservation['montant_total'];

            // Calculer la durée en minutes
            $dateDebut = new DateTime($reservation['date_debut']);
            $dateFin = new DateTime($reservation['date_fin']);
            $dureeMinutes = ($dateFin->getTimestamp() - $dateDebut->getTimestamp()) / 60;

            // Appliquer les minutes gratuites
            $freeMinutes = $subscriptionBenefits['free_minutes'] ?? 0;
            $billedMinutes = max(0, $dureeMinutes - $freeMinutes);

            if ($billedMinutes > 0) {
                // Recalculer le montant basé sur les minutes facturables
                $tarif = $originalAmount / ($dureeMinutes / 60); // Tarif horaire
                $newAmount = ($billedMinutes / 60) * $tarif;

                // Appliquer la réduction d'abonnement
                $discountPercent = $subscriptionBenefits['discount_percent'] ?? 0;
                if ($discountPercent > 0) {
                    $newAmount = $newAmount * (1 - ($discountPercent / 100));
                }

                $data['original_amount'] = $originalAmount;
                $data['final_amount'] = max(0, $newAmount);
                $data['total_savings'] = $originalAmount - $data['final_amount'];
            } else {
                // Entièrement gratuit avec les minutes gratuites
                $data['original_amount'] = $originalAmount;
                $data['final_amount'] = 0;
                $data['total_savings'] = $originalAmount;
            }
        }

        // Traiter le paiement si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validated = $this->validateRequiredFields(['mode_paiement']);
            if (!$validated) {
                $data['error'] = 'Veuillez sélectionner un mode de paiement.';
            } else {
                $modePayment = $validated['mode_paiement'];

                // Validation des conditions d'utilisation
                if (!isset($_POST['conditions'])) {
                    $data['error'] = 'Vous devez accepter les conditions d\'utilisation.';
                } else {
                    $montant = isset($data['final_amount']) ? $data['final_amount'] : $reservation['montant_total'];

                    // Créer l'enregistrement de paiement
                    $paymentId = $this->reservationModel->createPayment($id, $montant, $modePayment);

                    if ($paymentId) {
                        /* Générer une facture */
                        $this->reservationModel->generateInvoice($paymentId);

                        /* Journaliser l'action */
                        $this->logModel->addLog($_SESSION['user']['id'], 'payment_create', 'Paiement effectué: ' . $paymentId);

                        // Nettoyer les données de session pour le paiement immédiat
                        if ($isImmediatePayment) {
                            unset($_SESSION['immediate_payment']);
                        }

                        $this->redirectWithSuccess('reservation/confirmation/' . $id, 'Paiement effectué avec succès!');
                    } else {
                        $data['error'] = 'Une erreur est survenue lors du traitement du paiement.';
                    }
                }
            }
        }

        $this->renderView('reservation/payment', $data);
    }
    /**
     * Affiche la confirmation d'une réservation
     */
    public function confirmation($id = null)
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->isAuthenticated()) {
            $this->redirect('auth/login');
            return;
        }

        // Vérifier si l'ID de réservation est valide
        if (!$id) {
            $this->redirectWithError('auth/profile', 'ID de réservation non spécifié.');
            return;
        }

        // Récupérer la réservation avec ses détails
        $reservation = $this->reservationModel->getReservationById($id);

        // Vérifier si la réservation existe et appartient à l'utilisateur
        if (!$reservation || $reservation['user_id'] != $_SESSION['user']['id']) {
            $this->redirectWithError('auth/profile', 'Réservation non trouvée ou accès non autorisé.');
            return;
        }

        // Récupérer les informations de paiement associé
        $payment = null;
        if ($reservation['montant_total'] > 0) {
            $payment = $this->reservationModel->getPaymentByReservationId($id);
        }

        // Déterminer le code de sortie à afficher
        $code_sortie = '';
        $status_normalized = strtolower(str_replace(['é', 'è', 'ê'], 'e', $reservation['status']));
        if (in_array($status_normalized, ['terminee', 'confirmee']) || strpos($status_normalized, 'termine') !== false) {
            $code_sortie = $reservation['code_sortie'];
        }

        // Récupérer les informations de paiement immédiat si disponibles
        $immediate_payment_info = null;
        if (isset($_SESSION['immediate_payment']) && $_SESSION['immediate_payment']['reservation_id'] == $id) {
            $immediate_payment_info = $_SESSION['immediate_payment'];
            // Ajouter les détails de durée si pas déjà présents
            if (!isset($immediate_payment_info['duree_minutes']) && $reservation['date_debut'] && $reservation['date_fin']) {
                $dateDebut = new DateTime($reservation['date_debut']);
                $dateFin = new DateTime($reservation['date_fin']);
                $dureeMinutes = ($dateFin->getTimestamp() - $dateDebut->getTimestamp()) / 60;
                $immediate_payment_info['duree_minutes'] = $dureeMinutes;
            }
            // Nettoyer la session après utilisation
            unset($_SESSION['immediate_payment']);
        } else if ($reservation['date_debut'] && $reservation['date_fin']) {
            // Pour les réservations terminées, calculer la durée pour déterminer si c'est une réservation immédiate
            $dateDebut = new DateTime($reservation['date_debut']);
            $dateFin = new DateTime($reservation['date_fin']);
            $dureeMinutes = ($dateFin->getTimestamp() - $dateDebut->getTimestamp()) / 60;
            $immediate_payment_info = [
                'duree_minutes' => $dureeMinutes,
                'montant' => $reservation['montant_total']
            ];
        }

        // Vérifier si c'est une réservation immédiate basée sur plusieurs critères
        $is_immediate = false;

        // 1. Si le statut contient "immediat"
        if (strpos(strtolower($reservation['status']), 'immediat') !== false) {
            $is_immediate = true;
        }
        // 2. Si c'est une réservation terminée avec une durée très courte (moins de 6 heures)
        // ET qu'elle a un code de sortie (indicateur d'une réservation immédiate terminée)
        else if (
            strpos(strtolower(str_replace(['é', 'è', 'ê'], 'e', $reservation['status'])), 'termine') !== false &&
            isset($immediate_payment_info['duree_minutes']) &&
            $immediate_payment_info['duree_minutes'] < 360 &&
            !empty($reservation['code_sortie'])
        ) {
            $is_immediate = true;
        }
        // 3. Si on a des informations de paiement immédiat en session
        else if (isset($_SESSION['immediate_payment'])) {
            $is_immediate = true;
        }

        // Si c'est une réservation immédiate terminée, s'assurer que le code de sortie est défini
        if ($is_immediate && !empty($reservation['code_sortie'])) {
            $code_sortie = $reservation['code_sortie'];
        }

        // Récupérer les avantages d'abonnement pour les réservations immédiates
        $subscriptionBenefits = null;
        $original_amount = null;
        $final_amount = null;
        $total_savings = null;

        if ($is_immediate) {
            $activeSubscriptions = $this->subscriptionModel->getUserActiveSubscriptions($_SESSION['user']['id']);

            if (!empty($activeSubscriptions)) {
                $subscriptionBenefits = $activeSubscriptions[0];

                // Pour les réservations immédiates terminées avec paiement
                if ($payment && isset($immediate_payment_info['duree_minutes'])) {
                    // Le montant payé est le montant final (avec réductions appliquées)
                    $final_amount = floatval($payment['montant']);

                    // Le montant original est celui stocké dans la réservation
                    $original_amount = floatval($reservation['montant_total']);

                    // Les économies sont la différence
                    $total_savings = $original_amount - $final_amount;
                }
            }
        }

        // Debug : afficher les informations pour diagnostiquer
        error_log("DEBUG Confirmation - Réservation ID: " . $id);
        error_log("DEBUG Confirmation - Statut: " . $reservation['status']);
        error_log("DEBUG Confirmation - Code sortie DB: " . ($reservation['code_sortie'] ?? 'NULL'));
        error_log("DEBUG Confirmation - Code sortie final: " . $code_sortie);
        error_log("DEBUG Confirmation - Is immediate: " . ($is_immediate ? 'true' : 'false'));
        if (isset($immediate_payment_info)) {
            error_log("DEBUG Confirmation - Durée: " . ($immediate_payment_info['duree_minutes'] ?? 'NULL') . " minutes");
        }

        // Vérification finale pour l'affichage du code de sortie
        error_log("DEBUG Confirmation - Condition affichage code sortie (\$is_immediate && !\$empty(\$reservation['code_sortie'])): " .
            ($is_immediate && !empty($reservation['code_sortie']) ? 'TRUE' : 'FALSE'));
        $data = [
            'title' => 'Confirmation de réservation - ' . APP_NAME,
            'description' => 'Votre réservation a bien été enregistrée',
            'active_page' => 'reservation',
            'reservation' => $reservation,
            'payment' => $payment,
            'code_sortie' => $code_sortie,
            'immediate_payment_info' => $immediate_payment_info,
            'is_immediate' => $is_immediate,
            'subscription_benefits' => $subscriptionBenefits,
            'original_amount' => $original_amount,
            'final_amount' => $final_amount,
            'total_savings' => $total_savings
        ];

        $this->renderView('reservation/confirmation', $data);
    }
    /**
     * Annule une réservation utilisateur
     */
    public function cancel($id = null)
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->isAuthenticated()) {
            $this->redirect('auth/login');
            return;
        }

        // Vérifier si l'ID de réservation est valide (via URL ou POST)
        if (!$id && isset($_POST['reservation_id'])) {
            $id = intval($_POST['reservation_id']);
        }

        if (!$id) {
            $this->redirectWithError('auth/profile', 'ID de réservation non spécifié.');
            return;
        }

        // Récupérer la réservation
        $reservation = $this->reservationModel->getReservationById($id);

        // Vérifier si la réservation existe et appartient à l'utilisateur
        if (!$reservation || $reservation['user_id'] != $_SESSION['user']['id']) {
            $this->redirectWithError('auth/profile', 'Réservation non trouvée ou accès non autorisé.');
            return;
        }

        // Vérifier si la réservation peut être annulée
        $now = new DateTime();
        $dateDebut = new DateTime($reservation['date_debut']);

        // Règles d'annulation :
        // 1. Les réservations terminées, en cours ou annulées ne peuvent pas être annulées
        if (in_array($reservation['status'], ['terminée', 'en_cours', 'annulée', 'en_cours_immediat'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->jsonResponse(['success' => false, 'error' => 'Cette réservation ne peut pas être annulée.'], 400);
            } else {
                $this->redirectWithError('auth/profile', 'Cette réservation ne peut pas être annulée.');
            }
            return;
        }

        // 2. Les réservations confirmées ne peuvent être annulées que si elles commencent dans plus de 1 heure
        if ($reservation['status'] === 'confirmée') {
            $timeDiff = $dateDebut->getTimestamp() - $now->getTimestamp();
            if ($timeDiff < 3600) { // moins d'1 heure
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->jsonResponse(['success' => false, 'error' => 'Cette réservation ne peut plus être annulée (trop proche du début).'], 400);
                } else {
                    $this->redirectWithError('auth/profile', 'Cette réservation ne peut plus être annulée (trop proche du début).');
                }
                return;
            }
        }

        // Annuler la réservation
        $result = $this->reservationModel->cancelReservation($id);
        if ($result) {
            // Journaliser l'action
            $this->logModel->addLog($_SESSION['user']['id'], 'reservation_cancel', 'Réservation annulée: ' . $id);

            // Supprimer les notifications obsolètes liées à cette réservation
            $this->cleanupReservationNotifications($id);

            // Notifier l'utilisateur de l'annulation
            $this->userModel->createNotification(
                $_SESSION['user']['id'],
                'Réservation annulée',
                'Votre réservation #' . $id . ' a été annulée.',
                'info'
            );

            // Réponse selon le type de requête
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
                $this->jsonResponse(['success' => true, 'message' => 'Réservation annulée avec succès.']);
            } else {
                $this->redirectWithSuccess('auth/profile', 'Votre réservation a été annulée avec succès.');
            }
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
                $this->jsonResponse(['success' => false, 'error' => 'Une erreur est survenue lors de l\'annulation.'], 500);
            } else {
                $this->redirectWithError('auth/profile', 'Une erreur est survenue lors de l\'annulation de votre réservation.');
            }
        }
    }

    /**
     * Crée une réservation en mode invité (sans compte)
     */
    public function guestReserve()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL);
            return;
        }

        // Vérifier les données requises
        $validated = $this->validateRequiredFields([
            'place_id',
            'date_debut',
            'duree',
            'guest_name',
            'guest_email'
        ]);

        if (!$validated) {
            $this->jsonResponse(['success' => false, 'error' => 'Tous les champs requis doivent être remplis.'], 400);
            return;
        }

        $placeId = intval($validated['place_id']);
        $dateDebut = $validated['date_debut'];
        $duree = floatval($validated['duree']);
        $guestName = $validated['guest_name'];
        $guestEmail = $validated['guest_email'];
        $guestPhone = $_POST['guest_phone'] ?? null;

        // Validation de l'email
        if (!$this->isValidEmail($guestEmail)) {
            $this->jsonResponse(['success' => false, 'error' => 'Adresse email invalide.'], 400);
            return;
        }

        // Validation de la durée
        if (!is_numeric($duree) || $duree <= 0) {
            $this->jsonResponse(['success' => false, 'error' => 'La durée doit être un nombre positif.'], 400);
            return;
        }

        $dureeMinutes = $duree * 60;
        if ($dureeMinutes < 15) {
            $this->jsonResponse(['success' => false, 'error' => 'La durée minimale de réservation est de 15 minutes.'], 400);
            return;
        }

        if ($duree > 24) {
            $this->jsonResponse(['success' => false, 'error' => 'La durée maximale de réservation est de 24 heures.'], 400);
            return;
        }

        // Vérifier si la place existe
        $place = $this->placeModel->getById($placeId);
        if (!$place) {
            $this->jsonResponse(['success' => false, 'error' => 'Cette place n\'existe pas.'], 404);
            return;
        }

        // Formater les dates
        try {
            $dateDebutObj = new DateTime($dateDebut);
            $dateDebutFormatted = $dateDebutObj->format('Y-m-d H:i:s');

            $dateFinObj = clone $dateDebutObj;
            $dateFinObj->modify("+{$dureeMinutes} minutes");
            $dateFin = $dateFinObj->format('Y-m-d H:i:s');
        } catch (Exception) {
            $this->jsonResponse(['success' => false, 'error' => 'Format de date invalide.'], 400);
            return;
        }

        // Vérifier que la date de début est dans le futur (avec une tolérance de 5 minutes)
        $now = new DateTime();
        $toleranceTime = clone $now;
        $toleranceTime->modify('+5 minutes');

        if ($dateDebutObj < $toleranceTime) {
            $this->jsonResponse(['success' => false, 'error' => 'La date de début doit être d\'au moins 5 minutes dans le futur.'], 400);
            return;
        }

        // Vérifier la disponibilité de la place avec un message d'erreur détaillé
        $availability = $this->reservationModel->checkPlaceAvailabilityDetailed($placeId, $dateDebutFormatted, $dateFin);
        if (!$availability['available']) {
            $this->jsonResponse(['success' => false, 'error' => $availability['reason']], 409);
            return;
        }

        // Créer la réservation invité
        $result = $this->reservationModel->createGuestReservation(
            $placeId,
            $dateDebutFormatted,
            $dateFin,
            $guestName,
            $guestEmail,
            $guestPhone
        );

        if ($result && is_array($result)) {
            $reservationId = $result['reservation_id'];
            $guestToken = $result['guest_token'];

            /* Journaliser l'action */
            $this->logModel->addLog(1, 'guest_reservation_create', 'Réservation invité créée: ' . $reservationId);

            // Définir le jeton invité dans la session pour le paiement
            $_SESSION['guest_token'] = $guestToken;
            $this->jsonResponse([
                'success' => true,
                'reservation_id' => $reservationId,
                'guest_token' => $guestToken,
                'redirect_url' => buildUrl('reservation/guestPayment/' . $reservationId . '/' . $guestToken)
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la création de la réservation.'], 500);
        }
    }

    /**
     * Gère le paiement pour une réservation invité
     */
    public function guestPayment($id = null, $token = null)
    {
        // Vérifier si l'ID et le jeton sont valides
        if (!$id || !$token) {
            $this->redirect(BASE_URL);
            return;
        }

        // Récupérer la réservation par ID et jeton
        $reservation = $this->reservationModel->getReservationByGuestToken($token);

        // Vérifier si la réservation existe et correspond à l'ID
        if (!$reservation || $reservation['id'] != $id) {
            $this->redirectWithError(BASE_URL, 'Réservation non trouvée ou accès non autorisé.');
            return;
        }

        // Vérifier si la réservation n'est pas déjà payée
        $payment = $this->reservationModel->getPaymentByReservationId($id);
        if ($payment && $payment['status'] === 'valide') {
            $this->redirect('reservation/guestConfirmation/' . $id . '/' . $token);
            return;
        }

        $data = [
            'title' => 'Paiement de votre réservation - ' . APP_NAME,
            'description' => 'Finalisez votre réservation en effectuant le paiement',
            'reservation' => $reservation,
            'token' => $token,
            'modes_payment' => ['carte', 'paypal', 'virement']
        ];

        // Traiter le paiement si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validated = $this->validateRequiredFields(['mode_payment']);
            if (!$validated) {
                $data['error'] = 'Veuillez sélectionner un mode de paiement.';
            } else {
                $modePayment = $validated['mode_payment'];
                $montant = $reservation['montant_total'];

                // Créer l'enregistrement de paiement pour invité
                $paymentId = $this->reservationModel->createGuestPayment($id, $montant, $modePayment);

                if ($paymentId) {
                    /* Générer une facture */
                    $this->reservationModel->generateInvoice($paymentId);

                    /* Journaliser l'action */
                    $this->logModel->addLog(1, 'guest_payment_create', 'Paiement invité effectué: ' . $paymentId);

                    $this->redirect('reservation/guestConfirmation/' . $id . '/' . $token);
                } else {
                    $data['error'] = 'Une erreur est survenue lors du traitement du paiement.';
                }
            }
        }

        $this->renderGuestView('reservation/guest_payment', $data);
    }

    /**
     * Affiche la confirmation d'une réservation invité
     */
    public function guestConfirmation($id = null, $token = null)
    {
        // Vérifier si l'ID et le jeton sont valides
        if (!$id || !$token) {
            $this->redirect(BASE_URL);
            return;
        }

        // Récupérer la réservation par ID et jeton
        $reservation = $this->reservationModel->getReservationByGuestToken($token);

        // Vérifier si la réservation existe et correspond à l'ID
        if (!$reservation || $reservation['id'] != $id) {
            $this->redirectWithError(BASE_URL, 'Réservation non trouvée ou accès non autorisé.');
            return;
        }

        // Récupérer les données de paiement
        $payment = $this->reservationModel->getPaymentByReservationId($id);

        $data = [
            'title' => 'Confirmation de réservation - ' . APP_NAME,
            'description' => 'Votre réservation a bien été enregistrée',
            'reservation' => $reservation,
            'payment' => $payment,
            'token' => $token
        ];

        $this->renderGuestView('reservation/guest_confirmation', $data);
    }

    /**
     * Permet de suivre une réservation invité avec le token
     */
    public function trackReservation($token = null)
    {
        if (!$token) {
            $this->redirect('home/reservationTracking');
            return;
        }

        $reservation = $this->reservationModel->getReservationByGuestToken($token);

        if (!$reservation) {
            $this->redirectWithError('home/reservationTracking', 'Réservation non trouvée avec ce code de suivi.');
            return;
        }

        $_SESSION['guest_token'] = $token;

        // Récupérer les données de paiement
        $payment = $this->reservationModel->getPaymentByReservationId($reservation['id']);

        $data = [
            'title' => 'Suivi de réservation - ' . APP_NAME,
            'description' => 'Suivez l\'état de votre réservation',
            'reservation' => $reservation,
            'payment' => $payment,
            'token' => $token
        ];

        $this->renderGuestView('reservation/guest_tracking', $data);
    }

    /**
     * Annule une réservation invité
     */
    public function cancelGuestReservation($id = null, $token = null)
    {
        if (!$id || !$token) {
            $this->redirect(BASE_URL);
            return;
        }

        $reservation = $this->reservationModel->getReservationByGuestToken($token);

        if (!$reservation || $reservation['id'] != $id) {
            $this->redirectWithError(BASE_URL, 'Réservation non trouvée ou accès non autorisé.');
            return;
        }

        if (!in_array($reservation['status'], ['en_attente', 'confirmee', 'confirmée'])) {
            $this->redirectWithError(
                'reservation/trackReservation/' . $token,
                'Cette réservation ne peut pas être annulée.'
            );
            return;
        }

        $result = $this->reservationModel->cancelGuestReservation($id, $token);

        if ($result) {
            // Journaliser l'action
            $this->logModel->addLog(1, 'guest_reservation_cancel', 'Réservation invité annulée: ' . $id);
            $this->redirectWithSuccess(
                'reservation/trackReservation/' . $token,
                'Votre réservation a été annulée avec succès.'
            );
        } else {
            $this->redirectWithError(
                'reservation/trackReservation/' . $token,
                'Une erreur est survenue lors de l\'annulation de votre réservation.'
            );
        }
    }

    /**
     * Gère une réservation immédiate
     */
    public function reserveImmediate()
    {
        // Vérifier si c'est une requête AJAX
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        // Vérifier si l'utilisateur est connecté
        if (!$this->isAuthenticated()) {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'error' => 'Vous devez être connecté pour une réservation immédiate.'], 401);
            } else {
                $_SESSION['error'] = 'Vous devez être connecté pour une réservation immédiate.';
                header('Location: ' . buildUrl('auth/login'));
                exit;
            }
            return;
        }

        // Valider les données
        $validated = $this->validateRequiredFields(['place_id']);
        if (!$validated) {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'error' => 'ID de place manquant.'], 400);
            } else {
                $_SESSION['error'] = 'ID de place manquant.';
                header('Location: ' . buildUrl('home/places'));
                exit;
            }
            return;
        }

        $placeId = intval($validated['place_id']);

        // Vérifier si la place existe et est disponible
        $place = $this->placeModel->getById($placeId);
        if (!$place) {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'error' => 'Cette place n\'existe pas.'], 404);
            } else {
                $_SESSION['error'] = 'Cette place n\'existe pas.';
                header('Location: ' . buildUrl('home/places'));
                exit;
            }
            return;
        }

        if ($place['status'] !== 'libre') {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'error' => 'Cette place n\'est pas disponible actuellement.'], 409);
            } else {
                $_SESSION['error'] = 'Cette place n\'est pas disponible actuellement. Veuillez choisir une autre place.';
                header('Location: ' . buildUrl('home/places'));
                exit;
            }
            return;
        }

        // Vérifier si l'utilisateur n'a pas déjà une réservation immédiate en cours
        $activeReservations = $this->reservationModel->getUserActiveImmediateReservations($_SESSION['user']['id']);
        if (!empty($activeReservations)) {
            if ($isAjax) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Vous avez déjà une réservation immédiate en cours.',
                    'reservation_id' => $activeReservations[0]['id']
                ], 409);
            } else {
                $_SESSION['error'] = 'Vous avez déjà une réservation immédiate en cours.';
                header('Location: ' . buildUrl('reservation/immediate/' . $activeReservations[0]['id']));
                exit;
            }
            return;
        }

        // Créer une réservation immédiate
        $reservationId = $this->reservationModel->createImmediateReservation($_SESSION['user']['id'], $placeId);
        if ($reservationId) {
            // Récupérer les détails de la réservation créée
            $reservation = $this->reservationModel->getReservationById($reservationId);

            // Journaliser l'action
            $this->logModel->addLog($_SESSION['user']['id'], 'immediate_reservation_create', 'Réservation immédiate créée: ' . $reservationId);
            // Envoyer une notification de début de réservation immédiate
            if ($reservation && isset($reservation['code_acces'])) {
                $this->userModel->sendImmediateReservationStartNotification(
                    $_SESSION['user']['id'],
                    $reservationId,
                    'Place ' . $place['numero'],
                    $reservation['code_acces']
                );
            }

            $this->handleResponse(
                $isAjax,
                ['success' => true, 'reservation_id' => $reservationId, 'redirect_url' => buildUrl('reservation/immediate/' . $reservationId)],
                'Erreur lors de la création de la réservation immédiate.',
                buildUrl('home/places')
            );
        } else {
            $this->handleResponse(
                $isAjax,
                null,
                'Erreur lors de la création de la réservation immédiate.',
                buildUrl('home/places')
            );
        }
    }
    /**
     * Affiche les détails d'une réservation immédiate en cours
     */
    public function immediate($reservationId = null)
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->isAuthenticated()) {
            $this->redirect('auth/login');
            return;
        }

        if (!$reservationId) {
            $this->redirect(BASE_URL);
            return;
        }

        $reservation = $this->reservationModel->getReservationById($reservationId);

        // Vérifier si la réservation existe et appartient à l'utilisateur
        if (!$reservation || $reservation['user_id'] != $_SESSION['user']['id']) {
            $this->redirectWithError('auth/profile', 'Réservation non trouvée ou accès non autorisé.');
            return;
        }

        // Vérifier si c'est bien une réservation immédiate
        if ($reservation['status'] !== 'en_cours_immediat') {
            $this->redirect('auth/profile');
            return;
        }        // Récupérer les informations de la place
        $place = $this->placeModel->getById($reservation['place_id']);

        // Récupérer le tarif horaire pour cette place
        $tarifHoraire = $this->homeModel->getTarifByType($place['type']);        // Récupérer les avantages d'abonnement de l'utilisateur
        $activeSubscriptions = $this->subscriptionModel->getUserActiveSubscriptions($_SESSION['user']['id']);
        $subscriptionBenefits = null;

        if (!empty($activeSubscriptions)) {
            // Prendre le premier abonnement actif (le plus récent)
            $subscriptionBenefits = $activeSubscriptions[0];
        }

        $data = [
            'title' => 'Réservation immédiate en cours - ' . APP_NAME,
            'description' => 'Suivez votre réservation immédiate en cours',
            'active_page' => 'reservation',
            'reservation' => $reservation,
            'place' => $place,
            'tarifHoraire' => $tarifHoraire,
            'subscriptionBenefits' => $subscriptionBenefits
        ];

        $this->renderView('reservation/immediate_tracking', $data);
    }

    /**
     * Affichage d'une réservation immédiate pour les visiteurs
     */
    public function immediateTracking($reservationId = null)
    {
        if (!$reservationId) {
            $this->redirect(BASE_URL);
            return;
        }

        // Pour le suivi public, on utilise un code QR ou un écran dans le parking
        $reservation = $this->reservationModel->getReservationById($reservationId);

        if (!$reservation || $reservation['status'] !== 'en_cours_immediat') {
            $this->redirectWithError(BASE_URL, 'Réservation immédiate non trouvée ou non active.');
            return;
        }

        $data = [
            'title' => 'Suivi réservation immédiate - ' . APP_NAME,
            'description' => 'Suivez l\'état d\'une réservation immédiate',
            'reservation' => $reservation
        ];

        $this->renderView('reservation/immediate_tracking', $data);
    }

    /**
     * Termine une réservation immédiate
     */    public function endImmediate()
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['success' => false, 'error' => 'Vous devez être connecté pour terminer une réservation.'], 401);
            return;
        }

        // Valider les données
        $validated = $this->validateRequiredFields(['reservation_id']);
        if (!$validated) {
            $this->jsonResponse(['success' => false, 'error' => 'ID de réservation manquant.'], 400);
            return;
        }

        $reservationId = intval($validated['reservation_id']);

        // Récupérer la réservation
        $reservation = $this->reservationModel->getReservationById($reservationId);

        // Vérifier si la réservation existe et appartient à l'utilisateur
        if (!$reservation || $reservation['user_id'] != $_SESSION['user']['id']) {
            $this->jsonResponse(['success' => false, 'error' => 'Réservation non trouvée ou accès non autorisé.'], 403);
            return;
        }

        // Vérifier si c'est une requête AJAX
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        // Vérifier si c'est bien une réservation immédiate qui peut être terminée
        if (!in_array($reservation['status'], ['en_cours_immediat', 'en_attente_paiement'])) {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'error' => 'Cette réservation ne peut pas être terminée.'], 400);
            } else {
                $this->redirectWithError('auth/profile', 'Cette réservation ne peut pas être terminée.');
            }
            return;
        }

        // Si la réservation est déjà en attente de paiement, elle a déjà été terminée
        if ($reservation['status'] === 'en_attente_paiement') {
            // Récupérer les informations déjà calculées
            $montantTotal = $reservation['montant_total'];
            $dureeMinutes = (strtotime($reservation['date_fin']) - strtotime($reservation['date_debut'])) / 60;

            if ($montantTotal > 0) {
                $this->preparePaymentSession($reservationId, $montantTotal, $dureeMinutes);
                $response = $this->createReservationResponse($reservation, true);
                $response['duree_minutes'] = $dureeMinutes;

                $this->handleResponse(
                    $isAjax,
                    $response,
                    'Erreur lors du traitement du paiement',
                    buildUrl('reservation/payment/' . $reservationId)
                );
            } else {
                if ($isAjax) {
                    $this->jsonResponse([
                        'success' => true,
                        'reservation_id' => $reservationId,
                        'montant' => '0.00',
                        'duree_minutes' => $dureeMinutes,
                        'requires_payment' => false,
                        'redirect_url' => buildUrl('reservation/confirmation/' . $reservationId),
                        'message' => 'Réservation terminée. Aucun paiement requis.',
                        'code_sortie' => $reservation['code_sortie']
                    ]);
                } else {
                    $this->redirect('reservation/confirmation/' . $reservationId);
                }
            }
            return;
        }

        // Terminer la réservation et calculer le montant
        $result = $this->reservationModel->endImmediateReservation($reservationId);

        if ($result && $result['success']) {
            // Récupérer la réservation mise à jour avec le montant
            $updatedReservation = $this->reservationModel->getReservationById($reservationId);
            // Journaliser l'action
            $this->logModel->addLog($_SESSION['user']['id'], 'immediate_reservation_end', 'Réservation immédiate terminée: ' . $reservationId);
            // Envoyer une notification de fin de réservation immédiate
            if ($updatedReservation) {
                $place = $this->placeModel->getById($updatedReservation['place_id']);
                if ($place) {
                    // Envoyer une notification de fin de réservation
                    $this->userModel->createNotification(
                        $_SESSION['user']['id'],
                        '✅ Réservation terminée',
                        'Votre réservation immédiate pour la place ' . $place['numero'] . ' a été terminée avec succès.',
                        'reservation'
                    );
                }
            }

            // Utiliser le montant calculé par la méthode endImmediateReservation
            $montantTotal = $result['montant_total'];
            $dureeMinutes = $result['duree_minutes'];

            /* Si le montant est supérieur à 0, préparer les données de paiement */
            if ($montantTotal > 0) {
                $this->preparePaymentSession($reservationId, $montantTotal, $dureeMinutes);
                $response = $this->createReservationResponse(['id' => $reservationId, 'montant_total' => $montantTotal, 'code_sortie' => $result['code_sortie']], true);
                $response['duree_minutes'] = $dureeMinutes;

                $this->handleResponse(
                    $isAjax,
                    $response,
                    'Erreur lors du traitement du paiement',
                    buildUrl('reservation/payment/' . $reservationId)
                );
            } else {
                // Montant de 0€, pas besoin de paiement
                $_SESSION['immediate_payment'] = [
                    'reservation_id' => $reservationId,
                    'montant' => 0,
                    'duree' => ceil($dureeMinutes),
                    'needs_payment' => false
                ];

                if ($isAjax) {
                    $this->jsonResponse([
                        'success' => true,
                        'reservation_id' => $reservationId,
                        'montant' => '0.00',
                        'duree_minutes' => $dureeMinutes,
                        'requires_payment' => false,
                        'redirect_url' => buildUrl('reservation/confirmation/' . $reservationId),
                        'message' => 'Réservation terminée. Aucun paiement requis.',
                        'code_sortie' => $result['code_sortie']
                    ]);
                } else {
                    $this->redirect('reservation/confirmation/' . $reservationId);
                }
            }
        } else {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la terminaison de la réservation.'], 500);
            } else {
                $this->redirectWithError('auth/profile', 'Erreur lors de la terminaison de la réservation.');
            }
        }
    }

    /**
     * Affiche la page de fin de réservation (pour les réservations terminées)
     */
    public function end($id = null)
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->isAuthenticated()) {
            $this->redirect('auth/login');
            return;
        }

        // Vérifier si l'ID de réservation est valide
        if (!$id) {
            $this->redirectWithError('auth/profile', 'ID de réservation non spécifié.');
            return;
        }

        // Récupérer la réservation
        $reservation = $this->reservationModel->getReservationById($id);

        // Vérifier si la réservation existe et appartient à l'utilisateur
        if (!$reservation || $reservation['user_id'] != $_SESSION['user']['id']) {
            $this->redirectWithError('auth/profile', 'Réservation non trouvée ou accès non autorisé.');
            return;
        }        // Vérifier si la réservation est bien terminée
        if ($reservation['status'] !== 'terminée') {
            // Si la réservation n'est pas encore terminée, rediriger vers le bon endroit
            if ($reservation['status'] === 'en_attente') {
                $this->redirectWithError('reservation/payment/' . $id, 'Cette réservation nécessite un paiement.');
                return;
            } elseif ($reservation['status'] === 'confirmée' || $reservation['status'] === 'en_cours') {
                $this->redirectWithError('auth/profile', 'Cette réservation est encore active.');
                return;
            } else {
                $this->redirectWithError('auth/profile', 'Cette réservation n\'est pas accessible.');
                return;
            }
        }

        // Récupérer le paiement associé
        $payment = $this->reservationModel->getPaymentByReservationId($id);

        $data = [
            'title' => 'Réservation terminée - ' . APP_NAME,
            'description' => 'Résumé de votre réservation terminée',
            'active_page' => 'reservation',
            'reservation' => $reservation,
            'payment' => $payment
        ];

        $this->renderView('reservation/end', $data);
    }

    /**
     * Affiche une vue avec le template invité
     */
    protected function renderGuestView($view, $data = [])
    {
        // Ajouter des données communes pour les vues invité
        $data['active_page'] = 'guest';
        $data['is_guest'] = true;

        $this->renderView($view, $data, 'guest');
    }

    /**
     * Génère ou régénère le code d'accès pour une réservation immédiate
     */
    public function generateAccessCode()
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['success' => false, 'error' => 'Vous devez être connecté.'], 401);
            return;
        }

        // Valider les données
        $validated = $this->validateRequiredFields(['reservation_id']);
        if (!$validated) {
            $this->jsonResponse(['success' => false, 'error' => 'ID de réservation manquant.'], 400);
            return;
        }

        $reservationId = intval($validated['reservation_id']);

        // Récupérer la réservation
        $reservation = $this->reservationModel->getReservationById($reservationId);

        // Vérifier si la réservation existe et appartient à l'utilisateur
        if (!$reservation || $reservation['user_id'] != $_SESSION['user']['id']) {
            $this->jsonResponse(['success' => false, 'error' => 'Réservation non trouvée ou accès non autorisé.'], 403);
            return;
        }

        // Vérifier si c'est bien une réservation immédiate
        if ($reservation['status'] !== 'en_cours_immediat') {
            $this->jsonResponse(['success' => false, 'error' => 'Cette réservation n\'est pas une réservation immédiate active.'], 400);
            return;
        }

        // Générer un nouveau code d'accès si nécessaire
        $newCode = $this->reservationModel->generateOrUpdateAccessCode($reservationId);
        if ($newCode) {
            $this->jsonResponse([
                'success' => true,
                'code_acces' => $newCode,
                'message' => 'Code d\'accès généré avec succès.'
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la génération du code d\'accès.'], 500);
        }
    }

    /**
     * Crée une alerte de disponibilité pour une place
     */
    public function createAlert()
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['success' => false, 'error' => 'Vous devez être connecté pour créer une alerte.'], 401);
            return;
        }

        // Vérifier les données requises
        $validated = $this->validateRequiredFieldsAjax(['place_id', 'date_debut', 'duree']);
        if (!$validated) {
            $this->jsonResponse(['success' => false, 'error' => 'Données manquantes pour créer l\'alerte.'], 400);
            return;
        }

        try {
            $place_id = intval($_POST['place_id']);
            $date_debut = $_POST['date_debut'];
            $user_id = $_SESSION['user']['id'];

            /* Vérifier que la place existe */
            $place = $this->placeModel->getById($place_id);
            if (!$place) {
                $this->jsonResponse(['success' => false, 'error' => 'Place non trouvée.'], 404);
                return;
            }

            // Valider la date
            $dateObj = DateTime::createFromFormat('Y-m-d\TH:i', $date_debut);
            if (!$dateObj || $dateObj < new DateTime()) {
                $this->jsonResponse(['success' => false, 'error' => 'Date invalide ou dans le passé.'], 400);
                return;
            }

            // Créer l'alerte dans la base de données
            // Note: Il faudrait créer une table alerts et un AlertModel pour stocker les alertes
            // Pour l'instant, on simule la création

            // TODO: Implémenter la logique de stockage d'alerte
            // $alertId = $this->alertModel->createAlert($user_id, $place_id, $date_debut, $duree, $include_similar);

            /* TODO: Implémenter la logique de stockage d'alerte */

            // Log de l'action
            $this->logModel->addLog($user_id, 'CREATE_ALERT', 'Alerte créée pour place ' . $place_id);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Alerte créée avec succès. Vous serez notifié quand une place se libère.',
                'alert_id' => 'temp_' . time() // ID temporaire
            ]);
        } catch (Exception $e) {
            error_log("Erreur création alerte: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la création de l\'alerte.'], 500);
        }
    }

    /**
     * Supprime les notifications obsolètes liées à une réservation annulée
     */
    private function cleanupReservationNotifications($reservationId)
    {
        try {
            $db = Database::getInstance();

            // Supprimer les notifications de rappel/début/fin pour cette réservation
            $sql = "DELETE FROM notifications 
                    WHERE user_id = :user_id 
                    AND (message LIKE :pattern1 OR message LIKE :pattern2 OR message LIKE :pattern3)
                    AND lu = 0";

            $db->query($sql, [
                'user_id' => $_SESSION['user']['id'],
                'pattern1' => '%réservation #' . $reservationId . '%commence%',
                'pattern2' => '%réservation #' . $reservationId . '%termine%',
                'pattern3' => '%réservation #' . $reservationId . '%demain%'
            ]);
        } catch (Exception $e) {
            error_log("Erreur lors de la suppression des notifications pour la réservation {$reservationId}: " . $e->getMessage());
        }
    }

    /**
     * Génère les codes d'accès et de sortie manquants pour une réservation
     */
    public function generateCodes()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }

        // Récupérer les données JSON
        $input = json_decode(file_get_contents('php://input'), true);
        $reservationId = $input['reservation_id'] ?? null;

        if (!$reservationId) {
            $this->jsonResponse(['success' => false, 'message' => 'ID de réservation manquant']);
            return;
        }

        // Vérifier que la réservation appartient à l'utilisateur connecté
        $reservation = $this->reservationModel->getReservationById($reservationId);
        if (!$reservation || $reservation['user_id'] != $_SESSION['user']['id']) {
            $this->jsonResponse(['success' => false, 'message' => 'Réservation non trouvée']);
            return;
        }

        $response = ['success' => true];

        // Générer le code d'accès s'il n'existe pas
        if (empty($reservation['code_acces'])) {
            $newAccessCode = $this->reservationModel->generateOrUpdateAccessCode($reservationId);
            if ($newAccessCode) {
                $response['code_acces'] = $newAccessCode;
            }
        } else {
            $response['code_acces'] = $reservation['code_acces'];
        }

        // Générer le code de sortie s'il n'existe pas
        if (empty($reservation['code_sortie'])) {
            $newExitCode = $this->reservationModel->generateOrUpdateExitCode($reservationId);
            if ($newExitCode) {
                $response['code_sortie'] = $newExitCode;
            }
        } else {
            $response['code_sortie'] = $reservation['code_sortie'];
        }

        $this->jsonResponse($response);
    }
}
