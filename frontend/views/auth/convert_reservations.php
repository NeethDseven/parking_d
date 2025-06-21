<meta name="current-page" content="convert_reservations">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-link me-2"></i> Association de vos réservations</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Nous avons trouvé <?php echo count($guest_reservations); ?> réservation(s) effectuée(s) avec votre adresse email.
                        Vous pouvez maintenant les associer à votre compte pour les gérer plus facilement.
                    </div>

                    <form action="<?php echo BASE_URL; ?>auth/convertReservations" method="post">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="select-all" checked>
                                                <label class="form-check-label" for="select-all">
                                                    Tout sélectionner
                                                </label>
                                            </div>
                                        </th>
                                        <th>Référence</th>
                                        <th>Place</th>
                                        <th>Dates</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($guest_reservations as $reservation): ?>
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input reservation-checkbox" type="checkbox"
                                                        name="convert_<?php echo $reservation['id']; ?>" id="convert_<?php echo $reservation['id']; ?>"
                                                        value="1" checked>
                                                    <label class="form-check-label" for="convert_<?php echo $reservation['id']; ?>">
                                                        Associer
                                                    </label>
                                                </div>
                                            </td>
                                            <td>#<?php echo str_pad($reservation['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($reservation['numero']); ?>
                                                <span class="badge <?php
                                                                    if ($reservation['type'] === 'handicape') echo 'bg-primary';
                                                                    elseif ($reservation['type'] === 'electrique') echo 'bg-success';
                                                                    else echo 'bg-secondary';
                                                                    ?>"><?php echo ucfirst($reservation['type']); ?></span>
                                            </td>
                                            <td>
                                                <small>Du: <?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></small><br>
                                                <small>Au: <?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></small>
                                            </td>
                                            <td><?php echo number_format($reservation['montant_total'], 2); ?> €</td>
                                            <td>
                                                <?php if ($reservation['status'] === 'confirmée'): ?>
                                                    <span class="badge bg-success">Confirmée</span>
                                                <?php elseif ($reservation['status'] === 'en_attente'): ?>
                                                    <span class="badge bg-warning text-dark">En attente</span>
                                                <?php elseif ($reservation['status'] === 'annulée'): ?>
                                                    <span class="badge bg-danger">Annulée</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> Les réservations non sélectionnées resteront accessibles via leur code de suivi mais ne seront pas visibles dans votre compte.
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-link me-2"></i> Associer les réservations sélectionnées
                            </button>
                            <a href="<?php echo BASE_URL; ?>auth/login" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i> Ignorer
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- La gestion des cases à cocher est déléguée au composant reservationConverter.js -->