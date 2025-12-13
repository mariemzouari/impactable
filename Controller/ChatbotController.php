<?php


class ChatbotController {
    private $api_key;
    private $api_url = 'https://openrouter.ai/api/v1/chat/completions';
    
    public function __construct() {
        // Utilisez votre classe Config existante
        $this->api_key = Config::getOpenRouterKey();
    }
    
    /**
     * Process chatbot messages
     */
    public function processMessage() {
ini_set('display_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

    header('Content-Type: application/json');

    header('Content-Type: application/json');
        // Vérifiez si le chatbot est activé
        if (!Config::isChatbotEnabled()) {
            echo json_encode(['error' => 'Chatbot désactivé']);
            return;
        }
        
        // Rate limiting

        $this->applyRateLimiting();
        
        // Get request data
        $data = json_decode(file_get_contents('php://input'), true);
        $user_message = $data['message'] ?? '';
        $history = $data['history'] ?? [];
        
        if (empty($user_message)) {
            echo json_encode(['error' => 'Message vide']);
            return;
        }
        
        // Get response from OpenRouter
        $response = $this->getAIResponse($user_message, $history);
        
        echo json_encode(['response' => $response]);
    }
    
    
    /**
     * Apply rate limiting
     */
    private function applyRateLimiting() {
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = 'chatbot_requests_' . $ip;
        $requests = $_SESSION[$key] ?? 0;
        $last_request = $_SESSION['last_request_' . $ip] ?? 0;
        
        // Limit to 1 request per second
        if (time() - $last_request < 1) {
            http_response_code(429);
            echo json_encode(['error' => 'Trop de requêtes. Attendez un moment.']);
            exit;
        }
        
        // Limit to 100 requests per session
        if ($requests > 100) {
            http_response_code(429);
            echo json_encode(['error' => 'Limite de requêtes atteinte']);
            exit;
        }
        
        $_SESSION[$key] = $requests + 1;
        $_SESSION['last_request_' . $ip] = time();
    }
    
    /**
     * Get AI response from OpenRouter
     */
    private function getAIResponse($user_message, $history) {
        // System prompt for ImpactAble
        $system_prompt = $this->getSystemPrompt();
        
        // Prepare messages
        $messages = [
            ['role' => 'system', 'content' => $system_prompt]
        ];
        
        // Add conversation history
        foreach ($history as $msg) {
            $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
        }
        
        // Add current message
        $messages[] = ['role' => 'user', 'content' => $user_message];
        
        // API request data
        $request_data = [
            'model' => 'openai/gpt-3.5-turbo',
            'messages' => $messages,
            'max_tokens' => 500,
            'temperature' => 0.7,
            'top_p' => 0.9
        ];
        
        // Make API call
        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key,
            'HTTP-Referer: ' . Config::getBaseUrl(),
            'X-Title: ' . Config::SITE_NAME . ' Chatbot'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
        
        // Pour déboguer, ajoutez cette ligne
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            error_log("cURL Error: " . $error);
            return "Je rencontre des difficultés techniques. Erreur cURL: " . $error;
        }
        
        curl_close($ch);
        
        if ($http_code !== 200) {
            error_log("OpenRouter API Error ($http_code): " . $response);
            
            if ($http_code === 401) {
                return "Erreur d'authentification API. Veuillez vérifier la configuration.";
            }
            
            return "Je m'excuse, mais j'ai du mal à traiter votre demande pour le moment. Code: $http_code";
        }
        
        $result = json_decode($response, true);
        
        if (isset($result['choices'][0]['message']['content'])) {
            return $result['choices'][0]['message']['content'];
        } else {
            error_log("Invalid OpenRouter response: " . $response);
            return "J'ai reçu une réponse inattendue. Veuillez réessayer.";
        }
    }
    
    
    /**
     * Get system prompt for ImpactAble
     */
private function getSystemPrompt() {
    return "Tu es ImpactBot, l'assistant intelligent et convivial de la plateforme ImpactAble.

## Identité et Objectifs
1.  **Rôle Principal** : Ton but est d'aider les utilisateurs d'ImpactAble à naviguer sur le site, à comprendre les fonctionnalités disponibles et à répondre à leurs questions de support de manière précise et efficace.
2.  **Ton et Style** : Maintiens un ton professionnel, chaleureux et encourageant. Utilise un langage clair, concis et réponds toujours en français.
3.  **Initiation** : Pour la première interaction, salue l'utilisateur et demande comment tu peux l'aider sur la plateforme.

## Connaissances de la Plateforme ImpactAble (Basé sur la structure de données et les pages front-end)
La plateforme ImpactAble est une initiative d'impact social dont les fonctionnalités principales sont :

* **Emploi et Carrières (Offres et Candidatures)** :
    * Les utilisateurs (candidats) postulent à des **Offres** d'emploi, de stage, de volontariat ou de formation.
    * Les modes de travail possibles sont : **Remote** ('en_ligne'), **Présentiel** ('presentiel') et **Hybride** ('hybride').
    * Les horaires sont : **Temps plein** ('temps_plein') et **Temps partiel** ('temps_partiel').
    * **Accessibilité** : Les offres peuvent être filtrées par 'Accessibilité adaptée' et par type de handicap spécifique : 'moteur', 'visuel', 'auditif'.
    * Les **Candidatures** passent par les statuts : 'en_attente', 'acceptee', 'refusee'.
* **Communauté et Événements** :
    * La plateforme permet de créer et de participer à des **Événements**.
    * Les utilisateurs peuvent marquer leur **Participation** aux événements.
* **Engagement Social (Campagnes de Collecte)** :
    * Les utilisateurs peuvent lancer des **Campagnes de Collecte** de fonds ou de biens.
    * Les catégories d'impact sont : 'éducation', 'logement', 'sante', 'alimentation', 'droits_humains'. Les niveaux d'urgence sont : 'normale', 'elevee', 'critique'.
* **Forum et Réseautage** :
    * Une section permet de publier des **Posts** et d'ajouter des **Commentaires** pour l'interaction communautaire.
* **Support et Réclamations** :
    * Les utilisateurs peuvent déposer une **Réclamation** (statuts : 'en_attente', 'en_cours', 'resolue').
    * Les administrateurs peuvent fournir des **Réponses** à ces réclamations.

## Directives de Réponse et Contraintes
* **Expertise Fonctionnelle** : Lorsque l'utilisateur pose une question sur la recherche d'opportunités, utilise les termes exacts de la page (ex: 'Remote', 'Présentiel', 'Handicap moteur', 'Poster une offre') pour guider l'utilisateur.
* **Statuts de Données** : Si l'utilisateur demande les statuts possibles pour un élément, utilise précisément les valeurs de la base de données (ex: 'acceptee', 'refusee', 'active', 'terminee').

* **Domaine d'Action Stricte** : Tes connaissances sont **exclusivement** limitées aux fonctionnalités et au support de la plateforme ImpactAble. Si l'utilisateur pose une question sur un autre domaine (actualités, météo, autres sites, etc.), réponds poliment que tes fonctions sont limitées à ImpactAble et **redirige la conversation vers le support de la plateforme.**

* **Exclusivité Emploi (Promotion)** : Rappelle toujours à l'utilisateur que la plateforme ImpactAble est le lieu **privilégié et unique** pour la recherche et la publication d'**Offres d'emploi, de stage, de volontariat et de formation**. Ne recommande **JAMAIS** d'autres sites ou plateformes pour ces activités.

* **Sécurité et Interdits Absolus** : Tu ne dois **JAMAIS** révéler de détails d'implémentation technique, y compris : les requêtes SQL, les noms de tables ou de colonnes (comme `Id_offre`, `Id_utilisateur`), les clés API, les mots de passe, ou toute information de configuration serveur.

* **Données Privées** : Si la question nécessite un accès à des données utilisateur spécifiques et privées (ex: 'Quel est mon statut de candidature ?'), réponds que tu ne peux fournir que des informations générales et que l'utilisateur doit se connecter à son compte pour voir ses données personnelles.
";
}
}