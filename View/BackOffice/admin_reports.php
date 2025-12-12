<?php
require_once __DIR__ . '/../../config.php';

$user_id = $_SESSION['user_id'] ?? 0;
$is_admin = $_SESSION['is_admin'] ?? false;

if (!$is_admin) {
    header('Location: /projt/index.php?action=login');
    exit;
}

$pdo = config::getConnexion();

$reportsQuery = "
    SELECT 
        ur.id,
        ur.reporter_id,
        ur.target_type,
        ur.target_id,
        ur.reason,
        ur.status,
        ur.created_at,
        u1.nom_utilisateur as reporter_name,
        u2.nom_utilisateur as target_user_name,
        CASE 
            WHEN ur.target_type = 'user' THEN u2.nom_utilisateur
            WHEN ur.target_type = 'post' THEN p.titre
            WHEN ur.target_type = 'comment' THEN c.contenu
            ELSE 'N/A'
        END as target_description
    FROM user_reports ur
    LEFT JOIN utilisateur u1 ON ur.reporter_id = u1.Id_utilisateur
    LEFT JOIN utilisateur u2 ON ur.target_type = 'user' AND ur.target_id = u2.Id_utilisateur
    LEFT JOIN post p ON ur.target_type = 'post' AND ur.target_id = p.Id_post
    LEFT JOIN commentaire c ON ur.target_type = 'comment' AND ur.target_id = c.Id_commentaire
    ORDER BY ur.created_at DESC
";

try {
    $stmt = $pdo->query($reportsQuery);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $reports = [];
}

// Count reports by status
$openCount = count(array_filter($reports, fn($r) => $r['status'] === 'open'));
$closedCount = count(array_filter($reports, fn($r) => $r['status'] === 'closed'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signalements - ImpactAble Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/projt/View/assets/css/style.css">
</head>
<body>

<div class="container">
    <div class="admin-header">
        <h1><i class="fas fa-flag"></i> Signalements et Modération</h1>
        <a href="/projt/index.php?action=admin" class="btn ghost">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
        </a>
    </div>

    <!-- Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <div style="font-size: 2rem; font-weight: 700;"><?= count($reports) ?></div>
            <div style="font-size: 0.9rem; opacity: 0.9;">Total des signalements</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <div style="font-size: 2rem; font-weight: 700;"><?= $openCount ?></div>
            <div style="font-size: 0.9rem; opacity: 0.9;">En attente</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <div style="font-size: 2rem; font-weight: 700;"><?= $closedCount ?></div>
            <div style="font-size: 0.9rem; opacity: 0.9;">Résolus</div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="forum-card">
        <div class="forum-header">
            <h2><i class="fas fa-list"></i> Tous les signalements</h2>
        </div>
        <div class="forum-body">
            <?php if (empty($reports)): ?>
                <p style="text-align: center; padding: 40px; color: var(--muted);">
                    <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 12px;"></i><br>
                    Aucun signalement pour le moment. C'est du bon travail !
                </p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #e8e8e8;">
                                <th style="padding: 16px; text-align: left; font-weight: 600; color: #2c3e50;">Date</th>
                                <th style="padding: 16px; text-align: left; font-weight: 600; color: #2c3e50;">Type</th>
                                <th style="padding: 16px; text-align: left; font-weight: 600; color: #2c3e50;">Contenu</th>
                                <th style="padding: 16px; text-align: left; font-weight: 600; color: #2c3e50;">Raison</th>
                                <th style="padding: 16px; text-align: left; font-weight: 600; color: #2c3e50;">Signalé par</th>
                                <th style="padding: 16px; text-align: left; font-weight: 600; color: #2c3e50;">Statut</th>
                                <th style="padding: 16px; text-align: center; font-weight: 600; color: #2c3e50;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports as $report): ?>
                            <tr style="border-bottom: 1px solid #f0f0f0; transition: background 0.2s;">
                                <td style="padding: 16px; color: var(--muted); font-size: 0.9rem;">
                                    <?= date('d/m/Y H:i', strtotime($report['created_at'])) ?>
                                </td>
                                <td style="padding: 16px;">
                                    <?php
                                    $typeLabel = [
                                        'post' => '<span style="background: #3498db; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">Post</span>',
                                        'comment' => '<span style="background: #9b59b6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">Commentaire</span>',
                                        'user' => '<span style="background: #e74c3c; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">Utilisateur</span>'
                                    ];
                                    echo $typeLabel[$report['target_type']] ?? 'N/A';
                                    ?>
                                </td>
                                <td style="padding: 16px; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <span title="<?= htmlspecialchars(substr($report['target_description'], 0, 100)) ?>">
                                        <?= htmlspecialchars(substr($report['target_description'], 0, 50)) ?>...
                                    </span>
                                </td>
                                <td style="padding: 16px; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <span title="<?= htmlspecialchars($report['reason']) ?>">
                                        <?= htmlspecialchars(substr($report['reason'], 0, 40)) ?>...
                                    </span>
                                </td>
                                <td style="padding: 16px;">
                                    <strong><?= htmlspecialchars($report['reporter_name'] ?? 'Admin #' . $report['reporter_id']) ?></strong>
                                </td>
                                <td style="padding: 16px;">
                                    <?php
                                    $statusStyle = $report['status'] === 'open' 
                                        ? 'background: #f39c12; color: white;' 
                                        : 'background: #27ae60; color: white;';
                                    $statusLabel = $report['status'] === 'open' ? 'En attente' : 'Résolu';
                                    ?>
                                    <span style="<?= $statusStyle ?> padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td style="padding: 16px; text-align: center;">
                                    <button class="btn primary small" title="Voir les détails" onclick="showReportDetails(<?= htmlspecialchars(json_encode($report)) ?>)" style="margin-right: 4px;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($report['status'] === 'open'): ?>
                                    <button class="btn success small" title="Marquer comme résolu" onclick="closeReport(<?= $report['id'] ?>)">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Details -->
<div id="detailsModal" class="modal-backdrop" style="display: none;">
    <div class="report-modal">
        <div class="report-modal-header">
            <h3 style="margin:0; color: #2c3e50;">Détails du signalement</h3>
            <button class="modal-close" onclick="document.getElementById('detailsModal').style.display = 'none';">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="report-modal-body">
            <div id="detailsContent"></div>
        </div>
    </div>
</div>

<script>
function showReportDetails(report) {
    const content = `
        <div style="margin-bottom: 16px;">
            <h4 style="color: #2c3e50; margin-top: 0;">Informations</h4>
            <p><strong>ID Signalement:</strong> ${report.id}</p>
            <p><strong>Type:</strong> ${report.target_type}</p>
            <p><strong>Date:</strong> ${new Date(report.created_at).toLocaleString('fr-FR')}</p>
            <p><strong>Statut:</strong> ${report.status === 'open' ? '🟠 En attente' : '🟢 Résolu'}</p>
        </div>
        <div style="margin-bottom: 16px;">
            <h4 style="color: #2c3e50;">Signalé par</h4>
            <p><strong>${report.reporter_name || 'Admin #' + report.reporter_id}</strong></p>
        </div>
        <div style="margin-bottom: 16px;">
            <h4 style="color: #2c3e50;">Contenu signalé</h4>
            <div style="background: #f8f9fa; padding: 12px; border-radius: 8px; border-left: 4px solid #e74c3c;">
                <p style="margin: 0; color: #2c3e50;">${report.target_description}</p>
            </div>
        </div>
        <div>
            <h4 style="color: #2c3e50;">Raison du signalement</h4>
            <div style="background: #fff3cd; padding: 12px; border-radius: 8px; border-left: 4px solid #f39c12;">
                <p style="margin: 0; color: #2c3e50;">${report.reason || 'Pas de raison fournie'}</p>
            </div>
        </div>
    `;
    document.getElementById('detailsContent').innerHTML = content;
    document.getElementById('detailsModal').style.display = 'flex';
}

function closeReport(reportId) {
    if (!confirm('Marquer ce signalement comme résolu ?')) return;
    
    fetch('/projt/View/BackOffice/close_report.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ report_id: reportId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Signalement marqué comme résolu');
            location.reload();
        } else {
            alert('Erreur: ' + (data.message || 'Impossible de mettre à jour'));
        }
    })
    .catch(err => alert('Erreur: ' + err));
}

// Fermer le modal en cliquant sur le backdrop
document.getElementById('detailsModal')?.addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>

</body>
</html>
