<?php
/**
 * Créer une nouvelle réclamation
 * Endpoint API POST
 */

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "db.php";
require_once "ReclamationModel.php";

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Méthode non autorisée. Utilisez POST."
    ]);
    exit;
}

try {
    // Récupérer les données JSON
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Erreur de parsing JSON: " . json_last_error_msg());
    }
    
    // Valider les champs requis
    $requiredFields = [
        'nom', 'prenom', 'email', 'telephone', 'adresse',
        'typeHandicap', 'categorie', 'lieu', 'dateIncident',
        'sujet', 'description', 'solutionSouhaitee', 'priorite'
    ];
    
    $missingFields = [];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        throw new Exception("Champs manquants: " . implode(', ', $missingFields));
    }
    
    // Valider l'email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Format d'email invalide");
    }
    
    // Créer l'instance du modèle
    $model = new ReclamationModel($conn);
    
    // Créer la réclamation
    $result = $model->create($data);
    
    if ($result['success']) {
        http_response_code(201); // Created
        echo json_encode($result);
    } else {
        http_response_code(400); // Bad Request
        echo json_encode($result);
    }
    
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}

$conn->close();
?>