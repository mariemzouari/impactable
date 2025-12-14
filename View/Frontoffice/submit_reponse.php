<?php
require_once(__DIR__ . '/../../controller/ReponseController.php');
require_once(__DIR__ . '/../../MODEL/reponce.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    // Récupérer les données du formulaire (même logique que ajouter_reponse.php)
    $reclamationId = isset($_POST['reclamationId']) ? intval($_POST['reclamationId']) : 0;
    $userId = isset($_POST['userId']) ? intval($_POST['userId']) : 0;
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // Validation (identique à ajouter_reponse.php)
    if ($reclamationId <= 0) {
        throw new Exception('ID de réclamation invalide');
    }

    if ($userId <= 0) {
        throw new Exception('ID utilisateur invalide');
    }

    if (empty($message) || strlen($message) < 10) {
        throw new Exception('Le message doit contenir au moins 10 caractères');
    }

    if (strlen($message) > 1000) {
        throw new Exception('Le message ne peut pas dépasser 1000 caractères');
    }

    // Créer l'objet Reponse (même logique que ajouter_reponse.php)
    $reponse = new Reponse(
        null, // id
        $reclamationId,
        $userId,
        $message,
        new DateTime(), // dateReponse
        new DateTime()  // dernierUpdate
    );

    // Ajouter la réponse via le contrôleur (même logique que ajouter_reponse.php)
    $reponseController = new ReponseController();
    $result = $reponseController->addReponse($reponse);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Réponse ajoutée avec succès !'
        ]);
    } else {
        throw new Exception('Erreur lors de l\'ajout de la réponse');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

