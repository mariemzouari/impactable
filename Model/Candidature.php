<?php
class Candidature {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Récupère les dernières candidatures avec données minimales garanties pour le dashboard
     */
    public function getRecentCandidatures($limit = 5) {
        try {
            $query = "SELECT * FROM candidature ORDER BY date_candidature DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $result;
            
        } catch(PDOException $e) {
            error_log("Erreur getRecentCandidatures: " . $e->getMessage());
            return [];
        }
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
            // Vérifier d'abord que l'utilisateur est propriétaire de l'offre
            $checkStmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM offre 
                WHERE Id_offre = ? AND Id_utilisateur = ?
            ");
            $checkStmt->execute([$offreId, $userId]);
            $isOwner = $checkStmt->fetch()['count'] > 0;
            
            if (!$isOwner) {
                return [];
            }
            
            // Si propriétaire, récupérer les candidatures
            $stmt = $this->db->prepare("
                SELECT c.*, u.nom, u.prenom, u.email, u.numero_tel, u.type_handicap 
                FROM candidature c 
                JOIN utilisateur u ON c.Id_utilisateur = u.Id_utilisateur 
                WHERE c.Id_offre = ? 
                ORDER BY c.date_candidature DESC
            ");
            $stmt->execute([$offreId]);
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            error_log("Erreur récupération candidatures offre: " . $e->getMessage());
            return [];
        }
    }
    
    public function updateStatus($candidatureId, $status, $userId) {
        try {
            // Vérifier d'abord que l'utilisateur est propriétaire de l'offre
            $checkStmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM candidature c 
                JOIN offre o ON c.Id_offre = o.Id_offre 
                WHERE c.Id_candidature = ? AND o.Id_utilisateur = ?
            ");
            $checkStmt->execute([$candidatureId, $userId]);
            $canUpdate = $checkStmt->fetch()['count'] > 0;
            
            if (!$canUpdate) {
                return false;
            }
            
            // Mettre à jour le statut
            $stmt = $this->db->prepare("
                UPDATE candidature 
                SET status = ? 
                WHERE Id_candidature = ?
            ");
            return $stmt->execute([$status, $candidatureId]);
            
        } catch(PDOException $e) {
            error_log("Erreur mise à jour statut candidature: " . $e->getMessage());
            return false;
        }
    }

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

    /**
     * Supprimer une candidature (version utilisateur - avec restriction de propriétaire)
     */
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

    /**
     * Supprimer une candidature (version admin - sans restriction de propriétaire)
     */
    public function deleteAdmin($candidatureId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM candidature WHERE Id_candidature = ?");
            return $stmt->execute([$candidatureId]);
        } catch(PDOException $e) {
            error_log("Erreur suppression candidature admin: " . $e->getMessage());
            return false;
        }
    }

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

    public function getAll($filters = []) {
        try {
            $sql = "
                SELECT c.*, u.nom, u.prenom, u.email, o.titre as offre_titre, 
                       e.nom as entreprise_nom, e.prenom as entreprise_prenom
                FROM candidature c 
                JOIN utilisateur u ON c.Id_utilisateur = u.Id_utilisateur 
                JOIN offre o ON c.Id_offre = o.Id_offre 
                JOIN utilisateur e ON o.Id_utilisateur = e.Id_utilisateur 
                WHERE 1=1
            ";
            
            $params = [];
            
            if (!empty($filters['status'])) {
                $sql .= " AND c.status = ?";
                $params[] = $filters['status'];
            }
            
            if (!empty($filters['type_offre'])) {
                $sql .= " AND o.type_offre = ?";
                $params[] = $filters['type_offre'];
            }
            
            $sql .= " ORDER BY c.date_candidature DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            error_log("Erreur récupération toutes les candidatures: " . $e->getMessage());
            return [];  
        }
    }

    /**
     * Mettre à jour une candidature (version admin - sans restriction de propriétaire)
     */

public function update($id, $data) {
    try {
        error_log("=== UPDATE CANDIDATURE ===");
        error_log("ID: " . $id);
        error_log("Data: " . print_r($data, true));
        
        $sql = "UPDATE candidature SET 
                status = ?, 
                notes = ?
                WHERE Id_candidature = ?";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['status'],
            $data['notes'],
            $id
        ]);
        
        error_log("Résultat update: " . ($result ? 'SUCCÈS' : 'ÉCHEC'));
        error_log("Lignes affectées: " . $stmt->rowCount());
        
        return $result;
        
    } catch(PDOException $e) {
        error_log("Erreur mise à jour candidature: " . $e->getMessage());
        return false;
    }
}

    /**
     * Récupérer les statistiques des candidatures pour l'admin
     */
    public function getStats() {
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
            ");
            $stmt->execute();
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Erreur récupération stats candidatures: " . $e->getMessage());
            return ['total' => 0, 'en_attente' => 0, 'en_revue' => 0, 'entretien' => 0, 'retenu' => 0, 'refuse' => 0];
        }
    }

    /**
     * Vérifier si une candidature existe
     */
    public function exists($id) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM candidature WHERE Id_candidature = ?");
            $stmt->execute([$id]);
            return $stmt->fetch()['count'] > 0;
        } catch(PDOException $e) {
            error_log("Erreur vérification existence candidature: " . $e->getMessage());
            return false;
        }
    }
    
}