<?php
// Remplacer toutes les inclusions par include_once
include_once __DIR__ . '/../../controller/CampagneController.php';
include_once __DIR__ . '/../../controller/FrontCampagneController.php';
require_once __DIR__ . '/../../model/Campagne.php';

$campagneController = new CampagneController();
$frontController = new FrontCampagneController();
$list = $campagneController->getAllCampagnes();

// Récupérer les campagnes une seule fois
$campagnes = [];
$totalObjectif = 0;
$totalCollecte = 0;

if ($list) {
    $campagnes = $list->fetchAll(PDO::FETCH_ASSOC);
    foreach($campagnes as $c) {
        $totalObjectif += $c['objectif_montant'];
        $totalCollecte += $c['montant_actuel'];
    }
}

// Récupérer les campagnes problématiques
$campagnesProblemes = $frontController->getCampagnesAvecProblemes();
$countProblemes = count($campagnesProblemes);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactAble — Liste des Campagnes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* AJOUTER CES STYLES POUR LES ALERTES */
        .alert-warning {
            border-left: 4px solid #ff9800;
            background: #fff8e1;
        }
        
        .campagne-probleme {
            background: #fff8e1 !important;
            border-left: 3px solid #ff9800;
        }
        
        .badge.warning {
            background: #ff9800;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
        }
        
        .alert-message {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 15px;
        }
        
        .alert-message ul {
            margin: 10px 0 0 0;
            padding-left: 20px;
        }
        
        .alert-message li {
            margin-bottom: 8px;
            padding: 8px;
            background: white;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .alert-count {
            background: #ff9800;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8em;
            margin-left: 8px;
        }
        
        .stat-card.warning {
            background: #fff8e1;
            border-left: 4px solid #ff9800;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <!-- CODE EXISTANT DE LA SIDEBAR -->
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-title">Gestion de contenu</div>
                    <!-- ... autres liens ... -->
                    <a href="list-camp.php" class="sidebar-link active">
                        <i class="fas fa-hand-holding-heart"></i>
                        <span>Campagnes</span>
                        <?php if ($countProblemes > 0): ?>
                            <span class="alert-count"><?php echo $countProblemes; ?></span>
                        <?php endif; ?>
                    </a>
                    <!-- ... -->
                </div>
            </nav>
            <!-- ... -->
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <!-- CODE EXISTANT -->
            </header>

            <div class="admin-content">
                <?php if (isset($_GET['success'])): ?>
                <div class="alert success">
                    <?php 
                    if ($_GET['success'] == 1) echo 'Campagne créée avec succès!';
                    elseif ($_GET['success'] == 2) echo 'Campagne supprimée avec succès!';
                    elseif ($_GET['success'] == 3) echo 'Campagne modifiée avec succès!';
                    ?>
                </div>
                <?php endif; ?>

                <!-- NOUVELLE SECTION : Alertes pour campagnes problématiques -->
                <?php if ($countProblemes > 0): ?>
                <section class="content-card alert-warning">
                    <div class="card-header">
                        <h3><i class="fas fa-exclamation-triangle"></i> Alertes Campagnes</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert-message">
                            <p><strong><?php echo $countProblemes; ?> campagne(s) nécessite(nt) votre attention :</strong></p>
                            <ul>
                                <?php foreach ($campagnesProblemes as $campagne): 
                                    $joursDepuisFin = floor((time() - strtotime($campagne['date_fin'])) / (60 * 60 * 24));
                                    $progression = $campagne['objectif_montant'] > 0 ? ($campagne['montant_actuel'] / $campagne['objectif_montant']) * 100 : 0;
                                ?>
                                <li>
                                    <div>
                                        <strong>"<?php echo htmlspecialchars($campagne['titre']); ?>"</strong> - 
                                        Date de fin dépassée depuis <?php echo $joursDepuisFin; ?> jour(s) - 
                                        Progression : <?php echo number_format($progression, 1); ?>%
                                    </div>
                                    <a href="update-camp.php?id=<?php echo $campagne['Id_campagne']; ?>" class="btn small primary">
                                        <i class="fas fa-edit"></i> Modifier la date
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Section statistiques (MODIFIÉE) -->
                <section class="content-card">
                    <div class="card-body">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-number"><?php echo count($campagnes); ?></div>
                                <div class="stat-label">Total Campagnes</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">
                                    <?php echo number_format($totalObjectif, 0, ',', ' ') . ' TND'; ?>
                                </div>
                                <div class="stat-label">Objectif Total</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">
                                    <?php echo number_format($totalCollecte, 0, ',', ' ') . ' TND'; ?>
                                </div>
                                <div class="stat-label">Montant Collecté</div>
                            </div>
                            <!-- NOUVELLE STATISTIQUE -->
                            <div class="stat-card <?php echo $countProblemes > 0 ? 'warning' : ''; ?>">
                                <div class="stat-number"><?php echo $countProblemes; ?></div>
                                <div class="stat-label">Campagnes à problème</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Tableau des campagnes (MODIFIÉ) -->
                <section class="content-card">
                    <div class="card-header">
                        <h3>Toutes les Campagnes</h3>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Titre</th>
                                        <th>Catégorie</th>
                                        <th>Objectif</th>
                                        <th>Collecté</th>
                                        <th>Progression</th>
                                        <th>Statut</th>
                                        <th>Urgence</th>
                                        <th>Date Fin</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($campagnes)): 
                                        foreach ($campagnes as $campagne): 
                                            $progress = $campagne['objectif_montant'] > 0 ? ($campagne['montant_actuel'] / $campagne['objectif_montant']) * 100 : 0;
                                            $isProbleme = ($campagne['date_fin'] < date('Y-m-d') && $campagne['montant_actuel'] < $campagne['objectif_montant'] && !in_array($campagne['statut'], ['terminée', 'objectif_atteint']));
                                    ?>
                                    <tr class="<?php echo $isProbleme ? 'campagne-probleme' : ''; ?>">
                                        <td><?php echo $campagne['Id_campagne']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($campagne['titre']); ?></strong>
                                            <p class="text-muted small"><?php echo substr($campagne['description'], 0, 50) . '...'; ?></p>
                                            <?php if ($isProbleme): ?>
                                                <span class="badge warning" title="Campagne terminée sans atteindre l'objectif">
                                                    <i class="fas fa-exclamation-triangle"></i> Attention
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge"><?php echo ucfirst($campagne['categorie_impact']); ?></span>
                                        </td>
                                        <td><?php echo number_format($campagne['objectif_montant'], 0, ',', ' '); ?> TND</td>
                                        <td><?php echo number_format($campagne['montant_actuel'], 0, ',', ' '); ?> TND</td>
                                        <td>
                                            <div style="background: #f0f0f0; border-radius: 10px; height: 8px; width: 100px; margin-bottom: 5px;">
                                                <div style="background: var(--sage); height: 100%; border-radius: 10px; width: <?php echo min($progress, 100); ?>%;"></div>
                                            </div>
                                            <small><?php echo number_format($progress, 1); ?>%</small>
                                        </td>
                                        <td>
                                            <span class="status <?php echo $campagne['statut']; ?>">
                                                <?php echo ucfirst($campagne['statut']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge urgency-<?php echo $campagne['urgence']; ?>">
                                                <?php echo ucfirst($campagne['urgence']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y', strtotime($campagne['date_fin'])); ?>
                                            <?php if ($isProbleme): ?>
                                                <br><small class="text-danger">Dépassée</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="showCampagne.php?id=<?php echo $campagne['Id_campagne']; ?>" class="btn small" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="update-camp.php?id=<?php echo $campagne['Id_campagne']; ?>" class="btn small secondary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="deleteCampagne.php?id=<?php echo $campagne['Id_campagne']; ?>" class="btn small danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette campagne?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="10" style="text-align: center; padding: 40px;">
                                            <h3>Aucune campagne trouvée</h3>
                                            <p class="text-muted">Créez votre première campagne pour commencer</p>
                                            <a href="addCampagne.php" class="btn primary">Créer une campagne</a>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>