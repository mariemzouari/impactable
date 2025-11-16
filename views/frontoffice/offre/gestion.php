<?php 
$title = "Gestion Candidatures - " . Utils::escape($offre['titre']) . " | " . Config::SITE_NAME;
require_once __DIR__ . '/../templates/header.php'; 
?>

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
        
        <div class="stats-candidatures">
            <div class="stat-candidature">
                <div class="stat-number"><?php echo count($candidatures); ?></div>
                <div class="stat-label">Total</div>
            </div>
            <div class="stat-candidature">
                <div class="stat-number"><?php echo $stats['en_attente']; ?></div>
                <div class="stat-label">En attente</div>
            </div>
            <div class="stat-candidature">
                <div class="stat-number"><?php echo $stats['en_revue']; ?></div>
                <div class="stat-label">En revue</div>
            </div>
            <div class="stat-candidature">
                <div class="stat-number"><?php echo $stats['entretien']; ?></div>
                <div class="stat-label">Entretien</div>
            </div>
            <div class="stat-candidature">
                <div class="stat-number"><?php echo $stats['retenu']; ?></div>
                <div class="stat-label">Retenus</div>
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