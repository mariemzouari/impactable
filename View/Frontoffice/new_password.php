
<?php 
require_once __DIR__ . '/../../Controller/UtilisateurController.php';
require_once __DIR__ . '/../../Model/UtilisateurClass.php';


$erreur = "";
$userC = new UtilisateurController;

if (isset($_GET['email'])){
$user = $userC->verifyEmail($_GET['email']);}


if ($user){
$user_id = $user['Id_utilisateur'];

if (isset($_POST['password']) &&
isset($_POST['confirm']) ){
  if (!empty($_POST['password']) &&
  !empty($_POST['confirm']) ){
    
  $password = $_POST['password'];

  $user = new Utilisateur([
                    'Id_utilisateur' => $user_id,
                    'nom' => $user['nom'],
                    'prenom' => $user['prenom'],
                    'email' => $user['email'],
                    'numero_tel' => $user['numero_tel'],
                    'date_naissance' => $user['date_naissance'],
                    'genre' => $user['genre'],
                    'role' => $user['role'], 
                    'type_handicap' => $user['type_handicap'],
                    'mot_de_passe' => password_hash($password, PASSWORD_DEFAULT) 

            ]);


  try{
          $userC->updateUser($user, $user_id); 
          header('Location: login.php');
          exit;   

         }
         catch(Exception $e) {
                   $erreur = "Erreur lors de la modification: " . $e->getMessage();
               }


  }
  else { $erreur =" Veuillez remplir tout les champs.";}
  

}

}
else  { $erreur =" Utilisateur non trouvé.";}


?>



<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nouveau mot de passe</title>
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
        <a href="login.php" class="btn ghost" id="loginBtn">Se connecter</a>
        <a href="signup.php" class="btn primary" id="signupBtn">S'inscrire</a>
      </div>
    </header>
        <!-- Side Panel Navigation -->
    <div class="side-panel" id="sidePanel">
      <div class="panel-header">
         <div class="logo">
          <img src="assets/images/logo.png" alt="Inclusive Opportunities" class="logo-image">
        </div>
        <button class="panel-close" id="panelClose">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <nav class="panel-nav">
        <div class="nav-section">
          <div class="nav-title">Navigation</div>
          <a href="#home" class="nav-link active">
            <i class="fas fa-home"></i>
            <span>Accueil</span>
          </a>
          <a href="#opportunities" class="nav-link">
            <i class="fas fa-briefcase"></i>
            <span>Opportunités</span>
          </a>
          <a href="#events" class="nav-link">
            <i class="fas fa-calendar-alt"></i>
            <span>Événements</span>
          </a>
          <a href="#donations" class="nav-link">
            <i class="fas fa-hand-holding-heart"></i>
            <span>Campagnes</span>
          </a>
          <a href="#resources" class="nav-link">
            <i class="fas fa-book"></i>
            <span>Ressources</span>
          </a>
          <a href="#forum" class="nav-link">
            <i class="fas fa-comments"></i>
            <span>Forum</span>
          </a>
          <a href="#reclamations" class="nav-link">
            <i class="fas fa-comment-alt"></i>
            <span>Réclamations</span>
          </a>
        </div> 
      </nav>
      
      <div class="panel-footer">
        <div class="user-profile">
          <div class="user-avatar">VS</div>
          <div class="user-info">
            <h4>Visiteur</h4>
            <p>Connectez-vous pour plus de fonctionnalités</p>
          </div>
        </div>
      </div>
    </div>
    
    <div class="panel-overlay" id="panelOverlay"></div>

    <!-- Main Content -->
    <main class="modal-backdrop"  style="display: block;">

      <div class="modal">

        <div class="auth-decoration">
          <div class="auth-icon">
            <i class="fas fa-lock-open"></i>
          </div>
        </div>
        <div class="modal-body">
        <div class="form-header">
          <h2>Nouveau mot de passe</h2>
          <p>Créez votre nouveau mot de passe</p>
        </div>

        <form id="newPasswordForm" method="post" action="">
          <div class="form-group">
            <label for="new-password">
              <i class="fas fa-lock"></i>
              Nouveau mot de passe * <span id="strength-new" style="margin-left: 40%;"></span>
            </label>
            <input id="new-password" name="password" class="input" type="password" placeholder="Créez un nouveau mot de passe" >
             <span id="new-password-error" class="controle-saisie"></span>
          </div>

          <div class="form-group">
            <label for="confirm-new-password">
              <i class="fas fa-lock"></i>
              Confirmer le nouveau mot de passe *
            </label>
            <input id="confirm-new-password" name="confirm" class="input" type="password" placeholder="Confirmez votre nouveau mot de passe" >
             <span id="confirm-new-password-error" class="controle-saisie"></span>
          </div>

          <div class="form-footer">
            <button type="submit" class="btn primary">Réinitialiser le mot de passe</button>
            <div class="form-links">
              <a href="login.php"><i class="fas fa-arrow-left"></i> Retour à la connexion</a>
            </div>
          </div>
        </form>
        </div>
      </div>
    </main>

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
              <a href="#home">Accueil</a>
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
  <script src="assets/js/controle_saisie_user.js"></script>
  <script>passwordStrong("new-password", "strength-new");</script>
</body>
</html>