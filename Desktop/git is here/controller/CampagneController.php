<?php
class CampagneController {
    private $conn;
    
    public function __construct() {
        include_once __DIR__ . '/../config.php';
        $this->conn = $conn;
    }
    
    public function getAllCampagnes() {
        try {
            $sql = "SELECT c.*, u.nom, u.prenom 
                    FROM campagnecollecte c 
                    LEFT JOIN utilisateur u ON c.Id_utilisateur = u.Id_utilisateur 
                    ORDER BY c.Id_campagne DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt;
        } catch(PDOException $e) {
            error_log("Erreur getAllCampagnes: " . $e->getMessage());
            return false;
        }
    }
    
    public function showCampagne($id) {
        try {
            $sql = "SELECT c.*, u.nom, u.prenom 
                    FROM campagnecollecte c 
                    LEFT JOIN utilisateur u ON c.Id_utilisateur = u.Id_utilisateur 
                    WHERE c.Id_campagne = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erreur showCampagne: " . $e->getMessage());
            return false;
        }
    }
    
    public function addCampagne($campagne) {
        try {
            $sql = "INSERT INTO campagnecollecte (Id_utilisateur, titre, categorie_impact, urgence, description, statut, objectif_montant, montant_actuel, date_debut, date_fin) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                $campagne->getIdUtilisateur(),
                $campagne->getTitre(),
                $campagne->getCategorieImpact(),
                $campagne->getUrgence(),
                $campagne->getDescription(),
                $campagne->getStatut(),
                $campagne->getObjectifMontant(),
                $campagne->getMontantActuel(),
                $campagne->getDateDebut(),
                $campagne->getDateFin()
            ]);
        } catch(PDOException $e) {
            error_log("Erreur addCampagne: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateCampagne($campagne, $id) {
        try {
            $sql = "UPDATE campagnecollecte SET titre=?, categorie_impact=?, urgence=?, description=?, objectif_montant=?, date_debut=?, date_fin=?, statut=? WHERE Id_campagne=?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                $campagne->getTitre(),
                $campagne->getCategorieImpact(),
                $campagne->getUrgence(),
                $campagne->getDescription(),
                $campagne->getObjectifMontant(),
                $campagne->getDateDebut(),
                $campagne->getDateFin(),
                $campagne->getStatut(),
                $id
            ]);
        } catch(PDOException $e) {
            error_log("Erreur updateCampagne: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteCampagne($id) {
        try {
            $sql = "DELETE FROM campagnecollecte WHERE Id_campagne = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            error_log("Erreur deleteCampagne: " . $e->getMessage());
            return false;
        }
    }
}
?>