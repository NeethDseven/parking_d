<?php

/**
 * Component pour afficher l'onglet des réservations
 *
 * @param array $reservations Les réservations de l'utilisateur
 */
function renderProfileReservationsTab($reservations)
{
?>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0" data-i18n="profile.reservations.title">Mes réservations</h5>
        </div>
        <div class="card-body">
            <?php if (empty($reservations)): ?>
                <p class="text-muted" data-i18n="profile.reservations.no_reservations">Aucune réservation</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped reservations-table">
                        <thead>
                            <tr>
                                <th scope="col" data-i18n="profile.reservations.table.place">Place</th>
                                <th scope="col" data-i18n="profile.reservations.table.start">Début</th>
                                <th scope="col" data-i18n="profile.reservations.table.end">Fin</th>
                                <th scope="col" data-i18n="profile.reservations.table.status">Statut</th>
                                <th scope="col" data-i18n="profile.reservations.table.actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                                <tr data-reservation-id="<?php echo $reservation['id']; ?>"
                                    data-status="<?php echo $reservation['statut']; ?>"
                                    data-date-debut="<?php echo $reservation['date_debut']; ?>"
                                    data-date-fin="<?php echo $reservation['date_fin']; ?>">
                                    <td>
                                        Place n°<?php echo htmlspecialchars($reservation['numero']); ?>
                                        <span class="badge bg-secondary text-white"><?php echo htmlspecialchars(ucfirst($reservation['type_place'])); ?></span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></td>
                                    <td>
                                        <?php include 'reservation_status_badge.php'; ?>
                                    </td>
                                    <td class="reservation-actions">
                                        <?php include 'reservation_actions.php'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php
}
?>