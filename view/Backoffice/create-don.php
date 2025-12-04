<?php
include_once __DIR__ . '/../../controller/DonController.php';
include_once __DIR__ . '/../../controller/CampagneController.php';

$donController = new DonController();
$campagneController = new CampagneController();

// Récupérer les campagnes actives
$campagnes = $campagneController->getAllCampagnes();
$campagnesList = $campagnes ? $campagnes->fetchAll(PDO::FETCH_ASSOC) : [];

$error = '';
$success = '';

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
            // Créer le don via le controller
            $don_id = $donController->faireDon(
                $_POST['id_campagne'],
                $_POST['montant'],
                $_POST['message'] ?? '',
                $_POST['methode_paiment'],
                $_POST['email_donateur'],
                $_POST['nom_donateur']
            );
            
            if ($don_id) {
                $success = "Don créé avec succès ! ID: $don_id";
                
                // Réinitialiser le formulaire
                $_POST = [];
            } else {
                $error = "Erreur lors de la création du don";
            }
            
        } catch (Exception $e) {
            $error = "Erreur: " . $e->getMessage();
        }
    }
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
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <!-- Copier le sidebar depuis list-camp.php -->
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
                        <?php if ($error): ?>
                            <div class="alert error">
                                <i class="fas fa-exclamation-circle"></i>
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert success">
                                <i class="fas fa-check-circle"></i>
                                <?php echo $success; ?>
                                <a href="show-don.php?id=<?php echo $don_id; ?>" class="btn small" style="margin-left: 10px;">
                                    Voir le don
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="form-container" id="donForm">
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
                                        <option value="confirmé" selected>Confirmé</option>
                                        <option value="en_attente">En attente</option>
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
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
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
        
        // Validation du formulaire
        document.getElementById('donForm').addEventListener('submit', function(e) {
            const montant = parseFloat(document.getElementById('montant').value);
            const campagneId = document.getElementById('id_campagne').value;
            
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
            
            const email = document.getElementById('email_donateur').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Veuillez entrer un email valide');
                e.preventDefault();
                return false;
            }
            
            return true;
        });
        
        // Initialiser l'affichage si une campagne est déjà sélectionnée
        document.addEventListener('DOMContentLoaded', function() {
            const campagneSelect = document.getElementById('id_campagne');
            if (campagneSelect.value) {
                afficherInfosCampagne(campagneSelect.value);
            }
        });
    </script>
</body>
</html>