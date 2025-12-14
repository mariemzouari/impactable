<?php
include __DIR__ . '/../../Model/CampagneController.php';
require_once __DIR__ . '/../../model/Campagne.php';
$campagneController = new CampagneController();

if (isset($_GET["id"])) {
    if ($campagneController->deleteCampagne($_GET["id"])) {
        header('Location: list-camp.php?success=2');
        exit;
    } else {
        header('Location: list-camp.php?error=delete_failed');
        exit;
    }
} else {
    header('Location: list-camp.php');
    exit;
}
?>