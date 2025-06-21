<?php

/**
 * Template de pagination réutilisable
 * 
 * Variables attendues:
 * $current_page: int - Page actuelle
 * $total_pages: int - Nombre total de pages
 * $pagination_url: string - URL de base pour les liens de pagination (sans paramètre de page)
 * $url_params: array (optionnel) - Paramètres additionnels à ajouter à l'URL
 * $is_ajax: boolean (optionnel) - Si true, utilise des liens AJAX au lieu de liens standard
 */

// Si on n'a pas les variables nécessaires, on n'affiche pas la pagination
if (!isset($current_page) || !isset($total_pages) || !isset($pagination_url)) {
    return;
}

// S'assurer que les variables sont au bon format
$current_page = (int)$current_page;
$total_pages = (int)$total_pages;
$is_ajax = isset($is_ajax) ? (bool)$is_ajax : false;

// Si on n'a qu'une seule page, on n'affiche pas la pagination
if ($total_pages <= 1) {
    return;
}

// Construire les paramètres d'URL additionnels
$additional_params = '';
// Utiliser la variable $selected_type fournie directement ou extraire des paramètres URL
if (isset($selected_type)) {
    // Si $selected_type est fourni directement, l'utiliser
    $current_selected_type = $selected_type;
} else {
    // Sinon, extraire des paramètres URL
    $current_selected_type = '';
    if (isset($url_params) && is_array($url_params)) {
        foreach ($url_params as $key => $value) {
            if ($key !== 'page') { // Éviter de dupliquer le paramètre de page
                if (!($key === 'type' && ($value === 'all' || empty($value)))) {
                    $additional_params .= "&{$key}=" . urlencode($value);
                    if ($key === 'type' && !empty($value) && $value !== 'all') {
                        $current_selected_type = $value;
                    }
                }
            }
        }
    }
}

// Nombre de pages à montrer avant et après la page courante
$range = 2;
?>

<nav aria-label="Navigation des pages">
    <ul class="pagination justify-content-center">
        <!-- Bouton "Précédent" -->
        <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>"> <?php if ($is_ajax): ?>
                <a class="page-link ajax-page-link" href="javascript:void(0)" data-page="<?php echo ($current_page > 1) ? ($current_page - 1) : 1; ?>" <?php echo !empty($current_selected_type) ? 'data-type="' . htmlspecialchars($current_selected_type) . '"' : ''; ?> aria-label="Précédent">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="visually-hidden">Précédent</span>
                </a>
            <?php else: ?>
                <a class="page-link" href="<?php echo ($current_page > 1) ? "{$pagination_url}?page=" . ($current_page - 1) . $additional_params : 'javascript:void(0)'; ?>" aria-label="Précédent">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="visually-hidden">Précédent</span>
                </a>
            <?php endif; ?>
        </li>

        <?php
        // Premier page toujours visible
        if ($current_page > $range + 1) : ?>
            <li class="page-item"> <?php if ($is_ajax): ?>
                    <a class="page-link ajax-page-link" href="javascript:void(0)" data-page="1" <?php echo !empty($current_selected_type) ? 'data-type="' . htmlspecialchars($current_selected_type) . '"' : ''; ?>>1</a>
                <?php else: ?>
                    <a class="page-link" href="<?php echo "{$pagination_url}?page=1{$additional_params}"; ?>">1</a>
                <?php endif; ?>
            </li>
            <?php if ($current_page > $range + 2) : ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Pages autour de la page courante -->
        <?php for ($i = max(1, $current_page - $range); $i <= min($total_pages, $current_page + $range); $i++) : ?>
            <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>"> <?php if ($is_ajax): ?>
                    <a class="page-link ajax-page-link" href="javascript:void(0)" data-page="<?php echo $i; ?>" <?php echo !empty($current_selected_type) ? 'data-type="' . htmlspecialchars($current_selected_type) . '"' : ''; ?>><?php echo $i; ?></a>
                <?php else: ?>
                    <a class="page-link" href="<?php echo "{$pagination_url}?page={$i}{$additional_params}"; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            </li>
        <?php endfor; ?>

        <!-- Dernière page toujours visible -->
        <?php if ($current_page < $total_pages - $range) : ?>
            <?php if ($current_page < $total_pages - $range - 1) : ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
            <li class="page-item"> <?php if ($is_ajax): ?>
                    <a class="page-link ajax-page-link" href="javascript:void(0)" data-page="<?php echo $total_pages; ?>" <?php echo !empty($current_selected_type) ? 'data-type="' . htmlspecialchars($current_selected_type) . '"' : ''; ?>><?php echo $total_pages; ?></a>
                <?php else: ?>
                    <a class="page-link" href="<?php echo "{$pagination_url}?page={$total_pages}{$additional_params}"; ?>"><?php echo $total_pages; ?></a>
                <?php endif; ?>
            </li>
        <?php endif; ?>

        <!-- Bouton "Suivant" -->
        <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
            <?php if ($is_ajax): ?>
                <a class="page-link ajax-page-link" href="javascript:void(0)" data-page="<?php echo ($current_page < $total_pages) ? ($current_page + 1) : $total_pages; ?>" <?php echo !empty($current_selected_type) ? 'data-type="' . htmlspecialchars($current_selected_type) . '"' : ''; ?> aria-label="Suivant">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="visually-hidden">Suivant</span>
                </a>
            <?php else: ?>
                <a class="page-link" href="<?php echo ($current_page < $total_pages) ? "{$pagination_url}?page=" . ($current_page + 1) . $additional_params : 'javascript:void(0)'; ?>" aria-label="Suivant">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="visually-hidden">Suivant</span>
                </a>
            <?php endif; ?>
        </li>
    </ul>
</nav>