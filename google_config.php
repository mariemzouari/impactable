<?php
require_once __DIR__ . '/vendor/autoload.php';

$google_client = new Google\Client();
$google_client->setClientId("462581126284-ea7f0l1a9ahcvgqmkmv3np1v83k29the.apps.googleusercontent.com");
$google_client->setClientSecret("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");
$google_client->setRedirectUri("http://localhost/impactable/View/Frontoffice/google_callback.php");

$google_client->addScope("email");
$google_client->addScope("profile");
?>