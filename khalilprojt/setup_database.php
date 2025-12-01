<?php
/**
 * Script de configuration automatique de la base de données
 * Accès: http://localhost/khalil%20projt/setup_database.php
 * 
 * Ce script va:
 * 1. Créer la base de données khalilbenhamouda si elle n'existe pas
 * 2. Créer la table reclamation si elle n'existe pas
 * 3. Créer la table utilisateur si elle n'existe pas
 */

$servername = "localhost";
$username = "root";
$password = "";

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Configuration Base de Données</title>
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
        .step {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Configuration de la Base de Données</h1>";

try {
    // Étape 1: Connexion au serveur MySQL (sans base de données)
    echo "<div class='step'><h3>Étape 1: Connexion au serveur MySQL</h3>";
    $pdo = new PDO("mysql:host=$servername", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div class='success'>✅ Connexion au serveur MySQL réussie</div></div>";
    
    // Étape 2: Créer la base de données si elle n'existe pas
    echo "<div class='step'><h3>Étape 2: Création de la base de données</h3>";
    $dbname = "khalilbenhamouda";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    echo "<div class='success'>✅ Base de données '$dbname' créée ou déjà existante</div></div>";
    
    // Étape 3: Sélectionner la base de données
    $pdo->exec("USE `$dbname`");
    echo "<div class='info'>📌 Base de données '$dbname' sélectionnée</div>";
    
    // Étape 4: Créer la table utilisateur si elle n'existe pas
    echo "<div class='step'><h3>Étape 3: Création de la table utilisateur</h3>";
    $sqlUser = "CREATE TABLE IF NOT EXISTS `utilisateur` (
        `Id_utilisateur` INT(11) NOT NULL AUTO_INCREMENT,
        `nom` VARCHAR(100) NOT NULL,
        `prenom` VARCHAR(100) NOT NULL,
        `genre` ENUM('femme','homme','prefere_ne_pas_dire') NOT NULL DEFAULT 'prefere_ne_pas_dire',
        `date_naissance` DATE DEFAULT NULL,
        `email` VARCHAR(255) NOT NULL,
        `numero_tel` VARCHAR(20) DEFAULT NULL,
        `mot_de_passe` VARCHAR(255) NOT NULL,
        `role` ENUM('admin','user') DEFAULT 'user',
        `type_handicap` SET('aucun','moteur','visuel','auditif','mental','autre','tous') NOT NULL DEFAULT 'aucun',
        `date_inscription` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`Id_utilisateur`),
        UNIQUE KEY `email` (`email`),
        UNIQUE KEY `numero_tel` (`numero_tel`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $pdo->exec($sqlUser);
    echo "<div class='success'>✅ Table 'utilisateur' créée ou déjà existante</div></div>";
    
    // Étape 5: Créer la table reclamation
    echo "<div class='step'><h3>Étape 4: Création de la table reclamation</h3>";
    
    // Supprimer la table si elle existe avec une mauvaise structure
    $pdo->exec("DROP TABLE IF EXISTS `reclamation`");
    
    $sqlReclamation = "CREATE TABLE `reclamation` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `sujet` VARCHAR(255) NOT NULL,
        `description` TEXT NOT NULL,
        `categorie` VARCHAR(100) NOT NULL,
        `priorite` VARCHAR(50) NOT NULL,
        `statut` VARCHAR(50) NOT NULL,
        `dateCreation` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `derniereModification` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `utilisateurId` INT NOT NULL,
        `agentAttribue` VARCHAR(255) DEFAULT NULL,
        INDEX `idx_reclamation_user` (`utilisateurId`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $pdo->exec($sqlReclamation);
    echo "<div class='success'>✅ Table 'reclamation' créée avec succès</div></div>";
    
    // Étape 6: Vérifier la structure
    echo "<div class='step'><h3>Étape 5: Vérification</h3>";
    
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<div class='info'>📊 Tables dans la base de données:</div><ul>";
    foreach ($tables as $table) {
        echo "<li><strong>$table</strong></li>";
    }
    echo "</ul>";
    
    // Vérifier la structure de la table reclamation
    $columns = $pdo->query("DESCRIBE reclamation")->fetchAll();
    echo "<div class='info'>📋 Colonnes de la table 'reclamation':</div><ul>";
    foreach ($columns as $col) {
        echo "<li><strong>{$col['Field']}</strong> - {$col['Type']}</li>";
    }
    echo "</ul>";
    
    echo "<div class='success'>
            <h3>✅ Configuration terminée avec succès !</h3>
            <p>Vous pouvez maintenant accéder au dashboard admin :</p>
            <p><a href='VIEW/backoffice/admin_dashboard.php' style='color: #155724; font-weight: bold;'>Accéder au Dashboard Admin</a></p>
          </div>";
    
} catch (PDOException $e) {
    echo "<div class='error'>
            <strong>❌ Erreur:</strong><br>
            " . htmlspecialchars($e->getMessage()) . "
          </div>";
}

echo "    </div>
</body>
</html>";
?>

