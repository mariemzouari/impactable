<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config.php';
require_once '../model/post.php';

$postModel = new Post();

$user_id = $postModel->createDefaultUser();
if (!$user_id) {
    $user_id = 1;
}

$_SESSION['user_id'] = $user_id;
$_SESSION['user_name'] = 'Amine Dabbebi';
$_SESSION['is_admin'] = true;

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$is_admin = $_SESSION['is_admin'];

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
    default:
        $posts = $postModel->all();
        $user_id = $_SESSION['user_id'];
        $user_name = $_SESSION['user_name'];
        $is_admin = $_SESSION['is_admin'];
        require '../view/forum.php';
        exit;

    case 'create':
        $errors = [];
        $old_data = [];
        $user_id = $_SESSION['user_id'];
        $user_name = $_SESSION['user_name'];
        $is_admin = $_SESSION['is_admin'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre      = trim($_POST['titre'] ?? '');
            $contenu    = trim($_POST['contenu'] ?? '');
            $categorie  = trim($_POST['categorie'] ?? '');
            $piece_jointe = '';

            $old_data = [
                'titre' => $titre,
                'contenu' => $contenu,
                'categorie' => $categorie
            ];

            $errors = $postModel->validateStrict($titre, $contenu, $categorie);
            
            if (!empty($_FILES['piece_jointe']['name'])) {
                $fileErrors = $postModel->validateFile($_FILES['piece_jointe']);
                $errors = array_merge($errors, $fileErrors);
            }

            if (empty($errors)) {
                if (!empty($_FILES['piece_jointe']['name'])) {
                    $uploadDir = '../uploads/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                    $fileName = uniqid('img_') . '.' . pathinfo($_FILES['piece_jointe']['name'], PATHINFO_EXTENSION);
                    
                    if (move_uploaded_file($_FILES['piece_jointe']['tmp_name'], $uploadDir . $fileName)) {
                        $piece_jointe = 'uploads/' . $fileName;
                    }
                }

                if (!$postModel->userExists($user_id)) {
                    $newUserId = $postModel->createDefaultUser();
                    if ($newUserId) {
                        $user_id = $newUserId;
                        $_SESSION['user_id'] = $newUserId;
                    } else {
                        $errors[] = "Erreur: Impossible de créer un utilisateur.";
                        require '../view/ajout.php';
                        exit;
                    }
                }

                $success = $postModel->create($user_id, $titre, $categorie, $contenu, $piece_jointe);

                if ($success) {
                    header("Location: control.php?action=list");
                    exit;
                } else {
                    $errors[] = "Erreur lors de la création du post.";
                }
            }
        }

        require '../view/ajout.php';
        exit;

    case 'edit':
        $id = $_GET['id'] ?? 0;
        $from_admin = isset($_GET['from']) && $_GET['from'] == 'admin';
        $error_message = [];
        $user_id = $_SESSION['user_id'];
        $user_name = $_SESSION['user_name'];
        $is_admin = $_SESSION['is_admin'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim($_POST['titre'] ?? '');
            $contenu = trim($_POST['contenu'] ?? '');
            $categorie = trim($_POST['categorie'] ?? '');
            
            $error_message = $postModel->validateStrict($titre, $contenu, $categorie);

            if (empty($error_message)) {
                $success = $postModel->update($id, $titre, $categorie, $contenu);
                
                if ($success) {
                    if ($from_admin) {
                        $_SESSION['admin_message'] = 'Post modifié avec succès !';
                        header("Location: control.php?action=admin");
                    } else {
                        header("Location: control.php?action=list");
                    }
                    exit;
                } else {
                    $error_message = ["Erreur lors de la modification du post."];
                }
            }
            
            $post = [
                'Id_post' => $id,
                'titre' => $titre,
                'contenu' => $contenu,
                'categorie' => $categorie,
                'auteur' => $_SESSION['user_name']
            ];
            require '../view/edit.php';
            exit;
        } else {
            $post = $postModel->findById($id);
            if (!$post) {
                if ($from_admin) {
                    $_SESSION['admin_error'] = 'Post non trouvé.';
                    header("Location: control.php?action=admin");
                } else {
                    header("Location: control.php?action=list");
                }
                exit;
            }
            require '../view/edit.php';
        }
        exit;

    case 'delete':
        $id = $_GET['id'] ?? 0;
        $from_admin = isset($_GET['from']) && $_GET['from'] == 'admin';
        
        $success = $postModel->delete($id);
        
        if ($success) {
            if ($from_admin) {
                $_SESSION['admin_message'] = 'Post supprimé avec succès !';
                header("Location: control.php?action=admin");
            } else {
                header("Location: control.php?action=list");
            }
            exit;
        } else {
            if ($from_admin) {
                $_SESSION['admin_error'] = 'Erreur lors de la suppression du post.';
                header("Location: control.php?action=admin");
            } else {
                die("Erreur lors de la suppression du post.");
            }
            exit;
        }
        exit;

    case 'view':
        $id = $_GET['id'] ?? 0;
        $post = $postModel->findById($id);
        
        if (!$post) {
            header("Location: control.php?action=list");
            exit;
        }
        
        $comments = $postModel->getCommentsByPostId($id);
        $user_id = $_SESSION['user_id'];
        $user_name = $_SESSION['user_name'];
        $is_admin = $_SESSION['is_admin'];
        
        require '../view/view.php';
        exit;

    case 'admin':
        $posts = $postModel->all();
        $totalPosts = $postModel->countPosts();
        $totalUsers = $postModel->countUsers();
        $totalComments = $postModel->countComments();
        
        require '../view/admin.php';
        exit;

    // NOUVELLES ACTIONS POUR GESTION DES COMMENTAIRES ADMIN
    case 'admin_comments':
        if (!$is_admin) {
            header("Location: control.php?action=list");
            exit;
        }
        
        $comments = $postModel->getAllComments();
        $totalComments = $postModel->countComments();
        
        require '../view/admin_comments.php';
        exit;

    case 'delete_comment_admin':
        if (!$is_admin) {
            header("Location: control.php?action=admin");
            exit;
        }
        
        if (isset($_GET['id'])) {
            $comment_id = (int)$_GET['id'];
            $success = $postModel->deleteCommentAdmin($comment_id);
            
            if ($success) {
                $_SESSION['admin_message'] = 'Commentaire supprimé avec succès !';
            } else {
                $_SESSION['admin_error'] = 'Erreur lors de la suppression du commentaire.';
            }
        }
        
        header("Location: control.php?action=admin_comments");
        exit;

    case 'filter':
        $category = $_GET['category'] ?? '';
        if ($category) {
            $posts = $postModel->filterByCategory($category);
        } else {
            $posts = $postModel->all();
        }
        
        $user_id = $_SESSION['user_id'];
        $user_name = $_SESSION['user_name'];
        $is_admin = $_SESSION['is_admin'];
        
        require '../view/forum.php';
        exit;

    case 'search':
        $query = $_GET['q'] ?? '';
        if ($query) {
            $posts = $postModel->search($query);
        } else {
            $posts = $postModel->all();
        }
        
        $user_id = $_SESSION['user_id'];
        $user_name = $_SESSION['user_name'];
        $is_admin = $_SESSION['is_admin'];
        
        require '../view/forum.php';
        exit;

    case 'add_comment':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'], $_POST['contenu'])) {
            $post_id = (int)$_POST['post_id'];
            $contenu = trim($_POST['contenu']);
            $user_id = $_SESSION['user_id'];

            if (!$postModel->userExists($user_id)) {
                $_SESSION['comment_errors'] = ['Erreur: Utilisateur non valide.'];
                header("Location: control.php?action=view&id=" . $post_id);
                exit;
            }

            $post = $postModel->findById($post_id);
            if (!$post) {
                $_SESSION['comment_errors'] = ['Erreur: Post non trouvé.'];
                header("Location: control.php?action=view&id=" . $post_id);
                exit;
            }
            
            $errors = $postModel->validateCommentStrict($contenu);
            
            if (!empty($errors)) {
                $_SESSION['comment_errors'] = $errors;
                $_SESSION['old_comment'] = $contenu;
                header("Location: control.php?action=view&id=" . $post_id);
                exit;
            }
            
            try {
                $contenu = htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8');
                $success = $postModel->addComment($post_id, $user_id, $contenu);
                
                if ($success) {
                    $_SESSION['comment_success'] = 'Commentaire ajouté avec succès!';
                } else {
                    $_SESSION['comment_errors'] = ['Erreur lors de l\'ajout du commentaire'];
                }
            } catch (PDOException $e) {
                $_SESSION['comment_errors'] = ['Erreur lors de l\'ajout du commentaire.'];
            }
            
            header("Location: control.php?action=view&id=" . $post_id);
            exit;
        }
        break;
        
    case 'edit_comment':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'], $_POST['contenu'], $_POST['post_id'])) {
            $comment_id = (int)$_POST['comment_id'];
            $contenu = trim($_POST['contenu']);
            $post_id = (int)$_POST['post_id'];
            $user_id = $_SESSION['user_id'];
            
            $errors = $postModel->validateCommentStrict($contenu);
            
            if (!empty($errors)) {
                $_SESSION['comment_errors'] = $errors;
                header("Location: control.php?action=view&id=" . $post_id);
                exit;
            }
            
            try {
                $comment = $postModel->getCommentById($comment_id);
                
                if ($comment && $comment['Id_utilisateur'] == $user_id) {
                    $contenu = htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8');
                    $success = $postModel->updateComment($comment_id, $contenu);
                    
                    if ($success) {
                        $_SESSION['comment_success'] = 'Commentaire modifié avec succès!';
                    } else {
                        $_SESSION['comment_errors'] = ['Erreur lors de la modification'];
                    }
                } else {
                    $_SESSION['comment_errors'] = ['Non autorisé à modifier ce commentaire'];
                }
            } catch (PDOException $e) {
                $_SESSION['comment_errors'] = ['Erreur lors de la modification du commentaire.'];
            }
            
            header("Location: control.php?action=view&id=" . $post_id);
            exit;
        }
        break;
        
    case 'delete_comment':
        if (isset($_GET['id'], $_GET['post_id'])) {
            $comment_id = (int)$_GET['id'];
            $post_id = (int)$_GET['post_id'];
            $user_id = $_SESSION['user_id'];
            
            try {
                $comment = $postModel->getCommentById($comment_id);
                
                if ($comment && $comment['Id_utilisateur'] == $user_id) {
                    $success = $postModel->deleteComment($comment_id);
                    
                    if ($success) {
                        $_SESSION['comment_success'] = 'Commentaire supprimé avec succès!';
                    } else {
                        $_SESSION['comment_errors'] = ['Erreur lors de la suppression'];
                    }
                } else {
                    $_SESSION['comment_errors'] = ['Non autorisé à supprimer ce commentaire'];
                }
            } catch (PDOException $e) {
                $_SESSION['comment_errors'] = ['Erreur lors de la suppression du commentaire.'];
            }
            
            header("Location: control.php?action=view&id=" . $post_id);
            exit;
        }
        break;
}
?>