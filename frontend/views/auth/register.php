<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i> Inscription</h4>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo BASE_URL; ?>auth/register" method="post" id="register-form">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="prenom" class="form-label">Prénom</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="prenom" name="prenom" required value="<?php echo isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="nom" class="form-label">Nom</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="nom" name="nom" required value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="telephone" class="form-label">Téléphone <span class="text-muted">(optionnel)</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" id="telephone" name="telephone" value="<?php echo isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : ''; ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">Le mot de passe doit contenir au moins 6 caractères.</div>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                                <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="password-match-error">
                                Les mots de passe ne correspondent pas.
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="accept_terms" name="accept_terms" required>
                            <label class="form-check-label" for="accept_terms">
                                J'accepte les <a href="<?php echo BASE_URL; ?>home/terms" target="_blank">conditions d'utilisation</a> et la <a href="<?php echo BASE_URL; ?>home/privacy" target="_blank">politique de confidentialité</a>
                            </label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="register-btn">
                                <i class="fas fa-user-plus me-2"></i> S'inscrire
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    Vous avez déjà un compte ? <a href="<?php echo BASE_URL; ?>auth/login">Se connecter</a>
                </div>
            </div>
        </div>
    </div>
</div>