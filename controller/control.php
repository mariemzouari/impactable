<?php
session_start();

if (headers_sent()) {
    exit;
}

require_once '../Model/post.php';
require_once '../Model/auth.php';
require_once '../Model/comment.php';
require_once '../Model/like.php';

class Control {
    private $postModel;
    private $authModel;
    private $commentModel;
    private $likeModel;

    public function __construct() {
        $this->postModel = new Post();
        $this->authModel = new Auth();
        $this->commentModel = new Comment();
        $this->likeModel = new Like();
    }

    public function invoke() {
        $action = $_GET['action'] ?? 'list';

        switch ($action) {
            case 'register':
                $this->handleRegister();
                break;
            case 'login':
                $this->handleLogin();
                break;
            case 'logout':
                $this->handleLogout();
                break;
            case 'create':
                $this->handleCreatePost();
                break;
            case 'edit':
                $this->handleEditPost();
                break;
            case 'delete':
                $this->handleDeletePost();
                break;
            case 'view':
                $this->handleViewPost();
                break;
            case 'add_comment':
                $this->handleAddComment();
                break;
            case 'edit_comment':
                $this->handleEditComment();
                break;
            case 'delete_comment':
                $this->handleDeleteComment();
                break;
            case 'admin':
                $this->handleAdmin();
                break;
            case 'admin_comments':
                $this->handleAdminComments();
                break;
            case 'delete_comment_admin':
                $this->handleDeleteCommentAdmin();
                break;
            case 'search_comments':
                $this->handleSearchComments();
                break;
            case 'toggle_like':
                $this->handleToggleLike();
                break;
            case 'list':
            default:
                $this->handleListPosts();
                break;
        }
    }

    private function handleToggleLike() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Vous devez être connecté pour liker un post'
            ]);
            exit;
        }

        $post_id = $_POST['post_id'] ?? 0;
        $user_id = $_SESSION['user_id'];

        if ($post_id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Post invalide'
            ]);
            exit;
        }

        $result = $this->likeModel->toggleLike($post_id, $user_id);
        
        if ($result['success']) {
            $likes_count = $this->likeModel->getLikesCount($post_id);
            $user_liked = $this->likeModel->hasUserLiked($post_id, $user_id);
            
            echo json_encode([
                'success' => true,
                'likes_count' => $likes_count,
                'user_liked' => $user_liked,
                'message' => $result['message']
            ]);
        } else {
            echo json_encode($result);
        }
        exit;
    }

    private function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $email = $_POST['email'] ?? '';
            $mot_de_passe = $_POST['mot_de_passe'] ?? '';
            $confirmer_mot_de_passe = $_POST['confirmer_mot_de_passe'] ?? '';
            $telephone = $_POST['telephone'] ?? '';
            $date_naissance = $_POST['date_naissance'] ?? null;
            $handicap = $_POST['handicap'] ?? '';

            $errors = $this->authModel->validateRegistration($nom, $prenom, $email, $mot_de_passe, $confirmer_mot_de_passe);
            
            if (empty($errors)) {
                $result = $this->authModel->register($nom, $prenom, $email, $mot_de_passe, $telephone, $date_naissance, $handicap);
                
                if ($result['success']) {
                    $_SESSION['message'] = $result['message'];
                    header('Location: ../View/login.php');
                    exit;
                } else {
                    $errors[] = $result['message'];
                }
            }

            $old_data = $_POST;
            include '../View/register.php';
        } else {
            include '../View/register.php';
        }
    }

    private function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $mot_de_passe = $_POST['mot_de_passe'] ?? '';

            $errors = $this->authModel->validateLogin($email, $mot_de_passe);
            
            if (empty($errors)) {
                $result = $this->authModel->login($email, $mot_de_passe);
                
                if ($result['success']) {
                    header('Location: ../controller/control.php?action=list');
                    exit;
                } else {
                    $errors[] = $result['message'];
                }
            }

            $old_data = $_POST;
            include '../View/login.php';
        } else {
            include '../View/login.php';
        }
    }

    private function handleLogout() {
        session_destroy();
        header('Location: ../controller/control.php?action=list');
        exit;
    }

    private function handleCreatePost() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0) {
            header('Location: ../View/login.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim($_POST['titre'] ?? '');
            $contenu = trim($_POST['contenu'] ?? '');
            $categorie = $_POST['categorie'] ?? '';
            $piece_jointe = '';

            if (!empty($_FILES['piece_jointe']['name'])) {
                $uploadDir = '../uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileName = uniqid() . '_' . $_FILES['piece_jointe']['name'];
                $uploadFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['piece_jointe']['tmp_name'], $uploadFile)) {
                    $piece_jointe = 'uploads/' . $fileName;
                }
            }

            $errors = $this->postModel->validateStrict($titre, $contenu, $categorie);
            
            if (empty($errors)) {
                $contentErrors = $this->validateContentQuality($contenu);
                $errors = array_merge($errors, $contentErrors);
            }
            
            if (empty($errors)) {
                if ($this->postModel->create($_SESSION['user_id'], $titre, $categorie, $contenu, $piece_jointe)) {
                    header('Location: ../controller/control.php?action=list');
                    exit;
                } else {
                    $errors[] = "Erreur lors de la création du post";
                }
            }

            $old_data = $_POST;
            include '../View/ajout.php';
        } else {
            include '../View/ajout.php';
        }
    }

    private function handleListPosts() {
        $category = $_GET['category'] ?? '';
        
        // RÉCUPÉRER TOUS LES POSTS
        if (!empty($category)) {
            $posts = $this->postModel->filterByCategory($category);
        } else {
            $posts = $this->postModel->all();
        }
        
        // Enrichir avec les likes
        $user_id = $_SESSION['user_id'] ?? 0;
        $posts = $this->likeModel->enrichPostsWithLikes($posts, $user_id);
        
        // Compter les commentaires
        foreach ($posts as &$post) {
            $post['comments_count'] = $this->commentModel->countCommentsByPost($post['Id_post']);
        }
        
        include '../View/forum.php';
    }

    private function handleViewPost() {
        $id = $_GET['id'] ?? 0;
        $post = $this->postModel->findById($id);
        
        if (!$post) {
            header('Location: ../controller/control.php?action=list');
            exit;
        }

        $user_id = $_SESSION['user_id'] ?? 0;
        $post['likes_count'] = $this->likeModel->getLikesCount($id);
        $post['user_liked'] = $user_id > 0 ? $this->likeModel->hasUserLiked($id, $user_id) : false;

        $editing_comment_id = $_POST['edit_comment_id'] ?? 0;
        $comments = $this->commentModel->getCommentsByPost($id);
        
        include '../View/view.php';
    }

    private function handleAddComment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post_id = $_POST['post_id'] ?? 0;
            $contenu = trim($_POST['contenu'] ?? '');

            if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0) {
                $_SESSION['comment_errors'] = ['Vous devez être connecté pour commenter'];
                header("Location: ../controller/control.php?action=view&id=$post_id");
                exit;
            }

            $errors = $this->postModel->validateCommentStrict($contenu);
            
            if (empty($errors)) {
                $contentErrors = $this->validateCommentQuality($contenu);
                $errors = array_merge($errors, $contentErrors);
            }
            
            if (empty($errors)) {
                if ($this->commentModel->addComment($post_id, $_SESSION['user_id'], $contenu)) {
                    $_SESSION['comment_success'] = 'Commentaire ajouté avec succès';
                } else {
                    $_SESSION['comment_errors'] = ['Erreur lors de l\'ajout du commentaire'];
                }
            } else {
                $_SESSION['comment_errors'] = $errors;
                $_SESSION['old_comment'] = $contenu;
            }

            header("Location: ../controller/control.php?action=view&id=$post_id");
            exit;
        }
    }

    private function handleEditComment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $comment_id = $_POST['comment_id'] ?? 0;
            $post_id = $_POST['post_id'] ?? 0;
            $contenu = trim($_POST['contenu'] ?? '');

            $comment = $this->postModel->getCommentById($comment_id);
            if (!$comment || $comment['Id_utilisateur'] != $_SESSION['user_id']) {
                $_SESSION['comment_errors'] = ['Vous n\'avez pas la permission de modifier ce commentaire'];
                header("Location: ../controller/control.php?action=view&id=$post_id");
                exit;
            }

            $errors = $this->postModel->validateCommentStrict($contenu);
            
            if (empty($errors)) {
                $contentErrors = $this->validateCommentQuality($contenu);
                $errors = array_merge($errors, $contentErrors);
            }
            
            if (empty($errors)) {
                if ($this->postModel->updateComment($comment_id, $contenu)) {
                    $_SESSION['comment_success'] = 'Commentaire modifié avec succès';
                } else {
                    $_SESSION['comment_errors'] = ['Erreur lors de la modification du commentaire'];
                }
            } else {
                $_SESSION['comment_errors'] = $errors;
            }

            header("Location: ../controller/control.php?action=view&id=$post_id");
            exit;
        }
    }

    private function handleDeleteComment() {
        $comment_id = $_GET['id'] ?? 0;
        $post_id = $_GET['post_id'] ?? 0;

        $comment = $this->postModel->getCommentById($comment_id);
        if ($comment && ($comment['Id_utilisateur'] == $_SESSION['user_id'] || $_SESSION['is_admin'])) {
            if ($this->postModel->deleteComment($comment_id)) {
                $_SESSION['comment_success'] = 'Commentaire supprimé avec succès';
            } else {
                $_SESSION['comment_errors'] = ['Erreur lors de la suppression du commentaire'];
            }
        } else {
            $_SESSION['comment_errors'] = ['Vous n\'avez pas la permission de supprimer ce commentaire'];
        }

        header("Location: ../controller/control.php?action=view&id=$post_id");
        exit;
    }

    private function handleEditPost() {
        $id = $_GET['id'] ?? 0;
        $post = $this->postModel->findById($id);
        
        if (!$post) {
            header('Location: ../controller/control.php?action=list');
            exit;
        }

        if ($post['Id_utilisateur'] != $_SESSION['user_id'] && !$_SESSION['is_admin']) {
            header('Location: ../controller/control.php?action=list');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim($_POST['titre'] ?? '');
            $contenu = trim($_POST['contenu'] ?? '');
            $categorie = $_POST['categorie'] ?? '';

            $errors = $this->postModel->validateStrict($titre, $contenu, $categorie);
            
            if (empty($errors)) {
                $contentErrors = $this->validateContentQuality($contenu);
                $errors = array_merge($errors, $contentErrors);
            }
            
            if (empty($errors)) {
                if ($this->postModel->update($id, $titre, $categorie, $contenu)) {
                    $from_admin = isset($_GET['from']) && $_GET['from'] == 'admin';
                    if ($from_admin) {
                        header('Location: ../View/admin.php');
                    } else {
                        header('Location: ../controller/control.php?action=list');
                    }
                    exit;
                } else {
                    $errors[] = "Erreur lors de la modification du post";
                }
            }

            $error_message = $errors;
            include '../View/edit.php';
        } else {
            include '../View/edit.php';
        }
    }

    private function handleDeletePost() {
        $id = $_GET['id'] ?? 0;
        $post = $this->postModel->findById($id);
        
        if ($post && ($post['Id_utilisateur'] == $_SESSION['user_id'] || $_SESSION['is_admin'])) {
            $this->postModel->delete($id);
        }

        $from_admin = isset($_GET['from']) && $_GET['from'] == 'admin';
        if ($from_admin) {
            header('Location: ../View/admin.php');
        } else {
            header('Location: ../controller/control.php?action=list');
        }
        exit;
    }

    private function handleAdmin() {
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: ../controller/control.php?action=list');
            exit;
        }

        $posts = $this->postModel->all();
        
        $user_id = $_SESSION['user_id'] ?? 0;
        $posts = $this->likeModel->enrichPostsWithLikes($posts, $user_id);
        
        $totalPosts = $this->postModel->countPosts();
        $totalUsers = $this->postModel->countUsers();
        $totalComments = $this->postModel->countComments();

        include '../View/admin.php';
    }

    private function handleAdminComments() {
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: ../controller/control.php?action=list');
            exit;
        }

        $comments = $this->commentModel->getAllCommentsWithPosts();
        $totalComments = $this->commentModel->countComments();

        include '../View/admin_comments.php';
    }

    private function handleDeleteCommentAdmin() {
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: ../controller/control.php?action=list');
            exit;
        }

        $comment_id = $_GET['id'] ?? 0;
        
        if ($this->commentModel->deleteComment($comment_id)) {
            $_SESSION['admin_message'] = 'Commentaire supprimé avec succès';
        } else {
            $_SESSION['admin_error'] = 'Erreur lors de la suppression du commentaire';
        }

        header('Location: ../controller/control.php?action=admin_comments');
        exit;
    }

    private function handleSearchComments() {
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: ../controller/control.php?action=list');
            exit;
        }

        $posts = $this->postModel->all();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post_id = $_POST['post_id'] ?? 0;
            $selected_post = $this->postModel->findById($post_id);
            $comments = $this->commentModel->getCommentsByPost($post_id);
        }

        include '../View/searchComments.php';
    }

    private function validateContentQuality($text) {
        $errors = [];
        
        $bannedWords = ['spam', 'arnaque', 'hack', 'pirate'];
        foreach ($bannedWords as $word) {
            if (stripos($text, $word) !== false) {
                $errors[] = "Votre contenu contient des termes inappropriés";
                break;
            }
        }
        
        if (preg_match('/(.)\1{4,}/', $text)) {
            $errors[] = "Évitez la répétition excessive de caractères";
        }
        
        if (strlen($text) > 10 && $text === strtoupper($text)) {
            $errors[] = "Évitez d'écrire uniquement en majuscules";
        }
        
        return $errors;
    }

    private function validateCommentQuality($text) {
        return $this->validateContentQuality($text);
    }
}

$control = new Control();
$control->invoke();
?>