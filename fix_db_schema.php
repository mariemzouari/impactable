<?php
// fix_db_schema.php
require_once __DIR__ . '/config.php';

try {
    $db = config::getConnexion();

    echo "Attempting to fix database schema...\n";

    // 1. Drop the bad foreign key
    // multi-step to avoid errors if it doesn't exist? 
    // We strictly saw 'don_ibfk_2' in the error message.

    echo "Dropping bad foreign key 'don_ibfk_2'...\n";
    $sqlDrop = "ALTER TABLE don DROP FOREIGN KEY don_ibfk_2";
    try {
        $db->exec($sqlDrop);
        echo "Dropped 'don_ibfk_2' successfully.\n";
    } catch (PDOException $e) {
        echo "Warning dropping FK: " . $e->getMessage() . "\n";
    }

    // 2. Add the correct foreign key
    echo "Adding correct foreign key for 'id_campagne'...\n";
    $sqlAdd = "ALTER TABLE don ADD CONSTRAINT don_ibfk_2 FOREIGN KEY (id_campagne) REFERENCES campagnecollecte(Id_campagne) ON DELETE CASCADE ON UPDATE CASCADE";

    $db->exec($sqlAdd);
    echo "Added correct 'don_ibfk_2' successfully.\n";

    // Verify
    echo "Schema fixed.\n";

} catch (PDOException $e) {
    echo "CRITICAL ERROR: " . $e->getMessage() . "\n";
}
?>