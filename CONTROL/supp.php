<?php
/**
 * Supprimer une réclamation
 * Endpoint API DELETE
 */

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once "db.php";
require_once "ReclamationModel.php";

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Méthode non autorisée. Utilisez DELETE."
    ]);
    exit;
}

try {
    // L'ID peut être passé en paramètre GET ou dans le corps JSON
    $id = null;
    
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        // Essayer de récupérer depuis le corps JSON
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);
        
        if (json_last_error() === JSON_ERROR_NONE && !empty($data['id'])) {
            $id = $data['id'];
        }
    }
    
    if (empty($id)) {
        throw new Exception("ID de réclamation manquant");
    }
    
    // Créer l'instance du modèle
    $model = new ReclamationModel($conn);
    
    // Supprimer la réclamation
    $result = $model->delete($id);
    
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
