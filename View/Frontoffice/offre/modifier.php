<?php 
$title = "Modifier l'offre | " . Config::SITE_NAME;
require_once __DIR__ . '/../templates/header.php'; 
?>

<div class="modifier-offre-container">
    <div class="section-header">
        <h1><i class="fas fa-edit"></i> Modifier l'offre</h1>
        <p class="text-muted">Modifiez les informations de votre offre</p>
    </div>

    <!-- Aperçu de l'offre actuelle -->
    <div class="offre-current-state">
        <h3><i class="fas fa-info-circle"></i> État actuel de l'offre</h3>
        <div class="state-info">
            <div class="state-item">
                <span class="state-label">Statut</span>
                <span class="state-value <?php echo (strtotime($offre['date_expiration']) >= time() || empty($offre['date_expiration'])) ? 'active' : 'expired'; ?>">
                    <?php echo (strtotime($offre['date_expiration']) >= time() || empty($offre['date_expiration'])) ? 'Active' : 'Expirée'; ?>
                </span>
            </div>
            <div class="state-item">
                <span class="state-label">Date de publication</span>
                <span class="state-value"><?php echo date('d/m/Y', strtotime($offre['date_publication'])); ?></span>
            </div>
            <div class="state-item">
                <span class="state-label">Candidatures</span>
                <span class="candidatures-count">
                    <i class="fas fa-users"></i>
                    <?php echo $candidatureCount; ?> candidat(s)
                </span>
            </div>
        </div>
    </div>

    <!-- Messages d'alerte -->
    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <strong>Succès !</strong> Votre offre a été modifiée avec succès.
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Erreur</strong>
            <?php foreach ($errors as $error): ?>
                <div style="margin-top: 0.5rem;"><?php echo $error; ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- FORMULAIRE COMPLET -->
    <form method="POST" class="modification-form">
        <!-- Section Informations de base -->
        <div class="form-section">
            <h2><i class="fas fa-info-circle"></i> Informations de base</h2>
            
            <div class="form-group">
                <label class="form-label required" for="titre">
                    Titre de l'offre *
                </label>
                <input type="text" id="titre" name="titre" class="form-control" 
                       placeholder="Ex: Développeur Web Accessibilité" 
                       value="<?php echo htmlspecialchars($_POST['titre'] ?? $offre['titre']); ?>" required>
                <div class="form-help">Titre attractif qui décrit le poste</div>
            </div>

            <div class="form-group">
                <label class="form-label required" for="type_offre">
                    Type d'offre *
                </label>
                <select id="type_offre" name="type_offre" class="form-control" required>
                    <option value="emploi" <?php echo ($_POST['type_offre'] ?? $offre['type_offre']) === 'emploi' ? 'selected' : ''; ?>>Emploi</option>
                    <option value="stage" <?php echo ($_POST['type_offre'] ?? $offre['type_offre']) === 'stage' ? 'selected' : ''; ?>>Stage</option>
                    <option value="volontariat" <?php echo ($_POST['type_offre'] ?? $offre['type_offre']) === 'volontariat' ? 'selected' : ''; ?>>Volontariat</option>
                    <option value="formation" <?php echo ($_POST['type_offre'] ?? $offre['type_offre']) === 'formation' ? 'selected' : ''; ?>>Formation</option>
                    <option value="autre" <?php echo ($_POST['type_offre'] ?? $offre['type_offre']) === 'autre' ? 'selected' : ''; ?>>Autre</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label required" for="description">
                    Description du poste *
                </label>
                <textarea id="description" name="description" class="form-control" 
                          placeholder="Décrivez les missions, responsabilités et compétences requises..." 
                          rows="6" required><?php echo htmlspecialchars($_POST['description'] ?? $offre['description']); ?></textarea>
                <div class="form-help">Décrivez en détail le poste et les attentes</div>
            </div>
        </div>

        <!-- Section Modalités -->
        <div class="form-section">
            <h2><i class="fas fa-cogs"></i> Modalités de travail</h2>
            
            <div class="form-group">
                <label class="form-label required">Mode de travail *</label>
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="mode" value="presentiel" <?php echo ($_POST['mode'] ?? $offre['mode']) === 'presentiel' ? 'checked' : ''; ?> required>
                        <span class="radio-content">
                            <i class="fas fa-building"></i> Présentiel
                        </span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="mode" value="en_ligne" <?php echo ($_POST['mode'] ?? $offre['mode']) === 'en_ligne' ? 'checked' : ''; ?>>
                        <span class="radio-content">
                            <i class="fas fa-laptop-house"></i> En ligne
                        </span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="mode" value="hybride" <?php echo ($_POST['mode'] ?? $offre['mode']) === 'hybride' ? 'checked' : ''; ?>>
                        <span class="radio-content">
                            <i class="fas fa-balance-scale"></i> Hybride
                        </span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label required">Horaire de travail *</label>
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="horaire" value="temps_plein" <?php echo ($_POST['horaire'] ?? $offre['horaire']) === 'temps_plein' ? 'checked' : ''; ?> required>
                        <span class="radio-content">
                            <i class="fas fa-clock"></i> Temps plein
                        </span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="horaire" value="temps_partiel" <?php echo ($_POST['horaire'] ?? $offre['horaire']) === 'temps_partiel' ? 'checked' : ''; ?>>
                        <span class="radio-content">
                            <i class="fas fa-chart-pie"></i> Temps partiel
                        </span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="lieu">
                    Lieu de travail
                </label>
                <input type="text" id="lieu" name="lieu" class="form-control" 
                       placeholder="Ex: Tunis, Remote, Paris..." 
                       value="<?php echo htmlspecialchars($_POST['lieu'] ?? $offre['lieu']); ?>">
                <div class="form-help">Laisser vide pour "Non spécifié"</div>
            </div>
        </div>

        <!-- Section Accessibilité -->
        <div class="form-section">
            <h2><i class="fas fa-universal-access"></i> Accessibilité</h2>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="disability_friendly" value="1" <?php echo isset($_POST['disability_friendly']) ? 'checked' : ($offre['disability_friendly'] ? 'checked' : ''); ?>>
                    <span class="form-label">Cette offre est adaptée aux personnes en situation de handicap</span>
                </label>
                <div class="form-help">Cochez cette case si votre environnement de travail est accessible</div>
            </div>

            <div class="accessibility-options">
                <label class="form-label">Types de handicaps adaptés</label>
                <div class="checkbox-group">
                    <?php
                    $currentHandicaps = isset($_POST['type_handicap']) ? $_POST['type_handicap'] : explode(',', $offre['type_handicap']);
                    ?>
                    <label class="checkbox-label">
                        <input type="checkbox" name="type_handicap[]" value="moteur" <?php echo in_array('moteur', $currentHandicaps) ? 'checked' : ''; ?>>
                        <i class="fas fa-wheelchair"></i> Handicap moteur
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="type_handicap[]" value="visuel" <?php echo in_array('visuel', $currentHandicaps) ? 'checked' : ''; ?>>
                        <i class="fas fa-low-vision"></i> Handicap visuel
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="type_handicap[]" value="auditif" <?php echo in_array('auditif', $currentHandicaps) ? 'checked' : ''; ?>>
                        <i class="fas fa-deaf"></i> Handicap auditif
                    </label>
                </div>
                <div class="form-help">Sélectionnez les types de handicaps adaptés à votre environnement</div>
            </div>
        </div>

        <!-- Section Impact social -->
        <div class="form-section">
            <h2><i class="fas fa-heart"></i> Impact social</h2>
            
            <div class="form-group">
                <label class="form-label required" for="impact_sociale">
                    Impact social de cette offre *
                </label>
                <textarea id="impact_sociale" name="impact_sociale" class="form-control" 
                          placeholder="Décrivez comment cette offre contribue à l'inclusion et à l'impact social..." 
                          rows="4" required><?php echo htmlspecialchars($_POST['impact_sociale'] ?? $offre['impact_sociale']); ?></textarea>
                <div class="form-help">Expliquez en quoi ce poste favorise l'inclusion et a un impact positif</div>
            </div>

            <div class="form-group">
                <label class="form-label required" for="date_expiration">
                    Date d'expiration de l'offre *
                </label>
                <input type="date" id="date_expiration" name="date_expiration" class="form-control" 
                       value="<?php echo htmlspecialchars($_POST['date_expiration'] ?? $offre['date_expiration']); ?>" 
                       min="<?php echo date('Y-m-d'); ?>" required>
                <div class="form-help">Date jusqu'à laquelle l'offre sera visible</div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="btn-group-actions">
            <div class="preview-actions">
                <a href="index.php?action=details-offre&id=<?php echo $offreId; ?>" class="btn preview-btn" target="_blank">
                    <i class="fas fa-eye"></i>
                    Voir l'offre
                </a>
                <a href="index.php?action=supprimer-offre&id=<?php echo $offreId; ?>" 
                   class="btn delete-btn" 
                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ? Toutes les candidatures associées seront également supprimées. Cette action est irréversible.');">
                    <i class="fas fa-trash"></i>
                    Supprimer l'offre
                </a>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn primary">
                    <i class="fas fa-save"></i>
                    Enregistrer les modifications
                </button>
                <a href="index.php?action=mes-offres" class="btn secondary">
                    <i class="fas fa-times"></i>
                    Annuler
                </a>
            </div>
        </div>
    </form>
</div>

<script>
// Script pour gérer l'affichage des options d'accessibilité
const disabilityCheckbox = document.querySelector('input[name="disability_friendly"]');
const accessibilityOptions = document.querySelector('.accessibility-options');

function toggleAccessibilityOptions() {
    if (disabilityCheckbox.checked) {
        accessibilityOptions.style.display = 'block';
    } else {
        accessibilityOptions.style.display = 'none';
        // Décocher toutes les cases de type_handicap
        document.querySelectorAll('input[name="type_handicap[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    }
}

// Initialiser l'état
toggleAccessibilityOptions();

// Écouter les changements
if (disabilityCheckbox) {
    disabilityCheckbox.addEventListener('change', toggleAccessibilityOptions);
}

// Validation de la date
const dateExpiration = document.getElementById('date_expiration');
if (dateExpiration) {
    dateExpiration.min = new Date().toISOString().split('T')[0];
}
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>