<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0">Suivi de votre réservation</h1>
                </div>
                <div class="card-body">
                    <p class="mb-4">Entrez le code de suivi qui vous a été fourni ou l'adresse email utilisée lors de votre réservation pour retrouver les détails de votre réservation.</p>

                    <form action="<?php echo BASE_URL; ?>home/findReservation" method="post">
                        <div class="mb-4">
                            <label for="tracking-code" class="form-label">Code de suivi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="text" class="form-control" id="tracking-code" name="tracking_code" placeholder="Saisissez votre code de suivi">
                                <button class="btn btn-primary" type="submit" name="track_by_code">Rechercher</button>
                            </div>
                            <div class="form-text">Exemple: a1b2c3d4e5f6g7h8i9j0</div>
                        </div>
                    </form>

                    <div class="text-center my-3">
                        <span class="text-muted">- OU -</span>
                    </div>

                    <form action="<?php echo BASE_URL; ?>home/findReservation" method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Saisissez l'email utilisé lors de la réservation">
                                <button class="btn btn-primary" type="submit" name="track_by_email">Rechercher</button>
                            </div>
                        </div>
                    </form>

                    <?php if (isset($_SESSION['guest_reservation'])): ?>
                        <div class="alert alert-info mt-4">
                            <h5>Vous avez une réservation récente</h5>
                            <p>Vous avez récemment effectué une réservation avec l'email: <strong><?php echo htmlspecialchars($_SESSION['guest_reservation']['email']); ?></strong></p>
                            <a href="<?php echo BASE_URL; ?>reservation/trackReservation/<?php echo $_SESSION['guest_reservation']['token']; ?>" class="btn btn-primary btn-sm mt-2">
                                <i class="fas fa-eye me-2"></i> Voir ma réservation
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Besoin d'aide?</h5>
                    <p>Si vous ne retrouvez pas votre réservation, n'hésitez pas à contacter notre service client au <strong>01 23 45 67 89</strong> ou par email à <a href="mailto:contact@parkmein.com">contact@parkmein.com</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>