<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');

require_once(__DIR__ . '/../../SERVICE/ChatBot.php');

$response = ['success' => false, 'data' => null, 'error' => null];

try {
    $action = isset($_GET['action']) ? $_GET['action'] : 'message';
    
    switch ($action) {
        case 'welcome':
            $response['success'] = true;
            $response['data'] = ChatBot::getWelcomeMessage();
            $response['data']['suggestions'] = ChatBot::getSuggestions();
            break;
            
        case 'suggestions':
            $response['success'] = true;
            $response['data'] = ['suggestions' => ChatBot::getSuggestions()];
            break;
            
        case 'message':
        default:
            $message = '';
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                $message = isset($input['message']) ? $input['message'] : '';
                
                if (empty($message) && isset($_POST['message'])) {
                    $message = $_POST['message'];
                }
            } else {
                $message = isset($_GET['message']) ? $_GET['message'] : '';
            }
            
            if (empty($message)) {
                $response['error'] = 'Message vide';
                break;
            }
            
            $botResponse = ChatBot::processMessage($message);
            $response['success'] = true;
            $response['data'] = $botResponse;
            $response['data']['suggestions'] = ChatBot::getSuggestions();
            break;
    }
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
