<?php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'impactable';  
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonction de connexion
function getConnection() {
    global $conn;
    return $conn;
}
?>