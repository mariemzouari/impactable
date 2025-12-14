<?php
session_start();
include_once __DIR__ . '/../../Model/FrontCampagneController.php';
include_once __DIR__ . '/../../Model/StatsController.php';

$frontController = new FrontCampagneController();
$statsC = new StatsController();

$campagnesProblemes = $frontController->getCampagnesAvecProblemes();
$countProblemes = count($campagnesProblemes);
$globalStats = $statsC->getGlobalStats();
$campaignStats = $statsC->getCampaignStats();
$paymentStats = $statsC->getPaymentMethodStats();
$topDonors = $statsC->getTopDonors(5);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactAble — Statistiques</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .campaigns-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        /* Cartes de campagnes avec couleurs selon progression */
        .campaign-stat {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .campaign-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        /* Couleurs selon la progression (bordure gauche) */
        .campaign-stat.completed {
            border-left: 6px solid #4CAF50;
            background: linear-gradient(135deg, #E8F5E9 0%, #FFFFFF 100%);
        }

        .campaign-stat.high {
            border-left: 6px solid #8BC34A;
            background: linear-gradient(135deg, #F1F8E9 0%, #FFFFFF 100%);
        }

        .campaign-stat.medium {
            border-left: 6px solid #FFC107;
            background: linear-gradient(135deg, #FFFDE7 0%, #FFFFFF 100%);
        }

        .campaign-stat.low {
            border-left: 6px solid #FF9800;
            background: linear-gradient(135deg, #FFF3E0 0%, #FFFFFF 100%);
        }

        .campaign-stat.critical {
            border-left: 6px solid #F44336;
            background: linear-gradient(135deg, #FFEBEE 0%, #FFFFFF 100%);
        }

        /* BADGES DE CATÉGORIE - CORRIGÉS */
        .category-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75em;
            font-weight: bold;
            color: white;
            margin-right: 8px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* COULEURS EXACTES selon vos catégories */
        .education-badge {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            box-shadow: 0 2px 5px rgba(33, 150, 243, 0.3);
        }

        .logement-badge {
            background: linear-gradient(135deg, #9C27B0, #7B1FA2);
            box-shadow: 0 2px 5px rgba(156, 39, 176, 0.3);
        }

        .sante-badge {
            background: linear-gradient(135deg, #4CAF50, #388E3C);
            box-shadow: 0 2px 5px rgba(76, 175, 80, 0.3);
        }

        .alimentation-badge {
            background: linear-gradient(135deg, #FF9800, #F57C00);
            box-shadow: 0 2px 5px rgba(255, 152, 0, 0.3);
        }

        .droits-humains-badge {
            background: linear-gradient(135deg, #F44336, #D32F2F);
            box-shadow: 0 2px 5px rgba(244, 67, 54, 0.3);
        }

        .autre-badge {
            background: linear-gradient(135deg, #607D8B, #455A64);
            box-shadow: 0 2px 5px rgba(96, 125, 139, 0.3);
        }

        /* Badges de progression */
        .badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
            margin-left: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .badge.success {
            background: linear-gradient(135deg, #4CAF50, #388E3C);
            color: white;
        }

        .badge.warning {
            background: linear-gradient(135deg, #FFC107, #FFA000);
            color: white;
        }

        .badge.danger {
            background: linear-gradient(135deg, #F44336, #D32F2F);
            color: white;
        }

        .badge.info {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
        }

        .badge.primary {
            background: linear-gradient(135deg, var(--sage), #5A7D59);
            color: white;
        }

        .alert-count {
            background: #FF9800;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8em;
            margin-left: 8px;
        }

        /* Progress bar colorée */
        .progress {
            height: 10px;
            background: #eee;
            border-radius: 5px;
            overflow: hidden;
            margin: 10px 0;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            height: 100%;
            border-radius: 5px;
            transition: width 1s ease-in-out;
        }

        .progress-100 {
            background: linear-gradient(90deg, #4CAF50, #8BC34A);
        }

        .progress-75 {
            background: linear-gradient(90deg, #8BC34A, #CDDC39);
        }

        .progress-50 {
            background: linear-gradient(90deg, #FFC107, #FF9800);
        }

        .progress-25 {
            background: linear-gradient(90deg, #FF9800, #FF5722);
        }

        .progress-0 {
            background: linear-gradient(90deg, #F44336, #D32F2F);
        }

        /* Cartes de statistiques avec dégradés */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card:nth-child(1) {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .stat-card:nth-child(2) {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .stat-card:nth-child(3) {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .stat-card:nth-child(4) {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }

        /* Tableaux avec couleurs */
        .admin-table tbody tr:nth-child(odd) {
            background: #f8f9fa;
        }

        .admin-table tbody tr:hover {
            background: #e9ecef;
        }

        .admin-table tbody tr.success-row {
            border-left: 4px solid #4CAF50;
        }

        .admin-table tbody tr.warning-row {
            border-left: 4px solid #FFC107;
        }

        .admin-table tbody tr.danger-row {
            border-left: 4px solid #F44336;
        }

        /* Filtres */
        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 8px 20px;
            background: #f8f9fa;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9em;
            transition: all 0.3s;
        }

        .filter-tab:hover {
            background: #e9ecef;
        }

        .filter-tab.active {
            background: var(--sage);
            color: white;
            box-shadow: 0 3px 10px rgba(146, 168, 144, 0.3);
        }

        /* Avatar pour donateurs */
        .donor-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            margin-right: 10px;
        }

        .avatar-1 {
            background: linear-gradient(135deg, #FFD700, #FFC107);
        }

        .avatar-2 {
            background: linear-gradient(135deg, #C0C0C0, #9E9E9E);
        }

        .avatar-3 {
            background: linear-gradient(135deg, #CD7F32, #795548);
        }

        .avatar-other {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        /* Icônes de méthode de paiement */
        .payment-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 1.2em;
        }

        .payment-carte {
            background: #E3F2FD;
            color: #2196F3;
        }

        .payment-paypal {
            background: #E8F5E9;
            color: #4CAF50;
        }

        .payment-especes {
            background: #FFF3E0;
            color: #FF9800;
        }

        .payment-virement {
            background: #F3E5F5;
            color: #9C27B0;
        }

        .payment-mobile {
            background: #E0F7FA;
            color: #00BCD4;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .campaigns-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <!-- Sidebar identique -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="admin-logo">
                    <img src="assets/images/logo.png" alt="ImpactAble" class="admin-logo-image">
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-title">Principal</div>
                    <a href="index.php" class="sidebar-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tableau de bord</span>
                    </a>
                    <a href="#analytics" class="sidebar-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Analytiques</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-title">Gestion de contenu</div>
                    <a href="Ges_utilisateurs.php" class="sidebar-link">
                        <i class="fas fa-users"></i>
                        <span>Utilisateurs</span>
                    </a>
                    <a href="index.php?action=admin-dashboard" class="sidebar-link">
                        <i class="fas fa-briefcase"></i>
                        <span>Opportunités</span>
                    </a>
                    <a href="evenment_back.php" class="sidebar-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Événements</span>
                    </a>

                    <div class="sidebar-dropdown">
                        <a href="#" class="sidebar-link dropdown-toggle" aria-expanded="true">
                            <i class="fas fa-hand-holding-heart"></i>
                            <span>Campagnes</span>
                            <?php if (isset($countProblemes) && $countProblemes > 0): ?>
                                <span class="alert-count"><?php echo $countProblemes; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <div class="sidebar-submenu show">
                            <a href="list-camp.php" class="submenu-link">
                                <i class="fas fa-list"></i>
                                <span>Toutes les campagnes</span>
                            </a>
                            <a href="addCampagne.php" class="submenu-link">
                                <i class="fas fa-plus"></i>
                                <span>Nouvelle campagne</span>
                            </a>
                            <a href="Calendar.php" class="submenu-link">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Calendrier</span>
                            </a>
                            <a href="stats_dashboard.php" class="submenu-link active">
                                <i class="fas fa-chart-bar"></i>
                                <span>Statistiques</span>
                            </a>
                            <a href="referral.php" class="submenu-link">
                                <i class="fas fa-user-friends"></i>
                                <span>Parrainage</span>
                            </a>
                        </div>
                    </div>

                    <a href="list-don.php" class="sidebar-link">
                        <i class="fas fa-donate"></i>
                        <span>Dons</span>
                    </a>
                    <a href="#resources" class="sidebar-link">
                        <i class="fas fa-book"></i>
                        <span>Ressources</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-title">Communauté</div>
                    <a href="#forum" class="sidebar-link">
                        <i class="fas fa-comments"></i>
                        <span>Forum</span>
                    </a>
                    <a href="#reclamations" class="sidebar-link">
                        <i class="fas fa-comment-alt"></i>
                        <span>Réclamations</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-title">Paramètres</div>
                    <a href="#settings" class="sidebar-link">
                        <i class="fas fa-cog"></i>
                        <span>Configuration</span>
                    </a>
                </div>
            </nav>

            <div class="sidebar-footer">
                <div class="admin-user">
                    <div class="admin-avatar">AD</div>
                    <div class="admin-user-info">
                        <h4>Admin User</h4>
                        <p>Administrateur</p>
                    </div>
                </div>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <div>
                    <h2><i class="fas fa-chart-bar"></i> Tableau de Bord Statistiques</h2>
                    <p class="text-muted">Analyse détaillée des performances et impacts</p>
                </div>
                <div class="header-actions">
                    <div class="filter-tabs">
                        <button class="filter-tab active" onclick="filterByCategory('all')">Toutes</button>
                        <button class="filter-tab" onclick="filterByCategory('education')">Éducation</button>
                        <button class="filter-tab" onclick="filterByCategory('sante')">Santé</button>
                        <button class="filter-tab" onclick="filterByCategory('alimentation')">Alimentation</button>
                        <button class="filter-tab" onclick="filterByCategory('logement')">Logement</button>
                    </div>
                    <select id="periodSelect" class="select" style="width: auto; margin-left: 10px;">
                        <option value="7">7 derniers jours</option>
                        <option value="30" selected>30 derniers jours</option>
                        <option value="90">3 derniers mois</option>
                        <option value="365">1 an</option>
                    </select>
                    <a href="index.php" class="btn secondary" style="margin-left: 10px;">
                        <i class="fas fa-arrow-left"></i>
                        Retour
                    </a>
                </div>
            </header>

            <div class="admin-content">
                <!-- Cartes de statistiques -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-donate"></i>
                        </div>
                        <div class="stat-number">
                            <?= number_format($globalStats['dons']['montant_total'] ?? 0, 0) ?> TND
                        </div>
                        <div class="stat-label">Total Collecté</div>
                        <small><?= $globalStats['dons']['total_dons'] ?? 0 ?> dons confirmés</small>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <div class="stat-number">
                            <?= $globalStats['campagnes']['actives'] ?? 0 ?>
                        </div>
                        <div class="stat-label">Campagnes Actives</div>
                        <small>Sur <?= $globalStats['campagnes']['total_campagnes'] ?? 0 ?> total</small>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="stat-number">
                            <?php
                            $donsToday = array_sum(array_column($globalStats['dons_par_heure'], 'nombre_dons'));
                            echo $donsToday;
                            ?>
                        </div>
                        <div class="stat-label">Dons Aujourd'hui</div>
                        <small>Dons effectués aujourd'hui</small>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <div class="stat-number">
                            <?php
                            $completionRate = count($campaignStats) > 0 ?
                                array_sum(array_column($campaignStats, 'progression')) / count($campaignStats) : 0;
                            echo number_format($completionRate, 1);
                            ?>%
                        </div>
                        <div class="stat-label">Taux de Réussite</div>
                        <small>Moyenne des campagnes</small>
                    </div>
                </div>

                <!-- Graphique évolution -->
                <div class="content-card">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-line"></i> Évolution des Dons (30 jours)</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="evolutionChart"></canvas>
                    </div>
                </div>

                <!-- Performance des campagnes - SECTION AVEC BADGES CORRIGÉS -->
                <div class="content-card">
                    <div class="card-header">
                        <h3><i class="fas fa-trophy"></i> Performance des Campagnes</h3>
                        <div class="card-actions">
                            <button class="btn small" onclick="toggleView('grid')"><i class="fas fa-th"></i>
                                Grille</button>
                            <button class="btn small" onclick="toggleView('list')"><i class="fas fa-list"></i>
                                Liste</button>
                        </div>
                    </div>
                    <div class="card-body" id="campaignsView">
                        <div class="campaigns-grid" id="campaignsGrid">
                            <?php foreach (array_slice($campaignStats, 0, 6) as $camp): ?>
                                <?php
                                // 1. Déterminer la classe selon la progression
                                $progression = $camp['progression'] ?? 0;
                                $progressionClass = '';

                                if ($progression >= 100) {
                                    $progressionClass = 'completed';
                                } elseif ($progression >= 75) {
                                    $progressionClass = 'high';
                                } elseif ($progression >= 50) {
                                    $progressionClass = 'medium';
                                } elseif ($progression >= 25) {
                                    $progressionClass = 'low';
                                } else {
                                    $progressionClass = 'critical';
                                }

                                // 2. Déterminer le badge selon la catégorie EXACTE
                                $categorie = $camp['categorie_impact'] ?? 'autre';
                                $categorieBadge = 'autre-badge'; // Par défaut
                            
                                switch ($categorie) {
                                    case 'education':
                                        $categorieBadge = 'education-badge';
                                        $categorieIcon = '📚';
                                        break;
                                    case 'logement':
                                        $categorieBadge = 'logement-badge';
                                        $categorieIcon = '🏠';
                                        break;
                                    case 'sante':
                                        $categorieBadge = 'sante-badge';
                                        $categorieIcon = '🏥';
                                        break;
                                    case 'alimentation':
                                        $categorieBadge = 'alimentation-badge';
                                        $categorieIcon = '🍎';
                                        break;
                                    case 'droits_humains':
                                        $categorieBadge = 'droits-humains-badge';
                                        $categorieIcon = '⚖️';
                                        break;
                                    case 'autre':
                                    default:
                                        $categorieBadge = 'autre-badge';
                                        $categorieIcon = '🌍';
                                }

                                // 3. Badge de progression
                                $progressBadge = '';
                                if ($progression >= 100) {
                                    $progressBadge = 'success';
                                } elseif ($progression >= 50) {
                                    $progressBadge = 'warning';
                                } else {
                                    $progressBadge = 'danger';
                                }
                                ?>
                                <div class="campaign-stat <?= $progressionClass ?>" data-category="<?= $categorie ?>">
                                    <div style="display: flex; align-items: flex-start; margin-bottom: 10px;">
                                        <div style="font-size: 1.5em; margin-right: 10px;"><?= $categorieIcon ?></div>
                                        <div style="flex: 1;">
                                            <h4 style="margin: 0 0 5px 0;"><?= htmlspecialchars($camp['titre']) ?></h4>
                                            <span class="category-badge <?= $categorieBadge ?>">
                                                <?= ucfirst($categorie) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div style="display: flex; justify-content: space-between; margin: 15px 0;">
                                        <div>
                                            <div style="font-size: 0.9em; color: #666;">Collecté</div>
                                            <div style="font-size: 1.3em; font-weight: bold; color: var(--sage);">
                                                <?= number_format($camp['montant_actuel'], 0) ?> TND
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <div style="font-size: 0.9em; color: #666;">Objectif</div>
                                            <div style="font-size: 1.1em; font-weight: bold;">
                                                <?= number_format($camp['objectif_montant'], 0) ?> TND
                                            </div>
                                        </div>
                                    </div>

                                    <div class="progress" style="margin: 15px 0;">
                                        <div class="progress-bar 
                                        <?= $progression >= 100 ? 'progress-100' :
                                            ($progression >= 75 ? 'progress-75' :
                                                ($progression >= 50 ? 'progress-50' :
                                                    ($progression >= 25 ? 'progress-25' : 'progress-0'))) ?>"
                                            style="width: <?= min($progression, 100) ?>%;"
                                            data-progression="<?= $progression ?>%">
                                        </div>
                                    </div>

                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span class="badge <?= $progressBadge ?>">
                                            <?= number_format($progression, 1) ?>%
                                        </span>
                                        <div style="font-size: 0.85em; color: #666;">
                                            <i class="fas fa-users"></i> <?= $camp['nombre_dons'] ?? 0 ?> donateurs
                                        </div>
                                    </div>

                                    <!-- Info supplémentaire -->
                                    <div
                                        style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #eee; font-size: 0.8em; color: #666;">
                                        <div style="display: flex; justify-content: space-between;">
                                            <span>Montant moyen:
                                                <?=
                                                    isset($camp['nombre_dons']) && $camp['nombre_dons'] > 0
                                                    ? number_format($camp['montant_actuel'] / $camp['nombre_dons'], 0)
                                                    : '0'
                                                    ?> TND
                                            </span>
                                            <span>Dons: <?= $camp['nombre_dons'] ?? 0 ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (count($campaignStats) > 6): ?>
                            <div style="text-align: center; margin-top: 20px;">
                                <button class="btn secondary" onclick="loadMoreCampaigns()">
                                    <i class="fas fa-plus"></i> Voir plus de campagnes
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Méthodes de paiement -->
                <div class="content-card">
                    <div class="card-header">
                        <h3><i class="fas fa-credit-card"></i> Méthodes de Paiement</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Méthode</th>
                                        <th>Nombre de Dons</th>
                                        <th>Montant Total</th>
                                        <th>Moyenne</th>
                                        <th>Part</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $totalMontant = array_sum(array_column($paymentStats, 'montant_total'));
                                    foreach ($paymentStats as $index => $payment):
                                        $pourcentage = $totalMontant > 0 ? ($payment['montant_total'] / $totalMontant) * 100 : 0;

                                        // Icône selon méthode
                                        $paymentIcons = [
                                            'carte' => ['icon' => 'fas fa-credit-card', 'class' => 'payment-carte'],
                                            'paypal' => ['icon' => 'fab fa-paypal', 'class' => 'payment-paypal'],
                                            'especes' => ['icon' => 'fas fa-money-bill-wave', 'class' => 'payment-especes'],
                                            'virement' => ['icon' => 'fas fa-university', 'class' => 'payment-virement'],
                                            'mobile' => ['icon' => 'fas fa-mobile-alt', 'class' => 'payment-mobile']
                                        ];

                                        $method = strtolower($payment['methode_paiment']);
                                        $iconInfo = $paymentIcons[$method] ?? ['icon' => 'fas fa-money-check-alt', 'class' => 'payment-carte'];
                                        ?>
                                        <tr
                                            class="<?= $pourcentage > 50 ? 'success-row' : ($pourcentage > 25 ? 'warning-row' : '') ?>">
                                            <td>
                                                <div style="display: flex; align-items: center;">
                                                    <div class="payment-icon <?= $iconInfo['class'] ?>">
                                                        <i class="<?= $iconInfo['icon'] ?>"></i>
                                                    </div>
                                                    <div>
                                                        <strong><?= ucfirst($payment['methode_paiment']) ?></strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge primary"><?= $payment['nombre_dons'] ?></span>
                                            </td>
                                            <td>
                                                <strong><?= number_format($payment['montant_total'], 0) ?> TND</strong>
                                            </td>
                                            <td><?= number_format($payment['moyenne'], 0) ?> TND</td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <div
                                                        style="flex: 1; background: #eee; height: 8px; border-radius: 4px; overflow: hidden;">
                                                        <div style="width: <?= $pourcentage ?>%; height: 100%; 
                                                         background: var(--sage); border-radius: 4px;"></div>
                                                    </div>
                                                    <span style="min-width: 50px; text-align: right;">
                                                        <?= number_format($pourcentage, 1) ?>%
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Top donateurs -->
                <div class="content-card">
                    <div class="card-header">
                        <h3><i class="fas fa-crown"></i> Top Donateurs</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Donateur</th>
                                        <th>Nombre de Dons</th>
                                        <th>Montant Total</th>
                                        <th>Dernier Don</th>
                                        <th>Niveau</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topDonors as $index => $donor): ?>
                                        <?php
                                        // Déterminer le niveau
                                        $montant = $donor['montant_total'] ?? 0;
                                        $niveau = '';
                                        $niveauClass = '';

                                        if ($montant >= 10000) {
                                            $niveau = 'Diamant';
                                            $niveauClass = 'badge success';
                                        } elseif ($montant >= 5000) {
                                            $niveau = 'Or';
                                            $niveauClass = 'badge warning';
                                        } elseif ($montant >= 2000) {
                                            $niveau = 'Argent';
                                            $niveauClass = 'badge info';
                                        } else {
                                            $niveau = 'Bronze';
                                            $niveauClass = 'badge primary';
                                        }

                                        // Avatar coloré
                                        $avatarClass = 'avatar-other';
                                        if ($index === 0)
                                            $avatarClass = 'avatar-1';
                                        elseif ($index === 1)
                                            $avatarClass = 'avatar-2';
                                        elseif ($index === 2)
                                            $avatarClass = 'avatar-3';
                                        ?>
                                        <tr>
                                            <td>
                                                <div style="font-size: 1.2em; font-weight: bold; color: var(--sage);">
                                                    #<?= $index + 1 ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="display: flex; align-items: center;">
                                                    <div class="donor-avatar <?= $avatarClass ?>">
                                                        <?= substr($donor['nom_donateur'], 0, 2) ?>
                                                    </div>
                                                    <div>
                                                        <strong><?= htmlspecialchars($donor['nom_donateur']) ?></strong><br>
                                                        <small
                                                            style="color: #666;"><?= $donor['email_donateur'] ?? '' ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="text-align: center;">
                                                    <span class="badge primary"><?= $donor['nombre_dons'] ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-size: 1.1em; font-weight: bold; color: var(--sage);">
                                                    <?= number_format($montant, 0) ?> TND
                                                </div>
                                            </td>
                                            <td>
                                                <?= date('d/m/Y', strtotime($donor['dernier_don'])) ?>
                                            </td>
                                            <td>
                                                <span class="<?= $niveauClass ?>"><?= $niveau ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Graphique d'évolution
        const ctx = document.getElementById('evolutionChart').getContext('2d');
        const evolutionChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['1 Mar', '5 Mar', '10 Mar', '15 Mar', '20 Mar', '25 Mar', '30 Mar'],
                datasets: [{
                    label: 'Montant des Dons (TND)',
                    data: [1200, 1900, 1500, 2500, 2200, 3000, 2800],
                    borderColor: 'var(--sage)',
                    backgroundColor: 'rgba(146, 168, 144, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            callback: function (value) {
                                return value + ' TND';
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    }
                }
            }
        });

        // Filtrage par catégorie
        function filterByCategory(category) {
            // Mettre à jour les boutons actifs
            document.querySelectorAll('.filter-tab').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            const campaignCards = document.querySelectorAll('.campaign-stat');

            if (category === 'all') {
                campaignCards.forEach(card => {
                    card.style.display = 'block';
                });
                showNotification('Toutes les campagnes affichées');
            } else {
                let count = 0;
                campaignCards.forEach(card => {
                    const cardCategory = card.getAttribute('data-category');
                    if (cardCategory === category) {
                        card.style.display = 'block';
                        count++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                showNotification(`${count} campagnes de catégorie "${category}"`);
            }
        }

        // Basculer entre vue grille et liste
        function toggleView(viewType) {
            const gridView = document.getElementById('campaignsGrid');
            const campaignCards = document.querySelectorAll('.campaign-stat');

            if (viewType === 'list') {
                gridView.style.gridTemplateColumns = '1fr';
                gridView.style.gap = '15px';
                campaignCards.forEach(card => {
                    card.style.display = 'flex';
                    card.style.alignItems = 'center';
                    card.style.gap = '20px';
                    card.style.padding = '15px';
                });
                showNotification('Vue liste activée');
            } else {
                gridView.style.gridTemplateColumns = 'repeat(auto-fill, minmax(300px, 1fr))';
                gridView.style.gap = '20px';
                campaignCards.forEach(card => {
                    card.style.display = 'block';
                });
                showNotification('Vue grille activée');
            }
        }

        // Charger plus de campagnes
        function loadMoreCampaigns() {
            const btn = event.target;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement...';
            btn.disabled = true;

            // Simuler un chargement (en production, faire une requête AJAX)
            setTimeout(() => {
                showNotification('Fonctionnalité à implémenter');
                btn.innerHTML = '<i class="fas fa-plus"></i> Voir plus de campagnes';
                btn.disabled = false;
            }, 1500);
        }

        // Changement de période
        document.getElementById('periodSelect').addEventListener('change', function () {
            const period = this.value;
            showNotification(`Période changée: ${period} jours`);
            // Ici vous pouvez ajouter une requête AJAX pour mettre à jour les données
        });

        // Animation des barres de progression
        document.addEventListener('DOMContentLoaded', function () {
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 300);
            });
        });

        // Fonction de notification
        function showNotification(message) {
            const notification = document.createElement('div');
            notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--sage);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
        `;

            notification.innerHTML = `<i class="fas fa-info-circle" style="margin-right: 8px;"></i>${message}`;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Ajouter les styles d'animation
        const style = document.createElement('style');
        style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
        document.head.appendChild(style);
    </script>
</body>

</html>