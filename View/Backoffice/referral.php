<?php
session_start();
include_once __DIR__ . '/../../Model/FrontCampagneController.php';
include_once __DIR__ . '/../../Model/ReferralController.php';

$frontController = new FrontCampagneController();
$referralC = new ReferralController();
$campagnesProblemes = $frontController->getCampagnesAvecProblemes();
$countProblemes = count($campagnesProblemes);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Pour l'admin, on peut utiliser un ID par défaut ou rediriger
    $userId = 1; // ID admin par défaut
    // Ou: header('Location: login.php'); exit();
} else {
    $userId = $_SESSION['user_id'];
}

// Générer ou récupérer le code de parrainage
$codeParrainage = $referralC->genererCodeParrainage($userId);
$statsParrainage = $referralC->getStatsParrainage($userId);

// Générer le lien complet
$lienParrainage = "https://impactable.tn/ref/" . $codeParrainage;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactAble — Programme de Parrainage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .referral-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card-referral {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-icon-referral {
            font-size: 2.5em;
            color: var(--sage);
            margin-bottom: 10px;
        }

        .stat-number-referral {
            font-size: 2em;
            font-weight: bold;
            color: #333;
            margin: 10px 0;
        }

        .referral-link-box {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 25px;
            margin: 20px 0;
            border-left: 5px solid var(--sage);
        }

        .link-display {
            display: flex;
            gap: 10px;
            margin: 15px 0;
        }

        .link-input {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-family: monospace;
            font-size: 1em;
            background: white;
        }

        .share-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .btn-share {
            flex: 1;
            min-width: 120px;
            padding: 12px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-facebook {
            background: #1877f2;
            color: white;
        }

        .btn-whatsapp {
            background: #25d366;
            color: white;
        }

        .btn-email {
            background: #ea4335;
            color: white;
        }

        .btn-copy {
            background: var(--sage);
            color: white;
        }

        .btn-share:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .referred-list {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .referred-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .referred-item:last-child {
            border-bottom: none;
        }

        .badge-success {
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
        }

        .badge-pending {
            background: #ffc107;
            color: #333;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
        }

        .qr-code {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .how-it-works {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            margin-top: 30px;
        }

        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            gap: 15px;
        }

        .step-number {
            background: var(--sage);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <!-- Sidebar (identique à index.php) -->
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
                        <a href="#" class="sidebar-link dropdown-toggle" aria-expanded="true">
                            <i class="fas fa-hand-holding-heart"></i>
                            <span>Campagnes</span>
                            <?php if (isset($countProblemes) && $countProblemes > 0): ?>
                                <span class="alert-count"><?php echo $countProblemes; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <div class="sidebar-submenu show">
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
                            <a href="referral.php" class="submenu-link active">
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

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div>
                    <h2>Programme de Parrainage</h2>
                    <p class="text-muted">Invitez des amis et gagnez des récompenses</p>
                </div>
                <div class="header-actions">
                    <a href="stats_dashboard.php" class="btn">
                        <i class="fas fa-chart-bar"></i>
                        Voir Statistiques
                    </a>
                </div>
            </header>

            <div class="admin-content">
                <!-- Statistiques de parrainage -->
                <div class="referral-stats">
                    <div class="stat-card-referral">
                        <div class="stat-icon-referral">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="stat-number-referral"><?php echo $statsParrainage['total_referres'] ?? 0; ?></div>
                        <div class="stat-label">Personnes Parrainées</div>
                    </div>

                    <div class="stat-card-referral">
                        <div class="stat-icon-referral">
                            <i class="fas fa-donate"></i>
                        </div>
                        <div class="stat-number-referral">
                            <?php echo number_format($statsParrainage['dons_generes'] ?? 0, 0); ?> TND
                        </div>
                        <div class="stat-label">Dons Générés</div>
                    </div>

                    <div class="stat-card-referral">
                        <div class="stat-icon-referral">
                            <i class="fas fa-gift"></i>
                        </div>
                        <div class="stat-number-referral">
                            <?php echo number_format($statsParrainage['recompenses'] ?? 0, 0); ?> TND
                        </div>
                        <div class="stat-label">Récompenses</div>
                    </div>

                    <div class="stat-card-referral">
                        <div class="stat-icon-referral">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="stat-number-referral"><?php echo $statsParrainage['taux_conversion'] ?? 0; ?>%</div>
                        <div class="stat-label">Taux de Conversion</div>
                    </div>
                </div>

                <!-- Lien de parrainage -->
                <div class="content-card">
                    <div class="card-header">
                        <h3><i class="fas fa-link"></i> Votre Lien de Parrainage Unique</h3>
                    </div>
                    <div class="card-body">
                        <div class="referral-link-box">
                            <p><strong>Partagez ce lien avec vos amis :</strong> Pour chaque personne qui s'inscrit via
                                votre lien et fait un don, vous gagnez une récompense !</p>

                            <div class="link-display">
                                <input type="text" id="referralLink" value="<?php echo $lienParrainage; ?>" readonly
                                    class="link-input">
                                <button onclick="copyToClipboard()" class="btn-share btn-copy">
                                    <i class="fas fa-copy"></i> Copier
                                </button>
                            </div>

                            <div class="share-buttons">
                                <button onclick="shareOnFacebook()" class="btn-share btn-facebook">
                                    <i class="fab fa-facebook-f"></i> Facebook
                                </button>
                                <button onclick="shareOnWhatsApp()" class="btn-share btn-whatsapp">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </button>
                                <button onclick="shareByEmail()" class="btn-share btn-email">
                                    <i class="fas fa-envelope"></i> Email
                                </button>
                                <button onclick="shareOnTwitter()" class="btn-share"
                                    style="background: #1da1f2; color: white;">
                                    <i class="fab fa-twitter"></i> Twitter
                                </button>
                            </div>
                        </div>

                        <!-- QR Code pour mobile -->
                        <div class="qr-code">
                            <h4><i class="fas fa-qrcode"></i> QR Code pour Mobile</h4>
                            <div id="qrcode"></div>
                            <p class="text-muted" style="margin-top: 10px; font-size: 0.9em;">
                                Scannez ce QR code avec votre smartphone pour partager rapidement
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Comment ça marche -->
                <div class="how-it-works">
                    <h3><i class="fas fa-info-circle"></i> Comment ça marche ?</h3>

                    <div class="step">
                        <div class="step-number">1</div>
                        <div>
                            <h4>Partagez votre lien</h4>
                            <p>Copiez et partagez votre lien unique avec vos amis, famille ou sur les réseaux sociaux.
                            </p>
                        </div>
                    </div>

                    <div class="step">
                        <div class="step-number">2</div>
                        <div>
                            <h4>Ils s'inscrivent et donnent</h4>
                            <p>Vos amis s'inscrivent via votre lien et font un don à une campagne de leur choix.</p>
                        </div>
                    </div>

                    <div class="step">
                        <div class="step-number">3</div>
                        <div>
                            <h4>Vous gagnez des récompenses</h4>
                            <p>Pour chaque don effectué par une personne que vous avez parrainée, vous recevez 5% du
                                montant en crédits.</p>
                        </div>
                    </div>

                    <div class="step">
                        <div class="step-number">4</div>
                        <div>
                            <h4>Utilisez vos crédits</h4>
                            <p>Utilisez vos crédits pour faire vos propres dons ou les convertir en avantages.</p>
                        </div>
                    </div>
                </div>

                <!-- Liste des personnes parrainées -->
                <div class="referred-list">
                    <div class="card-header">
                        <h3><i class="fas fa-users"></i> Personnes que vous avez parrainées</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        // Récupérer la liste des personnes parrainées
                        $referredList = $referralC->getListeParraines($userId);

                        if (empty($referredList)): ?>
                            <p class="text-muted">Vous n'avez pas encore parrainé de personnes. Partagez votre lien pour
                                commencer !</p>
                        <?php else: ?>
                            <div style="display: grid; gap: 10px;">
                                <?php foreach ($referredList as $person): ?>
                                    <div class="referred-item">
                                        <div>
                                            <strong><?php echo htmlspecialchars($person['nom'] ?? 'Utilisateur'); ?></strong>
                                            <div style="font-size: 0.9em; color: #666;">
                                                Inscrit le: <?php echo date('d/m/Y', strtotime($person['date_inscription'])); ?>
                                                <?php if (!empty($person['email'])): ?>
                                                    | Email: <?php echo htmlspecialchars($person['email']); ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <?php if ($person['a_fait_don']): ?>
                                                <span class="badge-success">
                                                    <i class="fas fa-check"></i> A fait un don
                                                </span>
                                                <div style="font-size: 0.9em; margin-top: 5px;">
                                                    Montant: <?php echo number_format($person['montant_don'], 0); ?> TND
                                                </div>
                                            <?php else: ?>
                                                <span class="badge-pending">
                                                    <i class="fas fa-clock"></i> En attente
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Conditions et récompenses -->
                <div class="content-card">
                    <div class="card-header">
                        <h3><i class="fas fa-gift"></i> Récompenses et Conditions</h3>
                    </div>
                    <div class="card-body">
                        <div
                            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 6px;">
                                <h4><i class="fas fa-percentage" style="color: var(--sage);"></i> Commission</h4>
                                <p>Vous recevez <strong>5% du montant de chaque don</strong> effectué par une personne
                                    que vous avez parrainée.</p>
                            </div>

                            <div style="padding: 15px; background: #f8f9fa; border-radius: 6px;">
                                <h4><i class="fas fa-calendar-check" style="color: var(--sage);"></i> Validité</h4>
                                <p>Les crédits sont valables <strong>6 mois</strong> à partir de leur attribution.
                                    Utilisez-les avant qu'ils n'expirent !</p>
                            </div>

                            <div style="padding: 15px; background: #f8f9fa; border-radius: 6px;">
                                <h4><i class="fas fa-trophy" style="color: var(--sage);"></i> Niveaux de Parrainage</h4>
                                <ul style="margin-left: 20px;">
                                    <li><strong>Bronze:</strong> 1-5 parrainés</li>
                                    <li><strong>Argent:</strong> 6-20 parrainés</li>
                                    <li><strong>Or:</strong> 21+ parrainés</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- QR Code Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        // Générer QR Code
        document.addEventListener('DOMContentLoaded', function () {
            var qrcode = new QRCode(document.getElementById("qrcode"), {
                text: "<?php echo $lienParrainage; ?>",
                width: 150,
                height: 150,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });

            // Gestion des sous-menus
            const menuItems = document.querySelectorAll('.sidebar-link.has-submenu');
            menuItems.forEach(item => {
                item.addEventListener('click', function (e) {
                    if (e.target.closest('.sidebar-link')) {
                        e.preventDefault();
                        const subMenu = this.nextElementSibling;
                        document.querySelectorAll('.sub-menu').forEach(menu => {
                            if (menu !== subMenu) {
                                menu.classList.remove('active');
                                menu.previousElementSibling.classList.remove('active');
                            }
                        });
                        subMenu.classList.toggle('active');
                        this.classList.toggle('active');
                    }
                });
            });
        });

        // Copier le lien dans le clipboard
        function copyToClipboard() {
            var copyText = document.getElementById("referralLink");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // Pour mobile

            navigator.clipboard.writeText(copyText.value).then(function () {
                alert("Lien copié dans le presse-papier !");
            }, function (err) {
                console.error('Erreur de copie: ', err);
            });
        }

        // Partager sur Facebook
        function shareOnFacebook() {
            var url = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent("<?php echo $lienParrainage; ?>");
            window.open(url, '_blank', 'width=600,height=400');
        }

        // Partager sur WhatsApp
        function shareOnWhatsApp() {
            var text = "Rejoins ImpactAble via mon lien de parrainage et fais une différence avec moi ! ";
            var url = "https://wa.me/?text=" + encodeURIComponent(text + "<?php echo $lienParrainage; ?>");
            window.open(url, '_blank');
        }

        // Partager par Email
        function shareByEmail() {
            var subject = "Rejoins-moi sur ImpactAble !";
            var body = "Bonjour,\n\nJe t'invite à rejoindre ImpactAble, une plateforme de dons pour des causes importantes.\n\nUtilise mon lien de parrainage : <?php echo $lienParrainage; ?>\n\nÀ bientôt !";
            window.location.href = "mailto:?subject=" + encodeURIComponent(subject) + "&body=" + encodeURIComponent(body);
        }

        // Partager sur Twitter
        function shareOnTwitter() {
            var text = "Je soutiens des causes importantes sur ImpactAble. Rejoins-moi via mon lien de parrainage ! ";
            var url = "https://twitter.com/intent/tweet?text=" + encodeURIComponent(text) + "&url=" + encodeURIComponent("<?php echo $lienParrainage; ?>");
            window.open(url, '_blank', 'width=600,height=400');
        }
    </script>
</body>

</html>