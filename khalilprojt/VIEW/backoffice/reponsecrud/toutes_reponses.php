<?php
require_once(__DIR__ . '/../../../controller/ReponseController.php');
require_once(__DIR__ . '/../../../controller/ReclamationController.php');
require_once(__DIR__ . '/../../../CONFIGRRATION/config.php');

$reclamationController = new ReclamationController();
$reclamations = $reclamationController->listReclamations();

// Fonction pour récupérer toutes les réponses
function getAllReponses($db) {
    $sql = "SELECT r.*, rec.sujet as reclamation_sujet, rec.id as reclamation_id,
                   u.nom, u.prenom
            FROM reponse r
            LEFT JOIN reclamation rec ON r.Id_reclamation = rec.id
            LEFT JOIN utilisateur u ON r.Id_utilisateur = u.Id_utilisateur
            ORDER BY r.date_reponse DESC";
    
    try {
        $query = $db->query($sql);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

$db = config::getConnexion();
$toutesReponses = getAllReponses($db);
$totalReponses = count($toutesReponses);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toutes les Réponses - ImpactAble</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/admin-style.css">
</head>
<body class="with-sidebar">
    <aside class="sidebar">
        <div>
            <div class="logo">ImpactAble</div>
            <nav class="nav-links">
                <a href="../admin_dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="../statistiques_avancees.php">
                    <i class="fas fa-chart-line"></i> Statistiques IA
                </a>
                <a href="../gestion_reclamation/addReclamation.php">
                    <i class="fas fa-file-alt"></i> Réclamations
                </a>
                <a href="toutes_reponses.php" class="active">
                    <i class="fas fa-comments"></i> Réponses
                </a>
                <a href="../../frontoffice/index.php" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Front Office
                </a>
            </nav>
        </div>
        <footer>© 2025 ImpactAble</footer>
    </aside>

    <main>
        <div class="container">
            <header>
                <h1><i class="fas fa-comments"></i> Toutes les Réponses</h1>
                <div class="user-info">Admin</div>
            </header>

            <!-- Statistiques -->
            <div class="stats-card" style="margin-bottom: 30px;">
                <div class="stat-item">
                    <div class="stat-value"><?= $totalReponses ?></div>
                    <div class="stat-label">Réponses Totales</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= count($reclamations) ?></div>
                    <div class="stat-label">Réclamations</div>
                </div>
            </div>

            <!-- Filtre par réclamation -->
            <div class="filters-section" style="margin-bottom: 25px;">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchReponses" placeholder="Rechercher dans les réponses...">
                </div>
                <select id="filterReclamation" class="filter-select">
                    <option value="">Toutes les réclamations</option>
                    <?php foreach ($reclamations as $rec): ?>
                        <option value="<?= $rec['id'] ?>">
                            #<?= $rec['id'] ?> - <?= htmlspecialchars(substr($rec['sujet'], 0, 50)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Liste des réponses -->
            <div class="reponses-container">
                <h2><i class="fas fa-list"></i> Liste des Réponses (<?= $totalReponses ?>)</h2>
                
                <?php if (empty($toutesReponses)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>Aucune réponse pour le moment</h3>
                        <p>Il n'y a pas encore de réponses dans le système.</p>
                    </div>
                <?php else: ?>
                    <div id="reponsesList">
                        <?php foreach ($toutesReponses as $rep): ?>
                            <div class="reponse-card" 
                                 data-reclamation="<?= $rep['reclamation_id'] ?>"
                                 data-content="<?= htmlspecialchars(strtolower($rep['message'])) ?>">
                                <div class="reponse-header">
                                    <div class="reponse-author">
                                        <i class="fas fa-user-tie"></i>
                                        <?= htmlspecialchars(($rep['prenom'] ?? 'Admin') . ' ' . ($rep['nom'] ?? '')) ?>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                                        <a href="liste_reponses.php?reclamation_id=<?= $rep['reclamation_id'] ?>" 
                                           class="btn btn-info" style="padding: 5px 12px; font-size: 0.85em;">
                                            <i class="fas fa-hashtag"></i> Réclamation #<?= $rep['reclamation_id'] ?>
                                        </a>
                                        <div class="reponse-date">
                                            <i class="fas fa-clock"></i>
                                            <?= date('d/m/Y à H:i', strtotime($rep['date_reponse'])) ?>
                                        </div>
                                        <div class="reponse-actions">
                                            <a href="modifier_reponse.php?id=<?= $rep['Id_reponse'] ?>&reclamation_id=<?= $rep['reclamation_id'] ?>" 
                                               class="btn-edit-reponse" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="supprimer_reponse.php?id=<?= $rep['Id_reponse'] ?>&reclamation_id=<?= $rep['reclamation_id'] ?>" 
                                               class="btn-delete-reponse" 
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réponse ?')"
                                               title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($rep['reclamation_sujet'])): ?>
                                    <div style="background: #F4ECDD; padding: 10px 15px; border-radius: 8px; margin-bottom: 15px;">
                                        <strong style="color: #5E6D3B;">Sujet :</strong> 
                                        <span style="color: #4B2E16;"><?= htmlspecialchars($rep['reclamation_sujet']) ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="reponse-content">
                                    <?= nl2br(htmlspecialchars($rep['message'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        // Filtrage
        document.getElementById('searchReponses').addEventListener('input', filterReponses);
        document.getElementById('filterReclamation').addEventListener('change', filterReponses);

        function filterReponses() {
            const search = document.getElementById('searchReponses').value.toLowerCase();
            const reclamationFilter = document.getElementById('filterReclamation').value;
            const cards = document.querySelectorAll('.reponse-card');

            cards.forEach(card => {
                const content = card.getAttribute('data-content') || '';
                const reclamationId = card.getAttribute('data-reclamation');
                
                const matchSearch = !search || content.includes(search);
                const matchReclamation = !reclamationFilter || reclamationId === reclamationFilter;

                card.style.display = (matchSearch && matchReclamation) ? '' : 'none';
            });
        }
    </script>
</body>
</html>

