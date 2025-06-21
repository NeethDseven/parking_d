<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="base-url" content="<?php echo BASE_URL; ?>">
    <meta name="current-page" content="admin_<?php echo isset($active_page) ? $active_page : 'dashboard'; ?>">
    <title><?php echo isset($title) ? $title : 'Administration - ' . APP_NAME; ?></title>

    <!-- Script de rafraîchissement du cache -->
    <!-- cache-refresh.js a été remplacé par services/cacheService.js chargé par app.js -->

    <!-- Favicon -->
    <link rel="icon" type="image/webp" href="<?php echo BASE_URL; ?>frontend/assets/img/logo.webp">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Application principale -->
    <script src="<?php echo BASE_URL; ?>frontend/assets/js/core/app.js?v=<?php echo time(); ?>"></script> <!-- CSS Application - Structure optimisée -->
    <link href="<?php echo BASE_URL; ?>frontend/assets/css/app.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>

<body data-page="admin_<?php echo isset($active_page) ? $active_page : 'dashboard'; ?>">
    <div class="d-flex">
        <?php include FRONTEND_PATH . '/views/admin/templates/sidebar.php'; ?>

        <!-- Contenu principal -->
        <div class="content">
            <!-- Le contenu de chaque page sera inséré ici -->