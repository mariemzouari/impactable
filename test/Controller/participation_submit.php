<?php
require_once __DIR__ . "/../config/Config.php";
require_once __DIR__ . "/../Model/ParticipationModel.php";

// Ensure logs directory exists for temporary error logging
$logDir = __DIR__ . '/../logs';
if(!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

// Global exception handler to log unexpected errors (temporary, remove in production)
set_exception_handler(function($e) use ($logDir){
    $msg = "[" . date('c') . "] Uncaught Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n" . $e->getTraceAsString() . "\n\n";
    @file_put_contents($logDir . '/participation_errors.log', $msg, FILE_APPEND);
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error'=>'Server error','details'=>$e->getMessage()]);
    exit;
});

$Config = new Config();
$db = $Config->getPDO();
$model = new ParticipationModel($db);

header('Content-Type: application/json; charset=utf-8');

// Simple endpoint for frontoffice participation submissions.
// Expected POST: id_evenement, id_utilisateur (optional), email (optional), nom/prenom (optional), message, nombre_accompagnants
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$data = array_map('trim', $_POST);

// Basic validation
if(empty($data['id_evenement'])){
    http_response_code(400);
    echo json_encode(['error' => 'id_evenement is required']);
    exit;
}

// Determine user id: prefer provided id_utilisateur, otherwise try to find by email, otherwise default to guest id 1
$id_utilisateur = isset($data['id_utilisateur']) && intval($data['id_utilisateur']) > 0 ? intval($data['id_utilisateur']) : null;
$email = $data['email'] ?? '';
$nom = $data['nom'] ?? '';
$prenom = $data['prenom'] ?? '';

try {
    if(!$id_utilisateur && $email !== ''){
        // look for existing user by email (actual schema: id, nom, email, password, role, created_at)
        $stmt = $db->prepare("SELECT `id` FROM `utilisateur` WHERE `email` = ? LIMIT 1");
        $stmt->execute([$email]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);
        if($u){
            $id_utilisateur = intval($u['id']);
        } else {
            // create a new user record (temporary password)
            $displayName = trim(($prenom ? $prenom . ' ' : '') . $nom) ?: ($nom ?: 'User');
            $ins = $db->prepare("INSERT INTO `utilisateur` (`nom`,`email`,`password`,`role`,`created_at`) VALUES (?, ?, ?, ?, ?)");
            $ins->execute([
                $displayName,
                $email,
                password_hash('TempPassword123!', PASSWORD_DEFAULT),
                'user',
                date('Y-m-d H:i:s')
            ]);
            $id_utilisateur = intval($db->lastInsertId());
        }
    }
} catch(Exception $e){
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la recherche/creation utilisateur', 'details' => $e->getMessage()]);
    exit;
}

if(!$id_utilisateur) $id_utilisateur = 1; // fallback guest id

$payload = [];
$payload['id_evenement'] = intval($data['id_evenement']);
$payload['id_utilisateur'] = $id_utilisateur;
$payload['message'] = $data['message'] ?? null;
$payload['nombre_accompagnants'] = isset($data['nombre_accompagnants']) ? intval($data['nombre_accompagnants']) : 0;
$payload['besoins_accessibilite'] = $data['besoins_accessibilite'] ?? 'aucun_besoin';

// check for existing participation to avoid duplicate unique key error
try{
    $chk = $db->prepare("SELECT id FROM participation WHERE id_evenement = ? AND id_utilisateur = ? LIMIT 1");
    $chk->execute([$payload['id_evenement'], $payload['id_utilisateur']]);
    if($chk->fetch(PDO::FETCH_ASSOC)){
        http_response_code(409);
        echo json_encode(['error' => 'Vous êtes déjà inscrit à cet événement']);
        exit;
    }

    $ok = $model->create($payload);
    if($ok){
        echo json_encode(['success' => true, 'message' => 'Inscription enregistrée']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Impossible d\'enregistrer la participation']);
    }
} catch(Exception $e){
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur', 'details' => $e->getMessage()]);
}
