<?php
/**
 * 🧠 API de Détection d'Émotions
 * ImpactAble - Analyse Émotionnelle en Temps Réel
 * 
 * Endpoints:
 * - POST ?action=analyze : Analyser un texte
 * - GET ?action=emotions : Liste des émotions
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once(__DIR__ . '/../../SERVICE/EmotionDetector.php');

$response = [
    'success' => false,
    'data' => null,
    'error' => null,
    'timestamp' => date('Y-m-d H:i:s')
];

try {
    $action = $_GET['action'] ?? 'analyze';
    
    switch ($action) {
        
        case 'analyze':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $response['error'] = 'Méthode POST requise';
                break;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $texte = trim($input['texte'] ?? '');
            
            if (empty($texte)) {
                $response['error'] = 'Texte requis';
                break;
            }
            
            $analyse = EmotionDetector::analyser($texte);
            
            $response['success'] = true;
            $response['data'] = $analyse;
            break;
        
        case 'emotions':
            $response['success'] = true;
            $response['data'] = EmotionDetector::getEmotionsDisponibles();
            break;
        
        default:
            $response['error'] = 'Action non reconnue';
    }
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>

