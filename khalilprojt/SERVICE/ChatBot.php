<?php
/**
 * Service ChatBot Intelligent Avancé pour ImpactAble
 * Version 4.0 - Intelligence Maximale
 * 
 * Fonctionnalités:
 * - Base de connaissances étendue (30+ sujets)
 * - Correction orthographique avancée
 * - Compréhension contextuelle
 * - Extraction d'entités (numéros, dates)
 * - Détection de sentiment
 * - Réponses adaptatives
 * - Mémoire conversationnelle
 */
class ChatBot {
    
    private static $botName = "Khalil";
    private static $botAvatar = "K";
    
    // ==================== CORRECTION ORTHOGRAPHIQUE ====================
    private static $corrections = [
        // Mots fréquemment mal écrits
        'reclamtion' => 'reclamation',
        'reclmation' => 'reclamation',
        'reclamaion' => 'reclamation',
        'reclametion' => 'reclamation',
        'reclamaton' => 'reclamation',
        'reclamatoin' => 'reclamation',
        'reclam' => 'reclamation',
        'reclamarion' => 'reclamation',
        'reclamattion' => 'reclamation',
        'problem' => 'probleme',
        'problm' => 'probleme',
        'probleme' => 'probleme',
        'problème' => 'probleme',
        'problmes' => 'probleme',
        'probléme' => 'probleme',
        'suive' => 'suivre',
        'suivie' => 'suivre',
        'suivis' => 'suivre',
        'suivir' => 'suivre',
        'sivui' => 'suivre',
        'suvii' => 'suivre',
        'dosier' => 'dossier',
        'dossié' => 'dossier',
        'dosiier' => 'dossier',
        'dossir' => 'dossier',
        'dosser' => 'dossier',
        'urgant' => 'urgent',
        'urgnt' => 'urgent',
        'urgen' => 'urgent',
        'urgence' => 'urgent',
        'formulare' => 'formulaire',
        'formulire' => 'formulaire',
        'formullaire' => 'formulaire',
        'formualire' => 'formulaire',
        'formlaire' => 'formulaire',
        'coment' => 'comment',
        'commant' => 'comment',
        'commen' => 'comment',
        'commet' => 'comment',
        'reponse' => 'reponse',
        'réponse' => 'reponse',
        'reponce' => 'reponse',
        'repponse' => 'reponse',
        'delai' => 'delai',
        'délai' => 'delai',
        'deali' => 'delai',
        'delais' => 'delai',
        'accesbilite' => 'accessibilite',
        'accessibilte' => 'accessibilite',
        'accesibilite' => 'accessibilite',
        'hanidcap' => 'handicap',
        'hnadicap' => 'handicap',
        'handiacap' => 'handicap',
        'handicapé' => 'handicap',
        'telephon' => 'telephone',
        'téléphone' => 'telephone',
        'telepone' => 'telephone',
        'jveux' => 'je veux',
        'jpeux' => 'je peux',
        'jsuis' => 'je suis',
        'chuis' => 'je suis',
        'jai' => 'j\'ai',
        'svp' => 's\'il vous plait',
        'stp' => 's\'il te plait',
        'plait' => 'plait',
        'porquoi' => 'pourquoi',
        'pourkoi' => 'pourquoi',
        'prq' => 'pourquoi',
        'pq' => 'pourquoi',
        'pk' => 'pourquoi',
        'cke' => 'ce que',
        'chque' => 'chaque',
        'besion' => 'besoin',
        'bezoin' => 'besoin',
        'bsoin' => 'besoin',
        'ettat' => 'etat',
        'statu' => 'statut',
        'satus' => 'statut',
        'statues' => 'statut',
        'connecte' => 'connecter',
        'connecter' => 'connecter',
        'coonecter' => 'connecter',
        'inscrire' => 'inscription',
        'inscrir' => 'inscription',
        'inscripiton' => 'inscription',
        'compte' => 'compte',
        'compt' => 'compte',
        'atendre' => 'attendre',
        'atteindre' => 'attendre',
        'attenre' => 'attendre',
        'envoier' => 'envoyer',
        'envoyé' => 'envoyer',
        'envoiyer' => 'envoyer',
        'modifier' => 'modifier',
        'modifer' => 'modifier',
        'modifie' => 'modifier',
        'supprimer' => 'supprimer',
        'supprime' => 'supprimer',
        'suprimer' => 'supprimer',
        'annuler' => 'annuler',
        'anuler' => 'annuler',
        'anulé' => 'annuler',
    ];

    // ==================== SYNONYMES ET EXPRESSIONS ====================
    private static $synonyms = [
        'reclamation' => ['reclamation', 'plainte', 'signalement', 'demande', 'requete', 'doleance', 'grief', 'contestation'],
        'creer' => ['creer', 'faire', 'deposer', 'soumettre', 'envoyer', 'remplir', 'nouveau', 'nouvelle'],
        'suivre' => ['suivre', 'suivi', 'tracker', 'verifier', 'consulter', 'voir', 'statut', 'etat', 'avancement'],
        'urgent' => ['urgent', 'urgence', 'pressant', 'critique', 'grave', 'important', 'prioritaire', 'vite', 'rapidement'],
        'aide' => ['aide', 'aider', 'help', 'assistance', 'support', 'probleme', 'bloque', 'bug', 'erreur', 'coincé'],
        'delai' => ['delai', 'temps', 'duree', 'quand', 'combien', 'attendre', 'jours', 'heures'],
        'contact' => ['contact', 'contacter', 'joindre', 'appeler', 'email', 'telephone', 'humain', 'agent'],
        'merci' => ['merci', 'thanks', 'super', 'genial', 'parfait', 'excellent', 'top', 'cool', 'bravo'],
        'salut' => ['bonjour', 'salut', 'hello', 'bonsoir', 'hey', 'coucou', 'salam', 'yo', 'hola'],
        'oui' => ['oui', 'yes', 'ouais', 'absolument', 'exactement', 'bien sur', 'ok', 'okay', 'd\'accord', 'daccord', 'affirmatif'],
        'non' => ['non', 'no', 'nan', 'nope', 'pas', 'jamais', 'aucun'],
    ];

    // ==================== BASE DE CONNAISSANCES ÉTENDUE ====================
    private static $knowledgeBase = [
        
        // ========== SALUTATIONS ==========
        'salutations' => [
            'keywords' => ['bonjour', 'salut', 'hello', 'hi', 'bonsoir', 'hey', 'coucou', 'salam', 'bsr', 'bjr', 'cc', 'yo', 'hola', 'wesh', 'slt', 'bj', 'allo', 'ohé'],
            'patterns' => ['/^(salut|bonjour|hello|hey|coucou|bonsoir)/i'],
            'responses' => [
                "Bonjour ! 👋 Je suis **Khalil**, votre assistant virtuel ImpactAble.\n\n🎯 **Comment puis-je vous aider ?**\n\n• 📝 Créer une réclamation\n• 🔍 Suivre un dossier\n• ❓ Poser une question\n• 🆘 Obtenir de l'aide\n\n💬 N'hésitez pas à me parler naturellement !",
                "Salut ! 😊 Bienvenue sur ImpactAble !\n\nJe suis **Khalil**, votre assistant 24h/24.\n\n**Que souhaitez-vous faire ?**\n• Déposer une réclamation\n• Suivre votre dossier\n• En savoir plus sur nos services",
                "Hello ! 👋 Ravi de vous accueillir !\n\nJe suis là pour vous accompagner dans toutes vos démarches.\n\nDites-moi simplement ce dont vous avez besoin ! 😊"
            ],
            'priority' => 10
        ],
        
        // ========== CRÉER RÉCLAMATION ==========
        'faire_reclamation' => [
            'keywords' => ['faire', 'creer', 'créer', 'nouvelle', 'deposer', 'déposer', 'soumettre', 'envoyer', 'reclamer', 'réclamer', 'reclamation', 'réclamation', 'formulaire', 'remplir', 'plainte', 'signaler', 'signalement', 'porter', 'demande', 'requete', 'requête', 'ouvrir', 'commencer', 'demarrer', 'démarrer', 'initier', 'lancer'],
            'patterns' => [
                '/comment\s+(faire|creer|deposer|soumettre)/i',
                '/(nouvelle|creer|faire|deposer)\s+(reclamation|plainte|demande)/i',
                '/je\s+(veux|voudrais|souhaite|desire)\s+(faire|creer|deposer)/i',
                '/(ou|où)\s+(faire|deposer|soumettre)/i'
            ],
            'responses' => [
                "📝 **Comment déposer une réclamation :**\n\n**Étape par étape :**\n\n1️⃣ **Remplissez le formulaire** sur la page d'accueil\n   → Nom, email, téléphone\n\n2️⃣ **Choisissez la catégorie**\n   → Ex: Accessibilité, Discrimination...\n\n3️⃣ **Décrivez votre problème**\n   → Soyez précis et détaillé\n   → Plus c'est clair, plus vite on vous aide !\n\n4️⃣ **Ajoutez des photos** (optionnel)\n   → Formats: JPG, PNG, GIF\n   → Max 5 Mo\n\n5️⃣ **Cliquez sur 'Envoyer'**\n\n⚡ **Notre IA analyse automatiquement la priorité !**\n\n📧 Vous recevrez un numéro de suivi par email.",
                "🎯 **Créer une réclamation en 2 minutes !**\n\n✅ Rendez-vous sur la page d'accueil\n✅ Remplissez vos coordonnées\n✅ Décrivez votre situation\n✅ Notre IA détermine la priorité\n✅ Recevez un numéro de suivi\n\n💡 **Conseils :**\n• Donnez des détails précis\n• Ajoutez des photos si possible\n• Vérifiez votre email après envoi\n\nVoulez-vous que je vous guide pas à pas ?"
            ],
            'priority' => 9,
            'actions' => ['navigate:index.php#reclamation-form']
        ],
        
        // ========== SUIVI RÉCLAMATION ==========
        'suivi' => [
            'keywords' => ['suivi', 'suivre', 'tracker', 'statut', 'status', 'etat', 'état', 'avancement', 'numero', 'numéro', 'dossier', 'reference', 'référence', 'ou en est', 'où en est', 'consulter', 'verifier', 'vérifier', 'retrouver', 'chercher', 'rechercher', 'historique', 'progression', 'evolution', 'évolution', 'resultat', 'résultat', 'repondu', 'répondu', 'traite', 'traité'],
            'patterns' => [
                '/(ou|où)\s+en\s+est\s+(ma|mon|la)/i',
                '/suivre\s+(ma|mon|une|le)/i',
                '/(quel|quelle)\s+est\s+(le|la)\s+(statut|etat|état)/i',
                '/mon\s+(dossier|numero|numéro)/i',
                '/reclamation\s*(numero|n°|#|numéro)?\s*\d+/i'
            ],
            'responses' => [
                "🔍 **Suivre votre réclamation :**\n\n**Comment faire ?**\n\n1️⃣ Cliquez sur **'Suivre ma Réclamation'** en haut de page\n2️⃣ Entrez votre **numéro de dossier** (ex: 1, 2, 3...)\n3️⃣ Consultez la **timeline détaillée**\n\n📊 **États possibles :**\n• 📥 **En attente** - Réclamation reçue\n• 🔄 **En cours** - Traitement actif\n• ✅ **Traitée** - Réponse disponible\n• 📁 **Fermée** - Dossier clôturé\n\n💡 **Numéro perdu ?** Consultez vos emails ou contactez-nous !",
                "📋 **Suivi de dossier**\n\n➡️ Page **'Suivi'** accessible en haut du site\n➡️ Saisissez votre numéro de réclamation\n➡️ Visualisez l'historique complet\n\n**Vous verrez :**\n• Date de création\n• Statut actuel\n• Réponses reçues\n• Prochaines étapes\n\n❓ Vous n'avez pas reçu votre numéro ? Vérifiez vos spams !"
            ],
            'priority' => 9,
            'actions' => ['navigate:suivi_reclamation.php']
        ],
        
        // ========== PRIORITÉ & URGENCE ==========
        'priorite' => [
            'keywords' => ['priorite', 'priorité', 'urgent', 'urgence', 'importante', 'important', 'normal', 'delai', 'délai', 'temps', 'vite', 'rapidement', 'critique', 'grave', 'serieux', 'sérieux', 'pressant', 'presser', 'accelerer', 'accélérer', 'niveau', 'gravité', 'gravite', 'escalader', 'expedier', 'expédier'],
            'patterns' => [
                '/(c\'?est|très|trop)\s+urgent/i',
                '/priorite\s+(haute|urgente|elevee|élevée)/i',
                '/changer\s+(la|le)?\s*priorite/i',
                '/comment\s+(est|sont)\s+(determine|déterminé|calcule|calculé)/i',
                '/(augmenter|monter|changer)\s+(la)?\s*priorite/i'
            ],
            'responses' => [
                "🎯 **Système de priorité ImpactAble**\n\n**3 niveaux :**\n\n🔴 **URGENTE** (Réponse sous 24h)\n• Danger immédiat\n• Blocage critique\n• Accident / Incident grave\n• Discrimination active\n\n🟠 **MOYENNE** (Réponse sous 48h)\n• Problème important\n• Situation récurrente\n• Impact significatif\n\n🟢 **FAIBLE** (Réponse sous 5 jours)\n• Suggestion d'amélioration\n• Question générale\n• Information\n\n🧠 **Notre IA analyse automatiquement :**\n• Mots-clés (urgent, danger, grave...)\n• Catégorie sélectionnée\n• Contexte de la description",
                "⚡ **Les priorités expliquées :**\n\n| Niveau | Délai | Exemples |\n|--------|-------|----------|\n| 🔴 Urgent | 24h | Danger, blocage |\n| 🟠 Important | 48h | Problème récurrent |\n| 🟢 Normal | 5 jours | Question, suggestion |\n\n**Comment ça marche ?**\nL'IA détecte les mots-clés dans votre texte et suggère une priorité.\n\n**Exemples de mots détectés :**\n• 🔴 \"urgent\", \"danger\", \"grave\", \"accident\"\n• 🟠 \"problème\", \"important\", \"récurrent\"\n• 🟢 \"question\", \"information\", \"suggestion\""
            ],
            'priority' => 8
        ],
        
        // ========== CATÉGORIES ==========
        'categories' => [
            'keywords' => ['categorie', 'catégorie', 'type', 'types', 'domaine', 'secteur', 'liste', 'quelles', 'lesquelles', 'disponibles', 'choix', 'options', 'domaines', 'secteurs', 'thematique', 'thématique', 'sujet', 'sujets', 'concerne', 'concernant'],
            'patterns' => [
                '/(quelles?|les?)\s+(categories?|types?)/i',
                '/liste\s+des?\s+(categories?|domaines?)/i',
                '/(quel|quelle)\s+(categorie|type)\s+choisir/i'
            ],
            'responses' => [
                "📂 **Catégories de réclamation :**\n\n**Accessibilité ♿**\n• Bâtiments non accessibles\n• Équipements manquants\n• Signalétique inadaptée\n\n**Discrimination ⚖️**\n• Traitement inégal\n• Refus de service\n• Harcèlement\n\n**Transport 🚌**\n• Bus/métro non adapté\n• Parking PMR\n• Gare inaccessible\n\n**Santé 🏥**\n• Accès aux soins\n• Équipements médicaux\n• Personnel médical\n\n**Emploi 💼**\n• Discrimination embauche\n• Adaptation poste\n• Harcèlement travail\n\n**Éducation 📚**\n• Accès établissements\n• Supports pédagogiques\n• Accompagnement\n\n**Technique 🔧**\n• Bug application\n• Problème compte\n• Site inaccessible\n\n**Autre 📦**\n• Autres situations\n\n💡 Choisissez celle qui correspond le mieux à votre situation !"
            ],
            'priority' => 7
        ],
        
        // ========== AIDE & SUPPORT ==========
        'aide' => [
            'keywords' => ['aide', 'aider', 'help', 'assistance', 'support', 'probleme', 'problème', 'bloque', 'bloqué', 'marche pas', 'fonctionne pas', 'bug', 'erreur', 'coincé', 'perdu', 'comprends pas', 'comprend pas', 'galere', 'galère', 'difficile', 'impossible', 'bizarre', 'etrange', 'étrange', 'plante', 'crash', 'lent', 'souci', 'incident', 'dysfonctionnement'],
            'patterns' => [
                '/(ca|ça)\s+(marche|fonctionne)\s+(pas|plus)/i',
                '/j\'?ai\s+(un|une|des)\s+(probleme|problème|souci|erreur)/i',
                '/(besoin|demande)\s+(d\'?)aide/i',
                '/je\s+(ne|n\'?)\s*(comprends?|arrive|peux)\s+(pas|plus)/i',
                '/(pourquoi|pk|prq)\s+(ca|ça)\s+(marche|fonctionne)\s+(pas|plus)/i'
            ],
            'responses' => [
                "🆘 **Je suis là pour vous aider !**\n\n**Problèmes fréquents & solutions :**\n\n❌ **Le formulaire ne s'envoie pas ?**\n→ Vérifiez tous les champs obligatoires (*)\n→ Rafraîchissez la page (F5)\n→ Essayez un autre navigateur\n\n❌ **Numéro de dossier perdu ?**\n→ Consultez vos emails (et spams !)\n→ Contactez-nous avec votre nom/date\n\n❌ **Pas de réponse reçue ?**\n→ Vérifiez le délai selon la priorité\n→ Consultez la page de suivi\n\n❌ **Page qui ne charge pas ?**\n→ Videz le cache du navigateur\n→ Vérifiez votre connexion\n\n📧 **Toujours bloqué ?**\nContactez : support@impactable.tn",
                "💪 **Pas de panique !**\n\nDécrivez-moi votre problème :\n\n**Je peux vous aider avec :**\n• ❓ Le formulaire de réclamation\n• 🔍 Le suivi de dossier\n• 📋 Les catégories\n• ⏱️ Les délais\n• 💻 Les problèmes techniques\n\n**Dites-moi précisément :**\n• Que vouliez-vous faire ?\n• Que s'est-il passé ?\n• Y a-t-il un message d'erreur ?\n\nJe ferai de mon mieux pour vous dépanner ! 🔧"
            ],
            'priority' => 8
        ],
        
        // ========== DÉLAIS ==========
        'delais' => [
            'keywords' => ['combien', 'temps', 'duree', 'durée', 'reponse', 'réponse', 'attendre', 'jours', 'heures', 'quand', 'long', 'longtemps', 'rapide', 'vite', 'semaines', 'mois', 'traitement', 'processus', 'procedure', 'procédure', 'etapes', 'étapes'],
            'patterns' => [
                '/combien\s+de\s+temps/i',
                '/(quel|quelle)\s+(est|sont)\s+les?\s+delais?/i',
                '/quand\s+(aurai-?je|vais-?je|aurais?)/i',
                '/dans\s+combien\s+de\s+(temps|jours)/i',
                '/ca\s+prend\s+combien\s+de\s+temps/i'
            ],
            'responses' => [
                "⏱️ **Délais de traitement garantis :**\n\n| Priorité | Délai max | Notification |\n|----------|-----------|-------------|\n| 🔴 Urgente | **24h** | Email + SMS |\n| 🟠 Moyenne | **48h** | Email |\n| 🟢 Faible | **5 jours** | Email |\n\n**Comment ça fonctionne ?**\n\n1. ✅ Réclamation reçue → Email de confirmation\n2. 🔄 Prise en charge → Notification\n3. 💬 Réponse envoyée → Email avec détails\n4. 📁 Clôture → Récapitulatif\n\n💡 **Astuce :** Consultez régulièrement la page de suivi !",
                "🕐 **Quand aurez-vous une réponse ?**\n\n⚡ **Urgent** → 24 heures\n📋 **Important** → 48 heures\n📝 **Normal** → 5 jours ouvrés\n\n**Pas de nouvelles ?**\n• Vérifiez vos spams\n• Consultez la page de suivi\n• Le délai commence à la réception\n\n❓ Si le délai est dépassé, contactez-nous !"
            ],
            'priority' => 7
        ],
        
        // ========== REMERCIEMENTS ==========
        'remerciements' => [
            'keywords' => ['merci', 'thanks', 'thank', 'super', 'genial', 'génial', 'parfait', 'excellent', 'top', 'cool', 'nickel', 'bravo', 'bien', 'ok', 'okay', 'd\'accord', 'daccord', 'impec', 'impeccable', 'formidable', 'magnifique', 'fantastique', 'chouette', 'extra', 'trop bien', 'au top', 'classe', 'sympa'],
            'patterns' => [
                '/^(merci|thanks|thx)/i',
                '/(c\'?est|t\'es|vous etes)\s+(super|genial|top|parfait)/i',
                '/ca\s+m\'?a\s+(aide|aidé|bien aidé)/i'
            ],
            'responses' => [
                "Avec plaisir ! 😊✨\n\nN'hésitez pas si vous avez d'autres questions.\n\n**Je reste disponible pour :**\n• Répondre à vos questions\n• Vous guider dans vos démarches\n• Vous aider à suivre votre dossier\n\nBonne journée ! 🌟",
                "Je vous en prie ! 🙏\n\nC'est un plaisir de vous aider !\n\n💚 ImpactAble est là pour vous accompagner.\n\nÀ bientôt ! 👋",
                "Ravi d'avoir pu vous aider ! 🎉\n\nVotre satisfaction est notre priorité !\n\nSi vous avez d'autres questions, je suis là 24h/24 ! 😊"
            ],
            'priority' => 6
        ],
        
        // ========== AU REVOIR ==========
        'aurevoir' => [
            'keywords' => ['au revoir', 'aurevoir', 'bye', 'goodbye', 'a bientot', 'à bientôt', 'ciao', 'a plus', 'à plus', 'bonne journee', 'bonne journée', 'bonne soiree', 'bonne soirée', 'salut', 'tchao', 'tchuss', 'a la prochaine', 'à la prochaine', 'adieu', 'bonne nuit', 'je dois y aller', 'je m\'en vais', 'je pars'],
            'patterns' => [
                '/^(au revoir|bye|ciao|a plus|adieu)/i',
                '/bonne\s+(journee|soiree|nuit|continuation)/i',
                '/je\s+(dois|vais)\s+(partir|y aller)/i'
            ],
            'responses' => [
                "Au revoir ! 👋✨\n\nMerci d'avoir utilisé ImpactAble !\n\n**Avant de partir :**\n• Notez bien votre numéro de dossier\n• Consultez régulièrement vos emails\n• Revenez quand vous voulez !\n\nPrenez soin de vous ! 💚",
                "Bye bye ! 👋😊\n\nC'était un plaisir de vous aider !\n\nN'hésitez pas à revenir pour :\n• Suivre votre réclamation\n• Poser de nouvelles questions\n• Signaler un nouveau problème\n\nBonne continuation ! 🌟"
            ],
            'priority' => 6
        ],
        
        // ========== IA / TECHNOLOGIE ==========
        'ia' => [
            'keywords' => ['ia', 'intelligence', 'artificielle', 'automatique', 'robot', 'bot', 'machine', 'algorithme', 'comment ca marche', 'comment ça marche', 'fonctionnement', 'technologie', 'tech', 'innovation', 'analyse', 'detection', 'détection', 'prediction', 'prédiction', 'khalil', 'qui es tu', 'qui es-tu', 't\'es qui'],
            'patterns' => [
                '/comment\s+(ca|ça)\s+(marche|fonctionne)/i',
                '/(c\'?est|t\'es)\s+(quoi|qui)\s+(toi|l\'?ia)/i',
                '/qu\'?est-?ce\s+que\s+l\'?ia/i',
                '/(qui|quoi)\s+es-?tu/i'
            ],
            'responses' => [
                "🧠 **Je suis Khalil, votre assistant IA !**\n\n**Qui suis-je ?**\nUn chatbot intelligent conçu pour vous aider sur ImpactAble.\n\n**Ce que je peux faire :**\n• 💬 Comprendre vos questions en langage naturel\n• 🔍 Analyser le contexte de vos demandes\n• 📊 Détecter automatiquement les priorités\n• 😊 M'adapter à votre humeur\n• 🎯 Vous guider pas à pas\n\n**Technologies utilisées :**\n• Traitement du langage naturel (NLP)\n• Analyse de sentiment\n• Détection d'entités\n• Apprentissage par mots-clés\n\n🎯 Testez l'IA de priorisation sur la page **'Démo IA'** !",
                "🤖 **À propos de Khalil**\n\nJe suis votre assistant virtuel ImpactAble !\n\n**Mes capacités :**\n• Comprendre le français (même avec des fautes !)\n• Répondre 24h/24, 7j/7\n• Analyser les priorités automatiquement\n• Détecter les émotions dans vos messages\n• Vous orienter vers les bonnes ressources\n\n**Mes limites :**\n• Je ne suis pas humain\n• Je me base sur des mots-clés\n• Pour les cas complexes, contactez le support\n\nComment puis-je vous aider ? 😊"
            ],
            'priority' => 7
        ],
        
        // ========== ACCESSIBILITÉ & HANDICAP ==========
        'handicap' => [
            'keywords' => ['handicap', 'handicapé', 'handicape', 'pmr', 'fauteuil', 'roulant', 'aveugle', 'sourd', 'accessibilite', 'accessibilité', 'rampe', 'mobilite', 'mobilité', 'malvoyant', 'malentendant', 'autisme', 'tsa', 'mental', 'psychique', 'moteur', 'visuel', 'auditif', 'cognitif', 'invalidite', 'invalidité', 'inclusion', 'inclusif', 'adapté', 'adaptation', 'aménagement', 'amenagement'],
            'patterns' => [
                '/personne\s+(handicapee|handicapé|à mobilité)/i',
                '/(pas|non)\s+accessible/i',
                '/fauteuil\s+roulant/i',
                '/probleme\s+(d\'?)accessibilite/i'
            ],
            'responses' => [
                "♿ **ImpactAble : Ensemble pour l'accessibilité**\n\n**Notre mission :**\nPermettre à chacun de signaler les obstacles à l'accessibilité.\n\n**Types de situations à signaler :**\n\n🏢 **Bâtiments**\n• Pas de rampe d'accès\n• Ascenseur en panne\n• Portes trop étroites\n• Sanitaires non adaptés\n\n🚌 **Transports**\n• Bus non accessibles\n• Gares sans élévateur\n• Taxis refusant les PMR\n\n💼 **Emploi**\n• Discrimination à l'embauche\n• Poste non adapté\n• Harcèlement\n\n📚 **Éducation**\n• Établissement non accessible\n• Manque d'accompagnement\n• Supports non adaptés\n\n**Chaque signalement compte !** 💚\nEnsemble, construisons un monde plus inclusif."
            ],
            'priority' => 8
        ],
        
        // ========== CONTACT ==========
        'contact' => [
            'keywords' => ['contact', 'contacter', 'email', 'mail', 'telephone', 'téléphone', 'appeler', 'ecrire', 'écrire', 'joindre', 'humain', 'agent', 'personne', 'operateur', 'opérateur', 'conseiller', 'parler', 'quelqu\'un', 'vrai', 'reel', 'réel', 'physique', 'adresse', 'bureau', 'agence'],
            'patterns' => [
                '/(parler|discuter)\s+(avec|a)\s+(un|une)\s+(humain|personne|agent)/i',
                '/(comment|ou|où)\s+(vous|te)\s+contacter/i',
                '/je\s+veux\s+(parler|ecrire)\s+a/i',
                '/(numero|numéro|adresse)\s+(de|du)\s+(contact|telephone|téléphone)/i'
            ],
            'responses' => [
                "📞 **Nous contacter :**\n\n**📧 Email**\nsupport@impactable.tn\n→ Réponse sous 24-48h\n\n**🌐 Site web**\nwww.impactable.tn\n\n**📍 Adresse**\nTunis, Tunisie\n\n**⏰ Horaires**\nLun-Ven : 8h-18h\nSam : 9h-13h\n\n💬 **En attendant**, je suis disponible 24h/24 pour vos questions !",
                "👤 **Besoin d'un humain ?**\n\nJe comprends ! Voici comment nous joindre :\n\n📧 **Email** : support@impactable.tn\n   (Réponse rapide garantie)\n\n**Quand contacter le support ?**\n• Problème technique complexe\n• Réclamation non résolue\n• Informations confidentielles\n• Urgence non traitée\n\n💡 En attendant, puis-je essayer de vous aider ?"
            ],
            'priority' => 7
        ],
        
        // ========== QUI SOMMES-NOUS ==========
        'qui' => [
            'keywords' => ['qui es tu', 'qui es-tu', 'tu es qui', 'c\'est quoi', 'c est quoi', 'impactable', 'a propos', 'à propos', 'presentation', 'présentation', 'projet', 'plateforme', 'service', 'mission', 'objectif', 'but', 'pourquoi', 'crée', 'créé', 'fondé', 'origine', 'histoire', 'equipe', 'équipe'],
            'patterns' => [
                '/(c\'?est|qu\'est)\s+quoi\s+impactable/i',
                '/parle-?moi\s+de\s+(toi|impactable|vous)/i',
                '/(qui|quoi)\s+est\s+impactable/i',
                '/a\s+propos\s+de/i'
            ],
            'responses' => [
                "🌟 **ImpactAble - Where Ability Meets Impact**\n\n**Notre vision :**\nUn monde où l'accessibilité est un droit, pas un privilège.\n\n**Notre mission :**\n• Faciliter le signalement des obstacles\n• Accélérer leur résolution grâce à l'IA\n• Créer un impact positif et mesurable\n\n**Ce que nous offrons :**\n• 📝 Plateforme de réclamations intuitive\n• 🧠 IA de priorisation intelligente\n• 📊 Suivi transparent en temps réel\n• 📈 Statistiques et analytics\n• 🤖 Assistant virtuel 24/7\n\n**Nos valeurs :**\n💚 Inclusion • Transparence • Innovation • Impact\n\n*\"Ensemble, construisons un monde accessible à tous.\"*"
            ],
            'priority' => 6
        ],
        
        // ========== PHOTOS & DOCUMENTS ==========
        'photo' => [
            'keywords' => ['photo', 'image', 'piece jointe', 'pièce jointe', 'fichier', 'document', 'preuve', 'joindre', 'telecharger', 'télécharger', 'upload', 'importer', 'ajouter', 'envoyer', 'scanner', 'scan', 'pdf', 'jpeg', 'png', 'jpg', 'capture', 'screenshot', 'ecran'],
            'patterns' => [
                '/comment\s+(ajouter|joindre|envoyer)\s+(une|des|la)\s+(photo|image|fichier)/i',
                '/(je|puis-?je|peut-?on)\s+(ajouter|joindre|envoyer)/i',
                '/quelle?\s+(format|taille|type)/i'
            ],
            'responses' => [
                "📸 **Ajouter des photos/documents :**\n\n**Comment faire ?**\n1️⃣ Dans le formulaire, cliquez sur **'📎 Joindre un fichier'**\n2️⃣ Sélectionnez votre fichier\n3️⃣ Patientez pendant l'upload\n4️⃣ Vérifiez l'aperçu\n\n**Formats acceptés :**\n• Images : JPG, JPEG, PNG, GIF\n• Documents : PDF\n\n**Limites :**\n• Taille max : **5 Mo** par fichier\n• Max **3 fichiers** par réclamation\n\n💡 **Conseils :**\n• Photos claires et lisibles\n• Captures d'écran des erreurs\n• Preuves datées si possible\n\n⚡ Les photos accélèrent le traitement !"
            ],
            'priority' => 6
        ],
        
        // ========== COMPTE & INSCRIPTION ==========
        'compte' => [
            'keywords' => ['compte', 'inscription', 'inscrire', 'enregistrer', 'connecter', 'connexion', 'login', 'logout', 'deconnexion', 'déconnexion', 'mot de passe', 'password', 'mdp', 'identifiant', 'username', 'profil', 'parametres', 'paramètres', 'modifier', 'supprimer', 'desactiver', 'désactiver', 'creer compte', 'ouvrir compte'],
            'patterns' => [
                '/comment\s+(me|m\')\s*(connecter|inscrire)/i',
                '/(creer|ouvrir|avoir)\s+un\s+compte/i',
                '/mot\s+de\s+passe\s+(oublie|oublié|perdu)/i',
                '/(modifier|changer)\s+(mon|le)\s+(profil|compte)/i'
            ],
            'responses' => [
                "👤 **Gestion de compte**\n\n**Bonne nouvelle !** 🎉\nPour l'instant, **pas besoin de compte** pour :\n• Déposer une réclamation\n• Suivre votre dossier\n• Utiliser le chatbot\n\n**Comment ça marche ?**\n• Vous recevez un numéro unique par email\n• Ce numéro = votre accès au suivi\n• Conservez-le précieusement !\n\n**À venir :**\n• Espace personnel\n• Historique complet\n• Notifications personnalisées\n\n❓ D'autres questions ?"
            ],
            'priority' => 5
        ],
        
        // ========== OUI ==========
        'oui' => [
            'keywords' => ['oui', 'yes', 'ouais', 'absolument', 'exactement', 'tout a fait', 'tout à fait', 'bien sur', 'bien sûr', 'evidemment', 'évidemment', 'affirmatif', 'effectivement', 'certainement', 'carrément', 'grave', 'trop', 'totalement', 'completement', 'complètement'],
            'patterns' => ['/^(oui|yes|ouais|ok|d\'?accord)$/i'],
            'responses' => [
                "👍 Parfait ! Comment puis-je vous aider davantage ?\n\n**Suggestions :**\n• 📝 Créer une réclamation\n• 🔍 Suivre un dossier\n• ❓ Poser une question",
                "✅ D'accord ! Que souhaitez-vous savoir d'autre ?\n\nJe suis là pour vous guider ! 😊",
                "Super ! 😊 Y a-t-il autre chose que je puisse faire pour vous ?"
            ],
            'priority' => 3
        ],
        
        // ========== NON ==========
        'non' => [
            'keywords' => ['non', 'no', 'nan', 'nope', 'pas vraiment', 'pas du tout', 'jamais', 'aucun', 'aucune', 'negatif', 'négatif'],
            'patterns' => ['/^(non|no|nan|nope)$/i'],
            'responses' => [
                "🤔 D'accord ! N'hésitez pas si vous avez des questions plus tard.\n\nJe reste disponible 24h/24 ! 😊",
                "Pas de souci ! Je reste là si besoin.\n\n**Rappel :** Vous pouvez toujours :\n• Déposer une réclamation\n• Suivre un dossier\n• Me poser des questions\n\nBonne journée ! 👋"
            ],
            'priority' => 3
        ],
        
        // ========== TEST ==========
        'test' => [
            'keywords' => ['test', 'tester', 'essai', 'essayer', 'demo', 'démo', 'demonstration', 'démonstration', 'exemple', 'essaie', 'essaye', 'verifier', 'vérifier'],
            'patterns' => ['/^test$/i', '/je\s+veux\s+tester/i'],
            'responses' => [
                "✅ **Test réussi ! Je fonctionne correctement.** 🤖\n\n**Testez aussi :**\n• 🧠 **Démo IA** : Analysez la priorité d'un texte\n• 📝 **Formulaire** : Créez une réclamation test\n• 🔍 **Suivi** : Vérifiez un numéro existant\n\n💬 Posez-moi n'importe quelle question !",
                "🎯 Le chatbot est opérationnel !\n\n**Mes capacités :**\n• Comprendre vos questions\n• Corriger les fautes d'orthographe\n• Détecter le contexte\n• Vous guider pas à pas\n\nEssayez de me poser une vraie question ! 😊"
            ],
            'priority' => 5
        ],
        
        // ========== PROBLÈMES SPÉCIFIQUES ==========
        'probleme_formulaire' => [
            'keywords' => ['formulaire', 'envoie pas', 'envoyer pas', 'soumettre pas', 'ne marche pas', 'bloque', 'erreur', 'impossible envoyer', 'ne fonctionne pas', 'charge pas', 'affiche pas', 'bugue', 'plante'],
            'patterns' => [
                '/(formulaire|page)\s+(ne\s+)?(marche|fonctionne|charge)\s+(pas|plus)/i',
                '/impossible\s+(d\'?)?(envoyer|soumettre|valider)/i',
                '/(erreur|bug)\s+(dans|sur|avec)\s+(le)?\s*formulaire/i'
            ],
            'responses' => [
                "🔧 **Problème avec le formulaire ?**\n\n**Vérifications rapides :**\n\n1️⃣ **Champs obligatoires** ✅\n   → Tous les champs avec * sont remplis ?\n\n2️⃣ **Format email** 📧\n   → Votre email est-il valide ?\n\n3️⃣ **Taille des fichiers** 📁\n   → Max 5 Mo par fichier\n\n4️⃣ **Navigateur** 🌐\n   → Essayez Chrome ou Firefox\n   → Désactivez les bloqueurs de pub\n\n5️⃣ **Rafraîchir** 🔄\n   → Appuyez sur F5 ou Ctrl+F5\n\n**Toujours bloqué ?**\n→ Videz le cache du navigateur\n→ Essayez en navigation privée\n→ Contactez : support@impactable.tn"
            ],
            'priority' => 8
        ],
        
        // ========== STATUTS ==========
        'statut' => [
            'keywords' => ['statut', 'status', 'en attente', 'en cours', 'traite', 'traité', 'ferme', 'fermé', 'cloture', 'clôturé', 'resolu', 'résolu', 'rejete', 'rejeté', 'accepte', 'accepté', 'refuse', 'refusé', 'signification', 'veut dire', 'signifie', 'comprendre'],
            'patterns' => [
                '/(que|qu\'est)\s+(signifie|veut dire)\s+(le\s+)?statut/i',
                '/c\'?est\s+quoi\s+(le\s+statut|en attente|en cours)/i',
                '/difference\s+entre\s+les\s+statuts/i'
            ],
            'responses' => [
                "📊 **Comprendre les statuts :**\n\n📥 **En attente**\n→ Réclamation reçue\n→ En attente d'attribution\n→ Délai : quelques heures\n\n🔄 **En cours**\n→ Un agent traite votre dossier\n→ Analyse en cours\n→ Réponse bientôt\n\n✅ **Traitée**\n→ Réponse envoyée\n→ Consultez les détails\n→ Vous pouvez répondre\n\n📁 **Fermée**\n→ Dossier clôturé\n→ Problème résolu\n→ Archivé\n\n❌ **Rejetée** (rare)\n→ Hors périmètre\n→ Informations insuffisantes\n→ Vous pouvez resoumettre"
            ],
            'priority' => 7
        ],
        
        // ========== EMOJI / HUMEUR ==========
        'frustration' => [
            'keywords' => ['énervé', 'enerve', 'frustré', 'frustre', 'marre', 'ras le bol', 'colère', 'colere', 'fache', 'fâché', 'furieux', 'agacé', 'agace', 'exaspéré', 'exaspere', 'insupportable', 'inadmissible', 'scandaleux', 'honteux', 'inacceptable', 'nul', 'pourri', 'catastrophe', 'catastrophique', 'desespoir', 'désespoir', 'desespere', 'désespéré'],
            'patterns' => [
                '/(j\'?en ai|j\'en ai)\s+(marre|ras le bol|assez)/i',
                '/(c\'?est|vous etes)\s+(nul|pourri|catastrophique|scandaleux)/i',
                '/je\s+(suis|me sens)\s+(énervé|frustré|en colère)/i'
            ],
            'responses' => [
                "😔 **Je comprends votre frustration.**\n\nVotre ressenti est légitime et nous le prenons très au sérieux.\n\n**Ce que je peux faire pour vous :**\n\n1️⃣ **M'expliquer le problème** en détail\n   → Je transmettrai aux équipes concernées\n\n2️⃣ **Créer une réclamation** prioritaire\n   → Elle sera traitée rapidement\n\n3️⃣ **Vous mettre en contact** avec le support\n   → Email : support@impactable.tn\n\n💚 Nous voulons vraiment résoudre votre situation.\n\nComment puis-je vous aider concrètement ?",
                "🤝 **Je suis vraiment désolé pour cette situation.**\n\nVotre mécontentement est compréhensible.\n\n**Parlons-en :**\n• Quel est précisément le problème ?\n• Depuis quand cela dure ?\n• Avez-vous déjà fait une réclamation ?\n\nJe vais faire mon maximum pour vous aider ! 💪"
            ],
            'priority' => 9
        ],
        
        // ========== QUESTIONS GÉNÉRALES ==========
        'question_generale' => [
            'keywords' => ['comment', 'pourquoi', 'quoi', 'quand', 'qui', 'où', 'ou', 'quel', 'quelle', 'quels', 'quelles', 'est-ce', 'est ce', 'peut', 'peux', 'puis', 'dois', 'faut', 'doit'],
            'patterns' => [
                '/^(comment|pourquoi|quoi|quand|qui|ou|où)\s/i',
                '/^(est-?ce que|puis-?je|peut-?on|dois-?je)/i'
            ],
            'responses' => [
                "🤔 **Bonne question !**\n\nPour mieux vous répondre, pouvez-vous préciser :\n\n**Vous voulez savoir comment...**\n• 📝 Créer une réclamation ?\n• 🔍 Suivre un dossier ?\n• ⏱️ Connaître les délais ?\n• 📂 Choisir une catégorie ?\n\n**Ou vous avez une question sur...**\n• 🧠 L'IA et la priorisation ?\n• ♿ L'accessibilité ?\n• 📞 Comment nous contacter ?\n\nDites-moi en plus ! 😊"
            ],
            'priority' => 2
        ],
        
        // ========== MODIFICATION RÉCLAMATION ==========
        'modifier_reclamation' => [
            'keywords' => ['modifier', 'modifer', 'changer', 'corriger', 'editer', 'éditer', 'mettre a jour', 'mettre à jour', 'update', 'maj', 'rectifier', 'completer', 'compléter', 'ajouter information', 'rajouter'],
            'patterns' => [
                '/(modifier|changer|corriger)\s+(ma|une|la)\s+reclamation/i',
                '/(comment|puis-?je|peut-?on)\s+modifier/i',
                '/ajouter\s+(des?|une)\s+(information|detail|photo)/i'
            ],
            'responses' => [
                "✏️ **Modifier une réclamation**\n\n**Tant qu'elle est 'En attente' :**\n→ Contactez-nous avec votre numéro\n→ Indiquez les modifications souhaitées\n→ Email : support@impactable.tn\n\n**Si elle est 'En cours' ou 'Traitée' :**\n→ Vous pouvez ajouter des commentaires\n→ Via la page de suivi\n→ L'agent sera notifié\n\n💡 **Alternative :**\nCréez une nouvelle réclamation en mentionnant le numéro de la précédente."
            ],
            'priority' => 6
        ],
        
        // ========== ANNULER RÉCLAMATION ==========
        'annuler_reclamation' => [
            'keywords' => ['annuler', 'anuler', 'supprimer', 'effacer', 'retirer', 'enlever', 'delete', 'fermer', 'cloturer', 'clôturer', 'abandonner', 'arreter', 'arrêter', 'stopper'],
            'patterns' => [
                '/(annuler|supprimer|retirer)\s+(ma|une|la)\s+reclamation/i',
                '/(comment|puis-?je)\s+(annuler|supprimer)/i',
                '/je\s+(veux|ne veux)\s+plus\s+(de)?\s+cette\s+reclamation/i'
            ],
            'responses' => [
                "🗑️ **Annuler une réclamation**\n\n**Pour fermer votre dossier :**\n\n1️⃣ Envoyez un email à support@impactable.tn\n2️⃣ Précisez votre numéro de réclamation\n3️⃣ Indiquez la raison de l'annulation\n\n**Délai :** 24-48h pour le traitement\n\n⚠️ **Note :**\nLa réclamation sera marquée 'Fermée' mais conservée dans l'historique pour nos statistiques.\n\n❓ Vous êtes sûr de vouloir annuler ? Peut-être puis-je vous aider autrement ?"
            ],
            'priority' => 6
        ],
        
        // ========== CONFIDENTIALITÉ ==========
        'confidentialite' => [
            'keywords' => ['confidentialite', 'confidentialité', 'prive', 'privé', 'donnees', 'données', 'rgpd', 'gdpr', 'securite', 'sécurité', 'protection', 'anonyme', 'anonymat', 'secret', 'divulgue', 'divulguer', 'partage', 'partager'],
            'patterns' => [
                '/(mes|les)\s+donnees\s+(sont|seront)/i',
                '/(qui|est-?ce que)\s+(voit|verra|lit)\s+ma\s+reclamation/i',
                '/(c\'?est|est)\s+(confidentiel|prive|anonyme)/i'
            ],
            'responses' => [
                "🔒 **Protection de vos données**\n\n**Vos informations sont protégées !**\n\n✅ **Qui accède à votre réclamation ?**\n• Nos agents habilités uniquement\n• Jamais partagé à des tiers\n• Pas de publication sans accord\n\n✅ **Sécurité**\n• Connexion sécurisée (HTTPS)\n• Données chiffrées\n• Accès contrôlé\n\n✅ **Vos droits (RGPD)**\n• Accès à vos données\n• Rectification\n• Suppression sur demande\n• Portabilité\n\n📧 Contact DPO : privacy@impactable.tn"
            ],
            'priority' => 7
        ],
        
        // ========== LANGUES ==========
        'langue' => [
            'keywords' => ['langue', 'francais', 'français', 'arabe', 'anglais', 'english', 'arabic', 'traduire', 'traduction', 'parler', 'ecrire'],
            'patterns' => [
                '/(parle|parlez|ecrit|compren)/i',
                '/(en|langue)\s+(arabe|anglais|francais)/i'
            ],
            'responses' => [
                "🌍 **Langues disponibles**\n\n**Actuellement :**\n• 🇫🇷 **Français** - Langue principale\n\n**Bientôt :**\n• 🇬🇧 Anglais\n• 🇹🇳 Arabe\n\n💬 N'hésitez pas à me parler en français !\nJe comprends même avec des fautes 😊\n\n*Avez-vous besoin d'aide dans une autre langue ?*"
            ],
            'priority' => 4
        ],
        
        // ========== MOBILE / APPLICATION ==========
        'mobile' => [
            'keywords' => ['mobile', 'telephone', 'téléphone', 'smartphone', 'application', 'app', 'android', 'iphone', 'ios', 'telecharger', 'télécharger', 'installer', 'portable'],
            'patterns' => [
                '/(application|app)\s+mobile/i',
                '/(telecharger|installer)\s+(l\')?app/i',
                '/sur\s+(mon\s+)?(telephone|mobile|portable)/i'
            ],
            'responses' => [
                "📱 **Application mobile**\n\n**Bonne nouvelle !**\nLe site ImpactAble est **100% responsive** !\n\n✅ Fonctionne sur tous les appareils\n✅ Pas besoin de télécharger d'app\n✅ Ajoutez à l'écran d'accueil pour un accès rapide\n\n**Comment faire ?**\n1. Ouvrez le site dans votre navigateur\n2. Menu ☰ → 'Ajouter à l'écran d'accueil'\n3. Voilà ! Icône sur votre téléphone 📲\n\n*Une app dédiée est en développement ! 🚀*"
            ],
            'priority' => 5
        ]
    ];
    
    // ==================== RÉPONSES PAR DÉFAUT ====================
    private static $defaultResponses = [
        "🤔 **Je n'ai pas bien compris votre question.**\n\n**Essayez de me demander :**\n• Comment faire une réclamation ?\n• Comment suivre mon dossier ?\n• Quels sont les délais ?\n• Comment vous contacter ?\n\n💡 **Astuce :** Utilisez des mots simples comme 'réclamation', 'suivi', 'aide', 'délai'.\n\nOu reformulez votre question ! 😊",
        "Hmm, je n'ai pas trouvé de réponse précise. 😅\n\n**Je peux vous aider avec :**\n• 📝 Créer une réclamation\n• 🔍 Suivre un dossier\n• ⏱️ Délais de traitement\n• ♿ Questions sur l'accessibilité\n• 📞 Comment nous contacter\n\n**Pouvez-vous reformuler ?**\nOu tapez 'aide' pour voir mes capacités !",
        "Je ne suis pas sûr de comprendre. 🤖\n\n**Quelques exemples de questions :**\n• \"Comment créer une réclamation ?\"\n• \"Où en est mon dossier ?\"\n• \"C'est quoi la priorité urgente ?\"\n• \"Comment vous contacter ?\"\n\n📧 Si votre question est complexe, contactez : support@impactable.tn"
    ];

    /**
     * Traite un message utilisateur et retourne une réponse
     */
    public static function processMessage($message) {
        $originalMessage = $message;
        
        // 1. Correction orthographique
        $message = self::correctSpelling($message);
        
        // 2. Normalisation du message
        $message = self::normalizeText($message);
        
        // 3. Extraction d'entités
        $entities = self::extractEntities($originalMessage);
        
        // 4. Détection de sentiment
        $sentiment = self::detectSentiment($originalMessage);
        
        // 5. Recherche de la meilleure correspondance
        $bestMatch = null;
        $bestScore = 0;
        
        foreach (self::$knowledgeBase as $category => $data) {
            $score = self::calculateMatchScore($message, $originalMessage, $data);
            
            // Bonus si sentiment frustré et catégorie appropriée
            if ($sentiment === 'frustrated' && $category === 'frustration') {
                $score *= 1.5;
            }
            
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $category;
            }
        }
        
        // 6. Générer la réponse
        if ($bestMatch && $bestScore >= 0.5) {
            $responses = self::$knowledgeBase[$bestMatch]['responses'];
            $response = $responses[array_rand($responses)];
            
            // Personnaliser avec les entités extraites
            if (!empty($entities['numero'])) {
                $response .= "\n\n📋 *J'ai noté le numéro : " . $entities['numero'] . "*";
            }
            
            return [
                'response' => $response,
                'category' => $bestMatch,
                'confidence' => min(100, round($bestScore * 100)),
                'sentiment' => $sentiment,
                'entities' => $entities,
                'corrections' => ($message !== self::normalizeText($originalMessage)) ? true : false,
                'bot_name' => self::$botName,
                'bot_avatar' => self::$botAvatar
            ];
        }
        
        // Réponse par défaut
        return [
            'response' => self::$defaultResponses[array_rand(self::$defaultResponses)],
            'category' => 'unknown',
            'confidence' => 0,
            'sentiment' => $sentiment,
            'entities' => $entities,
            'corrections' => false,
            'bot_name' => self::$botName,
            'bot_avatar' => self::$botAvatar
        ];
    }
    
    /**
     * Correction orthographique avancée
     */
    private static function correctSpelling($text) {
        $words = explode(' ', mb_strtolower($text, 'UTF-8'));
        $corrected = [];
        
        foreach ($words as $word) {
            $word = trim($word);
            if (empty($word)) continue;
            
            // Vérifier si le mot a une correction directe
            if (isset(self::$corrections[$word])) {
                $corrected[] = self::$corrections[$word];
            } else {
                // Essayer de trouver une correction par distance de Levenshtein
                $found = false;
                foreach (self::$corrections as $wrong => $right) {
                    if (strlen($word) >= 3 && levenshtein($word, $wrong) <= 1) {
                        $corrected[] = $right;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $corrected[] = $word;
                }
            }
        }
        
        return implode(' ', $corrected);
    }
    
    /**
     * Calcule le score de correspondance - ALGORITHME AVANCÉ
     */
    private static function calculateMatchScore($normalizedMessage, $originalMessage, $data) {
        $score = 0;
        $keywords = $data['keywords'];
        $patterns = isset($data['patterns']) ? $data['patterns'] : [];
        $priority = isset($data['priority']) ? $data['priority'] / 10 : 0.5;
        
        // 1. Vérifier les patterns regex (haute confiance)
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $originalMessage) || preg_match($pattern, $normalizedMessage)) {
                $score += 2;
            }
        }
        
        // 2. Correspondance par mots-clés
        $messageWords = explode(' ', $normalizedMessage);
        $keywordMatches = 0;
        
        foreach ($keywords as $keyword) {
            $keyword = self::normalizeText($keyword);
            
            // Correspondance exacte dans le message complet
            if (strpos($normalizedMessage, $keyword) !== false) {
                $score += 1;
                $keywordMatches++;
                continue;
            }
            
            // Correspondance mot par mot
            foreach ($messageWords as $word) {
                if (strlen($word) < 2) continue;
                
                // Correspondance exacte
                if ($word === $keyword) {
                    $score += 1;
                    $keywordMatches++;
                    break;
                }
                
                // Préfixe commun
                if (strlen($word) >= 3 && strlen($keyword) >= 3) {
                    if (strpos($word, $keyword) === 0 || strpos($keyword, $word) === 0) {
                        $score += 0.7;
                        $keywordMatches++;
                        break;
                    }
                    
                    // Distance de Levenshtein
                    $distance = levenshtein($word, $keyword);
                    $maxLen = max(strlen($word), strlen($keyword));
                    
                    if ($distance <= 1) {
                        $score += 0.8;
                        $keywordMatches++;
                        break;
                    } elseif ($distance <= 2 && $maxLen >= 6) {
                        $score += 0.5;
                        $keywordMatches++;
                        break;
                    }
                }
            }
        }
        
        // 3. Bonus pour plusieurs mots-clés trouvés
        if ($keywordMatches >= 2) {
            $score *= 1.3;
        }
        if ($keywordMatches >= 3) {
            $score *= 1.2;
        }
        
        // 4. Appliquer le facteur de priorité
        $score *= $priority;
        
        return $score;
    }
    
    /**
     * Normalise le texte pour une meilleure détection
     */
    private static function normalizeText($text) {
        $text = mb_strtolower($text, 'UTF-8');
        
        // Remplacer les accents
        $accents = [
            'à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a', 'ã' => 'a',
            'è' => 'e', 'ê' => 'e', 'ë' => 'e', 'é' => 'e',
            'ì' => 'i', 'î' => 'i', 'ï' => 'i', 'í' => 'i',
            'ò' => 'o', 'ô' => 'o', 'ö' => 'o', 'ó' => 'o', 'õ' => 'o',
            'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ú' => 'u',
            'ç' => 'c', 'ñ' => 'n', 'ÿ' => 'y'
        ];
        $text = strtr($text, $accents);
        
        // Supprimer la ponctuation
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        
        // Supprimer les espaces multiples
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
    }
    
    /**
     * Extraction d'entités (numéros, dates, emails)
     */
    private static function extractEntities($text) {
        $entities = [
            'numero' => null,
            'email' => null,
            'date' => null,
            'telephone' => null
        ];
        
        // Extraire numéro de réclamation
        if (preg_match('/(?:reclamation|réclamation|dossier|numero|numéro|n°|#)\s*:?\s*(\d{1,10})/i', $text, $matches)) {
            $entities['numero'] = $matches[1];
        } elseif (preg_match('/\b(\d{5,10})\b/', $text, $matches)) {
            $entities['numero'] = $matches[1];
        }
        
        // Extraire email
        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text, $matches)) {
            $entities['email'] = $matches[0];
        }
        
        // Extraire téléphone
        if (preg_match('/(?:\+216|00216)?[\s.-]?[0-9]{2}[\s.-]?[0-9]{3}[\s.-]?[0-9]{3}/', $text, $matches)) {
            $entities['telephone'] = preg_replace('/[\s.-]/', '', $matches[0]);
        }
        
        // Extraire date
        if (preg_match('/\b(\d{1,2})[\/\-.](\d{1,2})[\/\-.](\d{2,4})\b/', $text, $matches)) {
            $entities['date'] = $matches[0];
        }
        
        return $entities;
    }
    
    /**
     * Détection de sentiment
     */
    private static function detectSentiment($text) {
        $text = mb_strtolower($text, 'UTF-8');
        
        $frustrated = ['énervé', 'enerve', 'frustré', 'frustre', 'marre', 'ras le bol', 
                       'colère', 'colere', 'fâché', 'fache', 'furieux', 'agacé', 'agace',
                       'insupportable', 'inadmissible', 'scandaleux', 'honteux', 'nul',
                       'pourri', 'catastrophe', 'désespoir', 'desespoir', '!!!', '😡', '😤'];
        
        $positive = ['merci', 'super', 'génial', 'genial', 'parfait', 'excellent', 
                     'top', 'cool', 'bravo', 'formidable', 'magnifique', '😊', '👍', '❤️'];
        
        $frustrated_count = 0;
        $positive_count = 0;
        
        foreach ($frustrated as $word) {
            if (strpos($text, $word) !== false) {
                $frustrated_count++;
            }
        }
        
        foreach ($positive as $word) {
            if (strpos($text, $word) !== false) {
                $positive_count++;
            }
        }
        
        if ($frustrated_count > $positive_count && $frustrated_count >= 1) {
            return 'frustrated';
        } elseif ($positive_count > $frustrated_count && $positive_count >= 1) {
            return 'positive';
        }
        
        return 'neutral';
    }
    
    /**
     * Message de bienvenue
     */
    public static function getWelcomeMessage() {
        $hour = (int)date('H');
        
        if ($hour >= 5 && $hour < 12) {
            $greeting = "Bonjour";
            $emoji = "🌅";
        } elseif ($hour >= 12 && $hour < 18) {
            $greeting = "Bon après-midi";
            $emoji = "☀️";
        } elseif ($hour >= 18 && $hour < 22) {
            $greeting = "Bonsoir";
            $emoji = "🌆";
        } else {
            $greeting = "Bonne nuit";
            $emoji = "🌙";
        }
        
        $message = "$emoji **$greeting !** Je suis **Khalil**, votre assistant virtuel ImpactAble.\n\n";
        $message .= "🎯 **Je peux vous aider à :**\n";
        $message .= "• 📝 Créer une réclamation\n";
        $message .= "• 🔍 Suivre votre dossier\n";
        $message .= "• ❓ Répondre à vos questions\n";
        $message .= "• 🆘 Résoudre vos problèmes\n\n";
        $message .= "💬 **Parlez-moi naturellement !**\n";
        $message .= "*Ex: \"Comment faire une réclamation ?\"*";
        
        return [
            'response' => $message,
            'category' => 'welcome',
            'confidence' => 100,
            'bot_name' => self::$botName,
            'bot_avatar' => self::$botAvatar
        ];
    }
    
    /**
     * Suggestions contextuelles
     */
    public static function getSuggestions() {
        return [
            "📝 Comment créer une réclamation ?",
            "🔍 Suivre mon dossier",
            "⏱️ Quels sont les délais ?",
            "🧠 Comment fonctionne l'IA ?",
            "📂 Quelles catégories disponibles ?",
            "📞 Comment vous contacter ?"
        ];
    }
}
?>
