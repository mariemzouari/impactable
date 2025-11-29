<?php
// test_debug.php
include_once(__DIR__ . '/../../config.php');
include_once(__DIR__ . '/../../model/Campagne.php');
include_once(__DIR__ . '/../../controller/CampagneController.php');

echo "<h1>Test Debug</h1>";

// Test connexion
try {
    $db = config::getConnexion();
    echo "<p style='color: green;'>✅ Connexion DB OK</p>";
    
    // Test table campagnecollecte
    $stmt = $db->query("SHOW TABLES LIKE 'campagnecollecte'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Table campagnecollecte existe</p>";
        
        // Test structure table
        $stmt = $db->query("DESCRIBE campagnecollecte");
        echo "<h3>Structure de la table:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ Table campagnecollecte n'existe pas</p>";
    }
    
    // Test utilisateur ID 1
    $campagneC = new CampagneController();
    if ($campagneC->userExists(1)) {
        echo "<p style='color: green;'>✅ Utilisateur ID 1 existe</p>";
    } else {
        echo "<p style='color: red;'>❌ Utilisateur ID 1 n'existe pas</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Erreur DB: " . $e->getMessage() . "</p>";
}
?>