<?php

/**
 * Page d'administration pour tester les notifications
 * URL: /admin/notifications
 */

require_once '../../config/config.php';
require_once '../../models/Database.php';
require_once '../NotificationService.php';

// Vérifier que c'est un admin
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    die('Accès interdit');
}

$notificationService = new NotificationService();

// Traitement des actions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'process_notifications':
                $processed = $notificationService->processScheduledNotifications();
                $message = "✅ {$processed} notifications traitées.";
                break;

            case 'test_notification':
                if (isset($_POST['user_id']) && isset($_POST['type'])) {
                    $userId = intval($_POST['user_id']);
                    $type = $_POST['type'];
                    $success = false;

                    switch ($type) {
                        case 'reservation_start':
                            $success = $notificationService->sendReservationStartNotification(
                                $userId,
                                999,
                                'Place de test',
                                date('Y-m-d H:i:s', strtotime('+15 minutes'))
                            );
                            break;
                        case 'reservation_end':
                            $success = $notificationService->sendReservationEndReminderNotification(
                                $userId,
                                999,
                                'Place de test',
                                date('Y-m-d H:i:s', strtotime('+30 minutes'))
                            );
                            break;
                        case 'immediate_start':
                            $success = $notificationService->sendImmediateReservationStartNotification(
                                $userId,
                                999,
                                'Place de test',
                                'TEST123'
                            );
                            break;
                        case 'subscription_confirm':
                            $success = $notificationService->sendSubscriptionConfirmationNotification(
                                $userId,
                                'Abonnement Test',
                                date('Y-m-d'),
                                date('Y-m-d', strtotime('+1 month'))
                            );
                            break;
                    }

                    $message = $success ? "✅ Notification de test envoyée." : "❌ Erreur lors de l'envoi.";
                } else {
                    $message = "❌ Paramètres manquants.";
                }
                break;
        }
    }
}

// Récupérer les utilisateurs pour les tests
$db = Database::getInstance();
$users = $db->findAll("SELECT id, email, nom, prenom FROM users WHERE role != 'admin' ORDER BY email LIMIT 10");

// Récupérer les dernières notifications
$recentNotifications = $db->findAll("
    SELECT n.*, u.email, u.nom, u.prenom 
    FROM notifications n 
    JOIN users u ON n.user_id = u.id 
    ORDER BY n.created_at DESC 
    LIMIT 20
");
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration des Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <h1><i class="fas fa-bell"></i> Administration des Notifications</h1>

                <?php if (isset($message)): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>

                <!-- Actions -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-cogs"></i> Actions Système</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="process_notifications">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-play"></i> Traiter les notifications en attente
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-test-tube"></i> Test de Notification</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="test_notification">

                                    <div class="mb-3">
                                        <label for="user_id" class="form-label">Utilisateur :</label>
                                        <select name="user_id" id="user_id" class="form-select" required>
                                            <option value="">Choisir un utilisateur</option>
                                            <?php foreach ($users as $user): ?>
                                                <option value="<?php echo $user['id']; ?>">
                                                    <?php echo htmlspecialchars($user['email'] . ' - ' . $user['prenom'] . ' ' . $user['nom']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="type" class="form-label">Type de notification :</label>
                                        <select name="type" id="type" class="form-select" required>
                                            <option value="">Choisir un type</option>
                                            <option value="reservation_start">🚗 Début de réservation</option>
                                            <option value="reservation_end">⏰ Fin de réservation</option>
                                            <option value="immediate_start">🚀 Réservation immédiate</option>
                                            <option value="subscription_confirm">✅ Confirmation d'abonnement</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-paper-plane"></i> Envoyer un test
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dernières notifications -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-history"></i> Dernières Notifications</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Utilisateur</th>
                                        <th>Type</th>
                                        <th>Titre</th>
                                        <th>Message</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentNotifications as $notif): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y H:i', strtotime($notif['created_at'])); ?></td>
                                            <td><?php echo htmlspecialchars($notif['email']); ?></td>
                                            <td>
                                                <span class="badge bg-secondary"><?php echo $notif['type']; ?></span>
                                            </td>
                                            <td><?php echo htmlspecialchars($notif['titre']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($notif['message'], 0, 50)) . '...'; ?></td>
                                            <td>
                                                <?php if ($notif['lu']): ?>
                                                    <span class="badge bg-success">Lue</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Non lue</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>