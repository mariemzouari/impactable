<?php
require_once __DIR__ . '/../../Controller/UtilisateurController.php';
require_once __DIR__ . '/../../Controller/ProfileController.php';

if (session_status() === PHP_SESSION_NONE)
  session_start();
// verifier si utilisateur connecté si non send to login
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}



if (isset($_GET['search']) && !empty($_GET['search'])) {
  $search = $_GET['search'];
} else {
  $search = "";
}


$userC = new UtilisateurController();
$profileC = new ProfileController();





//recuperer users and profiles
$users = $userC->listUsers($search);

// recuperer l'utilisateur de la session et son profil pour la photo
$user_id = $_SESSION['user_id'];
$user = $userC->showUser($user_id);
$profile = $profileC->showProfile($user_id);




// gestion  suppression
if (isset($_GET['delete_id'])) {
  $userC->deleteUser($_GET['delete_id']);
  header('Location: Ges_utilisateurs.php');
  exit;
}





// gestion du block
if (isset($_GET['block_id'])) {
  $userC->blockUser($_GET['block_id']);
  header('Location: Ges_utilisateurs.php');
  exit;
}

// gestion du unblock
if (isset($_GET['unblock_id'])) {
  $userC->unblockUser($_GET['unblock_id']);
  header('Location: Ges_utilisateurs.php');
  exit;
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gérer Utilisateurs</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
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
          <a href="index.php?action=admin-dashboard"
            class="sidebar-link <?= (isset($_GET['action']) && $_GET['action'] == 'admin-dashboard') ? 'active' : '' ?>">
            <i class="fas fa-briefcase"></i>
            <span>Opportunités</span>
          </a>
          <a href="evenment_back.php" class="sidebar-link">
            <i class="fas fa-calendar-alt"></i>
            <span>Événements</span>
          </a>

          <div class="sidebar-dropdown">
            <a href="#" class="sidebar-link dropdown-toggle" aria-expanded="false">
              <i class="fas fa-hand-holding-heart"></i>
              <span>Campagnes</span>
              <i class="fas fa-chevron-down dropdown-arrow"></i>
            </a>
            <div class="sidebar-submenu">
              <a href="list-camp.php" class="submenu-link">
                <i class="fas fa-list"></i>
                <span>Toutes les campagnes</span>
              </a>
              <a href="addCampagne.php" class="submenu-link">
                <i class="fas fa-plus"></i>
                <span>Nouvelle campagne</span>
              </a>
              <a href="Calendar.php" class="submenu-link">
                <i class="fas fa-calendar-alt"></i>
                <span>Calendrier</span>
              </a>
              <a href="stats_dashboard.php" class="submenu-link">
                <i class="fas fa-chart-bar"></i>
                <span>Statistiques</span>
              </a>
              <a href="referral.php" class="submenu-link">
                <i class="fas fa-user-friends"></i>
                <span>Parrainage</span>
              </a>
            </div>
          </div>

          <a href="list-don.php" class="sidebar-link">
            <i class="fas fa-donate"></i>
            <span>Dons</span>
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
            <h4><?php echo htmlspecialchars($user['nom']) . ' ' . htmlspecialchars($user['prenom']) ?></h4>
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
            <a href="logout.php">Déconnexion</a>
          </button>
        </div>
      </header>

      <div class="admin-content">
        <div class="content-header">
          <h1>Gestion des Utilisateurs</h1>
          <div class="header-actions">
            <a href="Ajouter_utilisateur.php" class="btn primary">
              <i class="fas fa-user-plus"></i>
              <span>Nouvel Utilisateur</span>
            </a>
          </div>
        </div>


        <div class="content-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
          <!-- Liste des utilisateurs -->
          <div class="content-card">
            <div class="card-header">
              <h3>Liste des Utilisateurs</h3>
              <form method="get" action="">
                <div class="search-bar">
                  <i class="fas fa-search"></i>
                  <input type="text" name="search" placeholder="Rechercher un utilisateur...">

                </div>
                <button type="submit" hidden> search </button>
              </form>
            </div>

            <div class="card-body">
              <div class="activity-list">
                <?php
                foreach ($users as $user) {
                  ?>
                  <div class="activity-item">
                    <div class="activity-icon">
                      <div class="user-avatar-small">

                        <!-- photo ici a faire-->
                        <img src="../../uploads/<?php echo $user['photo_profil'] ?> " alt="poto"
                          style="border-radius: 50%;s">
                      </div>
                    </div>

                    <div class="activity-content">
                      <h4><?php echo $user['prenom'] . ' ' . $user['nom']; ?></h4>
                      <p><?php echo $user['role'] . ' • ' . $user['type_handicap'] . ' • ' . $user['email']; ?></p>

                    </div>
                    <div class="activity-time">
                      <div class="table-actions">
                        <a href="Modifier_profile.php?id=<?php echo $user['Id_utilisateur']; ?>" class="btn ghost small">
                          <i class="fas fa-edit"></i>
                        </a>
                        <a href="Profile.php?id=<?php echo $user['Id_utilisateur']; ?>" class="btn ghost small">
                          <i class="fas fa-eye"></i>
                        </a>
                        <a href="Ges_utilisateurs.php?delete_id=<?php echo $user['Id_utilisateur']; ?>"
                          class="btn ghost small">
                          <i class="fas fa-trash"></i>
                        </a>



                        <?php if ($user['blocked'] == 0) { ?>
                          <a href="Ges_utilisateurs.php?block_id=<?php echo $user['Id_utilisateur']; ?>"
                            class="btn ghost small" title="Bloquer">
                            <i class="fas fa-ban"></i>
                          </a>
                        <?php } else { ?>
                          <a href="Ges_utilisateurs.php?unblock_id=<?php echo $user['Id_utilisateur']; ?>"
                            class="btn ghost small" title="Débloquer">
                            <i class="fas fa-unlock"></i>
                          </a>
                        <?php } ?>

                      </div>
                    </div>
                  </div>
                  <?php
                }
                ?>
              </div>
            </div>
          </div>

          <!-- Actions rapides -->
          <div class="content-card">
            <div class="card-header">
              <h3>Actions Rapides</h3>
            </div>
            <div class="card-body">
              <div style="display: flex ; flex-direction: column; gap: 12px;">
                <a href="Ajouter_utilisateur.php" class="btn primary">
                  <i class="fas fa-user-plus"></i>
                  <span>Nouvel Utilisateur</span>
                </a>
                <button class="btn secondary">
                  <i class="fas fa-download"></i>
                  <span>Exporter la liste</span>
                </button>
                <button class="btn secondary">
                  <i class="fas fa-sync-alt"></i>
                  <span>Actualiser</span>
                </button>
              </div>

              <!-- Filtres rapides --> <!-- Actions rapides
              <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid rgba(75,46,22,0.08);">
                <h4 style="margin-bottom: 16px; color: var(--brown);">Filtres rapides</h4>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                  <button class="btn ghost small" style="justify-content: flex-start;">
                    <i class="fas fa-user-check"></i>
                    <span>Utilisateurs actifs</span>
                  </button>
                  <button class="btn ghost small" style="justify-content: flex-start;">
                    <i class="fas fa-user-clock"></i>
                    <span>En attente</span>
                  </button>
                  <button class="btn ghost small" style="justify-content: flex-start;">
                    <i class="fas fa-user-slash"></i>
                    <span>Comptes suspendus</span>
                  </button>
                  <button class="btn ghost small" style="justify-content: flex-start;">
                    <i class="fas fa-user-tag"></i>
                    <span>Nouveaux cette semaine</span>
                  </button>  
                </div>
              </div>-->
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="assets/js/script.js"></script>
</body>

</html>