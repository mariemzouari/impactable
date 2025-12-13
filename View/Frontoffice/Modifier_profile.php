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

// recuperer l'utilisateur de la session et son profil 
$user_id = $_SESSION['user_id'];
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
      isset($_POST['password']) &&
      isset($_POST['new']) &&
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
            
            if (!empty($_POST['new'])) {
                if (!empty($_POST['password'])) {
                    if (password_verify($_POST['password'], $user['mot_de_passe'])) {
                       $password = password_hash($_POST['new'], PASSWORD_DEFAULT);
                    } else {
                        $erreur = "Le mot de passe actuel est incorrect";
                    }
                } else {
                    $erreur = "Veuillez confirmer votre mot de passe actuel";
                }
            }

        $new_name = $profile['photo_profil'];
        //photo profil    
        if (isset($_FILES['profile-pic']) && $_FILES['profile-pic']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['profile-pic']['name'], PATHINFO_EXTENSION));
        $new_name = uniqid('profile_') . '.' . $ext;
        $destination = __DIR__ . '/../../uploads/' . $new_name;
        move_uploaded_file($_FILES['profile-pic']['tmp_name'], $destination);}
     

        

     if (empty($erreur)) {
          $user = new Utilisateur([
                    'Id_utilisateur' => $user_id,
                    'nom' => $_POST['last'],
                    'prenom' => $_POST['name'],
                    'email' => $_POST['email'],
                    'numero_tel' => $_POST['phone'],
                    'date_naissance' => $_POST['birthday'],
                    'genre' => $_POST['gender'],
                    'role' => $user['role'], 
                    'type_handicap' => $handicap,
                    'mot_de_passe' => $password  

            ]);
         
            $profile = new Profil([
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
          $userC->updateUser($user, $user_id);
          $profileC->updateProfile($profile, $user_id); 
          header('Location: Profile.php?message=modification_reussie');
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
  <title>Modifier mon profil </title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/style_mariem.css">
</head>
<body>
  <div class="container"> 
    <!-- Header -->
    <header class="site-header" role="banner">
      <div class="brand">
        <button class="nav-toggle" id="navToggle" aria-label="Ouvrir le menu">
          <i class="fas fa-bars"></i>
        </button>
        <div class="logo">
          <img src="assets/images/logo.png" alt="Inclusive Opportunities" class="logo-image">
        </div>
      </div>

      <div class="header-actions">
        <span><b>Modifier mon profil</b></span>
        <a href="Profile.php" class="btn ghost">Retour au profil</a>
      </div>
    </header>

    <!-- Profile Edit Form -->
    <div class="form-container">
      <div class="form-card">
        <div class="form-header">
          <h2>Modifier mon profil</h2>
          <p>Mettez à jour vos informations personnelles</p>
          
         
        </div>

       

        <form method="POST" action="Modifier_profile.php" id="profileForm" enctype="multipart/form-data">
          
        <div class="profile-header">
          <div class="profile-avatar" style="margin-left: 50px;">
            <div class="avatar-placeholder">
              
            <!--Profile picture-->
          <div class="profile-avatar" >
          <img id="avatarPreview" src="../../uploads/<?php echo $profile['photo_profil']; ?>" alt="Photo de profil" >
          <div class="avatar-edit" id="avatarEditBtn">
          <i class="fas fa-camera"></i> Modifier
          <input type="file" id="avatarInput" style="display: none;" name="profile-pic">
          
          </div>


          </div>
            </div>
          </div>

          <div class="profile-info" style="margin-left: 50px;">
            <h1><?php echo $user['prenom'] . ' ' . $user['nom']; ?> </h1>
            <p><i class="fas fa-calendar"></i> Membre depuis : <?php echo date("Y-m-d",strtotime($user['date_inscription'])); ?></p>
          </div>

          
        </div>
        <?php if (!empty($erreur)): ?>
            <div class="alert-error" style="background: #fee; color: #c33; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
              <?php echo $erreur; ?>
            </div>
          <?php endif; ?>
        <span id="avatarInput-error" class="controle-saisie"></span>
        
        <!-- Informations personnelles -->
          <div class="form-section">
            <h3 class="form-section-title">Informations personnelles</h3>
            <div class="form-grid">
              <div class="form-group">
                <label for="last-name-profile">
                  <i class="fas fa-user"></i>
                  Nom
                </label>
                <input id="last-name-profile" name="last" class="input" type="text" 
                       value="<?php echo htmlspecialchars($user['nom'] ); ?>" >
              </div>
              
              <div class="form-group">
                <label for="name-profile">
                  <i class="fas fa-user"></i>
                  Prénom
                </label>
                <input id="name-profile" name="name" class="input" type="text" 
                       value="<?php echo htmlspecialchars($user['prenom'] ); ?>" >
              </div>
              
              <div class="form-group">
                <label for="gender-profile">
                  <i class="fas fa-venus-mars"></i>
                  Genre
                </label>
                <select id="gender-profile" name="gender" class="select" >
                  <option value="femme" <?php echo (isset($user['genre']) && $user['genre']=='femme')?'selected':''; ?>>Femme</option>
                  <option value="homme" <?php echo (isset($user['genre']) && $user['genre']=='homme')?'selected':''; ?>>Homme</option>
                  <option value="prefere_ne_pas_dire" <?php echo (isset($user['genre']) && $user['genre']=='prefere_ne_pas_dire')?'selected':''; ?>>Préfère ne pas répondre</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="birthday-profile">
                  <i class="fas fa-birthday-cake"></i>
                  Date de naissance
                </label>
                <input id="birthday-profile" name="birthday" class="input" type="date" 
                       value="<?php echo $user['date_naissance'] ; ?>" >
              </div>
              
              <div class="form-group">
                <label for="email-profile">
                  <i class="fas fa-envelope"></i>
                  E-mail
                </label>
                <input id="email-profile" name="email" class="input" type="text" 
                       value="<?php echo htmlspecialchars($user['email'] ); ?>" >
              </div>
              
              <div class="form-group">
                <label for="phone-profile">
                  <i class="fas fa-phone"></i>
                  Téléphone
                </label>
                <input id="phone-profile" name="phone" class="input" type="text" 
                       value="<?php echo htmlspecialchars($user['numero_tel'] ); ?>" >
              </div>

              
            </div>
            <span id="info-perso-error" class="controle-saisie"></span>
          </div>

          <!-- Informations de profil -->
          <div class="form-section">
            <h3 class="form-section-title">Informations de profil</h3>
            
            <div class="form-group">
              <label for="bio-profile">
                <i class="fas fa-edit"></i>
                Bio
              </label>
              <textarea id="bio-profile" class="textarea" name="bio"><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-grid">
              <div class="form-group">
                <label for="city-profile">
                  <i class="fas fa-city"></i>
                  Ville
                </label>
                <input id="city-profile" class="input" type="text" name="city" placeholder="Votre ville" 
                       value="<?php echo htmlspecialchars($profile['ville'] ?? ''); ?>">
              </div>
              
              <div class="form-group">
                <label for="country-profile">
                  <i class="fas fa-globe"></i>
                  Pays
                </label>
                <input id="country-profile" class="input" name="country" type="text" placeholder="Votre pays" 
                       value="<?php echo htmlspecialchars($profile['pays']?? ''); ?>">
              </div>
              
              <div class="form-group">
                <label for="job-profile">
                  <i class="fas fa-briefcase"></i>
                  Profession
                </label>
                <input id="job-profile" class="input" name="profession" type="text" placeholder="Votre profession" 
                       value="<?php echo htmlspecialchars($profile['profession'] ?? ''); ?>">
              </div>
              
              <div class="form-group">
                <label for="skills-profile">
                  <i class="fas fa-tools"></i>
                  Compétences
                </label>
                <input id="skills-profile" name="skills" class="input" type="text" placeholder="Vos compétences" 
                       value="<?php echo htmlspecialchars($profile['competences'] ?? ''); ?>">
              </div>
              
              <div class="form-group">
                <label for="linkedin-profile">
                  <i class="fab fa-linkedin"></i>
                  LinkedIn
                </label>
                <input type="text" name="linkedin" id="linkedin-profile" class="input" placeholder="Lien vers votre profil LinkedIn" 
                       value="<?php echo htmlspecialchars($profile['linkedin'] ?? ''); ?>">
              </div>
              
            </div>
            <span id="info-pro-error" class="controle-saisie"></span>
          </div>
          

          <!-- Accessibilité -->
          <div class="form-section">
            <h3 class="form-section-title">Informations d'accessibilité</h3>
            
            <div class="accessibility-options">
              <div class="accessibility-title">
                <i class="fas fa-universal-access"></i>
                Type de handicap (optionnel)
              </div>
              <div class="checkbox-group">
                
                <label class="checkbox-label">
                 <input type="checkbox" name="handicap[]" value="aucun" <?= in_array('aucun', $handicap_array) ? 'checked' : '' ?>> Aucun
                 <input type="checkbox" name="handicap[]" value="moteur" <?= in_array('moteur', $handicap_array) ? 'checked' : '' ?>> Moteur
                 <input type="checkbox" name="handicap[]" value="visuel" <?= in_array('visuel', $handicap_array) ? 'checked' : '' ?>> Visuel
                 <input type="checkbox" name="handicap[]" value="auditif" <?= in_array('auditif', $handicap_array) ? 'checked' : '' ?>> Auditif
                 <input type="checkbox" name="handicap[]" value="cognitif" <?= in_array('cognitif', $handicap_array) ? 'checked' : '' ?>> Cognitif
                 <input type="checkbox" name="handicap[]" value="autre" <?= in_array('autre', $handicap_array) ? 'checked' : '' ?>> Autre 
                </label>
            
              </div>
            </div>
          </div>


                    <!-- Sécurité -->
          <div class="form-section">
            <h3 class="form-section-title">Sécurité</h3>
            
            <div class="form-group">
              <label for="password-profile">
                <i class="fas fa-lock"></i>
                Mot de passe actuel
              </label>
              <input  id="password-profile" class="input" type="password" placeholder="Votre mot de passe actuel" name="password">
            </div>
            
            <div class="form-group">
              <label for="new-password-profile">
                <i class="fas fa-lock"></i>
                Nouveau mot de passe    <span  style="margin-left: 60%;" id="strength-profile"></span>  
              </label>
              
              <input id="new-password-profile" class="input" type="password" placeholder="Nouveau mot de passe" name="new">
                     
            </div>
            
            <div class="form-group">
              <label for="confirm-profile">
                <i class="fas fa-lock"></i>
                Confirmer le nouveau mot de passe
              </label>
              <input id="confirm-profile" class="input" type="password" placeholder="Confirmer le nouveau mot de passe" name="confirm">
            </div>

           <span id="security-error" class="controle-saisie"></span>
          <div class="form-footer">
            <a href="Profile.php" class="btn secondary">Annuler</a>
            <button class="btn primary" type="submit">Enregistrer les modifications</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Footer -->
    <footer class="site-footer">
      <div class="container">
        <div class="footer-content">
          <div class="footer-column">
            <h3>ImpactAble</h3>
            <p class="text-muted">Plateforme dédiée à l'inclusion et à l'impact social.</p>
            <div class="social-links">
              <a href="#"><i class="fab fa-facebook-f"></i></a>
              <a href="#"><i class="fab fa-twitter"></i></a>
              <a href="#"><i class="fab fa-linkedin-in"></i></a>
              <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
          </div>
          <div class="footer-column">
            <h3>Navigation</h3>
            <div class="footer-links">
              <a href="Index.php">Accueil</a>
              <a href="#opportunities">Opportunités</a>
              <a href="#events">Événements</a>
              <a href="#donations">Campagnes</a>
              <a href="#resources">Ressources</a>
              <a href="#forum">Forum</a>
            </div>
          </div>
          <div class="footer-column">
            <h3>Légal</h3>
            <div class="footer-links">
              <a href="#">Mentions légales</a>
              <a href="#">Politique de confidentialité</a>
              <a href="#">Conditions d'utilisation</a>
              <a href="#">Accessibilité</a>
            </div>
          </div>
          <div class="footer-column">
            <h3>Contact</h3>
            <div class="footer-links">
              <a href="mailto:contact@impactable.org">contact@impactable.org</a>
              <a href="tel:+21612345678">+216 12 345 678</a>
              <a href="#">Tunis, Tunisia</a>
            </div>
          </div>
        </div>
        <div class="footer-bottom">
          <p>© <span id="year"></span> ImpactAble — Tous droits réservés.</p>
        </div>
      </div>
    </footer>
  </div>

  <script src="assets/js/script.js"></script>
  <script src="assets\js\profile_picture_mariem.js"> </script>
  <script src="assets\js\controle_saisie_user.js"> </script>
  <script>  
 passwordStrong("new-password-profile", "strength-profile" );
 </script>



</body>
</html>