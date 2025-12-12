<?php
require_once __DIR__ . '/../../Model/PostModel.php';

$postModel = new PostModel();

$id = $_GET['id'] ?? 0;
$post = $postModel->findById($id);

if ($post && ($post['Id_utilisateur'] == $_SESSION['user_id'] || ($_SESSION['is_admin'] ?? false))) {
    $postModel->delete($id);
}

$from_admin = isset($_GET['from']) && $_GET['from'] == 'admin';
if ($from_admin) {
    header('Location: index.php?action=admin');
} else {
    header('Location: index.php?action=list');
}
exit;
?>
