<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0"><i class="fas fa-list me-2"></i> Vos réservations</h1>
                </div>
                <div class="card-body">
                    <p class="lead">Nous avons trouvé <?php echo count($reservations); ?> réservation(s) associée(s) à votre email.</p>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Place</th>
                                    <th>Dates</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $res): ?>
                                    <tr>
                                        <td>#<?php echo str_pad($res['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($res['numero']); ?>
                                            <span class="badge <?php
                                                                if ($res['type'] === 'handicape') echo 'bg-primary';
                                                                elseif ($res['type'] === 'electrique') echo 'bg-success';
                                                                else echo 'bg-secondary';
                                                                ?>"><?php echo ucfirst($res['type']); ?></span>
                                        </td>
                                        <td>
                                            <small>Du: <?php echo date('d/m/Y H:i', strtotime($res['date_debut'])); ?></small><br>
                                            <small>Au: <?php echo date('d/m/Y H:i', strtotime($res['date_fin'])); ?></small>
                                        </td>
                                        <td><?php echo number_format($res['montant_total'], 2); ?> €</td>
                                        <td>
                                            <?php if ($res['status'] === 'confirmée'): ?>
                                                <span class="badge bg-success">Confirmée</span>
                                            <?php elseif ($res['status'] === 'en_attente'): ?>
                                                <span class="badge bg-warning text-dark">En attente</span>
                                            <?php elseif ($res['status'] === 'annulée'): ?>
                                                <span class="badge bg-danger">Annulée</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>reservation/trackReservation/<?php echo $res['guest_token']; ?>"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i> Détails
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <a href="<?php echo BASE_URL; ?>home/reservationTracking" class="btn btn-outline-primary">
                    <i class="fas fa-search me-2"></i> Nouvelle recherche
                </a>
                <a href="<?php echo BASE_URL; ?>home" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i> Accueil
                </a>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <h5 class="card-title"><i class="fas fa-user-plus me-2 text-success"></i> Créez un compte pour simplifier vos futures réservations!</h5>
                            <p class="mb-0">En créant un compte, vous pourrez gérer facilement toutes vos réservations et bénéficier de fonctionnalités supplémentaires.</p>
                        </div>
                        <div class="col-md-3 text-end">
                            <a href="<?php echo BASE_URL; ?>auth/register" class="btn btn-success">
                                <i class="fas fa-user-plus me-2"></i> S'inscrire
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>