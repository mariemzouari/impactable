<?php require_once __DIR__ . '/../templates/header.php'; ?>

<div class="page-header">
    <h1>Détails de la candidature</h1>
    <a href="index.php?action=admin-dashboard" class="btn secondary">
        <i class="fas fa-arrow-left"></i> Retour aux candidatures
    </a>
</div>

<style>
.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    display: inline-block;
}
.status-en_attente { background: #fff3cd; color: #856404; }
.status-en_revue { background: #d1ecf1; color: #0c5460; }
.status-entretien { background: #cce7ff; color: #004085; }
.status-retenu { background: #d4edda; color: #155724; }
.status-refuse { background: #f8d7da; color: #721c24; }

.info-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.info-item {
    margin-bottom: 15px;
}

.info-label {
    font-weight: 600;
    color: #666;
    margin-bottom: 5px;
    font-size: 0.9rem;
}

.info-value {
    font-size: 1rem;
    color: #333;
}

.actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}
</style>

<div class="info-grid">
    <!-- Informations candidat -->
    <div class="info-card">
        <h3><i class="fas fa-user"></i> Informations du candidat</h3>
        <div class="info-item">
            <div class="info-label">Nom complet</div>
            <div class="info-value"><?= htmlspecialchars($candidat['prenom'] . ' ' . $candidat['nom']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Email</div>
            <div class="info-value"><?= htmlspecialchars($candidat['email']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Téléphone</div>
            <div class="info-value"><?= htmlspecialchars($candidat['numero_tel'] ?? 'Non renseigné') ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Type de handicap</div>
            <div class="info-value">
                <?php
                $handicapLabels = [
                    'aucun' => 'Aucun',
                    'moteur' => 'Moteur', 
                    'visuel' => 'Visuel',
                    'auditif' => 'Auditif',
                    'mental' => 'Mental',
                    'autre' => 'Autre',
                    'tous' => 'Tous'
                ];
                $types = explode(',', $candidat['type_handicap']);
                $labels = array_map(function($type) use ($handicapLabels) {
                    return $handicapLabels[$type] ?? $type;
                }, $types);
                echo htmlspecialchars(implode(', ', $labels));
                ?>
            </div>
        </div>
    </div>
<!-- Informations offre -->
<div class="info-card">
    <h3><i class="fas fa-briefcase"></i> Informations de l'offre</h3>
    <div class="info-item">
        <div class="info-label">Titre de l'offre</div>
        <div class="info-value"><?= htmlspecialchars($offre['titre']) ?></div>
    </div>
    <div class="info-item">
        <div class="info-label">Type d'offre</div>
        <div class="info-value">
            <?php
            $typeOffreLabels = [
                'emploi' => 'Emploi',
                'stage' => 'Stage',
                'volontariat' => 'Volontariat', 
                'formation' => 'Formation',
                'autre' => 'Autre'
            ];
            echo htmlspecialchars($typeOffreLabels[$offre['type_offre']] ?? $offre['type_offre']);
            ?>
        </div>
    </div>
    <div class="info-item">
        <div class="info-label">Mode de travail</div>
        <div class="info-value">
            <?php
            $modeLabels = [
                'presentiel' => 'Présentiel',
                'en_ligne' => 'En ligne',
                'hybride' => 'Hybride'
            ];
            echo htmlspecialchars($modeLabels[$offre['mode']] ?? $offre['mode']);
            ?>
        </div>
    </div>
    <div class="info-item">
        <div class="info-label">Lieu</div>
        <div class="info-value"><?= htmlspecialchars($offre['lieu'] ?? 'Non spécifié') ?></div>
    </div>
    <div class="info-item">
        <div class="info-label">Recruteur</div>
        <div class="info-value">
            <?php if ($recruteur): ?>
                <?= htmlspecialchars($recruteur['prenom'] . ' ' . $recruteur['nom']) ?>
            <?php else: ?>
                <span style="color: #666;">Non spécifié</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Détails candidature -->
<div class="info-card">
    <h3><i class="fas fa-file-alt"></i> Détails de la candidature</h3>
    
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Statut</div>
            <div class="info-value">
                <span class="status-badge status-<?= $candidature['status'] ?>">
                    <?php
                    $statusLabels = [
                        'en_attente' => 'En attente',
                        'en_revue' => 'En revue',
                        'entretien' => 'Entretien',
                        'retenu' => 'Retenu',
                        'refuse' => 'Refusé'
                    ];
                    echo htmlspecialchars($statusLabels[$candidature['status']] ?? $candidature['status']);
                    ?>
                </span>
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-label">Date de candidature</div>
            <div class="info-value"><?= date('d/m/Y à H:i', strtotime($candidature['date_candidature'])) ?></div>
        </div>
    </div>

    <!-- CV et LinkedIn -->
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">CV</div>
            <div class="info-value">
                <?php if (!empty($candidature['cv'])): ?>
                    <a href="<?= htmlspecialchars($candidature['cv']) ?>" target="_blank" class="btn small primary">
                        <i class="fas fa-external-link-alt"></i> Voir le CV
                    </a>
                <?php else: ?>
                    <span style="color: #666;">Non fourni</span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-label">LinkedIn</div>
            <div class="info-value">
                <?php if (!empty($candidature['linkedin'])): ?>
                    <a href="<?= htmlspecialchars($candidature['linkedin']) ?>" target="_blank" class="btn small primary">
                        <i class="fab fa-linkedin"></i> Voir le profil
                    </a>
                <?php else: ?>
                    <span style="color: #666;">Non fourni</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Lettre de motivation -->
    <div class="info-item">
        <div class="info-label">Lettre de motivation</div>
        <div class="info-value">
            <?php if (!empty($candidature['lettre_motivation'])): ?>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff;">
                    <?= nl2br(htmlspecialchars($candidature['lettre_motivation'])) ?>
                </div>
            <?php else: ?>
                <span style="color: #666;">Aucune lettre de motivation fournie</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Notes -->
    <?php if (!empty($candidature['notes'])): ?>
    <div class="info-item">
        <div class="info-label">Notes</div>
        <div class="info-value">
            <div style="background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;">
                <?= nl2br(htmlspecialchars($candidature['notes'])) ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Actions -->
<div class="info-card">
    <h3><i class="fas fa-cog"></i> Actions</h3>
    <div class="actions">
        <a href="index.php?action=admin-gestion-candidatures" class="btn secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
        <a href="index.php?action=admin-voir-offre&id=<?= $offre['Id_offre'] ?>" class="btn primary">
            <i class="fas fa-eye"></i> Voir l'offre
        </a>

    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>