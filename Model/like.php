<?php
require_once '../config.php';

class Like {
    public $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
        
        // Créer la table si elle n'existe pas
        $this->createLikesTable();
    }

    /**
     * Ajouter un like à un post
     */
    public function addLike($post_id, $user_id) {
        try {
            // Vérifier si l'utilisateur a déjà liké ce post
            if ($this->hasUserLiked($post_id, $user_id)) {
                return ['success' => false, 'message' => 'Vous avez déjà liké ce post'];
            }

            $sql = "INSERT INTO likes (Id_post, Id_utilisateur, date_creation) VALUES (?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            
            if ($stmt->execute([$post_id, $user_id])) {
                // Mettre à jour le compteur dans la table post
                $this->updateLikesCount($post_id);
                return ['success' => true, 'message' => 'Like ajouté'];
            }
            
            return ['success' => false, 'message' => 'Erreur lors de l\'ajout du like'];
        } catch (PDOException $e) {
            error_log("Erreur addLike: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de l\'ajout du like'];
        }
    }

    /**
     * Retirer un like d'un post
     */
    public function removeLike($post_id, $user_id) {
        try {
            $sql = "DELETE FROM likes WHERE Id_post = ? AND Id_utilisateur = ?";
            $stmt = $this->pdo->prepare($sql);
            
            if ($stmt->execute([$post_id, $user_id])) {
                // Mettre à jour le compteur dans la table post
                $this->updateLikesCount($post_id);
                return ['success' => true, 'message' => 'Like retiré'];
            }
            
            return ['success' => false, 'message' => 'Erreur lors du retrait du like'];
        } catch (PDOException $e) {
            error_log("Erreur removeLike: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors du retrait du like'];
        }
    }

    /**
     * Toggle like - Ajoute ou retire un like
     */
    public function toggleLike($post_id, $user_id) {
        if ($this->hasUserLiked($post_id, $user_id)) {
            return $this->removeLike($post_id, $user_id);
        } else {
            return $this->addLike($post_id, $user_id);
        }
    }

    /**
     * Vérifier si un utilisateur a liké un post
     */
    public function hasUserLiked($post_id, $user_id) {
        try {
            $sql = "SELECT Id_like FROM likes WHERE Id_post = ? AND Id_utilisateur = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$post_id, $user_id]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Erreur hasUserLiked: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir le nombre de likes d'un post
     */
    public function getLikesCount($post_id) {
        try {
            $sql = "SELECT COUNT(*) as count FROM likes WHERE Id_post = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$post_id]);
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            error_log("Erreur getLikesCount: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Mettre à jour le compteur de likes dans la table post
     */
    private function updateLikesCount($post_id) {
        try {
            $likes_count = $this->getLikesCount($post_id);
            $sql = "UPDATE post SET likes_count = ? WHERE Id_post = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$likes_count, $post_id]);
        } catch (PDOException $e) {
            error_log("Erreur updateLikesCount: " . $e->getMessage());
        }
    }

    /**
     * Enrichir les posts avec les informations de likes pour un utilisateur
     */
    public function enrichPostsWithLikes($posts, $user_id = 0) {
        foreach ($posts as &$post) {
            $post_id = $post['Id_post'];
            $post['likes_count'] = $this->getLikesCount($post_id);
            $post['user_liked'] = $user_id > 0 ? $this->hasUserLiked($post_id, $user_id) : false;
        }
        return $posts;
    }

    /**
     * Vérifier si la table likes existe
     */
    public function likesTableExists() {
        try {
            $sql = "SHOW TABLES LIKE 'likes'";
            $result = $this->pdo->query($sql);
            return $result->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur likesTableExists: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer la table likes
     */
    public function createLikesTable() {
        // Vérifier si la table existe déjà
        if ($this->likesTableExists()) {
            return true;
        }

        try {
            $sql = "CREATE TABLE IF NOT EXISTS likes (
                Id_like INT AUTO_INCREMENT PRIMARY KEY,
                Id_post INT NOT NULL,
                Id_utilisateur INT NOT NULL,
                date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (Id_post) REFERENCES post(Id_post) ON DELETE CASCADE,
                FOREIGN KEY (Id_utilisateur) REFERENCES utilisateur(Id_utilisateur) ON DELETE CASCADE,
                UNIQUE KEY unique_like (Id_post, Id_utilisateur)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            
            $this->pdo->exec($sql);
            
            // Ajouter la colonne likes_count à la table post si elle n'existe pas
            $this->addLikesCountColumn();
            
            return true;
        } catch (PDOException $e) {
            error_log("Erreur createLikesTable: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ajouter la colonne likes_count à la table post
     */
    private function addLikesCountColumn() {
        try {
            // Vérifier si la colonne existe déjà
            $sql = "SHOW COLUMNS FROM post LIKE 'likes_count'";
            $result = $this->pdo->query($sql);
            
            if ($result->rowCount() == 0) {
                $sql = "ALTER TABLE post ADD COLUMN likes_count INT DEFAULT 0";
                $this->pdo->exec($sql);
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Erreur addLikesCountColumn: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer tous les likes d'un post
     */
    public function deletePostLikes($post_id) {
        try {
            $sql = "DELETE FROM likes WHERE Id_post = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$post_id]);
        } catch (PDOException $e) {
            error_log("Erreur deletePostLikes: " . $e->getMessage());
            return false;
        }
    }
}
?>