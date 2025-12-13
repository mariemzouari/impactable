<?php
require_once __DIR__ . '/../../Model/PostModel.php';
require_once __DIR__ . '/../../Model/LikeModel.php';

$postModel = new PostModel();
$likeModel = new LikeModel();

if (($_SESSION['role'] ?? '') != 'admin') {
    header('Location: index.php?action=list');
    exit;
}

$posts = $postModel->all();

$user_id = $_SESSION['user_id'] ?? 0;
$posts = $likeModel->enrichPostsWithLikes($posts, $user_id);

$totalPosts = $postModel->countPosts();
$totalUsers = $postModel->countUsers();
$totalComments = $postModel->countComments();

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

$admin_message = $_SESSION['admin_message'] ?? '';
$admin_error = $_SESSION['admin_error'] ?? '';
unset($_SESSION['admin_message'], $_SESSION['admin_error']);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactAble – Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="View/assets/css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="admin-logo">
                    <img src="View/assets/images/logo1.png" alt="Inclusive Opportunities" class="admin-logo-image">
                </div>
            </div>
      
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-title">Principal</div>
                    <a href="index.php?action=admin" class="sidebar-link active">
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
                    <a href="index.php?action=admin_comments" class="sidebar-link">
                        <i class="fas fa-comment-alt"></i>
                        <span>Commentaires</span>
                    </a>
                    <a href="index.php?action=search_comments" class="sidebar-link">
                        <i class="fas fa-search"></i>
                        <span>Rechercher</span>
                    </a>
                    <a href="index.php?action=admin_reports" class="sidebar-link">
                        <i class="fas fa-flag"></i>
                        <span>Signalements</span>
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
                    <h2>Tableau de bord administrateur</h2>
                    <p class="text-muted">Bienvenue dans l'interface d'administration d'ImpactAble</p>
                </div>
        
                <div class="header-actions">
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Rechercher...">
                    </div>
                    <button class="btn primary" onclick="window.location.href='index.php?action=search_comments'">
                        <i class="fas fa-search"></i>
                        <span>Rechercher Commentaires</span>
                    </button>
                    <button class="btn primary" onclick="window.location.href='index.php?action=admin_comments'">
                        <i class="fas fa-comments"></i>
                        <span>Gérer les Commentaires</span>
                    </button>
                </div>
            </header>
      
            <div class="admin-content">
                <?php if (!empty($admin_message)): ?>
                <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($admin_message) ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($admin_error)): ?>
                <div class="alert alert-error" style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                    <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($admin_error) ?>
                </div>
                <?php endif; ?>

                <!-- Dashboard Content -->
                <div id="dashboard-content" class="tab-content active">
                    <div class="content-header">
                        <h1>Tableau de bord</h1>
                    </div>
          
                    <!-- Stats Grid -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="stat-number"><?= $totalPosts ?? count($posts) ?></div>
                            <div class="stat-label">Total des Posts</div>
                        </div>
            
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div class="stat-number"><?= count($newPosts) ?></div>
                            <div class="stat-label">Nouveaux Posts (7j)</div>
                        </div>
            
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-number"><?= $totalUsers ?? 0 ?></div>
                            <div class="stat-label">Utilisateurs inscrits</div>
                        </div>
            
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="stat-number"><?= $totalComments ?? 0 ?></div>
                            <div class="stat-label">Commentaires</div>
                        </div>
                    </div>
          
                    <!-- Posts Table -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3>Nouveaux Posts Récents</h3>
                            <span class="badge"><?= count($newPosts) ?> nouveaux</span>
                        </div>
                        <div class="card-body">
                            <table class="admin-table">
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
                                            <td colspan="6" style="text-align: center; padding: 30px; color: var(--muted);">
                                                Aucun nouveau post cette semaine.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($newPosts as $post): ?>
                                        <tr>
                                            <td><strong>#<?= $post["Id_post"] ?></strong></td>
                                            <td>
                                                <div style="font-weight: 600;"><?= htmlspecialchars($post["titre"]) ?></div>
                                                <div style="font-size: 0.85rem; color: var(--muted);">
                                                    par <?= htmlspecialchars($post["auteur"] ?? 'Auteur inconnu') ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status active"><?= ucfirst($post["categorie"]) ?></span>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($post["date_creation"])) ?></td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 5px;">
                                                    <i class="fas fa-heart" style="color: #e74c3c;"></i>
                                                    <span><?= $post["likes_count"] ?? 0 ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-actions">
                                                    <a href="index.php?action=view&id=<?= $post['Id_post'] ?>" 
                                                         class="btn ghost small" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="index.php?action=edit&id=<?= $post['Id_post'] ?>&from=admin" 
                                                         class="btn ghost small" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="index.php?action=delete&id=<?= $post['Id_post'] ?>&from=admin" 
                                                         class="btn danger small" 
                                                         title="Supprimer"
                                                         onclick="return confirm('Supprimer ce post ?')">
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

                    <!-- Old Posts Table -->
                    <div class="content-card" style="margin-top: 30px;">
                        <div class="card-header">
                            <h3>Anciens Posts</h3>
                            <span class="badge" style="background: var(--moss);"><?= count($oldPosts) ?> posts</span>
                        </div>
                        <div class="card-body">
                            <table class="admin-table">
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
                                            <td colspan="6" style="text-align: center; padding: 30px; color: var(--muted);">
                                                Aucun ancien post à afficher.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($oldPosts as $post): ?>
                                        <tr>
                                            <td>#<?= $post["Id_post"] ?></td>
                                            <td>
                                                <div style="font-weight: 600;"><?= htmlspecialchars($post["titre"]) ?></div>
                                                <div style="font-size: 0.85rem; color: var(--muted);">
                                                    par <?= htmlspecialchars($post["auteur"] ?? 'Auteur inconnu') ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status inactive"><?= ucfirst($post["categorie"]) ?></span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($post["date_creation"])) ?></td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 5px;">
                                                    <i class="fas fa-heart" style="color: #e74c3c;"></i>
                                                    <span><?= $post["likes_count"] ?? 0 ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-actions">
                                                    <a href="index.php?action=view&id=<?= $post['Id_post'] ?>" 
                                                         class="btn ghost small" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="index.php?action=edit&id=<?= $post['Id_post'] ?>&from=admin" 
                                                         class="btn ghost small" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="index.php?action=delete&id=<?= $post['Id_post'] ?>&from=admin" 
                                                         class="btn danger small" 
                                                         title="Supprimer"
                                                         onclick="return confirm('Supprimer ce post ?')">
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
            </div>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchBar = document.querySelector('.search-bar input');
            if (searchBar) {
                    searchBar.addEventListener('input', function() {
                            const searchTerm = this.value.toLowerCase();
                            const rows = document.querySelectorAll('tbody tr');
              
                            rows.forEach(row => {
                                    const text = row.textContent.toLowerCase();
                                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                            });
                    });
            }

            // Auto-hide alerts
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
