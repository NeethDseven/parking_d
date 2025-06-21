<div class="container">
    <h1 class="mb-4">Contactez-nous</h1>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Nos coordonnées</h5>
                    <div class="mt-4">
                        <p><i class="fas fa-map-marker-alt me-2 text-primary"></i> <strong>Adresse:</strong><br>
                            ParkMe In<br>
                            123 Rue du Parking<br>
                            75000 Paris, France
                        </p>
                        <p><i class="fas fa-phone me-2 text-primary"></i> <strong>Téléphone:</strong><br>
                            01 23 45 67 89
                        </p>
                        <p><i class="fas fa-envelope me-2 text-primary"></i> <strong>Email:</strong><br>
                            <a href="mailto:contact@parkmein.com">contact@parkmein.com</a>
                        </p>
                    </div>
                    <div class="mt-4">
                        <h5>Horaires d'ouverture</h5>
                        <p><i class="fas fa-clock me-2 text-primary"></i> <strong>Lundi - Vendredi:</strong> 8h - 20h</p>
                        <p><i class="fas fa-clock me-2 text-primary"></i> <strong>Samedi:</strong> 9h - 22h</p>
                        <p><i class="fas fa-clock me-2 text-primary"></i> <strong>Dimanche:</strong> 9h - 20h</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Envoyez-nous un message</h5>
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                        </div>
                    <?php else: ?>
                        <form action="<?php echo BASE_URL; ?>home/contact" method="post" class="mt-4">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom complet</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="sujet" class="form-label">Sujet</label>
                                <select class="form-select" id="sujet" name="sujet">
                                    <option value="demande_information">Demande d'information</option>
                                    <option value="reservation">Question sur une réservation</option>
                                    <option value="probleme">Signaler un problème</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="rgpd" name="rgpd" required>
                                <label class="form-check-label" for="rgpd">
                                    J'accepte que mes données soient traitées dans le cadre de ma demande
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary">Envoyer</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Comment nous trouver</h5>
                    <div class="ratio ratio-16x9 mt-3">
                        <!-- Placeholder for Google Maps, in production you'd use your own API key --> <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.1421866856915!2d2.3413574156743755!3d48.86091807928841!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e1f06e2b70f%3A0x40b82c3688c9460!2s123%20Rue%20du%20Faubourg%20Saint-Honor%C3%A9%2C%2075008%20Paris!5e0!3m2!1sen!2sfr!4v1623882189449!5m2!1sen!2sfr"
                            class="map-container" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>