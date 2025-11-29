<?php
// Don.php - CORRECTION
session_start();

// Chemin absolu pour l'inclusion
include __DIR__ . '/../../controller/FrontCampagneController.php';
include __DIR__ . '/../../controller/DonController.php';
include __DIR__ . '/../../controller/EmailController.php';

$campagneC = new FrontCampagneController();
$donC = new DonController();
$emailC = new EmailController();

// Récupérer la campagne
$id_campagne = $_GET['id_campagne'] ?? null;
$campagne = null;

if ($id_campagne) {
    $campagne = $campagneC->getCampagne($id_campagne);
}

// Traitement du formulaire
$message_success = '';
$message_erreur = '';
$result = null; // ← AJOUTER CETTE LIGNE
$email_donateur = ''; // ← AJOUTER AUSSI CETTE LIGNE

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['secure_token'] ?? '') !== 'don_secure_2024') {
        $message_erreur = "Erreur de sécurité. Veuillez réessayer.";
    } else {
        $montant = 0;
        if (isset($_POST['montant'])) {
            if ($_POST['montant'] === 'custom' && isset($_POST['custom_amount']) && !empty($_POST['custom_amount'])) {
                $montant = floatval($_POST['custom_amount']);
            } else {
                $montant = floatval($_POST['montant']);
            }
        }
        
        $message = $_POST['message'] ?? '';
        $methode_paiment = $_POST['methode_paiment'] ?? '';
        $email_donateur = $_POST['email_donateur'] ?? ''; // ← DÉPLACER ICI
        $nom_donateur = $_POST['nom_donateur'] ?? '';

        // Validation
        if ($montant > 0 && !empty($methode_paiment) && $campagne && !empty($email_donateur) && !empty($nom_donateur)) {
            
            $result = $donC->faireDon($id_campagne, $montant, $message, $methode_paiment, $email_donateur, $nom_donateur);
            
            if ($result) {
                $message_success = "Merci pour votre don de " . number_format($montant, 2) . " TND !";
// Rediriger vers listCampagnes.php après un don réussi
echo "<script>window.location.href = 'listCampagnes.php?don_success=1';</script>";
exit;
                
                // ACTUALISER LES DONNÉES DE LA CAMPAGNE
                $campagne = $campagneC->getCampagne($id_campagne); // ← AJOUTER CETTE LIGNE
                
                // Envoyer l'email de confirmation
                $email_envoye = $emailC->envoyerRecuDon($email_donateur, $nom_donateur, $montant, $result, $campagne['titre']);
                
                if (!$email_envoye) {
                    $message_erreur = "Votre don a été enregistré mais l'envoi du reçu a échoué.";
                }
            } else {
                $message_erreur = "Une erreur est survenue lors du traitement de votre don.";
            }
        } else {
            $message_erreur = "Veuillez remplir tous les champs obligatoires (*)";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ImpactAble — Faire un Don Sécurisé</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .form-etape { display: none; }
    .form-etape.active { display: block; }
    .progress-bar { height: 6px; background: #e0e0e0; border-radius: 3px; margin: 20px 0; }
    .progress { height: 100%; background: var(--moss); border-radius: 3px; transition: width 0.3s; }
    .etape-indicator { display: flex; justify-content: space-between; margin: 20px 0; }
    .etape { text-align: center; flex: 1; }
    .etape-number { 
        width: 30px; height: 30px; border-radius: 50%; 
        background: #e0e0e0; color: #666; 
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 5px; font-weight: bold;
    }
    .etape.active .etape-number { background: var(--moss); color: white; }
    .payment-method { border: 2px solid #e0e0e0; border-radius: 8px; padding: 15px; margin: 10px 0; cursor: pointer; transition: all 0.3s; }
    .payment-method:hover { border-color: var(--moss); }
    .payment-method.selected { border-color: var(--moss); background: var(--light-sage); }
    .payment-details { display: none; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 5px; }
    .security-badge { background: #e8f5e8; border: 1px solid #4caf50; color: #2e7d32; padding: 8px 12px; border-radius: 20px; font-size: 0.8em; margin-left: 10px; }
  </style>
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

    <!-- Don Modal -->
    <div class="modal-backdrop" id="donationModal" style="display: block; position: relative; background: transparent;">
      <div class="modal" style="position: relative; margin: 2rem auto; max-width: 600px;">
        <div class="auth-decoration">
          <div class="auth-icon">
            <i class="fas fa-hand-holding-heart"></i>
          </div>
        </div>
        <div class="modal-body">
          
          <?php if($message_success): ?>
          <div class="success-message">
            <i class="fas fa-check-circle"></i> <?= $message_success ?>
            <p>Un reçu a été envoyé à <?= htmlspecialchars($email_donateur) ?></p>
          </div>
          <?php endif; ?>
          
          <?php if($message_erreur): ?>
          <div class="error-message">
            <i class="fas fa-exclamation-triangle"></i> <?= $message_erreur ?>
          </div>
          <?php endif; ?>

          <?php if($campagne): ?>
          <h2 class="auth-title">Soutenir la Campagne</h2>
          <p class="auth-subtitle"><?= htmlspecialchars($campagne['titre']) ?></p>
          
          <!-- Info campagne -->
          <div class="campagne-info">
            <p><strong>Objectif :</strong> <?= number_format($campagne['objectif_montant'], 2) ?> TND</p>
            <p><strong>Collecté :</strong> <?= number_format($campagne['montant_actuel'], 2) ?> TND</p>
            <div class="progress">
              <div class="progress-bar" style="width: <?= min($campagneC->getProgression($campagne['Id_campagne']), 100) ?>%"></div>
            </div>
          </div>

          <!-- Indicateur d'étapes -->
          <div class="etape-indicator">
            <div class="etape active">
              <div class="etape-number">1</div>
              <div>Information</div>
            </div>
            <div class="etape">
              <div class="etape-number">2</div>
              <div>Paiement</div>
            </div>
          </div>

          <!-- Barre de progression -->
          <div class="progress-bar">
            <div class="progress" id="progressBar" style="width: 50%"></div>
          </div>
          
          <form id="donationForm" method="POST">
            <input type="hidden" name="id_campagne" value="<?= $id_campagne ?>">
            <input type="hidden" name="methode_paiment" id="methodePaimentInput" value="carte">

            <input type="hidden" name="secure_token" value="don_secure_2024">

            <!-- ÉTAPE 1: Informations personnelles -->
            <div class="form-etape active" id="etape1">
              <h3><i class="fas fa-user"></i> Vos informations</h3>
              
              <div class="form-group">
                <label for="nom_donateur">
                  <i class="fas fa-signature"></i>
                  Nom complet *
                </label>
                <input id="nom_donateur" name="nom_donateur" class="input" type="text" 
                       value="<?= htmlspecialchars($_POST['nom_donateur'] ?? '') ?>" 
                       placeholder="Votre nom complet" required>
              </div>

              <div class="form-group">
                <label for="email_donateur">
                  <i class="fas fa-envelope"></i>
                  Adresse email *
                </label>
                <input id="email_donateur" name="email_donateur" class="input" type="email" 
                       value="<?= htmlspecialchars($_POST['email_donateur'] ?? '') ?>" 
                       placeholder="votre@email.com" required>
              </div>

              <!-- Montant du don -->
              <div class="form-group">
                <label>
                  <i class="fas fa-coins"></i>
                  Montant de votre don (TND) *
                </label>
                <div class="checkbox-group" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                  <label class="checkbox-label montant-option" style="text-align: center; padding: 15px; border: 2px solid var(--moss); border-radius: var(--radius-sm); cursor: pointer; background-color: var(--light-sage);">
                    <input type="radio" name="montant" value="20" style="display: none;" required>
                    <div style="font-weight: bold; color: var(--moss);">20 TND</div>
                    <div class="text-muted small">Aide de base</div>
                  </label>
                  <label class="checkbox-label montant-option" style="text-align: center; padding: 15px; border: 2px solid var(--light-sage); border-radius: var(--radius-sm); cursor: pointer;">
                    <input type="radio" name="montant" value="50" style="display: none;">
                    <div style="font-weight: bold; color: var(--moss);">50 TND</div>
                    <div class="text-muted small">Soutien régulier</div>
                  </label>
                  <label class="checkbox-label montant-option" style="text-align: center; padding: 15px; border: 2px solid var(--light-sage); border-radius: var(--radius-sm); cursor: pointer;">
                    <input type="radio" name="montant" value="100" style="display: none;">
                    <div style="font-weight: bold; color: var(--moss);">100 TND</div>
                    <div class="text-muted small">Contribution</div>
                  </label>
                  <label class="checkbox-label montant-option" style="text-align: center; padding: 15px; border: 2px solid var(--light-sage); border-radius: var(--radius-sm); cursor: pointer;">
                    <input type="radio" name="montant" value="custom" style="display: none;">
                    <div style="font-weight: bold; color: var(--moss);">Autre montant</div>
                    <div class="text-muted small">Personnalisé</div>
                  </label>
                </div>
              </div>

              <div class="form-group" id="custom-amount-container" style="display: none;">
                <label for="custom_amount">
                  <i class="fas fa-edit"></i>
                  Votre montant personnalisé (TND) *
                </label>
                <input id="custom_amount" name="custom_amount" class="input" type="number" step="0.01" min="1" placeholder="Entrez le montant de votre choix">
              </div>

              <div class="form-footer">
                <a href="listCampagnes.php" class="btn secondary">
    <i class="fas fa-arrow-left"></i>
    Retour aux campagnes
</a>
                <button type="button" class="btn primary" onclick="nextEtape(2)">
                  Continuer <i class="fas fa-arrow-right"></i>
                </button>
              </div>
            </div>

            <!-- ÉTAPE 2: Paiement -->
            <div class="form-etape" id="etape2">
              <h3><i class="fas fa-credit-card"></i> Paiement sécurisé</h3>
              
              <!-- Méthodes de paiement améliorées -->
              <div class="form-group">
                <label>
                  <i class="fas fa-credit-card"></i>
                  Méthode de paiement *
                </label>
                
                <!-- Carte Bancaire -->
                <div class="payment-method selected" onclick="selectPaymentMethod('carte')">
                  <div style="display: flex; justify-content: between; align-items: center;">
                    <div>
                      <h4 style="margin: 0; display: flex; align-items: center;">
                        <i class="fas fa-credit-card" style="margin-right: 10px; color: var(--moss);"></i>
                        Carte Bancaire
                        <span class="security-badge"><i class="fas fa-lock"></i> Sécurisé</span>
                      </h4>
                      <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9em;">Visa, Mastercard, Carte Bleue</p>
                    </div>
                    <div class="payment-logos" style="display: flex; gap: 5px;">
                      <span style="font-size: 1.5em;">💳</span>
                      <span style="font-size: 1.5em;">🔒</span>
                    </div>
                  </div>
                </div>

                <!-- Virement Bancaire -->
                <div class="payment-method" onclick="selectPaymentMethod('virement')">
                  <div style="display: flex; justify-content: between; align-items: center;">
                    <div>
                      <h4 style="margin: 0; display: flex; align-items: center;">
                        <i class="fas fa-university" style="margin-right: 10px; color: var(--moss);"></i>
                        Virement Bancaire
                      </h4>
                      <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9em;">Transfert direct depuis votre banque</p>
                    </div>
                    <div class="payment-logos" style="display: flex; gap: 5px;">
                      <span style="font-size: 1.5em;">🏦</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Message personnel -->
              <div class="form-group">
                <label for="personal-message">
                  <i class="fas fa-comment-heart"></i>
                  Message d'encouragement (optionnel)
                </label>
                <textarea id="personal-message" name="message" class="textarea" placeholder="Laissez un message pour les bénéficiaires..." rows="3"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
              </div>

              <div class="security-notice" style="background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 15px 0;">
                <h4 style="margin: 0 0 10px 0; color: #1976d2;"><i class="fas fa-shield-alt"></i> Paiement 100% sécurisé</h4>
                <p style="margin: 0; font-size: 0.9em;">Vos données sont cryptées et protégées. Aucune information bancaire n'est stockée sur nos serveurs.</p>
              </div>

              <div class="form-footer">
                <button type="button" class="btn secondary" onclick="prevEtape(1)">
                  <i class="fas fa-arrow-left"></i> Retour
                </button>
                <button type="submit" class="btn success">
                  <i class="fas fa-heart"></i> Confirmer le don
                </button>
              </div>
            </div>
          </form>
          <?php else: ?>
          <div class="error-message" style="text-align: center; padding: 40px;">
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 20px;"></i>
            <h3>Campagne non trouvée</h3>
            <p>La campagne que vous cherchez n'existe pas ou a été supprimée.</p>
            <a href="listCampagnes.php" class="btn primary">Voir les campagnes</a>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="site-footer">
      <!-- ... votre footer existant ... -->
    </footer>
  </div>

  <script src="assets/js/script.js"></script>
  <script>
  let currentEtape = 1;
  let selectedPaymentMethod = 'carte';

  function nextEtape(etape) {
    if (validateEtape(currentEtape)) {
      document.getElementById('etape' + currentEtape).classList.remove('active');
      document.getElementById('etape' + etape).classList.add('active');
      document.querySelectorAll('.etape')[currentEtape - 1].classList.remove('active');
      document.querySelectorAll('.etape')[etape - 1].classList.add('active');
      document.getElementById('progressBar').style.width = (etape * 50) + '%';
      currentEtape = etape;
    }
  }

  function prevEtape(etape) {
    document.getElementById('etape' + currentEtape).classList.remove('active');
    document.getElementById('etape' + etape).classList.add('active');
    document.querySelectorAll('.etape')[currentEtape - 1].classList.remove('active');
    document.querySelectorAll('.etape')[etape - 1].classList.add('active');
    document.getElementById('progressBar').style.width = (etape * 50) + '%';
    currentEtape = etape;
  }

  function selectPaymentMethod(method) {
    selectedPaymentMethod = method;
    document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    
    // Mettre à jour le champ caché
    document.getElementById('methodePaimentInput').value = method;
  }

  function validateEtape(etape) {
    if (etape === 1) {
      const nom = document.getElementById('nom_donateur').value;
      const email = document.getElementById('email_donateur').value;
      const montant = document.querySelector('input[name="montant"]:checked');
      
      if (!nom || !email || !montant) {
        alert('Veuillez remplir tous les champs obligatoires');
        return false;
      }
      
      if (montant.value === 'custom' && !document.getElementById('custom_amount').value) {
        alert('Veuillez entrer un montant personnalisé');
        return false;
      }
    }
    return true;
  }

  // Gestion des options de montant
  document.querySelectorAll('.montant-option').forEach(option => {
    option.addEventListener('click', function() {
      document.querySelectorAll('.montant-option').forEach(opt => {
        opt.style.borderColor = 'var(--light-sage)';
        opt.style.backgroundColor = 'transparent';
      });
      this.style.borderColor = 'var(--moss)';
      this.style.backgroundColor = 'var(--light-sage)';
      
      const input = this.querySelector('input');
      input.checked = true;
      
      if (input.value === 'custom') {
        document.getElementById('custom-amount-container').style.display = 'block';
        document.getElementById('custom_amount').focus();
      } else {
        document.getElementById('custom-amount-container').style.display = 'none';
        document.getElementById('custom_amount').value = '';
      }
    });
  });

  // Initialisation
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.montant-option:first-child').click();
    document.querySelector('.payment-method').click();
  });
  </script>
</body>
</html>