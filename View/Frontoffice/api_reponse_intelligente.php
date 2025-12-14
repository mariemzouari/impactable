<?php
/**
 * API Réponse Intelligente - ImpactAble
 * Version 1.0
 * 
 * Endpoints:
 * - POST ?action=generate : Générer une réponse automatique
 * - POST ?action=analyze_sentiment : Analyser le sentiment d'un texte
 * - POST ?action=quality_score : Calculer le score de qualité d'une réponse
 * - GET ?action=templates : Obtenir les modèles par catégorie
 * - GET ?action=quick_responses : Obtenir les réponses rapides
 * - GET ?action=solutions : Obtenir les solutions suggérées
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once(__DIR__ . '/../../SERVICE/ReponseIntelligente.php');
require_once(__DIR__ . '/../../controller/ReclamationController.php');

$response = [
    'success' => false,
    'data' => null,
    'error' => null,
    'timestamp' => date('Y-m-d H:i:s')
];

try {
    $action = isset($_GET['action']) ? $_GET['action'] : 'generate';
    
    switch ($action) {
        
        // ==================== GÉNÉRER UNE RÉPONSE ====================
        case 'generate':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $response['error'] = 'Méthode POST requise';
                break;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Option 1: Générer depuis un ID de réclamation
            if (isset($input['reclamation_id'])) {
                $reclamationController = new ReclamationController();
                $reclamation = $reclamationController->showReclamationById(intval($input['reclamation_id']));
                
                if (!$reclamation) {
                    $response['error'] = 'Réclamation non trouvée';
                    break;
                }
            }
            // Option 2: Générer depuis les données fournies
            elseif (isset($input['reclamation'])) {
                $reclamation = $input['reclamation'];
            }
            else {
                $response['error'] = 'Données réclamation requises (reclamation_id ou reclamation)';
                break;
            }
            
            // Générer la réponse
            $result = ReponseIntelligente::genererReponse($reclamation);
            
            $response['success'] = true;
            $response['data'] = $result;
            break;
        
        // ==================== ANALYSER LE SENTIMENT ====================
        case 'analyze_sentiment':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $response['error'] = 'Méthode POST requise';
                break;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $texte = isset($input['texte']) ? trim($input['texte']) : '';
            
            if (empty($texte)) {
                $response['error'] = 'Texte requis pour l\'analyse';
                break;
            }
            
            $sentiment = ReponseIntelligente::analyserSentiment($texte);
            
            // Ajouter des informations sur le sentiment détecté
            $sentimentLabels = [
                'colere' => ['label' => 'Colère', 'emoji' => '😠', 'couleur' => '#e74c3c'],
                'frustration' => ['label' => 'Frustration', 'emoji' => '😤', 'couleur' => '#e67e22'],
                'urgence' => ['label' => 'Urgence', 'emoji' => '⚡', 'couleur' => '#f39c12'],
                'detresse' => ['label' => 'Détresse', 'emoji' => '😰', 'couleur' => '#9b59b6'],
                'neutre' => ['label' => 'Neutre', 'emoji' => '😐', 'couleur' => '#95a5a6'],
                'positif' => ['label' => 'Positif', 'emoji' => '😊', 'couleur' => '#27ae60']
            ];
            
            $sentiment['details'] = $sentimentLabels[$sentiment['type']] ?? $sentimentLabels['neutre'];
            
            $response['success'] = true;
            $response['data'] = $sentiment;
            break;
        
        // ==================== SCORE DE QUALITÉ ====================
        case 'quality_score':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $response['error'] = 'Méthode POST requise';
                break;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $texte = isset($input['texte']) ? trim($input['texte']) : '';
            $reclamation = isset($input['reclamation']) ? $input['reclamation'] : [];
            
            if (empty($texte)) {
                $response['error'] = 'Texte de réponse requis';
                break;
            }
            
            $score = ReponseIntelligente::calculerScoreQualite($texte, $reclamation);
            
            $response['success'] = true;
            $response['data'] = $score;
            break;
        
        // ==================== OBTENIR LES MODÈLES ====================
        case 'templates':
            $categorie = isset($_GET['categorie']) ? $_GET['categorie'] : null;
            
            if ($categorie) {
                $templates = ReponseIntelligente::getTemplatesByCategorie($categorie);
            } else {
                $templates = ReponseIntelligente::getTemplates();
            }
            
            $response['success'] = true;
            $response['data'] = [
                'templates' => $templates,
                'categories_disponibles' => array_keys(ReponseIntelligente::getTemplates())
            ];
            break;
        
        // ==================== RÉPONSES RAPIDES ====================
        case 'quick_responses':
            $type = isset($_GET['type']) ? $_GET['type'] : null;
            $params = [];
            
            // Récupérer les paramètres GET
            if (isset($_GET['numero'])) $params['numero'] = $_GET['numero'];
            if (isset($_GET['delai'])) $params['delai'] = $_GET['delai'];
            
            if ($type) {
                $reponseRapide = ReponseIntelligente::genererReponseRapide($type, $params);
                $response['success'] = true;
                $response['data'] = [
                    'type' => $type,
                    'texte' => $reponseRapide
                ];
            } else {
                // Retourner tous les types disponibles
                $response['success'] = true;
                $response['data'] = [
                    'types_disponibles' => [
                        'accuse_reception' => 'Accusé de réception',
                        'demande_info' => 'Demande d\'informations',
                        'en_cours' => 'En cours de traitement',
                        'resolution' => 'Résolution',
                        'escalade' => 'Escalade',
                        'cloture' => 'Clôture'
                    ],
                    'exemple' => 'Utilisez ?action=quick_responses&type=accuse_reception&numero=123'
                ];
            }
            break;
        
        // ==================== SOLUTIONS SUGGÉRÉES ====================
        case 'solutions':
            $categorie = isset($_GET['categorie']) ? $_GET['categorie'] : '';
            
            if (empty($categorie)) {
                $response['error'] = 'Catégorie requise';
                break;
            }
            
            $result = ReponseIntelligente::genererReponse(['categorie' => $categorie]);
            
            $response['success'] = true;
            $response['data'] = [
                'categorie' => $categorie,
                'solutions' => $result['solutions_disponibles']
            ];
            break;
        
        // ==================== INFO API ====================
        case 'info':
            $response['success'] = true;
            $response['data'] = [
                'api_version' => '1.0',
                'name' => 'API Réponse Intelligente',
                'description' => 'Génération automatique de réponses aux réclamations',
                'endpoints' => [
                    'POST ?action=generate' => 'Générer une réponse complète',
                    'POST ?action=analyze_sentiment' => 'Analyser le sentiment',
                    'POST ?action=quality_score' => 'Calculer le score de qualité',
                    'GET ?action=templates' => 'Obtenir les modèles',
                    'GET ?action=quick_responses' => 'Réponses rapides',
                    'GET ?action=solutions' => 'Solutions par catégorie'
                ],
                'fonctionnalites' => [
                    '✅ Génération automatique de réponses',
                    '✅ Analyse de sentiment',
                    '✅ Score de qualité',
                    '✅ Modèles par catégorie',
                    '✅ Réponses rapides prédéfinies',
                    '✅ Suggestions de solutions'
                ]
            ];
            break;
        
        default:
            $response['error'] = 'Action non reconnue: ' . htmlspecialchars($action);
    }
    
} catch (Exception $e) {
    $response['error'] = 'Erreur serveur: ' . $e->getMessage();
    error_log('API Reponse Intelligente Error: ' . $e->getMessage());
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>




