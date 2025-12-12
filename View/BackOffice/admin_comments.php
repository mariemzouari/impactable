<?php
require_once __DIR__ . '/../../Model/CommentModel.php';

$commentModel = new CommentModel();

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: index.php?action=list');
    exit;
}

$comments = $commentModel->getAllCommentsWithPosts();
$totalComments = $commentModel->countComments();

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
        <link rel="stylesheet" href="View/assets/css/admin-style.css">
</head>

<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="admin-logo">
                    <img src="View/assets/images/logo1.png" alt="Inclusive Opportunities" class="admin-logo-image">
                </div>
            </div>
      
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-title">Principal</div>
                    <a href="index.php?action=admin" class="sidebar-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tableau de bord</span>
                    </a>
                    <a href="index.php?action=admin" class="sidebar-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Analytiques</span>
                    </a>
                </div>
        
                <div class="nav-section">
                    <div class="nav-title">Gestion de contenu</div>
                    <a href="index.php?action=admin" class="sidebar-link">
                        <i class="fas fa-users"></i>
                        <span>Utilisateurs</span>
                    </a>
                    <a href="index.php?action=admin" class="sidebar-link">
                        <i class="fas fa-briefcase"></i>
                        <span>Opportunités</span>
                    </a>
                    <a href="index.php?action=admin" class="sidebar-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Événements</span>
                    </a>
                    <a href="index.php?action=admin" class="sidebar-link">
                        <i class="fas fa-hand-holding-heart"></i>
                        <span>Campagnes</span>
                    </a>
                    <a href="index.php?action=admin" class="sidebar-link">
                        <i class="fas fa-book"></i>
                        <span>Ressources</span>
                    </a>
                </div>
        
                <div class="nav-section">
                    <div class="nav-title">Communauté</div>
                    <a href="index.php?action=admin" class="sidebar-link">
                        <i class="fas fa-comments"></i>
                        <span>Forum</span>
                    </a>
                    <a href="index.php?action=admin_comments" class="sidebar-link active">
                        <i class="fas fa-comment-alt"></i>
                        <span>Commentaires</span>
                    </a>
                    <a href="index.php?action=search_comments" class="sidebar-link">
                        <i class="fas fa-search"></i>
                        <span>Rechercher</span>
                    </a>
                </div>
        
                <div class="nav-section">
                    <div class="nav-title">Paramètres</div>
                    <a href="index.php?action=admin" class="sidebar-link">
                        <i class="fas fa-cog"></i>
                        <span>Configuration</span>
                    </a>
                    <a href="index.php?action=logout" class="sidebar-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </a>
                </div>
            </nav>
      
            <div class="sidebar-footer">
                <div class="admin-user">
                    <div class="admin-avatar">AD</div>
                    <div class="admin-user-info">
                        <h4>Admin ImpactAble</h4>
                        <p>Administrateur</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <main class="admin-main">
            <header class="admin-header">
                <div>
                    <h2>Gestion des Commentaires</h2>
                    <p class="text-muted">Administration des commentaires du forum</p>
                </div>
        
                <div class="header-actions">
                    <button class="btn primary" onclick="window.location.href='index.php?action=admin'">
                        <i class="fas fa-arrow-left"></i>
                        <span>Retour au Tableau de bord</span>
                    </button>
                    <button class="btn primary" onclick="window.location.href='index.php?action=list'">
                        <i class="fas fa-comments"></i>
                        <span>Voir le Forum</span>
                    </button>
                </div>
            </header>
      
            <div class="admin-content">
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

                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="stat-number"><?= $totalComments ?? 0 ?></div>
                        <div class="stat-label">Total des Commentaires</div>
                    </div>
                </div>

                <!-- Comments Table -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>Liste des Commentaires</h3>
                        <span class="badge"><?= count($comments) ?> commentaires</span>
                    </div>
                    <div class="card-body">
                        <table class="admin-table">
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
                                        <td colspan="6" style="text-align: center; padding: 30px; color: var(--muted);">
                                            Aucun commentaire à afficher.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($comments as $comment): ?>
                                    <tr>
                                        <td><strong>#<?= $comment["Id_commentaire"] ?></strong></td>
                                        <td>
                                            <div class="comment-content" title="<?= htmlspecialchars($comment['contenu']) ?>" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                <?= htmlspecialchars($comment['contenu']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600;"><?= htmlspecialchars($comment['auteur']) ?></div>
                                            <div style="font-size: 0.85rem; color: var(--muted);">
                                                ID: <?= $comment['Id_utilisateur'] ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600;"><?= htmlspecialchars($comment['post_titre']) ?></div>
                                            <div style="font-size: 0.85rem; color: var(--muted);">
                                                Post ID: <?= $comment['Id_post'] ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.9rem;">
                                                <?= date('d/m/Y', strtotime($comment['date_creation'])) ?>
                                            </div>
                                            <div style="font-size: 0.8rem; color: var(--copper);">
                                                <?= date('H:i', strtotime($comment['date_creation'])) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="index.php?action=view&id=<?= $comment['Id_post'] ?>" 
                                                     class="btn ghost small" title="Voir le post">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="index.php?action=delete_comment_admin&id=<?= $comment['Id_commentaire'] ?>" 
                                                     class="btn danger small" 
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
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
