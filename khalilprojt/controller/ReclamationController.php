<?php
require_once(__DIR__ . '/../CONFIGRRATION/config.php');
require_once(__DIR__ . '/../MODEL/Reclamation.php');

class ReclamationController {

    /**
     * Ajouter une réclamation
     */
    public function addReclamation(Reclamation $reclamation) {
        $sql = "INSERT INTO reclamation (sujet, description, categorie, priorite, statut, dateCreation, derniereModification, utilisateurId, agentAttribue, image, nom, prenom, email, telephone, lieu, dateIncident, typeHandicap, personnesImpliquees, temoins, actionsPrecedentes, solutionSouhaitee) 
                VALUES (:sujet, :description, :categorie, :priorite, :statut, :dateCreation, :derniereModification, :utilisateurId, :agentAttribue, :image, :nom, :prenom, :email, :telephone, :lieu, :dateIncident, :typeHandicap, :personnesImpliquees, :temoins, :actionsPrecedentes, :solutionSouhaitee)";
        
        $db = config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'sujet' => $reclamation->getSujet(),
                'description' => $reclamation->getDescription(),
                'categorie' => $reclamation->getCategorie(),
                'priorite' => $reclamation->getPriorite(),
                'statut' => $reclamation->getStatut(),
                'dateCreation' => $reclamation->getDateCreation()->format('Y-m-d H:i:s'),
                'derniereModification' => $reclamation->getDerniereModification()->format('Y-m-d H:i:s'),
                'utilisateurId' => $reclamation->getUtilisateurId(),
                'agentAttribue' => $reclamation->getAgentAttribue(),
                'image' => null,
                'nom' => null,
                'prenom' => null,
                'email' => null,
                'telephone' => null,
                'lieu' => null,
                'dateIncident' => null,
                'typeHandicap' => null,
                'personnesImpliquees' => null,
                'temoins' => null,
                'actionsPrecedentes' => null,
                'solutionSouhaitee' => null
            ]);
            
            return true;
        } catch (Exception $e) {
            throw new Exception('Erreur lors de l\'ajout de la réclamation: ' . $e->getMessage());
        }
    }

    /**
     * Lister toutes les réclamations
     */
    public function listReclamations() {
        $sql = "SELECT * FROM reclamation ORDER BY dateCreation DESC";
        
        $db = config::getConnexion();
        
        try {
            $query = $db->query($sql);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Afficher une réclamation par ID
     */
    public function showReclamationById($id) {
        $sql = "SELECT * FROM reclamation WHERE id = :id";
        
        $db = config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Mettre à jour une réclamation
     */
    public function updateReclamation(Reclamation $reclamation, $id) {
        $sql = "UPDATE reclamation SET 
                sujet = :sujet,
                description = :description,
                categorie = :categorie,
                priorite = :priorite,
                statut = :statut,
                derniereModification = :derniereModification,
                utilisateurId = :utilisateurId,
                agentAttribue = :agentAttribue
                WHERE id = :id";
        
        $db = config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $id,
                'sujet' => $reclamation->getSujet(),
                'description' => $reclamation->getDescription(),
                'categorie' => $reclamation->getCategorie(),
                'priorite' => $reclamation->getPriorite(),
                'statut' => $reclamation->getStatut(),
                'derniereModification' => (new DateTime())->format('Y-m-d H:i:s'),
                'utilisateurId' => $reclamation->getUtilisateurId(),
                'agentAttribue' => $reclamation->getAgentAttribue()
            ]);
            
            return true;
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une réclamation
     */
    public function deleteReclamation($id) {
        $sql = "DELETE FROM reclamation WHERE id = :id";
        
        $db = config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return true;
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Obtenir les statistiques
     */
    public function getStats() {
        $sql = "SELECT 
                COUNT(CASE WHEN statut = 'En attente' THEN 1 END) as en_attente,
                COUNT(CASE WHEN statut = 'En cours' THEN 1 END) as en_cours,
                COUNT(CASE WHEN statut = 'Résolue' THEN 1 END) as resolues,
                COUNT(CASE WHEN priorite = 'Urgente' THEN 1 END) as urgentes,
                COUNT(*) as total
                FROM reclamation";
        
        $db = config::getConnexion();
        
        try {
            $query = $db->query($sql);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [
                'en_attente' => 0,
                'en_cours' => 0,
                'resolues' => 0,
                'urgentes' => 0,
                'total' => 0
            ];
        }
    }
}
?>