<?php
// DonView.php - VERSION AVEC RECOMMANDATIONS
session_start();

include_once __DIR__ . '/../../config.php';
include_once __DIR__ . '/../../Model/utils.php';
include __DIR__ . '/../../Model/FrontCampagneController.php';
include __DIR__ . '/../../Model/DonController.php';
include __DIR__ . '/../../Model/EmailController.php';
include __DIR__ . '/../../Model/FrontRecommandationController.php';

$campagneC = new FrontCampagneController();
$donC = new DonController();
$emailC = new EmailController();
$recommandationC = new FrontRecommandationController();

$id_campagne = $_GET['id_campagne'] ?? null;
$campagne = null;

if ($id_campagne) {
  $campagne = $campagneC->getCampagne($id_campagne);
}

$message_success = '';
$message_erreur = '';
$result = null;
$email_donateur = '';

$afficher_etape_whatsapp = false;
$whatsapp_code_genere = '';

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
    $email_donateur = $_POST['email_donateur'] ?? '';
    $nom_donateur = $_POST['nom_donateur'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $whatsapp_code = $_POST['whatsapp_code'] ?? '';

    if ($montant > 0 && !empty($methode_paiment) && $campagne && !empty($email_donateur) && !empty($nom_donateur)) {

      $result = $donC->faireDonAvecRecommandations($id_campagne, $montant, $message, $methode_paiment, $email_donateur, $nom_donateur, $telephone, $whatsapp_code);

      if ($result === "whatsapp_code_required") {
        $whatsapp_code_genere = $_SESSION['don_whatsapp_code'] ?? '123456';
        $message_erreur = "Vérification WhatsApp requise.";
        $afficher_etape_whatsapp = true;

      } elseif ($result) {
        $message_success = "Merci pour votre don de " . number_format($montant, 2) . " TND !";

        $campagne = $campagneC->getCampagne($id_campagne);

        $email_envoye = $emailC->envoyerRecuDon($email_donateur, $nom_donateur, $montant, $result, $campagne['titre']);

        if (!$email_envoye) {
          $message_erreur = "Votre don a été enregistré mais l'envoi du reçu a échoué.";
        }

        $_SESSION['don_success'] = true;
        $_SESSION['donateur_email'] = $email_donateur;
        $_SESSION['donateur_nom'] = $nom_donateur;

        setcookie('donateur_email', $email_donateur, time() + (30 * 24 * 60 * 60), '/');
        setcookie('donateur_nom', $nom_donateur, time() + (30 * 24 * 60 * 60), '/');

        header('Location: listCampagnes.php?don_success=1');
        exit;
      } else {
        $message_erreur = "Erreur lors de l'enregistrement du don.";
      }
    }
  }
}

$email_donateur_actuel = $email_donateur ?? ($_POST['email_donateur'] ?? '');
$nom_donateur_actuel = $nom_donateur ?? ($_POST['nom_donateur'] ?? '');

if (!empty($email_donateur_actuel) && !empty($nom_donateur_actuel)) {
  $recommandations = $recommandationC->genererRecommandationsPourDonateur($email_donateur_actuel, $nom_donateur_actuel, 3);
} else {
  $recommandations = $recommandationC->getRecommandationsPourVisiteur(3);
}
?>
<?php include_once 'templates/header.php'; ?>
<style>
  .form-etape {
    display: none;
  }

  .form-etape.active {
    display: block;
  }

  .progress-bar {
    height: 6px;
    background: #e0e0e0;
    border-radius: 3px;
    margin: 20px 0;
  }

  .progress {
    height: 100%;
    background: var(--moss);
    border-radius: 3px;
    transition: width 0.3s;
  }

  .etape-indicator {
    display: flex;
    justify-content: space-between;
    margin: 20px 0;
  }

  .etape {
    text-align: center;
    flex: 1;
  }

  .etape-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #e0e0e0;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 5px;
    font-weight: bold;
  }

  .etape.active .etape-number {
    background: var(--moss);
    color: white;
  }

  .payment-method {
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    margin: 10px 0;
    cursor: pointer;
    transition: all 0.3s;
  }

  .payment-method:hover {
    border-color: var(--moss);
  }

  .payment-method.selected {
    border-color: var(--moss);
    background: var(--light-sage);
  }

  .security-badge {
    background: #e8f5e8;
    border: 1px solid #4caf50;
    color: #2e7d32;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 0.8em;
    margin-left: 10px;
  }

  .whatsapp-box {
    background: #dcf8c6;
    border: 2px solid #25D366;
    border-radius: 15px;
    padding: 25px;
    margin: 20px 0;
  }

  .whatsapp-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
  }

  .whatsapp-status {
    background: white;
    border-radius: 10px;
    padding: 15px;
    margin: 15px 0;
    border-left: 4px solid #25D366;
  }

  .status-message {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
  }

  .timer {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #666;
    font-size: 0.9em;
  }

  .whatsapp-instructions {
    background: rgba(37, 211, 102, 0.1);
    border-radius: 10px;
    padding: 15px;
    margin: 15px 0;
  }

  .resend-section {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px dashed #ccc;
  }

  #countdown {
    color: #ff6b6b;
    font-weight: bold;
  }

  .whatsapp-icon {
    color: #25D366;
    font-size: 1.2em;
  }

  .error-message {
    color: #dc3545;
    font-size: 0.85rem;
    margin-top: 5px;
    padding: 5px 10px;
    background: #fff5f5;
    border-radius: 4px;
    border-left: 3px solid #dc3545;
  }

  .input-error {
    border-color: #dc3545 !important;
    background: #fff5f5 !important;
  }

  .form-group {
    position: relative;
  }

  .whatsapp-code-input {
    text-align: center;
    font-size: 1.5em !important;
    letter-spacing: 10px;
    font-weight: bold;
    font-family: monospace;
  }

  /* STYLES RECOMMANDATIONS */
  .recommendations-section {
    margin-top: 40px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 30px;
    border-radius: 15px;
    border-left: 5px solid #667eea;
  }

  .recommendations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-top: 20px;
  }

  .recommendation-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 2px solid transparent;
    position: relative;
  }

  .recommendation-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    border-color: #667eea;
  }

  .recommendation-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.8em;
    font-weight: bold;
    z-index: 2;
  }

  .progress-container {
    margin: 15px 0;
  }

  .progress-bar-custom {
    height: 8px;
    background: #e0e0e0;
    border-radius: 4px;
    overflow: hidden;
  }

  .progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #4CAF50, #45a049);
    border-radius: 4px;
    transition: width 0.5s ease;
  }

  .personalized-tag {
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: bold;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  @keyframes slideIn {
    from {
      opacity: 0;
      transform: translateY(20px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .recommendation-card {
    animation: slideIn 0.5s ease forwards;
  }
</style>
<div class="modal-backdrop" id="donationModal" style="display: block; position: relative; background: transparent;">
  <div class="modal" style="position: relative; margin: 2rem auto; max-width: 600px;">
    <div class="auth-decoration">
      <div class="auth-icon">
        <i class="fas fa-hand-holding-heart"></i>
      </div>
    </div>
    <div class="modal-body">

      <?php if ($message_success): ?>
        <div class="success-message">
          <i class="fas fa-check-circle"></i> <?= $message_success ?>
          <p>Un reçu a été envoyé à <?= htmlspecialchars($email_donateur) ?></p>
        </div>
      <?php endif; ?>

      <?php if ($message_erreur): ?>
        <div class="error-message">
          <i class="fas fa-exclamation-triangle"></i> <?= $message_erreur ?>
        </div>
      <?php endif; ?>

      <?php if ($campagne): ?>
        <h2 class="auth-title">Soutenir la Campagne</h2>
        <p class="auth-subtitle"><?= htmlspecialchars($campagne['titre']) ?></p>

        <div class="campagne-info">
          <p><strong>Objectif :</strong> <?= number_format($campagne['objectif_montant'], 2) ?> TND</p>
          <p><strong>Collecté :</strong> <?= number_format($campagne['montant_actuel'], 2) ?> TND</p>
          <div class="progress">
            <div class="progress-bar"
              style="width: <?= min($campagneC->getProgression($campagne['Id_campagne']), 100) ?>%"></div>
          </div>
        </div>

        <div class="etape-indicator">
          <div class="etape <?= !$afficher_etape_whatsapp ? 'active' : '' ?>">
            <div class="etape-number">1</div>
            <div>Information</div>
          </div>
          <div class="etape">
            <div class="etape-number">2</div>
            <div>Paiement</div>
          </div>
          <?php if ($afficher_etape_whatsapp): ?>
            <div class="etape active">
              <div class="etape-number">3</div>
              <div>WhatsApp</div>
            </div>
          <?php endif; ?>
        </div>

        <div class="progress-bar">
          <div class="progress" id="progressBar" style="width: <?= $afficher_etape_whatsapp ? '100%' : '50%' ?>">
          </div>
        </div>

        <form id="donationForm" method="POST">
          <input type="hidden" name="id_campagne" value="<?= $id_campagne ?>">
          <input type="hidden" name="methode_paiment" id="methodePaimentInput" value="carte">
          <input type="hidden" name="secure_token" value="don_secure_2024">

          <!-- ÉTAPE 1: Informations -->
          <div class="form-etape <?= !$afficher_etape_whatsapp ? 'active' : '' ?>" id="etape1">
            <h3><i class="fas fa-user"></i> Vos informations</h3>

            <div class="form-group">
              <label for="nom_donateur">
                <i class="fas fa-signature"></i>
                Nom complet *
              </label>
              <input id="nom_donateur" name="nom_donateur" class="input" type="text"
                value="<?= htmlspecialchars($_POST['nom_donateur'] ?? '') ?>" placeholder="Votre nom complet" required>
            </div>

            <div class="form-group">
              <label for="email_donateur">
                <i class="fas fa-envelope"></i>
                Adresse email *
              </label>
              <input id="email_donateur" name="email_donateur" class="input" type="email"
                value="<?= htmlspecialchars($_POST['email_donateur'] ?? '') ?>" placeholder="votre@email.com" required>
            </div>

            <div class="form-group">
              <label for="telephone">
                <i class="fab fa-whatsapp whatsapp-icon"></i>
                Numéro WhatsApp (pour vérification)
              </label>
              <input id="telephone" name="telephone" class="input" type="tel"
                value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>" placeholder="21612345678">
              <small style="color: #666;">Un code de vérification vous sera envoyé sur WhatsApp</small>
            </div>

            <div class="form-group">
              <label>
                <i class="fas fa-coins"></i>
                Montant de votre don (TND) *
              </label>
              <div class="checkbox-group" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                <label class="checkbox-label montant-option"
                  style="text-align: center; padding: 15px; border: 2px solid var(--moss); border-radius: var(--radius-sm); cursor: pointer; background-color: var(--light-sage);">
                  <input type="radio" name="montant" value="20" style="display: none;" required>
                  <div style="font-weight: bold; color: var(--moss);">20 TND</div>
                  <div class="text-muted small">Aide de base</div>
                </label>
                <label class="checkbox-label montant-option"
                  style="text-align: center; padding: 15px; border: 2px solid var(--light-sage); border-radius: var(--radius-sm); cursor: pointer;">
                  <input type="radio" name="montant" value="50" style="display: none;">
                  <div style="font-weight: bold; color: var(--moss);">50 TND</div>
                  <div class="text-muted small">Soutien régulier</div>
                </label>
                <label class="checkbox-label montant-option"
                  style="text-align: center; padding: 15px; border: 2px solid var(--light-sage); border-radius: var(--radius-sm); cursor: pointer;">
                  <input type="radio" name="montant" value="100" style="display: none;">
                  <div style="font-weight: bold; color: var(--moss);">100 TND</div>
                  <div class="text-muted small">Contribution</div>
                </label>
                <label class="checkbox-label montant-option"
                  style="text-align: center; padding: 15px; border: 2px solid var(--light-sage); border-radius: var(--radius-sm); cursor: pointer;">
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
              <input id="custom_amount" name="custom_amount" class="input" type="number" step="0.01" min="1"
                placeholder="Entrez le montant de votre choix">
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

            <div class="form-group">
              <label>
                <i class="fas fa-credit-card"></i>
                Méthode de paiement *
              </label>

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

              <div class="payment-method" onclick="selectPaymentMethod('virement')">
                <div style="display: flex; justify-content: between; align-items: center;">
                  <div>
                    <h4 style="margin: 0; display: flex; align-items: center;">
                      <i class="fas fa-university" style="margin-right: 10px; color: var(--moss);"></i>
                      Virement Bancaire
                    </h4>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9em;">Transfert direct depuis votre banque
                    </p>
                  </div>
                  <div class="payment-logos" style="display: flex; gap: 5px;">
                    <span style="font-size: 1.5em;">🏦</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="personal-message">
                <i class="fas fa-comment-heart"></i>
                Message d'encouragement (optionnel)
              </label>
              <textarea id="personal-message" name="message" class="textarea"
                placeholder="Laissez un message pour les bénéficiaires..."
                rows="3"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
            </div>

            <div class="security-notice" style="background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 15px 0;">
              <h4 style="margin: 0 0 10px 0; color: #1976d2;"><i class="fas fa-shield-alt"></i> Paiement 100% sécurisé
              </h4>
              <p style="margin: 0; font-size: 0.9em;">Vos données sont cryptées et protégées. Aucune information
                bancaire n'est stockée sur nos serveurs.</p>
            </div>

            <div class="form-footer">
              <button type="button" class="btn secondary" onclick="prevEtape(1)">
                <i class="fas fa-arrow-left"></i> Retour
              </button>
              <button type="button" class="btn primary" onclick="verifierEtEnvoyerWhatsApp()">
                Continuer <i class="fas fa-arrow-right"></i>
              </button>
            </div>
          </div>

          <!-- ÉTAPE 3: Vérification WhatsApp -->
          <?php if ($afficher_etape_whatsapp): ?>
            <div class="form-etape active" id="etape3">
              <h3><i class="fab fa-whatsapp whatsapp-icon"></i> Vérification WhatsApp</h3>

              <div class="whatsapp-box">
                <div class="whatsapp-header">
                  <i class="fab fa-whatsapp fa-2x whatsapp-icon"></i>
                  <h4>Vérification en cours</h4>
                </div>

                <div class="whatsapp-status">
                  <div class="status-message">
                    <i class="fas fa-check-circle whatsapp-icon"></i>
                    <span>Code WhatsApp envoyé à :</span>
                    <strong><?= htmlspecialchars($_POST['telephone'] ?? '') ?></strong>
                  </div>

                  <div class="timer">
                    <i class="fas fa-clock"></i>
                    <span id="countdown">10:00</span> restant(s)
                  </div>
                </div>

                <div class="whatsapp-instructions">
                  <p><strong>Vérifiez votre téléphone :</strong></p>
                  <p>Un message WhatsApp contenant un code à 6 chiffres vous a été envoyé.</p>

                  <div class="resend-section">
                    <p>Vous n'avez pas reçu le code ?</p>
                    <button type="button" class="btn small" onclick="resendWhatsAppCode()">
                      <i class="fas fa-redo"></i> Renvoyer le code
                    </button>
                  </div>
                </div>

                <div class="form-group">
                  <label for="whatsapp_code">
                    <i class="fas fa-key"></i>
                    Code de vérification WhatsApp *
                  </label>
                  <input type="text" name="whatsapp_code" id="whatsapp_code" class="input whatsapp-code-input"
                    placeholder="123456" maxlength="6" required autofocus>
                  <small>Code à 6 chiffres reçu sur WhatsApp (valide 10 minutes)</small>
                </div>

                <input type="hidden" name="nom_donateur" value="<?= htmlspecialchars($_POST['nom_donateur'] ?? '') ?>">
                <input type="hidden" name="email_donateur" value="<?= htmlspecialchars($_POST['email_donateur'] ?? '') ?>">
                <input type="hidden" name="telephone" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                <input type="hidden" name="montant" value="<?= htmlspecialchars($_POST['montant'] ?? '') ?>">
                <input type="hidden" name="custom_amount" value="<?= htmlspecialchars($_POST['custom_amount'] ?? '') ?>">
                <input type="hidden" name="message" value="<?= htmlspecialchars($_POST['message'] ?? '') ?>">
                <input type="hidden" name="methode_paiment"
                  value="<?= htmlspecialchars($_POST['methode_paiment'] ?? '') ?>">

                <div class="form-footer">
                  <button type="button" class="btn secondary" onclick="retourEtape2()">
                    <i class="fas fa-arrow-left"></i> Retour
                  </button>
                  <button type="submit" class="btn success">
                    <i class="fas fa-check-circle"></i> Vérifier et finaliser
                  </button>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </form>
      <?php else: ?>
        <div class="error-message" style="text-align: center; padding: 40px;">
          <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 20px;"></i>
          <h3>Campagne non trouvée</h3>
          <p>La campagne que vous cherchez n'existe pas ou a été supprimée.</p>
          <a href="listCampagnes.php" class="btn primary">Voir les campagnes</a>
        </div>
      <?php endif; ?>

      <?php
      $donsCampagne = $campagneC->getDonsParCampagne($id_campagne);

      if (!empty($donsCampagne)): ?>
        <div class="recent-dons">
          <h3>Dons récents pour cette campagne</h3>

          <?php foreach ($donsCampagne as $don): ?>
            <div class="don-item">
              <strong><?= htmlspecialchars($don['donateur_nom']) ?></strong>
              a donné <?= number_format($don['montant'], 0) ?> TND
              <small>le <?= date('d/m/Y', strtotime($don['date_don'])) ?></small>

              <?php if (!empty($don['message'])): ?>
                <p>"<?= htmlspecialchars($don['message']) ?>"</p>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- SECTION RECOMMANDATIONS PERSONNALISÉES -->
      <?php if (!empty($recommandations)): ?>
        <div class="recommendations-section">
          <h3 style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px; color: #333;">
            <i class="fas fa-lightbulb" style="color: #FFC107; font-size: 1.5em;"></i>
            <span>Campagnes qui pourraient vous intéresser</span>
          </h3>

          <p style="color: #666; margin-bottom: 25px; font-size: 0.95em;">
            <?php if (!empty($email_donateur_actuel)): ?>
              Basé sur vos centres d'intérêt
            <?php else: ?>
              Campagnes populaires du moment
            <?php endif; ?>
          </p>

          <div class="recommendations-grid">
            <?php foreach ($recommandations as $campagne_rec):
              $progression_rec = ($campagne_rec['montant_actuel'] / $campagne_rec['objectif_montant']) * 100;
              $jours_restants_rec = max(0, floor((strtotime($campagne_rec['date_fin']) - time()) / (60 * 60 * 24)));
              ?>
              <div class="recommendation-card">
                <?php if (!empty($email_donateur_actuel)): ?>
                  <div class="recommendation-badge">
                    <i class="fas fa-star"></i> Pour vous
                  </div>
                <?php endif; ?>

                <div style="display: flex; align-items: start; gap: 15px; margin-bottom: 15px;">
                  <?php if ($campagne_rec['image_campagne'] && !empty($campagne_rec['image_campagne'])): ?>
                    <img src="<?= htmlspecialchars($campagne_rec['image_campagne']) ?>"
                      alt="<?= htmlspecialchars($campagne_rec['titre']) ?>"
                      style="width: 70px; height: 70px; border-radius: 8px; object-fit: cover; border: 2px solid #f0f0f0;">
                  <?php else: ?>
                    <div
                      style="width: 70px; height: 70px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5em;">
                      <i class="fas fa-hand-holding-heart"></i>
                    </div>
                  <?php endif; ?>
                  <div style="flex: 1;">
                    <h4 style="margin: 0 0 8px 0; font-size: 1.1em; color: #333; line-height: 1.3;">
                      <?= htmlspecialchars($campagne_rec['titre']) ?>
                    </h4>
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                      <span class="personalized-tag">
                        <i class="fas fa-tag"></i> <?= htmlspecialchars($campagne_rec['categorie_impact']) ?>
                      </span>
                      <span
                        style="background: #fff3e0; color: #f57c00; padding: 4px 12px; border-radius: 20px; font-size: 0.85em; font-weight: bold;">
                        <i class="fas fa-clock"></i> <?= $jours_restants_rec ?> jours
                      </span>
                    </div>
                  </div>
                </div>

                <div style="background: #f8f9fa; padding: 12px; border-radius: 8px; margin: 15px 0;">
                  <div style="display: flex; justify-content: space-between; font-size: 0.9em; margin-bottom: 8px;">
                    <span style="color: #4CAF50; font-weight: bold;"><?= number_format($progression_rec, 1) ?>%</span>
                    <span style="color: #666;">
                      <?= number_format($campagne_rec['montant_actuel'] ?? 0, 0) ?> /
                      <?= number_format($campagne_rec['objectif_montant'] ?? 0, 0) ?> TND
                    </span>
                  </div>
                  <div class="progress-bar-custom">
                    <div class="progress-fill" style="width: <?= min($progression_rec, 100) ?>%"></div>
                  </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                  <a href="DonView.php?id_campagne=<?= $campagne_rec['Id_campagne'] ?>" class="btn primary"
                    style="flex: 1; text-align: center; padding: 10px; font-size: 0.95em; border-radius: 6px; background: #667eea; border-color: #667eea;">
                    <i class="fas fa-heart"></i> Soutenir cette campagne
                  </a>
                  <button class="btn ghost" style="padding: 10px 15px; border-radius: 6px;"
                    onclick="partagerRecommandation(<?= $campagne_rec['Id_campagne'] ?>, '<?= addslashes($campagne_rec['titre']) ?>')">
                    <i class="fas fa-share-alt"></i>
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <?php if (!empty($email_donateur_actuel)): ?>
            <div
              style="margin-top: 20px; padding: 15px; background: #e8f5e8; border-radius: 8px; border-left: 4px solid #4CAF50;">
              <p style="margin: 0; color: #2e7d32; font-size: 0.9em; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-info-circle"></i>
                Ces recommandations sont personnalisées selon vos dons précédents.
                <a href="javascript:void(0)" onclick="actualiserRecommandations()"
                  style="color: #1976d2; margin-left: auto; text-decoration: none;">
                  <i class="fas fa-sync-alt"></i> Actualiser
                </a>
              </p>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<footer class="site-footer">
  <!-- ... votre footer existant ... -->
</footer>
</div>

<script src="assets/js/script.js"></script>
<script>
  let currentEtape = <?= $afficher_etape_whatsapp ? 3 : 1 ?>;
  let selectedPaymentMethod = 'carte';
  let whatsappTimer = 600;

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

  function retourEtape2() {
    document.getElementById('etape3').style.display = 'none';
    document.getElementById('etape2').style.display = 'block';
    document.querySelectorAll('.etape')[2].classList.remove('active');
    document.querySelectorAll('.etape')[1].classList.add('active');
    document.getElementById('progressBar').style.width = '50%';
    currentEtape = 2;
  }

  function verifierEtEnvoyerWhatsApp() {
    if (validateEtape(2)) {
      const telephone = document.getElementById('telephone').value;

      if (telephone && telephone.trim() !== '') {
        document.getElementById('donationForm').submit();
      } else {
        document.getElementById('donationForm').submit();
      }
    }
  }

  function selectPaymentMethod(method) {
    selectedPaymentMethod = method;
    document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
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

    if (etape === 2) {
      const methode = document.getElementById('methodePaimentInput').value;
      if (!methode) {
        alert('Veuillez sélectionner une méthode de paiement');
        return false;
      }
    }

    return true;
  }

  document.querySelectorAll('.montant-option').forEach(option => {
    option.addEventListener('click', function () {
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

  function updateCountdown() {
    if (whatsappTimer <= 0) {
      document.getElementById('countdown').textContent = "00:00";
      document.getElementById('countdown').style.color = "red";
      return;
    }

    const minutes = Math.floor(whatsappTimer / 60);
    const seconds = whatsappTimer % 60;
    document.getElementById('countdown').textContent =
      `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

    whatsappTimer--;
    setTimeout(updateCountdown, 1000);
  }

  function resendWhatsAppCode() {
    const telephone = document.querySelector('input[name="telephone"]').value;

    if (!telephone) {
      alert('❌ Veuillez entrer un numéro de téléphone');
      return;
    }

    fetch('resend_whatsapp.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `telephone=${encodeURIComponent(telephone)}`
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('✅ Code WhatsApp renvoyé ! Vérifiez votre téléphone.');
          whatsappTimer = 600;
          updateCountdown();
        } else {
          alert('❌ Erreur : ' + (data.error || 'Impossible de renvoyer le code'));
        }
      })
      .catch(error => {
        alert('❌ Erreur de connexion au serveur');
      });
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('.montant-option:first-child').click();
    document.querySelector('.payment-method').click();

    <?php if ($afficher_etape_whatsapp): ?>
      updateCountdown();
    <?php endif; ?>

    const telInput = document.getElementById('telephone');
    if (telInput) {
      telInput.addEventListener('input', function () {
        this.value = this.value.replace(/[^\d]/g, '');
      });
    }

    const whatsappCodeInput = document.getElementById('whatsapp_code');
    if (whatsappCodeInput) {
      whatsappCodeInput.addEventListener('input', function () {
        this.value = this.value.replace(/[^\d]/g, '');
        if (this.value.length > 6) {
          this.value = this.value.substring(0, 6);
        }
      });
    }

    const donationForm = document.getElementById('donationForm');
    if (donationForm) {
      donationForm.addEventListener('submit', function (event) {
        if (!validateFinalForm()) {
          event.preventDefault();
          alert('❌ Veuillez corriger les erreurs dans le formulaire');
        }
      });
    }
  });

  function validateFinalForm() {
    let isValid = true;

    const nom = document.getElementById('nom_donateur');
    if (!nom || nom.value.trim() === '' || !/^[a-zA-ZàâäéèêëîïôöùûüÿçÀÂÄÉÈÊËÎÏÔÖÙÛÜŸÇ\s-]{2,}$/.test(nom.value.trim())) {
      showError(nom, 'Nom invalide (min. 2 lettres)');
      isValid = false;
    }

    const email = document.getElementById('email_donateur');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email || email.value.trim() === '' || !emailRegex.test(email.value.trim())) {
      showError(email, 'Email invalide');
      isValid = false;
    }

    const montantSelectionne = document.querySelector('input[name="montant"]:checked');
    if (!montantSelectionne) {
      alert('❌ Veuillez sélectionner un montant');
      isValid = false;
    } else if (montantSelectionne.value === 'custom') {
      const customMontant = document.getElementById('custom_amount');
      if (!customMontant || parseFloat(customMontant.value) < 1) {
        showError(customMontant, 'Montant minimum: 1 TND');
        isValid = false;
      }
    }

    return isValid;
  }

  function showError(input, message) {
    const existingError = input.parentNode.querySelector('.error-message');
    if (existingError) {
      existingError.remove();
    }

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;

    input.parentNode.appendChild(errorDiv);
    input.classList.add('input-error');
  }

  function partagerRecommandation(campagneId, titre) {
    const message = `Découvrez cette campagne sur ImpactAble : "${titre}"\n\nSoutenez cette cause importante avec moi !\n\n`;
    const url = window.location.origin + '/DonView.php?id_campagne=' + campagneId;

    if (navigator.share) {
      navigator.share({
        title: titre,
        text: message,
        url: url
      });
    } else {
      const shareText = message + url;
      navigator.clipboard.writeText(shareText).then(() => {
        alert('Lien copié dans le presse-papier ! Partagez-le avec vos amis.');
      });
    }
  }

  function actualiserRecommandations() {
    const btn = event.target;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    setTimeout(() => {
      location.reload();
    }, 1000);
  }

  function sauvegarderPreferencesDon(email, nom) {
    if (email && nom) {
      const expires = new Date();
      expires.setTime(expires.getTime() + (30 * 24 * 60 * 60 * 1000));
      document.cookie = `donateur_email=${encodeURIComponent(email)}; expires=${expires.toUTCString()}; path=/`;
      document.cookie = `donateur_nom=${encodeURIComponent(nom)}; expires=${expires.toUTCString()}; path=/`;

      localStorage.setItem('donateur_email', email);
      localStorage.setItem('donateur_nom', nom);

      console.log('Préférences sauvegardées pour:', email);
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('don_success')) {
      const emailInput = document.getElementById('email_donateur');
      const nomInput = document.getElementById('nom_donateur');

      if (emailInput && emailInput.value && nomInput && nomInput.value) {
        sauvegarderPreferencesDon(emailInput.value, nomInput.value);
      }
    }

    const cookies = document.cookie.split(';');
    let hasDonateurCookie = false;
    cookies.forEach(cookie => {
      if (cookie.trim().startsWith('donateur_email=')) {
        hasDonateurCookie = true;
      }
    });

    if (hasDonateurCookie) {
      console.log('Donateur reconnu via cookie');
    }
  });
</script>
</body>

</html>