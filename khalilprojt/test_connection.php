<?php
/**
 * Script de test de connexion à la base de données khalilbenhamouda
 * Accès: http://localhost/khalil%20projt/test_connection.php
 */

require_once(__DIR__ . '/CONFIGRRATION/config.php');

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test de Connexion - Base de Données</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
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
        h1 {
            color: #4B2E16;
        }
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
        tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Test de Connexion à la Base de Données</h1>";

try {
    $db = config::getConnexion();
    
    echo "<div class='success'>
            <strong>✅ Connexion réussie !</strong><br>
            Base de données: <strong>khalilbenhamouda</strong>
          </div>";
    
    // Vérifier si la table reclamation existe
    $query = $db->query("SHOW TABLES LIKE 'reclamation'");
    $tableExists = $query->rowCount() > 0;
    
    if ($tableExists) {
        echo "<div class='success'>
                <strong>✅ Table 'reclamation' trouvée !</strong>
              </div>";
        
        // Afficher la structure de la table
        $structure = $db->query("DESCRIBE reclamation")->fetchAll();
        
        echo "<div class='info'>
                <strong>📋 Structure de la table 'reclamation':</strong>
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
        
        // Compter les réclamations
        $count = $db->query("SELECT COUNT(*) as total FROM reclamation")->fetch();
        echo "<div class='info'>
                <strong>📊 Nombre de réclamations:</strong> {$count['total']}
              </div>";
        
        // Afficher les réclamations si elles existent
        if ($count['total'] > 0) {
            $reclamations = $db->query("SELECT * FROM reclamation ORDER BY dateCreation DESC LIMIT 5")->fetchAll();
            
            echo "<div class='info'>
                    <strong>📝 Dernières réclamations (max 5):</strong>
                  </div>";
            
            echo "<table>
                    <tr>
                        <th>ID</th>
                        <th>Sujet</th>
                        <th>Catégorie</th>
                        <th>Priorité</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>";
            
            foreach ($reclamations as $rec) {
                echo "<tr>
                        <td>{$rec['id']}</td>
                        <td>" . htmlspecialchars(substr($rec['sujet'], 0, 30)) . "...</td>
                        <td>{$rec['categorie']}</td>
                        <td>{$rec['priorite']}</td>
                        <td>{$rec['statut']}</td>
                        <td>" . date('d/m/Y H:i', strtotime($rec['dateCreation'])) . "</td>
                      </tr>";
            }
            
            echo "</table>";
        }
        
    } else {
        echo "<div class='error'>
                <strong>⚠️ Table 'reclamation' non trouvée !</strong><br>
                Veuillez exécuter le script SQL: <strong>khalilbenhamouda_reclamation.sql</strong>
              </div>";
    }
    
    // Vérifier si la table utilisateur existe
    $query = $db->query("SHOW TABLES LIKE 'utilisateur'");
    $userTableExists = $query->rowCount() > 0;
    
    if ($userTableExists) {
        $userCount = $db->query("SELECT COUNT(*) as total FROM utilisateur")->fetch();
        echo "<div class='info'>
                <strong>👥 Nombre d'utilisateurs:</strong> {$userCount['total']}
              </div>";
    } else {
        echo "<div class='error'>
                <strong>⚠️ Table 'utilisateur' non trouvée !</strong>
              </div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>
            <strong>❌ Erreur de connexion !</strong><br>
            Message: " . htmlspecialchars($e->getMessage()) . "
          </div>";
}

echo "    </div>
</body>
</html>";
?>

