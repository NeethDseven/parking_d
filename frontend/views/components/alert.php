<?php

/**
 * Component pour afficher des messages d'alerte
 *
 * @param string $type Le type d'alerte (success, danger, warning, info)
 * @param string $message Le message à afficher
 * @param bool $dismissible Si l'alerte peut être fermée
 * @param bool $autoclose Si l'alerte doit se fermer automatiquement
 */
function renderAlert($type, $message, $dismissible = true, $autoclose = true)
{
    $classes = 'alert alert-' . $type;
    if ($dismissible) {
        $classes .= ' alert-dismissible fade show';
    }
    if (!$autoclose) {
        $classes .= ' alert-permanent';
    }
?>
    <div class="<?php echo $classes; ?>" role="alert">
        <?php echo $message; ?>
        <?php if ($dismissible): ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <?php endif; ?>
    </div>
<?php
}

/**
 * Affiche les messages flash depuis la session
 * 
 * @param array $types Les types de messages à afficher (success, error, info, warning)
 */
function renderFlashMessages($types = ['success', 'error', 'info', 'warning'])
{
    foreach ($types as $type) {
        $alertType = ($type === 'error') ? 'danger' : $type;

        if (isset($_SESSION[$type])) {
            renderAlert($alertType, $_SESSION[$type]);
            unset($_SESSION[$type]);
        }
    }
}
?>