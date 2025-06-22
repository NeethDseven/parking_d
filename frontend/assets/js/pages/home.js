// Script pour la page d'accueil - Interactions et animations

document.addEventListener('DOMContentLoaded', function() {
    // Améliore l'accessibilité et l'interaction avec la carte
    const mapContainer = document.querySelector('.compact-map-iframe');
    if (mapContainer) {
        // Ajoute un titre accessible
        mapContainer.setAttribute('title', 'Carte Google Maps - Localisation ParkMe In, 123 Rue du Faubourg Saint-Honoré, Paris');

        // Améliore l'affichage au focus
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
    if (parkingCarousel) {
        // Améliore l'accessibilité pour les images
        const parkingImages = parkingCarousel.querySelectorAll('.parking-image');
        parkingImages.forEach(img => {
            img.addEventListener('click', function() {
                // Effet de zoom au clic
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

    // Ajoute l'animation CSS pour le pulse
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
