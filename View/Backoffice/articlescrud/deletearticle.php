<?php
include '../../../controller/ArticleController.php';

$articleC = new ArticleController();

// Vérifier si l'ID existe
if (isset($_GET["id"]) && !empty($_GET["id"])) {

    $articleC->deleteArticle($_GET["id"]);

    // Redirection vers la liste
    header('Location: articlelist.php');
    exit;

} else {
    echo "Error: missing article ID.";
}
?>

