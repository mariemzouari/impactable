<?php
require_once __DIR__ . '/../../Model/CommentModel.php';

$commentModel = new CommentModel();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0) {
    header('Location: index.php?action=login');
    exit;
}

$post_id = $_POST['post_id'] ?? 0;
$contenu = $_POST['contenu'] ?? '';
$media_url = $_POST['media_url'] ?? '';

$errors = [];

if (empty(trim($contenu)) && empty($media_url)) {
    $errors[] = "Le commentaire ou un média est obligatoire";
} elseif (!empty($contenu) && strlen(trim($contenu)) < 2) {
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

if (!empty($media_url)) {
    $isUrl = filter_var($media_url, FILTER_VALIDATE_URL);
    $isLocal = preg_match('#^uploads\/gifs\/[A-Za-z0-9_\-\.]+$#', $media_url);
    if (!$isUrl && !$isLocal) {
        $errors[] = "L'URL du média est invalide";
    }
}

if (empty($errors)) {
    $added = $commentModel->addComment($post_id, $_SESSION['user_id'], $contenu, $media_url);
    if ($added) {
        $_SESSION['comment_success'] = 'Commentaire ajouté avec succès';
        $_SESSION['old_comment'] = ''; 
    } else {
        $errors[] = 'Erreur lors de l\'ajout du commentaire';
        $_SESSION['old_comment'] = $contenu; 
    }
} else {
    $_SESSION['old_comment'] = $contenu; 
}

$_SESSION['comment_errors'] = $errors;

header('Location: index.php?action=view&id=' . $post_id);
exit;
?>
