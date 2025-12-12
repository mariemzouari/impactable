<?php
require_once __DIR__ . '/../../Model/PostModel.php';
require_once __DIR__ . '/../../Model/LikeModel.php';
require_once __DIR__ . '/../../Model/CommentModel.php';

$postModel = new PostModel();
$likeModel = new LikeModel();
$commentModel = new CommentModel();

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

if ($search) {
    $posts = $postModel->search($search);
} elseif ($category) {
    $posts = $postModel->filterByCategory($category);
} else {
    $posts = $postModel->all();
}

$user_id = $_SESSION['user_id'] ?? 0;
$posts = $likeModel->enrichPostsWithLikes($posts, $user_id);

foreach ($posts as &$post) {
    $post['comments_count'] = $commentModel->countCommentsByPost($post['Id_post']);
}
unset($post);

$allPosts = $postModel->all();
$categoryCounts = [];
$categoriesValides = ['Opportunités', 'Événements', 'Idées', 'Questions', 'Ressources'];
foreach ($categoriesValides as $cat) {
    $categoryCounts[$cat] = 0;
}
foreach ($allPosts as $p) {
    $cat = $p['categorie'];
    if (in_array($cat, $categoriesValides)) {
        $categoryCounts[$cat]++;
    }
}
$totalAllPosts = count($allPosts);

$current_category = $category;
$user_name = $_SESSION['user_name'] ?? 'Visiteur';
$is_admin = $_SESSION['is_admin'] ?? false;
$is_logged_in = ($user_id > 0);

error_log('DEBUG forum.php: user_id=' . $user_id . ', is_admin=' . ($is_admin ? 'true' : 'false'));


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactAble – Forum</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/projt/View/assets/css/style.css">
</head>
<body>


<div class="container">  
        <header class="site-header" role="banner">
            <div class="brand">
                <button class="nav-toggle" id="navToggle" aria-label="Ouvrir le menu">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="logo">
                    <img src="/projt/View/assets/images/logo.png" alt="ImpactAble" class="logo-image">
                </div>
            </div>

            <div class="header-actions">
                <button class="btn ghost" onclick="window.location.href='/projt/index.php?action=create'">
                    <i class="fas fa-plus"></i> Créer un Post
                </button>
        
                <?php if ($is_logged_in): ?>
                    <button class="btn secondary" onclick="window.location.href='/projt/index.php?action=logout'">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </button>
                <?php else: ?>
                    <button class="btn ghost" onclick="window.location.href='/projt/index.php?action=login'">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </button>
                    <button class="btn primary" onclick="window.location.href='/projt/index.php?action=register'">
                        <i class="fas fa-user-plus"></i> S'inscrire
                    </button>
                <?php endif; ?>
            </div>
        </header>

        <!-- Side Panel Navigation -->
        <div class="side-panel" id="sidePanel">
            <div class="panel-header">
                <div class="logo">
                    <img src="/projt/View/assets/images/logo.png" alt="ImpactAble" class="logo-image">
                </div>
                <button class="panel-close" id="panelClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
      
             <nav class="panel-nav">
                <div class="nav-section">
                    <div class="nav-title">Navigation</div>
                    <a href="/projt/index.php?action=list" class="nav-link active">
                        <i class="fas fa-home"></i>
                        <span>Accueil</span>
                    </a>
                    <a href="/projt/index.php?action=list&category=Opportunités" class="nav-link">
                        <i class="fas fa-briefcase"></i>
                        <span>Opportunités</span>
                    </a>
                    <a href="/projt/index.php?action=list&category=Événements" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Événements</span>
                    </a>

                    <a href="/projt/index.php?action=list&category=Ressources" class="nav-link">
                        <i class="fas fa-book"></i>
                        <span>Ressources</span>
                    </a>
                    <a href="/projt/index.php?action=list" class="nav-link">
                        <i class="fas fa-comments"></i>
                        <span>Forum</span>
                    </a>
                    <a href="/projt/index.php?action=reclamations" class="nav-link">
                        <i class="fas fa-comment-alt"></i>
                        <span>Réclamations</span>
                    </a>
                </div> 
  
                <?php if ($is_admin): ?>
                <div class="nav-section">
                    <div class="nav-title">Administration</div>
                    <a href="/projt/index.php?action=admin" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>Tableau de bord</span>
                    </a>
                </div>
                <?php endif; ?>
            </nav>
      
            <div class="panel-footer">
                <div class="user-profile">
                    <div class="user-avatar"><?= strtoupper(substr($user_name, 0, 1)) ?></div>
                    <div class="user-info">
                        <h4><?= htmlspecialchars($user_name) ?></h4>
                        <p><?= $is_logged_in ? 'Connecté' : 'Connectez-vous pour plus de fonctionnalités' ?></p>
                    </div>
                </div>
        
                <?php if (!$is_logged_in): ?>
                <div style="margin-top: 16px; display: flex; flex-direction: column; gap: 8px;">
                    <a href="/projt/index.php?action=login" class="btn ghost" style="width: 100%;">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </a>
                    <a href="/projt/index.php?action=register" class="btn primary" style="width: 100%;">
                        <i class="fas fa-user-plus"></i> S'inscrire
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    
        <div class="panel-overlay" id="panelOverlay"></div>

        <!-- Forum Section -->
        <div class="forum-container">
            <div class="forum-main">
                <!-- Create Post Card -->
                <?php if ($is_logged_in): ?>
                <div class="forum-card">
                    <div class="forum-body">
                        <div class="create-post">
                            <div class="user-avatar"><?= strtoupper(substr($user_name, 0, 1)) ?></div>
                            <div class="post-input">
                                <!-- Formulaire de création de post rapide -->
                                <form action="/projt/index.php?action=create" method="POST" enctype="multipart/form-data" id="quickPostForm">
                                    <textarea name="contenu" placeholder="Partagez vos idées, questions ou expériences avec la communauté..." 
                                                        id="quickPostContent" ></textarea>
                  
                                    <!-- Prévisualisation média -->
                                    <div id="mediaPreview" style="display: none; margin-top: 10px;">
                                        <img id="imagePreview" src="" alt="Aperçu" style="max-width: 200px; max-height: 150px; border-radius: 8px; display: none;">
                                        <video id="videoPreview" controls style="max-width: 200px; max-height: 150px; border-radius: 8px; display: none;"></video>
                                    </div>

                                    <div class="post-actions">
                                        <div class="post-options">
                                            <!-- Option Image -->
                                            <label id="imageLabel" class="post-option" style="cursor: pointer;" onclick="document.getElementById('imageUpload').click();">
                                                <i class="fas fa-image"></i>
                                                <span>Image</span>
                                            </label>
                                            <input type="file" id="imageUpload" name="piece_jointe" accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp" style="display: none;" onchange="previewMedia(this, 'image')">
                      
                                            <!-- Option Vidéo -->
                                            <label id="videoLabel" class="post-option" style="cursor: pointer;" onclick="document.getElementById('videoUpload').click();">
                                                <i class="fas fa-video"></i>
                                                <span>Vidéo</span>
                                            </label>
                                            <input type="file" id="videoUpload" name="video" accept=".mp4,.webm,.avi,.mov,video/mp4,video/webm,video/avi,video/quicktime" style="display: none;" onchange="previewMedia(this, 'video')">
                      
                                            <!-- Option Lien -->
                                            <button type="button" class="post-option" onclick="addLink()">
                                                <i class="fas fa-link"></i>
                                                <span>Lien</span>
                                            </button>
                                        </div>
                                        <button type="submit" class="btn primary" id="publishBtn">
                                            <i class="fas fa-paper-plane"></i> Publier
                                        </button>
                                    </div>

                                    <!-- Champs cachés pour le formulaire rapide -->
                                    <input type="hidden" name="titre" id="quickPostTitle" value="Post rapide">
                                    <input type="hidden" name="categorie" id="quickPostCategory" value="autre">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
        
                <!-- Indicateur de filtre actif -->
                <?php if (!empty($current_category)): ?>
                <div class="forum-card" style="margin-bottom: 20px;">
                    <div class="forum-body">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h3 style="margin: 0; color: var(--moss);">
                                    <i class="fas fa-filter"></i>
                                    Filtre actif : <?= htmlspecialchars($current_category) ?>
                                </h3>
                                <p style="margin: 5px 0 0 0; color: var(--muted); font-size: 0.9rem;">
                                    Affichage des posts de la catégorie "<?= htmlspecialchars($current_category) ?>"
                                </p>
                            </div>
                            <a href="/projt/index.php?action=list" class="btn ghost">
                                <i class="fas fa-times"></i> Supprimer le filtre
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
        
                <!-- Forum Posts -->
                <div class="forum-posts">
                    <?php if (empty($posts)): ?>
                        <div class="post">
                            <div class="post-content">
                                <p style="text-align: center; padding: 40px; color: var(--muted);">
                                    <?php if (!empty($current_category)): ?>
                                        Aucun post dans la catégorie "<?= htmlspecialchars($current_category) ?>". 
                                        <a href="/projt/index.php?action=create" style="color: var(--copper);">Soyez le premier à partager !</a>
                                    <?php else: ?>
                                        Aucun post pour le moment. 
                                        <a href="/projt/index.php?action=create" style="color: var(--copper);">Soyez le premier à partager !</a>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                        <div class="post" data-post-id="<?= $post['Id_post'] ?>">
                            <!-- En-tête du post -->
                            <div class="post-header">
                                <div class="post-author">
                                    <div class="user-avatar"><?= strtoupper(substr($post['auteur'] ?? 'U', 0, 1)) ?></div>
                                    <div class="author-info">
                                        <div class="author-name-row" style="display: flex; align-items: center; gap: 12px;">
                                            <span><?= htmlspecialchars($post['auteur'] ?? 'Utilisateur') ?></span>
                                            <?php if ($is_admin && $user_id != $post['Id_utilisateur']): ?>
                                                <button type="button" class="report-icon-btn" title="Signaler cet utilisateur" data-target-id="<?= $post['Id_utilisateur'] ?>" data-target-name="<?= htmlspecialchars($post['auteur'] ?? 'Utilisateur') ?>" onclick="openReportModal('user', <?= $post['Id_utilisateur'] ?>, '<?= addslashes(htmlspecialchars($post['auteur'] ?? 'Utilisateur')) ?>')">
                                                    <i class="fas fa-flag"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                        <div class="post-time">
                                            <?= date('d/m/Y à H:i', strtotime($post['date_creation'])) ?> · 
                                            <span class="badge"><?= ucfirst($post['categorie']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
              
                            <!-- Contenu du post -->
                            <div class="post-content">
                                <div class="post-text">
                                    <h3><?= htmlspecialchars($post['titre']) ?></h3>
                                    <p><?= nl2br(htmlspecialchars($post['contenu'])) ?></p>
                                </div>
                                <?php if (!empty($post['piece_jointe'])): ?>
                                <div class="post-image">
                                    <img src="/projt/<?= $post['piece_jointe'] ?>" alt="Image du post" 
                                             onclick="openModal('/projt/<?= $post['piece_jointe'] ?>')" style="cursor: zoom-in;">
                                </div>
                                <?php endif; ?>
                            </div>
              
                            <!-- Statistiques et interactions -->
                            <div class="post-stats">
                                <div class="post-stat like-btn" onclick="toggleLike(this, <?= $post['Id_post'] ?>)">
                                    <i class="fas fa-heart <?= $post['user_liked'] ? 'liked' : '' ?>" 
                                         style="<?= $post['user_liked'] ? 'color: #e74c3c;' : '' ?>"></i>
                                    <span class="likes-count"><?= $post['likes_count'] ?> j'aime</span>
                                </div>
                                <div class="post-stat" onclick="window.location.href='/projt/index.php?action=view&id=<?= $post['Id_post'] ?>'">
                                    <i class="fas fa-comment"></i>
                                    <span><?= $post['comments_count'] ?? 0 ?> commentaires</span>
                                </div>
                                <div class="post-stat" onclick="sharePost(<?= $post['Id_post'] ?>)">
                                    <i class="fas fa-share"></i>
                                    <span>Partager</span>
                                </div>
                            </div>
              
                            <!-- Boutons d'action sur la même ligne -->
                             <div class="post-interactions">
                                <button class="interaction-btn" onclick="window.location.href='/projt/index.php?action=view&id=<?= $post['Id_post'] ?>'">
                                    <i class="far fa-eye"></i>
                                    <span>Voir & Commenter</span>
                                </button>
                
                                <?php if ($post['Id_utilisateur'] == $user_id || $is_admin): ?>
                                <button class="interaction-btn" onclick="window.location.href='/projt/index.php?action=edit&id=<?= $post['Id_post'] ?>'">
                                    <i class="far fa-edit"></i>
                                    <span>Modifier</span>
                                </button>
                                <button class="interaction-btn" onclick="if(confirm('Supprimer ce post ?')) window.location.href='/projt/index.php?action=delete&id=<?= $post['Id_post'] ?>'">
                                    <i class="fas fa-trash"></i>
                                    <span>Supprimer</span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
      
            <!-- Forum Sidebar -->
            <div class="forum-sidebar">
             <!-- Categories -->
<div class="forum-card">
        <div class="forum-header">
                <h3>Catégories</h3>
                <?php if (!empty($current_category)): ?>
                    <a href="/projt/index.php?action=list" class="btn ghost small" title="Voir tous les posts">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
        </div>
        <div class="forum-body">
                <div class="forum-categories">
                        <?php
                        $categories = [
                                'Opportunités' => 'briefcase',
                                'Événements' => 'calendar-alt',
                                'Idées' => 'lightbulb',
                                'Questions' => 'question-circle',
                                'Ressources' => 'book'
                        ];
            
                        foreach ($categories as $category => $icon):
                                $is_active = ($current_category === $category);
                        ?>
                        <a href="/projt/index.php?action=list&category=<?= urlencode($category) ?>" 
                             class="category <?= $is_active ? 'active' : '' ?>">
                                <div class="category-info">
                                        <div class="category-icon">
                                                <i class="fas fa-<?= $icon ?>"></i>
                                        </div>
                                        <div class="category-name"><?= $category ?></div>
                                </div>
                                <div class="category-count"><?= $categoryCounts[$category] ?? 0 ?></div>
                        </a>
                        <?php endforeach; ?>
            
                        <!-- Lien pour voir tous les posts -->
                        <a href="/projt/index.php?action=list" class="category <?= empty($current_category) ? 'active' : '' ?>" style="border-top: 1px solid rgba(75,46,22,0.1); margin-top: 10px; padding-top: 10px;">
                                <div class="category-info">
                                        <div class="category-icon">
                                                <i class="fas fa-layer-group"></i>
                                        </div>
                                        <div class="category-name">Tous les posts</div>
                                </div>
                                <div class="category-count"><?= $totalAllPosts ?></div>
                        </a>
                </div>
        </div>
</div>
        
                <!-- Top Contributors -->
                <div class="forum-card">
                    <div class="forum-header">
                        <h3>Top Contributeurs</h3>
                    </div>
                    <div class="forum-body">
                        <div class="top-contributors">
                            <div class="contributor">
                                <div class="contributor-avatar">MZ</div>
                                <div class="contributor-info">
                                    <div class="contributor-name">Mariem Zouari</div>
                                    <div class="contributor-stats">42 posts, 128 likes</div>
                                </div>
                                <div class="contributor-points">570</div>
                            </div>
                            <div class="contributor">
                                <div class="contributor-avatar">AD</div>
                                <div class="contributor-info">
                                    <div class="contributor-name">Ahmed Dridi</div>
                                    <div class="contributor-stats">38 posts, 96 likes</div>
                                </div>
                                <div class="contributor-points">485</div>
                            </div>
                            <div class="contributor">
                                <div class="contributor-avatar">SB</div>
                                <div class="contributor-info">
                                    <div class="contributor-name">Sarah Ben</div>
                                    <div class="contributor-stats">29 posts, 142 likes</div>
                                </div>
                                <div class="contributor-points">420</div>
                            </div>
                            <div class="contributor">
                                <div class="contributor-avatar">KM</div>
                                <div class="contributor-info">
                                    <div class="contributor-name">Khalil Mrad</div>
                                    <div class="contributor-stats">25 posts, 87 likes</div>
                                </div>
                                <div class="contributor-points">356</div>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Trending Topics -->
                <div class="forum-card">
                    <div class="forum-header">
                        <h3>Sujets Tendances</h3>
                    </div>
                    <div class="forum-body">
                        <div class="forum-categories">
                            <div class="category">
                                <div class="category-info">
                                    <div class="category-name">Événements hybrides</div>
                                </div>
                                <div class="category-count">24</div>
                            </div>
                            <div class="category">
                                <div class="category-info">
                                    <div class="category-name">Accessibilité mobile</div>
                                </div>
                                <div class="category-count">18</div>
                            </div>
                            <div class="category">
                                <div class="category-info">
                                    <div class="category-name">Formation inclusive</div>
                                </div>
                                <div class="category-count">15</div>
                            </div>
                            <div class="category">
                                <div class="category-info">
                                    <div class="category-name">Télétravail adapté</div>
                                </div>
                                <div class="category-count">12</div>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Forum Rules -->
                <div class="forum-card">
                    <div class="forum-header">
                        <h3>Règles du Forum</h3>
                    </div>
                    <div class="forum-body">
                        <div style="font-size: 0.9rem; color: var(--muted); line-height: 1.5;">
                            <p>• Respectez tous les membres</p>
                            <p>• Partagez des contenus pertinents</p>
                            <p>• Utilisez un langage inclusif</p>
                            <p>• Citez vos sources</p>
                            <p>• Signalez les contenus inappropriés</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

<!-- Modal pour l'image -->
<div id="imageModal" class="modal-backdrop" style="display: none;">
    <div class="modal" style="max-width: 90%; max-height: 90%;">
        <div class="modal-header">
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" style="text-align: center;">
            <img id="modalImage" src="" alt="Image agrandie" style="max-width: 100%; max-height: 80vh;">
        </div>
    </div>
</div>

<!-- Modal pour signaler un utilisateur (admin only) -->
<div id="reportModal" class="modal-backdrop" style="display: none;">
    <div class="report-modal">
        <div class="report-modal-header">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-circle" style="font-size: 1.5rem; color: #e74c3c;"></i>
                <h3 style="margin:0; color: #2c3e50;">Signaler un utilisateur</h3>
            </div>
            <button class="modal-close" onclick="closeReportModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="report-modal-body">
            <div class="report-target-card">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-user-circle" style="font-size: 1.8rem; color: var(--moss);"></i>
                    <div>
                        <p style="margin: 0; font-size: 0.9rem; color: var(--muted);">Utilisateur</p>
                        <p id="reportTargetInfo" style="margin: 0; font-weight: 600; color: #2c3e50;"></p>
                    </div>
                </div>
            </div>
            <div style="margin-top: 20px;">
                <label for="reportReason" style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">
                    <i class="fas fa-comment-dots" style="margin-right: 6px;"></i>Décrivez la raison du signalement
                </label>
                <textarea id="reportReason" rows="4" style="width:100%; padding: 12px; border: 2px solid #e8e8e8; border-radius: 8px; font-family: inherit; font-size: 0.95rem; resize: vertical; max-length: 500;" placeholder="Soyez précis et objectif dans votre description..."></textarea>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                    <div id="reportFeedback" style="color: #e74c3c; display: none; font-size: 0.9rem;"></div>
                    <div style="color: var(--muted); font-size: 0.85rem;">
                        <span id="charCount">0</span> / 500 caractères
                    </div>
                </div>
            </div>
        </div>
        <div class="report-modal-footer">
            <button class="btn ghost" onclick="closeReportModal()">Annuler</button>
            <button class="btn primary" id="reportSubmitBtn" onclick="submitReport()" style="gap: 8px;">
                <i class="fas fa-paper-plane"></i> Envoyer
            </button>
        </div>
    </div>
</div>

<style>
.report-icon-btn {
    background: none;
    border: none;
    color: #e74c3c;
    font-size: 0.95rem;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.report-icon-btn:hover {
    background-color: rgba(231, 76, 60, 0.1);
    transform: scale(1.15);
}

.report-modal {
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    animation: slideUp 0.3s ease;
    max-width: 520px;
    width: 95%;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.report-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px;
    border-bottom: 2px solid #f0f0f0;
    background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
}

.report-modal-body {
    padding: 24px;
    max-height: 60vh;
    overflow-y: auto;
}

.report-target-card {
    background: #f8f9fa;
    padding: 16px;
    border-radius: 8px;
    border-left: 4px solid var(--moss);
}

.report-modal-footer {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    padding: 20px 24px;
    border-top: 2px solid #f0f0f0;
    background: #f8f9fa;
}

.report-modal-footer .btn {
    padding: 10px 20px;
    font-weight: 600;
}
</style>

<script>
// Navigation panel
document.getElementById('navToggle').addEventListener('click', function() {
        document.getElementById('sidePanel').classList.add('active');
        document.getElementById('panelOverlay').classList.add('active');
});

document.getElementById('panelClose').addEventListener('click', function() {
        document.getElementById('sidePanel').classList.remove('active');
        document.getElementById('panelOverlay').classList.remove('active');
});

document.getElementById('panelOverlay').addEventListener('click', function() {
        document.getElementById('sidePanel').classList.remove('active');
        this.classList.remove('active');
});

// Fonction pour prévisualiser les médias
function previewMedia(input, type) {
        const preview = document.getElementById('mediaPreview');
        const imagePreview = document.getElementById('imagePreview');
        const videoPreview = document.getElementById('videoPreview');
    
        if (input.files && input.files[0]) {
                const reader = new FileReader();
        
                reader.onload = function(e) {
                        if (type === 'image') {
                                imagePreview.src = e.target.result;
                                imagePreview.style.display = 'block';
                                videoPreview.style.display = 'none';
                        } else if (type === 'video') {
                                videoPreview.src = e.target.result;
                                videoPreview.style.display = 'block';
                                imagePreview.style.display = 'none';
                        }
                        preview.style.display = 'block';
                };
        
                reader.readAsDataURL(input.files[0]);
        }
}
// Reporting functions (modal + AJAX)
const CURRENT_USER_ID = <?= json_encode($user_id) ?>;
const IS_ADMIN = <?= json_encode($is_admin ? true : false) ?>;

let reportContext = { target_type: null, target_id: null, target_name: null };

function openReportModal(targetType, targetId, targetName) {
    if (!IS_ADMIN) {
        alert('Vous devez être administrateur pour signaler un utilisateur.');
        return;
    }

    // Prevent reporting yourself on the client side as well
    if (parseInt(CURRENT_USER_ID) === parseInt(targetId)) {
        alert("Vous ne pouvez pas vous signaler vous-même.");
        return;
    }

    reportContext = { target_type: targetType, target_id: targetId, target_name: targetName };
    document.getElementById('reportTargetInfo').textContent = 'Utilisateur: ' + (targetName || targetId);
    document.getElementById('reportReason').value = '';
    document.getElementById('reportFeedback').style.display = 'none';
    document.getElementById('reportModal').style.display = 'flex';
}

function closeReportModal() {
    document.getElementById('reportModal').style.display = 'none';
}

// Compteur de caractères en temps réel
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('reportReason');
    const charCount = document.getElementById('charCount');
    if (textarea && charCount) {
        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }
});

function submitReport() {
    const reason = document.getElementById('reportReason').value.trim();
    const feedback = document.getElementById('reportFeedback');
    if (!reason) {
        feedback.textContent = 'Veuillez saisir une raison pour le signalement.';
        feedback.style.display = 'block';
        return;
    }

    if (reason.length < 10) {
        feedback.textContent = 'La raison doit contenir au moins 10 caractères.';
        feedback.style.display = 'block';
        return;
    }

    const payload = {
        target_type: reportContext.target_type || 'user',
        target_id: reportContext.target_id,
        reason: reason
    };

    const submitBtn = document.getElementById('reportSubmitBtn');
    submitBtn.disabled = true;
    const originalContent = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';

    fetch('/projt/View/FrontOffice/report_user.php', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        if (data && data.success) {
            feedback.textContent = '';
            feedback.style.display = 'none';
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Signalé avec succès !';
            submitBtn.style.backgroundColor = '#27ae60';
            setTimeout(() => {
                closeReportModal();
                submitBtn.innerHTML = originalContent;
                submitBtn.style.backgroundColor = '';
            }, 1500);
            // Mark UI: disable the report button for that target
            const btns = document.querySelectorAll('[data-target-id="' + reportContext.target_id + '"]');
            btns.forEach(b => { 
                b.disabled = true; 
                b.style.opacity = '0.5';
                b.style.cursor = 'not-allowed';
            });
        } else {
            feedback.textContent = data && data.message ? data.message : 'Erreur lors de l\'envoi du signalement.';
            feedback.style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalContent;
        }
    })
    .catch(err => {
        console.error('report error', err);
        feedback.textContent = 'Erreur réseau lors de l\'envoi.';
        feedback.style.display = 'block';
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalContent;
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Envoyer';
    });
}

// Detect when file dialog is closed without selecting a file
(function(){
    function attachWatcher(labelId, inputId, fileTypeName){
        const label = document.getElementById(labelId);
        const input = document.getElementById(inputId);
        if(!label || !input) return;

        label.addEventListener('click', function(){
            // When label is clicked, wait for window to regain focus (dialog closed).
            function onFocus(){
                window.removeEventListener('focus', onFocus);
                        // If no file selected, do nothing (silent as requested)
                        if(!input.files || input.files.length === 0){
                            // silent no-op
                        }
            }
            window.addEventListener('focus', onFocus);
        });
    }

    attachWatcher('imageLabel', 'imageUpload', 'image');
    attachWatcher('videoLabel', 'videoUpload', 'vidéo');
})();

// Fonction pour ajouter un lien
function addLink() {
        const url = prompt('Entrez l\'URL du lien:');
        if (url) {
                const textarea = document.getElementById('quickPostContent');
                const currentText = textarea.value;
                textarea.value = currentText + ' ' + url;
        }
}

// Fonction pour partager un post
function sharePost(postId) {
        const url = window.location.origin + '/projt/index.php?action=view&id=' + postId;
    
        if (navigator.share) {
                navigator.share({
                        title: 'ImpactAble - Post',
                        url: url
                });
        } else {
                // Fallback pour les navigateurs qui ne supportent pas l'API Web Share
                navigator.clipboard.writeText(url).then(function() {
                        alert('Lien copié dans le presse-papier !');
                });
        }
}

// Modal pour les images
function openModal(imageSrc) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('imageModal').style.display = 'flex';
}

function closeModal() {
        document.getElementById('imageModal').style.display = 'none';
}

// Fermer le modal en cliquant à l'extérieur
document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
                closeModal();
        }
});

// Système de likes avec base de données
function toggleLike(element, postId) {
        // Vérifier si l'utilisateur est connecté
        <?php if (!$is_logged_in): ?>
                alert('Vous devez être connecté pour liker un post');
                return;
        <?php endif; ?>

        const heartIcon = element.querySelector('i.fa-heart');
        const likesCount = element.querySelector('.likes-count');
    
        // Animation immédiate pour l'UX
        const isCurrentlyLiked = heartIcon.classList.contains('liked');
    
        if (!isCurrentlyLiked) {
                heartIcon.classList.add('liked');
                heartIcon.style.color = '#e74c3c';
                heartIcon.style.transform = 'scale(1.3)';
                setTimeout(() => {
                        heartIcon.style.transform = 'scale(1)';
                }, 300);
        } else {
                heartIcon.classList.remove('liked');
                heartIcon.style.color = '';
        }
    
        // Appel AJAX vers le serveur
        fetch('/projt/index.php?action=toggle_like', {
                method: 'POST',
                headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'post_id=' + postId
        })
        .then(response => response.json())
        .then(data => {
                if (data.success) {
                        // Mettre à jour l'interface avec les vraies données
                        likesCount.textContent = data.likes_count + ' j\'aime';
            
                        if (data.user_liked) {
                                heartIcon.classList.add('liked');
                                heartIcon.style.color = '#e74c3c';
                        } else {
                                heartIcon.classList.remove('liked');
                                heartIcon.style.color = '';
                        }
                } else {
                        // Revenir à l'état précédent en cas d'erreur
                        if (!isCurrentlyLiked) {
                                heartIcon.classList.remove('liked');
                                heartIcon.style.color = '';
                        } else {
                                heartIcon.classList.add('liked');
                                heartIcon.style.color = '#e74c3c';
                        }
                        alert(data.message);
                }
        })
        .catch(error => {
                console.error('Erreur:', error);
                // Revenir à l'état précédent en cas d'erreur
                if (!isCurrentlyLiked) {
                        heartIcon.classList.remove('liked');
                        heartIcon.style.color = '';
                } else {
                        heartIcon.classList.add('liked');
                        heartIcon.style.color = '#e74c3c';
                }
        });
}

// Validation du formulaire rapide
document.getElementById('quickPostForm').addEventListener('submit', function(e) {
        const content = document.getElementById('quickPostContent').value.trim();
    
        if (!content) {
                e.preventDefault();
                alert('Veuillez écrire quelque chose avant de publier');
                return;
        }
    
        // Si le contenu est trop long, on le met dans le titre
        if (content.length > 100) {
                document.getElementById('quickPostTitle').value = content.substring(0, 50) + '...';
        } else {
                document.getElementById('quickPostTitle').value = content;
        }
});
</script>

</body>
</html>
