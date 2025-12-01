<?php
require_once(__DIR__ . '/../../../controller/ReponseController.php');
require_once(__DIR__ . '/../../../controller/ReclamationController.php');
require_once(__DIR__ . '/../../../MODEL/reponce.php');

$reclamationId = isset($_GET['reclamation_id']) ? intval($_GET['reclamation_id']) : 0;

if ($reclamationId <= 0) {
    header('Location: ../../admin_dashboard.php');
    exit();
}

$reclamationController = new ReclamationController();
$reclamation = $reclamationController->showReclamationById($reclamationId);

if (!$reclamation) {
    header('Location: ../../admin_dashboard.php');
    exit();
}

$error = '';
$success = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $userId = isset($_POST['userId']) ? intval($_POST['userId']) : 0;
    
    // Validation
    if (empty($message) || strlen($message) < 10) {
        $error = 'Le message doit contenir au moins 10 caractères';
    } elseif ($userId <= 0) {
        $error = 'ID utilisateur invalide';
    } else {
        try {
            // Créer l'objet Reponse
            $reponse = new Reponse(
                null, // id
                $reclamationId,
                $userId,
                $message,
                new DateTime(), // dateReponse
                new DateTime()  // dernierUpdate
            );
            
            // Ajouter la réponse
            $reponseController = new ReponseController();
            $result = $reponseController->addReponse($reponse);
            
            if ($result) {
                $success = 'Réponse ajoutée avec succès !';
                // Rediriger après 2 secondes
                header('refresh:2;url=liste_reponses.php?reclamation_id=' . $reclamationId);
            }
        } catch (Exception $e) {
            $error = 'Erreur lors de l\'ajout de la réponse: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Réponse - Réclamation #<?= $reclamationId ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/admin-style.css">
</head>
<body class="without-sidebar">
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-reply"></i> Ajouter une Réponse</h1>
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

                <form method="POST" action="" id="reponseForm" onsubmit="return validateAjouterReponseForm(event)">
                    <input type="hidden" name="reclamationId" value="<?= $reclamationId ?>">
                    
                    <div class="form-group">
                        <label for="message">Message de la réponse <span class="required">*</span></label>
                        <textarea id="message" name="message" placeholder="Écrivez votre réponse ici..."><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '' ?></textarea>
                        <div class="char-counter"><span id="charCount">0</span> / 1000 caractères</div>
                    </div>

                    <div class="form-group">
                        <label for="userId">ID Utilisateur <span class="required">*</span></label>
                        <input type="text" id="userId" name="userId" value="<?= isset($_POST['userId']) ? htmlspecialchars($_POST['userId']) : '1' ?>" placeholder="Entrez l'ID de l'utilisateur">
                    </div>

                    <div class="form-actions">
                        <a href="liste_reponses.php?reclamation_id=<?= $reclamationId ?>" class="btn-cancel">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane"></i> Envoyer la réponse
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Fonction de validation
        function validateAjouterReponseForm(event) {
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
                message.style.borderColor = '#A9B97D';
            }
            
            // Validation ID utilisateur
            const userId = document.getElementById('userId');
            const userIdNum = userId ? parseInt(userId.value) : 0;
            if (!userId || !userId.value.trim() || isNaN(userIdNum) || userIdNum < 1) {
                errors.push('L\'ID utilisateur doit être un nombre supérieur à 0');
                if (userId) userId.style.borderColor = '#C62828';
            } else if (userId) {
                userId.style.borderColor = '#A9B97D';
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
            messageTextarea.addEventListener('input', function() {
                const length = this.value.length;
                charCount.textContent = length;
                
                if (length > 1000) {
                    charCount.style.color = '#C62828';
                } else if (length > 800) {
                    charCount.style.color = '#FF9800';
                } else {
                    charCount.style.color = '#5E6D38';
                }
            });
            
            // Initialiser le compteur
            charCount.textContent = messageTextarea.value.length;
        }
    </script>
</body>
</html>

