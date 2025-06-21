<?php
class SubscriptionModel
{
    private $db;
    public function __construct()
    {
        $this->db = Database::getInstance();

        // S'assurer que les tables existent
        $this->checkTablesExist();
    }

    /**
     * Vérifie si les tables nécessaires existent et crée des données par défaut si nécessaire
     */    private function checkTablesExist()
    {
        // Vérifier que la table abonnements existe et contient des données
        $sql = "SELECT COUNT(*) as count FROM abonnements";
        $result = $this->db->findOne($sql);

        if (!$result || $result['count'] == 0) {
            // La table abonnements est vide, ajoutons quelques abonnements par défaut
            $defaultSubscriptions = [
                [
                    'nom' => 'Hebdomadaire',
                    'duree' => 'hebdomadaire',
                    'reduction' => 5.00,
                    'description' => 'Abonnement hebdomadaire avec 5% de réduction sur toutes les réservations',
                    'price' => 19.99
                ],
                [
                    'nom' => 'Mensuel',
                    'duree' => 'mensuel',
                    'reduction' => 15.00,
                    'description' => 'Abonnement mensuel avec 15% de réduction sur toutes les réservations',
                    'price' => 49.99
                ],
                [
                    'nom' => 'Annuel',
                    'duree' => 'annuel',
                    'reduction' => 30.00,
                    'description' => 'Abonnement annuel avec 30% de réduction sur toutes les réservations',
                    'price' => 159
                ]
            ];

            foreach ($defaultSubscriptions as $subscription) {
                $this->db->insert('abonnements', $subscription);
            }
        }

        // Vérifier si la colonne price existe dans la table
        $checkColumn = $this->db->safeFindOne("SHOW COLUMNS FROM abonnements LIKE 'price'");

        if (!$checkColumn) {
            try {
                // Ajouter la colonne price
                $this->db->query("ALTER TABLE `abonnements` ADD COLUMN `price` decimal(10,2) DEFAULT NULL COMMENT 'Prix de l\\'abonnement'");

                // Mettre à jour les prix par défaut
                $this->db->query("UPDATE `abonnements` SET `price` = 19.99 WHERE `duree` = 'hebdomadaire' AND (`price` IS NULL OR `price` = 0)");
                $this->db->query("UPDATE `abonnements` SET `price` = 49.99 WHERE `duree` = 'mensuel' AND (`price` IS NULL OR `price` = 0)");
                $this->db->query("UPDATE `abonnements` SET `price` = 159 WHERE `duree` = 'annuel' AND (`price` IS NULL OR `price` = 0)");
            } catch (Exception $e) {
                error_log("Erreur lors de la création de la colonne price : " . $e->getMessage());
            }
        }
    }

    /**
     * Récupère tous les abonnements disponibles
     * @param bool $activeOnly Ne récupérer que les abonnements actifs
     * @return array Liste des abonnements
     */    public function getAllSubscriptions($activeOnly = false)
    {
        // Vérifier si la colonne price existe dans la table
        $checkColumn = $this->db->safeFindOne("SHOW COLUMNS FROM abonnements LIKE 'price'");

        if ($checkColumn) {
            // Si la colonne price existe, l'utiliser
            $sql = "SELECT id, nom as name, description, 
                    CASE 
                        WHEN duree = 'hebdomadaire' THEN 7
                        WHEN duree = 'mensuel' THEN 30
                        WHEN duree = 'annuel' THEN 365
                        ELSE 0
                    END as duration_days,
                    reduction as discount_percent,
                    price,
                    CASE 
                        WHEN duree = 'hebdomadaire' THEN 5
                        WHEN duree = 'mensuel' THEN 15
                        WHEN duree = 'annuel' THEN 30
                        ELSE 0
                    END as free_minutes,
                    1 as is_active
                    FROM abonnements";
        } else {
            // Si la colonne price n'existe pas, utiliser les valeurs par défaut
            $sql = "SELECT id, nom as name, description, 
                    CASE 
                        WHEN duree = 'hebdomadaire' THEN 7
                        WHEN duree = 'mensuel' THEN 30
                        WHEN duree = 'annuel' THEN 365
                        ELSE 0
                    END as duration_days,
                    reduction as discount_percent, 
                    CASE 
                        WHEN duree = 'hebdomadaire' THEN 19.99
                        WHEN duree = 'mensuel' THEN 49.99
                        WHEN duree = 'annuel' THEN 159
                        ELSE 0
                    END as price,
                    CASE 
                        WHEN duree = 'hebdomadaire' THEN 5
                        WHEN duree = 'mensuel' THEN 15
                        WHEN duree = 'annuel' THEN 30
                        ELSE 0
                    END as free_minutes,
                    1 as is_active
                    FROM abonnements";
        }

        $sql .= " ORDER BY price ASC";

        return $this->db->findAll($sql);
    }

    /**
     * Récupère un abonnement par son ID
     * @param int $id ID de l'abonnement
     * @return array|false Données de l'abonnement ou false si non trouvé
     */    public function getSubscriptionById($id)
    {
        // Vérifier si la colonne price existe dans la table
        $checkColumn = $this->db->safeFindOne("SHOW COLUMNS FROM abonnements LIKE 'price'");

        if ($checkColumn) {
            // Si la colonne price existe, l'utiliser
            $sql = "SELECT id, nom as name, description, 
                    CASE 
                        WHEN duree = 'hebdomadaire' THEN 7
                        WHEN duree = 'mensuel' THEN 30
                        WHEN duree = 'annuel' THEN 365
                        ELSE 0
                    END as duration_days,
                    reduction as discount_percent,
                    price,
                    CASE 
                        WHEN duree = 'hebdomadaire' THEN 5
                        WHEN duree = 'mensuel' THEN 15
                        WHEN duree = 'annuel' THEN 30
                        ELSE 0
                    END as free_minutes,
                    1 as is_active
                    FROM abonnements 
                    WHERE id = :id";
        } else {
            // Si la colonne price n'existe pas, utiliser les valeurs par défaut
            $sql = "SELECT id, nom as name, description, 
                    CASE 
                        WHEN duree = 'hebdomadaire' THEN 7
                        WHEN duree = 'mensuel' THEN 30
                        WHEN duree = 'annuel' THEN 365
                        ELSE 0
                    END as duration_days,
                    reduction as discount_percent, 
                    CASE 
                        WHEN duree = 'hebdomadaire' THEN 19.99
                        WHEN duree = 'mensuel' THEN 49.99
                        WHEN duree = 'annuel' THEN 159
                        ELSE 0
                    END as price,
                    CASE 
                        WHEN duree = 'hebdomadaire' THEN 5
                        WHEN duree = 'mensuel' THEN 15
                        WHEN duree = 'annuel' THEN 30
                        ELSE 0
                    END as free_minutes,
                    1 as is_active
                    FROM abonnements 
                    WHERE id = :id";
        }

        return $this->db->findOne($sql, ['id' => $id]);
    }

    /**
     * Crée un nouvel abonnement
     * @param array $data Données de l'abonnement
     * @return int|false ID de l'abonnement créé ou false en cas d'échec
     */    public function createSubscription($data)
    {
        // Transformer les données au format de la table abonnements
        $abonnementData = [
            'nom' => $data['name'],
            'description' => $data['description'] ?? '',
            'duree' => $this->getDureeFromDays($data['duration_days']),
            'reduction' => $data['discount_percent']
        ];

        // Vérifier si la colonne price existe dans la table
        $checkColumn = $this->db->safeFindOne("SHOW COLUMNS FROM abonnements LIKE 'price'");

        // Si la colonne price existe et que le prix est défini, l'ajouter aux données
        if ($checkColumn && isset($data['price'])) {
            $abonnementData['price'] = $data['price'];
        }

        return $this->db->insert('abonnements', $abonnementData);
    }

    /**
     * Met à jour un abonnement existant
     * @param int $id ID de l'abonnement
     * @param array $data Nouvelles données
     * @return bool Succès de l'opération
     */    public function updateSubscription($id, $data)
    {
        // Transformer les données au format de la table abonnements
        $abonnementData = [];

        if (isset($data['name'])) {
            $abonnementData['nom'] = $data['name'];
        }

        if (isset($data['description'])) {
            $abonnementData['description'] = $data['description'];
        }

        if (isset($data['duration_days'])) {
            $abonnementData['duree'] = $this->getDureeFromDays($data['duration_days']);
        }

        if (isset($data['discount_percent'])) {
            $abonnementData['reduction'] = $data['discount_percent'];
        }

        // Vérifier si la colonne price existe dans la table
        $checkColumn = $this->db->safeFindOne("SHOW COLUMNS FROM abonnements LIKE 'price'");

        // Si la colonne price existe et que le prix est défini, l'ajouter aux données
        if ($checkColumn && isset($data['price'])) {
            $abonnementData['price'] = $data['price'];
        }

        if (empty($abonnementData)) {
            return false;
        }

        // Vérifier si les données sont différentes des données existantes
        $currentData = $this->getSubscriptionById($id);
        if (!$currentData) {
            return false;
        }

        // Faire la mise à jour
        $result = $this->db->update('abonnements', $abonnementData, 'id = :id', ['id' => $id]);

        // Si aucune ligne n'a été modifiée mais c'est juste parce que les données étaient identiques
        // on considère que c'est un succès
        if ($result === 0) {
            return true;
        }

        return $result > 0;
    }

    /**
     * Supprime un abonnement
     * @param int $id ID de l'abonnement
     * @return bool Succès de l'opération
     */
    public function deleteSubscription($id)
    {        // Vérifier s'il existe des utilisateurs avec cet abonnement
        $sql = "SELECT COUNT(*) as count FROM user_abonnements WHERE abonnement_id = :id AND status = 'actif'";
        $result = $this->db->findOne($sql, ['id' => $id]);

        if ($result && $result['count'] > 0) {
            // Ne pas supprimer l'abonnement car il est utilisé
            return false;
        }

        return $this->db->delete('abonnements', 'id = :id', ['id' => $id]) > 0;
    }

    /**
     * Convertit un nombre de jours en type de durée
     * @param int $days Nombre de jours
     * @return string Type de durée (hebdomadaire, mensuel, annuel)
     */
    private function getDureeFromDays($days)
    {
        if ($days <= 7) {
            return 'hebdomadaire';
        } else if ($days <= 31) {
            return 'mensuel';
        } else {
            return 'annuel';
        }
    }
    /**
     * Souscrit un utilisateur à un abonnement
     * @param int $userId ID de l'utilisateur
     * @param int $subscriptionId ID de l'abonnement
     * @param int $paymentId ID du paiement associé
     * @return int|false ID de la souscription ou false en cas d'échec
     */
    public function subscribeUser($userId, $subscriptionId, $paymentId = null)
    {
        // Récupérer les détails de l'abonnement
        $subscription = $this->getSubscriptionById($subscriptionId);
        if (!$subscription) {
            return false;
        }

        // Calculer les dates de début et de fin
        $startDate = date('Y-m-d H:i:s');
        $endDate = date('Y-m-d H:i:s', strtotime("+{$subscription['duration_days']} days"));

        // Préparer les données
        $data = [
            'user_id' => $userId,
            'abonnement_id' => $subscriptionId,
            'date_debut' => $startDate,
            'date_fin' => $endDate,
            'status' => 'actif',
            'payment_id' => $paymentId
        ];

        // Insérer la souscription
        $subscriptionId = $this->db->insert('user_abonnements', $data);

        if ($subscriptionId) {
            // Mettre à jour le statut d'abonnement de l'utilisateur
            $this->updateUserSubscriptionStatus($userId);
        }

        return $subscriptionId;
    }
    /**
     * Annule un abonnement utilisateur
     * @param int $userId ID de l'utilisateur
     * @param int $userSubscriptionId ID de la souscription utilisateur
     * @return bool Succès de l'opération
     */
    public function cancelUserSubscription($userId, $userSubscriptionId)
    {
        // Vérifier que l'abonnement appartient bien à l'utilisateur
        $sql = "SELECT id FROM user_abonnements WHERE id = :id AND user_id = :user_id";
        $subscription = $this->db->findOne($sql, ['id' => $userSubscriptionId, 'user_id' => $userId]);

        if (!$subscription) {
            return false;
        }

        // Mettre à jour le statut de l'abonnement
        $result = $this->db->update(
            'user_abonnements',
            ['status' => 'résilié'],
            'id = :id',
            ['id' => $userSubscriptionId]
        );

        if ($result) {
            // Mettre à jour le statut d'abonnement de l'utilisateur
            $this->updateUserSubscriptionStatus($userId);
        }

        return $result > 0;
    }
    /**
     * Vérifie et met à jour le statut d'abonnement d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return bool True si l'utilisateur a un abonnement actif
     */
    public function updateUserSubscriptionStatus($userId)
    {
        // Vérifier si l'utilisateur a un abonnement actif
        $sql = "SELECT COUNT(*) as count 
                FROM user_abonnements 
                WHERE user_id = :user_id 
                AND status = 'actif' 
                AND date_fin > NOW()";

        $result = $this->db->findOne($sql, ['user_id' => $userId]);
        $hasActiveSubscription = ($result && $result['count'] > 0);

        // Mettre à jour le statut de l'utilisateur
        $this->db->update(
            'users',
            ['is_subscribed' => $hasActiveSubscription ? 1 : 0],
            'id = :id',
            ['id' => $userId]
        );

        return $hasActiveSubscription;
    }
    /**
     * Récupère les abonnements actifs d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return array Liste des abonnements actifs
     */    public function getUserActiveSubscriptions($userId)
    {        // Vérifier si la colonne price existe dans la table
        $checkColumn = $this->db->safeFindOne("SHOW COLUMNS FROM abonnements LIKE 'price'");

        if ($checkColumn) {
            // Si la colonne price existe, l'utiliser avec free_minutes depuis la base
            $sql = "SELECT ua.id, ua.date_debut as start_date, ua.date_fin as end_date, ua.status,
                       a.nom as name, a.description, 
                       COALESCE(a.price,
                           CASE 
                               WHEN a.duree = 'hebdomadaire' THEN 19.99
                               WHEN a.duree = 'mensuel' THEN 49.99
                               WHEN a.duree = 'annuel' THEN 159
                               ELSE 0
                           END
                       ) as price, 
                       COALESCE(a.free_minutes, 0) as free_minutes,
                       a.reduction as discount_percent
                FROM user_abonnements ua
                JOIN abonnements a ON ua.abonnement_id = a.id
                WHERE ua.user_id = :user_id 
                AND ua.status = 'actif' 
                AND ua.date_fin > NOW()
                ORDER BY ua.date_fin DESC";
        } else {
            // Si la colonne price n'existe pas, utiliser les valeurs par défaut
            $sql = "SELECT ua.id, ua.date_debut as start_date, ua.date_fin as end_date, ua.status,
                       a.nom as name, a.description, 
                       CASE 
                           WHEN a.duree = 'hebdomadaire' THEN 19.99
                           WHEN a.duree = 'mensuel' THEN 49.99
                           WHEN a.duree = 'annuel' THEN 159
                           ELSE 0
                       END as price, 
                       COALESCE(a.free_minutes, 
                           CASE 
                               WHEN a.duree = 'hebdomadaire' THEN 5
                               WHEN a.duree = 'mensuel' THEN 15
                               WHEN a.duree = 'annuel' THEN 30
                               ELSE 0
                           END
                       ) as free_minutes,
                       a.reduction as discount_percent
                FROM user_abonnements ua
                JOIN abonnements a ON ua.abonnement_id = a.id
                WHERE ua.user_id = :user_id 
                AND ua.status = 'actif' 
                AND ua.date_fin > NOW()
                ORDER BY ua.date_fin DESC";
        }

        return $this->db->findAll($sql, ['user_id' => $userId]);
    }
    /**
     * Récupère l'historique d'abonnements d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return array Historique des abonnements
     */    public function getUserSubscriptionHistory($userId)
    {
        // Vérifier si la colonne price existe dans la table
        $checkColumn = $this->db->safeFindOne("SHOW COLUMNS FROM abonnements LIKE 'price'");

        if ($checkColumn) {
            // Si la colonne price existe, l'utiliser
            $sql = "SELECT ua.id, ua.date_debut as start_date, ua.date_fin as end_date, ua.status, ua.created_at,
                        a.nom as name, 
                        COALESCE(a.price,
                            CASE 
                                WHEN a.duree = 'hebdomadaire' THEN 19.99
                                WHEN a.duree = 'mensuel' THEN 49.99
                                WHEN a.duree = 'annuel' THEN 159
                                ELSE 0
                            END
                        ) as price
                    FROM user_abonnements ua
                    JOIN abonnements a ON ua.abonnement_id = a.id
                    WHERE ua.user_id = :user_id
                    ORDER BY ua.created_at DESC";
        } else {
            // Si la colonne price n'existe pas, utiliser les valeurs par défaut
            $sql = "SELECT ua.id, ua.date_debut as start_date, ua.date_fin as end_date, ua.status, ua.created_at,
                        a.nom as name, 
                        CASE 
                            WHEN a.duree = 'hebdomadaire' THEN 19.99
                            WHEN a.duree = 'mensuel' THEN 49.99
                            WHEN a.duree = 'annuel' THEN 159
                            ELSE 0
                        END as price
                    FROM user_abonnements ua
                    JOIN abonnements a ON ua.abonnement_id = a.id
                    WHERE ua.user_id = :user_id
                    ORDER BY ua.created_at DESC";
        }

        return $this->db->findAll($sql, ['user_id' => $userId]);
    }
    /**
     * Vérifie et met à jour le statut de tous les abonnements utilisateur expirés
     * @return array Statistiques sur les mises à jour
     */
    public function updateExpiredSubscriptions()
    {
        $now = date('Y-m-d H:i:s');
        $stats = [
            'updated' => 0,
            'errors' => 0
        ];

        // Récupérer les abonnements expirés mais toujours marqués comme actifs
        $sql = "SELECT id, user_id FROM user_abonnements 
                WHERE status = 'actif' AND date_fin < :now";

        $expiredSubscriptions = $this->db->findAll($sql, ['now' => $now]);

        foreach ($expiredSubscriptions as $subscription) {
            // Mettre à jour le statut de l'abonnement
            $result = $this->db->update(
                'user_abonnements',
                ['status' => 'expiré'],
                'id = :id',
                ['id' => $subscription['id']]
            );

            if ($result) {
                $stats['updated']++;

                // Mettre à jour le statut d'abonnement de l'utilisateur
                $this->updateUserSubscriptionStatus($subscription['user_id']);
            } else {
                $stats['errors']++;
            }
        }

        return $stats;
    }
    /**
     * Récupère le nombre d'abonnements actifs
     * @return int Nombre d'abonnements actifs
     */
    public function countActiveSubscriptions()
    {
        $sql = "SELECT COUNT(*) as count 
                FROM user_abonnements 
                WHERE status = 'actif' 
                AND date_fin > NOW()";

        $result = $this->db->findOne($sql);
        return $result ? $result['count'] : 0;
    }
    /**
     * Récupère le nombre d'abonnements actifs par type d'abonnement
     * @return array Statistiques par type d'abonnement
     */    public function getSubscriptionStats()
    {
        // S'il y a des abonnements actifs, les afficher
        $sql = "SELECT COUNT(*) as exists_count FROM user_abonnements WHERE status = 'actif' AND date_fin > NOW()";
        $result = $this->db->findOne($sql);

        if ($result && $result['exists_count'] > 0) {
            $sql = "SELECT a.nom as name, COUNT(*) as count
                    FROM user_abonnements ua
                    JOIN abonnements a ON ua.abonnement_id = a.id
                    WHERE ua.status = 'actif' AND ua.date_fin > NOW()
                    GROUP BY a.id, a.nom
                    ORDER BY count DESC";
            return $this->db->findAll($sql);
        } else {
            // S'il n'y a pas d'abonnements actifs, retourner les types d'abonnements avec un compteur à zéro
            $sql = "SELECT nom as name, 0 as count FROM abonnements ORDER BY id";
            return $this->db->findAll($sql);
        }
    }
    /**
     * Récupère le revenu total généré par les abonnements
     * @param string $period Période (today, week, month, year, total)
     * @return float Montant total
     */    public function calculateSubscriptionRevenue($period = 'total')
    {
        // Vérifier d'abord s'il y a des abonnements
        $checkSql = "SELECT COUNT(*) as count FROM user_abonnements";
        $checkResult = $this->db->findOne($checkSql);

        if (!$checkResult || $checkResult['count'] == 0) {
            return 0; // Pas d'abonnements, donc pas de revenus
        }

        $whereClause = '';

        if ($period === 'today') {
            $whereClause = "AND DATE(ua.created_at) = CURDATE()";
        } elseif ($period === 'week') {
            $whereClause = "AND ua.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        } elseif ($period === 'month') {
            $whereClause = "AND ua.created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')";
        } elseif ($period === 'year') {
            $whereClause = "AND ua.created_at >= DATE_FORMAT(NOW(), '%Y-01-01')";
        }

        // Vérifier si la colonne price existe dans la table
        $checkColumn = $this->db->safeFindOne("SHOW COLUMNS FROM abonnements LIKE 'price'");

        if ($checkColumn) {
            // Si la colonne price existe, l'utiliser
            $sql = "SELECT SUM(COALESCE(a.price, 
                        CASE 
                            WHEN a.duree = 'hebdomadaire' THEN 19.99
                            WHEN a.duree = 'mensuel' THEN 49.99
                            WHEN a.duree = 'annuel' THEN 159
                            ELSE 0
                        END)
                    ) as total
                    FROM user_abonnements ua
                    JOIN abonnements a ON ua.abonnement_id = a.id
                    WHERE 1=1 $whereClause";
        } else {
            // Sinon, utiliser les valeurs par défaut
            $sql = "SELECT SUM(
                        CASE 
                            WHEN a.duree = 'hebdomadaire' THEN 19.99
                            WHEN a.duree = 'mensuel' THEN 49.99
                            WHEN a.duree = 'annuel' THEN 159
                            ELSE 0
                        END
                    ) as total
                    FROM user_abonnements ua
                    JOIN abonnements a ON ua.abonnement_id = a.id
                    WHERE 1=1 $whereClause";
        }

        $result = $this->db->findOne($sql);
        return $result && $result['total'] ? $result['total'] : 0;
    }
}
