<?php
require_once "../../config/Config.php";
require_once "../../Model/EventModel.php";

$Config = new Config();
$db = $Config->getPDO();
$eventModel = new EventModel($db);
$events = $eventModel->getAll();

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
    <link rel="stylesheet" href="../../assets/css/style rayen.css">
</head>
<body>
<div class="container">
    <!-- Header -->
    <header class="site-header" role="banner">
        <div class="brand">
            <button class="nav-toggle" id="navToggle" aria-label="Ouvrir le menu"><i class="fas fa-bars"></i></button>
            <div class="logo">
                <img src="../../assets/images/logo.png" alt="ImpactAble" class="logo-image">
            </div>
        </div>
        <div class="header-actions">
            <button class="btn ghost">Se connecter</button>
            <button class="btn primary">S'inscrire</button>
        </div>
    </header>

    <!-- Menu latéral -->
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
                <a href="events-list.php" class="nav-link active"><i class="fas fa-calendar-alt"></i> <span>Événements</span></a>
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
                    <h4>Visiteur</h4>
                    <p>Connectez-vous pour plus de fonctionnalités</p>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-overlay" id="panelOverlay"></div>

    <!-- Contenu principal -->
    <section class="section">
        <div style="max-width:1200px; margin:0 auto; padding:3rem 0;">
            <h1 style="text-align:center; margin-bottom:2rem; color: #4b2e16;">
                <i class="fas fa-calendar-alt"></i> Tous nos événements
            </h1>

            <?php if(!empty($events)): ?>
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:2rem;">
                    <?php foreach($events as $event): ?>
                        <div style="background:#fffaf5; border-radius:12px; padding:1.5rem; box-shadow:0 4px 12px rgba(75,46,22,0.08); transition:transform 0.2s, box-shadow 0.2s; border-left: 4px solid #5e6d3b;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 6px 16px rgba(75,46,22,0.12)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(75,46,22,0.08)'">
                            <h3 style="margin:0 0 0.5rem 0; color:#4b2e16; font-weight:600;">
                                <?= htmlspecialchars($event['titre']) ?>
                            </h3>
                            <p style="margin:0.5rem 0; color:#6b4b44; font-size:0.9rem;">
                                <i class="fas fa-calendar" style="color:#5e6d3b; margin-right:0.5rem;"></i> 
                                <?= date('d M Y H:i', strtotime($event['date_event'])) ?>
                            </p>
                            <p style="margin:0.5rem 0; color:#6b4b44; font-size:0.9rem;">
                                <i class="fas fa-tag" style="color:#b47b47; margin-right:0.5rem;"></i> 
                                <span style="background:rgba(180,123,71,0.1); padding:3px 8px; border-radius:4px; color:#b47b47;">
                                    <?= htmlspecialchars($event['categorie']) ?>
                                </span>
                            </p>
                            <p style="margin:1rem 0; color:#4b2e16; font-size:0.95rem; line-height:1.4;">
                                <?= htmlspecialchars(substr($event['description'], 0, 100)) ?>...
                            </p>
                            <div style="display:flex; gap:10px; margin-top:1rem;">
                                <a href="event/event-detail.php?id=<?= intval($event['id']) ?>" style="flex:1; display:flex; align-items:center; justify-content:center; padding:10px 16px; background:#5e6d3b; color:white; border-radius:8px; text-decoration:none; font-weight:600; transition:all 0.2s;" onmouseover="this.style.background='#3a4a2a'" onmouseout="this.style.background='#5e6d3b'">
                                    <i class="fas fa-arrow-right" style="margin-right:0.5rem;"></i> Détails
                                </a>
                                <a href="my-participations.php?view_event=<?= intval($event['id']) ?>" style="flex:1; display:flex; align-items:center; justify-content:center; padding:10px 16px; background:#b47b47; color:white; border-radius:8px; text-decoration:none; font-weight:600; transition:all 0.2s;" onmouseover="this.style.background='#8f5e2f'" onmouseout="this.style.background='#b47b47'">
                                    <i class="fas fa-users" style="margin-right:0.5rem;"></i> Participants
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align:center; padding:3rem; background:#fffaf5; border-radius:12px; border: 1px solid rgba(75,46,22,0.1);">
                    <i class="fas fa-calendar" style="font-size:3rem; color:#a9b97d; margin-bottom:1rem;"></i>
                    <p style="font-size:1.1rem; color:#4b2e16; font-weight:600;">Aucun événement n'est actuellement disponible.</p>
                    <p style="color:#6b4b44;">Revenez bientôt pour découvrir nos prochains événements !</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
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
                        <a href="tel:+21612345678">+216 12 345 678</a>
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
</body>
</html>
