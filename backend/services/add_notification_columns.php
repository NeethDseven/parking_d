<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Database.php';

try {
    $db = Database::getInstance();
    echo "🔄 Ajout des colonnes de notification...\n";

    // Ajouter les colonnes une par une
    $alterQueries = [
        "ALTER TABLE `reservations` ADD COLUMN `notification_start_sent` TINYINT(1) DEFAULT 0 COMMENT 'Notification de début envoyée'",
        "ALTER TABLE `reservations` ADD COLUMN `notification_end_sent` TINYINT(1) DEFAULT 0 COMMENT 'Notification de fin envoyée'",
        "ALTER TABLE `reservations` ADD COLUMN `notification_reminder_sent` TINYINT(1) DEFAULT 0 COMMENT 'Notification de rappel envoyée'",
        "ALTER TABLE `user_abonnements` ADD COLUMN `notification_ending_sent` TINYINT(1) DEFAULT 0 COMMENT 'Notification de fin d\\'abonnement envoyée'"
    ];

    foreach ($alterQueries as $query) {
        try {
            $db->query($query);
            echo "✅ Colonne ajoutée\n";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "⚠️ Colonne existe déjà\n";
            } else {
                echo "❌ Erreur : " . $e->getMessage() . "\n";
            }
        }
    }

    echo "🎉 Terminé !\n";
} catch (Exception $e) {
    echo "❌ Erreur générale : " . $e->getMessage() . "\n";
}
