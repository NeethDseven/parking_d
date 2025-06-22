<?php

/**
 * Modèle pour la gestion des messages de contact
 */
class ContactModel extends BaseModel
{
    protected $tableName = 'contact_messages';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Crée un nouveau message de contact
     */
    public function createMessage($nom, $email, $sujet, $message)
    {
        $data = [
            'nom' => trim($nom),
            'email' => trim($email),
            'sujet' => $sujet,
            'message' => trim($message),
            'status' => 'nouveau'
        ];

        return $this->db->insert($this->tableName, $data);
    }

    /**
     * Récupère tous les messages avec pagination
     */
    public function getMessagesPaginated($offset, $limit, $statusFilter = null)
    {
        $sql = "SELECT cm.*, u.nom as admin_nom, u.prenom as admin_prenom
                FROM {$this->tableName} cm
                LEFT JOIN users u ON cm.admin_user_id = u.id";
        
        $params = [];
        
        if ($statusFilter && $statusFilter !== 'all') {
            $sql .= " WHERE cm.status = :status";
            $params['status'] = $statusFilter;
        }
        
        $sql .= " ORDER BY cm.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte le nombre total de messages
     */
    public function countMessages($statusFilter = null)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->tableName}";
        $params = [];
        
        if ($statusFilter && $statusFilter !== 'all') {
            $sql .= " WHERE status = :status";
            $params['status'] = $statusFilter;
        }
        
        $result = $this->db->findOne($sql, $params);
        return $result['count'];
    }

    /**
     * Met à jour le statut d'un message
     */
    public function updateStatus($id, $status, $adminUserId = null)
    {
        $data = ['status' => $status];
        
        if ($adminUserId) {
            $data['admin_user_id'] = $adminUserId;
        }
        
        return $this->db->update($this->tableName, $data, 'id = :id', ['id' => $id]);
    }

    /**
     * Ajoute une réponse d'admin à un message
     */
    public function addAdminResponse($id, $response, $adminUserId)
    {
        $data = [
            'admin_response' => trim($response),
            'admin_user_id' => $adminUserId,
            'responded_at' => date('Y-m-d H:i:s'),
            'status' => 'traite'
        ];
        
        return $this->db->update($this->tableName, $data, 'id = :id', ['id' => $id]);
    }

    /**
     * Récupère les statistiques des messages
     */
    public function getStats()
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'nouveau' THEN 1 ELSE 0 END) as nouveaux,
                    SUM(CASE WHEN status = 'lu' THEN 1 ELSE 0 END) as lus,
                    SUM(CASE WHEN status = 'traite' THEN 1 ELSE 0 END) as traites,
                    SUM(CASE WHEN status = 'archive' THEN 1 ELSE 0 END) as archives
                FROM {$this->tableName}";
        
        return $this->db->findOne($sql);
    }

    /**
     * Récupère les messages par sujet
     */
    public function getMessagesBySubject()
    {
        $sql = "SELECT sujet, COUNT(*) as count 
                FROM {$this->tableName} 
                GROUP BY sujet 
                ORDER BY count DESC";
        
        return $this->db->findAll($sql);
    }

    /**
     * Marque un message comme lu
     */
    public function markAsRead($id, $adminUserId)
    {
        $message = $this->getById($id);
        if ($message && $message['status'] === 'nouveau') {
            return $this->updateStatus($id, 'lu', $adminUserId);
        }
        return true;
    }

    /**
     * Archive un message
     */
    public function archiveMessage($id)
    {
        return $this->updateStatus($id, 'archive');
    }

    /**
     * Supprime définitivement un message
     */
    public function deleteMessage($id)
    {
        return $this->db->delete($this->tableName, 'id = :id', ['id' => $id]);
    }

    /**
     * Récupère les messages récents (pour le dashboard)
     */
    public function getRecentMessages($limit = 5)
    {
        $sql = "SELECT id, nom, email, sujet, LEFT(message, 100) as message_preview,
                       status, created_at
                FROM {$this->tableName}
                ORDER BY created_at DESC
                LIMIT :limit";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les messages d'un utilisateur par email
     */
    public function getMessagesByEmail($email)
    {
        $sql = "SELECT cm.*, u.nom as admin_nom, u.prenom as admin_prenom
                FROM {$this->tableName} cm
                LEFT JOIN users u ON cm.admin_user_id = u.id
                WHERE cm.email = :email
                ORDER BY cm.created_at DESC";

        return $this->db->findAll($sql, ['email' => $email]);
    }

    /**
     * Récupère tous les messages d'un utilisateur connecté (par user_id ET par email)
     * Cela inclut les messages envoyés avant la connexion avec le même email
     */
    public function getMessagesByUser($userId, $userEmail)
    {
        $sql = "SELECT cm.*, u.nom as admin_nom, u.prenom as admin_prenom
                FROM {$this->tableName} cm
                LEFT JOIN users u ON cm.admin_user_id = u.id
                WHERE cm.user_id = :user_id OR cm.email = :email
                ORDER BY cm.created_at DESC";

        return $this->db->findAll($sql, [
            'user_id' => $userId,
            'email' => $userEmail
        ]);
    }

    /**
     * Récupère un message spécifique par ID et email (pour sécurité)
     */
    public function getMessageByIdAndEmail($id, $email)
    {
        $sql = "SELECT cm.*, u.nom as admin_nom, u.prenom as admin_prenom
                FROM {$this->tableName} cm
                LEFT JOIN users u ON cm.admin_user_id = u.id
                WHERE cm.id = :id AND cm.email = :email";

        return $this->db->findOne($sql, ['id' => $id, 'email' => $email]);
    }

    /**
     * Compte les messages avec réponse pour un email donné
     */
    public function countMessagesWithResponseByEmail($email)
    {
        $sql = "SELECT COUNT(*) as count
                FROM {$this->tableName}
                WHERE email = :email AND admin_response IS NOT NULL";

        $result = $this->db->findOne($sql, ['email' => $email]);
        return $result['count'];
    }

    /**
     * Compte les messages avec réponse pour un utilisateur connecté (user_id ET email)
     */
    public function countMessagesWithResponseByUser($userId, $userEmail)
    {
        $sql = "SELECT COUNT(*) as count
                FROM {$this->tableName}
                WHERE (user_id = :user_id OR email = :email)
                AND admin_response IS NOT NULL";

        $result = $this->db->findOne($sql, [
            'user_id' => $userId,
            'email' => $userEmail
        ]);
        return $result['count'];
    }
}
