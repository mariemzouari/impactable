<?php 
// La variable $evenement sera fournie par EvenementController::modify() via index.php
// Pour éviter une erreur PHP si le contrôleur n'est pas encore implémenté ou si la variable est vide,
// nous initialisons $evenement à un tableau vide.
if (!isset($evenement) || !is_array($evenement)) {
    // Si la variable n'est pas définie (ex: erreur de route ou de contrôleur), initialiser avec des valeurs sûres
    $evenement = [
        'id' => $_GET['id'] ?? null,
        'titre' => '',
        'date_event' => '',
        'categorie' => 'conf',
        'description' => ''
    ];
}
$eventId = $evenement['id'] ?? $_GET['id'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin — Gérer les Événements</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .data-table-wrapper { padding: 0; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th, .data-table td { padding: 16px; text-align: left; border-bottom: 1px solid rgba(75,46,22,0.08); }
    .data-table th { font-weight: 600; color: var(--muted); font-size: 0.9rem; }
    .data-table td .btn { padding: 8px 12px; margin-right: 8px; }
  </style>
</head>
<body>
  <div class="container">
      	<header class="site-header" role="banner">
  	  <div class="brand">
  	    <button class="nav-toggle" id="navToggle"><i class="fas fa-bars"></i></button>
  	    <div class="logo"><img src="../../assets/images/logo.png" alt="ImpactAble Admin" class="logo-image"></div>
  	  </div>
  	  <div class="header-actions">
  	    <a href="#" class="btn ghost" aria-label="Notifications"><i class="fas fa-bell"></i></a>
  	    <a href="#" class="btn ghost" id="logoutBtn">Déconnexion</a>
  	  </div>
  	</header>
    
      	<div class="side-panel" id="sidePanel">
  	  <nav class="panel-nav">
  	    <div class="nav-section">
  	      <div class="nav-title">Menu Principal</div>
  	      <a href="index.php?page=admin-dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span></a>
  	      <a href="index.php?page=admin-events-list" class="nav-link active"><i class="fas fa-calendar-alt"></i><span>Gérer les Événements</span></a>
  	      <a href="index.php?page=admin-users-list" class="nav-link"><i class="fas fa-users"></i><span>Gérer les Utilisateurs</span></a>
  	      <a href="index.php?page=admin-reclamations-list" class="nav-link"><i class="fas fa-comment-alt"></i><span>Voir les Réclamations</span></a>
        </div> 
  	  </nav>
        <div class="panel-footer">
            <div class="user-profile">
                <div class="user-avatar">A</div>
                <div class="user-info"><h4>Admin Impact</h4><p>Administrateur</p></div>
            </div>
        </div>
  	</div>
  	<div class="panel-overlay" id="panelOverlay"></div>

    <main>
      <section class="section">
        <div class="section-header">
          <h2>Modifier l'événement #<?php echo htmlspecialchars($eventId); ?></h2>
          <a href="index.php?page=admin-events-list" class="btn ghost"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
        </div>
        <div class="card" style="max-width: 800px; margin: 0 auto;">
          <div class="card-body" style="padding: 2rem 3rem;">
            <form action="index.php?page=admin-events-handle-modify" method="POST" novalidate>
              <input type="hidden" name="id" value="<?php echo htmlspecialchars($eventId); ?>">
              
              <div class="form-group"><label for="titre">Titre</label><input type="text" id="titre" name="titre" class="input" value="<?php echo htmlspecialchars($evenement['titre']); ?>" required></div>
              
              <div class="form-group"><label for="date_event">Date & Heure</label><input type="datetime-local" id="date_event" name="date_event" class="input" value="<?php echo htmlspecialchars($evenement['date_event']); ?>" required></div>
              
              <div class="form-group"><label for="categorie">Catégorie</label><select id="categorie" name="categorie" class="select">
                  <option value="conf" <?php echo ($evenement['categorie'] === 'conf') ? 'selected' : ''; ?>>Conférence</option>
                  <option value="atelier" <?php echo ($evenement['categorie'] === 'atelier') ? 'selected' : ''; ?>>Atelier</option>
                  <option value="autre" <?php echo ($evenement['categorie'] === 'autre') ? 'selected' : ''; ?>>Autre</option>
              </select></div>
              
              <div class="form-group"><label for="description">Description</label><textarea id="description" name="description" class="input" rows="5" required><?php echo htmlspecialchars($evenement['description']); ?></textarea></div>
              
              <div class="form-footer" style="text-align: right; padding-top: 1rem; border-top: 1px solid rgba(75,46,22,0.08);">
                <button type="submit" class="btn primary"><i class="fas fa-save"></i> Enregistrer les modifications</button>
              </div>
            </form>
          </div>
        </div>
      </section>
    </main>
    
    <footer class="site-footer" style="background: transparent; border-top: 1px solid rgba(75,46,22,0.08);">
        <div class="container">
            <div class="footer-bottom" style="border-top: none; padding-top: 0;">
                <p>© <span id="year"></span> ImpactAble — Panel d'Administration</p>
            </div>
        </div>
    </footer>
  </div>
  <script src="../../assets/js/script.js"></script>
  <script src="../../assets/js/validation.js"></script>
</body>
</html>