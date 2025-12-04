<?php
include_once __DIR__ . '/../../controller/DonController.php';
include_once __DIR__ . '/../../controller/CampagneController.php';

$donController = new DonController();
$campagneController = new CampagneController();

if (!isset($_GET['id'])) {
    header('Location: list-don.php');
    exit;
}

$don_id = $_GET['id'];

// Récupérer le don avec jointures
$dons = $donController->getHistoriqueDonsComplet();
$don = null;

foreach ($dons as $d) {
    if ($d['Id_don'] == $don_id) {
        $don = $d;
        break;
    }
}

if (!$don) {
    header('Location: list-don.php');
    exit;
}

// Récupérer la campagne associée
$campagne = $campagneController->showCampagne($don['id_campagne']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactAble — Détails Don #<?php echo $don_id; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .details-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .detail-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .detail-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .detail-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 1.1em;
        }
        
        .receipt-box {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            padding: 30px;
            text-align: center;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .campagne-link {
            display: inline-block;
            background: var(--sage-light);
            color: var(--sage-dark);
            padding: 10px 15px;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 10px;
        }
        
        .campagne-link:hover {
            background: var(--sage);
            color: white;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <!-- Copier le sidebar depuis list-camp.php -->
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <div>
                    <h2>Détails du Don #<?php echo $don_id; ?></h2>
                    <p class="text-muted">Informations complètes sur ce don</p>
                </div>
                <div class="header-actions">
                    <a href="list-don.php" class="btn secondary">
                        <i class="fas fa-arrow-left"></i>
                        Retour aux dons
                    </a>
                    <a href="edit-don.php?id=<?php echo $don_id; ?>" class="btn primary">
                        <i class="fas fa-edit"></i>
                        Modifier
                    </a>
                    <button onclick="window.print()" class="btn">
                        <i class="fas fa-print"></i>
                        Imprimer
                    </button>
                </div>
            </header>

            <div class="admin-content">
                <div class="details-container">
                    <!-- Informations du don -->
                    <div class="detail-section">
                        <h3><i class="fas fa-info-circle"></i> Informations du don</h3>
                        <div class="detail-item">
                            <div class="detail-label">Montant</div>
                            <div class="detail-value" style="font-size: 1.5em; color: var(--sage);">
                                <strong><?php echo number_format($don['montant'], 2, ',', ' '); ?> TND</strong>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Statut</div>
                            <div class="detail-value">
                                <span class="badge <?php echo $don['statut']; ?>" style="font-size: 1em;">
                                    <?php echo ucfirst($don['statut']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Méthode de paiement</div>
                            <div class="detail-value">
                                <?php echo ucfirst($don['methode_paiment']); ?>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Date et heure</div>
                            <div class="detail-value">
                                <?php echo date('d/m/Y à H:i', strtotime($don['date_don'])); ?>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Numéro de reçu</div>
                            <div class="detail-value">
                                <code style="background: #f8f9fa; padding: 5px 10px; border-radius: 4px;">
                                    <?php echo $don['numero_reçu']; ?>
                                </code>
                            </div>
                        </div>
                        
                        <?php if (!empty($don['message'])): ?>
                        <div class="detail-item">
                            <div class="detail-label">Message du donateur</div>
                            <div class="detail-value">
                                <em>"<?php echo htmlspecialchars($don['message']); ?>"</em>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Informations du donateur -->
                    <div class="detail-section">
                        <h3><i class="fas fa-user"></i> Informations du donateur</h3>
                        <div class="detail-item">
                            <div class="detail-label">Nom complet</div>
                            <div class="detail-value">
                                <?php echo htmlspecialchars($don['donateur_nom']); ?>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Email</div>
                            <div class="detail-value">
                                <?php echo htmlspecialchars($don['donateur_email']); ?>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">ID Utilisateur</div>
                            <div class="detail-value">
                                #<?php echo $don['id_utilisateur']; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Informations de la campagne -->
                    <div class="detail-section" style="grid-column: 1 / -1;">
                        <h3><i class="fas fa-hand-holding-heart"></i> Campagne soutenue</h3>
                        
                        <?php if ($campagne): ?>
                        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; align-items: start;">
                            <div>
                                <div class="detail-item">
                                    <div class="detail-label">Titre</div>
                                    <div class="detail-value">
                                        <strong><?php echo htmlspecialchars($campagne['titre']); ?></strong>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">Catégorie</div>
                                    <div class="detail-value">
                                        <span class="badge"><?php echo ucfirst($campagne['categorie_impact']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">Statut</div>
                                    <div class="detail-value">
                                        <span class="status <?php echo $campagne['statut']; ?>">
                                            <?php echo ucfirst($campagne['statut']); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <a href="showCampagne.php?id=<?php echo $campagne['Id_campagne']; ?>" 
                                   class="campagne-link">
                                    <i class="fas fa-external-link-alt"></i>
                                    Voir la campagne
                                </a>
                            </div>
                            
                            <div>
                                <div class="detail-item">
                                    <div class="detail-label">Description</div>
                                    <div class="detail-value">
                                        <?php echo nl2br(htmlspecialchars(substr($campagne['description'], 0, 200))); ?>...
                                    </div>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px;">
                                    <div style="text-align: center;">
                                        <div style="font-size: 1.2em; font-weight: bold; color: var(--sage);">
                                            <?php echo number_format($campagne['objectif_montant'], 0, ',', ' '); ?> TND
                                        </div>
                                        <small>Objectif</small>
                                    </div>
                                    <div style="text-align: center;">
                                        <div style="font-size: 1.2em; font-weight: bold; color: var(--sage);">
                                            <?php echo number_format($campagne['montant_actuel'], 0, ',', ' '); ?> TND
                                        </div>
                                        <small>Collecté</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="text-muted">
                            <i class="fas fa-exclamation-triangle"></i>
                            Cette campagne n'existe plus.
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Reçu de don -->
                    <div class="detail-section" style="grid-column: 1 / -1;">
                        <h3><i class="fas fa-receipt"></i> Reçu de don</h3>
                        <div class="receipt-box">
                            <h2>ImpactAble</h2>
                            <p style="color: #666;">Plateforme d'impact social</p>
                            
                            <div style="margin: 30px 0;">
                                <h3>REÇU DE DON N° <?php echo $don['numero_reçu']; ?></h3>
                                <p style="font-size: 0.9em; color: #666;">
                                    Émis le <?php echo date('d/m/Y'); ?>
                                </p>
                            </div>
                            
                            <div style="text-align: left; max-width: 400px; margin: 0 auto;">
                                <p><strong>Donateur:</strong> <?php echo htmlspecialchars($don['donateur_nom']); ?></p>
                                <p><strong>Montant:</strong> <?php echo number_format($don['montant'], 2, ',', ' '); ?> TND</p>
                                <p><strong>Pour la campagne:</strong> <?php echo htmlspecialchars($don['campagne_titre']); ?></p>
                                <p><strong>Date du don:</strong> <?php echo date('d/m/Y', strtotime($don['date_don'])); ?></p>
                                <p><strong>Méthode de paiement:</strong> <?php echo ucfirst($don['methode_paiment']); ?></p>
                            </div>
                            
                            <div style="margin-top: 30px; padding-top: 20px; border-top: 2px dashed #dee2e6;">
                                <p>Merci pour votre générosité !</p>
                                <p><small>Votre contribution fait la différence.</small></p>
                            </div>
                        </div>
                        
                        <div style="text-align: center; margin-top: 20px;">
                            <button onclick="window.print()" class="btn primary">
                                <i class="fas fa-print"></i>
                                Imprimer le reçu
                            </button>
                            <button onclick="envoyerEmail()" class="btn secondary">
                                <i class="fas fa-envelope"></i>
                                Renvoyer par email
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        function envoyerEmail() {
            if (confirm("Renvoyer le reçu par email à <?php echo htmlspecialchars($don['donateur_email']); ?> ?")) {
                // Ici vous pourriez ajouter un appel AJAX
                alert("Email de reçu envoyé à <?php echo htmlspecialchars($don['donateur_email']); ?>");
            }
        }
    </script>
</body>
</html>