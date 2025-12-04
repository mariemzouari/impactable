<?php
// FrontCampagneController.php - Version CORRIGÉE
include_once __DIR__ . '/../config.php';

class FrontCampagneController {
    private $db;

    public function __construct() {
        $this->db = config::getConnexion();
    }

    public function listCampagnesActives() {
        try {
            // D'ABORD, on actualise tous les montants
            $this->actualiserTousLesMontants();
            
            // PUIS on récupère les campagnes
            $query = "SELECT * FROM campagnecollecte ORDER BY 
                     CASE 
                         WHEN statut = 'terminée' OR date_fin < CURDATE() THEN 2
                         ELSE 1 
                     END,
                     urgence DESC, 
                     date_debut DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur listCampagnesActives: " . $e->getMessage());
            return [];
        }
    }

    public function getCampagne($id_campagne) {
        try {
            // ACTUALISER le montant avant de récupérer
            $this->actualiserMontantCampagne($id_campagne);
            
            $query = "SELECT * FROM campagnecollecte WHERE Id_campagne = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id_campagne]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getCampagne: " . $e->getMessage());
            return null;
        }
    }

    public function getProgression($id_campagne) {
        try {
            $campagne = $this->getCampagne($id_campagne);
            if ($campagne && $campagne['objectif_montant'] > 0) {
                $progression = ($campagne['montant_actuel'] / $campagne['objectif_montant']) * 100;
                return min($progression, 100); // Maximum 100%
            }
            return 0;
        } catch (PDOException $e) {
            error_log("Erreur getProgression: " . $e->getMessage());
            return 0;
        }
    }

    
    public function actualiserMontantCampagne($id_campagne) {
        try {
            // Calculer la somme des dons CONFIRMÉS pour cette campagne
            $query = "SELECT COALESCE(SUM(montant), 0) as total_dons 
                     FROM don 
                     WHERE id_campagne = ? AND statut = 'confirmé'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id_campagne]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_dons = $result['total_dons'];
            
            // DEBUG: Log pour voir ce qui se passe
            error_log("DEBUG - Campagne $id_campagne: Total dons = $total_dons");
            
            // Mettre à jour la campagne
            $update_query = "UPDATE campagnecollecte SET montant_actuel = ? WHERE Id_campagne = ?";
            $update_stmt = $this->db->prepare($update_query);
            $success = $update_stmt->execute([$total_dons, $id_campagne]);
            
            if ($success) {
                error_log("✅ SUCCÈS - Campagne $id_campagne actualisée: $total_dons TND");
            } else {
                error_log("❌ ÉCHEC - Campagne $id_campagne non actualisée");
            }
            
            return $total_dons;
            
        } catch (PDOException $e) {
            error_log("❌ ERREUR actualiserMontantCampagne: " . $e->getMessage());
            return false;
        }
    }

    // NOUVELLE MÉTHODE : Actualiser tous les montants
    private function actualiserTousLesMontants() {
        try {
            // Récupérer toutes les campagnes
            $query = "SELECT Id_campagne FROM campagnecollecte";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $campagnes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($campagnes as $campagne) {
                $this->actualiserMontantCampagne($campagne['Id_campagne']);
            }
            
            error_log("✅ Tous les montants actualisés");
            
        } catch (PDOException $e) {
            error_log("❌ Erreur actualiserTousLesMontants: " . $e->getMessage());
        }
    }
    // FrontCampagneController.php - Ajouter cette méthode


    public function getCampagnesAvecProblemes() {
    try {
        // D'abord actualiser tous les montants
        $this->actualiserTousLesMontants();
        
        $query = "SELECT * FROM campagnecollecte 
                 WHERE date_fin < CURDATE() 
                 AND montant_actuel < objectif_montant
                 AND statut NOT IN ('terminée', 'objectif_atteint')
                 ORDER BY date_fin ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur getCampagnesAvecProblemes: " . $e->getMessage());
        return [];
    }
}

// Méthode pour compter les campagnes problématiques

    public function countCampagnesAvecProblemes() {
    try {
        $query = "SELECT COUNT(*) as count FROM campagnecollecte 
                 WHERE date_fin < CURDATE() 
                 AND montant_actuel < objectif_montant
                 AND statut NOT IN ('terminée', 'objectif_atteint')";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    } catch (PDOException $e) {
        error_log("Erreur countCampagnesAvecProblemes: " . $e->getMessage());
        return 0;
    }
}

    // ✅ NOUVELLE MÉTHODE POUR JOINTURE
    public function getDonsParCampagne($id_campagne) {
        try {
            // JOINTURE : Don + Utilisateur pour une campagne
            $query = "SELECT 
                        d.*,
                        u.nom as donateur_nom,
                        u.email as donateur_email
                     FROM don d
                     JOIN utilisateur u ON d.id_utilisateur = u.Id_utilisateur
                     WHERE d.id_campagne = ?
                     ORDER BY d.date_don DESC
                     LIMIT 10"; // Limite à 10 derniers dons
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id_campagne]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erreur getDonsParCampagne: " . $e->getMessage());
            return [];
        }
    }
}
?>