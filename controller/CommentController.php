<?php
require_once __DIR__ . '/../Model/CommentModel.php';

class CommentController {
    private $commentModel;

    public function __construct(){
        $this->commentModel = new CommentModel();
    }

    public function addComment($post_id,$user_id,$contenu){
        return $this->commentModel->addComment($post_id,$user_id,$contenu);
    }

    public function editComment($comment_id, $user_id, $contenu) {
        return $this->commentModel->editComment($comment_id, $user_id, $contenu);
    }

    public function deleteComment($id){ return $this->commentModel->deleteComment($id); }
}

?>
