<?php
require_once __DIR__ . '/../../Model/CommentModel.php';

$commentModel = new CommentModel();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0) {
    header('Location: index.php?action=login');
    exit;
}

$comment_id = $_POST['comment_id'] ?? 0;
$contenu = $_POST['contenu'] ?? '';
$post_id = $_POST['post_id'] ?? 0;

$comment = $commentModel->getCommentById($comment_id);
$errors = [];

if ($comment && ($comment['Id_utilisateur'] == $_SESSION['user_id'] || ($_SESSION['is_admin'] ?? false))) {
    if (empty(trim($contenu))) {
        $errors[] = "Le commentaire ne peut pas être vide";
    } elseif (strlen(trim($contenu)) < 2) {
        $errors[] = "Le commentaire doit contenir au moins 2 caractères";
    } elseif (strlen($contenu) > 1000) {
        $errors[] = "Le commentaire ne peut pas dépasser 1000 caractères";
    }

    $bannedWords = ['spam', 'arnaque', 'hack', 'pirate'];
    foreach ($bannedWords as $word) {
        if (stripos($contenu, $word) !== false) {
            $errors[] = "Votre contenu contient des termes inappropriés";
            break;
        }
    }

    if (empty($errors)) {
        require_once __DIR__ . '/../../config.php';
        $pdo = config::getConnexion();
        $sql = "UPDATE commentaire SET contenu = ?, date_modification = NOW() WHERE Id_commentaire = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$contenu, $comment_id])) {
            $_SESSION['comment_success'] = 'Commentaire modifié avec succès';
        } else {
            $errors[] = 'Erreur lors de la modification du commentaire';
        }
    }
} else {
    $errors[] = 'Vous n\'êtes pas autorisé à modifier ce commentaire';
}

$_SESSION['comment_errors'] = $errors;
$_SESSION['old_comment'] = $contenu;

header('Location: index.php?action=view&id=' . $post_id);
exit;
?>
