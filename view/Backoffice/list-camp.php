<?php
include __DIR__ . '/../../controller/CampagneController.php';
require_once __DIR__ . '/../../model/Campagne.php';
$campagneController = new CampagneController();
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactAble — Liste des Campagnes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="admin-logo">
                    <img src="assets/images/logo.png" alt="ImpactAble" class="admin-logo-image">
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-title">Principal</div>
                    <a href="index.php" class="sidebar-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tableau de bord</span>
                    </a>
                    <a href="#analytics" class="sidebar-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Analytiques</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-title">Gestion de contenu</div>
                    <a href="#users" class="sidebar-link">
                        <i class="fas fa-users"></i>
                        <span>Utilisateurs</span>
                    </a>
                    <a href="#opportunities" class="sidebar-link">
                        <i class="fas fa-briefcase"></i>
                        <span>Opportunités</span>
                    </a>
                    <a href="#events" class="sidebar-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Événements</span>
                    </a>
                    <a href="list-camp.php" class="sidebar-link active">
                        <i class="fas fa-hand-holding-heart"></i>
                        <span>Campagnes</span>
                    </a>
                    <a href="#resources" class="sidebar-link">
                        <i class="fas fa-book"></i>
                        <span>Ressources</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-title">Communauté</div>
                    <a href="#forum" class="sidebar-link">
                        <i class="fas fa-comments"></i>
                        <span>Forum</span>
                    </a>
                    <a href="#reclamations" class="sidebar-link">
                        <i class="fas fa-comment-alt"></i>
                        <span>Réclamations</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-title">Paramètres</div>
                    <a href="#settings" class="sidebar-link">
                        <i class="fas fa-cog"></i>
                        <span>Configuration</span>
                    </a>
                </div>
            </nav>
            
            <div class="sidebar-footer">
                <div class="admin-user">
                    <div class="admin-avatar">AD</div>
                    <div class="admin-user-info">
                        <h4>Admin User</h4>
                        <p>Administrateur</p>
                    </div>
                </div>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <div>
                    <h2>Liste des Campagnes</h2>
                    <p class="text-muted">Gestion des campagnes de collecte</p>
                </div>
                
                <div class="header-actions">
                    <a href="addCampagne.php" class="btn primary">
                        <i class="fas fa-plus-circle"></i>
                        Nouvelle Campagne
                    </a>
                    <a href="index.php" class="btn secondary">
                        <i class="fas fa-arrow-left"></i>
                        Retour
                    </a>
                </div>
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

                <!-- Statistiques -->
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
                        </div>
                    </div>
                </section>

                <!-- Liste des campagnes -->
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
                                    ?>
                                    <tr>
                                        <td><?php echo $campagne['Id_campagne']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($campagne['titre']); ?></strong>
                                            <p class="text-muted small"><?php echo substr($campagne['description'], 0, 50) . '...'; ?></p>
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
                                        <td><?php echo date('d/m/Y', strtotime($campagne['date_fin'])); ?></td>
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