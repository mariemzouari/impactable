<?php
date_default_timezone_set('Europe/Paris');

class Config {
    // Database constants
    const DB_HOST = 'localhost';
    const DB_NAME = 'impactable';
    const DB_USER = 'root';
    const DB_PASS = '';
    const DB_CHARSET = 'utf8mb4';
    
    // Site constants
    const SITE_NAME = 'ImpactAble';
    const SITE_URL = 'http://localhost/impactable_integration';
    
    private static $pdo = null;

    /**
     * Get PDO database connection (new method name)
     * @return PDO
     */
    public static function getPDO() {
        if (self::$pdo === null) {
            self::initConnection();
        }
        return self::$pdo;
    }

    /**
     * Get PDO database connection (legacy method name for backward compatibility)
     * @return PDO
     */
    public static function getConnexion() {
        return self::getPDO();
    }

    /**
     * Initialize the database connection
     */
    private static function initConnection() {
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
            die('Erreur de connexion à la base de données: ' . $e->getMessage());
        }
    }

    /**
     * Get base URL of the site
     * @return string
     */
    public static function getBaseUrl() {
        return self::SITE_URL;
    }
    
    /**
     * Get full URL for an asset
     * @param string $path
     * @return string
     */
    public static function getAssetUrl($path) {
        return self::SITE_URL . '/assets/' . ltrim($path, '/');
    }

    /**
     * Get site name
     * @return string
     */
    public static function getSiteName() {
        return self::SITE_NAME;
    }
}

/**
 * Backward compatibility class for lowercase 'config'
 * Only define if it doesn't already exist
 */
if (!class_exists('config')) {
    class config extends Config {
        // Inherits all methods from Config class
    }
}
?>