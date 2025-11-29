<?php
include_once(__DIR__ . '/../../config.php');
include_once(__DIR__ . '/../../model/Campagne.php');
include_once(__DIR__ . '/../../controller/CampagneController.php');
$campagneController = new CampagneController();
$error = '';
// Récupérer la liste des utilisateurs valides
try {
    $db = config::getConnexion();
    $stmt = $db->query("SELECT Id_utilisateur, nom, email FROM utilisateur LIMIT 5");
    $valid_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $valid_users = [];
}
if (isset($_POST['submit'])) {
    // Validation des champs obligatoires
    $required_fields = ['id_utilisateur', 'titre', 'categorie_impact', 'urgence', 'description', 'objectif_montant', 'date_fin'];
    
    $missing_fields = [];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        $error = "Champs obligatoires manquants : " . implode(', ', $missing_fields);
    } else {
        // Vérifier d'abord si l'utilisateur existe
        $id_utilisateur = 1;  // On utilise l'ID 1 pour tester
        
        // Gérer la catégorie (choix standard ou nouvelle catégorie)
        $categorie_impact = $_POST['categorie_impact'];
        if ($categorie_impact === 'autre' && !empty($_POST['nouvelle_categorie'])) {
            $categorie_impact = trim($_POST['nouvelle_categorie']);
        }
        
        // Validation des dates
        $date_debut = date('Y-m-d'); // Date actuelle
        $date_fin = $_POST['date_fin'];
        
        if ($date_fin < $date_debut) {
            $error = "La date de fin doit être postérieure à la date d'aujourd'hui";
        } elseif (!$campagneController->userExists($id_utilisateur)) {
            $error = "Erreur : L'utilisateur avec l'ID $id_utilisateur n'existe pas.";
            
            // Afficher les IDs valides disponibles
            if (!empty($valid_users)) {
                $error .= "<br><strong>IDs utilisateurs valides :</strong><br>";
                foreach ($valid_users as $user) {
                    $error .= "• ID: {$user['Id_utilisateur']} - {$user['nom']} ({$user['email']})<br>";
                }
            } else {
                $error .= "<br><strong>Aucun utilisateur trouvé dans la base !</strong>";
                $error .= "<br>Vous devez d'abord créer un utilisateur dans la table 'utilisateur'";
            }
        } else {
            // Valeurs par défaut
            $image_campagne = !empty($_FILES['image_campagne']['name']) ? $this->uploadImage($_FILES['image_campagne']) : null;
            $statut = 'active'; // Statut par défaut
            $montant_actuel = 0.00; // Montant actuel toujours à 0
            
            try {
                $campagne = new Campagne(
                    null, // Id_campagne sera auto-incrémenté
                    $id_utilisateur,
                    trim($_POST['titre']),
                    $categorie_impact,
                    $_POST['urgence'],
                    trim($_POST['description']),
                    $statut,
                    $image_campagne,
                    (float)$_POST['objectif_montant'],
                    $montant_actuel,
                    $date_debut,
                    $date_fin
                );
                
                if ($campagneController->addCampagne($campagne)) {
                    header('Location: list-camp.php?success=1');
                    exit();
                } else {
                    $error = "Erreur lors de l'ajout de la campagne dans la base de données.";
                }
            } 
            catch (Exception $e) {
                $error = "Erreur : " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactAble — Ajouter Campagne</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .error-message {
            background: #fee;
            border: 1px solid #fcc;
            color: #c00;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .error-field {
            border: 1px solid #c00 !important;
            background: #fee;
        }
        .nouvelle-categorie {
            display: none;
            margin-top: 10px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 4px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        label.required::after {
            content: " *";
            color: #c00;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .full-width {
            grid-column: 1 / -1;
        }
        .form-actions {
            grid-column: 1 / -1;
            text-align: right;
            margin-top: 20px;
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
                    <div class="nav-title">Principal</div>
                    <a href="index.php" class="sidebar-link">
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
                    <a href="list-camp.php" class="sidebar-link active">
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
                        <h4>Admin User</h4>
                        <p>Administrateur</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div>
                    <h2>Ajouter une Campagne</h2>
                    <p class="text-muted">Créez une nouvelle campagne de collecte</p>
                </div>
                <div class="header-actions">
                    <a href="list-camp.php" class="btn secondary">
                        <i class="fas fa-arrow-left"></i>
                        Retour aux campagnes
                    </a>
                </div>
            </header>

            <div class="admin-content">
                <div class="content-card">
                    <div class="card-header">
                        <h3>Informations de la campagne</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="campagneForm" class="form-grid" enctype="multipart/form-data" onsubmit="return validerFormulaireCampagne(event)">
   <!-- ID Utilisateur -->
    <div class="form-group">
        <label for="id_utilisateur" class="required">ID Utilisateur</label>
        <input type="number" name="id_utilisateur" id="id_utilisateur" 
               placeholder="Entrez l'ID de l'utilisateur" 
               min="1"
               value="<?= isset($_POST['id_utilisateur']) ? htmlspecialchars($_POST['id_utilisateur']) : '' ?>">
        <small class="form-text">L'ID utilisateur doit exister dans la base de données</small>
        
        <?php if (!empty($valid_users)): ?>
            <div style="background: #e8f5e8; padding: 8px; border-radius: 4px; margin-top: 5px; font-size: 12px;">
                <strong>📋 IDs valides disponibles :</strong><br>
                <?php foreach ($valid_users as $user): ?>
                    • <strong><?= $user['Id_utilisateur'] ?></strong> - <?= htmlspecialchars($user['nom']) ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Titre -->
    <div class="form-group">
        <label for="titre" class="required">Titre de la campagne</label>
        <input type="text" name="titre" id="titre" 
               placeholder="Titre de la campagne"
               value="<?= isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : '' ?>">
    </div>

    <!-- Catégorie d'impact -->
    <div class="form-group">
        <label for="categorie_impact" class="required">Catégorie d'impact</label>
        <select name="categorie_impact" id="categorie_impact">
            <option value="">Sélectionnez une catégorie</option>
            <option value="education" <?= (isset($_POST['categorie_impact']) && $_POST['categorie_impact'] == 'education') ? 'selected' : '' ?>>Éducation</option>
            <option value="environnement" <?= (isset($_POST['categorie_impact']) && $_POST['categorie_impact'] == 'environnement') ? 'selected' : '' ?>>Environnement</option>
            <option value="social" <?= (isset($_POST['categorie_impact']) && $_POST['categorie_impact'] == 'social') ? 'selected' : '' ?>>Social</option>
            <option value="sante" <?= (isset($_POST['categorie_impact']) && $_POST['categorie_impact'] == 'sante') ? 'selected' : '' ?>>Santé</option>
            <option value="logement" <?= (isset($_POST['categorie_impact']) && $_POST['categorie_impact'] == 'logement') ? 'selected' : '' ?>>Logement</option>
            <option value="droits_humains" <?= (isset($_POST['categorie_impact']) && $_POST['categorie_impact'] == 'droits_humains') ? 'selected' : '' ?>>Droits humains</option>
            <option value="alimentation" <?= (isset($_POST['categorie_impact']) && $_POST['categorie_impact'] == 'alimentation') ? 'selected' : '' ?>>Alimentation</option>
            <option value="autre" <?= (isset($_POST['categorie_impact']) && $_POST['categorie_impact'] == 'autre') ? 'selected' : '' ?>>Autre (spécifier)</option>
        </select>
        
        <div id="nouvelle_categorie_container" class="nouvelle-categorie">
            <label for="nouvelle_categorie" class="required">Nouvelle catégorie</label>
            <input type="text" name="nouvelle_categorie" id="nouvelle_categorie" 
                   placeholder="Entrez le nom de la nouvelle catégorie"
                   value="<?= isset($_POST['nouvelle_categorie']) ? htmlspecialchars($_POST['nouvelle_categorie']) : '' ?>">
            <small class="form-text">Créez une nouvelle catégorie personnalisée</small>
        </div>
    </div>

    <!-- Niveau d'urgence -->
    <div class="form-group">
        <label for="urgence" class="required">Niveau d'urgence</label>
        <select name="urgence" id="urgence">
            <option value="">Sélectionnez le niveau d'urgence</option>
            <option value="normale" <?= (isset($_POST['urgence']) && $_POST['urgence'] == 'normale') ? 'selected' : '' ?>>Normale</option>
            <option value="elevee" <?= (isset($_POST['urgence']) && $_POST['urgence'] == 'elevee') ? 'selected' : '' ?>>Élevée</option>
            <option value="critique" <?= (isset($_POST['urgence']) && $_POST['urgence'] == 'critique') ? 'selected' : '' ?>>Critique</option>
        </select>
    </div>

    <!-- Description -->
    <div class="form-group full-width">
        <label for="description" class="required">Description</label>
        <textarea name="description" id="description" 
                  placeholder="Description détaillée de la campagne" 
                  rows="5"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
    </div>

    <!-- Image de la campagne -->
    <div class="form-group full-width">
        <label for="image_campagne">Image de la campagne</label>
        <input type="file" name="image_campagne" id="image_campagne" 
               accept="image/*">
        <small class="form-text">Formats acceptés: JPG, PNG, GIF. Taille max: 2MB</small>
    </div>

    <!-- Objectif de montant -->
    <div class="form-group">
        <label for="objectif_montant" class="required">Objectif de montant (TND)</label>
        <input type="number" name="objectif_montant" id="objectif_montant" 
               placeholder="Montant objectif" 
               step="0.01" 
               min="1"
               value="<?= isset($_POST['objectif_montant']) ? htmlspecialchars($_POST['objectif_montant']) : '' ?>">
    </div>

    <!-- Date de fin -->
    <div class="form-group">
        <label for="date_fin" class="required">Date de fin</label>
        <input type="date" name="date_fin" id="date_fin"
               min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
               value="<?= isset($_POST['date_fin']) ? htmlspecialchars($_POST['date_fin']) : '' ?>">
        <small class="form-text">La date de fin doit être postérieure à aujourd'hui</small>
    </div>

    <div class="form-actions full-width">
        <button type="submit" name="submit" class="btn primary">
    <i class="fas fa-plus-circle"></i>
    Ajouter la campagne
</button>
        <a href="list-camp.php" class="btn secondary">
            <i class="fas fa-times"></i>
            Annuler
        </a>
    </div>
</form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Script simplifié pour addCampagne.php
document.addEventListener('DOMContentLoaded', function() {
    console.log("Page addCampagne chargée");
    
    // Gestion de l'affichage du champ "Nouvelle catégorie"
    const categorieSelect = document.getElementById("categorie_impact");
    const nouvelleCategorieContainer = document.getElementById("nouvelle_categorie_container");
    
    if (categorieSelect && nouvelleCategorieContainer) {
        categorieSelect.addEventListener("change", function() {
            if (this.value === 'autre') {
                nouvelleCategorieContainer.style.display = 'block';
            } else {
                nouvelleCategorieContainer.style.display = 'none';
                document.getElementById('nouvelle_categorie').value = '';
            }
        });
        
        // Initialiser l'affichage au chargement
        if (categorieSelect.value === 'autre') {
            nouvelleCategorieContainer.style.display = 'block';
        }
    }

    // Validation basique avant soumission
    const form = document.getElementById("campagneForm");
    if (form) {
        form.addEventListener("submit", function(event) {
            console.log("Formulaire en cours de soumission...");
            
            // Validation simple
            let idUtilisateur = document.getElementById("id_utilisateur").value;
            let titre = document.getElementById("titre").value.trim();
            let categorie = document.getElementById("categorie_impact").value;
            let urgence = document.getElementById("urgence").value;
            let description = document.getElementById("description").value.trim();
            let objectif = document.getElementById("objectif_montant").value;
            let dateFin = document.getElementById("date_fin").value;

            // Vérification des champs obligatoires
            if (!idUtilisateur || !titre || !categorie || !urgence || !description || !objectif || !dateFin) {
                alert("❌ Tous les champs obligatoires doivent être remplis.");
                event.preventDefault();
                return false;
            }

            // Vérification de la nouvelle catégorie si "autre" est sélectionné
            if (categorie === 'autre') {
                let nouvelleCategorie = document.getElementById("nouvelle_categorie").value.trim();
                if (!nouvelleCategorie) {
                    alert("❌ Veuillez spécifier le nom de la nouvelle catégorie.");
                    event.preventDefault();
                    return false;
                }
            }

            // Vérification de la date
            let aujourdhui = new Date();
            aujourdhui.setHours(0, 0, 0, 0);
            let finSaisi = new Date(dateFin);

            if (finSaisi <= aujourdhui) {
                alert("❌ La date de fin doit être postérieure à aujourd'hui.");
                event.preventDefault();
                return false;
            }

            // Si tout est OK
            console.log("Validation réussie, soumission du formulaire...");
            return true;
        });
    }

    // Validation en temps réel optionnelle
    const dateFinInput = document.getElementById("date_fin");
    if (dateFinInput) {
        dateFinInput.addEventListener("change", function() {
            const aujourdhui = new Date();
            aujourdhui.setHours(0, 0, 0, 0);
            const finSaisi = new Date(this.value);
            
            if (this.value && finSaisi > aujourdhui) {
                this.style.borderColor = "green";
            } else {
                this.style.borderColor = "red";
            }
        });
    }
});

// Fonction de confirmation de suppression (pour d'autres pages)
function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
    return confirm(message);
}
    </script>
</body>
</html>