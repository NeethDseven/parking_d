<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0"><i class="fas fa-search me-2"></i> Suivi de votre réservation</h1>
                </div>
                <div class="card-body">
                    <div class="mb-4 p-3 border rounded bg-light">
                        <h5>Informations de réservation</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Nom :</th>
                                        <td><?php echo htmlspecialchars($reservation['guest_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email :</th>
                                        <td><?php echo htmlspecialchars($reservation['guest_email']); ?></td>
                                    </tr>
                                    <?php if (!empty($reservation['guest_phone'])): ?>
                                        <tr>
                                            <th>Téléphone :</th>
                                            <td><?php echo htmlspecialchars($reservation['guest_phone']); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Réservation créée le :</th>
                                        <td><?php echo date('d/m/Y H:i', strtotime($reservation['created_at'])); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Numéro de réservation :</th>
                                        <td>#<?php echo str_pad($reservation['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Statut :</th>
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
                                    <tr>
                                        <th>Montant total :</th>
                                        <td><?php echo number_format($reservation['montant_total'], 2); ?> €</td>
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
                                                <span class="badge bg-secondary">Non payé</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Détails de la place</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th>Numéro de place :</th>
                                            <td><?php echo htmlspecialchars($reservation['numero']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Type :</th>
                                            <td>
                                                <?php if ($reservation['type'] === 'standard'): ?>
                                                    <span class="badge bg-secondary">Standard</span>
                                                <?php elseif ($reservation['type'] === 'handicape'): ?>
                                                    <span class="badge bg-primary">PMR</span>
                                                <?php elseif ($reservation['type'] === 'electrique'): ?>
                                                    <span class="badge bg-success">Électrique</span>
                                                <?php elseif ($reservation['type'] === 'moto/scooter'): ?>
                                                    <span class="badge bg-warning text-dark">Moto/Scooter</span>
                                                <?php elseif ($reservation['type'] === 'velo'): ?>
                                                    <span class="badge bg-info">Vélo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-dark"><?php echo ucfirst($reservation['type']); ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th>Date et heure d'arrivée :</th>
                                            <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Date et heure de départ :</th>
                                            <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($reservation['status'] === 'confirmée'): ?>
                        <div class="card mb-4 border-success">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">Code d'accès</h5>
                            </div>
                            <div class="card-body text-center">
                                <p>Utilisez ce code pour accéder au parking :</p>
                                <div class="d-inline-block p-3 mb-3 border border-dark rounded code-box">
                                    <span class="h2 mb-0"><?php echo $reservation['code_acces']; ?></span>
                                </div>

                                <!-- QR Code -->
                                <div class="mt-3 mb-3">
                                    <div id="qr-guest-tracking-code" class="mx-auto mb-3" style="width: 150px; height: 150px;"></div>
                                    <button class="btn btn-sm btn-outline-primary" onclick="copyAccessCodeTracking()" title="Copier le code d'accès">
                                        <i class="fas fa-copy me-1"></i> Copier le code
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($reservation['status'] === 'en_attente'): ?>
                        <div class="card mb-4 border-warning">
                            <div class="card-header bg-warning">
                                <h5 class="mb-0">Paiement en attente</h5>
                            </div>
                            <div class="card-body">
                                <p>Votre réservation est en attente de paiement. Pour finaliser votre réservation, veuillez cliquer sur le bouton ci-dessous :</p>
                                <div class="text-center">
                                    <a href="<?php echo BASE_URL; ?>reservation/guestPayment/<?php echo $reservation['id']; ?>/<?php echo $reservation['guest_token']; ?>" class="btn btn-primary">
                                        <i class="fas fa-credit-card me-2"></i> Procéder au paiement
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2">
                        <?php if ($reservation['status'] !== 'annulée' && strtotime($reservation['date_debut']) > time()): ?>
                            <a href="<?php echo BASE_URL; ?>reservation/cancelGuestReservation/<?php echo $reservation['id']; ?>/<?php echo $reservation['guest_token']; ?>"
                                class="btn btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                <i class="fas fa-times-circle me-2"></i> Annuler la réservation
                            </a>
                        <?php endif; ?>

                        <a href="javascript:window.print();" class="btn btn-outline-secondary">
                            <i class="fas fa-print me-2"></i> Imprimer les détails
                        </a>

                        <a href="<?php echo BASE_URL; ?>home/reservationTracking" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i> Retour à la page de suivi
                        </a>
                    </div>

                    <div class="alert alert-success mt-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-user-plus fa-2x"></i>
                            </div>
                            <div>
                                <h5>Créez un compte pour vos prochaines réservations !</h5>
                                <p class="mb-0">Créez un compte pour accéder à toutes les fonctionnalités et gérer facilement vos réservations futures.</p>
                                <a href="<?php echo BASE_URL; ?>auth/register" class="btn btn-success btn-sm mt-2">
                                    <i class="fas fa-user-plus me-2"></i> Créer un compte
                                </a>
                            </div>
                        </div>
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

    // Générer le QR code pour le code d'accès si la réservation est confirmée
    if (accessCode && '<?php echo $reservation['status']; ?>' === 'confirmée') {
        generateQRCode(accessCode, 'qr-guest-tracking-code', '007bff');
    }
});

// Fonction pour copier le code d'accès
function copyAccessCodeTracking() {
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