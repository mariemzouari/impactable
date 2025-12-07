<?php
/**
 * Service de Réponse Intelligente Avancé
 * Version 1.0 - ImpactAble
 * 
 * Fonctionnalités:
 * - Génération automatique de réponses
 * - Modèles de réponses par catégorie
 * - Analyse de sentiment
 * - Score de qualité
 * - Suggestions contextuelles
 */

class ReponseIntelligente {
    
    // ==================== MODÈLES DE RÉPONSES PAR CATÉGORIE ====================
    
    private static $templates = [
        'accessibilite' => [
            'introduction' => [
                "Nous avons bien reçu votre signalement concernant un problème d'accessibilité.",
                "Merci de nous avoir alerté sur cette situation d'accessibilité.",
                "Votre réclamation relative à l'accessibilité a été prise en compte avec la plus grande attention."
            ],
            'empathie' => [
                "Nous comprenons parfaitement l'impact que cela peut avoir sur votre quotidien.",
                "L'accessibilité est une priorité absolue pour nous.",
                "Nous sommes conscients de l'importance de ce problème pour votre autonomie."
            ],
            'action' => [
                "Notre équipe technique a été immédiatement mobilisée.",
                "Une intervention est programmée dans les plus brefs délais.",
                "Nous avons transmis votre dossier au service compétent pour action rapide."
            ],
            'conclusion' => [
                "Nous vous tiendrons informé(e) de l'avancement.",
                "N'hésitez pas à nous recontacter pour tout complément.",
                "Votre satisfaction et votre confort sont notre priorité."
            ]
        ],
        
        'discrimination' => [
            'introduction' => [
                "Nous accusons réception de votre signalement de discrimination.",
                "Votre témoignage concernant un acte discriminatoire a été enregistré.",
                "Nous prenons très au sérieux votre réclamation pour discrimination."
            ],
            'empathie' => [
                "Nous sommes profondément désolés pour cette expérience inacceptable.",
                "Ce type de comportement est contraire à nos valeurs fondamentales.",
                "Nous comprenons la gravité de ce que vous avez vécu."
            ],
            'action' => [
                "Une enquête interne a été immédiatement ouverte.",
                "Les mesures disciplinaires appropriées seront prises.",
                "Nous avons saisi le responsable concerné pour action corrective."
            ],
            'conclusion' => [
                "Nous vous recontacterons sous 48h avec les premières conclusions.",
                "Votre témoignage contribue à améliorer notre service.",
                "Nous restons à votre disposition pour tout échange complémentaire."
            ]
        ],
        
        'technique' => [
            'introduction' => [
                "Nous avons bien pris en compte votre problème technique.",
                "Merci de nous avoir signalé ce dysfonctionnement.",
                "Votre réclamation technique a été transmise à notre équipe support."
            ],
            'empathie' => [
                "Nous comprenons les désagréments causés par ce problème.",
                "Nous vous présentons nos excuses pour cette situation.",
                "Nous sommes conscients de l'urgence de résoudre ce problème."
            ],
            'action' => [
                "Nos techniciens analysent actuellement la situation.",
                "Un ticket de support a été créé (référence à communiquer).",
                "Une solution est en cours d'implémentation."
            ],
            'conclusion' => [
                "Le délai de résolution estimé est de 24-48h.",
                "Vous serez notifié dès la résolution du problème.",
                "Notre équipe reste mobilisée jusqu'à la résolution complète."
            ]
        ],
        
        'facturation' => [
            'introduction' => [
                "Nous avons reçu votre réclamation concernant la facturation.",
                "Merci de nous avoir signalé cette anomalie de facturation.",
                "Votre demande relative à votre facture est en cours de traitement."
            ],
            'empathie' => [
                "Nous comprenons l'importance de ce sujet pour vous.",
                "Nous prenons très au sérieux les questions financières.",
                "Nous regrettons sincèrement ce désagrément."
            ],
            'action' => [
                "Notre service comptabilité examine votre dossier.",
                "Une vérification complète de votre compte est en cours.",
                "Si une erreur est confirmée, un remboursement/avoir sera émis."
            ],
            'conclusion' => [
                "Vous recevrez un retour détaillé sous 5 jours ouvrés.",
                "Un justificatif vous sera transmis par email.",
                "N'hésitez pas à nous fournir tout document complémentaire."
            ]
        ],
        
        'transport' => [
            'introduction' => [
                "Nous avons bien reçu votre réclamation transport.",
                "Merci de nous avoir informé de ce problème de mobilité.",
                "Votre signalement concernant le transport a été enregistré."
            ],
            'empathie' => [
                "Nous comprenons à quel point les transports sont essentiels.",
                "L'accessibilité des transports est une priorité pour l'inclusion.",
                "Nous regrettons les difficultés rencontrées."
            ],
            'action' => [
                "Nous avons alerté le service transport concerné.",
                "Une inspection du véhicule/ligne sera effectuée.",
                "Des mesures correctives sont en cours d'étude."
            ],
            'conclusion' => [
                "Nous suivons de près l'amélioration de ce service.",
                "Votre retour contribue à améliorer la mobilité pour tous.",
                "Nous vous informerons des actions entreprises."
            ]
        ],
        
        'sante' => [
            'introduction' => [
                "Nous avons reçu votre réclamation relative aux soins de santé.",
                "Merci de nous avoir signalé cette situation médicale.",
                "Votre réclamation santé est traitée en priorité."
            ],
            'empathie' => [
                "Nous comprenons l'importance cruciale de ce sujet pour vous.",
                "La santé est notre préoccupation première.",
                "Nous sommes profondément concernés par votre situation."
            ],
            'action' => [
                "Le responsable médical a été immédiatement informé.",
                "Une revue de votre dossier est en cours.",
                "Nous coordonnons avec les services concernés pour vous aider."
            ],
            'conclusion' => [
                "Un interlocuteur dédié vous contactera rapidement.",
                "Nous restons disponibles pour tout besoin urgent.",
                "Votre santé et votre bien-être sont notre priorité absolue."
            ]
        ],
        
        'default' => [
            'introduction' => [
                "Nous avons bien reçu votre réclamation.",
                "Merci de nous avoir contacté.",
                "Votre demande a été enregistrée avec attention."
            ],
            'empathie' => [
                "Nous comprenons votre préoccupation.",
                "Nous prenons votre situation très au sérieux.",
                "Nous sommes désolés pour ce désagrément."
            ],
            'action' => [
                "Votre dossier est en cours de traitement.",
                "Nous avons transmis votre demande au service approprié.",
                "Notre équipe analyse votre situation."
            ],
            'conclusion' => [
                "Nous vous tiendrons informé de l'avancement.",
                "N'hésitez pas à nous recontacter si besoin.",
                "Cordialement, L'équipe ImpactAble."
            ]
        ]
    ];
    
    // ==================== DICTIONNAIRE DE SENTIMENT ====================
    
    private static $sentimentKeywords = [
        'colere' => [
            'keywords' => ['inadmissible', 'scandaleux', 'honteux', 'inacceptable', 'révoltant', 'furieux', 
                         'énervé', 'fâché', 'exaspéré', 'ras le bol', 'marre', 'excédé', '!!!', 'MAJUSCULES'],
            'score' => -10
        ],
        'frustration' => [
            'keywords' => ['encore', 'toujours', 'jamais résolu', 'ça fait X fois', 'personne ne', 
                         'impossible', 'bloqué', 'depuis longtemps', 'attente interminable'],
            'score' => -7
        ],
        'urgence' => [
            'keywords' => ['urgent', 'urgence', 'immédiat', 'critique', 'vital', 'danger', 
                         'vite', 'rapidement', 'au plus vite', 'maintenant'],
            'score' => -5
        ],
        'detresse' => [
            'keywords' => ['désespéré', 'perdu', 'ne sais plus', 'à bout', 'épuisé', 
                         'aide-moi', 'svp', 's\'il vous plaît', 'besoin'],
            'score' => -8
        ],
        'neutre' => [
            'keywords' => ['je voudrais', 'pourriez-vous', 'merci de', 'serait-il possible'],
            'score' => 0
        ],
        'positif' => [
            'keywords' => ['merci', 'bravo', 'excellent', 'bien', 'satisfait', 'content'],
            'score' => 5
        ]
    ];
    
    // ==================== PHRASES EMPATHIQUES PAR SENTIMENT ====================
    
    private static $empathyResponses = [
        'colere' => [
            "Nous comprenons parfaitement votre colère et elle est tout à fait légitime.",
            "Votre mécontentement est totalement compréhensible face à cette situation.",
            "Nous prenons très au sérieux votre indignation et allons agir immédiatement."
        ],
        'frustration' => [
            "Nous sommes vraiment désolés que ce problème persiste malgré vos efforts.",
            "Nous comprenons votre frustration face à cette situation récurrente.",
            "Il est inadmissible que vous ayez dû faire face à cela plusieurs fois."
        ],
        'urgence' => [
            "Nous traitons votre demande avec la plus haute priorité.",
            "Notre équipe est mobilisée en urgence pour résoudre votre problème.",
            "Nous comprenons le caractère urgent et agissons immédiatement."
        ],
        'detresse' => [
            "Nous sommes là pour vous aider et allons tout faire pour résoudre cette situation.",
            "Ne vous inquiétez pas, nous prenons votre dossier en main personnellement.",
            "Nous comprenons combien cette situation est difficile pour vous."
        ],
        'neutre' => [
            "Merci pour votre message.",
            "Nous avons bien pris note de votre demande.",
            "Votre requête est en cours de traitement."
        ],
        'positif' => [
            "Merci pour votre retour positif !",
            "Nous sommes ravis de savoir que vous êtes satisfait.",
            "Votre feedback nous encourage à continuer nos efforts."
        ]
    ];
    
    // ==================== SOLUTIONS PAR CATÉGORIE ====================
    
    private static $solutionsSuggestions = [
        'accessibilite' => [
            "Installation d'une rampe d'accès",
            "Mise en conformité des sanitaires PMR",
            "Installation d'un ascenseur ou monte-charge",
            "Ajout de bandes podotactiles",
            "Installation de signalétique adaptée",
            "Aménagement d'un parking PMR",
            "Formation du personnel à l'accueil handicap"
        ],
        'discrimination' => [
            "Enquête interne et sanctions disciplinaires",
            "Formation obligatoire sur la diversité",
            "Mise en place d'une cellule d'écoute",
            "Révision des procédures internes",
            "Accompagnement psychologique proposé",
            "Médiation avec les parties concernées"
        ],
        'technique' => [
            "Redémarrage du système",
            "Mise à jour logicielle",
            "Remplacement du composant défectueux",
            "Configuration des paramètres",
            "Intervention d'un technicien sur site",
            "Fourniture d'un équipement de remplacement"
        ],
        'facturation' => [
            "Émission d'un avoir",
            "Remboursement sous 5-7 jours",
            "Rectification de la facture",
            "Échelonnement de paiement proposé",
            "Annulation des pénalités",
            "Geste commercial en compensation"
        ],
        'transport' => [
            "Mise en conformité du véhicule",
            "Formation des chauffeurs",
            "Adaptation des horaires",
            "Mise à disposition d'un transport adapté",
            "Signalement aux autorités compétentes"
        ],
        'sante' => [
            "Rendez-vous prioritaire proposé",
            "Orientation vers un spécialiste",
            "Prise en charge des frais",
            "Accompagnement personnalisé",
            "Coordination avec votre médecin traitant"
        ]
    ];
    
    /**
     * Génère une réponse intelligente complète
     * 
     * @param array $reclamation Les données de la réclamation
     * @return array La réponse générée avec métadonnées
     */
    public static function genererReponse($reclamation) {
        $categorie = self::normaliserCategorie($reclamation['categorie'] ?? 'default');
        $priorite = $reclamation['priorite'] ?? 'Normale';
        $sujet = $reclamation['sujet'] ?? '';
        $description = $reclamation['description'] ?? '';
        $texteComplet = $sujet . ' ' . $description;
        
        // Analyser le sentiment
        $sentiment = self::analyserSentiment($texteComplet);
        
        // Obtenir les templates appropriés
        $templates = self::$templates[$categorie] ?? self::$templates['default'];
        
        // Construire la réponse
        $reponse = [];
        
        // 1. Introduction
        $reponse['introduction'] = $templates['introduction'][array_rand($templates['introduction'])];
        
        // 2. Empathie adaptée au sentiment
        $reponse['empathie'] = self::getEmpathieAdaptee($sentiment['type']);
        
        // 3. Actions selon la priorité
        $reponse['action'] = $templates['action'][array_rand($templates['action'])];
        
        // 4. Solutions suggérées
        $reponse['solutions'] = self::getSolutionsSuggestions($categorie, 3);
        
        // 5. Conclusion
        $reponse['conclusion'] = $templates['conclusion'][array_rand($templates['conclusion'])];
        
        // 6. Délai estimé
        $reponse['delai'] = self::getDelaiEstime($priorite);
        
        // Assembler le texte final
        $texteReponse = self::assemblerReponse($reponse, $priorite);
        
        // Calculer le score de qualité
        $scoreQualite = self::calculerScoreQualite($texteReponse, $reclamation);
        
        return [
            'texte' => $texteReponse,
            'composants' => $reponse,
            'sentiment' => $sentiment,
            'categorie' => $categorie,
            'priorite' => $priorite,
            'score_qualite' => $scoreQualite,
            'solutions_disponibles' => self::$solutionsSuggestions[$categorie] ?? [],
            'metadata' => [
                'generated_at' => date('Y-m-d H:i:s'),
                'version' => '1.0',
                'type' => 'auto_generated'
            ]
        ];
    }
    
    /**
     * Analyse le sentiment du texte
     */
    public static function analyserSentiment($texte) {
        $texte = mb_strtolower($texte, 'UTF-8');
        $scores = [];
        $motsDetectes = [];
        
        foreach (self::$sentimentKeywords as $sentiment => $data) {
            $score = 0;
            $mots = [];
            
            foreach ($data['keywords'] as $keyword) {
                if (strpos($texte, mb_strtolower($keyword, 'UTF-8')) !== false) {
                    $score += abs($data['score']);
                    $mots[] = $keyword;
                }
            }
            
            if ($score > 0) {
                $scores[$sentiment] = $score;
                $motsDetectes[$sentiment] = $mots;
            }
        }
        
        // Détecter les MAJUSCULES (signe de colère)
        if (preg_match('/[A-Z]{5,}/', $texte)) {
            $scores['colere'] = ($scores['colere'] ?? 0) + 5;
        }
        
        // Détecter les ponctuations multiples
        if (preg_match('/[!]{2,}/', $texte)) {
            $scores['colere'] = ($scores['colere'] ?? 0) + 3;
        }
        
        // Déterminer le sentiment dominant
        if (empty($scores)) {
            return [
                'type' => 'neutre',
                'score' => 0,
                'intensite' => 'faible',
                'mots_detectes' => []
            ];
        }
        
        $sentimentDominant = array_keys($scores, max($scores))[0];
        $scoreMax = max($scores);
        
        // Déterminer l'intensité
        $intensite = 'faible';
        if ($scoreMax >= 15) {
            $intensite = 'tres_forte';
        } elseif ($scoreMax >= 10) {
            $intensite = 'forte';
        } elseif ($scoreMax >= 5) {
            $intensite = 'moyenne';
        }
        
        return [
            'type' => $sentimentDominant,
            'score' => $scoreMax,
            'intensite' => $intensite,
            'mots_detectes' => $motsDetectes[$sentimentDominant] ?? [],
            'tous_sentiments' => $scores
        ];
    }
    
    /**
     * Obtient une phrase d'empathie adaptée au sentiment
     */
    private static function getEmpathieAdaptee($sentimentType) {
        $empathies = self::$empathyResponses[$sentimentType] ?? self::$empathyResponses['neutre'];
        return $empathies[array_rand($empathies)];
    }
    
    /**
     * Obtient des suggestions de solutions
     */
    private static function getSolutionsSuggestions($categorie, $limit = 3) {
        $solutions = self::$solutionsSuggestions[$categorie] ?? [];
        
        if (empty($solutions)) {
            return [];
        }
        
        shuffle($solutions);
        return array_slice($solutions, 0, $limit);
    }
    
    /**
     * Calcule le délai estimé selon la priorité
     */
    private static function getDelaiEstime($priorite) {
        $priorite = mb_strtolower($priorite, 'UTF-8');
        
        $delais = [
            'urgente' => ['texte' => '24 heures', 'jours' => 1],
            'haute' => ['texte' => '24-48 heures', 'jours' => 2],
            'moyenne' => ['texte' => '3-5 jours ouvrés', 'jours' => 5],
            'normale' => ['texte' => '5-7 jours ouvrés', 'jours' => 7],
            'faible' => ['texte' => '7-10 jours ouvrés', 'jours' => 10],
            'basse' => ['texte' => '10-15 jours ouvrés', 'jours' => 15]
        ];
        
        return $delais[$priorite] ?? $delais['normale'];
    }
    
    /**
     * Assemble tous les composants en une réponse cohérente
     */
    private static function assemblerReponse($composants, $priorite) {
        $texte = "";
        
        // Référence de la réclamation (sera remplacée dynamiquement)
        $texte .= "Objet : Traitement de votre réclamation\n\n";
        
        // Salutation
        $texte .= "Bonjour,\n\n";
        
        // Introduction
        $texte .= $composants['introduction'] . "\n\n";
        
        // Empathie
        $texte .= $composants['empathie'] . "\n\n";
        
        // Action en cours
        $texte .= $composants['action'] . "\n\n";
        
        // Solutions proposées (si disponibles)
        if (!empty($composants['solutions'])) {
            $texte .= "Les actions envisagées sont :\n";
            foreach ($composants['solutions'] as $solution) {
                $texte .= "• " . $solution . "\n";
            }
            $texte .= "\n";
        }
        
        // Délai
        $texte .= "Délai de traitement estimé : " . $composants['delai']['texte'] . "\n\n";
        
        // Conclusion
        $texte .= $composants['conclusion'] . "\n\n";
        
        // Signature
        $texte .= "Cordialement,\n";
        $texte .= "L'équipe ImpactAble\n";
        $texte .= "📧 support@impactable.tn";
        
        return $texte;
    }
    
    /**
     * Calcule un score de qualité pour la réponse
     */
    public static function calculerScoreQualite($texte, $reclamation = []) {
        $score = 0;
        $criteres = [];
        
        // 1. Longueur appropriée (idéal: 200-500 caractères)
        $longueur = mb_strlen($texte, 'UTF-8');
        if ($longueur >= 200 && $longueur <= 800) {
            $score += 20;
            $criteres['longueur'] = ['score' => 20, 'status' => 'ok', 'message' => 'Longueur appropriée'];
        } elseif ($longueur >= 100) {
            $score += 10;
            $criteres['longueur'] = ['score' => 10, 'status' => 'warning', 'message' => 'Longueur acceptable'];
        } else {
            $criteres['longueur'] = ['score' => 0, 'status' => 'error', 'message' => 'Réponse trop courte'];
        }
        
        // 2. Présence de formules de politesse
        $politesse = false;
        $motsPolitesse = ['bonjour', 'cordialement', 'sincèrement', 'merci', 'salutations'];
        foreach ($motsPolitesse as $mot) {
            if (stripos($texte, $mot) !== false) {
                $politesse = true;
                break;
            }
        }
        if ($politesse) {
            $score += 15;
            $criteres['politesse'] = ['score' => 15, 'status' => 'ok', 'message' => 'Formules de politesse présentes'];
        } else {
            $criteres['politesse'] = ['score' => 0, 'status' => 'warning', 'message' => 'Ajouter des formules de politesse'];
        }
        
        // 3. Empathie/Compréhension
        $empathie = false;
        $motsEmpathie = ['comprenons', 'désolé', 'regrettons', 'conscients', 'préoccupation'];
        foreach ($motsEmpathie as $mot) {
            if (stripos($texte, $mot) !== false) {
                $empathie = true;
                break;
            }
        }
        if ($empathie) {
            $score += 20;
            $criteres['empathie'] = ['score' => 20, 'status' => 'ok', 'message' => 'Empathie exprimée'];
        } else {
            $criteres['empathie'] = ['score' => 0, 'status' => 'warning', 'message' => 'Ajouter de l\'empathie'];
        }
        
        // 4. Actions concrètes mentionnées
        $actions = false;
        $motsActions = ['action', 'traitement', 'résolution', 'intervention', 'solution', 'mesures'];
        foreach ($motsActions as $mot) {
            if (stripos($texte, $mot) !== false) {
                $actions = true;
                break;
            }
        }
        if ($actions) {
            $score += 20;
            $criteres['actions'] = ['score' => 20, 'status' => 'ok', 'message' => 'Actions concrètes mentionnées'];
        } else {
            $criteres['actions'] = ['score' => 0, 'status' => 'warning', 'message' => 'Mentionner des actions'];
        }
        
        // 5. Délai mentionné
        $delai = preg_match('/(délai|heures|jours|semaines|rapidement)/i', $texte);
        if ($delai) {
            $score += 15;
            $criteres['delai'] = ['score' => 15, 'status' => 'ok', 'message' => 'Délai mentionné'];
        } else {
            $criteres['delai'] = ['score' => 0, 'status' => 'warning', 'message' => 'Indiquer un délai'];
        }
        
        // 6. Contact fourni
        $contact = preg_match('/(email|@|téléphone|contact|support)/i', $texte);
        if ($contact) {
            $score += 10;
            $criteres['contact'] = ['score' => 10, 'status' => 'ok', 'message' => 'Contact fourni'];
        } else {
            $criteres['contact'] = ['score' => 0, 'status' => 'info', 'message' => 'Ajouter un contact'];
        }
        
        // Déterminer le niveau de qualité
        $niveau = 'faible';
        $couleur = '#e74c3c';
        if ($score >= 80) {
            $niveau = 'excellent';
            $couleur = '#27ae60';
        } elseif ($score >= 60) {
            $niveau = 'bon';
            $couleur = '#2ecc71';
        } elseif ($score >= 40) {
            $niveau = 'moyen';
            $couleur = '#f39c12';
        }
        
        return [
            'score' => $score,
            'max' => 100,
            'pourcentage' => $score,
            'niveau' => $niveau,
            'couleur' => $couleur,
            'criteres' => $criteres,
            'recommandations' => self::getRecommandations($criteres)
        ];
    }
    
    /**
     * Génère des recommandations basées sur les critères manquants
     */
    private static function getRecommandations($criteres) {
        $recommandations = [];
        
        foreach ($criteres as $nom => $data) {
            if ($data['status'] !== 'ok') {
                $recommandations[] = [
                    'critere' => $nom,
                    'message' => $data['message'],
                    'priorite' => $data['status'] === 'error' ? 'haute' : 'moyenne'
                ];
            }
        }
        
        return $recommandations;
    }
    
    /**
     * Normalise le nom de la catégorie
     */
    private static function normaliserCategorie($categorie) {
        $categorie = mb_strtolower(trim($categorie), 'UTF-8');
        
        // Supprimer les accents
        $categorie = str_replace(
            ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'ù', 'û', 'ü', 'î', 'ï', 'ô', 'ö', 'ç'],
            ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'u', 'u', 'u', 'i', 'i', 'o', 'o', 'c'],
            $categorie
        );
        
        // Mapper vers les catégories connues
        $mapping = [
            'accessibilite' => 'accessibilite',
            'accessibilité' => 'accessibilite',
            'discrimination' => 'discrimination',
            'technique' => 'technique',
            'facturation' => 'facturation',
            'transport' => 'transport',
            'sante' => 'sante',
            'santé' => 'sante',
            'education' => 'default',
            'emploi' => 'default',
            'administration' => 'default',
            'service' => 'default',
            'produit' => 'default'
        ];
        
        return $mapping[$categorie] ?? 'default';
    }
    
    /**
     * Obtient tous les modèles de réponses disponibles
     */
    public static function getTemplates() {
        return self::$templates;
    }
    
    /**
     * Obtient les modèles pour une catégorie spécifique
     */
    public static function getTemplatesByCategorie($categorie) {
        $categorie = self::normaliserCategorie($categorie);
        return self::$templates[$categorie] ?? self::$templates['default'];
    }
    
    /**
     * Génère une réponse rapide basée sur un type prédéfini
     */
    public static function genererReponseRapide($type, $params = []) {
        $reponsesRapides = [
            'accuse_reception' => "Nous accusons réception de votre réclamation n°{numero}.\nVotre dossier est en cours de traitement.\nDélai estimé : {delai}",
            
            'demande_info' => "Pour traiter votre réclamation, nous avons besoin d'informations complémentaires :\n{infos_demandees}\nMerci de nous les communiquer dans les meilleurs délais.",
            
            'en_cours' => "Votre réclamation est actuellement en cours de traitement par notre équipe.\nNous vous tiendrons informé de l'avancement.",
            
            'resolution' => "Nous avons le plaisir de vous informer que votre réclamation a été résolue.\n{details_resolution}\nN'hésitez pas à nous contacter si vous avez des questions.",
            
            'escalade' => "Votre dossier a été transmis à un responsable pour un traitement prioritaire.\nVous serez recontacté sous 24h.",
            
            'cloture' => "Votre réclamation n°{numero} a été clôturée.\nMerci pour votre confiance.\nÀ bientôt sur ImpactAble !"
        ];
        
        $reponse = $reponsesRapides[$type] ?? "Merci de votre patience. Votre réclamation est en cours de traitement.";
        
        // Remplacer les variables
        foreach ($params as $key => $value) {
            $reponse = str_replace('{' . $key . '}', $value, $reponse);
        }
        
        return $reponse;
    }
    
    /**
     * Suggère des réponses similaires basées sur l'historique (simulé)
     */
    public static function suggererReponsesHistorique($categorie, $priorite) {
        // Dans une vraie implémentation, ceci interrogerait une base de données
        // d'historique des réponses réussies
        
        return [
            'info' => 'Cette fonctionnalité utilisera l\'historique des réponses pour suggérer les meilleures réponses.',
            'suggestions' => []
        ];
    }
}
?>




