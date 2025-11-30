<?php
// Temporary debug endpoint to log what edit_participant receives
require_once __DIR__ . "/../config/Config.php";
require_once __DIR__ . "/../Model/ParticipationModel.php";

if(session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['is_admin'] = 1; // force admin for this debug

header('Content-Type: application/json; charset=utf-8');

$Config = new Config();
$db = $Config->getPDO();
$model = new ParticipationModel($db);

// Log all POST data received
$postData = $_POST;
$logMsg = "DEBUG: POST received\n";
$logMsg .= "Data: " . json_encode($postData, JSON_PRETTY_PRINT) . "\n";

$id = intval($postData['id'] ?? 0);
if($id > 0){
    $participation = $model->getById($id);
    $logMsg .= "Participation: " . json_encode($participation, JSON_PRETTY_PRINT) . "\n";
    
    if($participation && !empty($participation['id_utilisateur'])){
        $userId = intval($participation['id_utilisateur']);
        
        // Get current utilisateur record
        $stmt = $db->prepare("SELECT * FROM `utilisateur` WHERE `id` = ? LIMIT 1");
        $stmt->execute([$userId]);
        $userBefore = $stmt->fetch(PDO::FETCH_ASSOC);
        $logMsg .= "Utilisateur BEFORE: " . json_encode($userBefore, JSON_PRETTY_PRINT) . "\n";
        
        // Try to update
        $prenom = trim($postData['prenom'] ?? '');
        $nom = trim($postData['nom'] ?? '');
        $email = trim($postData['email'] ?? '');
        $logMsg .= "Extracted: prenom='$prenom', nom='$nom', email='$email'\n";
        
        if($prenom !== '' || $nom !== '' || $email !== ''){
            $displayName = trim(($prenom !== '' ? $prenom . ' ' : '') . $nom);
            if($displayName === '') $displayName = ($nom ?: null);
            $logMsg .= "Display name to save: '$displayName'\n";
            
            $updateFields = [];
            $params = [];
            if($displayName !== '' && $displayName !== null){
                $updateFields[] = '`nom` = ?';
                $params[] = $displayName;
            }
            if($email !== ''){
                $updateFields[] = '`email` = ?';
                $params[] = $email;
            }
            if(!empty($updateFields)){
                $params[] = $userId;
                $sql = "UPDATE `utilisateur` SET " . implode(', ', $updateFields) . " WHERE `id` = ?";
                $logMsg .= "SQL: $sql\n";
                $logMsg .= "Params: " . json_encode($params) . "\n";
                
                try {
                    $stmt = $db->prepare($sql);
                    $result = $stmt->execute($params);
                    $logMsg .= "Execute result: " . ($result ? 'true' : 'false') . "\n";
                    $logMsg .= "Row count: " . $stmt->rowCount() . "\n";
                } catch(Exception $e){
                    $logMsg .= "Exception: " . $e->getMessage() . "\n";
                }
            } else {
                $logMsg .= "No fields to update\n";
            }
        } else {
            $logMsg .= "All fields empty\n";
        }
        
        // Get utilisateur AFTER
        $stmt = $db->prepare("SELECT * FROM `utilisateur` WHERE `id` = ? LIMIT 1");
        $stmt->execute([$userId]);
        $userAfter = $stmt->fetch(PDO::FETCH_ASSOC);
        $logMsg .= "Utilisateur AFTER: " . json_encode($userAfter, JSON_PRETTY_PRINT) . "\n";
    }
}

file_put_contents(__DIR__ . '/../logs/debug_edit.log', $logMsg, FILE_APPEND);

echo json_encode([
    'success' => true,
    'debug' => true,
    'message' => 'Debug logged to logs/debug_edit.log',
    'preview' => substr($logMsg, 0, 500)
]);
?>
