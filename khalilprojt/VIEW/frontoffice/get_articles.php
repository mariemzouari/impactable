<?php
header('Content-Type: application/json');

require_once(__DIR__ . '/../../controller/ArticleController.php');

$articleC = new ArticleController();

try {
    // Récupérer uniquement les articles publiés
    $articles = $articleC->listArticlesByStatus('publie');
    
    $articlesArray = [];
    
    if ($articles instanceof PDOStatement) {
        $articlesArray = $articles->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($articles instanceof Traversable) {
        $articlesArray = iterator_to_array($articles);
    }
    
    // Décoder les tags JSON pour chaque article
    foreach ($articlesArray as &$article) {
        $article['tags'] = json_decode($article['tags'] ?? '[]', true) ?? [];
    }
    
    echo json_encode([
        'success' => true,
        'articles' => $articlesArray
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des articles: ' . $e->getMessage()
    ]);
}
?>

