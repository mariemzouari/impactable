<?php
require_once(__DIR__ . '/../../../controller/ReclamationController.php');
require_once(__DIR__ . '/../../../CONFIGRRATION/config.php');

$controller = new ReclamationController();
$reclamation = null;

if (isset($_GET['id'])) {
    $reclamation = $controller->showReclamationById($_GET['id']);
    
    // Récupérer les informations de l'utilisateur
    $user = null;
    if ($reclamation) {
        try {
            $db = config::getConnexion();
            // Vérifier si la table utilisateur existe
            $checkTable = $db->query("SHOW TABLES LIKE 'utilisateur'");
            if ($checkTable->rowCount() > 0) {
                $userQuery = $db->prepare("SELECT * FROM utilisateur WHERE Id_utilisateur = :id");
                $userQuery->execute(['id' => $reclamation['utilisateurId']]);
                $user = $userQuery->fetch();
            }
        } catch (Exception $e) {
            // Table utilisateur n'existe pas, continuer sans
            $user = null;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Réclamation</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/admin-style.css">
</head>
<body class="without-sidebar">
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-file-alt"></i> Détails de la Réclamation</h1>
            <a href="../admin_dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <?php if ($reclamation): ?>
            <div class="info-section">
                <h3><i class="fas fa-info-circle"></i> Informations Générales</h3>
                <p><strong>ID:</strong> #<?= htmlspecialchars($reclamation['id']) ?></p>
                <p><strong>Date de création:</strong> <?= date('d/m/Y à H:i', strtotime($reclamation['dateCreation'])) ?></p>
                <p><strong>Dernière modification:</strong> <?= date('d/m/Y à H:i', strtotime($reclamation['derniereModification'])) ?></p>
                <p><strong>Statut:</strong> 
                    <span class="badge status-<?= htmlspecialchars($reclamation['statut']) ?>">
                        <?= htmlspecialchars($reclamation['statut']) ?>
                    </span>
                </p>
                <p><strong>Priorité:</strong> 
                    <span class="badge priority-<?= htmlspecialchars($reclamation['priorite']) ?>">
                        <?= htmlspecialchars($reclamation['priorite']) ?>
                    </span>
                </p>
            </div>

            <?php if ($user): ?>
            <div class="info-section">
                <h3><i class="fas fa-user"></i> Informations Utilisateur</h3>
                <p><strong>Nom complet:</strong> <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Téléphone:</strong> <?= htmlspecialchars($user['numero_tel'] ?? 'Non renseigné') ?></p>
                <p><strong>ID Utilisateur:</strong> <?= htmlspecialchars($reclamation['utilisateurId']) ?></p>
            </div>
            <?php endif; ?>

            <div class="info-section">
                <h3><i class="fas fa-file-alt"></i> Détails de la Réclamation</h3>
                <p><strong>Sujet:</strong> <?= htmlspecialchars($reclamation['sujet']) ?></p>
                <p><strong>Catégorie:</strong> <?= htmlspecialchars($reclamation['categorie']) ?></p>
                <p><strong>Description:</strong></p>
                <div style="background: white; padding: 15px; border-radius: 8px; margin-top: 10px; color: #4B2E16;">
                    <?= nl2br(htmlspecialchars($reclamation['description'])) ?>
                </div>
            </div>

            <div class="info-section">
                <h3><i class="fas fa-user-tie"></i> Agent Attribué</h3>
                <p><strong>Agent:</strong> <?= htmlspecialchars($reclamation['agentAttribue'] ?? 'Non attribué') ?></p>
            </div>

            <div class="action-buttons">
                <a href="../reponsecrud/ajouter_reponse.php?reclamation_id=<?= $reclamation['id'] ?>" class="btn btn-success">
                    <i class="fas fa-reply"></i> Répondre
                </a>
                <a href="../reponsecrud/liste_reponses.php?reclamation_id=<?= $reclamation['id'] ?>" class="btn btn-info">
                    <i class="fas fa-comments"></i> Voir les Réponses
                </a>
                <a href="updateReclamation.php?id=<?= $reclamation['id'] ?>" class="btn btn-secondary">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            </div>
        <?php else: ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i> Réclamation non trouvée
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
