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

                        <!-- QR Code -->
                        <div class="mt-3 mb-3">
                            <div id="qr-guest-access-code" class="mx-auto mb-3" style="width: 150px; height: 150px;"></div>
                            <button class="btn btn-sm btn-outline-primary" onclick="copyAccessCode()" title="Copier le code d'accès">
                                <i class="fas fa-copy me-1"></i> Copier le code
                            </button>
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
                                    <button class="btn btn-sm btn-outline-primary" onclick="copyGuestToken()" title="Copier le code de suivi">
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
                                            <td>
                                                <?php echo htmlspecialchars($reservation['numero']); ?>
                                                <?php if ($reservation['type'] === 'standard'): ?>
                                                    <span class="badge bg-secondary ms-2">Standard</span>
                                                <?php elseif ($reservation['type'] === 'handicape'): ?>
                                                    <span class="badge bg-primary ms-2">PMR</span>
                                                <?php elseif ($reservation['type'] === 'electrique'): ?>
                                                    <span class="badge bg-success ms-2">Électrique</span>
                                                <?php elseif ($reservation['type'] === 'moto/scooter'): ?>
                                                    <span class="badge bg-warning text-dark ms-2">Moto/Scooter</span>
                                                <?php elseif ($reservation['type'] === 'velo'): ?>
                                                    <span class="badge bg-info ms-2">Vélo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-dark ms-2"><?php echo ucfirst($reservation['type']); ?></span>
                                                <?php endif; ?>
                                            </td>
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
                                                <?php elseif (isset($payment) && $payment['status'] === 'en_attente'): ?>
                                                    <span class="badge bg-warning text-dark">En attente</span>
                                                <?php elseif (isset($payment) && $payment['status'] === 'annule'): ?>
                                                    <span class="badge bg-danger">Annulé</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Payé</span>
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

<!-- QR Code Generator et fonctions de copie -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const accessCode = '<?php echo htmlspecialchars($reservation['code_acces']); ?>';
    const guestToken = '<?php echo htmlspecialchars($reservation['guest_token']); ?>';

    // Fonction pour générer QR codes avec l'API QR Server
    function generateQRCode(text, containerId, color = '000000') {
        const container = document.getElementById(containerId);
        if (container && text) {
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(text)}&color=${color}&bgcolor=ffffff&margin=10`;
            const img = document.createElement('img');
            img.src = qrUrl;
            img.alt = 'QR Code: ' + text;
            img.style.width = '150px';
            img.style.height = '150px';
            img.style.border = '1px solid #ddd';
            img.style.borderRadius = '5px';

            img.onload = function() {
                console.log('✅ QR code généré pour le code d\'accès');
            };

            img.onerror = function() {
                console.error('❌ Erreur lors de la génération du QR code');
                container.innerHTML = '<div class="text-muted small">QR code indisponible</div>';
            };

            container.innerHTML = '';
            container.appendChild(img);
        }
    }

    // Générer le QR code pour le code d'accès
    if (accessCode) {
        generateQRCode(accessCode, 'qr-guest-access-code', '007bff');
    }
});

// Fonction pour copier le code d'accès
function copyAccessCode() {
    const accessCode = '<?php echo htmlspecialchars($reservation['code_acces']); ?>';
    if (navigator.clipboard) {
        navigator.clipboard.writeText(accessCode).then(function() {
            showCopySuccess('Code d\'accès copié !');
        }).catch(function() {
            fallbackCopy(accessCode);
        });
    } else {
        fallbackCopy(accessCode);
    }
}

// Fonction pour copier le token invité
function copyGuestToken() {
    const guestToken = '<?php echo htmlspecialchars($reservation['guest_token']); ?>';
    if (navigator.clipboard) {
        navigator.clipboard.writeText(guestToken).then(function() {
            showCopySuccess('Code de suivi copié !');
        }).catch(function() {
            fallbackCopy(guestToken);
        });
    } else {
        fallbackCopy(guestToken);
    }
}

// Fonction de fallback pour la copie
function fallbackCopy(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand('copy');
        showCopySuccess('Copié !');
    } catch (err) {
        console.error('Erreur lors de la copie:', err);
        alert('Impossible de copier automatiquement. Veuillez sélectionner et copier manuellement.');
    }
    document.body.removeChild(textArea);
}

// Fonction pour afficher le message de succès
function showCopySuccess(message) {
    // Créer une notification temporaire
    const notification = document.createElement('div');
    notification.className = 'alert alert-success position-fixed';
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 200px;';
    notification.innerHTML = `<i class="fas fa-check me-2"></i>${message}`;

    document.body.appendChild(notification);

    // Supprimer après 3 secondes
    setTimeout(function() {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}
</script>