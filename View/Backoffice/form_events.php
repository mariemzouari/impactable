<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ImpactAble — Administration</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 15px;
}

.form-group label {
  font-weight: 600;
  margin-bottom: 6px;
}

.form-input {
  padding: 10px 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
  width: 100%;
  background: #fff;
}

.form-input:focus {
  border-color: #4a6cf7;
  outline: none;
  box-shadow: 0 0 0 2px rgba(74,108,247,0.1);
}



  </style>
</head>
<body>
  <div class="admin-container">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <div class="sidebar-header">
         <div class="admin-logo">
      <img src="assets/images/logo.png" alt="Inclusive Opportunities" class="admin-logo-image">
    </div>
      </div>
      
      <nav class="sidebar-nav">
        <div class="nav-section">
          <div class="nav-title">Principal</div>
          <a href="#dashboard" class="sidebar-link active">
            <i class="fas fa-tachometer-alt"></i>
            <span>Tableau de bord</span>
          </a>
          <a href="#analytics" class="sidebar-link">
            <i class="fas fa-chart-bar"></i>
            <span>Analytiques</span>
          </a>
        </div>
        
        <div class="nav-section">
          <div class="nav-title">Gestion de contenu</div>
          <a href="#users" class="sidebar-link">
            <i class="fas fa-users"></i>
            <span>Utilisateurs</span>
          </a>
          <a href="index.php?action=admin-dashboard" class="sidebar-link <?= (isset($_GET['action']) && $_GET['action'] == 'admin-dashboard') ? 'active' : '' ?>">
            <i class="fas fa-briefcase"></i>
            <span>Opportunités</span>
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
          <div class="nav-title">Communauté</div>
          <a href="#forum" class="sidebar-link">
            <i class="fas fa-comments"></i>
            <span>Forum</span>
          </a>
          <a href="#reclamations" class="sidebar-link">
            <i class="fas fa-comment-alt"></i>
            <span>Réclamations</span>
          </a>
        </div>
        
        <div class="nav-section">
          <div class="nav-title">Paramètres</div>
          <a href="#settings" class="sidebar-link">
            <i class="fas fa-cog"></i>
            <span>Configuration</span>
          </a>
        </div>
      </nav>
      
      <div class="sidebar-footer">
        <div class="admin-user">
          <div class="admin-avatar">AD</div>
          <div class="admin-user-info">
            <h4>Admin ImpactAble</h4>
            <p>Administrateur</p>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main content -->
    <main class="admin-main">
      <header class="admin-header">
        <div>
          <h2>Tableau de bord administrateur</h2>
          <p class="text-muted">Bienvenue dans l'interface d'administration d'ImpactAble</p>
        </div>
        
        <div class="header-actions">
          <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Rechercher...">
          </div>
          <button class="btn secondary">
            <i class="fas fa-sign-out-alt"></i>
            <span>Déconnexion</span>
          </button>
        </div>
      </header>



      <!-- your code here-->

      <div class="admin-container">
   <!-- FORMULAIRE CRÉATION ÉVÉNEMENT (STYLE UNIFIÉ) -->
<section class="content-card" style="max-width: 700px; margin: 20px auto;">

    <h2 class="card-title">
        <i class="fas fa-plus-circle"></i> Créer un Événement
    </h2>

    <?php if(!empty($errors)): ?>
      <div class="errors" style="color: #c0392b; margin-bottom: 12px;">
        <ul style="margin:0 0 0 18px; padding:0;">
          <?php foreach($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php
// Determine form action and values for create vs edit
$mode = $action ?? 'create';
$eventTitle = $event['titre'] ?? '';
$eventDateDebutRaw = $event['date_debut'] ?? '';
$eventDateDebut = $eventDateDebutRaw ? date('Y-m-d\TH:i', strtotime($eventDateDebutRaw)) : '';
$eventDateFinRaw = $event['date_fin'] ?? '';
$eventDateFin = $eventDateFinRaw ? date('Y-m-d\TH:i', strtotime($eventDateFinRaw)) : '';
$eventCategorie = $event['categorie'] ?? '';
$eventDescription = $event['description'] ?? '';
$eventCapaciteMax = $event['capacite_max'] ?? '';
$eventLocation = $event['location'] ?? '';
$formAction = ($mode === 'edit' && isset($id)) ? "evenment_back.php?action=edit&id=" . intval($id) : 'evenment_back.php?action=create';
$submitLabel = ($mode === 'edit') ? 'Enregistrer les modifications' : 'Enregistrer';
?>

<form action="<?= htmlspecialchars($formAction) ?>" method="POST" class="event-form" enctype="multipart/form-data">

    <div class="form-group">
        <label for="titre">Titre de l'événement</label>
        <input type="text" id="titre" name="titre" class="form-input"
               placeholder="Ex : Nettoyage de plage"  value="<?= htmlspecialchars($eventTitle) ?>">
    </div>
 
    <!-- DATE DE DEBUT -->
    <div class="form-group">
        <label for="date_debut">Date & Heure de début</label>
        <input type="datetime-local" id="date_debut" name="date_debut" class="form-input"  value="<?= $eventDateDebut ?>">
    </div>
 
    <!-- DATE DE FIN -->
    <div class="form-group">
        <label for="date_fin">Date & Heure de fin</label>
        <input type="datetime-local" id="date_fin" name="date_fin" class="form-input"  value="<?= $eventDateFin ?>">
    </div>
 
    <!-- CATEGORIE -->
    <div class="form-group">
        <label for="categorie">Catégorie</label>
        <select id="categorie" name="categorie" class="form-input" >
            <option value="">-- Choisir une catégorie --</option>
            <option value="Éducation" <?= ($eventCategorie === 'Éducation') ? 'selected' : '' ?>>Éducation</option>
            <option value="Environnement" <?= ($eventCategorie === 'Environnement') ? 'selected' : '' ?>>Environnement</option>
            <option value="Santé" <?= ($eventCategorie === 'Santé') ? 'selected' : '' ?>>Santé</option>
            <option value="Solidarité" <?= ($eventCategorie === 'Solidarité') ? 'selected' : '' ?>>Solidarité</option>
            <option value="Culture" <?= ($eventCategorie === 'Culture') ? 'selected' : '' ?>>Culture</option>
            <option value="Sport" <?= ($eventCategorie === 'Sport') ? 'selected' : '' ?>>Sport</option>
            <option value="Métier" <?= ($eventCategorie === 'Métier') ? 'selected' : '' ?>>Métier</option>
        </select>
    </div>
 
    <div class="form-group">
        <label for="description">Description de l'événement</label>
        <textarea id="description" name="description" rows="4" class="form-input"
                  placeholder="Décrivez brièvement l'événement…" d><?= htmlspecialchars($eventDescription) ?></textarea>
    </div>
 
    <!-- CAPACITÉ MAX -->
    <div class="form-group">
        <label for="capacite_max">Nombre maximum de participants</label>
        <input type="number" id="capacite_max" name="capacite_max" class="form-input"
               placeholder="Ex : 100"  value="<?= htmlspecialchars($eventCapaciteMax ?? '') ?>">
    </div>
 
    <!-- LOCATION -->
    <div class="form-group">
        <label for="location">Lieu de l'événement</label>
        <input type="text" id="location" name="location" class="form-input"
               placeholder="Ex : Plage de La Marsa, Tunis"  value="<?= htmlspecialchars($eventLocation ?? '') ?>">




    <!-- Boutons -->
    <div class="form-buttons" style="margin-top: 20px; display: flex; gap: 10px;">
        <button type="submit" class="btn primary">
            <i class="fas fa-check"></i> <?= $submitLabel ?>
        </button>

        <a href="evenment_back.php" class="btn secondary">
            <i class="fas fa-arrow-left"></i> Annuler
        </a>
    </div>

</form>
</section>
</div>

























</main>
</div>

<script src="assets/js/script.js"> </script>
</body>
</html>


