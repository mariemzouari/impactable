<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Post - ImpactAble</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="assets/js/validation.js"></script>
    <style>
        :root { --brown: #4b2e16; --copper: #b47b47; --moss: #5e6d3b; --sage: #a9b97d; --sand: #f4ecdd; --white: #fffaf5; }
        body { font-family: Inter, sans-serif; background: var(--sand); margin: 0; padding: 15px; font-size: 14px; }
        .container { max-width: 800px; margin: 30px auto; background: var(--white); padding: 30px; border-radius: 14px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h1 { color: var(--brown); margin-bottom: 18px; border-bottom: 2px solid var(--sage); padding-bottom: 8px; font-size: 1.6rem; }
        label { font-weight: 600; margin-top: 18px; display: block; color: var(--brown); font-size: 0.95rem; }
        input, select, textarea { width: 100%; padding: 12px; border: 2px solid var(--sage); border-radius: 8px; background: var(--sand); font-size: 14px; margin-top: 5px; transition: border-color 0.3s ease; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: var(--moss); }
        textarea { height: 160px; resize: vertical; }
        button { background: var(--moss); color: white; padding: 12px 22px; border: none; border-radius: 8px; font-size: 15px; cursor: pointer; transition: all 0.3s ease; font-weight: 600; }
        button:hover { background: #4d5a2a; transform: translateY(-2px); }
        .back { text-decoration: none; color: var(--moss); font-weight: 600; display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; border: 2px solid var(--moss); border-radius: 8px; transition: all 0.3s ease; margin-left: 12px; font-size: 14px; }
        .back:hover { background: var(--moss); color: white; }
        .form-actions { display: flex; gap: 12px; align-items: center; margin-top: 22px; flex-wrap: wrap; }
        .file-info { font-size: 0.85rem; color: var(--copper); margin-top: 4px; font-style: italic; }
        .invalid { border-color: #f44336 !important; background-color: #ffebee !important; }
        .valid { border-color: #4CAF50 !important; background-color: #e8f5e8 !important; }
        .alert { padding: 12px 16px; margin: 15px 0; border-radius: 8px; font-weight: 500; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        @media (max-width: 768px) { .container { margin: 18px; padding: 18px; } .form-actions { flex-direction: column; } .back { margin-left: 0; margin-top: 8px; justify-content: center; } }
    </style>
</head>
<body>

<div class="container">
    <h1>Créer un Nouveau Post</h1>

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

    <form action="../controller/control.php?action=create" method="POST" enctype="multipart/form-data" id="postForm">
        
        <label for="titre">Titre du Post</label>
        <input type="text" name="titre" placeholder="Entrez un titre accrocheur..." value="<?= isset($old_data['titre']) ? htmlspecialchars($old_data['titre']) : '' ?>">

        <label for="contenu">Contenu</label>
        <textarea name="contenu" placeholder="Partagez vos idées, questions ou informations ici..."><?= isset($old_data['contenu']) ? htmlspecialchars($old_data['contenu']) : '' ?></textarea>

        <label for="categorie">Catégorie</label>
        <select name="categorie">
            <option value="">Sélectionnez une catégorie</option>
            <option value="opportunites" <?= (isset($old_data['categorie']) && $old_data['categorie'] == 'opportunites') ? 'selected' : '' ?>>Opportunités</option>
            <option value="evenements" <?= (isset($old_data['categorie']) && $old_data['categorie'] == 'evenements') ? 'selected' : '' ?>>Événements</option>
            <option value="campagnes" <?= (isset($old_data['categorie']) && $old_data['categorie'] == 'campagnes') ? 'selected' : '' ?>>Campagnes</option>
            <option value="questions" <?= (isset($old_data['categorie']) && $old_data['categorie'] == 'questions') ? 'selected' : '' ?>>Questions</option>
            <option value="ressources" <?= (isset($old_data['categorie']) && $old_data['categorie'] == 'ressources') ? 'selected' : '' ?>>Ressources</option>
            <option value="autre" <?= (isset($old_data['categorie']) && $old_data['categorie'] == 'autre') ? 'selected' : '' ?>>Autre</option>
        </select>

        <label for="piece_jointe">Image (optionnelle)</label>
        <input type="file" name="piece_jointe" accept="image/*">
        <div class="file-info">Formats acceptés : JPG, PNG, GIF, WebP - Max 5MB</div>

        <div class="form-actions">
            <button type="submit" id="submitBtn">
                <i class="fas fa-paper-plane"></i> Publier le Post
            </button>
            
            <a href="../controller/control.php?action=list" class="back">
                <i class="fas fa-arrow-left"></i> Retour au forum
            </a>
        </div>
    </form>
</div>

<script>
// Validation côté client SANS HTML5
document.getElementById('postForm').addEventListener('submit', function(e) {
    const titre = document.querySelector('input[name="titre"]').value.trim();
    const contenu = document.querySelector('textarea[name="contenu"]').value.trim();
    const categorie = document.querySelector('select[name="categorie"]').value;
    
    let errors = [];
    
    // Validation titre SANS HTML5
    if (titre.length < 5) {
        errors.push('Le titre doit contenir au moins 5 caractères');
        document.querySelector('input[name="titre"]').classList.add('invalid');
    } else {
        document.querySelector('input[name="titre"]').classList.remove('invalid');
    }
    
    // Validation contenu SANS HTML5
    if (contenu.length < 10) {
        errors.push('Le contenu doit contenir au moins 10 caractères');
        document.querySelector('textarea[name="contenu"]').classList.add('invalid');
    } else {
        document.querySelector('textarea[name="contenu"]').classList.remove('invalid');
    }
    
    // Validation catégorie SANS HTML5
    if (!categorie) {
        errors.push('Veuillez sélectionner une catégorie');
        document.querySelector('select[name="categorie"]').classList.add('invalid');
    } else {
        document.querySelector('select[name="categorie"]').classList.remove('invalid');
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