<?php
require_once __DIR__ . "/../config/Config.php";
require_once __DIR__ . "/../Model/ParticipationModel.php";

if(session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['user_id'] = 1; // Temporary: Simulate logged-in user


function is_admin(): bool {
    // simple session-based check; adapt to your auth system
    if(!empty($_SESSION['is_admin'])) return true;
    if(!empty($_SESSION['user']) && !empty($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') return true;
    return false;
}

function mapStatusToDb(string $status): string {
    @error_log("mapStatusToDb INPUT: " . $status, 3, __DIR__ . '/../logs/participation_debug.log');
    $s = mb_strtolower(trim($status));
    if($s === '') {
        @error_log("mapStatusToDb OUTPUT: inscrit (empty input)", 3, __DIR__ . '/../logs/participation_debug.log');
        return 'inscrit';
    }
    // common UI values -> DB enum mapping
    if(mb_stripos($s, 'confirm') !== false || mb_stripos($s, 'présent') !== false || mb_stripos($s, 'prés') !== false || mb_stripos($s, 'present') !== false || mb_stripos($s, 'confirmé') !== false) {
        @error_log("mapStatusToDb OUTPUT: confirmé", 3, __DIR__ . '/../logs/participation_debug.log');
        return 'confirmé';
    }
    if(mb_stripos($s, 'annul') !== false || mb_stripos($s, 'annulé') !== false) {
        @error_log("mapStatusToDb OUTPUT: annulé", 3, __DIR__ . '/../logs/participation_debug.log');
        return 'annulé';
    }
    if(mb_stripos($s, 'inscr') !== false) {
        @error_log("mapStatusToDb OUTPUT: inscrit", 3, __DIR__ . '/../logs/participation_debug.log');
        return 'inscrit';
    }
    // fallback: if DB expects 'inscrit' default
    @error_log("mapStatusToDb OUTPUT: inscrit (fallback)", 3, __DIR__ . '/../logs/participation_debug.log');
    return 'inscrit';
}

function findOrCreateUser($db, $email, $prenom = '', $nom = ''): int {
    // check if user with this email exists (actual schema uses `id`, `nom`, `email`, `password`, `role`, `created_at`)
    $stmt = $db->prepare("SELECT `id` FROM `utilisateur` WHERE `email` = ? LIMIT 1");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row) return intval($row['id']);

    // user doesn't exist; create one using columns present in DB
    $displayName = trim(($prenom ? $prenom . ' ' : '') . $nom) ?: ($nom ?: 'User');
    $stmt = $db->prepare("INSERT INTO `utilisateur` (`nom`, `email`, `password`, `role`, `created_at`) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $displayName,
        $email,
        password_hash('TempPassword123!', PASSWORD_DEFAULT),
        'user',
        date('Y-m-d H:i:s')
    ]);
    return intval($db->lastInsertId());
}

class ParticipationController {
    private $model;

    public function __construct(){
        $Config = new Config();
        $db = $Config->getPDO();
        $this->model = new ParticipationModel($db);
    }

    public function getParticipationsByUserId(int $userId): array
    {
        return $this->model->getByUserId($userId);
    }

    public function index(){
        $participations = $this->model->getAll();
        include __DIR__ . '/../View/Backoffice/list_participations.php';
    }

    public function delete($id){
        $this->model->delete($id);
        header('Location: participations.php');
        exit;
    }
}

// Simple API endpoints for AJAX requests
if((isset($_GET['action']) || isset($_POST['action']))) {
    header('Content-Type: application/json; charset=utf-8');
    $action = $_REQUEST['action'];
    $Config = new Config();
    $db = $Config->getPDO();
    $model = new ParticipationModel($db);

    try {
        if($action === 'get_event_participants'){
            $id = intval($_GET['id'] ?? 0);
            $rows = $model->getByEvent($id);
            echo json_encode(['success'=>true,'data'=>$rows]);
            exit;
        }

        if($action === 'add_participant' && $_SERVER['REQUEST_METHOD'] === 'POST'){
            // backoffice create requires admin
            if(!is_admin()){
                http_response_code(403);
                echo json_encode(['success'=>false,'error'=>'Unauthorized']);
                exit;
            }

            $payload = $_POST;
            // ensure id_evenement exist
            $payload['id_evenement'] = intval($payload['id_evenement'] ?? 0);
            // find or create user by email
            $email = trim($payload['email'] ?? '');
            $prenom = trim($payload['prenom'] ?? '');
            $nom = trim($payload['nom'] ?? '');
            if (empty($email) || empty($prenom) || empty($nom)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Le nom, le prénom et l\'email sont requis.']);
                exit;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Le format de l\'email est invalide.']);
                exit;
            }
            $payload['id_utilisateur'] = findOrCreateUser($db, $email, $prenom, $nom);
            // map UI status to DB enum
            $payload['statut'] = mapStatusToDb($payload['statut'] ?? 'inscrit');

            $insertId = $model->create($payload);
            if($insertId !== false && $insertId !== 0){
                echo json_encode(['success'=>true,'id'=>intval($insertId)]);
            } else {
                http_response_code(500);
                echo json_encode(['success'=>false,'error'=>'Unable to create']);
            }
            exit;
        }

        if($action === 'edit_participant' && $_SERVER['REQUEST_METHOD'] === 'POST'){
            if(!is_admin()){
                http_response_code(403);
                echo json_encode(['success'=>false,'error'=>'Unauthorized']);
                exit;
            }
            $id = intval($_POST['id'] ?? 0);
            $data = $_POST;

            // map status to DB value
            if(isset($data['statut'])) $data['statut'] = mapStatusToDb($data['statut']);

            // If name/email fields provided in POST, update the linked utilisateur record.
            try {
                $participation = $model->getById($id);
                if($participation && !empty($participation['id_utilisateur'])){
                    $userId = intval($participation['id_utilisateur']);
                    $prenom = trim($data['prenom'] ?? '');
                    $nom = trim($data['nom'] ?? '');
                    $email = trim($data['email'] ?? '');
                    if($prenom !== '' || $nom !== '' || $email !== ''){
                        // The `utilisateur` table in this schema stores full name in `nom`.
                        $displayName = trim(($prenom !== '' ? $prenom . ' ' : '') . $nom);
                        if($displayName === '') $displayName = ($nom ?: null);
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
                            $stmt = $db->prepare($sql);
                            $stmt->execute($params);
                        }
                    }
                }
            } catch(Exception $e){
                // log or ignore; proceed to update participation
                @error_log("Exception in utilisateur update: " . $e->getMessage() . "\n", 3, __DIR__ . '/../logs/edit_participant.log');
            }

            // Log the edit attempt
            @error_log("EDIT_PARTICIPANT: id=$id, prenom=" . ($data['prenom'] ?? '') . ", nom=" . ($data['nom'] ?? '') . ", email=" . ($data['email'] ?? '') . ", statut=" . ($data['statut'] ?? '') . "\n", 3, __DIR__ . '/../logs/edit_participant.log');

            // Prepare participation-specific fields for update
            $updateData = [
                'statut' => $data['statut'] ?? null,
                'message' => $data['message'] ?? null,
                'besoins_accessibilite' => $data['besoins_accessibilite'] ?? null,
                'nombre_accompagnants' => isset($data['nombre_accompagnants']) ? $data['nombre_accompagnants'] : null,
            ];

            $ok = $model->update($id, $updateData);
            echo json_encode(['success'=>boolval($ok)]);
            exit;
        }

        if($action === 'delete_participant' && $_SERVER['REQUEST_METHOD'] === 'POST'){
            if(!is_admin()){
                http_response_code(403);
                echo json_encode(['success'=>false,'error'=>'Unauthorized']);
                exit;
            }
            $id = intval($_POST['id'] ?? 0);
            $ok = $model->delete($id);
            echo json_encode(['success'=>boolval($ok)]);
            exit;
        }

        if($action === 'cancel_participation' && $_SERVER['REQUEST_METHOD'] === 'POST'){
            $eventId = intval($_POST['id_evenement'] ?? 0);
            $userId = intval($_POST['id_utilisateur'] ?? 0); // Assuming user ID is passed from session or hidden field

            // Basic validation
            if ($eventId === 0 || $userId === 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Données de participation manquantes ou invalides.']);
                exit;
            }

            // Get the participation record to ensure it belongs to the user
            $stmt = $db->prepare("SELECT id FROM {$model->getTable()} WHERE id_evenement = ? AND id_utilisateur = ?");
            $stmt->execute([$eventId, $userId]);
            $participation = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$participation) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Participation non trouvée pour cet utilisateur et événement.']);
                exit;
            }

            // Update the status to 'annulé' instead of deleting
            $ok = $model->update($participation['id'], ['statut' => 'annulé']);

            if($ok){
                echo json_encode(['success'=>true]);
            } else {
                http_response_code(500);
                echo json_encode(['success'=>false,'error'=>'Impossible d\'annuler la participation.']);
            }
            exit;
        }

        if($action === 'confirm_my_participation' && $_SERVER['REQUEST_METHOD'] === 'POST'){
            $participationId = intval($_POST['id'] ?? 0);
            $userId = $_SESSION['user_id'] ?? 0; // Get user ID from session

            if ($participationId === 0 || $userId === 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Données de participation manquantes ou invalides.']);
                exit;
            }

            // Verify that the participation belongs to the current user
            $participation = $model->getById($participationId);
            if (!$participation || intval($participation['id_utilisateur']) !== $userId) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Non autorisé à modifier cette participation.']);
                exit;
            }

            // Update the status to 'confirmé'
            $ok = $model->update($participationId, ['statut' => 'confirmé']);

            if($ok){
                echo json_encode(['success'=>true]);
            } else {
                http_response_code(500);
                echo json_encode(['success'=>false,'error'=>'Impossible de confirmer la participation.']);
            }
            exit;
        }

        if($action === 'participate' && $_SERVER['REQUEST_METHOD'] === 'POST'){
            $eventId = intval($_POST['id_evenement'] ?? 0);
            $userId = intval($_POST['id_utilisateur'] ?? 0); // Assuming user ID is passed from session or hidden field

            if ($eventId === 0 || $userId === 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Données de participation manquantes ou invalides.']);
                exit;
            }

            // Check if already participating
            if ($model->isUserParticipating($userId, $eventId)) {
                http_response_code(409); // Conflict
                echo json_encode(['success' => false, 'error' => 'Vous participez déjà à cet événement.']);
                exit;
            }

            $data = [
                'id_evenement' => $eventId,
                'id_utilisateur' => $userId,
                'statut' => 'inscrit'
            ];

            $insertId = $model->create($data);
            if($insertId !== false){
                echo json_encode(['success'=>true, 'id'=>$insertId]);
            } else {
                http_response_code(500);
                echo json_encode(['success'=>false,'error'=>'Impossible de s\'inscrire à l\'événement.']);
            }
            exit;
        }

        if($action === 'bulk_action' && $_SERVER['REQUEST_METHOD'] === 'POST'){
            $type = $_POST['type'] ?? '';
            $ids = $_POST['ids'] ?? [];
            // ensure ids are integers
            $ids = array_map('intval', (array)$ids);

            if($type === 'confirm'){
                $statusToUpdate = 'confirmé'; // Explicitly set to 'confirmé' for database
                $count = $model->bulkUpdateStatus($ids, $statusToUpdate);
                echo json_encode(['success'=>true, 'count'=>$count]);
            } else if ($type === 'delete'){
                $count = $model->bulkDelete($ids);
                echo json_encode(['success'=>true, 'count'=>$count]);
            } else {
                http_response_code(400);
                echo json_encode(['success'=>false,'error'=>'Action de masse inconnue.']);
            }
            exit;
        }

        echo json_encode(['success'=>false,'error'=>'Unknown action']);
    } catch(Exception $e){
        http_response_code(500);
        echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
    }
}
