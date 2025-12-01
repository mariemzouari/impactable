<?php
session_start();
require_once(__DIR__ . '/../../controller/ReclamationController.php');
require_once(__DIR__ . '/../../CONFIGRRATION/config.php');

$controller = new ReclamationController();
$reclamations = $controller->listReclamations();
$stats = $controller->getStats();

// Gestion de la suppression
if (isset($_GET['delete'])) {
    $controller->deleteReclamation($_GET['delete']);
    header('Location: admin_dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Gestion des Réclamations</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin-style.css">
</head>
<body class="with-sidebar">
    <aside class="sidebar">
        <div>
            <div class="logo">ImpactAble</div>
            <nav class="nav-links">
                <a href="admin_dashboard.php" class="active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="gestion_reclamation/addReclamation.php">
                    <i class="fas fa-file-alt"></i> Réclamations
                </a>
                <a href="reponsecrud/liste_reponses.php?reclamation_id=1">
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
            <h1><i class="fas fa-tachometer-alt"></i> Dashboard Admin - Gestion des Réclamations</h1>
            <div class="user-info">Admin</div>
        </header>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Réclamation mise à jour avec succès !
            </div>
        <?php endif; ?>

        <!-- Statistiques -->
        <section class="stats">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #FFF3CD, #FFE082);">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= $stats['en_attente'] ?? 0 ?></div>
                    <div class="stat-label">En attente</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #CCE5FF, #90CAF9);">
                    <i class="fas fa-spinner"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= $stats['en_cours'] ?? 0 ?></div>
                    <div class="stat-label">En cours</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #D4EDDA, #A5D6A7);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= $stats['resolues'] ?? 0 ?></div>
                    <div class="stat-label">Résolues</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #F8D7DA, #EF9A9A);">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= $stats['urgentes'] ?? 0 ?></div>
                    <div class="stat-label">Urgentes</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #E1BEE7, #CE93D8);">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
                    <div class="stat-label">Total Réclamations</div>
                </div>
            </div>
        </section>

        <!-- Filtres -->
        <section class="section">
            <div class="filters-section">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchReclamations" placeholder="Rechercher par ID, sujet, utilisateur...">
            </div>
            
            <select id="filterStatus" class="filter-select">
                <option value="">Tous les statuts</option>
                <option value="En attente">En attente</option>
                <option value="En cours">En cours</option>
                <option value="Résolue">Résolue</option>
                <option value="Fermée">Fermée</option>
            </select>

            <select id="filterPriorite" class="filter-select">
                <option value="">Toutes priorités</option>
                <option value="Urgente">Urgente</option>
                <option value="Moyenne">Moyenne</option>
                <option value="Faible">Faible</option>
            </select>
            <a href="gestion_reclamation/addReclamation.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter une Réclamation
            </a>
        </div>
        </section>

        <!-- Tableau -->
        <section class="section">
            <h2><i class="fas fa-list"></i> Liste des Réclamations</h2>
            <div class="table-container">
            <table class="reclamations-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Utilisateur</th>
                        <th>Sujet</th>
                        <th>Catégorie</th>
                        <th>Priorité</th>
                        <th>Statut</th>
                        <th>Agent</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="reclamationsTableBody">
                    <?php if (empty($reclamations)): ?>
                        <tr>
                            <td colspan="9" class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Aucune réclamation trouvée</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reclamations as $rec): ?>
                            <tr>
                                <td><strong>#<?= htmlspecialchars($rec['id']) ?></strong></td>
                                <td><?= date('d/m/Y H:i', strtotime($rec['dateCreation'])) ?></td>
                                <td>
                                    <div class="user-info">
                                        <?php if (isset($rec['nom']) && isset($rec['prenom'])): ?>
                                            <strong><?= htmlspecialchars($rec['prenom'] . ' ' . $rec['nom']) ?></strong>
                                            <?php if (isset($rec['email'])): ?>
                                                <small><?= htmlspecialchars($rec['email']) ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            ID: <?= htmlspecialchars($rec['utilisateurId']) ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars(substr($rec['sujet'], 0, 40)) ?><?= strlen($rec['sujet']) > 40 ? '...' : '' ?></td>
                                <td><?= htmlspecialchars($rec['categorie']) ?></td>
                                <td>
                                    <span class="priority-badge priority-<?= htmlspecialchars($rec['priorite']) ?>">
                                        <?= htmlspecialchars($rec['priorite']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= htmlspecialchars($rec['statut']) ?>">
                                        <?= htmlspecialchars($rec['statut']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($rec['agentAttribue'] ?? 'Non attribué') ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="gestion_reclamation/showReclamation.php?id=<?= $rec['id'] ?>" class="btn-view" title="Voir">
                                            <i class="fas fa-eye"></i> Voir
                                        </a>
                                        <a href="reponsecrud/liste_reponses.php?reclamation_id=<?= $rec['id'] ?>" class="btn-reply" title="Voir les réponses">
                                            <i class="fas fa-comments"></i> Réponses
                                        </a>
                                        <a href="gestion_reclamation/updateReclamation.php?id=<?= $rec['id'] ?>" class="btn-edit" title="Modifier">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                        <a href="?delete=<?= $rec['id'] ?>" class="btn-delete" title="Supprimer" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?')">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        </section>
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
                if (row.classList.contains('empty-state')) return;
                
                const text = row.textContent.toLowerCase();
                const statusMatch = !statusFilter || row.textContent.includes(statusFilter);
                const prioriteMatch = !prioriteFilter || row.textContent.includes(prioriteFilter);
                const searchMatch = !search || text.includes(search);

                row.style.display = (statusMatch && prioriteMatch && searchMatch) ? '' : 'none';
            });
        }
    </script>
    </main>
</body>
</html>

