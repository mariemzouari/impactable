<?php
// Inclure les fichiers nécessaires
require_once __DIR__ . '/../../../config/Config.php';
require_once __DIR__ . '/../../../Model/EventModel.php';
require_once __DIR__ . '/../../../Model/ParticipationModel.php';
require_once __DIR__ . '/../../../Controller/EventController.php';
require_once __DIR__ . '/../../../Controller/ParticipationController.php';

$config = new Config();
$db = $config->getPDO();

$eventModel = new EventModel($db);
$participationModel = new ParticipationModel($db);
$eventController = new EventController();
$participationController = new ParticipationController();

// Get event ID from URL
$eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($eventId === 0) {
    echo "ID d'événement manquant.";
    exit;
}

$event = $eventModel->getById($eventId);
if (!$event) {
    echo "Événement non trouvé.";
    exit;
}

// Check if user is logged in (for participation status)
$userId = 1; // Placeholder for logged-in user ID
$isParticipating = $participationModel->isUserParticipating($userId, $eventId);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'événement</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/style rayen.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($event['titre']); ?></h1>
    </header>
    <main>
        <div class="event-detail-container">
            <img src="../../assets/images/event_placeholder.jpg" alt="Image de l'événement" class="event-detail-image">
            <p><strong>Date:</strong> <?php echo htmlspecialchars($event['date_event']); ?></p>
            <p><strong>Catégorie:</strong> <?php echo htmlspecialchars($event['categorie']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($event['description']); ?></p>

            <?php if ($isParticipating): ?>
                <p class="participation-status">Vous participez à cet événement.</p>
                <form action="../../../Controller/ParticipationController.php?action=cancel" method="POST">
                    <input type="hidden" name="id_evenement" value="<?php echo $eventId; ?>">
                    <input type="hidden" name="id_utilisateur" value="<?php echo $userId; ?>">
                    <button type="submit" class="btn-cancel">Annuler participation</button>
                </form>
            <?php else: ?>
                <form action="../../../Controller/ParticipationController.php?action=participate" method="POST">
                    <input type="hidden" name="id_evenement" value="<?php echo $eventId; ?>">
                    <input type="hidden" name="id_utilisateur" value="<?php echo $userId; ?>">
                    <button type="submit" class="btn-participate">Participer</button>
                </form>
            <?php endif; ?>

            <!-- Edit Event Button (visible only to admins or event creators) -->
            <form action="../../../Controller/EventController.php?action=edit&id=<?php echo $eventId; ?>" method="GET">
                <button type="submit" class="btn-edit">Modifier l'événement</button>
            </form>

            <a href="../index.php" class="btn-back">Retour à la liste des événements</a>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 Tous droits réservés</p>
    </footer>
    <script src="../../assets/js/script.js"></script>
</body>
</html>