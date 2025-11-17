<?php
/**
 * Modèle pour la gestion des réclamations
 * Architecture MVC - Couche Model
 */

class ReclamationModel {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Générer un ID unique pour une réclamation
     */
    private function generateId() {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $id = 'REC-';
        for ($i = 0; $i < 3; $i++) {
            $id .= $chars[rand(0, strlen($chars) - 1)];
        }
        for ($i = 0; $i < 4; $i++) {
            $id .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $id;
    }
    
    /**
     * Créer une nouvelle réclamation
     */
    public function create($data) {
        try {
            $id = $this->generateId();
            $dateCreation = date('Y-m-d H:i:s');
            
            $sql = "INSERT INTO reclamations (
                id, nom, prenom, email, telephone, cin, dateNaissance, adresse,
                typeHandicap, descriptionHandicap, categorie, lieu, dateIncident,
                sujet, description, personnesImpliquees, temoins, actionsPrecedentes,
                reponseRecue, solutionSouhaitee, priorite, status, dateCreation
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Erreur de préparation: " . $this->conn->error);
            }
            
            $status = 'en_attente'; // Statut par défaut
            
            $stmt->bind_param(
                "sssssssssssssssssssssss",
                $id,
                $data['nom'],
                $data['prenom'],
                $data['email'],
                $data['telephone'],
                $data['cin'],
                $data['dateNaissance'],
                $data['adresse'],
                $data['typeHandicap'],
                $data['descriptionHandicap'],
                $data['categorie'],
                $data['lieu'],
                $data['dateIncident'],
                $data['sujet'],
                $data['description'],
                $data['personnesImpliquees'],
                $data['temoins'],
                $data['actionsPrecedentes'],
                $data['reponseRecue'],
                $data['solutionSouhaitee'],
                $data['priorite'],
                $status,
                $dateCreation
            );
            
            if ($stmt->execute()) {
                return [
                    "success" => true,
                    "id" => $id,
                    "message" => "Réclamation créée avec succès"
                ];
            } else {
                throw new Exception("Erreur d'exécution: " . $stmt->error);
            }
            
        } catch (Exception $e) {
            return [
                "success" => false,
                "error" => $e->getMessage()
            ];
        }
    }
    
    /**
     * Lire toutes les réclamations
     */
    public function getAll() {
        try {
            $sql = "SELECT * FROM reclamations ORDER BY dateCreation DESC";
            $result = $this->conn->query($sql);
            
            if (!$result) {
                throw new Exception("Erreur de requête: " . $this->conn->error);
            }
            
            $reclamations = [];
            while ($row = $result->fetch_assoc()) {
                $reclamations[] = $row;
            }
            
            return [
                "success" => true,
                "data" => $reclamations
            ];
            
        } catch (Exception $e) {
            return [
                "success" => false,
                "error" => $e->getMessage()
            ];
        }
    }
    
    /**
     * Lire une réclamation par ID
     */
    public function getById($id) {
        try {
            $sql = "SELECT * FROM reclamations WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Erreur de préparation: " . $this->conn->error);
            }
            
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                return [
                    "success" => true,
                    "data" => $row
                ];
            } else {
                return [
                    "success" => false,
                    "error" => "Réclamation non trouvée"
                ];
            }
            
        } catch (Exception $e) {
            return [
                "success" => false,
                "error" => $e->getMessage()
            ];
        }
    }
    
    /**
     * Mettre à jour une réclamation
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE reclamations SET status = ?, priorite = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Erreur de préparation: " . $this->conn->error);
            }
            
            $stmt->bind_param("sss", $data['status'], $data['priorite'], $id);
            
            if ($stmt->execute()) {
                return [
                    "success" => true,
                    "message" => "Réclamation mise à jour avec succès"
                ];
            } else {
                throw new Exception("Erreur d'exécution: " . $stmt->error);
            }
            
        } catch (Exception $e) {
            return [
                "success" => false,
                "error" => $e->getMessage()
            ];
        }
    }
    
    /**
     * Supprimer une réclamation
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM reclamations WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Erreur de préparation: " . $this->conn->error);
            }
            
            $stmt->bind_param("s", $id);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    return [
                        "success" => true,
                        "message" => "Réclamation supprimée avec succès"
                    ];
                } else {
                    return [
                        "success" => false,
                        "error" => "Réclamation non trouvée"
                    ];
                }
            } else {
                throw new Exception("Erreur d'exécution: " . $stmt->error);
            }
            
        } catch (Exception $e) {
            return [
                "success" => false,
                "error" => $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtenir les statistiques
     */
    public function getStatistics() {
        try {
            $stats = [
                'enAttente' => 0,
                'enCours' => 0,
                'resolues' => 0,
                'urgentes' => 0,
                'total' => 0
            ];
            
            // Total
            $result = $this->conn->query("SELECT COUNT(*) as count FROM reclamations");
            $stats['total'] = $result->fetch_assoc()['count'];
            
            // En attente
            $result = $this->conn->query("SELECT COUNT(*) as count FROM reclamations WHERE status = 'en_attente'");
            $stats['enAttente'] = $result->fetch_assoc()['count'];
            
            // En cours
            $result = $this->conn->query("SELECT COUNT(*) as count FROM reclamations WHERE status = 'en_cours'");
            $stats['enCours'] = $result->fetch_assoc()['count'];
            
            // Résolues
            $result = $this->conn->query("SELECT COUNT(*) as count FROM reclamations WHERE status = 'resolu'");
            $stats['resolues'] = $result->fetch_assoc()['count'];
            
            // Urgentes
            $result = $this->conn->query("SELECT COUNT(*) as count FROM reclamations WHERE priorite = 'urgente'");
            $stats['urgentes'] = $result->fetch_assoc()['count'];
            
            return [
                "success" => true,
                "data" => $stats
            ];
            
        } catch (Exception $e) {
            return [
                "success" => false,
                "error" => $e->getMessage()
            ];
        }
    }
    
    /**
     * Rechercher des réclamations par email
     */
    public function getByEmail($email) {
        try {
            $sql = "SELECT * FROM reclamations WHERE email = ? ORDER BY dateCreation DESC";
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Erreur de préparation: " . $this->conn->error);
            }
            
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $reclamations = [];
            while ($row = $result->fetch_assoc()) {
                $reclamations[] = $row;
            }
            
            return [
                "success" => true,
                "data" => $reclamations
            ];
            
        } catch (Exception $e) {
            return [
                "success" => false,
                "error" => $e->getMessage()
            ];
        }
    }
}
?>