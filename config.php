<?php
class config {
    private static $pdo = null;
    
    public static function getConnexion() {
        if (!self::$pdo) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=localhost;dbname=impactable;charset=utf8', 
                    'root', 
                    ''
                );
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
?>