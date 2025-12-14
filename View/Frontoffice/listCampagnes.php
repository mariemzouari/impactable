<?php
// listCampagnes.php - Version avec actualisation directe
include __DIR__ . '/../../Model/FrontCampagneController.php';
include __DIR__ . '/../../Model/DonController.php';

$campagneC = new FrontCampagneController();
$donC = new DonController();

// ACTUALISER les montants des campagnes avant d'afficher
$campagnes = $campagneC->listCampagnesActives();

// Pour chaque campagne, on actualise le montant depuis la table des dons
foreach ($campagnes as &$campagne) {
  $montant_actualise = $campagneC->actualiserMontantCampagne($campagne['Id_campagne']);
  if ($montant_actualise !== false) {
    $campagne['montant_actuel'] = $montant_actualise;
  }
}

// Séparer les campagnes par statut
$campagnes_actives = [];
$campagnes_terminees = [];

foreach ($campagnes as $campagne) {
  if ($campagne['statut'] === 'terminée' || (isset($campagne['date_fin']) && strtotime($campagne['date_fin']) < time())) {
    $campagnes_terminees[] = $campagne;
  } else {
    $campagnes_actives[] = $campagne;
  }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ImpactAble — Liste des Campagnes</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .section-terminées {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 20px;
      margin-top: 40px;
    }

    .campaign-card.terminée {
      opacity: 0.7;
      border: 2px dashed #ccc;
    }

    .status-badge {
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.8em;
      font-weight: bold;
      margin-left: 10px;
    }

    .status-active {
      background: #e8f5e8;
      color: #2e7d32;
    }

    .status-terminée {
      background: #f5f5f5;
      color: #666;
    }

    .empty-state {
      text-align: center;
      padding: 40px;
      color: #666;
    }

    /* Animation pour la barre de progression */
    .progress-fill {
      transition: width 1s ease-in-out;
      height: 100%;
      background: linear-gradient(90deg, #4CAF50, #45a049);
      border-radius: 10px;
    }

    .montant-actuel {
      font-size: 1.5em;
      font-weight: bold;
      color: #4CAF50;
    }

    .refresh-btn {
      background: #4CAF50;
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 5px;
      cursor: pointer;
      margin-left: 10px;
    }

    .refresh-btn:hover {
      background: #45a049;
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Header -->
    <header class="site-header" role="banner">
      <div class="brand">
        <div class="logo">
          <img src="assets/images/logo.png" alt="ImpactAble" class="logo-image">
        </div>
      </div>
      <div class="header-actions">
        <a href="index.php" class="btn ghost">
          <i class="fas fa-arrow-left"></i>
          Accueil
        </a>
        <button class="btn primary" onclick="actualiserPage()">
          <i class="fas fa-sync-alt"></i>
          Actualiser
        </button>
      </div>
    </header>

    <!-- Campagnes Actives -->
    <section class="section">
      <div class="section-header">
        <h1>Campagnes Actives
          <button class="refresh-btn" onclick="actualiserPage()" title="Actualiser les données">
            <i class="fas fa-sync-alt"></i>
          </button>
        </h1>
        <p>Soutenez nos campagnes en cours et faites la différence</p>

        <div style="color: var(--muted); font-size: 0.9rem;">
          <?= count($campagnes_actives) ?> campagne(s) active(s)
          <small style="margin-left: 10px;">Dernière actualisation: <?= date('H:i:s') ?></small>
        </div>
      </div>

      <!-- Grille des campagnes actives -->
      <div class="cards-grid">
        <?php if (!empty($campagnes_actives)): ?>
          <?php foreach ($campagnes_actives as $campagne):
            // Récupérer la progression ACTUALISÉE
            $progression = $campagneC->getProgression($campagne['Id_campagne']);
            $jours_restants = max(0, floor((strtotime($campagne['date_fin']) - time()) / (60 * 60 * 24)));
            ?>
            <article class="card campaign-card">
              <div class="card-header">
                <?php if ($campagne['image_campagne'] && !empty($campagne['image_campagne'])): ?>
                  <img src="<?= htmlspecialchars($campagne['image_campagne']) ?>"
                    alt="<?= htmlspecialchars($campagne['titre']) ?>" class="campaign-image">
                <?php else: ?>
                  <div class="campaign-placeholder">
                    <i class="fas fa-hand-holding-heart"></i>
                  </div>
                <?php endif; ?>

                <div class="campaign-badge <?= htmlspecialchars($campagne['urgence']) ?>">
                  <?= ucfirst(htmlspecialchars($campagne['urgence'])) ?>
                </div>

                <span class="status-badge status-active">
                  <i class="fas fa-play-circle"></i> Active
                </span>
              </div>

              <div class="card-body">
                <div class="campaign-category">
                  <i class="fas fa-tag"></i>
                  <?= htmlspecialchars($campagne['categorie_impact']) ?>
                </div>

                <h3><?= htmlspecialchars($campagne['titre']) ?></h3>

                <p class="campaign-description">
                  <?= htmlspecialchars($campagne['description'] ?? '') ?>
                </p>

                <div class="campaign-stats">
                  <div class="stat">
                    <div class="stat-value montant-actuel">
                      <?= number_format($campagne['montant_actuel'] ?? 0, 0) ?> TND
                    </div>
                    <div class="stat-label">Collectés</div>
                  </div>
                  <div class="stat">
                    <div class="stat-value"><?= number_format($campagne['objectif_montant'] ?? 0, 0) ?> TND</div>
                    <div class="stat-label">Objectif</div>
                  </div>
                  <div class="stat">
                    <div class="stat-value"><?= $jours_restants ?></div>
                    <div class="stat-label">Jours restants</div>
                  </div>
                </div>

                <!-- Barre de progression DYNAMIQUE -->
                <div class="progress-container">
                  <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= min($progression, 100) ?>%"></div>
                  </div>
                  <div class="progress-text">
                    <?= number_format($progression, 1) ?>% atteint
                  </div>
                </div>
              </div>

              <div class="card-footer">
                <a href="DonView.php?id_campagne=<?= $campagne['Id_campagne'] ?>" class="btn primary">
                  <i class="fas fa-heart"></i>
                  Faire un don
                </a>
                <button class="btn ghost share-btn" data-campaign="<?= $campagne['Id_campagne'] ?>">
                  <i class="fas fa-share-alt"></i>
                  Partager
                </button>
              </div>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="empty-state">
            <i class="fas fa-inbox" style="font-size: 4rem; color: #6b7280; margin-bottom: 1rem;"></i>
            <h3>Aucune campagne active</h3>
            <p>Il n'y a actuellement aucune campagne de collecte en cours.</p>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- Script pour l'actualisation -->
    <script>
      function actualiserPage() {
        // Afficher un indicateur de chargement
        const btn = event.target;
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualisation...';
        btn.disabled = true;

        // Recharger la page après un court délai pour voir l'animation
        setTimeout(() => {
          window.location.reload();
        }, 500);
      }

      // Actualiser automatiquement toutes les 2 minutes
      setInterval(() => {
        console.log('Actualisation automatique...');
        window.location.reload();
      }, 120000); // 2 minutes

      // Si l'utilisateur revient sur la page (après un don par exemple), actualiser
      document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
          // Petite pause puis actualisation
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        }
      });

      // Vérifier si on vient de faire un don (via URL parameter)
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('don_success')) {
        // Afficher un message de succès
        const message = document.createElement('div');
        message.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        `;
        message.innerHTML = '<i class="fas fa-check-circle"></i> Merci pour votre don !';
        document.body.appendChild(message);

        // Supprimer le message après 5 secondes
        setTimeout(() => {
          message.remove();
        }, 5000);

        // Actualiser les données après un don
        setTimeout(() => {
          window.location.reload();
        }, 2000);
      }
    </script>

    <!-- Footer -->
    <footer class="site-footer">
      <!-- ... votre footer existant ... -->
    </footer>
  </div>
</body>

</html>