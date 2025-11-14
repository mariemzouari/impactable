<?php
// config.php
class Config {
    const DB_HOST = 'localhost';
    const DB_NAME = 'impactable';
    const DB_USER = 'root';
    const DB_PASS = '';
    
    

    public static function getBaseUrl() {
        return self::SITE_URL;
    }
    
    public static function getAssetUrl($path) {
        return self::SITE_URL . '/assets/' . ltrim($path, '/');
    }
}
?>