<?php

require_once BACKEND_PATH . '/helpers/place_type_helper.php';

// Contrôleur de base pour centraliser la logique commune
abstract class BaseController
{
    public function __construct()
    {
        $this->updateReservationStatuses();
    }

    // Met à jour les réservations expirées (limité à 1x/5min)
    private function updateReservationStatuses()
    {
        try {
            $lastUpdate = $_SESSION['last_status_update'] ?? 0;
            $now = time();

            if ($now - $lastUpdate > 300) {
                require_once BACKEND_PATH . '/models/ReservationModel.php';
                $reservationModel = new ReservationModel();
                $reservationModel->updateExpiredReservations();
                $_SESSION['last_status_update'] = $now;
            }
        } catch (Exception $e) {
            error_log("Erreur mise à jour statuts réservations: " . $e->getMessage());
        }
    }

    // Rend une vue avec le layout approprié
    protected function renderView($view, $data = [], $layout = 'default')
    {
        // Ajoute les notifications si utilisateur connecté
        if ($layout !== 'guest' && isset($_SESSION['user'])) {
            $data = $this->addUserNotifications($data);
        }

        extract($data);

        // Détermine les templates selon le layout
        [$headerPath, $footerPath] = $this->getLayoutPaths($layout);

        if (file_exists($headerPath)) {
            include $headerPath;
        }

        $viewPath = FRONTEND_PATH . '/views/' . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new Exception("La vue {$view} n'existe pas");
        }

        if (file_exists($footerPath)) {
            include $footerPath;
        }
    }

    // Retourne les chemins des templates selon le layout
    private function getLayoutPaths($layout)
    {
        switch ($layout) {
            case 'admin':
                return [
                    FRONTEND_PATH . '/views/admin/templates/header.php',
                    FRONTEND_PATH . '/views/admin/templates/footer.php'
                ];
            default:
                return [
                    FRONTEND_PATH . '/views/templates/header.php',
                    FRONTEND_PATH . '/views/templates/footer.php'
                ];
        }
    }

    // Vérifie si l'utilisateur est connecté
    protected function isAuthenticated()
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }

    // Vérifie si l'utilisateur est administrateur
    protected function isAdmin()
    {
        return $this->isAuthenticated() && $_SESSION['user']['role'] === 'admin';
    }

    // Force l'accès admin ou redirige
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

    // Force l'authentification ou redirige vers login
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

    // Redirige avec message de succès
    protected function redirectWithSuccess($url, $message)
    {
        $_SESSION['success'] = $message;
        $this->redirect($url);
    }

    // Redirige avec message d'erreur
    protected function redirectWithError($url, $message)
    {
        $_SESSION['error'] = $message;
        $this->redirect($url);
    }

    // Effectue une redirection en normalisant l'URL
    protected function redirect($url)
    {
        // Utilise l'URL telle quelle si complète ou déjà normalisée
        if (preg_match('/^https?:\/\//', $url) || strpos($url, BASE_URL) === 0) {
            header('Location: ' . $url);
        } else {
            $normalizedUrl = rtrim(BASE_URL, '/') . '/' . ltrim($url, '/');
            header('Location: ' . $normalizedUrl);
        }
        exit;
    }

    // Retourne une réponse JSON
    protected function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Retourne une erreur JSON
    protected function jsonError($message, $statusCode = 400)
    {
        $this->jsonResponse([
            'success' => false,
            'error' => $message
        ], $statusCode);
    }

    // Valide les champs requis avec redirection si erreur
    protected function validateRequiredFields($fields, $data = null)
    {
        $result = $this->validateFields($fields, $data ?? $_POST);

        if ($result['missing']) {
            $this->redirectWithError(
                $_SERVER['HTTP_REFERER'] ?? BASE_URL,
                'Champs obligatoires manquants : ' . implode(', ', $result['missing'])
            );
            return false;
        }

        return $result['validated'];
    }

    // Valide les champs requis pour AJAX sans redirection
    protected function validateRequiredFieldsAjax($fields, $data = null)
    {
        $result = $this->validateFields($fields, $data ?? $_POST);
        return $result['missing'] ? false : $result['validated'];
    }

    // Méthode commune de validation des champs
    private function validateFields($fields, $data)
    {
        $validated = [];
        $missing = [];

        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $missing[] = $field;
            } else {
                $validated[$field] = trim($data[$field]);
            }
        }

        return ['validated' => $validated, 'missing' => $missing];
    }

    // Obtient l'URL actuelle complète
    protected function getCurrentUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    // Affiche la page d'accès refusé
    protected function accessDenied()
    {
        http_response_code(403);
        $this->renderView('error/403', [
            'title' => 'Accès refusé - ' . APP_NAME,
            'message' => 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.'
        ]);
        exit;
    }

    // Ajoute les notifications utilisateur aux données de vue
    private function addUserNotifications($data)
    {
        if (isset($_SESSION['user'])) {
            try {
                $userModel = new UserModel();
                $data['notifications'] = $userModel->getUserNotifications($_SESSION['user']['id']);
                $data['unread_notifications'] = $userModel->countUnreadNotifications($_SESSION['user']['id']);
            } catch (Exception) {
                $data['notifications'] = [];
                $data['unread_notifications'] = 0;
            }
        }

        return $data;
    }

    // Nettoie et sécurise une entrée utilisateur
    protected function sanitizeInput($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    // Valide une adresse email
    protected function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Génère un token sécurisé
    protected function generateSecureToken($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }

    // Log une action utilisateur si le modèle existe
    protected function logUserAction($action, $details = '')
    {
        if (isset($_SESSION['user']) && class_exists('LogModel')) {
            try {
                $logModel = new LogModel();
                $logModel->addLog($_SESSION['user']['id'], $action, $details);
            } catch (Exception $e) {
                error_log("Erreur lors du logging : " . $e->getMessage());
            }
        }
    }

    // Vérifie si la requête est AJAX
    protected function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    // Définit le menu actif pour la sidebar admin
    protected function setActiveMenu($menu)
    {
        return [
            'activeMenu' => $menu,
            'active_page' => $menu
        ];
    }

    // ====== MÉTHODES DE VALIDATION COMMUNES ======

    // Valide un email et un mot de passe avec confirmation
    protected function validateEmailAndPassword($email, $password, $confirmPassword, $redirectUrl = null)
    {
        if (!$this->isValidEmail($email)) {
            $error = 'L\'adresse email est invalide.';
            if ($redirectUrl) {
                $this->redirectWithError($redirectUrl, $error);
                return false;
            }
            return ['valid' => false, 'error' => $error];
        }

        if ($password !== $confirmPassword) {
            $error = 'Les mots de passe ne correspondent pas.';
            if ($redirectUrl) {
                $this->redirectWithError($redirectUrl, $error);
                return false;
            }
            return ['valid' => false, 'error' => $error];
        }

        if (strlen($password) < 6) {
            $error = 'Le mot de passe doit contenir au moins 6 caractères.';
            if ($redirectUrl) {
                $this->redirectWithError($redirectUrl, $error);
                return false;
            }
            return ['valid' => false, 'error' => $error];
        }

        return ['valid' => true];
    }

    // Valide l'unicité d'un email
    protected function validateEmailUniqueness($email, $excludeUserId = null, $redirectUrl = null)
    {
        $userModel = new UserModel();
        $existingUser = $userModel->getUserByEmail($email);

        if ($existingUser && (!$excludeUserId || $existingUser['id'] != $excludeUserId)) {
            $error = 'Un utilisateur avec cette adresse email existe déjà.';
            if ($redirectUrl) {
                $this->redirectWithError($redirectUrl, $error);
                return false;
            }
            return ['valid' => false, 'error' => $error];
        }

        return ['valid' => true];
    }

    // Valide une méthode POST
    protected function validatePostRequest($redirectUrl = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($redirectUrl) {
                $this->redirect($redirectUrl);
            }
            return false;
        }
        return true;
    }

    // Valide un ID numérique
    protected function validateNumericId($id, $fieldName = 'ID', $redirectUrl = null)
    {
        $numericId = intval($id);
        if (!$numericId) {
            $error = $fieldName . ' invalide.';
            if ($redirectUrl) {
                $this->redirectWithError($redirectUrl, $error);
                return false;
            }
            return ['valid' => false, 'error' => $error];
        }
        return ['valid' => true, 'id' => $numericId];
    }

    /* Gère les réponses AJAX vs redirections normales */
    protected function handleResponse($isAjax, $successData, $errorMessage, $redirectUrl = null)
    {
        if ($isAjax) {
            if ($successData) {
                $this->jsonResponse($successData);
            } else {
                $this->jsonError($errorMessage);
            }
        } else {
            if ($successData) {
                $this->redirect($redirectUrl ?? $successData['redirect_url'] ?? BASE_URL);
            } else {
                $this->redirectWithError($redirectUrl ?? BASE_URL, $errorMessage);
            }
        }
    }

    /* Prépare les données de paiement pour la session */
    protected function preparePaymentSession($reservationId, $montant, $duree)
    {
        $_SESSION['immediate_payment'] = [
            'reservation_id' => $reservationId,
            'montant' => $montant,
            'duree' => ceil($duree),
            'needs_payment' => true
        ];
    }

    /* Crée une réponse de réservation standardisée */
    protected function createReservationResponse($reservation, $requiresPayment = false)
    {
        $response = [
            'success' => true,
            'reservation_id' => $reservation['id'],
            'montant' => number_format($reservation['montant_total'], 2),
            'code_sortie' => $reservation['code_sortie'] ?? null
        ];

        if ($requiresPayment) {
            $response['requires_payment'] = true;
            $response['redirect_url'] = buildUrl('reservation/payment/' . $reservation['id']);
            $response['message'] = 'Réservation terminée. Paiement requis.';
        } else {
            $response['redirect_url'] = buildUrl('reservation/confirmation/' . $reservation['id']);
        }

        return $response;
    }
}
