<?php
class ParticipationModel {
    private $conn;
    private $table = 'participation';

    public function __construct($db){
        $this->conn = $db;
    }

    public function getAll(){
        $sql = "SELECT p.*, u.nom, u.email FROM {$this->table} p LEFT JOIN utilisateur u ON p.id_utilisateur = u.id ORDER BY p.date_inscription DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id){
        $sql = "SELECT p.*, u.nom, u.email FROM {$this->table} p LEFT JOIN utilisateur u ON p.id_utilisateur = u.id WHERE p.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUserId($userId){
        $sql = "SELECT p.*, e.nom AS nom_evenement, e.date AS date_evenement, e.lieu, e.adresse, e.description, e.duree_heures, e.image, u.nom AS nom_utilisateur, u.email
                FROM {$this->table} p
                LEFT JOIN evenement e ON p.id_evenement = e.id
                LEFT JOIN utilisateur u ON p.id_utilisateur = u.id
                WHERE p.id_utilisateur = ?
                ORDER BY e.date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByEvent($eventId){
        $sql = "SELECT p.*, u.nom, u.email FROM {$this->table} p LEFT JOIN utilisateur u ON p.id_utilisateur = u.id WHERE p.id_evenement = ? ORDER BY p.date_inscription DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $data){
        if (empty($data)) {
            return false; // No data to update
        }
    
        $update_fields = [];
        $params = [];
    
        // Dynamically build the SET part of the query
        if (isset($data['statut'])) {
            $update_fields[] = 'statut = ?';
            $params[] = $data['statut'];
        }
        if (isset($data['message'])) {
            $update_fields[] = 'message = ?';
            $params[] = $data['message'];
        }
        if (isset($data['besoins_accessibilite'])) {
            $update_fields[] = 'besoins_accessibilite = ?';
            $params[] = $data['besoins_accessibilite'];
        }
        if (isset($data['nombre_accompagnants'])) {
            $update_fields[] = 'nombre_accompagnants = ?';
            $params[] = $data['nombre_accompagnants'];
        }
    
        if (empty($update_fields)) {
            return false; // Nothing to update
        }
    
        $sql = "UPDATE {$this->table} SET " . implode(', ', $update_fields) . " WHERE id = ?";
        $params[] = $id;
    
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function bulkDelete(array $ids){
        if(empty($ids)) return 0;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        return $stmt->rowCount();
    }

    public function bulkUpdateStatus(array $ids, $status){
        @error_log("bulkUpdateStatus: IDs: " . implode(',', $ids) . ", Status: " . $status, 3, __DIR__ . '/../logs/participation_debug.log');
        if(empty($ids)) return 0;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE {$this->table} SET statut = ? WHERE id IN ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        $params = array_merge([$status], $ids);
        $ok = $stmt->execute($params);
        if ($ok) {
            $rowCount = $stmt->rowCount();
            @error_log("bulkUpdateStatus SUCCESS: Query: {$sql}, Params: " . json_encode($params) . ", Rows affected: {$rowCount}", 3, __DIR__ . '/../logs/participation_debug.log');
            return $rowCount;
        } else {
            $errorInfo = $stmt->errorInfo();
            @error_log("bulkUpdateStatus FAILED: Query: {$sql}, Params: " . json_encode($params) . ", Error: " . json_encode($errorInfo), 3, __DIR__ . '/../logs/participation_debug.log');
            return 0;
        }
    }

    public function exportByEvent($eventId){
        return $this->getByEvent($eventId);
    }

    public function create($data){
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (id_evenement, id_utilisateur, statut, message, besoins_accessibilite, nombre_accompagnants, date_inscription) VALUES (?, ?, ?, ?, ?, ?, ?)");
        // date_inscription: allow passing or use current timestamp
        $date = $data['date_inscription'] ?? date('Y-m-d H:i:s');
        $ok = $stmt->execute([
            $data['id_evenement'],
            $data['id_utilisateur'],
            $data['statut'] ?? 'inscrit',
            $data['message'] ?? null,
            $data['besoins_accessibilite'] ?? 'aucun_besoin',
            $data['nombre_accompagnants'] ?? 0,
            $date
        ]);
        if($ok){
            return (int)$this->conn->lastInsertId();
        }
        return false;
    }

    public function delete($id){
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function isUserParticipating($userId, $eventId){
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM {$this->table} WHERE id_utilisateur = ? AND id_evenement = ?");
        $stmt->execute([$userId, $eventId]);
        return $stmt->fetchColumn() > 0;
    }

    public function getTable() {
        return $this->table;
    }
}
