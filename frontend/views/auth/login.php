<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i> Connexion</h4>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo BASE_URL; ?>auth/login" method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="Votre adresse email">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Votre mot de passe">
                                <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Rester connecté</label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i> Se connecter
                            </button>
                        </div>
                    </form>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo BASE_URL; ?>auth/forgot-password">Mot de passe oublié ?</a>
                            <a href="<?php echo BASE_URL; ?>auth/register">Créer un compte</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body text-center">
                    <h5>Vous êtes nouveau ?</h5>
                    <p>Créez un compte pour profiter de tous nos services.</p>
                    <a href="<?php echo BASE_URL; ?>auth/register" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus me-2"></i> S'inscrire maintenant
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>