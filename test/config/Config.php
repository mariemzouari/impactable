<?php
class Config {
    const DB_HOST = 'localhost';
    const DB_NAME = 'impactable-db';
    const DB_USER = 'root';
    const DB_PASS = '';
    const DB_CHARSET = 'utf8mb4';
    
    const SITE_NAME = 'ImpactAble';
    const SITE_URL = 'http://localhost/test';
    
    private static $pdo = null;

    public static function getBaseUrl() {
        return self::SITE_URL;
    }
    
    public static function getAssetUrl($path) {
        return self::SITE_URL . '/assets/' . ltrim($path, '/');
    }
    
    public static function getPDO() {
        if (self::$pdo === null) {
            $dsn = "mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME . ";charset=" . self::DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            try {
                self::$pdo = new PDO($dsn, self::DB_USER, self::DB_PASS, $options);
            } catch (PDOException $e) {
                error_log('Connection failed: ' . $e->getMessage());
                die('Database connection error.');
            }
        }
        
        return self::$pdo;
    }
}
