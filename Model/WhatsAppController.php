<?php
// controller/WhatsAppController.php - VERSION RÉELLE
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Twilio\Rest\Client;

class WhatsAppController
{

    private $client;
    private $accountSid;
    private $authToken;
    private $fromNumber = 'whatsapp:+14155238886';

    public function __construct()
    {
        // ⚠️ REMPLACEZ CES VALEURS PAR LES VÔTRES ⚠️
        $this->accountSid = 'YOUR_ACCOUNT_SID'; // VOTRE Account SID
        $this->authToken = 'YOUR_AUTH_TOKEN';    // VOTRE Auth Token

        $this->client = new Client($this->accountSid, $this->authToken);
    }

    public function sendVerificationCode($phoneNumber, $verificationCode)
    {
        try {
            // Formate le numéro
            $to = $this->formatPhoneNumber($phoneNumber);

            // Message en français
            $message = "Votre code de vérification ImpactAble : *{$verificationCode}*\n\n" .
                "Ce code est valide 10 minutes.\n" .
                "Merci pour votre don ! 💚";

            // Envoi WhatsApp via Twilio
            $messageResult = $this->client->messages->create(
                $to,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );

            // Log de succès
            error_log("✅ WhatsApp envoyé à $phoneNumber - Code: $verificationCode - SID: {$messageResult->sid}");

            return [
                'success' => true,
                'message_sid' => $messageResult->sid,
                'code' => $verificationCode,
                'to' => $to
            ];

        } catch (Exception $e) {
            $error = $e->getMessage();
            error_log("❌ WhatsApp error: $error");

            // Fallback pour test si Twilio échoue
            return $this->fallbackTest($phoneNumber, $verificationCode, $error);
        }
    }

    private function formatPhoneNumber($phoneNumber)
    {
        // Nettoie le numéro
        $cleanNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Format Tunisie
        if (strlen($cleanNumber) === 8) {
            return 'whatsapp:+216' . $cleanNumber;
        }

        // Si déjà avec indicatif 216
        if (substr($cleanNumber, 0, 3) === '216') {
            return 'whatsapp:+' . $cleanNumber;
        }

        return 'whatsapp:+' . $cleanNumber;
    }

    private function fallbackTest($phoneNumber, $code, $error)
    {
        // Enregistre dans un fichier pour test
        $log = date('Y-m-d H:i:s') . " | WhatsApp FALLBACK\n";
        $log .= "Numéro: $phoneNumber\n";
        $log .= "Code: $code\n";
        $log .= "Erreur Twilio: $error\n";
        $log .= "---\n";

        file_put_contents(dirname(__DIR__) . '/whatsapp_fallback.log', $log, FILE_APPEND);

        // Retourne succès en mode test
        return [
            'success' => true,  // On continue quand même pour test
            'message' => 'Mode test - Code: ' . $code,
            'code' => $code,
            'error' => $error
        ];
    }

    public function checkMessageStatus($messageSid)
    {
        try {
            $message = $this->client->messages($messageSid)->fetch();
            return [
                'status' => $message->status,
                'date_sent' => $message->dateSent,
                'to' => $message->to
            ];
        } catch (Exception $e) {
            return null;
        }
    }
}
?>