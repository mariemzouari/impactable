<?php
require_once __DIR__ . '/../Model/PostModel.php';
require_once __DIR__ . '/../Model/LikeModel.php';

class PostController {
    private $postModel;
    private $likeModel;

    public function __construct(){
        $this->postModel = new PostModel();
        $this->likeModel = new LikeModel();
    }

    public function list($search='',$category=''){
        if ($search) $posts = $this->postModel->search($search);
        elseif ($category) $posts = $this->postModel->filterByCategory($category);
        else $posts = $this->postModel->all();

        $user_id = $_SESSION['user_id'] ?? 0;
        $posts = $this->likeModel->enrichPostsWithLikes($posts,$user_id);

        include __DIR__ . '/../View/forum.php';
    }

    // Additional methods can be added and used by the front controller
}

?>
