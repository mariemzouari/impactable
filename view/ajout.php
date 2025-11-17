<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Post - ImpactAble</title>

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
            padding: 15px;
            font-size: 14px;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            background: var(--white);
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        h1 {
            color: var(--brown);
            margin-bottom: 18px;
            border-bottom: 2px solid var(--sage);
            padding-bottom: 8px;
            font-size: 1.6rem;
        }

        label {
            font-weight: 600;
            margin-top: 18px;
            display: block;
            color: var(--brown);
            font-size: 0.95rem;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid var(--sage);
            border-radius: 8px;
            background: var(--sand);
            font-size: 14px;
            margin-top: 5px;
            transition: border-color 0.3s ease;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--moss);
        }

        textarea {
            height: 160px;
            resize: vertical;
        }

        button {
            background: var(--moss);
            color: white;
            padding: 12px 22px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
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
            gap: 6px;
            padding: 8px 18px;
            border: 2px solid var(--moss);
            border-radius: 8px;
            transition: all 0.3s ease;
            margin-left: 12px;
            font-size: 14px;
        }

        .back:hover {
            background: var(--moss);
            color: white;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-top: 22px;
            flex-wrap: wrap;
        }

        .file-info {
            font-size: 0.85rem;
            color: var(--copper);
            margin-top: 4px;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .container {
                margin: 18px;
                padding: 18px;
            }
            
            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .back {
                margin-left: 0;
                margin-top: 8px;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

<div class="container">
    <h1>Créer un Nouveau Post</h1>

    <!-- CORRECTION : Chemin absolu pour l'action -->
    <form action="/projt/control/control.php?action=create" method="POST" enctype="multipart/form-data">
        
        <label for="titre">Titre du Post</label>
        <input type="text" name="titre" placeholder="Entrez un titre accrocheur..." required>

        <label for="contenu">Contenu</label>
        <textarea name="contenu" placeholder="Partagez vos idées ici..." required></textarea>

        <label for="categorie">Catégorie</label>
        <select name="categorie" required>
            <option value="">Sélectionnez une catégorie</option>
            <option value="tempoignages">tempoignages</option>
            <option value="aides-subventions">aides-subventions</option>
            <option value="formations">formations</option>
            <option value="stages-alternance">stages-alternance</option>
            <option value="entreprises-inclusives">entreprises-inclusives</option>
            <option value="teletravail">teletravail</option>
        </select>

        <label for="piece_jointe">Image (optionnelle)</label>
        <input type="file" name="piece_jointe" accept="image/*">
        <div class="file-info">Formats acceptés : JPG, PNG, GIF, WebP - Max 5MB</div>

        <div class="form-actions">
            <button type="submit">
                <i class="fas fa-paper-plane"></i> Publier le Post
            </button>
            
            <a href="/projt/control/control.php?action=list" class="back">
                <i class="fas fa-arrow-left"></i> Retour au forum
            </a>
        </div>
    </form>
</div>

</body>
</html>