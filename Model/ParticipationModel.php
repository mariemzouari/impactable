<?php

class ParticipationModel {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Crée une nouvelle participation dans la base de données.
     * @param array $data Données de la participation.
     * @return string|false L'ID de la dernière insertion ou false en cas d'échec.
     */
    public function create(array $data) {
        // Correction défensive: Si id_utilisateur est 0 ou null, le convertir en NULL PHP
        if (isset($data['id_utilisateur']) && ($data['id_utilisateur'] === 0 || $data['id_utilisateur'] === null)) {
            $data['id_utilisateur'] = null;
        }

        // Ensure 'statut' is set, default to 'inscrit' if not provided
        // The database column 'statut' must exist with a VARCHAR(50) type,
        // and ideally a DEFAULT 'inscrit' or NOT NULL constraint.
        if (!isset($data['statut']) || empty($data['statut'])) {
            $data['statut'] = 'inscrit';
        }

        $columns = array_keys($data);
        $placeholders = array_map(fn($c) => ":$c", $columns);
        $sql = sprintf(
            'INSERT INTO participation (%s) VALUES (%s)',
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        $stmt = $this->db->prepare($sql);
        if ($stmt->execute($data)) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Recherche une participation par des critères spécifiques.
     * @param array $criteria Tableau associatif des critères de recherche (ex: ['id_utilisateur' => 1, 'id_evenement' => 10]).
     * @return array|false La participation trouvée ou false.
     */
    public function findOneBy(array $criteria) {
        $sql = 'SELECT * FROM participation WHERE id_evenement = :id_evenement';
        $params = ['id_evenement' => $criteria['id_evenement']];

        if (isset($criteria['id_utilisateur']) && $criteria['id_utilisateur'] !== null) {
            $sql .= ' AND id_utilisateur = :id_utilisateur';
            $params['id_utilisateur'] = $criteria['id_utilisateur'];
        } else if (isset($criteria['email'])) {
            // For guest participations, user_id is NULL and email is the unique identifier for the event
            $sql .= ' AND id_utilisateur IS NULL AND email = :email';
            $params['email'] = $criteria['email'];
        }

        $sql .= ' LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère toutes les participations d'un utilisateur, avec les détails de l'événement.
     * @param int $userId L'ID de l'utilisateur.
     * @return array Tableau de participations.
     */
    public function getByUserId($userId) {
        $sql = "SELECT 
                    p.*,
                    e.titre as nom_evenement,
                    e.date_event,
                    e.date_event as date_evenement,
                    e.location as adresse
                FROM participation p
                JOIN evenements e ON p.id_evenement = e.id
                LEFT JOIN utilisateur u ON p.id_utilisateur = u.id
                WHERE p.id_utilisateur = :user_id
                ORDER BY e.date_event DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $params = ['user_id' => $userId];

            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("PDOException in getByUserId: " . $e->getMessage());
            // You might want to rethrow or handle this error more gracefully in production
            return []; // Return empty array on error
        }
    }

    /**
     * Récupère tous les participants d'un événement, avec les détails de l'utilisateur.
     * Cette méthode est utilisée par le back-office.
     * @param int $eventId L'ID de l'événement.
     * @return array Tableau des participants.
     */
    public function getByEventIdWithUserDetails($eventId) {
        $sql = "SELECT 
                    p.id, p.date_inscription, p.statut, p.nombre_accompagnants, 
                    p.besoins_accessibilite, p.message, p.id_evenement, p.id_utilisateur,
                    p.num_identite,
                    COALESCE(p.prenom, u.prenom, 'N/A') as prenom,
                    COALESCE(p.nom, u.nom, 'N/A') as nom,
                    COALESCE(p.email, u.email, 'N/A') as email,
                    COALESCE(p.num_tel, u.numero_tel, 'N/A') as num_tel
                FROM participation p
                LEFT JOIN utilisateur u ON p.id_utilisateur = u.Id_utilisateur
                WHERE p.id_evenement = :event_id
                ORDER BY p.date_inscription ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['event_id' => $eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Met à jour une participation existante.
     * @param int $id L'ID de la participation.
     * @param array $data Données à mettre à jour.
     * @return bool Vrai si la mise à jour a réussi, faux sinon.
     */
    public function update($id, $data) {
        $updates = [];
        foreach (array_keys($data) as $key) {
            $updates[] = "$key = :$key";
        }
        $sql = sprintf(
            'UPDATE participation SET %s WHERE id = :id',
            implode(', ', $updates)
        );
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        
        // Exécuter la requête ET vérifier si des lignes ont été affectées.
        if ($stmt->execute($data)) {
            return $stmt->rowCount() > 0;
        }
        return false;
    }
    
    /**
     * Supprime une participation par son ID.
     * @param int $id L'ID de la participation à supprimer.
     * @return bool Vrai si la suppression a réussi, faux sinon.
     */
    public function delete($id) {
        $stmt = $this->db->prepare('DELETE FROM participation WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Supprime plusieurs participations par leurs IDs.
     * @param array $ids Tableau d'IDs de participations à supprimer.
     * @return int Le nombre de lignes affectées.
     */
    public function deleteByIds(array $ids) {
        if (empty($ids)) return 0;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare("DELETE FROM participation WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        return $stmt->rowCount();
    }
    
    /**
     * Met à jour le statut de plusieurs participations.
     * @param array $ids Tableau d'IDs de participations à mettre à jour.
     * @param string $status Le nouveau statut.
     * @return int Le nombre de lignes affectées.
     */
    public function updateStatusForIds(array $ids, $status) {
        if (empty($ids) || empty($status)) return 0;
        
        // RE-ENABLED: The 'statut' column should now exist in your 'participation' table.
        // This query will now correctly update the status.
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare("UPDATE participation SET statut = ? WHERE id IN ($placeholders)");
        $params = array_merge([$status], $ids);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Récupère le nombre de participations pour un événement donné.
     * @param int $eventId L'ID de l'événement.
     * @return int Le nombre de participants.
     */
    public function getParticipationCountByEventId($eventId) {
        $sql = "SELECT COUNT(*) FROM participation WHERE id_evenement = :event_id"; // Corrected 'participations' to 'participation'
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['event_id' => $eventId]);
        return $stmt->fetchColumn();
    }

    /**
     * Calcule la capacité restante pour un événement.
     * @param int $eventId L'ID de l'événement.
     * @param int $eventCapacity La capacité totale de l'événement.
     * @return int La capacité restante.
     */
    public function getRemainingCapacity($eventId, $eventCapacity) {
        $currentParticipants = $this->getParticipationCountByEventId($eventId);
        return $eventCapacity - $currentParticipants;
    }
}
?>
