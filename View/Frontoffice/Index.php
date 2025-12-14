<?php
if (session_status() === PHP_SESSION_NONE)
  session_start();
require_once __DIR__ . "/../../Config.php";
require_once __DIR__ . "/../../Model/Database.php";
require_once __DIR__ . "/../../Model/EventModel.php";
require_once __DIR__ . "/../../Model/ParticipationModel.php";
require_once __DIR__ . "/../../Model/utils.php";

$Config = new Config();
$db = $Config->getPDO();
$eventModel = new EventModel($db);
$participationModel = new ParticipationModel($db);

// Récupérer tous les événements et prendre les 3 premiers comme "vedette"
$allEvents = $eventModel->getAll();
$featuredEvents = array_slice($allEvents, 0, 3);

// Récupérer les campagnes pour l'accueil
require_once __DIR__ . '/../../Model/FrontCampagneController.php';
$frontController = new FrontCampagneController();
$campagnesAccueil = $frontController->listCampagnesActives();
$campagnesAccueil = array_slice($campagnesAccueil, 0, 3);
// Alias pour $campagneC utilisé dans la vue
$campagneC = $frontController;

$currentYear = date('Y');
$userId = $_SESSION['user_id'] ?? null;

// If an action targets the offers/candidatures features, dispatch to controllers
$action = $_GET['action'] ?? null;
if ($action) {
  require_once __DIR__ . '/../../Model/Database.php';
  require_once __DIR__ . '/../../Model/utils.php';
  require_once __DIR__ . '/../../Model/Offre.php';
  require_once __DIR__ . '/../../Model/Candidature.php';
  require_once __DIR__ . '/../../Model/UtilisateurClass.php';
  require_once __DIR__ . '/../../Controller/OffreController.php';
  require_once __DIR__ . '/../../Controller/CandidatureController.php';
  require_once __DIR__ . '/../../Controller/AuthController.php';
  require_once __DIR__ . '/../../Controller/ChatbotController.php';
  // favorites controller is an API endpoint; do not include here
  require_once __DIR__ . '/../../Controller/AdminController.php';

  $offreController = new OffreController();
  $candidatureController = new CandidatureController();
  $authController = new AuthController();
  $chatbotController = new ChatbotController();
  // ParticipationController is an API endpoint; don't include/instantiate it here.
  // no instantiation: it's an API endpoint
  $adminController = new AdminController();

  // Add FrontCampagneController for the homepage
  require_once __DIR__ . '/../../Model/FrontCampagneController.php';
  $frontController = new FrontCampagneController();

  switch ($action) {
    case 'offres':
      $offreController->liste();
      exit;
    case 'details-offre':
      $offreController->details();
      exit;
    case 'poster-offre':
      $offreController->poster();
      exit;
    case 'mes-offres':
      $offreController->mesOffres();
      exit;
    case 'modifier-offre':
      $offreController->modifier();
      exit;
    case 'supprimer-offre':
      $offreController->supprimer();
      exit;
    case 'gestion-offre':
      $offreController->gestion();
      exit;
    case 'postuler':
      $candidatureController->postuler();
      exit;
    case 'mes-candidatures':
      $candidatureController->mesCandidatures();
      exit;
    case 'connexion':
      $authController->connexion();
      exit;
    case 'deconnexion':
      $authController->deconnexion();
      exit;
    case 'chatbot':
      $chatbotController->processMessage();
      exit;
    case 'admin-dashboard':
      $adminController->dashboard();
      exit;
    case 'create':
      $offreController->poster();
      exit;
    default:
      // other actions continue to homepage
      break;
  }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ImpactAble - Plateforme Inclusive</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="../../assets/css/style rayen.css">
  <link rel="stylesheet" href="../../View/Frontoffice/assets/css/style.css">
  <style>
    /* Styles pour la modale */
    .modal-backdrop {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .modal-backdrop.active {
      opacity: 1;
      visibility: visible;
    }

    .modal-content {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      width: 90%;
      max-width: 500px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      transform: translateY(-20px);
      transition: transform 0.3s ease;
    }

    .modal-backdrop.active .modal-content {
      transform: translateY(0);
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #eee;
      padding-bottom: 15px;
      margin-bottom: 20px;
    }

    .modal-header h3 {
      margin: 0;
      color: var(--brown);
      font-size: 1.5rem;
    }

    .modal-close {
      background: none;
      border: none;
      font-size: 1.8rem;
      color: var(--muted);
      cursor: pointer;
      padding: 0;
    }

    .modal-close:hover {
      color: var(--brown);
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: var(--brown);
    }

    .form-input {
      width: 100%;
      padding: 12px;
      border: 1px solid rgba(75, 46, 22, 0.2);
      border-radius: 8px;
      font-family: inherit;
      font-size: 1rem;
      color: var(--brown);
      box-sizing: border-box;
    }

    .form-input:focus {
      outline: none;
      border-color: var(--moss);
      box-shadow: 0 0 0 3px rgba(94, 109, 59, 0.1);
    }

    .form-footer {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 25px;
      border-top: 1px solid #eee;
      padding-top: 20px;
    }

    .btn-modal-secondary {
      background: #f0f0f0;
      color: #333;
      padding: 10px 20px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      font-weight: 600;
      transition: background 0.2s;
    }

    .btn-modal-secondary:hover {
      background: #e0e0e0;
    }

    .btn-modal-primary {
      background: var(--moss);
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      font-weight: 600;
      transition: background 0.2s;
    }

    .btn-modal-primary:hover {
      background: #4a5830;
    }

    .global-status-message {
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #f8d7da;
      color: #721c24;
      padding: 10px 20px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      z-index: 1001;
      display: none;
      font-weight: 600;
    }

    .global-status-message.success {
      background-color: #d4edda;
      color: #155724;
    }

    /* Logo link styling */
    a.logo {
      display: flex;
      align-items: center;
      text-decoration: none;
      cursor: pointer;
      transition: opacity 0.3s ease;
    }

    a.logo:hover {
      opacity: 0.8;
    }

    a.logo .logo-image {
      transition: transform 0.3s ease;
    }

    a.logo:hover .logo-image {
      transform: scale(1.05);
    }
  </style>
</head>

<body>

  <div class="container">
    <!-- IMPROVED: Header with better navigation toggle placement -->
    <header class="site-header" role="banner">
      <div class="brand">
        <button class="nav-toggle" id="navToggle" aria-label="Ouvrir le menu">
          <i class="fas fa-bars"></i>
        </button>
        <a href="<?php echo Config::getBaseUrl(); ?>/View/Frontoffice/index.php" class="logo">
          <img src="../../assets/images/logo.png" alt="Inclusive Opportunities" class="logo-image">
        </a>
      </div>

      <div class="header-actions">
        <?php if (isset($_SESSION['user_id']) && Utils::isAuthenticated()): ?>
          <span class="user-welcome">Bonjour, <?= htmlspecialchars($_SESSION['user_prenom'] ?? ''); ?></span>
          <a href="index.php?action=mes-candidatures" class="btn ghost">
            <i class="fas fa-briefcase"></i>
            Mes candidatures
          </a>
          <a href="index.php?action=mes-offres" class="btn secondary">
            <i class="fas fa-list"></i>
            Mes offres
          </a>
          <button class="btn ghost" onclick="window.location.href='index.php?action=create'">
            <i class="fas fa-plus"></i> Créer un Post
          </button>
          <a href="index.php?action=deconnexion" class="btn secondary">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
          </a>
          <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <a class="btn ghost" href="index.php?action=admin-dashboard"><i class="fas fa-cog"></i> Admin</a>
          <?php endif; ?>
        <?php else: ?>
          <a href="index.php?action=connexion" class="btn ghost" id="loginBtn">Se connecter</a>
          <a href="index.php?action=inscription" class="btn primary" id="signupBtn">S'inscrire</a>
        <?php endif; ?>
      </div>
    </header>

    <!-- Side Panel Navigation -->
    <div class="side-panel" id="sidePanel">
      <div class="panel-header">
        <a href="<?php echo Config::getBaseUrl(); ?>/View/Frontoffice/index.php" class="logo">
          <img src="../../assets/images/logo.png" alt="Inclusive Opportunities" class="logo-image">
        </a>
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
        <h1 id="hero-title">Événements à Impact Positif</h1>
        <p class="lead">Découvrez des événements qui créent un impact social positif : formations inclusives,
          conférences engagées, actions de solidarité et bien plus encore.</p>

        <div class="hero-cta">
          <?php if ($userId): ?>
            <a class="btn primary" href="events-list.php">
              <i class="fas fa-calendar-check"></i>
              Voir le calendrier
            </a>
            <a class="btn secondary" href="my-participations.php">
              <i class="fas fa-heart"></i>
              Mes participations
            </a>
          <?php else: ?>
            <a class="btn primary" href="index.php?action=connexion">
              <i class="fas fa-sign-in-alt"></i>
              Se connecter pour découvrir
            </a>
          <?php endif; ?>
        </div>

        <div class="feature-list">
          <div class="badge">
            <i class="fas fa-graduation-cap"></i>
            Formations
          </div>
          <div class="badge">
            <i class="fas fa-hands-helping"></i>
            Solidarité
          </div>
          <div class="badge">
            <i class="fas fa-chalkboard-teacher"></i>
            Conférences
          </div>
          <div class="badge">
            <i class="fas fa-seedling"></i>
            Environnement
          </div>
        </div>
      </div>

      <div>
        <img
          src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=870&q=80"
          alt="Public participant à un événement solidaire" class="hero-img" />
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
        <p>Inscrivez-vous à des événements locaux et en ligne, avec des options d'accessibilité indiquées clairement.
        </p>
      </article>
      <article class="feature">
        <div class="feature-icon">
          <i class="fas fa-hand-holding-heart"></i>
        </div>
        <h3>Campagnes de dons</h3>
        <p>Lancez ou contribuez à des campagnes — suivez la progression et les rapports d'impact.</p>
      </article>
    </section>

    <!-- Opportunités: embedded list -->
    <?php
    if ($userId): // Only show offers if logged in
      // Prepare offers data to embed the list template
      require_once __DIR__ . '/../../Model/Offre.php';
      require_once __DIR__ . '/../../Model/Candidature.php';
      require_once __DIR__ . '/../../Model/UtilisateurClass.php';

      $offreManager = new Offre();
      $candidatureManager = new Candidature();

      $filters = [];
      if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!empty($_GET['type_offre']))
          $filters['type_offre'] = $_GET['type_offre'];
        if (!empty($_GET['mode']))
          $filters['mode'] = $_GET['mode'];
        if (!empty($_GET['horaire']))
          $filters['horaire'] = $_GET['horaire'];
        if (isset($_GET['disability_friendly']))
          $filters['disability_friendly'] = $_GET['disability_friendly'];
        if (!empty($_GET['type_handicap']))
          $filters['type_handicap'] = $_GET['type_handicap'];
      }

      $offres = $offreManager->getAll($filters, 6);
      $candidaturesPlacees = $candidatureManager->getCandidaturesPlacees();

      // Use EMBEDDED mode to avoid duplicating header/footer
      if (!defined('EMBEDDED'))
        define('EMBEDDED', true);
      require_once __DIR__ . '/offre/liste.php';
    else:
      ?>
      <section class="section" style="text-align: center; padding: 4rem 2rem; background: #f9f9f9;">
        <i class="fas fa-briefcase" style="font-size: 3rem; color: var(--sage); margin-bottom: 1rem;"></i>
        <h2>Opportunités Professionnelles</h2>
        <p style="max-width: 600px; margin: 0 auto 1.5rem;">Connectez-vous pour accéder à nos offres d'emploi, stages et
          formations exclusives.</p>
        <a href="index.php?action=connexion" class="btn primary">Voir les opportunités</a>
      </section>
    <?php endif; ?>

    <!-- Events Section - Enhanced from second file -->
    <section id="events" class="section">
      <div class="section-header">
        <h2>Événements à venir</h2>
        <div style="display: flex; gap: 10px; align-items: center;">
          <?php if ($userId): ?>
            <a href="events-list.php" class="btn ghost">
              <i class="fas fa-calendar-check"></i> Voir le calendrier
            </a>
            <a href="my-participations.php" class="btn ghost">
              <i class="fas fa-heart"></i> Mes participations
            </a>
          <?php endif; ?>
        </div>
      </div>
      <div class="timeline">
        <?php
        // Static event data (for the timeline section)
        $staticEvents = [
          ['id' => 1, 'titre' => 'Atelier Accessibilité Numérique', 'date' => '2024-12-15 14:00:00', 'badges' => [['icon' => 'fas fa-video', 'text' => 'Hybride'], ['icon' => 'fas fa-sign-language', 'text' => 'LSF'], ['icon' => 'fas fa-graduation-cap', 'text' => 'Formation']], 'description' => 'Formation pratique sur l\'accessibilité web avec exercices concrets. Développez des compétences pour créer un web plus inclusif.'],
          ['id' => 2, 'titre' => 'Collecte de Vêtements d\'Hiver', 'date' => '2024-12-18 10:00:00', 'badges' => [['icon' => 'fas fa-building', 'text' => 'Présentiel'], ['icon' => 'fas fa-hands-helping', 'text' => 'Solidarité'], ['icon' => 'fas fa-wheelchair', 'text' => 'Accessible']], 'description' => 'Action solidaire pour collecter des vêtements chauds destinés aux personnes sans-abri. Venez avec vos dons !'],
          ['id' => 3, 'titre' => 'Conférence Inclusion & Innovation Sociale', 'date' => '2025-01-10 09:00:00', 'badges' => [['icon' => 'fas fa-laptop-house', 'text' => 'En ligne'], ['icon' => 'fas fa-chalkboard-teacher', 'text' => 'Conférence'], ['icon' => 'fas fa-closed-captioning', 'text' => 'Sous-titres']], 'description' => 'Découvrez comment la technologie peut servir l\'inclusion sociale. Témoignages et solutions concrètes.'],
        ];

        foreach ($staticEvents as $sEvent):
          $isStaticEventParticipating = false;
          $staticParticipationCount = 0;

          if ($userId) {
            $staticParticipationDetails = $participationModel->findOneBy([
              'id_utilisateur' => $userId,
              'id_evenement' => $sEvent['id'],
              'statut' => ['inscrit', 'confirmé']
            ]);
            if ($staticParticipationDetails) {
              $isStaticEventParticipating = true;
            }
          }
          ?>
          <div class="event-item">
            <time
              datetime="<?= date('Y-m-d', strtotime($sEvent['date'])) ?>"><?= date('d M Y', strtotime($sEvent['date'])) ?><br><span
                style="font-size: 0.9rem; color: var(--muted);"><?= date('H\hi', strtotime($sEvent['date'])) ?></span></time>
            <div class="event-content">
              <h4><?= htmlspecialchars($sEvent['titre']) ?></h4>
              <p><?= htmlspecialchars($sEvent['description']) ?></p>
              <div class="feature-list" style="margin: 0.5rem 0;">
                <?php foreach ($sEvent['badges'] as $badge): ?>
                  <div class="badge">
                    <i class="<?= htmlspecialchars($badge['icon']) ?>"></i> <?= htmlspecialchars($badge['text']) ?>
                  </div>
                <?php endforeach; ?>
              </div>
              <div style="margin-top: 1rem;">
                <?php if ($userId): ?>
                  <?php if ($isStaticEventParticipating): ?>
                    <button class="btn btn-participer" disabled style="background: #27ae60; cursor: default;">
                      <i class="fas fa-check"></i> Inscrit
                    </button>
                    <span class="text-muted" style="margin: 0 1rem;">•</span>
                    <span class="text-muted participants-count">Vous participez</span>
                  <?php else: ?>
                    <button class="btn btn-participer"
                      onclick="openDetailedParticipationModal(<?= $sEvent['id'] ?>, <?= json_encode($userId) ?>)">Participer</button>
                    <span class="text-muted" style="margin: 0 1rem;">•</span>
                    <span class="text-muted participants-count"><?= $staticParticipationCount ?> participants inscrits</span>
                  <?php endif; ?>
                <?php else: ?>
                  <button class="btn btn-participer primary" onclick="window.location.href='index.php?action=connexion'"
                    title="Connectez-vous pour participer">
                    <i class="fas fa-user"></i> Se connecter
                  </button>
                  <span class="text-muted" style="margin: 0 1rem;">•</span>
                  <span class="text-muted participants-count">0 participants inscrits</span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Catégories d'Événements -->
    <section class="section">
      <div class="section-header">
        <h2>Catégories d'Événements</h2>
      </div>
      <div class="features">
        <article class="feature">
          <div class="feature-icon">
            <i class="fas fa-graduation-cap"></i>
          </div>
          <h3>Formations & Ateliers</h3>
          <p>Développez vos compétences pour un impact social positif grâce à nos formations accessibles.</p>
        </article>
        <article class="feature">
          <div class="feature-icon">
            <i class="fas fa-hands-helping"></i>
          </div>
          <h3>Actions Solidaires</h3>
          <p>Participez à des collectes, maraudes et autres actions de solidarité en faveur des plus démunis.</p>
        </article>
        <article class="feature">
          <div class="feature-icon">
            <i class="fas fa-chalkboard-teacher"></i>
          </div>
          <h3>Conférences & Débats</h3>
          <p>Échangez autour des enjeux sociaux et découvrez des initiatives qui changent le monde.</p>
        </article>
        <article class="feature">
          <div class="feature-icon">
            <i class="fas fa-seedling"></i>
          </div>
          <h3>Événements Écologiques</h3>
          <p>Rejoignez des actions de sensibilisation et de protection de l'environnement.</p>
        </article>
        <article class="feature">
          <div class="feature-icon">
            <i class="fas fa-heartbeat"></i>
          </div>
          <h3>Santé & Bien-être</h3>
          <p>Prenez part à des initiatives pour la santé physique et mentale, l'accès aux soins et le bien-être de tous.
          </p>
        </article>
        <article class="feature">
          <div class="feature-icon">
            <i class="fas fa-briefcase"></i>
          </div>
          <h3>Métier & Carrière</h3>
          <p>Découvrez des métiers, développez vos compétences professionnelles et explorez de nouvelles opportunités de
            carrière.</p>
        </article>
        <article class="feature">
          <div class="feature-icon">
            <i class="fas fa-palette"></i>
          </div>
          <h3>Art & Culture</h3>
          <p>Explorez la créativité à travers des expositions, des concerts et des ateliers artistiques.</p>
        </article>
        <article class="feature">
          <div class="feature-icon">
            <i class="fas fa-running"></i>
          </div>
          <h3>Sport & Bien-être</h3>
          <p>Participez à des événements sportifs, des cours de yoga ou des randonnées pour le bien-être de tous.</p>
        </article>
      </div>
    </section>

    <!-- Événements en Vedette -->
    <section class="section">
      <div class="section-header">
        <h2>Événements en Vedette</h2>
      </div>
      <div class="cards-grid">
        <?php if (!empty($featuredEvents)): ?>
          <?php foreach ($featuredEvents as $event):
            $isFeaturedEventParticipating = false;
            if ($userId) {
              $featuredParticipationDetails = $participationModel->findOneBy([
                'id_utilisateur' => $userId,
                'id_evenement' => $event['id'],
                'statut' => ['inscrit', 'confirmé']
              ]);
              if ($featuredParticipationDetails) {
                $isFeaturedEventParticipating = true;
              }
            }
            ?>
            <article class="card">
              <img class="card-img"
                src="<?= htmlspecialchars($event['image'] ?? 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=870&q=80') ?>"
                alt="Image pour <?= htmlspecialchars($event['titre']) ?>">
              <div class="card-body">
                <h3><?= htmlspecialchars($event['titre']) ?></h3>
                <p class="text-muted"><?= date('d M Y', strtotime($event['date_debut'])) ?> ·
                  <?= htmlspecialchars($event['adresse'] ?? 'Lieu à définir') ?>
                </p>
                <p class="card-excerpt"><?= htmlspecialchars(substr($event['description'], 0, 100)) ?>...</p>
                <div class="card-actions">
                  <?php if ($userId): ?>
                    <a class="btn ghost" href="event/event-detail.php?id=<?= intval($event['id']) ?>">Détails</a>
                  <?php else: ?>
                    <span class="btn ghost disabled" style="opacity: 0.5; cursor: not-allowed;">Détails</span>
                  <?php endif; ?>

                  <?php if ($userId): ?>
                    <?php if ($isFeaturedEventParticipating): ?>
                      <button class="btn primary" disabled style="background: #27ae60; cursor: default;">
                        <i class="fas fa-check"></i> Inscrit
                      </button>
                    <?php else: ?>
                      <button class="btn primary"
                        onclick="openDetailedParticipationModal(<?= $event['id'] ?>, <?= json_encode($userId) ?>)">Participer</button>
                    <?php endif; ?>
                  <?php else: ?>
                    <button class="btn primary" onclick="window.location.href='index.php?action=connexion'"
                      title="Connectez-vous pour participer">
                      <i class="fas fa-user"></i> Se connecter
                    </button>
                  <?php endif; ?>
                </div>
                <div class="success-msg" style="display: none; text-align: center; font-size: 0.9em; margin-top: 5px;">
                </div>
                <div class="error-msg"
                  style="display: none; color: red; text-align: center; font-size: 0.9em; margin-top: 5px;"></div>
              </div>
              <div class="card-footer">
                <span
                  class="text-muted small"><?= isset($event['capacite']) ? htmlspecialchars($event['capacite']) . ' places disponibles' : 'Places disponibles' ?></span>
                <span class="badge"><?= htmlspecialchars($event['categorie']) ?></span>
              </div>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="text-align: center; width: 100%; color: var(--muted);">Aucun événement à afficher pour le moment.</p>
        <?php endif; ?>
      </div>
    </section>

    <!-- Donations -->
    <section id="donations" class="section">
      <div class="section-header">
        <h2>Campagnes de dons</h2>
        <?php if ($userId): ?>
          <a href="listCampagnes.php" class="section-link">
            Voir toutes
            <i class="fas fa-arrow-right"></i>
          </a>
        <?php endif; ?>
      </div>
      <div class="cards-grid">
        <?php if (!empty($campagnesAccueil)): ?>
          <?php foreach ($campagnesAccueil as $campagne):
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
                <p class="small text-muted mt-8"><?= number_format($progression, 1) ?>% collectés · <?= $jours_restants ?>
                  jours restants</p>
                <div class="card-actions mt-16">
                  <?php if ($userId): ?>
                    <a class="btn primary" href="DonView.php?id_campagne=<?= $campagne['Id_campagne'] ?>">
                      Faire un don
                    </a>
                    <a class="btn ghost" href="listCampagnes.php">
                      Partager
                    </a>
                  <?php else: ?>
                    <a class="btn primary" href="index.php?action=connexion">
                      Se connecter
                    </a>
                  <?php endif; ?>
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

    <!-- Resources -->
    <section id="resources" class="section">
      <div class="section-header">
        <h2>Ressources inclusives</h2>
        <a href="#" class="section-link">
          Explorer
          <i class="fas fa-arrow-right"></i>
        </a>
      </div>
      <ul class="resource-list">
        <li>
          <a href="#">
            <div class="resource-icon">
              <i class="fas fa-book"></i>
            </div>
            <span>Guide accessible pour organiser un évènement</span>
          </a>
        </li>
        <li>
          <a href="#">
            <div class="resource-icon">
              <i class="fas fa-file-alt"></i>
            </div>
            <span>Templates & documents inclusifs</span>
          </a>
        </li>
        <li>
          <a href="#">
            <div class="resource-icon">
              <i class="fas fa-question-circle"></i>
            </div>
            <span>FAQ & aides</span>
          </a>
        </li>
        <li>
          <a href="#">
            <div class="resource-icon">
              <i class="fas fa-balance-scale"></i>
            </div>
            <span>Ressources légales pour l'inclusion</span>
          </a>
        </li>
      </ul>
    </section>

    <!-- Impact et Statistiques -->
    <section class="section">
      <div class="section-header">
        <h2>Notre Impact Collectif</h2>
      </div>
      <div class="stats">
        <div class="stat-card">
          <div class="stat-number"><?php echo $eventModel->countAll(); ?>+</div>
          <div class="stat-label">Événements organisés</div>
        </div>
        <div class="stat-card">
          <div class="stat-number"><?php echo $eventModel->countParticipations(); ?>+</div>
          <div class="stat-label">Participants engagés</div>
        </div>
        <div class="stat-card">
          <div class="stat-number">100%</div>
          <div class="stat-label">Événements accessibles</div>
        </div>
        <div class="stat-card">
          <div class="stat-number">50+</div>
          <div class="stat-label">Associations partenaires</div>
        </div>
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
          <p>© <?php echo $currentYear; ?> ImpactAble — Tous droits réservés.</p>
        </div>
      </div>
    </footer>
  </div> <!-- /.container -->

  <!-- Modal de participation détaillée -->
  <div id="detailedParticipationModal" class="modal-backdrop">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Inscription à l'événement</h3>
        <button class="modal-close" type="button" onclick="closeDetailedParticipationModal()"><i
            class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <form id="detailedParticipationForm">
          <input type="hidden" name="id_evenement" id="modalEventId">
          <input type="hidden" name="id_utilisateur" id="modalUserId">

          <?php if (!$userId): ?>
            <div class="form-group">
              <label for="modalPrenom">Prénom</label>
              <input type="text" id="modalPrenom" name="prenom" class="form-input" required>
            </div>
            <div class="form-group">
              <label for="modalNom">Nom</label>
              <input type="text" id="modalNom" name="nom" class="form-input" required>
            </div>
            <div class="form-group">
              <label for="modalEmail">Email</label>
              <input type="email" id="modalEmail" name="email" class="form-input" required>
            </div>
            <div class="form-group">
              <label for="modalNumTel">Numéro de téléphone</label>
              <input type="text" id="modalNumTel" name="num_tel" class="form-input" placeholder="Ex: +216 12 345 678"
                required>
            </div>
            <div class="form-group">
              <label for="modalNumIdentite">Numéro d'identité (CIN/Passeport)</label>
              <input type="text" id="modalNumIdentite" name="num_identite" class="form-input" placeholder="Ex: 08123456"
                required>
            </div>
          <?php else: ?>
            <div class="form-group">
              <label for="modalPrenom">Prénom</label>
              <input type="text" id="modalPrenom" name="prenom" class="form-input" value="" readonly>
            </div>
            <div class="form-group">
              <label for="modalNom">Nom</label>
              <input type="text" id="modalNom" name="nom" class="form-input" value="" readonly>
            </div>
            <div class="form-group">
              <label for="modalEmail">Email</label>
              <input type="email" id="modalEmail" name="email" class="form-input" value="" readonly>
            </div>
            <div class="form-group">
              <label for="modalNumTel">Numéro de téléphone</label>
              <input type="text" id="modalNumTel" name="num_tel" class="form-input" value="" required>
            </div>
            <div class="form-group">
              <label for="modalNumIdentite">Numéro d'identité (CIN/Passeport)</label>
              <input type="text" id="modalNumIdentite" name="num_identite" class="form-input" value="" required>
            </div>
          <?php endif; ?>

          <div class="form-group">
            <label for="modalAccompagnants">Nombre d'accompagnants</label>
            <input type="number" id="modalAccompagnants" name="nombre_accompagnants" class="form-input" value="0"
              min="0">
          </div>
          <div class="form-group">
            <label for="modalBesoins">Besoins d'accessibilité (facultatif)</label>
            <textarea id="modalBesoins" name="besoins_accessibilite" class="form-input" rows="3"
              placeholder="Ex: Rampe d'accès, interprète LSF..."></textarea>
          </div>
          <div class="form-group">
            <label for="modalMessage">Message/Notes (facultatif)</label>
            <textarea id="modalMessage" name="message" class="form-input" rows="3"
              placeholder="Toute autre information utile..."></textarea>
          </div>
          <div class="form-footer">
            <button type="button" class="btn-modal-secondary"
              onclick="closeDetailedParticipationModal()">Annuler</button>
            <button type="submit" class="btn-modal-primary">Confirmer l'inscription</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="global-status-message" id="globalStatusMessage"></div>

  <script src="../../assets/js/script.js"></script>
  <script src="../../assets/js/participant.js"></script>
  <script>
    // Update year in footer
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>

  <!-- Include Chatbot Component -->
  <?php if (Config::isChatbotEnabled()): ?>
    <?php require_once __DIR__ . '/../components/chatbot.php'; ?>
  <?php endif; ?>
</body>

</html>