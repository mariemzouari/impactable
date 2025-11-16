<?php
class Offre {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($filters = [], $limit = 6) {
        $sql = "SELECT * FROM offre WHERE 1=1";
        $params = [];
        
        if (!empty($filters)) {
            if (!empty($filters['type_offre'])) {
                $sql .= " AND type_offre = ?";
                $params[] = $filters['type_offre'];
            }
            
            if (!empty($filters['mode'])) {
                $sql .= " AND mode = ?";
                $params[] = $filters['mode'];
            }
            
            if (!empty($filters['horaire'])) {
                $sql .= " AND horaire = ?";
                $params[] = $filters['horaire'];
            }
            
            if (isset($filters['disability_friendly']) && $filters['disability_friendly'] == '1') {
                $sql .= " AND disability_friendly = 1";
            }
            
            if (!empty($filters['type_handicap'])) {
                $sql .= " AND (type_handicap LIKE ? OR type_handicap = 'tous')";
                $params[] = '%' . $filters['type_handicap'] . '%';
            }
        }
        
        $sql .= " ORDER BY date_publication DESC LIMIT " . (int)$limit;
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Erreur lors de la récupération des offres: " . $e->getMessage());
            return [];
        }
    }
    
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM offre WHERE Id_offre = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Erreur lors de la récupération de l'offre: " . $e->getMessage());
            return null;
        }
    }
    
    public function getStats() {
        try {
            $stats = [];
            
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM offre");
            $stats['total_offres'] = $stmt->fetch()['total'];
            
            $stmt = $this->db->query("SELECT COUNT(*) as accessible FROM offre WHERE disability_friendly = 1");
            $stats['offres_accessibles'] = $stmt->fetch()['accessible'];
            
            $stmt = $this->db->query("SELECT COUNT(DISTINCT Id_utilisateur) as entreprises FROM offre");
            $stats['entreprises'] = $stmt->fetch()['entreprises'];
            
            return $stats;
        } catch(PDOException $e) {
            error_log("Erreur lors de la récupération des statistiques: " . $e->getMessage());
            return ['total_offres' => 0, 'offres_accessibles' => 0, 'entreprises' => 0];
        }
    }
    
    public function create($data) {
        try {
            $sql = "INSERT INTO offre (Id_utilisateur, titre, description, date_expiration, impact_sociale, disability_friendly, type_handicap, type_offre, mode, horaire, lieu) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                $data['Id_utilisateur'],
                $data['titre'],
                $data['description'],
                $data['date_expiration'],
                $data['impact_sociale'],
                $data['disability_friendly'],
                $data['type_handicap'],
                $data['type_offre'],
                $data['mode'],
                $data['horaire'],
                $data['lieu']
            ]);
            
        } catch(PDOException $e) {
            error_log("Erreur lors de la création de l'offre: " . $e->getMessage());
            return false;
        }
    }
    
    public function getByUser($userId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM offre WHERE Id_utilisateur = ? ORDER BY date_publication DESC");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Erreur récupération offres utilisateur: " . $e->getMessage());
            return [];
        }
    }
    
    public function update($offreId, $data) {
        try {
            $sql = "UPDATE offre SET titre = ?, description = ?, date_expiration = ?, impact_sociale = ?, 
                    disability_friendly = ?, type_handicap = ?, type_offre = ?, mode = ?, horaire = ?, lieu = ?,
                    date_modification = CURRENT_TIMESTAMP 
                    WHERE Id_offre = ? AND Id_utilisateur = ?";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                $data['titre'],
                $data['description'],
                $data['date_expiration'],
                $data['impact_sociale'],
                $data['disability_friendly'],
                $data['type_handicap'],
                $data['type_offre'],
                $data['mode'],
                $data['horaire'],
                $data['lieu'],
                $offreId,
                $data['Id_utilisateur']
            ]);
            
        } catch(PDOException $e) {
            error_log("Erreur mise à jour offre: " . $e->getMessage());
            return false;
        }
    }
    
    public function delete($offreId, $userId) {
        try {
            // D'abord supprimer les candidatures associées
            $stmt = $this->db->prepare("DELETE FROM candidature WHERE Id_offre = ?");
            $stmt->execute([$offreId]);
            
            // Puis supprimer l'offre
            $stmt = $this->db->prepare("DELETE FROM offre WHERE Id_offre = ? AND Id_utilisateur = ?");
            return $stmt->execute([$offreId, $userId]);
            
        } catch(PDOException $e) {
            error_log("Erreur suppression offre: " . $e->getMessage());
            return false;
        }
    }
    
    public function isOwner($offreId, $userId) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM offre WHERE Id_offre = ? AND Id_utilisateur = ?");
            $stmt->execute([$offreId, $userId]);
            return $stmt->fetch()['count'] > 0;
        } catch(PDOException $e) {
            error_log("Erreur vérification propriétaire: " . $e->getMessage());
            return false;
        }
    }
    public function deleteAdmin($offreId) {
    try {
        // D'abord supprimer les candidatures associées
        $stmt = $this->db->prepare("DELETE FROM candidature WHERE Id_offre = ?");
        $stmt->execute([$offreId]);
        
        // Puis supprimer l'offre (sans vérification de propriétaire pour l'admin)
        $stmt = $this->db->prepare("DELETE FROM offre WHERE Id_offre = ?");
        return $stmt->execute([$offreId]);
        
    } catch(PDOException $e) {
        error_log("Erreur suppression offre admin: " . $e->getMessage());
        return false;
    }
}

// Ajoutez aussi cette méthode pour récupérer la connexion
public function getConnection() {
    return $this->db;
}
}