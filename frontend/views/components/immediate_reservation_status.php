<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <?php
            // Vérifier si l'utilisateur a une réservation immédiate active
            $activeReservationData = getActiveImmediateReservation();
            if (isset($_SESSION['user']) && $activeReservationData['reservation']):
                $reservation = $activeReservationData['reservation'];
                $place = $activeReservationData['place'];
                $tarifHoraire = $activeReservationData['tarifHoraire'];
            ?>
                <!-- Widget de suivi de réservation immédiate -->
                <div class="immediate-reservation-widget">
                    <div class="card">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-stopwatch me-2"></i>Réservation immédiate en cours</h5>
                            <span class="badge bg-warning text-dark">Place <?php echo $place['numero']; ?></span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="mb-2">Temps Écoulé</h6>
                                    <div class="h3 mb-0" id="timer" data-start-time="<?php echo $reservation['date_debut']; ?>" data-tarif="<?php echo $tarifHoraire; ?>">00:00:00</div>
                                    <p class="text-muted small mb-3">Début: <?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="mb-2">Coût Estimé</h6>
                                    <div class="h3 mb-0 text-danger" id="estimated-cost">0.00 €</div>
                                    <p class="text-muted small mb-3">Tarif: <?php echo number_format($tarifHoraire, 2); ?> €/h</p>
                                </div>
                                <div class="col-md-4 d-flex align-items-center">
                                    <div class="d-grid gap-2 w-100">
                                        <a href="<?php echo BASE_URL; ?>reservation/immediate/<?php echo $reservation['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-info-circle me-2"></i>Détails
                                        </a>
                                        <form action="<?php echo BASE_URL; ?>reservation/endImmediate" method="post" class="d-grid">
                                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir terminer cette réservation ?');">
                                                <i class="fas fa-stop-circle me-2"></i>Terminer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>