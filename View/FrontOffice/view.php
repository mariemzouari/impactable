<?php
require_once __DIR__ . '/../../Model/PostModel.php';
require_once __DIR__ . '/../../Model/LikeModel.php';
require_once __DIR__ . '/../../Model/CommentModel.php';

$postModel = new PostModel();
$likeModel = new LikeModel();
$commentModel = new CommentModel();

$id = $_GET['id'] ?? 0;
$post = $postModel->findById($id);


$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$requestUri = str_replace('\\', '/', $_SERVER['REQUEST_URI'] ?? '');
$baseUrl = '';
if (strpos($scriptName, '/index.php') !== false) {
    $baseUrl = substr($scriptName, 0, strpos($scriptName, '/index.php'));
} elseif (strpos($requestUri, '/index.php') !== false) {
    $baseUrl = substr($requestUri, 0, strpos($requestUri, '/index.php'));
} else {
    $baseUrl = rtrim(dirname($scriptName), '/');
}
if ($baseUrl === '/') $baseUrl = '';

if (!$post) {
    header("Location: {$baseUrl}/index.php?action=list");
    exit;
}

$user_id = $_SESSION['user_id'] ?? 0;
$post['likes_count'] = $likeModel->getLikesCount($id);
$post['user_liked'] = $likeModel->hasUserLiked($id, $user_id);

$comments = $commentModel->getCommentsByPost($id);

$user_name = $_SESSION['user_name'] ?? 'Visiteur';
$is_admin = ($_SESSION['role'] ?? '') == 'admin';
$is_logged_in = ($user_id > 0);

$old_comment = $_SESSION['old_comment'] ?? '';
$editing_comment_id = $_GET['edit_comment_id'] ?? 0;

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
    <link rel="stylesheet" href="<?= $baseUrl ?>/View/assets/css/style.css">
</head>
<body>
<!-- BASE_URL: <?= htmlspecialchars($baseUrl) ?> -->

<div class="container">
    <!-- Post Principal -->
    <div class="post">
        <div class="post-header">
            <div class="post-author">
                <div class="user-avatar"><?= strtoupper(substr($post['auteur'] ?? 'U', 0, 1)) ?></div>
                <div class="author-info">
                    <div class="author-name">
                                        <?= htmlspecialchars($post['auteur'] ?? 'Utilisateur') ?>
                                        <?php if ($is_admin && $post['Id_utilisateur'] != $_SESSION['user_id']): ?>
                                        <button type="button" class="report-icon-btn" title="Signaler cet utilisateur" onclick="openReportModal('user', <?= $post['Id_utilisateur'] ?>, '<?= addslashes(htmlspecialchars($post['auteur'] ?? 'Utilisateur')) ?>')">
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
            <button class="interaction-btn" onclick="window.location.href='<?= $baseUrl ?>/index.php?action=list'">
                <i class="fas fa-arrow-left"></i>
                <span>Retour au forum</span>
            </button>
            <button class="interaction-btn" onclick="window.location.href='<?= $baseUrl ?>/index.php?action=create'">
                <i class="fas fa-plus"></i>
                <span>Nouveau Post</span>
            </button>
            
            <?php if ($post['Id_utilisateur'] == $user_id || $is_admin): ?>
            <button class="interaction-btn" onclick="window.location.href='<?= $baseUrl ?>/index.php?action=edit&id=<?= $post['Id_post'] ?>'">
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
                    <form action="<?= $baseUrl ?>/index.php?action=add_comment" method="POST" id="commentForm">
                        <input type="hidden" name="post_id" value="<?= $post['Id_post'] ?>">
                        <input type="hidden" name="media_url" id="mediaUrl" value="">
                        <textarea name="contenu" placeholder="Partagez votre avis, posez une question ou apportez votre contribution..." id="commentTextarea" class="textarea"><?= htmlspecialchars($old_comment) ?></textarea>
                        
                        <div id="selectedMedia" style="display:none; margin: 10px 0;">
                            <img id="mediaPreview" src="" alt="GIF/Sticker" style="max-width: 150px; border-radius: 8px;">
                            <button type="button" onclick="removeMedia()" class="btn ghost small" style="margin-left: 10px;">
                                <i class="fas fa-times"></i> Retirer
                            </button>
                        </div>
                        
                        <div class="post-actions">
                            <button type="button" class="btn ghost" onclick="openCommentMediaModal()">
                                <i class="fas fa-image"></i> Ajouter Sticker/GIF
                            </button>
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
                                <div class="comment">
                                    <form action="<?= $baseUrl ?>/index.php?action=edit_comment" method="POST" class="edit-comment-form" id="editCommentForm">
                                        <input type="hidden" name="comment_id" value="<?= $comment['Id_commentaire'] ?>">
                                        <input type="hidden" name="post_id" value="<?= $post['Id_post'] ?>">
                                        <textarea name="contenu" id="editCommentTextarea" class="textarea"><?= htmlspecialchars($comment['contenu']) ?></textarea>
                                        <div class="edit-comment-actions">
                                            <button type="submit" class="btn primary">
                                                <i class="fas fa-save"></i> Enregistrer
                                            </button>
                                            <a href="<?= $baseUrl ?>/index.php?action=view&id=<?= $post['Id_post'] ?>" class="btn ghost">
                                                <i class="fas fa-times"></i> Annuler
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="comment" id="comment-<?= $comment['Id_commentaire'] ?>">
                                    <div class="comment-header">
                                        <div class="comment-author">
                                            <div class="user-avatar"><?= strtoupper(substr($comment['auteur'], 0, 1)) ?></div>
                                            <div class="author-info">
                                                <div class="author-name">
                                                    <?= htmlspecialchars($comment['auteur']) ?>
                                                    <?php if ($is_admin && $comment['Id_utilisateur'] != $_SESSION['user_id']): ?>
                                                    <button type="button" class="report-icon-btn" title="Signaler ce commentaire" onclick="openReportModal('comment', <?= $comment['Id_commentaire'] ?>, '<?= addslashes(htmlspecialchars($comment['auteur'])) ?>')">
                                                        <i class="fas fa-flag"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
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
                                        <?php if (!empty($comment['media_url'])): ?>
                                            <div style="margin-top: 12px; padding: 10px; background: #f9f9f9; border-radius: 8px; display: inline-block;">
                                                <img src="<?= htmlspecialchars($comment['media_url']) ?>" alt="GIF/Sticker" style="max-width: 250px; max-height: 250px; border-radius: 6px; display: block;">
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($is_your_comment): ?>
                                        <div class="comment-actions">
                                                          <a href="<?= $baseUrl ?>/index.php?action=view&id=<?= $post['Id_post'] ?>&edit_comment_id=<?= $comment['Id_commentaire'] ?>" 
                                                              class="btn ghost small">
                                                <i class="fas fa-edit"></i> Modifier
                                            </a>
                                            
                                                          <a href="<?= $baseUrl ?>/index.php?action=delete_comment&id=<?= $comment['Id_commentaire'] ?>&post_id=<?= $post['Id_post'] ?>" 
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
// Wrapper pour ouvrir le modal sticker/GIF avec vérification de login
function openCommentMediaModal() {
    <?php if (!$is_logged_in): ?>
        alert('Vous devez être connecté pour ajouter un sticker');
        return;
    <?php endif; ?>
    openMediaModal();
}

// Système de likes avec base de données
function toggleLike(element, postId) {
    const heartIcon = element.querySelector('i.fa-heart');
    const likesCount = element.querySelector('.likes-count');
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
    
    fetch('<?= $baseUrl ?>/index.php?action=toggle_like', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'post_id=' + postId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            likesCount.textContent = data.likes_count + ' j\'aime';
            if (data.user_liked) {
                heartIcon.classList.add('liked');
                heartIcon.style.color = '#e74c3c';
            } else {
                heartIcon.classList.remove('liked');
                heartIcon.style.color = '';
            }
        } else {
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
        if (!isCurrentlyLiked) {
            heartIcon.classList.remove('liked');
            heartIcon.style.color = '';
        } else {
            heartIcon.classList.add('liked');
            heartIcon.style.color = '#e74c3c';
        }
    });
}

// Validation des commentaires
document.addEventListener('DOMContentLoaded', function() {
    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        const commentTextarea = commentForm.querySelector('#commentTextarea');
        
        function hasRepeatedCharacters(text) {
            return /(.)\1{6,}/.test(text);
        }
        
        function isAllCaps(text) {
            return text.length > 20 && text === text.toUpperCase() && /[A-Z]/.test(text);
        }
        
        function checkComment() {
            const value = commentTextarea.value.trim();
            const mediaInput = document.getElementById('mediaUrl');
            const hasMedia = mediaInput && mediaInput.value && mediaInput.value.trim().length > 0;
            let errorMsg = '';

            if (!value && !hasMedia) {
                errorMsg = 'Commentaire obligatoire';
            } else if (value && value.length < 2) {
                errorMsg = 'Min 2 caractères';
            } else if (value && value.length > 1000) {
                errorMsg = 'Max 1000 caractères';
            } else if (value && hasRepeatedCharacters(value)) {
                errorMsg = 'Évitez la répétition excessive';
            } else if (value && isAllCaps(value)) {
                errorMsg = 'Évitez les majuscules continues';
            }
            
            if (!errorMsg) {
                commentTextarea.classList.remove('invalid');
                commentTextarea.classList.add('valid');
                clearFieldError(commentTextarea);
                return true;
            } else {
                commentTextarea.classList.remove('valid');
                commentTextarea.classList.add('invalid');
                showFieldError(commentTextarea, errorMsg);
                return false;
            }
        }
        
        function showFieldError(field, msg) {
            clearFieldError(field);
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + msg;
            field.parentElement.appendChild(errorDiv);
        }
        
        function clearFieldError(field) {
            const error = field.parentElement.querySelector('.field-error');
            if (error) error.remove();
        }
        
        commentTextarea.addEventListener('input', checkComment);
        commentTextarea.addEventListener('blur', checkComment);
        
        commentForm.addEventListener('submit', function(e) {
            if (!checkComment()) {
                e.preventDefault();
                let existingAlert = document.querySelector('.forum-body .alert');
                if (existingAlert) existingAlert.remove();
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-error';
                alertDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i><div><strong>Erreur de commentaire</strong></div>';
                commentForm.parentElement.insertBefore(alertDiv, commentForm);
            }
        });
    }
    
    const editCommentForm = document.getElementById('editCommentForm');
    if (editCommentForm) {
        const editCommentTextarea = editCommentForm.querySelector('#editCommentTextarea');
        
        function hasRepeatedCharacters(text) {
            return /(.)\1{6,}/.test(text);
        }
        
        function isAllCaps(text) {
            return text.length > 20 && text === text.toUpperCase() && /[A-Z]/.test(text);
        }
        
        function checkEditComment() {
            const value = editCommentTextarea.value.trim();
            let errorMsg = '';
            
            if (!value) {
                errorMsg = 'Commentaire obligatoire';
            } else if (value.length < 2) {
                errorMsg = 'Min 2 caractères';
            } else if (value.length > 1000) {
                errorMsg = 'Max 1000 caractères';
            } else if (hasRepeatedCharacters(value)) {
                errorMsg = 'Évitez la répétition excessive';
            } else if (isAllCaps(value)) {
                errorMsg = 'Évitez les majuscules continues';
            }
            
            if (!errorMsg) {
                editCommentTextarea.classList.remove('invalid');
                editCommentTextarea.classList.add('valid');
                clearEditError();
                return true;
            } else {
                editCommentTextarea.classList.remove('valid');
                editCommentTextarea.classList.add('invalid');
                showEditError(errorMsg);
                return false;
            }
        }
        
        function showEditError(msg) {
            clearEditError();
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + msg;
            editCommentTextarea.parentElement.appendChild(errorDiv);
        }
        
        function clearEditError() {
            const error = editCommentTextarea.parentElement.querySelector('.field-error');
            if (error) error.remove();
        }
        
        editCommentTextarea.addEventListener('input', checkEditComment);
        editCommentTextarea.addEventListener('blur', checkEditComment);
        
        editCommentForm.addEventListener('submit', function(e) {
            if (!checkEditComment()) {
                e.preventDefault();
                let existingAlert = document.querySelector('.forum-body .alert-error');
                if (!existingAlert) {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-error';
                    alertDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i><div><strong>Correction nécessaire</strong></div>';
                    editCommentForm.parentElement.insertBefore(alertDiv, editCommentForm);
                }
            }
        });
        
        editCommentTextarea.focus();
        editCommentTextarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    const successMsg = document.getElementById('successMessage');
    if (successMsg) {
        setTimeout(() => {
            successMsg.style.opacity = '0';
            successMsg.style.transition = 'opacity 0.5s ease';
            setTimeout(() => successMsg.remove(), 500);
        }, 5000);
    }
    
    if (document.querySelector('.alert-success')) {
        const comments = document.querySelectorAll('.comment');
        if (comments.length > 0) {
            comments[comments.length - 1].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});

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

// Modal stickers/GIFs
let isModalOpen = false;

function openMediaModal() {
    if (isModalOpen) return;
    isModalOpen = true;
    
    const modal = `
    <div id="mediaModal" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);display:flex;align-items:center;justify-content:center;z-index:9999;">
        <div style="background:white;border-radius:12px;padding:20px;max-width:600px;width:90%;max-height:80vh;overflow-y:auto;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">
                <h3 style="margin:0;">Ajouter un Sticker ou GIF</h3>
                <button type="button" onclick="closeMediaModal()" style="border:none;background:none;font-size:24px;cursor:pointer;">&times;</button>
            </div>
            
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:15px;">
                <button type="button" onclick="showStickers()" class="btn primary" style="width:100%;" id="stickersTabBtn">
                    <i class="fas fa-star"></i> Stickers
                </button>
                <button type="button" onclick="showGifs()" class="btn primary" style="width:100%;" id="gifsTabBtn">
                    <i class="fas fa-film"></i> GIFs
                </button>
            </div>
            
            <div id="stickerSearch" style="margin-bottom:15px;">
                <input type="text" id="stickerQuery" placeholder="Chercher un sticker..." 
                       style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;"
                       onkeypress="if(event.key === 'Enter') searchStickers()">
                <button type="button" onclick="searchStickers()" class="btn primary" style="margin-top:8px;width:100%;">
                    <i class="fas fa-search"></i> Chercher
                </button>
            </div>
            
            <div id="gifSearch" style="margin-bottom:15px;display:none;">
                <input type="text" id="gifQuery" placeholder="Chercher un GIF..." 
                       style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;"
                       onkeypress="if(event.key === 'Enter') searchGifs()">
                <button type="button" onclick="searchGifs()" class="btn primary" style="margin-top:8px;width:100%;">
                    <i class="fas fa-search"></i> Chercher
                </button>
            </div>
            
            <div id="mediaContainer" style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:15px;min-height:200px;"></div>
        </div>
    </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modal);
    
    setTimeout(() => {
        const stickersBtn = document.getElementById('stickersTabBtn');
        const gifsBtn = document.getElementById('gifsTabBtn');
        if (stickersBtn) {
            stickersBtn.style.backgroundColor = '#5e6d3b';
            stickersBtn.style.color = 'white';
        }
        if (gifsBtn) {
            gifsBtn.style.backgroundColor = '#e1e8c9';
            gifsBtn.style.color = '#333';
        }
    }, 10);
    
    loadDefaultStickers();
}

function closeMediaModal() {
    const modal = document.getElementById('mediaModal');
    if (modal) {
        modal.remove();
        isModalOpen = false;
    }
}

function showStickers() {
    document.getElementById('stickerSearch').style.display = 'block';
    document.getElementById('gifSearch').style.display = 'none';
    const stickersBtn = document.getElementById('stickersTabBtn');
    const gifsBtn = document.getElementById('gifsTabBtn');
    if (stickersBtn) stickersBtn.style.backgroundColor = '#5e6d3b';
    if (stickersBtn) stickersBtn.style.color = 'white';
    if (gifsBtn) gifsBtn.style.backgroundColor = '#e1e8c9';
    if (gifsBtn) gifsBtn.style.color = '#333';
    loadDefaultStickers();
}

function showGifs() {
    document.getElementById('stickerSearch').style.display = 'none';
    document.getElementById('gifSearch').style.display = 'block';
    const stickersBtn = document.getElementById('stickersTabBtn');
    const gifsBtn = document.getElementById('gifsTabBtn');
    if (stickersBtn) stickersBtn.style.backgroundColor = '#e1e8c9';
    if (stickersBtn) stickersBtn.style.color = '#333';
    if (gifsBtn) gifsBtn.style.backgroundColor = '#5e6d3b';
    if (gifsBtn) gifsBtn.style.color = 'white';
    loadDefaultGifs();
}

const stickerCollection = [
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f60d.png', label: '😍 Amour' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f44f.png', label: '👏 Bravo' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f929.png', label: '🤩 Wow' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f4af.png', label: '💯 Parfait' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f680.png', label: '🚀 Lancé' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/2b50.png', label: '⭐ Star' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f389.png', label: '🎉 Fête' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f3c6.png', label: '🏆 Champion' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f44d.png', label: '👍 OK' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f525.png', label: '🔥 Fire' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f973.png', label: '🥳 Party' },
    
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f602.png', label: '😂 Rire' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f62d.png', label: '😭 Pleurer' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f621.png', label: '😡 Colère' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f614.png', label: '😔 Triste' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f633.png', label: '😳 Gêné' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f631.png', label: '😱 Cri' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f60e.png', label: '😎 Cool' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f970.png', label: '🥰 Amoureux' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f928.png', label: '🤨 Sceptique' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f975.png', label: '🥵 Chaud' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f64c.png', label: '🙌 Bravo 2' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f44a.png', label: '👊 Coup' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f91d.png', label: '🤝 Poignée' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f4aa.png', label: '💪 Muscle' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f64f.png', label: '🙏 Merci' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f4a1.png', label: '💡 Idée' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f4a5.png', label: '💥 Explosion' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f4ab.png', label: '💫 Étourdi' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f4a3.png', label: '💣 Bombe' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f3b6.png', label: '🎶 Musique' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f4b0.png', label: '💰 Argent' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f436.png', label: '🐶 Chien' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f431.png', label: '🐱 Chat' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f42d.png', label: '🐭 Souris' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f438.png', label: '🐸 Grenouille' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f981.png', label: '🦁 Lion' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f355.png', label: '🍕 Pizza' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f354.png', label: '🍔 Burger' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/2615.png', label: '☕ Café' },
    { url: 'https://cdn.jsdelivr.net/npm/emoji-datasource-apple@7.0.2/img/apple/64/1f36a.png', label: '🍪 Cookie' }
];

const gifCollection = [
    { url: 'https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExd3FocHQxMDBwYnM0NWc5MGNnNjQydm9uZ3R2eWZzazB6dHZpNmVwOSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/M9NbzZjAcxq9jS9LZJ/giphy.gif', title: 'Merci' },
    { url: 'https://media2.giphy.com/media/v1.Y2lkPTc5MGI3NjExeHVyZWFhd2pmeGk0b2Nud3czejVnYWpkOWpkenh5aTJoN2l4MmcwcSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/fUYhyT9IjftxrxJXcE/giphy.gif', title: 'Drôle' },
    { url: 'https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExajR2eTVrOG00cWMyN3VvMWthaW04eHdpMTZ0MGV0ZDl1dTZmbGNweSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/g9582DNuQppxC/giphy.gif', title: 'Félicitations' },
    { url: 'https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExbXJkaXI1OWIxY3NvZGNscnhrZTZodDVrMXA3ZThiMDl0a2VhbXVkYiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/blSTtZehjAZ8I/giphy.gif', title: 'Dansant' },
    { url: 'https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExYjhlYzB4aGJjMWJzM3ZsZWhlMXBjMGo3bTRoM21qZ2RpeTZ6aWlubiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/Up1g60KcHfLnQ4atUe/giphy.gif', title: 'Bye' },
    { url: 'https://media3.giphy.com/media/v1.Y2lkPTc5MGI3NjExZHI3Y3lyZzl3MWRhM3hseHpuZjMyN2Y0aDJpZnkwNGo2MnJhbzkycyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/Ty9Sg8oHghPWg/giphy.gif', title: 'Triste' },
    { url: 'https://media2.giphy.com/media/v1.Y2lkPTc5MGI3NjExdm5vcWp5Y2hzY2FkZHpzanA4cGViNXB5Y2NjODg5Y2IwbzQ5ZjUyOCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/WuGSL4LFUMQU/giphy.gif', title: 'Choqué' },
    { url: 'https://media0.giphy.com/media/v1.Y2lkPTc5MGI3NjExcTA5YnY4MXU1OTBuN3lkb2Z1YnVuNjJ4enVtOHZsZTd5aWo3bmF5aiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/Fr51PdEf2NxOE/giphy.gif', title: 'En colère' },
    { url: 'https://media0.giphy.com/media/v1.Y2lkPTc5MGI3NjExb2UxNjdqd3NudjhqeGM3Zmluenh3bmt3NGFkcnlrcnhocXlid280OCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/NoPNkgzHD8HSK6Nr5l/giphy.gif', title: 'Yes' },
    { url: 'https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExOXA2dmFxYm9mZXhvcm55eHJudDhqeGRxejhvY2U5ajl1ZjY8Zmx5NyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/VduFvPwm3gfGO8duNN/giphy.gif', title: 'Calin' },
    { url: 'https://media3.giphy.com/media/v1.Y2lkPTc5MGI3NjExM3M4cGJ3aG41NmpwZWI0NHgxc3kxZGp2OW01NW9udG1laHd4bHg1aCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/LPHXLKEOZw6T6/giphy.gif', title: 'Fou' },
    
    { url: 'https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExdzd6emlzYjV4eHhjdWVoOXg2aTYxMTh4cjM1eHFrMHBwaWUzbTUxZSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/YDB4EF3U6i6IM/giphy.gif', title: 'Amour' },
    { url: 'https://media0.giphy.com/media/v1.Y2lkPTc5MGI3NjExY2ltZGdtcm82eG0wMnNld2MxaXg3a2plNXZjOWUxN3B0bTAxcWJndyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/KFt2DA9T82paOA1Yci/giphy.gif', title: 'Hein' },
    { url: 'https://media2.giphy.com/media/v1.Y2lkPTc5MGI3NjExbmdqMWt1eHlldTBuMmp1dzd4ejRncGtwMWU5ZDZqNWx0ZHNoNDlpaiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/YOkrK8agZLEk2cXeLi/giphy.gif', title: 'No' },
    { url: 'https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExanc3Y2E2NHQ5Ynp0dWx2NGdzdXZrM2s0Y2owZHY0bjl0eWRjcmVwbSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/bEs40jYsdQjmM/giphy.gif', title: 'Fatigué' }
];


function loadDefaultStickers() {
    const container = document.getElementById('mediaContainer');
    container.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Chargement des stickers...</div>';
    
    setTimeout(() => {
        container.innerHTML = '';
        stickerCollection.forEach(sticker => {
            const div = document.createElement('div');
            div.style.cursor = 'pointer';
            div.style.borderRadius = '8px';
            div.style.overflow = 'hidden';
            div.style.border = '2px solid #eee';
            div.style.transition = 'all 0.2s';
            div.style.position = 'relative';
            div.style.backgroundColor = '#fafafa';
            div.style.aspectRatio = '1';
            div.style.display = 'flex';
            div.style.alignItems = 'center';
            div.style.justifyContent = 'center';
            
            const img = document.createElement('img');
            img.src = sticker.url;
            img.alt = sticker.label;
            img.style.width = '80%';
            img.style.height = '80%';
            img.style.objectFit = 'contain';
            img.style.padding = '5px';
            img.loading = 'lazy';
            
            const label = document.createElement('div');
            label.style.position = 'absolute';
            label.style.bottom = '0';
            label.style.left = '0';
            label.style.right = '0';
            label.style.background = 'linear-gradient(transparent,rgba(0,0,0,0.7))';
            label.style.color = 'white';
            label.style.padding = '6px 3px';
            label.style.fontSize = '11px';
            label.style.textAlign = 'center';
            label.style.fontWeight = '500';
            label.textContent = sticker.label;
            
            div.appendChild(img);
            div.appendChild(label);
            
            div.onmouseover = () => {
                div.style.borderColor = '#b47b47';
                div.style.transform = 'scale(1.03)';
                div.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
            };
            
            div.onmouseout = () => {
                div.style.borderColor = '#eee';
                div.style.transform = 'scale(1)';
                div.style.boxShadow = 'none';
            };
            
            div.onclick = () => {
                selectMedia(sticker.url);
            };
            
            container.appendChild(div);
        });
    }, 50);
}

function loadDefaultGifs() {
    const container = document.getElementById('mediaContainer');
    container.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Chargement des GIFs...</div>';
    
    setTimeout(() => {
        container.innerHTML = '';
        gifCollection.forEach((gif, index) => {
            setTimeout(() => {
                const div = document.createElement('div');
                div.style.cursor = 'pointer';
                div.style.borderRadius = '8px';
                div.style.overflow = 'hidden';
                div.style.border = '2px solid #eee';
                div.style.transition = 'all 0.2s';
                div.style.backgroundColor = '#f9f9f9';
                div.style.position = 'relative';
                
                const img = document.createElement('img');
                img.src = gif.url;
                img.alt = gif.title;
                img.style.width = '100%';
                img.style.height = '120px';
                img.style.objectFit = 'cover';
                img.style.display = 'block';
                img.loading = 'lazy';
                
                img.onerror = function() {
                    this.src = 'https://via.placeholder.com/200x120?text=GIF+indisponible';
                };
                
                const label = document.createElement('div');
                label.style.position = 'absolute';
                label.style.bottom = '0';
                label.style.left = '0';
                label.style.right = '0';
                label.style.background = 'linear-gradient(transparent,rgba(0,0,0,0.6))';
                label.style.color = 'white';
                label.style.padding = '6px 8px';
                label.style.fontSize = '12px';
                label.style.textAlign = 'center';
                label.style.fontWeight = '600';
                label.textContent = gif.title || '';
                
                div.appendChild(img);
                div.appendChild(label);
                
                div.onmouseover = () => {
                    div.style.borderColor = '#b47b47';
                    div.style.transform = 'scale(1.02)';
                    div.style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
                };
                
                div.onmouseout = () => {
                    div.style.borderColor = '#eee';
                    div.style.transform = 'scale(1)';
                    div.style.boxShadow = 'none';
                };
                
                div.onclick = () => {
                    selectMedia(gif.url);
                };
                
                container.appendChild(div);
            }, index * 50);
        });
    }, 80);
}

function selectMedia(url) {
    closeMediaModal();
    const mediaUrlInput = document.getElementById('mediaUrl');
    const selectedMediaDiv = document.getElementById('selectedMedia');
    const mediaPreview = document.getElementById('mediaPreview');
    if (mediaUrlInput) mediaUrlInput.value = url;
    if (selectedMediaDiv) selectedMediaDiv.style.display = 'block';
    if (mediaPreview) mediaPreview.src = url;
}

function removeMedia() {
    const mediaUrlInput = document.getElementById('mediaUrl');
    const selectedMediaDiv = document.getElementById('selectedMedia');
    const mediaPreview = document.getElementById('mediaPreview');
    if (mediaUrlInput) mediaUrlInput.value = '';
    if (selectedMediaDiv) selectedMediaDiv.style.display = 'none';
    if (mediaPreview) mediaPreview.src = '';
}

function searchStickers() {
    const q = document.getElementById('stickerQuery').value.trim().toLowerCase();
    const container = document.getElementById('mediaContainer');
    container.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Recherche...</div>';
    setTimeout(() => {
        const results = stickerCollection.filter(s => s.label.toLowerCase().includes(q));
        container.innerHTML = '';
        if (results.length === 0) {
            container.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:20px;">Aucun résultat</div>';
            return;
        }
        results.forEach(s => {
            const div = document.createElement('div');
            div.style.cursor = 'pointer';
            div.style.borderRadius = '8px';
            div.style.overflow = 'hidden';
            div.style.border = '2px solid #eee';
            div.style.transition = 'all 0.2s';
            div.style.position = 'relative';
            div.style.backgroundColor = '#fafafa';
            div.style.aspectRatio = '1';
            const img = document.createElement('img');
            img.src = s.url; img.alt = s.label; img.style.width = '80%'; img.style.height = '80%'; img.style.objectFit = 'contain'; img.loading = 'lazy';
            div.appendChild(img); container.appendChild(div);
            div.onclick = () => selectMedia(s.url);
        });
    }, 120);
}

function searchGifs() {
    const q = document.getElementById('gifQuery').value.trim().toLowerCase();
    const container = document.getElementById('mediaContainer');
    container.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Recherche GIFs...</div>';
    setTimeout(() => {
        const results = gifCollection.filter(g => (g.title || '').toLowerCase().includes(q));
        container.innerHTML = '';
        if (results.length === 0) {
            container.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:20px;">Aucun résultat</div>';
            return;
        }
        results.forEach(g => {
            const div = document.createElement('div');
            div.style.cursor = 'pointer';
            div.style.borderRadius = '8px';
            div.style.overflow = 'hidden';
            div.style.border = '2px solid #eee';
            div.style.transition = 'all 0.2s';
            div.style.backgroundColor = '#f9f9f9';
            const img = document.createElement('img');
            img.src = g.url; img.alt = g.title; img.style.width = '100%'; img.style.height = '120px'; img.style.objectFit = 'cover'; img.loading = 'lazy';
            img.onerror = function() { this.src = 'https://via.placeholder.com/200x120?text=GIF+indispo'; };
            div.appendChild(img); container.appendChild(div);
            div.onclick = () => selectMedia(g.url);
        });
    }, 120);
}

// Fonctions utilitaires pour signalement
let reportContext = { target_type: null, target_id: null, target_name: null };

function openReportModal(targetType, targetId, targetName) {
    const IS_ADMIN = <?= json_encode($is_admin ? true : false) ?>;
    const CURRENT_USER_ID = <?= json_encode($user_id) ?>;
    
    if (!IS_ADMIN) {
        alert('Vous devez être administrateur pour signaler.');
        return;
    }

    if (parseInt(CURRENT_USER_ID) === parseInt(targetId)) {
        alert("Vous ne pouvez pas signaler l'auteur de votre commentaire.");
        return;
    }

    reportContext = { target_type: targetType, target_id: targetId, target_name: targetName };
    
    const targetLabel = {
        'post': 'Post',
        'comment': 'Commentaire',
        'user': 'Utilisateur'
    }[targetType] || 'Contenu';
    
    document.getElementById('reportTargetInfo').textContent = targetLabel + ': ' + (targetName || targetId);
    document.getElementById('reportReason').value = '';
    document.getElementById('charCount').textContent = '0';
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

    fetch('<?= $baseUrl ?>/View/FrontOffice/report_user.php', {
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
            const btns = document.querySelectorAll('[onclick*="openReportModal(\'' + reportContext.target_type + '\',' + reportContext.target_id + '"]');
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
    });
}
</script>

<!-- Modal pour signaler un post/commentaire/utilisateur (admin only) -->
<div id="reportModal" class="modal-backdrop" style="display: none;">
    <div class="report-modal">
        <div class="report-modal-header">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-circle" style="font-size: 1.5rem; color: #e74c3c;"></i>
                <h3 style="margin:0; color: #2c3e50;">Signaler un contenu</h3>
            </div>
            <button class="modal-close" onclick="closeReportModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="report-modal-body">
            <div class="report-target-card">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem; color: var(--moss);"></i>
                    <div>
                        <p style="margin: 0; font-size: 0.9rem; color: var(--muted);">Contenu</p>
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

</body>
</html>

