<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i> Vous avez effectué cette réservation en tant qu'invité. Conservez le lien de suivi qui vous a été fourni pour consulter et gérer votre réservation ultérieurement.
            </div>

            <div class="card border-success mb-4">
                <div class="card-header bg-success text-white">
                    <h1 class="h4 mb-0"><i class="fas fa-check-circle me-2"></i> Réservation confirmée !</h1>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle text-success fa-5x mb-3"></i>
                        <h2 class="h3">Votre réservation a été confirmée avec succès</h2>
                        <p class="lead">Voici votre code d'accès :</p>
                        <div class="d-inline-block p-3 mb-3 border border-dark rounded code-box">
                            <span class="h2 mb-0"><?php echo $reservation['code_acces']; ?></span>
                        </div>
                        <p>Veuillez conserver ce code, il vous sera demandé à l'entrée du parking.</p>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Votre code de suivi</h5>
                        </div>
                        <div class="card-body">
                            <p>Utilisez ce code pour retrouver les détails de votre réservation ultérieurement :</p>
                            <div class="alert alert-primary">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="font-monospace fw-bold"><?php echo $reservation['guest_token']; ?></span>
                                    <button class="btn btn-sm btn-outline-primary copy-btn" data-clipboard-text="<?php echo $reservation['guest_token']; ?>">
                                        <i class="fas fa-copy me-1"></i> Copier
                                    </button>
                                </div>
                            </div>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i> Conservez ce code précieusement. Il vous permettra de consulter et de gérer votre réservation.
                            </div>
                            <p>Vous pouvez également retrouver votre réservation à tout moment en utilisant l'email que vous avez fourni : <strong><?php echo htmlspecialchars($reservation['guest_email']); ?></strong></p>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Détails de la réservation</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th>Numéro de réservation :</th>
                                            <td>#<?php echo str_pad($reservation['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Place :</th>
                                            <td><?php echo htmlspecialchars($reservation['numero']); ?> (<?php echo ucfirst($reservation['type']); ?>)</td>
                                        </tr>
                                        <tr>
                                            <th>Montant total :</th>
                                            <td><?php echo number_format($reservation['montant_total'], 2); ?> €</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th>Date et heure d'arrivée :</th>
                                            <td><?php echo date('d/m/Y à H:i', strtotime($reservation['date_debut'])); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Date et heure de départ :</th>
                                            <td><?php echo date('d/m/Y à H:i', strtotime($reservation['date_fin'])); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Statut du paiement :</th>
                                            <td>
                                                <?php if (isset($payment) && $payment['status'] === 'valide'): ?>
                                                    <span class="badge bg-success">Payé</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">En attente</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Informations importantes</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i> <b>Adresse :</b> 123 Rue du Parking, 75000 Paris, France</li>
                                <li class="mb-2"><i class="fas fa-clock text-primary me-2"></i> <b>Horaires d'ouverture :</b> 24h/24, 7j/7</li>
                                <li class="mb-2"><i class="fas fa-phone text-primary me-2"></i> <b>Contact :</b> 01 23 45 67 89</li>
                                <li><i class="fas fa-info-circle text-primary me-2"></i> <b>Note :</b> En cas de problème pour accéder au parking, veuillez contacter notre service client au numéro ci-dessus.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="alert alert-success mb-4">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-user-plus fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Créez un compte pour faciliter vos prochaines réservations</h5>
                                <p class="mb-0">Créer un compte vous permettra de gérer facilement toutes vos réservations et de bénéficier d'avantages exclusifs.</p>
                                <a href="<?php echo BASE_URL; ?>auth/register" class="btn btn-success mt-2">
                                    <i class="fas fa-user-plus me-2"></i> Créer un compte
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="javascript:window.print();" class="btn btn-outline-dark me-2"><i class="fas fa-print me-2"></i> Imprimer la confirmation</a>
                        <a href="<?php echo BASE_URL; ?>home" class="btn btn-primary"><i class="fas fa-home me-2"></i> Retour à l'accueil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles gérés par la structure CSS optimisée -->

<!-- La fonctionnalité de copie est maintenant gérée par coreUIService.js -->