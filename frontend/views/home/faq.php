<div class="container py-5">
    <div class="row mb-5 align-items-center faq-header">
        <div class="col-md-8">
            <h1 class="display-4 mb-3">Foire Aux Questions</h1>
            <p class="lead">Voici les réponses aux questions fréquemment posées sur notre service de stationnement.</p>
            <!-- Ajout d'un champ de recherche pour la FAQ -->
            <div class="faq-search mt-4">
                <input type="text" class="form-control" id="faq-search-input" placeholder="Rechercher une question...">
            </div>
        </div>
        <div class="col-md-4 text-center">
            <img src="<?php echo BASE_URL; ?>frontend/assets/img/unequestion.webp" alt="FAQ" class="img-fluid faq-header-image img-height-200">
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="accordion shadow-sm faq-accordion" id="faqAccordion">
                <!-- Question 1 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading1">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                            Comment réserver une place de parking ?
                        </button>
                    </h2>
                    <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Pour réserver une place de parking, suivez ces étapes simples :</p>
                            <ol>
                                <li>Connectez-vous à votre compte (ou créez-en un si vous n'en avez pas)</li>
                                <li>Rendez-vous dans la section "Places disponibles"</li>
                                <li>Choisissez une place qui correspond à vos besoins</li>
                                <li>Cliquez sur "Réserver" et suivez les instructions pour compléter votre réservation</li>
                                <li>Après paiement, vous recevrez un code d'accès par email</li>
                            </ol>
                            <p>Vous pouvez également réserver en tant qu'invité sans créer de compte.</p>
                        </div>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                            Quels sont les moyens de paiement acceptés ?
                        </button>
                    </h2>
                    <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Nous acceptons les moyens de paiement suivants :</p>
                            <ul>
                                <li>Cartes de crédit/débit (Visa, Mastercard, American Express)</li>
                                <li>PayPal</li>
                                <li>Virement bancaire (pour les réservations à long terme uniquement)</li>
                            </ul>
                            <p>Tous les paiements sont sécurisés et vous recevrez une facture par email après chaque transaction.</p>
                        </div>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading3">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                            Comment annuler une réservation ?
                        </button>
                    </h2>
                    <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Pour annuler une réservation :</p>
                            <ol>
                                <li>Connectez-vous à votre compte</li>
                                <li>Accédez à la section "Mon profil" > "Mes réservations"</li>
                                <li>Trouvez la réservation que vous souhaitez annuler</li>
                                <li>Cliquez sur "Annuler" et confirmez l'annulation</li>
                            </ol>
                            <p><strong>Politique d'annulation :</strong> Les annulations effectuées au moins 24 heures avant le début de la réservation sont intégralement remboursées. Pour les annulations tardives, des frais peuvent s'appliquer.</p>
                        </div>
                    </div>
                </div>

                <!-- Question 4 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading4">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                            Quels sont les horaires d'ouverture du parking ?
                        </button>
                    </h2>
                    <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Nos horaires d'ouverture sont les suivants :</p>
                            <ul>
                                <li><strong>Lundi à jeudi :</strong> 8h00 - 20h00</li>
                                <li><strong>Vendredi :</strong> 8h00 - 22h00</li>
                                <li><strong>Samedi :</strong> 9h00 - 22h00</li>
                                <li><strong>Dimanche :</strong> 9h00 - 20h00</li>
                            </ul>
                            <p>Pour les abonnés disposant d'une carte d'accès, le parking est accessible 24h/24 et 7j/7.</p>
                        </div>
                    </div>
                </div>

                <!-- Question 5 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading5">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                            Comment fonctionnent les places pour véhicules électriques ?
                        </button>
                    </h2>
                    <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="heading5" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Nos places pour véhicules électriques sont équipées de bornes de recharge :</p>
                            <ul>
                                <li>Puissance de charge : jusqu'à 22kW</li>
                                <li>Connecteurs disponibles : Type 2 et CHAdeMO</li>
                                <li>Le coût de la recharge est inclus dans le tarif horaire</li>
                                <li>Temps de charge maximum : 4 heures consécutives</li>
                            </ul>
                            <p>Pour utiliser la borne de recharge, il vous suffit de suivre les instructions affichées sur place ou dans l'email de confirmation de votre réservation.</p>
                        </div>
                    </div>
                </div>

                <!-- Question 6 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading6">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
                            Comment suivre ma réservation si je n'ai pas de compte ?
                        </button>
                    </h2>
                    <div id="collapse6" class="accordion-collapse collapse" aria-labelledby="heading6" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Si vous avez réservé en tant qu'invité, vous pouvez suivre votre réservation de deux façons :</p>
                            <ol>
                                <li><strong>Via le code de suivi :</strong> Un code de suivi unique vous est envoyé par email lors de la confirmation de votre réservation. Utilisez ce code dans la section "Suivi de réservation" de notre site.</li>
                                <li><strong>Via votre email :</strong> Sur la page "Suivi de réservation", vous pouvez également entrer l'email utilisé lors de votre réservation pour accéder à toutes vos réservations.</li>
                            </ol>
                            <p>Pour accéder à la page de suivi de réservation, <a href="<?php echo BASE_URL; ?>home/reservationTracking">cliquez ici</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-4 shadow-sm border-0 rounded-card">
                <div class="card-header bg-primary text-white rounded-card-header">
                    <h5 class="mb-0"><i class="fas fa-headset me-2"></i>Besoin d'aide ?</h5>
                </div>
                <div class="card-body">
                    <p>Vous ne trouvez pas la réponse à votre question ? Contactez notre équipe support :</p>
                    <ul class="list-unstyled mb-3">
                        <li class="mb-3"><i class="fas fa-envelope me-2 text-primary"></i> <a href="mailto:support@parkmein.com" class="text-decoration-none">support@parkmein.com</a></li>
                        <li class="mb-3"><i class="fas fa-phone-alt me-2 text-primary"></i> 01 23 45 67 89</li>
                        <li class="mb-3"><i class="fas fa-clock me-2 text-primary"></i> Du lundi au vendredi, 9h à 18h</li>
                        <li class="mb-3"><i class="fas fa-map-marker-alt me-2 text-primary"></i> 123 Rue du Parking, 75001 Paris</li>
                    </ul>
                    <hr>
                    <div class="d-grid">
                        <a href="<?php echo BASE_URL; ?>home/contact" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Nous contacter
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations utiles</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="<?php echo BASE_URL; ?>home/terms" class="text-decoration-none">
                                <i class="fas fa-file-contract me-2"></i>Conditions générales
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="<?php echo BASE_URL; ?>home/privacy" class="text-decoration-none">
                                <i class="fas fa-user-shield me-2"></i>Politique de confidentialité
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="<?php echo BASE_URL; ?>home/about" class="text-decoration-none">
                                <i class="fas fa-building me-2"></i>À propos de nous
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>