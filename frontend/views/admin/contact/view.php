<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Détails du Message #<?php echo $message['id']; ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin/contact">Messages de Contact</a></li>
                            <li class="breadcrumb-item active">Message #<?php echo $message['id']; ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo BASE_URL; ?>admin/contact" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Détails du message -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Message de Contact</h5>
                        <?php
                        $statusClasses = [
                            'nouveau' => 'bg-warning',
                            'lu' => 'bg-info',
                            'traite' => 'bg-success',
                            'archive' => 'bg-secondary'
                        ];
                        $statusLabels = [
                            'nouveau' => 'Nouveau',
                            'lu' => 'Lu',
                            'traite' => 'Traité',
                            'archive' => 'Archivé'
                        ];
                        ?>
                        <span class="badge <?php echo $statusClasses[$message['status']]; ?>">
                            <?php echo $statusLabels[$message['status']]; ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informations de l'expéditeur -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Expéditeur</h6>
                            <p class="mb-0"><strong><?php echo htmlspecialchars($message['nom']); ?></strong></p>
                            <p class="mb-0">
                                <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>">
                                    <?php echo htmlspecialchars($message['email']); ?>
                                </a>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Informations</h6>
                            <p class="mb-1">
                                <strong>Sujet:</strong> 
                                <?php
                                $sujets = [
                                    'demande_information' => 'Demande d\'information',
                                    'reservation' => 'Question sur réservation',
                                    'probleme' => 'Signaler un problème',
                                    'autre' => 'Autre'
                                ];
                                echo $sujets[$message['sujet']] ?? $message['sujet'];
                                ?>
                            </p>
                            <p class="mb-0">
                                <strong>Date:</strong> 
                                <?php echo date('d/m/Y à H:i', strtotime($message['created_at'])); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="mb-4">
                        <h6 class="text-muted">Message</h6>
                        <div class="bg-light p-3 rounded">
                            <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                        </div>
                    </div>

                    <!-- Réponse admin si elle existe -->
                    <?php if (!empty($message['admin_response'])): ?>
                        <div class="mb-4">
                            <h6 class="text-muted">Réponse de l'administration</h6>
                            <div class="bg-success bg-opacity-10 border border-success border-opacity-25 p-3 rounded">
                                <?php echo nl2br(htmlspecialchars($message['admin_response'])); ?>
                            </div>
                            <?php if ($message['responded_at']): ?>
                                <small class="text-muted">
                                    Répondu le <?php echo date('d/m/Y à H:i', strtotime($message['responded_at'])); ?>
                                    <?php if (!empty($message['admin_nom'])): ?>
                                        par <?php echo htmlspecialchars($message['admin_nom'] . ' ' . $message['admin_prenom']); ?>
                                    <?php endif; ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Formulaire de réponse -->
                    <?php if ($message['status'] !== 'archive'): ?>
                        <div class="border-top pt-4">
                            <h6 class="text-muted">Répondre au message</h6>
                            <form action="<?php echo BASE_URL; ?>admin/contactRespond/<?php echo $message['id']; ?>" method="post">
                                <div class="mb-3">
                                    <label for="admin_response" class="form-label">Votre réponse</label>
                                    <textarea class="form-control" id="admin_response" name="admin_response" 
                                              rows="5" required placeholder="Saisissez votre réponse..."></textarea>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-reply me-1"></i> Envoyer la réponse
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="clearResponse()">
                                        <i class="fas fa-times me-1"></i> Annuler
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Actions et informations -->
        <div class="col-lg-4">
            <!-- Actions rapides -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Actions</h6>
                </div>
                <div class="card-body">
                    <!-- Changer le statut -->
                    <form action="<?php echo BASE_URL; ?>admin/contactUpdateStatus/<?php echo $message['id']; ?>" method="post" class="mb-3">
                        <input type="hidden" name="redirect" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <label for="status" class="form-label">Changer le statut</label>
                        <div class="input-group">
                            <select class="form-select" id="status" name="status">
                                <option value="nouveau" <?php echo $message['status'] === 'nouveau' ? 'selected' : ''; ?>>Nouveau</option>
                                <option value="lu" <?php echo $message['status'] === 'lu' ? 'selected' : ''; ?>>Lu</option>
                                <option value="traite" <?php echo $message['status'] === 'traite' ? 'selected' : ''; ?>>Traité</option>
                                <option value="archive" <?php echo $message['status'] === 'archive' ? 'selected' : ''; ?>>Archivé</option>
                            </select>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-save"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Actions supplémentaires -->
                    <div class="d-grid gap-2">
                        <?php if ($message['status'] !== 'archive'): ?>
                            <button type="button" class="btn btn-outline-warning" onclick="archiveMessage()">
                                <i class="fas fa-archive me-1"></i> Archiver
                            </button>
                        <?php endif; ?>
                        
                        <button type="button" class="btn btn-outline-danger" onclick="deleteMessage()">
                            <i class="fas fa-trash me-1"></i> Supprimer définitivement
                        </button>
                    </div>
                </div>
            </div>

            <!-- Informations techniques -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Informations techniques</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-12">
                            <small class="text-muted">ID du message:</small>
                            <div><?php echo $message['id']; ?></div>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Créé le:</small>
                            <div><?php echo date('d/m/Y à H:i:s', strtotime($message['created_at'])); ?></div>
                        </div>
                        <?php if ($message['updated_at'] !== $message['created_at']): ?>
                            <div class="col-12">
                                <small class="text-muted">Modifié le:</small>
                                <div><?php echo date('d/m/Y à H:i:s', strtotime($message['updated_at'])); ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($message['admin_user_id'])): ?>
                            <div class="col-12">
                                <small class="text-muted">Traité par:</small>
                                <div>
                                    <?php if (!empty($message['admin_nom'])): ?>
                                        <?php echo htmlspecialchars($message['admin_nom'] . ' ' . $message['admin_prenom']); ?>
                                    <?php else: ?>
                                        Admin #<?php echo $message['admin_user_id']; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function clearResponse() {
    document.getElementById('admin_response').value = '';
}

function archiveMessage() {
    if (confirm('Êtes-vous sûr de vouloir archiver ce message ?')) {
        window.location.href = '<?php echo BASE_URL; ?>admin/contactArchive/<?php echo $message['id']; ?>';
    }
}

function deleteMessage() {
    if (confirm('Êtes-vous sûr de vouloir supprimer définitivement ce message ?\n\nCette action est irréversible !')) {
        // Créer un formulaire pour la suppression (méthode POST requise)
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo BASE_URL; ?>admin/contactDelete/<?php echo $message['id']; ?>';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
