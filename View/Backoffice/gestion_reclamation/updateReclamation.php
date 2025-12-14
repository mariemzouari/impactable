<?php
require_once(__DIR__ . '/../../../controller/ReclamationController.php');
require_once(__DIR__ . '/../../../MODEL/Reclamation.php');

$erreur = '';
$reclamation = null;
$recC = new ReclamationController();

// Récupérer la réclamation à modifier
if (isset($_GET['id'])) {
    $reclamation = $recC->showReclamationById($_GET['id']);
}

// Si formulaire soumis
if (
    isset(
    $_POST['id'],
    $_POST['sujet'],
    $_POST['description'],
    $_POST['categorie'],
    $_POST['priorite'],
    $_POST['statut'],
    $_POST['utilisateurId']
)
) {
    if (
        !empty($_POST['id']) && !empty($_POST['sujet']) && !empty($_POST['description'])
        && !empty($_POST['categorie']) && !empty($_POST['priorite'])
        && !empty($_POST['statut']) && !empty($_POST['utilisateurId'])
    ) {
        // Vérifier que la réclamation existe
        if (!$reclamation) {
            $erreur = "Réclamation non trouvée.";
        } else {
            // Création d'un objet Reclamation avec les attributs de base
            try {
                $dateCreation = new DateTime($reclamation['dateCreation']);
            } catch (Exception $e) {
                $dateCreation = new DateTime();
            }

            $rec = new Reclamation(
                intval($_POST['id']),
                trim($_POST['sujet']),
                trim($_POST['description']),
                trim($_POST['categorie']),
                trim($_POST['priorite']),
                trim($_POST['statut']),
                $dateCreation,
                new DateTime(), // derniereModification mise à jour maintenant
                intval($_POST['utilisateurId']),
                isset($_POST['agentAttribue']) ? trim($_POST['agentAttribue']) : null
            );

            // Mise à jour dans la base de données
            try {
                $recC->updateReclamation($rec, $_POST['id']);
                header('Location: ../reclamation_back.php?success=1');
                exit();
            } catch (Exception $e) {
                $erreur = "Erreur lors de la mise à jour : " . $e->getMessage();
            }
        }
    } else {
        $erreur = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour Réclamation</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/admin-style.css">
</head>

<body class="without-sidebar">
    <div class="container">
        <div class="header">
            <h2><i class="fas fa-edit"></i> Mise à jour d'une Réclamation</h2>
            <a href="../reclamation_back.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <?php if (!empty($erreur)): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i> <?= $erreur ?>
            </div>
        <?php endif; ?>

        <?php if ($reclamation): ?>
            <form action="" method="POST" id="updateReclamationForm" onsubmit="return validateUpdateReclamationForm(event)">
                <input type="hidden" name="id" value="<?= $reclamation['id'] ?>">

                <div class="form-group">
                    <label><i class="fas fa-heading"></i> Sujet</label>
                    <input type="text" name="sujet" id="sujet" value="<?= htmlspecialchars($reclamation['sujet'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Description</label>
                    <textarea name="description"
                        id="description"><?= htmlspecialchars($reclamation['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-tags"></i> Catégorie</label>
                    <select name="categorie" id="categorie">
                        <?php
                        $cats = ["Technique", "Facturation", "Service", "Produit"];
                        foreach ($cats as $c) {
                            $sel = ($reclamation['categorie'] ?? '') === $c ? 'selected' : '';
                            echo "<option value='$c' $sel>$c</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-exclamation-triangle"></i> Priorité</label>
                    <select name="priorite" id="priorite">
                        <?php
                        $prio = ["Faible", "Moyenne", "Urgente"];
                        foreach ($prio as $p) {
                            $sel = ($reclamation['priorite'] ?? '') === $p ? 'selected' : '';
                            echo "<option value='$p' $sel>$p</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-info-circle"></i> Statut</label>
                    <select name="statut" id="statut">
                        <?php
                        $stats = ["En attente", "En cours", "Résolue", "Fermée"];
                        foreach ($stats as $st) {
                            $sel = ($reclamation['statut'] ?? '') === $st ? 'selected' : '';
                            echo "<option value='$st' $sel>$st</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-user"></i> ID Utilisateur</label>
                    <input type="text" name="utilisateurId" id="utilisateurId"
                        value="<?= $reclamation['utilisateurId'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-user-tie"></i> Agent attribué (optionnel)</label>
                    <input type="text" name="agentAttribue"
                        value="<?= htmlspecialchars($reclamation['agentAttribue'] ?? '') ?>">
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                    <a href="../reclamation_back.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        <?php else: ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i> Réclamation non trouvée
            </div>
            <a href="../reclamation_back.php" class="btn btn-secondary">Retour</a>
        <?php endif; ?>
    </div>

    <script>
        function validateUpdateReclamationForm(event) {
            const errors = [];

            // Validation sujet
            const sujet = document.getElementById('sujet');
            if (!sujet || !sujet.value.trim()) {
                errors.push('Le sujet est requis');
                if (sujet) sujet.style.borderColor = '#C62828';
            } else if (sujet) {
                sujet.style.borderColor = '#A9B97D';
            }

            // Validation description
            const description = document.getElementById('description');
            if (!description || !description.value.trim()) {
                errors.push('La description est requise');
                if (description) description.style.borderColor = '#C62828';
            } else if (description) {
                description.style.borderColor = '#A9B97D';
            }

            // Validation catégorie
            const categorie = document.getElementById('categorie');
            if (!categorie || !categorie.value) {
                errors.push('Veuillez sélectionner une catégorie');
                if (categorie) categorie.style.borderColor = '#C62828';
            } else if (categorie) {
                categorie.style.borderColor = '#A9B97D';
            }

            // Validation priorité
            const priorite = document.getElementById('priorite');
            if (!priorite || !priorite.value) {
                errors.push('Veuillez sélectionner une priorité');
                if (priorite) priorite.style.borderColor = '#C62828';
            } else if (priorite) {
                priorite.style.borderColor = '#A9B97D';
            }

            // Validation statut
            const statut = document.getElementById('statut');
            if (!statut || !statut.value) {
                errors.push('Veuillez sélectionner un statut');
                if (statut) statut.style.borderColor = '#C62828';
            } else if (statut) {
                statut.style.borderColor = '#A9B97D';
            }

            // Validation ID utilisateur
            const utilisateurId = document.getElementById('utilisateurId');
            const utilisateurIdNum = utilisateurId ? parseInt(utilisateurId.value) : 0;
            if (!utilisateurId || !utilisateurId.value.trim() || isNaN(utilisateurIdNum) || utilisateurIdNum < 1) {
                errors.push('L\'ID utilisateur doit être un nombre supérieur à 0');
                if (utilisateurId) utilisateurId.style.borderColor = '#C62828';
            } else if (utilisateurId) {
                utilisateurId.style.borderColor = '#A9B97D';
            }

            if (errors.length > 0) {
                event.preventDefault();
                alert('Veuillez corriger les erreurs suivantes :\n\n' + errors.join('\n'));
                return false;
            }

            return true;
        }
    </script>
</body>

</html>