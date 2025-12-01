<?php
require_once(__DIR__ . '/CONFIGRRATION/config.php');

echo "<h2>Test de la table articles</h2>";

try {
    $db = config::getConnexion();
    echo "<p style='color: green;'>✓ Connexion à la base de données réussie</p>";
    
    // Vérifier si la table existe
    $checkTable = $db->query("SHOW TABLES LIKE 'articles'");
    if ($checkTable->rowCount() > 0) {
        echo "<p style='color: green;'>✓ La table 'articles' existe</p>";
        
        // Afficher la structure de la table
        $columns = $db->query("SHOW COLUMNS FROM articles")->fetchAll(PDO::FETCH_ASSOC);
        echo "<h3>Structure de la table articles :</h3>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Compter les articles
        $count = $db->query("SELECT COUNT(*) as total FROM articles")->fetch();
        echo "<p><strong>Nombre d'articles dans la base : " . $count['total'] . "</strong></p>";
        
        // Afficher les articles récents
        $articles = $db->query("SELECT id, titre, auteur, statut, date_soumission FROM articles ORDER BY date_soumission DESC LIMIT 5")->fetchAll();
        if (count($articles) > 0) {
            echo "<h3>Derniers articles :</h3>";
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Titre</th><th>Auteur</th><th>Statut</th><th>Date soumission</th></tr>";
            foreach ($articles as $article) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($article['id']) . "</td>";
                echo "<td>" . htmlspecialchars($article['titre']) . "</td>";
                echo "<td>" . htmlspecialchars($article['auteur']) . "</td>";
                echo "<td>" . htmlspecialchars($article['statut']) . "</td>";
                echo "<td>" . htmlspecialchars($article['date_soumission']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ La table 'articles' n'existe pas</p>";
        echo "<p>Veuillez exécuter le script SQL : <code>create_articles_table.sql</code></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

