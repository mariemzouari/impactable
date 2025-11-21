<?php
if (!isset($comments)) {
    header('Location: ../controller/control.php?action=admin');
    exit;
}

$admin_message = $_SESSION['admin_message'] ?? '';
$admin_error = $_SESSION['admin_error'] ?? '';
unset($_SESSION['admin_message'], $_SESSION['admin_error']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commentaires - ImpactAble Admin</title>
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

        .btn-admin{
            padding:7px 14px;
            background:var(--moss);
            color:white;
            border-radius:15px;
            text-decoration:none;
            font-size:12px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-admin:hover {
            background:#4d5a2a;
        }

        .wrapper{
            max-width:1200px;
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

        .stat-box.comments {
            border-left-color: var(--copper);
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

        .no-comments {
            text-align: center;
            padding: 25px;
            color: #7A776A;
            font-style: italic;
            font-size: 0.9rem;
        }

        .alert {
            padding: 12px 16px;
            margin: 15px 0;
            border-radius: 8px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .comment-content {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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

<header class="main-header">
    <div class="logo-container">
        <img src="../view/assets/images/logo.png" alt="ImpactAble" class="logo-image">
        <div class="logo-text"></div>
    </div>

    <div class="header-actions">
        <a href="../controller/control.php?action=admin" class="btn-admin">
            <i class="fas fa-chart-line"></i> Tableau de Bord
        </a>
        <a href="../controller/control.php?action=list" class="btn-forum">
            <i class="fas fa-arrow-left"></i> Retour au Forum
        </a>
    </div>
</header>

<div class="wrapper">

    <h2>
        <i class="fas fa-comments"></i>
        Gestion des Commentaires
    </h2>

    <?php if (!empty($admin_message)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($admin_message) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($admin_error)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($admin_error) ?>
        </div>
    <?php endif; ?>

    <div class="stats">
        <div class="stat-box comments">
            <div class="stat-number"><?= $totalComments ?? 0 ?></div>
            <div class="stat-label">Total des Commentaires</div>
        </div>
    </div>

    <div class="section">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-list"></i>
                Liste des Commentaires
                <span class="section-badge"><?= count($comments) ?> commentaires</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Contenu</th>
                    <th>Auteur</th>
                    <th>Post</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($comments)): ?>
                    <tr>
                        <td colspan="6" class="no-comments">
                            Aucun commentaire à afficher.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <tr>
                            <td data-label="ID">
                                <strong>#<?= $comment["Id_commentaire"] ?></strong>
                            </td>
                            <td data-label="Contenu">
                                <div class="comment-content" title="<?= htmlspecialchars($comment['contenu']) ?>">
                                    <?= htmlspecialchars($comment['contenu']) ?>
                                </div>
                            </td>
                            <td data-label="Auteur">
                                <div style="font-weight: 600;"><?= htmlspecialchars($comment['auteur']) ?></div>
                                <div style="font-size: 0.7rem; color: #7A776A;">ID: <?= $comment['Id_utilisateur'] ?></div>
                            </td>
                            <td data-label="Post">
                                <div style="font-weight: 600;"><?= htmlspecialchars($comment['post_titre']) ?></div>
                                <div style="font-size: 0.7rem; color: #7A776A;">Post ID: <?= $comment['Id_post'] ?></div>
                            </td>
                            <td data-label="Date">
                                <div style="font-size: 0.8rem;">
                                    <?= date('d/m/Y', strtotime($comment['date_creation'])) ?>
                                </div>
                                <div style="font-size: 0.65rem; color: var(--copper);">
                                    <?= date('H:i', strtotime($comment['date_creation'])) ?>
                                </div>
                            </td>
                            <td data-label="Actions">
                                <div class="action-buttons">
                                    <a href="../controller/control.php?action=view&id=<?= $comment['Id_post'] ?>" class="btn-view" title="Voir le post">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="../controller/control.php?action=delete_comment_admin&id=<?= $comment['Id_commentaire'] ?>" 
                                       class="btn-delete" 
                                       title="Supprimer"
                                       onclick="return confirm('Voulez-vous vraiment supprimer ce commentaire ? Cette action est irréversible.')">
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
    document.addEventListener('DOMContentLoaded', function() {
        const ths = document.querySelectorAll('thead th');
        const table = document.querySelector('table');
        
        const tds = table.querySelectorAll('tbody td');
        tds.forEach(td => {
            const index = Array.from(td.parentNode.children).indexOf(td);
            if (ths[index]) {
                td.setAttribute('data-label', ths[index].textContent);
            }
        });

        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });
    });
</script>

</body>
</html>