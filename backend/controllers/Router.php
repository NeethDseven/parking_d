<?php
/**
 * Routeur principal optimisé
 * Analyse l'URL et achemine les requêtes vers les contrôleurs appropriés
 */
class Router
{
    /**
     * Routes protégées nécessitant une authentification
     */    private $protectedRoutes = [
        'auth/profile',
        'reservation/reserve',
        'reservation/payment',
        'reservation/confirmation',
        'reservation/generateAccessCode',
        'subscription', 
        'subscription/subscribe',
        'subscription/cancel',
        'notification'
    ];
    
    /**
     * Routes réservées aux administrateurs
     */
    private $adminRoutes = [
        'admin',
        'admin/dashboard',
        'admin/users',
        'admin/places',
        'admin/reservations',
        'admin/tarifs',
        'admin/subscriptions',
        'subscription/admin'  // Ajout de la route subscription/admin
    ];
    
    /**
     * Traite la requête et route vers le bon contrôleur
     */
    public function route()
    {
        // Récupérer l'URL demandée
        $url = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
        
        // Identifier la route actuelle
        $currentRoute = strtolower($url);

        // Vérifier les droits d'accès aux routes protégées
        if ($this->isProtectedRoute($currentRoute) && !$this->isAuthenticated()) {
            $this->redirectToLogin();
            return;
        }
        
        // Vérifier les droits d'accès aux routes admin
        if ($this->isAdminRoute($currentRoute) && !$this->isAdmin()) {
            $this->accessDenied();
            return;
        }

        if (empty($url)) {
            // Page d'accueil par défaut
            $controller = new HomeController();
            $controller->index();
            return;
        }

        // Diviser l'URL en segments
        $segments = explode('/', $url);
        $controller_name = ucfirst($segments[0]) . 'Controller';
        $action = isset($segments[1]) ? $segments[1] : 'index';
        $params = array_slice($segments, 2);

        // Vérifier si le contrôleur existe et appeler son action
        if (class_exists($controller_name)) {
            $controller = new $controller_name();

            // Vérifier si l'action existe
            if (method_exists($controller, $action)) {
                // Appeler la méthode avec les paramètres
                call_user_func_array([$controller, $action], $params);
            } else {
                $this->notFound();
            }
        } else {
            $this->notFound();
        }
    }

    /**
     * Vérifie si l'utilisateur est authentifié
     */
    private function isAuthenticated()
    {
        return isset($_SESSION['user']);
    }
    
    /**
     * Vérifie si l'utilisateur a les droits administrateur
     */
    private function isAdmin()
    {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }
    
    /**
     * Vérifie si la route demandée est protégée
     */
    private function isProtectedRoute($route)
    {
        foreach ($this->protectedRoutes as $protectedRoute) {
            if (strpos($route, $protectedRoute) === 0) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Vérifie si la route demandée est réservée aux administrateurs
     */
    private function isAdminRoute($route)
    {
        foreach ($this->adminRoutes as $adminRoute) {
            if (strpos($route, $adminRoute) === 0) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Redirige vers la page de connexion avec la route d'origine
     */
    private function redirectToLogin()
    {
        $redirect = urlencode($_SERVER['REQUEST_URI']);
        header("Location: " . BASE_URL . "auth/login?redirect=" . $redirect);
        exit;
    }
    
    /**
     * Affiche une page d'erreur accès refusé
     */
    private function accessDenied()
    {
        $controller = new ErrorController();
        $controller->forbidden();
    }

    /**
     * Affiche une page d'erreur 404
     */
    private function notFound()
    {
        header("HTTP/1.0 404 Not Found");
        $controller = new ErrorController();
        $controller->notFound();
    }
}
