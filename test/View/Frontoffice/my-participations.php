<?php
session_start();
require_once __DIR__ . "/../Controller/ParticipationController.php";
require_once __DIR__ . "/../Model/ParticipationModel.php";
require_once __DIR__ . "/../../config/Config.php";

$currentYear = date('Y');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login or home page if not logged in
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['user_id'];

$participationController = new ParticipationController();
$userParticipations = $participationController->getParticipationsByUserId($userId);

// Initialize counts for statistics
$totalParticipations = count($userParticipations);
$upcomingParticipations = 0;
$pastParticipations = 0;

foreach ($userParticipations as $participation) {
    // Assuming 'date_evenement' is a field in the participation data
    // You might need to fetch event details to get the date
    // For now, let's assume a simple date comparison
    if (isset($participation['date_evenement']) && strtotime($participation['date_evenement']) > time()) {
        $upcomingParticipations++;
    } else {
        $pastParticipations++;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes participations - ImpactAble</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/style rayen.css">
</head>
<body>
<div class="container">
    <!-- Header COMPLET -->
    <header class="site-header" role="banner">
        <div class="brand">
            <button class="nav-toggle" id="navToggle" aria-label="Ouvrir le menu"><i class="fas fa-bars"></i></button>
            <div class="logo"><img src="../../assets/images/logo.png" alt="ImpactAble" class="logo-image"></div>
        </div>
        <div class="header-actions">
            <button class="btn ghost">Se connecter</button>
            <button class="btn primary">S'inscrire</button>
        </div>
    </header>

    <!-- Side Panel Navigation COMPLET -->
    <div class="side-panel" id="sidePanel">
        <div class="panel-header">
            <div class="logo"><img src="../../assets/images/logo.png" alt="ImpactAble" class="logo-image"></div>
            <button class="panel-close" id="panelClose"><i class="fas fa-times"></i></button>
        </div>
        <nav class="panel-nav">
            <div class="nav-section">
                <div class="nav-title">Navigation</div>
                <a href="index.php" class="nav-link"><i class="fas fa-home"></i> <span>Accueil</span></a>
                <a href="#" class="nav-link"><i class="fas fa-briefcase"></i> <span>Opportunités</span></a>
                <a href="#" class="nav-link"><i class="fas fa-calendar-alt"></i> <span>Événements</span></a>
                <a href="#" class="nav-link"><i class="fas fa-hand-holding-heart"></i> <span>Campagnes</span></a>
                <a href="#" class="nav-link"><i class="fas fa-book"></i> <span>Ressources</span></a>
                <a href="#" class="nav-link"><i class="fas fa-comments"></i> <span>Forum</span></a>
                <a href="#" class="nav-link"><i class="fas fa-comment-alt"></i> <span>Réclamations</span></a>
            </div>
        </nav>
        <div class="panel-footer">
            <div class="user-profile">
                <div class="user-avatar">VS</div>
                <div class="user-info"><h4>Visiteur</h4><p>Connectez-vous pour plus de fonctionnalités</p></div>
            </div>
        </div>
    </div>
    <div class="panel-overlay" id="panelOverlay"></div>

    <!-- Main Content -->
    <main style="max-width: 1100px; margin: 40px auto; padding: 0 20px;">
        <div style="margin-bottom: 40px;">
            <h1 style="font-size: 2.2rem; color: #4b2e16; margin-bottom: 10px;"><i class="fas fa-heart" style="color:#ffb3b3;"></i> Mes Participations</h1>
            <p style="color: #6b4b44; font-size: 1.05rem;">Consultez et gérez toutes vos inscriptions aux événements</p>
        </div>

        <!-- Statistiques rapides -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px;">
            <div style="background: linear-gradient(135deg, rgba(94,109,59,0.1), rgba(169,185,125,0.1)); padding: 15px; border-radius: 10px; text-align: center; border-left: 3px solid #5e6d3b;">
                <div style="font-size: 1.8rem; font-weight: 700; color: #5e6d3b;"><?= $totalParticipations ?></div>
                <div style="font-size: 0.85rem; color: #6b4b44; font-weight: 500;">Événements inscrits</div>
            </div>
            <div style="background: linear-gradient(135deg, rgba(46, 204, 113, 0.1), rgba(27, 188, 155, 0.1)); padding: 15px; border-radius: 10px; text-align: center; border-left: 3px solid #27ae60;">
                <div style="font-size: 1.8rem; font-weight: 700; color: #27ae60;"><?= $upcomingParticipations ?></div>
                <div style="font-size: 0.85rem; color: #6b4b44; font-weight: 500;">À venir</div>
            </div>
            <div style="background: linear-gradient(135deg, rgba(180, 123, 71, 0.1), rgba(158, 89, 43, 0.1)); padding: 15px; border-radius: 10px; text-align: center; border-left: 3px solid #b47b47;">
                <div style="font-size: 1.8rem; font-weight: 700; color: #b47b47;"><?= $pastParticipations ?></div>
                <div style="font-size: 0.85rem; color: #6b4b44; font-weight: 500;">Terminés</div>
            </div>
        </div>

        <!-- Liste des participations -->
        <div id="participationsList" style="display: grid; gap: 20px;">
            <?php if (empty($userParticipations)): ?>
                <p style="text-align: center; color: #6b4b44;">Vous n'avez pas encore d'inscriptions à des événements.</p>
            <?php else: ?>
                <?php foreach ($userParticipations as $participation): ?>
                    <?php
                    // Determine status display
                    $statusText = ucfirst($participation['statut']);
                    $statusClass = '';
                    $statusIcon = '';
                    $borderColor = '';
                    $bgColor = '';
                    $textColor = '';

                    switch ($participation['statut']) {
                        case 'confirmé':
                            $statusClass = 'status-confirmed';
                            $statusIcon = 'fa-check-circle';
                            $borderColor = '#27ae60';
                            $bgColor = 'rgba(46, 204, 113, 0.1)';
                            $textColor = '#27ae60';
                            break;
                        case 'annulé':
                            $statusClass = 'status-cancelled';
                            $statusIcon = 'fa-times-circle';
                            $borderColor = '#e74c3c';
                            $bgColor = 'rgba(231, 76, 60, 0.1)';
                            $textColor = '#e74c3c';
                            break;
                        case 'en_attente':
                        default:
                            $statusText = 'En attente'; // Override for "inscrit" or other
                            $statusClass = 'status-pending';
                            $statusIcon = 'fa-clock';
                            $borderColor = '#f39c12';
                            $bgColor = 'rgba(243, 156, 18, 0.1)';
                            $textColor = '#f39c12';
                            break;
                    }
                    ?>
                    <div style="background: #fffaf5; border-left: 4px solid <?= $borderColor ?>; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(75,46,22,0.08);">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                            <div>
                                <h3 style="font-size: 1.3rem; color: #4b2e16; font-weight: 600; margin: 0;"><?= htmlspecialchars($participation['nom_evenement']) ?></h3>
                                <div style="color: #6b4b44; font-size: 0.95rem;"><i class="fas fa-calendar"></i> <?= htmlspecialchars(date('d M Y, H', strtotime($participation['date_evenement']))) ?>h - <?= htmlspecialchars(date('H', strtotime($participation['date_evenement']) + $participation['duree_heures'] * 3600)) ?>h</div>
                            </div>
                            <span class="<?= $statusClass ?>" style="padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; background: <?= $bgColor ?>; color: <?= $textColor ?>;">
                                <i class="fas <?= $statusIcon ?>"></i> <?= $statusText ?>
                            </span>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: #e1e8c9; display: flex; align-items: center; justify-content: center; color: #5e6d3b;"><i class="fas fa-map-marker-alt"></i></div>
                                <div><div style="font-weight: 600; color: #4b2e16;"><?= htmlspecialchars($participation['lieu']) ?></div><div style="color: #6b4b44; font-size: 0.9rem;"><?= htmlspecialchars($participation['adresse']) ?></div></div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: #e1e8c9; display: flex; align-items: center; justify-content: center; color: #5e6d3b;"><i class="fas fa-users"></i></div>
                                <div><div style="font-weight: 600; color: #4b2e16;">Accompagnants</div><div style="color: #6b4b44; font-size: 0.9rem;"><?= htmlspecialchars($participation['nombre_accompagnants']) ?> confirmé(s)</div></div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: #e1e8c9; display: flex; align-items: center; justify-content: center; color: #5e6d3b;"><i class="fas fa-wheelchair"></i></div>
                                <div><div style="font-weight: 600; color: #4b2e16;">Besoins</div><div style="color: #6b4b44; font-size: 0.9rem;"><?= htmlspecialchars($participation['besoins_accessibilite']) ?></div></div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: #e1e8c9; display: flex; align-items: center; justify-content: center; color: #5e6d3b;"><i class="fas fa-clock"></i></div>
                                <div><div style="font-weight: 600; color: #4b2e16;">Inscrit le</div><div style="color: #6b4b44; font-size: 0.9rem;"><?= htmlspecialchars(date('d M Y', strtotime($participation['date_inscription']))) ?></div></div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px; padding-top: 15px; border-top: 1px solid rgba(75,46,22,0.1);">
                            <a href="event_details.php?id=<?= htmlspecialchars($participation['id_evenement']) ?>" style="padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; background: #b47b47; color: white; font-weight: 600; text-decoration: none;"><i class="fas fa-eye"></i> Détails</a>
                            <?php if ($participation['statut'] === 'en_attente' || $participation['statut'] === 'inscrit'): ?>
                                <button class="btn-modifier" data-id="<?= htmlspecialchars($participation['id']) ?>" data-event-id="<?= htmlspecialchars($participation['id_evenement']) ?>" style="padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; background: #5e6d3b; color: white; font-weight: 600;"><i class="fas fa-edit"></i> Modifier</button>
                                <button class="btn-annuler" data-id="<?= htmlspecialchars($participation['id']) ?>" data-event-id="<?= htmlspecialchars($participation['id_evenement']) ?>" data-user-id="<?= htmlspecialchars($userId) ?>" style="padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; background: #e74c3c; color: white; font-weight: 600;"><i class="fas fa-times"></i> Annuler</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer COMPLET -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>ImpactAble</h3>
                    <p class="text-muted">Plateforme dédiée à l'inclusion et à l'impact social.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Navigation</h3>
                    <div class="footer-links">
                        <a href="index.php">Accueil</a>
                        <a href="events-list.php">Événements</a>
                        <a href="my-participations.php">Mes participations</a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Contact</h3>
                    <div class="footer-links">
                        <a href="mailto:contact@impactable.org">contact@impactable.org</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© <?= $currentYear ?> ImpactAble — Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</div>

<script src="../../assets/js/script.js"></script>
<script src="../../assets/js/participant.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Handle Annuler button click
        document.querySelectorAll('.btn-annuler').forEach(button => {
            button.addEventListener('click', async (e) => {
                const participationId = e.target.dataset.id;
                const eventId = e.target.dataset.eventId;
                const userId = e.target.dataset.userId;

                if (!confirm('Êtes-vous sûr de vouloir annuler votre participation à cet événement ?')) {
                    return;
                }

                try {
                    const response = await fetch('../Controller/ParticipationController.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=cancel_participation&id_evenement=${eventId}&id_utilisateur=${userId}`
                    });
                    const result = await response.json();

                    if (result.success) {
                        alert('Participation annulée avec succès.');
                        location.reload(); // Reload to reflect changes
                    } else {
                        alert('Erreur lors de l\'annulation: ' + result.error);
                    }
                } catch (error) {
                    console.error('Erreur AJAX:', error);
                    alert('Une erreur est survenue lors de l\'annulation.');
                }
            });
        });

        // Handle Modifier button click (to confirm status)
        document.querySelectorAll('.btn-modifier').forEach(button => {
            button.addEventListener('click', async (e) => {
                const participationId = e.target.dataset.id;
                const eventId = e.target.dataset.eventId; // Not strictly needed for status update, but good to have

                if (!confirm('Voulez-vous confirmer votre participation à cet événement ?')) {
                    return;
                }

                try {
                    const response = await fetch('../Controller/ParticipationController.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=confirm_my_participation&id=${participationId}`
                    });
                    const result = await response.json();

                    if (result.success) {
                        alert('Participation confirmée avec succès.');
                        location.reload(); // Reload to reflect changes
                    } else {
                        alert('Erreur lors de la confirmation: ' + result.error);
                    }
                } catch (error) {
                    console.error('Erreur AJAX:', error);
                    alert('Une erreur est survenue lors de la confirmation.');
                }
            });
        });
    });
</script>
</body>
</html>