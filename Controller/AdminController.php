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
    
    /**
     * Vérifie si l'utilisateur est admin
     */
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
        
        return $user;
    }
    
    /**
     * Dashboard administrateur
     */
    public function dashboard() {
        $user = $this->checkAdmin();
        
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
error_log("🔄 Dashboard - Appel de getRecentCandidatures");
$dernieres_candidatures = $this->candidatureManager->getRecentCandidatures(5);
        
        // Debug final
        error_log("📊 DASHBOARD FINAL:");
        error_log("   - Offres: " . count($dernieres_offres));
        error_log("   - Candidatures: " . count($dernieres_candidatures));
        error_log("   - Total en base: " . $stats['total_candidatures']);
        
        require_once __DIR__ . '/../View/Backoffice/admin/dashboard.php';
    }
    
    /**
     * Gestion des offres
     */
    public function gestionOffres() {
        $user = $this->checkAdmin();
        
        $filters = [];
        if (isset($_GET['type']) && !empty($_GET['type'])) {
            $filters['type_offre'] = $_GET['type'];
        }
        
        $offres = $this->offreManager->getAll($filters, 50);
        
        // Messages
        $success = $_SESSION['success'] ?? '';
        $error = $_SESSION['error'] ?? '';
        unset($_SESSION['success'], $_SESSION['error']);
        
        require_once __DIR__ . '/../View/Backoffice/admin/gestion_offres.php';
    }


    /**
     * Voir une offre spécifique
     */
    public function voirOffre() {
        $user = $this->checkAdmin();
        
        $offreId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $offre = $this->offreManager->getById($offreId);
        
        if (!$offre) {
            $_SESSION['error'] = "Offre non trouvée.";
            Utils::redirect('index.php?action=admin-gestion-offres');
        }
        
        $createur = $this->utilisateurManager->getById($offre['Id_utilisateur']);
        $candidatures = $this->candidatureManager->getByOffre($offreId, $offre['Id_utilisateur']);
        
        require_once __DIR__ . '/../View/Backoffice/admin/voir_offre.php';
    }
    
    /**
     * Modifier une offre
     */
    public function modifierOffre() {
        $user = $this->checkAdmin();
        
        $offreId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $offre = $this->offreManager->getById($offreId);
        
        if (!$offre) {
            $_SESSION['error'] = "Offre non trouvée.";
            Utils::redirect('index.php?action=admin-gestion-offres');
        }
        
        $createur = $this->utilisateurManager->getById($offre['Id_utilisateur']);
        $success = false;
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateOffreData($_POST);
            
            if (empty($errors)) {
                $offreData = $this->prepareOffreData($_POST, $offre['Id_utilisateur']);
                
                if ($this->offreManager->update($offreId, $offreData)) {
                    $success = true;
                    $_SESSION['success'] = "L'offre a été modifiée avec succès !";
                    $offre = $this->offreManager->getById($offreId);
                } else {
                    $errors[] = "Une erreur est survenue lors de la modification de l'offre.";
                }
            }
        }
        
        require_once __DIR__ . '/../View/Backoffice/admin/modifier_offre.php';
    }
    
    /**
     * Supprimer une offre
     */
    public function supprimerOffre() {
        $this->checkAdmin();
        
        $offreId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $offre = $this->offreManager->getById($offreId);
        
        if (!$offre) {
            $_SESSION['error'] = "Offre non trouvée.";
            Utils::redirect('index.php?action=admin-gestion-offres');
        }
        
        if ($this->offreManager->deleteAdmin($offreId)) {
            $_SESSION['success'] = "L'offre a été supprimée avec succès.";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la suppression de l'offre.";
        }
        
        Utils::redirect('index.php?action=admin-gestion-offres');
    }


    /**
     * Voir une candidature spécifique
     */

public function voirCandidature() {
    $user = $this->checkAdmin();
    
    $candidatureId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // Récupérer la candidature
    $candidature = $this->candidatureManager->getById($candidatureId);
    
    if (!$candidature) {
        $_SESSION['error'] = "Candidature non trouvée.";
        Utils::redirect('index.php?action=admin-gestion-candidatures');
    }
    
    // Récupérer le candidat
    $candidat = $this->utilisateurManager->getById($candidature['Id_utilisateur']);
    
    // Récupérer l'offre
    $offre = $this->offreManager->getById($candidature['Id_offre']);
    
    if (!$offre) {
        $_SESSION['error'] = "Offre non trouvée.";
        Utils::redirect('index.php?action=admin-gestion-candidatures');
    }
    
    // Récupérer le recruteur
    $recruteurId = $offre['Id_utilisateur'] ?? $candidature['id_recruteur'] ?? null;
    $recruteur = null;
    
    if ($recruteurId) {
        $recruteur = $this->utilisateurManager->getById($recruteurId);
    }
    
    // Messages
    $success = $_SESSION['success'] ?? '';
    $error = $_SESSION['error'] ?? '';
    unset($_SESSION['success'], $_SESSION['error']);
    
    require_once __DIR__ . '/../View/Backoffice/admin/voir_candidature.php';
}
    /**
     * Modifier le statut d'une candidature
     */
    public function modifierStatutCandidature() {
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Méthode non autorisée.";
            Utils::redirect('index.php?action=admin-gestion-candidatures');
        }
        
        $candidatureId = isset($_POST['candidature_id']) ? (int)$_POST['candidature_id'] : 0;
        $nouveauStatut = isset($_POST['status']) ? $_POST['status'] : '';
        
        $statutsAutorises = ['en_attente', 'en_revue', 'entretien', 'retenu', 'refuse'];
        
        if (!in_array($nouveauStatut, $statutsAutorises)) {
            $_SESSION['error'] = "Statut invalide.";
            Utils::redirect('index.php?action=admin-gestion-candidatures');
        }
        
        $stmt = $this->offreManager->getConnection()->prepare("
            UPDATE candidature SET status = ? WHERE Id_candidature = ?
        ");
        
        if ($stmt->execute([$nouveauStatut, $candidatureId])) {
            $_SESSION['success'] = "Statut de la candidature mis à jour avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour du statut.";
        }
        
        Utils::redirect('index.php?action=admin-gestion-candidatures');
    }
    
    /**
     * Gestion des utilisateurs
     */
    public function gestionUtilisateurs() {
        $user = $this->checkAdmin();
        
        $utilisateurs = $this->getAllUtilisateurs();
        
        require_once __DIR__ . '/../View/Backoffice/admin/gestion_utilisateurs.php';
    }
    
    /**
     * Voir un utilisateur spécifique
     */
    public function voirUtilisateur() {
        $user = $this->checkAdmin();
        
        $utilisateurId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $utilisateur = $this->utilisateurManager->getById($utilisateurId);
        
        if (!$utilisateur) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            Utils::redirect('index.php?action=admin-gestion-utilisateurs');
        }
        
        // Récupérer les données associées
        $candidatures = $this->candidatureManager->getByUser($utilisateurId);
        $offresPostees = $this->offreManager->getByUser($utilisateurId);
        
        require_once __DIR__ . '/../View/Backoffice/admin/voir_utilisateur.php';
    }

    
    /**
     * Valide les données d'une offre
     */
    private function validateOffreData($data) {
        $errors = [];
        
        if (empty($data['titre'])) {
            $errors[] = "Le titre de l'offre est obligatoire.";
        }
        
        if (empty($data['description'])) {
            $errors[] = "La description de l'offre est obligatoire.";
        }
        
        if (empty($data['impact_sociale'])) {
            $errors[] = "L'impact social de l'offre est obligatoire.";
        }
        
        if (empty($data['date_expiration']) || !strtotime($data['date_expiration'])) {
            $errors[] = "La date d'expiration est invalide.";
        }
        
        return $errors;
    }
    /**
 * Gestion des candidatures
 */
/**
 * Récupère les statistiques des candidatures
 */
private function getCandidatureStats() {
    try {
        $stmt = $this->offreManager->getConnection()->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                SUM(CASE WHEN status = 'en_revue' THEN 1 ELSE 0 END) as en_revue,
                SUM(CASE WHEN status = 'entretien' THEN 1 ELSE 0 END) as entretien,
                SUM(CASE WHEN status = 'retenu' THEN 1 ELSE 0 END) as retenu,
                SUM(CASE WHEN status = 'refuse' THEN 1 ELSE 0 END) as refuse
            FROM candidature
        ");
        $stmt->execute();
        return $stmt->fetch();
    } catch(PDOException $e) {
        error_log("Erreur récupération stats candidatures: " . $e->getMessage());
        return ['total' => 0, 'en_attente' => 0, 'en_revue' => 0, 'entretien' => 0, 'retenu' => 0, 'refuse' => 0];
    }
}
public function gestionCandidatures() {
    $user = $this->checkAdmin();
    
    // Filtres
    $filters = [];
    if (isset($_GET['status']) && !empty($_GET['status'])) {
        $filters['status'] = $_GET['status'];
    }
    if (isset($_GET['type_offre']) && !empty($_GET['type_offre'])) {
        $filters['type_offre'] = $_GET['type_offre'];
    }
    
    // Récupérer les candidatures
    $candidatures = $this->candidatureManager->getAll($filters);
    
    // Statistiques pour les filtres
    $stats = $this->getCandidatureStats();
    
    require_once __DIR__ . '/../View/Backoffice/admin/gestion_candidatures.php';
}
    /**
     * Prépare les données d'une offre pour l'insertion
     */
    private function prepareOffreData($data, $userId) {
        $type_handicap_str = !empty($data['type_handicap']) ? implode(',', $data['type_handicap']) : 'tous';
        
        return [
            'Id_utilisateur' => $userId,
            'titre' => Utils::sanitize($data['titre'] ?? ''),
            'description' => Utils::sanitize($data['description'] ?? ''),
            'date_expiration' => $data['date_expiration'] ?? '',
            'impact_sociale' => Utils::sanitize($data['impact_sociale'] ?? ''),
            'disability_friendly' => isset($data['disability_friendly']) ? 1 : 0,
            'type_handicap' => $type_handicap_str,
            'type_offre' => $data['type_offre'] ?? 'emploi',
            'mode' => $data['mode'] ?? 'presentiel',
            'horaire' => $data['horaire'] ?? 'temps_plein',
            'lieu' => Utils::sanitize($data['lieu'] ?? '')
        ];
    }
    
    /**
     * Récupère tous les utilisateurs
     */
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
    
    // =========================================================================
    // MÉTHODES DE COMPTAGE
    // =========================================================================
    
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
    /**
 * Afficher le formulaire de modification d'une candidature
 */
public function modifierCandidature() {
    $user = $this->checkAdmin();
    
    $candidatureId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // Récupérer la candidature
    $candidature = $this->candidatureManager->getById($candidatureId);
    
    if (!$candidature) {
        $_SESSION['error'] = "Candidature non trouvée.";
        Utils::redirect('index.php?action=admin-gestion-candidatures');
    }
    
    // Récupérer le candidat
    $candidat = $this->utilisateurManager->getById($candidature['Id_utilisateur']);
    
    // Récupérer l'offre
    $offre = $this->offreManager->getById($candidature['Id_offre']);
    
    if (!$offre) {
        $_SESSION['error'] = "Offre non trouvée.";
        Utils::redirect('index.php?action=admin-gestion-candidatures');
    }
    
    // Récupérer le recruteur
    $recruteur = $this->utilisateurManager->getById($offre['Id_utilisateur']);
    
    // Messages
    $success = $_SESSION['success'] ?? '';
    $error = $_SESSION['error'] ?? '';
    unset($_SESSION['success'], $_SESSION['error']);
    
    require_once __DIR__ . '/../View/Backoffice/admin/modifier_candidature.php';
}

/**
 * Traiter la modification d'une candidature
 */
public function modifierCandidatureTraitement() {
    $this->checkAdmin();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error'] = "Méthode non autorisée.";
        Utils::redirect('index.php?action=admin-gestion-candidatures');
    }
    
    $candidatureId = isset($_POST['id_candidature']) ? (int)$_POST['id_candidature'] : 0;
    
    // DEBUG
    error_log("=== TRAITEMENT MODIFICATION ===");
    error_log("ID: " . $candidatureId);
    error_log("Status: " . ($_POST['status'] ?? 'non défini'));
    error_log("Notes: " . ($_POST['notes'] ?? 'non définies'));
    
    if (!$candidatureId) {
        $_SESSION['error'] = "Candidature non spécifiée.";
        Utils::redirect('index.php?action=admin-gestion-candidatures');
    }
    
    // Validation simple
    $statutsAutorises = ['en_attente', 'en_revue', 'entretien', 'retenu', 'refuse'];
    
    if (empty($_POST['status']) || !in_array($_POST['status'], $statutsAutorises)) {
        $_SESSION['error'] = "Le statut de la candidature est invalide.";
        Utils::redirect('index.php?action=admin-modifier-candidature&id=' . $candidatureId);
    }
    
    // Préparer les données
    $data = [
        'status' => $_POST['status'],
        'notes' => $_POST['notes'] ?? null
    ];
    
    error_log("Données préparées: " . print_r($data, true));
    
    // Mettre à jour
    if ($this->candidatureManager->update($candidatureId, $data)) {
        $_SESSION['success'] = "Candidature mise à jour avec succès.";
        error_log("✅ Mise à jour réussie");
        Utils::redirect('index.php?action=admin-voir-candidature&id=' . $candidatureId);
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour de la candidature.";
        error_log("❌ Échec de la mise à jour");
        Utils::redirect('index.php?action=admin-modifier-candidature&id=' . $candidatureId);
    }
}
/**
 * Supprimer une candidature
 */

public function supprimerCandidature() {
    $this->checkAdmin();
    
    $candidatureId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if (!$candidatureId) {
        $_SESSION['error'] = "Candidature non spécifiée.";
        Utils::redirect('index.php?action=admin-gestion-candidatures');
    }
    
    // Vérifier que la candidature existe
    $candidature = $this->candidatureManager->getById($candidatureId);
    
    if (!$candidature) {
        $_SESSION['error'] = "Candidature non trouvée.";
        Utils::redirect('index.php?action=admin-gestion-candidatures');
    }
    
    // Supprimer la candidature (version admin)
    if ($this->candidatureManager->deleteAdmin($candidatureId)) {
        $_SESSION['success'] = "Candidature supprimée avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression de la candidature.";
    }
    
    Utils::redirect('index.php?action=admin-gestion-candidatures');
}

/**
 * Valider les données d'une candidature
 */
private function validateCandidatureData($data) {
    $errors = [];
    
    $statutsAutorises = ['en_attente', 'en_revue', 'entretien', 'retenu', 'refuse'];
    
    if (empty($data['status']) || !in_array($data['status'], $statutsAutorises)) {
        $errors[] = "Le statut de la candidature est invalide.";
    }
    
    return $errors;
}
/**
 * Afficher les candidatures d'une offre spécifique
 */
public function candidaturesOffre() {
    $user = $this->checkAdmin();
    
    $offreId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $offre = $this->offreManager->getById($offreId);
    
    if (!$offre) {
        $_SESSION['error'] = "Offre non trouvée.";
        Utils::redirect('index.php?action=admin-gestion-offres');
    }
    
    // Récupérer le créateur de l'offre
    $createur = $this->utilisateurManager->getById($offre['Id_utilisateur']);
    
    // Récupérer les candidatures pour cette offre
    $candidatures = $this->candidatureManager->getByOffre($offreId, $offre['Id_utilisateur']);
    
    // Messages
    $success = $_SESSION['success'] ?? '';
    $error = $_SESSION['error'] ?? '';
    unset($_SESSION['success'], $_SESSION['error']);
    
    require_once __DIR__ . '/../View/Backoffice/admin/candidatures_offre.php';
}
}