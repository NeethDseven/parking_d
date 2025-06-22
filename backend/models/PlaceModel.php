<?php

require_once __DIR__ . '/BaseModel.php';

class PlaceModel extends BaseModel
{
    protected $tableName = 'parking_spaces';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllPlaces()
    {
        $sql = "SELECT id, numero, type, status, created_at
                FROM parking_spaces
                ORDER BY numero ASC";

        return $this->db->findAll($sql);
    }

    /* Utilise la méthode héritée getById() de BaseModel */

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

    // Récupère tous les tarifs indexés par type de place
    public function getAllTarifs()
    {
        $sql = "SELECT id, type_place, prix_heure, prix_journee, prix_mois FROM tarifs";
        $result = $this->db->findAll($sql);
        $tarifs = [];

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
        /* Vérifie que le numéro n'existe pas déjà */
        if (!$this->isFieldUnique('numero', $numero)) {
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
        /* Vérifie que le numéro n'existe pas pour une autre place */
        if (!$this->isFieldUnique('numero', $numero, $id)) {
            return false;
        }

        $data = [
            'numero' => $numero,
            'type' => $type,
            'status' => $status
        ];

        $result = $this->db->update('parking_spaces', $data, "id = :id", ['id' => $id]);
        return $result > 0;
    }

    /* Méthode numeroExists supprimée - utilise isFieldUnique() de BaseModel */

    public function delete($id, $forceDelete = false)
    {
        // Vérifie si la place a des réservations
        if ($this->hasReservations($id)) {
            if (!$forceDelete) {
                return false;
            }
            // Supprime en cascade toutes les données liées
            $this->deleteRelatedData($id);
        }

        return $this->db->delete('parking_spaces', 'id = :id', ['id' => $id]);
    }

    // Supprime toutes les données liées à une place
    private function deleteRelatedData($placeId)
    {
        $sql = "SELECT id FROM reservations WHERE place_id = :id";
        $reservations = $this->db->findAll($sql, ['id' => $placeId]);

        foreach ($reservations as $reservation) {
            $this->deleteReservationData($reservation['id']);
        }

        // Supprime toutes les réservations de la place
        $this->db->delete('reservations', 'place_id = :id', ['id' => $placeId]);
    }

    // Supprime les données liées à une réservation
    private function deleteReservationData($reservationId)
    {
        $sql = "SELECT id FROM paiements WHERE reservation_id = :reservation_id";
        $paiements = $this->db->findAll($sql, ['reservation_id' => $reservationId]);

        foreach ($paiements as $paiement) {
            // Supprime remboursements et factures
            $this->db->delete('remboursements', 'paiement_id = :paiement_id', ['paiement_id' => $paiement['id']]);
            $this->db->delete('factures', 'paiement_id = :paiement_id', ['paiement_id' => $paiement['id']]);
        }

        // Supprime les paiements
        $this->db->delete('paiements', 'reservation_id = :reservation_id', ['reservation_id' => $reservationId]);
    }
    /* Compte le nombre de places par statut - utilise la méthode héritée */
    public function getPlacesByStatus()
    {
        return parent::countByStatus('status');
    }

    // Compte le nombre de places par type
    public function countByType()
    {
        return $this->countByField('type', ['standard', 'handicape', 'electrique', 'moto/scooter', 'velo']);
    }

    // Méthode générique pour compter par champ
    private function countByField($field, $defaultKeys)
    {
        $sql = "SELECT $field, COUNT(*) as count FROM parking_spaces GROUP BY $field";
        $results = $this->db->findAll($sql);

        $counts = array_fill_keys($defaultKeys, 0);

        foreach ($results as $result) {
            if (!empty($result[$field])) {
                $counts[$result[$field]] = (int)$result['count'];
            }
        }

        return $counts;
    }

    // Récupère les places avec pagination
    public function getPlacesPaginated($offset, $limit)
    {
        return $this->getFilteredPlaces(null, null, $offset, $limit);
    }

    /* Utilise la méthode héritée countTotal() de BaseModel */

    // Récupère les places avec pagination et filtres
    public function getFilteredPlaces($type = null, $status = null, $offset = 0, $limit = 10)
    {
        [$whereClause, $params] = $this->buildFilterConditions($type, $status);

        $sql = "SELECT id, numero, type, status, created_at
                FROM parking_spaces $whereClause
                ORDER BY numero ASC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->getConnection()->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Compte le nombre de places avec filtres
    public function countFilteredPlaces($type = null, $status = null)
    {
        [$whereClause, $params] = $this->buildFilterConditions($type, $status);

        $sql = "SELECT COUNT(*) as count FROM parking_spaces $whereClause";

        $stmt = $this->db->getConnection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    // Alias pour compatibilité
    public function countFiltered($type = null, $status = null)
    {
        return $this->countFilteredPlaces($type, $status);
    }

    // Construit les conditions de filtrage pour éviter la duplication
    private function buildFilterConditions($type, $status)
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

        $whereClause = empty($conditions) ? "" : "WHERE " . implode(" AND ", $conditions);

        return [$whereClause, $params];
    }

    // Vérifie si une place a des réservations associées
    public function hasReservations($id)
    {
        return $this->countReservations($id) > 0;
    }

    // Compte les réservations associées à une place
    public function countReservations($id)
    {
        $sql = "SELECT COUNT(*) as count FROM reservations WHERE place_id = :id";
        $result = $this->db->findOne($sql, ['id' => $id]);
        return $result['count'];
    }

    // Récupère les places similaires (même type, excluant une place donnée)
    public function getSimilarPlacesByType($type, $excludePlaceId)
    {
        $sql = "SELECT id, numero, type, status, created_at
                FROM parking_spaces
                WHERE type = :type AND id != :excludePlaceId
                ORDER BY numero ASC";

        return $this->db->findAll($sql, [
            'type' => $type,
            'excludePlaceId' => $excludePlaceId
        ]);
    }

    // Alias pour compatibilité
    public function countPlaces()
    {
        return $this->countTotal();
    }

    // Crée une nouvelle place avec validation
    public function createPlace($data)
    {
        if (!isset($data['numero']) || !isset($data['type'])) {
            return false;
        }

        $status = $data['status'] ?? 'libre';
        return $this->create($data['numero'], $data['type'], $status);
    }

    // Met à jour une place avec validation
    public function updatePlace($id, $data)
    {
        if (!$id || !is_array($data)) {
            return false;
        }

        $currentPlace = $this->getById($id);
        if (!$currentPlace) {
            return false;
        }

        // Utilise les valeurs existantes si non fournies
        $numero = $data['numero'] ?? $currentPlace['numero'];
        $type = $data['type'] ?? $currentPlace['type'];
        $status = $data['status'] ?? $currentPlace['status'];

        return $this->update($id, $numero, $type, $status);
    }

    // Alias pour compatibilité
    public function deletePlace($id, $forceDelete = false)
    {
        return $id ? $this->delete($id, $forceDelete) : false;
    }
}
