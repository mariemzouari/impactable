<?php $currentYear = date('Y'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier des événements - ImpactAble</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
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
                <a href="calendar.php" class="nav-link active"><i class="fas fa-calendar-alt"></i> <span>Événements</span></a>
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

    <!-- Contenu du Calendrier -->
    <section class="section">
        <h1 style="text-align:center; color:var(--brown); margin:3rem 0;">Calendrier des Événements</h1>

        <div style="max-width:1000px; margin:0 auto;">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px,1fr)); gap:2rem;">
                <!-- Événement 1 -->
                <article class="card">
                    <img class="card-img" src="https://images.unsplash.com/photo-1559027615-cd4628902d4a?auto=format&fit=crop&w=870&q=80" alt="Formation LSF">
                    <div class="card-body">
                        <h3>Formation LSF</h3>
                        <p class="text-muted"><i class="fas fa-calendar-alt"></i> 20 Janvier 2025 · 8 séances</p>
                        <p class="text-muted"><i class="fas fa-map-marker-alt"></i> Tunis</p>
                        <div class="feature-list" style="margin:1rem 0;">
                            <div class="badge">Débutant</div>
                            <div class="badge">Certifiante</div>
                        </div>
                        <a href="event/event-detail.php?id=1" class="btn primary" style="width:100%; text-align:center;">Voir les détails</a>
                    </div>
                </article>

                <!-- Événement 2 -->
                <article class="card">
                    <img class="card-img" src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=870&q=80" alt="Nettoyage plage">
                    <div class="card-body">
                        <h3>Nettoyage de Plage Solidaire</h3>
                        <p class="text-muted"><i class="fas fa-calendar-alt"></i> 5 Janvier 2025 · 4 heures</p>
                        <p class="text-muted"><i class="fas fa-map-marker-alt"></i> La Marsa</p>
                        <div class="feature-list" style="margin:1rem 0;">
                            <div class="badge">Écologie</div>
                            <div class="badge">Famille</div>
                        </div>
                        <a href="event/event-detail.php?id=2" class="btn primary" style="width:100%; text-align:center;">Voir les détails</a>
                    </div>
                </article>

                <!-- Événement 3 -->
                <article class="card">
                    <img class="card-img" src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=870&q=80" alt="Forum innovation">
                    <div class="card-body">
                        <h3>Forum Innovation Sociale</h3>
                        <p class="text-muted"><i class="fas fa-calendar-alt"></i> 25 Janvier 2025 · En ligne</p>
                        <p class="text-muted"><i class="fas fa-laptop"></i> Zoom + Live YouTube</p>
                        <div class="feature-list" style="margin:1rem 0;">
                            <div class="badge">Innovation</div>
                            <div class="badge">Réseautage</div>
                        </div>
                        <a href="event/event-detail.php?id=3" class="btn primary" style="width:100%; text-align:center;">Voir les détails</a>
                    </div>
                </article>
            </div>
        </div>
    </section>

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
                        <a href="calendar.php">Événements</a>
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
</body>
</html>