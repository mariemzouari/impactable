<?php require_once __DIR__ . '/../templates/header.php'; ?>

<style>
.status-en_attente { background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; display: inline-block; }
.status-en_revue { background: #d1ecf1; color: #0c5460; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; display: inline-block; }
.status-entretien { background: #cce7ff; color: #004085; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; display: inline-block; }
.status-retenu { background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; display: inline-block; }
.status-refuse { background: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; display: inline-block; }


</style>



<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px;">
    <!-- Dernières offres -->
    <div class="content-card">
        <div class="card-header">
            <h3>Dernières offres</h3>
            <a href="index.php?action=admin-gestion-offres" class="btn small primary">Voir tout</a>
        </div>
        <div class="card-body">
            <?php if (!empty($dernieres_offres)): ?>
                <div class="activity-list">
                    <?php foreach ($dernieres_offres as $offre): ?>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div class="activity-content">
                                <h4><?= htmlspecialchars($offre['titre']) ?></h4>
                                <p><?= substr(htmlspecialchars($offre['description']), 0, 80) ?>...</p>
                                <div class="activity-time">
                                    <?= date('d/m/Y', strtotime($offre['date_publication'])) ?> • 
                                    <span class="status <?= $offre['disability_friendly'] ? 'active' : 'inactive' ?>">
                                        <?= $offre['disability_friendly'] ? 'Accessible' : 'Standard' ?>
                                    </span>
                                </div>
                            </div>
                            <a href="index.php?action=admin-voir-offre&id=<?= $offre['Id_offre'] ?>" class="btn small ghost">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: var(--muted); padding: 20px;">Aucune offre récente.</p>
            <?php endif; ?>
        </div>
    </div>
<!-- Dernières candidatures -->
<div class="content-card">
    <div class="card-header">
        <h3>Dernières candidatures</h3>
        <a href="index.php?action=admin-gestion-candidatures" class="btn small primary">Voir tout</a>
    </div>
    <div class="card-body">
        <?php if (!empty($dernieres_candidatures)): ?>
            <div class="activity-list">
                <?php foreach ($dernieres_candidatures as $candidature): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="activity-content">
                            <h4>Candidature #<?= $candidature['Id_candidature'] ?></h4>
                            <p>Utilisateur: <?= $candidature['Id_utilisateur'] ?> | Offre: <?= $candidature['Id_offre'] ?></p>
                            <div class="activity-time">
                                <?= date('d/m/Y à H:i', strtotime($candidature['date_candidature'])) ?> • 
                                <span class="status-<?= $candidature['status'] ?>">
                                    <?php 
                                    $statusLabels = [
                                        'en_attente' => 'En attente',
                                        'en_revue' => 'En revue', 
                                        'entretien' => 'Entretien',
                                        'retenu' => 'Retenu',
                                        'refuse' => 'Refusé'
                                    ];
                                    echo $statusLabels[$candidature['status']] ?? 'Inconnu';
                                    ?>
                                </span>
                            </div>
                        </div>
                        <a href="index.php?action=admin-voir-candidature&id=<?= $candidature['Id_candidature'] ?>" class="btn small ghost">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: var(--muted); padding: 20px;">
                Aucune candidature récente.
            </p>
        <?php endif; ?>
    </div>
</div>
<!-- Actions rapides -->
<div class="content-card">
    <div class="card-header">
        <h3>Actions rapides</h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <a href="index.php?action=admin-gestion-offres" class="btn primary" style="justify-content: center;">
                <i class="fas fa-briefcase"></i>
                Gérer les offres
            </a>
            <a href="index.php?action=admin-gestion-candidatures" class="btn primary" style="justify-content: center;">
                <i class="fas fa-file-alt"></i>
                Gérer les candidatures
            </a>
            <a href="index.php?action=admin-gestion-utilisateurs" class="btn primary" style="justify-content: center;">
                <i class="fas fa-users"></i>
                Gérer les utilisateurs
            </a>
            <a href="index.php?action=offres" class="btn secondary" style="justify-content: center;" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                Voir le site
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>