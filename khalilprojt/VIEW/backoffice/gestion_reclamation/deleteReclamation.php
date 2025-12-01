<?php
require_once(__DIR__ . '/../../../controller/ReclamationController.php');

if (isset($_GET["id"])) {
    $reclamationC = new ReclamationController();
    $reclamationC->deleteReclamation($_GET["id"]);
}

// Retour au dashboard admin
header('Location: ../admin_dashboard.php');
exit();
?>
