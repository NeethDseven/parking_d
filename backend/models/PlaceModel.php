<?php
class PlaceModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $sql = "SELECT id, numero, type, status, created_at 
                FROM parking_spaces 
                ORDER BY numero ASC";

        return $this->db->findAll($sql);
    }

    public function getById($id)
    {
        $sql = "SELECT id, numero, type, status, created_at 
                FROM parking_spaces 
                WHERE id = :id";

        return $this->db->findOne($sql, ['id' => $id]);
    }

    public function getAvailable()
    {
        $sql = "SELECT id, numero, type, status, created_at 
                FROM parking_spaces 
                WHERE status = 'libre'
                ORDER BY numero ASC";

        return $this->db->findAll($sql);
    }

    public function getByType($type)
    {
        $sql = "SELECT id, numero, type, status, created_at 
                FROM parking_spaces 
                WHERE type = :type
                ORDER BY numero ASC";

        return $this->db->findAll($sql, ['type' => $type]);
    }

    public function getAvailableByType($type)
    {
        $sql = "SELECT id, numero, type, status, created_at 
                FROM parking_spaces 
                WHERE type = :type AND status = 'libre'
                ORDER BY numero ASC";

        return $this->db->findAll($sql, ['type' => $type]);
    }

    /**
     * Récupère tous les tarifs disponibles
     * @return array Tableau associatif des tarifs par type de place
     */
    public function getAllTarifs()
    {
        $sql = "SELECT id, type_place, prix_heure, prix_journee, prix_mois 
                FROM tarifs";

        $result = $this->db->findAll($sql);
        $tarifs = [];

        // Transformer le résultat en tableau associatif par type de place
        foreach ($result as $tarif) {
            $tarifs[$tarif['type_place']] = [
                'id' => $tarif['id'],
                'prix_heure' => $tarif['prix_heure'],
                'prix_journee' => $tarif['prix_journee'],
                'prix_mois' => $tarif['prix_mois']
            ];
        }

        return $tarifs;
    }

    public function updateStatus($id, $status)
    {
        $data = ['status' => $status];
        $where = "id = :id";
        $params = ['id' => $id];

        return $this->db->update('parking_spaces', $data, $where, $params);
    }

    public function create($numero, $type, $status = 'libre')
    {
        // Vérifier que le numéro n'existe pas déjà
        $sql = "SELECT id FROM parking_spaces WHERE numero = :numero";
        $existing = $this->db->findOne($sql, ['numero' => $numero]);

        if ($existing) {
            return false;
        }

        $data = [
            'numero' => $numero,
            'type' => $type,
            'status' => $status
        ];

        return $this->db->insert('parking_spaces', $data);
    }

    public function update($id, $numero, $type, $status)
    {
        // Vérifier que le numéro n'existe pas déjà pour une autre place
        $sql = "SELECT id FROM parking_spaces WHERE numero = :numero AND id != :id";
        $existing = $this->db->findOne($sql, ['numero' => $numero, 'id' => $id]);

        if ($existing) {
            return false;
        }

        $data = [
            'numero' => $numero,
            'type' => $type,
            'status' => $status
        ];

        $where = "id = :id";
        $params = ['id' => $id];

        $result = $this->db->update('parking_spaces', $data, $where, $params);
        return $result > 0; // Assurons-nous de retourner un boolean
    }

    public function delete($id, $forceDelete = false)
    {
        // Vérifier si la place est utilisée dans des réservations
        $sql = "SELECT COUNT(*) as count FROM reservations WHERE place_id = :id";
        $result = $this->db->findOne($sql, ['id' => $id]);

        if ($result['count'] > 0) {
            if (!$forceDelete) {
                // Si force_delete n'est pas activé, ne pas supprimer
                return false;
            }

            // Avec force_delete activé, supprimer d'abord les réservations associées

            // Trouver toutes les réservations associées
            $sql = "SELECT id FROM reservations WHERE place_id = :id";
            $reservations = $this->db->findAll($sql, ['id' => $id]);

            // Supprimer les paiements et factures associés à ces réservations
            foreach ($reservations as $reservation) {
                // 1. Trouver les paiements associés
                $sql = "SELECT id FROM paiements WHERE reservation_id = :reservation_id";
                $paiements = $this->db->findAll($sql, ['reservation_id' => $reservation['id']]);

                foreach ($paiements as $paiement) {
                    // Supprimer les remboursements associés aux paiements
                    $this->db->delete('remboursements', 'paiement_id = :paiement_id', ['paiement_id' => $paiement['id']]);

                    // Supprimer les factures associées aux paiements
                    $this->db->delete('factures', 'paiement_id = :paiement_id', ['paiement_id' => $paiement['id']]);
                }

                // 2. Supprimer les paiements
                $this->db->delete('paiements', 'reservation_id = :reservation_id', ['reservation_id' => $reservation['id']]);
            }

            // 3. Supprimer toutes les réservations associées à cette place
            $this->db->delete('reservations', 'place_id = :id', ['id' => $id]);
        }

        // Finalement, supprimer la place
        return $this->db->delete('parking_spaces', 'id = :id', ['id' => $id]);
    }
    /**
     * Compte le nombre de places par statut
     * @return array Tableau associatif des comptages par statut
     */
    public function countByStatus()
    {
        $sql = "SELECT status, COUNT(*) as count FROM parking_spaces GROUP BY status";
        $results = $this->db->findAll($sql);

        // Initialiser tous les statuts possibles avec 0
        $counts = array_fill_keys(['libre', 'occupe', 'maintenance'], 0);

        // Remplir avec les valeurs réelles
        foreach ($results as $result) {
            $counts[$result['status']] = (int)$result['count'];
        }

        return $counts;
    }

    /**
     * Compte le nombre de places par type
     * @return array Tableau associatif des comptages par type
     */      public function countByType()
    {
        $sql = "SELECT type, COUNT(*) as count FROM parking_spaces GROUP BY type";
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
     * Récupère les places avec pagination
     */
    public function getPlacesPaginated($offset, $limit)
    {
        $sql = "SELECT id, numero, type, status, created_at 
                FROM parking_spaces 
                ORDER BY numero ASC 
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte le nombre total de places
     */
    public function countTotal()
    {
        $sql = "SELECT COUNT(*) as count FROM parking_spaces";
        $result = $this->db->findOne($sql);
        return $result['count'];
    }

    /**
     * Récupère les places avec pagination et filtres
     */
    public function getFilteredPlaces($type = null, $status = null, $offset = 0, $limit = 10)
    {
        $conditions = [];
        $params = [];

        if ($type && $type !== '') {
            $conditions[] = "type = :type";
            $params['type'] = $type;
        }

        if ($status && $status !== '') {
            $conditions[] = "status = :status";
            $params['status'] = $status;
        }

        $whereClause = "";
        if (!empty($conditions)) {
            $whereClause = "WHERE " . implode(" AND ", $conditions);
        }

        $sql = "SELECT id, numero, type, status, created_at 
                FROM parking_spaces 
                $whereClause
                ORDER BY numero ASC 
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->getConnection()->prepare($sql);

        // Bind les paramètres de filtre
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        // Bind les paramètres de pagination
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte le nombre de places avec filtres
     */
    public function countFilteredPlaces($type = null, $status = null)
    {
        $conditions = [];
        $params = [];

        if ($type && $type !== '') {
            $conditions[] = "type = :type";
            $params['type'] = $type;
        }

        if ($status && $status !== '') {
            $conditions[] = "status = :status";
            $params['status'] = $status;
        }

        $whereClause = "";
        if (!empty($conditions)) {
            $whereClause = "WHERE " . implode(" AND ", $conditions);
        }

        $sql = "SELECT COUNT(*) as count FROM parking_spaces $whereClause";

        $stmt = $this->db->getConnection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    /**
     * Compte le nombre total de places avec filtres (ancienne méthode)
     */
    public function countFiltered($type = null, $status = null)
    {
        $conditions = [];
        $params = [];

        if ($type && $type !== '') {
            $conditions[] = "type = :type";
            $params['type'] = $type;
        }

        if ($status && $status !== '') {
            $conditions[] = "status = :status";
            $params['status'] = $status;
        }

        $whereClause = "";
        if (!empty($conditions)) {
            $whereClause = "WHERE " . implode(" AND ", $conditions);
        }

        $sql = "SELECT COUNT(*) as count FROM parking_spaces $whereClause";

        $result = $this->db->findOne($sql, $params);
        return $result['count'];
    }

    /**
     * Vérifie si une place a des réservations associées
     *
     * @param int $id ID de la place
     * @return bool True si la place a des réservations, false sinon
     */
    public function hasReservations($id)
    {
        $sql = "SELECT COUNT(*) as count FROM reservations WHERE place_id = :id";
        $result = $this->db->findOne($sql, ['id' => $id]);
        return $result['count'] > 0;
    }

    /**
     * Compte les réservations associées à une place
     *
     * @param int $id ID de la place
     * @return int Nombre de réservations
     */
    public function countReservations($id)
    {
        $sql = "SELECT COUNT(*) as count FROM reservations WHERE place_id = :id";
        $result = $this->db->findOne($sql, ['id' => $id]);
        return $result['count'];
    }

    /**
     * Récupère les places similaires à une place donnée (même type)
     * @param string $type Type de place recherché (standard, pmr, electrique, etc.)
     * @param int $excludePlaceId ID de la place à exclure (la place d'origine)
     * @return array Liste des places similaires
     */
    public function getSimilarPlacesByType($type, $excludePlaceId)
    {
        $sql = "SELECT id, numero, type, status, created_at 
                FROM parking_spaces 
                WHERE type = :type
                AND id != :excludePlaceId
                ORDER BY numero ASC";

        return $this->db->findAll($sql, [
            'type' => $type,
            'excludePlaceId' => $excludePlaceId
        ]);
    }

    /**
     * Count the total number of parking spaces
     * @return int Total number of parking spaces
     */
    public function countPlaces()
    {
        $sql = "SELECT COUNT(*) as count FROM parking_spaces";
        $result = $this->db->findOne($sql);
        return isset($result['count']) ? $result['count'] : 0;
    }

    /**
     * Create a new parking place
     * @param array $data Place data (numero, type, status)
     * @return int|bool ID of created place or false on failure
     */
    public function createPlace($data)
    {
        // Validation des données
        if (!isset($data['numero']) || !isset($data['type'])) {
            return false;
        }

        // Définir le statut par défaut si non fourni
        if (!isset($data['status'])) {
            $data['status'] = 'libre';
        }

        return $this->create($data['numero'], $data['type'], $data['status']);
    }

    /**
     * Update a parking place
     * @param int $id Place ID
     * @param array $data Place data to update (numero, type, status)
     * @return bool Success or failure
     */
    public function updatePlace($id, $data)
    {
        // Validation des données
        if (!$id || !is_array($data)) {
            return false;
        }

        // Récupérer les données actuelles de la place
        $currentPlace = $this->getById($id);
        if (!$currentPlace) {
            return false;
        }

        // Utiliser les valeurs existantes si non fournies
        $numero = isset($data['numero']) ? $data['numero'] : $currentPlace['numero'];
        $type = isset($data['type']) ? $data['type'] : $currentPlace['type'];
        $status = isset($data['status']) ? $data['status'] : $currentPlace['status'];

        return $this->update($id, $numero, $type, $status);
    }

    /**
     * Delete a parking place
     * @param int $id Place ID
     * @param bool $forceDelete Whether to force delete even if has reservations
     * @return bool Success or failure
     */
    public function deletePlace($id, $forceDelete = false)
    {
        if (!$id) {
            return false;
        }

        return $this->delete($id, $forceDelete);
    }
}
