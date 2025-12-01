<?php
header('Content-Type: application/json');

require_once(__DIR__ . '/../../controller/ArticleController.php');
require_once(__DIR__ . '/../../MODEL/Article.php');

$response = ['success' => false, 'message' => ''];

// Vérifier que tous les champs requis sont présents
if (
    !isset($_POST['titre']) || empty(trim($_POST['titre'])) ||
    !isset($_POST['auteur']) || empty(trim($_POST['auteur'])) ||
    !isset($_POST['date_creation']) || empty($_POST['date_creation']) ||
    !isset($_POST['categorie']) || empty(trim($_POST['categorie'])) ||
    !isset($_POST['contenu']) || empty(trim($_POST['contenu'])) ||
    !isset($_POST['auteur_id']) || empty($_POST['auteur_id'])
) {
    $response['message'] = 'Veuillez remplir tous les champs obligatoires.';
    echo json_encode($response);
    exit;
}

// Créer le dossier uploads s'il n'existe pas
$uploadDir = __DIR__ . '/../../uploads/articles/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Gestion de l'upload d'image
$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['image'];
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('article_', true) . '.' . $extension;
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Chemin relatif pour la base de données
            $imagePath = 'uploads/articles/' . $fileName;
        } else {
            $response['message'] = "Erreur lors de l'upload de l'image.";
            echo json_encode($response);
            exit;
        }
    } else {
        $response['message'] = "Format d'image non supporté ou fichier trop volumineux (max 5MB).";
        echo json_encode($response);
        exit;
    }
}

// Convertir les tags en tableau
$tags = !empty($_POST['tags']) ? explode(",", $_POST['tags']) : [];
$tags = array_map('trim', $tags);
$tags = array_filter($tags);

try {
    // Créer l'objet Article
    $article = new Article(
        null,
        trim($_POST['titre']),
        trim($_POST['auteur']),
        $_POST['date_creation'],
        trim($_POST['categorie']),
        trim($_POST['contenu']),
        $imagePath,
        (int)$_POST['auteur_id'],
        !empty($_POST['lieu']) ? trim($_POST['lieu']) : null,
        $tags
    );

    // Ajouter l'article (statut 'brouillon' par défaut, l'admin devra l'approuver)
    $articleC = new ArticleController();
    $articleC->addArticle($article, 'brouillon');

    $response['success'] = true;
    $response['message'] = 'Article soumis avec succès ! Il sera publié après validation par l\'administrateur.';
    
} catch (PDOException $e) {
    $response['message'] = 'Erreur de base de données: ' . $e->getMessage();
    $response['debug'] = 'Vérifiez que la table "articles" existe dans la base de données.';
} catch (Exception $e) {
    $response['message'] = 'Erreur lors de l\'ajout de l\'article: ' . $e->getMessage();
}

echo json_encode($response);
?>

