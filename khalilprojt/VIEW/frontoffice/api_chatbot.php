<?php
/**
 * API Chatbot Avancée - ImpactAble
 * Version 2.0 avec fonctionnalités enrichies
 * 
 * Endpoints:
 * - GET/POST ?action=message : Envoyer un message
 * - GET ?action=welcome : Message de bienvenue
 * - GET ?action=suggestions : Obtenir des suggestions
 * - GET ?action=history : Historique de conversation
 * - GET ?action=stats : Statistiques du chatbot
 * - GET ?action=quick_actions : Actions rapides disponibles
 * - POST ?action=feedback : Donner un feedback sur une réponse
 */

session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once(__DIR__ . '/../../SERVICE/ChatBot.php');

// Initialiser la session de conversation
if (!isset($_SESSION['chatbot'])) {
    $_SESSION['chatbot'] = [
        'history' => [],
        'started_at' => date('Y-m-d H:i:s'),
        'message_count' => 0,
        'feedbacks' => []
    ];
}

$response = [
    'success' => false,
    'data' => null,
    'error' => null,
    'timestamp' => date('Y-m-d H:i:s'),
    'session_id' => session_id()
];

try {
    $action = isset($_GET['action']) ? $_GET['action'] : 'message';
    
    switch ($action) {
        
        // ==================== MESSAGE DE BIENVENUE ====================
        case 'welcome':
            $welcomeData = ChatBot::getWelcomeMessage();
            $welcomeData['suggestions'] = ChatBot::getSuggestions();
            $welcomeData['quick_actions'] = getQuickActions();
            $welcomeData['session_info'] = [
                'is_new' => $_SESSION['chatbot']['message_count'] === 0,
                'message_count' => $_SESSION['chatbot']['message_count']
            ];
            
            // Ajouter au historique
            addToHistory('bot', $welcomeData['response'], 'welcome');
            
            $response['success'] = true;
            $response['data'] = $welcomeData;
            break;
        
        // ==================== SUGGESTIONS ====================
        case 'suggestions':
            $context = isset($_GET['context']) ? $_GET['context'] : '';
            $suggestions = getContextualSuggestions($context);
            
            $response['success'] = true;
            $response['data'] = ['suggestions' => $suggestions];
            break;
        
        // ==================== TRAITEMENT MESSAGE ====================
        case 'message':
            $message = '';
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                $message = isset($input['message']) ? trim($input['message']) : '';
                
                if (empty($message) && isset($_POST['message'])) {
                    $message = trim($_POST['message']);
                }
            } else {
                $message = isset($_GET['message']) ? trim($_GET['message']) : '';
            }
            
            if (empty($message)) {
                $response['error'] = 'Message vide. Veuillez entrer un message.';
                $response['error_code'] = 'EMPTY_MESSAGE';
                break;
            }
            
            // Limiter la longueur du message
            if (strlen($message) > 1000) {
                $response['error'] = 'Message trop long (max 1000 caractères).';
                $response['error_code'] = 'MESSAGE_TOO_LONG';
                break;
            }
            
            // Ajouter le message utilisateur à l'historique
            addToHistory('user', $message);
            $_SESSION['chatbot']['message_count']++;
            
            // Traiter le message
            $botResponse = ChatBot::processMessage($message);
            
            // Enrichir la réponse
            $botResponse['suggestions'] = getContextualSuggestions($botResponse['category']);
            $botResponse['quick_actions'] = getQuickActions($botResponse['category']);
            $botResponse['message_id'] = uniqid('msg_');
            $botResponse['response_time'] = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
            
            // Ajouter la réponse à l'historique
            addToHistory('bot', $botResponse['response'], $botResponse['category'], $botResponse['message_id']);
            
            // Détecter les actions spéciales
            $specialAction = detectSpecialAction($message);
            if ($specialAction) {
                $botResponse['special_action'] = $specialAction;
            }
            
            $response['success'] = true;
            $response['data'] = $botResponse;
            break;
        
        // ==================== HISTORIQUE ====================
        case 'history':
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
            $history = array_slice($_SESSION['chatbot']['history'], -$limit);
            
            $response['success'] = true;
            $response['data'] = [
                'history' => $history,
                'total_messages' => count($_SESSION['chatbot']['history']),
                'session_started' => $_SESSION['chatbot']['started_at']
            ];
            break;
        
        // ==================== STATISTIQUES ====================
        case 'stats':
            $response['success'] = true;
            $response['data'] = [
                'session' => [
                    'message_count' => $_SESSION['chatbot']['message_count'],
                    'started_at' => $_SESSION['chatbot']['started_at'],
                    'duration_seconds' => time() - strtotime($_SESSION['chatbot']['started_at'])
                ],
                'categories_used' => getCategoriesUsed(),
                'average_confidence' => getAverageConfidence(),
                'feedbacks' => $_SESSION['chatbot']['feedbacks']
            ];
            break;
        
        // ==================== ACTIONS RAPIDES ====================
        case 'quick_actions':
            $category = isset($_GET['category']) ? $_GET['category'] : '';
            
            $response['success'] = true;
            $response['data'] = [
                'quick_actions' => getQuickActions($category),
                'all_actions' => getAllQuickActions()
            ];
            break;
        
        // ==================== FEEDBACK ====================
        case 'feedback':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $response['error'] = 'Méthode POST requise pour le feedback.';
                break;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $messageId = isset($input['message_id']) ? $input['message_id'] : '';
            $rating = isset($input['rating']) ? intval($input['rating']) : 0;
            $comment = isset($input['comment']) ? trim($input['comment']) : '';
            
            if (empty($messageId) || $rating < 1 || $rating > 5) {
                $response['error'] = 'Feedback invalide. Rating doit être entre 1 et 5.';
                break;
            }
            
            $_SESSION['chatbot']['feedbacks'][] = [
                'message_id' => $messageId,
                'rating' => $rating,
                'comment' => $comment,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            $response['success'] = true;
            $response['data'] = ['message' => 'Merci pour votre feedback !'];
            break;
        
        // ==================== RESET SESSION ====================
        case 'reset':
            $_SESSION['chatbot'] = [
                'history' => [],
                'started_at' => date('Y-m-d H:i:s'),
                'message_count' => 0,
                'feedbacks' => []
            ];
            
            $response['success'] = true;
            $response['data'] = ['message' => 'Session réinitialisée.'];
            break;
        
        // ==================== INFO API ====================
        case 'info':
            $response['success'] = true;
            $response['data'] = [
                'api_version' => '2.0',
                'bot_name' => 'Khalil',
                'available_actions' => [
                    'message' => 'Envoyer un message au chatbot',
                    'welcome' => 'Obtenir le message de bienvenue',
                    'suggestions' => 'Obtenir des suggestions contextuelles',
                    'history' => 'Voir l\'historique de conversation',
                    'stats' => 'Statistiques de la session',
                    'quick_actions' => 'Actions rapides disponibles',
                    'feedback' => 'Donner un feedback (POST)',
                    'reset' => 'Réinitialiser la session',
                    'info' => 'Informations sur l\'API'
                ],
                'supported_languages' => ['fr'],
                'max_message_length' => 1000
            ];
            break;
        
        default:
            $response['error'] = 'Action non reconnue: ' . htmlspecialchars($action);
            $response['error_code'] = 'UNKNOWN_ACTION';
    }
    
} catch (Exception $e) {
    $response['error'] = 'Erreur serveur: ' . $e->getMessage();
    $response['error_code'] = 'SERVER_ERROR';
    error_log('Chatbot API Error: ' . $e->getMessage());
}

// ==================== FONCTIONS HELPER ====================

/**
 * Ajouter un message à l'historique
 */
function addToHistory($sender, $message, $category = null, $messageId = null) {
    $_SESSION['chatbot']['history'][] = [
        'id' => $messageId ?? uniqid('msg_'),
        'sender' => $sender,
        'message' => $message,
        'category' => $category,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // Limiter l'historique à 100 messages
    if (count($_SESSION['chatbot']['history']) > 100) {
        array_shift($_SESSION['chatbot']['history']);
    }
}

/**
 * Obtenir des suggestions contextuelles
 */
function getContextualSuggestions($category = '') {
    $baseSuggestions = ChatBot::getSuggestions();
    
    $contextualSuggestions = [
        'faire_reclamation' => [
            "📋 Quels documents fournir ?",
            "📸 Comment ajouter une photo ?",
            "⏱️ Quel délai de réponse ?"
        ],
        'suivi' => [
            "🔄 Ma réclamation est en retard",
            "📧 Je n'ai pas reçu de notification",
            "❓ C'est quoi le statut 'En cours' ?"
        ],
        'priorite' => [
            "🔴 Comment passer en urgent ?",
            "🧠 L'IA s'est trompée de priorité",
            "⏱️ Délai pour chaque priorité ?"
        ],
        'handicap' => [
            "♿ Problème d'accessibilité bâtiment",
            "🚌 Transport non adapté",
            "⚖️ Discrimination au travail"
        ],
        'aide' => [
            "💻 Le formulaire bug",
            "🔑 J'ai perdu mon numéro",
            "📞 Contacter un humain"
        ]
    ];
    
    if (isset($contextualSuggestions[$category])) {
        return $contextualSuggestions[$category];
    }
    
    return $baseSuggestions;
}

/**
 * Obtenir les actions rapides
 */
function getQuickActions($category = '') {
    $actions = [
        [
            'id' => 'new_reclamation',
            'label' => '📝 Nouvelle réclamation',
            'action' => 'navigate',
            'url' => 'index.php#reclamation-form'
        ],
        [
            'id' => 'track',
            'label' => '🔍 Suivre mon dossier',
            'action' => 'navigate',
            'url' => 'suivi_reclamation.php'
        ],
        [
            'id' => 'demo_ia',
            'label' => '🧠 Tester l\'IA',
            'action' => 'navigate',
            'url' => 'demo_ia.php'
        ]
    ];
    
    // Actions spécifiques selon le contexte
    if ($category === 'aide' || $category === 'erreur_formulaire') {
        array_unshift($actions, [
            'id' => 'contact',
            'label' => '📧 Contacter le support',
            'action' => 'mailto',
            'url' => 'mailto:support@impactable.tn'
        ]);
    }
    
    return $actions;
}

/**
 * Obtenir toutes les actions rapides
 */
function getAllQuickActions() {
    return [
        'navigation' => [
            ['id' => 'home', 'label' => '🏠 Accueil', 'url' => 'index.php'],
            ['id' => 'new', 'label' => '📝 Nouvelle réclamation', 'url' => 'index.php#reclamation-form'],
            ['id' => 'track', 'label' => '🔍 Suivi', 'url' => 'suivi_reclamation.php'],
            ['id' => 'demo', 'label' => '🧠 Démo IA', 'url' => 'demo_ia.php']
        ],
        'contact' => [
            ['id' => 'email', 'label' => '📧 Email', 'url' => 'mailto:support@impactable.tn'],
            ['id' => 'tel', 'label' => '📞 Téléphone', 'url' => 'tel:+21612345678']
        ],
        'help' => [
            ['id' => 'faq', 'label' => '❓ FAQ', 'message' => 'Questions fréquentes'],
            ['id' => 'guide', 'label' => '📖 Guide', 'message' => 'Comment utiliser ImpactAble ?']
        ]
    ];
}

/**
 * Détecter les actions spéciales dans le message
 */
function detectSpecialAction($message) {
    $message = mb_strtolower($message, 'UTF-8');
    
    // Détecter demande de navigation
    if (preg_match('/(aller|voir|accéder|ouvrir).*(formulaire|réclamation|suivi|accueil)/u', $message)) {
        if (strpos($message, 'suivi') !== false) {
            return ['type' => 'navigate', 'url' => 'suivi_reclamation.php', 'label' => 'Aller au suivi'];
        }
        if (strpos($message, 'formulaire') !== false || strpos($message, 'réclamation') !== false) {
            return ['type' => 'navigate', 'url' => 'index.php#reclamation-form', 'label' => 'Aller au formulaire'];
        }
    }
    
    // Détecter demande de contact
    if (preg_match('/(contacter|appeler|écrire|email|mail|téléphone)/u', $message)) {
        return ['type' => 'contact', 'email' => 'support@impactable.tn', 'label' => 'Contacter le support'];
    }
    
    // Détecter demande d'aide urgente
    if (preg_match('/(urgence|urgent|immédiat|vite|sos|aide)/u', $message)) {
        return ['type' => 'urgent', 'label' => 'Marquer comme urgent'];
    }
    
    return null;
}

/**
 * Obtenir les catégories utilisées dans la session
 */
function getCategoriesUsed() {
    $categories = [];
    foreach ($_SESSION['chatbot']['history'] as $msg) {
        if ($msg['sender'] === 'bot' && !empty($msg['category'])) {
            $cat = $msg['category'];
            if (!isset($categories[$cat])) {
                $categories[$cat] = 0;
            }
            $categories[$cat]++;
        }
    }
    return $categories;
}

/**
 * Calculer la confiance moyenne des réponses
 */
function getAverageConfidence() {
    // Placeholder - dans une vraie implémentation, on stockerait les scores
    return 85;
}

// Envoyer la réponse
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
