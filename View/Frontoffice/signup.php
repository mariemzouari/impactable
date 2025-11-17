<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../Controller/UtilisateurController.php';
require_once __DIR__ . '/../../Model/UtilisateurClass.php';

$erreur = "";
$userC = new UtilisateurController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        isset($_POST["last-name"]) &&
        isset($_POST["name"]) &&
        isset($_POST["email"]) &&
        isset($_POST["phone"]) &&
        isset($_POST["birthday"]) &&
        isset($_POST["password"]) &&
        isset($_POST["confirm"])
    ) {
        
        
        // Votre code existant...
        if (
            !empty($_POST["last-name"]) &&
            !empty($_POST["name"]) &&
            !empty($_POST["email"]) &&
            !empty($_POST["phone"]) &&
            !empty($_POST["birthday"]) &&
            !empty($_POST["password"]) &&
            !empty($_POST["confirm"])
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
                'nom' => $_POST['last-name'],
                'prenom' => $_POST['name'],
                'email' => $_POST['email'],
                'numero_tel' => $_POST['phone'] ?? null,
                'date_naissance' => $_POST['birthday'] ?? null,
                'mot_de_passe' => $_POST['password'],
                'genre' => $_POST['gender'] ?? 'prefere_ne_pas_dire',
                'role' => $_POST['role'] ?? 'user',
                'type_handicap' => $type_handicap
            ]);

            try {
                $userC->addUser($user);
                header('Location: login.php'); 
                exit;
            } catch (Exception $e) {
                $erreur = "Erreur lors de l'ajout : " . $e->getMessage();
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
  <title>Signup</title>
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

    <!-- Signup Modal -->
    <div class="modal-backdrop" id="signupModal">
      <div class="modal">
        <div class="auth-decoration">
          <div class="auth-icon">
            <i class="fas fa-user-plus"></i>
          </div>
        </div>
        <div class="modal-body">
          <h2 class="auth-title">Inscription</h2>
          <p class="auth-subtitle">Rejoignez la communauté ImpactAble</p>



          <form id="signupForm" method="post" action="signup.php">
            <div class="form-group">
              <label for="signup-name">
                <i class="fas fa-user"></i>
                Nom complet *
              </label>
              <input id="signup-last-name" class="input" type="text" placeholder="Nom" name="last-name">
              <input id="signup-name" class="input" type="text" placeholder="Prénom" name="name">
            </div>

            <div class="form-group">
              <label for="gender">
                <i class="fa-solid fa-mars-and-venus"></i>
                Genre
              </label>
              <select id="gender" class="select" name="gender">
                <option value="prefere_ne_pas_dire">Préfère ne pas dire</option>  
                <option value="femme">Femme</option>
                <option value="homme">Homme</option>
              </select>
            </div>

            <div class="form-group">
              <label for="birthday">
                <i class="fa-solid fa-calendar-alt"></i>
                Date de Naissance *
              </label>
              <input type="date" id="birthday" class="input" name="birthday">
            </div>

            <div class="form-group">
              <label for="signup-email">
                <i class="fas fa-envelope"></i>
                E‑mail *
              </label>
              <input name="email" id="signup-email" class="input" type="text" placeholder="vous@email.com">
            </div>

            <div class="form-group">
              <label for="phone-number">
                <i class="fas fa-phone"></i>
                Numéro *
              </label>
              <input id="phone-number" class="input" type="text" placeholder="+216 00 000 000" name="phone">
            </div>

            <div class="form-group">
              <label for="signup-password">
                <i class="fas fa-lock"></i>
                Mot de passe *
              </label>
              <input id="signup-password" class="input" type="password" placeholder="Créez un mot de passe" name="password">
            </div>
            <div class="form-group">
              <label for="signup-confirm">
                <i class="fas fa-lock"></i>
                Confirmer le mot de passe *
              </label>
              <input id="signup-confirm" class="input" type="password" placeholder="Confirmez votre mot de passe" name="confirm">
            </div>

            <div class="accessibility-options">
              <div class="accessibility-title">
                <i class="fas fa-universal-access"></i>
                Informations d'accessibilité (optionnel)
              </div>
              <div class="checkbox-group">
                <label class="checkbox-label">
                  <input type="checkbox" name="handicap-type[]" value="aucun" checked>
                  Aucun
                </label>
                <label class="checkbox-label">
                  <input type="checkbox" name="handicap-type[]" value="moteur">
                  Moteur
                </label>
                <label class="checkbox-label">
                  <input type="checkbox" name="handicap-type[]" value="visuel">
                  Visuel
                </label>
                <label class="checkbox-label">
                  <input type="checkbox" name="handicap-type[]" value="auditif">
                  Auditif
                </label>
                <label class="checkbox-label">
                  <input type="checkbox" name="handicap-type[]" value="cognitif">
                  Cognitif 
                </label>
                <label class="checkbox-label">
                  <input type="checkbox" name="handicap-type[]" value="autre">
                  Autre
                </label>
                <label class="checkbox-label">
                  <input type="checkbox" name="handicap-type[]" value="tous">
                  Tous
                </label>
              </div>
            </div>

            <span id="signup-control" class="controle-saisie"></span>

            <div class="form-footer">
              <button class="btn primary" type="submit">S'inscrire</button>
              <div class="text-center mt-24">
                <p class="text-muted">Déjà un compte? <a href="login.php" id="switchToLogin">Se connecter</a></p>
              </div>
            </div>
          </form>
        </div>
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