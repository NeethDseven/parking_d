<?php
/**
 * Template de page admin uniforme
 * Utilisez ce template comme base pour toutes les nouvelles pages admin
 */
?>

<!-- Container principal uniforme -->
<div class="admin-page-container">
    
    <!-- Header de page uniforme -->
    <div class="admin-page-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="admin-page-title">
                    <i class="fas fa-[ICON]"></i>
                    [TITRE DE LA PAGE]
                </h1>
                <p class="text-muted mb-0">[DESCRIPTION OPTIONNELLE]</p>
            </div>
            <div class="admin-page-actions">
                <!-- Boutons d'action -->
                <button class="admin-btn admin-btn-primary">
                    <i class="fas fa-plus"></i>
                    Ajouter
                </button>
            </div>
        </div>
    </div>

    <!-- Cartes statistiques (optionnel) -->
    <div class="admin-stats-grid">
        <div class="admin-stat-card primary">
            <div class="admin-stat-header">
                <div class="admin-stat-content">
                    <h3>123</h3>
                    <p>Titre statistique</p>
                </div>
                <div class="admin-stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="admin-stat-footer">
                <a href="#">
                    <i class="fas fa-arrow-right"></i>
                    Voir détails
                </a>
            </div>
        </div>
        
        <!-- Répéter pour d'autres stats -->
    </div>

    <!-- Contenu principal -->
    <div class="admin-content-card">
        <div class="admin-content-card-header">
            <h3 class="admin-content-card-title">
                <i class="fas fa-table me-2"></i>
                Données
            </h3>
            <div>
                <!-- Actions du header -->
                <button class="admin-btn admin-btn-outline admin-btn-sm">
                    <i class="fas fa-filter"></i>
                    Filtrer
                </button>
            </div>
        </div>
        <div class="admin-content-card-body">
            
            <!-- Tableau uniforme -->
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Colonne 1</th>
                            <th>Colonne 2</th>
                            <th>Colonne 3</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Donnée 1</td>
                            <td>Donnée 2</td>
                            <td>Donnée 3</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="admin-btn admin-btn-primary admin-btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="admin-btn admin-btn-danger admin-btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>

    <!-- Autre contenu -->
    <div class="admin-content-card">
        <div class="admin-content-card-header">
            <h3 class="admin-content-card-title">Autre section</h3>
        </div>
        <div class="admin-content-card-body">
            <!-- Contenu libre -->
        </div>
    </div>

</div>

<!-- Styles CSS spécifiques à cette page (optionnel) -->
<style>
/* Styles spécifiques à cette page uniquement */
</style>

<!-- Scripts JS spécifiques à cette page (optionnel) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scripts spécifiques à cette page
});
</script>
