<?php
/**
 * Service de Priorisation Intelligente
 * Analyse le contenu des réclamations pour déterminer automatiquement la priorité
 * Utilise un système de scoring basé sur des mots-clés
 */
class PrioriteIntelligente {
    
    // Mots-clés pour la priorité URGENTE (score élevé)
    private static $motsUrgents = [
        'urgent' => 10,
        'urgente' => 10,
        'urgence' => 10,
        'bloqué' => 9,
        'bloquée' => 9,
        'bloquer' => 8,
        'ne marche pas' => 9,
        'ne fonctionne pas' => 9,
        'erreur critique' => 10,
        'erreur grave' => 9,
        'panne' => 8,
        'cassé' => 8,
        'impossible' => 7,
        'catastrophe' => 10,
        'danger' => 10,
        'dangereux' => 10,
        'accident' => 10,
        'blessure' => 10,
        'sécurité' => 8,
        'immédiat' => 9,
        'immédiatement' => 9,
        'tout de suite' => 8,
        'maintenant' => 6,
        'critique' => 9,
        'grave' => 8,
        'gravité' => 8,
        'vital' => 10,
        'morte' => 10,
        'mort' => 10,
        'hôpital' => 9,
        'ambulance' => 10,
        'police' => 9,
        'pompier' => 10,
        'incendie' => 10,
        'feu' => 9,
        'inondation' => 9,
        'électrocution' => 10,
        'agression' => 10,
        'violence' => 9,
        'menace' => 8,
        'handicapé bloqué' => 10,
        'accessibilité urgente' => 9,
        'ascenseur bloqué' => 9,
        'coincé' => 8,
        'enfermé' => 9
    ];
    
    // Mots-clés pour la priorité IMPORTANTE (score moyen)
    private static $motsImportants = [
        'problème' => 5,
        'probleme' => 5,
        'souci' => 4,
        'difficulté' => 4,
        'difficile' => 4,
        'je n\'arrive pas' => 5,
        'besoin d\'aide' => 5,
        'aide' => 3,
        'aider' => 3,
        'dysfonctionnement' => 5,
        'bug' => 5,
        'erreur' => 5,
        'défaut' => 4,
        'défaillance' => 5,
        'récurrent' => 5,
        'répété' => 4,
        'plusieurs fois' => 4,
        'encore' => 3,
        'toujours pas' => 5,
        'pas résolu' => 5,
        'non résolu' => 5,
        'attente' => 3,
        'long' => 3,
        'lent' => 3,
        'retard' => 4,
        'délai' => 3,
        'plainte' => 4,
        'mécontent' => 4,
        'insatisfait' => 4,
        'déçu' => 3,
        'frustré' => 4,
        'agacé' => 3,
        'énervé' => 4,
        'discrimination' => 6,
        'discriminé' => 6,
        'injustice' => 5,
        'refus' => 4,
        'refusé' => 4,
        'rejeté' => 4,
        'ignoré' => 4,
        'négligé' => 4,
        'inaccessible' => 5,
        'rampe' => 4,
        'fauteuil roulant' => 5,
        'malvoyant' => 4,
        'malentendant' => 4,
        'sourd' => 4,
        'aveugle' => 5
    ];
    
    // Mots-clés qui réduisent la priorité
    private static $motsNormaux = [
        'suggestion' => -3,
        'proposer' => -2,
        'amélioration' => -2,
        'idée' => -3,
        'question' => -2,
        'demande d\'information' => -3,
        'renseignement' => -3,
        'curiosité' => -4,
        'simplement' => -2,
        'juste' => -2,
        'petit' => -2,
        'mineur' => -3,
        'pas pressé' => -4,
        'quand vous pouvez' => -3,
        'merci d\'avance' => -1,
        'cordialement' => -1
    ];
    
    /**
     * Analyse un texte et retourne la priorité suggérée
     * @param string $texte Le texte à analyser (sujet + description)
     * @param string $categorie La catégorie de la réclamation
     * @return array ['priorite' => string, 'score' => int, 'motsDetectes' => array, 'confiance' => float]
     */
    public static function analyser($texte, $categorie = '') {
        $texte = mb_strtolower($texte, 'UTF-8');
        $texte = self::normaliserTexte($texte);
        
        $score = 0;
        $motsDetectes = [];
        
        // Analyser les mots urgents
        foreach (self::$motsUrgents as $mot => $points) {
            if (self::contientMot($texte, $mot)) {
                $score += $points;
                $motsDetectes[] = ['mot' => $mot, 'type' => 'urgent', 'points' => $points];
            }
        }
        
        // Analyser les mots importants
        foreach (self::$motsImportants as $mot => $points) {
            if (self::contientMot($texte, $mot)) {
                $score += $points;
                $motsDetectes[] = ['mot' => $mot, 'type' => 'important', 'points' => $points];
            }
        }
        
        // Analyser les mots qui réduisent la priorité
        foreach (self::$motsNormaux as $mot => $points) {
            if (self::contientMot($texte, $mot)) {
                $score += $points; // points négatifs
                $motsDetectes[] = ['mot' => $mot, 'type' => 'normal', 'points' => $points];
            }
        }
        
        // Bonus par catégorie
        $score += self::getBonusCategorie($categorie);
        
        // Déterminer la priorité
        $priorite = self::scoreToPriorite($score);
        
        // Calculer le niveau de confiance (0-100%)
        $confiance = self::calculerConfiance($score, count($motsDetectes));
        
        return [
            'priorite' => $priorite,
            'score' => $score,
            'motsDetectes' => $motsDetectes,
            'confiance' => $confiance
        ];
    }
    
    /**
     * Normalise le texte pour une meilleure détection
     */
    private static function normaliserTexte($texte) {
        // Supprimer les accents pour certaines comparaisons
        $texte = str_replace(
            ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'ù', 'û', 'ü', 'î', 'ï', 'ô', 'ö', 'ç'],
            ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'u', 'u', 'u', 'i', 'i', 'o', 'o', 'c'],
            $texte
        );
        
        // Supprimer la ponctuation excessive
        $texte = preg_replace('/[!]{2,}/', '! ', $texte);
        $texte = preg_replace('/[?]{2,}/', '? ', $texte);
        
        // Détecter les majuscules excessives (signe d'urgence)
        // Note: déjà converti en minuscules, donc on vérifie avant
        
        return $texte;
    }
    
    /**
     * Vérifie si le texte contient un mot ou une expression
     */
    private static function contientMot($texte, $mot) {
        $mot = mb_strtolower($mot, 'UTF-8');
        
        // Recherche avec limites de mot pour les mots simples
        if (strpos($mot, ' ') === false) {
            return preg_match('/\b' . preg_quote($mot, '/') . '\b/u', $texte) === 1;
        }
        
        // Pour les expressions, recherche directe
        return strpos($texte, $mot) !== false;
    }
    
    /**
     * Retourne un bonus de score basé sur la catégorie
     */
    private static function getBonusCategorie($categorie) {
        $categorie = mb_strtolower($categorie, 'UTF-8');
        
        $bonusCategories = [
            'discrimination' => 5,
            'accessibilité' => 4,
            'accessibilite' => 4,
            'sécurité' => 5,
            'securite' => 5,
            'santé' => 4,
            'sante' => 4,
            'urgence' => 6,
            'technique' => 2,
            'facturation' => 1,
            'service' => 1,
            'produit' => 0,
            'transport' => 3,
            'éducation' => 2,
            'education' => 2,
            'emploi' => 2,
            'administration' => 1
        ];
        
        return $bonusCategories[$categorie] ?? 0;
    }
    
    /**
     * Convertit un score en priorité textuelle
     */
    private static function scoreToPriorite($score) {
        if ($score >= 15) {
            return 'Urgente';
        } elseif ($score >= 7) {
            return 'Moyenne';
        } else {
            return 'Faible';
        }
    }
    
    /**
     * Calcule le niveau de confiance de l'analyse
     */
    private static function calculerConfiance($score, $nombreMots) {
        if ($nombreMots === 0) {
            return 30; // Confiance faible si aucun mot détecté
        }
        
        // Plus de mots détectés = plus de confiance
        $confiance = min(95, 50 + ($nombreMots * 10) + abs($score) * 2);
        
        return round($confiance);
    }
    
    /**
     * Retourne une explication textuelle de l'analyse
     */
    public static function getExplication($resultat) {
        $priorite = $resultat['priorite'];
        $score = $resultat['score'];
        $confiance = $resultat['confiance'];
        $motsDetectes = $resultat['motsDetectes'];
        
        $explication = "Priorité suggérée : <strong>{$priorite}</strong><br>";
        $explication .= "Score d'analyse : {$score} points<br>";
        $explication .= "Niveau de confiance : {$confiance}%<br>";
        
        if (!empty($motsDetectes)) {
            $explication .= "<br>Mots-clés détectés :<br>";
            foreach ($motsDetectes as $detection) {
                $emoji = $detection['type'] === 'urgent' ? '🔴' : ($detection['type'] === 'important' ? '🟠' : '🟢');
                $explication .= "{$emoji} \"{$detection['mot']}\" ({$detection['points']} pts)<br>";
            }
        }
        
        return $explication;
    }
    
    /**
     * Retourne l'icône/badge correspondant à la priorité
     */
    public static function getPrioriteIcon($priorite) {
        switch (mb_strtolower($priorite, 'UTF-8')) {
            case 'urgente':
                return '🔴';
            case 'moyenne':
                return '🟠';
            case 'faible':
            default:
                return '🟢';
        }
    }
    
    /**
     * Retourne la classe CSS correspondant à la priorité
     */
    public static function getPrioriteClass($priorite) {
        switch (mb_strtolower($priorite, 'UTF-8')) {
            case 'urgente':
                return 'priority-urgente';
            case 'moyenne':
                return 'priority-moyenne';
            case 'faible':
            default:
                return 'priority-faible';
        }
    }
}
?>





