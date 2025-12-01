<?php
if (!isset($post)) {
    header('Location: ../controller/control.php?action=list');
    exit;
}

$user_id = $user_id ?? $_SESSION['user_id'] ?? 0;
$user_name = $user_name ?? $_SESSION['user_name'] ?? 'Visiteur';
$is_admin = $is_admin ?? $_SESSION['is_admin'] ?? false;
$is_logged_in = ($user_id > 0);

$old_comment = $_SESSION['old_comment'] ?? '';
$editing_comment_id = $_POST['edit_comment_id'] ?? 0;

$comment_success = $_SESSION['comment_success'] ?? '';
$comment_errors = $_SESSION['comment_errors'] ?? [];

unset($_SESSION['comment_success'], $_SESSION['comment_errors'], $_SESSION['old_comment']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['titre']) ?> - ImpactAble</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../View/assets/css/style.css">
</head>
<body>

<div class="container">
    <!-- Post Principal -->
    <div class="post">
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
        
        <div class="post-content">
            <div class="post-text">
                <h1><?= htmlspecialchars($post['titre']) ?></h1>
                <p><?= nl2br(htmlspecialchars($post['contenu'])) ?></p>
            </div>
            <?php if (!empty($post['piece_jointe'])): ?>
            <div class="post-image">
                <img src="../<?= $post['piece_jointe'] ?>" alt="Image du post">
            </div>
            <?php endif; ?>
        </div>
        
        <div class="post-stats">
            <div class="post-stat like-btn" onclick="toggleLike(this, <?= $post['Id_post'] ?>)">
                <i class="fas fa-heart <?= $post['user_liked'] ? 'liked' : '' ?>" 
                   style="<?= $post['user_liked'] ? 'color: #e74c3c;' : '' ?>"></i>
                <span class="likes-count"><?= $post['likes_count'] ?> j'aime</span>
            </div>
            <div class="post-stat">
                <i class="fas fa-comment"></i>
                <span><?= isset($comments) ? count($comments) : 0 ?> commentaires</span>
            </div>
            <div class="post-stat">
                <i class="fas fa-share"></i>
                <span>Partager</span>
            </div>
        </div>
        
        <div class="post-interactions">
            <button class="interaction-btn" onclick="window.location.href='../controller/control.php?action=list'">
                <i class="fas fa-arrow-left"></i>
                <span>Retour au forum</span>
            </button>
            <button class="interaction-btn" onclick="window.location.href='../controller/control.php?action=create'">
                <i class="fas fa-plus"></i>
                <span>Nouveau Post</span>
            </button>
            
            <?php if ($post['Id_utilisateur'] == $user_id || $is_admin): ?>
            <button class="interaction-btn" onclick="window.location.href='../controller/control.php?action=edit&id=<?= $post['Id_post'] ?>'">
                <i class="fas fa-edit"></i>
                <span>Modifier le Post</span>
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Section Commentaires -->
    <div class="forum-card">
        <div class="forum-header">
            <h2>
                <i class="far fa-comments"></i>
                Commentaires (<?= isset($comments) ? count($comments) : 0 ?>)
            </h2>
        </div>

        <div class="forum-body">
            <?php if (!empty($comment_success)): ?>
                <div class="alert alert-success" id="successMessage">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($comment_success) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($comment_errors)): ?>
                <div class="alert alert-error" id="errorMessage">
                    <strong>Erreurs à corriger :</strong>
                    <ul>
                        <?php foreach ($comment_errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Formulaire de commentaire -->
            <div class="create-post">
                <div class="user-avatar"><?= strtoupper(substr($user_name, 0, 1)) ?></div>
                <div class="post-input">
                    <form action="../controller/control.php?action=add_comment" method="POST" id="commentForm">
                        <input type="hidden" name="post_id" value="<?= $post['Id_post'] ?>">
                        <textarea name="contenu" placeholder="Partagez votre avis, posez une question ou apportez votre contribution..." id="commentTextarea"><?= htmlspecialchars($old_comment) ?></textarea>
                        <div class="post-actions">
                            <button type="submit" class="btn primary">
                                <i class="fas fa-paper-plane"></i> Publier le commentaire
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des commentaires -->
            <div class="comments-section" id="commentsSection">
                <?php if (empty($comments)): ?>
                    <div class="no-comments">
                        <i class="far fa-comments"></i>
                        <p>Aucun commentaire pour le moment. Soyez le premier à commenter !</p>
                    </div>
                <?php else: ?>
                    <div class="comments-list">
                        <?php foreach ($comments as $comment): ?>
                            <?php 
                            $is_your_comment = ($comment['Id_utilisateur'] == $user_id);
                            $is_editing = ($editing_comment_id == $comment['Id_commentaire']);
                            ?>
                            
                            <?php if ($is_editing): ?>
                                <!-- Formulaire d'édition -->
                                <div class="comment">
                                    <form action="../controller/control.php?action=edit_comment" method="POST" class="edit-comment-form">
                                        <input type="hidden" name="comment_id" value="<?= $comment['Id_commentaire'] ?>">
                                        <input type="hidden" name="post_id" value="<?= $post['Id_post'] ?>">
                                        <textarea name="contenu"><?= htmlspecialchars($comment['contenu']) ?></textarea>
                                        <div class="edit-comment-actions">
                                            <button type="submit" class="btn primary">
                                                <i class="fas fa-save"></i> Enregistrer
                                            </button>
                                            <a href="../controller/control.php?action=view&id=<?= $post['Id_post'] ?>" class="btn ghost">
                                                <i class="fas fa-times"></i> Annuler
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            <?php else: ?>
                                <!-- Affichage du commentaire -->
                                <div class="comment" id="comment-<?= $comment['Id_commentaire'] ?>">
                                    <div class="comment-header">
                                        <div class="comment-author">
                                            <div class="user-avatar"><?= strtoupper(substr($comment['auteur'], 0, 1)) ?></div>
                                            <div class="author-info">
                                                <div class="author-name"><?= htmlspecialchars($comment['auteur']) ?></div>
                                                <div class="post-time">
                                                    <?= date('d/m/Y à H:i', strtotime($comment['date_creation'])) ?>
                                                    <?php if ($is_your_comment): ?>
                                                        <span class="badge">(Votre commentaire)</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="comment-content">
                                        <?= nl2br(htmlspecialchars($comment['contenu'])) ?>
                                    </div>

                                    <?php if ($is_your_comment): ?>
                                        <div class="comment-actions">
                                            <form action="../controller/control.php?action=view&id=<?= $post['Id_post'] ?>" method="POST">
                                                <input type="hidden" name="edit_comment_id" value="<?= $comment['Id_commentaire'] ?>">
                                                <button type="submit" class="btn ghost small">
                                                    <i class="fas fa-edit"></i> Modifier
                                                </button>
                                            </form>
                                            
                                            <a href="../controller/control.php?action=delete_comment&id=<?= $comment['Id_commentaire'] ?>&post_id=<?= $post['Id_post'] ?>" 
                                               class="btn danger small" 
                                               onclick="return confirm('Voulez-vous vraiment supprimer votre commentaire ?')">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Système de likes avec base de données - MÊME VERSION QUE forum.php
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

// Validation des commentaires avec détection des mots inappropriés
document.getElementById('commentForm').addEventListener('submit', function(e) {
    const contenu = document.getElementById('commentTextarea').value.trim();
    let errors = [];
    
    // Validation de la longueur
    if (contenu.length < 2) {
        errors.push('Le commentaire doit contenir au moins 2 caractères');
    }
    
    if (contenu.length > 1000) {
        errors.push('Le commentaire ne peut pas dépasser 1000 caractères');
    }
    
    // Détection des mots interdits
    const bannedWords = ['spam', 'arnaque', 'hack', 'pirate', 'connerie', 'merde', 'putain', 'fuck', 'shit', 'bullshit'];
    const foundBanned = bannedWords.some(word => {
        const regex = new RegExp('\\b' + word + '\\b', 'i');
        return regex.test(contenu);
    });
    
    if (foundBanned) {
        errors.push('Votre commentaire contient des termes inappropriés');
    }
    
    // Détection de répétition excessive
    if (/(.)\1{4,}/.test(contenu)) {
        errors.push('Évitez la répétition excessive de caractères (ex: aaaaa)');
    }
    
    // Détection tout en majuscules
    if (contenu.length > 10 && contenu === contenu.toUpperCase()) {
        errors.push('Évitez d\'écrire uniquement en majuscules');
    }
    
    if (errors.length > 0) {
        e.preventDefault();
        
        // Supprimer les anciennes alertes
        const oldAlerts = document.querySelectorAll('.alert.alert-error');
        oldAlerts.forEach(alert => alert.remove());
        
        // Créer une nouvelle alerte
        let alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-error';
        alertDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i><div><strong>Erreurs à corriger :</strong><ul>${errors.map(error => `<li>${error}</li>`).join('')}</ul></div>`;
        
        document.querySelector('.forum-body').insertBefore(alertDiv, document.querySelector('.create-post'));
        
        // Marquer le champ comme invalide
        document.getElementById('commentTextarea').classList.add('invalid');
    }
});

// Retirer la classe invalid quand l'utilisateur tape
document.getElementById('commentTextarea').addEventListener('input', function() {
    this.classList.remove('invalid');
});

// Masquer les messages après 5 secondes
setTimeout(function() {
    const messages = document.querySelectorAll('.alert');
    messages.forEach(function(message) {
        message.style.opacity = '0';
        message.style.transition = 'opacity 0.5s ease';
        setTimeout(() => {
            if (message.parentNode) {
                message.parentNode.removeChild(message);
            }
        }, 500);
    });
}, 5000);

// Focus sur le textarea d'édition si présent
document.addEventListener('DOMContentLoaded', function() {
    const editForms = document.querySelectorAll('.edit-comment-form textarea');
    if (editForms.length > 0) {
        editForms[0].focus();
        editForms[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    // Scroll vers le dernier commentaire si succès
    if (document.querySelector('.alert-success')) {
        const comments = document.querySelectorAll('.comment');
        if (comments.length > 0) {
            comments[comments.length - 1].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});
</script>

</body>
</html>