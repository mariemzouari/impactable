<?php 
$title = "Mes Offres | " . Config::SITE_NAME;
require_once __DIR__ . '/../templates/header.php'; 

// Créer une instance de CandidatureManager pour la vue
$candidatureManager = new Candidature();
?>
<style>
/* Section Statistiques pour Mes Offres */
.stats-custom-section {
    margin: 2rem 0;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 10px;
}

.stats-custom-section h2 {
    margin-bottom: 1rem;
    color: #333;
    font-size: 1.5rem;
}

.stats-custom-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.stat-custom-item {
    text-align: center;
    padding: 1rem;
    border-radius: 8px;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-custom-total { border-top: 4px solid #6c757d; }
.stat-custom-active { border-top: 4px solid #28a745; }
.stat-custom-candidates { border-top: 4px solid #007bff; }

.stat-custom-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stat-custom-label {
    font-size: 0.9rem;
    color: #666;
    text-transform: uppercase;
    font-weight: 500;
}

/* Responsive pour les stats */
@media (max-width: 768px) {
    .stats-custom-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .stat-custom-number {
        font-size: 1.5rem;
    }
}

/* Styles pour les cartes d'offres */
.mes-offres-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.section-header {
    text-align: center;
    margin-bottom: 3rem;
}

.section-header h1 {
    color: #333;
    margin-bottom: 0.5rem;
}

.offres-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.offre-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1.5rem;
    background: white;
    transition: box-shadow 0.3s ease;
}

.offre-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.offre-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.offre-title {
    margin: 0 0 0.5rem 0;
    color: #333;
    flex: 1;
}

.offre-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    color: #666;
    font-size: 0.9rem;
}

.offre-meta span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.offre-status {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-end;
}

.card-excerpt {
    color: #666;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.offre-footer {
    border-top: 1px solid #e0e0e0;
    padding-top: 1rem;
    margin-bottom: 1rem;
}

.offre-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #666;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    color: #ddd;
}

.empty-state h3 {
    margin-bottom: 0.5rem;
    color: #333;
}

/* Badges de statut */
.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    text-transform: uppercase;
    white-space: nowrap;
}

.status-active { background: #d4edda; color: #155724; }
.status-expired { background: #f8d7da; color: #721c24; }

/* Responsive */
@media (max-width: 768px) {
    .mes-offres-container {
        padding: 1rem;
    }
    
    .offre-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .offre-status {
        flex-direction: row;
        align-items: center;
        width: 100%;
        justify-content: flex-start;
    }
    
    .offre-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .offre-actions {
        flex-direction: column;
    }
    
    .offre-actions .btn {
        text-align: center;
    }
}
</style>

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
        <?php
        // Calcul des statistiques
        $activeOffres = array_filter($offres, function($offre) {
            return strtotime($offre['date_expiration']) >= time() || empty($offre['date_expiration']);
        });
        
        $totalCandidatures = 0;
        foreach ($offres as $offre) {
            $totalCandidatures += $candidatureManager->getCountByOffre($offre['Id_offre']);
        }
        ?>
        
        <div class="stats-custom-section">
            <h2>Statistiques de Mes Offres</h2>
            <div class="stats-custom-grid">
                <div class="stat-custom-item stat-custom-total">
                    <div class="stat-custom-number"><?php echo count($offres); ?></div>
                    <div class="stat-custom-label">Offres publiées</div>
                </div>
                <div class="stat-custom-item stat-custom-active">
                    <div class="stat-custom-number"><?php echo count($activeOffres); ?></div>
                    <div class="stat-custom-label">Offres actives</div>
                </div>
                <div class="stat-custom-item stat-custom-candidates">
                    <div class="stat-custom-number"><?php echo $totalCandidatures; ?></div>
                    <div class="stat-custom-label">Candidatures totales</div>
                </div>
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