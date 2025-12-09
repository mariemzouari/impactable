<?php
/**
 * ============================================
 * 🧠 SYSTÈME DE DÉTECTION D'ÉMOTIONS AVANCÉ
 * ============================================
 * ImpactAble - Analyse Émotionnelle Intelligente
 * Version 1.0
 * 
 * Détecte : Colère, Frustration, Tristesse, Anxiété, Urgence, Neutre, Positif
 * Calcule : Intensité, Score, Recommandations
 */

class EmotionDetector {
    
    // ==================== DICTIONNAIRE D'ÉMOTIONS ====================
    
    private static $emotionPatterns = [
        'colere' => [
            'emoji' => '😠',
            'label' => 'Colère',
            'couleur' => '#E53935',
            'couleur_bg' => '#FFEBEE',
            'priorite_auto' => 'Urgente',
            'keywords' => [
                'inadmissible' => 10,
                'scandaleux' => 10,
                'honteux' => 9,
                'inacceptable' => 9,
                'révoltant' => 10,
                'furieux' => 10,
                'furieuse' => 10,
                'énervé' => 8,
                'énervée' => 8,
                'enerve' => 8,
                'enervé' => 8,
                'fâché' => 8,
                'fâchée' => 8,
                'fache' => 8,
                'faché' => 8,
                'exaspéré' => 9,
                'exaspere' => 9,
                'ras le bol' => 9,
                'ras-le-bol' => 9,
                'marre' => 7,
                'j\'en ai marre' => 9,
                'excédé' => 8,
                'en colère' => 10,
                'en colere' => 10,
                'je déteste' => 9,
                'je deteste' => 9,
                'n\'importe quoi' => 7,
                'nimporte quoi' => 7,
                'c\'est nul' => 8,
                'nul' => 5,
                'nulle' => 5,
                'pourri' => 7,
                'pourrie' => 7,
                'honte à vous' => 10,
                'honte a vous' => 10,
                'je vais porter plainte' => 10,
                'porter plainte' => 9,
                'avocat' => 8,
                'justice' => 7,
                'procès' => 9,
                'proces' => 9,
                'arnaque' => 10,
                'arnaquer' => 10,
                'voleurs' => 10,
                'voleur' => 9,
                'menteurs' => 9,
                'menteur' => 8,
                'incompétent' => 9,
                'incompétents' => 9,
                'incompetent' => 9,
                'incapable' => 8,
                'incapables' => 8,
                'dégueulasse' => 10,
                'degueulasse' => 10,
                'débile' => 8,
                'stupide' => 7,
                'idiot' => 7,
                'idiots' => 7,
                'crétin' => 8,
                'abusé' => 7,
                'abuse' => 7,
                'hors de question' => 8,
                'intolerable' => 9,
                'intolérable' => 9,
                'insupportable' => 9,
                'je refuse' => 7,
                'c\'est du vol' => 9,
                'escroquerie' => 10,
                'escrocs' => 10,
                'enragé' => 10,
                'enragée' => 10,
                'fou de rage' => 10,
                'folle de rage' => 10,
                // Problèmes graves avec colère
                'catastrophe' => 9,
                'catastrophique' => 9,
                'désastre' => 9,
                'desastre' => 9,
                'désastreux' => 9,
                'lamentable' => 8,
                'pitoyable' => 8,
                'minable' => 8,
                'ridicule' => 7,
                'c\'est ridicule' => 8,
                'absolument nul' => 9,
                'vraiment nul' => 8,
                'totalement nul' => 9,
                'zéro' => 6,
                'note zéro' => 8,
                '0/10' => 8,
                '0 sur 10' => 8,
                '1/10' => 7,
                'pire' => 6,
                'le pire' => 8,
                'c\'est le pire' => 9,
                'jamais vu ça' => 8,
                'du jamais vu' => 8,
                'impensable' => 8,
                'inconcevable' => 8,
                'invraisemblable' => 7,
                'irresponsable' => 9,
                'négligence' => 8,
                'negligence' => 8,
                'faute grave' => 9,
                'erreur impardonnable' => 10,
                'impardonnable' => 9,
                'inexcusable' => 9,
                'injustifiable' => 8,
                'vous moquez' => 8,
                'vous vous moquez' => 9,
                'c\'est une blague' => 7,
                'c\'est une honte' => 9,
                'quelle honte' => 9,
                'honteux' => 8,
                'deplorable' => 8,
                'déplorable' => 8
            ],
            'patterns' => [
                '/[!]{3,}/' => 5,  // !!! multiples
                '/[A-ZÉÈÊÀÂÙÛÔÎÇ]{5,}/' => 4,  // MAJUSCULES
                '/\b(jamais|plus jamais)\b/i' => 6,
                '/\b(nul|nulle|nulles|nuls)\b/i' => 5,
                '/\?\?+/' => 3  // ?? multiples (frustration/colère)
            ],
            'reponse_type' => 'empathique_urgent'
        ],
        
        'frustration' => [
            'emoji' => '😤',
            'label' => 'Frustration',
            'couleur' => '#FB8C00',
            'couleur_bg' => '#FFF3E0',
            'priorite_auto' => 'Moyenne',
            'keywords' => [
                'encore une fois' => 8,
                'toujours le même' => 8,
                'toujours le meme' => 8,
                'toujours pareil' => 8,
                'ça fait plusieurs fois' => 9,
                'ca fait plusieurs fois' => 9,
                'combien de fois' => 8,
                'j\'en ai assez' => 8,
                'assez' => 5,
                'pas normal' => 6,
                'anormal' => 6,
                'c\'est pas possible' => 7,
                'pas possible' => 6,
                'je comprends pas' => 5,
                'je comprend pas' => 5,
                'personne ne répond' => 8,
                'personne ne repond' => 8,
                'personne ne m\'aide' => 8,
                'pas d\'aide' => 6,
                'ignoré' => 7,
                'ignorée' => 7,
                'ignore' => 6,
                'on m\'ignore' => 8,
                'sans réponse' => 7,
                'sans reponse' => 7,
                'aucune nouvelle' => 7,
                'attend depuis' => 6,
                'j\'attends' => 5,
                'j\'attend' => 5,
                'toujours rien' => 7,
                'pas de solution' => 7,
                'pas de réponse' => 7,
                'pas de reponse' => 7,
                'ça traîne' => 6,
                'ca traine' => 6,
                'trop long' => 6,
                'lenteur' => 5,
                'retard' => 5,
                'bloqué' => 6,
                'bloquée' => 6,
                'bloque' => 5,
                'coincé' => 6,
                'coincée' => 6,
                'galère' => 7,
                'galere' => 7,
                'problème' => 4,
                'probleme' => 4,
                'bug' => 5,
                'bugue' => 5,
                'ne fonctionne pas' => 7,
                'fonctionne pas' => 6,
                'marche pas' => 6,
                'ne marche pas' => 7,
                'cassé' => 6,
                'casse' => 5,
                'panne' => 6,
                'erreur' => 5,
                'défaillance' => 6,
                'plantage' => 6,
                'plante' => 5,
                'impossible de' => 6,
                'je n\'arrive pas' => 6,
                'j\'arrive pas' => 6,
                'n\'y arrive pas' => 6,
                'difficile' => 4,
                'compliqué' => 5,
                'complique' => 5,
                // Mots courants de problèmes
                'grave' => 7,
                'très grave' => 9,
                'c\'est grave' => 8,
                'faute' => 5,
                'votre faute' => 8,
                'c\'est votre faute' => 9,
                'c\'est de votre faute' => 9,
                'ma faute' => 3,
                'erreur grave' => 8,
                'grosse erreur' => 8,
                'énorme erreur' => 9,
                'souci' => 4,
                'soucis' => 4,
                'gros souci' => 6,
                'gros problème' => 7,
                'gros probleme' => 7,
                'sérieux problème' => 8,
                'vrai problème' => 7,
                'c\'est un problème' => 6,
                'y a un problème' => 6,
                'il y a un problème' => 6,
                'ça pose problème' => 6,
                'problématique' => 5,
                'dysfonctionnement' => 6,
                'défaut' => 5,
                'defaut' => 5,
                'incident' => 5,
                'anomalie' => 5,
                'mauvais' => 4,
                'mauvaise' => 4,
                'mal fait' => 6,
                'mal fonctionné' => 6,
                'raté' => 5,
                'ratée' => 5,
                'loupé' => 5,
                'échec' => 6,
                'echoué' => 6,
                'échoué' => 6,
                'fail' => 5,
                'failed' => 5,
                'ko' => 4,
                'hs' => 5,
                'hors service' => 6,
                'indisponible' => 5,
                'inaccessible' => 6,
                'bloquant' => 7,
                'critique' => 7,
                'urgent' => 6,
                'urgence' => 7
            ],
            'patterns' => [
                '/encore[!?]+/i' => 4,
                '/toujours[!?]+/i' => 4,
                '/\b(\d+)\s*(fois|jours|semaines|mois)\b/i' => 5,
                '/probl[eè]me/i' => 4
            ],
            'reponse_type' => 'comprehensif'
        ],
        
        'tristesse' => [
            'emoji' => '😢',
            'label' => 'Tristesse',
            'couleur' => '#5C6BC0',
            'couleur_bg' => '#E8EAF6',
            'priorite_auto' => 'Moyenne',
            'keywords' => [
                'déçu' => 7,
                'déçue' => 7,
                'decu' => 7,
                'decue' => 7,
                'déception' => 8,
                'deception' => 8,
                'triste' => 8,
                'tristesse' => 8,
                'malheureux' => 8,
                'malheureuse' => 8,
                'dommage' => 5,
                'c\'est dommage' => 6,
                'quel dommage' => 6,
                'regrettable' => 6,
                'je regrette' => 6,
                'regrette' => 5,
                'mauvaise expérience' => 7,
                'mauvaise experience' => 7,
                'expérience négative' => 7,
                'experience negative' => 7,
                'pas satisfait' => 6,
                'pas satisfaite' => 6,
                'insatisfait' => 6,
                'insatisfaite' => 6,
                'me fait de la peine' => 8,
                'fait de la peine' => 7,
                'ça me touche' => 6,
                'ca me touche' => 6,
                'blessé' => 7,
                'blessée' => 7,
                'blesse' => 6,
                'humilié' => 9,
                'humiliée' => 9,
                'humilie' => 8,
                'honte' => 7,
                'j\'ai honte' => 8,
                'embarrassé' => 6,
                'embarrassée' => 6,
                'gêné' => 5,
                'gênée' => 5,
                'dur' => 4,
                'pénible' => 5,
                'penible' => 5,
                'navré' => 6,
                'navrée' => 6,
                'navrant' => 6,
                'attristé' => 7,
                'attristée' => 7,
                'chagrin' => 7,
                'mélancolique' => 6,
                'abattu' => 7,
                'abattue' => 7,
                'dépité' => 7,
                'dépitée' => 7,
                'découragé' => 7,
                'découragée' => 7,
                'decourage' => 7,
                'démotivé' => 7,
                'démotivée' => 7
            ],
            'patterns' => [
                '/😢|😭|💔/u' => 5
            ],
            'reponse_type' => 'empathique_doux'
        ],
        
        'anxiete' => [
            'emoji' => '😰',
            'label' => 'Anxiété',
            'couleur' => '#7E57C2',
            'couleur_bg' => '#EDE7F6',
            'priorite_auto' => 'Moyenne',
            'keywords' => [
                'inquiet' => 7,
                'inquiète' => 7,
                'inquiete' => 7,
                'inquiétude' => 7,
                'inquietude' => 7,
                'anxieux' => 8,
                'anxieuse' => 8,
                'angoissé' => 9,
                'angoissée' => 9,
                'angoisse' => 8,
                'stressé' => 7,
                'stressée' => 7,
                'stresse' => 6,
                'stress' => 6,
                'peur' => 8,
                'j\'ai peur' => 9,
                'fait peur' => 7,
                'effrayé' => 8,
                'effrayée' => 8,
                'effraye' => 7,
                'effrayant' => 7,
                'paniqué' => 9,
                'paniquée' => 9,
                'panique' => 8,
                'je panique' => 9,
                'ne sais pas quoi faire' => 7,
                'sais pas quoi faire' => 7,
                'ne sais plus' => 7,
                'sais plus quoi faire' => 8,
                'perdu' => 6,
                'perdue' => 6,
                'je suis perdu' => 7,
                'je suis perdue' => 7,
                'désemparé' => 8,
                'désemparée' => 8,
                'desempare' => 7,
                'que faire' => 5,
                'quoi faire' => 5,
                'comment faire' => 4,
                'au secours' => 9,
                'sos' => 8,
                'help' => 6,
                'aidez-moi' => 8,
                'aidez moi' => 8,
                'aide-moi' => 8,
                'aide moi' => 8,
                'besoin d\'aide' => 7,
                'j\'ai besoin d\'aide' => 8,
                'urgent' => 6,
                'urgence' => 7,
                'c\'est urgent' => 8,
                'très urgent' => 9,
                'tres urgent' => 9,
                'vite' => 5,
                'rapidement' => 4,
                'au plus vite' => 6,
                'dès que possible' => 5,
                'des que possible' => 5,
                'asap' => 6,
                // Mots liés aux conséquences graves
                'conséquence' => 5,
                'consequence' => 5,
                'conséquences graves' => 8,
                'risque' => 5,
                'risques' => 5,
                'dangereux' => 7,
                'danger' => 7,
                'en danger' => 8,
                'menace' => 6,
                'menacé' => 7,
                'menacée' => 7,
                'crainte' => 6,
                'crains' => 6,
                'je crains' => 7,
                'redoute' => 6,
                'je redoute' => 7,
                'appréhende' => 6,
                'j\'appréhende' => 7,
                'nerveux' => 6,
                'nerveuse' => 6,
                'tendu' => 5,
                'tendue' => 5,
                'préoccupé' => 6,
                'préoccupée' => 6,
                'preoccupe' => 5,
                'soucieux' => 6,
                'soucieuse' => 6
            ],
            'patterns' => [
                '/\?{2,}/' => 3,  // ?? multiples
                '/s\'il vous pla[iî]t/i' => 2,
                '/svp/i' => 2
            ],
            'reponse_type' => 'rassurant'
        ],
        
        'detresse' => [
            'emoji' => '😭',
            'label' => 'Détresse',
            'couleur' => '#8E24AA',
            'couleur_bg' => '#F3E5F5',
            'priorite_auto' => 'Urgente',
            'keywords' => [
                'désespéré' => 10,
                'désespérée' => 10,
                'désespoir' => 10,
                'à bout' => 9,
                'n\'en peux plus' => 9,
                'je craque' => 9,
                'épuisé' => 7,
                'épuisée' => 7,
                'plus de force' => 8,
                'abandonné' => 8,
                'abandonnée' => 8,
                'seul' => 6,
                'seule' => 6,
                'personne ne m\'écoute' => 9,
                'personne ne comprend' => 8,
                'impossible' => 6,
                'sans espoir' => 10,
                'plus d\'espoir' => 10,
                'je ne sais plus quoi faire' => 9,
                'c\'est trop' => 7,
                'trop c\'est trop' => 9
            ],
            'patterns' => [],
            'reponse_type' => 'urgent_humain'
        ],
        
        'positif' => [
            'emoji' => '😊',
            'label' => 'Positif',
            'couleur' => '#43A047',
            'couleur_bg' => '#E8F5E9',
            'priorite_auto' => 'Faible',
            'keywords' => [
                'merci' => 5,
                'remercie' => 5,
                'content' => 6,
                'contente' => 6,
                'contant' => 6,
                'contante' => 6,
                'satisfait' => 7,
                'satisfaite' => 7,
                'heureux' => 7,
                'heureuse' => 7,
                'super' => 6,
                'génial' => 7,
                'genial' => 7,
                'excellent' => 7,
                'parfait' => 7,
                'bravo' => 6,
                'beau' => 5,
                'belle' => 5,
                'beau travail' => 8,
                'bon travail' => 7,
                'bien fait' => 6,
                'avantage' => 4,
                'avantageux' => 5,
                'magnifique' => 7,
                'formidable' => 7,
                'fantastique' => 7,
                'incroyable' => 6,
                'impressionnant' => 7,
                'efficace' => 6,
                'rapide' => 4,
                'pratique' => 5,
                'utile' => 5,
                'facile' => 4,
                'simple' => 4,
                'agréable' => 6,
                'plaisant' => 5,
                'j\'aime' => 6,
                'j\'adore' => 8,
                'adore' => 7,
                'aime bien' => 5,
                'cool' => 5,
                'top' => 6,
                'nickel' => 6,
                'impeccable' => 7,
                'félicitations' => 7,
                'chapeau' => 6,
                'respect' => 5,
                'apprecie' => 6,
                'apprécie' => 6,
                'bien joué' => 6,
                'réactif' => 5,
                'professionnel' => 5,
                'je recommande' => 7,
                'très bien' => 6,
                'trop bien' => 7,
                'c\'est bien' => 5,
                'ça marche' => 4,
                'fonctionne' => 4,
                'résolu' => 6,
                'bonne continuation' => 4,
                'cordialement' => 2,
                'bonne journée' => 3,
                'content de' => 7,
                'ravi' => 7,
                'ravie' => 7,
                'enchanté' => 6,
                'enchantée' => 6,
                'au top' => 6,
                'génialissime' => 8,
                'merci beaucoup' => 7,
                'grand merci' => 7,
                'mille merci' => 8,
                'je vous remercie' => 6,
                'c\'est super' => 7,
                'c\'est génial' => 7,
                'c\'est parfait' => 7,
                'rien à dire' => 5,
                'sans faute' => 6,
                'je suis content' => 8,
                'je suis contente' => 8,
                'je suis satisfait' => 8,
                'je suis satisfaite' => 8,
                'je suis ravi' => 8,
                'je suis ravie' => 8
            ],
            'patterns' => [
                '/😊|😃|😄|👍|❤️|🙏|✨/u' => 4
            ],
            'reponse_type' => 'reconnaissant'
        ],
        
        'neutre' => [
            'emoji' => '😐',
            'label' => 'Neutre',
            'couleur' => '#78909C',
            'couleur_bg' => '#ECEFF1',
            'priorite_auto' => 'Normale',
            'keywords' => [],
            'patterns' => [],
            'reponse_type' => 'standard'
        ]
    ];
    
    // ==================== RÉPONSES AUTOMATIQUES PAR ÉMOTION ====================
    
    private static $autoResponses = [
        'empathique_urgent' => [
            "Nous comprenons parfaitement votre colère et prenons votre situation très au sérieux. Votre réclamation est désormais traitée en PRIORITÉ ABSOLUE.",
            "Votre mécontentement est totalement légitime. Nous avons immédiatement escaladé votre dossier et un responsable va vous contacter personnellement.",
            "Nous sommes sincèrement désolés pour cette situation inacceptable. Votre réclamation a été classée URGENTE et sera traitée dans les 24h."
        ],
        'comprehensif' => [
            "Nous comprenons votre frustration face à cette situation qui perdure. Soyez assuré(e) que nous mettons tout en œuvre pour y remédier rapidement.",
            "Il est tout à fait normal d'être frustré(e) dans cette situation. Nous prenons votre réclamation très au sérieux et accélérons son traitement.",
            "Nous sommes conscients que cette attente est difficile. Votre dossier est maintenant prioritaire."
        ],
        'empathique_doux' => [
            "Nous sommes sincèrement désolés que vous ayez vécu cette expérience décevante. Votre ressenti compte beaucoup pour nous.",
            "Nous regrettons profondément cette situation. Nous allons tout faire pour transformer cette mauvaise expérience.",
            "Votre déception nous touche et nous nous engageons à faire mieux. Merci de nous donner l'opportunité de nous rattraper."
        ],
        'rassurant' => [
            "Ne vous inquiétez pas, nous sommes là pour vous aider. Votre situation va être prise en charge rapidement.",
            "Nous comprenons votre inquiétude et allons tout faire pour résoudre cette situation. Vous n'êtes pas seul(e).",
            "Restez serein(e), notre équipe s'occupe de votre dossier. Nous vous tenons informé(e) de chaque avancée."
        ],
        'urgent_humain' => [
            "Nous entendons votre détresse et votre dossier devient notre priorité absolue. Un membre de notre équipe va vous contacter très rapidement.",
            "Votre situation nous préoccupe sincèrement. Nous mobilisons immédiatement nos ressources pour vous aider.",
            "Nous sommes là pour vous. Votre réclamation est traitée en urgence maximale et un suivi personnalisé est mis en place."
        ],
        'reconnaissant' => [
            "Merci pour votre retour positif ! C'est un plaisir de pouvoir vous aider.",
            "Nous sommes ravis que vous soyez satisfait(e). N'hésitez pas si vous avez d'autres questions.",
            "Votre satisfaction est notre plus belle récompense. Merci de votre confiance !"
        ],
        'standard' => [
            "Nous avons bien reçu votre réclamation et la traitons avec attention.",
            "Votre demande a été enregistrée. Notre équipe l'examine et reviendra vers vous rapidement.",
            "Merci pour votre message. Nous vous répondrons dans les meilleurs délais."
        ]
    ];
    
    /**
     * 🎯 Analyse complète d'un texte pour détecter l'émotion
     */
    public static function analyser($texte) {
        $texteOriginal = $texte;
        $texteNormalise = self::normaliserTexte($texte);
        $texteCorrige = self::corrigerOrthographe($texteNormalise);
        
        $scores = [];
        $details = [];
        
        // Analyser chaque émotion
        foreach (self::$emotionPatterns as $emotion => $data) {
            if ($emotion === 'neutre') continue;
            
            $score = 0;
            $motsDetectes = [];
            
            // Vérifier les mots-clés (sur texte original et corrigé)
            foreach ($data['keywords'] as $keyword => $points) {
                $keywordNorm = self::normaliserTexte($keyword);
                // Chercher dans le texte normalisé ET le texte corrigé
                if (mb_stripos($texteNormalise, $keywordNorm) !== false || 
                    mb_stripos($texteCorrige, $keywordNorm) !== false) {
                    $score += $points;
                    $motsDetectes[] = $keyword;
                }
            }
            
            // Vérifier les patterns regex
            foreach ($data['patterns'] as $pattern => $points) {
                if (preg_match($pattern, $texteOriginal)) {
                    $score += $points;
                }
            }
            
            if ($score > 0) {
                $scores[$emotion] = $score;
                $details[$emotion] = [
                    'score' => $score,
                    'mots' => $motsDetectes,
                    'data' => $data
                ];
            }
        }
        
        // Déterminer l'émotion dominante
        if (empty($scores)) {
            $emotionDominante = 'neutre';
            $scoreMax = 0;
        } else {
            $emotionDominante = array_keys($scores, max($scores))[0];
            $scoreMax = max($scores);
        }
        
        // Calculer l'intensité
        $intensite = self::calculerIntensite($scoreMax);
        
        // Obtenir les infos de l'émotion
        $emotionData = self::$emotionPatterns[$emotionDominante];
        
        // Générer la réponse automatique
        $reponseAuto = self::genererReponseAuto($emotionData['reponse_type']);
        
        return [
            'emotion' => $emotionDominante,
            'emoji' => $emotionData['emoji'],
            'label' => $emotionData['label'],
            'couleur' => $emotionData['couleur'],
            'couleur_bg' => $emotionData['couleur_bg'],
            'score' => $scoreMax,
            'intensite' => $intensite,
            'intensite_pourcent' => min(100, $scoreMax * 5),
            'priorite_suggeree' => $emotionData['priorite_auto'],
            'mots_detectes' => $details[$emotionDominante]['mots'] ?? [],
            'toutes_emotions' => $scores,
            'reponse_auto' => $reponseAuto,
            'conseil_agent' => self::getConseilAgent($emotionDominante, $intensite),
            'analyse_complete' => $details
        ];
    }
    
    /**
     * Calcule l'intensité de l'émotion
     */
    private static function calculerIntensite($score) {
        if ($score >= 25) return 'Très forte';
        if ($score >= 15) return 'Forte';
        if ($score >= 8) return 'Moyenne';
        if ($score >= 3) return 'Légère';
        return 'Neutre';
    }
    
    /**
     * Génère une réponse automatique adaptée
     */
    private static function genererReponseAuto($type) {
        $responses = self::$autoResponses[$type] ?? self::$autoResponses['standard'];
        return $responses[array_rand($responses)];
    }
    
    /**
     * Donne un conseil à l'agent selon l'émotion détectée
     */
    private static function getConseilAgent($emotion, $intensite) {
        $conseils = [
            'colere' => [
                'icon' => '⚠️',
                'titre' => 'Client en colère',
                'conseil' => 'Commencez par valider son ressenti. Ne minimisez pas. Proposez une solution concrète rapidement.',
                'a_eviter' => 'Évitez les réponses types et les justifications.'
            ],
            'frustration' => [
                'icon' => '🔄',
                'titre' => 'Client frustré (récurrence)',
                'conseil' => 'Reconnaissez l\'historique du problème. Montrez que vous comprenez que ça dure.',
                'a_eviter' => 'Ne demandez pas de répéter des informations déjà données.'
            ],
            'tristesse' => [
                'icon' => '💙',
                'titre' => 'Client déçu',
                'conseil' => 'Faites preuve d\'empathie sincère. Proposez un geste commercial si possible.',
                'a_eviter' => 'Évitez un ton trop formel ou distant.'
            ],
            'anxiete' => [
                'icon' => '🤝',
                'titre' => 'Client anxieux',
                'conseil' => 'Rassurez-le sur la prise en charge. Donnez des délais précis et tenez-les.',
                'a_eviter' => 'Évitez les incertitudes et les "peut-être".'
            ],
            'detresse' => [
                'icon' => '🚨',
                'titre' => 'Client en détresse',
                'conseil' => 'PRIORITÉ MAXIMALE. Contactez par téléphone si possible. Montrez une présence humaine.',
                'a_eviter' => 'Ne laissez jamais ce client sans réponse rapide.'
            ],
            'positif' => [
                'icon' => '⭐',
                'titre' => 'Client satisfait',
                'conseil' => 'Remerciez chaleureusement. Proposez de laisser un avis.',
                'a_eviter' => 'Ne négligez pas ce message positif.'
            ],
            'neutre' => [
                'icon' => '📋',
                'titre' => 'Message standard',
                'conseil' => 'Réponse professionnelle classique.',
                'a_eviter' => '-'
            ]
        ];
        
        return $conseils[$emotion] ?? $conseils['neutre'];
    }
    
    /**
     * Normalise le texte pour l'analyse
     */
    private static function normaliserTexte($texte) {
        $texte = mb_strtolower($texte, 'UTF-8');
        $texte = preg_replace('/\s+/', ' ', $texte);
        return trim($texte);
    }
    
    /**
     * Dictionnaire de corrections orthographiques courantes
     */
    private static $corrections = [
        // Variations sans accents
        'tres' => 'très',
        'enerve' => 'énervé',
        'fache' => 'fâché',
        'decu' => 'déçu',
        'decue' => 'déçue',
        'genial' => 'génial',
        'penible' => 'pénible',
        'desespere' => 'désespéré',
        'epuise' => 'épuisé',
        'gene' => 'gêné',
        'betise' => 'bêtise',
        
        // Fautes courantes
        'contant' => 'content',
        'contante' => 'contente',
        'satisfer' => 'satisfait',
        'satifait' => 'satisfait',
        'satifé' => 'satisfait',
        'heureus' => 'heureux',
        'heureue' => 'heureuse',
        'deçu' => 'déçu',
        'decéption' => 'déception',
        'deseption' => 'déception',
        'mecontant' => 'mécontent',
        'mecontent' => 'mécontent',
        'merçi' => 'merci',
        'suis contant' => 'suis content',
        'suis contante' => 'suis contente',
        'je suit' => 'je suis',
        'becoup' => 'beaucoup',
        'bocoup' => 'beaucoup',
        'bcp' => 'beaucoup',
        'chouette' => 'super',
        'top' => 'super',
        'pas mal' => 'bien',
        'nickel' => 'parfait',
        'ok' => 'bien',
        'okey' => 'bien',
        'okay' => 'bien',
        'problèmme' => 'problème',
        'probléme' => 'problème',
        'problém' => 'problème',
        'pb' => 'problème',
        'pblm' => 'problème',
        'pblme' => 'problème',
        'prblm' => 'problème',
        'tjrs' => 'toujours',
        'tjs' => 'toujours',
        'pcq' => 'parce que',
        'pk' => 'pourquoi',
        'pq' => 'pourquoi',
        'slt' => 'salut',
        'svp' => 's\'il vous plaît',
        'stp' => 's\'il te plaît',
        
        // Mots liés aux problèmes
        'grav' => 'grave',
        'faut' => 'faute',
        'defau' => 'défaut',
        'ereur' => 'erreur',
        'errer' => 'erreur',
        'érreur' => 'erreur',
        'fote' => 'faute',
        'fotte' => 'faute',
        
        // Émotions
        'colere' => 'colère',
        'colér' => 'colère',
        'triste' => 'triste',
        'trist' => 'triste',
        'heureu' => 'heureux',
        'ravi' => 'ravi',
        'ravie' => 'ravie',
        
        // Abréviations SMS
        'mrc' => 'merci',
        'mrci' => 'merci',
        'bvo' => 'bravo',
        'sup' => 'super',
        'supr' => 'super',
        'tkt' => 'ne t\'inquiète pas',
        'nrv' => 'énervé',
        'nrvé' => 'énervé',
        'ouf' => 'stressant',
        'mdrrr' => 'drôle',
        'mdr' => 'drôle',
        'lol' => 'drôle',
        'ptdr' => 'très drôle',
        'jsuis' => 'je suis',
        'chuis' => 'je suis',
        'jai' => 'j\'ai',
        'jé' => 'j\'ai',
        'kan' => 'quand',
        'tro' => 'trop',
        'bo' => 'beau',
        'bô' => 'beau',
        'vrmt' => 'vraiment',
        'vrmnt' => 'vraiment',
        'vrement' => 'vraiment',
        'vraiement' => 'vraiment',
        
        // Négations
        'pa' => 'pas',
        'pô' => 'pas',
        'po' => 'pas',
        
        // Services
        'serv' => 'service',
        'servic' => 'service',
        'recla' => 'réclamation',
        'reclam' => 'réclamation',
        'reclamation' => 'réclamation',
        'cmd' => 'commande',
        'commde' => 'commande',
        'livr' => 'livraison',
        'livrsn' => 'livraison',
        
        // Intensificateurs
        'vrm' => 'vraiment',
        'absolument' => 'absolument',
        'totalmt' => 'totalement',
        'complètmt' => 'complètement',
        'completement' => 'complètement'
    ];
    
    /**
     * Corrige les fautes d'orthographe courantes
     */
    private static function corrigerOrthographe($texte) {
        $texteCorrige = $texte;
        foreach (self::$corrections as $faute => $correction) {
            $texteCorrige = str_ireplace($faute, $correction, $texteCorrige);
        }
        return $texteCorrige;
    }
    
    /**
     * Retourne toutes les émotions disponibles
     */
    public static function getEmotionsDisponibles() {
        $emotions = [];
        foreach (self::$emotionPatterns as $key => $data) {
            $emotions[$key] = [
                'emoji' => $data['emoji'],
                'label' => $data['label'],
                'couleur' => $data['couleur'],
                'couleur_bg' => $data['couleur_bg']
            ];
        }
        return $emotions;
    }
    
    /**
     * Génère le HTML du badge d'émotion
     */
    public static function getBadgeHTML($emotion, $showLabel = true) {
        $data = self::$emotionPatterns[$emotion] ?? self::$emotionPatterns['neutre'];
        
        $html = '<span class="emotion-badge" style="';
        $html .= 'background: ' . $data['couleur_bg'] . ';';
        $html .= 'color: ' . $data['couleur'] . ';';
        $html .= 'padding: 4px 12px;';
        $html .= 'border-radius: 20px;';
        $html .= 'font-weight: 600;';
        $html .= 'font-size: 0.9em;';
        $html .= 'display: inline-flex;';
        $html .= 'align-items: center;';
        $html .= 'gap: 5px;';
        $html .= '">';
        $html .= $data['emoji'];
        if ($showLabel) {
            $html .= ' ' . $data['label'];
        }
        $html .= '</span>';
        
        return $html;
    }
}
?>

