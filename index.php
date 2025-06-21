<?php
// Démarrer la session
session_start();

// Charger la configuration
require_once 'backend/config/config.php';

// Charger le routeur
$router = new Router();
$router->route();
