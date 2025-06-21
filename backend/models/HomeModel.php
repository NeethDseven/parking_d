<?php
/**
 * Modèle pour les fonctionnalités de la page d'accueil
 * Centralise les fonctions liées aux places, tarifs et statistiques publiques
 */
class HomeModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    /**
     * Récupère les places disponibles avec pagination
     * Inclut maintenant les places avec statut 'occupe'
     */
    public function getAvailablePlaces($page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;

        $sql = "SELECT id, numero, type, status, created_at 
                FROM parking_spaces 
                WHERE status IN ('libre', 'occupe')
                ORDER BY numero ASC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Compte le nombre total de places disponibles
     * Inclut maintenant les places avec statut 'occupe'
     */
    public function countAvailablePlaces()
    {
        $sql = "SELECT COUNT(*) as count
                FROM parking_spaces 
                WHERE status IN ('libre', 'occupe')";

        $result = $this->db->findOne($sql);
        return $result['count'];
    }
    /**
     * Récupère les places disponibles d'un certain type avec pagination
     * Inclut maintenant les places avec statut 'occupe'
     */
    public function getAvailablePlacesByType($type, $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;

        $sql = "SELECT id, numero, type, status, created_at 
                FROM parking_spaces 
                WHERE type = :type AND status IN ('libre', 'occupe')
                ORDER BY numero ASC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':type', $type, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Compte le nombre de places disponibles d'un certain type
     * Inclut maintenant les places avec statut 'occupe'
     */
    public function countAvailablePlacesByType($type)
    {
        $sql = "SELECT COUNT(*) as count
                FROM parking_spaces 
                WHERE type = :type AND status IN ('libre', 'occupe')";

        $result = $this->db->findOne($sql, ['type' => $type]);
        return $result['count'];
    }

    public function getAllPlaces()
    {
        $sql = "SELECT id, numero, type, status, created_at 
                FROM parking_spaces 
                ORDER BY numero ASC";

        return $this->db->findAll($sql);
    }

    public function getPlacesByType($type)
    {
        $sql = "SELECT id, numero, type, status, created_at 
                FROM parking_spaces 
                WHERE type = :type AND status = 'libre'
                ORDER BY numero ASC";

        return $this->db->findAll($sql, ['type' => $type]);
    }

    public function getPlaceById($id)
    {
        $sql = "SELECT id, numero, type, status, created_at 
                FROM parking_spaces 
                WHERE id = :id";

        return $this->db->findOne($sql, ['id' => $id]);
    }

    public function getTarifs()
    {
        $sql = "SELECT id, type_place, prix_heure, prix_journee, prix_mois 
                FROM tarifs";

        return $this->db->findAll($sql);
    }    public function getTarifByType($type)
    {
        $sql = "SELECT prix_heure 
                FROM tarifs 
                WHERE type_place = :type
                ORDER BY id DESC
                LIMIT 1";

        $result = $this->db->findOne($sql, ['type' => $type]);
        return $result ? (float) $result['prix_heure'] : 2.0; // Tarif par défaut de 2€/h
    }

    /**
     * Récupère les détails d'un tarif par son ID
     * @param int $id L'ID du tarif
     * @return array|false Les données du tarif ou false si non trouvé
     */
    public function getTarifById($id)
    {
        $sql = "SELECT id, type_place, prix_heure, prix_journee, prix_mois 
                FROM tarifs 
                WHERE id = :id";
        
        return $this->db->findOne($sql, ['id' => $id]);
    }    /**
     * Met à jour un tarif
     * @param int $id L'ID du tarif à mettre à jour
     * @param array $data Les données à mettre à jour
     * @return bool Succès ou échec de la mise à jour
     */
    public function updateTarif($id, $data)
    {
        return $this->db->update('tarifs', $data, 'id = :id', ['id' => $id]);
    }

    /**
     * Ajouter un nouveau tarif
     */
    public function addTarif($type_place, $prix_heure, $prix_journee, $prix_mois, $free_minutes = 0)
    {
        $data = [
            'type_place' => $type_place,
            'prix_heure' => $prix_heure,
            'prix_journee' => $prix_journee,
            'prix_mois' => $prix_mois,
            'free_minutes' => $free_minutes
        ];

        return $this->db->insert('tarifs', $data);
    }

    /**
     * Supprimer un tarif
     */
    public function deleteTarif($id)
    {
        return $this->db->delete('tarifs', 'id = :id', ['id' => $id]);
    }

    public function getHoraires()
    {
        $sql = "SELECT id, jour_semaine, heure_ouverture, heure_fermeture 
                FROM horaires_ouverture 
                ORDER BY jour_semaine ASC";

        return $this->db->findAll($sql);
    }

    public function getHorairesFormatted()
    {
        $horaires = $this->getHoraires();
        $jours = [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche'
        ];

        $formatted = [];
        foreach ($horaires as $horaire) {
            $formatted[] = [
                'jour' => $jours[$horaire['jour_semaine']],
                'heures' => date('H\hi', strtotime($horaire['heure_ouverture'])) . ' - ' .
                    date('H\hi', strtotime($horaire['heure_fermeture']))
            ];
        }

        return $formatted;
    }

    public function getStatistiques()
    {
        // Récupérer toutes les données en une seule requête
        $sql = "SELECT 
                (SELECT COUNT(*) FROM parking_spaces) as total,
                (SELECT COUNT(*) FROM parking_spaces WHERE status = 'libre') as disponibles";
        $stats = $this->db->findOne($sql);

        // Nombre de places par type
        $sql2 = "SELECT type, COUNT(*) as nombre FROM parking_spaces GROUP BY type";
        $parType = $this->db->findAll($sql2);

        // Réservations récentes
        $sql3 = "SELECT r.id, r.date_debut, r.date_fin, u.prenom, u.nom, p.numero
                FROM reservations r
                JOIN users u ON r.user_id = u.id
                JOIN parking_spaces p ON r.place_id = p.id
                WHERE r.status = 'confirmée'
                ORDER BY r.created_at DESC
                LIMIT 5";
        $reservations = $this->db->findAll($sql3);

        return [
            'total' => $stats['total'],
            'disponibles' => $stats['disponibles'],
            'occupation' => $stats['total'] > 0 ? round(($stats['total'] - $stats['disponibles']) / $stats['total'] * 100) : 0,
            'par_type' => $parType,
            'reservations_recentes' => $reservations
        ];
    }

    /**
     * Récupère les créneaux déjà réservés pour une place
     * @param int $placeId ID de la place
     * @return array Liste des créneaux réservés
     */
    public function getReservedTimeSlotsByPlace($placeId)
    {
        $sql = "SELECT date_debut, date_fin, status 
                FROM reservations 
                WHERE place_id = :place_id 
                AND status IN ('confirmée', 'en_cours')
                AND date_fin > NOW()
                ORDER BY date_debut ASC";

        return $this->db->findAll($sql, ['place_id' => $placeId]);
    }

    /**
     * Récupère les créneaux réservés pour toutes les places
     * @return array Liste des créneaux réservés par place_id
     */
    public function getAllReservedTimeSlots()
    {
        $sql = "SELECT place_id, date_debut, date_fin, status 
                FROM reservations 
                WHERE status IN ('confirmée', 'en_cours')
                AND date_fin > NOW()
                ORDER BY date_debut ASC";

        $slots = $this->db->findAll($sql);

        // Organiser les créneaux par place_id
        $slotsByPlace = [];
        foreach ($slots as $slot) {
            if (!isset($slotsByPlace[$slot['place_id']])) {
                $slotsByPlace[$slot['place_id']] = [];
            }
            $slotsByPlace[$slot['place_id']][] = $slot;
        }

        return $slotsByPlace;
    }

    /**
     * Récupère les informations sur l'occupation actuelle des places
     * @return array Tableau associatif [place_id] => [information sur l'occupation]
     */
    public function getCurrentOccupationInfo()
    {
        $now = date('Y-m-d H:i:s');

        $sql = "SELECT r.place_id, r.date_debut, r.date_fin, r.status
                FROM reservations r
                WHERE r.status IN ('confirmée', 'en_cours')
                AND :now BETWEEN r.date_debut AND r.date_fin";

        $occupations = $this->db->findAll($sql, ['now' => $now]);

        // Organiser par place_id
        $infoByPlace = [];
        foreach ($occupations as $occupation) {
            $infoByPlace[$occupation['place_id']] = $occupation;
        }

        return $infoByPlace;
    }

    /**
     * Récupérer l'historique des modifications de tarifs
     */
    public function getTarifsHistory($limit = 50)
    {
        $sql = "SELECT l.*, u.nom, u.prenom 
                FROM logs l
                LEFT JOIN users u ON l.user_id = u.id
                WHERE l.action IN ('modification_tarif', 'ajout_tarif', 'suppression_tarif')
                ORDER BY l.created_at DESC
                LIMIT :limit";

        return $this->db->findAll($sql, ['limit' => $limit]);
    }    /**
     * Compter les réservations par type de tarif
     */
    public function countReservationsByTarifType($typePlaceOrTarifId)
    {
        // Si c'est un ID numérique, récupérer d'abord le type de place
        if (is_numeric($typePlaceOrTarifId)) {
            $tarif = $this->getTarifById($typePlaceOrTarifId);
            $typePlaceOrTarifId = $tarif ? $tarif['type_place'] : null;
        }
        
        if (!$typePlaceOrTarifId) {
            return 0;
        }

        $sql = "SELECT COUNT(*) as count 
                FROM reservations r 
                INNER JOIN parking_spaces p ON r.place_id = p.id 
                WHERE p.type = :type_place";
        
        $result = $this->db->query($sql, ['type_place' => $typePlaceOrTarifId]);
        $data = $result->fetch();
        return $data ? (int)$data['count'] : 0;
    }
}
