
<?php
// Vérification des variables
if (!isset($posts)) {
    header('Location: ../controller/control.php?action=list');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche de Commentaires par Post - ImpactAble</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../View/assets/css/admin-style.css">
</head>
<body>
  <div class="admin-container">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <div class="sidebar-header">
        <div class="admin-logo">
          <img src="../View/assets/images/logo1.png" alt="ImpactAble" class="admin-logo-image">
        </div>
      </div>
      
      <nav class="sidebar-nav">
        <div class="nav-section">
          <div class="nav-title">Principal</div>
          <a href="../controller/control.php?action=admin" class="sidebar-link">
            <i class="fas fa-tachometer-alt"></i>
            <span>Tableau de bord</span>
          </a>
          <a href="../controller/control.php?action=admin" class="sidebar-link">
            <i class="fas fa-chart-bar"></i>
            <span>Analytiques</span>
          </a>
        </div>
        
        <div class="nav-section">
          <div class="nav-title">Gestion de contenu</div>
          <a href="../controller/control.php?action=admin" class="sidebar-link">
            <i class="fas fa-users"></i>
            <span>Utilisateurs</span>
          </a>
          <a href="../controller/control.php?action=admin" class="sidebar-link">
            <i class="fas fa-briefcase"></i>
            <span>Opportunités</span>
          </a>
          <a href="../controller/control.php?action=admin" class="sidebar-link">
            <i class="fas fa-calendar-alt"></i>
            <span>Événements</span>
          </a>
          <a href="../controller/control.php?action=admin" class="sidebar-link">
            <i class="fas fa-hand-holding-heart"></i>
            <span>Campagnes</span>
          </a>
          <a href="../controller/control.php?action=admin" class="sidebar-link">
            <i class="fas fa-book"></i>
            <span>Ressources</span>
          </a>
        </div>
        
        <div class="nav-section">
          <div class="nav-title">Communauté</div>
          <a href="../controller/control.php?action=admin" class="sidebar-link">
            <i class="fas fa-comments"></i>
            <span>Forum</span>
          </a>
          <a href="../controller/control.php?action=admin_comments" class="sidebar-link">
            <i class="fas fa-comment-alt"></i>
            <span>Commentaires</span>
          </a>
          <a href="../controller/control.php?action=search_comments" class="sidebar-link active">
            <i class="fas fa-search"></i>
            <span>Rechercher</span>
          </a>
        </div>
        
        <div class="nav-section">
          <div class="nav-title">Paramètres</div>
          <a href="../controller/control.php?action=admin" class="sidebar-link">
            <i class="fas fa-cog"></i>
            <span>Configuration</span>
          </a>
          <a href="../controller/control.php?action=logout" class="sidebar-link">
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
          <h2>Recherche de Commentaires par Post</h2>
          <p class="text-muted">Recherchez les commentaires associés à un post spécifique</p>
        </div>
        
        <div class="header-actions">
          <button class="btn primary" onclick="window.location.href='../controller/control.php?action=admin_comments'">
            <i class="fas fa-arrow-left"></i>
            <span>Retour aux Commentaires</span>
          </button>
          <button class="btn primary" onclick="window.location.href='../controller/control.php?action=admin'">
            <i class="fas fa-tachometer-alt"></i>
            <span>Retour au Tableau de bord</span>
          </button>
        </div>
      </header>
      
      <div class="admin-content">
        <div class="content-card">
          <div class="card-header">
            <h3><i class="fas fa-search"></i> Recherche</h3>
          </div>
          <div class="card-body">
            <form method="POST" class="form-container" style="max-width: 100%;">
              <div class="form-group">
                <label for="post_id">Sélectionnez un post :</label>
                <select name="post_id" id="post_id" class="select" >
                  <option value="">Choisissez un post</option>
                  <?php foreach ($posts as $post): ?>
                    <option value="<?= $post['Id_post'] ?>" 
                      <?= isset($selected_post) && $selected_post['Id_post'] == $post['Id_post'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($post['titre']) ?> 
                      (par <?= htmlspecialchars($post['auteur'] ?? 'Auteur inconnu') ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <button type="submit" class="btn primary">
                <i class="fas fa-search"></i> Rechercher les commentaires
              </button>
            </form>
          </div>
        </div>

        <?php if (isset($selected_post)): ?>
          <div class="content-card">
            <div class="card-header">
              <h3>
                <i class="fas fa-file-alt"></i>
                Post sélectionné : "<?= htmlspecialchars($selected_post['titre']) ?>"
              </h3>
            </div>
            
            <div class="card-body">
              <h4 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="far fa-comments"></i>
                Commentaires (<?= isset($comments) ? count($comments) : 0 ?>)
              </h4>
              
              <?php if (empty($comments)): ?>
                <div style="text-align: center; padding: 40px; color: var(--muted);">
                  <i class="far fa-comments" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                  <p>Aucun commentaire pour ce post.</p>
                  <p>Soyez le premier à commenter !</p>
                </div>
              <?php else: ?>
                <div class="comments-list">
                  <?php foreach ($comments as $comment): ?>
                    <div class="comment-item" style="background: rgba(169,185,125,0.05); padding: 20px; border-radius: var(--radius-sm); margin-bottom: 16px; border-left: 4px solid var(--copper);">
                      <div class="comment-header" style="display: flex; justify-content: space-between; margin-bottom: 12px; align-items: center;">
                        <div class="comment-author" style="font-weight: 600; display: flex; align-items: center; gap: 8px;">
                          <i class="fas fa-user" style="color: var(--copper);"></i>
                          <?= htmlspecialchars($comment['auteur']) ?>
                        </div>
                        <div class="comment-date" style="color: var(--muted); font-size: 0.9rem; display: flex; align-items: center; gap: 6px;">
                          <i class="far fa-clock"></i>
                          <?= date('d/m/Y à H:i', strtotime($comment['date_creation'])) ?>
                        </div>
                      </div>
                      <div class="comment-content" style="line-height: 1.6; color: var(--brown);">
                        <?= nl2br(htmlspecialchars($comment['contenu'])) ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
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
[file content end]