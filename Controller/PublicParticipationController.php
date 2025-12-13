<?php
// Controller/PublicParticipationController.php

require_once __DIR__ . "/../config/Config.php";
require_once __DIR__ . "/../Model/ParticipationModel.php";

// Ensure logs directory exists for temporary error logging
$logDir = __DIR__ . '/../logs';
if(!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

// Global exception handler to log unexpected errors (temporary, remove in production)
set_exception_handler(function($e) use ($logDir){
    $msg = "[" . date('c') . "] Uncaught Exception in PublicParticipationController: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n" . $e->getTraceAsString() . "\n\n";
    @file_put_contents($logDir . '/public_participation_errors.log', $msg, FILE_APPEND);
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error'=>'Server error','details'=>$e->getMessage()]);
    exit;
});

class PublicParticipationController {
    private $participationModel;
    private $db;

    public function __construct() {
        $Config = new Config();
        $this->db = $Config->getPDO();
        $this->participationModel = new ParticipationModel($this->db);
    }

    public function handleRequest() {
        error_log("[" . date('c') . "] PublicParticipationController: Request received. Method: " . $_SERVER['REQUEST_METHOD'] . "\n", 3, __DIR__ . '/../logs/public_participation_debug.log');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendJsonResponse(['error' => 'Method Not Allowed'], 405);
            return;
        }

        $payload = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("[" . date('c') . "] PublicParticipationController: Invalid JSON payload. Error: " . json_last_error_msg() . ". Raw: " . file_get_contents('php://input') . "\n", 3, __DIR__ . '/../logs/public_participation_debug.log');
            $this->sendJsonResponse(['error' => 'Invalid JSON payload'], 400);
            return;
        }

        error_log("[" . date('c') . "] PublicParticipationController: Payload: " . json_encode($payload) . "\n", 3, __DIR__ . '/../logs/public_participation_debug.log');

        if (!isset($payload['action'])) {
            $this->sendJsonResponse(['error' => 'Action not specified'], 400);
            return;
        }

        switch ($payload['action']) {
            case 'participate_with_details':
                $this->participateWithDetails($payload);
                break;
            case 'cancel_participation': // Added action for cancellation
                $this->cancelParticipation($payload);
                break;
            default:
                $this->sendJsonResponse(['error' => 'Unknown action'], 400);
        }
    }

    private function participateWithDetails($data) {
        // Validation
        $errors = [];
        if (empty($data['event_id'])) {
            $errors[] = 'Event ID is required.';
        }

        $isGuest = empty($data['user_id']);
        if ($isGuest) {
            if (empty($data['prenom'])) $errors[] = 'Prénom is required for guests.';
            if (empty($data['nom'])) $errors[] = 'Nom is required for guests.';
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'A valid email is required for guests.';
            }
        }

        if (!empty($errors)) {
            error_log("[" . date('c') . "] PublicParticipationController: Validation errors: " . implode(' ', $errors) . "\n", 3, __DIR__ . '/../logs/public_participation_debug.log');
            $this->sendJsonResponse(['success' => false, 'error' => implode(' ', $errors)], 400);
            return;
        }
        
        // Check for existing participation
        $criteria = [
            'id_evenement' => $data['event_id'],
        ];

        if ($isGuest) {
            // For guests, check by email
            if (!empty($data['email'])) {
                $stmt = $this->db->prepare("SELECT Id_utilisateur FROM utilisateur WHERE email = :email LIMIT 1");
                $stmt->execute(['email' => $data['email']]);
                $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existingUser) {
                    // If user exists, use their ID for participation
                    $data['user_id'] = $existingUser['Id_utilisateur'];
                    $criteria['id_utilisateur'] = $data['user_id'];
                } else {
                    $criteria['email'] = $data['email'];
                }
            }
        } else {
            $criteria['id_utilisateur'] = $data['user_id'];
        }

        if ($this->participationModel->findOneBy($criteria)) {
            error_log("[" . date('c') . "] PublicParticipationController: Existing participation found. Criteria: " . json_encode($criteria) . "\n", 3, __DIR__ . '/../logs/public_participation_debug.log');
            $this->sendJsonResponse(['success' => false, 'error' => 'Vous êtes déjà inscrit à cet événement.'], 409);
            return;
        }

        // Prepare data for model
        $participationData = [
            'id_evenement' => intval($data['event_id']),
            'id_utilisateur' => isset($data['user_id']) && $data['user_id'] !== null ? intval($data['user_id']) : null,
            'prenom' => trim($data['prenom'] ?? ''),
            'nom' => trim($data['nom'] ?? ''),
            'email' => trim($data['email'] ?? ''),
            'num_tel' => trim($data['num_tel'] ?? ''),
            'num_identite' => trim($data['num_identite'] ?? ''),
            'nombre_accompagnants' => intval($data['nombre_accompagnants'] ?? 0),
            'besoins_accessibilite' => trim($data['besoins_accessibilite'] ?? 'aucun_besoin'),
            'message' => trim($data['message'] ?? ''),
            'statut' => 'inscrit'
        ];

        // Create participation
        try {
            $result = $this->participationModel->create($participationData);
            if ($result) {
                error_log("[" . date('c') . "] PublicParticipationController: Participation created. ID: " . $result . "\n", 3, __DIR__ . '/../logs/public_participation_debug.log');
                $this->sendJsonResponse(['success' => true, 'message' => 'Inscription enregistrée avec succès !']);
            } else {
                error_log("[" . date('c') . "] PublicParticipationController: Failed to create participation. Data: " . json_encode($participationData) . "\n", 3, __DIR__ . '/../logs/public_participation_debug.log');
                $this->sendJsonResponse(['success' => false, 'error' => 'Une erreur est survenue lors de l\'enregistrement de votre participation.'], 500);
            }
        } catch (PDOException $e) {
            error_log("[" . date('c') . "] PublicParticipationController: PDOException during participation creation: " . $e->getMessage() . "\n", 3, __DIR__ . '/../logs/public_participation_debug.log');
            $this->sendJsonResponse(['success' => false, 'error' => 'Database error during participation creation.', 'details' => $e->getMessage()], 500);
        }
    }

    private function cancelParticipation($data) {
        if (empty($data['id'])) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Participation ID is required.'], 400);
            return;
        }
        
        try {
            $result = $this->participationModel->delete(intval($data['id']));
            if ($result) {
                $this->sendJsonResponse(['success' => true, 'message' => 'Participation annulée avec succès.']);
            } else {
                $this->sendJsonResponse(['success' => false, 'error' => 'Impossible d\'annuler la participation.'], 500);
            }
        } catch (PDOException $e) {
            error_log("[" . date('c') . "] PublicParticipationController: PDOException during participation cancellation: " . $e->getMessage() . "\n", 3, __DIR__ . '/../logs/public_participation_debug.log');
            $this->sendJsonResponse(['success' => false, 'error' => 'Database error during cancellation.', 'details' => $e->getMessage()], 500);
        }
    }

    private function sendJsonResponse($data, $httpCode = 200) {
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit; // Ensure script terminates after sending JSON response
    }
}

// Route the request
$controller = new PublicParticipationController();
$controller->handleRequest();
