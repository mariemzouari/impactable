
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactAble — Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="admin-logo">
                    <img src="assets/images/logo.png" alt="ImpactAble" class="admin-logo-image">
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-title">Principal</div>
                    <a href="index.php" class="sidebar-link active">
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
                
                <div class="nav-section">
                    <div class="nav-title">Gestion de contenu</div>
                    <a href="list-camp.php" class="sidebar-link">
                        <i class="fas fa-hand-holding-heart"></i>
                        <span>Campagnes</span>
                    </a>
                </div>
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

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div>
                    <h2>Tableau de Bord</h2>
                    <p class="text-muted">Vue d'ensemble de votre plateforme</p>
                </div>
                <div class="header-actions">
                    <a href="list-camp.php" class="btn primary">
                        <i class="fas fa-hand-holding-heart"></i>
                        Gérer les Campagnes
                    </a>
                </div>
            </header>

            <div class="admin-content">
                <!-- Cartes de statistiques -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <div class="stat-number">12</div>
                        <div class="stat-label">Campagnes Actives</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number">156</div>
                        <div class="stat-label">Utilisateurs</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-donate"></i>
                        </div>
                        <div class="stat-number">45,230 TND</div>
                        <div class="stat-label">Dons Collectés</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <div class="stat-number">78%</div>
                        <div class="stat-label">Objectifs Atteints</div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>Actions Rapides</h3>
                    </div>
                    <div class="card-body">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                            <a href="addCampagne.php" class="btn primary" style="flex-direction: column; padding: 20px; text-align: center;">
                                <i class="fas fa-plus-circle" style="font-size: 2rem; margin-bottom: 10px;"></i>
                                <span>Nouvelle Campagne</span>
                            </a>
                            <a href="list-camp.php" class="btn secondary" style="flex-direction: column; padding: 20px; text-align: center;">
                                <i class="fas fa-list" style="font-size: 2rem; margin-bottom: 10px;"></i>
                                <span>Voir Toutes les Campagnes</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>