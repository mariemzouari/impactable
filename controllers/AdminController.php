<?php
class AdminController {
    private $offreManager;
    private $candidatureManager;
    private $utilisateurManager;
    
    public function __construct() {
        $this->offreManager = new Offre();
        $this->candidatureManager = new Candidature();
        $this->utilisateurManager = new Utilisateur();
    }
    
private function checkAdmin() {
    if (!Utils::isAuthenticated()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        Utils::redirect('index.php?action=connexion');
    }
    
    $user = $this->utilisateurManager->getById($_SESSION['user_id']);
    if ($user['role'] !== 'admin') {
        $_SESSION['error'] = "Accès refusé. Réservé aux administrateurs.";
        Utils::redirect('index.php?action=offres');
    }
}
    
    public function dashboard() {
        $this->checkAdmin();
        
        $user = $this->utilisateurManager->getById($_SESSION['user_id']);
        
        // Statistiques
        $stats = [
            'total_offres' => $this->offreManager->getStats()['total_offres'],
            'offres_actives' => $this->getOffresActivesCount(),
            'total_candidatures' => $this->getTotalCandidatures(),
            'utilisateurs_inscrits' => $this->getUtilisateursCount()
        ];
        
        // Dernières offres
        $dernieres_offres = $this->offreManager->getAll([], 5);
        
        // Dernières candidatures
        $dernieres_candidatures = $this->getDernieresCandidatures(5);
        
        require_once __DIR__ . '/../views/backoffice/admin/dashboard.php';
    }
    
    public function gestionOffres() {
        $this->checkAdmin();
        
        $user = $this->utilisateurManager->getById($_SESSION['user_id']);
        $offres = $this->offreManager->getAll([], 50); // Toutes les offres
        
        // Messages
        $success = $_SESSION['success'] ?? '';
        $error = $_SESSION['error'] ?? '';
        unset($_SESSION['success'], $_SESSION['error']);
        
        require_once __DIR__ . '/../views/backoffice/admin/gestion_offres.php';
    }
    
    public function voirOffre() {
        $this->checkAdmin();
        
        $offreId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $offre = $this->offreManager->getById($offreId);
        
        if (!$offre) {
            $_SESSION['error'] = "Offre non trouvée.";
            Utils::redirect('index.php?action=admin-gestion-offres');
        }
        
        $user = $this->utilisateurManager->getById($_SESSION['user_id']);
        $createur = $this->utilisateurManager->getById($offre['Id_utilisateur']);
        $candidatures = $this->candidatureManager->getByOffre($offreId, $offre['Id_utilisateur']);
        
        require_once __DIR__ . '/../views/backoffice/admin/voir_offre.php';
    }
    
    public function modifierOffre() {
        $this->checkAdmin();
        
        $offreId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $offre = $this->offreManager->getById($offreId);
        
        if (!$offre) {
            $_SESSION['error'] = "Offre non trouvée.";
            Utils::redirect('index.php?action=admin-gestion-offres');
        }
        
        $user = $this->utilisateurManager->getById($_SESSION['user_id']);
        $createur = $this->utilisateurManager->getById($offre['Id_utilisateur']);
        
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
                    'Id_utilisateur' => $offre['Id_utilisateur'], // Garder le propriétaire original
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
                    $_SESSION['success'] = "L'offre a été modifiée avec succès !";
                    // Recharger les données de l'offre
                    $offre = $this->offreManager->getById($offreId);
                } else {
                    $errors[] = "Une erreur est survenue lors de la modification de l'offre.";
                }
            }
        }
        
        require_once __DIR__ . '/../views/backoffice/admin/modifier_offre.php';
    }
    
    public function supprimerOffre() {
        $this->checkAdmin();
        
        $offreId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $offre = $this->offreManager->getById($offreId);
        
        if (!$offre) {
            $_SESSION['error'] = "Offre non trouvée.";
            Utils::redirect('index.php?action=admin-gestion-offres');
        }
        
        // Supprimer l'offre (l'admin peut supprimer n'importe quelle offre)
        if ($this->offreManager->deleteAdmin($offreId)) {
            $_SESSION['success'] = "L'offre a été supprimée avec succès.";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la suppression de l'offre.";
        }
        
        Utils::redirect('index.php?action=admin-gestion-offres');
    }
    
    public function gestionCandidatures() {
        $this->checkAdmin();
        
        $user = $this->utilisateurManager->getById($_SESSION['user_id']);
        $candidatures = $this->getToutesCandidatures();
        
        require_once __DIR__ . '/../views/backoffice/admin/gestion_candidatures.php';
    }
    
    public function gestionUtilisateurs() {
        $this->checkAdmin();
        
        $user = $this->utilisateurManager->getById($_SESSION['user_id']);
        $utilisateurs = $this->getAllUtilisateurs();
        
        require_once __DIR__ . '/../views/backoffice/admin/gestion_utilisateurs.php';
    }
    
    // Méthodes utilitaires pour les statistiques
    private function getOffresActivesCount() {
        try {
            $stmt = $this->offreManager->getConnection()->prepare(
                "SELECT COUNT(*) as count FROM offre WHERE date_expiration >= CURDATE() OR date_expiration IS NULL"
            );
            $stmt->execute();
            return $stmt->fetch()['count'];
        } catch(PDOException $e) {
            error_log("Erreur comptage offres actives: " . $e->getMessage());
            return 0;
        }
    }
    
    private function getTotalCandidatures() {
        try {
            $stmt = $this->offreManager->getConnection()->prepare("SELECT COUNT(*) as count FROM candidature");
            $stmt->execute();
            return $stmt->fetch()['count'];
        } catch(PDOException $e) {
            error_log("Erreur comptage candidatures: " . $e->getMessage());
            return 0;
        }
    }
    
    private function getUtilisateursCount() {
        try {
            $stmt = $this->offreManager->getConnection()->prepare("SELECT COUNT(*) as count FROM utilisateur");
            $stmt->execute();
            return $stmt->fetch()['count'];
        } catch(PDOException $e) {
            error_log("Erreur comptage utilisateurs: " . $e->getMessage());
            return 0;
        }
    }
    
    private function getDernieresCandidatures($limit = 5) {
        try {
            $stmt = $this->offreManager->getConnection()->prepare("
                SELECT c.*, u.prenom, u.nom, u.email, o.titre 
                FROM candidature c 
                JOIN utilisateur u ON c.Id_utilisateur = u.Id_utilisateur 
                JOIN offre o ON c.Id_offre = o.Id_offre 
                ORDER BY c.date_candidature DESC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Erreur récupération dernières candidatures: " . $e->getMessage());
            return [];
        }
    }
    
    private function getToutesCandidatures() {
        try {
            $stmt = $this->offreManager->getConnection()->prepare("
                SELECT c.*, u.prenom, u.nom, u.email, o.titre, o.Id_utilisateur as id_recruteur 
                FROM candidature c 
                JOIN utilisateur u ON c.Id_utilisateur = u.Id_utilisateur 
                JOIN offre o ON c.Id_offre = o.Id_offre 
                ORDER BY c.date_candidature DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Erreur récupération toutes candidatures: " . $e->getMessage());
            return [];
        }
    }
    
    
    private function getAllUtilisateurs() {
        try {
            $stmt = $this->offreManager->getConnection()->prepare("
                SELECT * FROM utilisateur ORDER BY date_inscription DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Erreur récupération utilisateurs: " . $e->getMessage());
            return [];
        }
    }
}