<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



// verifier si utilisateur  connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../../Controller/UtilisateurController.php';
require_once __DIR__ . '/../../Model/UtilisateurClass.php';

$erreur = "";
$user = null;
$userC = new UtilisateurController();

// recuperer l'utilisateur depuis la session
$user_id = $_SESSION['user_id'];
$user = $userC->showUser($user_id);



//  la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST["last-name"], 
        $_POST["name"], 
        $_POST["email"], 
        $_POST["phone"], 
        $_POST["birthday"], 
        $_POST["gender"])
    ) {
        if (
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

            // mot de passe
            $mot_de_passe = $user['mot_de_passe']; 
            
            if (!empty($_POST['new-password'])) {
                if (!empty($_POST['current-password'])) {
                    if (password_verify($_POST['current-password'], $user['mot_de_passe'])) {
                       $mot_de_passe = password_hash($_POST['new-password'], PASSWORD_DEFAULT);
                    } else {
                        $erreur = "Le mot de passe actuel est incorrect";
                    }
                } else {
                    $erreur = "Veuillez confirmer votre mot de passe actuel";
                }
            }

            // si pas d'erreur la modification
            if (empty($erreur)) {
                $user = new Utilisateur([
                    'Id_utilisateur' => $user_id,
                    'nom' => $_POST['last-name'],
                    'prenom' => $_POST['name'],
                    'email' => $_POST['email'],
                    'numero_tel' => $_POST['phone'],
                    'date_naissance' => $_POST['birthday'],
                    'genre' => $_POST['gender'],
                    'role' => $user['role'], 
                    'type_handicap' => $type_handicap,
                    'mot_de_passe' => $mot_de_passe  
                ]);

                try {
                    $userC->updateUser($user, $user_id);
                    
                    // mise a jour de la session
                    $_SESSION['user_nom'] = $_POST['last-name'];
                    $_SESSION['user_prenom'] = $_POST['name'];
                    $_SESSION['user_email'] = $_POST['email'];
                    
                    header('Location: Profile.php?message=modification_reussie');
                    exit;
                } catch (Exception $e) {
                    $erreur = "Erreur lors de la modification: " . $e->getMessage();
                }
            }
        } else {
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

        <div class="profile-header">
          <div class="profile-avatar">
            <div class="avatar-placeholder">
              
            <!--fix for picture-->
          <div class="profile-avatar">
          <img id="avatarPreview" src="" alt="Photo de profil" style="display: none;">
          <div id="avatarPlaceholder" class="avatar-placeholder">VS</div>
          <input type="file" id="avatarInput" style="display: none;">
          <div class="avatar-edit" id="avatarEditBtn">
          <i class="fas fa-camera"></i> Modifier
          </div>
          </div>
            
            
            
            </div>
          </div>

          <div class="profile-info">
            <h1><?php echo $user['prenom'] . ' ' . $user['nom']; ?> </h1>
            <p>Membre depuis <?php echo $user['date_inscription']; ?></p>
          </div>
        </div>

        <form method="POST" action="Modifier_profile.php" id="profileForm">
          <!-- Informations personnelles -->
          <div class="form-section">
            <h3 class="form-section-title">Informations personnelles</h3>
            <div class="form-grid">
              <div class="form-group">
                <label for="last-name-profile">
                  <i class="fas fa-user"></i>
                  Nom
                </label>
                <input id="last-name-profile" name="last-name" class="input" type="text" 
                       value="<?php echo htmlspecialchars($user['nom'] ?? ''); ?>" >
              </div>
              
              <div class="form-group">
                <label for="name-profile">
                  <i class="fas fa-user"></i>
                  Prénom
                </label>
                <input id="name-profile" name="name" class="input" type="text" 
                       value="<?php echo htmlspecialchars($user['prenom'] ?? ''); ?>" >
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
                       value="<?php echo $user['date_naissance'] ?? ''; ?>" >
              </div>
              
              <div class="form-group">
                <label for="email-profile">
                  <i class="fas fa-envelope"></i>
                  E-mail
                </label>
                <input id="email-profile" name="email" class="input" type="text" 
                       value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" >
              </div>
              
              <div class="form-group">
                <label for="phone-number-profile">
                  <i class="fas fa-phone"></i>
                  Téléphone
                </label>
                <input id="phone-number-profile" name="phone" class="input" type="text" 
                       value="<?php echo htmlspecialchars($user['numero_tel'] ?? ''); ?>" >
              </div>
            </div>
          </div>

          <!-- Informations de profil -->
          <div class="form-section">
            <h3 class="form-section-title">Informations de profil</h3>
            
            <div class="form-group">
              <label for="bio-profile">
                <i class="fas fa-edit"></i>
                Bio
              </label>
              <textarea id="bio-profile" class="textarea" placeholder="Parlez-nous de vous..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-grid">
              <div class="form-group">
                <label for="city-profile">
                  <i class="fas fa-city"></i>
                  Ville
                </label>
                <input id="city-profile" class="input" type="text" placeholder="Votre ville" 
                       value="<?php echo htmlspecialchars($user['ville'] ?? ''); ?>">
              </div>
              
              <div class="form-group">
                <label for="country-profile">
                  <i class="fas fa-globe"></i>
                  Pays
                </label>
                <input id="country-profile" class="input" type="text" placeholder="Votre pays" 
                       value="<?php echo htmlspecialchars($user['pays'] ?? ''); ?>">
              </div>
              
              <div class="form-group">
                <label for="job-profile">
                  <i class="fas fa-briefcase"></i>
                  Profession
                </label>
                <input id="job-profile" class="input" type="text" placeholder="Votre profession" 
                       value="<?php echo htmlspecialchars($user['profession'] ?? ''); ?>">
              </div>
              
              <div class="form-group">
                <label for="skills-profile">
                  <i class="fas fa-tools"></i>
                  Compétences
                </label>
                <input id="skills-profile" class="input" type="text" placeholder="Vos compétences" 
                       value="<?php echo htmlspecialchars($user['competences'] ?? ''); ?>">
              </div>
              
              <div class="form-group">
                <label for="linkedin-profile">
                  <i class="fab fa-linkedin"></i>
                  LinkedIn
                </label>
                <input type="text" id="linkedin-profile" class="input" placeholder="Lien vers votre profil LinkedIn" 
                       value="<?php echo htmlspecialchars($user['linkedin'] ?? ''); ?>">
              </div>
            </div>
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
                <?php
                $handicapTypes = ['aucun', 'moteur', 'visuel', 'auditif', 'cognitif', 'autre', 'tous'];
                $currentHandicap = isset($user['type_handicap']) ? explode(', ', $user['type_handicap']) : ['aucun'];
                
                foreach($handicapTypes as $type):
                ?>
                <label class="checkbox-label">
                  <input type="checkbox" name="handicap-type[]" value="<?php echo $type; ?>" 
                    <?php echo in_array($type, $currentHandicap) ? 'checked' : ''; ?>>
                  <?php echo ucfirst($type); ?>
                </label>
                <?php endforeach; ?>
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
              <input  id="password-profile" class="input" type="password" placeholder="Votre mot de passe actuel" name="current-password">
            </div>
            
            <div class="form-group">
              <label for="new-password-profile">
                <i class="fas fa-lock"></i>
                Nouveau mot de passe
              </label>
              <input id="new-password-profile" class="input" type="password" placeholder="Nouveau mot de passe" name="new-password">
            </div>
            
            <div class="form-group">
              <label for="confirm-profile">
                <i class="fas fa-lock"></i>
                Confirmer le nouveau mot de passe
              </label>
              <input id="confirm-profile" class="input" type="password" placeholder="Confirmer le nouveau mot de passe" name="confirm-password">
            </div>

          <span id="profile-control" class="controle-saisie"></span> 
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
</body>
</html>