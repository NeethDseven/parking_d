<?php
// Routeur principal - Analyse l'URL et route vers les contrôleurs
class Router
{
    // Routes nécessitant une authentification
    private $protectedRoutes = [
        'auth/profile',
        'reservation/reserve',
        'reservation/reserveImmediate',
        'reservation/payment',
        'reservation/confirmation',
        'reservation/generateAccessCode',
        'reservation/immediate',
        'reservation/endImmediate',
        'subscription',
        'subscription/subscribe',
        'subscription/cancel',
        'notification'
    ];

    // Routes réservées aux administrateurs
    private $adminRoutes = [
        'admin',
        'admin/dashboard',
        'admin/users',
        'admin/places',
        'admin/reservations',
        'admin/tarifs',
        'admin/subscriptions',
        'subscription/admin'
    ];

    // Traite la requête et route vers le bon contrôleur
    public function route()
    {
        $url = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
        $currentRoute = strtolower($url);

        // Vérifie les droits d'accès
        if ($this->isProtectedRoute($currentRoute) && !$this->isAuthenticated()) {
            $this->redirectToLogin();
            return;
        }

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

        // Parse l'URL en segments
        $segments = explode('/', $url);
        $controller_name = ucfirst($segments[0]) . 'Controller';
        $action = isset($segments[1]) ? $segments[1] : 'index';
        $params = array_slice($segments, 2);

        // Instancie et appelle le contrôleur
        if (class_exists($controller_name)) {
            $controller = new $controller_name();

            if (method_exists($controller, $action)) {
                call_user_func_array([$controller, $action], $params);
            } else {
                $this->notFound();
            }
        } else {
            $this->notFound();
        }
    }

    // Vérifie si l'utilisateur est authentifié
    private function isAuthenticated()
    {
        return isset($_SESSION['user']);
    }

    // Vérifie si l'utilisateur est administrateur
    private function isAdmin()
    {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }

    // Vérifie si la route nécessite une authentification
    private function isProtectedRoute($route)
    {
        foreach ($this->protectedRoutes as $protectedRoute) {
            if (strpos($route, $protectedRoute) === 0) {
                return true;
            }
        }
        return false;
    }

    // Vérifie si la route est réservée aux administrateurs
    private function isAdminRoute($route)
    {
        foreach ($this->adminRoutes as $adminRoute) {
            if (strpos($route, $adminRoute) === 0) {
                return true;
            }
        }
        return false;
    }

    // Redirige vers login en conservant la route d'origine
    private function redirectToLogin()
    {
        $redirect = urlencode($_SERVER['REQUEST_URI']);
        header("Location: " . BASE_URL . "auth/login?redirect=" . $redirect);
        exit;
    }

    // Affiche la page d'accès refusé
    private function accessDenied()
    {
        $controller = new ErrorController();
        $controller->forbidden();
    }

    // Affiche la page 404
    private function notFound()
    {
        header("HTTP/1.0 404 Not Found");
        $controller = new ErrorController();
        $controller->notFound();
    }
}
