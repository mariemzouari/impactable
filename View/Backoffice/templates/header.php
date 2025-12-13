<?php
// Vérifier si l'utilisateur est admin
if (!isset($user) || $user['role'] !== 'admin') {
    header('Location: index.php?action=connexion');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ImpactAble — Administration</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/offre2/views/backoffice/templates/assetsback/css/style2.css">
</head>
<body>
  <div class="admin-container">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <div class="sidebar-header">
        <div class="admin-logo">
          <img src="/offre2/views/backoffice/templates/assetsback/images/logo.png" alt="ImpactAble" class="admin-logo-image">
        </div>
      </div>
      
      <nav class="sidebar-nav">
        <div class="nav-section">
          <div class="nav-title">Principal</div>
          <a href="#admin" class="sidebar-link ">
            <i class="fas fa-tachometer-alt"></i>
            <span>Tableau de bord</span>
          </a>
        </div>
        
        
        <div class="nav-section">
          <div class="nav-title">Gestion de contenu</div>
          <a href="#user" class="sidebar-link ">
            <i class="fas fa-users"></i>
            <span>Utilisateurs</span>
          </a>
          <a href="index.php?action=admin-dashboard" class="sidebar-link <?= (isset($_GET['action']) && $_GET['action'] == 'admin-dashboard') ? 'active' : '' ?>">
            <i class="fas fa-briefcase"></i>
            <span>opportunities</span>
          </a>
                    <a href="#events" class="sidebar-link">
            <i class="fas fa-calendar-alt"></i>
            <span>Événements</span>
          </a>
                    <a href="#campaigns" class="sidebar-link">
            <i class="fas fa-hand-holding-heart"></i>
            <span>Campagnes</span>
          </a>
                    <a href="#resources" class="sidebar-link">
            <i class="fas fa-book"></i>
            <span>Ressources</span>
          </a>
        </div>
        
        <div class="nav-section">
          <div class="nav-title">Actions rapides</div>
          <a href="index.php?action=offres" class="sidebar-link" target="_blank">
            <i class="fas fa-external-link-alt"></i>
            <span>Voir le site</span>
          </a>
        </div>
      </nav>
      
      <div class="sidebar-footer">
        <div class="admin-user">
          <div class="admin-avatar">
            <?php 
              if (isset($user) && isset($user['prenom'])) {
                echo strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1));
              } else {
                echo 'AD';
              }
            ?>
          </div>
          <div class="admin-user-info">
            <h4>
              <?php 
                if (isset($user)) {
                  echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']);
                } else {
                  echo 'Admin ImpactAble';
                }
              ?>
            </h4>
            <p>Administrateur</p>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main content -->
    <main class="admin-main">
      <header class="admin-header">
        <div>
          <h2>
            <?php
              // Titre dynamique basé sur la page actuelle
              $pageTitles = [
                'admin-dashboard' => 'Gestion des offres',
                'admin-gestion-offres' => 'Gestion des offres',
                'admin-gestion-candidatures' => 'Gestion des candidatures',
                'admin-gestion-utilisateurs' => 'Gestion des utilisateurs',
                'admin-voir-offre' => 'Détails de l\'offre',
                'admin-modifier-offre' => 'Modifier l\'offre'
              ];
              
              $currentAction = $_GET['action'] ?? 'admin-dashboard';
              echo $pageTitles[$currentAction] ?? 'Administration ImpactAble';
            ?>
          </h2>
          <p class="text-muted">
            <?php
              $pageDescriptions = [
                'admin-dashboard' => 'Bienvenue dans l\'interface d\'administration d\'ImpactAble',
                'admin-gestion-offres' => 'Gérer toutes les offres d\'emploi de la plateforme',
                'admin-gestion-candidatures' => 'Consulter et gérer les candidatures',
                'admin-gestion-utilisateurs' => 'Gérer les utilisateurs et leurs permissions',
                'admin-voir-offre' => 'Détails complets de l\'offre sélectionnée',
                'admin-modifier-offre' => 'Modifier les informations de l\'offre'
              ];
              echo $pageDescriptions[$currentAction] ?? 'Interface d\'administration';
            ?>
          </p>
        </div>
        
        <div class="header-actions">
          <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Rechercher...">
          </div>
          <a href="index.php?action=deconnexion" class="btn secondary">
            <i class="fas fa-sign-out-alt"></i>
            <span>Déconnexion</span>
          </a>
        </div>
      </header>

      <div class="admin-content">