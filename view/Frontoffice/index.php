
<?php 
// index.php - Correction du chemin
include __DIR__ . '/../../controller/FrontCampagneController.php';
$campagneC = new FrontCampagneController();
$campagnes = $campagneC->listCampagnesActives();

// Prendre seulement 2 campagnes pour l'affichage sur la page d'accueil
$campagnesAccueil = array_slice($campagnes, 0, 2);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ImpactAble — Plateforme Inclusive</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <div class="container">  
    <!-- Header -->
    <header class="site-header" role="banner">
      <div class="brand">
        <button class="nav-toggle" id="navToggle" aria-label="Ouvrir le menu">
          <i class="fas fa-bars"></i>
        </button>
        <div class="logo">
          <img src="assets/images/logo.png" alt="Inclusive Opportunities" class="logo-image">
        </div>
      </div>

      <div class="header-actions">
        <button class="btn ghost" id="loginBtn">Se connecter</button>
        <button class="btn primary" id="signupBtn">S'inscrire</button>
      </div>
    </header>

    <!-- Side Panel Navigation -->
    <div class="side-panel" id="sidePanel">
      <div class="panel-header">
        <div class="logo">
          <img src="assets/images/logo.png" alt="Inclusive Opportunities" class="logo-image">
        </div>
        <button class="panel-close" id="panelClose">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <nav class="panel-nav">
        <div class="nav-section">
          <div class="nav-title">Navigation</div>
          <a href="#home" class="nav-link active">
            <i class="fas fa-home"></i>
            <span>Accueil</span>
          </a>
          <a href="#opportunities" class="nav-link">
            <i class="fas fa-briefcase"></i>
            <span>Opportunités</span>
          </a>
          <a href="#events" class="nav-link">
            <i class="fas fa-calendar-alt"></i>
            <span>Événements</span>
          </a>
          <a href="listCampagnes.php" class="nav-link">
            <i class="fas fa-hand-holding-heart"></i>
            <span>Campagnes</span>
          </a>
          <a href="#resources" class="nav-link">
            <i class="fas fa-book"></i>
            <span>Ressources</span>
          </a>
          <a href="#forum" class="nav-link">
            <i class="fas fa-comments"></i>
            <span>Forum</span>
          </a>
          <a href="#reclamations" class="nav-link">
            <i class="fas fa-comment-alt"></i>
            <span>Réclamations</span>
          </a>
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

    <!-- Hero -->
    <section id="home" class="hero" aria-labelledby="hero-title">
      <div class="hero-content">
        <h1 id="hero-title">Donnez de la force à l'impact — Ensemble</h1>
        <p class="lead">Découvrez des opportunités inclusives, participez à des événements, lancez ou soutenez des campagnes de dons et accédez à des ressources accessibles pour tous.</p>

        <div class="hero-cta">
          <a class="btn primary" href="#opportunities">
            <i class="fas fa-search"></i>
            Voir Opportunités
          </a>
          <a class="btn secondary" href="#events">
            <i class="fas fa-calendar-check"></i>
            Prochains événements
          </a>
          <a class="btn offer" href="listCampagnes.php">
            <i class="fas fa-plus-circle"></i>
            Voir les campagnes
          </a>
        </div>

        <div class="feature-list">
          <div class="badge">
            <i class="fas fa-universal-access"></i>
            Accessible
          </div>
          <div class="badge">
            <i class="fas fa-users"></i>
            Collaboratif
          </div>
          <div class="badge">
            <i class="fas fa-database"></i>
            Open data
          </div>
        </div>
      </div>

      <div>
        <img src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=774&q=80" alt="Équipe diverse travaillant ensemble" class="hero-img" />
      </div>
    </section>

    <!-- Features -->
    <section class="features">
      <article class="feature">
        <div class="feature-icon">
          <i class="fas fa-briefcase"></i>
        </div>
        <h3>Opportunités</h3>
        <p>Recherchez des missions, stages, emplois ou volontariats adaptés et filtrables par accessibilité.</p>
      </article>
      <article class="feature">
        <div class="feature-icon">
          <i class="fas fa-calendar-alt"></i>
        </div>
        <h3>Événements</h3>
        <p>Inscrivez-vous à des événements locaux et en ligne, avec des options d'accessibilité indiquées clairement.</p>
      </article>
      <article class="feature">
        <div class="feature-icon">
          <i class="fas fa-hand-holding-heart"></i>
        </div>
        <h3>Campagnes de dons</h3>
        <p>Lancez ou contribuez à des campagnes — suivez la progression et les rapports d'impact.</p>
      </article>
    </section>

    <!-- Donations Section Dynamique -->
    <section id="donations" class="section">
      <div class="section-header">
        <h2>Campagnes de dons</h2>
        <a href="listCampagnes.php" class="section-link">
          Voir toutes
          <i class="fas fa-arrow-right"></i>
        </a>
      </div>
      <div class="cards-grid">
        <?php if(!empty($campagnesAccueil)): ?>
          <?php foreach($campagnesAccueil as $campagne): 
            $progression = $campagneC->getProgression($campagne['Id_campagne']);
            $jours_restants = max(0, floor((strtotime($campagne['date_fin']) - time()) / (60 * 60 * 24)));
          ?>
          <article class="card">
            <div class="card-body">
              <h3><?= htmlspecialchars($campagne['titre']) ?></h3>
              <p class="text-muted">
                Cible: <?= number_format($campagne['objectif_montant'], 0) ?> TND · 
                Collecté: <?= number_format($campagne['montant_actuel'], 0) ?> TND
              </p>
              <div class="progress" aria-hidden="true">
                <div class="progress-bar" style="width:<?= min($progression, 100) ?>%"></div>
              </div>
              <p class="small text-muted mt-8"><?= number_format($progression, 1) ?>% collectés · <?= $jours_restants ?> jours restants</p>
              <div class="card-actions mt-16">
                <a class="btn primary" href="DonView.php?id_campagne=<?= $campagne['Id_campagne'] ?>">
                  Faire un don
                </a>
                <a class="btn ghost" href="listCampagnes.php">
                  Partager
                </a>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        <?php else: ?>
          <article class="card">
            <div class="card-body">
              <h3>Aucune campagne active</h3>
              <p class="text-muted">Revenez plus tard pour découvrir de nouvelles campagnes.</p>
              <div class="card-actions mt-16">
                <a class="btn primary" href="listCampagnes.php">
                  Voir toutes les campagnes
                </a>
              </div>
            </div>
          </article>
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
              <a href="#home">Accueil</a>
              <a href="#opportunities">Opportunités</a>
              <a href="#events">Événements</a>
              <a href="listCampagnes.php">Campagnes</a>
              <a href="#resources">Ressources</a>
              <a href="#forum">Forum</a>
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
          <p>© <span id="year"></span> ImpactAble — Tous droits réservés.</p>
        </div>
      </div>
    </footer>
  </div>

  <script src="assets/js/script.js"></script>
</body>
</html>
