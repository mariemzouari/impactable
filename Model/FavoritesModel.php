<?php

class FavoritesModel {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Ajoute un événement aux favoris d'un utilisateur.
     * @param int $userId L'ID de l'utilisateur.
     * @param int $eventId L'ID de l'événement.
     * @return bool Vrai si l'ajout a réussi, faux sinon.
     */
    public function addFavorite($userId, $eventId) {
        // Prevent duplicate entries
        if ($this->isFavorite($userId, $eventId)) {
            return true;
        }

        $sql = "INSERT INTO favoris (id_utilisateur, id_evenement) VALUES (:user_id, :event_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['user_id' => $userId, 'event_id' => $eventId]);
    }

    /**
     * Supprime un événement des favoris d'un utilisateur.
     * @param int $userId L'ID de l'utilisateur.
     * @param int $eventId L'ID de l'événement.
     * @return bool Vrai si la suppression a réussi, faux sinon.
     */
    public function removeFavorite($userId, $eventId) {
        $sql = "DELETE FROM favoris WHERE id_utilisateur = :user_id AND id_evenement = :event_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['user_id' => $userId, 'event_id' => $eventId]);
    }

    /**
     * Vérifie si un événement est dans les favoris d'un utilisateur.
     * @param int $userId L'ID de l'utilisateur.
     * @param int $eventId L'ID de l'événement.
     * @return bool Vrai si c'est un favori, faux sinon.
     */
    public function isFavorite($userId, $eventId) {
        $sql = "SELECT COUNT(*) FROM favoris WHERE id_utilisateur = :user_id AND id_evenement = :event_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'event_id' => $eventId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Recherche une entrée favorite par des critères spécifiques (e.g., userId et eventId).
     * @param array $criteria Tableau associatif des critères de recherche (ex: ['id_utilisateur' => 1, 'id_evenement' => 10]).
     * @return array|false L'entrée favorite trouvée ou false.
     */
    public function findOneBy(array $criteria) {
        $sql = "SELECT * FROM favoris WHERE 1=1";
        $params = [];
        foreach ($criteria as $key => $value) {
            $sql .= " AND $key = :$key";
            $params[":$key"] = $value;
        }
        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
