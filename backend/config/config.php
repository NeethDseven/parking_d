<?php
// Configuration de l'application
define('BASE_URL', '/projet/parking_d/');
define('APP_NAME', 'ParkMe In');
define('DEBUG', true); // Mode de débogage pour afficher les erreurs en détail

// Chemins d'accès
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
define('BACKEND_PATH', ROOT_PATH . '/backend');
define('FRONTEND_PATH', ROOT_PATH . '/frontend');

// Chargement des fonctions globales
require_once 'global_functions.php';

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'parking_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Chargement automatique des classes
spl_autoload_register(function ($class_name) {
    // Chercher dans les controllers
    $controller_path = BACKEND_PATH . '/controllers/' . $class_name . '.php';
    if (file_exists($controller_path)) {
        require_once $controller_path;
        return;
    }    // Chercher dans les models
    $model_path = BACKEND_PATH . '/models/' . $class_name . '.php';
    if (file_exists($model_path)) {
        require_once $model_path;
        return;
    }

    // Chercher dans les services
    $service_path = BACKEND_PATH . '/services/' . $class_name . '.php';
    if (file_exists($service_path)) {
        require_once $service_path;
        return;
    }
});
