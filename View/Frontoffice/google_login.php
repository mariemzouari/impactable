<?php
require_once __DIR__ . '/../../google_config.php';

$google_url = $google_client->createAuthUrl();
header("Location: " . $google_url);
exit;
?>