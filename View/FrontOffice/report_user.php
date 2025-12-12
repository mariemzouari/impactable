<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$result = ['success' => false, 'message' => 'Erreur inconnue'];

try {
    $reporter = $_SESSION['user_id'] ?? null;
    $is_admin = $_SESSION['is_admin'] ?? false;
    if (!$reporter) {
        throw new Exception('Non connecté');
    }
    if (!$is_admin) {
        throw new Exception('Non admin');
    }

    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $input = null;
    if (stripos($contentType, 'application/json') !== false) {
        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?: [];
    } else {
        $input = $_POST;
    }

    $target_type = isset($input['target_type']) ? trim($input['target_type']) : '';
    $target_id = isset($input['target_id']) ? (int)$input['target_id'] : 0;
    $reason = isset($input['reason']) ? trim($input['reason']) : '';

    if (!in_array($target_type, ['post', 'comment', 'user'])) {
        throw new Exception('Type invalide');
    }
    if ($target_id <= 0) {
        throw new Exception('ID cible invalide');
    }
    if ($target_type === 'user' && $target_id == $reporter) {
        throw new Exception('Vous ne pouvez pas vous signaler');
    }

    $pdo = config::getConnexion();
    $stmt = $pdo->prepare('INSERT INTO user_reports (reporter_id, target_type, target_id, reason) VALUES (?, ?, ?, ?)');
    $ok = $stmt->execute([$reporter, $target_type, $target_id, $reason]);
    if (!$ok) {
        throw new Exception('Échec insertion BD');
    }

    $result['success'] = true;
    $result['message'] = 'Signalement envoyé';

} catch (Exception $e) {
    $result['message'] = 'ERREUR: ' . $e->getMessage();
}

echo json_encode($result);
exit;
?>
