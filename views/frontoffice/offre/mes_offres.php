<?php 
$title = "Mes Offres | " . Config::SITE_NAME;
require_once __DIR__ . '/../templates/header.php'; 

// Créer une instance de CandidatureManager pour la vue
$candidatureManager = new Candidature();
?>

<div class="mes-offres-container">
    <div class="section-header">
        <h1><i class="fas fa-briefcase"></i> Mes Offres Publiées</h1>
        <p class="text-muted">Gérez vos offres et consultez les candidatures</p>
    </div>

    <!-- Messages -->
    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo $success; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <!-- Statistiques -->
    <?php if (!empty($offres)): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($offres); ?></div>
                <div class="stat-label">Offres publiées</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php 
                    $activeOffres = array_filter($offres, function($offre) {
                        return strtotime($offre['date_expiration']) >= time() || empty($offre['date_expiration']);
                    });
                    echo count($activeOffres);
                    ?>
                </div>
                <div class="stat-label">Offres actives</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php 
                    $totalCandidatures = 0;
                    foreach ($offres as $offre) {
                        $totalCandidatures += $candidatureManager->getCountByOffre($offre['Id_offre']);
                    }
                    echo $totalCandidatures;
                    ?>
                </div>
                <div class="stat-label">Candidatures totales</div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Liste des offres -->
    <?php if (empty($offres)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>Aucune offre publiée</h3>
            <p class="text-muted">Vous n'avez pas encore publié d'offres.</p>
            <a href="index.php?action=poster-offre" class="btn primary">
                <i class="fas fa-plus-circle"></i>
                Publier votre première offre
            </a>
        </div>
    <?php else: ?>
        <div class="offres-list">
            <?php foreach ($offres as $offre): 
                $isExpired = !empty($offre['date_expiration']) && strtotime($offre['date_expiration']) < time();
                $candidatureCount = $candidatureManager->getCountByOffre($offre['Id_offre']);
            ?>
                <div class="offre-card">
                    <div class="offre-header">
                        <div>
                            <h3 class="offre-title"><?php echo Utils::escape($offre['titre']); ?></h3>
                            <div class="offre-meta">
                                <span>
                                    <i class="fas fa-briefcase"></i>
                                    <?php echo Utils::formatTypeOffre($offre['type_offre']); ?>
                                </span>
                                <span>
                                    <i class="fas fa-laptop-house"></i>
                                    <?php echo Utils::formatMode($offre['mode']); ?>
                                </span>
                                <span>
                                    <i class="fas fa-clock"></i>
                                    <?php echo str_replace('_', ' ', $offre['horaire']); ?>
                                </span>
                                <?php if (!empty($offre['lieu'])): ?>
                                    <span>
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo Utils::escape($offre['lieu']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="offre-status">
                            <span class="status-badge <?php echo $isExpired ? 'status-expired' : 'status-active'; ?>">
                                <?php echo $isExpired ? 'Expirée' : 'Active'; ?>
                            </span>
                            <span class="status-badge" style="background: #cce7ff; color: #004085;">
                                <i class="fas fa-users"></i> <?php echo $candidatureCount; ?> candidats
                            </span>
                        </div>
                    </div>
                    
                    <p class="card-excerpt"><?php echo Utils::escape(Utils::getExcerpt($offre['description'])); ?></p>
                    
                    <div class="offre-footer">
                        <span class="text-muted small">
                            <i class="far fa-clock"></i>
                            Publiée le <?php echo date('d/m/Y à H:i', strtotime($offre['date_publication'])); ?>
                            <?php if (!empty($offre['date_expiration'])): ?>
                                • Expire le <?php echo date('d/m/Y', strtotime($offre['date_expiration'])); ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <div class="offre-actions">
                        <a href="index.php?action=gestion-offre&id=<?php echo $offre['Id_offre']; ?>" class="btn primary btn-small">
                            <i class="fas fa-eye"></i>
                            Voir candidatures
                        </a>
                        <a href="index.php?action=modifier-offre&id=<?php echo $offre['Id_offre']; ?>" class="btn secondary btn-small">
                            <i class="fas fa-edit"></i>
                            Modifier
                        </a>
                        <a href="index.php?action=supprimer-offre&id=<?php echo $offre['Id_offre']; ?>" 
                           class="btn ghost btn-small" 
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ? Cette action est irréversible.');">
                            <i class="fas fa-trash"></i>
                            Supprimer
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>