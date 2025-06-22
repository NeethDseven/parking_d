<?php

/**
 * Helper pour l'affichage des badges de type de place
 */

if (!function_exists('getTypePlaceBadge')) {
    /**
     * Fonction helper pour afficher les badges de type de place
     * 
     * @param string $type Le type de place
     * @return string Le HTML du badge
     */
    function getTypePlaceBadge($type)
    {
        switch ($type) {
            case 'standard':
                return '<span class="badge bg-secondary">Standard</span>';
            case 'handicape':
                return '<span class="badge bg-primary">PMR</span>';
            case 'electrique':
                return '<span class="badge bg-success">Électrique</span>';
            case 'moto/scooter':
                return '<span class="badge bg-info">Moto/Scooter</span>';
            case 'velo':
                return '<span class="badge bg-warning">Vélo</span>';
            default:
                // Pour les types personnalisés, utiliser un badge personnalisé
                $displayName = ucfirst(str_replace('-', ' ', $type));
                return '<span class="badge bg-dark"><i class="fas fa-star"></i> ' . htmlspecialchars($displayName) . '</span>';
        }
    }
}

if (!function_exists('getAllTypesPlaces')) {
    /**
     * Retourne tous les types de places disponibles
     * 
     * @return array Les types de places avec leur label d'affichage
     */
    function getAllTypesPlaces()
    {
        return [
            'standard' => 'Standard',
            'handicape' => 'PMR',
            'electrique' => 'Électrique',
            'moto/scooter' => 'Moto/Scooter',
            'velo' => 'Vélo'
        ];
    }
}

if (!function_exists('isCustomPlaceType')) {
    /**
     * Vérifie si un type de place est personnalisé
     * 
     * @param string $type Le type de place à vérifier
     * @return bool True si le type est personnalisé, false sinon
     */
    function isCustomPlaceType($type)
    {
        $predefinedTypes = array_keys(getAllTypesPlaces());
        return !in_array($type, $predefinedTypes);
    }
}

if (!function_exists('formatCustomPlaceType')) {
    /**
     * Formate un nom de type personnalisé pour l'affichage
     * 
     * @param string $type Le type personnalisé
     * @return string Le nom formaté
     */
    function formatCustomPlaceType($type)
    {
        return ucfirst(str_replace('-', ' ', $type));
    }
}

if (!function_exists('sanitizePlaceType')) {
    /**
     * Nettoie et formate un type de place pour le stockage
     * 
     * @param string $type Le type de place à nettoyer
     * @return string Le type nettoyé
     */
    function sanitizePlaceType($type)
    {
        return strtolower(trim(preg_replace('/[^a-zA-Z0-9\s\-\/]/', '', $type)));
    }
}
