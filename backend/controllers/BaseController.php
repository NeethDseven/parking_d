<?php

// Inclure les helpers nécessaires
require_once BACKEND_PATH . '/helpers/place_type_helper.php';

/**
 * Contrôleur de base pour toutes les fonctionnalités communes
 * Centralise la logique répétitive entre les contrôleurs
 */
abstract class BaseController
{
    /**
     * Constructeur de la classe de base
     */
    public function __construct()
    {
        // Initialisation commune à tous les contrôleurs
        $this->updateReservationStatuses();
    }

    /**
     * Met à jour automatiquement les statuts des réservations expirées
     * Appelé à chaque requête pour maintenir les données à jour
     */
    private function updateReservationStatuses()
    {
        try {
            // Éviter les appels trop fréquents avec un système de cache
            $lastUpdate = $_SESSION['last_status_update'] ?? 0;
            $now = time();

            // Mettre à jour seulement toutes les 5 minutes max
            if ($now - $lastUpdate > 300) {
                require_once BACKEND_PATH . '/models/ReservationModel.php';
                $reservationModel = new ReservationModel();
                $reservationModel->updateExpiredReservations();
                $_SESSION['last_status_update'] = $now;
            }
        } catch (Exception $e) {
            // Enregistrer l'erreur mais ne pas interrompre l'application
            error_log("Erreur mise à jour statuts réservations: " . $e->getMessage());
        }
    }

    /**
     * Rend une vue avec les données spécifiées
     * @param string $view Chemin de la vue (ex: 'home/index')
     * @param array $data Données à passer à la vue
     * @param string $layout Layout à utiliser ('default', 'admin', 'guest')
     */
    protected function renderView($view, $data = [], $layout = 'default')
    {
        // Ajouter les notifications pour l'utilisateur connecté si nécessaire
        if ($layout !== 'guest' && isset($_SESSION['user'])) {
            $data = $this->addUserNotifications($data);
        }

        // Extraire les données pour les rendre disponibles dans la vue
        extract($data);

        // Déterminer les chemins des templates selon le layout
        switch ($layout) {
            case 'admin':
                $headerPath = FRONTEND_PATH . '/views/admin/templates/header.php';
                $footerPath = FRONTEND_PATH . '/views/admin/templates/footer.php';
                break;
            case 'guest':
                $headerPath = FRONTEND_PATH . '/views/templates/header.php';
                $footerPath = FRONTEND_PATH . '/views/templates/footer.php';
                break;
            default:
                $headerPath = FRONTEND_PATH . '/views/templates/header.php';
                $footerPath = FRONTEND_PATH . '/views/templates/footer.php';
                break;
        }

        // Charger le header
        if (file_exists($headerPath)) {
            include $headerPath;
        }

        // Charger la vue principale
        $viewPath = FRONTEND_PATH . '/views/' . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new Exception("La vue {$view} n'existe pas");
        }

        // Charger le footer
        if (file_exists($footerPath)) {
            include $footerPath;
        }
    }

    /**
     * Vérifie si l'utilisateur est connecté
     * @return bool
     */
    protected function isAuthenticated()
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }

    /**
     * Vérifie si l'utilisateur est administrateur
     * @return bool
     */
    protected function isAdmin()
    {
        return $this->isAuthenticated() && $_SESSION['user']['role'] === 'admin';
    }

    /**
     * Vérifie l'accès administrateur et redirige si nécessaire
     * @param string $redirectUrl URL de redirection si non autorisé
     * @throws Exception Si l'accès est refusé
     */
    protected function requireAdmin($redirectUrl = null)
    {
        if (!$this->isAdmin()) {
            if ($redirectUrl) {
                $this->redirect($redirectUrl);
            } else {
                $this->accessDenied();
            }
        }
    }

    /**
     * Vérifie l'authentification et redirige si nécessaire
     * @param string $redirectUrl URL de redirection si non connecté
     */
    protected function requireAuth($redirectUrl = null)
    {
        if (!$this->isAuthenticated()) {
            if ($redirectUrl) {
                $_SESSION['redirect_after_login'] = $this->getCurrentUrl();
                $this->redirect($redirectUrl);
            } else {
                $this->redirect(BASE_URL . 'auth/login');
            }
        }
    }

    /**
     * Redirige vers une URL avec un message de succès
     * @param string $url URL de destination
     * @param string $message Message de succès
     */
    protected function redirectWithSuccess($url, $message)
    {
        $_SESSION['success'] = $message;
        $this->redirect($url);
    }

    /**
     * Redirige vers une URL avec un message d'erreur
     * @param string $url URL de destination
     * @param string $message Message d'erreur
     */
    protected function redirectWithError($url, $message)
    {
        $_SESSION['error'] = $message;
        $this->redirect($url);
    }
    /**
     * Effectue une redirection
     * @param string $url URL de destination
     */
    protected function redirect($url)
    {
        // Si l'URL contient déjà le domaine ou commence par BASE_URL, l'utiliser telle quelle
        if (preg_match('/^https?:\/\//', $url) || strpos($url, BASE_URL) === 0) {
            header('Location: ' . $url);
        } else {
            // Sinon, normaliser l'URL avec BASE_URL
            $normalizedUrl = rtrim(BASE_URL, '/') . '/' . ltrim($url, '/');
            header('Location: ' . $normalizedUrl);
        }
        exit;
    }

    /**
     * Retourne une réponse JSON
     * @param array $data Données à retourner
     * @param int $statusCode Code de statut HTTP
     */
    protected function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Retourne une erreur JSON
     * @param string $message Message d'erreur
     * @param int $statusCode Code de statut HTTP
     */
    protected function jsonError($message, $statusCode = 400)
    {
        $this->jsonResponse([
            'success' => false,
            'error' => $message
        ], $statusCode);
    }

    /**
     * Valide les champs requis dans une requête
     * @param array $fields Champs requis
     * @param array $data Données à valider (par défaut $_POST)
     * @return array|false Données validées ou false si erreur
     */
    protected function validateRequiredFields($fields, $data = null)
    {
        if ($data === null) {
            $data = $_POST;
        }

        $validated = [];
        $missing = [];

        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $missing[] = $field;
            } else {
                $validated[$field] = trim($data[$field]);
            }
        }

        if (!empty($missing)) {
            $this->redirectWithError(
                $_SERVER['HTTP_REFERER'] ?? BASE_URL,
                'Champs obligatoires manquants : ' . implode(', ', $missing)
            );
            return false;
        }

        return $validated;
    }

    /**
     * Valide les champs requis sans redirection (pour les requêtes AJAX)
     * @param array $fields Champs requis
     * @param array|null $data Données à valider (par défaut $_POST)
     * @return array|false Données validées ou false si erreur
     */
    protected function validateRequiredFieldsAjax($fields, $data = null)
    {
        if ($data === null) {
            $data = $_POST;
        }

        $validated = [];
        $missing = [];

        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $missing[] = $field;
            } else {
                $validated[$field] = trim($data[$field]);
            }
        }

        if (!empty($missing)) {
            return false;
        }

        return $validated;
    }

    /**
     * Obtient l'URL actuelle
     * @return string
     */
    protected function getCurrentUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Gère l'accès refusé
     */
    protected function accessDenied()
    {
        http_response_code(403);
        $this->renderView('error/403', [
            'title' => 'Accès refusé - ' . APP_NAME,
            'message' => 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.'
        ]);
        exit;
    }

    /**
     * Ajoute les notifications utilisateur aux données de la vue
     * @param array $data Données existantes
     * @return array Données avec notifications
     */
    private function addUserNotifications($data)
    {
        if (isset($_SESSION['user'])) {
            try {
                $userModel = new UserModel();
                $data['notifications'] = $userModel->getUserNotifications($_SESSION['user']['id']);
                $data['unread_notifications'] = $userModel->countUnreadNotifications($_SESSION['user']['id']);
            } catch (Exception $e) {
                // En cas d'erreur, continuer sans notifications
                $data['notifications'] = [];
                $data['unread_notifications'] = 0;
            }
        }

        return $data;
    }

    /**
     * Nettoie et sécurise une entrée utilisateur
     * @param string $input Entrée à nettoyer
     * @return string Entrée nettoyée
     */
    protected function sanitizeInput($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Valide une adresse email
     * @param string $email Email à valider
     * @return bool
     */
    protected function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Génère un token sécurisé
     * @param int $length Longueur du token
     * @return string Token généré
     */
    protected function generateSecureToken($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Log une action utilisateur
     * @param string $action Action effectuée
     * @param string $details Détails de l'action
     */
    protected function logUserAction($action, $details = '')
    {
        if (isset($_SESSION['user']) && class_exists('LogModel')) {
            try {
                $logModel = new LogModel();
                $logModel->addLog($_SESSION['user']['id'], $action, $details);
            } catch (Exception $e) {
                // En cas d'erreur, continuer silencieusement
                error_log("Erreur lors du logging : " . $e->getMessage());
            }
        }
    }

    /**
     * Vérifie si la requête est AJAX
     * @return bool
     */
    protected function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**     * Définit le menu actif pour la sidebar admin
     * @param string $menu Nom du menu actif
     * @return array Données avec le menu actif et la page active
     */
    protected function setActiveMenu($menu)
    {
        return [
            'activeMenu' => $menu,
            'active_page' => $menu  // Pour compatibilité avec l'attribut data-page
        ];
    }
}
