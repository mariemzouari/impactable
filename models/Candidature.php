<?php
class Candidature {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getCountByOffre($offreId) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM candidature WHERE Id_offre = ?");
            $stmt->execute([$offreId]);
            return $stmt->fetch()['count'];
        } catch(PDOException $e) {
            error_log("Erreur lors du comptage des candidatures: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getCandidaturesPlacees() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM candidature WHERE status IN ('retenu', 'entretien')");
            return $stmt->fetch()['count'];
        } catch(PDOException $e) {
            error_log("Erreur lors du comptage des candidatures placées: " . $e->getMessage());
            return 0;
        }
    }
    
    public function hasAlreadyApplied($userId, $offreId) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM candidature WHERE Id_utilisateur = ? AND Id_offre = ?");
            $stmt->execute([$userId, $offreId]);
            return $stmt->fetch()['count'] > 0;
        } catch(PDOException $e) {
            error_log("Erreur vérification candidature: " . $e->getMessage());
            return false;
        }
    }
    
    public function create($data) {
        try {
            $sql = "INSERT INTO candidature (Id_offre, Id_utilisateur, cv, linkedin, lettre_motivation, notes, status) 
                    VALUES (?, ?, ?, ?, ?, ?, 'en_attente')";
            $stmt = $this->db->prepare($sql);
            
            $result = $stmt->execute([
                $data['Id_offre'],
                $data['Id_utilisateur'],
                $data['cv'],
                $data['linkedin'],
                $data['lettre_motivation'],
                $data['notes']
            ]);
            
            return $result;
            
        } catch(PDOException $e) {
            error_log("Erreur lors de la création de la candidature: " . $e->getMessage());
            return false;
        }
    }
    
    public function getByUser($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, o.titre, o.type_offre, o.mode, o.lieu 
                FROM candidature c 
                JOIN offre o ON c.Id_offre = o.Id_offre 
                WHERE c.Id_utilisateur = ? 
                ORDER BY c.date_candidature DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Erreur récupération candidatures utilisateur: " . $e->getMessage());
            return [];
        }
    }
    
    public function getByOffre($offreId, $userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, u.nom, u.prenom, u.email, u.numero_tel, u.type_handicap 
                FROM candidature c 
                JOIN utilisateur u ON c.Id_utilisateur = u.Id_utilisateur 
                WHERE c.Id_offre = ? 
                AND c.Id_offre IN (SELECT Id_offre FROM offre WHERE Id_utilisateur = ?)
                ORDER BY c.date_candidature DESC
            ");
            $stmt->execute([$offreId, $userId]);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Erreur récupération candidatures offre: " . $e->getMessage());
            return [];
        }
    }
    
    public function updateStatus($candidatureId, $status, $userId) {
        try {
            $stmt = $this->db->prepare("
                UPDATE candidature 
                SET status = ? 
                WHERE Id_candidature = ? 
                AND Id_offre IN (SELECT Id_offre FROM offre WHERE Id_utilisateur = ?)
            ");
            return $stmt->execute([$status, $candidatureId, $userId]);
        } catch(PDOException $e) {
            error_log("Erreur mise à jour statut candidature: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour récupérer une candidature spécifique
    public function getById($candidatureId) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, o.titre, o.Id_utilisateur as id_recruteur 
                FROM candidature c 
                JOIN offre o ON c.Id_offre = o.Id_offre 
                WHERE c.Id_candidature = ?
            ");
            $stmt->execute([$candidatureId]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Erreur récupération candidature: " . $e->getMessage());
            return null;
        }
    }

    // Méthode pour supprimer une candidature
    public function delete($candidatureId, $userId) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM candidature 
                WHERE Id_candidature = ? 
                AND Id_utilisateur = ?
            ");
            return $stmt->execute([$candidatureId, $userId]);
        } catch(PDOException $e) {
            error_log("Erreur suppression candidature: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour vérifier si l'utilisateur est propriétaire de la candidature
    public function isOwner($candidatureId, $userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM candidature 
                WHERE Id_candidature = ? AND Id_utilisateur = ?
            ");
            $stmt->execute([$candidatureId, $userId]);
            return $stmt->fetch()['count'] > 0;
        } catch(PDOException $e) {
            error_log("Erreur vérification propriétaire candidature: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour récupérer les statistiques des candidatures d'un utilisateur
    public function getUserStats($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                    SUM(CASE WHEN status = 'en_revue' THEN 1 ELSE 0 END) as en_revue,
                    SUM(CASE WHEN status = 'entretien' THEN 1 ELSE 0 END) as entretien,
                    SUM(CASE WHEN status = 'retenu' THEN 1 ELSE 0 END) as retenu,
                    SUM(CASE WHEN status = 'refuse' THEN 1 ELSE 0 END) as refuse
                FROM candidature 
                WHERE Id_utilisateur = ?
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Erreur récupération stats candidatures: " . $e->getMessage());
            return ['total' => 0, 'en_attente' => 0, 'en_revue' => 0, 'entretien' => 0, 'retenu' => 0, 'refuse' => 0];
        }
    }
}