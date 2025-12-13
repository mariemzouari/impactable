<?php
class EventModel {
    private $db;
    protected $table = 'evenements'; // Define the table property

    /**
     * Constructor to inject the database connection.
     * This method is essential for initializing $this->db.
     * @param PDO $db The PDO database connection object.
     */
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Récupère tous les événements avec option de tri.
     * @param string $sortBy Critère de tri (ex: 'date_asc', 'title_desc').
     * @return array Tableau de tous les événements.
     */
    public function getAll($sortBy = 'date_asc') {
        $orderClause = $this->getOrderByClause($sortBy);
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " " . $orderClause);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recherche les événements par titre avec option de tri.
     * @param string $title Le titre à rechercher.
     * @param string $sortBy Critère de tri.
     * @return array Tableau des événements correspondants.
     */
    public function searchByTitle($title, $sortBy = 'date_asc') {
        $orderClause = $this->getOrderByClause($sortBy);
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE titre LIKE :title " . $orderClause);
        $stmt->execute(['title' => '%' . $title . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Construit la clause ORDER BY basée sur le critère de tri.
     * @param string $sortBy Critère de tri.
     * @return string Clause ORDER BY.
     */
    private function getOrderByClause($sortBy) {
        switch ($sortBy) {
            case 'date_asc':
                return "ORDER BY date_debut ASC";
            case 'date_desc':
                return "ORDER BY date_debut DESC";
            case 'title_asc':
                return "ORDER BY titre ASC";
            case 'title_desc':
                return "ORDER BY titre DESC";
            default:
                return "ORDER BY date_debut ASC";
        }
    }

    public function getById($id){
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data){
        $stmt = $this->db->prepare("INSERT INTO " . $this->table . " (titre, date_debut, date_fin, categorie, description, capacite_max, location) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$data['titre'], $data['date_debut'], $data['date_fin'], $data['categorie'], $data['description'], $data['capacite_max'], $data['location']]);
    }

    public function update($id, $data){
        $updates = [];
        foreach ($data as $key => $value) {
            $updates[] = "$key = :$key";
        }
        $sql = sprintf(
            'UPDATE %s SET %s WHERE id = :id',
            $this->table,
            implode(', ', $updates)
        );
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id; // Add id to data for named parameters
        
        if ($stmt->execute($data)) {
            return $stmt->rowCount() > 0; // Return true only if a row was affected
        }
        return false;
    }

    public function delete($id){
        try {
            // Use transaction: first remove dependent participations, then the event
            $this->db->beginTransaction();

            $stmt1 = $this->db->prepare("DELETE FROM participation WHERE id_evenement = ?");
            $stmt1->execute([$id]);

            $stmt2 = $this->db->prepare("DELETE FROM " . $this->table . " WHERE id = ?");
            $stmt2->execute([$id]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            if($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            // Log or rethrow as needed; return false to indicate failure
            return false;
        }
    }

    // Statistics methods
    public function countAll(){
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM " . $this->table);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result['total']);
    }

    public function countParticipations(){
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM participation");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result['total']);
    }

    public function getParticipationsByEvent(){
        $stmt = $this->db->prepare("SELECT e.id as id_evenement, e.titre, COALESCE(COUNT(p.id), 0) as count FROM " . $this->table . " e LEFT JOIN participation p ON e.id = p.id_evenement GROUP BY e.id, e.titre ORDER BY count DESC");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as &$row) {
            $row['count'] = (int)$row['count'];
        }
        return $results;
    }

    public function getUpcomingEvents(){
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE date_debut >= NOW() ORDER BY date_debut ASC LIMIT 5");
        $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // New: Count events with no participations
    public function countEventsWithNoParticipations(){
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM " . $this->table . " e LEFT JOIN participation p ON e.id = p.id_evenement WHERE p.id IS NULL");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result['total']);
    }

    // New: Calculate average participations per event
    public function getAverageParticipationsPerEvent(){
        $stmt = $this->db->prepare("SELECT AVG(participation_count) as average FROM (SELECT COUNT(p.id) as participation_count FROM " . $this->table . " e LEFT JOIN participation p ON e.id = p.id_evenement GROUP BY e.id) as subquery");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return round(floatval($result['average']), 2);
    }

    /**
     * Récupère le nombre total d'événements.
     * @return int Le nombre total d'événements.
     */
    public function getTotalEventsCount() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM " . $this->table);
        return $stmt->fetchColumn();
    }

    /**
     * Récupère le nombre d'événements sans participants.
     * @return int Le nombre d'événements sans participants.
     */
    public function getEventsWithoutParticipantsCount() {
        $sql = "SELECT COUNT(e.id) 
                FROM " . $this->table . " e
                LEFT JOIN participation p ON e.id = p.id_evenement
                WHERE p.id IS NULL";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }

    public function getEventsCountByCategory() {
        $sql = "SELECT categorie, COUNT(*) as count FROM evenements GROUP BY categorie";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // New: Count finished events
    public function countFinishedEvents(){
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM " . $this->table . " WHERE date_fin < NOW()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result['total']);
    }
}