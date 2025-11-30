<?php
class OffreController {
    private $offreManager;
    private $candidatureManager;
    private $utilisateurManager;
    
    public function __construct() {
        $this->offreManager = new Offre();
        $this->candidatureManager = new Candidature();
        $this->utilisateurManager = new Utilisateur();
    }
    
    public function liste() {
        if (!Utils::isAuthenticated()) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            Utils::redirect('index.php?action=connexion');
        }
        
        $filters = [];
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!empty($_GET['type_offre'])) $filters['type_offre'] = $_GET['type_offre'];
            if (!empty($_GET['mode'])) $filters['mode'] = $_GET['mode'];
            if (!empty($_GET['horaire'])) $filters['horaire'] = $_GET['horaire'];
            if (isset($_GET['disability_friendly'])) $filters['disability_friendly'] = $_GET['disability_friendly'];
            if (!empty($_GET['type_handicap'])) $filters['type_handicap'] = $_GET['type_handicap'];
        }
        
        $offres = $this->offreManager->getAll($filters, 6);
        $stats = $this->offreManager->getStats();
        $candidaturesPlacees = $this->candidatureManager->getCandidaturesPlacees();
        $user = $this->utilisateurManager->getById($_SESSION['user_id']);
        
        require_once __DIR__ . '/../views/frontoffice/offre/liste.php';
    }
    
public function details() {
    $offreId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $offre = $this->offreManager->getById($offreId);
    
    if (!$offre) {
        header('HTTP/1.0 404 Not Found');
        require_once __DIR__ . '/../views/errors/404.php';
        exit;
    }
    
    $hasApplied = false;
    if (isset($_SESSION['user_id'])) {
        $hasApplied = $this->candidatureManager->hasAlreadyApplied($_SESSION['user_id'], $offreId);
    }
    
    $viewPath = __DIR__ . '/../views/frontoffice/offre/details.php';
    if (!file_exists($viewPath)) {
        die("View file not found: " . $viewPath);
    }
    
    require_once $viewPath;
}
    
    public function poster() {
        if (!Utils::isAuthenticated()) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            Utils::redirect('index.php?action=connexion');
        }
        
        $user = $this->utilisateurManager->getById($_SESSION['user_id']);
        $success = false;
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = Utils::sanitize($_POST['titre'] ?? '');
            $description = Utils::sanitize($_POST['description'] ?? '');
            $date_expiration = $_POST['date_expiration'] ?? '';
            $impact_sociale = Utils::sanitize($_POST['impact_sociale'] ?? '');
            $disability_friendly = isset($_POST['disability_friendly']) ? 1 : 0;
            $type_handicap = $_POST['type_handicap'] ?? [];
            $type_offre = $_POST['type_offre'] ?? 'emploi';
            $mode = $_POST['mode'] ?? 'presentiel';
            $horaire = $_POST['horaire'] ?? 'temps_plein';
            $lieu = Utils::sanitize($_POST['lieu'] ?? '');
            
            // Validation
            if (empty($titre)) {
                $errors[] = "Le titre de l'offre est obligatoire.";
            }
            
            if (empty($description)) {
                $errors[] = "La description de l'offre est obligatoire.";
            }
            
            if (empty($impact_sociale)) {
                $errors[] = "L'impact social de l'offre est obligatoire.";
            }
            
            if (empty($date_expiration) || !strtotime($date_expiration)) {
                $errors[] = "La date d'expiration est invalide.";
            }
            
            if (empty($errors)) {
                $type_handicap_str = !empty($type_handicap) ? implode(',', $type_handicap) : 'tous';
                
                $offreData = [
                    'Id_utilisateur' => $_SESSION['user_id'],
                    'titre' => $titre,
                    'description' => $description,
                    'date_expiration' => $date_expiration,
                    'impact_sociale' => $impact_sociale,
                    'disability_friendly' => $disability_friendly,
                    'type_handicap' => $type_handicap_str,
                    'type_offre' => $type_offre,
                    'mode' => $mode,
                    'horaire' => $horaire,
                    'lieu' => $lieu
                ];
                
                if ($this->offreManager->create($offreData)) {
                    $success = true;
                    $_SESSION['success'] = "Votre offre a été publiée avec succès !";
                    Utils::redirect('index.php?action=offres');
                } else {
                    $errors[] = "Une erreur est survenue lors de la publication de l'offre.";
                }
            }
        }
        
        require_once __DIR__ . '/../views/frontoffice/offre/poster.php';
    }
    
    public function mesOffres() {
        if (!Utils::isAuthenticated()) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            Utils::redirect('index.php?action=connexion');
        }

        $userId = $_SESSION['user_id'];
        $user = $this->utilisateurManager->getById($userId);
        $offres = $this->offreManager->getByUser($userId);

        // Messages de succès
        $success = $_SESSION['success'] ?? '';
        $error = $_SESSION['error'] ?? '';
        unset($_SESSION['success'], $_SESSION['error']);

        require_once __DIR__ . '/../views/frontoffice/offre/mes_offres.php';
    }

    public function modifier() {
        if (!Utils::isAuthenticated()) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            Utils::redirect('index.php?action=connexion');
        }

        $userId = $_SESSION['user_id'];
        $user = $this->utilisateurManager->getById($userId);
        $offreId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        // Vérifier que l'utilisateur est propriétaire de l'offre
        if (!$this->offreManager->isOwner($offreId, $userId)) {
            $_SESSION['error'] = "Vous n'avez pas accès à cette offre.";
            Utils::redirect('index.php?action=mes-offres');
        }

        $offre = $this->offreManager->getById($offreId);
        $candidatureCount = $this->candidatureManager->getCountByOffre($offreId);

        // Traitement du formulaire de modification
        $success = false;
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = Utils::sanitize($_POST['titre'] ?? '');
            $description = Utils::sanitize($_POST['description'] ?? '');
            $date_expiration = $_POST['date_expiration'] ?? '';
            $impact_sociale = Utils::sanitize($_POST['impact_sociale'] ?? '');
            $disability_friendly = isset($_POST['disability_friendly']) ? 1 : 0;
            $type_handicap = $_POST['type_handicap'] ?? [];
            $type_offre = $_POST['type_offre'] ?? 'emploi';
            $mode = $_POST['mode'] ?? 'presentiel';
            $horaire = $_POST['horaire'] ?? 'temps_plein';
            $lieu = Utils::sanitize($_POST['lieu'] ?? '');
            
            // Validation
            if (empty($titre)) {
                $errors[] = "Le titre de l'offre est obligatoire.";
            }
            
            if (empty($description)) {
                $errors[] = "La description de l'offre est obligatoire.";
            }
            
            if (empty($impact_sociale)) {
                $errors[] = "L'impact social de l'offre est obligatoire.";
            }
            
            if (empty($date_expiration) || !strtotime($date_expiration)) {
                $errors[] = "La date d'expiration est invalide.";
            }
            
            if (empty($errors)) {
                $type_handicap_str = !empty($type_handicap) ? implode(',', $type_handicap) : 'tous';
                
                $offreData = [
                    'Id_utilisateur' => $userId,
                    'titre' => $titre,
                    'description' => $description,
                    'date_expiration' => $date_expiration,
                    'impact_sociale' => $impact_sociale,
                    'disability_friendly' => $disability_friendly,
                    'type_handicap' => $type_handicap_str,
                    'type_offre' => $type_offre,
                    'mode' => $mode,
                    'horaire' => $horaire,
                    'lieu' => $lieu
                ];
                
                if ($this->offreManager->update($offreId, $offreData)) {
                    $success = true;
                    $_SESSION['success'] = "Votre offre a été modifiée avec succès !";
                    // Recharger les données de l'offre
                    $offre = $this->offreManager->getById($offreId);
                } else {
                    $errors[] = "Une erreur est survenue lors de la modification de l'offre.";
                }
            }
        }

        require_once __DIR__ . '/../views/frontoffice/offre/modifier.php';
    }

    public function supprimer() {
        if (!Utils::isAuthenticated()) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            Utils::redirect('index.php?action=connexion');
        }

        $userId = $_SESSION['user_id'];
        $offreId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        // Vérifier que l'utilisateur est propriétaire de l'offre
        if (!$this->offreManager->isOwner($offreId, $userId)) {
            $_SESSION['error'] = "Vous n'avez pas accès à cette offre.";
            Utils::redirect('index.php?action=mes-offres');
        }

        // Supprimer l'offre
        if ($this->offreManager->delete($offreId, $userId)) {
            $_SESSION['success'] = "L'offre a été supprimée avec succès.";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la suppression de l'offre.";
        }

        Utils::redirect('index.php?action=mes-offres');
    }

    public function gestion() {
        if (!Utils::isAuthenticated()) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            Utils::redirect('index.php?action=connexion');
        }

        $userId = $_SESSION['user_id'];
        $user = $this->utilisateurManager->getById($userId);
        $offreId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        // Vérifier que l'utilisateur est propriétaire de l'offre
        if (!$this->offreManager->isOwner($offreId, $userId)) {
            $_SESSION['error'] = "Vous n'avez pas accès à cette offre.";
            Utils::redirect('index.php?action=mes-offres');
        }

        $offre = $this->offreManager->getById($offreId);
        $candidatures = $this->candidatureManager->getByOffre($offreId, $userId);

        // Traitement du changement de statut
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $candidatureId = (int)$_POST['candidature_id'];
            $action = $_POST['action'];
            
            $statusMap = [
                'accepter' => 'retenu',
                'refuser' => 'refuse',
                'entretien' => 'entretien',
                'en_revue' => 'en_revue'
            ];
            
            if (isset($statusMap[$action])) {
                if ($this->candidatureManager->updateStatus($candidatureId, $statusMap[$action], $userId)) {
                    $_SESSION['success'] = "Statut de la candidature mis à jour.";
                } else {
                    $_SESSION['error'] = "Erreur lors de la mise à jour du statut.";
                }
            }
            
            Utils::redirect("index.php?action=gestion-offre&id=$offreId");
        }

        // Messages
        $success = $_SESSION['success'] ?? '';
        $error = $_SESSION['error'] ?? '';
        unset($_SESSION['success'], $_SESSION['error']);

        require_once __DIR__ . '/../views/frontoffice/offre/gestion.php';
    }
}