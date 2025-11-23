<?php 
require_once __DIR__ . '/../../Controller/UtilisateurController.php';
require_once __DIR__ . '/../../Controller/ProfileController.php';
require_once __DIR__ . '/../../Model/UtilisateurClass.php';
require_once __DIR__ . '/../../Model/ProfileClass.php';


session_start();


// verifier si utilisateur connecté si non send to login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$erreur = "";

// controllers
$userC = new UtilisateurController();
$profileC = new ProfileController();

if(!isset($_GET['id'])) {
    header('Location: Ges_utilisateurs.php?error=id_manquant');
    exit;
}

// recuperer l'utilisateur 
$user_id = $_GET['id'];
$user = $userC->showUser($user_id);
$profile = $profileC->showProfile($user_id);

// pour gérer les checkboxes
$type_handicap = $user['type_handicap'] ?? '';
$handicap_array = $type_handicap ? explode(',', $type_handicap) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
   
 if ( isset($_POST['last']) &&
      isset($_POST['name']) &&
      isset($_POST['gender']) &&
      isset($_POST['birthday']) &&
      isset($_POST['email']) &&
      isset($_POST['phone']) &&
      isset($_POST['bio']) &&
      isset($_POST['city']) &&
      isset($_POST['country']) &&
      isset($_POST['profession']) &&
      isset($_POST['skills']) &&
      isset($_POST['linkedin']) 

      ){
        if (
            !empty($_POST["last"]) && 
            !empty($_POST["name"]) && 
            !empty($_POST["email"]) && 
            !empty($_POST["phone"]) && 
            !empty($_POST["birthday"])
        ){
             //checkboxes
             if (empty($_POST['handicap'])) $handicap = 'aucun';
             else $handicap = implode(',', $_POST['handicap']);

             // mot de passe
            $password = $user['mot_de_passe']; 
            

        $new_name = $profile['photo_profil'];
        //photo profil    
        if (isset($_FILES['profile-pic']) && $_FILES['profile-pic']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['profile-pic']['name'], PATHINFO_EXTENSION));
        $new_name = uniqid('profile_') . '.' . $ext;
        $destination = __DIR__ . '/../../uploads/' . $new_name;
        move_uploaded_file($_FILES['profile-pic']['tmp_name'], $destination);}
     

        

     if (empty($erreur)) {
          $user1 = new Utilisateur([
                    'Id_utilisateur' => $user_id,
                    'nom' => $_POST['last'],
                    'prenom' => $_POST['name'],
                    'email' => $_POST['email'],
                    'numero_tel' => $_POST['phone'],
                    'date_naissance' => $_POST['birthday'],
                    'genre' => $_POST['gender'],
                    'role' => $_POST['role'], 
                    'type_handicap' => $handicap,
                    'mot_de_passe' => $password  

            ]);
         
            $profile1 = new Profil([
                'Id_utilisateur' => $user_id, 
                'bio' => $_POST['bio'],
                'ville' => $_POST['city'],
                'pays' => $_POST['country'],
                'profession' => $_POST['profession'],
                'competences' => $_POST['skills'],
                'linkedin' => $_POST['linkedin'],
                'photo_profil' => $new_name ?? $profile['photo_profil']
        ]);
       
        
         try{
          $userC->updateUser($user1, $user_id);
          $profileC->updateProfile($profile1, $user_id); 
          header('Location: Ges_utilisateurs.php?message=modification_reussie');
          exit;   

         }
         catch(Exception $e) {
                   $erreur = "Erreur lors de la modification: " . $e->getMessage();
               }
        


        }


        }
        else {
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
        <!-- Edit Form -->
        <form class="user-edit-form" id="usereditForm" method="post" action="?id=<?php echo $user_id; ?>"enctype="multipart/form-data" >
       
        <!-- User Summary -->
<div class="user-summary-card">
  <div class="user-avatar-large">
    <div class="avatar-container">
      <img id="avatarPreview" src="../../uploads/<?php echo $profile['photo_profil']; ?>" alt="Photo de profil" class="avatar-image" style="border-radius: 50%; height: 100px; width: 100px; border: solid 0.5px rgb(166, 98, 56);  ">
      <label for="avatarInput" class="avatar-upload-btn">
        <i class="fas fa-camera"></i>
      </label>
      <input type="file" id="avatarInput" name="profile-pic" accept="image/jpeg,image/png,image/gif" style="display: none;">
    </div>
  </div>
  <div class="user-summary-info">
    <h1>
      <?php echo $user['prenom'] . ' ' . $user['nom']; ?>
    </h1>
    <p class="user-email">
      <?php echo $user['email'] ?>
    </p>
    <div class="user-meta">
      <span class="user-badge active">Actif</span>
      <span class="user-role">
        <?php echo $user['role'] ?>
      </span>
      <span class="user-join-date">
        <i class="fas fa-calendar-alt"></i>
        Membre depuis 
        <?php echo date("Y-m-d",strtotime($user['date_inscription'])); ?>
      </span>
    </div>
  </div>
</div>



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
                  <input type="text" id="edit-nom" name="last" class="input" value="<?php echo $user['nom'] ; ?>">
                </div>
                
                <div class="form-group">
                  <label for="edit-prenom">Prénom</label>
                  <input type="text" id="edit-prenom" name="name" class="input" value="<?php echo $user['prenom'] ; ?>">
                </div>
                
                <div class="form-group">
                  <label for="edit-email">Email</label>
                  <input type="text" id="edit-email" name="email" class="input" value="<?php echo $user['email'] ; ?>">
                </div>
                
                <div class="form-group">
                  <label for="edit-telephone">Téléphone</label>
                  <input type="text" id="edit-telephone" name="phone" class="input" value="<?php echo $user['numero_tel'] ; ?>">
                </div>
                
                <div class="form-group">
                  <label for="edit-date-naissance">Date de naissance</label>
                  <input type="date" id="edit-date-naissance" name="birthday" class="input" value="<?php echo $user['date_naissance'] ; ?>">
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
                  <input type="text" id="edit-profession" class="input" value="<?php echo htmlspecialchars($profile['profession'] ?? ''); ?>" name="profession">
                </div>
                
                <div class="form-group">
                  <label for="edit-competences">Compétences</label>
                  <input type="text" id="edit-competences" class="input" value="<?php echo htmlspecialchars($profile['competences'] ?? ''); ?>" name="skills">
                </div>
                
                <div class="form-group">
                  <label for="edit-ville">Ville</label>
                  <input type="text" id="edit-ville" class="input" value="<?php echo htmlspecialchars($profile['ville'] ?? ''); ?>" name="city">
                </div>
                
                <div class="form-group">
                  <label for="edit-pays">Pays</label>
                  <input type="text" id="edit-pays" class="input"  value="<?php echo htmlspecialchars($profile['pays']?? ''); ?>" name="country">
                </div>
              </div>

              <div class="form-group" style="margin: 0 30px;">
                <label for="edit-linkedin">LinkedIn</label>
                <input type="text" id="edit-linkedin" class="input" style="width: 49%" stplaceholder="Lien vers votre profil LinkedIn" name="linkedin" value="<?php echo htmlspecialchars($profile['linkedin'] ?? ''); ?>">
              </div>
                
              <div class="form-group" style="margin: 30px 30px;" >
                <label for="edit-bio">Bio</label>
                <textarea id="edit-bio" class="textarea" rows="4" placeholder="Description de l'utilisateur..." name="bio"><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
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
                  
                
                  <label class="checkbox-option"> <input type="checkbox" name="handicap[]" value="aucun" <?= in_array('aucun', $handicap_array) ? 'checked' : '' ?>> Aucun  <span class="checkmark"></span> </label>
                  <label class="checkbox-option">  <input type="checkbox" name="handicap[]" value="moteur" <?= in_array('moteur', $handicap_array) ? 'checked' : '' ?>> Moteur <span class="checkmark"></span> </label>
                  <label class="checkbox-option">  <input type="checkbox" name="handicap[]" value="visuel" <?= in_array('visuel', $handicap_array) ? 'checked' : '' ?>> Visuel <span class="checkmark"></span> </label>
                  <label class="checkbox-option">  <input type="checkbox" name="handicap[]" value="auditif" <?= in_array('auditif', $handicap_array) ? 'checked' : '' ?>> Auditif <span class="checkmark"></span> </label>
                  <label class="checkbox-option">   <input type="checkbox" name="handicap[]" value="cognitif" <?= in_array('cognitif', $handicap_array) ? 'checked' : '' ?>> Cognitif <span class="checkmark"></span> </label>
                  <label class="checkbox-option">  <input type="checkbox" name="handicap[]" value="autre" <?= in_array('autre', $handicap_array) ? 'checked' : '' ?>> Autre <span class="checkmark"></span> </label>
        
                 


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
  <script>// Profile picture preview only (no validation needed)
document.addEventListener('DOMContentLoaded', function() {
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');
    
    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
});</script>
</body>
</html>