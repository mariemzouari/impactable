<?php 
$title = "Postuler - " . Utils::escape($offre['titre']) . " | " . Config::SITE_NAME;
require_once __DIR__ . '/../templates/header.php'; 
?>

<div class="postuler-container">
    <!-- Aperçu de l'offre -->
    <div class="offer-preview">
        <h1><?php echo Utils::escape($offre['titre']); ?></h1>
        <div class="offer-meta">
            <span class="offer-type">
                <i class="fas fa-briefcase"></i>
                <?php echo Utils::formatTypeOffre($offre['type_offre']); ?>
            </span>
            <span class="offer-mode">
                <i class="fas fa-laptop-house"></i>
                <?php echo Utils::formatMode($offre['mode']); ?>
            </span>
            <span class="offer-schedule">
                <i class="fas fa-clock"></i>
                <?php echo str_replace('_', ' ', $offre['horaire']); ?>
            </span>
            <?php if (!empty($offre['lieu'])): ?>
                <span class="offer-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <?php echo Utils::escape($offre['lieu']); ?>
                </span>
            <?php endif; ?>
        </div>
        <p class="text-muted"><?php echo nl2br(Utils::escape($offre['description'])); ?></p>
    </div>

    <!-- Informations utilisateur -->
    <div class="user-info">
        <h3><i class="fas fa-user"></i> Vos informations</h3>
        <p><strong>Nom :</strong> <?php echo Utils::escape($user['prenom'] . ' ' . $user['nom']); ?></p>
        <p><strong>Email :</strong> <?php echo Utils::escape($user['email']); ?></p>
        <?php if (!empty($user['numero_tel'])): ?>
            <p><strong>Téléphone :</strong> <?php echo Utils::escape($user['numero_tel']); ?></p>
        <?php endif; ?>
    </div>

    <!-- Messages d'alerte -->
    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <strong>Félicitations !</strong> Votre candidature a été envoyée avec succès.
        </div>
        <div class="text-center">
            <a href="index.php?action=mes-candidatures" class="btn primary">
                <i class="fas fa-briefcase"></i>
                Voir mes candidatures
            </a>
            <a href="index.php?action=offres" class="btn secondary">
                <i class="fas fa-search"></i>
                Voir d'autres offres
            </a>
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

        <!-- Formulaire de candidature -->
        <form method="POST" class="candidature-form">
            <h2><i class="fas fa-paper-plane"></i> Postuler à cette offre</h2>
            
            <div class="form-group">
                <label class="form-label" for="cv">
                    <i class="fas fa-file-pdf"></i>
                    Lien vers votre CV (URL)
                </label>
                <input type="url" id="cv" name="cv" class="form-control" 
                       placeholder="https://drive.google.com/... ou https://linkedin.com/in/..." 
                       value="<?php echo htmlspecialchars($_POST['cv'] ?? ''); ?>">
                <div class="form-help">Facultatif - Lien vers votre CV en ligne (Google Drive, Dropbox, etc.)</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="linkedin">
                    <i class="fab fa-linkedin"></i>
                    Profil LinkedIn
                </label>
                <input type="url" id="linkedin" name="linkedin" class="form-control" 
                       placeholder="https://linkedin.com/in/votre-profil" 
                       value="<?php echo htmlspecialchars($_POST['linkedin'] ?? ''); ?>">
                <div class="form-help">Facultatif - Votre profil LinkedIn professionnel</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="lettre_motivation">
                    <i class="fas fa-envelope-open-text"></i>
                    Lettre de motivation *
                </label>
                <textarea id="lettre_motivation" name="lettre_motivation" class="form-control" 
                          placeholder="Expliquez pourquoi vous êtes le candidat idéal pour ce poste. Décrivez vos compétences, votre expérience et votre motivation..." 
                          rows="8" required><?php echo htmlspecialchars($_POST['lettre_motivation'] ?? ''); ?></textarea>
                <div class="form-help">Cette lettre est très importante pour convaincre l'employeur de votre candidature</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="notes">
                    <i class="fas fa-sticky-note"></i>
                    Informations complémentaires
                </label>
                <textarea id="notes" name="notes" class="form-control" 
                          placeholder="Disponibilités, questions, informations supplémentaires, particularités..." 
                          rows="4"><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                <div class="form-help">Facultatif - Toute information supplémentaire que vous souhaitez partager avec le recruteur</div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn primary">
                    <i class="fas fa-paper-plane"></i>
                    Envoyer ma candidature
                </button>
                <a href="index.php?action=details-offre&id=<?php echo $offreId; ?>" class="btn secondary">
                    <i class="fas fa-arrow-left"></i>
                    Retour à l'offre
                </a>
                <a href="index.php?action=offres" class="btn ghost">
                    <i class="fas fa-times"></i>
                    Annuler
                </a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>