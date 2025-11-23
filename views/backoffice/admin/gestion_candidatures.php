<?php require_once __DIR__ . '/../templates/header.php'; ?>

<div class="page-header">
    <h1>Gestion des candidatures</h1>
    <div class="header-actions">
        <a href="index.php?action=admin-dashboard" class="btn secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
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

.filters-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filter-group {
    display: flex;
    gap: 15px;
    align-items: end;
    flex-wrap: wrap;
}

.filter-item {
    flex: 1;
    min-width: 200px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.stat-card {
    background: white;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #007bff;
}

.stat-label {
    font-size: 0.9rem;
    color: #666;
}

.table-container {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.data-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.data-table tr:hover {
    background: #f8f9fa;
}

.actions {
    display: flex;
    gap: 5px;
}

.btn.small {
    padding: 6px 10px;
    font-size: 0.8rem;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #666;
}

.empty-state i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 15px;
}
/* STYLES AVANCÉS POUR LES SELECTS */
.form-select-wrapper {
    position: relative;
    width: 100%;
}

.form-select {
    width: 100%;
    padding: 12px 40px 12px 12px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    background-color: white;
    font-size: 0.9rem;
    color: #333;
    cursor: pointer;
    transition: all 0.3s ease;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3E%3Cpath fill='%23666' d='M8 11.5L3.5 7l1.05-1.05L8 9.4l3.45-3.45L12.5 7z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 16px;
}

/* Indicateur de couleur pour le statut */
select[name="status"] {
    border-left: 4px solid;
    padding-left: 8px;
}

select[name="status"]:invalid {
    border-left-color: #6c757d;
}

select[name="status"][value="en_attente"] {
    border-left-color: #ffc107;
}

select[name="status"][value="en_revue"] {
    border-left-color: #17a2b8;
}

select[name="status"][value="entretien"] {
    border-left-color: #007bff;
}

select[name="status"][value="retenu"] {
    border-left-color: #28a745;
}

select[name="status"][value="refuse"] {
    border-left-color: #dc3545;
}

/* Indicateur de couleur pour le type d'offre */
select[name="type_offre"] {
    border-left: 4px solid;
    padding-left: 8px;
}

select[name="type_offre"]:invalid {
    border-left-color: #6c757d;
}

select[name="type_offre"][value="emploi"] {
    border-left-color: #007bff;
}

select[name="type_offre"][value="stage"] {
    border-left-color: #6f42c1;
}

select[name="type_offre"][value="volontariat"] {
    border-left-color: #28a745;
}

select[name="type_offre"][value="formation"] {
    border-left-color: #fd7e14;
}
</style>

<!-- Filtres et Statistiques -->
<div class="filters-card">
    <h3>Filtres et statistiques</h3>
    
    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $stats['total'] ?? 0 ?></div>
            <div class="stat-label">Total</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stats['en_attente'] ?? 0 ?></div>
            <div class="stat-label">En attente</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stats['en_revue'] ?? 0 ?></div>
            <div class="stat-label">En revue</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stats['entretien'] ?? 0 ?></div>
            <div class="stat-label">Entretien</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stats['retenu'] ?? 0 ?></div>
            <div class="stat-label">Retenus</div>
        </div>
    </div>

    <!-- Filtres -->
    <form method="GET" action="">
        <input type="hidden" name="action" value="admin-gestion-candidatures">
        
        <div class="filter-group">
            <div class="filter-item">
                <label for="status" class="form-label">Statut</label>
                <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" <?= (($_GET['status'] ?? '') == 'en_attente') ? 'selected' : '' ?>>En attente</option>
                    <option value="en_revue" <?= (($_GET['status'] ?? '') == 'en_revue') ? 'selected' : '' ?>>En revue</option>
                    <option value="entretien" <?= (($_GET['status'] ?? '') == 'entretien') ? 'selected' : '' ?>>Entretien</option>
                    <option value="retenu" <?= (($_GET['status'] ?? '') == 'retenu') ? 'selected' : '' ?>>Retenu</option>
                    <option value="refuse" <?= (($_GET['status'] ?? '') == 'refuse') ? 'selected' : '' ?>>Refusé</option>
                </select>
            </div>
            
            <div class="filter-item">
                <label for="type_offre" class="form-label">Type d'offre</label>
                <select name="type_offre" id="type_offre" class="form-select" onchange="this.form.submit()">
                    <option value="">Tous les types</option>
                    <option value="emploi" <?= (($_GET['type_offre'] ?? '') == 'emploi') ? 'selected' : '' ?>>Emploi</option>
                    <option value="stage" <?= (($_GET['type_offre'] ?? '') == 'stage') ? 'selected' : '' ?>>Stage</option>
                    <option value="volontariat" <?= (($_GET['type_offre'] ?? '') == 'volontariat') ? 'selected' : '' ?>>Volontariat</option>
                    <option value="formation" <?= (($_GET['type_offre'] ?? '') == 'formation') ? 'selected' : '' ?>>Formation</option>
                </select>
            </div>
            
            <div class="filter-item">
                <button type="submit" class="btn primary">Filtrer</button>
                <a href="index.php?action=admin-gestion-candidatures" class="btn secondary">Réinitialiser</a>
            </div>
        </div>
    </form>
</div>

<!-- Liste des candidatures -->
<div class="table-container">
    <?php if (!empty($candidatures)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Candidat</th>
                    <th>Offre</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($candidatures as $candidature): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($candidature['prenom'] . ' ' . $candidature['nom']) ?></strong>
                            <br>
                            <small style="color: #666;"><?= htmlspecialchars($candidature['email']) ?></small>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($candidature['offre_titre']) ?></strong>
                            <br>
                            <small style="color: #666;">
                                <?php
                                // Utilisation sécurisée de type_offre
                                $typeOffre = $candidature['type_offre'] ?? 'emploi';
                                $typeOffreLabels = [
                                    'emploi' => 'Emploi',
                                    'stage' => 'Stage',
                                    'volontariat' => 'Volontariat',
                                    'formation' => 'Formation',
                                    'autre' => 'Autre'
                                ];
                                echo htmlspecialchars($typeOffreLabels[$typeOffre] ?? $typeOffre);
                                ?>
                            </small>
                        </td>
                        <td>
                            <?php
                            // Utilisation sécurisée de type_offre
                            $typeOffre = $candidature['type_offre'] ?? 'emploi';
                            $typeOffreLabels = [
                                'emploi' => 'Emploi',
                                'stage' => 'Stage',
                                'volontariat' => 'Volontariat',
                                'formation' => 'Formation',
                                'autre' => 'Autre'
                            ];
                            echo htmlspecialchars($typeOffreLabels[$typeOffre] ?? $typeOffre);
                            ?>
                        </td>
                        <td>
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
                        </td>
                        <td>
                            <?= date('d/m/Y', strtotime($candidature['date_candidature'])) ?>
                            <br>
                            <small style="color: #666;"><?= date('H:i', strtotime($candidature['date_candidature'])) ?></small>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="index.php?action=admin-voir-candidature&id=<?= $candidature['Id_candidature'] ?>" 
                                   class="btn small primary" title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="index.php?action=admin-voir-offre&id=<?= $candidature['Id_offre'] ?>" 
                                   class="btn small secondary" title="Voir l'offre">
                                    <i class="fas fa-briefcase"></i>
                                </a>
                                <a href="index.php?action=admin-voir-utilisateur&id=<?= $candidature['Id_utilisateur'] ?>" 
                                   class="btn small secondary" title="Voir le candidat">
                                    <i class="fas fa-user"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-file-alt"></i>
            <h3>Aucune candidature trouvée</h3>
            <p>Aucune candidature ne correspond à vos critères de recherche.</p>
            <a href="index.php?action=admin-gestion-candidatures" class="btn primary">
                Réinitialiser les filtres
            </a>
        </div>
    <?php endif; ?>
</div>


<?php require_once __DIR__ . '/../templates/footer.php'; ?>