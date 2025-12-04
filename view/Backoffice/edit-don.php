<?php
// edit-don.php - Version avec modèle Don
include_once __DIR__ . '/../../controller/DonController.php';
include_once __DIR__ . '/../../controller/CampagneController.php';
include_once __DIR__ . '/../../controller/FrontCampagneController.php';
require_once __DIR__ . '/../../model/Don.php';

$donController = new DonController();
$campagneController = new CampagneController();

if (!isset($_GET['id'])) {
    header('Location: list-don.php');
    exit;
}

$don_id = $_GET['id'];

// Récupérer le don
$dons = $donController->getHistoriqueDonsComplet();
$donData = null;

foreach ($dons as $d) {
    if ($d['Id_don'] == $don_id) {
        $donData = $d;
        break;
    }
}

if (!$donData) {
    header('Location: list-don.php');
    exit;
}

// Créer un objet Don à partir des données
$don = new Don();
$don->setIdDon($donData['Id_don']);
$don->setIdCampagne($donData['id_campagne']);
$don->setIdUtilisateur($donData['id_utilisateur']);
$don->setMontant($donData['montant']);
$don->setMessage($donData['message']);
$don->setMethodePaiment($donData['methode_paiment']);
$don->setEmailDonateur($donData['donateur_email']);
$don->setNomDonateur($donData['donateur_nom']);
$don->setNumeroReçu($donData['numero_reçu']);
$don->setDateDon($donData['date_don']);
$don->setStatut($donData['statut']);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation
    $required = ['montant', 'statut', 'methode_paiment', 'nom_donateur', 'email_donateur'];
    $missing = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $missing[] = $field;
        }
    }
    
    if (!empty($missing)) {
        $error = "Champs manquants: " . implode(', ', $missing);
    } else {
        try {
            // Mettre à jour le don
            $db = config::getConnexion();
            
            $query = "UPDATE don SET 
                      montant = :montant,
                      statut = :statut,
                      methode_paiment = :methode,
                      message = :message,
                      email_donateur = :email,
                      nom_donateur = :nom
                      WHERE Id_don = :id";
            
            $stmt = $db->prepare($query);
            
            $result = $stmt->execute([
                ':montant' => $_POST['montant'],
                ':statut' => $_POST['statut'],
                ':methode' => $_POST['methode_paiment'],
                ':message' => $_POST['message'],
                ':email' => $_POST['email_donateur'],
                ':nom' => $_POST['nom_donateur'],
                ':id' => $don_id
            ]);
            
            if ($result) {
                // Mettre à jour l'objet Don
                $don->setMontant($_POST['montant']);
                $don->setStatut($_POST['statut']);
                $don->setMethodePaiment($_POST['methode_paiment']);
                $don->setMessage($_POST['message']);
                $don->setEmailDonateur($_POST['email_donateur']);
                $don->setNomDonateur($_POST['nom_donateur']);
                
                // Actualiser le montant de la campagne
                $frontController = new FrontCampagneController();
                $frontController->actualiserMontantCampagne($don->getIdCampagne());
                
                $success = "Don mis à jour avec succès !";
                
                // Recharger les données
                $dons = $donController->getHistoriqueDonsComplet();
                foreach ($dons as $d) {
                    if ($d['Id_don'] == $don_id) {
                        $donData = $d;
                        break;
                    }
                }
            } else {
                $error = "Erreur lors de la mise à jour.";
            }
            
        } catch (PDOException $e) {
            $error = "Erreur base de données: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactAble — Modifier Don #<?php echo $don->getIdDon(); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
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
                    <div class="nav-title">Principal</div>
                    <a href="index.php" class="sidebar-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tableau de bord</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-title">Gestion de contenu</div>
                    <a href="list-camp.php" class="sidebar-link">
                        <i class="fas fa-hand-holding-heart"></i>
                        <span>Campagnes</span>
                    </a>
                    <a href="list-don.php" class="sidebar-link active">
                        <i class="fas fa-donate"></i>
                        <span>Dons</span>
                    </a>
                </div>
            </nav>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <div>
                    <h2>Modifier le Don #<?php echo $don->getIdDon(); ?></h2>
                    <p class="text-muted">Mettre à jour les informations de ce don</p>
                </div>
                <div class="header-actions">
                    <a href="show-don.php?id=<?php echo $don->getIdDon(); ?>" class="btn">
                        <i class="fas fa-eye"></i> Voir
                    </a>
                    <a href="list-don.php" class="btn secondary">
                        <i class="fas fa-arrow-left"></i> Retour
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
                                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert success">
                                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="form-container">
                            <div class="form-grid">
                                <!-- Montant -->
                                <div class="form-group">
                                    <label for="montant" class="required">Montant (TND)</label>
                                    <input type="number" 
                                           name="montant" 
                                           id="montant" 
                                           class="input"
                                           value="<?php echo htmlspecialchars($don->getMontant()); ?>"
                                           step="0.01"
                                           min="1"
                                           required>
                                </div>
                                
                                <!-- Statut -->
                                <div class="form-group">
                                    <label for="statut" class="required">Statut</label>
                                    <select name="statut" id="statut" class="select" required>
                                        <option value="confirmé" <?php echo $don->getStatut() == 'confirmé' ? 'selected' : ''; ?>>Confirmé</option>
                                        <option value="en_attente" <?php echo $don->getStatut() == 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                                        <option value="annulé" <?php echo $don->getStatut() == 'annulé' ? 'selected' : ''; ?>>Annulé</option>
                                    </select>
                                </div>
                                
                                <!-- Méthode de paiement -->
                                <div class="form-group">
                                    <label for="methode_paiment" class="required">Méthode de paiement</label>
                                    <select name="methode_paiment" id="methode_paiment" class="select" required>
                                        <option value="carte" <?php echo $don->getMethodePaiment() == 'carte' ? 'selected' : ''; ?>>Carte bancaire</option>
                                        <option value="paypal" <?php echo $don->getMethodePaiment() == 'paypal' ? 'selected' : ''; ?>>PayPal</option>
                                        <option value="virement" <?php echo $don->getMethodePaiment() == 'virement' ? 'selected' : ''; ?>>Virement bancaire</option>
                                        <option value="especes" <?php echo $don->getMethodePaiment() == 'especes' ? 'selected' : ''; ?>>Espèces</option>
                                        <option value="cheque" <?php echo $don->getMethodePaiment() == 'cheque' ? 'selected' : ''; ?>>Chèque</option>
                                    </select>
                                </div>
                                
                                <!-- Numéro de reçu -->
                                <div class="form-group">
                                    <label>Numéro de reçu</label>
                                    <input type="text" 
                                           class="input" 
                                           value="<?php echo htmlspecialchars($don->getNumeroReçu()); ?>"
                                           disabled
                                           style="background: #f5f5f5;">
                                    <small class="text-muted">Généré automatiquement, non modifiable</small>
                                </div>
                                
                                <!-- Donateur -->
                                <div class="form-group">
                                    <label for="nom_donateur" class="required">Nom du donateur</label>
                                    <input type="text" 
                                           name="nom_donateur" 
                                           id="nom_donateur"
                                           class="input"
                                           value="<?php echo htmlspecialchars($don->getNomDonateur()); ?>"
                                           required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email_donateur" class="required">Email du donateur</label>
                                    <input type="email" 
                                           name="email_donateur" 
                                           id="email_donateur"
                                           class="input"
                                           value="<?php echo htmlspecialchars($don->getEmailDonateur()); ?>"
                                           required>
                                </div>
                                
                                <!-- Message -->
                                <div class="form-group full-width">
                                    <label for="message">Message du donateur</label>
                                    <textarea name="message" 
                                              id="message" 
                                              class="textarea" 
                                              rows="3"><?php echo htmlspecialchars($don->getMessage()); ?></textarea>
                                </div>
                                
                                <!-- Informations de la campagne -->
                                <div class="form-group full-width" style="background: #f8f9fa; padding: 15px; border-radius: 4px;">
                                    <label style="margin-bottom: 10px; display: block;">
                                        <i class="fas fa-info-circle"></i> Campagne liée
                                    </label>
                                    <p>
                                        <strong><?php echo htmlspecialchars($donData['campagne_titre'] ?? 'N/A'); ?></strong><br>
                                        <span class="badge"><?php echo ucfirst($donData['categorie_impact'] ?? 'inconnue'); ?></span>
                                    </p>
                                    <small class="text-muted">
                                        Campagne ID: <?php echo $don->getIdCampagne(); ?> • 
                                        Cette information ne peut pas être modifiée ici.
                                    </small>
                                </div>
                            </div>
                            
                            <div class="form-footer">
                                <a href="show-don.php?id=<?php echo $don->getIdDon(); ?>" class="btn secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                                <button type="submit" class="btn primary">
                                    <i class="fas fa-save"></i> Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const montant = document.getElementById('montant').value;
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
    </script>
</body>
</html>