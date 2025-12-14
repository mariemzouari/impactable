<?php
// list-don.php - Version sans modèle
include_once __DIR__ . '/../../controller/DonController.php';
include_once __DIR__ . '/../../controller/CampagneController.php';

$donController = new DonController();
$campagneController = new CampagneController();

// Récupérer tous les dons avec jointures
$dons = $donController->getHistoriqueDonsComplet();

// Calculer les statistiques
$totalDons = 0;
$nombreDons = count($dons);
$donsParMethode = [];
$donsParCampagne = [];

foreach ($dons as $don) {
    $totalDons += $don['montant'];

    // Dons par méthode de paiement
    $methode = $don['methode_paiment'];
    if (!isset($donsParMethode[$methode])) {
        $donsParMethode[$methode] = 0;
    }
    $donsParMethode[$methode] += $don['montant'];

    // Dons par campagne
    $campagneId = $don['id_campagne'];
    if (!isset($donsParCampagne[$campagneId])) {
        $donsParCampagne[$campagneId] = [
            'titre' => $don['campagne_titre'],
            'total' => 0,
            'count' => 0
        ];
    }
    $donsParCampagne[$campagneId]['total'] += $don['montant'];
    $donsParCampagne[$campagneId]['count']++;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactAble — Gestion des Dons</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Copier les styles depuis la version précédente */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: var(--sage);
            margin: 10px 0;
        }

        .badge.confirmé {
            background: #d4edda;
            color: #155724;
        }

        .badge.en_attente {
            background: #fff3cd;
            color: #856404;
        }

        .badge.annulé {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <!-- Sidebar simplifiée -->
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
                        <a href="#" class="sidebar-link dropdown-toggle" aria-expanded="false">
                            <i class="fas fa-hand-holding-heart"></i>
                            <span>Campagnes</span>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <div class="sidebar-submenu">
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
                            <a href="stats_dashboard.php" class="submenu-link">
                                <i class="fas fa-chart-bar"></i>
                                <span>Statistiques</span>
                            </a>
                            <a href="referral.php" class="submenu-link">
                                <i class="fas fa-user-friends"></i>
                                <span>Parrainage</span>
                            </a>
                        </div>
                    </div>

                    <a href="list-don.php" class="sidebar-link active">
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
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <div>
                    <h2>Gestion des Dons</h2>
                    <p class="text-muted">Suivi de tous les dons effectués</p>
                </div>
                <div class="header-actions">
                    <a href="create-don.php" class="btn primary">
                        <i class="fas fa-plus"></i>
                        Nouveau don
                    </a>
                </div>
            </header>

            <div class="admin-content">
                <!-- Statistiques -->
                <section class="content-card">
                    <div class="card-body">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-number"><?php echo $nombreDons; ?></div>
                                <div class="stat-label">Total Dons</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number"><?php echo number_format($totalDons, 2, ',', ' '); ?> TND</div>
                                <div class="stat-label">Montant Total</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number"><?php echo count($donsParCampagne); ?></div>
                                <div class="stat-label">Campagnes financées</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">
                                    <?php echo number_format($nombreDons > 0 ? $totalDons / $nombreDons : 0, 2, ',', ' '); ?>
                                    TND
                                </div>
                                <div class="stat-label">Don moyen</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Tableau des dons -->
                <section class="content-card">
                    <div class="card-header">
                        <h3>Historique des Dons</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Donateur</th>
                                        <th>Campagne</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($dons)): ?>
                                        <?php foreach ($dons as $don): ?>
                                            <tr>
                                                <td>#<?php echo $don['Id_don']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($don['donateur_nom']); ?></strong><br>
                                                    <small><?php echo htmlspecialchars($don['donateur_email']); ?></small>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($don['campagne_titre']); ?></strong>
                                                </td>
                                                <td>
                                                    <strong><?php echo number_format($don['montant'], 2, ',', ' '); ?>
                                                        TND</strong>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $don['statut']; ?>">
                                                        <?php echo ucfirst($don['statut']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo date('d/m/Y', strtotime($don['date_don'])); ?>
                                                </td>
                                                <td>
                                                    <div class="table-actions">
                                                        <a href="show-don.php?id=<?php echo $don['Id_don']; ?>"
                                                            class="btn small">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="edit-don.php?id=<?php echo $don['Id_don']; ?>"
                                                            class="btn small secondary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="delete-don.php?id=<?php echo $don['Id_don']; ?>"
                                                            class="btn small danger"
                                                            onclick="return confirm('Supprimer ce don ?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <p>Aucun don trouvé</p>
                                                <a href="create-don.php" class="btn primary">Créer un don</a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>

</html>