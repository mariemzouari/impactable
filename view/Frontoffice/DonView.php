<?php
// faire-don.php - VERSION AVEC VERIFICATION TELEPHONIQUE
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
$result = null;
$don_id = null;
$code_envoye = false;
$etape = 1; // 1: formulaire, 2: vérification

// Si on a un don_id en session, on passe à l'étape de vérification
if (isset($_SESSION['don_en_attente'])) {
    $don_id = $_SESSION['don_en_attente'];
    $etape = 2;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Étape 1 : Soumission du formulaire de don
    if (isset($_POST['etape']) && $_POST['etape'] == 1) {
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

            // Nettoyer le téléphone (supprimer les espaces)
            $telephone = str_replace(' ', '', $telephone);
            
            // Retirer le +216 si présent
            $telephone = str_replace('+216', '', $telephone);
            
            // S'assurer que le numéro commence par 0
            if (substr($telephone, 0, 1) !== '0' && $telephone !== '') {
                $telephone = '0' . $telephone;
            }

            // Validation
            $erreurs = [];
            
            if (empty($nom_donateur) || strlen(trim($nom_donateur)) < 2) {
                $erreurs[] = "Le nom complet est requis (min. 2 caractères)";
            }
            
            if (empty($email_donateur) || !filter_var($email_donateur, FILTER_VALIDATE_EMAIL)) {
                $erreurs[] = "Une adresse email valide est requise";
            }
            
            // Validation téléphone améliorée
            if (empty($telephone)) {
                $erreurs[] = "Le numéro de téléphone est requis";
            } else {
                // Vérifier que ce n'est pas juste "0"
                if ($telephone === '0') {
                    $erreurs[] = "Veuillez entrer un numéro de téléphone complet";
                }
                // Vérifier le format : doit commencer par 0 et avoir 8 chiffres au total
                elseif (!preg_match('/^0[0-9]{7,8}$/', $telephone)) {
                    $erreurs[] = "Numéro invalide. Format: 0X XXX XXX (8 ou 9 chiffres)";
                }
                // Vérifier que tous les chiffres ne sont pas les mêmes (ex: 00000000)
                elseif (preg_match('/^0(.)\1+$/', $telephone)) {
                    $erreurs[] = "Numéro de téléphone invalide";
                }
            }
            
            if ($montant <= 0) {
                $erreurs[] = "Le montant doit être supérieur à 0";
            }
            
            if (empty($methode_paiment)) {
                $erreurs[] = "La méthode de paiement est requise";
            }
            
            if (!$campagne) {
                $erreurs[] = "Campagne non trouvée";
            }
            
            if (empty($erreurs)) {
                // Faire le don (avec téléphone)
                $result = $donC->faireDon($id_campagne, $montant, $message, $methode_paiment, 
                                         $email_donateur, $nom_donateur, $telephone);
                
                if ($result['success']) {
                    $don_id = $result['don_id'];
                    $_SESSION['don_en_attente'] = $don_id;
                    $_SESSION['don_email'] = $email_donateur;
                    $_SESSION['don_nom'] = $nom_donateur;
                    $_SESSION['don_montant'] = $montant;
                    $_SESSION['don_campagne_titre'] = $campagne['titre'];
                    $etape = 2;
                    $code_envoye = true;
                    $message_success = "Un code de vérification a été envoyé à votre téléphone.";
                } else {
                    $message_erreur = $result['message'] ?? "Une erreur est survenue lors du traitement de votre don.";
                }
            } else {
                $message_erreur = implode("<br>", $erreurs);
            }
        }
    }
    
    // Étape 2 : Vérification du code
    elseif (isset($_POST['etape']) && $_POST['etape'] == 2) {
        $don_id = $_POST['don_id'] ?? $_SESSION['don_en_attente'] ?? null;
        $code_saisi = '';
        
        // Récupérer le code soit du champ hidden, soit des inputs séparés
        if (isset($_POST['code_verification']) && is_string($_POST['code_verification'])) {
            $code_saisi = $_POST['code_verification'];
        } elseif (isset($_POST['code_verification']) && is_array($_POST['code_verification'])) {
            $code_saisi = implode('', $_POST['code_verification']);
        }
        
        if ($don_id && !empty($code_saisi)) {
            // Récupérer l'IP et user agent
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            
            // Vérifier le code
            $result = $donC->verifierDon($don_id, $code_saisi, $ip_address, $user_agent);
            
            if ($result['success']) {
                $message_success = "Merci ! Votre don a été confirmé avec succès.";
                
                // Envoyer l'email de confirmation finale
                if (isset($_SESSION['don_email'])) {
                    $email_envoye = $emailC->envoyerRecuDon(
                        $_SESSION['don_email'],
                        $_SESSION['don_nom'],
                        $_SESSION['don_montant'],
                        $don_id,
                        $_SESSION['don_campagne_titre']
                    );
                }
                
                // Nettoyer la session
                unset($_SESSION['don_en_attente']);
                unset($_SESSION['don_email']);
                unset($_SESSION['don_nom']);
                unset($_SESSION['don_montant']);
                unset($_SESSION['don_campagne_titre']);
                
                // Rediriger après 3 secondes
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'listCampagnes.php?don_success=1';
                    }, 3000);
                </script>";
            } else {
                $message_erreur = $result['message'];
                $etape = 2; // Reste à l'étape de vérification
            }
        } else {
            $message_erreur = "Veuillez entrer le code de vérification.";
            $etape = 2;
        }
    }
    
    // Demander un nouveau code
    elseif (isset($_POST['renvoyer_code'])) {
        $don_id = $_POST['don_id'] ?? $_SESSION['don_en_attente'] ?? null;
        
        if ($don_id) {
            $result = $donC->renvoyerCode($don_id);
            if ($result['success']) {
                $message_success = "Un nouveau code a été envoyé à votre téléphone.";
                $code_envoye = true;
            } else {
                $message_erreur = $result['message'];
            }
        }
        $etape = 2;
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
    .code-inputs { display: flex; gap: 10px; justify-content: center; margin: 20px 0; }
    .code-input { width: 40px; height: 50px; text-align: center; font-size: 1.5rem; font-weight: bold; 
                 border: 2px solid #ddd; border-radius: 5px; }
    .code-input:focus { border-color: var(--moss); outline: none; }
    .verification-step { text-align: center; padding: 20px; }
    .countdown { font-size: 0.9em; color: #666; margin: 10px 0; }
    .security-badge { background: #e8f5e8; border: 1px solid #4caf50; color: #2e7d32; padding: 8px 12px; border-radius: 20px; font-size: 0.8em; margin-left: 10px; }
  </style>
  <style>
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

.input-success {
    border-color: #28a745 !important;
}

.form-group {
    position: relative;
}

.phone-input {
    display: flex;
    align-items: center;
    gap: 10px;
}

.phone-prefix {
    background: #f0f0f0;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 5px 0 0 5px;
    font-weight: bold;
}

.phone-number {
    flex: 1;
    border-radius: 0 5px 5px 0;
}

.tel-hint {
    font-size: 0.8em;
    color: #666;
    margin-top: 5px;
}
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
          </div>
          <?php endif; ?>
          
          <?php if($message_erreur): ?>
          <div class="error-message">
            <i class="fas fa-exclamation-triangle"></i> <?= $message_erreur ?>
          </div>
          <?php endif; ?>

          <?php if($campagne): ?>
          
          <!-- Indicateur d'étapes -->
          <div class="etape-indicator">
            <div class="etape <?= $etape == 1 ? 'active' : '' ?>">
              <div class="etape-number">1</div>
              <div>Information</div>
            </div>
            <div class="etape <?= $etape == 2 ? 'active' : '' ?>">
              <div class="etape-number">2</div>
              <div>Vérification</div>
            </div>
            <div class="etape">
              <div class="etape-number">3</div>
              <div>Confirmation</div>
            </div>
          </div>

          <!-- Barre de progression -->
          <div class="progress-bar">
            <div class="progress" id="progressBar" style="width: <?= ($etape-1) * 33.33 ?>%"></div>
          </div>
          
          <?php if($etape == 1): ?>
          <!-- ÉTAPE 1: Formulaire de don -->
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
          
          <form id="donationForm" method="POST">
            <input type="hidden" name="etape" value="1">
            <input type="hidden" name="id_campagne" value="<?= $id_campagne ?>">
            <input type="hidden" name="methode_paiment" id="methodePaimentInput" value="carte">
            <input type="hidden" name="secure_token" value="don_secure_2024">

            <div class="form-group">
              <label for="nom_donateur">
                <i class="fas fa-signature"></i>
                Nom complet *
              </label>
              <input id="nom_donateur" name="nom_donateur" class="input" type="text" 
                     value="<?= htmlspecialchars($_POST['nom_donateur'] ?? '') ?>" 
                     placeholder="Votre nom complet">
              <div class="tel-hint">Minimum 2 caractères</div>
            </div>

            <div class="form-group">
              <label for="email_donateur">
                <i class="fas fa-envelope"></i>
                Adresse email *
              </label>
              <input id="email_donateur" name="email_donateur" class="input" type="email" 
                     value="<?= htmlspecialchars($_POST['email_donateur'] ?? '') ?>" 
                     placeholder="votre@email.com">
            </div>

            <div class="form-group">
              <label for="telephone">
                <i class="fas fa-phone"></i>
                Numéro de téléphone *
              </label>
              <div class="phone-input">
                <div class="phone-prefix">+216</div>
                <input id="telephone" name="telephone" class="input phone-number" type="tel" 
                       value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>" 
                       placeholder="Ex: 09 819 273"
                       oninput="formatPhoneNumber(this)">
              </div>
              <div class="tel-hint">Format: 0X XXX XXX (8 ou 9 chiffres au total)</div>
            </div>

            <!-- Montant du don -->
            <div class="form-group">
              <label>
                <i class="fas fa-coins"></i>
                Montant de votre don (TND) *
              </label>
              <div class="checkbox-group" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                <label class="checkbox-label montant-option" style="text-align: center; padding: 15px; border: 2px solid var(--moss); border-radius: var(--radius-sm); cursor: pointer; background-color: var(--light-sage);">
                  <input type="radio" name="montant" value="20" style="display: none;">
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

            <!-- Méthodes de paiement -->
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
              <p style="margin: 0; font-size: 0.9em;">Vos données sont cryptées et protégées. Un code de vérification sera envoyé à votre téléphone.</p>
            </div>

            <div class="form-footer">
              <a href="listCampagnes.php" class="btn secondary">
                <i class="fas fa-arrow-left"></i>
                Retour aux campagnes
              </a>
              <button type="submit" class="btn primary" onclick="return validateDonationForm()">
                <i class="fas fa-sms"></i> Envoyer le code de vérification
              </button>
            </div>
          </form>
          
          <?php elseif($etape == 2): ?>
          <!-- ÉTAPE 2: Vérification téléphonique -->
          <div class="verification-step">
            <h2 class="auth-title">Vérification téléphonique</h2>
            
            <?php if($code_envoye): ?>
            <div class="success-message">
              <i class="fas fa-check-circle"></i> Code envoyé avec succès !
            </div>
            <?php endif; ?>
            
            <div class="info-box" style="background: #f0f9ff; padding: 20px; border-radius: 10px; margin: 20px 0;">
              <h4><i class="fas fa-mobile-alt"></i> Vérification par SMS</h4>
              <p>Nous avons envoyé un code à 6 chiffres au numéro que vous avez fourni.</p>
              <p>Ce code est valable pendant 10 minutes.</p>
            </div>
            
            <form method="POST" id="verificationForm">
              <input type="hidden" name="etape" value="2">
              <input type="hidden" name="don_id" value="<?= $don_id ?>">
              <input type="hidden" name="code_verification" id="full_code">
              
              <div class="form-group">
                <label for="code_verification">
                  <i class="fas fa-key"></i>
                  Code de vérification *
                </label>
                <div class="code-inputs">
                  <input type="text" name="code1" class="code-input" maxlength="1" oninput="moveToNext(this, 1)">
                  <input type="text" name="code2" class="code-input" maxlength="1" oninput="moveToNext(this, 2)">
                  <input type="text" name="code3" class="code-input" maxlength="1" oninput="moveToNext(this, 3)">
                  <input type="text" name="code4" class="code-input" maxlength="1" oninput="moveToNext(this, 4)">
                  <input type="text" name="code5" class="code-input" maxlength="1" oninput="moveToNext(this, 5)">
                  <input type="text" name="code6" class="code-input" maxlength="1" oninput="moveToNext(this, 6)">
                </div>
              </div>
              
              <div class="countdown" id="countdown">
                Code valable pendant: <span id="timer">10:00</span>
              </div>
              
              <div class="form-footer" style="justify-content: center; gap: 20px;">
                <form method="POST" style="display: inline;">
                  <input type="hidden" name="renvoyer_code" value="1">
                  <input type="hidden" name="don_id" value="<?= $don_id ?>">
                  <button type="submit" class="btn secondary">
                    <i class="fas fa-redo"></i> Renvoyer le code
                  </button>
                </form>
                <button type="submit" class="btn primary" onclick="return validateVerificationForm()">
                  <i class="fas fa-check-circle"></i> Vérifier le code
                </button>
              </div>
            </form>
            
            <div class="security-notice" style="background: #fff8e1; padding: 15px; border-radius: 5px; margin-top: 20px;">
              <h4><i class="fas fa-exclamation-triangle"></i> Important</h4>
              <p style="margin: 5px 0; font-size: 0.9em;">• Le code est valable 10 minutes</p>
              <p style="margin: 5px 0; font-size: 0.9em;">• 3 tentatives maximum avant blocage</p>
              <p style="margin: 5px 0; font-size: 0.9em;">• Contactez-nous si vous ne recevez pas le code</p>
            </div>
          </div>
          <?php endif; ?>
          
          <!-- Affichage des dons récents -->
          <?php
          $donsCampagne = $campagneC->getDonsParCampagne($id_campagne);
          if(!empty($donsCampagne)): ?>
          <div class="recent-dons" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <h4>Dons récents pour cette campagne</h4>
            
            <?php foreach($donsCampagne as $don): ?>
            <div class="don-item" style="background: #f9f9f9; padding: 10px; margin: 5px 0; border-radius: 5px;">
              <strong><?= htmlspecialchars($don['donateur_nom']) ?></strong>
              a donné <?= number_format($don['montant'], 0) ?> TND
              <small>le <?= date('d/m/Y', strtotime($don['date_don'])) ?></small>
              
              <?php if(!empty($don['message'])): ?>
              <p style="margin: 5px 0 0 20px; font-style: italic;">"<?= htmlspecialchars($don['message']) ?>"</p>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          
          <?php else: ?>
          <!-- Campagne non trouvée -->
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

  // Sélection méthode de paiement
  function selectPaymentMethod(method) {
    document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    document.getElementById('methodePaimentInput').value = method;
  }

  // Formatage automatique du numéro de téléphone
  function formatPhoneNumber(input) {
    // Supprimer tout sauf les chiffres
    let value = input.value.replace(/[^0-9]/g, '');
    
    // Ajouter automatiquement le 0 si manquant et non vide
    if (value.length > 0 && value.charAt(0) !== '0') {
      value = '0' + value;
    }
    
    // Limiter à 9 chiffres maximum (0 + 8 chiffres)
    if (value.length > 9) {
      value = value.substring(0, 9);
    }
    
    // Formatage avec espaces
    let formattedValue = '';
    if (value.length > 0) {
      formattedValue = value.charAt(0); // Le 0
    }
    if (value.length > 1) {
      formattedValue += ' ' + value.substring(1, 2); // Premier chiffre après le 0
    }
    if (value.length > 2) {
      formattedValue += ' ' + value.substring(2, 5); // 3 chiffres suivants
    }
    if (value.length > 5) {
      formattedValue += ' ' + value.substring(5, 8); // 3 derniers chiffres
    }
    if (value.length > 8) {
      formattedValue += ' ' + value.substring(8); // Dernier chiffre si 9 chiffres
    }
    
    input.value = formattedValue.trim();
    
    // Validation visuelle
    const cleanValue = value.replace(/\s/g, '');
    if (cleanValue === '0') {
      input.style.borderColor = '#dc3545';
    } else if (/^0[0-9]{7,8}$/.test(cleanValue)) {
      input.style.borderColor = '#28a745';
    } else if (cleanValue.length > 0) {
      input.style.borderColor = '#ffc107';
    } else {
      input.style.borderColor = '';
    }
  }

  // Gestion des inputs de code
  function moveToNext(input, currentIndex) {
    const inputs = document.querySelectorAll('.code-input');
    
    // Vérifier si c'est un chiffre
    if (/^[0-9]$/.test(input.value)) {
      // Passer au prochain input
      if (currentIndex < inputs.length) {
        inputs[currentIndex].focus();
      }
    } else {
      input.value = '';
    }
    
    // Mettre à jour le champ hidden
    updateFullCode();
  }

  function updateFullCode() {
    const inputs = document.querySelectorAll('.code-input');
    let fullCode = '';
    inputs.forEach(input => {
      fullCode += input.value;
    });
    document.getElementById('full_code').value = fullCode;
  }

  // Timer pour le code
  function startTimer(duration, display) {
    let timer = duration, minutes, seconds;
    const interval = setInterval(function () {
      minutes = parseInt(timer / 60, 10);
      seconds = parseInt(timer % 60, 10);

      minutes = minutes < 10 ? "0" + minutes : minutes;
      seconds = seconds < 10 ? "0" + seconds : seconds;

      display.textContent = minutes + ":" + seconds;

      if (--timer < 0) {
        clearInterval(interval);
        display.textContent = "EXPIRÉ";
        display.style.color = "red";
        
        // Désactiver le formulaire
        document.querySelectorAll('.code-input').forEach(input => {
          input.disabled = true;
        });
        document.querySelector('button[type="submit"]').disabled = true;
      }
    }, 1000);
  }

  // Démarrer le timer quand on est à l'étape 2
  <?php if($etape == 2): ?>
  window.onload = function () {
    const display = document.querySelector('#timer');
    startTimer(600, display); // 10 minutes
  };
  <?php endif; ?>

  // Validation du formulaire de don
  function validateDonationForm() {
    const nom = document.getElementById('nom_donateur');
    const email = document.getElementById('email_donateur');
    const telephone = document.getElementById('telephone');
    const montantSelectionne = document.querySelector('input[name="montant"]:checked');
    const methodePaiment = document.getElementById('methodePaimentInput');
    
    let erreurs = [];
    
    // Valider nom
    if (!nom.value.trim()) {
      erreurs.push("Le nom complet est requis");
      nom.style.borderColor = '#dc3545';
    } else if (nom.value.trim().length < 2) {
      erreurs.push("Le nom doit avoir au moins 2 caractères");
      nom.style.borderColor = '#dc3545';
    } else {
      nom.style.borderColor = '';
    }
    
    // Valider email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email.value.trim()) {
      erreurs.push("L'adresse email est requise");
      email.style.borderColor = '#dc3545';
    } else if (!emailRegex.test(email.value)) {
      erreurs.push("L'adresse email n'est pas valide");
      email.style.borderColor = '#dc3545';
    } else {
      email.style.borderColor = '';
    }
    
    // Valider téléphone
    const phoneValue = telephone.value.replace(/\s/g, '');
    if (!phoneValue) {
      erreurs.push("Le numéro de téléphone est requis");
      telephone.style.borderColor = '#dc3545';
    } else if (phoneValue === '0') {
      erreurs.push("Veuillez entrer un numéro de téléphone complet, pas juste '0'");
      telephone.style.borderColor = '#dc3545';
    } else if (!/^0[0-9]{7,8}$/.test(phoneValue)) {
      erreurs.push("Numéro invalide. Format: 0X XXX XXX (8 ou 9 chiffres)");
      telephone.style.borderColor = '#dc3545';
    } else {
      telephone.style.borderColor = '';
    }
    
    // Valider montant
    if (!montantSelectionne) {
      erreurs.push("Veuillez sélectionner un montant");
    } else if (montantSelectionne.value === 'custom') {
      const customMontant = document.getElementById('custom_amount');
      if (!customMontant.value || parseFloat(customMontant.value) < 1) {
        erreurs.push("Le montant personnalisé doit être d'au moins 1 TND");
        customMontant.style.borderColor = '#dc3545';
      } else {
        customMontant.style.borderColor = '';
      }
    }
    
    // Valider méthode de paiement
    if (!methodePaiment.value) {
      erreurs.push("Veuillez sélectionner une méthode de paiement");
    }
    
    if (erreurs.length > 0) {
      alert("Veuillez corriger les erreurs suivantes:\n\n" + erreurs.join("\n"));
      return false;
    }
    
    return true;
  }

  // Validation du formulaire de vérification
  function validateVerificationForm() {
    const inputs = document.querySelectorAll('.code-input');
    let codeComplet = '';
    
    inputs.forEach(input => {
      codeComplet += input.value;
    });
    
    if (codeComplet.length !== 6) {
      alert("Veuillez entrer le code complet à 6 chiffres");
      return false;
    }
    
    if (!/^\d{6}$/.test(codeComplet)) {
      alert("Le code doit contenir uniquement des chiffres");
      return false;
    }
    
    return true;
  }

  // Initialisation
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.montant-option:first-child').click();
    document.querySelector('.payment-method.selected').click();
    
    // Formater le téléphone si déjà rempli
    const telInput = document.getElementById('telephone');
    if (telInput && telInput.value) {
      formatPhoneNumber(telInput);
    }
  });
  </script>
</body>
</html>