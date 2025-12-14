<?php
require_once __DIR__ . '/Model/WhatsAppController.php';

$wa = new WhatsAppController();
echo "Testing WhatsApp sending...\n";
$res = $wa->sendVerificationCode('98192738', '123456');

print_r($res);
?>