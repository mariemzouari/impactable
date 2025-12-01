<?php
include '../../../controller/ArticleController.php';
$articleC = new ArticleController();

// Get status filter from URL (default to 'brouillon' for pending articles)
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'brouillon';
$validStatuses = ['brouillon', 'publie'];
if (!in_array($statusFilter, $validStatuses)) {
    $statusFilter = 'brouillon';
}

// Get articles by status
$list = $articleC->listArticlesByStatus($statusFilter);

if ($list instanceof PDOStatement) {
    $list = $list->fetchAll(PDO::FETCH_ASSOC);
} elseif ($list instanceof Traversable) {
    $list = iterator_to_array($list);
}

// Get statistics
$stats = $articleC->getStats();

// Get counts for filter tabs
$pendingList = $articleC->listArticlesByStatus('brouillon');
$acceptedList = $articleC->listArticlesByStatus('publie');

$pendingCount = 0;
$acceptedCount = 0;

if ($pendingList instanceof PDOStatement) {
    $pendingCount = count($pendingList->fetchAll(PDO::FETCH_ASSOC));
} elseif ($pendingList instanceof Traversable) {
    $pendingCount = count(iterator_to_array($pendingList));
}

if ($acceptedList instanceof PDOStatement) {
    $acceptedCount = count($acceptedList->fetchAll(PDO::FETCH_ASSOC));
} elseif ($acceptedList instanceof Traversable) {
    $acceptedCount = count(iterator_to_array($acceptedList));
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search !== '') {
    $needle = mb_strtolower($search);
    $list = array_filter($list, function ($article) use ($needle) {
        $haystack = mb_strtolower(
            ($article['titre'] ?? '') . ' ' .
            ($article['auteur'] ?? '') . ' ' .
            ($article['categorie'] ?? '') . ' ' .
            ($article['lieu'] ?? '')
        );
        return strpos($haystack, $needle) !== false;
    });
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Gestion des Articles</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #F4ECDD 0%, #FFF4F5 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(75, 46, 22, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header h1 {
            color: #4B2E16;
            font-size: 2em;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-add {
            background: linear-gradient(135deg, #5E6D38, #4B2E16);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            font-size: 1em;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(94, 109, 56, 0.4);
        }

        .btn-back {
            background: linear-gradient(135deg, #0277BD, #01579B);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            font-size: 1em;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(2, 119, 189, 0.4);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border: 3px solid #F4ECDD;
            border-radius: 20px;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 4px 15px rgba(75, 46, 22, 0.1);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(75, 46, 22, 0.15);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2em;
            color: white;
        }

        .stat-content {
            flex: 1;
        }

        .stat-value {
            font-size: 2.5em;
            font-weight: 700;
            color: #4B2E16;
            line-height: 1;
        }

        .stat-label {
            color: #5E6D38;
            font-size: 1em;
            margin-top: 5px;
        }

        .filters-section {
            background: white;
            border: 3px solid #F4ECDD;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            box-shadow: 0 4px 15px rgba(75, 46, 22, 0.1);
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #5E6D38;
            font-size: 1.1em;
        }

        .search-box input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #A9B57D;
            border-radius: 12px;
            font-size: 1em;
            font-family: 'Inter', sans-serif;
        }

        .search-box input:focus {
            outline: none;
            border-color: #5E6D38;
            box-shadow: 0 0 0 4px rgba(94, 109, 56, 0.1);
        }

        .filter-select {
            padding: 12px 15px;
            border: 2px solid #A9B57D;
            border-radius: 12px;
            font-size: 0.95em;
            font-family: 'Inter', sans-serif;
            color: #4B2E16;
            cursor: pointer;
            background: white;
        }

        .filter-select:focus {
            outline: none;
            border-color: #5E6D38;
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 10px 20px;
            border: 2px solid #A9B57D;
            border-radius: 12px;
            background: white;
            color: #4B2E16;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .filter-tab:hover {
            border-color: #5E6D38;
            background: #F4ECDD;
        }

        .filter-tab.active {
            background: linear-gradient(135deg, #5E6D38, #4B2E16);
            color: white;
            border-color: #4B2E16;
        }

        .table-container {
            background: white;
            border: 3px solid #F4ECDD;
            border-radius: 20px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(75, 46, 22, 0.1);
            overflow-x: auto;
        }

        .articles-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        .articles-table thead {
            background: linear-gradient(135deg, #5E6D38, #4B2E16);
            color: white;
        }

        .articles-table th {
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 0.95em;
        }

        .articles-table tbody tr {
            border-bottom: 1px solid #F4ECDD;
            transition: all 0.3s;
        }

        .articles-table tbody tr:hover {
            background: #FFF4F5;
        }

        .articles-table td {
            padding: 15px;
            color: #4B2E16;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: 600;
            display: inline-block;
        }

        .status-brouillon {
            background: #FFF3CD;
            color: #856404;
        }

        .status-publie {
            background: #D4EDDA;
            color: #155724;
        }

        .status-archive {
            background: #E0E0E0;
            color: #424242;
        }

        .tag-chip {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            background: #F4ECDD;
            color: #5E6D38;
            font-size: 0.8em;
            margin: 2px;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-view, .btn-edit, .btn-delete, .btn-approve {
            padding: 8px 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9em;
            transition: all 0.3s;
            color: white;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-view {
            background: linear-gradient(135deg, #5E6D38, #4B2E16);
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(94, 109, 56, 0.4);
        }

        .btn-edit {
            background: linear-gradient(135deg, #0277BD, #01579B);
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(2, 119, 189, 0.4);
        }

        .btn-approve {
            background: linear-gradient(135deg, #28A745, #20C997);
        }

        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        }

        .btn-delete {
            background: linear-gradient(135deg, #D32F2F, #B71C1C);
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(211, 47, 47, 0.4);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #5E6D38;
        }

        .empty-state i {
            font-size: 4em;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: stretch;
            }

            .header h1 {
                font-size: 1.5em;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .filters-section {
                flex-direction: column;
            }

            .search-box {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-newspaper"></i> Dashboard Admin - Gestion des Articles</h1>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <a href="../admin_dashboard.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Retour Dashboard
                </a>
                <a href="addArticle.php" class="btn-add">
                    <i class="fas fa-plus"></i> Ajouter un Article
                </a>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #FFF3CD, #FFE082);">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= $stats['brouillons'] ?? 0 ?></div>
                    <div class="stat-label">En Brouillon</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #D4EDDA, #A5D6A7);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= $stats['publies'] ?? 0 ?></div>
                    <div class="stat-label">Publiés</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #E0E0E0, #BDBDBD);">
                    <i class="fas fa-archive"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= $stats['archives'] ?? 0 ?></div>
                    <div class="stat-label">Archivés</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #E1BEE7, #CE93D8);">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
                    <div class="stat-label">Total Articles</div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters-section">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <form method="GET" action="" style="display: inline-block; width: 100%;">
                    <input type="hidden" name="status" value="<?= $statusFilter ?>">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher par titre, auteur, catégorie...">
                </form>
            </div>
            
            <div class="filter-tabs">
                <a href="?status=brouillon<?= $search ? '&search=' . urlencode($search) : '' ?>" 
                   class="filter-tab <?= $statusFilter === 'brouillon' ? 'active' : '' ?>">
                    Brouillons (<?= $pendingCount ?>)
                </a>
                <a href="?status=publie<?= $search ? '&search=' . urlencode($search) : '' ?>" 
                   class="filter-tab <?= $statusFilter === 'publie' ? 'active' : '' ?>">
                    Publiés (<?= $acceptedCount ?>)
                </a>
            </div>
        </div>

        <!-- Tableau -->
        <div class="table-container">
            <table class="articles-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Date création</th>
                        <th>Catégorie</th>
                        <th>Lieu</th>
                        <th>Tags</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                        <tr>
                            <td colspan="9" class="empty-state">
                                <i class="fas fa-newspaper"></i>
                                <p>Aucun article trouvé</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($list as $article): ?>
                            <?php
                            $tags = json_decode($article['tags'] ?? '[]', true) ?? [];
                            ?>
                            <tr>
                                <td><strong>#<?= htmlspecialchars($article['id']) ?></strong></td>
                                <td><?= htmlspecialchars(substr($article['titre'] ?? '', 0, 40)) ?><?= strlen($article['titre'] ?? '') > 40 ? '...' : '' ?></td>
                                <td><?= htmlspecialchars($article['auteur'] ?? '') ?></td>
                                <td><?= htmlspecialchars($article['date_creation'] ?? '') ?></td>
                                <td><?= htmlspecialchars($article['categorie'] ?? '') ?></td>
                                <td><?= htmlspecialchars($article['lieu'] ?? 'N/A') ?></td>
                                <td>
                                    <?php foreach (array_slice($tags, 0, 3) as $tag): ?>
                                        <span class="tag-chip"><?= htmlspecialchars($tag) ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($tags) > 3): ?>
                                        <span class="tag-chip">+<?= count($tags) - 3 ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= htmlspecialchars($article['statut'] ?? 'brouillon') ?>">
                                        <?= htmlspecialchars(ucfirst($article['statut'] ?? 'brouillon')) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="showarticle.php?id=<?= $article['id'] ?>" class="btn-view" title="Voir">
                                            <i class="fas fa-eye"></i> Voir
                                        </a>
                                        <a href="updatearticle.php?id=<?= $article['id'] ?>" class="btn-edit" title="Modifier">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                        <?php if ($article['statut'] === 'brouillon'): ?>
                                            <form method="POST" action="approveArticle.php" style="display: inline;">
                                                <input type="hidden" name="id" value="<?= $article['id'] ?>">
                                                <button type="submit" class="btn-approve" title="Approuver">
                                                    <i class="fas fa-check"></i> Approuver
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <a href="deletearticle.php?id=<?= $article['id'] ?>" 
                                           class="btn-delete" 
                                           title="Supprimer"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Auto-submit search form on input
        document.querySelector('.search-box input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });
    </script>
</body>
</html>
