<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès refusé - ParkMe In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-exclamation-triangle"></i>
                            Accès refusé
                        </h4>
                    </div>
                    <div class="card-body text-center">
                        <p class="lead">Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>
                        <p>Seuls les administrateurs peuvent accéder à cette section.</p>
                        <a href="<?php echo BASE_URL; ?>" class="btn btn-primary">
                            <i class="fas fa-home"></i>
                            Retour à l'accueil
                        </a>
                        <a href="<?php echo BASE_URL; ?>auth/login" class="btn btn-outline-secondary">
                            <i class="fas fa-sign-in-alt"></i>
                            Se connecter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>