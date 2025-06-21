<?php

/**
 * Component pour afficher l'onglet des informations personnelles
 *
 * @param array $user Les données de l'utilisateur
 */
function renderProfileInfoTab($user)
{
?>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0" data-i18n="profile.info.title">Informations personnelles</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>auth/updateProfile" method="post" id="updateProfileForm">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="prenom" class="form-label" data-i18n="profile.info.firstname">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="nom" class="form-label" data-i18n="profile.info.lastname">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label" data-i18n="profile.info.email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="telephone" class="form-label" data-i18n="profile.info.phone">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone" value="<?php echo htmlspecialchars($user['telephone']); ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" data-i18n="profile.info.update">Mettre à jour</button>
            </form>

            <hr class="my-4">

            <h5 class="mb-4" data-i18n="profile.info.password_change">Changer le mot de passe</h5>
            <form action="<?php echo BASE_URL; ?>auth/changePassword" method="post" id="changePasswordForm">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="current_password" class="form-label">Mot de passe actuel</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-secondary">Changer le mot de passe</button>
            </form>
        </div>
    </div>
<?php
}
?>