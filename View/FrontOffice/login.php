<?php
require_once __DIR__ . '/../../Model/UserModel.php';

$userModel = new UserModel();
$errors = [];
$old_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    $old_data = $_POST;

    $errors = $userModel->validateLogin($email, $mot_de_passe);

    if (empty($errors)) {
        $result = $userModel->login($email, $mot_de_passe);
        if ($result['success']) {
            $_SESSION['user_id'] = $result['user']['Id_utilisateur'];
            $_SESSION['user_name'] = $result['user']['prenom'] . ' ' . $result['user']['nom'];
            $_SESSION['role'] = $result['user']['role'];
            header('Location: index.php?action=list');
            exit;
        } else {
            $errors[] = $result['message'];
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ImpactAble</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="View/assets/css/style.css">
</head>
<body>

<div class="container">
    <div class="form-container" style="max-width: 450px; margin-top: 60px;">
        <div class="form-card">
            <div class="auth-decoration">
                <div class="auth-icon">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
            </div>
            
            <div class="form-header">
                <h2>Connexion</h2>
                <p>Bienvenue sur ImpactAble</p>
            </div>

            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-error">
                    <strong>Erreurs :</strong>
                    <ul style="margin: 8px 0 0 20px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (isset($message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?action=login" method="POST" id="loginForm">
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" name="email" id="email" class="input" placeholder="votre@email.com" value="<?= isset($old_data['email']) ? htmlspecialchars($old_data['email']) : '' ?>" >
                </div>

                <div class="form-group">
                    <label for="mot_de_passe">
                        <i class="fas fa-lock"></i> Mot de passe
                    </label>
                    <input type="password" name="mot_de_passe" id="mot_de_passe" class="input" placeholder="Votre mot de passe">
                </div>

                <button type="submit" class="btn primary" style="width: 100%; margin-top: 20px;">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>

            <div class="form-footer" style="margin-top: 24px; text-align: center; flex-direction: column;">
                <p style="color: var(--muted);">
                    Pas encore de compte ? 
                    <a href="index.php?action=register" style="color: var(--copper); font-weight: 600;">
                        S'inscrire
                    </a>
                </p>
                <a href="index.php?action=list" style="color: var(--muted); margin-top: 12px;">
                    <i class="fas fa-arrow-left"></i> Retour au forum
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('mot_de_passe').value;
    let errors = [];
    
    if (!email) {
        errors.push('L\'email est obligatoire');
    }
    
    if (!password) {
        errors.push('Le mot de passe est obligatoire');
    }
    
    if (errors.length > 0) {
        e.preventDefault();
        alert(errors.join('\n'));
    }
});
</script>

</body>
</html>
