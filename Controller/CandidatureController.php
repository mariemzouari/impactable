<?php
class CandidatureController {
    private $candidatureManager;
    private $offreManager;
    private $utilisateurManager;
    
    public function __construct() {
        $this->candidatureManager = new Candidature();
        $this->offreManager = new Offre();
        $this->utilisateurManager = new Utilisateur();
    }
    
    public function postuler() {
        if (!Utils::isAuthenticated()) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            Utils::redirect('index.php?action=connexion');
        }

        $offreId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $offre = $this->offreManager->getById($offreId);

        // Vérifier si l'offre existe
        if (!$offre) {
            $_SESSION['error'] = "L'offre demandée n'existe pas.";
            Utils::redirect('index.php?action=offres');
        }

        // Vérifier si l'utilisateur a déjà postulé
        $userId = $_SESSION['user_id'];
        if ($this->candidatureManager->hasAlreadyApplied($userId, $offreId)) {
            $_SESSION['info'] = "Vous avez déjà postulé à cette offre.";
            Utils::redirect('index.php?action=offres');
        }

        // Récupérer les informations de l'utilisateur
        $user = $this->utilisateurManager->getById($userId);

        // Traitement du formulaire
        $success = false;
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cv = Utils::sanitize($_POST['cv'] ?? '');
            $linkedin = Utils::sanitize($_POST['linkedin'] ?? '');
            $lettre_motivation = Utils::sanitize($_POST['lettre_motivation'] ?? '');
            $notes = Utils::sanitize($_POST['notes'] ?? '');
            
            // Validation
            if (empty($lettre_motivation)) {
                $errors[] = "La lettre de motivation est obligatoire.";
            }
            
            if (empty($cv) && empty($linkedin)) {
                $errors[] = "Veuillez fournir au moins un CV ou un profil LinkedIn.";
            }
            
            if (empty($errors)) {
                $candidatureData = [
                    'Id_offre' => $offreId,
                    'Id_utilisateur' => $userId,
                    'cv' => $cv,
                    'linkedin' => $linkedin,
                    'lettre_motivation' => $lettre_motivation,
                    'notes' => $notes
                ];
                
                if ($this->candidatureManager->create($candidatureData)) {
                    $success = true;
                    $_SESSION['success'] = "Votre candidature a été envoyée avec succès !";
                } else {
                    $errors[] = "Une erreur est survenue lors de l'envoi de votre candidature.";
                }
            }
        }

        require_once __DIR__ . '/../View/Frontoffice/candidature/postuler.php';
    }
    
    public function mesCandidatures() {
        if (!Utils::isAuthenticated()) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            Utils::redirect('index.php?action=connexion');
        }

        $userId = $_SESSION['user_id'];
        $user = $this->utilisateurManager->getById($userId);
        
        // Variables pour les messages
        $success = '';
        $error = '';
        
        // Gérer la suppression de candidature
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'supprimer_candidature') {
            $candidatureId = $_POST['candidature_id'] ?? '';
            
            if ($candidatureId) {
                // Vérifier que l'utilisateur est bien propriétaire de la candidature
                if ($this->candidatureManager->isOwner($candidatureId, $userId)) {
                    if ($this->candidatureManager->delete($candidatureId, $userId)) {
                        $success = "Votre candidature a été retirée avec succès.";
                    } else {
                        $error = "Une erreur est survenue lors du retrait de votre candidature.";
                    }
                } else {
                    $error = "Vous n'êtes pas autorisé à retirer cette candidature.";
                }
            } else {
                $error = "Identifiant de candidature manquant.";
            }
        }
        
        // Récupérer les candidatures de l'utilisateur
        $candidatures = $this->candidatureManager->getByUser($userId);

        require_once __DIR__ . '/../View/Frontoffice/candidature/mes_candidatures.php';
    }
}