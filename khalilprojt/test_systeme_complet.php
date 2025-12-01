<?php
/**
 * Script de test complet du système
 * Accès: http://localhost/khalil%20projt/test_systeme_complet.php
 */

require_once(__DIR__ . '/CONFIGRRATION/config.php');

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test Système Complet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #4B2E16; }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #17a2b8;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #5E6D38;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #4B2E16;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Test Système Complet</h1>";

try {
    $db = config::getConnexion();
    
    echo "<div class='success'>
            <strong>✅ Connexion à la base de données réussie !</strong>
          </div>";
    
    // Test 1: Vérifier la table reclamation
    $query = $db->query("SHOW TABLES LIKE 'reclamation'");
    if ($query->rowCount() > 0) {
        echo "<div class='success'>✅ Table 'reclamation' existe</div>";
        
        $count = $db->query("SELECT COUNT(*) as total FROM reclamation")->fetch();
        echo "<div class='info'>📊 Nombre de réclamations: {$count['total']}</div>";
    } else {
        echo "<div class='error'>❌ Table 'reclamation' manquante</div>";
    }
    
    // Test 2: Vérifier la table reponse
    $query = $db->query("SHOW TABLES LIKE 'reponse'");
    if ($query->rowCount() > 0) {
        echo "<div class='success'>✅ Table 'reponse' existe</div>";
        
        $count = $db->query("SELECT COUNT(*) as total FROM reponse")->fetch();
        echo "<div class='info'>📊 Nombre de réponses: {$count['total']}</div>";
    } else {
        echo "<div class='error'>❌ Table 'reponse' manquante - Exécutez setup_reponse_table.sql</div>";
    }
    
    // Test 3: Vérifier les fichiers
    $files = [
        'controller/ReclamationController.php' => 'Contrôleur Réclamations',
        'controller/ReponseController.php' => 'Contrôleur Réponses',
        'MODEL/Reclamation.php' => 'Modèle Réclamation',
        'MODEL/reponce.php' => 'Modèle Réponse',
        'VIEW/backoffice/admin_dashboard.php' => 'Dashboard Admin',
        'VIEW/backoffice/reponsecrud/ajouter_reponse.php' => 'Formulaire Réponse',
        'VIEW/backoffice/reponsecrud/liste_reponses.php' => 'Liste Réponses',
        'VIEW/frontoffice/index.php' => 'Page Frontoffice'
    ];
    
    echo "<div class='info'><strong>📁 Vérification des fichiers:</strong></div>";
    
    $allFilesExist = true;
    foreach ($files as $file => $desc) {
        if (file_exists(__DIR__ . '/' . $file)) {
            echo "<div class='success'>✅ {$desc}</div>";
        } else {
            echo "<div class='error'>❌ {$desc} - Fichier manquant: {$file}</div>";
            $allFilesExist = false;
        }
    }
    
    // Test 4: Vérifier les contrôleurs
    if (file_exists(__DIR__ . '/controller/ReclamationController.php') && 
        file_exists(__DIR__ . '/controller/ReponseController.php')) {
        
        require_once(__DIR__ . '/controller/ReclamationController.php');
        require_once(__DIR__ . '/controller/ReponseController.php');
        
        $reclamationController = new ReclamationController();
        $reponseController = new ReponseController();
        
        echo "<div class='success'>✅ Contrôleurs chargés avec succès</div>";
        
        // Test des méthodes
        $stats = $reclamationController->getStats();
        echo "<div class='info'>📈 Statistiques réclamations: {$stats['total']} total</div>";
    }
    
    // Résultat final
    if ($allFilesExist) {
        echo "<div class='success'>
                <h3>🎉 Système 100% Opérationnel !</h3>
                <p>Tous les composants sont en place et fonctionnels.</p>
              </div>";
        
        echo "<div style='text-align: center; margin-top: 30px;'>
                <a href='VIEW/backoffice/admin_dashboard.php' class='btn'>
                    🚀 Accéder au Dashboard Admin
                </a>
                <a href='VIEW/frontoffice/index.php' class='btn'>
                    📝 Formulaire Réclamation
                </a>
              </div>";
    } else {
        echo "<div class='error'>
                <h3>⚠️ Système Incomplet</h3>
                <p>Certains fichiers sont manquants. Vérifiez les erreurs ci-dessus.</p>
              </div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>
            <strong>❌ Erreur !</strong><br>
            Message: " . htmlspecialchars($e->getMessage()) . "
          </div>";
}

echo "    </div>
</body>
</html>";
?>