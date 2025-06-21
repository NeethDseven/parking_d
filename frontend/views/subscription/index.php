<?php require_once FRONTEND_PATH . '/views/templates/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">Abonnements</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (!empty($userSubscriptions)): ?>
        <div class="alert alert-info mb-4">
            <h4 class="alert-heading">Vous avez déjà un abonnement actif</h4>
            <p>Vous êtes actuellement abonné à <strong><?php echo htmlspecialchars($userSubscriptions[0]['name']); ?></strong></p>
            <p>Votre abonnement est valable jusqu'au <?php echo date('d/m/Y', strtotime($userSubscriptions[0]['end_date'])); ?></p>
            <hr>
            <p class="mb-0">Vous pouvez gérer vos abonnements depuis <a href="<?php echo BASE_URL; ?>auth/profile" class="alert-link">votre profil</a>.</p>
        </div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-center mb-4">Nos formules d'abonnement</h2>
            <p class="text-center mb-5">Choisissez l'offre qui correspond le mieux à vos besoins</p>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-5">
        <?php foreach ($subscriptions as $subscription): ?>
            <div class="col">
                <div class="card h-100 subscription-card">
                    <div class="card-header text-center py-3">
                        <h3 class="card-title pricing-card-title"><?php echo htmlspecialchars($subscription['name']); ?></h3>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="text-center mb-4">
                            <span class="display-5"><?php echo number_format($subscription['price'], 2); ?> €</span>
                            <small class="text-muted">/ <?php echo $subscription['duration_days'] == 30 ? 'mois' : ($subscription['duration_days'] == 365 ? 'an' : $subscription['duration_days'] . ' jours'); ?></small>
                        </div>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <?php echo $subscription['free_minutes']; ?> minutes gratuites par réservation
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <?php echo $subscription['discount_percent']; ?>% de réduction sur toutes les réservations
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Accès prioritaire aux places <?php echo strpos(strtolower($subscription['name']), 'premium') !== false ? 'premium' : 'standard'; ?>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Résiliation à tout moment
                            </li>
                        </ul>
                        <div class="mt-auto pt-3">
                            <?php if (empty($userSubscriptions)): ?>
                                <button class="btn btn-primary w-100 subscription-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#subscriptionPaymentModal"
                                    data-subscription-id="<?php echo $subscription['id']; ?>"
                                    data-subscription-name="<?php echo htmlspecialchars($subscription['name']); ?>"
                                    data-subscription-price="<?php echo $subscription['price']; ?>"
                                    data-subscription-duration="<?php echo $subscription['duration_days']; ?>">
                                    Souscrire
                                </button>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary w-100" disabled>Déjà abonné</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h3 class="card-title">Pourquoi souscrire à un abonnement ?</h3>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><i class="fas fa-piggy-bank text-primary me-2"></i> Économisez sur vos réservations régulières</li>
                        <li class="list-group-item"><i class="fas fa-hourglass-half text-primary me-2"></i> Profitez de minutes gratuites à chaque réservation</li>
                        <li class="list-group-item"><i class="fas fa-percent text-primary me-2"></i> Bénéficiez de réductions exclusives</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><i class="fas fa-medal text-primary me-2"></i> Accès prioritaire aux places de parking</li>
                        <li class="list-group-item"><i class="fas fa-calendar-alt text-primary me-2"></i> Réservez votre place jusqu'à 30 jours à l'avance</li>
                        <li class="list-group-item"><i class="fas fa-handshake text-primary me-2"></i> Résiliez à tout moment sans frais</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm faq-section">
        <div class="card-body">
            <h3 class="card-title mb-4">Questions fréquentes sur les abonnements</h3>

            <div class="accordion accordion-flush" id="subscriptionFaq">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            Comment fonctionnent les minutes gratuites ?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#subscriptionFaq">
                        <div class="accordion-body">
                            Les minutes gratuites sont automatiquement déduites du temps total de votre réservation. Par exemple, si vous avez un abonnement avec 30 minutes gratuites et que vous réservez pendant 2 heures, vous ne serez facturé que pour 1 heure et 30 minutes.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Comment résilier mon abonnement ?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#subscriptionFaq">
                        <div class="accordion-body">
                            Vous pouvez résilier votre abonnement à tout moment depuis votre profil utilisateur. La résiliation sera effective immédiatement, mais vous continuerez à bénéficier des avantages jusqu'à la fin de la période payée.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Les abonnements sont-ils renouvelés automatiquement ?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#subscriptionFaq">
                        <div class="accordion-body">
                            Non, les abonnements ne sont pas renouvelés automatiquement. Vous devrez souscrire à nouveau à la fin de votre période d'abonnement si vous souhaitez continuer à bénéficier des avantages.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            Puis-je changer d'abonnement en cours de période ?
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#subscriptionFaq">
                        <div class="accordion-body">
                            Pour changer d'abonnement, vous devez d'abord résilier votre abonnement actuel depuis votre profil, puis souscrire au nouvel abonnement de votre choix.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de paiement pour abonnement -->
<div class="modal fade" id="subscriptionPaymentModal" tabindex="-1" aria-labelledby="subscriptionPaymentModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subscriptionPaymentModalLabel">Paiement de votre abonnement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Récapitulatif de l'abonnement -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Récapitulatif de votre abonnement</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Abonnement:</div>
                            <div class="col-md-8" id="subscription-name-display">-</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Durée:</div>
                            <div class="col-md-8" id="subscription-duration-display">-</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Prix:</div>
                            <div class="col-md-8 fs-5 text-primary fw-bold" id="subscription-price-display">0,00 €</div>
                        </div>
                    </div>
                </div>

                <!-- Formulaire de paiement -->
                <form action="<?php echo BASE_URL; ?>subscription/processPayment" method="post" id="subscription-payment-form">
                    <input type="hidden" name="subscription_id" id="subscription_id" value="">

                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Choisissez votre mode de paiement</h6>
                        </div>
                        <div class="card-body">
                            <!-- Carte bancaire -->
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="mode_paiement" id="subscription_carte" value="carte" checked>
                                    <label class="form-check-label" for="subscription_carte">
                                        <i class="fas fa-credit-card me-2"></i> Carte bancaire
                                    </label>
                                </div>
                                <div id="subscription-carte-details" class="payment-details ms-4">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="subscription_card_number" class="form-label">Numéro de carte</label>
                                            <input type="text" class="form-control" id="subscription_card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="subscription_card_expiry" class="form-label">Date d'expiration</label>
                                            <input type="text" class="form-control" id="subscription_card_expiry" placeholder="MM/AA" maxlength="5">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="subscription_card_cvv" class="form-label">CVV</label>
                                            <input type="text" class="form-control" id="subscription_card_cvv" placeholder="123" maxlength="4">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="subscription_card_name" class="form-label">Nom sur la carte</label>
                                            <input type="text" class="form-control" id="subscription_card_name" placeholder="JEAN DUPONT">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PayPal -->
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="mode_paiement" id="subscription_paypal" value="paypal">
                                    <label class="form-check-label" for="subscription_paypal">
                                        <i class="fab fa-paypal me-2"></i> PayPal
                                    </label>
                                </div>
                                <div id="subscription-paypal-details" class="payment-details d-none">
                                    <p class="text-muted">Vous serez redirigé vers PayPal pour finaliser le paiement.</p>
                                </div>
                            </div>

                            <!-- Virement bancaire -->
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="mode_paiement" id="subscription_virement" value="virement">
                                    <label class="form-check-label" for="subscription_virement">
                                        <i class="fas fa-university me-2"></i> Virement bancaire
                                    </label>
                                </div>
                                <div id="subscription-virement-details" class="payment-details d-none">
                                    <p class="text-muted">Coordonnées bancaires pour le virement:</p>
                                    <p>
                                        IBAN: FR76 1234 5678 9012 3456 7890 123<br>
                                        BIC: AGRIFRPP123<br>
                                        Titulaire: ParkMe In SAS<br>
                                        Référence à indiquer: <span id="subscription-reference">SUB-XXX</span>
                                    </p>
                                    <div class="alert alert-warning">
                                        <small>Votre abonnement sera activé dès réception du paiement (1-3 jours ouvrés).</small>
                                    </div>
                                </div>
                            </div> <!-- Conditions -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="subscription_conditions" name="subscription_conditions" required>
                                <label class="form-check-label" for="subscription_conditions">
                                    J'accepte les <a href="<?php echo BASE_URL; ?>home/terms" target="_blank">conditions d'utilisation</a> et la <a href="<?php echo BASE_URL; ?>home/privacy" target="_blank">politique de confidentialité</a>
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="subscription-payment-form" class="btn btn-primary" id="pay-subscription-btn">
                    <i class="fas fa-credit-card me-2"></i>
                    Payer <span id="pay-amount">0,00 €</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Script de gestion des abonnements -->
<script src="<?php echo BASE_URL; ?>frontend/assets/js/components/subscriptionManager.js"></script>

<!-- Logique de gestion des abonnements maintenant dans subscriptionManager.js -->
<meta name="current-page" content="subscription">