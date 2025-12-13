<?php
if (session_status() === PHP_SESSION_NONE) {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

// Ensure this file only handles the request when executed directly
// (e.g. called via URL /Controller/ParticipationController.php) and not when included.
if (realpath($_SERVER['SCRIPT_FILENAME']) !== realpath(__FILE__)) {
    // When included, just define helpers and models, don't send headers or exit.
    return;
}

header('Content-Type: application/json');

// Fonction pour envoyer une réponse JSON et terminer le script.
function send_json($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Safe logging function that won't crash if directory doesn't exist
function safe_log($message) {
    $logDir = __DIR__ . '/../logs';
    $logFile = $logDir . '/participation_debug.log';
    
    // Only try to log if directory exists and is writable
    if (file_exists($logDir) && is_writable($logDir)) {
        @error_log($message, 3, $logFile);
    }
    
    // Always fallback to PHP's error_log
    error_log($message);
}

// Wrapper try-catch pour attraper toutes les erreurs (y compris fatales) et les retourner en JSON.
try {
    safe_log("[" . date('c') . "] ParticipationController: REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A'));
    safe_log("[" . date('c') . "] ParticipationController: _REQUEST: " . json_encode($_REQUEST));
    safe_log("[" . date('c') . "] ParticipationController: _POST: " . json_encode($_POST));
    $rawInput = file_get_contents('php://input');
    safe_log("[" . date('c') . "] ParticipationController: Raw input: " . $rawInput);

    // Try multiple possible paths for Config.php
    $possibleConfigPaths = [
        __DIR__ . '/../Config.php',
        __DIR__ . '/../config/Config.php',
        __DIR__ . '/../../Config.php',
    ];
    
    $configPath = null;
    foreach ($possibleConfigPaths as $path) {
        if (file_exists($path)) {
            $configPath = $path;
            break;
        }
    }
    
    if (!$configPath) {
        throw new Exception("Fichier de configuration manquant. Chemins essayés: " . implode(', ', $possibleConfigPaths));
    }
    
    // Try multiple possible paths for ParticipationModel.php
    $possibleModelPaths = [
        __DIR__ . '/../Model/ParticipationModel.php',
        __DIR__ . '/../../Model/ParticipationModel.php',
    ];
    
    $modelPath = null;
    foreach ($possibleModelPaths as $path) {
        if (file_exists($path)) {
            $modelPath = $path;
            break;
        }
    }
    
    if (!$modelPath) {
        throw new Exception("Fichier de modèle manquant. Chemins essayés: " . implode(', ', $possibleModelPaths));
    }
    
    require_once $configPath;
    require_once $modelPath;

    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_REQUEST['action'] ?? null; // Tente de récupérer l'action depuis GET ou POST (pour FormData)
    $input_data = [];

    // Gérer les données d'entrée selon le Content-Type
    if ($method === 'POST') {
        if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            $json_input = file_get_contents('php://input');
            $input_data = json_decode($json_input, true) ?: [];
            // Pour les requêtes JSON, l'action est souvent dans le corps
            if (isset($input_data['action'])) {
                $action = $input_data['action'];
            }
        } else {
            // Pour les requêtes de type formulaire (FormData), les données sont dans $_POST
            $input_data = $_POST;
        }
    } else { // Pour GET requests, use $_GET
        $input_data = $_GET;
    }


    $db = Config::getPDO();
    $participationModel = new ParticipationModel($db);

    // --- GESTION DES REQUÊTES GET ---
    if ($method === 'GET') {
        if ($action === 'get_event_participants') {
            $eventId = $input_data['id'] ?? 0;
            if ($eventId <= 0) send_json(['success' => false, 'error' => 'ID d\'événement invalide.'], 400);
            $participants = $participationModel->getByEventIdWithUserDetails($eventId);
            send_json(['success' => true, 'data' => $participants]);
        }
        // Si aucune action GET spécifique, passer au 404
    }

    // --- GESTION DES REQUÊTES POST ---
    if ($method === 'POST') {
        switch ($action) {
            case 'add_participant': // Action du back-office (FormData)
                $data = [
                    'id_evenement' => $input_data['id_evenement'] ?? null,
                    'prenom' => $input_data['prenom'] ?? null,
                    'nom' => $input_data['nom'] ?? null,
                    'email' => $input_data['email'] ?? null,
                    'num_tel' => $input_data['num_tel'] ?? null, // Added num_tel
                    'num_identite' => $input_data['num_identite'] ?? null, // Added num_identite
                    'nombre_accompagnants' => $input_data['nombre_accompagnants'] ?? 0,
                    'besoins_accessibilite' => $input_data['besoins_accessibilite'] ?? '',
                    'statut' => $input_data['statut'] ?? 'inscrit',
                    'message' => $input_data['message'] ?? '',
                    'id_utilisateur' => null, // Ajout manuel, pas d'utilisateur lié
                    'date_inscription' => date('Y-m-d H:i:s')
                ];

                if (empty($data['id_evenement'])) send_json(['success' => false, 'error' => 'ID d\'événement manquant.'], 400);
                if (empty($data['prenom']) || empty($data['nom']) || empty($data['email'])) send_json(['success' => false, 'error' => 'Champs requis manquants.'], 400);
                
                $resultId = $participationModel->create($data);
                send_json(['success' => (bool)$resultId, 'error' => $resultId ? '' : 'Échec de l\'ajout.']);
                break;

            case 'edit_participant': // Action du back-office (FormData)
                safe_log("[" . date('c') . "] ParticipationController: edit_participant action. Input data: " . json_encode($input_data));
                $id = $input_data['id'] ?? 0;
                $data = [
                    'prenom' => $input_data['prenom'], 'nom' => $input_data['nom'], 'email' => $input_data['email'],
                    'num_tel' => $input_data['num_tel'] ?? null, // Added num_tel
                    'num_identite' => $input_data['num_identite'] ?? null, // Added num_identite
                    'nombre_accompagnants' => $input_data['nombre_accompagnants'],
                    'besoins_accessibilite' => $input_data['besoins_accessibilite'],
                    'statut' => $input_data['statut'], 'message' => $input_data['message']
                ];
                $result = $participationModel->update($id, $data);
                if ($result) {
                    send_json(['success' => true, 'error' => '']);
                } else {
                    send_json(['success' => false, 'error' => 'La mise à jour a échoué. Aucune ligne modifiée.']);
                }
                break;

            case 'delete_participant': // Action du back-office (FormData)
                $id = $input_data['id'] ?? 0;
                $result = $participationModel->delete($id);
                send_json(['success' => (bool)$result, 'error' => $result ? '' : 'La suppression a échoué']);
                break;

            case 'bulk_action': // Action du back-office (FormData)
                safe_log("[" . date('c') . "] ParticipationController: bulk_action action. Input data: " . json_encode($input_data));
                $type = $input_data['type'] ?? '';
                $ids = $input_data['ids'] ?? [];
                if (!is_array($ids)) $ids = [$ids]; // Assurez-vous que c'est un tableau

                if ($type === 'delete') {
                    $deleted = $participationModel->deleteByIds($ids);
                    send_json(['success' => true, 'deleted' => $deleted, 'error' => '']);
                } elseif ($type === 'status') {
                    $status = $input_data['status'] ?? '';
                    $updated = $participationModel->updateStatusForIds($ids, $status);
                    if ($updated > 0) {
                        send_json(['success' => true, 'updated' => $updated, 'error' => '']);
                    } else {
                        send_json(['success' => false, 'error' => 'Aucun participant mis à jour. Vérifiez les IDs ou le statut.'], 400);
                    }
                } else {
                    send_json(['success' => false, 'error' => 'Type d\'action en masse non valide.'], 400);
                }
                break;

            // --- Actions Front-office (utilisent JSON) ---
            case 'cancel_participation':
                $participationId = $input_data['id'] ?? 0;
                if ($participationId <= 0) send_json(['success' => false, 'error' => 'ID de participation manquant.'], 400);
                $result = $participationModel->update($participationId, ['statut' => 'annulé']);
                send_json(['success' => (bool)$result, 'error' => $result ? '' : 'L\'annulation a échoué.']);
                break;

            case 'confirm_my_participation':
                $participationId = $input_data['id'] ?? 0;
                if ($participationId <= 0) send_json(['success' => false, 'error' => 'ID de participation manquant.'], 400);
                $result = $participationModel->update($participationId, ['statut' => 'confirmé']);
                send_json(['success' => (bool)$result, 'error' => $result ? '' : 'La confirmation a échoué.']);
                break;
            
            case 'participate_with_details': // NOUVELLE ACTION POUR LE FORMULAIRE DÉTAILLÉ
                $eventId = $input_data['event_id'] ?? null;
                $userId = $input_data['user_id'] ?? null;
                $prenom = $input_data['prenom'] ?? null;
                $nom = $input_data['nom'] ?? null;
                $email = $input_data['email'] ?? null;
                $num_tel = $input_data['num_tel'] ?? null;
                $num_identite = $input_data['num_identite'] ?? null;
                $nombre_accompagnants = $input_data['nombre_accompagnants'] ?? 0;
                $besoins_accessibilite = $input_data['besoins_accessibilite'] ?? '';
                $message = $input_data['message'] ?? '';

                if (!$eventId) send_json(['success' => false, 'error' => 'ID d\'événement manquant.'], 400);

                // Validation : Si pas de userId, alors prenom, nom, email sont obligatoires
                if (!$userId && (empty($prenom) || empty($nom) || empty($email))) {
                    send_json(['success' => false, 'error' => 'Pour une inscription sans compte, le prénom, nom et email sont requis.'], 400);
                }
                
                // Vérifier si l'utilisateur est déjà inscrit ou confirmé (seulement si userId existe)
                if ($userId) {
                    $existing = $participationModel->findOneBy(['id_utilisateur' => $userId, 'id_evenement' => $eventId]);
                    if ($existing && in_array($existing['statut'], ['inscrit', 'confirmé'])) {
                        send_json(['success' => false, 'error' => 'Vous êtes déjà inscrit à cet événement.']);
                    }
                }

                $participationData = [
                    'id_evenement' => $eventId,
                    'id_utilisateur' => $userId, // Sera null si non connecté
                    'prenom' => $prenom,
                    'nom' => $nom,
                    'email' => $email,
                    'num_tel' => $num_tel,
                    'num_identite' => $num_identite,
                    'date_inscription' => date('Y-m-d H:i:s'),
                    'statut' => 'inscrit',
                    'nombre_accompagnants' => $nombre_accompagnants,
                    'besoins_accessibilite' => $besoins_accessibilite,
                    'message' => $message
                ];

                $resultId = $participationModel->create($participationData);
                if ($resultId) {
                    send_json(['success' => true, 'message' => 'Participation enregistrée avec succès.']);
                } else {
                    send_json(['success' => false, 'error' => 'Erreur lors de l\'enregistrement de la participation.']);
                }
                break;

            case 'toggle_favorite': // Action pour les favoris
                $eventId = $input_data['event_id'] ?? null;
                $userId = $input_data['user_id'] ?? null;

                if (!$userId) send_json(['success' => false, 'error' => 'Utilisateur non connecté.'], 401);
                if (!$eventId) send_json(['success' => false, 'error' => 'ID d\'événement manquant.'], 400);

                $existingFavorite = $participationModel->findOneBy(['id_utilisateur' => $userId, 'id_evenement' => $eventId, 'statut' => 'favori']);
                
                if ($existingFavorite) {
                    $result = $participationModel->delete($existingFavorite['id']);
                    if ($result) {
                        send_json(['success' => true, 'message' => 'Événement retiré des favoris.']);
                    } else {
                        send_json(['success' => false, 'error' => 'Échec de la suppression des favoris.']);
                    }
                } else {
                    $favoriteData = [
                        'id_evenement' => $eventId,
                        'id_utilisateur' => $userId,
                        'date_inscription' => date('Y-m-d H:i:s'),
                        'statut' => 'favori',
                        'nombre_accompagnants' => 0,
                        'besoins_accessibilite' => 'N/A',
                        'message' => 'Favori'
                    ];
                    $resultId = $participationModel->create($favoriteData);
                    if ($resultId) {
                        send_json(['success' => true, 'message' => 'Événement ajouté aux favoris !']);
                    } else {
                        send_json(['success' => false, 'error' => 'Échec de l\'ajout aux favoris.']);
                    }
                }
                break;

            default:
                send_json(['success' => false, 'error' => "Action POST non reconnue : $action"], 400);
                break;
        }
    }

    // Si la requête n'a pas été traitée par les blocs ci-dessus
    send_json(['success' => false, 'error' => 'Action non valide ou méthode de requête non autorisée.'], 400);

} catch (Throwable $e) {
    safe_log("ERROR: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    send_json([
        'success' => false,
        'error' => 'Une erreur serveur est survenue.',
        'details' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], 500);
}
?>