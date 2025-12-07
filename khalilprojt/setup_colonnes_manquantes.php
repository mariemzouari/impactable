<?php
/**
 * Script pour ajouter les colonnes manquantes à la table reclamation
 * Exécuter une fois via le navigateur: http://localhost/khalilprojt/setup_colonnes_manquantes.php
 */

require_once(__DIR__ . '/CONFIGRRATION/config.php');

echo "<h2>🔧 Configuration de la table reclamation</h2>";
echo "<style>
    body { font-family: 'Inter', Arial, sans-serif; padding: 20px; background: #f4ecdd; }
    .success { color: #2E7D32; background: #E8F5E9; padding: 10px; border-radius: 8px; margin: 5px 0; }
    .error { color: #C62828; background: #FFEBEE; padding: 10px; border-radius: 8px; margin: 5px 0; }
    .info { color: #1565C0; background: #E3F2FD; padding: 10px; border-radius: 8px; margin: 5px 0; }
    h2 { color: #4B2E16; }
</style>";

try {
    $db = config::getConnexion();
    
    // Liste des colonnes à ajouter
    $colonnes = [
        ['nom' => 'image', 'type' => 'VARCHAR(255) DEFAULT NULL'],
        ['nom' => 'nom', 'type' => 'VARCHAR(100) DEFAULT NULL'],
        ['nom' => 'prenom', 'type' => 'VARCHAR(100) DEFAULT NULL'],
        ['nom' => 'email', 'type' => 'VARCHAR(255) DEFAULT NULL'],
        ['nom' => 'telephone', 'type' => 'VARCHAR(20) DEFAULT NULL'],
        ['nom' => 'lieu', 'type' => 'VARCHAR(255) DEFAULT NULL'],
        ['nom' => 'dateIncident', 'type' => 'DATE DEFAULT NULL'],
        ['nom' => 'typeHandicap', 'type' => 'VARCHAR(100) DEFAULT NULL'],
        ['nom' => 'personnesImpliquees', 'type' => 'TEXT DEFAULT NULL'],
        ['nom' => 'temoins', 'type' => 'TEXT DEFAULT NULL'],
        ['nom' => 'actionsPrecedentes', 'type' => 'TEXT DEFAULT NULL'],
        ['nom' => 'solutionSouhaitee', 'type' => 'TEXT DEFAULT NULL'],
    ];
    
    // Vérifier les colonnes existantes
    $existingColumns = [];
    $result = $db->query("DESCRIBE reclamation");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $existingColumns[] = $row['Field'];
    }
    
    echo "<div class='info'>📋 Colonnes existantes : " . implode(', ', $existingColumns) . "</div>";
    
    $ajoutees = 0;
    $existantes = 0;
    
    foreach ($colonnes as $colonne) {
        if (in_array($colonne['nom'], $existingColumns)) {
            echo "<div class='info'>✓ Colonne '{$colonne['nom']}' existe déjà</div>";
            $existantes++;
        } else {
            try {
                $sql = "ALTER TABLE reclamation ADD COLUMN `{$colonne['nom']}` {$colonne['type']}";
                $db->exec($sql);
                echo "<div class='success'>✅ Colonne '{$colonne['nom']}' ajoutée avec succès</div>";
                $ajoutees++;
            } catch (Exception $e) {
                echo "<div class='error'>❌ Erreur pour '{$colonne['nom']}': " . $e->getMessage() . "</div>";
            }
        }
    }
    
    echo "<hr>";
    echo "<div class='success'><strong>Résumé:</strong> $ajoutees colonnes ajoutées, $existantes colonnes existaient déjà.</div>";
    
    // Vérifier la table reponse aussi
    echo "<h2>🔧 Vérification de la table reponse</h2>";
    
    try {
        $result = $db->query("DESCRIBE reponse");
        $reponseColumns = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $reponseColumns[] = $row['Field'];
        }
        echo "<div class='info'>📋 Table 'reponse' existe avec les colonnes : " . implode(', ', $reponseColumns) . "</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ Table 'reponse' n'existe pas. Création...</div>";
        
        $createReponse = "CREATE TABLE IF NOT EXISTS `reponse` (
            `Id_reponse` INT AUTO_INCREMENT PRIMARY KEY,
            `Id_reclamation` INT NOT NULL,
            `Id_utilisateur` INT NOT NULL,
            `message` TEXT NOT NULL,
            `type_reponse` VARCHAR(50) DEFAULT 'premiere',
            `date_reponse` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `dernier_update` DATETIME DEFAULT NULL,
            INDEX `idx_reclamation` (`Id_reclamation`),
            INDEX `idx_utilisateur` (`Id_utilisateur`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        
        try {
            $db->exec($createReponse);
            echo "<div class='success'>✅ Table 'reponse' créée avec succès</div>";
        } catch (Exception $e2) {
            echo "<div class='error'>❌ Erreur création table reponse: " . $e2->getMessage() . "</div>";
        }
    }
    
    echo "<hr>";
    echo "<h3>✅ Configuration terminée !</h3>";
    echo "<p><a href='VIEW/frontoffice/index.php' style='color: #5E6D3B; font-weight: bold;'>→ Aller au Front Office</a></p>";
    echo "<p><a href='VIEW/backoffice/admin_dashboard.php' style='color: #5E6D3B; font-weight: bold;'>→ Aller au Dashboard Admin</a></p>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Erreur de connexion: " . $e->getMessage() . "</div>";
}
?>





