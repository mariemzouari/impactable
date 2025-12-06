<?php
session_start();
require_once(__DIR__ . '/../../controller/ReclamationController.php');
require_once(__DIR__ . '/../../controller/ReponseController.php');
require_once(__DIR__ . '/../../CONFIGRRATION/config.php');
require_once(__DIR__ . '/../../SERVICE/PrioriteIntelligente.php');

$controller = new ReclamationController();
$reponseController = new ReponseController();
$reclamations = $controller->listReclamations();
$stats = $controller->getStats();

// Fonction pour obtenir les statistiques avancées
function getStatistiquesAvancees($db) {
    $stats = [];
    
    // Réclamations par jour (7 derniers jours)
    $sql = "SELECT DATE(dateCreation) as jour, COUNT(*) as nombre 
            FROM reclamation 
            WHERE dateCreation >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(dateCreation) 
            ORDER BY jour ASC";
    try {
        $query = $db->query($sql);
        $stats['par_jour'] = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $stats['par_jour'] = [];
    }
    
    // Réclamations par catégorie
    $sql = "SELECT categorie, COUNT(*) as nombre FROM reclamation GROUP BY categorie ORDER BY nombre DESC";
    try {
        $query = $db->query($sql);
        $stats['par_categorie'] = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $stats['par_categorie'] = [];
    }
    
    // Réclamations par priorité
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
    
    // Réclamations par statut
    $sql = "SELECT statut, COUNT(*) as nombre FROM reclamation GROUP BY statut";
    try {
        $query = $db->query($sql);
        $stats['par_statut'] = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $stats['par_statut'] = [];
    }
    
    // Top 5 utilisateurs (plus de réclamations)
    $sql = "SELECT 
                COALESCE(CONCAT(nom, ' ', prenom), CONCAT('Utilisateur #', utilisateurId)) as utilisateur,
                utilisateurId,
                email,
                COUNT(*) as nombre 
            FROM reclamation 
            GROUP BY utilisateurId, nom, prenom, email
            ORDER BY nombre DESC 
            LIMIT 5";
    try {
        $query = $db->query($sql);
        $stats['top_utilisateurs'] = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $stats['top_utilisateurs'] = [];
    }
    
    // Temps moyen de résolution (en jours)
    $sql = "SELECT 
                AVG(DATEDIFF(derniereModification, dateCreation)) as temps_moyen
            FROM reclamation 
            WHERE statut = 'Résolue'";
    try {
        $query = $db->query($sql);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $stats['temps_moyen_resolution'] = round($result['temps_moyen'] ?? 0, 1);
    } catch (Exception $e) {
        $stats['temps_moyen_resolution'] = 0;
    }
    
    // Réclamations par mois (6 derniers mois)
    $sql = "SELECT 
                DATE_FORMAT(dateCreation, '%Y-%m') as mois,
                DATE_FORMAT(dateCreation, '%M %Y') as mois_label,
                COUNT(*) as nombre 
            FROM reclamation 
            WHERE dateCreation >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(dateCreation, '%Y-%m')
            ORDER BY mois ASC";
    try {
        $query = $db->query($sql);
        $stats['par_mois'] = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $stats['par_mois'] = [];
    }
    
    // Taux de résolution
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
    } catch (Exception $e) {
        $stats['taux_resolution'] = 0;
    }
    
    // Nombre de réponses total
    $sql = "SELECT COUNT(*) as total FROM reponse";
    try {
        $query = $db->query($sql);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $stats['total_reponses'] = $result['total'] ?? 0;
    } catch (Exception $e) {
        $stats['total_reponses'] = 0;
    }
    
    // Réclamations urgentes non résolues
    $sql = "SELECT COUNT(*) as nombre FROM reclamation WHERE priorite = 'Urgente' AND statut != 'Résolue' AND statut != 'Fermée'";
    try {
        $query = $db->query($sql);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $stats['urgentes_non_resolues'] = $result['nombre'] ?? 0;
    } catch (Exception $e) {
        $stats['urgentes_non_resolues'] = 0;
    }
    
    return $stats;
}

$db = config::getConnexion();
$statsAvancees = getStatistiquesAvancees($db);

// Préparer les données pour les graphiques
$joursLabels = [];
$joursData = [];
foreach ($statsAvancees['par_jour'] as $jour) {
    $joursLabels[] = date('d/m', strtotime($jour['jour']));
    $joursData[] = $jour['nombre'];
}

$categoriesLabels = [];
$categoriesData = [];
foreach ($statsAvancees['par_categorie'] as $cat) {
    $categoriesLabels[] = $cat['categorie'];
    $categoriesData[] = $cat['nombre'];
}

$prioriteLabels = [];
$prioriteData = [];
$prioriteColors = [];
foreach ($statsAvancees['par_priorite'] as $prio) {
    $prioriteLabels[] = $prio['priorite'];
    $prioriteData[] = $prio['nombre'];
    switch ($prio['priorite']) {
        case 'Urgente': $prioriteColors[] = '#D32F2F'; break;
        case 'Moyenne': $prioriteColors[] = '#FF9800'; break;
        case 'Faible': $prioriteColors[] = '#4CAF50'; break;
        default: $prioriteColors[] = '#9E9E9E';
    }
}

$statutLabels = [];
$statutData = [];
$statutColors = [];
foreach ($statsAvancees['par_statut'] as $st) {
    $statutLabels[] = $st['statut'];
    $statutData[] = $st['nombre'];
    switch ($st['statut']) {
        case 'En attente': $statutColors[] = '#FFC107'; break;
        case 'En cours': $statutColors[] = '#2196F3'; break;
        case 'Résolue': $statutColors[] = '#4CAF50'; break;
        case 'Fermée': $statutColors[] = '#9E9E9E'; break;
        default: $statutColors[] = '#607D8B';
    }
}

$moisLabels = [];
$moisData = [];
foreach ($statsAvancees['par_mois'] as $mois) {
    $moisLabels[] = $mois['mois_label'];
    $moisData[] = $mois['nombre'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques Avancées - ImpactAble</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin-style.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* Styles spécifiques pour les statistiques */
        .stats-dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: var(--white);
            border-radius: var(--radius);
            padding: 25px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: all 0.3s;
            border-left: 4px solid var(--moss);
        }
        
        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(75, 46, 22, 0.2);
        }
        
        .stat-box.urgent {
            border-left-color: #D32F2F;
        }
        
        .stat-box.success {
            border-left-color: #4CAF50;
        }
        
        .stat-box.warning {
            border-left-color: #FF9800;
        }
        
        .stat-box.info {
            border-left-color: #2196F3;
        }
        
        .stat-box .stat-number {
            font-size: 3em;
            font-weight: 700;
            color: var(--brown);
            line-height: 1;
        }
        
        .stat-box .stat-label {
            font-size: 0.95em;
            color: var(--moss);
            margin-top: 10px;
            font-weight: 600;
        }
        
        .stat-box .stat-icon {
            font-size: 2em;
            margin-bottom: 15px;
            opacity: 0.7;
        }
        
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .chart-container {
            background: var(--white);
            border-radius: var(--radius);
            padding: 25px;
            box-shadow: var(--shadow);
        }
        
        .chart-container h3 {
            color: var(--brown);
            margin-bottom: 20px;
            font-size: 1.2em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chart-wrapper {
            position: relative;
            height: 300px;
        }
        
        .top-users-list {
            background: var(--white);
            border-radius: var(--radius);
            padding: 25px;
            box-shadow: var(--shadow);
        }
        
        .top-users-list h3 {
            color: var(--brown);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: var(--sand);
            border-radius: 10px;
            margin-bottom: 10px;
            transition: all 0.3s;
        }
        
        .user-item:hover {
            background: #fff3e8;
            transform: translateX(5px);
        }
        
        .user-rank {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2em;
            margin-right: 15px;
        }
        
        .user-rank.gold {
            background: linear-gradient(135deg, #FFD700, #FFA000);
            color: white;
        }
        
        .user-rank.silver {
            background: linear-gradient(135deg, #C0C0C0, #9E9E9E);
            color: white;
        }
        
        .user-rank.bronze {
            background: linear-gradient(135deg, #CD7F32, #8B4513);
            color: white;
        }
        
        .user-rank.default {
            background: var(--sage);
            color: white;
        }
        
        .user-info-stats {
            flex: 1;
        }
        
        .user-info-stats .user-name {
            font-weight: 600;
            color: var(--brown);
        }
        
        .user-info-stats .user-email {
            font-size: 0.85em;
            color: var(--moss);
        }
        
        .user-count {
            font-size: 1.5em;
            font-weight: 700;
            color: var(--copper);
        }
        
        .ia-analysis-section {
            background: linear-gradient(135deg, var(--moss), var(--brown));
            border-radius: var(--radius);
            padding: 30px;
            color: white;
            margin-bottom: 30px;
        }
        
        .ia-analysis-section h3 {
            margin-bottom: 20px;
            font-size: 1.4em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .ia-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .ia-feature {
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
            padding: 20px;
            backdrop-filter: blur(10px);
        }
        
        .ia-feature h4 {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .ia-feature p {
            font-size: 0.9em;
            opacity: 0.9;
            line-height: 1.6;
        }
        
        .performance-indicator {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 10px;
        }
        
        .performance-bar {
            flex: 1;
            height: 8px;
            background: rgba(255,255,255,0.3);
            border-radius: 4px;
            overflow: hidden;
        }
        
        .performance-bar .fill {
            height: 100%;
            border-radius: 4px;
            transition: width 1s ease-out;
        }
        
        .performance-bar .fill.success {
            background: #4CAF50;
        }
        
        .performance-bar .fill.warning {
            background: #FF9800;
        }
        
        .performance-bar .fill.danger {
            background: #D32F2F;
        }
        
        @media (max-width: 768px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-dashboard {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body class="with-sidebar">
    <aside class="sidebar">
        <div>
            <div class="logo">ImpactAble</div>
            <nav class="nav-links">
                <a href="admin_dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="statistiques_avancees.php" class="active">
                    <i class="fas fa-chart-line"></i> Statistiques
                </a>
                <a href="gestion_reclamation/addReclamation.php">
                    <i class="fas fa-file-alt"></i> Réclamations
                </a>
                <a href="reponsecrud/toutes_reponses.php">
                    <i class="fas fa-comments"></i> Réponses
                </a>
                <a href="../frontoffice/index.php" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Front Office
                </a>
            </nav>
        </div>
        <footer>© 2025 ImpactAble</footer>
    </aside>

    <main>
        <div class="container">
            <header>
                <h1><i class="fas fa-chart-line"></i> Statistiques Avancées & Intelligence Artificielle</h1>
                <div class="user-info">Admin</div>
            </header>

            <!-- Section IA -->
            <section class="ia-analysis-section">
                <h3><i class="fas fa-brain"></i> Système de Priorisation Intelligente</h3>
                <div class="ia-features">
                    <div class="ia-feature">
                        <h4><i class="fas fa-robot"></i> Analyse Automatique</h4>
                        <p>Notre système analyse automatiquement le contenu des réclamations pour détecter l'urgence et assigner une priorité intelligente.</p>
                    </div>
                    <div class="ia-feature">
                        <h4><i class="fas fa-search"></i> Détection de Mots-clés</h4>
                        <p>Plus de 100 mots-clés sont analysés pour identifier les situations urgentes, importantes ou normales.</p>
                    </div>
                    <div class="ia-feature">
                        <h4><i class="fas fa-percentage"></i> Niveau de Confiance</h4>
                        <p>Chaque analyse est accompagnée d'un score de confiance pour garantir la fiabilité des priorisations.</p>
                    </div>
                    <div class="ia-feature">
                        <h4><i class="fas fa-tachometer-alt"></i> Taux de Résolution</h4>
                        <div class="performance-indicator">
                            <span><?= $statsAvancees['taux_resolution'] ?>%</span>
                            <div class="performance-bar">
                                <div class="fill <?= $statsAvancees['taux_resolution'] >= 70 ? 'success' : ($statsAvancees['taux_resolution'] >= 40 ? 'warning' : 'danger') ?>" 
                                     style="width: <?= $statsAvancees['taux_resolution'] ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- KPIs Principaux -->
            <section class="stats-dashboard">
                <div class="stat-box">
                    <div class="stat-icon">📊</div>
                    <div class="stat-number"><?= $stats['total'] ?? 0 ?></div>
                    <div class="stat-label">Total Réclamations</div>
                </div>
                
                <div class="stat-box urgent">
                    <div class="stat-icon">🔴</div>
                    <div class="stat-number"><?= $statsAvancees['urgentes_non_resolues'] ?></div>
                    <div class="stat-label">Urgentes Non Résolues</div>
                </div>
                
                <div class="stat-box success">
                    <div class="stat-icon">✅</div>
                    <div class="stat-number"><?= $stats['resolues'] ?? 0 ?></div>
                    <div class="stat-label">Résolues</div>
                </div>
                
                <div class="stat-box warning">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-number"><?= $stats['en_attente'] ?? 0 ?></div>
                    <div class="stat-label">En Attente</div>
                </div>
                
                <div class="stat-box info">
                    <div class="stat-icon">🔄</div>
                    <div class="stat-number"><?= $stats['en_cours'] ?? 0 ?></div>
                    <div class="stat-label">En Cours</div>
                </div>
                
                <div class="stat-box">
                    <div class="stat-icon">💬</div>
                    <div class="stat-number"><?= $statsAvancees['total_reponses'] ?></div>
                    <div class="stat-label">Réponses Total</div>
                </div>
                
                <div class="stat-box success">
                    <div class="stat-icon">📈</div>
                    <div class="stat-number"><?= $statsAvancees['taux_resolution'] ?>%</div>
                    <div class="stat-label">Taux de Résolution</div>
                </div>
                
                <div class="stat-box info">
                    <div class="stat-icon">⏱️</div>
                    <div class="stat-number"><?= $statsAvancees['temps_moyen_resolution'] ?></div>
                    <div class="stat-label">Jours Moy. Résolution</div>
                </div>
            </section>

            <!-- Graphiques -->
            <section class="charts-grid">
                <!-- Graphique Réclamations par Jour -->
                <div class="chart-container">
                    <h3><i class="fas fa-calendar-alt"></i> Réclamations (7 derniers jours)</h3>
                    <div class="chart-wrapper">
                        <canvas id="chartParJour"></canvas>
                    </div>
                </div>
                
                <!-- Graphique par Priorité -->
                <div class="chart-container">
                    <h3><i class="fas fa-exclamation-triangle"></i> Répartition par Priorité</h3>
                    <div class="chart-wrapper">
                        <canvas id="chartPriorite"></canvas>
                    </div>
                </div>
                
                <!-- Graphique par Catégorie -->
                <div class="chart-container">
                    <h3><i class="fas fa-tags"></i> Répartition par Catégorie</h3>
                    <div class="chart-wrapper">
                        <canvas id="chartCategorie"></canvas>
                    </div>
                </div>
                
                <!-- Graphique par Statut -->
                <div class="chart-container">
                    <h3><i class="fas fa-info-circle"></i> Répartition par Statut</h3>
                    <div class="chart-wrapper">
                        <canvas id="chartStatut"></canvas>
                    </div>
                </div>
                
                <!-- Graphique Évolution Mensuelle -->
                <div class="chart-container" style="grid-column: span 2;">
                    <h3><i class="fas fa-chart-area"></i> Évolution Mensuelle (6 derniers mois)</h3>
                    <div class="chart-wrapper">
                        <canvas id="chartMensuel"></canvas>
                    </div>
                </div>
            </section>

            <!-- Top Utilisateurs -->
            <section class="top-users-list">
                <h3><i class="fas fa-users"></i> Top 5 Utilisateurs (Plus de réclamations)</h3>
                <?php if (empty($statsAvancees['top_utilisateurs'])): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Aucune donnée disponible</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($statsAvancees['top_utilisateurs'] as $index => $user): ?>
                        <div class="user-item">
                            <div class="user-rank <?= $index === 0 ? 'gold' : ($index === 1 ? 'silver' : ($index === 2 ? 'bronze' : 'default')) ?>">
                                <?= $index + 1 ?>
                            </div>
                            <div class="user-info-stats">
                                <div class="user-name"><?= htmlspecialchars($user['utilisateur']) ?></div>
                                <div class="user-email"><?= htmlspecialchars($user['email'] ?? 'Email non renseigné') ?></div>
                            </div>
                            <div class="user-count">
                                <?= $user['nombre'] ?> <small>réclamations</small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <script>
        // Configuration commune des graphiques
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#4b2e16';
        
        // Couleurs du thème
        const themeColors = {
            brown: '#4b2e16',
            copper: '#b47b47',
            moss: '#5e6d3b',
            sage: '#a9b97d',
            sand: '#f4ecdd'
        };

        // Graphique Réclamations par Jour
        new Chart(document.getElementById('chartParJour'), {
            type: 'line',
            data: {
                labels: <?= json_encode($joursLabels) ?>,
                datasets: [{
                    label: 'Réclamations',
                    data: <?= json_encode($joursData) ?>,
                    borderColor: themeColors.moss,
                    backgroundColor: 'rgba(94, 109, 59, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: themeColors.moss,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        // Graphique par Priorité (Donut)
        new Chart(document.getElementById('chartPriorite'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($prioriteLabels) ?>,
                datasets: [{
                    data: <?= json_encode($prioriteData) ?>,
                    backgroundColor: <?= json_encode($prioriteColors) ?>,
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // Graphique par Catégorie (Bar horizontal)
        new Chart(document.getElementById('chartCategorie'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($categoriesLabels) ?>,
                datasets: [{
                    label: 'Nombre',
                    data: <?= json_encode($categoriesData) ?>,
                    backgroundColor: [
                        themeColors.moss,
                        themeColors.copper,
                        themeColors.sage,
                        themeColors.brown,
                        '#FF9800',
                        '#2196F3',
                        '#9C27B0',
                        '#00BCD4',
                        '#E91E63',
                        '#607D8B'
                    ],
                    borderRadius: 8
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Graphique par Statut (Pie)
        new Chart(document.getElementById('chartStatut'), {
            type: 'pie',
            data: {
                labels: <?= json_encode($statutLabels) ?>,
                datasets: [{
                    data: <?= json_encode($statutData) ?>,
                    backgroundColor: <?= json_encode($statutColors) ?>,
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // Graphique Évolution Mensuelle (Area)
        new Chart(document.getElementById('chartMensuel'), {
            type: 'line',
            data: {
                labels: <?= json_encode($moisLabels) ?>,
                datasets: [{
                    label: 'Réclamations',
                    data: <?= json_encode($moisData) ?>,
                    borderColor: themeColors.copper,
                    backgroundColor: 'rgba(180, 123, 71, 0.2)',
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: themeColors.copper,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    </script>
</body>
</html>

