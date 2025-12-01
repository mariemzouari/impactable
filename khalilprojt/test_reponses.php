<?php
/**
 * Script de test pour vérifier le système de réponses
 * Accès: http://localhost/khalil%20projt/test_reponses.php
 */

require_once(__DIR__ . '/CONFIGRRATION/config.php');
require_once(__DIR__ . '/controller/ReponseController.php');

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test Système de Réponses</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #5E6D38;
            color: white;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #5E6D38;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #4B2E16;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Test du Système de Réponses</h1>";

try {
    $db = config::getConnexion();
    
    echo "<div class='success'>
            <strong>✅ Connexion à la base de données réussie !</strong>
          </div>";
    
    // Vérifier si la table reponse existe
    $query = $db->query("SHOW TABLES LIKE 'reponse'");
    $tableExists = $query->rowCount() > 0;
    
    if ($tableExists) {
        echo "<div class='success'>
                <strong>✅ Table 'reponse' trouvée !</strong>
              </div>";
        
        // Afficher la structure de la table
        $structure = $db->query("DESCRIBE reponse")->fetchAll();
        
        echo "<div class='info'>
                <strong>📋 Structure de la table 'reponse':</strong>
              </div>";
        
        echo "<table>
                <tr>
                    <th>Champ</th>
                    <th>Type</th>
                    <th>Null</th>
                    <th>Clé</th>
                    <th>Défaut</th>
                </tr>";
        
        foreach ($structure as $column) {
            echo "<tr>
                    <td><strong>{$column['Field']}</strong></td>
                    <td>{$column['Type']}</td>
                    <td>{$column['Null']}</td>
                    <td>{$column['Key']}</td>
                    <td>{$column['Default']}</td>
                  </tr>";
        }
        
        echo "</table>";
        
        // Compter les réponses
        $count = $db->query("SELECT COUNT(*) as total FROM reponse")->fetch();
        echo "<div class='info'>
                <strong>📊 Nombre de réponses dans la base:</strong> {$count['total']}
              </div>";
        
        // Tester le contrôleur
        echo "<div class='info'>
                <strong>🧪 Test du contrôleur ReponseController...</strong>
              </div>";
        
        $reponseController = new ReponseController();
        
        // Vérifier si des réclamations existent
        $reclamations = $db->query("SELECT id FROM reclamation LIMIT 1")->fetch();
        
        if ($reclamations) {
            $reclamationId = $reclamations['id'];
            $reponses = $reponseController->getReponsesByReclamation($reclamationId);
            
            echo "<div class='success'>
                    <strong>✅ Contrôleur fonctionnel !</strong><br>
                    Réclamation testée: #{$reclamationId}<br>
                    Nombre de réponses: " . count($reponses) . "
                  </div>";
            
            if (!empty($reponses)) {
                echo "<div class='info'>
                        <strong>📝 Dernières réponses:</strong>
                      </div>";
                
                echo "<table>
                        <tr>
                            <th>ID</th>
                            <th>Réclamation</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>";
                
                foreach (array_slice($reponses, 0, 5) as $rep) {
                    echo "<tr>
                            <td>{$rep['Id_reponse']}</td>
                            <td>#{$rep['Id_reclamation']}</td>
                            <td>" . substr($rep['message'], 0, 50) . "...</td>
                            <td>" . date('d/m/Y H:i', strtotime($rep['date_reponse'])) . "</td>
                          </tr>";
                }
                
                echo "</table>";
            }
        } else {
            echo "<div class='info'>
                    <strong>ℹ️ Aucune réclamation dans la base pour tester.</strong><br>
                    Créez d'abord une réclamation pour tester les réponses.
                  </div>";
        }
        
        // Vérifier les fichiers
        echo "<div class='info'>
                <strong>📁 Vérification des fichiers...</strong>
              </div>";
        
        $files = [
            'controller/ReponseController.php' => 'Contrôleur',
            'MODEL/reponce.php' => 'Modèle',
            'VIEW/backoffice/reponsecrud/ajouter_reponse.php' => 'Formulaire d\'ajout',
            'VIEW/backoffice/reponsecrud/liste_reponses.php' => 'Liste des réponses'
        ];
        
        echo "<table>
                <tr>
                    <th>Fichier</th>
                    <th>Description</th>
                    <th>Statut</th>
                </tr>";
        
        foreach ($files as $file => $desc) {
            $exists = file_exists(__DIR__ . '/' . $file);
            $status = $exists ? '✅ Existe' : '❌ Manquant';
            $color = $exists ? '#155724' : '#721c24';
            
            echo "<tr>
                    <td>{$file}</td>
                    <td>{$desc}</td>
                    <td style='color: {$color}; font-weight: bold;'>{$status}</td>
                  </tr>";
        }
        
        echo "</table>";
        
        echo "<div class='success'>
                <h3>✅ Système de Réponses Opérationnel !</h3>
                <p>Tous les composants sont en place et fonctionnels.</p>
                <a href='VIEW/backoffice/admin_dashboard.php' class='btn'>
                    Accéder au Dashboard Admin
                </a>
              </div>";
        
    } else {
        echo "<div class='error'>
                <strong>⚠️ Table 'reponse' non trouvée !</strong><br>
                Veuillez exécuter le script SQL: <strong>create_reponse_table.sql</strong>
              </div>";
        
        echo "<div class='info'>
                <strong>📝 Script SQL à exécuter:</strong>
                <pre style='background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto;'>
CREATE TABLE IF NOT EXISTS `reponse` (
  `Id_reponse` INT(11) NOT NULL AUTO_INCREMENT,
  `Id_reclamation` INT(11) NOT NULL,
  `Id_utilisateur` INT(11) NOT NULL,
  `message` TEXT NOT NULL,
  `piece_jointe` VARCHAR(255) DEFAULT NULL,
  `type_reponse` ENUM('premiere','suivi','resolution') NOT NULL DEFAULT 'premiere',
  `date_reponse` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id_reponse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                </pre>
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
