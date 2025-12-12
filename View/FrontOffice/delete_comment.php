<?php
require_once __DIR__ . '/../../Model/CommentModel.php';

$commentModel = new CommentModel();

$comment_id = $_GET['id'] ?? 0;
$post_id = $_GET['post_id'] ?? 0;

$comment = $commentModel->getCommentById($comment_id);
$errors = [];

if ($comment && ($comment['Id_utilisateur'] == $_SESSION['user_id'] || ($_SESSION['is_admin'] ?? false))) {
    $commentModel->deleteComment($comment_id);
    $_SESSION['comment_success'] = 'Commentaire supprimé avec succès';
} else {
    $errors[] = 'Vous n\'êtes pas autorisé à supprimer ce commentaire';
    $_SESSION['comment_errors'] = $errors;
}

header('Location: index.php?action=view&id=' . $post_id);
exit;
?>
