<?php

class NotificationController extends BaseController
{
    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead()
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Non autorisé']);
            return;
        }

        // Vérifier que c'est une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
            return;
        }

        $notificationId = $_POST['notification_id'] ?? null;

        if (!$notificationId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID de notification manquant']);
            return;
        }

        try {
            // Mettre à jour la notification pour la marquer comme lue
            $sql = "UPDATE notifications SET lu = 1 WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$notificationId, $_SESSION['user']['id']]);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Notification marquée comme lue']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Impossible de marquer la notification']);
            }
        } catch (Exception $e) {
            error_log('Erreur lors du marquage de notification: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erreur serveur']);
        }
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Non autorisé']);
            return;
        }

        try {
            $sql = "UPDATE notifications SET lu = 1 WHERE user_id = ? AND lu = 0";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$_SESSION['user']['id']]);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Toutes les notifications marquées comme lues']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Erreur lors du marquage']);
            }
        } catch (Exception $e) {
            error_log('Erreur lors du marquage de toutes les notifications: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erreur serveur']);
        }
    }
}
