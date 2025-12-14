<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
// Vérifiez bien que ces chemins sont corrects par rapport à l'emplacement de ce fichier
require_once "../../Config.php";
require_once "../../Model/ParticipationModel.php";
require_once "../../Model/EventModel.php";

$Config = new Config();
$db = $Config->getPDO();
$eventModel = new EventModel($db);
$participationModel = new ParticipationModel($db);

$sort = $_GET['sort'] ?? '';

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $events = $eventModel->searchByTitle($_GET['search'], $sort);
} else {
    $events = $eventModel->getAll($sort);
}

$userId = $_SESSION['user_id'] ?? null;
$currentYear = date('Y');
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements — ImpactAble</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/style%20rayen.css">
</head>

<body>
    <div class="container">
        <header class="site-header" role="banner">
            <div class="brand">
                <button class="nav-toggle" id="navToggle" aria-label="Ouvrir le menu"><i
                        class="fas fa-bars"></i></button>
                <div class="logo">
                    <img src="../../assets/images/logo.png" alt="ImpactAble" class="logo-image">
                </div>
            </div>
            <div class="header-actions">
                <?php if (!$userId): ?>
                    <a href="index.php?action=connexion" class="btn ghost">Se connecter</a>
                    <a href="index.php?action=inscription" class="btn primary">S'inscrire</a>
                <?php else: ?>
                    <span class="user-welcome">Bonjour, <?= htmlspecialchars($_SESSION['user_prenom'] ?? ''); ?></span>
                    <a href="my-participations.php" class="btn ghost">Mes participations</a>
                    <a href="index.php?action=deconnexion" class="btn secondary">Déconnexion</a>
                <?php endif; ?>
            </div>
        </header>

        <div class="side-panel" id="sidePanel">
            <div class="panel-header">
                <div class="logo">
                    <img src="../../assets/images/logo.png" alt="ImpactAble" class="logo-image">
                </div>
                <button class="panel-close" id="panelClose"><i class="fas fa-times"></i></button>
            </div>
            <nav class="panel-nav">
                <div class="nav-section">
                    <div class="nav-title">Navigation</div>
                    <a href="index.php" class="nav-link"><i class="fas fa-home"></i> <span>Accueil</span></a>
                    <a href="#" class="nav-link"><i class="fas fa-briefcase"></i> <span>Opportunités</span></a>
                    <a href="events-list.php" class="nav-link active"><i class="fas fa-calendar-alt"></i>
                        <span>Événements</span></a>
                    <a href="#" class="nav-link"><i class="fas fa-hand-holding-heart"></i> <span>Campagnes</span></a>
                    <a href="#" class="nav-link"><i class="fas fa-book"></i> <span>Ressources</span></a>
                    <a href="#" class="nav-link"><i class="fas fa-comments"></i> <span>Forum</span></a>
                    <a href="#" class="nav-link"><i class="fas fa-comment-alt"></i> <span>Réclamations</span></a>
                </div>
            </nav>
            <div class="panel-footer">
                <div class="user-profile">
                    <div class="user-avatar">VS</div>
                    <div class="user-info">
                        <h4><?= $userId ? 'Utilisateur' : 'Visiteur' ?></h4>
                        <p><?= $userId ? 'Bienvenue' : 'Connectez-vous pour plus de fonctionnalités' ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-overlay" id="panelOverlay"></div>

        <section class="section">
            <div style="max-width:1200px; margin:0 auto; padding:3rem 0;">
                <h1 style="text-align:center; margin-bottom:2rem; color: #4b2e16;">
                    <i class="fas fa-calendar-alt"></i> Tous nos événements
                </h1>

                <div
                    style="margin-bottom: 2rem; text-align: center; display: flex; justify-content: center; gap: 10px;">
                    <form action="events-list.php" method="GET" style="display: flex; gap: 10px;">
                        <input type="text" name="search" placeholder="Rechercher un événement..."
                            style="padding: 10px; width: 300px; border-radius: 5px; border: 1px solid #ccc;"
                            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <button type="submit"
                            style="padding: 10px 20px; border-radius: 5px; border: none; background-color: #5e6d3b; color: white; cursor: pointer;">Rechercher</button>
                        <select name="sort" onchange="this.form.submit()"
                            style="padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                            <option value="">Trier par</option>
                            <option value="date_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'date_asc') ? 'selected' : '' ?>>Date (croissant)</option>
                            <option value="date_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'date_desc') ? 'selected' : '' ?>>Date (décroissant)</option>
                            <option value="title_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'title_asc') ? 'selected' : '' ?>>Titre (A-Z)</option>
                            <option value="title_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'title_desc') ? 'selected' : '' ?>>Titre (Z-A)</option>
                        </select>
                    </form>
                </div>


                <?php if (!empty($events)): ?>
                    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:2rem;">
                        <?php foreach ($events as $event): ?>
                            <div style="background:#fffaf5; border-radius:12px; padding:1.5rem; box-shadow:0 4px 12px rgba(75,46,22,0.08); transition:transform 0.2s, box-shadow 0.2s; border-left: 4px solid #5e6d3b;"
                                onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 6px 16px rgba(75,46,22,0.12)'"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(75,46,22,0.08)'">
                                <h3 style="margin:0 0 0.5rem 0; color:#4b2e16; font-weight:600;">
                                    <?= htmlspecialchars($event['titre']) ?>
                                </h3>
                                <p style="margin:0.5rem 0; color:#6b4b44; font-size:0.9rem;">
                                    <i class="fas fa-calendar" style="color:#5e6d3b; margin-right:0.5rem;"></i>
                                    <?= date('d M Y H:i', strtotime($event['date_debut'])) ?>
                                </p>
                                <p style="margin:0.5rem 0; color:#6b4b44; font-size:0.9rem;">
                                    <?php
                                    $category = htmlspecialchars($event['categorie']);
                                    $logoPath = '';
                                    $metierIcon = '';

                                    switch (mb_strtolower(trim($category), 'UTF-8')) {
                                        case 'éducation':
                                            $logoPath = '../../assets/images/logo_education.png';
                                            $metierIcon = '<i class="fas fa-graduation-cap" style="color:#b47b47; margin-right:0.5rem;"></i>';
                                            break;

                                        case 'environnement':
                                            $logoPath = '../../assets/images/logo_environment.jpeg';
                                            $metierIcon = '<i class="fas fa-leaf" style="color:#5e6d3b; margin-right:0.5rem;"></i>';
                                            break;

                                        case 'santé':
                                            $logoPath = '../../assets/images/logo_health.jpg';
                                            $metierIcon = '<i class="fas fa-heartbeat" style="color:#d9534f; margin-right:0.5rem;"></i>';
                                            break;

                                        case 'solidarité':
                                            $logoPath = '../../assets/images/logo_default.jpeg';
                                            $metierIcon = '<i class="fas fa-hands-helping" style="color:#5bc0de; margin-right:0.5rem;"></i>';
                                            break;

                                        case 'culture':
                                            $logoPath = '../../assets/images/logo_default.jpeg';
                                            $metierIcon = '<i class="fas fa-palette" style="color:#f0ad4e; margin-right:0.5rem;"></i>';
                                            break;

                                        case 'sport':
                                            $logoPath = '../../assets/images/logo_default.jpeg';
                                            $metierIcon = '<i class="fas fa-futbol" style="color:#337ab7; margin-right:0.5rem;"></i>';
                                            break;

                                        case 'métier':
                                            $logoPath = '../../assets/images/logo_default.jpeg';
                                            $metierIcon = '<i class="fas fa-briefcase" style="color:#6b4b44; margin-right:0.5rem;"></i>';
                                            break;

                                        default:
                                            $logoPath = '../../assets/images/logo_default.jpeg';
                                            $metierIcon = '<i class="fas fa-tag" style="color:#6b4b44; margin-right:0.5rem;"></i>';
                                            break;
                                    }
                                    ?>
                                    <?= $metierIcon ?>
                                    <img src="<?= $logoPath ?>" alt="<?= $category ?> logo"
                                        style="height:16px; width:16px; margin-right:0.5rem; object-fit: contain;">
                                    <span
                                        style="background:rgba(180,123,71,0.1); padding:3px 8px; border-radius:4px; color:#b47b47;">
                                        <?= $category ?>
                                    </span>
                                </p>
                                <p style="margin:1rem 0; color:#4b2e16; font-size:0.95rem; line-height:1.4;">
                                    <?= htmlspecialchars(substr($event['description'], 0, 100)) ?>...
                                </p>
                                <?php if (!empty($event['location'])): ?>
                                    <div style="margin:1rem 0;">
                                        <h4 style="margin:0 0 0.5rem 0; color:#4b2e16; font-weight:600; font-size:1rem;">
                                            <i class="fas fa-map-marker-alt" style="color:#b47b47; margin-right:0.5rem;"></i> Lieu
                                            de l'événement:
                                        </h4>
                                        <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($event['location']) ?>"
                                            target="_blank" class="btn-map">
                                            <i class="fas fa-map-marked-alt"></i> Voir sur la carte
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <?php
                                $participantsCount = $participationModel->getParticipationCountByEventId($event['id']);
                                $placesRestantes = $event['capacite_max'] - $participantsCount;
                                ?>
                                <p style="margin:0.5rem 0; color:#6b4b44; font-size:0.9rem;">
                                    <i class="fas fa-users" style="color:#5e6d3b; margin-right:0.5rem;"></i> Inscrits:
                                    <?= $participantsCount ?>
                                </p>
                                <p style="margin:0.5rem 0; color:#6b4b44; font-size:0.9rem;">
                                    <i class="fas fa-chair" style="color:#b47b47; margin-right:0.5rem;"></i> Places restantes:
                                    <?= max(0, $placesRestantes) ?> / <?= htmlspecialchars($event['capacite_max']) ?>
                                </p>
                                <div style="display:flex; gap:10px; margin-top:1rem;">
                                    <a href="event/event-detail.php?id=<?= intval($event['id']) ?>"
                                        style="flex:1; display:flex; align-items:center; justify-content:center; padding:10px 16px; background:#5e6d3b; color:white; border-radius:8px; text-decoration:none; font-weight:600; transition:all 0.2s;"
                                        onmouseover="this.style.background='#3a4a2a'"
                                        onmouseout="this.style.background='#5e6d3b'">
                                        <i class="fas fa-arrow-right" style="margin-right:0.5rem;"></i> Détails
                                    </a>
                                    <div style="flex:1;">
                                        <?php if ($placesRestantes > 0): ?>
                                            <button
                                                onclick="openDetailedParticipationModal(<?= intval($event['id']) ?>, <?= $userId !== null ? $userId : 'null' ?>)"
                                                style="width:100%; display:flex; align-items:center; justify-content:center; padding:10px 16px; background:#b47b47; color:white; border-radius:8px; text-decoration:none; font-weight:600; transition:all 0.2s; border:none; cursor:pointer; font-size:inherit; font-family:inherit;"
                                                onmouseover="this.style.background='#8f5e2f'"
                                                onmouseout="this.style.background='#b47b47'">
                                                <i class="fas fa-heart" style="margin-right:0.5rem;"></i> Participer
                                            </button>
                                        <?php else: ?>
                                            <button disabled
                                                style="width:100%; padding:10px 16px; background:#ccc; color:#666; border-radius:8px; border:none; cursor:not-allowed; font-weight:600;">
                                                Complet
                                            </button>
                                        <?php endif; ?>
                                        <div class="success-msg"
                                            style="display: none; color: #27ae60; text-align: center; font-weight: 600; font-size:0.9rem; margin-top:8px;">
                                            ✔ Inscription enregistrée !</div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div
                        style="text-align:center; padding:3rem; background:#fffaf5; border-radius:12px; border: 1px solid rgba(75,46,22,0.1);">
                        <i class="fas fa-calendar" style="font-size:3rem; color:#a9b97d; margin-bottom:1rem;"></i>
                        <p style="font-size:1.1rem; color:#4b2e16; font-weight:600;">Aucun événement n'est actuellement
                            disponible.</p>
                        <p style="color:#6b4b44;">Revenez bientôt pour découvrir nos prochains événements !</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

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
                            <a href="#">Opportunités</a>
                            <a href="events-list.php">Événements</a>
                            <a href="#">Campagnes</a>
                            <a href="#">Ressources</a>
                            <a href="#">Forum</a>
                        </div>
                    </div>
                    <div class="footer-column">
                        <h3>Légal</h3>
                        <div class="footer-links">
                            <a href="#">Mentions légales</a>
                            <a href="#">Politique de confidentialité</a>
                            <a href="#">Conditions d'utilisation</a>
                            <a href="#">Accessibilité</a>
                        </div>
                    </div>
                    <div class="footer-column">
                        <h3>Contact</h3>
                        <div class="footer-links">
                            <a href="mailto:contact@impactable.org">contact@impactable.org</a>
                            <a href="tel:+21629297014">+216 29297014</a>
                            <a href="#">Tunis, Tunisia</a>
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
</body>

</html>