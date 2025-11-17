<?php
session_start();

// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config.php';
require_once '../model/post.php';

$postModel = new Post();

// Vérifier si l'utilisateur est connecté, sinon définir des valeurs par défaut
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Valeur par défaut pour le test
    $_SESSION['user_name'] = 'Amine Dabbebi';
    $_SESSION['is_admin'] = true;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$is_admin = $_SESSION['is_admin'];

$action = $_GET['action'] ?? 'list';

// Fonctions pour gérer les commentaires
function addComment($postModel) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'], $_POST['contenu'])) {
        $post_id = $_POST['post_id'];
        $contenu = trim($_POST['contenu']);
        $user_id = $_SESSION['user_id'];
        
        // Validation
        if (empty($contenu)) {
            header("Location: ../control/control.php?action=view&id=$post_id&error=empty_comment");
            exit;
        }
        
        try {
            $success = $postModel->addComment($post_id, $user_id, $contenu);
            
            if ($success) {
                header("Location: ../control/control.php?action=view&id=$post_id&success=comment_added");
                exit;
            } else {
                header("Location: ../control/control.php?action=view&id=$post_id&error=comment_failed");
                exit;
            }
        } catch (PDOException $e) {
            header("Location: ../control/control.php?action=view&id=$post_id&error=database_error");
            exit;
        }
    } else {
        header("Location: ../control/control.php?action=list");
        exit;
    }
}

function editComment($postModel) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'], $_POST['contenu'], $_POST['post_id'])) {
        $comment_id = $_POST['comment_id'];
        $contenu = trim($_POST['contenu']);
        $post_id = $_POST['post_id'];
        $user_id = $_SESSION['user_id'];
        
        // Validation
        if (empty($contenu)) {
            header("Location: ../control/control.php?action=view&id=$post_id&error=empty_comment");
            exit;
        }
        
        try {
            // Vérifier que l'utilisateur est bien l'auteur du commentaire
            $comment = $postModel->getCommentById($comment_id);
            
            if ($comment && $comment['Id_utilisateur'] == $user_id) {
                $success = $postModel->updateComment($comment_id, $contenu);
                
                if ($success) {
                    header("Location: ../control/control.php?action=view&id=$post_id&success=comment_edited");
                    exit;
                } else {
                    header("Location: ../control/control.php?action=view&id=$post_id&error=edit_failed");
                    exit;
                }
            } else {
                header("Location: ../control/control.php?action=view&id=$post_id&error=unauthorized");
                exit;
            }
        } catch (PDOException $e) {
            header("Location: ../control/control.php?action=view&id=$post_id&error=database_error");
            exit;
        }
    } else {
        header("Location: ../control/control.php?action=list");
        exit;
    }
}

function deleteComment($postModel) {
    if (isset($_GET['id'], $_GET['post_id'])) {
        $comment_id = $_GET['id'];
        $post_id = $_GET['post_id'];
        $user_id = $_SESSION['user_id'];
        
        try {
            // Vérifier que l'utilisateur est bien l'auteur du commentaire
            $comment = $postModel->getCommentById($comment_id);
            
            if ($comment && $comment['Id_utilisateur'] == $user_id) {
                $success = $postModel->deleteComment($comment_id);
                
                if ($success) {
                    header("Location: ../control/control.php?action=view&id=$post_id&success=comment_deleted");
                    exit;
                } else {
                    header("Location: ../control/control.php?action=view&id=$post_id&error=delete_failed");
                    exit;
                }
            } else {
                header("Location: ../control/control.php?action=view&id=$post_id&error=unauthorized");
                exit;
            }
        } catch (PDOException $e) {
            header("Location: ../control/control.php?action=view&id=$post_id&error=database_error");
            exit;
        }
    } else {
        header("Location: ../control/control.php?action=list");
        exit;
    }
}

switch ($action) {
    case 'list':
    default:
        $posts = $postModel->all();
        require '../view/forum.php';
        exit;

    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre      = trim($_POST['titre']);
            $contenu    = trim($_POST['contenu']);
            $categorie  = trim($_POST['categorie']);
            $piece_jointe = '';

            // --- Upload image ---
            if (!empty($_FILES['piece_jointe']['name'])) {
                $uploadDir = '../uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir);

                $fileName = uniqid('img_') . '.' . pathinfo($_FILES['piece_jointe']['name'], PATHINFO_EXTENSION);
                
                if (move_uploaded_file($_FILES['piece_jointe']['tmp_name'], $uploadDir . $fileName)) {
                    $piece_jointe = 'uploads/' . $fileName;
                }
            }

            // --- récupérer automatiquement un utilisateur ---
            $db = config::getConnexion();
            $user = $db->query("SELECT Id_utilisateur FROM utilisateur LIMIT 1")->fetch();

            if (!$user) die("Erreur : Aucun utilisateur trouvé dans la table utilisateur.");

            $idUser = $user['Id_utilisateur'];

            // --- insertion AVEC LE MODÈLE ---
            $success = $postModel->create($idUser, $titre, $categorie, $contenu, $piece_jointe);

            if ($success) {
                header("Location: control.php?action=list");
                exit;
            } else {
                die("Erreur lors de la création du post.");
            }
        }

        require '../view/ajout.php';
        exit;

    case 'admin':
        $posts = $postModel->all();
        $totalPosts = $postModel->countPosts();
        $totalUsers = $postModel->countUsers();
        $totalComments = $postModel->countComments();
        require '../view/admin.php';
        exit;

    case 'edit':
        $id = $_GET['id'] ?? 0;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim($_POST['titre']);
            $contenu = trim($_POST['contenu']);
            $categorie = trim($_POST['categorie']);
            
            $success = $postModel->update($id, $titre, $categorie, $contenu);
            
            if ($success) {
                header("Location: control.php?action=list");
                exit;
            } else {
                die("Erreur lors de la modification du post.");
            }
        } else {
            $post = $postModel->findById($id);
            if (!$post) {
                header("Location: control.php?action=list");
                exit;
            }
            require '../view/edit.php';
        }
        exit;

    case 'delete':
        $id = $_GET['id'] ?? 0;
        $success = $postModel->delete($id);
        
        if ($success) {
            header("Location: control.php?action=list");
            exit;
        } else {
            die("Erreur lors de la suppression du post.");
        }
        exit;

    case 'view':
        $id = $_GET['id'] ?? 0;
        $post = $postModel->findById($id);
        
        if (!$post) {
            header("Location: control.php?action=list");
            exit;
        }
        
        // Récupérer les commentaires pour ce post
        $comments = $postModel->getCommentsByPostId($id);
        
        require '../view/view.php';
        exit;

    // Actions pour les commentaires
    case 'add_comment':
        addComment($postModel);
        break;
        
    case 'edit_comment':
        editComment($postModel);
        break;
        
    case 'delete_comment':
        deleteComment($postModel);
        break;
}

?>