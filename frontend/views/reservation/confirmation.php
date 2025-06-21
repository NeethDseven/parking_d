<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php
            // S'assurer que la variable $is_immediate est définie
            $is_immediate = isset($is_immediate) ? $is_immediate : false;

            // Déterminer le statut de la réservation pour l'affichage correct
            $status = isset($reservation['status']) ? strtolower($reservation['status']) : '';
            $status = str_replace(['é', 'è', 'ê'], 'e', $status);

            $cardClass = 'border-success';
            $headerClass = 'bg-success';
            $title = 'Réservation confirmée !';

            if ($is_immediate) {
                $cardClass = 'border-info';
                $headerClass = 'bg-info';
                $title = 'Réservation immédiate terminée !';
            } elseif (strpos($status, 'annul') !== false) {
                $cardClass = 'border-danger';
                $headerClass = 'bg-danger';
                $title = 'Réservation annulée';
            } elseif (strpos($status, 'expir') !== false) {
                $cardClass = 'border-dark';
                $headerClass = 'bg-dark';
                $title = 'Réservation expirée';
            } elseif (strpos($status, 'termin') !== false) {
                $cardClass = 'border-secondary';
                $headerClass = 'bg-secondary';
                $title = 'Réservation terminée';
            } elseif (strpos($status, 'en_cours') !== false) {
                $cardClass = 'border-info';
                $headerClass = 'bg-info';
                $title = 'Réservation en cours';
            }
            ?>
            <div class="card <?php echo $cardClass; ?> mb-4">
                <div class="card-header <?php echo $headerClass; ?> text-white">
                    <h1 class="h4 mb-0"><?php echo $title; ?></h1>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4"> <?php
                                                    // Afficher le bon message selon le statut de la réservation
                                                    if ($is_immediate):
                                                    ?>
                            <i class="fas fa-parking text-info fa-5x mb-3"></i>
                            <h2 class="h3">Merci pour votre stationnement !</h2>
                            <div class="alert alert-success" role="alert">
                                <h4><i class="fas fa-check-circle me-2"></i> Votre réservation immédiate est terminée et payée</h4>
                                <p><strong>Votre code de sortie a été généré avec succès.</strong> Utilisez ce code aux bornes de sortie pour quitter le parking.</p>
                                <?php if (isset($immediate_payment_info) && isset($immediate_payment_info['duree_minutes'])): ?>
                                    <p>
                                        <strong>Durée de stationnement :</strong>
                                        <?php
                                                            $minutes = $immediate_payment_info['duree_minutes'];
                                                            $hours = floor($minutes / 60);
                                                            $remainingMinutes = round($minutes % 60);

                                                            if ($hours > 0) {
                                                                echo $hours . ' heure' . ($hours > 1 ? 's' : '');
                                                                if ($remainingMinutes > 0) {
                                                                    echo ' et ' . $remainingMinutes . ' minute' . ($remainingMinutes > 1 ? 's' : '');
                                                                }
                                                            } else {
                                                                echo max(1, $remainingMinutes) . ' minute' . ($remainingMinutes > 1 ? 's' : '');
                                                            }
                                        ?>
                                    </p>
                                <?php elseif ($reservation['date_debut'] && $reservation['date_fin']): ?>
                                    <p>
                                        <strong>Durée de stationnement :</strong>
                                        <?php
                                                            $dateDebut = new DateTime($reservation['date_debut']);
                                                            $dateFin = new DateTime($reservation['date_fin']);
                                                            $dureeMinutes = ($dateFin->getTimestamp() - $dateDebut->getTimestamp()) / 60;
                                                            $hours = floor($dureeMinutes / 60);
                                                            $remainingMinutes = round($dureeMinutes % 60);

                                                            if ($hours > 0) {
                                                                echo $hours . ' heure' . ($hours > 1 ? 's' : '');
                                                                if ($remainingMinutes > 0) {
                                                                    echo ' et ' . $remainingMinutes . ' minute' . ($remainingMinutes > 1 ? 's' : '');
                                                                }
                                                            } else {
                                                                echo max(1, $remainingMinutes) . ' minute' . ($remainingMinutes > 1 ? 's' : '');
                                                            }
                                        ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php elseif (strpos($status, 'annul') !== false): ?>
                            <i class="fas fa-times-circle text-danger fa-5x mb-3"></i>
                            <h2 class="h3">Cette réservation a été annulée</h2>
                            <div class="alert alert-warning" role="alert">
                                <p>Cette réservation n'est plus active. Si vous souhaitez réserver à nouveau, veuillez retourner à la page des places.</p>
                                <a href="<?php echo BASE_URL; ?>home/places" class="btn btn-primary mt-2">Voir les places disponibles</a>
                            </div>
                            <!-- On n'affiche pas le code d'accès pour les réservations annulées -->
                        <?php elseif (strpos($status, 'expir') !== false): ?>
                            <i class="fas fa-hourglass-end text-dark fa-5x mb-3"></i>
                            <h2 class="h3">Cette réservation a expiré</h2>
                            <div class="alert alert-secondary" role="alert">
                                <p>Le délai de paiement pour cette réservation est écoulé. Veuillez effectuer une nouvelle réservation.</p>
                                <a href="<?php echo BASE_URL; ?>home/places" class="btn btn-primary mt-2">Voir les places disponibles</a>
                            </div>
                            <!-- On n'affiche pas le code d'accès pour les réservations expirées -->
                        <?php elseif (strpos($status, 'termin') !== false): ?>
                            <i class="fas fa-flag-checkered text-secondary fa-5x mb-3"></i>
                            <h2 class="h3">Réservation terminée</h2>
                            <div class="alert alert-info" role="alert">
                                <p>Cette réservation est terminée. Nous espérons que vous avez apprécié nos services.</p>
                            </div>
                        <?php elseif (strpos($status, 'en_cours') !== false): ?>
                            <i class="fas fa-play-circle text-info fa-5x mb-3"></i>
                            <h2 class="h3">Réservation actuellement en cours</h2>
                            <div class="alert alert-info" role="alert">
                                <p>Cette réservation est actuellement en cours d'utilisation.</p>
                            </div> <?php else: ?>
                            <i class="fas fa-check-circle text-success fa-5x mb-3"></i>
                            <h2 class="h3">Votre réservation a été confirmée avec succès</h2>

                            <!-- Afficher le code d'accès uniquement pour les réservations confirmées ou en cours -->
                            <p class="lead">Voici votre code d'accès :</p>
                            <div class="d-inline-block p-3 mb-3 border border-dark rounded code-box">
                                <span class="h2 mb-0"><?php echo $reservation['code_acces']; ?></span>
                            </div>

                            <!-- QR Code pour réservation classique -->
                            <div class="mt-4 mb-4">
                                <h5><i class="fas fa-qrcode me-2"></i>QR Code</h5>
                                <div class="d-flex justify-content-center">
                                    <div class="text-center">
                                        <div id="qr-classic-access-code" class="mx-auto mb-3" style="width: 150px; height: 150px;"></div>
                                        <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('<?php echo htmlspecialchars($reservation['code_acces']); ?>')" title="Copier le code">
                                            <i class="fas fa-copy me-1"></i> Copier le code
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <p>Veuillez conserver ce code, il vous sera demandé à l'entrée du parking.</p>
                            <?php endif; ?><?php if ($is_immediate && !empty($reservation['code_sortie'])): ?>
                            <div class="alert alert-warning" role="alert">
                                <h4><i class="fas fa-exclamation-triangle me-2"></i> Important</h4>
                                <p>Utilisez le code de sortie ci-dessous pour quitter le parking.</p>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0">Code d'entrée</h5>
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="d-inline-block p-3 border border-primary rounded code-box mb-3">
                                                <span class="h3 mb-0"><?php echo htmlspecialchars($reservation['code_acces']); ?></span>
                                            </div>
                                            <div id="qr-entry-code-confirm" class="mx-auto mb-3" style="width: 150px; height: 150px;"></div>
                                            <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('<?php echo htmlspecialchars($reservation['code_acces']); ?>')" title="Copier le code">
                                                <i class="fas fa-copy me-1"></i> Copier
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header bg-success text-white">
                                            <h5 class="mb-0">Code de sortie</h5>
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="d-inline-block p-3 border border-success rounded code-box mb-3">
                                                <span class="h3 mb-0"><?php echo htmlspecialchars($reservation['code_sortie']); ?></span>
                                            </div>
                                            <div id="qr-exit-code-confirm" class="mx-auto mb-3" style="width: 150px; height: 150px;"></div>
                                            <button class="btn btn-sm btn-outline-success" onclick="copyToClipboard('<?php echo htmlspecialchars($reservation['code_sortie']); ?>')" title="Copier le code">
                                                <i class="fas fa-copy me-1"></i> Copier
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <p class="alert alert-warning"><strong>Important :</strong> Veuillez conserver votre code de sortie, il vous sera demandé pour quitter le parking.</p>
                        <?php endif; ?>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><?php echo $is_immediate ? 'Récapitulatif de votre stationnement' : 'Détails de la réservation'; ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Numéro de réservation :</div>
                                <div class="col-md-8">#<?php echo str_pad($reservation['id'], 6, '0', STR_PAD_LEFT); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Place :</div>
                                <div class="col-md-8"><?php echo htmlspecialchars($reservation['numero']); ?> (<?php echo ucfirst($reservation['type']); ?>)</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold"><?php echo $is_immediate ? 'Heure d\'arrivée :' : 'Date et heure d\'arrivée :'; ?></div>
                                <div class="col-md-8"><?php echo date('d/m/Y à H:i', strtotime($reservation['date_debut'])); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold"><?php echo $is_immediate ? 'Heure de sortie :' : 'Date et heure de départ :'; ?></div>
                                <div class="col-md-8"><?php echo date('d/m/Y à H:i', strtotime($reservation['date_fin'])); ?></div>
                            </div>
                            <?php if ($is_immediate && isset($immediate_payment_info)): ?>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Durée de stationnement :</div>
                                    <div class="col-md-8">
                                        <?php
                                        $minutes = $immediate_payment_info['duree_minutes'];
                                        $hours = floor($minutes / 60);
                                        $remainingMinutes = $minutes % 60;

                                        if ($hours > 0) {
                                            echo $hours . ' heure' . ($hours > 1 ? 's' : '');
                                            if ($remainingMinutes > 0) {
                                                echo ' et ' . $remainingMinutes . ' minute' . ($remainingMinutes > 1 ? 's' : '');
                                            }
                                        } else {
                                            echo $remainingMinutes . ' minute' . ($remainingMinutes > 1 ? 's' : '');
                                        }
                                        ?>
                                    </div>
                                </div> <?php endif; ?>

                            <?php if (isset($subscription_benefits) && isset($final_amount)): ?>
                                <div class="alert alert-success mb-3">
                                    <i class="fas fa-star me-2"></i>
                                    <strong>Avantages Abonnement "<?php echo htmlspecialchars($subscription_benefits['name']); ?>" appliqués :</strong>
                                    <ul class="mb-0 mt-2">
                                        <li><?php echo intval($subscription_benefits['free_minutes']); ?> minutes gratuites</li>
                                        <li><?php echo number_format($subscription_benefits['discount_percent'], 0); ?>% de réduction</li>
                                    </ul>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Montant initial :</div>
                                    <div class="col-md-8 text-muted text-decoration-line-through"><?php echo number_format($original_amount, 2); ?> €</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Économies réalisées :</div>
                                    <div class="col-md-8 text-success">-<?php echo number_format($total_savings, 2); ?> €</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Montant payé :</div>
                                    <div class="col-md-8 text-success fw-bold"><?php echo number_format($final_amount, 2); ?> €</div>
                                </div>
                            <?php else: ?>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Montant payé :</div>
                                    <div class="col-md-8"><?php echo number_format($reservation['montant_total'], 2); ?> €</div>
                                </div>
                            <?php endif; ?><div class="row mb-2">
                                <div class="col-md-4 fw-bold">Statut du paiement :</div>
                                <div class="col-md-8">
                                    <?php if (strpos($status, 'annul') !== false): ?>
                                        <span class="badge bg-danger">Annulé</span>
                                    <?php elseif (strpos($status, 'expir') !== false): ?>
                                        <span class="badge bg-dark">Expiré</span>
                                    <?php elseif (isset($payment) && is_array($payment) && isset($payment['status']) && $payment['status'] === 'valide'): ?>
                                        <span class="badge bg-success">Payé</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">En attente</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><?php echo $is_immediate ? 'Informations de sortie' : 'Informations importantes'; ?></h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <?php if (!$is_immediate): ?>
                                    <li class="mb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i> <b>Adresse :</b> 123 Rue du Parking, 75000 Paris, France</li>
                                    <li class="mb-2"><i class="fas fa-clock text-primary me-2"></i> <b>Horaires d'ouverture :</b> 24h/24, 7j/7</li>
                                <?php endif; ?> <li class="mb-2"><i class="fas fa-phone text-primary me-2"></i> <b>Contact :</b> 01 23 45 67 89</li>

                                <?php if (!(strpos($status, 'annul') !== false || strpos($status, 'expir') !== false)): ?>
                                    <?php if ($is_immediate): ?>
                                        <li class="mb-2"><i class="fas fa-key text-danger me-2"></i> <b>Important :</b> N'oubliez pas votre code de sortie <b><?php echo $code_sortie; ?></b> pour pouvoir quitter le parking.</li>
                                    <?php else: ?>
                                        <li class="mb-2"><i class="fas fa-key text-danger me-2"></i> <b>Important :</b> Votre code d'accès <b><?php echo $reservation['code_acces']; ?></b> est nécessaire pour entrer dans le parking.</li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($is_immediate): ?>
                                    <li><i class="fas fa-info-circle text-primary me-2"></i> <b>Note :</b> En cas de problème pour sortir du parking, veuillez contacter notre service client au numéro ci-dessus.</li>
                                <?php else: ?>
                                    <li><i class="fas fa-info-circle text-primary me-2"></i> <b>Note :</b> En cas de problème pour accéder au parking, veuillez contacter notre service client au numéro ci-dessus.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="javascript:window.print();" class="btn btn-outline-dark me-2"><i class="fas fa-print me-2"></i> Imprimer la confirmation</a>
                        <a href="<?php echo BASE_URL; ?>auth/profile" class="btn btn-primary">Voir mes réservations</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles gérés par la structure CSS optimisée -->

<!-- QR Code Generator avec API QR Server -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Informations de base
        const isImmediate = <?php echo $is_immediate ? 'true' : 'false'; ?>;
        const entryCode = '<?php echo !empty($reservation['code_acces']) ? htmlspecialchars($reservation['code_acces']) : ''; ?>';
        const exitCode = '<?php echo !empty($reservation['code_sortie']) ? htmlspecialchars($reservation['code_sortie']) : ''; ?>';

        // Fonction pour générer QR codes avec l'API QR Server
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

        if (isImmediate && entryCode && exitCode) {
            console.log('✅ Génération des QR codes pour réservation immédiate');

            // QR codes pour réservation immédiate
            generateQRCode(entryCode, 'qr-entry-code-confirm', '007bff');
            generateQRCode(exitCode, 'qr-exit-code-confirm', '198754');
        } else if (entryCode && !isImmediate) {
            console.log('✅ Génération du QR code pour réservation classique');

            // QR code pour réservation classique
            generateQRCode(entryCode, 'qr-classic-access-code', '007bff');
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