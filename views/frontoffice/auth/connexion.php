<?php 
$title = "Connexion | " . Config::SITE_NAME;
require_once __DIR__ . '/../templates/header.php'; 
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <i class="fas fa-lock"></i>
            </div>
            <h1>Connexion</h1>
            <p class="text-muted">Accédez à votre compte</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert-error">
                <?php foreach ($errors as $error): ?>
                    <div><?php echo $error; ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=connexion">
            <div class="form-group">
                <label class="form-label" for="email">
                    <i class="fas fa-envelope"></i>
                    Adresse email
                </label>
                <input type="email" id="email" name="email" class="form-control" 
                       placeholder="votre@email.com" 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="mot_de_passe">
                    <i class="fas fa-lock"></i>
                    Mot de passe
                </label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" 
                       placeholder="Votre mot de passe" required>
            </div>

            <button type="submit" class="btn primary" style="width: 100%;">
                <i class="fas fa-sign-in-alt"></i>
                Se connecter
            </button>
        </form>

        <div class="auth-footer">
            <p class="text-muted">Vous n'avez pas de compte ?</p>
            <a href="inscription.php" class="btn secondary" style="width: 100%;">
                <i class="fas fa-user-plus"></i>
                Créer un compte
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>