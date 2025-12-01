<?php
/**
 * Script d'installation pour créer la table reponse
 * Exécutez ce fichier une fois via le navigateur ou en ligne de commande
 * URL: http://localhost/lobza/install_reponse_table.php
 */

require_once(__DIR__ . '/CONFIGRRATION/config.php');

try {
    $db = config::getConnexion();
    
    // Vérifier si la table existe déjà
    $checkTable = $db->query("SHOW TABLES LIKE 'reponse'");
    
    if ($checkTable->rowCount() > 0) {
        echo "<h2 style='color: orange;'>⚠️ La table 'reponse' existe déjà !</h2>";
        echo "<p>Si vous voulez la recréer, supprimez-la d'abord dans phpMyAdmin.</p>";
    } else {
        // Créer la table
        $sql = "CREATE TABLE IF NOT EXISTS `reponse` (
            `Id_reponse` INT(11) NOT NULL AUTO_INCREMENT,
            `Id_reclamation` INT(11) NOT NULL,
            `Id_utilisateur` INT(11) NOT NULL,
            `message` TEXT NOT NULL,
            `type_reponse` VARCHAR(50) DEFAULT 'premiere',
            `date_reponse` DATETIME NOT NULL,
            `dernier_update` DATETIME DEFAULT NULL,
            PRIMARY KEY (`Id_reponse`),
            INDEX `idx_reclamation` (`Id_reclamation`),
            INDEX `idx_utilisateur` (`Id_utilisateur`),
            INDEX `idx_date_reponse` (`date_reponse`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->exec($sql);
        
        echo "<h2 style='color: green;'>✅ Table 'reponse' créée avec succès !</h2>";
        echo "<p>Vous pouvez maintenant utiliser la fonctionnalité d'ajout de réponses.</p>";
    }
    
    // Afficher la structure de la table
    echo "<hr>";
    echo "<h3>Structure de la table 'reponse' :</h3>";
    $structure = $db->query("DESCRIBE reponse");
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";
    while ($row = $structure->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Erreur :</h2>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Table Reponse</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h2, h3 {
            color: #333;
        }
        table {
            width: 100%;
            background: white;
            margin-top: 20px;
        }
        th {
            background: #5E6D3B;
            color: white;
            padding: 10px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <p><a href="VIEW/frontoffice/">← Retour à la page frontoffice</a></p>
</body>
</html>

