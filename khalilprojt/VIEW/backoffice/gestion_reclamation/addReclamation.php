<?php
require_once(__DIR__ . '/../../../controller/ReclamationController.php');
require_once(__DIR__ . '/../../../MODEL/Reclamation.php');

$error = "";

// Si le formulaire est soumis
if (isset($_POST["sujet"]) &&
    isset($_POST["description"]) &&
    isset($_POST["categorie"]) &&
    isset($_POST["priorite"]) &&
    isset($_POST["statut"]) &&
    isset($_POST["utilisateurId"])
) {
    if (
        !empty($_POST["sujet"]) &&
        !empty($_POST["description"]) &&
        !empty($_POST["categorie"]) &&
        !empty($_POST["priorite"]) &&
        !empty($_POST["statut"]) &&
        !empty($_POST["utilisateurId"])
    ) {

        $rec = new Reclamation(
            null,                                   // id
            $_POST['sujet'],                        // sujet
            $_POST['description'],                  // description
            $_POST['categorie'],                    // categorie
            $_POST['priorite'],                     // priorite
            $_POST['statut'],                       // statut
            new DateTime(),                         // dateCreation
            new DateTime(),                         // derniereModification
            intval($_POST['utilisateurId']),        // utilisateurId
            $_POST['agentAttribue'] ?? null         // agentAttribue
        );

        $reclamationController = new ReclamationController();
        $reclamationController->addReclamation($rec);

        header('Location: ../admin_dashboard.php');
        exit();

    } else {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Réclamation</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/admin-style.css">
</head>
<body class="without-sidebar">
    <div class="container">
        <div class="header">
            <h2><i class="fas fa-plus-circle"></i> Ajouter une Réclamation</h2>
            <a href="../admin_dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label><i class="fas fa-heading"></i> Sujet</label>
                <input type="text" name="sujet" id="sujet" placeholder="Entrez le sujet de la réclamation">
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Description</label>
                <textarea name="description" id="description" placeholder="Décrivez la réclamation en détail..."></textarea>
            </div>

            <div class="form-group">
                <label><i class="fas fa-tags"></i> Catégorie</label>
                <select name="categorie" id="categorie">
                    <option value="">-- Choisir une catégorie --</option>
                    <option value="Technique">Technique</option>
                    <option value="Facturation">Facturation</option>
                    <option value="Service">Service</option>
                    <option value="Produit">Produit</option>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fas fa-exclamation-triangle"></i> Priorité</label>
                <select name="priorite" id="priorite">
                    <option value="Faible">Faible</option>
                    <option value="Moyenne">Moyenne</option>
                    <option value="Urgente">Urgente</option>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fas fa-info-circle"></i> Statut</label>
                <select name="statut" id="statut">
                    <option value="En attente">En attente</option>
                    <option value="En cours">En cours</option>
                    <option value="Résolue">Résolue</option>
                    <option value="Fermée">Fermée</option>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fas fa-user"></i> ID Utilisateur</label>
                <input type="text" name="utilisateurId" id="utilisateurId" placeholder="Entrez l'ID de l'utilisateur">
            </div>

            <div class="form-group">
                <label><i class="fas fa-user-tie"></i> Agent Attribué (optionnel)</label>
                <input type="text" name="agentAttribue" placeholder="Nom de l'agent">
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Ajouter la Réclamation
                </button>
                <a href="../admin_dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>

    <script>
        function validateAddReclamationForm(event) {
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
