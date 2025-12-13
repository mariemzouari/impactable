<?php
require_once __DIR__ . '/../config.php';

class PostModel {
    public $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    public function all() {
        try {
            $sql = "SELECT p.*, CONCAT(u.prenom, ' ', u.nom) AS auteur 
                    FROM post p 
                    INNER JOIN utilisateur u ON p.Id_utilisateur = u.Id_utilisateur
                    ORDER BY p.date_creation DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur all posts: " . $e->getMessage());
            return [];
        }
    }

    public function create($id_utilisateur, $titre, $categorie, $contenu, $piece_jointe = '') {
        try {
            $sql = "INSERT INTO post (Id_utilisateur, titre, categorie, contenu, piece_jointe, date_creation) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id_utilisateur, $titre, $categorie, $contenu, $piece_jointe]);
        } catch (PDOException $e) {
            error_log("Erreur création post: " . $e->getMessage());
            return false;
        }
    }

    public function findById($id) {
        try {
            $sql = "SELECT p.*, CONCAT(u.prenom, ' ', u.nom) AS auteur 
                    FROM post p 
                    INNER JOIN utilisateur u ON p.Id_utilisateur = u.Id_utilisateur
                    WHERE p.Id_post = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur findById: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $titre, $categorie, $contenu) {
        try {
            $sql = "UPDATE post SET titre = ?, categorie = ?, contenu = ?, date_modification = NOW() WHERE Id_post = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$titre, $categorie, $contenu, $id]);
        } catch (PDOException $e) {
            error_log("Erreur update post: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $sql_comments = "DELETE FROM commentaire WHERE Id_post = ?";
            $stmt_comments = $this->pdo->prepare($sql_comments);
            $stmt_comments->execute([$id]);
            
            $sql_likes = "DELETE FROM likes WHERE Id_post = ?";
            $stmt_likes = $this->pdo->prepare($sql_likes);
            $stmt_likes->execute([$id]);
            
            $sql = "DELETE FROM post WHERE Id_post = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Erreur delete post: " . $e->getMessage());
            return false;
        }
    }

    public function countPosts() {
        try {
            $sql = "SELECT COUNT(*) as total FROM post";
            $result = $this->pdo->query($sql)->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function countUsers() {
        try {
            $sql = "SELECT COUNT(*) as total FROM utilisateur";
            $result = $this->pdo->query($sql)->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
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

    public function filterByCategory($category) {
        try {
            $sql = "SELECT p.*, CONCAT(u.prenom, ' ', u.nom) AS auteur 
                    FROM post p 
                    INNER JOIN utilisateur u ON p.Id_utilisateur = u.Id_utilisateur
                    WHERE p.categorie = ?
                    ORDER BY p.date_creation DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$category]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur filterByCategory: " . $e->getMessage());
            return [];
        }
    }

    public function search($query) {
        try {
            $sql = "SELECT p.*, CONCAT(u.prenom, ' ', u.nom) AS auteur 
                    FROM post p 
                    INNER JOIN utilisateur u ON p.Id_utilisateur = u.Id_utilisateur
                    WHERE p.titre LIKE ? OR p.contenu LIKE ?
                    ORDER BY p.date_creation DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $searchTerm = '%' . $query . '%';
            $stmt->execute([$searchTerm, $searchTerm]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur search: " . $e->getMessage());
            return [];
        }
    }

    public function validateStrict($titre, $contenu, $categorie) {
        $errors = [];

        if (empty($titre)) {
            $errors[] = "Le titre est obligatoire";
        } elseif (strlen($titre) < 5) {
            $errors[] = "Le titre doit contenir au moins 5 caractères";
        } elseif (strlen($titre) > 255) {
            $errors[] = "Le titre ne peut pas dépasser 255 caractères";
        }

        if (empty($contenu)) {
            $errors[] = "Le contenu est obligatoire";
        } elseif (strlen($contenu) < 10) {
            $errors[] = "Le contenu doit contenir au moins 10 caractères";
        }

        $categoriesValides = ['Opportunités', 'Événements', 'Idées', 'Questions','Ressources'];
        $categorie = trim($categorie); // Nettoyer les espaces
        if (empty($categorie)) {
            $errors[] = "La catégorie est obligatoire";
        } elseif (!in_array($categorie, $categoriesValides)) {
            $errors[] = "Veuillez sélectionner une catégorie valide";
        }

        return $errors;
    }

    public function validateFile($file) {
        $errors = [];
        
        if (!empty($file['name'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxFileSize = 5 * 1024 * 1024;
            
            if (!in_array($file['type'], $allowedTypes)) {
                $errors[] = "Type de fichier non autorisé. Formats acceptés: JPG, PNG, GIF, WebP";
            }
            
            if ($file['size'] > $maxFileSize) {
                $errors[] = "Le fichier est trop volumineux (max 5MB)";
            }
        }
        
        return $errors;
    }

    public function validateCommentStrict($contenu) {
        $errors = [];
        
        if (empty(trim($contenu))) {
            $errors[] = "Le commentaire ne peut pas être vide";
        } elseif (strlen(trim($contenu)) < 2) {
            $errors[] = "Le commentaire doit contenir au moins 2 caractères";
        } elseif (strlen($contenu) > 1000) {
            $errors[] = "Le commentaire ne peut pas dépasser 1000 caractères";
        }
        
        return $errors;
    }
}

?>
