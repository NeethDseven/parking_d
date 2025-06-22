<?php
// Vérifier que les données nécessaires sont présentes
if (!isset($reservation) || !isset($place) || !isset($tarifHoraire)) {
    echo "Erreur: Données manquantes.";
    exit;
}
?>

<div class="container py-5" style="background: transparent !important;">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-stopwatch me-2"></i>Réservation Immédiate en Cours</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i> Votre réservation immédiate est en cours. Le chronométrage est actif et le montant sera calculé à la fin de votre stationnement.
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Détails de la Place</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Numéro de place
                                    <span class="badge bg-primary rounded-pill"><?php echo htmlspecialchars($place['numero']); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Type de place
                                    <span class="badge bg-secondary rounded-pill"><?php echo ucfirst(htmlspecialchars($place['type'])); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Tarif horaire
                                    <span class="badge bg-success rounded-pill"><?php echo number_format($tarifHoraire, 2); ?> €/h</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-key me-2"></i>Code d'Accès</h5>
                            <div class="p-3 bg-light rounded text-center">
                                <?php if (!empty($reservation['code_acces'])): ?>
                                    <span class="display-4 fw-bold text-primary"><?php echo htmlspecialchars($reservation['code_acces']); ?></span>
                                    <p class="text-muted mt-2">Utilisez ce code pour accéder au parking</p>
                                    <div class="mt-3">
                                        <div id="qr-access-code" class="mx-auto" style="width: 150px; height: 150px;"></div>
                                    </div>
                                    <button class="btn btn-sm btn-outline-secondary mt-2" onclick="copyToClipboard('<?php echo htmlspecialchars($reservation['code_acces']); ?>')" title="Copier le code">
                                        <i class="fas fa-copy me-1"></i> Copier
                                    </button>
                                <?php else: ?>
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="spinner-border text-primary mb-2" role="status">
                                            <span class="visually-hidden">Chargement...</span>
                                        </div>
                                        <span class="text-primary fw-bold">Génération du code...</span>
                                        <p class="text-muted mt-2 small">Veuillez patientez quelques instants</p>
                                        <button class="btn btn-sm btn-primary mt-2" onclick="location.reload()" title="Actualiser">
                                            <i class="fas fa-refresh me-1"></i> Actualiser
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div><!-- Chronomètre -->
                    <div class="text-center mb-5 py-4">
                        <h4>Temps Écoulé</h4>
                        <div class="display-1" id="timer">
                            <span id="duration-display">00:00:00</span>
                        </div>
                        <p class="text-muted">Heure de début: <?php echo date('d/m/Y H:i:s', strtotime($reservation['date_debut'])); ?></p>
                    </div> <!-- Estimation du coût - MASQUÉE -->
                    <div class="card bg-light mb-4" style="display: none;">
                        <div class="card-body">
                            <h5 class="card-title">Coût Estimé</h5> <?php if (isset($subscriptionBenefits) && $subscriptionBenefits): ?>
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-star me-2"></i>
                                    <strong>Avantages Abonnement "<?php echo htmlspecialchars($subscriptionBenefits['name'] ?? $subscriptionBenefits['nom'] ?? 'Inconnu'); ?>" :</strong>
                                    <ul class="mb-0 mt-2">
                                        <li><?php echo intval($subscriptionBenefits['free_minutes']); ?> minutes gratuites</li>
                                        <li><?php echo number_format($subscriptionBenefits['discount_percent'], 0); ?>% de réduction</li>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-2">Durée actuelle:</p>
                                    <p class="mb-2">Tarif horaire:</p>
                                    <?php if (isset($subscriptionBenefits) && $subscriptionBenefits): ?>
                                        <p class="mb-2 text-success">Minutes gratuites:</p>
                                        <p class="mb-2 text-success">Réduction:</p>
                                    <?php endif; ?>
                                    <p class="mb-0 fw-bold">Montant estimé:</p>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="mb-2"><span id="duration-text">--:--:--</span></p>
                                    <p class="mb-2"><?php echo number_format($tarifHoraire, 2); ?> €/h</p>
                                    <?php if (isset($subscriptionBenefits) && $subscriptionBenefits): ?>
                                        <p class="mb-2 text-success"><?php echo intval($subscriptionBenefits['free_minutes']); ?> min</p>
                                        <p class="mb-2 text-success">-<?php echo number_format($subscriptionBenefits['discount_percent'], 0); ?>%</p>
                                    <?php endif; ?>
                                    <p class="mb-0 fw-bold"><span id="cost-display">0.00 €</span></p>
                                </div>
                            </div>
                        </div>
                    </div><!-- Formulaire pour terminer la réservation -->
                    <form id="endReservationForm" action="<?php echo BASE_URL; ?>reservation/endImmediate" method="post">
                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger btn-lg" id="endReservationBtn">
                                <i class="fas fa-stop-circle me-2"></i>Terminer et procéder au paiement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script de tracking géré par unifiedReservationManager.js -->
<script src="<?php echo BASE_URL; ?>frontend/assets/js/components/unifiedReservationManager.js"></script>
<!-- QR Code Generator - Alternative avec QR Server API -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🔄 Initialisation du timer de réservation immédiate'); // Fonction de mise à jour du timer
        function updateTimer() {
            const startTimeStr = '<?php echo $reservation['date_debut']; ?>';
            const tarif = <?php echo $tarifHoraire; ?>;
            const freeMinutes = <?php echo isset($subscriptionBenefits['free_minutes']) ? intval($subscriptionBenefits['free_minutes']) : 0; ?>;
            const discountPercent = <?php echo isset($subscriptionBenefits['discount_percent']) ? floatval($subscriptionBenefits['discount_percent']) : 0; ?>;

            if (!startTimeStr) {
                console.warn('⚠️ Pas de date de début trouvée');
                return;
            }

            const startTime = new Date(startTimeStr);
            const now = new Date();
            const elapsedMs = now - startTime;

            if (elapsedMs < 0) {
                console.warn('⚠️ La réservation n\'a pas encore commencé');
                return;
            }

            const elapsedMinutes = Math.floor(elapsedMs / 60000);
            const elapsedHours = Math.floor(elapsedMinutes / 60);
            const remainingMinutes = elapsedMinutes % 60;
            const elapsedSeconds = Math.floor((elapsedMs % 60000) / 1000);

            // Appliquer les minutes gratuites
            const billedMinutes = Math.max(0, elapsedMinutes - freeMinutes);

            // Calculer le coût avec un minimum de 15 minutes facturables (après déduction des minutes gratuites)
            const finalBilledMinutes = Math.max(billedMinutes, elapsedMinutes > freeMinutes ? 15 : 0);
            let cost = (finalBilledMinutes / 60) * tarif;

            // Appliquer la réduction d'abonnement
            if (discountPercent > 0) {
                cost = cost * (1 - (discountPercent / 100));
            }

            // Format d'affichage : HH:MM:SS
            const timeString = String(elapsedHours).padStart(2, '0') + ':' +
                String(remainingMinutes).padStart(2, '0') + ':' +
                String(elapsedSeconds).padStart(2, '0');

            // Mettre à jour les éléments d'affichage
            const durationDisplays = document.querySelectorAll('#duration-display, #duration-text');
            const costDisplays = document.querySelectorAll('#cost-display');

            durationDisplays.forEach(target => {
                if (target) target.textContent = timeString;
            });

            costDisplays.forEach(target => {
                if (target) target.textContent = cost.toFixed(2) + ' €';
            });

            // Debug simplifié (seulement toutes les 10 secondes pour éviter le spam)
            if (elapsedSeconds % 10 === 0) {
                console.log(`⏱️ Timer: ${timeString}, Minutes facturées: ${finalBilledMinutes}, Coût avec avantages: ${cost.toFixed(2)} €`);
            }
        }

        // Initialiser le gestionnaire de réservations si disponible
        if (typeof window.app !== 'undefined' && window.app.reservationManager) {
            window.app.reservationManager.setupReservationTracking();
            console.log('✅ Tracking de réservation via app.reservationManager');
        } else {
            console.log('⚠️ app.reservationManager non disponible, utilisation du fallback');
        } // Démarrer le timer (fallback garanti)
        updateTimer(); // Mise à jour immédiate
        window.timerInterval = setInterval(updateTimer, 1000); // Mise à jour chaque seconde

        // Fonction pour afficher la confirmation de terminaison
        function displayReservationComplete(data) {
            // Remplacer le contenu de la carte par la confirmation
            const cardBody = document.querySelector('.card-body');
            const cardHeader = document.querySelector('.card-header h3');

            // Changer le titre
            cardHeader.innerHTML = '<i class="fas fa-check-circle me-2"></i>Réservation Terminée';
            cardHeader.parentElement.className = 'card-header bg-success text-white';

            // Créer le contenu de confirmation
            const confirmationHtml = `
            <div class="alert alert-success mb-4">
                <i class="fas fa-check-circle me-2"></i> Votre réservation immédiate a été terminée avec succès !
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Résumé de la Réservation</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Durée totale
                            <span class="badge bg-primary rounded-pill">${Math.ceil(data.duree_minutes)} minutes</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Montant total
                            <span class="badge bg-success rounded-pill">${data.montant} €</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Paiement requis
                            <span class="badge ${data.requires_payment ? 'bg-warning' : 'bg-success'} rounded-pill">
                                ${data.requires_payment ? 'Oui' : 'Non'}
                            </span>
                        </li>
                    </ul>
                </div>                <div class="col-md-6">
                    <h5>Code de Sortie</h5>
                    <div class="p-3 bg-light rounded text-center">
                        ${data.requires_payment ? `
                            <span class="display-4 fw-bold text-muted">---</span>
                            <p class="text-muted mt-2">Disponible après paiement</p>
                        ` : `
                            <span class="display-4 fw-bold">${data.code_sortie || 'N/A'}</span>
                            <p class="text-muted mt-2">Utilisez ce code pour quitter le parking</p>
                        `}
                    </div>
                </div>
            </div>
            
            ${data.requires_payment ? `
                <div class="alert alert-warning mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i> 
                    Un paiement de <strong>${data.montant} €</strong> est requis pour quitter le parking.
                </div>
                
                <div class="d-grid gap-2">
                    <a href="${data.redirect_url}" class="btn btn-warning btn-lg">
                        <i class="fas fa-credit-card me-2"></i>Procéder au Paiement
                    </a>
                </div>
            ` : `
                <div class="alert alert-success mb-4">
                    <i class="fas fa-check-circle me-2"></i> 
                    Aucun paiement requis. Vous pouvez quitter le parking avec votre code de sortie.
                </div>
                
                <div class="d-grid gap-2">
                    <a href="<?php echo BASE_URL; ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-home me-2"></i>Retour à l'Accueil
                    </a>
                </div>
            `}
        `;

            cardBody.innerHTML = confirmationHtml;
        }
        // Gérer la terminaison de réservation
        const endForm = document.getElementById('endReservationForm');
        const endBtn = document.getElementById('endReservationBtn');

        console.log('🔧 Configuration du gestionnaire de terminaison');
        console.log('📋 Formulaire:', endForm);
        console.log('🔘 Bouton:', endBtn);

        if (endForm && endBtn) {
            endForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('🚀 Événement de soumission déclenché');

                if (!confirm('Êtes-vous sûr de vouloir terminer cette réservation ? Vous devrez procéder au paiement pour quitter le parking.')) {
                    console.log('❌ Confirmation annulée par l\'utilisateur');
                    return;
                }

                console.log('✅ Confirmation acceptée, début de la terminaison...');

                // Désactiver le bouton pendant le traitement
                endBtn.disabled = true;
                endBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Terminaison en cours...';

                const formData = new FormData(endForm);
                console.log('📤 Données du formulaire:', {
                    reservation_id: formData.get('reservation_id'),
                    action: endForm.action
                });

                fetch(endForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        console.log('📊 Statut de la réponse:', response.status, response.statusText);

                        if (!response.ok) {
                            throw new Error(`Erreur HTTP: ${response.status} ${response.statusText}`);
                        }

                        return response.json();
                    }).then(data => {
                        console.log('✅ Réponse de terminaison reçue:', data);
                        if (data.success) {
                            // Arrêter le timer
                            clearInterval(window.timerInterval);
                            console.log('⏱️ Timer arrêté');

                            if (data.requires_payment) {
                                // Si un paiement est requis, rediriger vers la page de paiement
                                console.log('💳 Redirection vers le paiement:', data.redirect_url);
                                console.log('🔄 Redirection en cours...'); // Forcer la redirection avec un petit délai pour s'assurer que les logs sont visibles
                                setTimeout(() => {
                                    console.log('🚀 Execution de window.location.replace =', data.redirect_url);
                                    try {
                                        window.location.replace(data.redirect_url);
                                    } catch (e) {
                                        console.warn('🔄 Fallback: window.location.href');
                                        window.location.href = data.redirect_url;
                                    }
                                }, 100);
                            } else {
                                // Si pas de paiement requis, rediriger vers la confirmation
                                console.log('✅ Redirection vers la confirmation:', data.redirect_url);
                                console.log('🔄 Redirection en cours...');
                                setTimeout(() => {
                                    console.log('🚀 Execution de window.location.replace =', data.redirect_url);
                                    try {
                                        window.location.replace(data.redirect_url);
                                    } catch (e) {
                                        console.warn('🔄 Fallback: window.location.href');
                                        window.location.href = data.redirect_url;
                                    }
                                }, 100);
                            }
                        } else {
                            console.error('❌ Erreur dans la réponse:', data.error);
                            alert('Erreur: ' + (data.error || 'Erreur inconnue'));
                            endBtn.disabled = false;
                            endBtn.innerHTML = '<i class="fas fa-stop-circle me-2"></i>Terminer et procéder au paiement';
                        }
                    })
                    .catch(error => {
                        console.error('💥 Erreur de connexion:', error);
                        alert('Erreur de connexion: ' + error.message);
                        endBtn.disabled = false;
                        endBtn.innerHTML = '<i class="fas fa-stop-circle me-2"></i>Terminer et procéder au paiement';
                    });
            });
        } else {
            console.warn('⚠️ Formulaire ou bouton de terminaison non trouvé');
            console.log('Formulaire trouvé:', !!endForm);
            console.log('Bouton trouvé:', !!endBtn);
        }
        console.log('✅ Timer de réservation immédiate démarré'); // Générer les QR codes si le code d'accès est disponible
        const accessCode = '<?php echo !empty($reservation['code_acces']) ? htmlspecialchars($reservation['code_acces']) : ''; ?>';
        const reservationId = <?php echo $reservation['id']; ?>;
        if (accessCode) {
            console.log('✅ Code d\'accès disponible:', accessCode);

            // Générer QR codes avec l'API QR Server
            function generateQRCode(text, containerId, color = '000000') {
                const container = document.getElementById(containerId);
                if (container) {
                    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(text)}&color=${color}&bgcolor=ffffff&margin=10`;
                    const img = document.createElement('img');
                    img.src = qrUrl;
                    img.alt = 'QR Code: ' + text;
                    img.style.width = '150px';
                    img.style.height = '150px';
                    img.style.border = '1px solid #ddd';
                    img.style.borderRadius = '5px';

                    img.onload = function() {
                        console.log('✅ QR code généré pour:', containerId);
                    };

                    img.onerror = function() {
                        console.error('❌ Erreur lors de la génération du QR code pour:', containerId);
                        container.innerHTML = '<div class="text-muted small">QR code indisponible</div>';
                    };

                    container.innerHTML = '';
                    container.appendChild(img);
                }
            }

            // Générer le QR code d'accès uniquement
            generateQRCode(accessCode, 'qr-access-code', '007bff');
        } else if (!accessCode) {
            console.log('⏳ Code d\'accès non disponible, tentative de génération...');

            // Tenter de générer le code d'accès manquant
            fetch('<?php echo BASE_URL; ?>reservation/generateAccessCode', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'reservation_id=' + encodeURIComponent(reservationId)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('✅ Code d\'accès généré:', data.code_acces);
                        // Recharger la page pour afficher le nouveau code
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    } else {
                        console.error('❌ Erreur lors de la génération du code:', data.error);
                    }
                })
                .catch(error => {
                    console.error('💥 Erreur de connexion lors de la génération:', error);
                });
        }

        // Fonction pour copier dans le presse-papiers
        window.copyToClipboard = function(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Créer une notification temporaire
                const notification = document.createElement('div');
                notification.className = 'alert alert-success position-fixed';
                notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 250px;';
                notification.innerHTML = '<i class="fas fa-check me-2"></i>Code copié dans le presse-papiers !';
                document.body.appendChild(notification);

                // Supprimer la notification après 2 secondes
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 2000);
            }).catch(function(err) {
                console.error('Erreur lors de la copie:', err);
                alert('Code: ' + text + '\n\nCopiez ce code manuellement.');
            });
        };
    });
</script>