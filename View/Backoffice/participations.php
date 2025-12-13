<?php
require_once "../../Controller/ParticipationController.php";

$controller = new ParticipationController();
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

switch($action){
    case 'delete':
        $controller->delete($id);
        break;
    default:
        $controller->index();
        break;
}

