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
                'fâché' => 8,
                'fâchée' => 8,
                'exaspéré' => 9,
                'ras le bol' => 9,
                'marre' => 7,
                'excédé' => 8,
                'en colère' => 10,
                'je déteste' => 9,
                'n\'importe quoi' => 7,
                'c\'est nul' => 8,
                'honte à vous' => 10,
                'je vais porter plainte' => 10,
                'avocat' => 8,
                'justice' => 7,
                'procès' => 9,
                'arnaque' => 10,
                'voleurs' => 10,
                'menteurs' => 9,
                'incompétent' => 9,
                'incompétents' => 9,
                'incapable' => 8,
                'incapables' => 8
            ],
            'patterns' => [
                '/[!]{3,}/' => 5,  // !!! multiples
                '/[A-ZÉÈÊÀÂÙÛÔÎÇ]{5,}/' => 4,  // MAJUSCULES
                '/\b(jamais|plus jamais)\b/i' => 6
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
                'toujours pareil' => 8,
                'ça fait plusieurs fois' => 9,
                'combien de fois' => 8,
                'j\'en ai assez' => 8,
                'pas normal' => 6,
                'c\'est pas possible' => 7,
                'je comprends pas' => 5,
                'personne ne répond' => 8,
                'personne ne m\'aide' => 8,
                'ignoré' => 7,
                'ignorée' => 7,
                'on m\'ignore' => 8,
                'sans réponse' => 7,
                'aucune nouvelle' => 7,
                'attend depuis' => 6,
                'j\'attends' => 5,
                'toujours rien' => 7,
                'pas de solution' => 7,
                'pas de réponse' => 7,
                'ça traîne' => 6,
                'trop long' => 6,
                'lenteur' => 5,
                'retard' => 5
            ],
            'patterns' => [
                '/encore[!?]+/i' => 4,
                '/toujours[!?]+/i' => 4,
                '/\b(\d+)\s*(fois|jours|semaines|mois)\b/i' => 5
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
                'déception' => 8,
                'triste' => 8,
                'malheureux' => 8,
                'malheureuse' => 8,
                'dommage' => 5,
                'regrettable' => 6,
                'je regrette' => 6,
                'mauvaise expérience' => 7,
                'expérience négative' => 7,
                'pas satisfait' => 6,
                'insatisfait' => 6,
                'me fait de la peine' => 8,
                'ça me touche' => 6,
                'blessé' => 7,
                'blessée' => 7,
                'humilié' => 9,
                'humiliée' => 9,
                'honte' => 7,
                'j\'ai honte' => 8,
                'difficile' => 4,
                'dur' => 4,
                'pénible' => 5
            ],
            'patterns' => [
                '/:(|😢|😭|💔/u' => 5
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
                'inquiétude' => 7,
                'anxieux' => 8,
                'anxieuse' => 8,
                'angoissé' => 9,
                'angoissée' => 9,
                'stressé' => 7,
                'stressée' => 7,
                'peur' => 8,
                'j\'ai peur' => 9,
                'effrayé' => 8,
                'effrayée' => 8,
                'paniqué' => 9,
                'paniquée' => 9,
                'ne sais pas quoi faire' => 7,
                'ne sais plus' => 7,
                'perdu' => 6,
                'perdue' => 6,
                'désemparé' => 8,
                'désemparée' => 8,
                'que faire' => 5,
                'comment faire' => 4,
                'au secours' => 9,
                'help' => 6,
                'aidez-moi' => 8,
                'aide-moi' => 8,
                'besoin d\'aide' => 7,
                'urgent' => 6,
                'urgence' => 7,
                'vite' => 5,
                'rapidement' => 4
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
                'satisfait' => 7,
                'satisfaite' => 7,
                'heureux' => 7,
                'heureuse' => 7,
                'super' => 6,
                'génial' => 7,
                'excellent' => 7,
                'parfait' => 7,
                'bravo' => 6,
                'bien joué' => 6,
                'bon travail' => 6,
                'efficace' => 5,
                'rapide' => 4,
                'réactif' => 5,
                'professionnel' => 5,
                'je recommande' => 7,
                'très bien' => 6,
                'bonne continuation' => 4,
                'cordialement' => 2,
                'bonne journée' => 3
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
        
        $scores = [];
        $details = [];
        
        // Analyser chaque émotion
        foreach (self::$emotionPatterns as $emotion => $data) {
            if ($emotion === 'neutre') continue;
            
            $score = 0;
            $motsDetectes = [];
            
            // Vérifier les mots-clés
            foreach ($data['keywords'] as $keyword => $points) {
                $keywordNorm = self::normaliserTexte($keyword);
                if (mb_stripos($texteNormalise, $keywordNorm) !== false) {
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
        // Garder les accents pour une meilleure détection en français
        $texte = preg_replace('/\s+/', ' ', $texte);
        return trim($texte);
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

