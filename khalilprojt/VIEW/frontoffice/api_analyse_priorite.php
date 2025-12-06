<?php
/**
 * API pour tester le système de priorisation intelligente
 * Endpoint: api_analyse_priorite.php
 * Méthode: POST ou GET
 * Paramètres: texte (string), categorie (string, optionnel)
 */

require_once(__DIR__ . '/../../SERVICE/PrioriteIntelligente.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

// Récupérer les paramètres
$texte = '';
$categorie = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $texte = isset($_POST['texte']) ? $_POST['texte'] : '';
    $categorie = isset($_POST['categorie']) ? $_POST['categorie'] : '';
} else {
    $texte = isset($_GET['texte']) ? $_GET['texte'] : '';
    $categorie = isset($_GET['categorie']) ? $_GET['categorie'] : '';
}

// Validation
if (empty($texte)) {
    echo json_encode([
        'success' => false,
        'message' => 'Le paramètre "texte" est requis',
        'exemple' => 'api_analyse_priorite.php?texte=Je suis bloqué dans l\'ascenseur, c\'est urgent!'
    ]);
    exit;
}

// Analyser le texte
$resultat = PrioriteIntelligente::analyser($texte, $categorie);

// Formater la réponse
$response = [
    'success' => true,
    'texte_analyse' => $texte,
    'categorie' => $categorie ?: 'Non spécifiée',
    'resultat' => [
        'priorite' => $resultat['priorite'],
        'priorite_icon' => PrioriteIntelligente::getPrioriteIcon($resultat['priorite']),
        'score' => $resultat['score'],
        'confiance' => $resultat['confiance'] . '%',
        'nombre_mots_detectes' => count($resultat['motsDetectes']),
        'mots_detectes' => array_map(function($mot) {
            return [
                'mot' => $mot['mot'],
                'type' => $mot['type'],
                'points' => $mot['points']
            ];
        }, $resultat['motsDetectes'])
    ],
    'interpretation' => getInterpretation($resultat)
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

/**
 * Génère une interprétation humaine du résultat
 */
function getInterpretation($resultat) {
    $priorite = $resultat['priorite'];
    $confiance = $resultat['confiance'];
    $score = $resultat['score'];
    
    $interpretation = '';
    
    switch ($priorite) {
        case 'Urgente':
            $interpretation = "⚠️ Cette réclamation nécessite une attention IMMÉDIATE. ";
            $interpretation .= "Le système a détecté des indicateurs d'urgence élevés (score: $score). ";
            $interpretation .= "Une intervention rapide est recommandée.";
            break;
            
        case 'Moyenne':
            $interpretation = "📋 Cette réclamation est d'importance MODÉRÉE. ";
            $interpretation .= "Le système recommande un traitement dans les délais standards. ";
            $interpretation .= "Priorité normale avec surveillance.";
            break;
            
        case 'Faible':
            $interpretation = "✅ Cette réclamation est de priorité BASSE. ";
            $interpretation .= "Le contenu ne présente pas d'indicateurs d'urgence particuliers. ";
            $interpretation .= "Traitement selon la file d'attente normale.";
            break;
    }
    
    if ($confiance >= 80) {
        $interpretation .= " (Confiance élevée: $confiance%)";
    } elseif ($confiance >= 50) {
        $interpretation .= " (Confiance moyenne: $confiance%)";
    } else {
        $interpretation .= " (Confiance faible: $confiance% - vérification manuelle recommandée)";
    }
    
    return $interpretation;
}
?>

