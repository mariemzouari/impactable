<?php
// debug_don_insert.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/Model/DonController.php';
require_once __DIR__ . '/config.php';

// Mock session
session_start();

$controller = new DonController();

// Simulate data
$id_campagne = 61; // from user error log
$montant = 20.0;
$message = 'Debug Message';
$methode = 'carte';
$email = 'test_debug@example.com';
$nom = 'Debug User';
$telephone = '98192738';
// We skip whatsapp code check by not providing it in session or strictly, 
// OR we can rely on the controller logic. 
// If we provide telephone but no code in params, it might try to send code. 
// BUT we want to test the INSERTION part.
// The controller logic:
// if (!empty($telephone)) { ... logic ... }
// If we omit telephone in this test, we skip verification logic and go straight to DB.
// Let's first test DB insertion without telephone to rule out DB issues.
echo "Testing DB Insertion (skipping phone logic for now)...\n";

$res = $controller->faireDon(
    $id_campagne,
    $montant,
    $message,
    $methode,
    $email,
    $nom,
    '', // empty phone to fail-fast to DB
    ''
);

if ($res) {
    echo "SUCCESS! Don ID: $res\n";
} else {
    echo "FAILURE.\n";
    // We can't easily see internal error_logs unless we redirect them or they go to stdout.
    // Let's try to inspect what might verify failure.
}

// Now let's try with phone but verify logic
echo "\nTesting WITH Phone logic...\n";
// We need to bypass the "send code" part.
// The controller sends code if $whatsapp_code is empty.
// If $whatsapp_code is NOT empty, it verifies session.
// So we must set session.
$_SESSION['don_whatsapp_code'] = '111111';
$_SESSION['don_whatsapp_phone'] = $telephone;
$_SESSION['don_whatsapp_time'] = time();

$res2 = $controller->faireDon(
    $id_campagne,
    $montant,
    $message,
    $methode,
    $email,
    $nom,
    $telephone,
    '111111'
);

if ($res2 === "whatsapp_code_required") {
    echo "Result: VALIDATION REQUIRED (Unexpected)\n";
} elseif ($res2) {
    echo "SUCCESS with Phone! Don ID: $res2\n";
} else {
    echo "FAILURE with Phone.\n";
}
?>