<?php
require_once __DIR__ . '/../../Model/PostModel.php';

$postModel = new PostModel();
$post = null;
$error_message = [];

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0) {
    header('Location: index.php?action=login');
    exit;
}

$id = $_GET['id'] ?? 0;
$post = $postModel->findById($id);
$from_admin = isset($_GET['from']) && $_GET['from'] == 'admin';

if (!$post || ($post['Id_utilisateur'] != $_SESSION['user_id'] && !($_SESSION['is_admin'] ?? false))) {
    header('Location: index.php?action=list');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'] ?? '';
    $contenu = $_POST['contenu'] ?? '';
    $categorie = $_POST['categorie'] ?? '';

    $error_message = $postModel->validateStrict($titre, $contenu, $categorie);

    $bannedWords = ['spam', 'arnaque', 'hack', 'pirate'];
    foreach ($bannedWords as $word) {
        if (stripos($titre . ' ' . $contenu, $word) !== false) {
            $error_message[] = "Votre contenu contient des termes inappropriés";
            break;
        }
    }

    if (empty($error_message)) {
            if ($postModel->update($id, $titre, $categorie, $contenu)) {
                if ($from_admin) {
                    header('Location: index.php?action=admin');
                } else {
                    header('Location: index.php?action=list');
                }
                exit;
            } else {
                $error_message[] = "Erreur lors de la modification du post";
            }
        }
    }

    ?>
    <!DOCTYPE html> 
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Modifier le Post - ImpactAble</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="View/assets/css/style.css">
    </head>
    <body>

    <div class="container">
        <div class="form-container">
            <div class="form-card">
                <div class="auth-decoration">
                    <div class="auth-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                </div>
            
                <div class="form-header">
                    <h2>Modifier le Post</h2>
                    <p>Modifiez votre publication</p>
                </div>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error">
                        <strong>Erreurs à corriger :</strong>
                        <ul style="margin: 8px 0 0 20px;">
                            <?php foreach ($error_message as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="index.php?action=edit&id=<?= $post['Id_post'] ?><?= $from_admin ? '&from=admin' : '' ?>" method="POST" id="editForm">
                    <div class="form-group">
                        <label for="titre">
                            <i class="fas fa-heading"></i> Titre du Post
                        </label>
                        <input type="text" name="titre" id="titre" class="input" placeholder="Entrez un titre accrocheur..." value="<?= htmlspecialchars($post['titre']) ?>" >
                    </div>

                    <div class="form-group">
                        <label for="contenu">
                            <i class="fas fa-align-left"></i> Contenu
                        </label>
                        <textarea name="contenu" id="contenu" class="textarea" placeholder="Partagez vos idées, questions ou informations ici..."><?= htmlspecialchars($post['contenu']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="categorie">
                            <i class="fas fa-tag"></i> Catégorie
                        </label>
                        <select name="categorie" id="categorie" class="select" >
                            <option value="">Sélectionnez une catégorie</option>
                            <option value="Opportunités" <?= $post['categorie'] == 'Opportunités' ? 'selected' : '' ?>>Opportunités</option>
                            <option value="Événements" <?= $post['categorie'] == 'Événements' ? 'selected' : '' ?>>Événements</option>
                            <option value="Idées" <?= $post['categorie'] == 'Idées' ? 'selected' : '' ?>>Idées</option>
                            <option value="Questions" <?= $post['categorie'] == 'Questions' ? 'selected' : '' ?>>Questions</option>
                            <option value="Ressources" <?= $post['categorie'] == 'Ressources' ? 'selected' : '' ?>>Ressources</option>
                        </select>
                    </div>

                    <button type="submit" class="btn primary" style="width: 100%; margin-top: 20px;">
                        <i class="fas fa-save"></i> Modifier le Post
                    </button>
                </form>

                <div class="form-footer" style="margin-top: 24px; text-align: center;">
                    <a href="index.php?action=<?= $from_admin ? 'admin' : 'list' ?>" class="btn ghost" style="width: 100%;">
                        <i class="fas fa-arrow-left"></i> Retour <?= $from_admin ? "à l'admin" : 'au forum' ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
    // VALIDATION SIMPLE ET ROBUSTE - AFFICHAGE ROUGE/VERT EN DIRECT
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editForm');
        if (!form) return;
    
        const titreField = form.querySelector('#titre');
        const contenuField = form.querySelector('#contenu');
        const categorieField = form.querySelector('#categorie');
    
        // Détection des mots/caractères interdits
        function hasRepeatedCharacters(text) {
            return /(.)\1{4,}/.test(text);
        }
    
        function isAllCaps(text) {
            return text.length > 10 && text === text.toUpperCase() && /[A-Z]/.test(text);
        }
    
        // Fonction simple de validation
        function checkTitre() {
            const value = titreField.value.trim();
            let errorMsg = '';
        
            if (!value) {
                errorMsg = 'Titre obligatoire';
            } else if (value.length < 5) {
                errorMsg = 'Min 5 caractères';
            } else if (value.length > 255) {
                errorMsg = 'Max 255 caractères';
            } else if (hasRepeatedCharacters(value)) {
                errorMsg = 'Évitez la répétition excessive';
            } else if (isAllCaps(value)) {
                errorMsg = 'Évitez les majuscules continues';
            }
        
            if (!errorMsg) {
                titreField.classList.remove('invalid');
                titreField.classList.add('valid');
                clearFieldError(titreField);
                return true;
            } else {
                titreField.classList.remove('valid');
                titreField.classList.add('invalid');
                showFieldError(titreField, errorMsg);
                return false;
            }
        }
    
        function checkContenu() {
            const value = contenuField.value.trim();
            let errorMsg = '';
        
            if (!value) {
                errorMsg = 'Contenu obligatoire';
            } else if (value.length < 10) {
                errorMsg = 'Min 10 caractères';
            } else if (hasRepeatedCharacters(value)) {
                errorMsg = 'Évitez la répétition excessive';
            } else if (isAllCaps(value)) {
                errorMsg = 'Évitez les majuscules continues';
            }
        
            if (!errorMsg) {
                contenuField.classList.remove('invalid');
                contenuField.classList.add('valid');
                clearFieldError(contenuField);
                return true;
            } else {
                contenuField.classList.remove('valid');
                contenuField.classList.add('invalid');
                showFieldError(contenuField, errorMsg);
                return false;
            }
        }
    
        function checkCategorie() {
            const value = categorieField.value;
        
            if (value) {
                categorieField.classList.remove('invalid');
                categorieField.classList.add('valid');
                clearFieldError(categorieField);
                return true;
            } else {
                categorieField.classList.remove('valid');
                categorieField.classList.add('invalid');
                showFieldError(categorieField, 'Sélectionner une catégorie');
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
    
        // Validation en temps réel
        titreField.addEventListener('input', checkTitre);
        contenuField.addEventListener('input', checkContenu);
        categorieField.addEventListener('change', checkCategorie);
    
        titreField.addEventListener('blur', checkTitre);
        contenuField.addEventListener('blur', checkContenu);
        categorieField.addEventListener('blur', checkCategorie);
    
        // Validation à la soumission
        form.addEventListener('submit', function(e) {
            const titreOk = checkTitre();
            const contenuOk = checkContenu();
            const categorieOk = checkCategorie();
        
            if (!titreOk || !contenuOk || !categorieOk) {
                e.preventDefault();
            
                let existingAlert = form.querySelector('.alert');
                if (existingAlert) existingAlert.remove();
            
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-error';
                alertDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i><div><strong>Veuillez corriger les erreurs</strong></div>';
                form.insertBefore(alertDiv, form.firstChild);
                alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
    </script>

    </body>
    </html>
