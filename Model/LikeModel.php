<?php
require_once __DIR__ . '/../config.php';

class LikeModel {
    public $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
        $this->createLikesTable();
    }

    public function toggleLike($post_id, $user_id) {
        if ($this->hasUserLiked($post_id, $user_id)) {
            return $this->removeLike($post_id, $user_id);
        } else {
            return $this->addLike($post_id, $user_id);
        }
    }

    private function addLike($post_id, $user_id) {
        try {
            if ($this->hasUserLiked($post_id, $user_id)) {
                return ['success' => false, 'message' => 'Vous avez déjà liké ce post'];
            }

            $sql = "INSERT INTO likes (Id_post, Id_utilisateur, date_creation) VALUES (?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            
            if ($stmt->execute([$post_id, $user_id])) {
                return ['success' => true, 'message' => 'Like ajouté'];
            }
            
            return ['success' => false, 'message' => 'Erreur lors de l\'ajout du like'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de l\'ajout du like'];
        }
    }

    private function removeLike($post_id, $user_id) {
        try {
            $sql = "DELETE FROM likes WHERE Id_post = ? AND Id_utilisateur = ?";
            $stmt = $this->pdo->prepare($sql);
            
            if ($stmt->execute([$post_id, $user_id])) {
                return ['success' => true, 'message' => 'Like retiré'];
            }
            
            return ['success' => false, 'message' => 'Erreur lors du retrait du like'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors du retrait du like'];
        }
    }

    public function hasUserLiked($post_id, $user_id) {
        try {
            $sql = "SELECT Id_like FROM likes WHERE Id_post = ? AND Id_utilisateur = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$post_id, $user_id]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getLikesCount($post_id) {
        try {
            $sql = "SELECT COUNT(*) as count FROM likes WHERE Id_post = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$post_id]);
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function enrichPostsWithLikes($posts, $user_id = 0) {
        foreach ($posts as &$post) {
            $post_id = $post['Id_post'];
            $post['likes_count'] = $this->getLikesCount($post_id);
            $post['user_liked'] = $user_id > 0 ? $this->hasUserLiked($post_id, $user_id) : false;
        }
        unset($post);
        return $posts;
    }

    private function createLikesTable() {
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
            
            // Add likes_count column if it doesn't exist
            $sql = "ALTER TABLE post ADD COLUMN likes_count INT DEFAULT 0";
            try {
                $this->pdo->exec($sql);
            } catch (PDOException $e) {
                // Column already exists, ignore error
            }
        } catch (PDOException $e) {
            error_log("Erreur createLikesTable: " . $e->getMessage());
        }
    }

    public function deletePostLikes($post_id) {
        try {
            $sql = "DELETE FROM likes WHERE Id_post = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$post_id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}

?>
