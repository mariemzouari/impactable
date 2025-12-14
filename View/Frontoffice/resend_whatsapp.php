<?php
// resend_whatsapp.php
session_start();
require_once __DIR__ . '/../../Model/WhatsAppController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telephone = $_POST['telephone'] ?? '';

    if (!empty($telephone)) {
        $newCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        $whatsapp = new WhatsAppController();
        $result = $whatsapp->sendVerificationCode($telephone, $newCode);

        if ($result['success']) {
            $_SESSION['don_whatsapp_code'] = $newCode;
            $_SESSION['don_whatsapp_phone'] = $telephone;
            $_SESSION['don_whatsapp_time'] = time();

            if (isset($result['message_sid'])) {
                $_SESSION['whatsapp_message_sid'] = $result['message_sid'];
            }

            echo json_encode([
                'success' => true,
                'message' => 'Code WhatsApp renvoyé',
                'code' => $newCode
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => $result['error'] ?? 'Erreur inconnue'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Numéro manquant'
        ]);
    }
}
?>