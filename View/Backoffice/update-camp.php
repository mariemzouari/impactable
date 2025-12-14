<?php

include __DIR__ . '/../../Model/CampagneController.php';
require_once __DIR__ . '/../../model/Campagne.php';


$error = '';
$campagne = null;
$campagneController = new CampagneController();

if (isset($_GET['id'])) {
    $campagne = $campagneController->showCampagne($_GET['id']);
    if (!$campagne) {
        header('Location: list-camp.php');
        exit;
    }
}

if (
    isset(
    $_POST['Id_campagne'],
    $_POST['titre'],
    $_POST['categorie_impact'],
    $_POST['urgence'],
    $_POST['description'],
    $_POST['objectif_montant'],
    $_POST['date_debut'],
    $_POST['date_fin']
)
) {
    if (
        !empty($_POST['Id_campagne']) && !empty($_POST['titre']) && !empty($_POST['categorie_impact']) &&
        !empty($_POST['urgence']) && !empty($_POST['description']) && !empty($_POST['objectif_montant']) &&
        !empty($_POST['date_debut']) && !empty($_POST['date_fin'])
    ) {
        $campagneObj = new Campagne(
            $_POST['Id_campagne'],
            $campagne['Id_utilisateur'] ?? 1,
            $_POST['titre'],
            $_POST['categorie_impact'],
            $_POST['urgence'],
            $_POST['description'],
            $_POST['statut'] ?? 'active',
            $campagne['image_campagne'] ?? null,
            $_POST['objectif_montant'],
            $campagne['montant_actuel'] ?? 0.00,
            $_POST['date_debut'],
            $_POST['date_fin']
        );

        if ($campagneController->updateCampagne($campagneObj, $_POST['Id_campagne'])) {
            header('Location: list-camp.php?success=3');
            exit();
        } else {
            $error = 'Erreur lors de la mise à jour de la campagne';
        }
    } else {
        $error = 'Veuillez remplir tous les champs obligatoires';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactAble — Modifier Campagne</title>
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
                    <a href="#analytics" class="sidebar-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Analytiques</span>
                    </a>
                </div>

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

                    <a href="list-don.php" class="sidebar-link">
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
                    <h2>Modifier la Campagne</h2>
                    <p class="text-muted">Mettre à jour les informations de la campagne</p>
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
                        <h3>Modifier les informations</h3>
                    </div>

                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert error">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST" class="form-container"
                            onsubmit="return validerFormulaireModification(event)">
                            <input type="hidden" name="Id_campagne"
                                value="<?php echo $campagne['Id_campagne'] ?? ''; ?>">

                            <div class="form-grid">
                                <!-- Titre -->
                                <div class="form-group full-width">
                                    <label for="titre">Titre de la campagne</label>
                                    <input type="text" class="input" id="titre" name="titre"
                                        value="<?php echo htmlspecialchars($campagne['titre'] ?? ''); ?>">
                                </div>

                                <!-- Catégorie et Urgence -->
                                <div class="form-group">
                                    <label for="categorie_impact">Catégorie d'impact</label>
                                    <select class="select" id="categorie_impact" name="categorie_impact">
                                        <option value="education" <?php echo (isset($campagne['categorie_impact']) && $campagne['categorie_impact'] == 'education') ? 'selected' : ''; ?>>Éducation
                                        </option>
                                        <option value="logement" <?php echo (isset($campagne['categorie_impact']) && $campagne['categorie_impact'] == 'logement') ? 'selected' : ''; ?>>Logement
                                        </option>
                                        <option value="sante" <?php echo (isset($campagne['categorie_impact']) && $campagne['categorie_impact'] == 'sante') ? 'selected' : ''; ?>>Santé</option>
                                        <option value="alimentation" <?php echo (isset($campagne['categorie_impact']) && $campagne['categorie_impact'] == 'alimentation') ? 'selected' : ''; ?>>
                                            Alimentation</option>
                                        <option value="droits_humains" <?php echo (isset($campagne['categorie_impact']) && $campagne['categorie_impact'] == 'droits_humains') ? 'selected' : ''; ?>>
                                            Droits
                                            humains</option>
                                        <option value="autre" <?php echo (isset($campagne['categorie_impact']) && $campagne['categorie_impact'] == 'autre') ? 'selected' : ''; ?>>Autre</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="urgence">Niveau d'urgence</label>
                                    <select class="select" id="urgence" name="urgence">
                                        <option value="normale" <?php echo (isset($campagne['urgence']) && $campagne['urgence'] == 'normale') ? 'selected' : ''; ?>>Normale</option>
                                        <option value="elevee" <?php echo (isset($campagne['urgence']) && $campagne['urgence'] == 'elevee') ? 'selected' : ''; ?>>Élevée</option>
                                        <option value="critique" <?php echo (isset($campagne['urgence']) && $campagne['urgence'] == 'critique') ? 'selected' : ''; ?>>Critique</option>
                                    </select>
                                </div>

                                <!-- Objectif -->
                                <div class="form-group full-width">
                                    <label for="objectif_montant">Objectif de collecte (TND)</label>
                                    <input type="number" class="input" id="objectif_montant" name="objectif_montant"
                                        value="<?php echo $campagne['objectif_montant'] ?? ''; ?>" min="1" step="0.01">
                                </div>

                                <!-- Dates -->
                                <div class="form-group">
                                    <label for="date_debut">Date de début</label>
                                    <input type="date" class="input" id="date_debut" name="date_debut"
                                        value="<?php echo $campagne['date_debut'] ?? ''; ?>">
                                </div>

                                <div class="form-group">
                                    <label for="date_fin">Date de fin</label>
                                    <input type="date" class="input" id="date_fin" name="date_fin"
                                        value="<?php echo $campagne['date_fin'] ?? ''; ?>">
                                </div>
                                <?php
                                // Calculer une suggestion de nouvelle date (30 jours supplémentaires)
                                $dateFinActuelle = $campagne['date_fin'];
                                $nouvelleDateSuggestion = date('Y-m-d', strtotime($dateFinActuelle . ' + 30 days'));
                                ?>

                                <?php if ($campagne['date_fin'] < date('Y-m-d') && $campagne['montant_actuel'] < $campagne['objectif_montant']): ?>
                                    <div class="alert warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Attention :</strong> Cette campagne a dépassé sa date de fin sans atteindre
                                        son objectif.
                                        <br>Suggestion : Prolongez jusqu'au
                                        <strong><?php echo $nouvelleDateSuggestion; ?></strong>
                                    </div>
                                <?php endif; ?>

                                <!-- Description -->
                                <div class="form-group full-width">
                                    <label for="description">Description détaillée</label>
                                    <textarea class="textarea" id="description" name="description"
                                        rows="6"><?php echo htmlspecialchars($campagne['description'] ?? ''); ?></textarea>
                                </div>

                                <!-- Statut -->
                                <div class="form-group">
                                    <label for="statut">Statut</label>
                                    <select class="select" id="statut" name="statut">
                                        <option value="active" <?php echo (isset($campagne['statut']) && $campagne['statut'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="terminee" <?php echo (isset($campagne['statut']) && $campagne['statut'] == 'terminee') ? 'selected' : ''; ?>>Terminée</option>
                                        <option value="objectif_atteint" <?php echo (isset($campagne['statut']) && $campagne['statut'] == 'objectif_atteint') ? 'selected' : ''; ?>>Objectif
                                            atteint
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-footer">
                                <a href="list-camp.php" class="btn secondary">
                                    <i class="fas fa-times"></i>
                                    Annuler
                                </a>
                                <button type="submit" class="btn primary">
                                    <i class="fas fa-save"></i>
                                    Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                        <script src="assets/js/script.js"></script>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>

</html>