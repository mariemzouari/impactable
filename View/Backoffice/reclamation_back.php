<?php
session_start();
require_once(__DIR__ . '/../../controller/ReclamationController.php');
require_once(__DIR__ . '/../../config.php');

$controller = new ReclamationController();
$reclamations = $controller->listReclamations();
$stats = $controller->getStats();

// Gestion de la suppression
if (isset($_GET['delete'])) {
    $controller->deleteReclamation($_GET['delete']);
    header('Location: reclamation_back.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Gestion des Réclamations</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/backoffice-style.css">
</head>

<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="admin-logo">
                    <img src="assets/logo-white.png" alt="ImpactAble" class="admin-logo-image">
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-title">Principal</div>
                    <a href="index.php" class="sidebar-link active">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tableau de bord</span>
                    </a>
                    <a href="statistiques_pro.php" class="sidebar-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Analytiques</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-title">Gestion</div>
                    <a href="#reclamations-section" class="sidebar-link">
                        <i class="fas fa-file-alt"></i>
                        <span>Réclamations</span>
                    </a>
                    <a href="reponsecrud/toutes_reponses.php" class="sidebar-link">
                        <i class="fas fa-comments"></i>
                        <span>Réponses</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-title">Liens</div>
                    <a href="../frontoffice/index.php" target="_blank" class="sidebar-link">
                        <i class="fas fa-external-link-alt"></i>
                        <span>Front Office</span>
                    </a>
                </div>
            </nav>

            <div class="sidebar-footer">
                <div class="admin-user">
                    <div class="admin-avatar">A</div>
                    <div class="admin-user-info">
                        <h4>Admin</h4>
                        <p>Administrateur</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="admin-main">
            <?php if (isset($_GET['error']) && $_GET['error'] === 'no_id'): ?>
                <div class="notification notification-warning"
                    style="background: #FFF3CD; border: 1px solid #B47B47; color: #856404; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-info-circle"></i>
                    <span>Veuillez sélectionner une réclamation dans le tableau ci-dessous pour voir ses détails.</span>
                    <button onclick="this.parentElement.style.display='none'"
                        style="margin-left: auto; background: none; border: none; cursor: pointer; font-size: 1.2em;">&times;</button>
                </div>
            <?php endif; ?>

            <header class="admin-header">
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchReclamations" placeholder="Rechercher...">
                </div>
                <div class="header-actions">
                    <button class="btn ghost">
                        <i class="fas fa-bell"></i>
                    </button>
                    <button class="btn ghost">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>
            </header>

            <div class="admin-content">
                <div class="content-header">
                    <h1>Tableau de bord</h1>
                    <a href="gestion_reclamation/addReclamation.php" class="btn primary">
                        <i class="fas fa-plus"></i> Nouvelle Réclamation
                    </a>
                </div>

                <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                    <div
                        style="background: rgba(46, 204, 113, 0.1); color: #27ae60; padding: 15px 20px; border-radius: 10px; margin-bottom: 24px; font-weight: 600;">
                        <i class="fas fa-check-circle"></i> Réclamation mise à jour avec succès !
                    </div>
                <?php endif; ?>

                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-number"><?= $stats['en_attente'] ?? 0 ?></div>
                        <div class="stat-label">En attente</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-spinner"></i>
                        </div>
                        <div class="stat-number"><?= $stats['en_cours'] ?? 0 ?></div>
                        <div class="stat-label">En cours</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number"><?= $stats['resolues'] ?? 0 ?></div>
                        <div class="stat-label">Résolues</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(231, 76, 60, 0.1); color: #e74c3c;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-number" style="color: #e74c3c;"><?= $stats['urgentes'] ?? 0 ?></div>
                        <div class="stat-label">Urgentes</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(75, 46, 22, 0.1); color: #4b2e16;">
                            <i class="fas fa-list"></i>
                        </div>
                        <div class="stat-number" style="color: #4b2e16;"><?= $stats['total'] ?? 0 ?></div>
                        <div class="stat-label">Total</div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="content-card">
                    <div class="card-header">
                        <h3><i class="fas fa-filter"></i> Filtres</h3>
                    </div>
                    <div class="card-body" style="display: flex; gap: 16px; flex-wrap: wrap;">
                        <select id="filterStatus" class="select" style="width: auto; min-width: 180px;">
                            <option value="">Tous les statuts</option>
                            <option value="En attente">En attente</option>
                            <option value="En cours">En cours</option>
                            <option value="Résolue">Résolue</option>
                            <option value="Fermée">Fermée</option>
                        </select>
                        <select id="filterPriorite" class="select" style="width: auto; min-width: 180px;">
                            <option value="">Toutes priorités</option>
                            <option value="Urgente">Urgente</option>
                            <option value="Moyenne">Moyenne</option>
                            <option value="Faible">Faible</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="content-card" id="reclamations-section">
                    <div class="card-header">
                        <h3><i class="fas fa-list"></i> Liste des Réclamations</h3>
                        <span style="color: var(--muted);"><?= count($reclamations) ?> réclamation(s)</span>
                    </div>
                    <div class="card-body" style="padding: 0; overflow-x: auto;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Utilisateur</th>
                                    <th>Sujet</th>
                                    <th>Catégorie</th>
                                    <th>Priorité</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="reclamationsTableBody">
                                <?php if (empty($reclamations)): ?>
                                    <tr>
                                        <td colspan="8"
                                            style="text-align: center; padding: 60px 20px; color: var(--muted);">
                                            <i class="fas fa-inbox"
                                                style="font-size: 3em; margin-bottom: 16px; display: block; opacity: 0.5;"></i>
                                            <p>Aucune réclamation trouvée</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reclamations as $rec): ?>
                                        <tr>
                                            <td><strong>#<?= htmlspecialchars($rec['id']) ?></strong></td>
                                            <td><?= date('d/m/Y', strtotime($rec['dateCreation'])) ?></td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <div class="user-avatar">
                                                        <?= !empty($rec['user_prenom']) ? strtoupper(substr($rec['user_prenom'], 0, 1)) : 'U' ?>
                                                    </div>
                                                    <div>
                                                        <?php if (!empty($rec['user_nom']) || !empty($rec['user_prenom'])): ?>
                                                            <strong><?= htmlspecialchars(($rec['user_prenom'] ?? '') . ' ' . ($rec['user_nom'] ?? '')) ?></strong>
                                                        <?php else: ?>
                                                            ID: <?= htmlspecialchars($rec['utilisateurId']) ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars(substr($rec['sujet'], 0, 30)) ?><?= strlen($rec['sujet']) > 30 ? '...' : '' ?>
                                            </td>
                                            <td><?= htmlspecialchars($rec['categorie']) ?></td>
                                            <td>
                                                <?php
                                                $prioClass = 'pending';
                                                if ($rec['priorite'] == 'Urgente')
                                                    $prioClass = 'rejected';
                                                elseif ($rec['priorite'] == 'Faible')
                                                    $prioClass = 'active';
                                                ?>
                                                <span
                                                    class="status <?= $prioClass ?>"><?= htmlspecialchars($rec['priorite']) ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                $statClass = 'pending';
                                                if ($rec['statut'] == 'Résolue')
                                                    $statClass = 'active';
                                                elseif ($rec['statut'] == 'Fermée')
                                                    $statClass = 'inactive';
                                                elseif ($rec['statut'] == 'En cours')
                                                    $statClass = 'pending';
                                                ?>
                                                <span
                                                    class="status <?= $statClass ?>"><?= htmlspecialchars($rec['statut']) ?></span>
                                            </td>
                                            <td>
                                                <div class="table-actions">
                                                    <a href="gestion_reclamation/showReclamation.php?id=<?= $rec['id'] ?>"
                                                        class="btn small ghost" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="gestion_reclamation/updateReclamation.php?id=<?= $rec['id'] ?>"
                                                        class="btn small ghost" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?delete=<?= $rec['id'] ?>" class="btn small danger"
                                                        title="Supprimer"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Filtrage en temps réel
        document.getElementById('searchReclamations').addEventListener('input', filterTable);
        document.getElementById('filterStatus').addEventListener('change', filterTable);
        document.getElementById('filterPriorite').addEventListener('change', filterTable);

        function filterTable() {
            const search = document.getElementById('searchReclamations').value.toLowerCase();
            const statusFilter = document.getElementById('filterStatus').value;
            const prioriteFilter = document.getElementById('filterPriorite').value;
            const rows = document.querySelectorAll('#reclamationsTableBody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const statusMatch = !statusFilter || row.textContent.includes(statusFilter);
                const prioriteMatch = !prioriteFilter || row.textContent.includes(prioriteFilter);
                const searchMatch = !search || text.includes(search);

                row.style.display = (statusMatch && prioriteMatch && searchMatch) ? '' : 'none';
            });
        }
    </script>
</body>

</html>