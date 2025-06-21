<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Database.php';

try {
    $db = Database::getInstance();
    echo "🔄 Mise à jour du schéma des notifications...\n";
    // Vérifier si les colonnes existent déjà
    $checkColumns = [
        "SHOW COLUMNS FROM reservations LIKE 'notification_start_sent'",
        "SHOW COLUMNS FROM reservations LIKE 'notification_end_sent'",
        "SHOW COLUMNS FROM reservations LIKE 'notification_reminder_sent'",
        "SHOW COLUMNS FROM user_abonnements LIKE 'notification_ending_sent'"
    ];

    $columnsToAdd = [];

    foreach ($checkColumns as $i => $check) {
        $result = $db->query($check);
        if (empty($result)) {
            $columnsToAdd[] = $i;
        }
    }
    // Ajouter les colonnes manquantes
    $alterQueries = [
        "ALTER TABLE `reservations` ADD COLUMN `notification_start_sent` TINYINT(1) DEFAULT 0 COMMENT 'Notification de début envoyée'",
        "ALTER TABLE `reservations` ADD COLUMN `notification_end_sent` TINYINT(1) DEFAULT 0 COMMENT 'Notification de fin envoyée'",
        "ALTER TABLE `reservations` ADD COLUMN `notification_reminder_sent` TINYINT(1) DEFAULT 0 COMMENT 'Notification de rappel envoyée'",
        "ALTER TABLE `user_abonnements` ADD COLUMN `notification_ending_sent` TINYINT(1) DEFAULT 0 COMMENT 'Notification de fin d\\'abonnement envoyée'"
    ];

    foreach ($columnsToAdd as $index) {
        try {
            $db->query($alterQueries[$index]);
            echo "✅ Colonne ajoutée : " . ($index < 3 ? "reservations" : "user_abonnements") . "\n";
        } catch (Exception $e) {
            echo "⚠️ Erreur pour la colonne $index : " . $e->getMessage() . "\n";
        }
    }

    // Créer les index pour améliorer les performances
    try {
        $db->query("CREATE INDEX idx_reservations_notifications ON reservations(status, date_debut, date_fin, notification_start_sent, notification_end_sent, notification_reminder_sent)");
        echo "✅ Index créé pour reservations\n";
    } catch (Exception $e) {
        echo "⚠️ Index reservations existe déjà ou erreur : " . $e->getMessage() . "\n";
    }

    try {
        $db->query("CREATE INDEX idx_subscriptions_notifications ON user_abonnements(status, date_fin, notification_ending_sent)");
        echo "✅ Index créé pour user_abonnements\n";
    } catch (Exception $e) {
        echo "⚠️ Index user_abonnements existe déjà ou erreur : " . $e->getMessage() . "\n";
    }

    echo "🎉 Mise à jour terminée avec succès !\n";
} catch (Exception $e) {
    echo "❌ Erreur lors de la mise à jour : " . $e->getMessage() . "\n";
}
