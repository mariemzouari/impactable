
<?php
if (!isset($post)) {
    header('Location: ../controller/control.php?action=list');
    exit;
}

$from_admin = isset($_GET['from']) && $_GET['from'] == 'admin';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Post - ImpactAble</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../View/assets/css/style.css">
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

            <form action="../controller/control.php?action=edit&id=<?= $post['Id_post'] ?><?= $from_admin ? '&from=admin' : '' ?>" method="POST" id="editForm">
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
                        <option value="Idées & Projets" <?= $post['categorie'] == 'Idées & Projets' ? 'selected' : '' ?>>Idées & Projets</option>
                        <option value="Questions" <?= $post['categorie'] == 'Questions' ? 'selected' : '' ?>>Questions</option>
                        <option value="Ressources" <?= $post['categorie'] == 'autre' ? 'selected' : '' ?>>Ressources</option>
                    </select>
                </div>

                <button type="submit" class="btn primary" style="width: 100%; margin-top: 20px;">
                    <i class="fas fa-save"></i> Modifier le Post
                </button>
            </form>

            <div class="form-footer" style="margin-top: 24px; text-align: center;">
                <a href="../controller/control.php?action=<?= $from_admin ? 'admin' : 'list' ?>" class="btn ghost" style="width: 100%;">
                    <i class="fas fa-arrow-left"></i> Retour <?= $from_admin ? "à l'admin" : 'au forum' ?>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Validation côté client SANS HTML5
document.getElementById('editForm').addEventListener('submit', function(e) {
    const titre = document.getElementById('titre').value.trim();
    const contenu = document.getElementById('contenu').value.trim();
    const categorie = document.getElementById('categorie').value;
    
    let errors = [];
    
    // Validation titre SANS HTML5
    if (!titre || titre.length < 5) {
        errors.push('Le titre doit contenir au moins 5 caractères');
        document.getElementById('titre').classList.add('invalid');
    } else {
        document.getElementById('titre').classList.remove('invalid');
    }
    
    // Validation contenu SANS HTML5
    if (!contenu || contenu.length < 10) {
        errors.push('Le contenu doit contenir au moins 10 caractères');
        document.getElementById('contenu').classList.add('invalid');
    } else {
        document.getElementById('contenu').classList.remove('invalid');
    }
    
    // Validation catégorie SANS HTML5
    if (!categorie) {
        errors.push('Veuillez sélectionner une catégorie');
        document.getElementById('categorie').classList.add('invalid');
    } else {
        document.getElementById('categorie').classList.remove('invalid');
    }
    
    if (errors.length > 0) {
        e.preventDefault();
        let alertDiv = document.querySelector('.alert');
        if (!alertDiv) {
            alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-error';
            document.querySelector('form').insertBefore(alertDiv, document.querySelector('form').firstChild);
        }
        alertDiv.innerHTML = `<strong>Erreurs à corriger :</strong><ul style="margin: 8px 0 0 20px;">${errors.map(error => `<li>${error}</li>`).join('')}</ul>`;
        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

// Retirer la classe invalid quand l'utilisateur commence à taper
document.querySelectorAll('input, textarea, select').forEach(element => {
    element.addEventListener('input', function() {
        this.classList.remove('invalid');
    });
});
</script>

</body>
</html>
