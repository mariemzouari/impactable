<?php
require_once(__DIR__ . '/../../../controller/ReponseController.php');

// Vérification
if (!isset($_GET['id']) || !isset($_GET['reclamation_id'])) {
    header('Location: liste_reponses.php?reclamation_id=' . (isset($_GET['reclamation_id']) ? intval($_GET['reclamation_id']) : 0));
    exit();
}

$id = intval($_GET['id']);
$reclamationId = intval($_GET['reclamation_id']);

try {
    $controller = new ReponseController();
    $controller->deleteReponse($id);
    
    // Rediriger vers la liste des réponses avec un message de succès
    header('Location: liste_reponses.php?reclamation_id=' . $reclamationId . '&success=1');
    exit();
} catch (Exception $e) {
    // Rediriger avec un message d'erreur
    header('Location: liste_reponses.php?reclamation_id=' . $reclamationId . '&error=' . urlencode($e->getMessage()));
    exit();
}
