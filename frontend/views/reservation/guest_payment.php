<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i> Vous effectuez une réservation en tant qu'invité. Un lien de suivi vous sera fourni pour consulter et gérer votre réservation.
            </div>

            <div class="card border-primary mb-4">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0"><i class="fas fa-credit-card me-2"></i> Paiement de votre réservation</h1>
                </div>
                <div class="card-body">
                    <h5 class="mb-4">Récapitulatif de votre réservation</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Nom:</th>
                                    <td><?php echo htmlspecialchars($reservation['guest_name']); ?></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><?php echo htmlspecialchars($reservation['guest_email']); ?></td>
                                </tr>
                                <?php if (!empty($reservation['guest_phone'])): ?>
                                    <tr>
                                        <th>Téléphone:</th>
                                        <td><?php echo htmlspecialchars($reservation['guest_phone']); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Numéro de place:</th>
                                    <td><?php echo htmlspecialchars($reservation['numero']); ?> (<?php echo ucfirst($reservation['type']); ?>)</td>
                                </tr>
                                <tr>
                                    <th>Date de début:</th>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                                </tr>
                                <tr>
                                    <th>Date de fin:</th>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Montant total:</h5>
                                <span class="h3 text-primary mb-0"><?php echo number_format($reservation['montant_total'], 2); ?> €</span>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3">Choisissez votre mode de paiement</h5>
                    <form action="<?php echo BASE_URL; ?>reservation/guestPayment/<?php echo $reservation['id']; ?>/<?php echo $reservation['guest_token']; ?>" method="post" id="guest-payment-form">
                        <div class="mb-4">
                            <div class="form-check mb-3 border p-3 rounded">
                                <input class="form-check-input" type="radio" name="mode_paiement" id="carte" value="carte" checked>
                                <label class="form-check-label" for="carte">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <i class="fas fa-credit-card fa-2x me-3 text-primary"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold">Carte bancaire</span>
                                            <div class="small text-muted">Paiement sécurisé via notre passerelle</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div id="carte-details" class="payment-details ms-4 mb-4">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="card_number" class="form-label">Numéro de carte</label>
                                        <input type="text" class="form-control" id="card_number" placeholder="1234 5678 9012 3456">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="card_name" class="form-label">Nom sur la carte</label>
                                        <input type="text" class="form-control" id="card_name" placeholder="JEAN DUPONT">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="card_expiry" class="form-label">Date d'expiration</label>
                                        <input type="text" class="form-control" id="card_expiry" placeholder="MM/AA">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="card_cvv" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="card_cvv" placeholder="123">
                                    </div>
                                </div>
                            </div>

                            <div class="form-check mb-3 border p-3 rounded">
                                <input class="form-check-input" type="radio" name="mode_paiement" id="paypal" value="paypal">
                                <label class="form-check-label" for="paypal">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <i class="fab fa-paypal fa-2x me-3 text-primary"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold">PayPal</span>
                                            <div class="small text-muted">Paiement sécurisé via PayPal</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="form-check border p-3 rounded">
                                <input class="form-check-input" type="radio" name="mode_paiement" id="virement" value="virement">
                                <label class="form-check-label" for="virement">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <i class="fas fa-university fa-2x me-3 text-primary"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold">Virement bancaire</span>
                                            <div class="small text-muted">Paiement par virement bancaire</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div id="virement-details" class="payment-details hidden mt-3">
                                <div class="alert alert-info">
                                    <p class="mb-0">Coordonnées bancaires pour le virement:</p>
                                    <p class="mb-0">
                                        IBAN: FR76 1234 5678 9012 3456 7890 123<br>
                                        BIC: AGRIFRPP123<br>
                                        Titulaire: ParkMe In SAS<br>
                                        Référence à indiquer: RES-<?php echo $reservation['id']; ?>
                                    </p>
                                </div>
                                <div class="alert alert-warning">
                                    <small>Votre réservation sera confirmée dès réception du paiement (1-3 jours ouvrés).</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input type="checkbox" class="form-check-input" id="conditions" required>
                            <label class="form-check-label" for="conditions">
                                J'accepte les <a href="<?php echo BASE_URL; ?>home/terms" target="_blank">conditions d'utilisation</a> et la <a href="<?php echo BASE_URL; ?>home/privacy" target="_blank">politique de confidentialité</a>
                            </label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-lock me-2"></i> Payer <?php echo number_format($reservation['montant_total'], 2); ?> € de façon sécurisée
                            </button>
                            <a href="<?php echo BASE_URL; ?>reservation/cancelGuestReservation/<?php echo $reservation['id']; ?>/<?php echo $reservation['guest_token']; ?>" class="btn btn-outline-danger"
                                onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                <i class="fas fa-times me-2"></i> Annuler la réservation
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-shield-alt me-2 text-success"></i> Paiement sécurisé</h5>
                    <p class="mb-0">Toutes vos informations de paiement sont cryptées et sécurisées. Nous ne stockons pas vos données de carte bancaire.</p>
                </div>
            </div>
        </div>
    </div>
</div>