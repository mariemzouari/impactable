<?php
// Démarrer la session si pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 0;
    $_SESSION['user_name'] = 'Visiteur';
    $_SESSION['is_admin'] = false;
}

$user_id = $_SESSION['user_id'] ?? 0;
$user_name = $_SESSION['user_name'] ?? 'Visiteur';
$is_admin = $_SESSION['is_admin'] ?? false;
$is_logged_in = ($user_id > 0);

// Vérifier si les posts sont définis, sinon rediriger
if (!isset($posts)) {
    header('Location: ../controller/control.php?action=list');
    exit;
}

// Récupérer la catégorie active
$current_category = $_GET['category'] ?? '';

// Debug: Vérifier le nombre de posts
error_log("=== FORUM.PHP ===");
error_log("Nombre de posts reçus: " . count($posts));
foreach ($posts as $index => $post) {
    error_log("Post $index - ID: {$post['Id_post']}, Titre: {$post['titre']}");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ImpactAble – Forum</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../View/assets/css/style.css">
</head>
<body>

<div class="container">  
    <!-- Header -->
    <header class="site-header" role="banner">
      <div class="brand">
        <button class="nav-toggle" id="navToggle" aria-label="Ouvrir le menu">
          <i class="fas fa-bars"></i>
        </button>
        <div class="logo">
          <img src="../View/assets/images/logo.png" alt="ImpactAble" class="logo-image">
        </div>
      </div>

      <div class="header-actions">
        <button class="btn ghost" onclick="window.location.href='../controller/control.php?action=create'">
          <i class="fas fa-plus"></i> Créer un Post
        </button>
        
        <?php if ($is_logged_in): ?>
          <button class="btn secondary" onclick="window.location.href='../controller/control.php?action=logout'">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
          </button>
        <?php else: ?>
          <button class="btn ghost" onclick="window.location.href='../controller/control.php?action=login'">
            <i class="fas fa-sign-in-alt"></i> Se connecter
          </button>
          <button class="btn primary" onclick="window.location.href='../controller/control.php?action=register'">
            <i class="fas fa-user-plus"></i> S'inscrire
          </button>
        <?php endif; ?>
      </div>
    </header>

    <!-- Side Panel Navigation -->
    <div class="side-panel" id="sidePanel">
      <div class="panel-header">
        <div class="logo">
          <img src="../View/assets/images/logo.png" alt="ImpactAble" class="logo-image">
        </div>
        <button class="panel-close" id="panelClose">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
       <nav class="panel-nav">
        <div class="nav-section">
          <div class="nav-title">Navigation</div>
          <a href="#home" class="nav-link active">
            <i class="fas fa-home"></i>
            <span>Accueil</span>
          </a>
          <a href="#opportunities" class="nav-link">
            <i class="fas fa-briefcase"></i>
            <span>Opportunités</span>
          </a>
          <a href="#events" class="nav-link">
            <i class="fas fa-calendar-alt"></i>
            <span>Événements</span>
          </a>
          <a href="#donations" class="nav-link">
            <i class="fas fa-hand-holding-heart"></i>
            <span>Campagnes</span>
          </a>
          <a href="#resources" class="nav-link">
            <i class="fas fa-book"></i>
            <span>Ressources</span>
          </a>
          <a href="#forum" class="nav-link">
            <i class="fas fa-comments"></i>
            <span>Forum</span>
          </a>
          <a href="#reclamations" class="nav-link">
            <i class="fas fa-comment-alt"></i>
            <span>Réclamations</span>
          </a>
        </div> 
  
        <?php if ($is_admin): ?>
        <div class="nav-section">
          <div class="nav-title">Administration</div>
          <a href="../controller/control.php?action=admin" class="nav-link">
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
          <a href="../controller/control.php?action=login" class="btn ghost" style="width: 100%;">
            <i class="fas fa-sign-in-alt"></i> Se connecter
          </a>
          <a href="../controller/control.php?action=register" class="btn primary" style="width: 100%;">
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
                <form action="../controller/control.php?action=create" method="POST" enctype="multipart/form-data" id="quickPostForm">
                  <textarea name="contenu" placeholder="Partagez vos idées, questions ou expériences avec la communauté..." 
                            id="quickPostContent" required></textarea>
                  
                  <!-- Prévisualisation média -->
                  <div id="mediaPreview" style="display: none; margin-top: 10px;">
                    <img id="imagePreview" src="" alt="Aperçu" style="max-width: 200px; max-height: 150px; border-radius: 8px; display: none;">
                    <video id="videoPreview" controls style="max-width: 200px; max-height: 150px; border-radius: 8px; display: none;"></video>
                  </div>

                  <div class="post-actions">
                    <div class="post-options">
                      <!-- Option Image -->
                      <label for="imageUpload" class="post-option" style="cursor: pointer;">
                        <i class="fas fa-image"></i>
                        <span>Image</span>
                      </label>
                      <input type="file" id="imageUpload" name="piece_jointe" accept="image/*" style="display: none;" onchange="previewMedia(this, 'image')">
                      
                      <!-- Option Vidéo -->
                      <label for="videoUpload" class="post-option" style="cursor: pointer;">
                        <i class="fas fa-video"></i>
                        <span>Vidéo</span>
                      </label>
                      <input type="file" id="videoUpload" name="video" accept="video/*" style="display: none;" onchange="previewMedia(this, 'video')">
                      
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
              <a href="../controller/control.php?action=list" class="btn ghost">
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
                    <a href="../controller/control.php?action=create" style="color: var(--copper);">Soyez le premier à partager !</a>
                  <?php else: ?>
                    Aucun post pour le moment. 
                    <a href="../controller/control.php?action=create" style="color: var(--copper);">Soyez le premier à partager !</a>
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
                    <div class="author-name"><?= htmlspecialchars($post['auteur'] ?? 'Utilisateur') ?></div>
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
                  <img src="../<?= $post['piece_jointe'] ?>" alt="Image du post" 
                       onclick="openModal('../<?= $post['piece_jointe'] ?>')" style="cursor: zoom-in;">
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
                <div class="post-stat" onclick="window.location.href='../controller/control.php?action=view&id=<?= $post['Id_post'] ?>'">
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
                <button class="interaction-btn" onclick="window.location.href='../controller/control.php?action=view&id=<?= $post['Id_post'] ?>'">
                  <i class="far fa-eye"></i>
                  <span>Voir & Commenter</span>
                </button>
                
                <?php if ($post['Id_utilisateur'] == $user_id || $is_admin): ?>
                <button class="interaction-btn" onclick="window.location.href='../controller/control.php?action=edit&id=<?= $post['Id_post'] ?>'">
                  <i class="far fa-edit"></i>
                  <span>Modifier</span>
                </button>
                <button class="interaction-btn" onclick="if(confirm('Supprimer ce post ?')) window.location.href='../controller/control.php?action=delete&id=<?= $post['Id_post'] ?>'">
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
          <a href="../controller/control.php?action=list" class="btn ghost small" title="Voir tous les posts">
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
                'Idées & Projets' => 'lightbulb',
                'Questions' => 'question-circle',
                'Ressources' => 'hand-holding-heart'
            ];
            
            // Compter les posts par catégorie pour tous les posts (pas seulement ceux filtrés)
            $allPosts = $this->postModel->all();
            $categoryCounts = [];
            foreach ($categories as $category => $icon) {
                $count = 0;
                foreach ($allPosts as $post) {
                    if ($post['categorie'] === $category) {
                        $count++;
                    }
                }
                $categoryCounts[$category] = $count;
            }
            
            foreach ($categories as $category => $icon):
                $is_active = ($current_category === $category);
            ?>
            <a href="../controller/control.php?action=list&category=<?= urlencode($category) ?>" 
               class="category <?= $is_active ? 'active' : '' ?>">
                <div class="category-info">
                    <div class="category-icon">
                        <i class="fas fa-<?= $icon ?>"></i>
                    </div>
                    <div class="category-name"><?= $category ?></div>
                </div>
                <div class="category-count"><?= $categoryCounts[$category] ?></div>
            </a>
            <?php endforeach; ?>
            
            <!-- Lien pour voir tous les posts -->
            <a href="../controller/control.php?action=list" class="category <?= empty($current_category) ? 'active' : '' ?>" style="border-top: 1px solid rgba(75,46,22,0.1); margin-top: 10px; padding-top: 10px;">
                <div class="category-info">
                    <div class="category-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="category-name">Tous les posts</div>
                </div>
                <div class="category-count"><?= count($allPosts) ?></div>
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
              <!-- Cette section peut être dynamisée avec des données de la BD -->
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
    const url = window.location.origin + '/controller/control.php?action=view&id=' + postId;
    
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
    fetch('../controller/control.php?action=toggle_like', {
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