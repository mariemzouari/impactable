<?php
require_once __DIR__ . '/../../Model/PostModel.php';

$postModel = new PostModel();
$errors = [];
$old_data = [];

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0) {
    header('Location: index.php?action=login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'] ?? '';
    $contenu = $_POST['contenu'] ?? '';
    $categorie = trim($_POST['categorie'] ?? ''); 

    $old_data = $_POST;

    $errors = $postModel->validateStrict($titre, $contenu, $categorie);

    $bannedWords = ['spam', 'arnaque', 'hack', 'pirate'];
    foreach ($bannedWords as $word) {
        if (stripos($titre . ' ' . $contenu, $word) !== false) {
            $errors[] = "Votre contenu contient des termes inappropriés";
            break;
        }
    }

    if (preg_match('/(.)\1{4,}/', $titre . ' ' . $contenu)) {
        $errors[] = "Évitez la répétition excessive de caractères";
    }

    if (strlen($titre . ' ' . $contenu) > 10 && ($titre . ' ' . $contenu) === strtoupper($titre . ' ' . $contenu)) {
        $errors[] = "Évitez d'écrire uniquement en majuscules";
    }

    $piece_jointe = '';

    if (isset($_FILES['piece_jointe']) && $_FILES['piece_jointe']['error'] == 0) {
        $file_errors = $postModel->validateFile($_FILES['piece_jointe']);
        $errors = array_merge($errors, $file_errors);

        if (empty($file_errors)) {
            $target_dir = 'uploads/';
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $file_type = strtolower(pathinfo($_FILES['piece_jointe']['name'], PATHINFO_EXTENSION));
            $target_file = $target_dir . uniqid() . '.' . $file_type;

            if (move_uploaded_file($_FILES['piece_jointe']['tmp_name'], $target_file)) {
                $piece_jointe = $target_file;
            } else {
                $errors[] = 'Erreur lors de l\'upload de l\'image';
            }
        }
    }

    if (empty($errors)) {
        $created = $postModel->create($_SESSION['user_id'], $titre, $categorie, $contenu, $piece_jointe);
        if ($created) {
            header('Location: index.php?action=list');
            exit;
        } else {
            $errors[] = 'Erreur lors de la création du post';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Post - ImpactAble</title>
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
                    <i class="fas fa-plus"></i>
                </div>
            </div>
            
            <div class="form-header">
                <h2>Créer un Nouveau Post</h2>
                <p>Partagez vos idées avec la communauté</p>
            </div>

            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-error">
                    <strong>Erreurs à corriger :</strong>
                    <ul style="margin: 8px 0 0 20px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="index.php?action=create" method="POST" enctype="multipart/form-data" id="postForm">
                <div class="form-group">
                    <label for="titre">
                        <i class="fas fa-heading"></i> Titre du Post
                    </label>
                    <input type="text" name="titre" id="titre" class="input" placeholder="Entrez un titre accrocheur..." value="<?= isset($old_data['titre']) ? htmlspecialchars($old_data['titre']) : '' ?>" >
                </div>

                <div class="form-group">
                    <label for="contenu">
                        <i class="fas fa-align-left"></i> Contenu
                    </label>
                    <textarea name="contenu" id="contenu" class="textarea" placeholder="Partagez vos idées, questions ou informations ici..." ><?= isset($old_data['contenu']) ? htmlspecialchars($old_data['contenu']) : '' ?></textarea>
                </div>

                <div class="form-group">
                    <label for="categorie">
                        <i class="fas fa-tag"></i> Catégorie
                    </label>
                    <select name="categorie" id="categorie" class="select" >
                        <option value="">Sélectionnez une catégorie</option>
                        <option value="Opportunités" <?= (isset($old_data['categorie']) && $old_data['categorie'] == 'Opportunités') ? 'selected' : '' ?>>Opportunités</option>
                        <option value="Événements" <?= (isset($old_data['categorie']) && $old_data['categorie'] == 'Événements') ? 'selected' : '' ?>>Événements</option>
                        <option value="Idées" <?= (isset($old_data['categorie']) && $old_data['categorie'] == 'Idées') ? 'selected' : '' ?>>Idées</option>
                        <option value="Questions" <?= (isset($old_data['categorie']) && $old_data['categorie'] == 'Questions') ? 'selected' : '' ?>>Questions</option>
                        <option value="Ressources" <?= (isset($old_data['categorie']) && $old_data['categorie'] == 'Ressources') ? 'selected' : '' ?>>Ressources</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="piece_jointe">
                        <i class="fas fa-image"></i> Image (optionnelle)
                    </label>
                    <input type="file" name="piece_jointe" id="piece_jointe" class="input" accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp">
                    <div class="file-info" style="font-size: 0.8rem; color: var(--muted); margin-top: 5px;">
                        Formats acceptés : JPG, PNG, GIF, WebP - Max 5MB
                    </div>
                </div>

                <button type="submit" class="btn primary" style="width: 100%; margin-top: 20px;">
                    <i class="fas fa-paper-plane"></i> Publier le Post
                </button>
            </form>

            <div class="form-footer" style="margin-top: 24px; text-align: center;">
                <a href="index.php?action=list" class="btn ghost" style="width: 100%;">
                    <i class="fas fa-arrow-left"></i> Retour au forum
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('postForm');
    if (!form) return;
    
    const titreField = form.querySelector('#titre');
    const contenuField = form.querySelector('#contenu');
    const categorieField = form.querySelector('#categorie');
    
    function checkTitre() {
        const value = titreField.value.trim();
        if (value.length >= 5 && value.length <= 255) {
            titreField.classList.remove('invalid');
            titreField.classList.add('valid');
            clearFieldError(titreField);
            return true;
        } else {
            titreField.classList.remove('valid');
            titreField.classList.add('invalid');
            let msg = '';
            if (!value) msg = 'Titre obligatoire';
            else if (value.length < 5) msg = 'Min 5 caractères';
            else msg = 'Max 255 caractères';
            showFieldError(titreField, msg);
            return false;
        }
    }
    
    function checkContenu() {
        const value = contenuField.value.trim();
        if (value.length >= 10) {
            contenuField.classList.remove('invalid');
            contenuField.classList.add('valid');
            clearFieldError(contenuField);
            return true;
        } else {
            contenuField.classList.remove('valid');
            contenuField.classList.add('invalid');
            let msg = '';
            if (!value) msg = 'Contenu obligatoire';
            else msg = 'Min 10 caractères';
            showFieldError(contenuField, msg);
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
    
    titreField.addEventListener('input', checkTitre);
    contenuField.addEventListener('input', checkContenu);
    categorieField.addEventListener('change', checkCategorie);
    
    titreField.addEventListener('blur', checkTitre);
    contenuField.addEventListener('blur', checkContenu);
    categorieField.addEventListener('blur', checkCategorie);
    
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
