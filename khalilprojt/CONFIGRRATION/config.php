<?php
class config
{   
    private static $pdo = null;
    
    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "khalilbenhamouda";  // Base de données khalilbenhamouda
            
            try {
                self::$pdo = new PDO(
                    "mysql:host=$servername;dbname=$dbname",
                    $username,
                    $password
                );
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$pdo->exec("SET NAMES utf8mb4");
                
            } catch (Exception $e) {
                die('Erreur de connexion à la base de données: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
?>
