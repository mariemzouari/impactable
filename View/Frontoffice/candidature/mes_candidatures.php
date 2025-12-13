<?php 
$title = "Mes Candidatures | " . Config::SITE_NAME;
require_once __DIR__ . '/../templates/header.php'; 

// Initialiser les variables de message
$success = $success ?? '';
$error = $error ?? '';
?>
<style>
/* Section Statistiques pour Mes Candidatures */
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
.stat-custom-pending { border-top: 4px solid #ffc107; }
.stat-custom-review { border-top: 4px solid #17a2b8; }
.stat-custom-interview { border-top: 4px solid #007bff; }
.stat-custom-accepted { border-top: 4px solid #28a745; }
.stat-custom-rejected { border-top: 4px solid #dc3545; }

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

/* Styles pour les cartes de candidatures */
.candidatures-container {
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

.candidatures-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.candidature-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1.5rem;
    background: white;
    transition: box-shadow 0.3s ease;
    position: relative;
}

.candidature-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.candidature-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.candidature-title {
    margin: 0;
    color: #333;
    flex: 1;
}

.candidature-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
    color: #666;
    font-size: 0.9rem;
}

.candidature-meta span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.candidature-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #e0e0e0;
    padding-top: 1rem;
}

.candidature-actions {
    display: flex;
    gap: 0.5rem;
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
}

.status-en_attente { background: #fff3cd; color: #856404; }
.status-en_revue { background: #d1ecf1; color: #0c5460; }
.status-entretien { background: #cce7ff; color: #004085; }
.status-retenu { background: #d4edda; color: #155724; }
.status-refuse { background: #f8d7da; color: #721c24; }

/* Boutons */
.btn-small {
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
    border-radius: 4px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}

/* Alertes */
.alert {
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Responsive */
@media (max-width: 768px) {
    .candidatures-container {
        padding: 1rem;
    }
    
    .candidature-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .candidature-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .candidature-footer {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .candidature-actions {
        width: 100%;
        justify-content: flex-start;
    }
}
</style>

<div class="candidatures-container">
    <div class="section-header">
        <h1><i class="fas fa-briefcase"></i> Mes Candidatures</h1>
        <p class="text-muted">Retrouvez ici l'historique de toutes vos candidatures</p>
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

    <!-- Statistiques des candidatures -->
    <?php if (!empty($candidatures)): ?>
        <?php
        // Calcul des statistiques
        $stats = [
            'en_attente' => 0,
            'en_revue' => 0,
            'entretien' => 0,
            'retenu' => 0,
            'refuse' => 0
        ];
        
        foreach ($candidatures as $candidature) {
            $stats[$candidature['status']]++;
        }
        ?>
        
        <div class="stats-custom-section">
            <h2>Mes Statistiques de Candidatures</h2>
            <div class="stats-custom-grid">
                <div class="stat-custom-item stat-custom-total">
                    <div class="stat-custom-number"><?php echo count($candidatures); ?></div>
                    <div class="stat-custom-label">Total</div>
                </div>
                <div class="stat-custom-item stat-custom-pending">
                    <div class="stat-custom-number"><?php echo $stats['en_attente']; ?></div>
                    <div class="stat-custom-label">En attente</div>
                </div>
                <div class="stat-custom-item stat-custom-review">
                    <div class="stat-custom-number"><?php echo $stats['en_revue']; ?></div>
                    <div class="stat-custom-label">En revue</div>
                </div>
                <div class="stat-custom-item stat-custom-interview">
                    <div class="stat-custom-number"><?php echo $stats['entretien']; ?></div>
                    <div class="stat-custom-label">Entretien</div>
                </div>
                <div class="stat-custom-item stat-custom-accepted">
                    <div class="stat-custom-number"><?php echo $stats['retenu']; ?></div>
                    <div class="stat-custom-label">Retenus</div>
                </div>
                <div class="stat-custom-item stat-custom-rejected">
                    <div class="stat-custom-number"><?php echo $stats['refuse']; ?></div>
                    <div class="stat-custom-label">Refusés</div>
                </div>
            </div>
        </div>
    <?php endif; ?>

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
                        
                        <div class="candidature-actions">

                            <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir retirer votre candidature ? Cette action est irréversible.');">
                                <input type="hidden" name="candidature_id" value="<?php echo $candidature['Id_candidature']; ?>">
                                <button type="submit" name="action" value="supprimer_candidature" class="btn-danger btn-small">
                                    <i class="fas fa-trash"></i>
                                    Retirer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>