<?php
include '../../controller/CampagneController.php';
include '../../model/Campagne.php';

$error = "";
$campagneController = new CampagneController();

if (isset($_POST["titre"])) {
    if (!empty($_POST["titre"]) && !empty($_POST["categorie_impact"]) && !empty($_POST["urgence"]) && !empty($_POST["description"]) && !empty($_POST["objectif_montant"]) && !empty($_POST["date_debut"]) && !empty($_POST["date_fin"])) {
        
        $campagne = new Campagne(
            null, // Id_campagne
            1,    // Id_utilisateur (l'admin que vous venez de créer)
            $_POST['titre'],
            $_POST['categorie_impact'],
            $_POST['urgence'],
            $_POST['description'],
            'active', // statut
            null,     // image_campagne
            $_POST['objectif_montant'],
            0.00,     // montant_actuel
            $_POST['date_debut'],
            $_POST['date_fin']
        );
        
        if ($campagneController->addCampagne($campagne)) {
            header('Location: list-camp.php?success=1');
            exit;
        } else {
            $error = "Erreur lors de l'ajout de la campagne";
        }
    } else {
        $error = "Veuillez remplir tous les champs obligatoires";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactAble — Nouvelle Campagne</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="admin-container">
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
                    <a href="list-camp.php" class="sidebar-link active">
                        <i class="fas fa-hand-holding-heart"></i>
                        <span>Campagnes</span>
                    </a>
                </div>
            </nav>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <div>
                    <h2>Nouvelle Campagne</h2>
                    <p class="text-muted">Créer une nouvelle campagne de collecte</p>
                </div>
                
                <div class="header-actions">
                    <a href="list-camp.php" class="btn secondary">
                        <i class="fas fa-arrow-left"></i>
                        Retour à la liste
                    </a>
                </div>
            </header>

            <div class="admin-content">
                <section class="content-card">
                    <div class="card-header">
                        <h3>Informations de la campagne</h3>
                    </div>

                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                        <div class="alert error">
                            <?php echo $error; ?>
                        </div>
                        <?php endif; ?>

                        <form action="" method="POST" class="form-container">
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label for="titre">Titre de la campagne</label>
                                    <input type="text" class="input" id="titre" name="titre" placeholder="Ex: Aide aux personnes handicapées" required>
                                </div>

                                <div class="form-group">
                                    <label for="categorie_impact">Catégorie d'impact</label>
                                    <select class="select" id="categorie_impact" name="categorie_impact" required>
                                        <option value="">Choisir une catégorie</option>
                                        <option value="education">Éducation</option>
                                        <option value="logement">Logement</option>
                                        <option value="sante">Santé</option>
                                        <option value="alimentation">Alimentation</option>
                                        <option value="droits_humains">Droits humains</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="urgence">Niveau d'urgence</label>
                                    <select class="select" id="urgence" name="urgence" required>
                                        <option value="">Choisir le niveau</option>
                                        <option value="normale">Normale</option>
                                        <option value="elevee">Élevée</option>
                                        <option value="critique">Critique</option>
                                    </select>
                                </div>

                                <div class="form-group full-width">
                                    <label for="objectif_montant">Objectif de collecte (TND)</label>
                                    <input type="number" class="input" id="objectif_montant" name="objectif_montant" placeholder="Ex: 50000" min="1" step="0.01" required>
                                </div>

                                <div class="form-group">
                                    <label for="date_debut">Date de début</label>
                                    <input type="date" class="input" id="date_debut" name="date_debut" required>
                                </div>

                                <div class="form-group">
                                    <label for="date_fin">Date de fin</label>
                                    <input type="date" class="input" id="date_fin" name="date_fin" required>
                                </div>

                                <div class="form-group full-width">
                                    <label for="description">Description détaillée</label>
                                    <textarea class="textarea" id="description" name="description" rows="6" placeholder="Décrivez en détail l'objectif de cette campagne..." required></textarea>
                                </div>
                            </div>

                            <div class="form-footer">
                                <button type="reset" class="btn secondary">Réinitialiser</button>
                                <button type="submit" class="btn primary">Créer la campagne</button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>