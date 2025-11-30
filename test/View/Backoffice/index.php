<?php
require_once "../../Controller/EventController.php";

// ROUTER MVC
$controller = new EventController();

$action = $_GET['action'] ?? 'index';
$id     = $_GET['id'] ?? null;

switch($action){
    case 'create':
        $controller->create();
        break;

    case 'edit':
        $controller->edit($id);
        break;
    case 'delete':
        $controller->delete($id);
        break;
    default:
        $controller->index();
        break;
}

?>
