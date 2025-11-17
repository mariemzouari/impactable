<?php
require_once '../config.php';

class Post {
    public $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    public function all() {
        $sql = "SELECT p.*, CONCAT(u.prenom, ' ', u.nom) AS auteur 
                FROM post p 
                JOIN utilisateur u ON p.Id_utilisateur = u.Id_utilisateur
                ORDER BY p.date_creation DESC";

        return $this->pdo->query($sql)->fetchAll();
    }

    // AJOUT : Méthode create manquante
    public function create($id_utilisateur, $titre, $categorie, $contenu, $piece_jointe) {
        $sql = "INSERT INTO post (Id_utilisateur, titre, categorie, contenu, piece_jointe, date_creation) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_utilisateur, $titre, $categorie, $contenu, $piece_jointe]);
    }

    // AJOUT : Méthodes supplémentaires pour les fonctionnalités complètes
    public function findById($id) {
        $sql = "SELECT p.*, CONCAT(u.prenom, ' ', u.nom) AS auteur 
                FROM post p 
                JOIN utilisateur u ON p.Id_utilisateur = u.Id_utilisateur
                WHERE p.Id_post = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $titre, $categorie, $contenu) {
        $sql = "UPDATE post SET titre = ?, categorie = ?, contenu = ?, date_modification = NOW() WHERE Id_post = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$titre, $categorie, $contenu, $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM post WHERE Id_post = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function countPosts() {
        $sql = "SELECT COUNT(*) as total FROM post";
        $result = $this->pdo->query($sql)->fetch();
        return $result['total'];
    }

    public function countUsers() {
        $sql = "SELECT COUNT(*) as total FROM utilisateur";
        $result = $this->pdo->query($sql)->fetch();
        return $result['total'];
    }

    public function countComments() {
        $sql = "SELECT COUNT(*) as total FROM commentaire";
        $result = $this->pdo->query($sql)->fetch();
        return $result['total'];
    }

    // AJOUT : Méthodes pour les commentaires
    public function addComment($post_id, $user_id, $contenu) {
        $sql = "INSERT INTO commentaire (Id_post, Id_utilisateur, contenu, date_creation) VALUES (?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$post_id, $user_id, $contenu]);
    }

    public function updateComment($comment_id, $contenu) {
        $sql = "UPDATE commentaire SET contenu = ?, date_modification = NOW() WHERE Id_commentaire = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$contenu, $comment_id]);
    }

    public function deleteComment($comment_id) {
        $sql = "DELETE FROM commentaire WHERE Id_commentaire = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$comment_id]);
    }

    public function getCommentById($comment_id) {
        $sql = "SELECT c.*, CONCAT(u.prenom, ' ', u.nom) AS auteur, c.Id_utilisateur
                FROM commentaire c 
                JOIN utilisateur u ON c.Id_utilisateur = u.Id_utilisateur 
                WHERE c.Id_commentaire = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$comment_id]);
        return $stmt->fetch();
    }

    public function getCommentsByPostId($post_id) {
        $sql = "SELECT c.*, CONCAT(u.prenom, ' ', u.nom) AS auteur, c.Id_utilisateur
                FROM commentaire c 
                JOIN utilisateur u ON c.Id_utilisateur = u.Id_utilisateur 
                WHERE c.Id_post = ? 
                ORDER BY c.date_creation ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$post_id]);
        return $stmt->fetchAll();
    }
}
?>