<?php
class EventModel {
    private $conn;
    private $table = "evenements";
    public function __construct($db){
        $this->conn = $db;
    }

    public function getAll(){
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} ORDER BY date_event ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id){
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data){
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (titre, date_event, categorie, description) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['titre'], $data['date_event'], $data['categorie'], $data['description']]);
    }

    public function update($id, $data){
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET titre = ?, date_event = ?, categorie = ?, description = ? WHERE id = ?");
        return $stmt->execute([$data['titre'], $data['date_event'], $data['categorie'], $data['description'], $id]);
    }

    public function delete($id){
        try {
            // Use transaction: first remove dependent participations, then the event
            $this->conn->beginTransaction();

            $stmt1 = $this->conn->prepare("DELETE FROM participation WHERE id_evenement = ?");
            $stmt1->execute([$id]);

            $stmt2 = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
            $stmt2->execute([$id]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            if($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            // Log or rethrow as needed; return false to indicate failure
            return false;
        }
    }

    // Statistics methods
    public function countAll(){
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM {$this->table}");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result['total']);
    }

    public function countParticipations(){
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM participation");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result['total']);
    }

    public function getParticipationsByEvent(){
        $stmt = $this->conn->prepare("SELECT id_evenement, titre, COUNT(p.id) as count FROM {$this->table} e LEFT JOIN participation p ON e.id = p.id_evenement GROUP BY e.id ORDER BY count DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUpcomingEvents(){
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE date_event >= NOW() ORDER BY date_event ASC LIMIT 5");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
