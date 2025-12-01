<?php
include '../../../controller/ArticleController.php';

$articleC = new ArticleController();

if (isset($_POST["id"]) && !empty($_POST["id"])) {
    $articleC->approveArticle($_POST["id"]);
    header('Location: articlelist.php');
    exit;
} else {
    echo "Error: missing article ID.";
}
?>

