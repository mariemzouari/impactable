<?php

require_once __DIR__ . '/../../Controller/UtilisateurController.php';
require_once __DIR__ . '/../../Model/UtilisateurClass.php';

  $erreur = "";
  $user = null;
$userC = new UtilisateurController();


if(isset($_GET['id'])) {
    $user = $userC->showUser($_GET['id']);
    $password = $user['mot_de_passe'];
}


// user info recuperation
if(isset($_POST['id'])) {

   $existingUser = $userC->showUser($_POST['id']);
   $password = $existingUser['mot_de_passe'];

    $user = $userC->showUser($_GET['id']);
}
    if (
        isset($_POST["id"], 
        $_POST["last-name"], 
        $_POST["name"], 
        $_POST["email"], 
        $_POST["phone"], 
        $_POST["birthday"], 
        $_POST["gender"], 
        $_POST["role"])
    ) {
        if (
            !empty($_POST["id"]) && 
            !empty($_POST["last-name"]) && 
            !empty($_POST["name"]) && 
            !empty($_POST["email"]) && 
            !empty($_POST["phone"]) && 
            !empty($_POST["birthday"])
        ) {
            // Gestion des checkboxes
            $type_handicap = 'aucun';
            if (isset($_POST['handicap-type']) && is_array($_POST['handicap-type'])) {
                if (in_array('tous', $_POST['handicap-type'])) {
                    $type_handicap = 'tous';
                } else {
                    $type_handicap = implode(', ', $_POST['handicap-type']);
                }
            }

            $user = new Utilisateur([
                'Id_utilisateur' => $_POST['id'],
                'nom' => $_POST['last-name'],
                'prenom' => $_POST['name'],
                'email' => $_POST['email'],
                'numero_tel' => $_POST['phone'],
                'date_naissance' => $_POST['birthday'],
                'genre' => $_POST['gender'],
                'role' => $_POST['role'],
                'type_handicap' => $type_handicap,
                'mot_de_passe' => $password 
            ]);

            try {
                $userC->updateUser($user, $_POST['id']);
                header('Location: Ges_utilisateurs.php');
                exit;
            } catch (Exception $e) {
                $erreur = "erreur lors de la modification  " . $e->getMessage();
            }

    }
}


if (!$user && !isset($_POST['id'])) {
    header('Location: Ges_utilisateurs.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier Profile</title>
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
          <h1>Modifier Utilisateur</h1>
          <div class="header-actions">
            <a href="Profile_utilisateur.php?id=<?php echo $user['Id_utilisateur']; ?>" class="btn secondary">
              <i class="fas fa-eye"></i>
              Voir le profil
            </a>
          </div>
        </div>

        <!-- User Summary -->
        <div class="user-summary-card">
          <div class="user-avatar-large">
            <div class="avatar-placeholder">
              <?php echo strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1));?>
            </div>
          </div>
          <div class="user-summary-info">
            <h1>
              <?php
                  echo $user['prenom'] . ' ' . $user['nom'];
              ?>
            </h1>
            <p class="user-email">
              <?php 
              echo $user['email']
        
              ?>
            </p>
            <div class="user-meta">
              <span class="user-badge active">Actif</span>
              <span class="user-role">
                <?php 
                echo $user['role']
              
                ?>
              </span>
              <span class="user-join-date">
                <i class="fas fa-calendar-alt"></i>
                Membre depuis 
                <?php 
                    echo $user['date_inscription'];
                ?>
              </span>
            </div>
          </div>
        </div>

        <!-- Edit Form -->
        <form class="user-edit-form" id="usereditForm" method="post" action="">
          <!-- ID (Hidden) -->
          <input type="hidden" name="id" value="<?php echo $user['Id_utilisateur'] ?? ''; ?>">



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
                  <label for="edit-nom">Nom</label>
                  <input type="text" id="edit-nom" name="last-name" class="input" value="<?php echo $user['nom'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                  <label for="edit-prenom">Prénom</label>
                  <input type="text" id="edit-prenom" name="name" class="input" value="<?php echo $user['prenom'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                  <label for="edit-email">Email</label>
                  <input type="text" id="edit-email" name="email" class="input" value="<?php echo $user['email'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                  <label for="edit-telephone">Téléphone</label>
                  <input type="text" id="edit-telephone" name="phone" class="input" value="<?php echo $user['numero_tel'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                  <label for="edit-date-naissance">Date de naissance</label>
                  <input type="date" id="edit-date-naissance" name="birthday" class="input" value="<?php echo $user['date_naissance'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                  <label for="edit-genre">Genre</label>
                  <select id="edit-genre" name="gender" class="select">
                    <option value="femme" <?php echo (isset($user['genre']) && $user['genre']=='femme')?'selected':''; ?>>Femme</option>
                    <option value="homme" <?php echo (isset($user['genre']) && $user['genre']=='homme')?'selected':''; ?>>Homme</option>
                    <option value="prefere_ne_pas_dire" <?php echo (isset($user['genre']) && $user['genre']=='prefere_ne_pas_dire')?'selected':''; ?>>Préfère ne pas répondre</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Informations professionnelles -->
            <div class="form-section-card">
              <div class="section-header">
                <h3>
                  <i class="fas fa-briefcase"></i>
                  Informations professionnelles
                </h3>
              </div>
              
              <div class="form-grid">
                <div class="form-group">
                  <label for="edit-profession">Profession</label>
                  <input type="text" id="edit-profession" class="input" value="Développeuse Web">
                </div>
                
                <div class="form-group">
                  <label for="edit-competences">Compétences</label>
                  <input type="text" id="edit-competences" class="input" value="HTML, CSS, JavaScript, React">
                </div>
                
                <div class="form-group">
                  <label for="edit-ville">Ville</label>
                  <input type="text" id="edit-ville" class="input" value="Tunis">
                </div>
                
                <div class="form-group">
                  <label for="edit-pays">Pays</label>
                  <input type="text" id="edit-pays" class="input" value="Tunisie">
                </div>
              </div>

              <div class="form-group" style="margin: 0 30px;">
                <label for="edit-linkedin">LinkedIn</label>
                <input type="text" id="edit-linkedin" class="input" placeholder="Lien vers votre profil LinkedIn">
              </div>
                
              <div class="form-group" style="margin: 0 30px;">
                <label for="edit-bio">Bio</label>
                <textarea id="edit-bio" class="textarea" rows="4" placeholder="Description de l'utilisateur...">Développeuse passionnée par l'accessibilité web et l'inclusion numérique.</textarea>
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
              <!-- fix for the handicap type issue-->
              <div class="accessibility-section">
                <label class="section-label">Type de handicap</label>
                <div class="checkbox-grid">
                  <?php
                  $handicapTypes = ['aucun', 'moteur', 'visuel', 'auditif', 'cognitif', 'autre', 'tous'];
                  $currentHandicap = isset($user['type_handicap']) ? explode(', ', $user['type_handicap']) : ['aucun'];
                  
                  foreach($handicapTypes as $type):
                  ?>
                  <label class="checkbox-option">
                    <input type="checkbox" name="handicap-type[]" value="<?php echo $type; ?>" 
                      <?php echo in_array($type, $currentHandicap) ? 'checked' : ''; ?>>
                    <span class="checkmark"></span>
                    <?php echo ucfirst($type); ?>
                  </label>
                  <?php endforeach; ?>
                </div>
              </div>




            </div>

            <!-- Paramètres du compte -->
            <div class="form-section-card">
              <div class="section-header">
                <h3>
                  <i class="fas fa-cog"></i>
                  Paramètres du compte
                </h3>
              </div>
              
              <div class="form-grid">
                <div class="form-group">
                  <label for="edit-statut">Statut du compte</label>
                  <select id="edit-statut" class="select">
                    <option value="actif" selected>Actif</option>
                    <option value="inactif">Inactif</option>
                    <option value="suspendu">Suspendu</option>
                  </select>
                </div>
                
                <div class="form-group">
                  <label for="edit-role">Rôle</label>
                  <select id="edit-role" name="role" class="select">
                    <option value="user" <?php echo (isset($user['role']) && $user['role']=='user')?'selected':''; ?>>Utilisateur</option>
                    <option value="admin" <?php echo (isset($user['role']) && $user['role']=='admin')?'selected':''; ?>>Administrateur</option>
                  </select>
                </div>
              </div>
              
              <div class="password-reset-section">
                <div class="password-info">
                  <i class="fas fa-info-circle"></i>
                  Réinitialiser le mot de passe de l'utilisateur
                </div>
                <button type="button" class="btn secondary" style="margin-top: 12px;">
                  <i class="fas fa-key"></i>
                  Envoyer un lien de réinitialisation
                </button>
              </div>
            </div>

            <span id="useredit-control" class="controle-saisie"></span>
          </div>

          <!-- Form Actions -->
          <div class="form-actions">
            <a href="Ges_utilisateurs.php" class="btn secondary">
              <i class="fas fa-times"></i>
              Annuler
            </a>
            <button type="submit" class="btn primary">
              <i class="fas fa-save"></i>
              Enregistrer les modifications
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>

  <script src="assets/js/script.js"></script>
  <script src="assets/js/controle_saisie_user.js"></script>
</body>
</html>