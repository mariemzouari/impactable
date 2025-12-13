<?php
require_once __DIR__ . '/../../Model/LikeModel.php';

header('Content-Type: application/json');

$likeModel = new LikeModel();

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

$result = $likeModel->toggleLike($post_id, $user_id);

if ($result['success']) {
    $likes_count = $likeModel->getLikesCount($post_id);
    $user_liked = $likeModel->hasUserLiked($post_id, $user_id);
    
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
?>
