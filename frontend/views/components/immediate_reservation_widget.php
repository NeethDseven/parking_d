<div class="immediate-reservation-widget">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-stopwatch me-2"></i>Réservation en cours</h5>
        </div>
        <div class="card-body p-3">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-3">Temps Écoulé</h6>
                    <div class="display-6 mb-2" id="timer" data-start-time="<?php echo $reservation['date_debut']; ?>" data-tarif="<?php echo $tarifHoraire; ?>">00:00:00</div>
                    <p class="text-muted small">Heure de début: <?php echo date('d/m/Y H:i:s', strtotime($reservation['date_debut'])); ?></p>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-3">Coût Estimé</h6>
                    <table class="table table-sm">
                        <tr>
                            <td>Durée actuelle:</td>
                            <td class="text-end timer-dependent-element" id="current-duration" data-target="duration">--:--:--</td>
                        </tr>
                        <tr>
                            <td>Tarif horaire:</td>
                            <td class="text-end"><?php echo number_format($tarifHoraire, 2); ?> €/h</td>
                        </tr>
                        <tr class="table-light fw-bold">
                            <td>Montant estimé:</td>
                            <td class="text-end timer-dependent-element" id="estimated-cost" data-target="cost">0.00 €</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="d-grid gap-2 mt-3">
                <a href="<?php echo BASE_URL; ?>reservation/immediate/<?php echo $reservation['id']; ?>" class="btn btn-info">
                    <i class="fas fa-info-circle me-2"></i>Détails
                </a>
                <form action="<?php echo BASE_URL; ?>reservation/endImmediate" method="post" class="d-grid">
                    <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir terminer cette réservation ? Vous devrez procéder au paiement pour quitter le parking.');">
                        <i class="fas fa-stop-circle me-2"></i>Terminer et payer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>