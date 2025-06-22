<?php
class UserModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    public function authenticate($email, $password)
    {
        // Vérifier d'abord si la colonne is_subscribed existe
        $hasIsSubscribed = $this->columnExists('users', 'is_subscribed');

        if ($hasIsSubscribed) {
            $sql = "SELECT id, email, password, nom, prenom, role, telephone, status, is_subscribed 
                    FROM users 
                    WHERE email = :email";
        } else {
            $sql = "SELECT id, email, password, nom, prenom, role, telephone, status 
                    FROM users 
                    WHERE email = :email";
        }

        $user = $this->db->findOne($sql, ['email' => $email]);

        if ($user && password_verify($password, $user['password'])) {
            // Vérifier si le compte est actif
            if (isset($user['status']) && $user['status'] === 'inactif') {
                // Compte désactivé
                return false;
            }

            // Ajouter la valeur par défaut pour is_subscribed si elle n'existe pas
            if (!isset($user['is_subscribed'])) {
                $user['is_subscribed'] = 0;
            }

            // Ne pas inclure le mot de passe dans la session
            unset($user['password']);
            return $user;
        }

        return false;
    }
    /**
     * Vérifie si une colonne existe dans une table
     */
    private function columnExists($table, $column)
    {
        // Important: pour SHOW COLUMNS, on ne peut pas utiliser de paramètres préparés avec LIKE
        // Donc on doit l'échapper manuellement
        $escapedColumn = str_replace('`', '``', $column); // Échapper les backticks

        $sql = "SHOW COLUMNS FROM `{$table}` LIKE '{$escapedColumn}'";
        try {
            // Exécution directe sans préparation de la requête
            $stmt = $this->db->getConnection()->query($sql);
            $result = $stmt ? $stmt->fetch() : false;
            return $result ? true : false;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification de la colonne: " . $e->getMessage());
            return false;
        }
    }

    public function createUser($nom, $prenom, $email, $telephone, $password)
    {
        try {
            // Vérifier si l'email existe déjà
            if ($this->emailExists($email)) {
                return false;
            }

            // Connexion directe
            $connection = $this->db->getConnection();

            // SOLUTION DÉFINITIVE : Toujours générer un ID unique manuellement

            // 1. Supprimer tous les utilisateurs avec ID 0
            $connection->exec("DELETE FROM users WHERE id = 0");

            // 2. Obtenir le prochain ID disponible
            $stmt = $connection->query("SELECT COALESCE(MAX(id), 0) + 1 as next_id FROM users WHERE id > 0");
            $result = $stmt->fetch();
            $nextId = $result['next_id'];

            // 3. S'assurer que l'ID est au minimum 1
            if ($nextId < 1) {
                $nextId = 1;
            }

            // 4. Vérifier que cet ID n'existe pas déjà (sécurité supplémentaire)
            $stmt = $connection->prepare("SELECT COUNT(*) as count FROM users WHERE id = ?");
            $stmt->execute([$nextId]);
            $exists = $stmt->fetch();

            // Si l'ID existe déjà, trouver le premier ID libre
            $attempts = 0;
            while ($exists['count'] > 0 && $attempts < 100) {
                $nextId++;
                $attempts++;
                $stmt->execute([$nextId]);
                $exists = $stmt->fetch();
            }

            if ($attempts >= 100) {
                return false;
            }

            // 5. Insérer avec l'ID spécifique (sans created_at pour éviter les erreurs)
            $sql = "INSERT INTO users (id, nom, prenom, email, telephone, password, role, notifications_active, status)
                    VALUES (?, ?, ?, ?, ?, ?, 'user', 1, 'actif')";

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $params = [
                $nextId,
                $nom,
                $prenom,
                $email,
                $telephone,
                $hashedPassword
            ];

            $stmt = $connection->prepare($sql);
            $result = $stmt->execute($params);

            if (!$result) {
                return false;
            }

            // 6. Mettre à jour l'AUTO_INCREMENT pour les prochaines insertions
            $connection->exec("ALTER TABLE users AUTO_INCREMENT = " . ($nextId + 1));

            // 7. Vérifier que l'utilisateur a bien été créé avec le bon ID
            $stmt = $connection->prepare("SELECT id FROM users WHERE email = ? AND id = ?");
            $stmt->execute([$email, $nextId]);
            $createdUser = $stmt->fetch();

            if ($createdUser && $createdUser['id'] == $nextId) {
                return $nextId;
            } else {
                return false;
            }

        } catch (Exception $e) {
            error_log("Erreur dans createUser: " . $e->getMessage());
            return false;
        }
    }

    public function updateUser($id, $data)
    {
        // Si le mot de passe est inclus, le hasher
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            // Ne pas mettre à jour le mot de passe s'il est vide
            unset($data['password']);
        }

        return $this->db->update('users', $data, 'id = :id', ['id' => $id]);
    }

    public function emailExists($email)
    {
        try {
            $sql = "SELECT id FROM users WHERE email = :email";
            $user = $this->db->findOne($sql, ['email' => $email]);

            return $user !== false && $user !== null;
        } catch (Exception $e) {
            error_log("Erreur dans emailExists: " . $e->getMessage());
            return false; // En cas d'erreur, considérer que l'email n'existe pas
        }
    }
    public function getUserById($id)
    {
        $hasIsSubscribed = $this->columnExists('users', 'is_subscribed');

        if ($hasIsSubscribed) {
            $sql = "SELECT id, email, telephone, nom, prenom, role, notifications_active, created_at, status, is_subscribed
                    FROM users 
                    WHERE id = :id";
        } else {
            $sql = "SELECT id, email, telephone, nom, prenom, role, notifications_active, created_at, status
                    FROM users 
                    WHERE id = :id";
        }

        $user = $this->db->findOne($sql, ['id' => $id]);

        if ($user && !isset($user['is_subscribed'])) {
            $user['is_subscribed'] = 0; // Par défaut, non abonné
        }

        return $user;
    }
    public function getUserReservations($userId)
    {
        $sql = "SELECT r.id, r.date_debut, r.date_fin, r.status, r.code_acces, r.code_sortie, r.montant_total,
                       p.numero as place_numero, p.type as place_type,
                       pa.id as paiement_id, pa.status as paiement_status
                FROM reservations r
                JOIN parking_spaces p ON r.place_id = p.id
                LEFT JOIN paiements pa ON r.id = pa.reservation_id
                WHERE r.user_id = :user_id
                ORDER BY r.date_debut DESC";

        return $this->db->findAll($sql, ['user_id' => $userId]);
    }

    public function getUserNotifications($userId)
    {
        $sql = "SELECT id, titre, message, type, lu, created_at 
                FROM notifications 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT 10";

        return $this->db->findAll($sql, ['user_id' => $userId]);
    }

    public function markNotificationAsRead($notificationId)
    {
        $data = ['lu' => 1];
        $where = "id = :id";
        $params = ['id' => $notificationId];

        return $this->db->update('notifications', $data, $where, $params);
    }

    public function markAllNotificationsAsRead($userId)
    {
        $data = ['lu' => 1];
        $where = "user_id = :user_id AND lu = 0";

        return $this->db->update('notifications', $data, $where, ['user_id' => $userId]);
    }

    public function countUnreadNotifications($userId)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM notifications 
                WHERE user_id = :user_id AND lu = 0";

        $result = $this->db->findOne($sql, ['user_id' => $userId]);
        return $result['count'];
    }

    public function createNotification($userId, $titre, $message, $type = 'system')
    {
        $data = [
            'user_id' => $userId,
            'titre' => $titre,
            'message' => $message,
            'type' => $type,
            'lu' => 0
        ];

        return $this->db->insert('notifications', $data);
    }

    /**
     * Récupère les utilisateurs avec pagination
     */
    public function getUsersPaginated($offset, $limit)
    {
        $sql = "SELECT id, email, telephone, nom, prenom, role, notifications_active, status, created_at 
                FROM users 
                ORDER BY id DESC 
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les utilisateurs avec filtres et tri
     */
    public function getFilteredUsers($role = null, $status = null, $sort = 'created_at_desc', $offset = 0, $limit = 10)
    {
        $conditions = [];
        $params = [];

        if ($role && $role !== '') {
            $conditions[] = "role = :role";
            $params['role'] = $role;
        }

        if ($status && $status !== '') {
            $conditions[] = "status = :status";
            $params['status'] = $status;
        }

        $whereClause = "";
        if (!empty($conditions)) {
            $whereClause = "WHERE " . implode(" AND ", $conditions);
        }

        // Gestion du tri
        $orderClause = "ORDER BY ";
        switch ($sort) {
            case 'created_at_asc':
                $orderClause .= "created_at ASC";
                break;
            case 'nom_asc':
                $orderClause .= "nom ASC, prenom ASC";
                break;
            case 'nom_desc':
                $orderClause .= "nom DESC, prenom DESC";
                break;
            case 'email_asc':
                $orderClause .= "email ASC";
                break;
            case 'created_at_desc':
            default:
                $orderClause .= "created_at DESC";
                break;
        }

        $sql = "SELECT * FROM users $whereClause $orderClause LIMIT :offset, :limit";

        $stmt = $this->db->getConnection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte le nombre d'utilisateurs avec filtres
     */
    public function countFilteredUsers($role = null, $status = null)
    {
        $conditions = [];
        $params = [];

        if ($role && $role !== '') {
            $conditions[] = "role = :role";
            $params['role'] = $role;
        }

        if ($status && $status !== '') {
            $conditions[] = "status = :status";
            $params['status'] = $status;
        }

        $whereClause = "";
        if (!empty($conditions)) {
            $whereClause = "WHERE " . implode(" AND ", $conditions);
        }

        $sql = "SELECT COUNT(*) as count FROM users $whereClause";

        $stmt = $this->db->getConnection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    /**
     * Compte le nombre total d'utilisateurs
     */
    public function countUsers()
    {
        $sql = "SELECT COUNT(*) as count FROM users";
        $result = $this->db->findOne($sql);
        return $result['count'];
    }

    /**
     * Compte les utilisateurs actifs du dernier mois (ceux qui ont fait une réservation)
     */
    public function countActiveUsersLastMonth()
    {
        $sql = "SELECT COUNT(DISTINCT user_id) as count 
                FROM reservations 
                WHERE user_id > 0 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";

        $result = $this->db->findOne($sql);
        return $result['count'];
    }

    /**
     * Compte les nouveaux utilisateurs du mois en cours
     */
    public function countNewUsersThisMonth()
    {
        $sql = "SELECT COUNT(*) as count 
                FROM users 
                WHERE created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')";

        $result = $this->db->findOne($sql);
        return $result['count'];
    }

    /**
     * Supprime un utilisateur
     */    public function deleteUser($id)
    {
        try {
            // Commencer une transaction pour assurer la cohérence des données
            $this->db->beginTransaction();

            // Vérifier d'abord si l'utilisateur a des réservations
            $sql = "SELECT COUNT(*) as count FROM reservations WHERE user_id = :id";
            $result = $this->db->findOne($sql, ['id' => $id]);

            if ($result['count'] > 0) {
                $this->db->rollBack();
                return false; // Ne pas supprimer un utilisateur avec des réservations
            }

            // Récupérer toutes les tables qui peuvent avoir une référence à l'utilisateur
            $tables = $this->getTablesWithForeignKeyToUsers();

            // Supprimer ou mettre à jour toutes les références à l'utilisateur dans chaque table
            foreach ($tables as $table => $column) {
                // Selon la table, soit supprimer les enregistrements, soit mettre à jour la référence
                if ($table === 'logs') {
                    // Pour les logs, nous voulons garder l'historique mais anonymiser
                    $this->db->update($table, [$column => 0], "$column = :id", ['id' => $id]);
                } else {
                    // Pour les autres tables, supprimer les enregistrements
                    $this->db->delete($table, "$column = :id", ['id' => $id]);
                }
            }

            // Supprimer spécifiquement les notifications (déjà inclus ci-dessus, mais pour être sûr)
            $this->db->delete('notifications', 'user_id = :id', ['id' => $id]);

            // Supprimer les alertes de disponibilité
            if ($this->tableExists('availability_alerts')) {
                $this->db->delete('availability_alerts', 'user_id = :id', ['id' => $id]);
            }

            // Supprimer explicitement toute autre référence
            $this->cleanupAdditionalReferences($id);

            // Forcer la suppression de l'utilisateur, même s'il reste des références
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->db->getConnection()->prepare($sql);
            $success = $stmt->execute(['id' => $id]);

            // Confirmer la transaction si tout s'est bien passé
            $this->db->commit();
            return $success;
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            // Log l'erreur pour le débogage
            error_log("Erreur lors de la suppression de l'utilisateur #$id: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());

            // Essayer une suppression directe sans transaction
            if ($this->forceDeleteUser($id)) {
                return true;
            }

            // En dernier recours, désactiver l'utilisateur au lieu de le supprimer
            return $this->deactivateUserInsteadOfDelete($id, $e);
        }
    }

    /**
     * Désactive un utilisateur quand la suppression échoue à cause de contraintes de clé étrangère
     * Cette méthode est utilisée comme fallback
     */
    private function deactivateUserInsteadOfDelete($id, $exception)
    {
        try {
            // Mettre à jour le statut utilisateur en 'inactif' et anonymiser les données sensibles
            $updatedData = [
                'status' => 'inactif',
                'email' => 'deleted_' . time() . '_' . $id . '@deleted.user',
                // Ajoutez d'autres champs à anonymiser si nécessaire
            ];

            $this->db->update('users', $updatedData, 'id = :id', ['id' => $id]);

            // Log l'événement
            error_log("L'utilisateur #$id a été désactivé au lieu d'être supprimé en raison de contraintes de clé étrangère.");

            return true; // La désactivation est considérée comme un succès
        } catch (Exception $e) {
            error_log("La désactivation de l'utilisateur #$id a également échoué: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Active ou désactive un compte utilisateur
     * @param int $id ID de l'utilisateur
     * @param string $status Statut (actif ou inactif)
     * @return bool Succès de l'opération
     */
    public function updateUserStatus($id, $status)
    {
        // Vérifier que le statut est valide
        if (!in_array($status, ['actif', 'inactif'])) {
            return false;
        }

        // Vérifier qu'on ne désactive pas le compte admin principal
        if ($id == 1 && $status == 'inactif') {
            return false;
        }

        return $this->db->update('users', ['status' => $status], 'id = :id', ['id' => $id]);
    }

    /**
     * Met à jour uniquement le statut d'un utilisateur
     * @param int $id ID de l'utilisateur
     * @param string $status Le nouveau statut (actif ou inactif)
     * @return bool Succès de l'opération
     */
    public function updateUserStatusOnly($id, $status)
    {
        // Vérifier que le statut est valide
        if (!in_array($status, ['actif', 'inactif'])) {
            return false;
        }

        // Vérifier qu'on ne désactive pas le compte admin principal
        if ($id == 1 && $status == 'inactif') {
            return false;
        }

        // Ne mettre à jour que le champ status
        return $this->db->update('users', ['status' => $status], 'id = :id', ['id' => $id]);
    }

    /**
     * Récupère toutes les tables qui ont une clé étrangère vers la table users
     * @return array Un tableau associatif de [nom_table => nom_colonne]
     */
    private function getTablesWithForeignKeyToUsers()
    {
        // Cette liste devrait être mise à jour manuellement si la structure de la base change
        return [
            'notifications' => 'user_id',
            'logs' => 'user_id',
            'availability_alerts' => 'user_id',
            'user_preferences' => 'user_id',
            'user_sessions' => 'user_id',
            'user_tokens' => 'user_id'
            // Ajoutez d'autres tables au besoin
        ];
    }

    /**
     * Vérifie si une table existe dans la base de données
     * @param string $tableName Nom de la table à vérifier
     * @return bool True si la table existe, false sinon
     */
    private function tableExists($tableName)
    {
        try {
            $sql = "SHOW TABLES LIKE :tableName";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute(['tableName' => $tableName]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Nettoie les références additionnelles à l'utilisateur
     * @param int $userId ID de l'utilisateur
     */
    private function cleanupAdditionalReferences($userId)
    {
        // Supprimer toutes les références additionnelles qui pourraient exister
        // Par exemple, vérifier les tables qui ont pu être ajoutées récemment

        // Si une table user_roles existe
        if ($this->tableExists('user_roles')) {
            $this->db->delete('user_roles', 'user_id = :id', ['id' => $userId]);
        }

        // Si une table user_permissions existe
        if ($this->tableExists('user_permissions')) {
            $this->db->delete('user_permissions', 'user_id = :id', ['id' => $userId]);
        }
    }

    /**
     * Force la suppression d'un utilisateur en ignorant les contraintes de clé étrangère
     * @param int $userId ID de l'utilisateur
     * @return bool True si l'utilisateur a été supprimé avec succès
     */
    private function forceDeleteUser($userId)
    {
        try {
            // Désactiver temporairement les contraintes de clé étrangère
            $this->db->getConnection()->exec('SET FOREIGN_KEY_CHECKS=0');

            // Supprimer l'utilisateur directement
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->db->getConnection()->prepare($sql);
            $success = $stmt->execute(['id' => $userId]);

            // Réactiver les contraintes de clé étrangère
            $this->db->getConnection()->exec('SET FOREIGN_KEY_CHECKS=1');

            return $success;
        } catch (Exception $e) {
            // Log l'erreur
            error_log("Force delete failed for user #$userId: " . $e->getMessage());

            // Réactiver les contraintes de clé étrangère en cas d'erreur
            try {
                $this->db->getConnection()->exec('SET FOREIGN_KEY_CHECKS=1');
            } catch (Exception $ex) {
                // Ignorer cette erreur
            }

            return false;
        }
    }

    /**
     * Supprime toutes les notifications d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return bool Succès de l'opération
     */
    public function deleteUserNotifications($userId)
    {
        try {
            return $this->db->delete('notifications', 'user_id = :id', ['id' => $userId]);
        } catch (Exception $e) {
            error_log("Erreur lors de la suppression des notifications pour l'utilisateur $userId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère un utilisateur par son email
     * @param string $email L'email de l'utilisateur
     * @return array|false Les données de l'utilisateur ou false si non trouvé
     */
    public function getUserByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        return $this->db->findOne($sql, ['email' => $email]);
    }

    /**
     * Ajoute un nouvel utilisateur
     * @param array $userData Les données de l'utilisateur à ajouter
     * @return int|false L'ID du nouvel utilisateur ou false en cas d'échec
     */
    public function addUser($userData)
    {
        return $this->db->insert('users', $userData);
    }

    /**
     * Supprime un utilisateur et toutes ses réservations (suppression forcée)
     */
    public function deleteUserWithReservations($id)
    {
        try {
            // Commencer une transaction pour assurer la cohérence des données
            $this->db->beginTransaction();

            // 1. Supprimer toutes les réservations de l'utilisateur et leurs dépendances
            $this->deleteAllUserReservations($id);

            // 2. Supprimer toutes les autres données liées à l'utilisateur
            $this->deleteAllUserRelatedData($id);

            // 3. Supprimer l'utilisateur lui-même
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->db->getConnection()->prepare($sql);
            $success = $stmt->execute(['id' => $id]);

            // Confirmer la transaction si tout s'est bien passé
            $this->db->commit();
            return $success;
            
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            // Log l'erreur pour le débogage
            error_log("Erreur lors de la suppression forcée de l'utilisateur #$id: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());

            return false;
        }
    }

    /**
     * Supprime toutes les réservations d'un utilisateur et leurs dépendances
     */
    private function deleteAllUserReservations($userId)
    {
        // Récupérer toutes les réservations de l'utilisateur
        $reservations = $this->getUserReservations($userId);

        foreach ($reservations as $reservation) {
            $reservationId = $reservation['id'];

            // Supprimer les paiements liés à cette réservation
            $this->db->delete('paiements', 'reservation_id = :id', ['id' => $reservationId]);

            // Supprimer les remboursements liés à cette réservation
            if ($this->tableExists('remboursements')) {
                $this->db->delete('remboursements', 'reservation_id = :id', ['id' => $reservationId]);
            }

            // Supprimer les factures liées à cette réservation
            if ($this->tableExists('factures')) {
                $this->db->delete('factures', 'reservation_id = :id', ['id' => $reservationId]);
            }

            // Libérer la place si elle était occupée par cette réservation
            if (in_array($reservation['status'], ['confirmée', 'en_cours', 'en_cours_immediat'])) {
                $this->db->update('parking_spaces', ['status' => 'libre'], 'id = :id', ['id' => $reservation['place_id']]);
            }
        }

        // Supprimer toutes les réservations de l'utilisateur
        $this->db->delete('reservations', 'user_id = :id', ['id' => $userId]);
    }

    /**
     * Supprime toutes les autres données liées à l'utilisateur
     */
    private function deleteAllUserRelatedData($userId)
    {
        // Supprimer les notifications
        $this->db->delete('notifications', 'user_id = :id', ['id' => $userId]);

        // Supprimer les alertes de disponibilité
        if ($this->tableExists('availability_alerts')) {
            $this->db->delete('availability_alerts', 'user_id = :id', ['id' => $userId]);
        }

        if ($this->tableExists('alertes_disponibilite')) {
            $this->db->delete('alertes_disponibilite', 'user_id = :id', ['id' => $userId]);
        }

        // Supprimer les abonnements utilisateur
        if ($this->tableExists('user_abonnements')) {
            $this->db->delete('user_abonnements', 'user_id = :id', ['id' => $userId]);
        }

        // Anonymiser les logs au lieu de les supprimer (pour garder l'historique)
        if ($this->tableExists('logs')) {
            $this->db->update('logs', ['user_id' => 0], 'user_id = :id', ['id' => $userId]);
        }
    }    /**
     * Compte les réservations d'un utilisateur par statut
     */
    public function countUserReservationsByStatus($userId)
    {
        $sql = "SELECT status, COUNT(*) as count 
                FROM reservations 
                WHERE user_id = :user_id 
                GROUP BY status";
        
        $results = $this->db->findAll($sql, ['user_id' => $userId]);
        
        $counts = [];
        foreach ($results as $result) {
            $counts[$result['status']] = (int)$result['count'];
        }
          return $counts;
    }
}