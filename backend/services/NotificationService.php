<?php

class NotificationService
{
    private $userModel;
    private $logModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->logModel = new LogModel();
    }

    /**
     * Envoie une notification de début de réservation
     */
    public function sendReservationStartNotification($userId, $reservationId, $placeName, $dateDebut)
    {
        $titre = "🚗 Votre réservation commence !";
        $message = "Votre réservation #{$reservationId} pour la place {$placeName} commence le " .
            date('d/m/Y à H:i', strtotime($dateDebut)) . ". N'oubliez pas vos codes d'accès !";

        return $this->userModel->createNotification($userId, $titre, $message, 'reservation_start');
    }

    /**
     * Envoie une notification de fin de réservation
     */
    public function sendReservationEndNotification($userId, $reservationId, $placeName, $dateFin)
    {
        $titre = "⏰ Votre réservation se termine bientôt !";
        $message = "Votre réservation #{$reservationId} pour la place {$placeName} se termine le " .
            date('d/m/Y à H:i', strtotime($dateFin)) . ". Pensez à libérer la place à temps.";

        return $this->userModel->createNotification($userId, $titre, $message, 'reservation_end');
    }

    /**
     * Envoie une notification de confirmation d'abonnement
     */
    public function sendSubscriptionConfirmationNotification($userId, $subscriptionName, $startDate, $endDate)
    {
        $titre = "✅ Abonnement confirmé !";
        $message = "Votre abonnement '{$subscriptionName}' est maintenant actif du " .
            date('d/m/Y', strtotime($startDate)) . " au " . date('d/m/Y', strtotime($endDate)) .
            ". Profitez de vos avantages !";

        return $this->userModel->createNotification($userId, $titre, $message, 'subscription_confirm');
    }

    /**
     * Envoie une notification de fin d'abonnement proche
     */
    public function sendSubscriptionEndingNotification($userId, $subscriptionName, $endDate)
    {
        $titre = "⚠️ Votre abonnement se termine bientôt !";
        $message = "Votre abonnement '{$subscriptionName}' se termine le " .
            date('d/m/Y', strtotime($endDate)) . ". Pensez à le renouveler pour continuer à bénéficier de vos avantages.";

        return $this->userModel->createNotification($userId, $titre, $message, 'subscription_ending');
    }

    /**
     * Envoie une notification de début de réservation immédiate
     */
    public function sendImmediateReservationStartNotification($userId, $reservationId, $placeName, $accessCode)
    {
        $titre = "🚀 Réservation immédiate activée !";
        $message = "Votre réservation immédiate #{$reservationId} pour la place {$placeName} est maintenant active. " .
            "Code d'accès : {$accessCode}. Vous avez 15 minutes pour vous présenter.";

        return $this->userModel->createNotification($userId, $titre, $message, 'immediate_start');
    }

    /**
     * Envoie une notification de fin de réservation immédiate
     */
    public function sendImmediateReservationEndNotification($userId, $reservationId, $placeName)
    {
        $titre = "⏱️ Réservation immédiate terminée";
        $message = "Votre réservation immédiate #{$reservationId} pour la place {$placeName} est maintenant terminée. " .
            "Merci d'avoir utilisé notre service !";

        return $this->userModel->createNotification($userId, $titre, $message, 'immediate_end');
    }
    /**
     * Envoie une notification de rappel avant le début de réservation (24h avant)
     */
    public function sendReservationReminderNotification($userId, $reservationId, $placeName, $dateDebut)
    {
        $dateReservation = new DateTime($dateDebut);
        $maintenant = new DateTime();

        // Calculer la différence en heures
        $diff = $maintenant->diff($dateReservation);
        $heuresRestantes = ($diff->days * 24) + $diff->h;

        // Déterminer le texte selon le timing
        if ($heuresRestantes <= 1) {
            $timing = "dans moins d'une heure";
            $titre = "� Réservation imminente !";
        } elseif ($heuresRestantes <= 6) {
            $timing = "dans quelques heures";
            $titre = "🔔 Réservation aujourd'hui !";
        } elseif ($heuresRestantes <= 24) {
            $timing = "aujourd'hui";
            $titre = "�🔔 Réservation aujourd'hui !";
        } else {
            $timing = "demain";
            $titre = "🔔 Rappel : Réservation demain !";
        }

        $message = "N'oubliez pas ! Votre réservation #{$reservationId} pour la place {$placeName} commence {$timing} le " .
            date('d/m/Y à H:i', strtotime($dateDebut)) . ".";

        return $this->userModel->createNotification($userId, $titre, $message, 'reservation_reminder');
    }

    /**
     * Envoie une notification de rappel avant la fin de réservation (30 min avant)
     */
    public function sendReservationEndReminderNotification($userId, $reservationId, $placeName, $dateFin)
    {
        $titre = "⚠️ Fin de réservation dans 30 minutes !";
        $message = "Votre réservation #{$reservationId} pour la place {$placeName} se termine dans 30 minutes (" .
            date('H:i', strtotime($dateFin)) . "). Pensez à libérer la place.";

        return $this->userModel->createNotification($userId, $titre, $message, 'reservation_end_reminder');
    }

    /**
     * Traite toutes les notifications en attente
     */
    public function processScheduledNotifications()
    {
        $processed = 0;
        $now = date('Y-m-d H:i:s');

        try {
            // Notifications de début de réservation (15 minutes avant)
            $processed += $this->processReservationStartNotifications($now);

            // Notifications de fin de réservation (30 minutes avant)
            $processed += $this->processReservationEndNotifications($now);

            // Notifications de rappel de réservation (24h avant)
            $processed += $this->processReservationReminders($now);

            // Notifications de fin d'abonnement (7 jours avant)
            $processed += $this->processSubscriptionEndingNotifications($now);
        } catch (Exception $e) {
            error_log("Erreur lors du traitement des notifications: " . $e->getMessage());
        }

        return $processed;
    }

    /**
     * Traite les notifications de début de réservation
     */
    private function processReservationStartNotifications($now)
    {
        $db = Database::getInstance();
        $processed = 0;        // Réservations qui commencent dans 15 minutes
        $sql = "SELECT r.id, r.user_id, r.date_debut, CONCAT('Place ', ps.numero) as place_name 
                FROM reservations r 
                JOIN parking_spaces ps ON r.place_id = ps.id 
                WHERE r.status = 'confirmee' 
                AND r.date_debut BETWEEN :now1 AND DATE_ADD(:now2, INTERVAL 15 MINUTE)
                AND r.notification_start_sent = 0";

        $reservations = $db->findAll($sql, ['now1' => $now, 'now2' => $now]);

        foreach ($reservations as $reservation) {
            if ($this->sendReservationStartNotification(
                $reservation['user_id'],
                $reservation['id'],
                $reservation['place_name'],
                $reservation['date_debut']
            )) {
                // Marquer comme envoyée
                $db->update(
                    'reservations',
                    ['notification_start_sent' => 1],
                    ['id' => $reservation['id']]
                );
                $processed++;
            }
        }

        return $processed;
    }

    /**
     * Traite les notifications de fin de réservation
     */
    private function processReservationEndNotifications($now)
    {
        $db = Database::getInstance();
        $processed = 0;        // Réservations qui se terminent dans 30 minutes
        $sql = "SELECT r.id, r.user_id, r.date_fin, CONCAT('Place ', ps.numero) as place_name 
                FROM reservations r 
                JOIN parking_spaces ps ON r.place_id = ps.id 
                WHERE r.status = 'confirmee' 
                AND r.date_fin BETWEEN :now1 AND DATE_ADD(:now2, INTERVAL 30 MINUTE)
                AND r.notification_end_sent = 0";

        $reservations = $db->findAll($sql, ['now1' => $now, 'now2' => $now]);

        foreach ($reservations as $reservation) {
            if ($this->sendReservationEndReminderNotification(
                $reservation['user_id'],
                $reservation['id'],
                $reservation['place_name'],
                $reservation['date_fin']
            )) {
                // Marquer comme envoyée
                $db->update(
                    'reservations',
                    ['notification_end_sent' => 1],
                    ['id' => $reservation['id']]
                );
                $processed++;
            }
        }

        return $processed;
    }

    /**
     * Traite les notifications de rappel 24h avant
     */
    private function processReservationReminders($now)
    {
        $db = Database::getInstance();
        $processed = 0;        // Réservations qui commencent dans 24h
        $sql = "SELECT r.id, r.user_id, r.date_debut, CONCAT('Place ', ps.numero) as place_name 
                FROM reservations r 
                JOIN parking_spaces ps ON r.place_id = ps.id 
                WHERE r.status = 'confirmee' 
                AND r.date_debut BETWEEN DATE_ADD(:now1, INTERVAL 23 HOUR) 
                AND DATE_ADD(:now2, INTERVAL 25 HOUR)
                AND r.notification_reminder_sent = 0";

        $reservations = $db->findAll($sql, ['now1' => $now, 'now2' => $now]);

        foreach ($reservations as $reservation) {
            if ($this->sendReservationReminderNotification(
                $reservation['user_id'],
                $reservation['id'],
                $reservation['place_name'],
                $reservation['date_debut']
            )) {
                // Marquer comme envoyée
                $db->update(
                    'reservations',
                    ['notification_reminder_sent' => 1],
                    ['id' => $reservation['id']]
                );
                $processed++;
            }
        }

        return $processed;
    }

    /**
     * Traite les notifications de fin d'abonnement
     */
    private function processSubscriptionEndingNotifications($now)
    {
        $db = Database::getInstance();
        $processed = 0;        // Abonnements qui se terminent dans 7 jours
        $sql = "SELECT ua.user_id, a.nom as name, ua.date_fin as end_date 
                FROM user_abonnements ua 
                JOIN abonnements a ON ua.abonnement_id = a.id 
                WHERE ua.status = 'active' 
                AND ua.date_fin BETWEEN DATE_ADD(:now1, INTERVAL 6 DAY) 
                AND DATE_ADD(:now2, INTERVAL 8 DAY)
                AND ua.notification_ending_sent = 0";

        $subscriptions = $db->findAll($sql, ['now1' => $now, 'now2' => $now]);

        foreach ($subscriptions as $subscription) {
            if ($this->sendSubscriptionEndingNotification(
                $subscription['user_id'],
                $subscription['name'],
                $subscription['end_date']
            )) {                // Marquer comme envoyée
                $db->update(
                    'user_abonnements',
                    ['notification_ending_sent' => 1],
                    ['user_id' => $subscription['user_id']]
                );
                $processed++;
            }
        }

        return $processed;
    }
}
