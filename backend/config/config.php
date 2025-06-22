<?php
// Configuration principale de l'application
define('BASE_URL', '/projet/parking_d/');
define('APP_NAME', 'ParkMe In');
define('DEBUG', true); // Active les erreurs détaillées en développement

// Chemins d'accès
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
define('BACKEND_PATH', ROOT_PATH . '/backend');
define('FRONTEND_PATH', ROOT_PATH . '/frontend');

require_once 'global_functions.php';

// Configuration base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'parking_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Autoloader pour charger automatiquement les classes
spl_autoload_register(function ($class_name) {
    // Cherche dans controllers
    $controller_path = BACKEND_PATH . '/controllers/' . $class_name . '.php';
    if (file_exists($controller_path)) {
        require_once $controller_path;
        return;
    }

    // Cherche dans models
    $model_path = BACKEND_PATH . '/models/' . $class_name . '.php';
    if (file_exists($model_path)) {
        require_once $model_path;
        return;
    }

    // Cherche dans services
    $service_path = BACKEND_PATH . '/services/' . $class_name . '.php';
    if (file_exists($service_path)) {
        require_once $service_path;
        return;
    }
});
