            </main>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts personnalisés admin -->
    <script src="<?php echo BASE_URL; ?>frontend/assets/js/core/scriptManager.js"></script>
    <script src="<?php echo BASE_URL; ?>frontend/assets/js/components/unifiedAdminManager.js"></script>

    <!-- Script de correction des modales admin -->
    <script src="<?php echo BASE_URL; ?>frontend/assets/js/modal-fix-final.js"></script>

    <!-- Admin specific scripts -->
    <script>
        // Sidebar toggle uniquement pour mobile et tablette
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');

            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    const isMobileOrTablet = window.innerWidth < 992;

                    // Fonctionnalité uniquement sur mobile/tablette
                    if (isMobileOrTablet) {
                        sidebar.classList.toggle('show');
                    }
                });

                // Gérer le redimensionnement de la fenêtre
                window.addEventListener('resize', function() {
                    const isMobileOrTablet = window.innerWidth < 992;

                    if (!isMobileOrTablet) {
                        // Retour au desktop : nettoyer les classes mobile
                        sidebar.classList.remove('show');
                    }
                });
            }
        });
    </script>
</body>
</html>
