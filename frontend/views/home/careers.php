<meta name="current-page" content="careers">
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="mb-4">Rejoignez notre équipe</h1>

            <div class="bg-light p-4 rounded mb-4">
                <h2 class="h3 mb-3">Pourquoi travailler chez <?php echo APP_NAME; ?> ?</h2>
                <p>Chez <?php echo APP_NAME; ?>, nous croyons que notre plus grande force réside dans notre équipe. Nous sommes à la recherche de personnes talentueuses, motivées et passionnées par l'innovation pour nous aider à transformer l'expérience de stationnement urbain.</p>
                <p>Rejoindre notre équipe, c'est s'engager dans une aventure professionnelle stimulante où votre contribution aura un impact direct sur notre succès et sur l'amélioration de la mobilité urbaine.</p>
            </div>

            <h2 class="h3 mb-4">Nos offres d'emploi actuelles</h2>

            <?php
            // Liste des postes fictifs
            $postes = [
                [
                    'titre' => 'Développeur Full-Stack',
                    'departement' => 'Technologie & Innovation',
                    'type' => 'CDI',
                    'lieu' => 'Paris',
                    'description' => 'Nous recherchons un développeur Full-Stack expérimenté pour rejoindre notre équipe technique. Vous serez responsable du développement et de la maintenance de nos applications web et mobiles.',
                    'date' => '15/06/2025'
                ],
                [
                    'titre' => 'Chargé de clientèle',
                    'departement' => 'Service Client',
                    'type' => 'CDI',
                    'lieu' => 'Lyon',
                    'description' => 'Rejoignez notre équipe de service client et aidez nos utilisateurs à profiter pleinement de nos solutions de stationnement intelligent.',
                    'date' => '10/06/2025'
                ],
                [
                    'titre' => 'Ingénieur IoT',
                    'departement' => 'Technologie & Innovation',
                    'type' => 'CDI',
                    'lieu' => 'Paris',
                    'description' => 'Participez au développement de nos capteurs intelligents et de nos solutions IoT pour améliorer l\'expérience de stationnement.',
                    'date' => '05/06/2025'
                ],
                [
                    'titre' => 'Responsable Marketing Digital',
                    'departement' => 'Marketing',
                    'type' => 'CDI',
                    'lieu' => 'Paris',
                    'description' => 'Définissez et mettez en œuvre notre stratégie de marketing digital pour accroître notre notoriété et acquérir de nouveaux clients.',
                    'date' => '20/06/2025'
                ],
                [
                    'titre' => 'Technicien de maintenance',
                    'departement' => 'Opérations',
                    'type' => 'CDD',
                    'lieu' => 'Marseille',
                    'description' => 'Assurez l\'installation et la maintenance de nos équipements dans les parkings partenaires.',
                    'date' => '01/06/2025'
                ]
            ];

            // Afficher les postes
            foreach ($postes as $index => $poste):
            ?>
                <div class="card mb-3 job-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h3 class="h5 card-title mb-0"><?php echo htmlspecialchars($poste['titre']); ?></h3>
                            <span class="badge bg-primary"><?php echo htmlspecialchars($poste['type']); ?></span>
                        </div>
                        <div class="mb-3">
                            <span class="me-3"><i class="fas fa-building me-1"></i> <?php echo htmlspecialchars($poste['departement']); ?></span>
                            <span class="me-3"><i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($poste['lieu']); ?></span>
                            <span><i class="fas fa-calendar-alt me-1"></i> Publié le <?php echo htmlspecialchars($poste['date']); ?></span>
                        </div>
                        <p class="card-text"><?php echo htmlspecialchars($poste['description']); ?></p>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#jobModal"
                            data-job-title="<?php echo htmlspecialchars($poste['titre']); ?>"
                            data-job-ref="REF<?php echo 20250600 + $index; ?>">
                            Voir l'offre
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Si aucun poste n'est disponible -->
            <?php if (empty($postes)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Aucune offre d'emploi n'est disponible actuellement. Revenez bientôt ou envoyez-nous une candidature spontanée !
                </div>
            <?php endif; ?>

            <div class="mt-5">
                <h3 class="h4 mb-3">Candidature spontanée</h3>
                <p>Vous ne trouvez pas le poste qui vous correspond ? Envoyez-nous une candidature spontanée !</p>
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#candidatureModal">
                    <i class="fas fa-paper-plane me-2"></i>Envoyer ma candidature
                </a>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="h5 mb-0">Avantages employés</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Flexibilité du travail (hybride)</li>
                        <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Équipe jeune et dynamique</li>
                        <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Participation aux bénéfices</li>
                        <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Formation continue</li>
                        <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Titres-restaurant</li>
                        <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Mutuelle d'entreprise</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h3 class="h5 mb-0">Témoignages</h3>
                </div>
                <div class="card-body">
                    <div class="testimonial mb-3 pb-3 border-bottom">
                        <p class="fst-italic">"Travailler chez <?php echo APP_NAME; ?> est une expérience enrichissante. J'ai l'opportunité de participer à des projets innovants et d'évoluer dans un environnement de travail stimulant."</p>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white icon-circle">
                                    <span>ML</span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <strong>Marie L.</strong>
                                <div class="text-muted small">Développeuse front-end depuis 2023</div>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial">
                        <p class="fst-italic">"Ce qui me plaît chez <?php echo APP_NAME; ?>, c'est l'impact concret de notre travail sur la vie quotidienne des utilisateurs. Nous contribuons à améliorer la mobilité urbaine."</p>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white icon-circle">
                                    <span>TD</span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <strong>Thomas D.</strong>
                                <div class="text-muted small">Chef de projet depuis 2022</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour voir une offre d'emploi -->
<div class="modal fade" id="jobModal" tabindex="-1" aria-labelledby="jobModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jobModalLabel">Détails de l'offre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <!-- Le contenu sera injecté dynamiquement via JavaScript -->
                <div class="text-center text-muted my-5">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="mt-2">Chargement des détails de l'offre...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour candidature spontanée -->
<div class="modal fade" id="candidatureModal" tabindex="-1" aria-labelledby="candidatureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="candidatureModalLabel">Candidature spontanée</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <form id="candidatureForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="col-md-6">
                            <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="prenom" name="prenom" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="poste" class="form-label">Poste souhaité <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="poste" name="poste" required>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Lettre de motivation <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="cv" class="form-label">CV (PDF, max 2Mo) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="cv" name="cv" accept=".pdf" required>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="rgpd" name="rgpd" required>
                        <label class="form-check-label" for="rgpd">J'accepte que mes données personnelles soient conservées par <?php echo APP_NAME; ?> à des fins de recrutement. <span class="text-danger">*</span></label>
                    </div>

                    <p class="text-muted small">Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires.</p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="submitCandidature()">
                    <i class="fas fa-paper-plane me-2"></i>Envoyer ma candidature
                </button>
            </div>
        </div>
    </div>
</div>