<?php
class ReservationModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    public function createReservation($userId, $placeId, $dateDebut, $dateFin)
    {
        // Valide les entrées de base
        if (!$userId || !$placeId || !$dateDebut || !$dateFin) {
            return false;
        }

        // Valide les dates
        $dateValidation = $this->validateDates($dateDebut, $dateFin);
        if (!$dateValidation['valid']) {
            return false;
        }

        // Vérifie la place et sa disponibilité
        $place = $this->getPlaceById($placeId);
        if (!$place || !$this->isPlaceAvailableForTimeSlot($placeId, $dateDebut, $dateFin)) {
            return false;
        }

        // Calcule le montant avec les avantages d'abonnement
        $montantData = $this->calculateReservationAmount(
            $place['type'],
            $dateValidation['debut'],
            $dateValidation['fin'],
            $userId
        );

        if (!$montantData) {
            return false;
        }

        // Prépare les données de réservation
        $data = [
            'user_id' => $userId,
            'place_id' => $placeId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'status' => 'en_attente',
            'code_acces' => $this->generateAccessCode(),
            'montant_total' => $montantData['montant'],
            'expiration_time' => $this->getExpirationTime()
        ];

        $reservationId = $this->db->insert('reservations', $data);

        // Met à jour le statut de la place si nécessaire
        if ($reservationId && $place['status'] === 'libre') {
            $this->updatePlaceStatusIfNeeded($placeId);
        }

        return $reservationId;
    }

    // Valide les dates de début et fin
    private function validateDates($dateDebut, $dateFin)
    {
        try {
            $debutObj = new DateTime($dateDebut);
            $finObj = new DateTime($dateFin);

            if ($debutObj >= $finObj) {
                return ['valid' => false];
            }

            return [
                'valid' => true,
                'debut' => $debutObj,
                'fin' => $finObj
            ];
        } catch (Exception) {
            return ['valid' => false];
        }
    }

    // Calcule le montant de la réservation avec avantages d'abonnement
    private function calculateReservationAmount($placeType, $debutObj, $finObj, $userId)
    {
        $tarifHoraire = $this->getTarifByType($placeType);
        if (!$tarifHoraire) {
            return false;
        }

        $dureeMinutes = ($finObj->getTimestamp() - $debutObj->getTimestamp()) / 60;

        // Récupère les avantages d'abonnement
        $subscriptionBenefits = $this->getUserSubscriptionBenefits($userId);
        $freeMinutes = $subscriptionBenefits['free_minutes'] ?? 0;
        $discountPercent = $subscriptionBenefits['discount_percent'] ?? 0;

        // Applique les minutes gratuites
        $dureeFacturee = max(0, $dureeMinutes - $freeMinutes);
        $dureeHeures = $dureeFacturee / 60;

        // Calcule le montant avec réduction
        $montantBase = $dureeHeures * $tarifHoraire;
        $montantTotal = $montantBase * (1 - ($discountPercent / 100));

        return ['montant' => $montantTotal];
    }

    // Génère un code d'accès aléatoire
    private function generateAccessCode()
    {
        return strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
    }

    // Calcule le temps d'expiration (15 minutes)
    private function getExpirationTime()
    {
        $expirationTime = new DateTime();
        $expirationTime->add(new DateInterval('PT15M'));
        return $expirationTime->format('Y-m-d H:i:s');
    }

    // Met à jour le statut de la place si nécessaire
    private function updatePlaceStatusIfNeeded($placeId)
    {
        $now = date('Y-m-d H:i:s');
        $activeReservations = $this->getActiveReservationsForPlace($placeId, $now);
        if (empty($activeReservations)) {
            $this->updatePlaceStatus($placeId, 'occupe');
        }
    }

    public function getPlaceById($placeId)
    {
        $sql = "SELECT id, numero, type, status 
                FROM parking_spaces 
                WHERE id = :id";

        return $this->db->findOne($sql, ['id' => $placeId]);
    }
    public function getTarifByType($type)
    {
        $sql = "SELECT prix_heure 
                FROM tarifs 
                WHERE type_place = :type
                ORDER BY id DESC
                LIMIT 1";

        $result = $this->db->findOne($sql, ['type' => $type]);
        return $result ? (float) $result['prix_heure'] : 2.0; // Tarif par défaut de 2€/h
    }

    public function updatePlaceStatus($placeId, $status)
    {
        $data = ['status' => $status];
        $where = "id = :id";
        $params = ['id' => $placeId];

        return $this->db->update('parking_spaces', $data, $where, $params);
    }
    public function getReservationById($id)
    {
        $sql = "SELECT r.id, r.user_id, r.place_id, r.date_debut, r.date_fin, r.status, 
                       r.code_acces, r.code_sortie, r.montant_total, r.created_at,
                       p.numero, p.type, p.status as place_status
                FROM reservations r 
                JOIN parking_spaces p ON r.place_id = p.id 
                WHERE r.id = :id";

        return $this->db->findOne($sql, ['id' => $id]);
    }

    public function cancelReservation($reservationId)
    {
        // Récupérer les informations de la réservation
        $reservation = $this->getReservationById($reservationId);

        if (!$reservation) {
            return false;
        }

        // Mettre à jour le statut de la réservation
        $this->db->update(
            'reservations',
            ['status' => 'annulée'],
            "id = :id",
            ['id' => $reservationId]
        );

        // Libérer la place de parking
        $this->updatePlaceStatus($reservation['place_id'], 'libre');

        // Vérifier s'il y a un paiement associé
        $paiement = $this->getPaymentByReservationId($reservationId);

        if ($paiement && $paiement['status'] === 'valide') {
            // Mettre à jour le statut du paiement
            $this->db->update(
                'paiements',
                ['status' => 'annule'],
                "id = :id",
                ['id' => $paiement['id']]
            );

            // Créer une demande de remboursement
            $this->createRefund($paiement['id'], $paiement['montant']);
        }

        return true;
    }

    public function getPaymentByReservationId($reservationId)
    {
        $sql = "SELECT id, montant, mode_paiement, status, date_paiement 
                FROM paiements 
                WHERE reservation_id = :reservation_id";

        return $this->db->findOne($sql, ['reservation_id' => $reservationId]);
    }

    public function createRefund($paiementId, $montant)
    {
        $data = [
            'paiement_id' => $paiementId,
            'montant' => $montant,
            'raison' => 'Annulation par l\'utilisateur',
            'status' => 'en_cours'
        ];

        return $this->db->insert('remboursements', $data);
    }
    public function createPayment($reservationId, $montant, $modePayment = null)
    {
        $data = [
            'reservation_id' => $reservationId,
            'montant' => $montant,
            'mode_paiement' => $modePayment,
            'status' => 'valide'
        ];

        $paymentId = $this->db->insert('paiements', $data);

        if ($paymentId) {
            // Récupérer la réservation pour déterminer le bon statut
            $reservation = $this->getReservationById($reservationId);

            // Déterminer le nouveau statut selon le type de réservation
            if ($reservation['status'] === 'en_attente_paiement') {
                // Réservation immédiate terminée -> statut "terminee"
                $newStatus = 'terminee';
            } else {
                // Réservation normale -> statut "confirmée" 
                $newStatus = 'confirmée';
            }

            // Mettre à jour le statut de la réservation
            $this->db->update(
                'reservations',
                ['status' => $newStatus],
                "id = :id",
                ['id' => $reservationId]
            );

            // Générer une facture
            $this->generateInvoice($paymentId);
        }

        return $paymentId;
    }

    public function generateInvoice($paymentId)
    {
        // Générer un numéro de facture unique (format: YYYYMMDD-XXXX)
        $date = date('Ymd');

        // Trouver le dernier numéro de facture pour aujourd'hui
        $sql = "SELECT MAX(numero_facture) as last_number FROM factures WHERE numero_facture LIKE :pattern";
        $result = $this->db->findOne($sql, ['pattern' => $date . '-%']);

        if ($result && $result['last_number']) {
            $last = explode('-', $result['last_number']);
            $next = sprintf('%04d', intval($last[1]) + 1);
        } else {
            $next = '0001';
        }

        $numeroFacture = $date . '-' . $next;
        $cheminPdf = 'factures/facture_' . $numeroFacture . '.pdf';

        $data = [
            'paiement_id' => $paymentId,
            'numero_facture' => $numeroFacture,
            'chemin_pdf' => $cheminPdf
        ];

        // Dans une application réelle, on génèrerait le PDF ici
        // Pour cet exemple, on se contente d'enregistrer les informations

        return $this->db->insert('factures', $data);
    }

    public function getActiveReservations()
    {
        $now = date('Y-m-d H:i:s');

        $sql = "SELECT r.id, r.user_id, r.place_id, r.date_debut, r.date_fin, r.status,
                   COALESCE(u.nom, r.guest_name) as nom, 
                   COALESCE(u.prenom, '') as prenom, 
                   p.numero
            FROM reservations r
            LEFT JOIN users u ON r.user_id = u.id
            JOIN parking_spaces p ON r.place_id = p.id
            WHERE r.status = 'confirmée'
              AND r.date_debut <= :now_start
              AND r.date_fin >= :now_end
            ORDER BY r.date_fin ASC";

        return $this->db->findAll($sql, [
            'now_start' => $now,
            'now_end' => $now
        ]);
    }

    public function getUpcomingReservations()
    {
        $now = date('Y-m-d H:i:s');

        $sql = "SELECT r.id, r.user_id, r.place_id, r.date_debut, r.date_fin, r.status,
                   COALESCE(u.nom, r.guest_name) as nom, 
                   COALESCE(u.prenom, '') as prenom, 
                   p.numero
            FROM reservations r
            LEFT JOIN users u ON r.user_id = u.id
            JOIN parking_spaces p ON r.place_id = p.id
            WHERE r.status = 'confirmée'
              AND r.date_debut > :now
            ORDER BY r.date_debut ASC
            LIMIT 10";

        return $this->db->findAll($sql, ['now' => $now]);
    }
    // Crée une réservation pour un invité
    public function createGuestReservation($placeId, $dateDebut, $dateFin, $guestName, $guestEmail, $guestPhone = null)
    {
        // Valide les entrées de base
        if (!$placeId || !$dateDebut || !$dateFin || !$guestName || !$guestEmail) {
            return false;
        }

        // Valide les dates
        $dateValidation = $this->validateDates($dateDebut, $dateFin);
        if (!$dateValidation['valid']) {
            return false;
        }

        // Vérifie la place et sa disponibilité
        $place = $this->getPlaceById($placeId);
        if (
            !$place || $place['status'] === 'maintenance' ||
            !$this->isPlaceAvailableForTimeSlot($placeId, $dateDebut, $dateFin)
        ) {
            return false;
        }

        // Calcule le montant (les invités n'ont pas d'avantages d'abonnement)
        $montantData = $this->calculateGuestReservationAmount(
            $place['type'],
            $dateValidation['debut'],
            $dateValidation['fin']
        );

        if (!$montantData) {
            return false;
        }

        // Récupère ou crée l'utilisateur guest
        $guestUserId = $this->getOrCreateGuestUser();
        $guestToken = bin2hex(random_bytes(16));

        // Prépare les données de réservation
        $data = [
            'user_id' => $guestUserId,
            'place_id' => $placeId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'status' => 'en_attente',
            'code_acces' => $this->generateAccessCode(),
            'montant_total' => $montantData['montant'],
            'guest_name' => $guestName,
            'guest_email' => $guestEmail,
            'guest_phone' => $guestPhone,
            'guest_token' => $guestToken,
            'expiration_time' => $this->getExpirationTime()
        ];

        $reservationId = $this->db->insert('reservations', $data);

        // Met à jour le statut de la place si nécessaire
        if ($reservationId && $place['status'] === 'libre') {
            $this->updatePlaceStatusIfNeeded($placeId);
        }

        return $reservationId ? [
            'reservation_id' => $reservationId,
            'guest_token' => $guestToken,
            'user_id' => $guestUserId
        ] : false;
    }

    // Calcule le montant pour une réservation d'invité (sans avantages)
    private function calculateGuestReservationAmount($placeType, $debutObj, $finObj)
    {
        $tarifHoraire = $this->getTarifByType($placeType);
        if (!$tarifHoraire) {
            return false;
        }

        $dureeMinutes = ($finObj->getTimestamp() - $debutObj->getTimestamp()) / 60;
        $dureeHeures = $dureeMinutes / 60;
        $montantTotal = $dureeHeures * $tarifHoraire;

        return ['montant' => $montantTotal];
    }

    // Récupère ou crée l'utilisateur guest
    private function getOrCreateGuestUser()
    {
        $sql = "SELECT id FROM users WHERE email = 'guest@parkme.in' LIMIT 1";
        $guestUser = $this->db->findOne($sql);

        if (!$guestUser) {
            return $this->db->insert('users', [
                'email' => 'guest@parkme.in',
                'password' => 'NO_LOGIN',
                'nom' => 'Guest',
                'prenom' => 'User',
                'role' => 'user',
                'notifications_active' => 0
            ]);
        }

        return $guestUser['id'];
    }

    // Récupère une réservation par son token d'invité
    public function getReservationByGuestToken($token)
    {
        $sql = "SELECT r.id, r.place_id, r.date_debut, r.date_fin, r.status,
                       r.code_acces, r.montant_total, r.created_at, r.guest_name, r.guest_email,
                       r.guest_phone, r.guest_token,
                       p.numero, p.type, p.status as place_status
                FROM reservations r
                JOIN parking_spaces p ON r.place_id = p.id
                WHERE r.guest_token = :token";

        return $this->db->findOne($sql, ['token' => $token]);
    }

    // Crée un paiement pour une réservation d'invité
    public function createGuestPayment($reservationId, $montant, $modePayment = null)
    {
        $data = [
            'reservation_id' => $reservationId,
            'montant' => $montant,
            'mode_paiement' => $modePayment,
            'status' => 'valide'
        ];

        $paymentId = $this->db->insert('paiements', $data);

        if ($paymentId) {
            $this->db->update(
                'reservations',
                ['status' => 'confirmée'],
                "id = :id",
                ['id' => $reservationId]
            );

            $this->generateInvoice($paymentId);
        }

        return $paymentId;
    }

    // Annule une réservation d'invité
    public function cancelGuestReservation($reservationId, $token)
    {
        // Vérifie que la réservation existe et correspond au token
        $sql = "SELECT id, place_id FROM reservations WHERE id = :id AND guest_token = :token";
        $reservation = $this->db->findOne($sql, ['id' => $reservationId, 'token' => $token]);

        if (!$reservation) {
            return false;
        }

        // Met à jour le statut de la réservation
        $this->db->update(
            'reservations',
            ['status' => 'annulée'],
            "id = :id",
            ['id' => $reservationId]
        );

        // Libère la place de parking
        $this->updatePlaceStatus($reservation['place_id'], 'libre');

        // Vérifie s'il y a un paiement associé
        $paiement = $this->getPaymentByReservationId($reservationId);

        if ($paiement && $paiement['status'] === 'valide') {
            $this->db->update(
                'paiements',
                ['status' => 'annule'],
                "id = :id",
                ['id' => $paiement['id']]
            );

            // Créer une demande de remboursement
            $this->createRefund($paiement['id'], $paiement['montant']);
        }

        return true;
    }

    /**
     * Convertir une réservation invité en réservation utilisateur
     */
    public function convertGuestReservationToUser($reservationId, $userId)
    {
        // Récupérer la réservation invité
        $sql = "SELECT * FROM reservations WHERE id = :id AND guest_token IS NOT NULL";
        $reservation = $this->db->findOne($sql, ['id' => $reservationId]);

        if (!$reservation) {
            return false;
        }

        // Mettre à jour la réservation avec l'ID utilisateur
        $data = [
            'user_id' => $userId,
            'guest_name' => null,
            'guest_email' => null,
            'guest_phone' => null,
            'guest_token' => null
        ];

        return $this->db->update('reservations', $data, 'id = :id', ['id' => $reservationId]);
    }

    /**
     * Récupérer toutes les réservations d'invités
     */
    public function getAllGuestReservations()
    {
        $sql = "SELECT r.id, r.place_id, r.date_debut, r.date_fin, r.status, 
                       r.code_acces, r.montant_total, r.created_at, r.guest_name, r.guest_email, 
                       r.guest_phone, p.numero, p.type
                FROM reservations r 
                JOIN parking_spaces p ON r.place_id = p.id 
                WHERE r.user_id = 0
                ORDER BY r.date_debut DESC";

        return $this->db->findAll($sql);
    }

    /**
     * Récupérer les réservations par email d'invité
     */
    public function getReservationsByGuestEmail($email)
    {
        $sql = "SELECT r.id, r.place_id, r.date_debut, r.date_fin, r.status, 
                       r.code_acces, r.montant_total, r.created_at, r.guest_name, r.guest_email, 
                       r.guest_phone, r.guest_token,
                       p.numero, p.type, p.status as place_status
                FROM reservations r 
                JOIN parking_spaces p ON r.place_id = p.id 
                WHERE r.guest_email = :email
                ORDER BY r.created_at DESC";

        return $this->db->findAll($sql, ['email' => $email]);
    }

    /**
     * Récupérer les réservations d'invités pour un email spécifique
     * (utilisé lors de la conversion après création d'un compte)
     */
    public function getPendingGuestReservationsByEmail($email)
    {
        $sql = "SELECT r.id, r.place_id, r.date_debut, r.date_fin, r.status, 
                       r.code_acces, r.montant_total, r.guest_token,
                       p.numero, p.type
                FROM reservations r 
                JOIN parking_spaces p ON r.place_id = p.id 
                WHERE r.guest_email = :email 
                  AND r.user_id = 0
                  AND r.date_fin > NOW()
                ORDER BY r.date_debut ASC";

        return $this->db->findAll($sql, ['email' => $email]);
    }
    /**
     * Récupère les réservations avec pagination et filtres
     */
    public function getReservationsPaginated($offset, $limit, $statusFilter = null, $dateFilter = null)
    {
        $sql = "SELECT r.id, r.user_id, r.place_id, r.date_debut, r.date_fin, r.status, 
                       r.montant_total, r.created_at, r.guest_name, r.guest_email,
                       p.numero as place_numero, p.type as place_type, 
                       COALESCE(u.nom, 'Invité') as nom, COALESCE(u.prenom, '') as prenom
                FROM reservations r
                JOIN parking_spaces p ON r.place_id = p.id
                LEFT JOIN users u ON r.user_id = u.id";

        $conditions = [];
        $params = [];

        // Filtre par statut
        if ($statusFilter) {
            $conditions[] = "r.status = :status";
            $params[':status'] = $statusFilter;
        }

        // Filtre par date
        if ($dateFilter) {
            switch ($dateFilter) {
                case 'today':
                    $conditions[] = "DATE(r.date_debut) = CURDATE()";
                    break;
                case 'week':
                    $conditions[] = "YEARWEEK(r.date_debut) = YEARWEEK(CURDATE())";
                    break;
                case 'month':
                    $conditions[] = "YEAR(r.date_debut) = YEAR(CURDATE()) AND MONTH(r.date_debut) = MONTH(CURDATE())";
                    break;
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->getConnection()->prepare($sql);

        // Bind des paramètres de filtre
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte le nombre total de réservations
     */
    public function countTotalReservations()
    {
        $sql = "SELECT COUNT(*) as count FROM reservations";
        $result = $this->db->findOne($sql);
        return $result['count'];
    }

    /**
     * Compte les réservations d'aujourd'hui
     */
    public function countTodayReservations()
    {
        $sql = "SELECT COUNT(*) as count 
                FROM reservations 
                WHERE DATE(created_at) = CURDATE()";

        $result = $this->db->findOne($sql);
        return $result['count'];
    }

    /**
     * Compte les réservations du mois en cours
     */
    public function countMonthReservations()
    {
        $sql = "SELECT COUNT(*) as count 
                FROM reservations 
                WHERE created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')";

        $result = $this->db->findOne($sql);
        return $result['count'];
    }

    /**
     * Compte les réservations par statut
     */
    public function countReservationsByStatus()
    {
        $sql = "SELECT status, COUNT(*) as count 
                FROM reservations 
                GROUP BY status";

        $results = $this->db->findAll($sql);

        $statuses = array_fill_keys(['en_attente', 'confirmée', 'annulée'], 0);

        foreach ($results as $result) {
            $statuses[$result['status']] = $result['count'];
        }

        return $statuses;
    }
    /**
     * Count the total number of reservations with filters
     * @return int Total number of reservations
     */
    public function countReservations($statusFilter = null, $dateFilter = null)
    {
        $sql = "SELECT COUNT(*) as count FROM reservations r";

        $conditions = [];
        $params = [];

        // Filtre par statut
        if ($statusFilter) {
            $conditions[] = "r.status = :status";
            $params[':status'] = $statusFilter;
        }

        // Filtre par date
        if ($dateFilter) {
            switch ($dateFilter) {
                case 'today':
                    $conditions[] = "DATE(r.date_debut) = CURDATE()";
                    break;
                case 'week':
                    $conditions[] = "YEARWEEK(r.date_debut) = YEARWEEK(CURDATE())";
                    break;
                case 'month':
                    $conditions[] = "YEAR(r.date_debut) = YEAR(CURDATE()) AND MONTH(r.date_debut) = MONTH(CURDATE())";
                    break;
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        if (!empty($params)) {
            $stmt = $this->db->getConnection()->prepare($sql);

            // Bind des paramètres de filtre
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $result = $this->db->findOne($sql);
        }

        return isset($result['count']) ? $result['count'] : 0;
    }

    /**
     * Count reservations grouped by status
     * @return array Associative array of counts by status
     */
    public function countByStatus()
    {
        $sql = "SELECT status, COUNT(*) as count FROM reservations GROUP BY status";
        $results = $this->db->findAll($sql);

        // Initialize all possible statuses with 0
        $counts = array_fill_keys(['en_attente', 'confirmee', 'en_cours', 'terminee', 'annulee', 'expiree'], 0);

        // Fill with actual values
        foreach ($results as $result) {
            $counts[$result['status']] = $result['count'];
        }

        return $counts;
    }

    /**
     * Calcule les revenus (aujourd'hui, cette semaine, ce mois, total)
     */
    public function calculateRevenue($period = 'total')
    {
        $whereClause = '';

        if ($period === 'today') {
            $whereClause = "AND DATE(p.date_paiement) = CURDATE()";
        } elseif ($period === 'week') {
            $whereClause = "AND p.date_paiement >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        } elseif ($period === 'month') {
            $whereClause = "AND p.date_paiement >= DATE_FORMAT(NOW(), '%Y-%m-01')";
        }

        $sql = "SELECT SUM(p.montant) as total 
                FROM paiements p 
                WHERE p.status = 'valide' $whereClause";

        $result = $this->db->findOne($sql);

        return $result['total'] ?? 0;
    }

    /**
     * Récupère les réservations actives pour une place donnée
     */
    public function getActiveReservationsForPlace($placeId, $now = null)
    {
        if ($now === null) {
            $now = date('Y-m-d H:i:s');
        }

        $sql = "SELECT id FROM reservations 
                WHERE place_id = :place_id 
                AND status = 'confirmée' 
                AND date_fin > :now";

        return $this->db->findAll($sql, [
            'place_id' => $placeId,
            'now' => $now
        ]);
    }

    /**
     * Annule toutes les réservations actives pour une place donnée
     */
    public function cancelReservationsForPlace($placeId)
    {
        $now = date('Y-m-d H:i:s');

        // Récupérer les ID des réservations actives pour cette place
        $sql = "SELECT id FROM reservations 
                WHERE place_id = :place_id 
                AND status = 'confirmée' 
                AND date_fin > :now";

        $reservations = $this->db->findAll($sql, [
            'place_id' => $placeId,
            'now' => $now
        ]);

        $cancelledCount = 0;
        foreach ($reservations as $reservation) {
            if ($this->cancelReservation($reservation['id'])) {
                $cancelledCount++;
            }
        }

        return $cancelledCount;
    }

    /**
     * Met à jour le statut des réservations expirées (passé la date de fin)
     * Libère également les places de parking associées
     * @return array Statistiques sur les mises à jour effectuées
     */    public function updateExpiredReservations()
    {
        // 1. Annuler les réservations en attente dont le délai de paiement est expiré
        $cancelledWaiting = $this->cancelExpiredWaitingReservations();

        // 2. Finaliser les réservations immédiates en attente de paiement depuis trop longtemps
        $expiredImmediatePayments = $this->finalizeExpiredImmediatePayments();

        // 3. Mettre à jour confirmées -> en_cours (seulement pour celles qui commencent maintenant)
        $updatedToInProgress = $this->updateConfirmedToInProgress();

        // 3. Mettre à jour confirmées -> terminée (pour celles qui sont déjà terminées)
        $confirmedToCompleted = $this->updateConfirmedToCompleted();

        // 4. Mettre à jour en_cours -> terminée
        $updatedToCompleted = $this->updateInProgressToCompleted();

        // Calculer les totaux
        $totalCompleted = $confirmedToCompleted['updated'] + $updatedToCompleted['updated'];
        $totalPlacesLiberated = $confirmedToCompleted['places_liberated'] + $updatedToCompleted['places_liberated'];
        $totalErrors = $cancelledWaiting['errors'] + $expiredImmediatePayments['errors'] + $confirmedToCompleted['errors'] + $updatedToCompleted['errors'];

        return [
            'cancelled_waiting' => $cancelledWaiting['cancelled'],
            'expired_immediate_payments' => $expiredImmediatePayments['finalized'],
            'to_in_progress' => $updatedToInProgress,
            'to_completed' => $totalCompleted,
            'places_liberated' => $totalPlacesLiberated,
            'errors' => $totalErrors
        ];
    }

    /**
     * Mettre à jour les réservations confirmées vers "en cours" lorsque la date de début est passée
     * @return int Nombre de réservations mises à jour
     */
    public function updateConfirmedToInProgress()
    {
        $now = date('Y-m-d H:i:s');

        // Trouver toutes les réservations confirmées dont la date de début est passée
        $sql = "SELECT id FROM reservations 
                WHERE status = 'confirmée' 
                AND date_debut <= :now";

        $reservations = $this->db->findAll($sql, ['now' => $now]);
        $updatedCount = 0;

        // Mettre à jour chaque réservation vers "en cours"
        foreach ($reservations as $reservation) {
            $updateResult = $this->db->update(
                'reservations',
                ['status' => 'en_cours'],
                'id = :id',
                ['id' => $reservation['id']]
            );

            if ($updateResult) {
                $updatedCount++;
            }
        }

        return $updatedCount;
    }

    /**
     * Mettre à jour les réservations confirmées vers "terminée" lorsque la date de fin est passée
     * Libère également les places de parking associées
     * @return array Statistiques sur les mises à jour effectuées
     */
    public function updateConfirmedToCompleted()
    {
        $now = date('Y-m-d H:i:s');
        $stats = [
            'updated' => 0,
            'places_liberated' => 0,
            'errors' => 0
        ];

        // Trouver toutes les réservations confirmées dont la date de fin est passée
        $sql = "SELECT id, place_id FROM reservations 
                WHERE status = 'confirmée' 
                AND date_fin < :now";

        $reservations = $this->db->findAll($sql, ['now' => $now]);

        // Mettre à jour chaque réservation et libérer les places
        foreach ($reservations as $reservation) {
            // 1. Mettre à jour le statut de la réservation
            $updateResult = $this->db->update(
                'reservations',
                ['status' => 'terminée'],
                'id = :id',
                ['id' => $reservation['id']]
            );

            if ($updateResult) {
                $stats['updated']++;

                // 2. Libérer la place
                $placeResult = $this->updatePlaceStatus($reservation['place_id'], 'libre');
                if ($placeResult) {
                    $stats['places_liberated']++;
                } else {
                    $stats['errors']++;
                }
            } else {
                $stats['errors']++;
            }
        }

        return $stats;
    }
    /**
     * Mettre à jour les réservations "en cours" vers "terminée" lorsque la date de fin est passée
     * Libère également les places de parking associées
     * @return array Statistiques sur les mises à jour effectuées
     */
    public function updateInProgressToCompleted()
    {
        $now = date('Y-m-d H:i:s');
        $stats = [
            'updated' => 0,
            'places_liberated' => 0,
            'errors' => 0
        ];

        // Trouver toutes les réservations en cours dont la date de fin est passée
        $sql = "SELECT id, place_id FROM reservations 
                WHERE status = 'en_cours' 
                AND date_fin < :now";

        $reservations = $this->db->findAll($sql, ['now' => $now]);

        // Mettre à jour chaque réservation et libérer les places
        foreach ($reservations as $reservation) {
            // 1. Mettre à jour le statut de la réservation
            $updateResult = $this->db->update(
                'reservations',
                ['status' => 'terminée'],
                'id = :id',
                ['id' => $reservation['id']]
            );

            if ($updateResult) {
                $stats['updated']++;

                // 2. Libérer la place
                $placeResult = $this->updatePlaceStatus($reservation['place_id'], 'libre');
                if ($placeResult) {
                    $stats['places_liberated']++;
                } else {
                    $stats['errors']++;
                }
            } else {
                $stats['errors']++;
            }
        }

        return $stats;
    }

    /**
     * Vérifie si une place est disponible pour un créneau horaire spécifique
     * @param int $placeId ID de la place
     * @param string $dateDebut Date et heure de début (format Y-m-d H:i:s)
     * @param string $dateFin Date et heure de fin (format Y-m-d H:i:s)
     * @param int $excludeReservationId Optionnel - ID de la réservation à exclure de la vérification
     * @return bool True si la place est disponible, false sinon
     */    public function isPlaceAvailableForTimeSlot($placeId, $dateDebut, $dateFin, $excludeReservationId = null)
    {
        // Vérifier d'abord le statut général de la place
        $place = $this->getPlaceById($placeId);
        if (!$place || $place['status'] === 'maintenance') {
            return false;
        }        // Vérifier s'il existe des réservations qui se chevauchent pour cette place
        // Un chevauchement existe si le début de la nouvelle réservation est avant la fin d'une réservation existante
        // ET la fin de la nouvelle réservation est après le début d'une réservation existante
        $sql = "SELECT COUNT(*) as count 
                FROM reservations 
                WHERE place_id = :place_id 
                AND status IN ('confirmée', 'en_cours', 'en_attente', 'en_cours_immediat') 
                AND (:date_debut < date_fin) 
                AND (:date_fin > date_debut)";

        // Pour les réservations immédiates, vérifier aussi si elles sont encore actives (pas de date_fin ou date_fin future)
        $sql .= " AND (status != 'en_cours_immediat' OR date_fin IS NULL OR date_fin > NOW())";

        // Si on doit exclure une réservation (par exemple pour vérifier si une réservation peut être payée)
        $params = [
            'place_id' => $placeId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ];

        if ($excludeReservationId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeReservationId;
        }
        $result = $this->db->findOne($sql, $params);

        // Pour le débogage - enregistrer dans un log les conflits
        if ($result['count'] > 0) {
            // Pour identifier quelles réservations entrent en conflit
            $conflictSql = "SELECT id, date_debut, date_fin, status 
                FROM reservations 
                WHERE place_id = :place_id 
                AND status IN ('confirmée', 'en_cours', 'en_attente') 
                AND (:date_debut < date_fin) 
                AND (:date_fin > date_debut)";

            $conflicts = $this->db->findAll($conflictSql, [
                'place_id' => $placeId,
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin
            ]);

            // Enregistrer les conflits dans un fichier de log
            $logMessage = "Conflit de réservation détecté pour place_id=$placeId, date_debut=$dateDebut, date_fin=$dateFin\n";
            $logMessage .= "Réservations en conflit : " . json_encode($conflicts, JSON_PRETTY_PRINT) . "\n";
            error_log($logMessage);
        }

        // La place est disponible s'il n'y a pas de réservations qui se chevauchent
        return $result['count'] == 0;
    }

    /**
     * Annule les réservations en attente dont le délai de paiement est expiré
     * @return array Statistiques sur les annulations effectuées
     */
    public function cancelExpiredWaitingReservations()
    {
        $now = date('Y-m-d H:i:s');
        $stats = [
            'cancelled' => 0,
            'errors' => 0
        ];

        // Trouver toutes les réservations en attente créées il y a plus de 30 minutes
        // et qui n'ont pas été payées
        $expirationTime = date('Y-m-d H:i:s', strtotime('-30 minutes'));

        $sql = "SELECT r.id, r.place_id 
                FROM reservations r
                LEFT JOIN paiements p ON r.id = p.reservation_id
                WHERE r.status = 'en_attente'
                AND r.created_at < :expiration_time
                AND (p.id IS NULL OR p.status != 'valide')";

        $reservations = $this->db->findAll($sql, ['expiration_time' => $expirationTime]);

        // Annuler chaque réservation et libérer les places
        foreach ($reservations as $reservation) {            // Mettre à jour le statut de la réservation
            $updateResult = $this->db->update(
                'reservations',
                [
                    'status' => 'annulée'
                    // Pas de champ date_annulation dans la table
                ],
                'id = :id',
                ['id' => $reservation['id']]
            );

            if ($updateResult) {
                $stats['cancelled']++;

                // Libérer la place
                $this->updatePlaceStatus($reservation['place_id'], 'libre');
            } else {
                $stats['errors']++;
            }
        }

        return $stats;
    }

    /**
     * Finalise les réservations immédiates en attente de paiement depuis trop longtemps
     * @return array Statistiques sur les finalisations effectuées
     */
    public function finalizeExpiredImmediatePayments()
    {
        $now = date('Y-m-d H:i:s');
        $stats = [
            'finalized' => 0,
            'errors' => 0
        ];

        // Trouver toutes les réservations immédiates en attente de paiement depuis plus de 2 heures
        $expirationTime = date('Y-m-d H:i:s', strtotime('-2 hours'));

        $sql = "SELECT r.id, r.place_id
                FROM reservations r
                WHERE r.status = 'en_attente_paiement'
                AND r.date_fin < :expiration_time";

        $reservations = $this->db->findAll($sql, ['expiration_time' => $expirationTime]);

        // Finaliser chaque réservation
        foreach ($reservations as $reservation) {
            // Mettre à jour le statut de la réservation vers "terminée"
            $updateResult = $this->db->update(
                'reservations',
                ['status' => 'terminée'],
                'id = :id',
                ['id' => $reservation['id']]
            );

            if ($updateResult) {
                $stats['finalized']++;
                // Note: La place devrait déjà être libérée lors de endImmediateReservation
                // Mais on s'assure qu'elle est bien libre
                $this->updatePlaceStatus($reservation['place_id'], 'libre');
            } else {
                $stats['errors']++;
            }
        }

        return $stats;
    }

    /**
     * Crée une alerte de disponibilité pour un utilisateur
     * @param int $userId ID de l'utilisateur
     * @param int $placeId ID de la place
     * @param string $dateDebut Date et heure de début souhaitées
     * @param string $dateFin Date et heure de fin souhaitées
     * @return int|bool ID de l'alerte créée ou false en cas d'erreur
     */    public function createAvailabilityAlert($userId, $placeId, $dateDebut, $dateFin, $includeSimilarPlaces = false)
    {
        // Calculer la date d'expiration (1 jour après la date de début souhaitée)
        $expiresAt = new DateTime($dateDebut);
        $expiresAt->add(new DateInterval('P1D')); // P1D = 1 jour

        $data = [
            'user_id' => $userId,
            'place_id' => $placeId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            'notified' => 0,
            'include_similar_places' => $includeSimilarPlaces ? 1 : 0
        ];

        return $this->db->insert('availability_alerts', $data);
    }

    /**
     * Vérifie et envoie les notifications pour les créneaux devenus disponibles
     * @return array Statistiques sur les notifications envoyées
     */
    public function checkAvailabilityAlerts()
    {
        $now = date('Y-m-d H:i:s');
        $stats = [
            'alerts_checked' => 0,
            'notifications_sent' => 0,
            'alerts_expired' => 0,
            'errors' => 0
        ];

        // Récupérer les alertes actives non expirées
        $sql = "SELECT a.*, u.email, p.numero 
                FROM availability_alerts a
                JOIN users u ON a.user_id = u.id
                JOIN parking_spaces p ON a.place_id = p.id
                WHERE a.notified = 0 
                AND a.expires_at > :now";

        $alerts = $this->db->findAll($sql, ['now' => $now]);
        $stats['alerts_checked'] = count($alerts);
        foreach ($alerts as $alert) {
            // Vérifier si le créneau est disponible pour la place demandée
            $isAvailable = $this->isPlaceAvailableForTimeSlot(
                $alert['place_id'],
                $alert['date_debut'],
                $alert['date_fin']
            );

            // Variables pour gérer les places similaires
            $availableSimilarPlace = null;
            $includeSimilarPlaces = isset($alert['include_similar_places']) && $alert['include_similar_places'] == 1;

            // Si la place n'est pas disponible mais que l'utilisateur a demandé à être alerté pour des places similaires
            if (!$isAvailable && $includeSimilarPlaces) {
                // Récupérer le type de la place demandée
                $placeModel = new PlaceModel();
                $place = $placeModel->getById($alert['place_id']);

                if ($place) {
                    // Rechercher des places similaires disponibles (même type)
                    $similarPlaces = $placeModel->getSimilarPlacesByType($place['type'], $alert['place_id']);

                    foreach ($similarPlaces as $similarPlace) {
                        if ($this->isPlaceAvailableForTimeSlot(
                            $similarPlace['id'],
                            $alert['date_debut'],
                            $alert['date_fin']
                        )) {
                            $availableSimilarPlace = $similarPlace;
                            break;
                        }
                    }
                }
            }

            if ($isAvailable || $availableSimilarPlace) {
                // Créer une notification pour l'utilisateur
                $userModel = new UserModel();

                $placeInfo = $isAvailable
                    ? 'la place ' . $alert['numero']
                    : 'une place similaire (' . $availableSimilarPlace['numero'] . ')';

                $notificationResult = $userModel->createNotification(
                    $alert['user_id'],
                    'Créneau disponible',
                    'Le créneau que vous souhaitiez pour ' . $placeInfo . ' est maintenant disponible ! Du ' .
                        (new DateTime($alert['date_debut']))->format('d/m/Y H:i') . ' au ' .
                        (new DateTime($alert['date_fin']))->format('d/m/Y H:i') . '.',
                    'availability'
                );

                if ($notificationResult) {
                    // Marquer l'alerte comme notifiée
                    $this->db->update(
                        'availability_alerts',
                        ['notified' => 1],
                        "id = :id",
                        ['id' => $alert['id']]
                    );
                    $stats['notifications_sent']++;
                } else {
                    $stats['errors']++;
                }
            }
        }

        // Supprimer les alertes expirées
        $deleteExpired = $this->db->delete('availability_alerts', "expires_at < :now", ['now' => $now]);
        $stats['alerts_expired'] = $deleteExpired;

        return $stats;
    }

    /**
     * Vérifie si l'utilisateur a déjà une alerte pour ce créneau
     * @param int $userId ID de l'utilisateur
     * @param int $placeId ID de la place
     * @param string $dateDebut Date et heure de début souhaitées
     * @param string $dateFin Date et heure de fin souhaitées
     * @return int Nombre d'alertes existantes
     */
    public function getExistingAlerts($userId, $placeId, $dateDebut, $dateFin)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM availability_alerts 
                WHERE user_id = :user_id 
                AND place_id = :place_id 
                AND notified = 0
                AND (:date_debut < date_fin) 
                AND (:date_fin > date_debut)";

        $result = $this->db->findOne($sql, [
            'user_id' => $userId,
            'place_id' => $placeId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ]);

        return $result['count'];
    }
    /**
     * Récupère un utilisateur par son ID
     */
    public function getUserById($userId)
    {
        // Vérifier si la colonne is_subscribed existe
        try {
            // Pour SHOW COLUMNS, on ne peut pas utiliser de requêtes préparées avec LIKE
            $stmt = $this->db->getConnection()->query("SHOW COLUMNS FROM `users` LIKE 'is_subscribed'");
            $columnExists = $stmt ? $stmt->fetch() : false;

            if ($columnExists) {
                $sql = "SELECT id, email, nom, prenom, role, is_subscribed 
                        FROM users 
                        WHERE id = :id";
            } else {
                $sql = "SELECT id, email, nom, prenom, role, 0 as is_subscribed 
                        FROM users 
                        WHERE id = :id";
            }

            $user = $this->db->findOne($sql, ['id' => $userId]);

            if ($user && !isset($user['is_subscribed'])) {
                $user['is_subscribed'] = 0; // Par défaut, non abonné
            }

            return $user;
        } catch (Exception $e) {
            error_log("Erreur dans getUserById: " . $e->getMessage());

            // En cas d'erreur, retourner une valeur par défaut
            $sql = "SELECT id, email, nom, prenom, role 
                    FROM users 
                    WHERE id = :id";

            $user = $this->db->findOne($sql, ['id' => $userId]);

            if ($user) {
                $user['is_subscribed'] = 0; // Par défaut, non abonné
            }

            return $user;
        }
    }
    public function createImmediateReservation($userId, $placeId)
    {
        // Validation de base des entrées
        if (!$userId || !$placeId) {
            return false;
        }

        // Vérifier si la place existe
        $place = $this->getPlaceById($placeId);
        if (!$place) {
            return false;
        }

        // Vérifier si la place est disponible maintenant
        $now = date('Y-m-d H:i:s');
        if (!$this->isPlaceAvailableForNow($placeId)) {
            return false;
        }

        // Récupérer le tarif de la place
        $tarifHoraire = $this->getTarifByType($place['type']);
        if (!$tarifHoraire) {
            return false; // Type de place sans tarif défini
        }

        // Générer un code d'accès aléatoire plus long et plus visible
        $codeAcces = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

        // Pour les réservations immédiates, on définit une date de début immédiate et pas de date de fin
        $data = [
            'user_id' => $userId,
            'place_id' => $placeId,
            'date_debut' => $now,
            'date_fin' => null, // Date de fin non définie
            'status' => 'en_cours_immediat', // Statut spécial pour les réservations immédiates
            'code_acces' => $codeAcces,
            'montant_total' => 0, // Montant initial à 0, sera calculé à la fin
            'expiration_time' => null // Pas d'expiration pour les réservations immédiates
        ];

        // Insérer la réservation
        $reservationId = $this->db->insert('reservations', $data);

        // Mettre à jour le statut de la place
        if ($reservationId) {
            $this->updatePlaceStatus($placeId, 'occupe');

            // Vérifier que le code d'accès a bien été inséré
            $inserted = $this->getReservationById($reservationId);
            if ($inserted && empty($inserted['code_acces'])) {
                // Forcer la génération du code d'accès si nécessaire
                $this->generateOrUpdateAccessCode($reservationId);
            }
        }

        return $reservationId;
    }

    public function isPlaceAvailableForNow($placeId)
    {
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT COUNT(*) as count 
            FROM reservations 
            WHERE place_id = :place_id 
            AND status IN ('confirmée', 'en_cours', 'en_cours_immediat') 
            AND (
                (date_debut <= :now1 AND date_fin >= :now2) OR
                (date_debut <= :now3 AND date_fin IS NULL) -- Cas des réservations immédiates
            )";

        $result = $this->db->findOne($sql, [
            'place_id' => $placeId,
            'now1' => $now,
            'now2' => $now,
            'now3' => $now
        ]);

        return ($result['count'] == 0); // Retourne true si aucune réservation n'est active pour cette place maintenant
    }
    public function endImmediateReservation($reservationId)
    {
        // Récupérer les détails de la réservation
        $reservation = $this->getReservationById($reservationId);
        if (!$reservation || $reservation['status'] !== 'en_cours_immediat') {
            error_log("Erreur: Réservation non trouvée ou statut incorrect - ID: $reservationId");
            return false;
        }

        // Obtenir l'heure actuelle comme heure de fin
        $now = date('Y-m-d H:i:s');
        $dateFinObj = new DateTime($now);
        $dateDebutObj = new DateTime($reservation['date_debut']);

        // Calculer la durée en minutes
        $dureeMinutes = ($dateFinObj->getTimestamp() - $dateDebutObj->getTimestamp()) / 60;

        // Log de débogage
        error_log("Debug endImmediateReservation - Réservation ID: $reservationId");
        error_log("Date début: {$reservation['date_debut']}");
        error_log("Date fin: $now");
        error_log("Durée calculée: $dureeMinutes minutes");        // Récupérer le tarif de la place
        $place = $this->getPlaceById($reservation['place_id']);
        $tarifHoraire = $this->getTarifByType($place['type']);

        error_log("Place type: {$place['type']}");
        error_log("Tarif horaire: $tarifHoraire €/h");

        // Récupérer les avantages d'abonnement de l'utilisateur
        $subscriptionBenefits = $this->getUserSubscriptionBenefits($reservation['user_id']);

        // Appliquer les minutes gratuites et la réduction si l'utilisateur est abonné
        $freeMinutes = 0;
        $discountPercent = 0;

        if ($subscriptionBenefits) {
            $freeMinutes = $subscriptionBenefits['free_minutes'];
            $discountPercent = $subscriptionBenefits['discount_percent'];
            error_log("Avantages abonnement - Minutes gratuites: $freeMinutes, Réduction: $discountPercent%");
        } else {
            error_log("Aucun avantage d'abonnement");
        }        // Appliquer les minutes gratuites
        $dureeFacturee = max(0, $dureeMinutes - $freeMinutes);
        error_log("Durée facturée après minutes gratuites: $dureeFacturee minutes");

        // Convertir en heures pour le calcul du prix
        $dureeHeures = $dureeFacturee / 60;
        error_log("Durée en heures: $dureeHeures");

        // Calculer le montant de base avec un minimum de 15 minutes facturables
        $dureeMinimaleHeures = max($dureeHeures, 0.25); // Minimum 15 minutes (0.25h)
        $montantBase = $dureeMinimaleHeures * $tarifHoraire;
        error_log("Montant de base (avec minimum 15min): $montantBase");

        // Appliquer la réduction d'abonnement
        $montantTotal = $montantBase * (1 - ($discountPercent / 100));
        error_log("Montant total après réduction: $montantTotal");

        // Générer un code de sortie distinct du code d'entrée
        $codeSortie = strtoupper(substr(md5(uniqid(rand(), true) . time()), 0, 8));        // Mettre à jour la réservation avec la date de fin, le montant et le code de sortie
        $data = [
            'date_fin' => $now,
            'montant_total' => $montantTotal,
            'status' => $montantTotal > 0 ? 'en_attente_paiement' : 'terminée', // Corriger le statut
            'code_sortie' => $codeSortie // Ajout du code de sortie
        ];

        $result = $this->db->update('reservations', $data, 'id = :id', ['id' => $reservationId]);

        if ($result) {
            // Libérer la place
            $this->updatePlaceStatus($reservation['place_id'], 'libre');
            error_log("Réservation mise à jour avec succès - Montant: $montantTotal");
        } else {
            error_log("Erreur lors de la mise à jour de la réservation");
        }

        return [
            'success' => (bool)$result,
            'duree_minutes' => $dureeMinutes,
            'montant_total' => $montantTotal,
            'code_sortie' => $codeSortie // Retourner le code de sortie
        ];
    }

    public function getActiveImmediateReservation($userId)
    {
        $sql = "SELECT r.*, p.numero as place_numero, p.type as place_type 
            FROM reservations r
            JOIN parking_spaces p ON r.place_id = p.id
            WHERE r.user_id = :user_id 
            AND r.status = 'en_cours_immediat'
            ORDER BY r.date_debut DESC
            LIMIT 1";

        return $this->db->findOne($sql, ['user_id' => $userId]);
    }

    /**
     * Récupère toutes les réservations immédiates actives d'un utilisateur
     *
     * @param int $userId ID de l'utilisateur
     * @return array Liste des réservations immédiates actives
     */
    public function getUserActiveImmediateReservations($userId)
    {
        $sql = "SELECT r.*, p.numero as place_numero, p.type as place_type 
            FROM reservations r
            JOIN parking_spaces p ON r.place_id = p.id
            WHERE r.user_id = :user_id 
            AND r.status = 'en_cours_immediat'
            ORDER BY r.date_debut DESC";

        return $this->db->findAll($sql, ['user_id' => $userId]);
    }

    /**
     * Récupère les avantages d'abonnement de l'utilisateur
     * @param int $userId ID de l'utilisateur
     * @return array|null Les avantages d'abonnement ou null si pas d'abonnement actif
     */
    public function getUserSubscriptionBenefits($userId)
    {
        $sql = "SELECT 
                    COALESCE(
                        CASE 
                            WHEN a.duree = 'hebdomadaire' THEN 5
                            WHEN a.duree = 'mensuel' THEN 15
                            WHEN a.duree = 'annuel' THEN 30
                            ELSE 0
                        END, 
                        a.free_minutes, 
                        0
                    ) as free_minutes,
                    COALESCE(a.reduction, 0) as discount_percent,
                    a.nom as subscription_name,
                    ua.date_fin as end_date
                FROM user_abonnements ua
                JOIN abonnements a ON ua.abonnement_id = a.id
                WHERE ua.user_id = :user_id 
                AND ua.status = 'actif' 
                AND ua.date_fin > NOW()
                ORDER BY ua.date_fin DESC
                LIMIT 1";

        return $this->db->findOne($sql, ['user_id' => $userId]);
    }

    /**
     * Compte les réservations par type de place
     * @return array Tableau associatif avec le nombre de réservations par type de place
     */
    public function countReservationsByPlaceType()
    {
        $sql = "SELECT p.type, COUNT(r.id) as count 
                FROM reservations r
                JOIN parking_spaces p ON r.place_id = p.id
                WHERE r.status != 'annulée'
                GROUP BY p.type
                ORDER BY count DESC";

        $results = $this->db->findAll($sql);

        // Initialiser tous les types possibles avec 0
        $counts = array_fill_keys(['standard', 'handicape', 'electrique', 'moto/scooter', 'velo'], 0);

        // Remplir avec les valeurs réelles
        foreach ($results as $result) {
            if (!empty($result['type'])) {
                $counts[$result['type']] = (int)$result['count'];
            }
        }

        return $counts;
    }

    /**
     * Génère ou met à jour le code d'accès d'une réservation
     */
    public function generateOrUpdateAccessCode($reservationId)
    {
        if (!$reservationId) {
            return false;
        }

        // Générer un code d'accès aléatoire plus long et plus visible
        $newCode = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

        // Mettre à jour la réservation avec le nouveau code
        $result = $this->db->update(
            'reservations',
            ['code_acces' => $newCode],
            'id = :id',
            ['id' => $reservationId]
        );

        return $result ? $newCode : false;
    }

    /**
     * Génère ou met à jour le code de sortie d'une réservation
     */
    public function generateOrUpdateExitCode($reservationId)
    {
        if (!$reservationId) {
            return false;
        }

        // Générer un code de sortie aléatoire plus long et plus visible
        $newCode = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

        // Mettre à jour la réservation avec le nouveau code
        $result = $this->db->update(
            'reservations',
            ['code_sortie' => $newCode],
            'id = :id',
            ['id' => $reservationId]
        );

        return $result ? $newCode : false;
    }

    /**
     * Vérifie si une place a une réservation immédiate active
     * @param int $placeId ID de la place
     * @return array|false Retourne les détails de la réservation immédiate ou false
     */
    public function hasActiveImmediateReservation($placeId)
    {
        $sql = "SELECT id, date_debut, date_fin, guest_name, user_id
                FROM reservations 
                WHERE place_id = :place_id 
                AND status = 'en_cours_immediat'
                AND (date_fin IS NULL OR date_fin > NOW())
                ORDER BY date_debut DESC
                LIMIT 1";

        return $this->db->findOne($sql, ['place_id' => $placeId]);
    }

    /**
     * Vérifie si une place est disponible pour un créneau en tenant compte des réservations immédiates
     * @param int $placeId ID de la place
     * @param string $dateDebut Date de début souhaitée
     * @param string $dateFin Date de fin souhaitée
     * @param int|null $excludeReservationId ID de réservation à exclure
     * @return array Retourne un array avec 'available' (bool) et 'reason' (string)
     */
    public function checkPlaceAvailabilityDetailed($placeId, $dateDebut, $dateFin, $excludeReservationId = null)
    {
        // Vérifier d'abord le statut général de la place
        $place = $this->getPlaceById($placeId);
        if (!$place || $place['status'] === 'maintenance') {
            return [
                'available' => false,
                'reason' => 'Cette place est actuellement en maintenance.'
            ];
        }

        // Vérifier s'il y a une réservation immédiate en cours
        $immediateReservation = $this->hasActiveImmediateReservation($placeId);
        if ($immediateReservation) {
            $userName = $immediateReservation['user_id'] > 0 ? 'un utilisateur' : ($immediateReservation['guest_name'] ?: 'un invité');
            return [
                'available' => false,
                'reason' => "Cette place est actuellement occupée par une réservation immédiate de {$userName}."
            ];
        }

        // Vérifier les réservations classiques qui se chevauchent
        $sql = "SELECT COUNT(*) as count 
                FROM reservations 
                WHERE place_id = :place_id 
                AND status IN ('confirmée', 'en_cours', 'en_attente') 
                AND (:date_debut < date_fin) 
                AND (:date_fin > date_debut)";

        $params = [
            'place_id' => $placeId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ];

        if ($excludeReservationId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeReservationId;
        }

        $result = $this->db->findOne($sql, $params);

        if ($result['count'] > 0) {
            return [
                'available' => false,
                'reason' => 'Cette place n\'est pas disponible pour ce créneau horaire (réservation existante).'
            ];
        }

        return [
            'available' => true,
            'reason' => 'Place disponible'
        ];
    }

    /**
     * Récupère les changements de statut récents des réservations
     * @param int $lastCheck Timestamp de la dernière vérification
     * @return array Liste des réservations modifiées
     */
    public function getRecentStatusChanges($lastCheck)
    {
        $lastCheckDate = date('Y-m-d H:i:s', $lastCheck);

        // Utiliser created_at puisque updated_at n'existe pas dans la table
        $sql = "SELECT r.id, r.status, r.place_id, r.created_at as updated_at, p.numero as place_numero
                FROM reservations r
                JOIN parking_spaces p ON r.place_id = p.id
                WHERE r.created_at > :last_check
                   OR (r.status IN ('terminee', 'annulee') AND r.created_at > :last_check_alt)
                ORDER BY r.created_at DESC
                LIMIT 50";

        return $this->db->findAll($sql, [
            'last_check' => $lastCheckDate,
            'last_check_alt' => $lastCheckDate
        ]);
    }
}
