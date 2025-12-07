<?php
/**
 * Service ChatBot Intelligent Avancé pour ImpactAble
 * Version améliorée avec plus de compétences et d'intelligence
 */
class ChatBot {
    
    private static $botName = "ImpactBot";
    private static $botAvatar = "🤖";
    
    // Base de connaissances enrichie du chatbot
    private static $knowledgeBase = [
        
        // ==================== SALUTATIONS ====================
        'salutations' => [
            'keywords' => ['bonjour', 'salut', 'hello', 'hi', 'bonsoir', 'hey', 'coucou', 'salam', 'bsr', 'bjr', 'cc'],
            'responses' => [
                "Bonjour ! 👋 Je suis **ImpactBot**, votre assistant virtuel dédié à l'accessibilité.\n\nComment puis-je vous aider aujourd'hui ?\n\n• 📝 Créer une réclamation\n• 🔍 Suivre un dossier\n• ❓ Poser une question",
                "Salut ! 😊 Bienvenue sur ImpactAble !\n\nJe suis là pour vous accompagner dans toutes vos démarches. Que souhaitez-vous faire ?",
                "Bienvenue ! 🌟 Je suis ImpactBot, votre assistant personnel.\n\n**'Where Ability Meets Impact'** - Ensemble, faisons la différence !",
                "Hello ! 👋 Ravi de vous voir sur ImpactAble !\n\nJe peux vous aider à :\n• Déposer une réclamation\n• Suivre votre dossier\n• Répondre à vos questions\n\nQue puis-je faire pour vous ?"
            ]
        ],
        
        // ==================== CRÉER UNE RÉCLAMATION ====================
        'faire_reclamation' => [
            'keywords' => ['faire réclamation', 'créer réclamation', 'nouvelle réclamation', 'déposer réclamation', 'soumettre', 'envoyer réclamation', 'comment réclamer', 'porter plainte', 'signaler', 'déclarer', 'formulaire', 'remplir'],
            'responses' => [
                "📝 **Comment déposer une réclamation :**\n\n**Étape 1** : Sur la page d'accueil, remplissez le formulaire\n**Étape 2** : Décrivez précisément votre situation\n**Étape 3** : Ajoutez des photos si nécessaire\n**Étape 4** : Choisissez la catégorie appropriée\n**Étape 5** : Cliquez sur 'Envoyer'\n\n⏱️ **Délai** : Réponse sous 48h garantie !\n\n💡 **Astuce** : Plus votre description est détaillée, plus vite nous pourrons vous aider !",
                "Pour créer une réclamation, c'est simple ! 🎯\n\n1️⃣ Remplissez vos **informations personnelles**\n2️⃣ Décrivez le **problème rencontré**\n3️⃣ Indiquez **lieu et date** de l'incident\n4️⃣ Notre **IA analyse automatiquement** la priorité\n5️⃣ Vous recevez un **numéro de suivi**\n\n📞 Besoin d'aide pour remplir ? Demandez-moi !",
                "Je vous guide pas à pas ! 📋\n\n**Informations requises :**\n• Nom et prénom\n• Email et téléphone\n• Description détaillée\n• Catégorie du problème\n• Lieu et date\n\n**Bonus** : Vous pouvez joindre des photos comme preuve !\n\nVoulez-vous que je vous explique une section en particulier ?"
            ]
        ],
        
        // ==================== SUIVI DE RÉCLAMATION ====================
        'suivi' => [
            'keywords' => ['suivi', 'suivre', 'où en est', 'statut', 'état', 'avancement', 'tracker', 'numéro', 'dossier', 'ma réclamation', 'mon dossier', 'référence'],
            'responses' => [
                "🔍 **Suivre votre réclamation :**\n\n1️⃣ Cliquez sur '**Suivre ma Réclamation**' en haut de page\n2️⃣ Entrez votre **numéro de dossier** (ex: 1, 2, 3...)\n3️⃣ Visualisez la **timeline** complète\n\n📊 **Les 4 étapes :**\n• 📥 Reçue\n• 🔄 En traitement\n• ✅ Résolue\n• 📁 Clôturée\n\nVous recevrez aussi des notifications par email !",
                "Pour suivre votre réclamation en temps réel ! 📡\n\n**Option 1** : Page 'Suivre ma Réclamation'\n**Option 2** : Entrez directement votre numéro\n\n💡 Chaque changement de statut vous est notifié automatiquement.\n\nVous avez perdu votre numéro ? Contactez-nous avec votre email !",
                "Le suivi est disponible 24h/24 ! ⏰\n\n🎯 Rendez-vous sur la page de suivi et entrez votre identifiant.\n\nVous verrez :\n• L'historique complet\n• Les réponses de l'équipe\n• L'analyse IA de priorité\n• La date estimée de résolution"
            ]
        ],
        
        // ==================== PRIORITÉS ====================
        'priorite' => [
            'keywords' => ['priorité', 'urgent', 'urgence', 'importante', 'normal', 'délai', 'temps', 'vite', 'rapidement', 'attendre', 'quand'],
            'responses' => [
                "🎯 **Notre système de priorité intelligent :**\n\n🔴 **URGENTE** (Traitement immédiat)\n→ Danger, blocage, accident, panne critique\n→ Réponse sous **24 heures**\n\n🟠 **MOYENNE** (Traitement prioritaire)\n→ Problème important, accessibilité\n→ Réponse sous **48 heures**\n\n🟢 **FAIBLE** (Traitement standard)\n→ Suggestion, amélioration, question\n→ Réponse sous **5 jours**\n\n🧠 Notre **IA analyse automatiquement** votre message pour suggérer la bonne priorité !",
                "Comment fonctionne la priorisation ? 🤔\n\n**L'IA détecte les mots-clés :**\n\n• \"urgent\", \"bloqué\", \"danger\" → 🔴 Urgente\n• \"problème\", \"difficulté\", \"aide\" → 🟠 Moyenne\n• \"suggestion\", \"question\" → 🟢 Faible\n\n**Catégories sensibles** (priorité augmentée) :\n• Accessibilité\n• Discrimination\n• Santé/Sécurité\n\nVous pouvez toujours modifier la priorité manuellement !"
            ]
        ],
        
        // ==================== CATÉGORIES ====================
        'categories' => [
            'keywords' => ['catégorie', 'type', 'domaine', 'secteur', 'quel type', 'quelles catégories', 'liste'],
            'responses' => [
                "📂 **Nos catégories de réclamations :**\n\n♿ **Accessibilité** - Rampes, ascenseurs, signalétique\n⚖️ **Discrimination** - Traitement inégal, refus de service\n🔧 **Technique** - Équipements défaillants\n💰 **Facturation** - Erreurs de paiement\n🚌 **Transport** - Bus, métro, gares non accessibles\n🏥 **Santé** - Accès aux soins, hôpitaux\n📚 **Éducation** - Écoles, universités\n💼 **Emploi** - Discrimination à l'embauche\n🏛️ **Administration** - Services publics\n📦 **Service/Produit** - Commerce, services\n🏢 **Logement** - Habitat, immeubles\n🎭 **Loisirs** - Culture, sport, événements\n\nChoisissez celle qui correspond le mieux !"
            ]
        ],
        
        // ==================== AIDE ET SUPPORT ====================
        'aide' => [
            'keywords' => ['aide', 'help', 'assistance', 'contact', 'support', 'problème', 'bloqué', 'ne marche pas', 'bug', 'erreur', 'coincé', 'perdu', 'comprends pas'],
            'responses' => [
                "🆘 **Je suis là pour vous aider !**\n\n**Problèmes fréquents :**\n\n❓ *Formulaire ne s'envoie pas ?*\n→ Vérifiez que tous les champs obligatoires sont remplis\n\n❓ *Numéro de suivi perdu ?*\n→ Vérifiez votre email ou contactez-nous\n\n❓ *Pas de réponse ?*\n→ Les délais varient selon la priorité\n\n❓ *Autre problème ?*\n→ Décrivez-le moi, je ferai mon maximum !\n\n📧 Contact direct : support@impactable.tn",
                "Pas de panique, on va résoudre ça ensemble ! 💪\n\n**Dites-moi quel est le problème :**\n\n1. 📝 Problème avec le formulaire ?\n2. 🔍 Problème de suivi ?\n3. 💻 Bug technique ?\n4. ❓ Question générale ?\n\nJe suis programmé pour vous aider 24h/24 !",
                "Je comprends votre frustration. 🤝\n\n**Voici ce que je peux faire :**\n\n• Vous guider étape par étape\n• Expliquer le fonctionnement\n• Répondre à vos questions\n\nSi je ne peux pas résoudre votre problème, un agent humain vous contactera sous 24h.\n\nQu'est-ce qui vous bloque exactement ?"
            ]
        ],
        
        // ==================== DÉLAIS ====================
        'delais' => [
            'keywords' => ['combien de temps', 'délai', 'durée', 'réponse', 'attendre', 'jours', 'heures', 'quand réponse', 'temps traitement'],
            'responses' => [
                "⏱️ **Nos délais de traitement garantis :**\n\n| Priorité | Délai max |\n|----------|----------|\n| 🔴 Urgente | 24 heures |\n| 🟠 Moyenne | 48 heures |\n| 🟢 Faible | 5 jours ouvrés |\n\n**Ce qui accélère le traitement :**\n✅ Description détaillée\n✅ Photos/preuves jointes\n✅ Coordonnées correctes\n✅ Catégorie bien choisie\n\n📧 Vous êtes notifié à chaque étape !",
                "**Quand aurez-vous une réponse ?** 🤔\n\nCela dépend de la priorité :\n\n⚡ **Urgent** : Nous traitons dans les 24h\n📋 **Important** : Maximum 48h\n📝 **Standard** : 5 jours ouvrés\n\n**Bon à savoir :**\n• Les weekends ne comptent pas dans les délais\n• Les cas complexes peuvent prendre plus de temps\n• Vous pouvez relancer via le suivi"
            ]
        ],
        
        // ==================== REMERCIEMENTS ====================
        'remerciements' => [
            'keywords' => ['merci', 'thanks', 'thank you', 'super', 'génial', 'parfait', 'excellent', 'top', 'cool', 'nickel', 'bravo', 'bien'],
            'responses' => [
                "Avec grand plaisir ! 😊✨\n\n**Votre satisfaction est notre mission !**\n\nN'hésitez pas à revenir si vous avez d'autres questions.\n\n🌟 *'Where Ability Meets Impact'*\n\nBonne journée ! 🌈",
                "Je vous en prie ! 🙏💚\n\nC'est un plaisir de vous aider.\n\n**Petit rappel :**\n• Suivez votre réclamation régulièrement\n• Répondez aux questions de l'équipe\n• N'hésitez pas à ajouter des infos\n\nÀ bientôt sur ImpactAble ! 👋",
                "Ravi d'avoir pu vous aider ! 🎉\n\n**Ensemble, on fait avancer l'accessibilité !**\n\nVotre avis compte : si vous avez des suggestions pour améliorer notre service, n'hésitez pas !\n\nMerci de votre confiance ! 💪"
            ]
        ],
        
        // ==================== AU REVOIR ====================
        'aurevoir' => [
            'keywords' => ['au revoir', 'bye', 'goodbye', 'à bientôt', 'ciao', 'à plus', 'bonne journée', 'bonne soirée', 'tchao', 'a+'],
            'responses' => [
                "Au revoir et à très bientôt ! 👋✨\n\n**Rappel important :**\n📌 Conservez votre numéro de réclamation\n📌 Vérifiez vos emails\n📌 Je suis disponible 24h/24\n\nPrenez soin de vous ! 💚",
                "À bientôt sur ImpactAble ! 🌟\n\n*'Where Ability Meets Impact'*\n\nVotre voix compte, continuez à nous faire confiance !\n\nBelle journée/soirée ! ☀️🌙",
                "Bye bye ! 👋😊\n\nN'oubliez pas :\n• Votre réclamation est entre de bonnes mains\n• Vous pouvez revenir à tout moment\n• L'équipe travaille pour vous\n\nÀ la prochaine ! 🚀"
            ]
        ],
        
        // ==================== IA / TECHNOLOGIE ====================
        'ia' => [
            'keywords' => ['ia', 'intelligence', 'artificielle', 'automatique', 'robot', 'bot', 'machine', 'algorithme', 'comment ça marche', 'technologie'],
            'responses' => [
                "🧠 **Notre Intelligence Artificielle expliquée :**\n\n**Comment ça marche ?**\n\n1️⃣ **Analyse sémantique** : L'IA lit votre texte\n2️⃣ **Détection de mots-clés** : 150+ mots analysés\n3️⃣ **Scoring** : Points attribués selon la gravité\n4️⃣ **Priorisation** : Suggestion automatique\n\n**Mots détectés automatiquement :**\n• 🔴 \"urgent\", \"bloqué\", \"danger\", \"accident\"\n• 🟠 \"problème\", \"aide\", \"difficulté\"\n• 🟢 \"suggestion\", \"amélioration\"\n\n🎯 Testez sur la page **'Démo IA'** !",
                "**Je suis ImpactBot, votre assistant IA !** 🤖\n\n**Mes capacités :**\n• Comprendre vos questions en langage naturel\n• Analyser la priorité des réclamations\n• Guider pas à pas\n• Répondre 24h/24\n\n**Ce que je ne peux PAS faire :**\n• Traiter directement votre dossier\n• Accéder à vos données personnelles\n• Remplacer un agent humain\n\nMais je fais de mon mieux pour vous aider ! 💪"
            ]
        ],
        
        // ==================== ACCESSIBILITÉ / HANDICAP ====================
        'handicap' => [
            'keywords' => ['handicap', 'handicapé', 'pmr', 'fauteuil', 'aveugle', 'sourd', 'accessibilité', 'rampe', 'mobilité', 'malvoyant', 'malentendant', 'roulant', 'béquilles', 'prothèse'],
            'responses' => [
                "♿ **ImpactAble : Votre allié pour l'accessibilité**\n\n**Notre mission :**\nFaire entendre votre voix et améliorer l'accessibilité partout !\n\n**Types de problèmes que vous pouvez signaler :**\n\n🚪 Accès aux bâtiments (rampes, portes, ascenseurs)\n🚌 Transports non adaptés\n🏥 Accès aux soins de santé\n📚 Éducation et formation\n💼 Discrimination à l'emploi\n🛒 Commerces non accessibles\n🎭 Loisirs et culture\n\n**Chaque signalement compte !**\nEnsemble, construisons un monde plus inclusif. 💚",
                "**Nous comprenons vos défis quotidiens.** 🤝\n\n**ImpactAble vous permet de :**\n\n✅ Signaler les obstacles d'accessibilité\n✅ Dénoncer les discriminations\n✅ Suivre le traitement de votre dossier\n✅ Obtenir des réponses concrètes\n\n**Types de handicaps pris en compte :**\n• Moteur\n• Visuel\n• Auditif\n• Cognitif\n• Invisible\n\n*'Where Ability Meets Impact'* - Votre capacité est votre force ! 💪"
            ]
        ],
        
        // ==================== PHOTOS / PIÈCES JOINTES ====================
        'photos' => [
            'keywords' => ['photo', 'image', 'pièce jointe', 'fichier', 'document', 'preuve', 'joindre', 'ajouter image', 'capture', 'screenshot'],
            'responses' => [
                "📸 **Comment ajouter des photos ?**\n\n**Dans le formulaire :**\n1️⃣ Cliquez sur le bouton '📎 Joindre un fichier'\n2️⃣ Sélectionnez votre image\n3️⃣ Formats acceptés : JPG, PNG, GIF\n4️⃣ Taille max : 5 Mo\n\n**Conseils pour de bonnes photos :**\n✅ Bonne luminosité\n✅ Problème visible clairement\n✅ Plusieurs angles si possible\n✅ Date et lieu identifiables\n\n📌 Les photos accélèrent le traitement de votre réclamation !"
            ]
        ],
        
        // ==================== CONTACT / EMAIL ====================
        'contact_email' => [
            'keywords' => ['email', 'mail', 'adresse', 'contacter', 'téléphone', 'appeler', 'numéro', 'joindre équipe', 'parler humain', 'agent'],
            'responses' => [
                "📞 **Nous contacter :**\n\n**Email :** support@impactable.tn\n**Délai de réponse :** 24-48h\n\n**Quand contacter directement ?**\n• Problème technique urgent\n• Question complexe\n• Réclamation sensible\n• Besoin d'un suivi personnalisé\n\n💬 En attendant, je suis là pour répondre à vos questions basiques 24h/24 !\n\n*Préférez le formulaire de réclamation pour un suivi optimal.*"
            ]
        ],
        
        // ==================== DROITS ====================
        'droits' => [
            'keywords' => ['droit', 'loi', 'légal', 'juridique', 'recours', 'obligation', 'règlement', 'législation'],
            'responses' => [
                "⚖️ **Vos droits en matière d'accessibilité :**\n\n**En Tunisie :**\n• Loi n°2005-83 sur la protection des personnes handicapées\n• Obligation d'accessibilité des bâtiments publics\n• Non-discrimination à l'emploi\n\n**Ce que vous pouvez faire :**\n1. Signaler via ImpactAble\n2. Déposer une plainte officielle\n3. Contacter les associations\n4. Saisir les autorités compétentes\n\n📌 **Votre réclamation peut servir de preuve !**\n\n*ImpactAble travaille avec les autorités pour faire respecter vos droits.*"
            ]
        ],
        
        // ==================== QUI SOMMES-NOUS ====================
        'qui_sommes_nous' => [
            'keywords' => ['qui êtes', 'c\'est quoi', 'impactable', 'plateforme', 'site', 'entreprise', 'association', 'organisation', 'à propos'],
            'responses' => [
                "🌟 **ImpactAble - Qui sommes-nous ?**\n\n**Notre mission :**\nAméliorer l'accessibilité et l'inclusion pour tous !\n\n**Ce que nous faisons :**\n• Plateforme de réclamations accessible\n• Système intelligent de priorisation\n• Suivi transparent des dossiers\n• Mise en relation avec les responsables\n\n**Notre slogan :**\n*'Where Ability Meets Impact'*\n\n**Notre vision :**\nUn monde où chaque personne, quelle que soit sa situation, peut vivre dignement et pleinement.\n\n💚 Merci de nous faire confiance !"
            ]
        ],
        
        // ==================== STATISTIQUES ====================
        'statistiques' => [
            'keywords' => ['statistique', 'chiffre', 'combien', 'nombre', 'total', 'rapport', 'bilan'],
            'responses' => [
                "📊 **Statistiques ImpactAble :**\n\n**Notre impact :**\n• Réclamations traitées chaque mois\n• Taux de résolution élevé\n• Temps de réponse optimisé grâce à l'IA\n\n**Dashboard Admin :**\nLes administrateurs ont accès à :\n• Graphiques en temps réel\n• Répartition par catégorie\n• Évolution des réclamations\n• Performance de l'équipe\n\n📈 Votre réclamation contribue à améliorer nos statistiques et nos services !"
            ]
        ],
        
        // ==================== LANGUES ====================
        'langues' => [
            'keywords' => ['français', 'arabe', 'anglais', 'langue', 'traduction', 'العربية', 'english'],
            'responses' => [
                "🌍 **Langues disponibles :**\n\n🇫🇷 **Français** - Actuellement\n🇹🇳 **Arabe** - Bientôt disponible\n🇬🇧 **Anglais** - Bientôt disponible\n\n**En attendant :**\n• Vous pouvez écrire en dialecte tunisien\n• Je comprends les messages mixtes\n• Les agents sont multilingues\n\nMerci de votre patience ! 🙏"
            ]
        ],
        
        // ==================== SÉCURITÉ / CONFIDENTIALITÉ ====================
        'securite' => [
            'keywords' => ['sécurité', 'confidentialité', 'données', 'privé', 'protection', 'rgpd', 'personnel'],
            'responses' => [
                "🔒 **Sécurité et Confidentialité :**\n\n**Vos données sont protégées !**\n\n✅ Connexion sécurisée (HTTPS)\n✅ Données chiffrées\n✅ Accès restreint aux agents autorisés\n✅ Pas de partage avec des tiers\n\n**Vos droits :**\n• Accès à vos données\n• Modification\n• Suppression sur demande\n\n**Note :** Je suis un chatbot, je n'ai pas accès à vos données personnelles ni à vos réclamations.\n\n🛡️ Votre confiance est notre priorité !"
            ]
        ],
        
        // ==================== HUMOUR / CONVERSATION ====================
        'humour' => [
            'keywords' => ['blague', 'drôle', 'rire', 'humour', 'joke', 'amusant', 'ennui', 'tu fais quoi'],
            'responses' => [
                "😄 Ha ha ! Vous voulez rire un peu ?\n\n**Petite blague :**\n*Pourquoi les robots ne prennent jamais de vacances ?*\n*Parce qu'ils ont peur de perdre leurs données de congés !* 🤖😂\n\n...Bon, je retourne à mes vraies compétences : vous aider avec vos réclamations ! 📝\n\nUne question sérieuse peut-être ? 😊",
                "Je suis programmé pour être utile, pas drôle... mais je vais essayer ! 😅\n\n*Un utilisateur demande à un chatbot : 'Tu es intelligent ?'*\n*Le chatbot répond : 'Je suis artificiel, l'intelligence est en option !'* 🤖\n\n...Allez, revenons aux choses sérieuses ! Comment puis-je vous aider ? 🎯"
            ]
        ],
        
        // ==================== ERREURS COMMUNES ====================
        'erreur_formulaire' => [
            'keywords' => ['erreur formulaire', 'ne s\'envoie pas', 'bouton marche pas', 'champs obligatoires', 'validation', 'impossible envoyer'],
            'responses' => [
                "🔧 **Problème avec le formulaire ?**\n\n**Vérifications à faire :**\n\n1️⃣ **Champs obligatoires** (*) tous remplis ?\n2️⃣ **Email** au bon format ? (exemple@mail.com)\n3️⃣ **Téléphone** valide ? (8 chiffres)\n4️⃣ **Description** assez détaillée ? (min. 20 caractères)\n5️⃣ **Image** pas trop grande ? (max 5 Mo)\n\n**Toujours bloqué ?**\n• Rafraîchissez la page (F5)\n• Essayez un autre navigateur\n• Videz le cache\n\n📧 Si le problème persiste : support@impactable.tn"
            ]
        ]
    ];
    
    // Réponses par défaut améliorées
    private static $defaultResponses = [
        "🤔 Hmm, je n'ai pas trouvé de réponse précise à votre question.\n\n**Mais je peux vous aider avec :**\n• 📝 'Comment faire une réclamation ?'\n• 🔍 'Comment suivre mon dossier ?'\n• ⏱️ 'Quels sont les délais ?'\n• 🎯 'C'est quoi la priorisation IA ?'\n\nReformulons ensemble ! 😊",
        "Je suis encore en apprentissage ! 🤖📚\n\n**Questions populaires :**\n• 'Aide-moi à créer une réclamation'\n• 'Où en est mon dossier ?'\n• 'Comment fonctionne l'IA ?'\n• 'Qui est ImpactAble ?'\n\nOu décrivez votre problème autrement, je ferai de mon mieux ! 💪",
        "Je n'ai pas compris cette demande. 😅\n\n**Essayez de me demander :**\n• Des informations sur les réclamations\n• Comment suivre un dossier\n• Les délais de traitement\n• L'accessibilité\n\n💡 **Astuce** : Posez une question simple et directe !\n\nJe suis là pour vous aider ! 🌟"
    ];
    
    /**
     * Traite un message utilisateur et retourne une réponse
     */
    public static function processMessage($message) {
        $originalMessage = $message;
        $message = mb_strtolower(trim($message), 'UTF-8');
        $message = self::normalizeText($message);
        
        // Vérifier les expressions exactes en premier
        $exactMatch = self::checkExactExpressions($message);
        if ($exactMatch) {
            return $exactMatch;
        }
        
        $bestMatch = null;
        $bestScore = 0;
        
        foreach (self::$knowledgeBase as $category => $data) {
            $score = self::calculateMatchScore($message, $data['keywords']);
            
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $category;
            }
        }
        
        // Seuil de confiance abaissé pour plus de tolérance
        if ($bestMatch && $bestScore >= 0.2) {
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
    
    /**
     * Vérifie les expressions exactes communes
     */
    private static function checkExactExpressions($message) {
        $expressions = [
            'oui' => "👍 D'accord ! Comment puis-je vous aider plus précisément ?",
            'non' => "🤔 Pas de souci ! Y a-t-il autre chose que je puisse faire pour vous ?",
            'ok' => "✅ Parfait ! N'hésitez pas si vous avez d'autres questions !",
            'd\'accord' => "👍 Super ! Je reste à votre disposition !",
            'comment' => "🤔 Que voulez-vous savoir exactement ? Je peux vous expliquer :\n• Comment faire une réclamation\n• Comment suivre un dossier\n• Comment fonctionne l'IA",
            'pourquoi' => "🤔 Bonne question ! Pouvez-vous préciser ce que vous voulez comprendre ?",
            'quoi' => "❓ Que souhaitez-vous savoir ? Je suis là pour répondre à vos questions sur ImpactAble !",
            'qui' => "🤖 Je suis ImpactBot, votre assistant virtuel ! Et vous, comment puis-je vous aider ?",
            '?' => "❓ Vous avez une question ? N'hésitez pas à la poser clairement, je ferai de mon mieux pour y répondre !",
            'test' => "✅ Test reçu ! Je fonctionne correctement. Comment puis-je vous aider ? 🤖",
        ];
        
        foreach ($expressions as $expr => $response) {
            if ($message === $expr || trim($message) === $expr) {
                return [
                    'response' => $response,
                    'category' => 'expression',
                    'confidence' => 100,
                    'bot_name' => self::$botName,
                    'bot_avatar' => self::$botAvatar
                ];
            }
        }
        
        return null;
    }
    
    /**
     * Calcule le score de correspondance amélioré
     */
    private static function calculateMatchScore($message, $keywords) {
        $score = 0;
        $maxScore = count($keywords);
        $words = explode(' ', $message);
        
        foreach ($keywords as $keyword) {
            $keyword = mb_strtolower($keyword, 'UTF-8');
            
            // Correspondance exacte (score maximum)
            if (strpos($message, $keyword) !== false) {
                $score += 1;
                continue;
            }
            
            // Correspondance partielle avec chaque mot
            foreach ($words as $word) {
                if (strlen($word) < 3) continue;
                
                // Distance de Levenshtein pour la tolérance aux fautes
                $distance = levenshtein($word, $keyword);
                $maxLen = max(strlen($word), strlen($keyword));
                
                if ($distance <= 2 || ($maxLen > 5 && $distance <= 3)) {
                    $score += 0.7;
                    break;
                }
                
                // Correspondance de début de mot
                if (strpos($keyword, $word) === 0 || strpos($word, $keyword) === 0) {
                    $score += 0.5;
                    break;
                }
            }
        }
        
        return $maxScore > 0 ? $score / $maxScore : 0;
    }
    
    /**
     * Normalise le texte
     */
    private static function normalizeText($text) {
        // Supprimer la ponctuation excessive
        $text = preg_replace('/[!?]{2,}/', ' ', $text);
        // Garder les lettres, chiffres, espaces et apostrophes
        $text = preg_replace('/[^\p{L}\p{N}\s\']/u', ' ', $text);
        // Supprimer les espaces multiples
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
    }
    
    /**
     * Retourne un message de bienvenue
     */
    public static function getWelcomeMessage() {
        $hour = date('H');
        $greeting = $hour < 12 ? "Bonjour" : ($hour < 18 ? "Bon après-midi" : "Bonsoir");
        
        $messages = [
            "$greeting ! 👋 Je suis **ImpactBot**, votre assistant virtuel.\n\n🎯 Je peux vous aider à :\n• Créer une réclamation\n• Suivre votre dossier\n• Répondre à vos questions\n\n*'Where Ability Meets Impact'* ✨\n\nComment puis-je vous aider ?",
        ];
        
        return [
            'response' => $messages[array_rand($messages)],
            'category' => 'welcome',
            'confidence' => 100,
            'bot_name' => self::$botName,
            'bot_avatar' => self::$botAvatar
        ];
    }
    
    /**
     * Retourne des suggestions contextuelles
     */
    public static function getSuggestions() {
        return [
            "📝 Comment faire une réclamation ?",
            "🔍 Suivre mon dossier",
            "⏱️ Quels sont les délais ?",
            "🧠 Comment fonctionne l'IA ?"
        ];
    }
}
?>
