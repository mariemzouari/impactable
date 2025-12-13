
<?php 
require_once __DIR__ . '/../../Controller/UtilisateurController.php';
require_once __DIR__ . '/../../Controller/ProfileController.php';
require_once __DIR__ . '/../../Model/UtilisateurClass.php';
require_once __DIR__ . '/../../Model/ProfileClass.php';


$erreur = "";
$userC = new UtilisateurController();

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
 if ( isset($_POST['last']) &&
      isset($_POST['name']) &&
      isset($_POST['gender']) &&
      isset($_POST['birthday']) &&
      isset($_POST['email']) &&
      isset($_POST['phone']) &&
      isset($_POST['role']) &&
      isset($_POST['password']) &&
      isset($_POST['confirm']) 
    ){

        if( !empty($_POST['last']) &&
            !empty($_POST['name']) &&
            !empty($_POST['email']) &&
            !empty($_POST['phone']) &&
            !empty($_POST['role']) &&
            !empty($_POST['birthday']) &&
            !empty($_POST['password']) &&
            !empty($_POST['confirm'])  
            ){
            
             //checkboxes
             if (empty($_POST['handicap'])) $handicap = 'aucun';
             else $handicap = implode(',', $_POST['handicap']);
             


           
            $user = new Utilisateur([
                'nom' => $_POST['last'],
                'prenom' => $_POST['name'],
                'email' => $_POST['email'],
                'numero_tel' => $_POST['phone'] ,
                'date_naissance' => $_POST['birthday'] ,
                'mot_de_passe' => $_POST['password'],
                'genre' => $_POST['gender'],
                'role' => $_POST['role'] ,
                'type_handicap' => $handicap

            ]);
            
    
      
          
          try{
                $user_id = $userC->addUser($user);

                $profileC = new ProfileController();
                $empty_profile = new Profil([
                'Id_utilisateur' => $user_id,
                'photo_profil' => 'default.jpg',
                'bio' => '',
                'ville' => '',
                'pays' => '', 
                'profession' => '',
                'competences' => '',
                'linkedin' => '',
                'date_creation' => date("Y-m-d H:i:s"),
                'date_modification' => date("Y-m-d H:i:s")
                ]);

                $profileC->addProfile($empty_profile);
                header('Location: Ges_utilisateurs.php'); 
                exit;
          }
          catch(Exception $e) {
                 $erreur = "Erreur lors de l'ajout : " . $e->getMessage();
          }


        }
        else{
          $erreur = "Veuillez remplir tous les champs obligatoires";   
        }


    }


}


?>






<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ajouter Utilisateur</title>
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
          <a href="index.php?action=admin-dashboard" class="sidebar-link <?= (isset($_GET['action']) && $_GET['action'] == 'admin-dashboard') ? 'active' : '' ?>">
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
            <span>Déconnexion</span>
          </button>
        </div>
      </header>

      <div class="admin-content">
        <div class="content-header">
          <h1>Ajouter un Utilisateur</h1>
          <div class="header-actions">
            <a href="Ges_utilisateurs.php" class="btn secondary">
              <i class="fas fa-arrow-left"></i>
              Retour à la liste
            </a>
          </div>
        </div>

        <!-- Nouveau résumé utilisateur -->
        <div class="user-summary-card">
          <div class="user-avatar-large">
            <div class="avatar-placeholder">
              <i class="fas fa-user-plus"></i>
            </div>
          </div>
          <div class="user-summary-info">
            <h1>Nouvel Utilisateur</h1>
            <p class="user-email">Compte en cours de création</p>
            <div class="user-meta">
              <span class="user-badge inactive">Non activé</span>
              <span class="user-role">Utilisateur</span>
              <span class="user-join-date">
                <i class="fas fa-calendar-alt"></i>
                Date de création : Aujourd'hui
              </span>
            </div>
          </div>
        </div>

        <!-- Formulaire d'ajout -->
        <form class="user-edit-form" id="useraddForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
           
           <!-- Errur -->
          <?php if (!empty($erreur)) {echo $erreur;} ?>

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
                  <label for="add-last-name">Nom *</label>
                  <input type="text" id="add-last-name" class="input" placeholder="Ecrivez votre nom" name="last">
                  <span id="add-last-name-error" class="controle-saisie"></span>
                </div>
                
                <div class="form-group">
                  <label for="add-name">Prénom *</label>
                  <input type="text" id="add-name" class="input" placeholder="Ecrivez votre prénom" name="name" >
                  <span id="add-name-error" class="controle-saisie"></span>
                </div>
                
                <div class="form-group">
                  <label for="add-email">Email *</label>
                  <input type="text" id="add-email" class="input" placeholder="sarah.ben@example.com"  name="email">
                  <span id="add-email-error" class="controle-saisie"></span>
                </div>
                
                <div class="form-group">
                  <label for="add-phone">Téléphone *</label>
                  <input type="text" id="add-phone" class="input" placeholder="+216 12 345 678" name="phone">
                  <span id="add-phone-error" class="controle-saisie"></span>
                </div>
                
                <div class="form-group">
                  <label for="add-birthday">Date de naissance *</label>
                  <input type="date" id="add-birthday" class="input" name="birthday">
                  <span id="add-birthday-error" class="controle-saisie"></span>
                </div>
                
                <div class="form-group">
                  <label for="add-gender">Genre</label>
                  <select id="add-gender" class="select" name="gender">
                    <option value="femme" selected>Femme</option>
                    <option value="homme">Homme</option>
                    <option value="prefere-ne-pas">Préfère ne pas répondre</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Sécurité -->
            <div class="form-section-card">
              <div class="section-header">
                <h3>
                  <i class="fas fa-lock"></i>
                  Sécurité 
                </h3>
              </div>
              
              <div class="form-grid">
                <div class="form-group">
                  <label for="add-password">Mot de passe * <span id="strength-add" style="margin-left: 70%;"></span> </label>
                  <input type="password" id="add-password" class="input" placeholder="Créez un mot de passe" name="password">
                  <span id="add-password-error" class="controle-saisie"></span>
                </div>
                
                <div class="form-group">
                  <label for="add-confirm">Confirmer le mot de passe *</label>
                  <input type="password" id="add-confirm" class="input" placeholder="Confirmez votre mot de passe" name="confirm" >
                  <span id="add-confirm-error" class="controle-saisie"></span>
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
                    <option value="user"  >Utilisateur</option>
                    <option value="admin" >Administrateur</option>
                  </select>
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
                  <label class="checkbox-option">
                    <input type="checkbox" name="handicap[]" value="aucun" >
                    <span class="checkmark"></span>
                    Aucun
                  </label>
                    
                  <label class="checkbox-option">
                    <input type="checkbox" name="handicap[]" value="moteur">
                    <span class="checkmark"></span>
                    Moteur
                  </label>
                  
                  <label class="checkbox-option">
                    <input type="checkbox" name="handicap[]" value="visuel">
                    <span class="checkmark"></span>
                    Visuel
                  </label>

                  <label class="checkbox-option">
                    <input type="checkbox" name="handicap[]" value="auditif">
                    <span class="checkmark"></span>
                    Auditif
                  </label>
                  <label class="checkbox-option">
                    <input type="checkbox" name="handicap[]" value="cognitif">
                    <span class="checkmark"></span>
                    Cognitif
                  </label>
                  <label class="checkbox-option">
                    <input type="checkbox" name="handicap[]" value="autre">
                    <span class="checkmark"></span>
                    Autre
                  </label>
                </div>
              </div>
            </div>

            
          </div>

          <!-- Form Actions -->
          <div class="form-actions">
            <a href="Ges_utilisateurs.php" class="btn secondary">
              <i class="fas fa-times"></i>
              Annuler
            </a>
            <button type="submit" class="btn primary">
              <i class="fas fa-user-plus"></i>
              Créer utilisateur
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
   <script>


</script>
  <script src="assets/js/script.js"></script>
  <script src="assets/js/controle_saisie_user.js"></script>
  <script> passwordStrong("add-password", "strength-add"); </script>
</body>
</html>