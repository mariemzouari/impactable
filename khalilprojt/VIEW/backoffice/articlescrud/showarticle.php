<?php
include '../../../controller/ArticleController.php';

$articleC = new ArticleController();
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

if (!$id) {
    header('Location: articlelist.php');
    exit;
}

$article = $articleC->showArticle($id);

if ($article) {
    $article['tags'] = json_decode($article['tags'] ?? '[]', true) ?? [];
} else {
    $article = null;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails article</title>
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
            max-width: 1400px;
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
            max-width: 1400px;
            margin: 0 auto;
        }
        .article-hero {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 30px;
            padding: 36px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: linear-gradient(120deg, rgba(255,0,199,0.18), rgba(0,255,234,0.12));
            box-shadow: 0 25px 60px rgba(0,0,0,0.45);
            margin-bottom: 30px;
        }
        .cover-frame {
            border-radius: var(--radius);
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 20px 40px rgba(0,0,0,0.35);
            min-height: 260px;
            background: rgba(5,0,20,0.6);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cover-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 28px;
        }
        .meta-tile {
            padding: 16px 20px;
            border-radius: var(--radius-sm);
            border: 1px solid rgba(255, 0, 199, 0.25);
            background: rgba(6,0,25,0.75);
        }
        .meta-tile span {
            display: block;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--text-light);
        }
        .meta-tile strong {
            font-size: 1.2rem;
            color: #fff;
        }
        .chip {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 999px;
            border: 1px solid rgba(0,255,234,0.4);
            color: var(--cyan);
            margin: 4px;
            font-size: 0.85rem;
        }
        .detail-card {
            margin-top: 36px;
            padding: 32px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: rgba(5,0,20,0.9);
            box-shadow: 0 25px 60px rgba(0,0,0,0.45);
        }
        .detail-card h3 {
            margin-bottom: 16px;
            font-family: 'Orbitron', sans-serif;
            color: var(--cyan);
        }
        .actions-bar {
            margin-top: 30px;
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }
        .btn-outline, .btn-ghost {
            padding: 12px 24px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: rgba(255, 0, 199, 0.15);
            color: var(--text);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-outline:hover, .btn-ghost:hover {
            border-color: var(--primary);
            box-shadow: 0 0 20px rgba(255,0,199,0.35);
        }
        .btn-ghost {
            background: transparent;
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
            <a href="articlelist.php" class="nav-link active">Articles</a>
            <a href="addArticle.php" class="nav-link">Ajouter Article</a>
        </nav>
    </div>
</header>

<main class="admin-main">
    <div class="container">

        <?php if (!$article): ?>
            <section style="background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); padding: 40px; text-align: center;">
                <h2>Article introuvable</h2>
                <p style="color: var(--text-light);">Aucun article ne correspond à l'identifiant demandé.</p>
                <a href="articlelist.php" class="btn-outline" style="margin-top: 20px; display: inline-block;">Retour à la liste</a>
            </section>
        <?php else: ?>

        <section class="article-hero">
            <div class="cover-frame">
                <?php if (!empty($article['image'])): ?>
                    <img src="../../../<?= htmlspecialchars($article['image']); ?>" alt="<?= htmlspecialchars($article['titre']); ?>">
                <?php else: ?>
                    <span style="color: var(--text-light);">Pas d'image</span>
                <?php endif; ?>
            </div>
            <div>
                <h1 style="font-family: 'Orbitron', sans-serif; font-size: 2.4rem; margin-bottom: 10px;"><?= htmlspecialchars($article['titre']); ?></h1>
                <p style="color: var(--text-light); margin-bottom: 20px;">
                    <?= htmlspecialchars($article['auteur']); ?> • <?= htmlspecialchars($article['categorie']); ?>
                </p>
                <div class="meta-grid">
                    <div class="meta-tile">
                        <span>Date de création</span>
                        <strong><?= htmlspecialchars($article['date_creation']); ?></strong>
                    </div>
                    <div class="meta-tile">
                        <span>Lieu</span>
                        <strong><?= $article['lieu'] ?? '--'; ?></strong>
                    </div>
                    <div class="meta-tile">
                        <span>Auteur ID</span>
                        <strong>#<?= $article['auteur_id']; ?></strong>
                    </div>
                    <div class="meta-tile">
                        <span>Statut</span>
                        <strong style="text-transform: capitalize;"><?= htmlspecialchars($article['statut'] ?? 'brouillon'); ?></strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="detail-card">
            <h3>Contenu de l'article</h3>
            <p style="line-height: 1.8; color: var(--text);"><?= nl2br(htmlspecialchars($article['contenu'] ?? '')); ?></p>

            <?php if (!empty($article['tags'])): ?>
                <h3 style="margin-top: 30px;">Tags</h3>
                <?php foreach ($article['tags'] as $tag): ?>
                    <span class="chip"><?= htmlspecialchars($tag); ?></span>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="actions-bar">
                <a href="updatearticle.php?id=<?= $article['id']; ?>" class="btn-outline">
                    Modifier
                </a>
                <a href="articlelist.php" class="btn-ghost">Retour liste</a>
            </div>
        </section>

        <?php endif; ?>
    </div>
</main>

<footer style="text-align: center; padding: 20px; color: var(--text-light);">
    Admin Panel • Gestion des Articles
</footer>

</body>
</html>

