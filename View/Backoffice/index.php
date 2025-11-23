
<?php 
require_once __DIR__ . '/../../Controller/UtilisateurController.php';
require_once __DIR__ . '/../../Controller/ProfileController.php';

session_start();


// verifier si utilisateur connecté si non send to login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../Frontoffice/login.php');
    exit;
}


// controllers
$userC = new UtilisateurController();
$profileC = new ProfileController();

// recuperer l'utilisateur de la session et son profil pour la photo
$user_id = $_SESSION['user_id'];
$user = $userC->showUser($user_id);
$profile = $profileC->showProfile($user_id);

//si il n'ya pas de user
if (!$user) {
    echo "UTILISATEUR NON TROUVE EN BASE";
    exit;
}


?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administration</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets\css\style.css">
</head>
<body>
  <div class="admin-container">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <div class="sidebar-header">
         <div class="admin-logo">
      <img src="assets\images\logo.png" alt="Inclusive Opportunities" class="admin-logo-image">
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
          <a href="Ges_utilisateurs.php" class="sidebar-link">
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
          <a href="#campaigns" class="sidebar-link">
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
          <img src="../../uploads/<?php echo $profile['photo_profil'] ?>" class="admin-avatar"></img>
          <div class="admin-user-info">
            <h4><?php echo htmlspecialchars($user['nom']) . ' '. htmlspecialchars($user['prenom'])  ?></h4>
            <p>Administrateur</p>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main content -->
    <main class="admin-main">
      <header class="admin-header">
        <div>
          <h2>Tableau de bord administrateur</h2>
          <p class="text-muted">Bienvenue dans l'interface d'administration d'ImpactAble</p>
        </div>
        
        <div class="header-actions">
          <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Rechercher...">
          </div>
          <button class="btn secondary">
            <i class="fas fa-sign-out-alt"></i>
            <a href="logout.php" >Déconnexion</a>
          </button>
        </div>
      </header>
      
      <div class="admin-content">
        <!-- Dashboard Content -->
        <div id="dashboard-content" class="tab-content active">
          <div class="content-header">
            <h1>Tableau de bord</h1>
    
          </div>
          
          <!-- Stats Grid -->
          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fas fa-users"></i>
              </div>
              <div class="stat-number">4,827</div>
              <div class="stat-label">Utilisateurs inscrits</div>
            </div>
            
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fas fa-briefcase"></i>
              </div>
              <div class="stat-number">1,253</div>
              <div class="stat-label">Opportunités actives</div>
            </div>
            
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fas fa-calendar-alt"></i>
              </div>
              <div class="stat-number">89</div>
              <div class="stat-label">Événements à venir</div>
            </div>
            
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fas fa-hand-holding-heart"></i>
              </div>
              <div class="stat-number">47</div>
              <div class="stat-label">Campagnes en cours</div>
            </div>
          </div>
          
          <div class="content-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
            <!-- Recent Activity -->
            <div class="content-card">
              <div class="card-header">
                <h3>Activité récente</h3>
                <a href="#" class="btn ghost small">Voir tout</a>
              </div>
              <div class="card-body">
                <div class="activity-list">
                  <div class="activity-item">
                    <div class="activity-icon">
                      <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="activity-content">
                      <h4>Nouvel utilisateur inscrit</h4>
                      <p>Sarah Ben a créé un compte sur la plateforme</p>
                    </div>
                    <div class="activity-time">Il y a 10 min</div>
                  </div>
                  
                  <div class="activity-item">
                    <div class="activity-icon">
                      <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="activity-content">
                      <h4>Nouvelle opportunité publiée</h4>
                      <p>Une offre de "Développeur Accessibilité Web" a été postée</p>
                    </div>
                    <div class="activity-time">Il y a 25 min</div>
                  </div>
                  
                  <div class="activity-item">
                    <div class="activity-icon">
                      <i class="fas fa-comment"></i>
                    </div>
                    <div class="activity-content">
                      <h4>Nouveau message sur le forum</h4>
                      <p>Ahmed Dridi a répondu à un sujet sur les événements hybrides</p>
                    </div>
                    <div class="activity-time">Il y a 1 heure</div>
                  </div>
                  
                  <div class="activity-item">
                    <div class="activity-icon">
                      <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div class="activity-content">
                      <h4>Nouveau don reçu</h4>
                      <p>Un don de 200 TND pour la campagne "Matériel adapté pour écoles"</p>
                    </div>
                    <div class="activity-time">Il y a 2 heures</div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="content-card">
              <div class="card-header">
                <h3>Actions rapides</h3>
              </div>
              <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 12px;">
                  <button class="btn primary">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nouvelle opportunité</span>
                  </button>
                  <button class="btn secondary">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Créer un événement</span>
                  </button>
                  <button class="btn secondary">
                    <i class="fas fa-campground"></i>
                    <span>Lancer une campagne</span>
                  </button>
                  <button class="btn secondary">
                    <i class="fas fa-file-alt"></i>
                    <span>Ajouter une ressource</span>
                  </button>
                  <button class="btn secondary">
                    <i class="fas fa-user-cog"></i>
                    <span>Gérer les utilisateurs</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        
        
  </div>

</main>
<script src="assets\js\script.js"> </script>

</body>
</php>