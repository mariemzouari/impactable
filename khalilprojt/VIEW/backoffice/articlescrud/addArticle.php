<?php
include '../../../controller/ArticleController.php';
require_once __DIR__ . '/../../../MODEL/Article.php';

$error = "";
$articleC = new ArticleController();

// Créer le dossier uploads s'il n'existe pas
$uploadDir = __DIR__ . '/uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// ======= Vérification formulaire =======
if (
    isset($_POST["titre"]) &&
    isset($_POST["auteur"]) &&
    isset($_POST["date_creation"]) &&
    isset($_POST["categorie"]) &&
    isset($_POST["contenu"]) &&
    isset($_POST["auteur_id"])
) {
    if (
        !empty($_POST["titre"]) &&
        !empty($_POST["auteur"]) &&
        !empty($_POST["date_creation"]) &&
        !empty($_POST["categorie"]) &&
        !empty($_POST["contenu"]) &&
        !empty($_POST["auteur_id"])
    ) {

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
                    $imagePath = 'VIEW/backoffice/articlescrud/uploads/' . $fileName;
                } else {
                    $error = "Erreur lors de l'upload de l'image.";
                }
            } else {
                $error = "Format d'image non supporté ou fichier trop volumineux (max 5MB).";
            }
        } elseif (isset($_POST['image_url']) && !empty($_POST['image_url'])) {
            // Fallback: utiliser l'URL si fournie
            $imagePath = $_POST['image_url'];
        }

        // conversion tableau JSON pour tags
        $tags = !empty($_POST['tags']) ? explode(",", $_POST['tags']) : [];

        if (empty($error)) {
            // Création de l'objet Article
            $article = new Article(
                null,
                $_POST['titre'],
                $_POST['auteur'],
                $_POST['date_creation'],
                $_POST['categorie'],
                $_POST['contenu'],
                $imagePath,
                (int)$_POST['auteur_id'],
                $_POST['lieu'] ?? null,
                $tags
            );

            $articleC->addArticle($article);

            header('Location: articlelist.php');
            exit;
        }
    } else {
        $error = "Missing information";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Article</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css" />
    <style>
        .admin-body {
            background: linear-gradient(135deg, #0a0a1a 0%, #1a0a2e 100%);
            min-height: 100vh;
            color: #fff;
            font-family: 'Inter', sans-serif;
        }
        .admin-header {
            background: rgba(5, 0, 20, 0.9);
            border-bottom: 1px solid rgba(255, 0, 199, 0.2);
            padding: 20px 0;
        }
        .admin-header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-logo {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #00ffea;
        }
        .admin-nav {
            display: flex;
            gap: 20px;
        }
        .nav-link {
            color: #fff;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(255, 0, 199, 0.2);
            color: #ff00c7;
        }
        .admin-main {
            padding: 40px 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .form-card {
            background: rgba(5, 0, 20, 0.9);
            border: 1px solid rgba(255, 0, 199, 0.2);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.45);
        }
        .form-section {
            margin-bottom: 30px;
        }
        .form-section-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #00ffea;
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #fff;
        }
        .neon-field {
            width: 100%;
            padding: 12px 16px;
            background: rgba(5, 0, 20, 0.75);
            border: 1px solid rgba(255, 0, 199, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .neon-field:focus {
            outline: none;
            border-color: #ff00c7;
            box-shadow: 0 0 18px rgba(255,0,199,0.35);
        }
        textarea.neon-field {
            min-height: 140px;
            resize: vertical;
        }
        .stack-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .cta-zone {
            margin-top: 30px;
            display: flex;
            justify-content: center;
        }
        .btn-submit {
            padding: 12px 36px;
            border-radius: 999px;
            border: none;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(120deg, #ff00c7, #7c00ff, #00ffea);
            background-size: 200% 200%;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            background-position: 100% 0;
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.45);
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-danger {
            background: rgba(255, 0, 0, 0.2);
            border: 1px solid rgba(255, 0, 0, 0.5);
            color: #ff6b6b;
        }
    </style>
</head>

<body class="admin-body">
<header class="admin-header">
    <div class="container">
        <div class="admin-logo">
            Admin Panel
        </div>
        <nav class="admin-nav">
            <a href="../admin_dashboard.php" class="nav-link">Dashboard</a>
            <a href="articlelist.php" class="nav-link">Articles</a>
            <a href="addArticle.php" class="nav-link active">Ajouter Article</a>
        </nav>
    </div>
</header>

<main class="admin-main">
    <div class="container">

        <section class="form-card">
            <h1 style="font-family: 'Orbitron', sans-serif; margin-bottom: 20px;">Ajouter un nouvel article</h1>
            
            <?php if (!empty($error)) { ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php } ?>

            <form action="" method="POST" enctype="multipart/form-data" class="form-neon">

                <div class="form-section">
                    <p class="form-section-title">Infos principales</p>
                    <div class="stack-2">
                        <div>
                            <label class="form-label">Titre de l'article</label>
                            <input type="text" class="neon-field" name="titre" id="titre" required>
                        </div>
                        <div>
                            <label class="form-label">Auteur</label>
                            <input type="text" class="neon-field" name="auteur" id="auteur" required>
                        </div>
                        <div>
                            <label class="form-label">Date de création</label>
                            <input type="date" class="neon-field" name="date_creation" id="date_creation" required>
                        </div>
                        <div>
                            <label class="form-label">Catégorie</label>
                            <select class="neon-field" name="categorie" id="categorie" required>
                                <option value="">-- Choisir --</option>
                                <option>Actualité</option>
                                <option>Tutoriel</option>
                                <option>Review</option>
                                <option>Guide</option>
                                <option>Autre</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <p class="form-section-title">Contenu</p>
                    <div>
                        <label class="form-label">Contenu de l'article</label>
                        <textarea class="neon-field" name="contenu" id="contenu" required></textarea>
                    </div>
                    <div style="margin-top: 20px;">
                        <label class="form-label">Image de l'article</label>
                        <input type="file" class="neon-field" name="image" id="image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                        <small style="color: #888; font-size: 0.85rem; display: block; margin-top: 6px;">
                            Formats acceptés: JPG, PNG, GIF, WebP (max 5MB)
                        </small>
                        <input type="hidden" name="image_url" id="image_url" value="">
                    </div>
                </div>

                <div class="form-section">
                    <p class="form-section-title">Meta & distribution</p>
                    <div class="stack-2">
                        <div>
                            <label class="form-label">Auteur ID</label>
                            <input type="number" class="neon-field" name="auteur_id" id="auteur_id" required>
                        </div>
                        <div>
                            <label class="form-label">Lieu</label>
                            <input type="text" class="neon-field" name="lieu">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <p class="form-section-title">Tags</p>
                    <div>
                        <label class="form-label">Tags (séparer par des virgules)</label>
                        <input type="text" class="neon-field" name="tags" placeholder="Technologie, Gaming, News">
                        <small style="color: #888; font-size: 0.85rem; display: block; margin-top: 6px;">
                            Mots-clés utilisés pour le SEO interne.
                        </small>
                    </div>
                </div>

                <div class="cta-zone">
                    <button type="submit" class="btn-submit">Ajouter l'article</button>
                </div>

            </form>
        </section>

    </div>
</main>

<footer style="text-align: center; padding: 20px; color: #888;">
    Admin Panel • Gestion des Articles
</footer>

</body>
</html>

