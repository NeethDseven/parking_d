<td class="align-middle">
    <?php if ($reservation['status'] === 'confirmée'): ?>
        <span class="badge bg-success">Confirmée</span>
    <?php elseif ($reservation['status'] === 'en_attente'): ?>
        <span class="badge bg-warning text-dark">En attente</span>
    <?php elseif ($reservation['status'] === 'annulée'): ?>
        <span class="badge bg-danger">Annulée</span>
    <?php elseif ($reservation['status'] === 'terminée'): ?>
        <span class="badge bg-secondary">Terminée</span>
    <?php endif; ?>
</td>