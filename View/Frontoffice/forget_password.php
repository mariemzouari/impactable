<?php 
require_once __DIR__ . '/../../Controller/UtilisateurController.php';
require_once __DIR__ . '/../../Controller/OtpController.php';
require_once __DIR__ . '/../../Model/OtpClass.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

$erreur = "";
$userC = new UtilisateurController;
$otpC = new OtpController;

if (isset($_POST['email'])){
   if (!empty($_POST['email'])){
    
    $email = $_POST ['email'];
    $user = $userC->verifyEmail($email);


    if ($user){
    // géneration du code
    $code = random_int(100000, 999999);
    $user_id = $user['Id_utilisateur'];
    $expires_at = date('Y-m-d H:i:s', time() + 600); // 10 mins

    $otp = new OTP([
    'Id_utilisateur' => $user_id ,
    'code' => $code,
    'expires_at' => $expires_at ,
    ]);
    
    $otpC->addOTP($otp);

    
    // email sending 
    
    $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;

                // my email
                $mail->Username = 'mariemzouari014@gmail.com';
                // generated code from google settings
                $mail->Password = 'wseqzosmnorbobqm';

                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                // sender
                $mail->setFrom('mariemzouari014@gmail.com', 'Impactable');

                // receiver
                $mail->addAddress($email);

                // email content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Code';
                $mail->Body = "
                    <p>Hello,</p>
                    <p>Your password reset code is: <b>$code</b></p>
                    <p>It will expire in 10 minutes.</p>
                ";

                $mail->send();

     header("location: reset_code.php?email=$email"); 
     exit;
     }
    catch(Exception $e) {
     $erreur = "Erreur: " . $e->getMessage();

      }


    }
    else {
      $erreur = "L'email n'existe pas.";
    }

   }
  else {
   $erreur ="Veuillez remplir tous les champs";

  }

}



?>




<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mot de passe oublié </title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style_mariem.css">
  <link rel="stylesheet" href="assets/css/style.css">
  

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
            <i class="fas fa-key"></i>
          </div>
        </div>
        <div class="modal-body">
        <div class="form-header">
          <h2>Mot de passe oublié</h2>
          <p>Entrez votre email pour recevoir un code de réinitialisation</p>
        </div>

        <form id="forgotPasswordForm" method="post" action="">
          <div class="form-group">
            <label for="forget-email">
              <i class="fas fa-envelope"></i>
              Adresse email *
            </label>
            <input id="forget-email" name="email" class="input" type="text" placeholder="example@email.com" >
           <span id="forget-email-error" class="controle-saisie"></span>
          </div>

          <div class="form-footer">
            <button type="submit" class="btn primary">Envoyer le code</button>
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
   <script src="assets\js\controle_saisie_user.js"> </script>
</body>
</html>