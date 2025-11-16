<?php 
$title = "Poster une Offre | " . Config::SITE_NAME;
require_once __DIR__ . '/../templates/header.php'; 
?>
<script src="<?php echo Config::SITE_URL; ?>public\js\form-accessibility.js"></script>
<div class="poster-offre-container">
    <div class="section-header">
        <h1><i class="fas fa-plus-circle"></i> Poster une Offre</h1>
        <p class="text-muted">Publiez une nouvelle opportunité d'emploi, stage, formation ou volontariat</p>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <strong>Félicitations !</strong> Votre offre a été publiée avec succès.
        </div>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Erreur</strong>
                <?php foreach ($errors as $error): ?>
                    <div style="margin-top: 0.5rem;"><?php echo $error; ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="offre-form">
            <!-- Section Informations de base -->
            <div class="form-section">
                <h2><i class="fas fa-info-circle"></i> Informations de base</h2>
                
                <div class="form-group">
                    <label class="form-label" for="titre">
                        Titre de l'offre *
                    </label>
                    <input type="text" id="titre" name="titre" class="form-control" 
                           placeholder="Ex: Développeur Web Accessibilité" 
                           value="<?php echo htmlspecialchars($_POST['titre'] ?? ''); ?>">
                    <div class="form-help">Titre attractif qui décrit le poste</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="type_offre">
                        Type d'offre *
                    </label>
                    <select id="type_offre" name="type_offre" class="form-control">
                        <option value="emploi" <?php echo ($_POST['type_offre'] ?? '') === 'emploi' ? 'selected' : ''; ?>>Emploi</option>
                        <option value="stage" <?php echo ($_POST['type_offre'] ?? '') === 'stage' ? 'selected' : ''; ?>>Stage</option>
                        <option value="volontariat" <?php echo ($_POST['type_offre'] ?? '') === 'volontariat' ? 'selected' : ''; ?>>Volontariat</option>
                        <option value="formation" <?php echo ($_POST['type_offre'] ?? '') === 'formation' ? 'selected' : ''; ?>>Formation</option>
                        <option value="autre" <?php echo ($_POST['type_offre'] ?? '') === 'autre' ? 'selected' : ''; ?>>Autre</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">
                        Description du poste *
                    </label>
                    <textarea id="description" name="description" class="form-control" 
                              placeholder="Décrivez les missions, responsabilités et compétences requises..." 
                              rows="6"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    <div class="form-help">Décrivez en détail le poste et les attentes</div>
                </div>
            </div>

            <!-- Section Modalités -->
            <div class="form-section">
                <h2><i class="fas fa-cogs"></i> Modalités de travail</h2>
                
                <div class="form-group">
                    <label class="form-label">Mode de travail *</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="mode" value="presentiel" <?php echo ($_POST['mode'] ?? 'presentiel') === 'presentiel' ? 'checked' : ''; ?>>
                            <i class="fas fa-building"></i> Présentiel
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="mode" value="en_ligne" <?php echo ($_POST['mode'] ?? '') === 'en_ligne' ? 'checked' : ''; ?>>
                            <i class="fas fa-laptop-house"></i> En ligne
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="mode" value="hybride" <?php echo ($_POST['mode'] ?? '') === 'hybride' ? 'checked' : ''; ?>>
                            <i class="fas fa-balance-scale"></i> Hybride
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Horaire de travail *</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="horaire" value="temps_plein" <?php echo ($_POST['horaire'] ?? 'temps_plein') === 'temps_plein' ? 'checked' : ''; ?>>
                            <i class="fas fa-clock"></i> Temps plein
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="horaire" value="temps_partiel" <?php echo ($_POST['horaire'] ?? '') === 'temps_partiel' ? 'checked' : ''; ?>>
                            <i class="fas fa-chart-pie"></i> Temps partiel
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="lieu">
                        Lieu de travail
                    </label>
                    <input type="text" id="lieu" name="lieu" class="form-control" 
                           placeholder="Ex: Tunis, Remote, Paris..." 
                           value="<?php echo htmlspecialchars($_POST['lieu'] ?? ''); ?>">
                    <div class="form-help">Laisser vide pour "Non spécifié"</div>
                </div>
            </div>

            <!-- Section Accessibilité -->
            <div class="form-section">
                <h2><i class="fas fa-universal-access"></i> Accessibilité</h2>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="disability_friendly" value="1" <?php echo isset($_POST['disability_friendly']) ? 'checked' : ''; ?>>
                        <span class="form-label">Cette offre est adaptée aux personnes en situation de handicap</span>
                    </label>
                    <div class="form-help">Cochez cette case si votre environnement de travail est accessible</div>
                </div>

                <div class="accessibility-options">
                    <label class="form-label">Types de handicaps adaptés</label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="type_handicap[]" value="moteur" <?php echo in_array('moteur', $_POST['type_handicap'] ?? []) ? 'checked' : ''; ?>>
                            <i class="fas fa-wheelchair"></i> Handicap moteur
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="type_handicap[]" value="visuel" <?php echo in_array('visuel', $_POST['type_handicap'] ?? []) ? 'checked' : ''; ?>>
                            <i class="fas fa-low-vision"></i> Handicap visuel
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="type_handicap[]" value="auditif" <?php echo in_array('auditif', $_POST['type_handicap'] ?? []) ? 'checked' : ''; ?>>
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
                    <label class="form-label" for="impact_sociale">
                        Impact social de cette offre *
                    </label>
                    <textarea id="impact_sociale" name="impact_sociale" class="form-control" 
                              placeholder="Décrivez comment cette offre contribue à l'inclusion et à l'impact social..." 
                              rows="4"><?php echo htmlspecialchars($_POST['impact_sociale'] ?? ''); ?></textarea>
                    <div class="form-help">Expliquez en quoi ce poste favorise l'inclusion et a un impact positif</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="date_expiration">
                        Date d'expiration de l'offre *
                    </label>
                    <input type="date" id="date_expiration" name="date_expiration" class="form-control" 
                           value="<?php echo htmlspecialchars($_POST['date_expiration'] ?? ''); ?>">
                    <div class="form-help">Date jusqu'à laquelle l'offre sera visible</div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="btn-group">
                <button type="submit" class="btn primary">
                    <i class="fas fa-paper-plane"></i>
                    Publier l'offre
                </button>
                <a href="index.php?action=offres" class="btn ghost">
                    <i class="fas fa-times"></i>
                    Annuler
                </a>
            </div>
        </form>
    <?php endif; ?>
</div>

<script src="<?php echo Config::SITE_URL; ?>/public/js/form-accessibility.js"></script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>