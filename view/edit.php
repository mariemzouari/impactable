<?php
// PROJT/view/edit.php
if (!isset($post)) {
    header('Location: ../control/control.php?action=list');
    exit;
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

    <style>
        :root {
            --brown: #4b2e16;
            --copper: #b47b47;
            --moss: #5e6d3b;
            --sage: #a9b97d;
            --sand: #f4ecdd;
            --white: #fffaf5;
        }

        body {
            font-family: Inter, sans-serif;
            background: var(--sand);
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 850px;
            margin: 40px auto;
            background: var(--white);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        h1 {
            color: var(--brown);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--sage);
            padding-bottom: 10px;
        }

        label {
            font-weight: 600;
            margin-top: 20px;
            display: block;
            color: var(--brown);
        }

        input, select, textarea {
            width: 100%;
            padding: 14px;
            border: 2px solid var(--sage);
            border-radius: 10px;
            background: var(--sand);
            font-size: 15px;
            margin-top: 6px;
            transition: border-color 0.3s ease;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--moss);
        }

        textarea {
            height: 180px;
            resize: vertical;
        }

        button {
            margin-top: 25px;
            background: var(--moss);
            color: white;
            padding: 14px 24px;
            border: none;
            border-radius: 10px;
            font-size: 17px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        button:hover {
            background: #4d5a2a;
            transform: translateY(-2px);
        }

        .back {
            text-decoration: none;
            color: var(--moss);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            padding: 10px 20px;
            border: 2px solid var(--moss);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .back:hover {
            background: var(--moss);
            color: white;
        }

        .form-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 30px;
        }

        .admin-link {
            background: var(--copper);
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .admin-link:hover {
            background: #a56a3a;
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
            
            .form-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

<div class="container">
    <div class="form-header">
        <h1>Modifier le Post</h1>
    </div>

    <form action="control.php?action=edit&id=<?= $post['Id_post'] ?>" method="POST">

        <label for="titre">Titre du Post</label>
        <input type="text" name="titre" value="<?= htmlspecialchars($post['titre']) ?>" required>

        <label for="contenu">Contenu</label>
        <textarea name="contenu" required><?= htmlspecialchars($post['contenu']) ?></textarea>

        <label for="categorie">Catégorie</label>
        <select name="categorie" required>
            <option value="tempoignages" <?= $post['categorie'] == 'opportunites' ? 'selected' : '' ?>>tempoignages</option>
            <option value="aides-subventions" <?= $post['categorie'] == 'evenements' ? 'selected' : '' ?>>aides-subventions</option>
            <option value="formations" <?= $post['categorie'] == 'campagnes' ? 'selected' : '' ?>>formations</option>
            <option value="stages-alternance" <?= $post['categorie'] == 'questions' ? 'selected' : '' ?>>stages-alternance</option>
            <option value="entreprises-inclusives" <?= $post['categorie'] == 'ressources' ? 'selected' : '' ?>>entreprises-inclusives</option>
            <option value="teletravail" <?= $post['categorie'] == 'autre' ? 'selected' : '' ?>>teletravail</option>
        </select>

        <button type="submit">
            <i class="fas fa-save"></i> Modifier le Post
        </button>
    </form>

    <a href="control.php?action=list" class="back">
        <i class="fas fa-arrow-left"></i> Retour au forum
    </a>
</div>

</body>
</html>