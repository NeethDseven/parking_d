<meta name="current-page" content="error_404">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-1 fw-bold text-primary">404</h1>
            <h2 class="mb-4">Page non trouvée</h2>
            <p class="fs-5">Oups ! La page que vous recherchez semble avoir disparu ou n'existe pas.</p>

            <div class="illustration my-5">
                <img src="<?php echo BASE_URL; ?>frontend/assets/img/404-car.svg" alt="Page non trouvée" class="img-fluid img-height-250">
            </div>

            <div class="my-4">
                <h4>Voici quelques liens utiles pour vous aider :</h4>
                <div class="d-flex justify-content-center flex-wrap gap-2 mt-3">
                    <a href="<?php echo BASE_URL; ?>" class="btn btn-primary m-2">
                        <i class="fas fa-home me-2"></i>Page d'accueil
                    </a>
                    <a href="<?php echo BASE_URL; ?>home/places" class="btn btn-outline-primary m-2">
                        <i class="fas fa-car me-2"></i>Places disponibles
                    </a>
                    <a href="<?php echo BASE_URL; ?>home/contact" class="btn btn-outline-primary m-2">
                        <i class="fas fa-envelope me-2"></i>Contactez-nous
                    </a>
                    <a href="<?php echo BASE_URL; ?>home/faq" class="btn btn-outline-primary m-2">
                        <i class="fas fa-question-circle me-2"></i>FAQ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SVG de secours pour la page 404 géré par components/errorPage.js -->
</script>