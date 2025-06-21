<style>
    /* Styles personnalisés pour les types de places */
    .bg-moto-scooter {
        background-color: #8a2be2 !important;
        /* Couleur violette pour moto/scooter */
        color: white !important;
    }

    .bg-standard {
        background-color: #6c757d !important;
        /* Gris (bg-secondary) pour standard */
        color: white !important;
    }

    /* Styles pour l'affichage des cartes de places */
    .place-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .place-card:hover {
        transform: translateY(-5px);
    }

    .place-card .card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .place-card .card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .place-card-image {
        height: 200px;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .place-card-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.1));
    }

    .place-card .card-header {
        position: relative;
        z-index: 2;
    }

    .btn-reserve,
    .btn-reserve-immediate {
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
        width: 100%;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        cursor: pointer;
    }

    .btn-reserve {
        background: linear-gradient(45deg, #007bff, #0056b3);
        color: white;
        margin-bottom: 8px;
    }

    .btn-reserve:hover {
        background: linear-gradient(45deg, #0056b3, #004085);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        color: white;
    }

    .btn-reserve-immediate {
        background: linear-gradient(45deg, #28a745, #1e7e34);
        color: white;
    }

    .btn-reserve-immediate:hover {
        background: linear-gradient(45deg, #1e7e34, #155724);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        color: white;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .place-card-image {
            height: 150px;
        }
    }

    /* Animation fade-in pour les cartes */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .place-card {
        animation: fadeInUp 0.6s ease forwards;
    }
</style>

<div class="places-page-background">
    <div class="container">
        <h1 class="mb-4 text-center animate-fade-in">Places disponibles</h1>
        <div class="row mb-5 justify-content-center animate-on-scroll animated">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 hover-card animate-on-scroll animated">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-car me-2"></i>Places actuellement disponibles</h5>
                                <p class="card-text">
                                    <?php
                                    // Initialisation par défaut des variables
                                    $available_count = isset($total_places_available) ? $total_places_available : 0;
                                    $total = isset($total_places) ? $total_places : 0;
                                    ?>
                                    <span class="fs-1 fw-bold text-success" id="available-count"><?php echo $available_count; ?></span> /
                                    <span class="fs-4" id="total-count"><?php echo $total; ?></span>
                                </p>
                                <div class="progress progress-slim">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: <?php echo ($total > 0) ? ($available_count / $total * 100) : 0; ?>%"
                                        aria-valuenow="<?php echo $available_count; ?>"
                                        aria-valuemin="0"
                                        aria-valuemax="<?php echo $total; ?>"
                                        id="places-progress">
                                    </div>
                                </div>
                                <small class="text-muted mt-2 d-block"><?php echo $available_count; ?> places disponibles sur <?php echo $total; ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 hover-card animate-on-scroll animated">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-filter me-2"></i>Filtrer les places</h5>
                                <div class="mb-3">
                                    <label for="type-filter" class="form-label">Type de place :</label> <select class="form-select custom-select-places" id="type-filter">
                                        <option value="all" <?php echo (!isset($selected_type) || $selected_type === 'all') ? 'selected' : ''; ?>>Tous les types</option>
                                        <option value="standard" <?php echo (isset($selected_type) && $selected_type === 'standard') ? 'selected' : ''; ?>>Standard</option>
                                        <option value="handicape" <?php echo (isset($selected_type) && $selected_type === 'handicape') ? 'selected' : ''; ?>>Handicapé</option>
                                        <option value="electrique" <?php echo (isset($selected_type) && $selected_type === 'electrique') ? 'selected' : ''; ?>>Électrique</option>
                                        <option value="moto/scooter" <?php echo (isset($selected_type) && $selected_type === 'moto/scooter') ? 'selected' : ''; ?>>Moto/Scooter</option>
                                        <option value="velo" <?php echo (isset($selected_type) && $selected_type === 'velo') ? 'selected' : ''; ?>>Vélo</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="show-fees">
                                        <label class="form-check-label" for="show-fees">
                                            Afficher les tarifs
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- Section des tarifs -->
        <div class="tarifs-info mb-5 animate-on-scroll animated hidden" style="display: none;">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card hover-card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Grille tarifaire</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($tarifs as $type => $tarif): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100 <?php
                                                                if ($type === 'handicape') {
                                                                    echo 'border-warning';
                                                                } elseif ($type === 'electrique') {
                                                                    echo 'border-success';
                                                                } elseif ($type === 'moto' || $type === 'moto/scooter') {
                                                                    echo 'border-warning';
                                                                } elseif ($type === 'velo') {
                                                                    echo 'border-info';
                                                                } else {
                                                                    echo 'border-secondary';
                                                                }
                                                                ?>">
                                            <div class="card-header <?php
                                                                    if ($type === 'handicape') {
                                                                        echo 'bg-warning text-dark';
                                                                    } elseif ($type === 'electrique') {
                                                                        echo 'bg-success text-white';
                                                                    } elseif ($type === 'moto' || $type === 'moto/scooter') {
                                                                        echo 'bg-moto-scooter'; // Couleur violette personnalisée
                                                                    } elseif ($type === 'velo') {
                                                                        echo 'bg-info text-white';
                                                                    } else {
                                                                        echo 'bg-standard'; // Gris standard personnalisé
                                                                    }
                                                                    ?>">
                                                Place <?php echo ucfirst($type); ?> <?php if ($type === 'handicape'): ?>
                                                    <i class="fas fa-wheelchair float-end"></i>
                                                <?php elseif ($type === 'electrique'): ?>
                                                    <i class="fas fa-charging-station float-end"></i>
                                                <?php elseif ($type === 'moto' || $type === 'moto/scooter'): ?>
                                                    <i class="fas fa-motorcycle float-end"></i>
                                                <?php elseif ($type === 'velo'): ?>
                                                    <i class="fas fa-bicycle float-end"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-car float-end"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        Tarif horaire
                                                        <span class="badge bg-primary rounded-pill"><?php echo number_format($tarif['prix_heure'], 2); ?> €</span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        Tarif journalier
                                                        <span class="badge bg-primary rounded-pill"><?php echo number_format($tarif['prix_journee'], 2); ?> €</span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        Abonnement mensuel
                                                        <span class="badge bg-primary rounded-pill"><?php echo number_format($tarif['prix_mois'], 2); ?> €</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- Indicateur de chargement -->
        <div id="loading-spinner" class="text-center py-5 mb-3 d-none animate-on-scroll animated hidden" style="display: none;">
            <div class="spinner-border text-primary spinner-large" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <p class="mt-3 text-primary">Chargement des places disponibles...</p>
        </div>

        <!-- Container des places avec disposition 3 par ligne -->
        <div class="row justify-content-center animate-on-scroll" id="places-container"
            data-current-page="<?php echo isset($current_page) ? $current_page : 1; ?>"
            data-selected-type="<?php echo $selected_type ?? 'all'; ?>">
            <?php
            // Vérifier que $places et $tarifs sont bien définis
            if (isset($places) && !empty($places) && isset($tarifs)):
                // Pagination côté serveur : 6 places par page
                $placesPerPage = 6;
                $currentPage = isset($current_page) ? $current_page : 1;
                $startIndex = ($currentPage - 1) * $placesPerPage;
                $paginatedPlaces = array_slice($places, $startIndex, $placesPerPage);

                foreach ($paginatedPlaces as $key => $place):
                    // Vérifier que le type de place existe dans le tableau des tarifs
                    $tarifFloat = isset($tarifs[$place['type']]['prix_heure']) ?
                        floatval($tarifs[$place['type']]['prix_heure']) : 0;
            ?>
                    <!-- Carte de place avec col-lg-4 pour 3 cartes par ligne sur desktop -->
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4 place-card animate-on-scroll fade-in"
                        data-type="<?php echo htmlspecialchars($place['type']); ?>"
                        style="--card-index: <?php echo $key; ?>;">
                        <div class="card h-100 shadow-sm hover-effect">
                            <!-- Image différente selon le type de place -->
                            <?php
                            $placeImage = '';
                            if ($place['type'] === 'standard') {
                                // Sélectionner une image aléatoire parmi les 7 disponibles pour les places standard
                                $standardImages = [
                                    'parking-propre.webp',
                                    'download.webp',
                                    'download-1.webp',
                                    'istockphoto-182490567-612x612.webp',
                                    'standard1.webp',
                                    'standard2.webp',
                                    'standard3.webp',
                                    'empty-underground-parking-garage (4).jpg',
                                    'empty-underground-parking-garage (3).jpg',
                                    'empty-underground-parking-garage (2).jpg',
                                    'empty-underground-parking-garage (1).jpg',
                                    'empty-parking-lot.jpg',
                                    'empty-interior-with-railings.jpg',
                                ];
                                $randomIndex = array_rand($standardImages);
                                $placeImage = BASE_URL . 'frontend/assets/img/' . $standardImages[$randomIndex];
                            } elseif ($place['type'] === 'handicape') {
                                $handicapImages = [
                                    'pmr1.jpg',
                                    'pmr2.jpg',
                                    'pmr3.jpg',
                                    'pmr4.jpg',
                                    'pmr5.jpg',
                                    'pmr6.jpg',
                                    'pmr7.jpg',
                                    'pmr1.webp',
                                ];

                                // Rotation basée sur l'ID (ou autre identifiant unique)
                                $selectedHandicapImage = $handicapImages[$place['id'] % count($handicapImages)];
                                $placeImage = BASE_URL . 'frontend/assets/img/' . $selectedHandicapImage;
                            } elseif ($place['type'] === 'moto/scooter') {
                                // Sélectionner une image aléatoire parmi les 3 disponibles pour les places moto/scooter
                                $motoImages = [
                                    'moto.jpg',
                                    'moto1.jpg',
                                    'moto2.jpg',
                                    'moto3.jpg',
                                    'moto4.jpg',
                                ];
                                $randomIndex = array_rand($motoImages);
                                $placeImage = BASE_URL . 'frontend/assets/img/' . $motoImages[$randomIndex];
                            } elseif ($place['type'] === 'velo') {
                                // Sélectionner une image aléatoire parmi les 3 disponibles pour les places vélo
                                $bikeImages = [
                                    'velo1.webp',
                                    'velo2.webp',
                                    'velo3.webp'
                                ];
                                $randomIndex = array_rand($bikeImages);
                                $placeImage = BASE_URL . 'frontend/assets/img/' . $bikeImages[$randomIndex];
                            } elseif ($place['type'] === 'electrique') {
                                // Sélectionner une image aléatoire parmi les 5 disponibles pour les places électriques
                                $electricImages = [
                                    'elec1.webp',
                                    'elec2.webp',
                                    'elec3.webp',
                                    'elec4.webp',
                                    'elec5.webp'
                                ];
                                $randomIndex = array_rand($electricImages);
                                $placeImage = BASE_URL . 'frontend/assets/img/' . $electricImages[$randomIndex];
                            }
                            ?>
                            <div class="place-card-image" style="background-image: url('<?php echo $placeImage; ?>');">
                                <div class="card-header bg-transparent border-0 <?php
                                                                                if ($place['type'] === 'handicape') echo 'text-white';
                                                                                else if ($place['type'] === 'electrique') echo 'text-white';
                                                                                ?>">
                                    <span class="badge <?php
                                                        if ($place['type'] === 'handicape') echo 'bg-warning text-dark';
                                                        elseif ($place['type'] === 'electrique') echo 'bg-success';
                                                        else echo 'bg-secondary text-white';
                                                        ?>">
                                        Place <?php echo htmlspecialchars($place['numero']); ?>
                                        <?php if ($place['type'] === 'handicape'): ?>
                                            <i class="fas fa-wheelchair ms-1"></i>
                                        <?php elseif ($place['type'] === 'electrique'): ?>
                                            <i class="fas fa-charging-station ms-1"></i>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Type: <?php echo ucfirst($place['type']); ?></h5>
                                <p class="card-text">
                                    <?php if ($place['status'] === 'libre'): ?>
                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i> Disponible</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Occupée</span>
                                    <?php endif; ?>
                                </p>
                                <p class="card-text">
                                    <strong>Tarif:</strong> <?php echo number_format($tarifFloat, 2); ?> € / heure
                                </p>

                                <!-- Affichage des créneaux réservés -->
                                <?php if (isset($reservedTimeSlots[$place['id']]) && !empty($reservedTimeSlots[$place['id']])): ?>
                                    <div class="reserved-slots mb-3">
                                        <h6 class="text-danger"><i class="fas fa-clock me-1"></i> Créneaux indisponibles:</h6>
                                        <ul class="list-unstyled small">
                                            <?php foreach ($reservedTimeSlots[$place['id']] as $slot): ?>
                                                <li>
                                                    <span class="badge bg-danger">
                                                        <?php echo date('d/m/Y H:i', strtotime($slot['date_debut'])); ?> -
                                                        <?php echo date('d/m/Y H:i', strtotime($slot['date_fin'])); ?>
                                                    </span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div> <?php else: ?> <?php if ($place['status'] === 'occupe'): ?>
                                        <p class="text-warning small"><i class="fas fa-clock me-1"></i>
                                            <?php if (isset($currentOccupation[$place['id']])): ?>
                                                Occupée jusqu'à <?php echo date('d/m/Y H:i', strtotime($currentOccupation[$place['id']]['date_fin'])); ?>
                                            <?php else: ?>
                                                Temporairement occupée
                                            <?php endif; ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="text-success small"><i class="fas fa-clock me-1"></i> Tous les créneaux sont disponibles</p>
                                    <?php endif; ?>
                                <?php endif; ?> <button class="btn-reserve" data-bs-toggle="modal" data-bs-target="#reservationModal"
                                    data-place-id="<?php echo $place['id']; ?>"
                                    data-place-numero="<?php echo htmlspecialchars($place['numero']); ?>"
                                    data-place-type="<?php echo htmlspecialchars($place['type']); ?>"
                                    data-place-tarif="<?php echo isset($place['type']) && isset($tarifs[$place['type']]) ? $tarifs[$place['type']]['prix_heure'] : 0; ?>">
                                    <i class="fas fa-calendar-check me-2"></i> Réserver
                                </button>
                                <!-- Bouton de réservation immédiate -->
                                <form action="<?php echo BASE_URL; ?>reservation/reserveImmediate" method="post" class="mt-2">
                                    <input type="hidden" name="place_id" value="<?php echo $place['id']; ?>">
                                    <button type="submit" class="btn-reserve-immediate">
                                        <i class="fas fa-stopwatch me-2"></i> Réserver immédiatement
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Aucune place disponible pour le moment. Veuillez réessayer ultérieurement.
                    </div>
                </div>
            <?php endif; ?>
        </div> <!-- Pagination -->
        <div id="pagination-container" class="places-pagination mt-5 mb-4 text-center">
            <?php
            $is_ajax = true; // Activer le mode AJAX pour la pagination
            include_once FRONTEND_PATH . '/views/templates/pagination.php';
            ?>
            <!-- Indicateur de pagination active -->
            <div class="pagination-status d-none">
                <small class="text-muted">Page <span id="current-page-indicator"><?php echo $current_page; ?></span> / <?php echo $total_pages; ?></small>
            </div>
        </div>
    </div>

    <!-- Modal de réservation -->
    <div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservationModalLabel">Réservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Affichage des créneaux réservés -->
                    <div id="reserved-slots-container" class="mb-3 d-none">
                        <!-- Les créneaux réservés seront injectés ici -->
                    </div>
                    <!-- Formulaire de réservation -->
                    <?php if (isset($_SESSION['user'])): ?>
                        <form action="<?php echo BASE_URL; ?>reservation/reserve" method="post" id="reservation-form" onsubmit="return (typeof validateReservationForm === 'function' ? validateReservationForm() : true)" data-place-id-check="true">
                            <input type="hidden" name="place_id" id="place_id" value=""
                                data-required="true" data-error-msg="La place n'a pas été correctement sélectionnée">
                            <div class="mb-3">
                                <label for="date_debut" class="form-label">Date et heure de début</label>
                                <input type="datetime-local" class="form-control" id="date_debut" name="date_debut"
                                    value="<?php echo date('Y-m-d\TH:i', time()); ?>" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="duree_heures" class="form-label">Heures</label>
                                    <input type="number" class="form-control" id="duree_heures" name="duree_heures" min="0" max="24" value="0" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="duree_minutes" class="form-label">Minutes</label>
                                    <select class="form-select" id="duree_minutes" name="duree_minutes">
                                        <option value="0">0 min</option>
                                        <option value="15">15 min</option>
                                        <option value="30" selected>30 min</option>
                                        <option value="45">45 min</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted">Vous pouvez réserver pour une durée minimale de 15 minutes et maximale de 24 heures.</small>
                                </div>
                                <input type="hidden" id="duree" name="duree" value="0.5">
                            </div> <!-- Le calcul de durée et prix est maintenant géré par reservationPriceCalculator.js -->

                            <div class="mb-3 alert alert-success hidden" id="free-minutes-info">
                                <i class="fas fa-gift me-2"></i> <span id="free-minutes-text">Les 15 premières minutes sont gratuites !</span>
                            </div>
                            <div class="alert alert-info">
                                <p id="tarif-info" data-tarif="<?php echo isset($tarifs) && isset($place) && isset($tarifs[$place['type']]) ? $tarifs[$place['type']]['prix_heure'] : '2.00'; ?>">
                                    Tarif: <?php echo isset($tarifs) && isset($tarifs['standard']) ? number_format($tarifs['standard']['prix_heure'], 2) : '2.00'; ?> € / heure
                                </p>
                                <p class="mb-0">Total: <strong id="montant-total">0.50 €</strong></p>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" id="confirm-reservation-btn">
                                    <i class="fas fa-check me-2"></i> Confirmer la réservation
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="create-alert-btn">
                                    <i class="fas fa-bell me-2"></i> Créer une alerte si indisponible
                                </button>
                            </div>
                        </form>
                        <!-- Formulaire caché pour créer une alerte -->
                        <form action="<?php echo BASE_URL; ?>reservation/createAlert" method="post" id="alert-form" class="hidden">
                            <input type="hidden" name="place_id" id="alert_place_id" value="">
                            <input type="hidden" name="date_debut" id="alert_date_debut" value="">
                            <input type="hidden" name="duree" id="alert_duree" value="">
                            <input type="hidden" name="include_similar_places" id="alert_include_similar" value="0">
                            <input type="submit" id="alert_submit" class="hidden">
                        </form> <!-- Logique des alertes maintenant dans unifiedReservationManager.js -->

                        <!-- Données d'abonnement pour JavaScript -->
                        <?php if (isset($userSubscriptionBenefits) && $userSubscriptionBenefits): ?>
                            <div id="subscription-benefits" class="d-none"
                                data-free-minutes="<?php echo $userSubscriptionBenefits['free_minutes']; ?>"
                                data-discount-percent="<?php echo $userSubscriptionBenefits['discount_percent']; ?>"
                                data-subscription-name="<?php echo htmlspecialchars($userSubscriptionBenefits['subscription_name']); ?>">
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Formulaire de réservation pour invités -->
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Réservation en tant qu'invité</strong><br>
                            <small>Vous pouvez réserver sans créer de compte. Vos informations seront utilisées uniquement pour cette réservation.</small>
                        </div>

                        <form action="<?php echo BASE_URL; ?>reservation/guestReserve" method="post" id="guest-reservation-form" data-place-id-check="true">
                            <input type="hidden" name="place_id" id="guest_place_id" value=""
                                data-required="true" data-error-msg="La place n'a pas été correctement sélectionnée">

                            <!-- Informations personnelles -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="guest_name" class="form-label">Nom complet *</label>
                                    <input type="text" class="form-control" id="guest_name" name="guest_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="guest_email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="guest_email" name="guest_email" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="guest_phone" class="form-label">Téléphone (optionnel)</label>
                                <input type="tel" class="form-control" id="guest_phone" name="guest_phone">
                            </div>

                            <!-- Date et heure -->
                            <div class="mb-3">
                                <label for="guest_date_debut" class="form-label">Date et heure de début</label>
                                <input type="datetime-local" class="form-control" id="guest_date_debut" name="date_debut"
                                    value="<?php echo date('Y-m-d\TH:i', time()); ?>" required>
                            </div>

                            <!-- Durée -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="guest_duree_heures" class="form-label">Heures</label>
                                    <input type="number" class="form-control" id="guest_duree_heures" name="duree_heures" min="0" max="24" value="0" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="guest_duree_minutes" class="form-label">Minutes</label>
                                    <select class="form-select" id="guest_duree_minutes" name="duree_minutes">
                                        <option value="0">0 min</option>
                                        <option value="15">15 min</option>
                                        <option value="30" selected>30 min</option>
                                        <option value="45">45 min</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted">Vous pouvez réserver pour une durée minimale de 15 minutes et maximale de 24 heures.</small>
                                </div>
                                <input type="hidden" id="guest_duree" name="duree" value="0.5">
                            </div>

                            <!-- Informations tarifaires -->
                            <div class="alert alert-info">
                                <p id="guest-tarif-info" data-tarif="<?php echo isset($tarifs) && isset($tarifs['standard']) ? $tarifs['standard']['prix_heure'] : '2.00'; ?>">
                                    Tarif: <?php echo isset($tarifs) && isset($tarifs['standard']) ? number_format($tarifs['standard']['prix_heure'], 2) : '2.00'; ?> € / heure
                                </p>
                                <p class="mb-0">Total: <strong id="guest-montant-total">0.50 €</strong></p>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" id="confirm-guest-reservation-btn">
                                    <i class="fas fa-check me-2"></i> Réserver en tant qu'invité
                                </button>
                            </div>
                        </form>

                        <!-- Option de connexion -->
                        <div class="text-center mt-3">
                            <hr>
                            <p class="text-muted small">Vous avez déjà un compte ?</p>
                            <a href="<?php echo BASE_URL; ?>auth/login" class="btn btn-outline-primary btn-sm me-2">
                                <i class="fas fa-sign-in-alt me-1"></i> Se connecter
                            </a>
                            <a href="<?php echo BASE_URL; ?>auth/register" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-user-plus me-1"></i> Créer un compte
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Logique de réservation maintenant dans unifiedReservationManager.js -->
                    <meta name="current-page" content="places">
                    <!-- Pagination AJAX maintenant intégrée dans unifiedUIManager.js -->

                </div> <!-- Fermeture du container -->
            </div> <!-- Fermeture du places-page-background -->
        <!-- Tous les scripts inline ont été transférés vers unifiedReservationManager.js -->

        <!-- Correctif anti-conflit pour les cartes et le spinner -->
        <!-- <script src="<?php echo BASE_URL; ?>frontend/assets/js/components/places-fix.js"></script> -->

        <!-- Script de validation pour la page places -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('🔧 Validation page places - Initialisation...');
                // S'assurer que la grille tarifaire est cachée par défaut
                const tarifsInfo = document.querySelector('.tarifs-info');
                const showFeesCheckbox = document.getElementById('show-fees');

                if (tarifsInfo && showFeesCheckbox) {
                    // S'assurer que la grille tarifaire est cachée par défaut
                    if (!showFeesCheckbox.checked) {
                        tarifsInfo.style.display = 'none';
                        console.log('✅ Grille tarifaire cachée par défaut');
                    } else {
                        tarifsInfo.style.display = 'block';
                        console.log('✅ Grille tarifaire affichée selon checkbox');
                    }

                    // Gestionnaire pour la checkbox des tarifs
                    showFeesCheckbox.addEventListener('change', function() {
                        if (this.checked) {
                            tarifsInfo.style.display = 'block';
                            console.log('📋 Grille tarifaire affichée');
                        } else {
                            tarifsInfo.style.display = 'none';
                            console.log('📋 Grille tarifaire cachée');
                        }
                    });
                }

                // S'assurer que le spinner de chargement est caché par défaut
                const loadingSpinner = document.getElementById('loading-spinner');
                if (loadingSpinner) {
                    // Forcer l'état caché
                    loadingSpinner.classList.add('d-none');
                    loadingSpinner.style.display = 'none';
                    console.log('✅ Spinner de chargement caché par défaut');
                }

                // S'assurer que toutes les cartes de places sont visibles
                const placeCards = document.querySelectorAll('.place-card');
                if (placeCards.length > 0) {
                    placeCards.forEach((card, index) => {
                        // Forcer la visibilité des cartes
                        card.style.display = 'block';
                        card.style.opacity = '1';

                        // Animation d'apparition progressive
                        setTimeout(() => {
                            card.style.transform = 'translateY(0)';
                            card.style.transition = 'all 0.3s ease';
                        }, index * 100);
                    });
                    console.log(`✅ ${placeCards.length} cartes de places rendues visibles avec animations`);
                } else {
                    console.warn('⚠️ Aucune carte de place trouvée dans le DOM');
                }

                // S'assurer que le container des places est visible
                const placesContainer = document.getElementById('places-container');
                if (placesContainer) {
                    placesContainer.style.display = 'flex';
                    placesContainer.style.flexWrap = 'wrap';
                    console.log('✅ Container des places forcé visible en mode flex');
                }

                // Fonction utilitaire pour montrer le spinner lors des requêtes AJAX
                window.showAjaxSpinner = function() {
                    if (loadingSpinner) {
                        loadingSpinner.classList.remove('d-none');
                        loadingSpinner.style.display = 'block';
                        console.log('🔄 Spinner AJAX affiché');
                    }
                };

                // Fonction utilitaire pour cacher le spinner
                window.hideAjaxSpinner = function() {
                    if (loadingSpinner) {
                        loadingSpinner.classList.add('d-none');
                        loadingSpinner.style.display = 'none';
                        console.log('✅ Spinner AJAX caché');
                    }
                };
                console.log('✅ Validation page places terminée - Affichage optimisé');
            });
        </script>