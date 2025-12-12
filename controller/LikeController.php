<?php
require_once __DIR__ . '/../Model/LikeModel.php';

class LikeController {
    private $likeModel;

    public function __construct(){
        $this->likeModel = new LikeModel();
    }

    public function toggle($post_id,$user_id){
        return $this->likeModel->toggleLike($post_id,$user_id);
    }
}

?>
