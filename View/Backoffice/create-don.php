<?php
session_start();
include_once __DIR__ . '/../../controller/DonController.php';
include_once __DIR__ . '/../../controller/CampagneController.php';
include_once __DIR__ . '/../../controller/FrontCampagneController.php';

$donController = new DonController();
$campagneController = new CampagneController();
$frontCampagneController = new FrontCampagneController();

// Récupérer les campagnes actives
$campagnes = $campagneController->getAllCampagnes();
$campagnesList = $campagnes ? $campagnes->fetchAll(PDO::FETCH_ASSOC) : [];

$error = '';
$success = '';
$don_id = null;
$sms_code_generated = '';
$afficher_etape_sms = false;

// Variables pour le SMS (comme dans DonView.php)
if (!isset($_SESSION['don_sms_code'])) {
    // Générer un code de test comme dans la frontoffice
    $_SESSION['don_sms_code'] = '123456'; // Code de test fixe comme dans DonView.php
    $_SESSION['don_sms_time'] = time();
}

$sms_code_genere = $_SESSION['don_sms_code'] ?? '123456';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation
    $required = ['id_campagne', 'montant', 'nom_donateur', 'email_donateur', 'methode_paiment'];
    $missing = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $missing[] = $field;
        }
    }
    
    if (!empty($missing)) {
        $error = "Champs obligatoires manquants: " . implode(', ', $missing);
    } else {
        try {
            // Récupérer les données du formulaire
            $id_campagne = $_POST['id_campagne'];
            $montant = $_POST['montant'];
            $message = $_POST['message'] ?? '';
            $methode_paiment = $_POST['methode_paiment'];
            $email_donateur = $_POST['email_donateur'];
            $nom_donateur = $_POST['nom_donateur'];
            $telephone = $_POST['telephone'] ?? '';
            $sms_code = $_POST['sms_code'] ?? '';
            $statut = $_POST['statut'] ?? 'confirmé';
            
            // LOGIQUE SMS IDENTIQUE À LA FRONTOFFICE
            if (!empty($telephone)) {
                // Si pas de code fourni, on en génère un (comme dans DonView.php)
                if (empty($sms_code)) {
                    // Générer un code à 6 chiffres comme dans la frontoffice
                    $sms_code_generated = $_SESSION['don_sms_code'];
                    $error = "Code SMS requis. Code de test: <strong>$sms_code_generated</strong>";
                    $afficher_etape_sms = true;
                    
                    // Stocker les données du formulaire en session pour les réafficher
                    $_SESSION['don_form_data'] = [
                        'id_campagne' => $id_campagne,
                        'montant' => $montant,
                        'message' => $message,
                        'methode_paiment' => $methode_paiment,
                        'email_donateur' => $email_donateur,
                        'nom_donateur' => $nom_donateur,
                        'telephone' => $telephone,
                        'statut' => $statut
                    ];
                    
                    // Ne pas créer le don maintenant, attendre le code SMS
                    $don_id = null;
                } else {
                    // Vérifier le code saisi (comme dans DonController.php)
                    $code_attendu = $_SESSION['don_sms_code'] ?? '';
                    $temps_code = $_SESSION['don_sms_time'] ?? 0;
                    
                    if ($sms_code !== $code_attendu) {
                        $error = "Code SMS incorrect. Code attendu: $code_attendu";
                        $afficher_etape_sms = true;
                        $sms_code_generated = $code_attendu;
                    } elseif ((time() - $temps_code) > 600) {
                        $error = "Code SMS expiré. Veuillez générer un nouveau code.";
                        $afficher_etape_sms = true;
                        // Regénérer un code
                        $_SESSION['don_sms_code'] = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                        $_SESSION['don_sms_time'] = time();
                        $sms_code_generated = $_SESSION['don_sms_code'];
                    } else {
                        // Code SMS valide
                        error_log("✅ Code SMS vérifié dans admin: $sms_code");
                        
                        // Nettoyer la session SMS
                        unset($_SESSION['don_sms_code']);
                        unset($_SESSION['don_sms_time']);
                        unset($_SESSION['don_form_data']);
                    }
                }
            }
            
            // Si pas d'erreur SMS, créer le don
            if (!$error || ($error && strpos($error, "Code SMS requis") === false)) {
                // Si on a des données en session (après étape SMS), les utiliser
                if (isset($_SESSION['don_form_data']) && empty($sms_code)) {
                    $form_data = $_SESSION['don_form_data'];
                    $id_campagne = $form_data['id_campagne'];
                    $montant = $form_data['montant'];
                    $message = $form_data['message'];
                    $methode_paiment = $form_data['methode_paiment'];
                    $email_donateur = $form_data['email_donateur'];
                    $nom_donateur = $form_data['nom_donateur'];
                    $telephone = $form_data['telephone'];
                    $statut = $form_data['statut'];
                }
                
                if (!$afficher_etape_sms) {
                    // Créer le don via le controller avec SMS
                    $don_id = $donController->faireDon(
                        $id_campagne,
                        $montant,
                        $message,
                        $methode_paiment,
                        $email_donateur,
                        $nom_donateur,
                        $telephone,
                        $sms_code
                    );
                    
                    if ($don_id) {
                        // Si statut spécifié différent, mettre à jour
                        if ($statut !== 'confirmé') {
                            $db = config::getConnexion();
                            $updateQuery = "UPDATE don SET statut = ? WHERE Id_don = ?";
                            $updateStmt = $db->prepare($updateQuery);
                            $updateStmt->execute([$statut, $don_id]);
                            
                            // Si statut changé, actualiser le montant de la campagne
                            if ($statut !== 'confirmé') {
                                $frontCampagneController->actualiserMontantCampagne($id_campagne);
                            }
                        }
                        
                        $success = "Don créé avec succès ! ID: $don_id";
                        
                        // Envoyer l'email si demandé
                        if (isset($_POST['envoyer_email']) && $_POST['envoyer_email'] == 'on') {
                            $campagne = $campagneController->getCampagne($id_campagne);
                            if ($campagne) {
                                include_once __DIR__ . '/../../controller/EmailController.php';
                                $emailC = new EmailController();
                                $email_envoye = $emailC->envoyerRecuDon(
                                    $email_donateur, 
                                    $nom_donateur, 
                                    $montant, 
                                    $don_id, 
                                    $campagne['titre']
                                );
                                
                                if ($email_envoye) {
                                    $success .= "<br>Un email de confirmation a été envoyé à $email_donateur";
                                } else {
                                    $error .= "<br>Le don a été créé mais l'email n'a pas pu être envoyé.";
                                }
                            }
                        }
                        
                        // Réinitialiser le formulaire si succès complet
                        if (empty($error)) {
                            $_POST = [];
                            $telephone = '';
                            $sms_code = '';
                            unset($_SESSION['don_form_data']);
                        }
                    } else {
                        $error = "Erreur lors de la création du don";
                    }
                }
            }
            
        } catch (Exception $e) {
            $error = "Erreur: " . $e->getMessage();
        }
    }
}

// Si on revient après étape SMS, pré-remplir les champs
if (isset($_SESSION['don_form_data']) && !$afficher_etape_sms) {
    $form_data = $_SESSION['don_form_data'];
    $_POST['id_campagne'] = $form_data['id_campagne'];
    $_POST['montant'] = $form_data['montant'];
    $_POST['message'] = $form_data['message'];
    $_POST['methode_paiment'] = $form_data['methode_paiment'];
    $_POST['email_donateur'] = $form_data['email_donateur'];
    $_POST['nom_donateur'] = $form_data['nom_donateur'];
    $_POST['telephone'] = $form_data['telephone'];
    $_POST['statut'] = $form_data['statut'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactAble — Créer un Don</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .campagne-preview {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-top: 10px;
            display: none;
        }
        
        .campagne-preview.active {
            display: block;
        }
        
        .campagne-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }
        
        .sms-section {
            background: #f8f9fa;
            border: 2px solid var(--moss);
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .sms-code-display {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            font-family: monospace;
            font-size: 1.5em;
            text-align: center;
            letter-spacing: 3px;
        }
        
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
        
        .sms-hint {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="admin-logo">
                    <img src="assets/images/logo.png" alt="ImpactAble" class="admin-logo-image">
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-title">Gestion de contenu</div>
                    <a href="Ges_utilisateurs.php" class="sidebar-link">
                        <i class="fas fa-users"></i>
                        <span>Utilisateurs</span>
                    </a>
                    <a href="index.php?action=admin-dashboard" class="sidebar-link">
                        <i class="fas fa-briefcase"></i>
                        <span>Opportunités</span>
                    </a>
                    <a href="evenment_back.php" class="sidebar-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Événements</span>
                    </a>
                    
                    <div class="sidebar-dropdown">
                        <a href="#" class="sidebar-link dropdown-toggle" aria-expanded="false">
                            <i class="fas fa-hand-holding-heart"></i>
                            <span>Campagnes</span>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <div class="sidebar-submenu">
                            <a href="list-camp.php" class="submenu-link">
                                <i class="fas fa-list"></i>
                                <span>Toutes les campagnes</span>
                            </a>
                            <a href="addCampagne.php" class="submenu-link">
                                <i class="fas fa-plus"></i>
                                <span>Nouvelle campagne</span>
                            </a>
                            <a href="Calendar.php" class="submenu-link">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Calendrier</span>
                            </a>
                            <a href="stats_dashboard.php" class="submenu-link">
                                <i class="fas fa-chart-bar"></i>
                                <span>Statistiques</span>
                            </a>
                            <a href="referral.php" class="submenu-link">
                                <i class="fas fa-user-friends"></i>
                                <span>Parrainage</span>
                            </a>
                        </div>
                    </div>

                    <a href="list-don.php" class="sidebar-link active">
                        <i class="fas fa-donate"></i>
                        <span>Dons</span>
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
                        <h4>Admin User</h4>
                        <p>Administrateur</p>
                    </div>
                </div>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <div>
                    <h2>Créer un Nouveau Don</h2>
                    <p class="text-muted">Ajouter manuellement un don pour une campagne</p>
                </div>
                <div class="header-actions">
                    <a href="list-don.php" class="btn secondary">
                        <i class="fas fa-arrow-left"></i>
                        Retour aux dons
                    </a>
                </div>
            </header>

            <div class="admin-content">
                <div class="content-card">
                    <div class="card-header">
                        <h3>Informations du don</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert success">
                                <i class="fas fa-check-circle"></i>
                                <?php echo $success; ?>
                                <?php if ($don_id): ?>
                                    <a href="show-don.php?id=<?php echo $don_id; ?>" class="btn small" style="margin-left: 10px;">
                                        Voir le don
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert error">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Indicateur d'étapes pour le SMS -->
                        <?php if ($afficher_etape_sms): ?>
                        <div class="etape-indicator">
                            <div class="etape">
                                <div class="etape-number">1</div>
                                <div>Information</div>
                            </div>
                            <div class="etape active">
                                <div class="etape-number">2</div>
                                <div>SMS</div>
                            </div>
                        </div>
                        
                        <div class="progress-bar">
                            <div class="progress" style="width: 100%"></div>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="form-container" id="donForm">
                            <!-- ÉTAPE 1: Informations de base (masquée si étape SMS) -->
                            <div class="form-etape <?= !$afficher_etape_sms ? 'active' : '' ?>" id="etape1">
                                <div class="form-grid">
                                    <!-- Campagne -->
                                    <div class="form-group">
                                        <label for="id_campagne" class="required">Campagne</label>
                                        <select name="id_campagne" 
                                                id="id_campagne" 
                                                class="select" 
                                                required
                                                onchange="afficherInfosCampagne(this.value)">
                                            <option value="">Sélectionner une campagne</option>
                                            <?php foreach ($campagnesList as $campagne): ?>
                                                <?php if ($campagne['statut'] !== 'terminée'): ?>
                                                    <option value="<?php echo $campagne['Id_campagne']; ?>"
                                                            data-objectif="<?php echo $campagne['objectif_montant']; ?>"
                                                            data-actuel="<?php echo $campagne['montant_actuel']; ?>"
                                                            <?php echo ($_POST['id_campagne'] ?? '') == $campagne['Id_campagne'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($campagne['titre']); ?>
                                                        (<?php echo number_format($campagne['objectif_montant'], 0, ',', ' '); ?> TND)
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                        
                                        <!-- Prévisualisation de la campagne -->
                                        <div id="campagnePreview" class="campagne-preview">
                                            <strong id="campagneTitre"></strong>
                                            <div class="campagne-info">
                                                <div>
                                                    <small>Objectif:</small><br>
                                                    <span id="campagneObjectif"></span>
                                                </div>
                                                <div>
                                                    <small>Collecté:</small><br>
                                                    <span id="campagneCollecte"></span>
                                                </div>
                                            </div>
                                            <div style="margin-top: 10px;">
                                                <small>Progression:</small>
                                                <div style="background: #f0f0f0; height: 5px; border-radius: 3px;">
                                                    <div id="campagneProgression" style="background: var(--sage); height: 100%; border-radius: 3px; width: 0%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Montant -->
                                    <div class="form-group">
                                        <label for="montant" class="required">Montant (TND)</label>
                                        <input type="number" 
                                               name="montant" 
                                               id="montant" 
                                               class="input"
                                               value="<?php echo $_POST['montant'] ?? ''; ?>"
                                               step="0.01"
                                               min="1"
                                               placeholder="Ex: 100.50"
                                               required>
                                        <small class="text-muted">Minimum: 1 TND</small>
                                    </div>
                                    
                                    <!-- Méthode de paiement -->
                                    <div class="form-group">
                                        <label for="methode_paiment" class="required">Méthode de paiement</label>
                                        <select name="methode_paiment" id="methode_paiment" class="select" required>
                                            <option value="">Sélectionner</option>
                                            <option value="carte" <?php echo ($_POST['methode_paiment'] ?? '') == 'carte' ? 'selected' : ''; ?>>Carte bancaire</option>
                                            <option value="paypal" <?php echo ($_POST['methode_paiment'] ?? '') == 'paypal' ? 'selected' : ''; ?>>PayPal</option>
                                            <option value="virement" <?php echo ($_POST['methode_paiment'] ?? '') == 'virement' ? 'selected' : ''; ?>>Virement bancaire</option>
                                            <option value="especes" <?php echo ($_POST['methode_paiment'] ?? '') == 'especes' ? 'selected' : ''; ?>>Espèces</option>
                                            <option value="cheque" <?php echo ($_POST['methode_paiment'] ?? '') == 'cheque' ? 'selected' : ''; ?>>Chèque</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Statut -->
                                    <div class="form-group">
                                        <label for="statut" class="required">Statut</label>
                                        <select name="statut" id="statut" class="select" required>
                                            <option value="confirmé" <?php echo ($_POST['statut'] ?? 'confirmé') == 'confirmé' ? 'selected' : ''; ?>>Confirmé</option>
                                            <option value="en_attente" <?php echo ($_POST['statut'] ?? '') == 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                                            <option value="annulé" <?php echo ($_POST['statut'] ?? '') == 'annulé' ? 'selected' : ''; ?>>Annulé</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Donateur -->
                                    <div class="form-group">
                                        <label for="nom_donateur" class="required">Nom du donateur</label>
                                        <input type="text" 
                                               name="nom_donateur" 
                                               id="nom_donateur"
                                               class="input"
                                               value="<?php echo $_POST['nom_donateur'] ?? ''; ?>"
                                               placeholder="Nom complet"
                                               required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="email_donateur" class="required">Email du donateur</label>
                                        <input type="email" 
                                               name="email_donateur" 
                                               id="email_donateur"
                                               class="input"
                                               value="<?php echo $_POST['email_donateur'] ?? ''; ?>"
                                               placeholder="email@exemple.com"
                                               required>
                                    </div>
                                    
                                    <!-- Téléphone -->
                                    <div class="form-group">
                                        <label for="telephone">
                                            <i class="fas fa-mobile-alt"></i>
                                            Numéro de téléphone (pour SMS)
                                        </label>
                                        <input type="tel" 
                                               name="telephone" 
                                               id="telephone"
                                               class="input"
                                               value="<?php echo $_POST['telephone'] ?? ''; ?>"
                                               placeholder="21612345678">
                                        <small class="text-muted">
                                            Un code SMS sera envoyé pour vérification
                                        </small>
                                    </div>
                                    
                                    <!-- Message -->
                                    <div class="form-group full-width">
                                        <label for="message">Message du donateur</label>
                                        <textarea name="message" 
                                                  id="message" 
                                                  class="textarea" 
                                                  rows="3"
                                                  placeholder="Message optionnel..."><?php echo $_POST['message'] ?? ''; ?></textarea>
                                    </div>
                                    
                                    <!-- Envoyer l'email -->
                                    <div class="form-group full-width" style="background: #e8f5e8; padding: 15px; border-radius: 4px;">
                                        <label style="display: flex; align-items: center; gap: 10px;">
                                            <input type="checkbox" 
                                                   name="envoyer_email" 
                                                   id="envoyer_email" 
                                                   checked>
                                            <span>
                                                <i class="fas fa-envelope"></i>
                                                Envoyer un email de confirmation au donateur
                                            </span>
                                        </label>
                                        <small class="text-muted">
                                            Un reçu électronique sera envoyé à l'adresse email fournie.
                                        </small>
                                    </div>
                                    
                                    <div class="sms-hint">
                                        <strong><i class="fas fa-info-circle"></i> Information SMS :</strong>
                                        <p style="margin: 5px 0;">
                                            Si un numéro de téléphone est fourni, un code SMS sera requis.
                                            <br>
                                            <strong>Code de test : 123456</strong> (comme dans la frontoffice)
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="form-footer">
                                    <button type="reset" class="btn secondary">
                                        <i class="fas fa-redo"></i>
                                        Réinitialiser
                                    </button>
                                    <button type="submit" class="btn primary">
                                        <i class="fas fa-plus-circle"></i>
                                        Créer le don
                                    </button>
                                </div>
                            </div>

                            <!-- ÉTAPE 2: Vérification SMS (seulement si nécessaire) -->
                            <?php if($afficher_etape_sms): ?>
                            <div class="form-etape active" id="etape2">
                                <div class="sms-section">
                                    <h3><i class="fas fa-sms"></i> Vérification par SMS</h3>
                                    
                                    <p>Un code SMS a été généré pour le numéro: 
                                        <strong><?php echo htmlspecialchars($_POST['telephone'] ?? ''); ?></strong>
                                    </p>
                                    
                                    <div class="sms-code-display">
                                        <i class="fas fa-key"></i> CODE DE TEST : <?php echo $sms_code_genere; ?>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="sms_code">
                                            <i class="fas fa-shield-alt"></i>
                                            Entrez le code reçu *
                                        </label>
                                        <input type="text" 
                                               name="sms_code" 
                                               id="sms_code" 
                                               class="input"
                                               placeholder="123456" 
                                               maxlength="6"
                                               style="text-align: center; font-size: 1.2em; letter-spacing: 3px;"
                                               required>
                                        <small>Code à 6 chiffres (valide 10 minutes)</small>
                                    </div>
                                    
                                    <!-- Champs cachés pour conserver les données -->
                                    <input type="hidden" name="id_campagne" value="<?php echo htmlspecialchars($_POST['id_campagne'] ?? ''); ?>">
                                    <input type="hidden" name="montant" value="<?php echo htmlspecialchars($_POST['montant'] ?? ''); ?>">
                                    <input type="hidden" name="message" value="<?php echo htmlspecialchars($_POST['message'] ?? ''); ?>">
                                    <input type="hidden" name="methode_paiment" value="<?php echo htmlspecialchars($_POST['methode_paiment'] ?? ''); ?>">
                                    <input type="hidden" name="nom_donateur" value="<?php echo htmlspecialchars($_POST['nom_donateur'] ?? ''); ?>">
                                    <input type="hidden" name="email_donateur" value="<?php echo htmlspecialchars($_POST['email_donateur'] ?? ''); ?>">
                                    <input type="hidden" name="telephone" value="<?php echo htmlspecialchars($_POST['telephone'] ?? ''); ?>">
                                    <input type="hidden" name="statut" value="<?php echo htmlspecialchars($_POST['statut'] ?? 'confirmé'); ?>">
                                    <input type="hidden" name="envoyer_email" value="<?php echo isset($_POST['envoyer_email']) ? 'on' : ''; ?>">
                                    
                                    <div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;">
                                        <h4 style="margin: 0 0 10px 0; color: #856404;">
                                            <i class="fas fa-info-circle"></i> Sécurité renforcée
                                        </h4>
                                        <p style="margin: 0; font-size: 0.9em;">
                                            Cette vérification protège votre don contre les fraudes.
                                        </p>
                                    </div>
                                    
                                    <div class="form-footer">
                                        <button type="button" class="btn secondary" onclick="retourEtape1()">
                                            <i class="fas fa-arrow-left"></i> Retour
                                        </button>
                                        <button type="submit" class="btn success">
                                            <i class="fas fa-check-circle"></i> Finaliser le don
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Variables globales
        let currentEtape = <?php echo $afficher_etape_sms ? 2 : 1; ?>;
        
        // Afficher les infos de la campagne sélectionnée
        function afficherInfosCampagne(campagneId) {
            const select = document.getElementById('id_campagne');
            const option = select.selectedOptions[0];
            const preview = document.getElementById('campagnePreview');
            
            if (campagneId && option) {
                const titre = option.text;
                const objectif = parseFloat(option.dataset.objectif) || 0;
                const actuel = parseFloat(option.dataset.actuel) || 0;
                const progression = objectif > 0 ? (actuel / objectif) * 100 : 0;
                
                document.getElementById('campagneTitre').textContent = titre.split('(')[0].trim();
                document.getElementById('campagneObjectif').textContent = 
                    objectif.toLocaleString('fr-FR') + ' TND';
                document.getElementById('campagneCollecte').textContent = 
                    actuel.toLocaleString('fr-FR') + ' TND';
                document.getElementById('campagneProgression').style.width = 
                    Math.min(progression, 100) + '%';
                
                preview.classList.add('active');
                
                // Suggestion de montant (reste à collecter / 10)
                const montantInput = document.getElementById('montant');
                const reste = objectif - actuel;
                if (reste > 0 && !montantInput.value) {
                    montantInput.value = Math.max(1, Math.round(reste / 10));
                }
            } else {
                preview.classList.remove('active');
            }
        }
        
        // Retour à l'étape 1
        function retourEtape1() {
            // Soumettre un formulaire spécial pour revenir en arrière
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            
            // Ajouter tous les champs cachés
            const hiddenFields = [
                'id_campagne', 'montant', 'message', 'methode_paiment',
                'nom_donateur', 'email_donateur', 'telephone', 'statut'
            ];
            
            hiddenFields.forEach(field => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = field;
                input.value = document.querySelector(`[name="${field}"]`)?.value || '';
                form.appendChild(input);
            });
            
            // Ajouter la checkbox email
            const emailCheck = document.createElement('input');
            emailCheck.type = 'hidden';
            emailCheck.name = 'envoyer_email';
            emailCheck.value = document.getElementById('envoyer_email')?.checked ? 'on' : '';
            form.appendChild(emailCheck);
            
            document.body.appendChild(form);
            form.submit();
        }
        
        // Validation du formulaire
        document.getElementById('donForm').addEventListener('submit', function(e) {
            const campagneId = document.getElementById('id_campagne')?.value;
            const montant = parseFloat(document.getElementById('montant')?.value || 0);
            const telephone = document.getElementById('telephone')?.value.trim() || '';
            const smsCode = document.getElementById('sms_code')?.value.trim() || '';
            
            if (currentEtape === 1) {
                if (!campagneId) {
                    alert('Veuillez sélectionner une campagne');
                    e.preventDefault();
                    return false;
                }
                
                if (montant <= 0) {
                    alert('Le montant doit être supérieur à 0');
                    e.preventDefault();
                    return false;
                }
                
                const email = document.getElementById('email_donateur')?.value || '';
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    alert('Veuillez entrer un email valide');
                    e.preventDefault();
                    return false;
                }
                
                // Validation du téléphone si fourni
                if (telephone) {
                    const phoneRegex = /^216\d{8}$/;
                    if (!phoneRegex.test(telephone)) {
                        alert('Format de téléphone invalide. Utilisez: 216 suivi de 8 chiffres (ex: 21612345678)');
                        e.preventDefault();
                        return false;
                    }
                }
                
                // Demander confirmation si montant élevé
                if (montant > 1000) {
                    if (!confirm(`Confirmez-vous la création d'un don de ${montant} TND ?`)) {
                        e.preventDefault();
                        return false;
                    }
                }
            } else if (currentEtape === 2) {
                // Validation étape SMS
                if (!smsCode) {
                    alert('Veuillez entrer le code SMS');
                    e.preventDefault();
                    return false;
                }
                
                const smsRegex = /^\d{6}$/;
                if (!smsRegex.test(smsCode)) {
                    alert('Le code SMS doit contenir exactement 6 chiffres');
                    e.preventDefault();
                    return false;
                }
            }
            
            return true;
        });
        
        // Validation en temps réel du téléphone
        const telephoneInput = document.getElementById('telephone');
        if (telephoneInput) {
            telephoneInput.addEventListener('input', function() {
                const phone = this.value.trim();
                if (phone) {
                    // Nettoyer le numéro
                    this.value = phone.replace(/[^\d]/g, '');
                    
                    // Ajouter le préfixe 216 si nécessaire
                    if (phone.length > 0 && !phone.startsWith('216')) {
                        this.value = '216' + phone.replace(/^216/, '');
                    }
                    
                    // Limiter à 11 chiffres (216 + 8)
                    if (this.value.length > 11) {
                        this.value = this.value.substring(0, 11);
                    }
                }
            });
        }
        
        // Validation en temps réel du code SMS
        const smsCodeInput = document.getElementById('sms_code');
        if (smsCodeInput) {
            smsCodeInput.addEventListener('input', function() {
                // Nettoyer - garder seulement les chiffres
                this.value = this.value.replace(/[^\d]/g, '');
                
                // Limiter à 6 chiffres
                if (this.value.length > 6) {
                    this.value = this.value.substring(0, 6);
                }
            });
        }
        
        // Réinitialisation du formulaire
        function resetForm() {
            document.getElementById('campagnePreview').classList.remove('active');
            // Régénérer le code SMS
            fetch('?generate_sms=1', {method: 'POST'})
                .then(() => {
                    window.location.reload();
                });
        }
        
        // Initialiser l'affichage si une campagne est déjà sélectionnée
        document.addEventListener('DOMContentLoaded', function() {
            const campagneSelect = document.getElementById('id_campagne');
            if (campagneSelect && campagneSelect.value) {
                afficherInfosCampagne(campagneSelect.value);
            }
            
            // Auto-focus sur le champ approprié
            if (currentEtape === 1) {
                document.getElementById('id_campagne')?.focus();
            } else if (currentEtape === 2) {
                document.getElementById('sms_code')?.focus();
            }
            
            // Empêcher la soumission par Enter dans les champs numériques
            document.querySelectorAll('input[type="number"]').forEach(input => {
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>