<div class="container-fluid">
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Messages de Contact</h1>
                <div class="d-flex gap-2">
                    <!-- Filtres par statut -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-1"></i>
                            Filtrer par statut
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item <?php echo $statusFilter === 'all' ? 'active' : ''; ?>"
                                   href="<?php echo BASE_URL; ?>admin/contact?status=all">Tous</a></li>
                            <li><a class="dropdown-item <?php echo $statusFilter === 'nouveau' ? 'active' : ''; ?>"
                                   href="<?php echo BASE_URL; ?>admin/contact?status=nouveau">Nouveaux</a></li>
                            <li><a class="dropdown-item <?php echo $statusFilter === 'lu' ? 'active' : ''; ?>"
                                   href="<?php echo BASE_URL; ?>admin/contact?status=lu">Lus</a></li>
                            <li><a class="dropdown-item <?php echo $statusFilter === 'traite' ? 'active' : ''; ?>"
                                   href="<?php echo BASE_URL; ?>admin/contact?status=traite">Traités</a></li>
                            <li><a class="dropdown-item <?php echo $statusFilter === 'archive' ? 'active' : ''; ?>"
                                   href="<?php echo BASE_URL; ?>admin/contact?status=archive">Archivés</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white" style="background-color: #939393;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $stats['total']; ?></h4>
                            <p class="mb-0">Total</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-envelope fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $stats['nouveaux']; ?></h4>
                            <p class="mb-0">Nouveaux</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $stats['lus']; ?></h4>
                            <p class="mb-0">Lus</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-eye fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $stats['traites']; ?></h4>
                            <p class="mb-0">Traités</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des messages -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        Messages de Contact 
                        <?php if ($statusFilter !== 'all'): ?>
                            <span class="badge bg-secondary"><?php echo ucfirst($statusFilter); ?></span>
                        <?php endif; ?>
                        <span class="badge bg-light text-dark"><?php echo $totalMessages; ?> message(s)</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($messages)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun message trouvé</h5>
                            <p class="text-muted">
                                <?php if ($statusFilter !== 'all'): ?>
                                    Aucun message avec le statut "<?php echo $statusFilter; ?>" pour le moment.
                                <?php else: ?>
                                    Aucun message de contact reçu pour le moment.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Expéditeur</th>
                                        <th>Sujet</th>
                                        <th>Message</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($messages as $message): ?>
                                        <tr class="<?php echo $message['status'] === 'nouveau' ? 'table-warning' : ''; ?>">
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($message['nom']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($message['email']); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $sujets = [
                                                    'demande_information' => 'Demande d\'information',
                                                    'reservation' => 'Question sur réservation',
                                                    'probleme' => 'Signaler un problème',
                                                    'autre' => 'Autre'
                                                ];
                                                echo $sujets[$message['sujet']] ?? $message['sujet'];
                                                ?>
                                            </td>
                                            <td>
                                                <div style="max-width: 200px;">
                                                    <?php echo htmlspecialchars(substr($message['message'], 0, 100)); ?>
                                                    <?php if (strlen($message['message']) > 100): ?>...<?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
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
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo BASE_URL; ?>admin/contactView/<?php echo $message['id']; ?>"
                                                       class="btn btn-outline-primary" title="Voir les détails">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($message['status'] !== 'archive'): ?>
                                                        <button type="button" class="btn btn-outline-secondary"
                                                                onclick="archiveMessage(<?php echo $message['id']; ?>)"
                                                                title="Archiver">
                                                            <i class="fas fa-archive"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div class="card-footer">
                                <nav aria-label="Navigation des messages">
                                    <ul class="pagination justify-content-center mb-0">
                                        <?php if ($currentPage > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?php echo BASE_URL; ?>admin/contact?page=<?php echo $currentPage - 1; ?>&status=<?php echo $statusFilter; ?>">
                                                    Précédent
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                            <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                                <a class="page-link" href="<?php echo BASE_URL; ?>admin/contact?page=<?php echo $i; ?>&status=<?php echo $statusFilter; ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($currentPage < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?php echo BASE_URL; ?>admin/contact?page=<?php echo $currentPage + 1; ?>&status=<?php echo $statusFilter; ?>">
                                                    Suivant
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function archiveMessage(messageId) {
    if (confirm('Êtes-vous sûr de vouloir archiver ce message ?')) {
        window.location.href = '<?php echo BASE_URL; ?>admin/contactArchive/' + messageId;
    }
}
</script>
