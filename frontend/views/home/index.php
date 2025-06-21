<div class="jumbotron rounded-3 py-5 mb-4 text-white hero-section">
    <!-- Overlay sombre pour améliorer la lisibilité du texte -->
    <div class="hero-overlay">
        <div class="container text-center position-relative hero-content">
            <h1 class="display-4 text-white">Bienvenue chez ParkMe In</h1>
            <p class="lead">Votre solution de stationnement intelligente au cœur de Paris</p>
            <p>Trouvez facilement une place de parking disponible et réservez-la en quelques clics.</p>
            <div class="mt-4 d-flex justify-content-center gap-3">
                <a class="btn btn-primary btn-lg" href="<?php echo BASE_URL; ?>home/places" role="button">
                    <i class="fas fa-parking me-2"></i> Voir les places disponibles
                </a>
                <?php if (!isset($_SESSION['user'])): ?>
                    <a class="btn btn-outline-light btn-lg" href="<?php echo BASE_URL; ?>auth/register" role="button">
                        <i class="fas fa-user-plus me-2"></i> S'inscrire
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Séparateur visuel simplifié entre les sections -->
<div class="section-separator">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="separator-line"></div>
                <div class="separator-line"></div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques - Bien espacées entre les deux sections principales -->
<div class="container my-5 py-4 animate-fade-in" style="margin-top: 4rem !important; margin-bottom: 4rem !important;">
    <div class="row justify-content-center">
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100 shadow-sm hover-card">
                <div class="card-body">
                    <div class="display-1 text-primary mb-3"><?php echo $stats['disponibles']; ?></div>
                    <h3>Places disponibles</h3>
                    <p class="text-muted">sur un total de <?php echo $stats['total']; ?> places</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100 shadow-sm hover-card">
                <div class="card-body">
                    <div class="display-1 text-primary mb-3">
                        <?php
                        $handicapes = 0;
                        foreach ($stats['par_type'] as $type) {
                            if ($type['type'] === 'handicape') {
                                $handicapes = $type['nombre'];
                                break;
                            }
                        }
                        echo $handicapes;
                        ?>
                    </div>
                    <h3>Places handicapées</h3>
                    <p class="text-muted">Accessibilité garantie</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100 shadow-sm hover-card">
                <div class="card-body">
                    <div class="display-1 text-primary mb-3">
                        <?php
                        $electriques = 0;
                        foreach ($stats['par_type'] as $type) {
                            if ($type['type'] === 'electrique') {
                                $electriques = $type['nombre'];
                                break;
                            }
                        }
                        echo $electriques;
                        ?>
                    </div>
                    <h3>Places électriques</h3>
                    <p class="text-muted">Avec bornes de recharge</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section Parking - Identification visuelle -->
<div class="features-section animate-fade-in" style="background: url('/projet/parking_d/frontend/assets/img/parking-underground.webp') center center / cover no-repeat fixed; position: relative;">
    <div class="container" style="position: relative; z-index: 2;">
        <h2 class="text-center mb-4 text-white"><i class="fas fa-building me-2"></i>Votre Parking ParkMe In</h2>
        <p class="text-center mb-5 lead"><i class="fas fa-map-marker-alt me-2"></i>123 Rue du Faubourg Saint-Honoré, 75008 Paris</p>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm parking-identification-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h5 class="card-title text-primary">🎯 Comment identifier notre parking ?</h5>

                                <div class="identification-points">
                                    <div class="d-flex align-items-start mb-3 location-info" style="opacity: 1; transform: translateY(0px); transition: opacity 0.6s, transform 0.6s;">
                                        <div class="identification-icon" style="animation: 2s ease 0s infinite normal none running pulse;">
                                            <i class="fas fa-sign text-primary"></i>
                                        </div>
                                        <div>
                                            <strong>Enseigne bleue "ParkMe In"</strong><br>
                                            <small class="text-muted">Grande enseigne lumineuse bien visible depuis la rue</small>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-start mb-3 location-info" style="opacity: 1; transform: translateY(0px); transition: opacity 0.6s, transform 0.6s;">
                                        <div class="identification-icon" style="animation: 2s ease 0s infinite normal none running pulse;">
                                            <i class="fas fa-road text-primary"></i>
                                        </div>
                                        <div>
                                            <strong>Barrières automatiques d'entrée</strong><br>
                                            <small class="text-muted">Système de contrôle d'accès moderne</small>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-start mb-3 location-info" style="opacity: 1; transform: translateY(0px); transition: opacity 0.6s, transform 0.6s;">
                                        <div class="identification-icon" style="animation: 2s ease 0s infinite normal none running pulse;">
                                            <i class="fas fa-building text-primary"></i>
                                        </div>
                                        <div>
                                            <strong>Façade moderne en verre</strong><br>
                                            <small class="text-muted">Architecture contemporaine distinctive</small>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-start mb-3 location-info" style="opacity: 1; transform: translateY(0px); transition: opacity 0.6s, transform 0.6s;">
                                        <div class="identification-icon" style="animation: 2s ease 0s infinite normal none running pulse;">
                                            <i class="fas fa-shield-alt text-success"></i>
                                        </div>
                                        <div>
                                            <strong>Sécurité 24h/24</strong><br>
                                            <small class="text-muted">Caméras de surveillance visibles</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="transport-info mt-4">
                                    <h6 class="text-primary"><i class="fas fa-subway me-2"></i>Accès transports</h6>
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-primary me-2">M</span>
                                        <small>Concorde (5 min) - Lignes 1, 8, 12</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-secondary me-2">BUS</span>
                                        <small>Arrêt Concorde - Lignes 42, 52, 72, 84, 94</small>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="<?php echo BASE_URL; ?>home/contact" class="btn btn-outline-primary">
                                        <i class="fas fa-question-circle me-2"></i>Besoin d'aide pour nous trouver ?
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="card-title text-primary">📸 Aperçu de notre parking</h5>

                                <!-- Carrousel d'images du parking -->
                                <div id="parkingCarousel" class="carousel slide mb-3" data-bs-ride="false">
                                    <div class="carousel-indicators">
                                        <button type="button" data-bs-target="#parkingCarousel" data-bs-slide-to="0" class="active" aria-label="Vue 1 du parking" aria-current="true"></button>
                                        <button type="button" data-bs-target="#parkingCarousel" data-bs-slide-to="1" aria-label="Vue 2 du parking"></button>
                                        <button type="button" data-bs-target="#parkingCarousel" data-bs-slide-to="2" aria-label="Vue 3 du parking"></button>
                                    </div>
                                    <div class="carousel-inner parking-carousel">
                                        <div class="carousel-item active">
                                            <img src="<?php echo BASE_URL; ?>frontend/assets/img/parking-dark.webp" class="d-block w-100 parking-image" alt="Vue générale du parking ParkMe In">
                                            <div class="carousel-caption d-none d-md-block">
                                                <h6>Vue générale du parking</h6>
                                            </div>
                                        </div>
                                        <div class="carousel-item">
                                            <img src="<?php echo BASE_URL; ?>frontend/assets/img/adminback.webp" class="d-block w-100 parking-image" alt="Système de sécurité du parking">
                                            <div class="carousel-caption d-none d-md-block">
                                                <h6>Sécurité renforcée</h6>
                                            </div>
                                        </div>
                                        <div class="carousel-item">
                                            <img src="<?php echo BASE_URL; ?>frontend/assets/img/parking-dark2.webp" class="d-block w-100 parking-image" alt="Signalétique parking">
                                            <div class="carousel-caption d-none d-md-block">
                                                <h6>Signalétique parking</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#parkingCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                        <span class="visually-hidden">Précédent</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#parkingCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                        <span class="visually-hidden">Suivant</span>
                                    </button>
                                </div>

                                <!-- Map compacte en dessous -->
                                <div class="compact-map">
                                    <h6 class="text-primary mb-2"><i class="fas fa-map me-2"></i>Localisation précise</h6>
                                    <div class="ratio ratio-21x9">
                                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.2719!2d2.3213574!3d48.8659181!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDjCsDUxJzU3LjMiTiAywrAyMCcxNi45IkU!5e0!3m2!1sfr!2sfr!4v1623882189449" class="border border-light rounded compact-map-iframe" allowfullscreen="" loading="lazy" style="border:0;" title="Carte Google Maps - Localisation ParkMe In, 123 Rue du Faubourg Saint-Honoré, Paris">
                                        </iframe>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Cliquez sur la carte pour voir l'itinéraire depuis votre position
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Footer de la carte avec informations pratiques -->
                    <div class="card-footer bg-light">
                        <div class="row text-center">
                            <div class="col-md-3 mb-2">
                                <i class="fas fa-shield-alt text-success fs-4"></i>
                                <div class="mt-1">
                                    <small class="fw-bold">Sécurisé</small><br>
                                    <small class="text-muted">24h/24</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <i class="fas fa-car text-primary fs-4"></i>
                                <div class="mt-1">
                                    <small class="fw-bold">41 places</small><br>
                                    <small class="text-muted">Disponibles</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <i class="fas fa-credit-card text-warning fs-4"></i>
                                <div class="mt-1">
                                    <small class="fw-bold">Paiement</small><br>
                                    <small class="text-muted">Sans contact</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <i class="fas fa-mobile-alt text-info fs-4"></i>
                                <div class="mt-1">
                                    <small class="fw-bold">Réservation</small><br>
                                    <small class="text-muted">Mobile</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section abonnements -->
<div class="container mb-5 animate-fade-in">
    <h2 class="text-center mb-4">Nos abonnements</h2>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100 subscription-promo-card">
                <div class="card-body">
                    <h3 class="card-title">Économisez avec nos abonnements</h3>
                    <p class="card-text">Profitez de nombreux avantages en souscrivant à un abonnement mensuel ou annuel :</p>
                    <ul>
                        <li>Jusqu'à 30 minutes gratuites par réservation</li>
                        <li>Réductions exclusives sur toutes vos réservations</li>
                        <li>Accès prioritaire aux places de parking</li>
                        <li>Résiliation possible à tout moment</li>
                    </ul>
                    <a href="<?php echo BASE_URL; ?>subscription" class="btn btn-primary mt-3">Voir les offres</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100 subscription-cta-card">
                <div class="card-body text-center">
                    <div class="subscription-icon mb-3">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <h3 class="card-title mb-3">Abonnements à partir de 29,99€/mois</h3>
                    <p class="card-text mb-4">Stationnez plus, payez moins. Nos abonnements s'adaptent à vos besoins.</p>
                    <a href="http://127.0.0.1/projet/parking_d/" class="btn btn-lg" style="background-color: #2c3e50; color: #fff; border: none;">Voir les détails</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="container mb-5 animate-fade-in">
    <div class="card shadow reservation-cta-card" style="background: url('<?php echo BASE_URL; ?>frontend/assets/img/readytores.webp') center center / cover no-repeat; border: none;">
        <!-- Overlay sombre pour améliorer la lisibilité -->
        <div style="background: rgba(0, 0, 0, 0.7); position: absolute; top: 0; left: 0; width: 100%; height: 100%; border-radius: inherit;"></div>
        <div class="card-body p-5 text-center text-white" style="position: relative; z-index: 2;">
            <h2>Prêt à réserver votre place ?</h2>
            <p class="lead">N'attendez plus et trouvez la place idéale pour votre véhicule.</p>
            <a class="btn btn-primary btn-lg me-2" href="<?php echo BASE_URL; ?>home/places"> Réserver maintenant </a>
        </div>
    </div>
</div>

<!-- Script pour améliorer l'interaction avec la section parking -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Amélioration de l'accessibilité et de l'interaction avec la carte
        const mapContainer = document.querySelector('.compact-map-iframe');
        if (mapContainer) {
            // Ajout d'un titre accessible
            mapContainer.setAttribute('title', 'Carte Google Maps - Localisation ParkMe In, 123 Rue du Faubourg Saint-Honoré, Paris');

            // Amélioration de l'affichage au focus
            mapContainer.addEventListener('focus', function() {
                this.style.outline = '2px solid var(--accent-primary)';
                this.style.outlineOffset = '2px';
            });

            mapContainer.addEventListener('blur', function() {
                this.style.outline = 'none';
            });
        }

        // Animation des éléments de localisation au scroll
        const locationInfoElements = document.querySelectorAll('.location-info');
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    entry.target.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                }
            });
        }, observerOptions);

        locationInfoElements.forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(20px)';
            observer.observe(element);
        });

        // Gestion du carrousel de parking
        const parkingCarousel = document.getElementById('parkingCarousel');
        if (parkingCarousel) { // Le carousel est configuré en statique (pas de défilement automatique)

            // Amélioration de l'accessibilité pour les images
            const parkingImages = parkingCarousel.querySelectorAll('.parking-image');
            parkingImages.forEach(img => {
                img.addEventListener('click', function() {
                    // Effet de zoom au clic (optionnel)
                    this.style.transition = 'transform 0.3s ease';
                    this.style.transform = 'scale(1.02)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 300);
                });

                img.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });

            // Indicateurs de progression pour l'accessibilité
            const indicators = parkingCarousel.querySelectorAll('.carousel-indicators button');
            indicators.forEach((indicator, index) => {
                indicator.setAttribute('aria-label', `Vue ${index + 1} du parking`);
            });
        }

        // Effet de pulse sur les icônes d'identification
        const identificationIcons = document.querySelectorAll('.identification-icon');
        identificationIcons.forEach((icon, index) => {
            // Animation décalée pour chaque icône
            setTimeout(() => {
                icon.style.animation = 'pulse 2s infinite';
            }, index * 200);
        });

        // Animation CSS pour le pulse
        const style = document.createElement('style');
        style.textContent = `
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    `;
        document.head.appendChild(style);

        // Analytics pour suivre l'interaction avec la section parking
        const trackParkingInteraction = (action, element) => {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'parking_section_interaction', {
                    'action': action,
                    'element': element
                });
            }
        };

        // Suivi des clics sur les éléments importants
        document.querySelector('a[href*="contact"]')?.addEventListener('click', () => {
            trackParkingInteraction('contact_click', 'help_button');
        });

        parkingCarousel?.addEventListener('slide.bs.carousel', (e) => {
            trackParkingInteraction('carousel_slide', `slide_to_${e.to}`);
        });

        mapContainer?.addEventListener('click', () => {
            trackParkingInteraction('map_click', 'location_map');
        });
    });
</script>