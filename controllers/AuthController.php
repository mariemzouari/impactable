<?php
class AuthController {
    private $utilisateurManager;
    
    public function __construct() {
        $this->utilisateurManager = new Utilisateur();
    }
    
    public function connexion() {
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = Utils::sanitize($_POST['email'] ?? '');
            $mot_de_passe = $_POST['mot_de_passe'] ?? '';
            
            if (empty($email) || empty($mot_de_passe)) {
                $errors[] = "Veuillez remplir tous les champs.";
            } else {
                $user = $this->utilisateurManager->connecter($email, $mot_de_passe);
                
                if ($user) {
                    $_SESSION['user_id'] = $user['Id_utilisateur'];
                    $_SESSION['user_nom'] = $user['nom'];
                    $_SESSION['user_prenom'] = $user['prenom'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    $redirect = $_SESSION['redirect_url'] ?? 'index.php?action=offres';
                    unset($_SESSION['redirect_url']);
                    Utils::redirect($redirect);
                } else {
                    $errors[] = "Email ou mot de passe incorrect.";
                }
            }
        }
        
        require_once __DIR__ . '/../views/frontoffice/auth/connexion.php';
    }
    
    public function deconnexion() {
        Utils::deconnecter();
        Utils::redirect('index.php?action=connexion');
    }
}