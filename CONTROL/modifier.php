<?php
/**
 * Modifier une réclamation existante
 * Endpoint API PUT
 */

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type");

require_once "db.php";
require_once "ReclamationModel.php";

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Méthode non autorisée. Utilisez PUT."
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
    
    // Valider les données
    if (empty($data['id'])) {
        throw new Exception("ID de réclamation manquant");
    }
    
    if (empty($data['status']) && empty($data['priorite'])) {
        throw new Exception("Aucune donnée à mettre à jour (status ou priorite requis)");
    }
    
    // Valider le statut si fourni
    $validStatuses = ['en_attente', 'en_cours', 'resolu', 'rejete'];
    if (!empty($data['status']) && !in_array($data['status'], $validStatuses)) {
        throw new Exception("Statut invalide. Valeurs autorisées: " . implode(', ', $validStatuses));
    }
    
    // Valider la priorité si fournie
    $validPriorites = ['basse', 'moyenne', 'haute', 'urgente'];
    if (!empty($data['priorite']) && !in_array($data['priorite'], $validPriorites)) {
        throw new Exception("Priorité invalide. Valeurs autorisées: " . implode(', ', $validPriorites));
    }
    
    // Créer l'instance du modèle
    $model = new ReclamationModel($conn);
    
    // Mettre à jour la réclamation
    $result = $model->update($data['id'], $data);
    
    if ($result['success']) {
        http_response_code(200);
        echo json_encode($result);
    } else {
        http_response_code(400);
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