<?php 
$title = "Gestion Candidatures - " . Utils::escape($offre['titre']) . " | " . Config::SITE_NAME;
require_once __DIR__ . '/../templates/header.php'; 
?>
<style>
/* Section Statistiques */
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

/* Le reste du CSS pour les candidats reste inchangé */
.candidature-card {
    margin-bottom: 1.5rem;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1.5rem;
    background: white;
    transition: box-shadow 0.3s ease;
}

.candidature-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.candidature-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.candidate-avatar {
    width: 50px;
    height: 50px;
    background: #007bff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.candidature-info {
    flex: 1;
}

.candidature-info h3 {
    margin: 0 0 0.5rem 0;
    color: #333;
}

.candidature-contact {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    font-size: 0.9rem;
    color: #666;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.handicap-info {
    color: #e74c3c;
    font-weight: 500;
}

.documents-links {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.doc-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #f8f9fa;
    border-radius: 4px;
    text-decoration: none;
    color: #007bff;
    transition: background-color 0.3s ease;
}

.doc-link:hover {
    background: #e9ecef;
}

.lettre-motivation,
.additional-info {
    margin-bottom: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 4px;
}

.lettre-motivation h4,
.additional-info h4 {
    margin: 0 0 0.5rem 0;
    color: #333;
}

.candidature-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e0e0e0;
}

.candidature-date {
    color: #666;
    font-size: 0.9rem;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.action-form {
    margin: 0;
}

@media (max-width: 768px) {
    .candidature-header {
        flex-direction: column;
    }
    
    .candidature-footer {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .action-buttons {
        width: 100%;
        justify-content: flex-start;
    }
    
    .candidature-contact {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .documents-links {
        flex-direction: column;
    }
}
</style>

<div class="gestion-offre-container">
    <!-- En-tête de l'offre -->
    <div class="offre-header">
        <h1><?php echo Utils::escape($offre['titre']); ?></h1>
        <div class="offer-meta">
            <span class="offer-type">
                <i class="fas fa-briefcase"></i>
                <?php echo Utils::formatTypeOffre($offre['type_offre']); ?>
            </span>
            <span class="offer-mode">
                <i class="fas fa-laptop-house"></i>
                <?php echo Utils::formatMode($offre['mode']); ?>
            </span>
            <span class="offer-schedule">
                <i class="fas fa-clock"></i>
                <?php echo str_replace('_', ' ', $offre['horaire']); ?>
            </span>
        </div>
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
            <h2>Statistiques des Candidatures</h2>
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

    <!-- Liste des candidatures -->
    <?php if (empty($candidatures)): ?>
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <h3>Aucune candidature</h3>
            <p class="text-muted">Aucun candidat n'a encore postulé à cette offre.</p>
        </div>
    <?php else: ?>
        <div class="candidatures-list">
            <?php foreach ($candidatures as $candidature): ?>
                <div class="candidature-card">
                    <div class="candidature-header">
                        <div class="candidature-info">
                            <h3><?php echo Utils::escape($candidature['prenom'] . ' ' . $candidature['nom']); ?></h3>
                            <div class="candidature-contact">
                                <i class="fas fa-envelope"></i> <?php echo Utils::escape($candidature['email']); ?>
                                <?php if (!empty($candidature['numero_tel'])): ?>
                                    • <i class="fas fa-phone"></i> <?php echo Utils::escape($candidature['numero_tel']); ?>
                                <?php endif; ?>
                                <?php if (!empty($candidature['type_handicap']) && $candidature['type_handicap'] !== 'aucun'): ?>
                                    • <i class="fas fa-wheelchair"></i> <?php echo Utils::escape($candidature['type_handicap']); ?>
                                <?php endif; ?>
                            </div>
                        </div>
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
                    
                    <div class="candidature-content">
                        <?php if (!empty($candidature['cv'])): ?>
                            <p><strong>CV:</strong> <a href="<?php echo Utils::escape($candidature['cv']); ?>" target="_blank">Voir le CV</a></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($candidature['linkedin'])): ?>
                            <p><strong>LinkedIn:</strong> <a href="<?php echo Utils::escape($candidature['linkedin']); ?>" target="_blank">Voir le profil</a></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($candidature['lettre_motivation'])): ?>
                            <div class="lettre-motivation">
                                <strong>Lettre de motivation:</strong>
                                <p><?php echo nl2br(Utils::escape($candidature['lettre_motivation'])); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($candidature['notes'])): ?>
                            <div class="lettre-motivation">
                                <strong>Informations supplémentaires:</strong>
                                <p><?php echo nl2br(Utils::escape($candidature['notes'])); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="candidature-footer">
                        <span class="text-muted small">
                            <i class="far fa-clock"></i>
                            Candidature envoyée le <?php echo date('d/m/Y à H:i', strtotime($candidature['date_candidature'])); ?>
                        </span>
                    </div>
                    
                    <div class="candidature-actions">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="candidature_id" value="<?php echo $candidature['Id_candidature']; ?>">
                            <button type="submit" name="action" value="en_revue" class="btn secondary btn-small">
                                <i class="fas fa-eye"></i> En revue
                            </button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="candidature_id" value="<?php echo $candidature['Id_candidature']; ?>">
                            <button type="submit" name="action" value="entretien" class="btn primary btn-small">
                                <i class="fas fa-calendar"></i> Entretien
                            </button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="candidature_id" value="<?php echo $candidature['Id_candidature']; ?>">
                            <button type="submit" name="action" value="accepter" class="btn success btn-small">
                                <i class="fas fa-check"></i> Accepter
                            </button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="candidature_id" value="<?php echo $candidature['Id_candidature']; ?>">
                            <button type="submit" name="action" value="refuser" class="btn error btn-small">
                                <i class="fas fa-times"></i> Refuser
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>