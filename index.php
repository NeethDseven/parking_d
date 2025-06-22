<?php
// Point d'entrée principal de l'application
session_start();

require_once 'backend/config/config.php';

// Route la requête vers le bon contrôleur
$router = new Router();
$router->route();
