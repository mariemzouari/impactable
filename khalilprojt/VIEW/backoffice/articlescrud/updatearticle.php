<?php
include '../../../controller/ArticleController.php';
require_once __DIR__ . '/../../../MODEL/Article.php';

$error = '';
$article = null;
$tagsValue = '';
$articleC = new ArticleController();

// Récupérer les données de l'article (from GET or POST)
$articleId = $_GET['id'] ?? $_POST['id'] ?? null;

if ($articleId) {
    $article = $articleC->showArticle($articleId);
    if ($article) {
        $tagsValue = implode(", ", json_decode($article['tags'] ?? '[]', true) ?? []);
    }
}

// Créer le dossier uploads s'il n'existe pas
$uploadDir = __DIR__ . '/uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Mise à jour
if (
    isset($_POST['id'], $_POST['titre'], $_POST['auteur'], $_POST['date_creation'], $_POST['categorie'], $_POST['contenu'], $_POST['auteur_id'])
) {
    if (
        !empty($_POST['id']) && !empty($_POST['titre']) && !empty($_POST['auteur']) &&
        !empty($_POST['date_creation']) && !empty($_POST['categorie']) && !empty($_POST['contenu']) &&
        !empty($_POST['auteur_id'])
    ) {

        // Gestion de l'upload d'image
        $imagePath = $article['image'] ?? null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fileName = uniqid('article_', true) . '.' . $extension;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $imagePath = 'VIEW/backoffice/articlescrud/uploads/' . $fileName;
                } else {
                    $error = "Erreur lors de l'upload de l'image.";
                }
            } else {
                $error = "Format d'image non supporté ou fichier trop volumineux (max 5MB).";
            }
        } elseif (isset($_POST['image_url']) && !empty($_POST['image_url'])) {
            $imagePath = $_POST['image_url'];
        }

        // Convertir les tags
        $tags = !empty($_POST['tags']) ? explode(",", $_POST['tags']) : [];

        if (empty($error)) {
            $articleObj = new Article(
                $_POST['id'],
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

            $articleC->updateArticle($articleObj, $_POST['id']);

            header('Location: articlelist.php');
            exit();
        }
    } else {
        $error = 'Veuillez remplir tous les champs requis.';
    }
} elseif ($article) {
    $tagsValue = implode(", ", json_decode($article['tags'] ?? '[]', true) ?? []);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Modifier Article</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css" />
    <style>
        :root {
            --primary: #ff00c7;
            --cyan: #00ffea;
            --bg: #0a0a1a;
            --bg-card: rgba(5, 0, 20, 0.9);
            --border: rgba(255, 0, 199, 0.2);
            --text: #fff;
            --text-light: #aaa;
            --radius: 16px;
            --radius-sm: 8px;
            --transition: all 0.3s ease;
        }
        .admin-body {
            background: linear-gradient(135deg, #0a0a1a 0%, #1a0a2e 100%);
            min-height: 100vh;
            color: var(--text);
            font-family: 'Inter', sans-serif;
        }
        .admin-header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
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
            color: var(--cyan);
        }
        .admin-nav {
            display: flex;
            gap: 20px;
        }
        .nav-link {
            color: var(--text);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: var(--transition);
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(255, 0, 199, 0.2);
            color: var(--primary);
        }
        .admin-main {
            padding: 40px 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
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
            color: var(--cyan);
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text);
        }
        .neon-field {
            width: 100%;
            padding: 12px 16px;
            background: rgba(5, 0, 20, 0.75);
            border: 1px solid rgba(255, 0, 199, 0.2);
            border-radius: var(--radius-sm);
            color: var(--text);
            font-size: 1rem;
            transition: var(--transition);
        }
        .neon-field:focus {
            outline: none;
            border-color: var(--primary);
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
            gap: 16px;
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
            transition: var(--transition);
        }
        .btn-submit:hover {
            background-position: 100% 0;
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.45);
        }
        .btn-ghost {
            padding: 12px 24px;
            border: 1px solid var(--border);
            border-radius: 999px;
            background: transparent;
            color: var(--text);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-ghost:hover {
            border-color: var(--primary);
            box-shadow: 0 0 20px rgba(255,0,199,0.35);
        }
        .alert {
            padding: 15px;
            border-radius: var(--radius-sm);
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
            <a href="addArticle.php" class="nav-link">Ajouter Article</a>
        </nav>
    </div>
</header>

<main class="admin-main">
    <div class="container">

        <section class="form-card">
            <h1 style="font-family: 'Orbitron', sans-serif; margin-bottom: 20px;">Modifier l'article</h1>
            
            <?php if (!empty($error)) { ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php } ?>

            <?php if ($article): ?>
            <form action="" method="POST" enctype="multipart/form-data" class="form-neon">
                <input type="hidden" name="id" value="<?= $article['id'] ?>">

                <div class="form-section">
                    <p class="form-section-title">Infos principales</p>
                    <div class="stack-2">
                        <div>
                            <label class="form-label">Titre de l'article</label>
                            <input type="text" class="neon-field" name="titre" id="titre" value="<?= htmlspecialchars($article['titre'] ?? '') ?>" required>
                        </div>
                        <div>
                            <label class="form-label">Auteur</label>
                            <input type="text" class="neon-field" name="auteur" id="auteur" value="<?= htmlspecialchars($article['auteur'] ?? '') ?>" required>
                        </div>
                        <div>
                            <label class="form-label">Date de création</label>
                            <input type="date" class="neon-field" name="date_creation" id="date_creation" value="<?= htmlspecialchars($article['date_creation'] ?? '') ?>" required>
                        </div>
                        <div>
                            <label class="form-label">Catégorie</label>
                            <select class="neon-field" name="categorie" id="categorie" required>
                                <option value="">-- Choisir --</option>
                                <option value="Actualité" <?= ($article['categorie'] ?? '') === 'Actualité' ? 'selected' : '' ?>>Actualité</option>
                                <option value="Tutoriel" <?= ($article['categorie'] ?? '') === 'Tutoriel' ? 'selected' : '' ?>>Tutoriel</option>
                                <option value="Review" <?= ($article['categorie'] ?? '') === 'Review' ? 'selected' : '' ?>>Review</option>
                                <option value="Guide" <?= ($article['categorie'] ?? '') === 'Guide' ? 'selected' : '' ?>>Guide</option>
                                <option value="Autre" <?= ($article['categorie'] ?? '') === 'Autre' ? 'selected' : '' ?>>Autre</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <p class="form-section-title">Contenu</p>
                    <div>
                        <label class="form-label">Contenu de l'article</label>
                        <textarea class="neon-field" name="contenu" id="contenu" required><?= htmlspecialchars($article['contenu'] ?? '') ?></textarea>
                    </div>
                    <div style="margin-top: 20px;">
                        <label class="form-label">Image de l'article</label>
                        <?php if (!empty($article['image'])): ?>
                            <div style="margin-bottom: 10px;">
                                <img src="../../../<?= htmlspecialchars($article['image']) ?>" alt="Image actuelle" style="max-width: 200px; border-radius: 8px; border: 1px solid var(--border);">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="neon-field" name="image" id="image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                        <small style="color: var(--text-light); font-size: 0.85rem; display: block; margin-top: 6px;">
                            Formats acceptés: JPG, PNG, GIF, WebP (max 5MB)
                        </small>
                        <input type="hidden" name="image_url" id="image_url" value="<?= htmlspecialchars($article['image'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-section">
                    <p class="form-section-title">Meta & distribution</p>
                    <div class="stack-2">
                        <div>
                            <label class="form-label">Auteur ID</label>
                            <input type="number" class="neon-field" name="auteur_id" id="auteur_id" value="<?= htmlspecialchars($article['auteur_id'] ?? '') ?>" required>
                        </div>
                        <div>
                            <label class="form-label">Lieu</label>
                            <input type="text" class="neon-field" name="lieu" value="<?= htmlspecialchars($article['lieu'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <p class="form-section-title">Tags</p>
                    <div>
                        <label class="form-label">Tags (séparer par des virgules)</label>
                        <input type="text" class="neon-field" name="tags" value="<?= htmlspecialchars($tagsValue) ?>" placeholder="Technologie, Gaming, News">
                        <small style="color: var(--text-light); font-size: 0.85rem; display: block; margin-top: 6px;">
                            Mots-clés utilisés pour le SEO interne.
                        </small>
                    </div>
                </div>

                <div class="cta-zone">
                    <button type="submit" class="btn-submit">Mettre à jour l'article</button>
                    <a href="articlelist.php" class="btn-ghost">Annuler</a>
                </div>

            </form>
            <?php else: ?>
                <div class="alert alert-danger">Article non trouvé</div>
                <a href="articlelist.php" class="btn-ghost">Retour à la liste</a>
            <?php endif; ?>
        </section>

    </div>
</main>

<footer style="text-align: center; padding: 20px; color: var(--text-light);">
    Admin Panel • Gestion des Articles
</footer>

</body>
</html>

