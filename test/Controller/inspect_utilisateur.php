<?php
require_once __DIR__ . "/../config/Config.php";
header('Content-Type: application/json; charset=utf-8');
try{
    $db = Config::getPDO();
    $stmt = $db->query("DESCRIBE utilisateur");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success'=>true,'columns'=>$cols]);
} catch(Exception $e){
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
