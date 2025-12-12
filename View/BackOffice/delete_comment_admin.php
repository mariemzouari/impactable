<?php
require_once __DIR__ . '/../../Model/CommentModel.php';

$commentModel = new CommentModel();

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: index.php?action=list');
    exit;
}

$comment_id = $_GET['id'] ?? 0;

if ($commentModel->deleteComment($comment_id)) {
    $_SESSION['admin_message'] = 'Commentaire supprimé avec succès';
} else {
    $_SESSION['admin_error'] = 'Erreur lors de la suppression du commentaire';
}

header('Location: index.php?action=admin_comments');
exit;
?>
