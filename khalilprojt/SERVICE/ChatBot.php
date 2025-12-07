<?php
/**
 * Service ChatBot Intelligent Avancé pour ImpactAble
 * Version 3.0 - Intelligence améliorée
 */
class ChatBot {
    
    private static $botName = "Khalil";
    private static $botAvatar = "K";
    
    // Base de connaissances enrichie
    private static $knowledgeBase = [
        
        // ==================== SALUTATIONS ====================
        'salutations' => [
            'keywords' => ['bonjour', 'salut', 'hello', 'hi', 'bonsoir', 'hey', 'coucou', 'salam', 'bsr', 'bjr', 'cc', 'yo', 'hola'],
            'responses' => [
                "Bonjour ! 👋 Je suis **Khalil**, votre assistant virtuel.\n\nComment puis-je vous aider ?\n\n• 📝 Créer une réclamation\n• 🔍 Suivre un dossier\n• ❓ Poser une question",
                "Salut ! 😊 Bienvenue sur ImpactAble !\n\nJe suis là pour vous accompagner. Que souhaitez-vous faire ?",
                "Hello ! 👋 Ravi de vous voir !\n\nJe peux vous aider à déposer ou suivre une réclamation. Que puis-je faire pour vous ?"
            ]
        ],
        
        // ==================== CRÉER RÉCLAMATION ====================
        'faire_reclamation' => [
            'keywords' => ['faire', 'creer', 'créer', 'nouvelle', 'deposer', 'déposer', 'soumettre', 'envoyer', 'reclamer', 'réclamer', 'reclamation', 'réclamation', 'formulaire', 'remplir', 'plainte', 'signaler'],
            'responses' => [
                "📝 **Comment déposer une réclamation :**\n\n1️⃣ Remplissez le formulaire sur la page d'accueil\n2️⃣ Décrivez votre problème en détail\n3️⃣ Ajoutez des photos si nécessaire\n4️⃣ Cliquez sur 'Envoyer'\n\n⏱️ Réponse garantie sous 48h !\n\n💡 Plus votre description est détaillée, plus vite nous pourrons vous aider !",
                "Pour créer une réclamation :\n\n✅ Allez sur la page d'accueil\n✅ Remplissez vos informations\n✅ Décrivez le problème\n✅ Notre IA analyse automatiquement la priorité\n\nBesoin d'aide pour remplir le formulaire ?"
            ]
        ],
        
        // ==================== SUIVI ====================
        'suivi' => [
            'keywords' => ['suivi', 'suivre', 'tracker', 'statut', 'status', 'etat', 'état', 'avancement', 'numero', 'numéro', 'dossier', 'reference', 'référence', 'ou en est', 'où en est'],
            'responses' => [
                "🔍 **Suivre votre réclamation :**\n\n1️⃣ Cliquez sur '**Suivre ma Réclamation**'\n2️⃣ Entrez votre **numéro de dossier**\n3️⃣ Visualisez la timeline complète\n\n📊 **États possibles :**\n• 📥 Reçue\n• 🔄 En traitement\n• ✅ Résolue\n• 📁 Clôturée",
                "Pour suivre votre réclamation :\n\n➡️ Page 'Suivi' en haut du site\n➡️ Entrez votre numéro (ex: 1, 2, 3...)\n➡️ Consultez l'avancement en temps réel\n\nVous avez perdu votre numéro ? Vérifiez vos emails !"
            ]
        ],
        
        // ==================== PRIORITÉ ====================
        'priorite' => [
            'keywords' => ['priorite', 'priorité', 'urgent', 'urgence', 'importante', 'important', 'normal', 'delai', 'délai', 'temps', 'vite', 'rapidement'],
            'responses' => [
                "🎯 **Système de priorité :**\n\n🔴 **URGENTE** - Réponse sous 24h\n→ Danger, blocage, accident\n\n🟠 **MOYENNE** - Réponse sous 48h\n→ Problème important\n\n🟢 **FAIBLE** - Réponse sous 5 jours\n→ Suggestion, question\n\n🧠 Notre IA détecte automatiquement la priorité !",
                "Les niveaux de priorité :\n\n• 🔴 Urgent = 24h\n• 🟠 Important = 48h\n• 🟢 Normal = 5 jours\n\nL'IA analyse vos mots-clés pour suggérer la bonne priorité."
            ]
        ],
        
        // ==================== CATÉGORIES ====================
        'categories' => [
            'keywords' => ['categorie', 'catégorie', 'type', 'types', 'domaine', 'secteur', 'liste', 'quelles'],
            'responses' => [
                "📂 **Catégories disponibles :**\n\n♿ Accessibilité\n⚖️ Discrimination\n🔧 Technique\n💰 Facturation\n🚌 Transport\n🏥 Santé\n📚 Éducation\n💼 Emploi\n🏛️ Administration\n📦 Service/Produit\n\nChoisissez celle qui correspond à votre situation !"
            ]
        ],
        
        // ==================== AIDE ====================
        'aide' => [
            'keywords' => ['aide', 'aider', 'help', 'assistance', 'support', 'probleme', 'problème', 'bloque', 'bloqué', 'marche pas', 'bug', 'erreur', 'coincé', 'perdu', 'comprends pas'],
            'responses' => [
                "🆘 **Je suis là pour vous aider !**\n\n**Problèmes fréquents :**\n\n❓ Formulaire ne s'envoie pas ?\n→ Vérifiez les champs obligatoires\n\n❓ Numéro perdu ?\n→ Vérifiez vos emails\n\n❓ Pas de réponse ?\n→ Délai selon la priorité\n\n📧 Contact : support@impactable.tn",
                "Pas de panique ! 💪\n\nDites-moi votre problème :\n• Formulaire ?\n• Suivi ?\n• Autre ?\n\nJe ferai de mon mieux pour vous aider !"
            ]
        ],
        
        // ==================== DÉLAIS ====================
        'delais' => [
            'keywords' => ['combien', 'temps', 'duree', 'durée', 'reponse', 'réponse', 'attendre', 'jours', 'heures', 'quand'],
            'responses' => [
                "⏱️ **Délais de traitement :**\n\n| Priorité | Délai |\n|----------|-------|\n| 🔴 Urgente | 24h |\n| 🟠 Moyenne | 48h |\n| 🟢 Faible | 5 jours |\n\n📧 Notification à chaque étape !",
                "Quand aurez-vous une réponse ?\n\n⚡ Urgent : 24h\n📋 Important : 48h\n📝 Normal : 5 jours ouvrés\n\nVous pouvez suivre l'avancement en temps réel !"
            ]
        ],
        
        // ==================== MERCI ====================
        'remerciements' => [
            'keywords' => ['merci', 'thanks', 'thank', 'super', 'genial', 'génial', 'parfait', 'excellent', 'top', 'cool', 'nickel', 'bravo', 'bien', 'ok', 'okay', 'd\'accord', 'daccord'],
            'responses' => [
                "Avec plaisir ! 😊✨\n\nN'hésitez pas si vous avez d'autres questions.\n\nBonne journée ! 🌟",
                "Je vous en prie ! 🙏\n\nC'est un plaisir de vous aider.\n\nÀ bientôt sur ImpactAble ! 👋",
                "Ravi d'avoir pu vous aider ! 🎉\n\nVotre satisfaction est notre priorité !"
            ]
        ],
        
        // ==================== AU REVOIR ====================
        'aurevoir' => [
            'keywords' => ['au revoir', 'aurevoir', 'bye', 'goodbye', 'a bientot', 'à bientôt', 'ciao', 'a plus', 'à plus', 'bonne journee', 'bonne journée', 'bonne soiree', 'bonne soirée'],
            'responses' => [
                "Au revoir ! 👋✨\n\nPrenez soin de vous !\n\nÀ bientôt sur ImpactAble ! 💚",
                "Bye bye ! 👋😊\n\nN'hésitez pas à revenir si besoin.\n\nBonne continuation ! 🌟"
            ]
        ],
        
        // ==================== IA ====================
        'ia' => [
            'keywords' => ['ia', 'intelligence', 'artificielle', 'automatique', 'robot', 'bot', 'machine', 'algorithme', 'comment ca marche', 'comment ça marche', 'fonctionnement'],
            'responses' => [
                "🧠 **Notre Intelligence Artificielle :**\n\n**Comment ça marche ?**\n\n1️⃣ Analyse de votre texte\n2️⃣ Détection de mots-clés\n3️⃣ Calcul du score d'urgence\n4️⃣ Suggestion de priorité\n\n🎯 Testez sur la page **'Démo IA'** !",
                "Je suis **Khalil**, votre assistant IA ! 🤖\n\n**Mes capacités :**\n• Comprendre vos questions\n• Analyser les priorités\n• Vous guider pas à pas\n• Répondre 24h/24\n\nJe fais de mon mieux pour vous aider ! 💪"
            ]
        ],
        
        // ==================== ACCESSIBILITÉ ====================
        'handicap' => [
            'keywords' => ['handicap', 'handicapé', 'handicape', 'pmr', 'fauteuil', 'roulant', 'aveugle', 'sourd', 'accessibilite', 'accessibilité', 'rampe', 'mobilite', 'mobilité'],
            'responses' => [
                "♿ **ImpactAble pour l'accessibilité**\n\n**Vous pouvez signaler :**\n• Accès aux bâtiments\n• Transports non adaptés\n• Discrimination\n• Problèmes de santé\n\n**Chaque signalement compte !**\nEnsemble, construisons un monde plus inclusif. 💚"
            ]
        ],
        
        // ==================== CONTACT ====================
        'contact' => [
            'keywords' => ['contact', 'contacter', 'email', 'mail', 'telephone', 'téléphone', 'appeler', 'ecrire', 'écrire', 'joindre', 'humain', 'agent', 'personne'],
            'responses' => [
                "📞 **Nous contacter :**\n\n📧 Email : support@impactable.tn\n⏱️ Réponse : 24-48h\n\n💬 En attendant, je suis là 24h/24 pour répondre à vos questions basiques !"
            ]
        ],
        
        // ==================== QUI ====================
        'qui' => [
            'keywords' => ['qui es tu', 'qui es-tu', 'tu es qui', 'c\'est quoi', 'c est quoi', 'impactable', 'a propos', 'à propos', 'presentation', 'présentation'],
            'responses' => [
                "🌟 **ImpactAble**\n\n**Notre mission :**\nAméliorer l'accessibilité pour tous !\n\n**Ce que nous faisons :**\n• Plateforme de réclamations\n• Système IA de priorisation\n• Suivi transparent\n\n*'Where Ability Meets Impact'* 💚"
            ]
        ],
        
        // ==================== PHOTO ====================
        'photo' => [
            'keywords' => ['photo', 'image', 'piece jointe', 'pièce jointe', 'fichier', 'document', 'preuve', 'joindre', 'telecharger', 'télécharger', 'upload'],
            'responses' => [
                "📸 **Ajouter des photos :**\n\n1️⃣ Cliquez sur '📎 Joindre un fichier'\n2️⃣ Sélectionnez votre image\n3️⃣ Formats : JPG, PNG, GIF\n4️⃣ Taille max : 5 Mo\n\n💡 Les photos accélèrent le traitement !"
            ]
        ],
        
        // ==================== OUI / NON ====================
        'oui' => [
            'keywords' => ['oui', 'yes', 'ouais', 'absolument', 'exactement', 'tout a fait', 'tout à fait', 'bien sur', 'bien sûr', 'evidemment', 'évidemment'],
            'responses' => [
                "👍 Parfait ! Comment puis-je vous aider davantage ?",
                "✅ D'accord ! Que souhaitez-vous savoir d'autre ?",
                "Super ! 😊 Y a-t-il autre chose que je puisse faire pour vous ?"
            ]
        ],
        
        'non' => [
            'keywords' => ['non', 'no', 'nan', 'nope', 'pas vraiment', 'pas du tout'],
            'responses' => [
                "🤔 D'accord ! N'hésitez pas si vous avez des questions plus tard.",
                "Pas de souci ! Je reste disponible si besoin. 😊",
                "OK ! Revenez quand vous voulez. Bonne journée ! 👋"
            ]
        ],
        
        // ==================== TEST ====================
        'test' => [
            'keywords' => ['test', 'tester', 'essai', 'essayer', 'demo', 'démo', 'demonstration', 'démonstration'],
            'responses' => [
                "✅ Test réussi ! Je fonctionne correctement. 🤖\n\n🧠 Pour tester l'IA de priorisation, allez sur la page **'Démo IA'** !",
                "🎯 Le chatbot fonctionne ! Comment puis-je vous aider ?"
            ]
        ]
    ];
    
    // Réponses par défaut
    private static $defaultResponses = [
        "🤔 Je n'ai pas bien compris.\n\n**Essayez de me demander :**\n• Comment faire une réclamation ?\n• Comment suivre mon dossier ?\n• Quels sont les délais ?\n\nOu reformulez votre question ! 😊",
        "Hmm, je ne suis pas sûr de comprendre. 😅\n\n**Je peux vous aider avec :**\n• Réclamations\n• Suivi de dossier\n• Questions sur l'accessibilité\n\nPouvez-vous reformuler ?",
        "Je n'ai pas trouvé de réponse précise. 🤖\n\nEssayez des mots simples comme :\n• 'réclamation'\n• 'suivi'\n• 'aide'\n• 'priorité'"
    ];
    
    /**
     * Traite un message utilisateur et retourne une réponse
     */
    public static function processMessage($message) {
        $originalMessage = $message;
        
        // Normalisation du message
        $message = self::normalizeText($message);
        
        // Recherche de la meilleure correspondance
        $bestMatch = null;
        $bestScore = 0;
        
        foreach (self::$knowledgeBase as $category => $data) {
            $score = self::calculateMatchScore($message, $data['keywords']);
            
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $category;
            }
        }
        
        // Si on a trouvé une correspondance suffisante
        if ($bestMatch && $bestScore > 0) {
            $responses = self::$knowledgeBase[$bestMatch]['responses'];
            $response = $responses[array_rand($responses)];
            
            return [
                'response' => $response,
                'category' => $bestMatch,
                'confidence' => min(100, round($bestScore * 100)),
                'bot_name' => self::$botName,
                'bot_avatar' => self::$botAvatar
            ];
        }
        
        // Réponse par défaut
        return [
            'response' => self::$defaultResponses[array_rand(self::$defaultResponses)],
            'category' => 'unknown',
            'confidence' => 0,
            'bot_name' => self::$botName,
            'bot_avatar' => self::$botAvatar
        ];
    }
    
    /**
     * Calcule le score de correspondance - ALGORITHME AMÉLIORÉ
     */
    private static function calculateMatchScore($message, $keywords) {
        $score = 0;
        $messageWords = explode(' ', $message);
        
        foreach ($keywords as $keyword) {
            $keyword = self::normalizeText($keyword);
            
            // 1. Correspondance exacte du mot-clé dans le message
            if (strpos($message, $keyword) !== false) {
                $score += 1;
                continue;
            }
            
            // 2. Correspondance mot par mot
            foreach ($messageWords as $word) {
                if (strlen($word) < 2) continue;
                
                // Correspondance exacte du mot
                if ($word === $keyword) {
                    $score += 1;
                    break;
                }
                
                // Le mot commence par le keyword ou inverse
                if (strpos($word, $keyword) === 0 || strpos($keyword, $word) === 0) {
                    $score += 0.8;
                    break;
                }
                
                // Distance de Levenshtein pour tolérance aux fautes
                if (strlen($word) >= 3 && strlen($keyword) >= 3) {
                    $distance = levenshtein($word, $keyword);
                    $maxLen = max(strlen($word), strlen($keyword));
                    
                    if ($distance <= 1) {
                        $score += 0.9;
                        break;
                    } elseif ($distance <= 2 && $maxLen >= 5) {
                        $score += 0.6;
                        break;
                    }
                }
            }
        }
        
        return $score;
    }
    
    /**
     * Normalise le texte pour une meilleure détection
     */
    private static function normalizeText($text) {
        // Convertir en minuscules
        $text = mb_strtolower($text, 'UTF-8');
        
        // Remplacer les accents
        $accents = [
            'à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a',
            'è' => 'e', 'ê' => 'e', 'ë' => 'e', 'é' => 'e',
            'ì' => 'i', 'î' => 'i', 'ï' => 'i', 'í' => 'i',
            'ò' => 'o', 'ô' => 'o', 'ö' => 'o', 'ó' => 'o',
            'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ú' => 'u',
            'ç' => 'c', 'ñ' => 'n'
        ];
        $text = strtr($text, $accents);
        
        // Supprimer la ponctuation
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        
        // Supprimer les espaces multiples
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
    }
    
    /**
     * Message de bienvenue
     */
    public static function getWelcomeMessage() {
        $hour = (int)date('H');
        
        if ($hour >= 5 && $hour < 12) {
            $greeting = "Bonjour";
        } elseif ($hour >= 12 && $hour < 18) {
            $greeting = "Bon après-midi";
        } else {
            $greeting = "Bonsoir";
        }
        
        $message = "$greeting ! 👋 Je suis **Khalil**, votre assistant virtuel.\n\n";
        $message .= "🎯 Je peux vous aider à :\n";
        $message .= "• Créer une réclamation\n";
        $message .= "• Suivre votre dossier\n";
        $message .= "• Répondre à vos questions\n\n";
        $message .= "Comment puis-je vous aider ?";
        
        return [
            'response' => $message,
            'category' => 'welcome',
            'confidence' => 100,
            'bot_name' => self::$botName,
            'bot_avatar' => self::$botAvatar
        ];
    }
    
    /**
     * Suggestions
     */
    public static function getSuggestions() {
        return [
            "📝 Nouvelle réclamation",
            "🔍 Suivre mon dossier",
            "⏱️ Délais de traitement",
            "🧠 Comment fonctionne l'IA ?"
        ];
    }
}
?>
