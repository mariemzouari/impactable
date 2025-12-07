<?php
/**
 * Service ChatBot Intelligent pour ImpactAble
 * Répond aux questions des utilisateurs sur les réclamations
 * Utilise un système de matching de mots-clés et de réponses contextuelles
 */
class ChatBot {
    
    private static $botName = "ImpactBot";
    private static $botAvatar = "🤖";
    
    // Base de connaissances du chatbot
    private static $knowledgeBase = [
        // Salutations
        'salutations' => [
            'keywords' => ['bonjour', 'salut', 'hello', 'hi', 'bonsoir', 'hey', 'coucou'],
            'responses' => [
                "Bonjour ! 👋 Je suis ImpactBot, votre assistant virtuel. Comment puis-je vous aider aujourd'hui ?",
                "Salut ! 😊 Je suis là pour vous aider avec vos réclamations. Que puis-je faire pour vous ?",
                "Bienvenue sur ImpactAble ! Je suis ImpactBot. Posez-moi vos questions sur les réclamations."
            ]
        ],
        
        // Comment faire une réclamation
        'faire_reclamation' => [
            'keywords' => ['faire réclamation', 'créer réclamation', 'nouvelle réclamation', 'déposer réclamation', 'soumettre', 'envoyer réclamation', 'comment réclamer'],
            'responses' => [
                "Pour faire une réclamation :\n\n1️⃣ Allez sur la page d'accueil\n2️⃣ Remplissez le formulaire avec vos informations\n3️⃣ Décrivez votre problème en détail\n4️⃣ Choisissez la priorité\n5️⃣ Cliquez sur 'Envoyer'\n\nVotre réclamation sera traitée sous 48h ! ⏱️",
                "C'est simple ! 📝\n\n• Cliquez sur 'Nouvelle Réclamation'\n• Remplissez tous les champs obligatoires\n• Notre système IA analysera automatiquement la priorité\n• Vous recevrez un numéro de suivi\n\nBesoin d'aide pour remplir le formulaire ?"
            ]
        ],
        
        // Suivi de réclamation
        'suivi' => [
            'keywords' => ['suivi', 'suivre', 'où en est', 'statut', 'état', 'avancement', 'tracker', 'numéro'],
            'responses' => [
                "Pour suivre votre réclamation :\n\n1️⃣ Cliquez sur 'Suivre ma Réclamation' en haut de la page\n2️⃣ Entrez votre numéro de réclamation\n3️⃣ Vous verrez l'état actuel et l'historique\n\n📊 États possibles : En attente → En cours → Résolue → Clôturée",
                "Vous pouvez suivre votre réclamation en temps réel ! 🔍\n\nCliquez sur le bouton 'Suivre ma Réclamation' et entrez votre numéro de suivi (ex: #123).\n\nVous verrez une timeline avec toutes les étapes."
            ]
        ],
        
        // Priorités
        'priorite' => [
            'keywords' => ['priorité', 'urgent', 'urgence', 'importante', 'normal', 'délai', 'temps'],
            'responses' => [
                "Notre système de priorité intelligent :\n\n🔴 **Urgente** : Traitement immédiat (danger, blocage)\n🟠 **Moyenne** : Traitement sous 48h\n🟢 **Faible** : Traitement sous 5 jours\n\n💡 Notre IA analyse automatiquement votre message pour suggérer la bonne priorité !",
                "Les niveaux de priorité :\n\n• 🔴 Urgente : Situations critiques, danger\n• 🟠 Moyenne : Problèmes importants\n• 🟢 Faible : Suggestions, questions\n\nL'IA détecte automatiquement les mots-clés d'urgence dans votre réclamation."
            ]
        ],
        
        // Catégories
        'categories' => [
            'keywords' => ['catégorie', 'type', 'domaine', 'secteur', 'accessibilité', 'discrimination', 'technique'],
            'responses' => [
                "Nos catégories de réclamations :\n\n♿ Accessibilité\n⚖️ Discrimination\n🔧 Technique\n💰 Facturation\n🚌 Transport\n🏥 Santé\n📚 Éducation\n💼 Emploi\n🏛️ Administration\n📦 Service/Produit\n\nChoisissez celle qui correspond le mieux à votre situation.",
                "Vous pouvez choisir parmi plusieurs catégories :\n\nAccessibilité, Discrimination, Technique, Service, Transport, Santé, Éducation, Emploi...\n\nCela nous aide à diriger votre réclamation vers le bon service ! 🎯"
            ]
        ],
        
        // Aide / Contact
        'aide' => [
            'keywords' => ['aide', 'help', 'assistance', 'contact', 'support', 'problème', 'bloqué', 'ne marche pas'],
            'responses' => [
                "Je suis là pour vous aider ! 🤝\n\nVoici ce que je peux faire :\n• Vous guider pour créer une réclamation\n• Expliquer le processus de suivi\n• Répondre à vos questions\n\nSi vous avez besoin d'une assistance humaine, un agent vous répondra sous 24h.",
                "Pas de panique, je suis là ! 💪\n\nDites-moi quel est votre problème :\n• Formulaire ?\n• Suivi ?\n• Autre question ?\n\nJe ferai de mon mieux pour vous aider !"
            ]
        ],
        
        // Délais
        'delais' => [
            'keywords' => ['combien de temps', 'délai', 'quand', 'durée', 'réponse', 'attendre'],
            'responses' => [
                "⏱️ Nos délais de traitement :\n\n• Réclamation urgente : 24h\n• Réclamation moyenne : 48h\n• Réclamation normale : 5 jours ouvrés\n\nVous recevrez une notification dès qu'un agent traite votre dossier.",
                "Le temps de traitement dépend de la priorité :\n\n🔴 Urgent : Réponse sous 24h\n🟠 Important : Réponse sous 48h\n🟢 Normal : Réponse sous 5 jours\n\nVous pouvez suivre l'avancement en temps réel !"
            ]
        ],
        
        // Remerciements
        'remerciements' => [
            'keywords' => ['merci', 'thanks', 'thank you', 'super', 'génial', 'parfait', 'excellent'],
            'responses' => [
                "Avec plaisir ! 😊 N'hésitez pas si vous avez d'autres questions. Bonne journée ! 🌟",
                "Je vous en prie ! 🙏 Je suis toujours là si vous avez besoin. À bientôt sur ImpactAble ! 👋",
                "Ravi d'avoir pu vous aider ! ✨ Votre satisfaction est notre priorité. À bientôt !"
            ]
        ],
        
        // Au revoir
        'aurevoir' => [
            'keywords' => ['au revoir', 'bye', 'goodbye', 'à bientôt', 'ciao', 'salut', 'bonne journée'],
            'responses' => [
                "Au revoir ! 👋 N'hésitez pas à revenir si vous avez des questions. Bonne journée ! ☀️",
                "À bientôt sur ImpactAble ! 🎯 Prenez soin de vous ! 💚",
                "Bye bye ! 👋 J'espère avoir pu vous aider. Revenez quand vous voulez ! 😊"
            ]
        ],
        
        // IA / Intelligence
        'ia' => [
            'keywords' => ['ia', 'intelligence', 'artificielle', 'automatique', 'robot', 'bot', 'machine'],
            'responses' => [
                "🧠 Notre système utilise l'Intelligence Artificielle pour :\n\n• Analyser automatiquement vos réclamations\n• Détecter le niveau d'urgence\n• Suggérer la bonne priorité\n• Accélérer le traitement\n\nVous pouvez tester notre IA sur la page 'Démo IA' !",
                "Oui, ImpactAble utilise l'IA ! 🤖\n\nNotre système analyse les mots-clés de votre réclamation pour déterminer automatiquement sa priorité.\n\nPlus de 100 mots-clés sont détectés pour une priorisation précise."
            ]
        ],
        
        // Accessibilité / Handicap
        'handicap' => [
            'keywords' => ['handicap', 'handicapé', 'pmr', 'fauteuil', 'aveugle', 'sourd', 'accessibilité', 'rampe'],
            'responses' => [
                "♿ ImpactAble est spécialement conçu pour les personnes en situation de handicap.\n\nVous pouvez signaler :\n• Problèmes d'accessibilité\n• Discrimination\n• Manque d'aménagements\n• Difficultés de transport\n\nVotre voix compte ! Nous sommes là pour vous aider. 💪",
                "Nous prenons très au sérieux les réclamations liées au handicap. 🤝\n\nN'hésitez pas à détailler votre situation dans le formulaire. Notre équipe est formée pour traiter ces cas en priorité.\n\n'Where Ability Meets Impact' - C'est notre mission ! 🎯"
            ]
        ]
    ];
    
    // Réponse par défaut si aucun mot-clé ne correspond
    private static $defaultResponses = [
        "Je ne suis pas sûr de comprendre. 🤔 Pouvez-vous reformuler votre question ?\n\nJe peux vous aider avec :\n• Créer une réclamation\n• Suivre une réclamation\n• Comprendre les priorités\n• Questions générales",
        "Hmm, je n'ai pas trouvé de réponse précise. 😅\n\nEssayez de me demander :\n• 'Comment faire une réclamation ?'\n• 'Comment suivre ma réclamation ?'\n• 'Quels sont les délais ?'",
        "Je suis encore en apprentissage ! 🤖\n\nPour une question spécifique, vous pouvez :\n1. Créer une réclamation\n2. Contacter le support\n\nOu reformulez votre question et je ferai de mon mieux !"
    ];
    
    /**
     * Traite un message utilisateur et retourne une réponse
     */
    public static function processMessage($message) {
        $message = mb_strtolower(trim($message), 'UTF-8');
        $message = self::normalizeText($message);
        
        $bestMatch = null;
        $bestScore = 0;
        
        foreach (self::$knowledgeBase as $category => $data) {
            $score = self::calculateMatchScore($message, $data['keywords']);
            
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $category;
            }
        }
        
        if ($bestMatch && $bestScore >= 0.3) {
            $responses = self::$knowledgeBase[$bestMatch]['responses'];
            $response = $responses[array_rand($responses)];
            
            return [
                'response' => $response,
                'category' => $bestMatch,
                'confidence' => round($bestScore * 100),
                'bot_name' => self::$botName,
                'bot_avatar' => self::$botAvatar
            ];
        }
        
        return [
            'response' => self::$defaultResponses[array_rand(self::$defaultResponses)],
            'category' => 'unknown',
            'confidence' => 0,
            'bot_name' => self::$botName,
            'bot_avatar' => self::$botAvatar
        ];
    }
    
    private static function calculateMatchScore($message, $keywords) {
        $score = 0;
        $maxScore = count($keywords);
        
        foreach ($keywords as $keyword) {
            $keyword = mb_strtolower($keyword, 'UTF-8');
            
            if (strpos($message, $keyword) !== false) {
                $score += 1;
            } else {
                $words = explode(' ', $message);
                foreach ($words as $word) {
                    if (strlen($word) > 3 && levenshtein($word, $keyword) <= 2) {
                        $score += 0.5;
                        break;
                    }
                }
            }
        }
        
        return $maxScore > 0 ? $score / $maxScore : 0;
    }
    
    private static function normalizeText($text) {
        $text = preg_replace('/[!?]{2,}/', ' ', $text);
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
    
    public static function getWelcomeMessage() {
        $messages = [
            "Bonjour ! 👋 Je suis **ImpactBot**, votre assistant virtuel.\n\nJe peux vous aider à :\n• Créer une réclamation\n• Suivre votre dossier\n• Répondre à vos questions\n\nComment puis-je vous aider ?",
        ];
        
        return [
            'response' => $messages[array_rand($messages)],
            'category' => 'welcome',
            'confidence' => 100,
            'bot_name' => self::$botName,
            'bot_avatar' => self::$botAvatar
        ];
    }
    
    public static function getSuggestions() {
        return [
            "Comment faire une réclamation ?",
            "Comment suivre ma réclamation ?",
            "Quels sont les délais ?",
            "C'est quoi la priorisation IA ?"
        ];
    }
}
?>
