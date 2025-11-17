<?php
// PROJT/view/forum.php
if (!isset($posts)) {
    header('Location: ../control/control.php?action=list');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactAble – Forum</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --brown: #4b2e16;
            --copper: #b47b47;
            --moss: #5e6d3b;
            --sage: #a9b97d;
            --sand: #f4ecdd;
            --white: #fffaf5;
            --light-sage: #e1e8c9;
            --dark-green: #3a4a2a;
            --brown-600: rgba(75,46,22,0.9);
            --brown-300: rgba(75,46,22,0.2);
            --muted: #6b4b44;
            --card-bg: #ffffff;
            --radius: 16px;
            --radius-sm: 10px;
            --shadow: 0 8px 22px rgba(75,46,22,0.08);
            --shadow-lg: 0 12px 30px rgba(75,46,22,0.12);
            --maxw: 1100px;
            --focus: 0 0 0 3px rgba(180,123,71,0.18);
            --input-height: 48px;
            --gap: 1rem;
            --ease-s: 200ms cubic-bezier(.2,.9,.2,1);
            --ease-l: 350ms cubic-bezier(.2,.9,.2,1);
            --font-sans: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
        }

        * { 
            box-sizing: border-box; 
            margin: 0;
            padding: 0;
        }
        
        html, body { 
            height: 100%; 
            overflow-x: hidden;
        }
        
        body {
            background: var(--sand);
            color: var(--brown);
            line-height: 1.6;
            font-size: 15px;
            -webkit-font-smoothing: antialiased;
            font-family: var(--font-sans);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a { 
            color: inherit; 
            text-decoration: none;
        }
        
        img { 
            max-width: 100%; 
            display: block; 
            height: auto;
        }
        
        button { 
            font-family: inherit; 
            cursor: pointer;
            border: none;
            background: none;
        }
        
        ul, ol {
            list-style: none;
        }

        /* Layout principal */
        .main-container {
            flex: 1;
            width: 100%;
            max-width: var(--maxw);
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Header */
        .site-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            background: var(--white);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
            margin-bottom: 15px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-toggle {
            font-size: 1.2rem;
            color: var(--brown);
            padding: 6px;
            border-radius: var(--radius-sm);
            transition: all var(--ease-s);
        }

        .nav-toggle:hover {
            background: var(--brown-300);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--moss);
        }

        .logo-image {
            height: 32px;
            width: auto;
            object-fit: contain;
        }

        .header-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 14px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all var(--ease-s);
            border: none;
            gap: 5px;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .btn.primary {
            background: var(--sage);
            color: var(--brown);
        }

        .btn.primary:hover {
            background: var(--moss);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn.admin {
            background: var(--copper);
            color: white;
        }

        .btn.admin:hover {
            background: #a56a3a;
        }

        /* Side Panel */
        .side-panel {
            position: fixed;
            top: 0;
            left: -280px;
            width: 280px;
            height: 100vh;
            background: var(--white);
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            transition: left var(--ease-l);
            display: flex;
            flex-direction: column;
        }

        .side-panel.active {
            left: 0;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 18px;
            border-bottom: 1px solid var(--brown-300);
        }

        .panel-close {
            font-size: 1.1rem;
            color: var(--brown);
            padding: 6px;
            border-radius: var(--radius-sm);
            transition: all var(--ease-s);
        }

        .panel-close:hover {
            background: var(--brown-300);
        }

        .panel-nav {
            flex: 1;
            padding: 18px 0;
            overflow-y: auto;
        }

        .nav-section {
            margin-bottom: 18px;
        }

        .nav-title {
            padding: 0 18px 6px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 18px;
            color: var(--brown);
            transition: all var(--ease-s);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .nav-link:hover, .nav-link.active {
            background: var(--sage);
            color: var(--brown);
        }

        .nav-link i {
            width: 18px;
            text-align: center;
            font-size: 0.9rem;
        }

        .panel-footer {
            padding: 14px 18px;
            border-top: 1px solid var(--brown-300);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--copper);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .user-info h4 {
            font-size: 0.85rem;
            margin-bottom: 2px;
        }

        .user-info p {
            font-size: 0.75rem;
            color: var(--muted);
        }

        .panel-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all var(--ease-l);
        }

        .panel-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Section Forum */
        .section {
            padding: 25px 0;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .section-header h2 {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--brown);
        }

        .section-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 7px 12px;
            background: var(--sage);
            color: var(--brown);
            border-radius: var(--radius-sm);
            font-weight: 600;
            transition: all var(--ease-s);
            font-size: 0.85rem;
        }

        .section-link:hover {
            background: var(--moss);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .forum-container {
            display: grid;
            grid-template-columns: 1fr 260px;
            gap: 18px;
        }

        /* Forum Cards */
        .forum-card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 18px;
        }

        .forum-header {
            padding: 14px 18px;
            border-bottom: 1px solid var(--brown-300);
        }

        .forum-header h3 {
            font-size: 1rem;
            font-weight: 600;
        }

        .forum-body {
            padding: 18px;
        }

        /* Posts */
        .post {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 18px;
            transition: transform var(--ease-s);
        }

        .post:hover {
            transform: translateY(-2px);
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 14px 18px;
            border-bottom: 1px solid var(--brown-300);
        }

        .post-author {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            flex: 1;
        }

        .author-info {
            display: flex;
            flex-direction: column;
        }

        .author-name {
            font-weight: 600;
            color: var(--brown);
            font-size: 0.9rem;
        }

        .post-time {
            font-size: 0.75rem;
            color: var(--muted);
        }

        .post-menu-btn {
            color: var(--muted);
            padding: 5px;
            border-radius: var(--radius-sm);
            transition: all var(--ease-s);
            font-size: 0.9rem;
        }

        .post-menu-btn:hover {
            background: var(--brown-300);
        }

        .post-content {
            padding: 14px 18px;
        }

        .post-text h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--brown);
        }

        .post-text p {
            margin-bottom: 8px;
            color: var(--muted);
            line-height: 1.6;
            font-size: 0.9rem;
        }

        .post-image {
            width: 100%;
            border-radius: var(--radius-sm);
            margin-top: 10px;
        }

        .post-stats {
            display: flex;
            gap: 12px;
            padding: 0 18px 8px;
            border-bottom: 1px solid var(--brown-300);
        }

        .post-stat {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.8rem;
            color: var(--muted);
        }

        .post-interactions {
            display: flex;
            padding: 10px 18px;
            flex-wrap: wrap;
            gap: 4px;
        }

        .interaction-btn {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 5px 10px;
            color: var(--muted);
            font-weight: 500;
            transition: all var(--ease-s);
            font-size: 0.8rem;
            border-radius: 6px;
        }

        .interaction-btn:hover {
            background: var(--brown-300);
            color: var(--brown);
        }

        /* Forum Sidebar */
        .forum-sidebar {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .forum-categories {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .category {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid var(--brown-300);
        }

        .category:last-child {
            border-bottom: none;
        }

        .category-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .category-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--sage);
            color: var(--brown);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }

        .category-name {
            font-weight: 500;
            font-size: 0.85rem;
        }

        .category-count {
            font-size: 0.75rem;
            color: var(--muted);
        }

        .top-contributors {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .contributor {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .contributor-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--copper);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .contributor-info {
            flex: 1;
        }

        .contributor-name {
            font-weight: 600;
            font-size: 0.8rem;
        }

        .contributor-stats {
            font-size: 0.7rem;
            color: var(--muted);
        }

        .contributor-points {
            font-weight: 600;
            color: var(--copper);
            font-size: 0.75rem;
        }

        /* Footer */
        .site-footer {
            background: var(--dark-green);
            color: white;
            padding: 25px 0 12px;
            margin-top: 35px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 22px;
            margin-bottom: 22px;
        }

        .footer-column h3 {
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .text-muted {
            color: rgba(255,255,255,0.7);
            margin-bottom: 10px;
            font-size: 0.85rem;
        }

        .social-links {
            display: flex;
            gap: 6px;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            transition: all var(--ease-s);
            font-size: 0.9rem;
        }

        .social-links a:hover {
            background: var(--sage);
            color: var(--brown);
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .footer-links a {
            color: rgba(255,255,255,0.7);
            transition: all var(--ease-s);
            font-size: 0.85rem;
        }

        .footer-links a:hover {
            color: white;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 12px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.7);
            font-size: 0.8rem;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .forum-container {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .forum-sidebar {
                order: -1;
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
                gap: 15px;
            }
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 0 12px;
            }

            .site-header {
                padding: 8px 0;
                margin-bottom: 12px;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .post-interactions {
                flex-direction: column;
            }
            
            .interaction-btn {
                justify-content: flex-start;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .header-actions {
                flex-direction: column;
                gap: 6px;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .main-container {
                padding: 0 10px;
            }

            .brand {
                gap: 6px;
            }

            .logo {
                font-size: 1rem;
            }

            .logo-image {
                height: 28px;
            }

            .section-header h2 {
                font-size: 1.2rem;
            }

            .forum-sidebar {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="main-container">
    <!-- Header -->
    <header class="site-header" role="banner">
        <div class="brand">
            <button class="nav-toggle" id="navToggle" aria-label="Ouvrir le menu">
                <i class="fas fa-bars"></i>
            </button>
            <div class="logo">
                <img src="../assets/images/logo.png" alt="ImpactAble" class="logo-image">
                
            </div>
        </div>

        <div class="header-actions">
            <a href="control.php?action=create" class="btn primary">
                <i class="fas fa-plus"></i> Créer un Post
            </a>
            <?php if ($is_admin): ?>
            <a href="control.php?action=admin" class="btn admin">
                <i class="fas fa-cog"></i> Admin
            </a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Side Panel Navigation -->
    <div class="side-panel" id="sidePanel">
        <div class="panel-header">
            <div class="logo">
                <img src="../assets/images/logo.png" alt="ImpactAble" class="logo-image">
                
            </div>
            <button class="panel-close" id="panelClose">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
       <nav class="panel-nav">
    <div class="nav-section">
        <div class="nav-title">Navigation</div>
        <a href="control.php?action=list" class="nav-link active">
            <i class="fas fa-home"></i>
            <span>Accueil</span>
        </a>
        <a href="control.php?action=create" class="nav-link">
            <i class="fas fa-plus"></i>
            <span>Créer un Post</span>
        </a>
        <?php if ($is_admin): ?>
            <a href="control.php?action=admin" class="nav-link">
                <i class="fas fa-cog"></i>
                <span>Admin</span>
            </a>
        <?php endif; ?>
    </div>
    
    <div class="nav-section">
        <div class="nav-title">Catégories</div>
        <a href="control.php?action=filter&category=opportunites" class="nav-link">
            <i class="fas fa-briefcase"></i>
            <span>Opportunités</span>
        </a>
        <a href="control.php?action=filter&category=campagnes" class="nav-link">
            <i class="fas fa-lightbulb"></i>
            <span>Campagnes</span>
        </a>
        <a href="control.php?action=filter&category=ressources" class="nav-link">
            <i class="fas fa-hand-holding-heart"></i>
            <span>Ressources</span>
        </a>
        <a href="control.php?action=filter&category=evenements" class="nav-link">
            <i class="fas fa-calendar-alt"></i>
            <span>Événements</span>
        </a>
        <a href="control.php?action=filter&category=questions" class="nav-link">
            <i class="fas fa-question-circle"></i>
            <span>Questions</span>
        </a>
    </div>
</nav>
        
        <div class="panel-footer">
            <div class="user-profile">
                <div class="user-avatar"><?= isset($user_name) ? strtoupper(substr($user_name, 0, 1)) : 'VS' ?></div>
                <div class="user-info">
                    <h4><?= isset($user_name) ? htmlspecialchars($user_name) : 'Visiteur' ?></h4>
                    <p><?= isset($user_name) ? 'Connecté' : 'Connectez-vous pour plus de fonctionnalités' ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="panel-overlay" id="panelOverlay"></div>

    <!-- Forum Main Content -->
    <section class="section">
      <div class="section-header">
        <h2>Forum Communautaire</h2>
        
      </div>
        
        <div class="forum-container">
            <div class="forum-main">
                <!-- Forum Posts -->
                <div class="forum-posts">
                    <?php if (empty($posts)): ?>
                        <div class="forum-card">
                            <div class="forum-body">
                                <p style="text-align: center; color: var(--muted);">Aucun post pour le moment. Soyez le premier à partager !</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                        <div class="post">
                            <div class="post-header">
                                <div class="post-author">
                                    <div class="user-avatar"><?= strtoupper(substr($post['auteur'] ?? 'U', 0, 1)) ?></div>
                                    <div class="author-info">
                                        <div class="author-name"><?= htmlspecialchars($post['auteur'] ?? 'Utilisateur') ?></div>
                                        <div class="post-time"><?= date('d/m/Y à H:i', strtotime($post['date_creation'])) ?></div>
                                    </div>
                                </div>
                                <div class="post-actions-menu">
                                    <button class="post-menu-btn">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="post-content">
                                <div class="post-text">
                                    <h3><?= htmlspecialchars($post['titre']) ?></h3>
                                    <p><?= nl2br(htmlspecialchars($post['contenu'])) ?></p>
                                </div>
                                <?php if (!empty($post['piece_jointe'])): ?>
                                    <img src="../<?= $post['piece_jointe'] ?>" class="post-image" alt="Image du post">
                                <?php endif; ?>
                            </div>
                            
                            <div class="post-stats">
                                <div class="post-stat">
                                    <i class="fas fa-heart"></i>
                                    <span><?= $post['likes'] ?> j'aime</span>
                                </div>
                                <div class="post-stat">
                                    <i class="fas fa-tag"></i>
                                    <span><?= ucfirst($post['categorie']) ?></span>
                                </div>
                            </div>
                            
                            <div class="post-interactions">
                                <a href="control.php?action=view&id=<?= $post['Id_post'] ?>" class="interaction-btn">
                                    <i class="fas fa-eye"></i>
                                    <span>Voir & Commenter</span>
                                </a>
                                
                                <?php if ($post['Id_utilisateur'] == $user_id || $is_admin): ?>
                                    <a href="control.php?action=edit&id=<?= $post['Id_post'] ?>" class="interaction-btn">
                                        <i class="fas fa-edit"></i>
                                        <span>Modifier</span>
                                    </a>
                                    <a href="control.php?action=delete&id=<?= $post['Id_post'] ?>" 
                                       class="interaction-btn" 
                                       onclick="return confirm('Supprimer ce post ?')">
                                        <i class="fas fa-trash"></i>
                                        <span>Supprimer</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Forum Sidebar -->
            <div class="forum-sidebar">
                <!-- Categories -->
                <div class="forum-card">
                    <div class="forum-header">
                        <h3>Catégories</h3>
                    </div>
                    <div class="forum-body">
                        <div class="forum-categories">
                            <div class="category">
                                <div class="category-info">
                                    <div class="category-icon">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <div class="category-name">Opportunités</div>
                                </div>
                                <div class="category-count">42</div>
                            </div>
                            <div class="category">
                                <div class="category-info">
                                    <div class="category-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="category-name">Événements</div>
                                </div>
                                <div class="category-count">15</div>
                            </div>
                            <div class="category">
                                <div class="category-info">
                                    <div class="category-icon">
                                        <i class="fas fa-lightbulb"></i>
                                    </div>
                                    <div class="category-name">Campagnes</div>
                                </div>
                                <div class="category-count">28</div>
                            </div>
                            <div class="category">
                                <div class="category-info">
                                    <div class="category-icon">
                                        <i class="fas fa-question-circle"></i>
                                    </div>
                                    <div class="category-name">Questions</div>
                                </div>
                                <div class="category-count">33</div>
                            </div>
                            <div class="category">
                                <div class="category-info">
                                    <div class="category-icon">
                                        <i class="fas fa-hand-holding-heart"></i>
                                    </div>
                                    <div class="category-name">Ressources</div>
                                </div>
                                <div class="category-count">19</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Top Contributors -->
                <div class="forum-card">
                    <div class="forum-header">
                        <h3>Top Contributeurs</h3>
                    </div>
                    <div class="forum-body">
                        <div class="top-contributors">
                            <div class="contributor">
                                <div class="contributor-avatar">SM</div>
                                <div class="contributor-info">
                                    <div class="contributor-name">Sarah Martin</div>
                                    <div class="contributor-stats">8 posts, 142 likes</div>
                                </div>
                                <div class="contributor-points">285</div>
                            </div>
                            <div class="contributor">
                                <div class="contributor-avatar">MD</div>
                                <div class="contributor-info">
                                    <div class="contributor-name">Marina Dubois</div>
                                    <div class="contributor-stats">6 posts, 98 likes</div>
                                </div>
                                <div class="contributor-points">187</div>
                            </div>
                            <div class="contributor">
                                <div class="contributor-avatar">TL</div>
                                <div class="contributor-info">
                                    <div class="contributor-name">Thomas Leroy</div>
                                    <div class="contributor-stats">5 posts, 76 likes</div>
                                </div>
                                <div class="contributor-points">154</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Forum Rules -->
                <div class="forum-card">
                    <div class="forum-header">
                        <h3>Règles du Forum</h3>
                    </div>
                    <div class="forum-body">
                        <div style="font-size: 0.8rem; color: var(--muted); line-height: 1.5;">
                            <p>• Respectez tous les membres</p>
                            <p>• Partagez des contenus pertinents</p>
                            <p>• Utilisez un langage inclusif</p>
                            <p>• Citez vos sources</p>
                            <p>• Signalez les contenus inappropriés</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="main-container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>ImpactAble</h3>
                    <p class="text-muted">Plateforme dédiée à l'inclusion et à l'impact social.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Navigation</h3>
                    <div class="footer-links">
                        <a href="control.php?action=list">Accueil</a>
                        <a href="control.php?action=create">Créer un Post</a>
                        <?php if ($is_admin): ?>
                            <a href="control.php?action=admin">Admin</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Légal</h3>
                    <div class="footer-links">
                        <a href="#">Mentions légales</a>
                        <a href="#">Politique de confidentialité</a>
                        <a href="#">Conditions d'utilisation</a>
                        <a href="#">Accessibilité</a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Contact</h3>
                    <div class="footer-links">
                        <a href="mailto:contact@impactable.org">contact@impactable.org</a>
                        <a href="tel:+21612345678">+216 12 345 678</a>
                        <a href="#">Tunis, Tunisia</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© <span id="year"><?= date('Y') ?></span> ImpactAble – Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</div>

<script>
    // Navigation Panel Toggle
    document.getElementById('navToggle').addEventListener('click', function() {
        document.getElementById('sidePanel').classList.add('active');
        document.getElementById('panelOverlay').classList.add('active');
    });

    document.getElementById('panelClose').addEventListener('click', function() {
        document.getElementById('sidePanel').classList.remove('active');
        document.getElementById('panelOverlay').classList.remove('active');
    });

    document.getElementById('panelOverlay').addEventListener('click', function() {
        document.getElementById('sidePanel').classList.remove('active');
        this.classList.remove('active');
    });

    // Set current year in footer
    document.getElementById('year').textContent = new Date().getFullYear();
</script>

</body>
</html>