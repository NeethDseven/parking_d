<?php

/**
 * Script de traitement des notifications planifiées
 * À exécuter via une tâche CRON toutes les 5 minutes
 * 
 * Crontab entry:
 * 0,5,10,15,20,25,30,35,40,45,50,55 * * * * /usr/bin/php /path/to/parking_d/backend/services/notification_cron.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/NotificationService.php';

try {
    $notificationService = new NotificationService();

    echo "[" . date('Y-m-d H:i:s') . "] Démarrage du traitement des notifications...\n";

    // Traiter toutes les notifications en attente
    $processed = $notificationService->processScheduledNotifications();

    echo "[" . date('Y-m-d H:i:s') . "] Traitement terminé. {$processed} notifications envoyées.\n";

    // Nettoyer les anciennes notifications (plus de 30 jours)
    $db = Database::getInstance();
    $deleted = $db->query("DELETE FROM notifications WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY) AND lu = 1");

    if ($deleted) {
        echo "[" . date('Y-m-d H:i:s') . "] Nettoyage effectué : anciennes notifications supprimées.\n";
    }
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ERREUR : " . $e->getMessage() . "\n";
    error_log("Erreur CRON notifications: " . $e->getMessage());
}
