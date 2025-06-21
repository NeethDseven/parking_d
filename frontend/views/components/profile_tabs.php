<?php

/**
 * Component pour afficher les onglets du profil
 *
 * @param array $user Les données de l'utilisateur
 * @param array $reservations Les réservations de l'utilisateur
 * @param array $notifications Les notifications de l'utilisateur
 * @param int $unread_notifications Le nombre de notifications non lues
 */
function renderProfileTabs($user, $reservations, $notifications, $unread_notifications)
{
?>
    <!-- Navigation par onglets -->
    <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="informations-tab" data-bs-toggle="tab" data-bs-target="#informations" type="button" role="tab" aria-controls="informations" aria-selected="true" data-i18n="profile.tabs.info">
                <i class="fas fa-user-circle me-2"></i>Mes informations
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reservations-tab" data-bs-toggle="tab" data-bs-target="#reservations" type="button" role="tab" aria-controls="reservations" aria-selected="false" data-i18n="profile.tabs.reservations">
                <i class="fas fa-ticket-alt me-2"></i>Mes réservations
                <?php if (!empty($reservations)): ?>
                    <span class="badge rounded-pill bg-primary"><?php echo count($reservations); ?></span>
                <?php endif; ?>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab" aria-controls="notifications" aria-selected="false" data-i18n="profile.tabs.notifications">
                <i class="fas fa-bell me-2"></i>Notifications
                <?php if ($unread_notifications > 0): ?>
                    <span class="badge rounded-pill bg-danger notification-badge"><?php echo $unread_notifications; ?></span>
                <?php endif; ?>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="profileTabsContent">
        <!-- Onglet Informations -->
        <div class="tab-pane fade show active" id="informations" role="tabpanel" aria-labelledby="informations-tab">
            <?php include 'profile_info_tab.php'; ?>
        </div>

        <!-- Onglet Réservations -->
        <div class="tab-pane fade" id="reservations" role="tabpanel" aria-labelledby="reservations-tab">
            <?php include 'profile_reservations_tab.php'; ?>
        </div>

        <!-- Onglet Notifications -->
        <div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
            <?php include 'profile_notifications_tab.php'; ?>
        </div>
    </div>
<?php
}
?>