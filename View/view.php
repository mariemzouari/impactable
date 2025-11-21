<?php
if (!isset($post)) {
    header('Location: ../controller/control.php?action=list');
    exit;
}

$user_id = $user_id ?? $_SESSION['user_id'] ?? 0;
$user_name = $user_name ?? $_SESSION['user_name'] ?? 'Visiteur';
$is_admin = $is_admin ?? $_SESSION['is_admin'] ?? false;

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
    <style>
        :root { --brown: #4b2e16; --copper: #b47b47; --moss: #5e6d3b; --sage: #a9b97d; --sand: #f4ecdd; --white: #fffaf5; }
        body { font-family: 'Inter', sans-serif; background: var(--sand); margin: 0; padding: 20px; color: var(--brown); }
        .container { max-width: 800px; margin: 40px auto; background: var(--white); padding: 40px; border-radius: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .post-header { border-bottom: 2px solid var(--sage); padding-bottom: 20px; margin-bottom: 30px; }
        .post-title { font-size: 2rem; font-weight: 700; color: var(--brown); margin-bottom: 10px; }
        .post-meta { display: flex; gap: 20px; color: #6b4b44; font-size: 0.9rem; flex-wrap: wrap; }
        .post-content { font-size: 1.1rem; line-height: 1.7; margin-bottom: 30px; }
        .actions { display: flex; gap: 15px; margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--sage); flex-wrap: wrap; }
        .btn { padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; font-size: 0.9rem; }
        .btn-primary { background: var(--moss); color: white; }
        .btn-primary:hover { background: #4d5a2a; }
        .btn-back { background: transparent; color: var(--moss); border: 2px solid var(--moss); }
        .btn-back:hover { background: var(--moss); color: white; }
        .comments-section { margin-top: 40px; padding-top: 30px; border-top: 2px solid var(--sage); }
        .comments-header { font-size: 1.5rem; font-weight: 700; margin-bottom: 20px; color: var(--brown); }
        .comment-form { background: var(--sand); padding: 20px; border-radius: 12px; margin-bottom: 30px; }
        .comment-form textarea { width: 100%; min-height: 100px; padding: 12px; border: 2px solid var(--sage); border-radius: 10px; font-family: inherit; font-size: 0.95rem; resize: vertical; margin-bottom: 10px; }
        .comment { background: var(--sand); padding: 20px; border-radius: 12px; margin-bottom: 15px; }
        .comment-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
        .comment-author { display: flex; align-items: center; gap: 10px; }
        .comment-avatar { width: 35px; height: 35px; border-radius: 50%; background: var(--copper); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.8rem; }
        .comment-name { font-weight: 600; color: var(--brown); font-size: 0.9rem; }
        .comment-date { font-size: 0.75rem; color: #6b4b44; }
        .comment-content { margin: 10px 0; line-height: 1.6; color: var(--brown); }
        .comment-actions { display: flex; gap: 10px; margin-top: 10px; }
        .comment-btn { padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 500; text-decoration: none; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 5px; border: none; cursor: pointer; background: transparent; }
        .comment-btn.edit { color: var(--moss); border: 1px solid var(--moss); }
        .comment-btn.edit:hover { background: var(--moss); color: white; }
        .comment-btn.delete { color: var(--copper); border: 1px solid var(--copper); }
        .comment-btn.delete:hover { background: var(--copper); color: white; }
        .no-comments { text-align: center; padding: 30px; color: #6b4b44; font-style: italic; }
        .alert { padding: 12px 16px; margin: 15px 0; border-radius: 8px; font-weight: 500; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .edit-comment-form { background: var(--white); padding: 15px; border-radius: 8px; border: 2px solid var(--sage); margin-top: 10px; }
        .edit-comment-form textarea { width: 100%; min-height: 80px; padding: 10px; border: 1px solid var(--sage); border-radius: 6px; font-family: inherit; font-size: 0.9rem; resize: vertical; margin-bottom: 10px; }
        .edit-comment-actions { display: flex; gap: 10px; }
        .invalid { border-color: #f44336 !important; background-color: #ffebee !important; }
        @media (max-width: 768px) { .container { margin: 20px; padding: 20px; } .post-title { font-size: 1.5rem; } .actions, .comment-actions { flex-direction: column; } }
    </style>
</head>
<body>

<div class="container">
    <div class="post-header">
        <h1 class="post-title"><?= htmlspecialchars($post['titre']) ?></h1>
        <div class="post-meta">
            <div class="post-author">
                <i class="fas fa-user"></i>
                <span><?= htmlspecialchars($post['auteur']) ?></span>
            </div>
            <span><i class="far fa-calendar"></i> <?= date('d/m/Y à H:i', strtotime($post['date_creation'])) ?></span>
            <span><i class="fas fa-tag"></i> <?= ucfirst($post['categorie']) ?></span>
        </div>
    </div>

    <div class="post-content">
        <?= nl2br(htmlspecialchars($post['contenu'])) ?>
    </div>

    <?php if (!empty($post['piece_jointe'])): ?>
        <div style="margin: 20px 0;">
            <img src="../<?= $post['piece_jointe'] ?>" style="max-width: 100%; border-radius: 10px;" alt="Image du post">
        </div>
    <?php endif; ?>

    <div class="actions">
        <a href="../controller/control.php?action=list" class="btn btn-back">
            <i class="fas fa-arrow-left"></i> Retour au forum
        </a>
        <a href="../controller/control.php?action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau Post
        </a>
        
        <?php if ($post['Id_utilisateur'] == $user_id || $is_admin): ?>
            <a href="../controller/control.php?action=edit&id=<?= $post['Id_post'] ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier le Post
            </a>
        <?php endif; ?>
    </div>

    <div class="comments-section">
        <h2 class="comments-header">
            <i class="far fa-comments"></i> 
            Commentaires (<?= isset($comments) ? count($comments) : 0 ?>)
        </h2>

        <?php if (!empty($comment_success)): ?>
            <div class="alert alert-success" id="successMessage">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($comment_success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($comment_errors)): ?>
            <div class="alert alert-error" id="errorMessage">
                <strong>Erreurs à corriger :</strong>
                <ul style="margin: 8px 0 0 20px;">
                    <?php foreach ($comment_errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="comment-form">
            <form action="../controller/control.php?action=add_comment" method="POST" id="commentForm">
                <input type="hidden" name="post_id" value="<?= $post['Id_post'] ?>">
                <textarea name="contenu" placeholder="Partagez votre avis, posez une question ou apportez votre contribution..."><?= htmlspecialchars($old_comment) ?></textarea>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Publier le commentaire
                </button>
            </form>
        </div>

        <div class="comments-list">
            <?php if (empty($comments)): ?>
                <div class="no-comments">
                    <i class="far fa-comments" style="font-size: 2rem; margin-bottom: 10px; opacity: 0.5;"></i>
                    <p>Aucun commentaire pour le moment. Soyez le premier à commenter !</p>
                </div>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <?php 
                    $is_your_comment = ($comment['Id_utilisateur'] == $user_id);
                    $is_editing = ($editing_comment_id == $comment['Id_commentaire']);
                    ?>
                    
                    <?php if ($is_editing): ?>
                        <div class="comment">
                            <form action="../controller/control.php?action=edit_comment" method="POST" class="edit-comment-form">
                                <input type="hidden" name="comment_id" value="<?= $comment['Id_commentaire'] ?>">
                                <input type="hidden" name="post_id" value="<?= $post['Id_post'] ?>">
                                <textarea name="contenu"><?= htmlspecialchars($comment['contenu']) ?></textarea>
                                <div class="edit-comment-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Enregistrer
                                    </button>
                                    <a href="../controller/control.php?action=view&id=<?= $post['Id_post'] ?>" class="btn btn-back">
                                        <i class="fas fa-times"></i> Annuler
                                    </a>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="comment">
                            <div class="comment-header">
                                <div class="comment-author">
                                    <div class="comment-avatar">
                                        <?= strtoupper(substr($comment['auteur'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="comment-name"><?= htmlspecialchars($comment['auteur']) ?></div>
                                        <div class="comment-date">
                                            <?= date('d/m/Y à H:i', strtotime($comment['date_creation'])) ?>
                                            <?php if ($is_your_comment): ?>
                                                <span style="color: var(--moss); margin-left: 8px;">(Votre commentaire)</span>
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
                                    <form action="../controller/control.php?action=view&id=<?= $post['Id_post'] ?>" method="POST" style="display: inline;">
                                        <input type="hidden" name="edit_comment_id" value="<?= $comment['Id_commentaire'] ?>">
                                        <button type="submit" class="comment-btn edit">
                                            <i class="fas fa-edit"></i> Modifier
                                        </button>
                                    </form>
                                    
                                    <a href="../controller/control.php?action=delete_comment&id=<?= $comment['Id_commentaire'] ?>&post_id=<?= $post['Id_post'] ?>" 
                                       class="comment-btn delete" 
                                       onclick="return confirm('Voulez-vous vraiment supprimer votre commentaire ?')">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Validation des commentaires SANS HTML5
document.getElementById('commentForm').addEventListener('submit', function(e) {
    const contenu = document.querySelector('textarea[name="contenu"]').value.trim();
    let errors = [];
    
    if (contenu.length < 2) {
        errors.push('Le commentaire doit contenir au moins 2 caractères');
        document.querySelector('textarea[name="contenu"]').classList.add('invalid');
    } else {
        document.querySelector('textarea[name="contenu"]').classList.remove('invalid');
    }
    
    if (contenu.length > 1000) {
        errors.push('Le commentaire ne peut pas dépasser 1000 caractères');
        document.querySelector('textarea[name="contenu"]').classList.add('invalid');
    }
    
    if (errors.length > 0) {
        e.preventDefault();
        
        const oldAlerts = document.querySelectorAll('.alert.alert-error');
        oldAlerts.forEach(alert => alert.remove());
        
        let alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-error';
        alertDiv.innerHTML = `<strong>Erreurs à corriger :</strong><ul style="margin: 8px 0 0 20px;">${errors.map(error => `<li>${error}</li>`).join('')}</ul>`;
        
        document.querySelector('.comments-section').insertBefore(alertDiv, document.querySelector('.comment-form'));
        
        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

// Masquer les messages APRÈS 10 SECONDES
setTimeout(function() {
    const messages = document.querySelectorAll('.alert');
    messages.forEach(function(message) {
        message.style.opacity = '0';
        message.style.transition = 'opacity 1s ease';
        setTimeout(() => {
            if (message.parentNode) {
                message.parentNode.removeChild(message);
            }
        }, 1000);
    });
}, 10000);

// Focus sur le textarea du commentaire quand on clique sur "Modifier"
document.addEventListener('DOMContentLoaded', function() {
    const editForms = document.querySelectorAll('.edit-comment-form textarea');
    if (editForms.length > 0) {
        editForms[0].focus();
    }
    
    document.querySelectorAll('textarea').forEach(element => {
        element.addEventListener('input', function() {
            this.classList.remove('invalid');
        });
    });
});
</script>

</body>
</html>