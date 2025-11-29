<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Campagne.php';

class CampagneController {
    
    public function listCampagnes() {
        $sql = "SELECT * FROM campagnecollecte";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function deleteCampagne($id) {
    $db = config::getConnexion();
    
    try {
        // D'abord supprimer tous les dons associés
        $sqlDeleteDons = "DELETE FROM don WHERE Id_campagne = :id";
        $reqDons = $db->prepare($sqlDeleteDons);
        $reqDons->bindValue(':id', $id);
        $reqDons->execute();
        
        // Ensuite supprimer la campagne
        $sqlDeleteCampagne = "DELETE FROM campagnecollecte WHERE Id_campagne = :id";
        $reqCampagne = $db->prepare($sqlDeleteCampagne);
        $reqCampagne->bindValue(':id', $id);
        $reqCampagne->execute();
        
        return true;
        
    } catch (Exception $e) {
        error_log('Error deleting campaign: ' . $e->getMessage());
        return false;
    }
}

    public function addCampagne(Campagne $campagne) {
        $sql = "INSERT INTO campagnecollecte 
                (Id_utilisateur, titre, categorie_impact, urgence, description, statut, image_campagne, objectif_montant, montant_actuel, date_debut, date_fin) 
                VALUES (:id_utilisateur, :titre, :categorie_impact, :urgence, :description, :statut, :image_campagne, :objectif_montant, :montant_actuel, :date_debut, :date_fin)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            
            // Convertir les dates en format string
            $dateDebut = $campagne->getDateDebut();
            $dateFin = $campagne->getDateFin();
            
            $date_debut_str = $dateDebut ? $dateDebut->format('Y-m-d') : null;
            $date_fin_str = $dateFin ? $dateFin->format('Y-m-d') : null;
            
            $query->execute([
                'id_utilisateur' => $campagne->getIdUtilisateur(),
                'titre' => $campagne->getTitre(),
                'categorie_impact' => $campagne->getCategorieImpact(),
                'urgence' => $campagne->getUrgence(),
                'description' => $campagne->getDescription(),
                'statut' => $campagne->getStatut(),
                'image_campagne' => $campagne->getImageCampagne(),
                'objectif_montant' => $campagne->getObjectifMontant(),
                'montant_actuel' => $campagne->getMontantActuel(),
                'date_debut' => $date_debut_str,
                'date_fin' => $date_fin_str
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    public function updateCampagne(Campagne $campagne, $id) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE campagnecollecte SET 
                    titre = :titre,
                    categorie_impact = :categorie_impact,
                    urgence = :urgence,
                    description = :description,
                    statut = :statut,
                    objectif_montant = :objectif_montant,
                    date_debut = :date_debut,
                    date_fin = :date_fin
                WHERE Id_campagne = :id'
            );
            
            // Convertir les dates en format string
            $dateDebut = $campagne->getDateDebut();
            $dateFin = $campagne->getDateFin();
            
            $date_debut_str = $dateDebut ? $dateDebut->format('Y-m-d') : null;
            $date_fin_str = $dateFin ? $dateFin->format('Y-m-d') : null;
            
            $query->execute([
                'id' => $id,
                'titre' => $campagne->getTitre(),
                'categorie_impact' => $campagne->getCategorieImpact(),
                'urgence' => $campagne->getUrgence(),
                'description' => $campagne->getDescription(),
                'statut' => $campagne->getStatut(),
                'objectif_montant' => $campagne->getObjectifMontant(),
                'date_debut' => $date_debut_str,
                'date_fin' => $date_fin_str
            ]);
            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function showCampagne($id) {
        $sql = "SELECT * FROM campagnecollecte WHERE Id_campagne = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id', $id);

        try {
            $query->execute();
            $campagne = $query->fetch(PDO::FETCH_ASSOC);
            return $campagne;
        } catch (Exception $e) {
            die('Error: '. $e->getMessage());
        }
    }

    // Check if user exists
    public function userExists($id_utilisateur) {
        try {
            $sql = "SELECT COUNT(*) FROM utilisateur WHERE Id_utilisateur = :id";
            $db = config::getConnexion();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $id_utilisateur);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            return $count > 0;
        } catch (PDOException $e) {
            error_log("Error checking user: " . $e->getMessage());
            return false;
        }
    }

    // Get campaign by ID (alias for showCampagne)
    public function getCampagne($id) {
        return $this->showCampagne($id);
    }

    // Get all campaigns (alias for listCampagnes)
    public function getAllCampagnes() {
        return $this->listCampagnes();
    }
}
?>