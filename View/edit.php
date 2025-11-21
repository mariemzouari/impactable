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
    <script src="../assets/js/validation.js"></script>
    <style>
        :root { --brown: #4b2e16; --copper: #b47b47; --moss: #5e6d3b; --sage: #a9b97d; --sand: #f4ecdd; --white: #fffaf5; --error-red: #e74c3c; --error-bg: #fdeded; }
        body { font-family: Inter, sans-serif; background: var(--sand); margin: 0; padding: 20px; }
        .container { max-width: 850px; margin: 40px auto; background: var(--white); padding: 40px; border-radius: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h1 { color: var(--brown); margin-bottom: 20px; border-bottom: 2px solid var(--sage); padding-bottom: 10px; }
        label { font-weight: 600; margin-top: 20px; display: block; color: var(--brown); }
        input, select, textarea { width: 100%; padding: 14px; border: 2px solid var(--sage); border-radius: 10px; background: var(--sand); font-size: 15px; margin-top: 6px; transition: border-color 0.3s ease; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: var(--moss); }
        textarea { height: 180px; resize: vertical; }
        button { margin-top: 25px; background: var(--moss); color: white; padding: 14px 24px; border: none; border-radius: 10px; font-size: 17px; cursor: pointer; transition: all 0.3s ease; font-weight: 600; }
        button:hover { background: #4d5a2a; transform: translateY(-2px); }
        .back { text-decoration: none; color: var(--moss); font-weight: 600; display: inline-flex; align-items: center; gap: 8px; margin-top: 20px; padding: 10px 20px; border: 2px solid var(--moss); border-radius: 10px; transition: all 0.3s ease; }
        .back:hover { background: var(--moss); color: white; }
        .form-header { display: flex; justify-content: between; align-items: center; margin-bottom: 30px; }
        .error-container { background: var(--error-bg); border: 1px solid var(--error-red); border-radius: 10px; padding: 15px 20px; margin-bottom: 25px; color: var(--error-red); }
        .error-list { margin: 10px 0 0 0; padding-left: 20px; }
        .error-list li { margin-bottom: 5px; }
        .field-error { border-color: var(--error-red) !important; background-color: #ffebee !important; }
        .error-icon { margin-right: 8px; }
        @media (max-width: 768px) { .container { margin: 20px; padding: 20px; } .form-header { flex-direction: column; gap: 15px; align-items: flex-start; } }
    </style>
</head>
<body>

<div class="container">
    <div class="form-header">
        <h1>Modifier le Post</h1>
    </div>

    <?php if (!empty($error_message)): ?>
        <div class="error-container">
            <strong>
                <i class="fas fa-exclamation-triangle error-icon"></i>
                Veuillez corriger les erreurs suivantes :
            </strong>
            <ul class="error-list">
                <?php foreach ($error_message as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="../controller/control.php?action=edit&id=<?= $post['Id_post'] ?><?= $from_admin ? '&from=admin' : '' ?>" method="POST" id="editForm">
        <label for="titre">Titre du Post</label>
        <input type="text" name="titre" value="<?= htmlspecialchars($post['titre']) ?>">

        <label for="contenu">Contenu</label>
        <textarea name="contenu"><?= htmlspecialchars($post['contenu']) ?></textarea>

        <label for="categorie">Catégorie</label>
        <select name="categorie">
            <option value="">Sélectionnez une catégorie</option>
            <option value="opportunites" <?= $post['categorie'] == 'opportunites' ? 'selected' : '' ?>>Opportunités</option>
            <option value="evenements" <?= $post['categorie'] == 'evenements' ? 'selected' : '' ?>>Événements</option>
            <option value="campagnes" <?= $post['categorie'] == 'campagnes' ? 'selected' : '' ?>>Campagnes</option>
            <option value="questions" <?= $post['categorie'] == 'questions' ? 'selected' : '' ?>>Questions</option>
            <option value="ressources" <?= $post['categorie'] == 'ressources' ? 'selected' : '' ?>>Ressources</option>
            <option value="autre" <?= $post['categorie'] == 'autre' ? 'selected' : '' ?>>Autre</option>
        </select>

        <button type="submit">
            <i class="fas fa-save"></i> Modifier le Post
        </button>
    </form>

    <a href="../controller/control.php?action=<?= $from_admin ? 'admin' : 'list' ?>" class="back">
        <i class="fas fa-arrow-left"></i> Retour <?= $from_admin ? "à l'admin" : 'au forum' ?>
    </a>
</div>

<script>
// Validation côté client SANS HTML5
document.getElementById('editForm').addEventListener('submit', function(e) {
    const titre = document.querySelector('input[name="titre"]').value;
    const contenu = document.querySelector('textarea[name="contenu"]').value;
    const categorie = document.querySelector('select[name="categorie"]').value;
    
    let errors = [];
    
    // Validation titre SANS HTML5
    if (!titre || titre.length < 5) {
        errors.push('Le titre doit contenir au moins 5 caractères');
        document.querySelector('input[name="titre"]').classList.add('field-error');
    } else {
        document.querySelector('input[name="titre"]').classList.remove('field-error');
    }
    
    // Validation contenu SANS HTML5
    if (!contenu || contenu.length < 10) {
        errors.push('Le contenu doit contenir au moins 10 caractères');
        document.querySelector('textarea[name="contenu"]').classList.add('field-error');
    } else {
        document.querySelector('textarea[name="contenu"]').classList.remove('field-error');
    }
    
    // Validation catégorie SANS HTML5
    if (!categorie) {
        errors.push('Veuillez sélectionner une catégorie');
        document.querySelector('select[name="categorie"]').classList.add('field-error');
    } else {
        document.querySelector('select[name="categorie"]').classList.remove('field-error');
    }
    
    if (errors.length > 0) {
        e.preventDefault();
        let errorContainer = document.querySelector('.error-container');
        if (!errorContainer) {
            errorContainer = document.createElement('div');
            errorContainer.className = 'error-container';
            errorContainer.innerHTML = `
                <strong>
                    <i class="fas fa-exclamation-triangle error-icon"></i>
                    Veuillez corriger les erreurs suivantes :
                </strong>
                <ul class="error-list"></ul>
            `;
            document.querySelector('form').insertBefore(errorContainer, document.querySelector('form').firstChild);
        }
        
        const errorList = errorContainer.querySelector('.error-list');
        errorList.innerHTML = errors.map(error => `<li>${error}</li>`).join('');
        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

// Retirer la classe field-error quand l'utilisateur commence à taper
document.querySelectorAll('input, textarea, select').forEach(element => {
    element.addEventListener('input', function() {
        this.classList.remove('field-error');
    });
});
</script>

</body>
</html>