<?php
require_once(__DIR__ . '/../../../controller/ReponseController.php');
require_once(__DIR__ . '/../../../controller/ReclamationController.php');
require_once(__DIR__ . '/../../../MODEL/reponce.php');

$reponseId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$reclamationId = isset($_GET['reclamation_id']) ? intval($_GET['reclamation_id']) : 0;

if ($reponseId <= 0 || $reclamationId <= 0) {
    header('Location: liste_reponses.php?reclamation_id=' . $reclamationId);
    exit();
}

$reponseController = new ReponseController();
$reponse = $reponseController->getReponseById($reponseId);

if (!$reponse) {
    header('Location: liste_reponses.php?reclamation_id=' . $reclamationId);
    exit();
}

$reclamationController = new ReclamationController();
$reclamation = $reclamationController->showReclamationById($reclamationId);

if (!$reclamation) {
    header('Location: ../../reclamation_back.php');
    exit();
}

$error = '';
$success = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // Validation
    if (empty($message) || strlen($message) < 10) {
        $error = 'Le message doit contenir au moins 10 caractères';
    } elseif (strlen($message) > 1000) {
        $error = 'Le message ne peut pas dépasser 1000 caractères';
    } else {
        try {
            // Créer l'objet Reponse avec les données existantes
            $reponseObj = new Reponse(
                $reponseId,
                $reclamationId,
                $reponse['Id_utilisateur'],
                $message,
                new DateTime($reponse['date_reponse']), // dateReponse originale
                new DateTime()  // dernierUpdate mis à jour
            );

            // Mettre à jour la réponse
            $result = $reponseController->updateReponse($reponseObj, $reponseId);

            if ($result) {
                // Rediriger vers la liste avec un message de succès
                header('Location: liste_reponses.php?reclamation_id=' . $reclamationId . '&updated=1');
                exit();
            }
        } catch (Exception $e) {
            $error = 'Erreur lors de la modification de la réponse: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une Réponse - Réclamation #<?= $reclamationId ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/admin-style.css">
</head>

<body class="without-sidebar">
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-edit"></i> Modifier une Réponse</h1>
            <a href="liste_reponses.php?reclamation_id=<?= $reclamationId ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                    <p style="margin-top: 10px; font-weight: normal;">Redirection en cours...</p>
                </div>
            <?php else: ?>
                <div class="reclamation-info">
                    <h3><i class="fas fa-file-alt"></i> Réclamation #<?= htmlspecialchars($reclamationId) ?></h3>
                    <p><strong>Sujet:</strong> <?= htmlspecialchars($reclamation['sujet']) ?></p>
                    <p><strong>Catégorie:</strong> <?= htmlspecialchars($reclamation['categorie']) ?></p>
                </div>

                <form method="POST" action="" id="modifierReponseForm" onsubmit="return validateModifierReponseForm(event)">
                    <div class="form-group">
                        <label for="message">Message de la réponse <span class="required">*</span></label>
                        <textarea id="message" name="message"
                            placeholder="Écrivez votre réponse ici..."><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : htmlspecialchars($reponse['message']) ?></textarea>
                        <div class="char-counter"><span id="charCount">0</span> / 1000 caractères</div>
                    </div>

                    <div class="form-actions">
                        <a href="liste_reponses.php?reclamation_id=<?= $reclamationId ?>" class="btn-cancel">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Fonction de validation
        function validateModifierReponseForm(event) {
            const errors = [];

            // Validation message
            const message = document.getElementById('message');
            if (!message || !message.value.trim() || message.value.trim().length < 10) {
                errors.push('Le message doit contenir au moins 10 caractères');
                if (message) message.style.borderColor = '#C62828';
            } else if (message.value.trim().length > 1000) {
                errors.push('Le message ne peut pas dépasser 1000 caractères');
                if (message) message.style.borderColor = '#C62828';
            } else if (message) {
                message.style.borderColor = 'var(--sage)';
            }

            if (errors.length > 0) {
                event.preventDefault();
                alert('Veuillez corriger les erreurs suivantes :\n\n' + errors.join('\n'));
                return false;
            }

            return true;
        }

        // Compteur de caractères
        const messageTextarea = document.getElementById('message');
        const charCount = document.getElementById('charCount');

        if (messageTextarea && charCount) {
            messageTextarea.addEventListener('input', function () {
                const length = this.value.length;
                charCount.textContent = length;

                if (length > 1000) {
                    charCount.style.color = '#C62828';
                } else if (length > 800) {
                    charCount.style.color = '#FF9800';
                } else {
                    charCount.style.color = 'var(--moss)';
                }
            });

            // Initialiser le compteur
            charCount.textContent = messageTextarea.value.length;
        }
    </script>
</body>

</html>