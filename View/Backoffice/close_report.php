<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$result = ['success' => false, 'message' => 'Erreur inconnue'];

try {
    $is_admin = (($_SESSION['role'] ?? '') == 'admin');
    if (!$is_admin) {
        throw new Exception('Accès non autorisé');
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $report_id = isset($data['report_id']) ? (int)$data['report_id'] : 0;

    if ($report_id <= 0) {
        throw new Exception('ID invalide');
    }

    $pdo = config::getConnexion();
    $stmt = $pdo->prepare('UPDATE user_reports SET status = ? WHERE id = ?');
    $ok = $stmt->execute(['closed', $report_id]);

    if (!$ok) {
        throw new Exception('Impossible de mettre à jour le signalement');
    }

    $result['success'] = true;
    $result['message'] = 'Signalement marqué comme résolu';

} catch (Exception $e) {
    $result['message'] = $e->getMessage();
}

echo json_encode($result);
exit;
?>
