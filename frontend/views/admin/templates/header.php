<!DOCTYPE html>
<?php
// Fonction helper pour éviter les erreurs liées à BASE_URL
function getBaseUrl()
{
    return defined('BASE_URL') ? BASE_URL : '/projet/parking_d/';
}
?>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?php echo getBaseUrl(); ?>">
    <meta name="description" content="<?php echo isset($description) ? $description : 'ParkMe In - Administration'; ?>">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- Ajout du data-page pour le chargement conditionnel des scripts -->
    <meta name="current-page" content="<?php echo isset($active_page) ? 'admin_' . $active_page : 'admin'; ?>">

    <?php if (isset($_SESSION['user'])): ?>
        <meta name="user-data" content="<?php echo htmlspecialchars(json_encode([
                                            'id' => $_SESSION['user']['id'],
                                            'email' => $_SESSION['user']['email'],
                                            'nom' => $_SESSION['user']['nom'],
                                            'prenom' => $_SESSION['user']['prenom'],
                                            'role' => $_SESSION['user']['role']
                                        ])); ?>">
    <?php endif; ?>
    
    <title><?php echo isset($title) ? $title : 'Administration - ParkMe In'; ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/webp" href="<?php echo getBaseUrl(); ?>frontend/assets/img/logo.webp">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- CSS Files -->
    <link href="<?php echo getBaseUrl(); ?>frontend/assets/css/app.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="<?php echo getBaseUrl(); ?>frontend/assets/css/components.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="<?php echo getBaseUrl(); ?>frontend/assets/css/admin.css?v=<?php echo time(); ?>" rel="stylesheet">

    <!-- Scripts -->
    <script src="<?php echo getBaseUrl(); ?>frontend/assets/js/core/logger.js?v=<?php echo time(); ?>"></script>
    <script src="<?php echo getBaseUrl(); ?>frontend/assets/js/core/app.js?v=<?php echo time(); ?>"></script>
</head>

<body data-page="<?php echo isset($active_page) ? 'admin_' . $active_page : 'admin'; ?>" class="admin-layout">
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <?php include FRONTEND_PATH . '/views/admin/templates/sidebar.php'; ?>
        
        <!-- Main content wrapper -->
        <div class="main-content">
            <!-- Bouton hamburger fixe en haut à droite - toujours visible pour fermer la sidebar -->
            <button class="btn sidebar-toggle-btn" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Page content -->
            <main class="content-area">
