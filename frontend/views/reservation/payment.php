<meta name="current-page" content="payment">
<div class="container py-5">
    <h1 class="mb-4">Paiement de votre réservation</h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Récapitulatif de votre réservation</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Place:</div>
                        <div class="col-md-8"><?php echo htmlspecialchars($reservation['numero']); ?> (<?php echo ucfirst($reservation['type']); ?>)</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Date de début:</div>
                        <div class="col-md-8"><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Date de fin:</div>
                        <div class="col-md-8"><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Durée:</div>
                        <div class="col-md-8">
                            <?php
                            $debut = new DateTime($reservation['date_debut']);
                            $fin = new DateTime($reservation['date_fin']);
                            $duree = $fin->diff($debut);
                            echo $duree->h . ' heure(s)';
                            if ($duree->d > 0) {
                                echo ' et ' . $duree->d . ' jour(s)';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Montant total:</div>
                        <div class="col-md-8">
                            <?php if (isset($subscription_benefits) && isset($final_amount)): ?>
                                <div class="d-flex flex-column">
                                    <span class="text-muted text-decoration-line-through"><?php echo number_format($original_amount, 2); ?> €</span>
                                    <span class="text-success fw-bold"><?php echo number_format($final_amount, 2); ?> €</span>
                                    <small class="text-muted">Économie avec abonnement : <?php echo number_format($total_savings, 2); ?> €</small>
                                </div>
                            <?php else: ?>
                                <?php echo number_format($reservation['montant_total'], 2); ?> €
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (isset($subscription_benefits)): ?>
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-star me-2"></i>
                            <strong>Avantages Abonnement "<?php echo htmlspecialchars($subscription_benefits['name']); ?>" :</strong>
                            <ul class="mb-0 mt-2">
                                <li><?php echo intval($subscription_benefits['free_minutes']); ?> minutes gratuites</li>
                                <li><?php echo number_format($subscription_benefits['discount_percent'], 0); ?>% de réduction</li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Choisissez votre mode de paiement</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>reservation/payment/<?php echo $reservation['id']; ?>" method="post" id="payment-form">
                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="mode_paiement" id="carte" value="carte" checked>
                                <label class="form-check-label" for="carte">
                                    <i class="fas fa-credit-card me-2"></i> Carte bancaire
                                </label>
                            </div>
                            <div id="carte-details" class="payment-details ms-4">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="card_number" class="form-label">Numéro de carte</label>
                                        <input type="text" class="form-control" id="card_number" placeholder="1234 5678 9012 3456">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="card_expiry" class="form-label">Date d'expiration</label>
                                        <input type="text" class="form-control" id="card_expiry" placeholder="MM/AA">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="card_cvv" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="card_cvv" placeholder="123">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="card_name" class="form-label">Nom sur la carte</label>
                                        <input type="text" class="form-control" id="card_name" placeholder="JEAN DUPONT">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="mode_paiement" id="paypal" value="paypal">
                                <label class="form-check-label" for="paypal">
                                    <i class="fab fa-paypal me-2"></i> PayPal
                                </label>
                            </div>
                            <div id="paypal-details" class="payment-details hidden">
                                <p class="text-muted">Vous serez redirigé vers PayPal pour finaliser le paiement.</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="mode_paiement" id="virement" value="virement">
                                <label class="form-check-label" for="virement">
                                    <i class="fas fa-university me-2"></i> Virement bancaire
                                </label>
                            </div>
                            <div id="virement-details" class="payment-details hidden">
                                <p class="text-muted">Coordonnées bancaires pour le virement:</p>
                                <p>
                                    IBAN: FR76 1234 5678 9012 3456 7890 123<br>
                                    BIC: AGRIFRPP123<br>
                                    Titulaire: ParkMe In SAS<br>
                                    Référence à indiquer: RES-<?php echo $reservation['id']; ?>
                                </p>
                                <div class="alert alert-warning">
                                    <small>Votre réservation sera confirmée dès réception du paiement (1-3 jours ouvrés).</small>
                                </div>
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="conditions" name="conditions" required>
                            <label class="form-check-label" for="conditions">
                                J'accepte les <a href="<?php echo BASE_URL; ?>home/terms" target="_blank">conditions d'utilisation</a> et la <a href="<?php echo BASE_URL; ?>home/privacy" target="_blank">politique de confidentialité</a>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">Payer <?php echo number_format(isset($final_amount) ? $final_amount : $reservation['montant_total'], 2); ?> €</button>
                        <a href="<?php echo BASE_URL; ?>auth/profile" class="btn btn-outline-secondary">Annuler</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informations importantes</h5>
                </div>
                <div class="card-body">
                    <p><i class="fas fa-info-circle text-primary me-2"></i> Votre réservation sera confirmée immédiatement après le paiement.</p>
                    <p><i class="fas fa-clock text-primary me-2"></i> Vous pourrez accéder au parking avec votre code d'accès qui vous sera fourni après le paiement.</p>
                    <p><i class="fas fa-exclamation-triangle text-warning me-2"></i> En cas d'annulation, des frais peuvent s'appliquer selon nos conditions.</p>
                    <hr>
                    <p><i class="fas fa-shield-alt text-success me-2"></i> Paiement 100% sécurisé</p>
                </div>
            </div>
        </div>
    </div>
</div>