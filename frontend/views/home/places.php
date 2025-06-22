<!-- Tous les styles CSS ont été déplacés vers frontend/assets/css/places-improvements.css -->

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

        <!-- Container des places avec disposition 3 par ligne - Structure 3x2 grid -->
        <div id="places-container" class="places-grid-container"
            data-current-page="<?php echo isset($current_page) ? $current_page : 1; ?>"
            data-selected-type="<?php echo $selected_type ?? 'all'; ?>">
            <?php
            // Vérifier que $places et $tarifs sont bien définis
            if (isset($places) && !empty($places) && isset($tarifs)):
                // Pagination côté serveur : 6 places par page (3x2 grid)
                $placesPerPage = 6;
                $currentPage = isset($current_page) ? $current_page : 1;
                $startIndex = ($currentPage - 1) * $placesPerPage;
                $paginatedPlaces = array_slice($places, $startIndex, $placesPerPage);

                foreach ($paginatedPlaces as $key => $place):
                    // Vérifier que le type de place existe dans le tableau des tarifs
                    $tarifFloat = isset($tarifs[$place['type']]['prix_heure']) ?
                        floatval($tarifs[$place['type']]['prix_heure']) : 0;
            ?>
                    <!-- Carte de place - Structure 3x2 grid -->
                    <div class="place-card-item animate-on-scroll fade-in"
                        data-type="<?php echo htmlspecialchars($place['type']); ?>"
                        data-card-index="<?php echo $key; ?>">
                        <div class="card h-100 shadow-sm hover-effect"
                            <!-- Image différente selon le type de place -->
                            <?php
                            $placeImage = '';
                            if ($place['type'] === 'standard') {
                                // Images pour les places standard
                                $standardImages = [
                                    'standar.jpg',
                                    'standar1.jpg',
                                    'standar2.jpg',
                                    'standar3.jpg',
                                    'standar4.jpg',
                                    'standar5.jpg',
                                    'standar6.webp',
                                    'standar7.jpg',
                                    'standar8.jpg',
                                    'standar9.jpg',
                                    'standar10.jpg',
                                    'standar11.webp',
                                    'standar12.webp',
                                    'standard1.webp',
                                    'standard2.webp',
                                    'standard3.webp'
                                ];
                                $randomIndex = array_rand($standardImages);
                                $placeImage = BASE_URL . 'frontend/assets/img/' . $standardImages[$randomIndex];
                            } elseif ($place['type'] === 'handicape') {
                                // Images pour les places PMR/handicapé
                                $handicapImages = [
                                    'pmr.jpg',
                                    'pmr1.jpg',
                                    'pmr1.webp',
                                    'pmr2.jpg',
                                    'pmr3.jpg',
                                    'pmr4.jpg',
                                    'pmr5.jpg',
                                    'pmr6.jpg',
                                    'pmr7.jpg',
                                    'pmr8.jpg',
                                    'pmr9.webp'
                                ];

                                // Rotation basée sur l'ID (ou autre identifiant unique)
                                $selectedHandicapImage = $handicapImages[$place['id'] % count($handicapImages)];
                                $placeImage = BASE_URL . 'frontend/assets/img/' . $selectedHandicapImage;
                            } elseif ($place['type'] === 'moto/scooter') {
                                // Images pour les places moto/scooter
                                $motoImages = [
                                    'moto.jpg',
                                    'moto1.jpg',
                                    'moto2.jpg',
                                    'moto3.jpg',
                                    'moto4.jpg'
                                ];
                                $randomIndex = array_rand($motoImages);
                                $placeImage = BASE_URL . 'frontend/assets/img/' . $motoImages[$randomIndex];
                            } elseif ($place['type'] === 'velo') {
                                // Images pour les places vélo
                                $bikeImages = [
                                    'velo.jpg',
                                    'velo1.webp',
                                    'velo2.jpg',
                                    'velo4.jpg',
                                    'velo5.jpg',
                                    'velo6.webp',
                                    'velo7.jpg'
                                ];
                                $randomIndex = array_rand($bikeImages);
                                $placeImage = BASE_URL . 'frontend/assets/img/' . $bikeImages[$randomIndex];
                            } elseif ($place['type'] === 'electrique') {
                                // Images pour les places électriques (elec1 à elec6)
                                $electricImages = [
                                    'elec1.webp',
                                    'elec2.webp',
                                    'elec3.webp',
                                    'elec4.webp',
                                    'elec5.webp',
                                    'elec6.webp'
                                ];
                                $randomIndex = array_rand($electricImages);
                                $placeImage = BASE_URL . 'frontend/assets/img/' . $electricImages[$randomIndex];
                            }
                            ?>
                            <!-- Image de fond avec header -->
                            <div class="place-card-image" style="background-image: url('<?php echo $placeImage; ?>');">
                                <div class="card-header bg-transparent border-0">
                                    <?php
                                    // Badge avec couleur selon le type
                                    $badgeClass = 'bg-secondary text-white';
                                    $icon = 'fas fa-car';

                                    switch($place['type']) {
                                        case 'handicape':
                                            $badgeClass = 'bg-warning text-dark';
                                            $icon = 'fas fa-wheelchair';
                                            break;
                                        case 'electrique':
                                            $badgeClass = 'bg-success text-white';
                                            $icon = 'fas fa-charging-station';
                                            break;
                                        case 'moto/scooter':
                                            $badgeClass = 'bg-secondary text-white';
                                            $icon = 'fas fa-motorcycle';
                                            break;
                                        case 'velo':
                                            $badgeClass = 'bg-info text-white';
                                            $icon = 'fas fa-bicycle';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        Place <?php echo htmlspecialchars($place['numero']); ?>
                                        <i class="<?php echo $icon; ?> ms-1"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Corps de la carte -->
                            <div class="card-body">
                                <h5 class="card-title">Type: <?php echo ucfirst($place['type']); ?></h5>

                                <!-- Statut -->
                                <p class="card-text">
                                    <?php if ($place['status'] === 'libre'): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i> Disponible
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i> Occupée
                                        </span>
                                    <?php endif; ?>
                                </p>

                                <!-- Tarif -->
                                <p class="card-text">
                                    <strong>Tarif:</strong> <?php echo number_format($tarifFloat, 2); ?> € / heure
                                </p>

                                <!-- Disponibilité -->
                                <?php if (isset($reservedTimeSlots[$place['id']]) && !empty($reservedTimeSlots[$place['id']])): ?>
                                    <p class="text-warning small">
                                        <i class="fas fa-clock me-1"></i> Créneaux indisponibles
                                    </p>
                                <?php elseif ($place['status'] === 'occupe'): ?>
                                    <p class="text-warning small">
                                        <i class="fas fa-clock me-1"></i> Temporairement occupée
                                    </p>
                                <?php else: ?>
                                    <p class="text-success small">
                                        <i class="fas fa-clock me-1"></i> Tous les créneaux sont disponibles
                                    </p>
                                <?php endif; ?>

                                <!-- Boutons d'action -->
                                <button class="btn-reserve"
                                        data-bs-toggle="modal"
                                        data-bs-target="#reservationModal"
                                        data-place-id="<?php echo $place['id']; ?>"
                                        data-place-numero="<?php echo htmlspecialchars($place['numero']); ?>"
                                        data-place-type="<?php echo htmlspecialchars($place['type']); ?>"
                                        data-place-tarif="<?php echo $tarifFloat; ?>">
                                    <i class="fas fa-calendar-check me-2"></i> Réserver
                                </button>

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

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" id="confirm-reservation-btn">
                                    <i class="fas fa-check me-2"></i> Confirmer la réservation
                                </button>
                            </div>
                        </form>

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

        <!-- Script de correction pour les formulaires de réservation -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🔧 Script de correction formulaires chargé');

            // Vérifier si l'utilisateur est connecté
            const isUserLoggedIn = <?php echo isset($_SESSION['user']) ? 'true' : 'false'; ?>;
            console.log('👤 Utilisateur connecté:', isUserLoggedIn);

            // Attendre que la modal soit disponible et que le JavaScript principal soit chargé
            setTimeout(function() {
                if (isUserLoggedIn) {
                    // Utilisateur connecté - vérifier le formulaire utilisateur
                    const userForm = document.getElementById('reservation-form');
                    console.log('🔍 Formulaire utilisateur trouvé:', userForm);

                    if (userForm) {
                        console.log('✅ Formulaire utilisateur disponible - pas de correction nécessaire');
                    } else {
                        console.warn('⚠️ Formulaire utilisateur non trouvé');
                    }
                } else {
                    // Utilisateur non connecté - gérer le formulaire invité
                    const guestForm = document.getElementById('guest-reservation-form');
                    console.log('🔍 Formulaire invité trouvé:', guestForm);

                    if (guestForm) {
                        // Vérifier si le gestionnaire est déjà attaché
                        if (!guestForm.dataset.handlerAttached) {
                            console.log('🔧 Ajout du gestionnaire AJAX au formulaire invité');

                            // Marquer comme traité
                            guestForm.dataset.handlerAttached = 'true';

                            // Ajouter un gestionnaire AJAX
                            guestForm.addEventListener('submit', function(event) {
                                event.preventDefault();
                                event.stopPropagation();
                                console.log('🚀 Formulaire invité soumis via AJAX (script de correction)');

                                // Vérifier le place_id
                                const placeIdField = document.getElementById('guest_place_id');
                                console.log('🔍 Champ place_id:', placeIdField, 'Valeur:', placeIdField?.value);

                                if (!placeIdField || !placeIdField.value) {
                                    alert('Erreur: Place non sélectionnée. Veuillez fermer cette fenêtre et cliquer à nouveau sur "Réserver" pour une place.');
                                    return;
                                }

                                // Désactiver le bouton de soumission
                                const submitButton = guestForm.querySelector('button[type="submit"]');
                                if (submitButton) {
                                    submitButton.disabled = true;
                                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Traitement...';
                                }

                                // Créer FormData
                                const formData = new FormData(guestForm);
                                console.log('📋 Données du formulaire:');
                                for (let [key, value] of formData.entries()) {
                                    console.log(`  ${key}: ${value}`);
                                }

                                // Envoyer la requête AJAX
                                fetch('<?php echo BASE_URL; ?>reservation/guestReserve', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    console.log('📡 Réponse du serveur:', data);
                                    if (data.success) {
                                        console.log('✅ Redirection vers:', data.redirect_url);
                                        window.location.href = data.redirect_url;
                                    } else {
                                        alert('Erreur: ' + (data.error || 'Une erreur est survenue'));
                                        // Réactiver le bouton
                                        if (submitButton) {
                                            submitButton.disabled = false;
                                            submitButton.innerHTML = '<i class="fas fa-check me-2"></i> Réserver en tant qu\'invité';
                                        }
                                    }
                                })
                                .catch(error => {
                                    console.error('❌ Erreur:', error);
                                    alert('Erreur de communication avec le serveur');
                                    // Réactiver le bouton
                                    if (submitButton) {
                                        submitButton.disabled = false;
                                        submitButton.innerHTML = '<i class="fas fa-check me-2"></i> Réserver en tant qu\'invité';
                                    }
                                });
                            });

                            console.log('✅ Gestionnaire AJAX ajouté au formulaire invité');
                        } else {
                            console.log('ℹ️ Gestionnaire déjà attaché au formulaire invité');
                        }
                    } else {
                        console.log('ℹ️ Formulaire invité non trouvé (normal pour utilisateur non connecté)');
                    }
                }
            }, 1500); // Attendre 1.5 secondes pour que tout soit chargé
        });
        </script>

        <!-- Correctif anti-conflit pour les cartes et le spinner -->
        <!-- <script src="<?php echo BASE_URL; ?>frontend/assets/js/components/places-fix.js"></script> -->

        <!-- Gestionnaire des places - Logique fonctionnelle séparée -->
        <script src="<?php echo BASE_URL; ?>frontend/assets/js/components/placesManager.js"></script>