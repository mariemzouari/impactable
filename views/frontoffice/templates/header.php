<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? Config::SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo Config::getBaseUrl(); ?>/assets/css/style.css">
</head>
<body>
    <div class="container">
        <header class="site-header" role="banner">
            <div class="brand">
                <button class="nav-toggle" id="navToggle" aria-label="Ouvrir le menu">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="logo">
                    <img src="<?php echo Config::getBaseUrl(); ?>/assets/images/logo.png" alt="ImpactAble" class="logo-image">
                </div>
            </div>
            <div class="header-actions">
                <?php if (Utils::isAuthenticated()): ?>
                    <span class="user-welcome">Bonjour, <?php echo Utils::escape($_SESSION['user_prenom']); ?></span>
                    <a href="index.php?action=offres" class="btn ghost">
                        <i class="fas fa-briefcase"></i>
                        Voir les offres
                    </a>
                            <a href="index.php?action=mes-candidatures" class="btn ghost">
            <i class="fas fa-briefcase"></i>
            Mes candidatures
        </a>
                <a href="index.php?action=mes-offres" class="btn secondary">
            <i class="fas fa-list"></i>
            Mes offres
        </a>

                    <?php if (Utils::isAuthenticated() && $_SESSION['user_role'] === 'admin'): ?>
    <li class="nav-item">
        <a class="nav-link" href="index.php?action=admin-dashboard">
            <i class="fas fa-cog"></i> Administration
        </a>
    </li>
<?php endif; ?>
                    <a href="index.php?action=deconnexion" class="btn secondary">
                        <i class="fas fa-sign-out-alt"></i>
                        Déconnexion
                    </a>
                <?php else: ?>
                    <a href="index.php?action=connexion" class="btn primary">Se connecter</a>
                <?php endif; ?>
            </div>
        </header>