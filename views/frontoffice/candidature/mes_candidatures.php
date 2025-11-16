<?php 
$title = "Mes Candidatures | " . Config::SITE_NAME;
require_once __DIR__ . '/../templates/header.php'; 
?>

<div class="candidatures-container">
    <div class="section-header">
        <h1><i class="fas fa-briefcase"></i> Mes Candidatures</h1>
        <p class="text-muted">Retrouvez ici l'historique de toutes vos candidatures</p>
    </div>

    <?php if (empty($candidatures)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>Aucune candidature</h3>
            <p class="text-muted">Vous n'avez pas encore postulé à des offres.</p>
            <a href="index.php?action=offres" class="btn primary">
                <i class="fas fa-search"></i>
                Parcourir les offres
            </a>
        </div>
    <?php else: ?>
        <div class="candidatures-list">
            <?php foreach ($candidatures as $candidature): ?>
                <div class="candidature-card">
                    <div class="candidature-header">
                        <h3 class="candidature-title"><?php echo Utils::escape($candidature['titre']); ?></h3>
                        <span class="status-badge status-<?php echo $candidature['status']; ?>">
                            <?php 
                            $statusLabels = [
                                'en_attente' => 'En attente',
                                'en_revue' => 'En revue',
                                'entretien' => 'Entretien',
                                'retenu' => 'Retenu',
                                'refuse' => 'Refusé'
                            ];
                            echo $statusLabels[$candidature['status']] ?? $candidature['status'];
                            ?>
                        </span>
                    </div>
                    
                    <div class="candidature-meta">
                        <span>
                            <i class="fas fa-briefcase"></i>
                            <?php echo Utils::formatTypeOffre($candidature['type_offre']); ?>
                        </span>
                        <span>
                            <i class="fas fa-laptop-house"></i>
                            <?php echo Utils::formatMode($candidature['mode']); ?>
                        </span>
                        <?php if (!empty($candidature['lieu'])): ?>
                            <span>
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo Utils::escape($candidature['lieu']); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="candidature-footer">
                        <span class="text-muted small">
                            <i class="far fa-clock"></i>
                            Candidature envoyée le <?php echo date('d/m/Y à H:i', strtotime($candidature['date_candidature'])); ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>