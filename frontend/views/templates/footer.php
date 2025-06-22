</main>

<!-- Footer -->
<footer class="footer py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5>ParkMe In</h5>
                <p>Votre solution de stationnement intelligente.</p>
                <div class="social-links">
                    <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <h5>Liens rapides</h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo getBaseUrl(); ?>" class="text-white">Accueil</a></li>
                    <li><a href="<?php echo getBaseUrl(); ?>home/places" class="text-white">Places disponibles</a></li>
                    <li><a href="<?php echo getBaseUrl(); ?>home/about" class="text-white">À propos</a></li>
                    <li><a href="<?php echo getBaseUrl(); ?>home/contact" class="text-white">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-3">
                <h5>Informations</h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo getBaseUrl(); ?>home/terms" class="text-white">Conditions d'utilisation</a></li>
                    <li><a href="<?php echo getBaseUrl(); ?>home/privacy" class="text-white">Politique de confidentialité</a></li>
                    <li><a href="<?php echo getBaseUrl(); ?>home/faq" class="text-white">FAQ</a></li>
                    <li><a href="<?php echo getBaseUrl(); ?>home/careers" class="text-white">Carrières</a></li>
                </ul>
            </div>
            <div class="col-md-2 mb-3">
                <h5>Contact</h5>
                <address class="mb-0">
                    <p class="mb-0">123 Rue du Parking</p>
                    <p class="mb-0">75000 Paris</p>
                    <p class="mb-0">01 23 45 67 89</p>
                    <p class="mb-0"><a href="mailto:contact@parkmein.com" class="text-white">contact@parkmein.com</a></p>
                </address>
            </div>
        </div>
        <hr class="my-3 bg-light">
        <div class="text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> ParkMe In. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script minimal pour navbar mobile -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const navbarCollapse = document.querySelector('#navbarNav');
    const navbarToggler = document.querySelector('.navbar-toggler');

    // S'assurer que l'état initial est correct
    if (navbarCollapse && navbarToggler) {
        // Forcer l'état fermé au chargement en mobile
        if (window.innerWidth < 992) {
            navbarCollapse.classList.remove('show');
            navbarToggler.classList.add('collapsed');
            navbarToggler.setAttribute('aria-expanded', 'false');
        }

        // Fermer la navbar mobile quand on clique sur un lien
        const navLinks = document.querySelectorAll('#navbarNav .nav-link:not(.dropdown-toggle)');
        navLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992 && !navbarToggler.classList.contains('collapsed')) {
                    navbarToggler.click();
                }
            });
        });
    }
});
</script>

<!-- Scripts de gestion des styles obsolètes - remplacés par la structure CSS optimisée -->

<!-- Script pour le suivi des réservations immédiates, si besoin -->
<?php if (isset($_SESSION['user'])): ?>
    <?php
    $activeReservationData = function_exists('getActiveImmediateReservation') ? getActiveImmediateReservation() : false;
    if ($activeReservationData && isset($activeReservationData['reservation'])):
    ?>
        <!-- Composants consolidés dans unifiedReservationManager.js et chargés par app.js -->
        <!-- La gestion des timers est maintenant prise en charge par reservationTimerService.js -->
    <?php endif; ?>
<?php endif; ?>

<!-- Application du data-page pour le chargement des modules --> <!-- La gestion de l'identifiant de page est maintenant gérée par navbarComponent.js -->
</body>

</html><?php
        // Fonction helper pour éviter les erreurs liées à BASE_URL
        if (!function_exists('getBaseUrl')) {
            function getBaseUrl()
            {
                return defined('BASE_URL') ? BASE_URL : '/projet/parking_d/';
            }
        }
        ?>