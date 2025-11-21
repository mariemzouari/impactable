<?php
require_once '../config.php';

class Post {
    public $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    public function all() {
        try {
            $sql = "SELECT p.*, CONCAT(u.prenom, ' ', u.nom) AS auteur 
                    FROM post p 
                    JOIN utilisateur u ON p.Id_utilisateur = u.Id_utilisateur
                    ORDER BY p.date_creation DESC";
            return $this->pdo->query($sql)->fetchAll();
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
                    JOIN utilisateur u ON p.Id_utilisateur = u.Id_utilisateur
                    WHERE p.Id_post = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
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
            error_log("Erreur countPosts: " . $e->getMessage());
            return 0;
        }
    }

    public function countUsers() {
        try {
            $sql = "SELECT COUNT(*) as total FROM utilisateur";
            $result = $this->pdo->query($sql)->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Erreur countUsers: " . $e->getMessage());
            return 0;
        }
    }

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

    public function userExists($user_id) {
        try {
            $sql = "SELECT Id_utilisateur FROM utilisateur WHERE Id_utilisateur = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Erreur userExists: " . $e->getMessage());
            return false;
        }
    }

    public function createDefaultUser() {
        try {
            $tableExists = $this->pdo->query("SHOW TABLES LIKE 'utilisateur'")->rowCount() > 0;
            
            if (!$tableExists) {
                error_log("❌ Table utilisateur n'existe pas");
                return false;
            }
            
            $sql = "SELECT COUNT(*) as count FROM utilisateur";
            $result = $this->pdo->query($sql)->fetch();
            
            if ($result['count'] == 0) {
                $sql = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                
                $nom = "Admin";
                $prenom = "ImpactAble";
                $email = "admin@impactable.org";
                $mot_de_passe = password_hash("admin123", PASSWORD_DEFAULT);
                $role = "admin";
                
                if ($stmt->execute([$nom, $prenom, $email, $mot_de_passe, $role])) {
                    $newId = $this->pdo->lastInsertId();
                    error_log("✅ Utilisateur admin créé avec ID: $newId");
                    return $newId;
                } else {
                    error_log("❌ Échec exécution requête création utilisateur");
                    return false;
                }
            }
            
            $sql = "SELECT Id_utilisateur FROM utilisateur WHERE role = 'admin' LIMIT 1";
            $result = $this->pdo->query($sql)->fetch();
            if ($result) {
                return $result['Id_utilisateur'];
            }
            
            $sql = "SELECT Id_utilisateur FROM utilisateur LIMIT 1";
            $result = $this->pdo->query($sql)->fetch();
            return $result ? $result['Id_utilisateur'] : false;
            
        } catch (PDOException $e) {
            error_log("❌ Erreur createDefaultUser: " . $e->getMessage());
            return false;
        }
    }

    public function getAllUsers() {
        try {
            $sql = "SELECT Id_utilisateur, nom, prenom, email, role FROM utilisateur";
            return $this->pdo->query($sql)->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur getAllUsers: " . $e->getMessage());
            return [];
        }
    }

    public function addComment($post_id, $user_id, $contenu) {
        try {
            $sql = "INSERT INTO commentaire (Id_post, Id_utilisateur, contenu, date_creation) 
                    VALUES (?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$post_id, $user_id, $contenu]);
        } catch (PDOException $e) {
            error_log("Erreur ajout commentaire: " . $e->getMessage());
            return false;
        }
    }

    public function updateComment($comment_id, $contenu) {
        try {
            $sql = "UPDATE commentaire SET contenu = ?, date_modification = NOW() WHERE Id_commentaire = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$contenu, $comment_id]);
        } catch (PDOException $e) {
            error_log("Erreur updateComment: " . $e->getMessage());
            return false;
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

    public function getCommentById($comment_id) {
        try {
            $sql = "SELECT c.*, CONCAT(u.prenom, ' ', u.nom) AS auteur, c.Id_utilisateur
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

    public function getCommentsByPostId($post_id) {
        try {
            $sql = "SELECT c.*, CONCAT(u.prenom, ' ', u.nom) AS auteur, c.Id_utilisateur
                    FROM commentaire c 
                    JOIN utilisateur u ON c.Id_utilisateur = u.Id_utilisateur 
                    WHERE c.Id_post = ? 
                    ORDER BY c.date_creation ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$post_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur getCommentsByPostId: " . $e->getMessage());
            return [];
        }
    }

    // NOUVELLES MÉTHODES POUR ADMIN
    public function getAllComments() {
        try {
            $sql = "SELECT c.*, 
                           p.titre as post_titre,
                           CONCAT(u.prenom, ' ', u.nom) AS auteur,
                           u.Id_utilisateur
                    FROM commentaire c 
                    JOIN post p ON c.Id_post = p.Id_post
                    JOIN utilisateur u ON c.Id_utilisateur = u.Id_utilisateur
                    ORDER BY c.date_creation DESC";
            return $this->pdo->query($sql)->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur getAllComments: " . $e->getMessage());
            return [];
        }
    }

    public function deleteCommentAdmin($comment_id) {
        try {
            $sql = "DELETE FROM commentaire WHERE Id_commentaire = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$comment_id]);
        } catch (PDOException $e) {
            error_log("Erreur deleteCommentAdmin: " . $e->getMessage());
            return false;
        }
    }

    public function countCommentsByUser($user_id) {
        try {
            $sql = "SELECT COUNT(*) as total FROM commentaire WHERE Id_utilisateur = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$user_id]);
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Erreur countCommentsByUser: " . $e->getMessage());
            return 0;
        }
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

        $categoriesValides = ['opportunites', 'evenements', 'campagnes', 'questions', 'ressources', 'autre'];
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
            
            $fileType = $file['type'];
            $fileSize = $file['size'];
            
            if (!in_array($fileType, $allowedTypes)) {
                $errors[] = "Type de fichier non autorisé. Formats acceptés: JPG, PNG, GIF, WebP";
            }
            
            if ($fileSize > $maxFileSize) {
                $errors[] = "Le fichier est trop volumineux (max 5MB)";
            }
        }
        
        return $errors;
    }

    public function filterByCategory($category) {
        try {
            $sql = "SELECT p.*, CONCAT(u.prenom, ' ', u.nom) AS auteur 
                    FROM post p 
                    JOIN utilisateur u ON p.Id_utilisateur = u.Id_utilisateur
                    WHERE p.categorie = ?
                    ORDER BY p.date_creation DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$category]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur filterByCategory: " . $e->getMessage());
            return [];
        }
    }

    public function search($query) {
        try {
            $sql = "SELECT p.*, CONCAT(u.prenom, ' ', u.nom) AS auteur 
                    FROM post p 
                    JOIN utilisateur u ON p.Id_utilisateur = u.Id_utilisateur
                    WHERE p.titre LIKE ? OR p.contenu LIKE ?
                    ORDER BY p.date_creation DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $searchTerm = '%' . $query . '%';
            $stmt->execute([$searchTerm, $searchTerm]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur search: " . $e->getMessage());
            return [];
        }
    }
}
?>