<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../Controller/UtilisateurController.php';
require_once __DIR__ . '/../../Model/UtilisateurClass.php';

$userC = new UtilisateurController();

// recuperer l'utilisateur
if(isset($_GET['id'])) {
    $user = $userC->showUser($_GET['id']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/style_mariem.css">
</head>
<body>
  <div class="admin-container">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <div class="sidebar-header">
         <div class="admin-logo">
      <img src="assets/images/logo.png" alt="Inclusive Opportunities" class="admin-logo-image">
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
          <a href="Ges_utilisateurs.php" class="sidebar-link active">
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
          <div class="admin-avatar">AD</div>
          <div class="admin-user-info">
            <h4>Admin ImpactAble</h4>
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
        <div class="content-header">
          <h1>Profil Utilisateur</h1>
          <div class="header-actions">
            <a href="Ges_utilisateurs.php" class="btn secondary">
              <i class="fas fa-arrow-left"></i>
              Retour à la liste
            </a>
            <a href="Modifier_utilisateur.php?id=<?php echo $user['Id_utilisateur']; ?>" class="btn primary">
              <i class="fas fa-edit"></i>
              Modifier le profil
            </a>
          </div>
        </div>

        <!-- User Summary -->
        <div class="user-summary-card">
          <div class="user-avatar-large">
            <div class="avatar-placeholder">
              
            </div>
          </div>
          <div class="user-summary-info">
            <h1><?php echo $user['prenom'] . ' ' . $user['nom']; ?></h1>
            <p class="user-email"><?php echo $user['email']; ?></p>
            <div class="user-meta">
              <span class="user-badge active">Actif</span>
              <span class="user-role"><?php echo $user['role']; ?></span>
              <span class="user-join-date">
                <i class="fas fa-calendar-alt"></i>
                Membre depuis <?php echo $user['date_inscription']; ?>
              </span>
            </div>
          </div>
        </div>

        <!-- Profile Details -->
        <div class="form-sections">
          <!-- Informations personnelles -->
          <div class="form-section-card">
            <div class="section-header">
              <h3>
                <i class="fas fa-user-circle"></i>
                Informations personnelles
              </h3>
            </div>
            
            <div class="form-grid">
              <div class="form-group">
                <label>Nom</label>
                <div class="profile-field-value"><?php echo $user['nom']; ?></div>
              </div>
              
              <div class="form-group">
                <label>Prénom</label>
                <div class="profile-field-value"><?php echo $user['prenom']; ?></div>
              </div>
              
              <div class="form-group">
                <label>Email</label>
                <div class="profile-field-value"><?php echo $user['email']; ?></div>
              </div>
              
              <div class="form-group">
                <label>Téléphone</label>
                <div class="profile-field-value"><?php echo $user['numero_tel']; ?></div>
              </div>
              
              <div class="form-group">
                <label>Date de naissance</label>
                <div class="profile-field-value">
                  <?php 
                      echo $user['date_naissance'];
              
                  ?>
                </div>
              </div>
              
              <div class="form-group">
                <label>Genre</label>
                <div class="profile-field-value">
                  <?php 
                    switch($user['genre']) {
                      case 'femme': echo 'Femme'; break;
                      case 'homme': echo 'Homme'; break;
                      case 'prefere_ne_pas_dire': echo 'Préfère ne pas répondre'; break;
                      default: echo $user['genre'];
                    }
                  ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Informations d'accessibilité -->
          <div class="form-section-card">
            <div class="section-header">
              <h3>
                <i class="fas fa-universal-access"></i>
                Informations d'accessibilité
              </h3>
            </div>
            
            <div class="accessibility-section">
              <label class="section-label">Type de handicap</label>
              <div class="checkbox-grid">
                <div class="profile-field-value"><?php echo $user['type_handicap']; ?></div>
              </div>
            </div>
          </div>

          <!-- Informations du compte -->
          <div class="form-section-card">
            <div class="section-header">
              <h3>
                <i class="fas fa-cog"></i>
                Informations du compte
              </h3>
            </div>
            
            <div class="form-grid">
              <div class="form-group">
                <label>Rôle</label>
                <div class="profile-field-value"><?php echo $user['role']; ?></div>
              </div>
              
              <div class="form-group">
                <label>Date d'inscription</label>
                <div class="profile-field-value"><?php echo $user['date_inscription']; ?></div>
              </div>
              
              <div class="form-group">
                <label>ID Utilisateur</label>
                <div class="profile-field-value">#<?php echo $user['Id_utilisateur']; ?></div>
              </div>
            </div>
          </div>

          <!-- Statistiques et activité -->
          <div class="form-section-card">
            <div class="section-header">
              <h3>
                <i class="fas fa-chart-line"></i>
                Statistiques et activité
              </h3>
            </div>
            
            <div class="stats-grid">
              <div class="stat-card">
                <div class="stat-icon">
                  <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-number">0</div>
                <div class="stat-label">Offres postées</div>
              </div>
              
              <div class="stat-card">
                <div class="stat-icon">
                  <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-number">0</div>
                <div class="stat-label">Candidatures</div>
              </div>
              
              <div class="stat-card">
                <div class="stat-icon">
                  <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-number">0</div>
                <div class="stat-label">Événements participés</div>
              </div>
              
              <div class="stat-card">
                <div class="stat-icon">
                  <i class="fas fa-comments"></i>
                </div>
                <div class="stat-number">0</div>
                <div class="stat-label">Messages forum</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="assets/js/script.js"></script>
</body>
</html>