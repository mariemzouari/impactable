<?php
session_start();
require_once(__DIR__ . '/../../controller/ReclamationController.php');
require_once(__DIR__ . '/../../controller/ReponseController.php');
require_once(__DIR__ . '/../../CONFIGRRATION/config.php');
require_once(__DIR__ . '/../../SERVICE/PrioriteIntelligente.php');
require_once(__DIR__ . '/../../SERVICE/EmotionDetector.php');

$controller = new ReclamationController();
$reponseController = new ReponseController();
$reclamations = $controller->listReclamations();
$stats = $controller->getStats();

// Connexion DB
$db = config::getConnexion();

// ============================================
// STATISTIQUES AVANCÉES PROFESSIONNELLES
// ============================================

function getStatistiquesPro($db, $reclamations) {
    $stats = [];
    
    // 1. Réclamations par jour (14 derniers jours)
    $sql = "SELECT DATE(dateCreation) as jour, COUNT(*) as nombre 
            FROM reclamation 
            WHERE dateCreation >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
            GROUP BY DATE(dateCreation) 
            ORDER BY jour ASC";
    try {
        $query = $db->query($sql);
        $stats['par_jour'] = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $stats['par_jour'] = [];
    }
    
    // 2. Réclamations par catégorie
    $sql = "SELECT categorie, COUNT(*) as nombre FROM reclamation GROUP BY categorie ORDER BY nombre DESC";
    try {
        $query = $db->query($sql);
        $stats['par_categorie'] = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $stats['par_categorie'] = [];
    }
    
    // 3. Réclamations par priorité
    $sql = "SELECT priorite, COUNT(*) as nombre FROM reclamation GROUP BY priorite ORDER BY 
            CASE priorite 
                WHEN 'Urgente' THEN 1 
                WHEN 'Moyenne' THEN 2 
                WHEN 'Faible' THEN 3 
            END";
    try {
        $query = $db->query($sql);
        $stats['par_priorite'] = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $stats['par_priorite'] = [];
    }
    
    // 4. Réclamations par statut
    $sql = "SELECT statut, COUNT(*) as nombre FROM reclamation GROUP BY statut";
    try {
        $query = $db->query($sql);
        $stats['par_statut'] = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $stats['par_statut'] = [];
    }
    
    // 5. Temps moyen de résolution
    $sql = "SELECT AVG(DATEDIFF(derniereModification, dateCreation)) as temps_moyen
            FROM reclamation WHERE statut = 'Résolue'";
    try {
        $query = $db->query($sql);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $stats['temps_moyen_resolution'] = round($result['temps_moyen'] ?? 0, 1);
    } catch (Exception $e) {
        $stats['temps_moyen_resolution'] = 0;
    }
    
    // 6. Taux de résolution
    $sql = "SELECT 
                COUNT(CASE WHEN statut = 'Résolue' THEN 1 END) as resolues,
                COUNT(*) as total
            FROM reclamation";
    try {
        $query = $db->query($sql);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $stats['taux_resolution'] = $result['total'] > 0 
            ? round(($result['resolues'] / $result['total']) * 100, 1) 
            : 0;
        $stats['total_resolues'] = $result['resolues'];
        $stats['total_reclamations'] = $result['total'];
    } catch (Exception $e) {
        $stats['taux_resolution'] = 0;
        $stats['total_resolues'] = 0;
        $stats['total_reclamations'] = 0;
    }
    
    // 7. Total réponses
    $sql = "SELECT COUNT(*) as total FROM reponse";
    try {
        $query = $db->query($sql);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $stats['total_reponses'] = $result['total'] ?? 0;
    } catch (Exception $e) {
        $stats['total_reponses'] = 0;
    }
    
    // 8. Urgentes non résolues
    $sql = "SELECT COUNT(*) as nombre FROM reclamation WHERE priorite = 'Urgente' AND statut != 'Résolue' AND statut != 'Fermée'";
    try {
        $query = $db->query($sql);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $stats['urgentes_non_resolues'] = $result['nombre'] ?? 0;
    } catch (Exception $e) {
        $stats['urgentes_non_resolues'] = 0;
    }
    
    // 9. Réclamations cette semaine vs semaine dernière
    $sql = "SELECT 
                COUNT(CASE WHEN dateCreation >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as cette_semaine,
                COUNT(CASE WHEN dateCreation >= DATE_SUB(CURDATE(), INTERVAL 14 DAY) AND dateCreation < DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as semaine_derniere
            FROM reclamation";
    try {
        $query = $db->query($sql);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $stats['cette_semaine'] = $result['cette_semaine'] ?? 0;
        $stats['semaine_derniere'] = $result['semaine_derniere'] ?? 0;
        
        if ($stats['semaine_derniere'] > 0) {
            $stats['tendance'] = round((($stats['cette_semaine'] - $stats['semaine_derniere']) / $stats['semaine_derniere']) * 100, 1);
        } else {
            $stats['tendance'] = $stats['cette_semaine'] > 0 ? 100 : 0;
        }
    } catch (Exception $e) {
        $stats['cette_semaine'] = 0;
        $stats['semaine_derniere'] = 0;
        $stats['tendance'] = 0;
    }
    
    // 10. Analyse des émotions sur les réclamations
    $emotionStats = [
        'colere' => 0,
        'frustration' => 0,
        'tristesse' => 0,
        'anxiete' => 0,
        'detresse' => 0,
        'positif' => 0,
        'neutre' => 0
    ];
    
    if (!empty($reclamations)) {
        foreach ($reclamations as $rec) {
            $description = isset($rec['description']) ? $rec['description'] : '';
            if (!empty($description)) {
                $analyse = EmotionDetector::analyser($description);
                $emotionStats[$analyse['emotion']]++;
            }
        }
    }
    $stats['emotions'] = $emotionStats;
    
    // 11. Évolution mensuelle (12 mois)
    $sql = "SELECT 
                DATE_FORMAT(dateCreation, '%Y-%m') as mois,
                DATE_FORMAT(dateCreation, '%b %Y') as mois_label,
                COUNT(*) as nombre,
                COUNT(CASE WHEN statut = 'Résolue' THEN 1 END) as resolues
            FROM reclamation 
            WHERE dateCreation >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(dateCreation, '%Y-%m')
            ORDER BY mois ASC";
    try {
        $query = $db->query($sql);
        $stats['par_mois'] = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $stats['par_mois'] = [];
    }
    
    // 12. Performance par catégorie
    $sql = "SELECT 
                categorie,
                COUNT(*) as total,
                AVG(DATEDIFF(derniereModification, dateCreation)) as temps_moyen,
                COUNT(CASE WHEN statut = 'Résolue' THEN 1 END) as resolues
            FROM reclamation 
            GROUP BY categorie";
    try {
        $query = $db->query($sql);
        $stats['performance_categorie'] = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $stats['performance_categorie'] = [];
    }
    
    return $stats;
}

$statsPro = getStatistiquesPro($db, $reclamations);

// Préparer données JSON pour les graphiques
$joursLabels = array_column($statsPro['par_jour'], 'jour');
$joursData = array_column($statsPro['par_jour'], 'nombre');

$moisLabels = array_column($statsPro['par_mois'], 'mois_label');
$moisData = array_column($statsPro['par_mois'], 'nombre');

// Émotions pour graphique
$emotionLabels = ['Colère', 'Frustration', 'Tristesse', 'Anxiété', 'Détresse', 'Positif', 'Neutre'];
$emotionData = array_values($statsPro['emotions']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Pro - ImpactAble Dashboard</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <style>
        /* ========================================
           PALETTE IMPACTABLE
           ======================================== */
        :root {
            --brown: #4b2e16;
            --copper: #b47b47;
            --moss: #5e6d3b;
            --sage: #a9b97d;
            --sand: #f4ecdd;
            --white: #ffffff;
            
            /* Variations */
            --brown-light: #6b4e36;
            --moss-light: #7e8d5b;
            --copper-light: #d49b67;
            --sand-dark: #e4dcc8;
            
            /* Sémantiques */
            --success: #5e6d3b;
            --warning: #b47b47;
            --danger: #c45c3b;
            --info: #4b7b9b;
            
            --shadow: 0 4px 20px rgba(75, 46, 22, 0.12);
            --shadow-lg: 0 10px 40px rgba(75, 46, 22, 0.18);
            --radius: 16px;
            --radius-sm: 10px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--sand) 0%, #ebe3d4 100%);
            color: var(--brown);
            min-height: 100vh;
        }
        
        /* ========================================
           BACK BUTTON
           ======================================== */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: var(--white);
            color: var(--brown);
            text-decoration: none;
            font-weight: 600;
            font-size: 1em;
            border-radius: var(--radius-sm);
            border: 2px solid var(--sage);
            transition: all 0.3s;
            box-shadow: var(--shadow);
            margin-bottom: 24px;
        }
        
        .back-btn:hover {
            background: var(--moss);
            color: var(--white);
            border-color: var(--moss);
            transform: translateX(-5px);
        }
        
        .back-btn i {
            transition: transform 0.3s;
        }
        
        .back-btn:hover i {
            transform: translateX(-4px);
        }
        
        /* ========================================
           MAIN CONTENT
           ======================================== */
        .main-content {
            padding: 32px;
            max-width: 1600px;
            margin: 0 auto;
        }
        
        /* Header */
        .header-section {
            margin-bottom: 32px;
        }
        
        .header-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .header-title h2 {
            font-size: 2.2em;
            font-weight: 700;
            color: var(--brown);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .header-title h2 i {
            color: var(--moss);
        }
        
        .header-subtitle {
            color: var(--moss);
            font-size: 1.1em;
        }
        
        .live-badge {
            background: linear-gradient(135deg, var(--moss), var(--sage));
            color: var(--white);
            padding: 8px 18px;
            border-radius: 25px;
            font-size: 0.85em;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.9; transform: scale(1.02); }
        }
        
        .live-badge::before {
            content: '';
            width: 10px;
            height: 10px;
            background: var(--white);
            border-radius: 50%;
            animation: blink 1s infinite;
        }
        
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        
        /* ========================================
           KPI CARDS
           ======================================== */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        
        .kpi-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 24px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
            box-shadow: var(--shadow);
            border: 2px solid transparent;
        }
        
        .kpi-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
            border-color: var(--sage);
        }
        
        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--moss);
        }
        
        .kpi-card.copper::before { background: var(--copper); }
        .kpi-card.moss::before { background: var(--moss); }
        .kpi-card.sage::before { background: var(--sage); }
        .kpi-card.brown::before { background: var(--brown); }
        .kpi-card.danger::before { background: var(--danger); }
        
        .kpi-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }
        
        .kpi-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4em;
        }
        
        .kpi-card.moss .kpi-icon { background: rgba(94, 109, 59, 0.15); color: var(--moss); }
        .kpi-card.copper .kpi-icon { background: rgba(180, 123, 71, 0.15); color: var(--copper); }
        .kpi-card.sage .kpi-icon { background: rgba(169, 185, 125, 0.2); color: var(--moss); }
        .kpi-card.brown .kpi-icon { background: rgba(75, 46, 22, 0.12); color: var(--brown); }
        .kpi-card.danger .kpi-icon { background: rgba(196, 92, 59, 0.15); color: var(--danger); }
        
        .kpi-trend {
            font-size: 0.8em;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .kpi-trend.up {
            background: rgba(94, 109, 59, 0.15);
            color: var(--moss);
        }
        
        .kpi-trend.down {
            background: rgba(196, 92, 59, 0.15);
            color: var(--danger);
        }
        
        .kpi-value {
            font-size: 2.8em;
            font-weight: 700;
            color: var(--brown);
            margin-bottom: 8px;
            line-height: 1;
        }
        
        .kpi-label {
            color: var(--moss);
            font-size: 0.95em;
            font-weight: 600;
        }
        
        .kpi-sublabel {
            color: var(--copper);
            font-size: 0.8em;
            margin-top: 6px;
        }
        
        /* ========================================
           EMOTION SECTION - IA
           ======================================== */
        .emotion-section {
            background: linear-gradient(135deg, var(--moss) 0%, var(--brown) 100%);
            border-radius: var(--radius);
            padding: 30px;
            margin-bottom: 32px;
            box-shadow: var(--shadow-lg);
        }
        
        .emotion-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }
        
        .emotion-title {
            font-size: 1.4em;
            font-weight: 700;
            color: var(--white);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .emotion-title span {
            font-size: 1.3em;
        }
        
        .ai-badge {
            background: rgba(255,255,255,0.2);
            color: var(--white);
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.85em;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            backdrop-filter: blur(10px);
        }
        
        .emotion-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 16px;
        }
        
        .emotion-stat {
            text-align: center;
            padding: 20px 12px;
            background: rgba(255,255,255,0.12);
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,0.15);
            transition: all 0.3s;
            backdrop-filter: blur(10px);
        }
        
        .emotion-stat:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.2);
        }
        
        .emotion-emoji {
            font-size: 2.5em;
            margin-bottom: 12px;
        }
        
        .emotion-count {
            font-size: 2em;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 6px;
        }
        
        .emotion-label {
            color: rgba(255,255,255,0.85);
            font-size: 0.85em;
            font-weight: 500;
        }
        
        .emotion-bar {
            height: 5px;
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
            margin-top: 14px;
            overflow: hidden;
        }
        
        .emotion-bar-fill {
            height: 100%;
            border-radius: 3px;
            background: var(--sand);
            transition: width 1s ease-out;
        }
        
        /* ========================================
           CHARTS GRID
           ======================================== */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 32px;
        }
        
        .chart-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow);
            border: 2px solid var(--sand);
        }
        
        .chart-card.full-width {
            grid-column: span 2;
        }
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--sand);
        }
        
        .chart-title {
            font-size: 1.15em;
            font-weight: 700;
            color: var(--brown);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chart-title i {
            color: var(--moss);
        }
        
        .chart-subtitle {
            color: var(--copper);
            font-size: 0.85em;
            margin-top: 4px;
        }
        
        /* ========================================
           INSIGHTS
           ======================================== */
        .insights-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }
        
        .insight-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow);
            border-left: 5px solid var(--moss);
        }
        
        .insight-card.alert {
            border-left-color: var(--danger);
        }
        
        .insight-card.success {
            border-left-color: var(--moss);
        }
        
        .insight-card.info {
            border-left-color: var(--copper);
        }
        
        .insight-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5em;
            margin-bottom: 16px;
        }
        
        .insight-card.alert .insight-icon {
            background: rgba(196, 92, 59, 0.12);
            color: var(--danger);
        }
        
        .insight-card.success .insight-icon {
            background: rgba(94, 109, 59, 0.12);
            color: var(--moss);
        }
        
        .insight-card.info .insight-icon {
            background: rgba(180, 123, 71, 0.12);
            color: var(--copper);
        }
        
        .insight-title {
            font-size: 1.1em;
            font-weight: 700;
            color: var(--brown);
            margin-bottom: 8px;
        }
        
        .insight-desc {
            color: var(--moss);
            font-size: 0.9em;
            line-height: 1.6;
        }
        
        .insight-value {
            font-size: 1.5em;
            font-weight: 700;
            margin-top: 12px;
        }
        
        /* ========================================
           PERFORMANCE TABLE
           ======================================== */
        .performance-section {
            background: var(--white);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow);
            border: 2px solid var(--sand);
        }
        
        .performance-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--sand);
        }
        
        .performance-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .performance-table th {
            text-align: left;
            padding: 14px 16px;
            color: var(--moss);
            font-weight: 700;
            font-size: 0.85em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: var(--sand);
            border-radius: 8px;
        }
        
        .performance-table th:first-child {
            border-radius: 8px 0 0 8px;
        }
        
        .performance-table th:last-child {
            border-radius: 0 8px 8px 0;
        }
        
        .performance-table td {
            padding: 16px;
            border-bottom: 1px solid var(--sand);
            color: var(--brown);
        }
        
        .performance-table tr:hover td {
            background: rgba(169, 185, 125, 0.08);
        }
        
        .category-badge {
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9em;
            background: var(--sand);
            color: var(--brown);
            border: 1px solid var(--sage);
        }
        
        .progress-mini {
            width: 100px;
            height: 8px;
            background: var(--sand);
            border-radius: 4px;
            overflow: hidden;
            display: inline-block;
            margin-right: 12px;
            vertical-align: middle;
        }
        
        .progress-mini-fill {
            height: 100%;
            border-radius: 4px;
        }
        
        .progress-mini-fill.high { background: var(--moss); }
        .progress-mini-fill.medium { background: var(--copper); }
        .progress-mini-fill.low { background: var(--danger); }
        
        /* ========================================
           RESPONSIVE
           ======================================== */
        @media (max-width: 1200px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
            .chart-card.full-width {
                grid-column: span 1;
            }
            .emotion-grid {
                grid-template-columns: repeat(4, 1fr);
            }
            .insights-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .emotion-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .insights-grid {
                grid-template-columns: 1fr;
            }
            .main-content {
                padding: 16px;
            }
            .header-title {
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <main class="main-content">
        <!-- Bouton Retour -->
        <a href="admin_dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Retour au Dashboard
        </a>
        <!-- Header -->
        <div class="header-section">
            <div class="header-title">
                <div>
                    <h2><i class="fas fa-chart-network"></i> Analytics Avancées</h2>
                    <p class="header-subtitle">Vue d'ensemble des performances et analyses IA</p>
                </div>
                <div class="live-badge">EN DIRECT</div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="kpi-grid">
            <div class="kpi-card moss">
                <div class="kpi-header">
                    <div class="kpi-icon"><i class="fas fa-inbox"></i></div>
                    <div class="kpi-trend <?= $statsPro['tendance'] >= 0 ? 'up' : 'down' ?>">
                        <?= $statsPro['tendance'] >= 0 ? '↑' : '↓' ?> <?= abs($statsPro['tendance']) ?>%
                    </div>
                </div>
                <div class="kpi-value" data-count="<?= $statsPro['total_reclamations'] ?>">0</div>
                <div class="kpi-label">Total Réclamations</div>
                <div class="kpi-sublabel"><?= $statsPro['cette_semaine'] ?> cette semaine</div>
            </div>
            
            <div class="kpi-card sage">
                <div class="kpi-header">
                    <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
                </div>
                <div class="kpi-value" data-count="<?= $statsPro['total_resolues'] ?>">0</div>
                <div class="kpi-label">Résolues</div>
                <div class="kpi-sublabel"><?= $statsPro['taux_resolution'] ?>% taux de succès</div>
            </div>
            
            <div class="kpi-card danger">
                <div class="kpi-header">
                    <div class="kpi-icon"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
                <div class="kpi-value" data-count="<?= $statsPro['urgentes_non_resolues'] ?>">0</div>
                <div class="kpi-label">Urgentes Ouvertes</div>
                <div class="kpi-sublabel">Action requise</div>
            </div>
            
            <div class="kpi-card copper">
                <div class="kpi-header">
                    <div class="kpi-icon"><i class="fas fa-reply-all"></i></div>
                </div>
                <div class="kpi-value" data-count="<?= $statsPro['total_reponses'] ?>">0</div>
                <div class="kpi-label">Réponses</div>
                <div class="kpi-sublabel">Total envoyées</div>
            </div>
            
            <div class="kpi-card brown">
                <div class="kpi-header">
                    <div class="kpi-icon"><i class="fas fa-clock"></i></div>
                </div>
                <div class="kpi-value"><?= $statsPro['temps_moyen_resolution'] ?><small style="font-size:0.35em;color:var(--copper)">j</small></div>
                <div class="kpi-label">Temps Moyen</div>
                <div class="kpi-sublabel">Jours de résolution</div>
            </div>
            
            <div class="kpi-card moss">
                <div class="kpi-header">
                    <div class="kpi-icon"><i class="fas fa-percentage"></i></div>
                </div>
                <div class="kpi-value" data-count="<?= $statsPro['taux_resolution'] ?>" data-suffix="%">0%</div>
                <div class="kpi-label">Taux Résolution</div>
                <div class="kpi-sublabel">Objectif: 85%</div>
            </div>
        </div>

        <!-- Emotion Detection Stats -->
        <div class="emotion-section">
            <div class="emotion-header">
                <div class="emotion-title">
                    <span>🧠</span> Analyse Émotionnelle des Réclamations
                </div>
                <div class="ai-badge">
                    <i class="fas fa-robot"></i> Propulsé par IA
                </div>
            </div>
            
            <?php 
            $totalEmotions = array_sum($statsPro['emotions']);
            $emotionEmojis = ['colere' => '😡', 'frustration' => '😤', 'tristesse' => '😢', 'anxiete' => '😰', 'detresse' => '😭', 'positif' => '😊', 'neutre' => '😐'];
            $emotionNames = ['colere' => 'Colère', 'frustration' => 'Frustration', 'tristesse' => 'Tristesse', 'anxiete' => 'Anxiété', 'detresse' => 'Détresse', 'positif' => 'Positif', 'neutre' => 'Neutre'];
            ?>
            
            <div class="emotion-grid">
                <?php foreach ($statsPro['emotions'] as $key => $count): ?>
                    <?php $percent = $totalEmotions > 0 ? round(($count / $totalEmotions) * 100) : 0; ?>
                    <div class="emotion-stat">
                        <div class="emotion-emoji"><?= $emotionEmojis[$key] ?></div>
                        <div class="emotion-count"><?= $count ?></div>
                        <div class="emotion-label"><?= $emotionNames[$key] ?></div>
                        <div class="emotion-bar">
                            <div class="emotion-bar-fill" style="width: <?= $percent ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <div class="chart-card full-width">
                <div class="chart-header">
                    <div>
                        <div class="chart-title"><i class="fas fa-chart-area"></i> Évolution des Réclamations</div>
                        <div class="chart-subtitle">Tendance sur les 14 derniers jours</div>
                    </div>
                </div>
                <div id="chartEvolution" style="height: 350px;"></div>
            </div>
            
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <div class="chart-title"><i class="fas fa-chart-pie"></i> Par Priorité</div>
                        <div class="chart-subtitle">Distribution actuelle</div>
                    </div>
                </div>
                <div id="chartPriorite" style="height: 300px;"></div>
            </div>
            
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <div class="chart-title"><i class="fas fa-tags"></i> Par Catégorie</div>
                        <div class="chart-subtitle">Répartition thématique</div>
                    </div>
                </div>
                <div id="chartCategorie" style="height: 300px;"></div>
            </div>
            
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <div class="chart-title"><i class="fas fa-brain"></i> Émotions Détectées</div>
                        <div class="chart-subtitle">Analyse IA automatique</div>
                    </div>
                </div>
                <div id="chartEmotions" style="height: 300px;"></div>
            </div>
            
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <div class="chart-title"><i class="fas fa-tasks"></i> Par Statut</div>
                        <div class="chart-subtitle">État de traitement</div>
                    </div>
                </div>
                <div id="chartStatut" style="height: 300px;"></div>
            </div>
        </div>

        <!-- Insights -->
        <div class="insights-grid">
            <div class="insight-card <?= $statsPro['urgentes_non_resolues'] > 0 ? 'alert' : 'success' ?>">
                <div class="insight-icon">
                    <i class="fas fa-<?= $statsPro['urgentes_non_resolues'] > 0 ? 'exclamation-circle' : 'check-circle' ?>"></i>
                </div>
                <div class="insight-title">
                    <?= $statsPro['urgentes_non_resolues'] > 0 ? 'Attention Requise' : 'Tout est sous contrôle' ?>
                </div>
                <div class="insight-desc">
                    <?= $statsPro['urgentes_non_resolues'] > 0 
                        ? $statsPro['urgentes_non_resolues'] . ' réclamation(s) urgente(s) nécessitent une action immédiate.'
                        : 'Aucune réclamation urgente en attente. Excellent travail !' ?>
                </div>
            </div>
            
            <div class="insight-card info">
                <div class="insight-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="insight-title">Tendance Hebdomadaire</div>
                <div class="insight-desc">
                    <?= $statsPro['tendance'] >= 0 
                        ? 'Augmentation de ' . $statsPro['tendance'] . '% des réclamations cette semaine.'
                        : 'Diminution de ' . abs($statsPro['tendance']) . '% des réclamations. Bonne tendance !' ?>
                </div>
                <div class="insight-value" style="color: <?= $statsPro['tendance'] >= 0 ? 'var(--copper)' : 'var(--moss)' ?>">
                    <?= $statsPro['cette_semaine'] ?> vs <?= $statsPro['semaine_derniere'] ?>
                </div>
            </div>
            
            <div class="insight-card <?= $statsPro['taux_resolution'] >= 70 ? 'success' : 'alert' ?>">
                <div class="insight-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="insight-title">Performance Globale</div>
                <div class="insight-desc">
                    <?= $statsPro['taux_resolution'] >= 70 
                        ? 'Excellent taux de résolution ! Continuez ainsi.'
                        : 'Le taux de résolution peut être amélioré. Objectif: 70%' ?>
                </div>
                <div class="insight-value" style="color: <?= $statsPro['taux_resolution'] >= 70 ? 'var(--moss)' : 'var(--danger)' ?>">
                    <?= $statsPro['taux_resolution'] ?>%
                </div>
            </div>
        </div>

        <!-- Performance Table -->
        <div class="performance-section">
            <div class="performance-header">
                <div class="chart-title"><i class="fas fa-medal"></i> Performance par Catégorie</div>
            </div>
            <table class="performance-table">
                <thead>
                    <tr>
                        <th>Catégorie</th>
                        <th>Total</th>
                        <th>Résolues</th>
                        <th>Taux de Succès</th>
                        <th>Temps Moyen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statsPro['performance_categorie'] as $perf): ?>
                        <?php 
                        $taux = $perf['total'] > 0 ? round(($perf['resolues'] / $perf['total']) * 100) : 0;
                        $temps = round($perf['temps_moyen'] ?? 0, 1);
                        $tauxClass = $taux >= 70 ? 'high' : ($taux >= 40 ? 'medium' : 'low');
                        ?>
                        <tr>
                            <td><span class="category-badge"><?= htmlspecialchars($perf['categorie'] ?? 'N/A') ?></span></td>
                            <td><strong><?= $perf['total'] ?></strong></td>
                            <td><?= $perf['resolues'] ?></td>
                            <td>
                                <div class="progress-mini">
                                    <div class="progress-mini-fill <?= $tauxClass ?>" style="width: <?= $taux ?>%"></div>
                                </div>
                                <strong><?= $taux ?>%</strong>
                            </td>
                            <td><?= $temps ?> jours</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        // Animation des compteurs
        document.querySelectorAll('.kpi-value[data-count]').forEach(el => {
            const target = parseInt(el.dataset.count);
            const suffix = el.dataset.suffix || '';
            const duration = 2000;
            const start = Date.now();
            
            function animate() {
                const elapsed = Date.now() - start;
                const progress = Math.min(elapsed / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                const current = Math.floor(target * eased);
                el.textContent = current + suffix;
                
                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    el.textContent = target + suffix;
                }
            }
            animate();
        });

        // Couleurs ImpactAble pour ApexCharts
        const impactColors = {
            brown: '#4b2e16',
            copper: '#b47b47',
            moss: '#5e6d3b',
            sage: '#a9b97d',
            sand: '#f4ecdd',
            danger: '#c45c3b'
        };
        
        const chartPalette = ['#5e6d3b', '#b47b47', '#4b2e16', '#a9b97d', '#c45c3b', '#6b8e23', '#d4a574'];

        // Graphique Évolution
        new ApexCharts(document.querySelector("#chartEvolution"), {
            chart: { 
                type: 'area', 
                height: 350,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            series: [{ name: 'Réclamations', data: <?= json_encode($joursData) ?> }],
            xaxis: { 
                categories: <?= json_encode($joursLabels) ?>, 
                labels: { style: { colors: impactColors.moss } }
            },
            yaxis: { labels: { style: { colors: impactColors.moss } } },
            colors: [impactColors.moss],
            fill: { 
                type: 'gradient', 
                gradient: { 
                    shadeIntensity: 1, 
                    opacityFrom: 0.5, 
                    opacityTo: 0.1,
                    colorStops: [
                        { offset: 0, color: impactColors.moss, opacity: 0.4 },
                        { offset: 100, color: impactColors.sage, opacity: 0.1 }
                    ]
                } 
            },
            stroke: { curve: 'smooth', width: 3 },
            grid: { borderColor: impactColors.sand },
            dataLabels: { enabled: false },
            tooltip: { theme: 'light' }
        }).render();

        // Graphique Priorité
        <?php
        $prioLabels = array_column($statsPro['par_priorite'], 'priorite');
        $prioData = array_column($statsPro['par_priorite'], 'nombre');
        ?>
        new ApexCharts(document.querySelector("#chartPriorite"), {
            chart: { type: 'donut', height: 300, fontFamily: 'Inter, sans-serif' },
            series: <?= json_encode($prioData) ?>,
            labels: <?= json_encode($prioLabels) ?>,
            colors: [impactColors.danger, impactColors.copper, impactColors.moss],
            legend: { position: 'bottom', labels: { colors: impactColors.brown } },
            plotOptions: { 
                pie: { 
                    donut: { 
                        size: '60%', 
                        labels: { 
                            show: true, 
                            total: { 
                                show: true, 
                                label: 'Total', 
                                color: impactColors.brown,
                                fontWeight: 700
                            } 
                        } 
                    } 
                } 
            },
            dataLabels: { enabled: false },
            stroke: { width: 3, colors: ['#fff'] }
        }).render();

        // Graphique Catégorie
        <?php
        $catLabels = array_column($statsPro['par_categorie'], 'categorie');
        $catData = array_column($statsPro['par_categorie'], 'nombre');
        ?>
        new ApexCharts(document.querySelector("#chartCategorie"), {
            chart: { type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
            series: [{ name: 'Réclamations', data: <?= json_encode($catData) ?> }],
            xaxis: { categories: <?= json_encode($catLabels) ?>, labels: { style: { colors: impactColors.brown } } },
            yaxis: { labels: { style: { colors: impactColors.moss } } },
            colors: chartPalette,
            plotOptions: { bar: { borderRadius: 8, horizontal: true, distributed: true } },
            grid: { borderColor: impactColors.sand },
            dataLabels: { enabled: false },
            legend: { show: false }
        }).render();

        // Graphique Émotions
        new ApexCharts(document.querySelector("#chartEmotions"), {
            chart: { type: 'polarArea', height: 300, fontFamily: 'Inter, sans-serif' },
            series: <?= json_encode($emotionData) ?>,
            labels: <?= json_encode($emotionLabels) ?>,
            colors: ['#c45c3b', '#d4874d', '#5C6BC0', '#7E57C2', '#EC407A', '#5e6d3b', '#78909C'],
            legend: { position: 'bottom', labels: { colors: impactColors.brown } },
            stroke: { width: 1, colors: ['#fff'] },
            fill: { opacity: 0.85 }
        }).render();

        // Graphique Statut
        <?php
        $statLabels = array_column($statsPro['par_statut'], 'statut');
        $statData = array_column($statsPro['par_statut'], 'nombre');
        ?>
        new ApexCharts(document.querySelector("#chartStatut"), {
            chart: { type: 'radialBar', height: 300, fontFamily: 'Inter, sans-serif' },
            series: <?= json_encode($statData) ?>,
            labels: <?= json_encode($statLabels) ?>,
            colors: [impactColors.copper, '#4b7b9b', impactColors.moss, '#8b949e'],
            plotOptions: {
                radialBar: {
                    dataLabels: {
                        name: { fontSize: '14px', color: impactColors.moss },
                        value: { fontSize: '20px', color: impactColors.brown, fontWeight: 700 },
                        total: { 
                            show: true, 
                            label: 'Total', 
                            color: impactColors.brown,
                            formatter: function(w) { return w.globals.seriesTotals.reduce((a,b) => a+b, 0); } 
                        }
                    }
                }
            }
        }).render();
    </script>
</body>
</html>
