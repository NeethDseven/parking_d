<?php
class LogModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Ajoute un log d'action
     * @param int $userId ID de l'utilisateur (utiliser 1 pour les invités)
     * @param string $action Type d'action
     * @param string $description Description de l'action
     * @return int|bool ID du log créé ou false en cas d'échec
     */
    public function addLog($userId, $action, $description)
    {
        // Vérifier si l'utilisateur existe
        if (!$userId) {
            // Utiliser l'ID utilisateur système par défaut
            $userId = 1; // ID de l'utilisateur système/admin
        }

        // Vérifier que l'utilisateur existe
        $sql = "SELECT id FROM users WHERE id = :id LIMIT 1";
        $userExists = $this->db->findOne($sql, ['id' => $userId]);

        if (!$userExists) {
            // Si l'utilisateur n'existe pas, utiliser l'utilisateur "guest@parkme.in" ou créer un utilisateur système
            $guestSql = "SELECT id FROM users WHERE email = 'guest@parkme.in' OR role = 'admin' LIMIT 1";
            $guestUser = $this->db->findOne($guestSql);

            if ($guestUser) {
                $userId = $guestUser['id'];
            } else {
                // Créer un utilisateur système si nécessaire
                $systemUserData = [
                    'email' => 'system@parkme.in',
                    'password' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
                    'nom' => 'Système',
                    'prenom' => 'ParkMe',
                    'role' => 'admin',
                    'notifications_active' => 0
                ];

                $userId = $this->db->insert('users', $systemUserData);
                if (!$userId) {
                    // En cas d'échec, ne pas créer de log
                    return false;
                }
            }
        }

        $data = [
            'user_id' => $userId,
            'action' => $action,
            'description' => $description
        ];

        return $this->db->insert('logs', $data);
    }

    public function getLogsByUser($userId, $limit = 50)
    {
        $sql = "SELECT id, action, description, created_at 
                FROM logs 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit";

        // Pour utiliser LIMIT avec des paramètres préparés, on doit spécifier le type
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAllLogs($limit = 100)
    {
        $sql = "SELECT l.id, l.action, l.description, l.created_at, 
                       u.nom, u.prenom, u.email 
                FROM logs l 
                LEFT JOIN users u ON l.user_id = u.id 
                ORDER BY l.created_at DESC 
                LIMIT :limit";

        // Pour utiliser LIMIT avec des paramètres préparés, on doit spécifier le type
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Anonymise les logs d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return bool Succès de l'opération
     */
    public function anonymizeUserLogs($userId)
    {
        try {
            // Mettre à jour les logs pour remplacer l'ID de l'utilisateur par 0 (anonyme)
            $data = ['user_id' => 0];
            $where = 'user_id = :user_id';
            $params = ['user_id' => $userId];

            return $this->db->update('logs', $data, $where, $params);
        } catch (Exception $e) {
            error_log("Erreur lors de l'anonymisation des logs pour l'utilisateur $userId: " . $e->getMessage());
            return false;
        }
    }
}
