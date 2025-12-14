<?php
session_start();
require_once(__DIR__ . '/../../controller/ReclamationController.php');
require_once(__DIR__ . '/../../config.php');

$controller = new ReclamationController();
$smartStats = $controller->getSmartDashboardStats();
$basicStats = $smartStats['basic'];
$byCategory = $smartStats['by_category'];
$byPriority = $smartStats['by_priority'];
$byStatus = $smartStats['by_status'];
$perDay = $smartStats['per_day'];
$topUsers = $smartStats['top_users'];
$avgResolutionTime = $smartStats['avg_resolution_time'];
$resolutionRate = $smartStats['resolution_rate'];
$urgentPending = $smartStats['urgent_pending'];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧠 Dashboard Intelligent - Système IA de Réclamations</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --brown: #4b2e16;
            --copper: #b47b47;
            --moss: #5e6d3b;
            --sage: #a9b97d;
            --sand: #f4ecdd;
            --white: #fffaf5;
            --radius: 14px;
            --shadow: 0 8px 20px rgba(75, 46, 22, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--sand) 0%, #FFF4F5 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
        }

        /* HEADER */
        .header {
            background: var(--white);
            border-radius: var(--radius);
            padding: 25px 35px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header h1 {
            color: var(--brown);
            font-size: 1.8em;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header h1 .ai-badge {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.5em;
            font-weight: 600;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .btn-back {
            background: linear-gradient(135deg, var(--moss), var(--brown));
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: var(--radius);
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(94, 109, 56, 0.4);
        }

        /* KPI CARDS */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .kpi-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 25px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: all 0.3s;
            border-left: 4px solid var(--sage);
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(75, 46, 22, 0.15);
        }

        .kpi-card.urgent {
            border-left-color: #D32F2F;
        }

        .kpi-card.important {
            border-left-color: #FF9800;
        }

        .kpi-card.normal {
            border-left-color: #4CAF50;
        }

        .kpi-card.info {
            border-left-color: #2196F3;
        }

        .kpi-icon {
            font-size: 2.5em;
            margin-bottom: 15px;
        }

        .kpi-card.urgent .kpi-icon {
            color: #D32F2F;
        }

        .kpi-card.important .kpi-icon {
            color: #FF9800;
        }

        .kpi-card.normal .kpi-icon {
            color: #4CAF50;
        }

        .kpi-card.info .kpi-icon {
            color: #2196F3;
        }

        .kpi-value {
            font-size: 2.5em;
            font-weight: 800;
            color: var(--brown);
            line-height: 1;
        }

        .kpi-label {
            color: var(--moss);
            font-size: 0.95em;
            margin-top: 8px;
            font-weight: 600;
        }

        /* CHARTS SECTION */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            margin-bottom: 30px;
        }

        @media (max-width: 1200px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        .chart-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 25px;
            box-shadow: var(--shadow);
        }

        .chart-card h3 {
            color: var(--brown);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1em;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        /* AI ANALYSIS SECTION */
        .ai-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: var(--radius);
            padding: 30px;
            margin-bottom: 30px;
            color: white;
        }

        .ai-section h2 {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        .ai-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .ai-info-card {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 20px;
            backdrop-filter: blur(10px);
        }

        .ai-info-card h4 {
            margin-bottom: 10px;
            font-size: 1em;
            opacity: 0.9;
        }

        .ai-info-card .value {
            font-size: 2em;
            font-weight: 700;
        }

        /* TOP USERS */
        .users-section {
            background: var(--white);
            border-radius: var(--radius);
            padding: 25px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .users-section h3 {
            color: var(--brown);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
        }

        .users-table th,
        .users-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--sand);
        }

        .users-table th {
            background: var(--sand);
            color: var(--brown);
            font-weight: 600;
        }

        .users-table tr:hover {
            background: #FFF4F5;
        }

        .user-rank {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
        }

        .rank-1 {
            background: linear-gradient(135deg, #FFD700, #FFA000);
        }

        .rank-2 {
            background: linear-gradient(135deg, #C0C0C0, #9E9E9E);
        }

        .rank-3 {
            background: linear-gradient(135deg, #CD7F32, #8B4513);
        }

        .rank-other {
            background: var(--sage);
        }

        /* URGENT ALERTS */
        .urgent-section {
            background: var(--white);
            border-radius: var(--radius);
            padding: 25px;
            box-shadow: var(--shadow);
            border-left: 4px solid #D32F2F;
        }

        .urgent-section h3 {
            color: #D32F2F;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .urgent-item {
            background: #FFEBEE;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }

        .urgent-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(211, 47, 47, 0.2);
        }

        .urgent-item:last-child {
            margin-bottom: 0;
        }

        .urgent-info h4 {
            color: var(--brown);
            margin-bottom: 5px;
        }

        .urgent-info p {
            color: var(--moss);
            font-size: 0.9em;
        }

        .urgent-date {
            color: #D32F2F;
            font-weight: 600;
            font-size: 0.85em;
        }

        .btn-view-urgent {
            background: #D32F2F;
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.85em;
            transition: all 0.3s;
        }

        .btn-view-urgent:hover {
            background: #B71C1C;
            transform: translateY(-2px);
        }

        /* PRIORITY LEGEND */
        .priority-legend {
            display: flex;
            gap: 20px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9em;
            color: var(--brown);
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .legend-dot.urgent {
            background: #D32F2F;
        }

        .legend-dot.important {
            background: #FF9800;
        }

        .legend-dot.normal {
            background: #4CAF50;
        }

        /* EMPTY STATE */
        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--moss);
        }

        .empty-state i {
            font-size: 3em;
            opacity: 0.5;
            margin-bottom: 15px;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }

            .kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <h1>
                <i class="fas fa-brain"></i>
                Dashboard Intelligent
                <span class="ai-badge"><i class="fas fa-robot"></i> IA Active</span>
            </h1>
            <a href="index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour au Dashboard
            </a>
        </div>

        <!-- AI ANALYSIS SECTION -->
        <div class="ai-section">
            <h2>
                <i class="fas fa-magic"></i>
                Analyse Intelligente du Système
            </h2>
            <div class="ai-info-grid">
                <div class="ai-info-card">
                    <h4><i class="fas fa-clock"></i> Temps moyen de résolution</h4>
                    <div class="value"><?= $avgResolutionTime ?> jours</div>
                </div>
                <div class="ai-info-card">
                    <h4><i class="fas fa-check-circle"></i> Taux de résolution</h4>
                    <div class="value"><?= $resolutionRate ?>%</div>
                </div>
                <div class="ai-info-card">
                    <h4><i class="fas fa-fire"></i> Réclamations urgentes en attente</h4>
                    <div class="value"><?= count($urgentPending) ?></div>
                </div>
                <div class="ai-info-card">
                    <h4><i class="fas fa-chart-line"></i> Total analysé</h4>
                    <div class="value"><?= $basicStats['total'] ?? 0 ?></div>
                </div>
            </div>

            <div class="priority-legend">
                <div class="legend-item">
                    <span class="legend-dot urgent"></span>
                    <strong>🔴 Urgent</strong> - Mots: urgent, bloqué, panne, danger...
                </div>
                <div class="legend-item">
                    <span class="legend-dot important"></span>
                    <strong>🟠 Important</strong> - Mots: problème, aide, bug, erreur...
                </div>
                <div class="legend-item">
                    <span class="legend-dot normal"></span>
                    <strong>🟢 Normal</strong> - Autres cas
                </div>
            </div>
        </div>

        <!-- KPI CARDS -->
        <div class="kpi-grid">
            <div class="kpi-card urgent">
                <div class="kpi-icon"><i class="fas fa-exclamation-circle"></i></div>
                <div class="kpi-value"><?= $basicStats['urgentes'] ?? 0 ?></div>
                <div class="kpi-label">🔴 Urgentes</div>
            </div>
            <div class="kpi-card important">
                <div class="kpi-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="kpi-value"><?= $basicStats['importantes'] ?? 0 ?></div>
                <div class="kpi-label">🟠 Importantes</div>
            </div>
            <div class="kpi-card normal">
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-value"><?= $basicStats['normales'] ?? 0 ?></div>
                <div class="kpi-label">🟢 Normales</div>
            </div>
            <div class="kpi-card info">
                <div class="kpi-icon"><i class="fas fa-clock"></i></div>
                <div class="kpi-value"><?= $basicStats['en_attente'] ?? 0 ?></div>
                <div class="kpi-label">⏳ En attente</div>
            </div>
            <div class="kpi-card info">
                <div class="kpi-icon"><i class="fas fa-spinner"></i></div>
                <div class="kpi-value"><?= $basicStats['en_cours'] ?? 0 ?></div>
                <div class="kpi-label">🔄 En cours</div>
            </div>
            <div class="kpi-card normal">
                <div class="kpi-icon"><i class="fas fa-check-double"></i></div>
                <div class="kpi-value"><?= $basicStats['resolues'] ?? 0 ?></div>
                <div class="kpi-label">✅ Résolues</div>
            </div>
        </div>

        <!-- CHARTS -->
        <div class="charts-grid">
            <!-- Graphique par jour -->
            <div class="chart-card">
                <h3><i class="fas fa-chart-line"></i> Réclamations par jour (30 derniers jours)</h3>
                <div class="chart-container">
                    <canvas id="chartPerDay"></canvas>
                </div>
            </div>

            <!-- Graphique par priorité -->
            <div class="chart-card">
                <h3><i class="fas fa-chart-pie"></i> Répartition par priorité (IA)</h3>
                <div class="chart-container">
                    <canvas id="chartPriority"></canvas>
                </div>
            </div>

            <!-- Graphique par catégorie -->
            <div class="chart-card">
                <h3><i class="fas fa-chart-bar"></i> Réclamations par catégorie</h3>
                <div class="chart-container">
                    <canvas id="chartCategory"></canvas>
                </div>
            </div>

            <!-- Graphique par statut -->
            <div class="chart-card">
                <h3><i class="fas fa-tasks"></i> Répartition par statut</h3>
                <div class="chart-container">
                    <canvas id="chartStatus"></canvas>
                </div>
            </div>
        </div>

        <!-- TWO COLUMNS: TOP USERS + URGENT -->
        <div class="charts-grid">
            <!-- TOP USERS -->
            <div class="users-section">
                <h3><i class="fas fa-users"></i> Top utilisateurs (réclamations)</h3>
                <?php if (!empty($topUsers)): ?>
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>Rang</th>
                                <th>Utilisateur</th>
                                <th>Total</th>
                                <th>Urgentes</th>
                                <th>Résolues</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topUsers as $index => $user): ?>
                                <tr>
                                    <td>
                                        <div class="user-rank rank-<?= $index < 3 ? ($index + 1) : 'other' ?>">
                                            <?= $index + 1 ?>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($user['nom_complet'] ?? 'Utilisateur #' . $user['utilisateurId']) ?></strong>
                                        <?php if (!empty($user['email'])): ?>
                                            <br><small style="color: var(--moss);"><?= htmlspecialchars($user['email']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= $user['total_reclamations'] ?></strong></td>
                                    <td style="color: #D32F2F;"><?= $user['urgentes'] ?></td>
                                    <td style="color: #4CAF50;"><?= $user['resolues'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>Aucune donnée disponible</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- URGENT ALERTS -->
            <div class="urgent-section">
                <h3><i class="fas fa-fire-alt"></i> Réclamations urgentes en attente</h3>
                <?php if (!empty($urgentPending)): ?>
                    <?php foreach ($urgentPending as $urgent): ?>
                        <div class="urgent-item">
                            <div class="urgent-info">
                                <h4>#<?= $urgent['id'] ?> -
                                    <?= htmlspecialchars(substr($urgent['sujet'], 0, 50)) ?>
                                    <?= strlen($urgent['sujet']) > 50 ? '...' : '' ?>
                                </h4>
                                <p>
                                    <i class="fas fa-tag"></i> <?= htmlspecialchars($urgent['categorie']) ?>
                                    &nbsp;|&nbsp;
                                    <i class="fas fa-info-circle"></i> <?= htmlspecialchars($urgent['statut']) ?>
                                </p>
                                <span class="urgent-date">
                                    <i class="fas fa-calendar"></i>
                                    <?= date('d/m/Y H:i', strtotime($urgent['dateCreation'])) ?>
                                </span>
                            </div>
                            <a href="gestion_reclamation/showReclamation.php?id=<?= $urgent['id'] ?>" class="btn-view-urgent">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-check-circle" style="color: #4CAF50;"></i>
                        <p>Aucune réclamation urgente en attente ! 🎉</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- CHART.JS SCRIPTS -->
    <script>
        // Couleurs cohérentes avec la palette
        const colors = {
            brown: '#4b2e16',
            copper: '#b47b47',
            moss: '#5e6d3b',
            sage: '#a9b97d',
            sand: '#f4ecdd',
            urgent: '#D32F2F',
            important: '#FF9800',
            normal: '#4CAF50',
            info: '#2196F3'
        };

        // ========================================
        // GRAPHIQUE PAR JOUR
        // ========================================
        const perDayData = <?= json_encode($perDay) ?>;
        const perDayLabels = perDayData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
        });
        const perDayValues = perDayData.map(item => item.count);

        new Chart(document.getElementById('chartPerDay'), {
            type: 'line',
            data: {
                labels: perDayLabels,
                datasets: [{
                    label: 'Réclamations',
                    data: perDayValues,
                    borderColor: colors.moss,
                    backgroundColor: 'rgba(94, 109, 59, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: colors.moss,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
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

        // ========================================
        // GRAPHIQUE PAR PRIORITÉ (DONUT)
        // ========================================
        const priorityData = <?= json_encode($byPriority) ?>;
        const priorityLabels = priorityData.map(item => item.priorite || 'Non définie');
        const priorityValues = priorityData.map(item => item.count);
        const priorityColors = priorityLabels.map(label => {
            if (label.toLowerCase().includes('urgent')) return colors.urgent;
            if (label.toLowerCase().includes('moyenne') || label.toLowerCase().includes('important')) return colors.important;
            return colors.normal;
        });

        new Chart(document.getElementById('chartPriority'), {
            type: 'doughnut',
            data: {
                labels: priorityLabels,
                datasets: [{
                    data: priorityValues,
                    backgroundColor: priorityColors,
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
                        labels: { padding: 20 }
                    }
                }
            }
        });

        // ========================================
        // GRAPHIQUE PAR CATÉGORIE (BAR)
        // ========================================
        const categoryData = <?= json_encode($byCategory) ?>;
        const categoryLabels = categoryData.map(item => item.categorie || 'Autre');
        const categoryValues = categoryData.map(item => item.count);

        new Chart(document.getElementById('chartCategory'), {
            type: 'bar',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Réclamations',
                    data: categoryValues,
                    backgroundColor: [
                        colors.moss,
                        colors.copper,
                        colors.sage,
                        colors.brown,
                        colors.info
                    ],
                    borderRadius: 8
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

        // ========================================
        // GRAPHIQUE PAR STATUT (POLAR AREA)
        // ========================================
        const statusData = <?= json_encode($byStatus) ?>;
        const statusLabels = statusData.map(item => item.statut || 'Non défini');
        const statusValues = statusData.map(item => item.count);
        const statusColors = [
            '#FFC107', // En attente
            '#2196F3', // En cours
            '#4CAF50', // Résolue
            '#9E9E9E', // Fermée
            '#D32F2F'  // Autre
        ];

        new Chart(document.getElementById('chartStatus'), {
            type: 'polarArea',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusValues,
                    backgroundColor: statusColors.map(c => c + 'CC'),
                    borderColor: statusColors,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 15 }
                    }
                }
            }
        });
    </script>
</body>

</html>