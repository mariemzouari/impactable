<?php
include __DIR__ . '/../../controller/CampagneController.php';
require_once __DIR__ . '/../../model/Campagne.php';
$campagneController = new CampagneController();

if (isset($_GET['id'])) {
    $campagne = $campagneController->showCampagne($_GET['id']);
    if (!$campagne) {
        header('Location: list-camp.php');
        exit;
    }
} else {
    header('Location: list-camp.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactAble — Détails Campagne</title>
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
                    <h2>Détails de la Campagne</h2>
                    <p class="text-muted">Informations complètes sur la campagne</p>
                </div>
                
                <div class="header-actions">
                    <a href="list-camp.php" class="btn secondary">
                        <i class="fas fa-arrow-left"></i>
                        Retour à la liste
                    </a>
                    <a href="update-camp.php?id=<?php echo $campagne['Id_campagne']; ?>" class="btn primary">
                        <i class="fas fa-edit"></i>
                        Modifier
                    </a>
                </div>
            </header>

            <div class="admin-content">
                <section class="content-card">
                    <div class="card-body">
                        <div class="campagne-details">
                            <div class="detail-header">
                                <h1><?php echo htmlspecialchars($campagne['titre']); ?></h1>
                                <div class="detail-badges">
                                    <span class="badge category"><?php echo ucfirst($campagne['categorie_impact']); ?></span>
                                    <span class="badge urgency-<?php echo $campagne['urgence']; ?>"><?php echo ucfirst($campagne['urgence']); ?></span>
                                    <span class="status <?php echo $campagne['statut']; ?>"><?php echo ucfirst($campagne['statut']); ?></span>
                                </div>
                            </div>

                            <div class="detail-content">
                                <div class="detail-section">
                                    <h3><i class="fas fa-info-circle"></i> Description</h3>
                                    <p><?php echo nl2br(htmlspecialchars($campagne['description'])); ?></p>
                                </div>

                                <div class="stats-grid">
                                    <div class="stat-card large">
                                        <div class="stat-number"><?php echo number_format($campagne['objectif_montant'], 0, ',', ' '); ?> TND</div>
                                        <div class="stat-label">Objectif de collecte</div>
                                    </div>
                                    <div class="stat-card large">
                                        <div class="stat-number"><?php echo number_format($campagne['montant_actuel'], 0, ',', ' '); ?> TND</div>
                                        <div class="stat-label">Montant collecté</div>
                                    </div>
                                    <div class="stat-card large">
                                        <div class="stat-number">
                                            <?php 
                                            $progress = $campagne['objectif_montant'] > 0 ? ($campagne['montant_actuel'] / $campagne['objectif_montant']) * 100 : 0;
                                            echo number_format($progress, 1) . '%';
                                            ?>
                                        </div>
                                        <div class="stat-label">Progression</div>
                                    </div>
                                </div>

                                <div class="detail-dates">
                                    <div class="date-item">
                                        <strong>Date de début :</strong>
                                        <span><?php echo date('d/m/Y', strtotime($campagne['date_debut'])); ?></span>
                                    </div>
                                    <div class="date-item">
                                        <strong>Date de fin :</strong>
                                        <span><?php echo date('d/m/Y', strtotime($campagne['date_fin'])); ?></span>
                                    </div>
                                    <div class="date-item">
                                        <strong>Jours restants :</strong>
                                        <span>
                                            <?php
                                            $today = new DateTime();
                                            $endDate = new DateTime($campagne['date_fin']);
                                            if ($endDate > $today) {
                                                $interval = $today->diff($endDate);
                                                echo $interval->days . ' jours';
                                            } else {
                                                echo 'Terminé';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>