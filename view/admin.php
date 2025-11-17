<?php
// PROJT/view/admin.php
if (!isset($posts)) {
    header('Location: ../control/control.php?action=admin');
    exit;
}

// Séparation des posts récents (moins de 7 jours) et anciens
$newPosts = [];
$oldPosts = [];
$oneWeekAgo = date('Y-m-d H:i:s', strtotime('-7 days'));

foreach ($posts as $post) {
    if (strtotime($post['date_creation']) >= strtotime($oneWeekAgo)) {
        $newPosts[] = $post;
    } else {
        $oldPosts[] = $post;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --brown: #4b2e16;
            --copper: #b47b47;
            --moss: #5e6d3b;
            --sage: #a9b97d;
            --sand: #f4ecdd;
            --white: #fffaf5;
        }

        body{
            margin:0;
            padding:0;
            font-family:'Inter',sans-serif;
            background:var(--sand);
            font-size: 13px;
        }

        /* HEADER */
        .main-header{
            width:100%;
            padding:12px 20px;
            background:white;
            display:flex;
            align-items:center;
            justify-content:space-between;
            border-bottom:1px solid #e5e0d8;
            position:sticky;
            top:0;
            z-index:100;
            box-shadow: 0 1px 5px rgba(0,0,0,0.08);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo-image {
            height: 30px;
            width: auto;
        }

        .logo-text {
            font-weight:700;
            font-size:18px;
            color:#2F2E2C;
        }

        .search-bar{
            width:350px;
            padding:7px 12px;
            border-radius:20px;
            border:1px solid #bbb;
            font-size:13px;
        }

        .header-actions {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .btn-forum{
            padding:7px 14px;
            background:var(--copper);
            color:white;
            border-radius:15px;
            text-decoration:none;
            font-size:12px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-forum:hover {
            background:#a56a3a;
        }

        /* MAIN */
        .wrapper{
            max-width:1050px;
            margin:20px auto;
            background:white;
            padding:25px;
            border-radius:15px;
            box-shadow:0 1px 5px rgba(0,0,0,0.08);
        }

        h2{
            color:#3A382F;
            margin-bottom:20px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.4rem;
        }

        /* STATS */
        .stats{
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
            margin-bottom:25px;
        }

        .stat-box{
            background:#F2EBDE;
            padding:18px;
            border-radius:12px;
            text-align:center;
            transition: transform 0.3s ease;
            border-left: 3px solid var(--moss);
        }

        .stat-box.new-posts {
            border-left-color: var(--copper);
        }

        .stat-box.users {
            border-left-color: var(--sage);
        }

        .stat-box.comments {
            border-left-color: #8B4513;
        }

        .stat-box:hover {
            transform: translateY(-3px);
        }

        .stat-number{
            font-size:24px;
            font-weight:700;
            color:#3A3A1E;
            margin-bottom: 4px;
        }

        .stat-label{
            font-size:12px;
            color:#7A776A;
            font-weight: 600;
        }

        .stat-subtext {
            font-size: 10px;
            color: #9C9286;
            margin-top: 3px;
        }

        /* SECTIONS */
        .section {
            margin-bottom: 25px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 6px;
            border-bottom: 1px solid var(--sage);
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--brown);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .section-badge {
            background: var(--copper);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        /* TABLE */
        table{
            width:100%;
            border-collapse:collapse;
            margin-top: 6px;
            font-size: 12px;
        }

        thead{
            background:var(--moss);
            color:white;
        }

        thead th{
            padding:10px 12px;
            font-size:12px;
            text-align: left;
        }

        tbody tr {
            transition: background-color 0.3s ease;
        }

        tbody tr:hover {
            background: #f9f5f0;
        }

        tbody td{
            padding:12px;
            background:white;
            border-bottom:1px solid #e6e1d5;
        }

        .post-new {
            background: #fffaf0 !important;
            border-left: 2px solid var(--copper);
        }

        .btn-edit{
            padding:4px 10px;
            background:var(--sage);
            color:var(--brown);
            border-radius:15px;
            font-size:11px;
            text-decoration:none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }

        .btn-edit:hover {
            background:var(--moss);
            color: white;
        }

        .btn-delete{
            padding:4px 10px;
            border:1px solid var(--copper);
            color:var(--copper);
            border-radius:15px;
            font-size:11px;
            text-decoration:none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }

        .btn-delete:hover {
            background:var(--copper);
            color: white;
        }

        .btn-view{
            padding:4px 10px;
            background:var(--copper);
            color:white;
            border-radius:15px;
            font-size:11px;
            text-decoration:none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            margin-right: 3px;
        }

        .btn-view:hover {
            background:#a56a3a;
        }

        .action-buttons {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }

        .category-badge {
            display: inline-block;
            padding: 2px 8px;
            background: var(--sage);
            color: var(--brown);
            border-radius: 10px;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .author-info {
            display: flex;
            flex-direction: column;
        }

        .author-name {
            font-weight: 600;
            font-size: 0.8rem;
        }

        .post-date {
            font-size: 0.7rem;
            color: #7A776A;
        }

        .no-posts {
            text-align: center;
            padding: 25px;
            color: #7A776A;
            font-style: italic;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .stats {
                grid-template-columns: 1fr;
            }
            
            .main-header {
                flex-direction: column;
                gap: 10px;
                padding: 10px 15px;
            }
            
            .search-bar {
                width: 100%;
                max-width: 100%;
            }
            
            .wrapper {
                margin: 15px;
                padding: 15px;
            }
            
            table {
                font-size: 11px;
            }
            
            thead {
                display: none;
            }
            
            tbody tr {
                display: block;
                margin-bottom: 15px;
                border: 1px solid #e6e1d5;
                border-radius: 6px;
                padding: 6px;
            }
            
            tbody td {
                display: block;
                text-align: right;
                padding: 6px;
                border-bottom: 1px solid #e6e1d5;
            }
            
            tbody td:before {
                content: attr(data-label);
                float: left;
                font-weight: bold;
                font-size: 0.7rem;
            }

            .header-actions {
                flex-direction: column;
                width: 100%;
            }

            .section-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }

            .action-buttons {
                justify-content: center;
            }
        }
    </style>
</head>

<body>

<!-- HEADER -->
<header class="main-header">
    <div class="logo-container">
        <img src="../assets/images/logo.png" alt="ImpactAble" class="logo-image">
        <div class="logo-text"></div>
    </div>

    <input type="text" class="search-bar" placeholder="Rechercher des posts…">

    <div class="header-actions">
        <a href="control.php?action=list" class="btn-forum">
            <i class="fas fa-arrow-left"></i> Retour au Forum
        </a>
    </div>
</header>

<div class="wrapper">

    <h2>
        <i class="fas fa-chart-line"></i>
        Tableau de Bord Administrateur
    </h2>

    <!-- STATISTIQUES -->
    <div class="stats">
        <div class="stat-box">
            <div class="stat-number"><?= $totalPosts ?? 0 ?></div>
            <div class="stat-label">Total des Posts</div>
            <div class="stat-subtext">Depuis le début</div>
        </div>

        <div class="stat-box new-posts">
            <div class="stat-number"><?= count($newPosts) ?></div>
            <div class="stat-label">Nouveaux Posts</div>
            <div class="stat-subtext">7 derniers jours</div>
        </div>

        <div class="stat-box users">
            <div class="stat-number"><?= $totalUsers ?? 0 ?></div>
            <div class="stat-label">Membres Inscrits</div>
            <div class="stat-subtext">Communauté active</div>
        </div>

        <div class="stat-box comments">
            <div class="stat-number"><?= $totalComments ?? 0 ?></div>
            <div class="stat-label">Commentaires</div>
            <div class="stat-subtext">Engagement total</div>
        </div>
    </div>

    <!-- NOUVEAUX POSTS (7 derniers jours) -->
    <div class="section">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-bolt"></i>
                Nouveaux Posts Récents
                <span class="section-badge"><?= count($newPosts) ?> nouveaux</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre & Auteur</th>
                    <th>Catégorie</th>
                    <th>Date</th>
                    <th>Likes</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($newPosts)): ?>
                    <tr>
                        <td colspan="6" class="no-posts">
                            Aucun nouveau post cette semaine.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($newPosts as $post): ?>
                        <tr class="post-new">
                            <td data-label="ID">
                                <strong>#<?= $post["Id_post"] ?></strong>
                            </td>
                            <td data-label="Titre & Auteur">
                                <div style="font-weight: 600; margin-bottom: 3px;">
                                    <?= htmlspecialchars($post["titre"] ?: 'Sans titre') ?>
                                </div>
                                <div class="author-info">
                                    <span class="author-name"><?= htmlspecialchars($post["auteur"] ?? 'Auteur inconnu') ?></span>
                                </div>
                            </td>
                            <td data-label="Catégorie">
                                <span class="category-badge"><?= ucfirst($post["categorie"] ?: 'non spécifiée') ?></span>
                            </td>
                            <td data-label="Date">
                                <div class="post-date">
                                    <?= date('d/m/Y', strtotime($post["date_creation"])) ?>
                                </div>
                                <div style="font-size: 0.65rem; color: var(--copper);">
                                    <?= date('H:i', strtotime($post["date_creation"])) ?>
                                </div>
                            </td>
                            <td data-label="Likes">
                                <div style="display: flex; align-items: center; gap: 3px;">
                                    <i class="fas fa-heart" style="color: #e74c3c;"></i>
                                    <span><?= $post["likes"] ?? 0 ?></span>
                                </div>
                            </td>
                            <td data-label="Actions">
                                <div class="action-buttons">
                                    <a href="control.php?action=view&id=<?= $post['Id_post'] ?>" class="btn-view" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="control.php?action=edit&id=<?= $post['Id_post'] ?>" class="btn-edit" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="control.php?action=delete&id=<?= $post['Id_post'] ?>" class="btn-delete" title="Supprimer" onclick="return confirm('Supprimer ce post ?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ANCIENS POSTS -->
    <div class="section">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-history"></i>
                Anciens Posts
                <span class="section-badge" style="background: var(--moss);"><?= count($oldPosts) ?> posts</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre & Auteur</th>
                    <th>Catégorie</th>
                    <th>Date</th>
                    <th>Likes</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($oldPosts)): ?>
                    <tr>
                        <td colspan="6" class="no-posts">
                            Aucun ancien post à afficher.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($oldPosts as $post): ?>
                        <tr>
                            <td data-label="ID">
                                #<?= $post["Id_post"] ?>
                            </td>
                            <td data-label="Titre & Auteur">
                                <div style="font-weight: 600; margin-bottom: 3px;">
                                    <?= htmlspecialchars($post["titre"] ?: 'Sans titre') ?>
                                </div>
                                <div class="author-info">
                                    <span class="author-name"><?= htmlspecialchars($post["auteur"] ?? 'Auteur inconnu') ?></span>
                                </div>
                            </td>
                            <td data-label="Catégorie">
                                <span class="category-badge"><?= ucfirst($post["categorie"] ?: 'non spécifiée') ?></span>
                            </td>
                            <td data-label="Date">
                                <div class="post-date">
                                    <?= date('d/m/Y', strtotime($post["date_creation"])) ?>
                                </div>
                            </td>
                            <td data-label="Likes">
                                <div style="display: flex; align-items: center; gap: 3px;">
                                    <i class="fas fa-heart" style="color: #e74c3c;"></i>
                                    <span><?= $post["likes"] ?? 0 ?></span>
                                </div>
                            </td>
                            <td data-label="Actions">
                                <div class="action-buttons">
                                    <a href="control.php?action=view&id=<?= $post['Id_post'] ?>" class="btn-view" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="control.php?action=edit&id=<?= $post['Id_post'] ?>" class="btn-edit" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="control.php?action=delete&id=<?= $post['Id_post'] ?>" class="btn-delete" title="Supprimer" onclick="return confirm('Supprimer ce post ?')">
                                        <i class="fas fa-trash"></i>
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
    // Responsive table labels
    document.addEventListener('DOMContentLoaded', function() {
        const ths = document.querySelectorAll('thead th');
        const tables = document.querySelectorAll('table');
        
        tables.forEach(table => {
            const tds = table.querySelectorAll('tbody td');
            tds.forEach(td => {
                const index = Array.from(td.parentNode.children).indexOf(td);
                if (ths[index]) {
                    td.setAttribute('data-label', ths[index].textContent);
                }
            });
        });

        // Recherche en temps réel
        const searchBar = document.querySelector('.search-bar');
        if (searchBar) {
            searchBar.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>

</body>
</html>