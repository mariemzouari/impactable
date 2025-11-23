<?php  
session_start();
require_once __DIR__ . '/../../Controller/UtilisateurController.php';

$error = "";
$userC = new UtilisateurController();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && 
    isset($_POST['email']) && 
    isset($_POST['password'])){
     
        if (!empty($_POST['email']) && 
            !empty($_POST['password'])){

             $email = $_POST['email'];
             $password = $_POST['password'];
             try{
             $user = $userC->verifyLogin($email, $password); 
              if($user){
                $_SESSION['user_id'] = $user['Id_utilisateur'];
                $_SESSION['user_role'] = $user['role'];
                
                if($_SESSION['user_role'] == "user"){
                header('Location: Profile.php');
                exit;}       
                
                else {
                header('Location: ../Backoffice/index.php');
                exit;}


              }   
              else { $error = "Email ou mot de passe incorrect"; }


             }
             catch(Exception $e){
             $error = "Erreur: " . $e->getMessage();

             }
     
     
 } 

else {$error = "Veuillez remplir tous les champs";}

}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
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

    <!-- Modal de connexion -->
    <div class="modal-backdrop" id="loginModal" style="display: block;">
      <div class="modal">
        <div class="auth-decoration">
          <div class="auth-icon">
            <i class="fas fa-sign-in-alt"></i>
          </div>
        </div>
        <div class="modal-body">
          <h2 class="auth-title">Connexion</h2>
          <p class="auth-subtitle">Accédez à votre compte ImpactAble</p>
          
          <?php if (!empty($error)): ?>
            <div class="alert-error" style="background: #fee; color: #c33; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
              <?php echo $error; ?>
            </div>
          <?php endif; ?>
          
          <form method="POST" action="login.php"  id= "loginForm">
            <div class="form-group">
              <label for="login-email">
                <i class="fas fa-envelope"></i>
                E‑mail
              </label>
              <input id="login-email" name="email" class="input" type="text" placeholder="votremail@email.com" >
            </div>
            <div class="form-group">
              <label for="login-password">
                <i class="fas fa-lock"></i>
                Mot de passe
              </label>
              <input id="login-password" name="password" class="input" type="password" placeholder="Votre mot de passe" >
            </div>
             

            <span id="login-control" class="controle-saisie"></span> 

            <div class="form-footer">
              <div class="form-links">
                <a href="#">Mot de passe oublié?</a>
              </div>
              <button class="btn primary" type="submit">Se connecter</button>
            </div>
          </form>
          <div class="text-center mt-24">
            <p class="text-muted">Pas encore de compte? <a href="signup.php" id="switchToSignup">S'inscrire</a></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer-->
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
   <script src="assets\js\controle_saisie_user.js"> </script>

</body>
</html>