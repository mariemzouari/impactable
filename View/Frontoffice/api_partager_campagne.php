<?php
// api_partager_campagne.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Non authentifié']);
    exit();
}

include_once __DIR__ . '/../../Model/ReferralController.php';

$campagneId = $_POST['campagne_id'] ?? null;
$message = $_POST['message'] ?? '';

if (!$campagneId) {
    echo json_encode(['success' => false, 'error' => 'Campagne non spécifiée']);
    exit();
}

$referralC = new ReferralController();
$userId = $_SESSION['user_id'];

$result = $referralC->partagerCampagne($userId, $campagneId, $message);

echo json_encode($result);
?>