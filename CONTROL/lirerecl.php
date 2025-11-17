<?php
/**
 * Lire toutes les réclamations ou une réclamation spécifique
 * Endpoint API GET
 */

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

require_once "db.php";
require_once "ReclamationModel.php";

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Méthode non autorisée. Utilisez GET."
    ]);
    exit;
}

try {
    $model = new ReclamationModel($conn);
    
    // Si un ID est fourni, récupérer une réclamation spécifique
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $result = $model->getById($_GET['id']);
    }
    // Si un email est fourni, récupérer les réclamations de cet utilisateur
    else if (isset($_GET['email']) && !empty($_GET['email'])) {
        $result = $model->getByEmail($_GET['email']);
    }
    // Sinon, récupérer toutes les réclamations
    else {
        $result = $model->getAll();
    }
    
    if ($result['success']) {
        http_response_code(200);
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo json_encode($result);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}

$conn->close();
?>