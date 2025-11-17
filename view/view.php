<?php
// PROJT/view/view.php
// session_start() a été retiré car déjà appelé dans control.php

if (!isset($post)) {
    header('Location: ../control/control.php?action=list');
    exit;
}

// Récupérer l'ID utilisateur depuis la session (déjà définie dans control.php)
$user_id = $_SESSION['user_id'] ?? 0;

// Commentaires d'exemple pour démonstration
$example_comments = [
    [
        'Id_commentaire' => 1,
        'auteur' => 'Vous', // Votre nom d'utilisateur
        'contenu' => 'Ceci est mon commentaire. Je peux le modifier ou le supprimer car je suis l\'auteur.',
        'date_creation' => date('Y-m-d H:i:s', strtotime('-1 hour')),
        'Id_utilisateur' => $user_id // Vous êtes l'auteur
    ],
    [
        'Id_commentaire' => 2,
        'auteur' => 'Marie Martin',
        'contenu' => 'Ceci est le commentaire d\'un autre utilisateur. Je ne peux pas le modifier.',
        'date_creation' => date('Y-m-d H:i:s', strtotime('-2 hours')),
        'Id_utilisateur' => 999 // Un autre utilisateur
    ]
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['titre']) ?> - ImpactAble</title>

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
            --light-sage: #e1e8c9;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--sand);
            margin: 0;
            padding: 20px;
            color: var(--brown);
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            background: var(--white);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .post-header {
            border-bottom: 2px solid var(--sage);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .post-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--brown);
            margin-bottom: 10px;
        }

        .post-meta {
            display: flex;
            gap: 20px;
            color: #6b4b44;
            font-size: 0.9rem;
            flex-wrap: wrap;
        }

        .post-author {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .post-content {
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .post-image {
            width: 100%;
            border-radius: 10px;
            margin: 20px 0;
        }

        .actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--sage);
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: var(--moss);
            color: white;
        }

        .btn-primary:hover {
            background: #4d5a2a;
        }

        .btn-secondary {
            background: var(--sage);
            color: var(--brown);
        }

        .btn-secondary:hover {
            background: var(--moss);
            color: white;
        }

        .btn-danger {
            background: transparent;
            color: var(--copper);
            border: 2px solid var(--copper);
        }

        .btn-danger:hover {
            background: var(--copper);
            color: white;
        }

        .btn-back {
            background: transparent;
            color: var(--moss);
            border: 2px solid var(--moss);
        }

        .btn-back:hover {
            background: var(--moss);
            color: white;
        }

        .category-badge {
            display: inline-block;
            padding: 5px 15px;
            background: var(--sage);
            color: var(--brown);
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        /* Section Commentaires */
        .comments-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid var(--sage);
        }

        .comments-header {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--brown);
        }

        .comment-form {
            background: var(--sand);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .comment-form textarea {
            width: 100%;
            min-height: 100px;
            padding: 12px;
            border: 2px solid var(--sage);
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.95rem;
            resize: vertical;
            margin-bottom: 10px;
        }

        .comment-form textarea:focus {
            outline: none;
            border-color: var(--moss);
        }

        .comment {
            background: var(--sand);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .comment.editing {
            background: var(--light-sage);
            border-left: 4px solid var(--copper);
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .comment-author {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .comment-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--copper);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .comment-info {
            display: flex;
            flex-direction: column;
        }

        .comment-name {
            font-weight: 600;
            color: var(--brown);
            font-size: 0.9rem;
        }

        .comment-date {
            font-size: 0.75rem;
            color: #6b4b44;
        }

        .comment-content {
            margin: 10px 0;
            line-height: 1.6;
            color: var(--brown);
        }

        .comment-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .comment-btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: none;
            cursor: pointer;
            background: transparent;
        }

        .comment-btn.edit {
            color: var(--moss);
            border: 1px solid var(--moss);
        }

        .comment-btn.edit:hover {
            background: var(--moss);
            color: white;
        }

        .comment-btn.delete {
            color: var(--copper);
            border: 1px solid var(--copper);
        }

        .comment-btn.delete:hover {
            background: var(--copper);
            color: white;
        }

        .no-comments {
            text-align: center;
            padding: 30px;
            color: #6b4b44;
            font-style: italic;
        }

        .edit-form {
            display: none;
            margin-top: 10px;
        }

        .edit-form.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .edit-form textarea {
            width: 100%;
            min-height: 80px;
            padding: 10px;
            border: 2px solid var(--sage);
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.9rem;
            margin-bottom: 8px;
            resize: vertical;
        }

        .edit-form textarea:focus {
            outline: none;
            border-color: var(--moss);
        }

        .edit-form-actions {
            display: flex;
            gap: 8px;
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 0.8rem;
        }

        /* Style pour vos propres commentaires */
        .your-comment {
            border: 2px solid var(--sage);
        }

        /* Messages de notification */
        .message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: #4CAF50;
            color: white;
            border-radius: 5px;
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }

        .message.error {
            background: #f44336;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
            
            .post-title {
                font-size: 1.5rem;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .btn {
                justify-content: center;
            }

            .comment-header {
                flex-direction: column;
                gap: 10px;
            }

            .comment-actions {
                flex-wrap: wrap;
            }

            .edit-form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<div class="container">
    <div class="post-header">
        <div class="category-badge"><?= ucfirst($post['categorie']) ?></div>
        <h1 class="post-title"><?= htmlspecialchars($post['titre']) ?></h1>
        <div class="post-meta">
            <div class="post-author">
                <i class="fas fa-user"></i>
                <span><?= htmlspecialchars($post['auteur']) ?></span>
            </div>
            <span><i class="far fa-calendar"></i> <?= date('d/m/Y à H:i', strtotime($post['date_creation'])) ?></span>
            <span><i class="far fa-heart"></i> <?= $post['likes'] ?> likes</span>
        </div>
    </div>

    <div class="post-content">
        <?= nl2br(htmlspecialchars($post['contenu'])) ?>
        
        <?php if (!empty($post['piece_jointe'])): ?>
            <img src="../<?= $post['piece_jointe'] ?>" class="post-image" alt="Image du post">
        <?php endif; ?>
    </div>

    <div class="actions">
        <a href="../control/control.php?action=list" class="btn btn-back">
            <i class="fas fa-arrow-left"></i> Retour au forum
        </a>
        <?php if ($post['Id_utilisateur'] == $user_id): ?>
            <a href="../control/control.php?action=edit&id=<?= $post['Id_post'] ?>" class="btn btn-secondary">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="../control/control.php?action=delete&id=<?= $post['Id_post'] ?>" 
               class="btn btn-danger" 
               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce post ?')">
                <i class="fas fa-trash"></i> Supprimer
            </a>
        <?php endif; ?>
        <a href="../control/control.php?action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau Post
        </a>
    </div>

    <!-- Section Commentaires -->
    <div class="comments-section">
        <h2 class="comments-header">
            <i class="far fa-comments"></i> 
            Commentaires (<span id="comments-count"><?= isset($comments) && !empty($comments) ? count($comments) : count($example_comments) ?></span>)
        </h2>

        <!-- Formulaire d'ajout de commentaire -->
        <div class="comment-form">
            <form action="../control/control.php?action=add_comment" method="POST" id="commentForm">
                <input type="hidden" name="post_id" value="<?= $post['Id_post'] ?>">
                <textarea name="contenu" placeholder="Ajoutez un commentaire..." required id="commentText"></textarea>
                <button type="submit" class="btn btn-primary" id="submitComment">
                    <i class="fas fa-paper-plane"></i> Publier
                </button>
            </form>
        </div>

        <!-- Liste des commentaires -->
        <div class="comments-list" id="commentsList">
            <?php 
            // Utiliser les commentaires de la base de données s'ils existent, sinon utiliser les exemples
            $display_comments = isset($comments) && !empty($comments) ? $comments : $example_comments;
            ?>
            
            <?php if (empty($display_comments)): ?>
                <div class="no-comments">
                    Aucun commentaire pour le moment. Soyez le premier à commenter !
                </div>
            <?php else: ?>
                <?php foreach ($display_comments as $comment): ?>
                    <?php 
                    // Déterminer si c'est votre commentaire
                    $is_your_comment = ($comment['Id_utilisateur'] == $user_id);
                    ?>
                    <div class="comment <?= $is_your_comment ? 'your-comment' : '' ?>" id="comment-<?= $comment['Id_commentaire'] ?>">
                        <div class="comment-header">
                            <div class="comment-author">
                                <div class="comment-avatar">
                                    <?= strtoupper(substr($comment['auteur'], 0, 1)) ?>
                                </div>
                                <div class="comment-info">
                                    <span class="comment-name"><?= htmlspecialchars($comment['auteur']) ?></span>
                                    <span class="comment-date">
                                        <?= date('d/m/Y à H:i', strtotime($comment['date_creation'])) ?>
                                        <?php if ($is_your_comment): ?>
                                            <span style="color: var(--moss); font-weight: 600;">(Votre commentaire)</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="comment-content" id="content-<?= $comment['Id_commentaire'] ?>">
                            <?= nl2br(htmlspecialchars($comment['contenu'])) ?>
                        </div>

                        <!-- BOUTONS MODIFIER ET SUPPRIMER - VISIBLES UNIQUEMENT POUR VOS PROPRES COMMENTAIRES -->
                        <?php if ($is_your_comment): ?>
                            <div class="comment-actions">
                                <!-- Bouton Modifier - seulement pour vos commentaires -->
                                <button class="comment-btn edit" onclick="toggleEditForm(<?= $comment['Id_commentaire'] ?>)">
                                    <i class="fas fa-edit"></i> Modifier
                                </button>
                                
                                <!-- Bouton Supprimer - seulement pour vos commentaires -->
                                <button class="comment-btn delete" onclick="deleteComment(<?= $comment['Id_commentaire'] ?>, <?= $post['Id_post'] ?>)">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>

                            <!-- Formulaire d'édition (caché par défaut) -->
                            <div class="edit-form" id="edit-form-<?= $comment['Id_commentaire'] ?>">
                                <form id="editForm-<?= $comment['Id_commentaire'] ?>">
                                    <input type="hidden" name="comment_id" value="<?= $comment['Id_commentaire'] ?>">
                                    <input type="hidden" name="post_id" value="<?= $post['Id_post'] ?>">
                                    <textarea name="contenu" required><?= htmlspecialchars($comment['contenu']) ?></textarea>
                                    <div class="edit-form-actions">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-check"></i> Enregistrer
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEditForm(<?= $comment['Id_commentaire'] ?>)">
                                            <i class="fas fa-times"></i> Annuler
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Fonction pour basculer l'affichage du formulaire d'édition
function toggleEditForm(commentId) {
    const commentElement = document.getElementById('comment-' + commentId);
    const editForm = document.getElementById('edit-form-' + commentId);
    const content = document.getElementById('content-' + commentId);
    
    if (editForm.classList.contains('active')) {
        // Mode affichage
        editForm.classList.remove('active');
        content.style.display = 'block';
        commentElement.classList.remove('editing');
    } else {
        // Mode édition
        editForm.classList.add('active');
        content.style.display = 'none';
        commentElement.classList.add('editing');
        
        // Focus sur le textarea
        const textarea = editForm.querySelector('textarea');
        if (textarea) {
            textarea.focus();
        }
    }
}

// Fonction pour ajouter un commentaire via AJAX
function addComment(postId, content) {
    const formData = new FormData();
    formData.append('post_id', postId);
    formData.append('contenu', content);

    fetch('../control/control.php?action=add_comment', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur réseau');
        }
        return response.text();
    })
    .then(data => {
        // Recharger la page pour voir le nouveau commentaire
        location.reload();
    })
    .catch(error => {
        console.error('Erreur:', error);
        showMessage('Erreur lors de l\'ajout du commentaire', 'error');
        
        // Réactiver le bouton
        const submitBtn = document.getElementById('submitComment');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Publier';
    });
}

// Fonction pour modifier un commentaire via AJAX
function updateComment(commentId, content, postId) {
    const formData = new FormData();
    formData.append('comment_id', commentId);
    formData.append('contenu', content);
    formData.append('post_id', postId);

    fetch('../control/control.php?action=edit_comment', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur réseau');
        }
        return response.text();
    })
    .then(data => {
        // Mettre à jour le contenu du commentaire sans recharger
        const contentElement = document.getElementById(`content-${commentId}`);
        contentElement.innerHTML = nl2br(escapeHtml(content));
        
        // Revenir en mode affichage
        toggleEditForm(commentId);
        
        // Afficher un message de succès
        showMessage('Commentaire modifié avec succès!', 'success');
        
        // Réactiver le bouton
        const form = document.getElementById(`editForm-${commentId}`);
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check"></i> Enregistrer';
    })
    .catch(error => {
        console.error('Erreur:', error);
        showMessage('Erreur lors de la modification du commentaire', 'error');
        
        // Réactiver le bouton
        const form = document.getElementById(`editForm-${commentId}`);
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check"></i> Enregistrer';
    });
}

// Fonction pour supprimer un commentaire via AJAX
function deleteComment(commentId, postId) {
    if (!confirm('Voulez-vous vraiment supprimer votre commentaire ?')) {
        return;
    }

    fetch(`../control/control.php?action=delete_comment&id=${commentId}&post_id=${postId}`)
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur réseau');
        }
        return response.text();
    })
    .then(data => {
        // Supprimer l'élément du DOM
        const commentElement = document.getElementById(`comment-${commentId}`);
        if (commentElement) {
            commentElement.remove();
        }
        
        // Mettre à jour le compteur de commentaires
        updateCommentsCount();
        
        // Afficher un message de succès
        showMessage('Commentaire supprimé avec succès!', 'success');
    })
    .catch(error => {
        console.error('Erreur:', error);
        showMessage('Erreur lors de la suppression du commentaire', 'error');
    });
}

// Fonctions utilitaires
function nl2br(str) {
    return str.replace(/\n/g, '<br>');
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function showMessage(message, type) {
    // Créer un élément de message temporaire
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type === 'error' ? 'error' : ''}`;
    messageDiv.textContent = message;
    
    document.body.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
}

function updateCommentsCount() {
    const comments = document.querySelectorAll('.comment');
    const commentsCount = document.getElementById('comments-count');
    if (commentsCount) {
        commentsCount.textContent = comments.length;
    }
}

// Gestion des événements
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du formulaire d'ajout de commentaire
    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const postId = this.querySelector('input[name="post_id"]').value;
            const content = this.querySelector('textarea[name="contenu"]').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            
            if (!content.trim()) {
                alert('Veuillez écrire un commentaire');
                return;
            }
            
            // Désactiver le bouton pendant l'envoi
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publication...';
            
            addComment(postId, content);
        });
    }
    
    // Gestion des formulaires de modification de commentaire
    document.querySelectorAll('[id^="editForm-"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const commentId = this.querySelector('input[name="comment_id"]').value;
            const postId = this.querySelector('input[name="post_id"]').value;
            const content = this.querySelector('textarea[name="contenu"]').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            
            if (!content.trim()) {
                alert('Le commentaire ne peut pas être vide');
                return;
            }
            
            // Désactiver le bouton pendant l'envoi
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
            
            updateComment(commentId, content, postId);
        });
    });
});

// Message de démonstration
console.log('Vous pouvez modifier et supprimer vos propres commentaires (marqués comme "Votre commentaire")');
</script>

</body>
</html>