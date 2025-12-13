<?php
require_once __DIR__ . '/../../Model/UserModel.php';

$userModel = new UserModel();
$errors = [];
$old_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $confirmer_mot_de_passe = $_POST['confirmer_mot_de_passe'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? null;
    $handicap = $_POST['handicap'] ?? '';

    $old_data = $_POST;

    $errors = $userModel->validateRegistration($nom, $prenom, $email, $mot_de_passe, $confirmer_mot_de_passe);

    $bannedWords = ['spam', 'arnaque', 'hack', 'pirate'];
    foreach ($bannedWords as $word) {
        if (stripos($nom . ' ' . $prenom, $word) !== false) {
            $errors[] = "Votre nom contient des termes inappropriés";
            break;
        }
    }

    if (empty($errors)) {
        $result = $userModel->register($nom, $prenom, $email, $mot_de_passe, $telephone, $date_naissance, $handicap);
        if ($result['success']) {
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['user_name'] = $prenom . ' ' . $nom;
            $_SESSION['role'] = 'user';
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
    <title>Inscription - ImpactAble</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="View/assets/css/style.css">
</head>
<body>

<div class="container">
    <div class="form-container" style="max-width: 600px; margin-top: 60px;">
        <div class="form-card">
            <div class="auth-decoration">
                <div class="auth-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
            
            <div class="form-header">
                <h2>Inscription</h2>
                <p>Rejoignez la communauté ImpactAble</p>
            </div>

            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-error">
                    <strong>Erreurs à corriger :</strong>
                    <ul style="margin: 8px 0 0 20px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="index.php?action=register" method="POST" id="registerForm">
                <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="nom">
                            <i class="fas fa-user"></i> Nom
                        </label>
                        <input type="text" name="nom" id="nom" class="input" placeholder="Votre nom" value="<?= isset($old_data['nom']) ? htmlspecialchars($old_data['nom']) : '' ?>" >
                    </div>

                    <div class="form-group">
                        <label for="prenom">
                            <i class="fas fa-user"></i> Prénom
                        </label>
                        <input type="text" name="prenom" id="prenom" class="input" placeholder="Votre prénom" value="<?= isset($old_data['prenom']) ? htmlspecialchars($old_data['prenom']) : '' ?>" >
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" name="email" id="email" class="input" placeholder="votre@email.com" value="<?= isset($old_data['email']) ? htmlspecialchars($old_data['email']) : '' ?>" >
                </div>

                <div class="form-group">
                    <label for="telephone">
                        <i class="fas fa-phone"></i> Téléphone (optionnel)
                    </label>
                    <input type="tel" name="telephone" id="telephone" class="input" placeholder="+216 XX XXX XXX" value="<?= isset($old_data['telephone']) ? htmlspecialchars($old_data['telephone']) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="date_naissance">
                        <i class="fas fa-calendar"></i> Date de naissance (optionnel)
                    </label>
                    <input type="date" name="date_naissance" id="date_naissance" class="input" value="<?= isset($old_data['date_naissance']) ? htmlspecialchars($old_data['date_naissance']) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="mot_de_passe">
                        <i class="fas fa-lock"></i> Mot de passe
                    </label>
                    <input type="password" name="mot_de_passe" id="mot_de_passe" class="input" placeholder="Au moins 6 caractères" >
                </div>

                <div class="form-group">
                    <label for="confirmer_mot_de_passe">
                        <i class="fas fa-lock"></i> Confirmer le mot de passe
                    </label>
                    <input type="password" name="confirmer_mot_de_passe" id="confirmer_mot_de_passe" class="input" placeholder="Confirmez votre mot de passe" >
                </div>

                <div class="form-group">
                    <label for="handicap">
                        <i class="fas fa-wheelchair"></i> Type de handicap (optionnel)
                    </label>
                    <select name="handicap" id="handicap" class="select">
                        <option value="">Sélectionnez (optionnel)</option>
                        <option value="moteur" <?= (isset($old_data['handicap']) && $old_data['handicap'] == 'moteur') ? 'selected' : '' ?>>Moteur</option>
                        <option value="visuel" <?= (isset($old_data['handicap']) && $old_data['handicap'] == 'visuel') ? 'selected' : '' ?>>Visuel</option>
                        <option value="auditif" <?= (isset($old_data['handicap']) && $old_data['handicap'] == 'auditif') ? 'selected' : '' ?>>Auditif</option>
                        <option value="mental" <?= (isset($old_data['handicap']) && $old_data['handicap'] == 'mental') ? 'selected' : '' ?>>Mental</option>
                        <option value="autre" <?= (isset($old_data['handicap']) && $old_data['handicap'] == 'autre') ? 'selected' : '' ?>>Autre</option>
                    </select>
                </div>

                <button type="submit" class="btn primary" style="width: 100%; margin-top: 20px;">
                    <i class="fas fa-user-plus"></i> S'inscrire
                </button>
            </form>

            <div class="form-footer" style="margin-top: 24px; text-align: center; flex-direction: column;">
                <p style="color: var(--muted);">
                    Vous avez déjà un compte ? 
                    <a href="index.php?action=login" style="color: var(--copper); font-weight: 600;">
                        Se connecter
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
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const nom = document.getElementById('nom').value.trim();
    const prenom = document.getElementById('prenom').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('mot_de_passe').value;
    const confirmPassword = document.getElementById('confirmer_mot_de_passe').value;
    let errors = [];
    
    if (!nom) errors.push('Le nom est obligatoire');
    if (!prenom) errors.push('Le prénom est obligatoire');
    if (!email) errors.push('L\'email est obligatoire');
    if (!password) errors.push('Le mot de passe est obligatoire');
    if (password.length < 6) errors.push('Le mot de passe doit contenir au moins 6 caractères');
    if (password !== confirmPassword) errors.push('Les mots de passe ne correspondent pas');
    
    if (errors.length > 0) {
        e.preventDefault();
        alert(errors.join('\n'));
    }
});
</script>

</body>
</html>
