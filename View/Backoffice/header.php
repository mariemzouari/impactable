<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ImpactAble — Administration</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
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
          <a href="index.php" class="sidebar-link active">
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
          <a href="#opportunities" class="sidebar-link">
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

<div class="admin-content">
      <h2>Gestion des Événements</h2>
      <p class="text-muted">Créez, modifiez et gérez les événements communautaires</p>
      <div class="top-actions">
        <a href="evenment_back.php?action=create" class="btn primary"><i class="fas fa-plus"></i> Nouvel Événement</a>
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Rechercher événement...">
        </div>
      </div>

      <?php if(!empty($error)): ?>
        <div class="alert alert-error" style="background:#fdecea;color:#9b1c1c;padding:10px;border-radius:6px;margin:12px 0;">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <?php if(!empty($success)): ?>
        <div class="alert alert-success" style="background:#e9f7ef;color:#1e7f3e;padding:10px;border-radius:6px;margin:12px 0;">
          <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>

      <!-- STATISTIQUES -->
      <?php if(!empty($stats)): ?>
      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:1.5rem; margin-bottom:2rem;">
        
        <!-- Card: Total Événements -->
        <div style="background:#fffaf5; border:2px solid #5e6d3b; color:#4b2e16; padding:1.5rem; border-radius:16px; box-shadow:0 8px 22px rgba(75,46,22,0.08);">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
              <p style="margin:0; font-size:0.9rem; color:#6b4b44;">Total Événements</p>
              <h3 style="margin:0.5rem 0 0 0; font-size:2.5rem; color:#5e6d3b;"><?= htmlspecialchars($stats['total_events']) ?></h3>
            </div>
            <i class="fas fa-calendar-alt" style="font-size:2.5rem; color:#a9b97d; opacity:0.6;"></i>
          </div>
        </div>

        <!-- Card: Total Participations -->
        <div style="background:#fffaf5; border:2px solid #b47b47; color:#4b2e16; padding:1.5rem; border-radius:16px; box-shadow:0 8px 22px rgba(75,46,22,0.08);">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
              <p style="margin:0; font-size:0.9rem; color:#6b4b44;">Total Participations</p>
              <h3 style="margin:0.5rem 0 0 0; font-size:2.5rem; color:#b47b47;"><?= htmlspecialchars($stats['total_participations']) ?></h3>
            </div>
            <i class="fas fa-users" style="font-size:2.5rem; color:#b47b47; opacity:0.6;"></i>
          </div>
        </div>

        <!-- Card: Événements à venir -->
        <div style="background:#fffaf5; border:2px solid #3a4a2a; color:#4b2e16; padding:1.5rem; border-radius:16px; box-shadow:0 8px 22px rgba(75,46,22,0.08);">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
              <p style="margin:0; font-size:0.9rem; color:#6b4b44;">Événements à Venir</p>
              <h3 style="margin:0.5rem 0 0 0; font-size:2.5rem; color:#3a4a2a;"><?= count($stats['upcoming_events']) ?></h3>
            </div>
            <i class="fas fa-clock" style="font-size:2.5rem; color:#3a4a2a; opacity:0.6;"></i>
          </div>
        </div>

        <!-- Card: Participation Moyenne -->
        <div style="background:#fffaf5; border:2px solid #a9b97d; color:#4b2e16; padding:1.5rem; border-radius:16px; box-shadow:0 8px 22px rgba(75,46,22,0.08);">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
              <p style="margin:0; font-size:0.9rem; color:#6b4b44;">Taux de Participation</p>
              <h3 style="margin:0.5rem 0 0 0; font-size:2.5rem; color:#a9b97d;">
                <?php 
                  $avg = $stats['total_events'] > 0 ? round($stats['total_participations'] / $stats['total_events']) : 0;
                  echo $avg;
                ?>
              </h3>
            </div>
            <i class="fas fa-chart-pie" style="font-size:2.5rem; color:#a9b97d; opacity:0.6;"></i>
          </div>
        </div>
      </div>

      <!-- GRAPHIQUE -->
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem; margin-bottom:2rem;">
        <!-- Bar Chart: Participations par Événement -->
        <div style="background:#fffaf5; padding:1.5rem; border-radius:16px; box-shadow:0 8px 22px rgba(75,46,22,0.08);">
          <h3 style="margin-top:0; color:#4b2e16; border-bottom:2px solid #a9b97d; padding-bottom:12px;">Participations par Événement</h3>
          <canvas id="chartBar"></canvas>
        </div>

        <!-- Pie Chart: Distribution -->
        <div style="background:#fffaf5; padding:1.5rem; border-radius:16px; box-shadow:0 8px 22px rgba(75,46,22,0.08);">
          <h3 style="margin-top:0; color:#4b2e16; border-bottom:2px solid #b47b47; padding-bottom:12px;">Distribution par catégories</h3>
          <canvas id="chartPie"></canvas>
        </div>
      </div>

      <script>
      // Préparer les données pour les graphiques
      <?php
      $eventNames = [];
      $participationCounts = [];
      // Couleurs du template: brown, copper, moss, sage, sand, light-sage, dark-green
      $templateColors = ['#5e6d3b', '#b47b47', '#a9b97d', '#4b2e16', '#3a4a2a', '#e1e8c9'];
      $chartColors = [];
      
      if(!empty($stats['participations_by_event'])){
        foreach($stats['participations_by_event'] as $key => $row){
          $eventNames[] = substr($row['titre'], 0, 20);
          $participationCounts[] = $row['count'];
          $chartColors[] = $templateColors[$key % count($templateColors)];
        }
      }
      ?>

      // Bar Chart
      const ctxBar = document.getElementById('chartBar').getContext('2d');
      const chartBar = new Chart(ctxBar, {
        type: 'bar',
        data: {
          labels: <?= json_encode($eventNames) ?>,
          datasets: [{
            label: 'Nombre de Participations',
            data: <?= json_encode($participationCounts) ?>,
            backgroundColor: <?= json_encode($chartColors) ?>,
            borderColor: '#4b2e16',
            borderWidth: 2,
            borderRadius: 6
          }]
        },
        options: {
          responsive: true,
          plugins: { 
            legend: { display: true, labels: { color: '#4b2e16', font: { weight: 'bold' } } }
          },
          scales: { 
            y: { beginAtZero: true, ticks: { color: '#4b2e16' }, grid: { color: 'rgba(75,46,22,0.1)' } },
            x: { ticks: { color: '#4b2e16' }, grid: { color: 'rgba(75,46,22,0.05)' } }
          }
        }
      });

      // Pie Chart
      const ctxPie = document.getElementById('chartPie').getContext('2d');
      const chartPie = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
          labels: <?= json_encode($eventNames) ?>,
          datasets: [{
            data: <?= json_encode($participationCounts) ?>,
            backgroundColor: <?= json_encode($chartColors) ?>,
            borderWidth: 3,
            borderColor: '#fffaf5'
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { position: 'bottom', labels: { color: '#4b2e16', font: { weight: 'bold' } } } }
        }
      });
      </script>
      <?php endif; ?>

      <table class="admin-table">
        <thead>
          <tr>
            <th>Événement</th>
            <th>Date</th>
            <th>Catégorie</th>
            <th>Description</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if(isset($events) && is_array($events)): ?>
            <?php foreach($events as $event): ?>
              <tr>
                <td>
                  <strong><?= htmlspecialchars($event['titre']) ?></strong><br>
                  <small class="text-muted"><?= htmlspecialchars($event['categorie']) ?></small>
                </td>
                <td><?= date('d M Y H:i', strtotime($event['date_event'])) ?></td>
                <td><?= htmlspecialchars($event['categorie']) ?></td>
                <td><?= htmlspecialchars($event['description']) ?></td>
                <td class="action-btns">
                  <a href="view_event_participants.php?id=<?= $event['id'] ?>" class="btn primary" title="Voir les participants" style="margin-right: 8px;">
                    <i class="fas fa-users"></i> Participants
                  </a>
                  <a href="evenment_back.php?action=edit&id=<?= $event['id'] ?>" class="btn edit" title="Éditer"><i class="fas fa-pen"></i></a>
                  <a href="evenment_back.php?action=delete&id=<?= $event['id'] ?>" class="btn delete" onclick="return confirm('Voulez-vous vraiment supprimer ?');" title="Supprimer"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
              <tr><td colspan="5">Aucun événement trouvé.</td></tr>
          <?php endif; ?>       
        </tbody>
      </table>
      </div>

























</main>
</div>

<script src="assets/js/script.js"> </script>
</body>
</html>

