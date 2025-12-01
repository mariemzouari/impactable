<?php
require_once(__DIR__ . '/../CONFIGRRATION/config.php');
require_once(__DIR__ . '/../MODEL/reponce.php');

class ReponseController {

    /**
     * Ajouter une réponse à une réclamation
     */
    public function addReponse(Reponse $reponse) {
        $sql = "INSERT INTO reponse (Id_reclamation, Id_utilisateur, message, type_reponse, date_reponse) 
                VALUES (:reclamationId, :adminId, :contenu, :typeReponse, :dateReponse)";
        
        $db = config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'reclamationId' => $reponse->getReclamationId(),
                'adminId' => $reponse->getAdminId(),
                'contenu' => $reponse->getContenu(),
                'typeReponse' => 'premiere',
                'dateReponse' => $reponse->getDateReponse()->format('Y-m-d H:i:s')
            ]);
            
            return true;
        } catch (Exception $e) {
            throw new Exception('Erreur lors de l\'ajout de la réponse: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer toutes les réponses d'une réclamation
     */
    public function getReponsesByReclamation($reclamationId) {
        $sql = "SELECT r.*, u.nom, u.prenom 
                FROM reponse r
                LEFT JOIN utilisateur u ON r.Id_utilisateur = u.Id_utilisateur
                WHERE r.Id_reclamation = :reclamationId
                ORDER BY r.date_reponse DESC";
        
        $db = config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['reclamationId' => $reclamationId]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Compter les réponses d'une réclamation
     */
    public function countReponses($reclamationId) {
        $sql = "SELECT COUNT(*) as total FROM reponse WHERE Id_reclamation = :reclamationId";
        
        $db = config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['reclamationId' => $reclamationId]);
            $result = $query->fetch();
            return $result['total'];
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Récupérer une réponse par ID
     */
    public function getReponseById($id) {
        $sql = "SELECT r.*, u.nom, u.prenom 
                FROM reponse r
                LEFT JOIN utilisateur u ON r.Id_utilisateur = u.Id_utilisateur
                WHERE r.Id_reponse = :id";
        
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
     * Mettre à jour une réponse
     */
    public function updateReponse(Reponse $reponse, $id) {
        $sql = "UPDATE reponse SET 
                message = :contenu,
                dernier_update = :dernierUpdate
                WHERE Id_reponse = :id";
        
        $db = config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $id,
                'contenu' => $reponse->getContenu(),
                'dernierUpdate' => (new DateTime())->format('Y-m-d H:i:s')
            ]);
            
            return true;
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une réponse
     */
    public function deleteReponse($id) {
        $sql = "DELETE FROM reponse WHERE Id_reponse = :id";
        
        $db = config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return true;
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
}
?>
