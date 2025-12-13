<?php
require_once __DIR__ . '/../config.php';

class CommentModel {
    public $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    public function getCommentsByPost($post_id) {
        try {
            $sql = "SELECT c.*, CONCAT(u.prenom, ' ', u.nom) AS auteur 
                    FROM commentaire c 
                    JOIN utilisateur u ON c.Id_utilisateur = u.Id_utilisateur 
                    WHERE c.Id_post = ? 
                    ORDER BY c.date_creation ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$post_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getCommentsByPost: " . $e->getMessage());
            return [];
        }
    }

    public function addComment($post_id, $user_id, $contenu, $media_url = '') {
        try {
            $sql = "INSERT INTO commentaire (Id_post, Id_utilisateur, contenu, media_url, date_creation) 
                    VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$post_id, $user_id, $contenu, $media_url]);
        } catch (PDOException $e) {
            error_log("Erreur addComment: " . $e->getMessage());
            return false;
        }
    }

    public function getAllCommentsWithPosts() {
        try {
            $sql = "SELECT c.*, p.titre as post_titre, CONCAT(u.prenom, ' ', u.nom) AS auteur
                    FROM commentaire c 
                    JOIN post p ON c.Id_post = p.Id_post
                    JOIN utilisateur u ON c.Id_utilisateur = u.Id_utilisateur
                    ORDER BY c.date_creation DESC";
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllCommentsWithPosts: " . $e->getMessage());
            return [];
        }
    }

    public function countComments() {
        try {
            $sql = "SELECT COUNT(*) as total FROM commentaire";
            $result = $this->pdo->query($sql)->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function countCommentsByPost($post_id) {
        try {
            $sql = "SELECT COUNT(*) as total FROM commentaire WHERE Id_post = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$post_id]);
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function deleteComment($comment_id) {
        try {
            $sql = "DELETE FROM commentaire WHERE Id_commentaire = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$comment_id]);
        } catch (PDOException $e) {
            error_log("Erreur deleteComment: " . $e->getMessage());
            return false;
        }
    }

    public function editComment($comment_id, $user_id, $contenu) {
        try {
            $sql = "UPDATE commentaire SET contenu = ? WHERE Id_commentaire = ? AND Id_utilisateur = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$contenu, $comment_id, $user_id]);
        } catch (PDOException $e) {
            error_log("Erreur editComment: " . $e->getMessage());
            return false;
        }
    }

    public function getCommentById($comment_id) {
        try {
            $sql = "SELECT c.*, CONCAT(u.prenom, ' ', u.nom) AS auteur
                    FROM commentaire c 
                    JOIN utilisateur u ON c.Id_utilisateur = u.Id_utilisateur 
                    WHERE c.Id_commentaire = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$comment_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getCommentById: " . $e->getMessage());
            return false;
        }
    }
}

?>
