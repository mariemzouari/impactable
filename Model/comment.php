<?php
require_once '../config.php';

class Comment {
    public $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    // Récupérer tous les commentaires d'un post avec jointure
    public function getCommentsByPost($post_id) {
        try {
            $sql = "SELECT c.*, CONCAT(u.prenom, ' ', u.nom) AS auteur 
                    FROM commentaire c 
                    JOIN utilisateur u ON c.Id_utilisateur = u.Id_utilisateur 
                    WHERE c.Id_post = ? 
                    ORDER BY c.date_creation ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$post_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur getCommentsByPost: " . $e->getMessage());
            return [];
        }
    }

    // Ajouter un commentaire
    public function addComment($post_id, $user_id, $contenu) {
        try {
            $sql = "INSERT INTO commentaire (Id_post, Id_utilisateur, contenu, date_creation) 
                    VALUES (?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$post_id, $user_id, $contenu]);
        } catch (PDOException $e) {
            error_log("Erreur addComment: " . $e->getMessage());
            return false;
        }
    }

    // Récupérer tous les commentaires avec infos des posts (pour admin)
    public function getAllCommentsWithPosts() {
        try {
            $sql = "SELECT c.*, p.titre as post_titre, CONCAT(u.prenom, ' ', u.nom) AS auteur
                    FROM commentaire c 
                    JOIN post p ON c.Id_post = p.Id_post
                    JOIN utilisateur u ON c.Id_utilisateur = u.Id_utilisateur
                    ORDER BY c.date_creation DESC";
            return $this->pdo->query($sql)->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur getAllCommentsWithPosts: " . $e->getMessage());
            return [];
        }
    }

    // Compter les commentaires
    public function countComments() {
        try {
            $sql = "SELECT COUNT(*) as total FROM commentaire";
            $result = $this->pdo->query($sql)->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Erreur countComments: " . $e->getMessage());
            return 0;
        }
    }

    // Compter les commentaires par post
    public function countCommentsByPost($post_id) {
        try {
            $sql = "SELECT COUNT(*) as total FROM commentaire WHERE Id_post = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$post_id]);
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Erreur countCommentsByPost: " . $e->getMessage());
            return 0;
        }
    }

    // Supprimer un commentaire (pour admin)
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

    // Récupérer un commentaire par son ID
    public function getCommentById($comment_id) {
        try {
            $sql = "SELECT c.*, CONCAT(u.prenom, ' ', u.nom) AS auteur
                    FROM commentaire c 
                    JOIN utilisateur u ON c.Id_utilisateur = u.Id_utilisateur 
                    WHERE c.Id_commentaire = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$comment_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erreur getCommentById: " . $e->getMessage());
            return false;
        }
    }
}
?>