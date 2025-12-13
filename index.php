<?php

session_start();

if (headers_sent()) {
    exit;
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 0;
    $_SESSION['user_name'] = 'Visiteur';
    $_SESSION['role'] = 'guest';
}

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'register':
        include 'View/FrontOffice/register.php';
        break;
    case 'login':
        include 'View/FrontOffice/login.php';
        break;
    case 'logout':
        session_destroy();
        header('Location: index.php?action=list');
        exit;
    case 'create':
        include 'View/FrontOffice/ajout.php';
        break;
    case 'edit':
        include 'View/FrontOffice/edit.php';
        break;
    case 'delete':
        include 'View/FrontOffice/delete.php';
        break;
    case 'view':
        include 'View/FrontOffice/view.php';
        break;
    case 'report_user':
        include 'View/FrontOffice/report_user.php';
        break;
    case 'add_comment':
        include 'View/FrontOffice/add_comment.php';
        break;
    case 'edit_comment':
        include 'View/FrontOffice/edit_comment.php';
        break;
    case 'delete_comment':
        include 'View/FrontOffice/delete_comment.php';
        break;
    case 'toggle_like':
        include 'View/FrontOffice/toggle_like.php';
        break;
    case 'admin':
        include 'View/BackOffice/admin.php';
        break;
    case 'admin_reports':
        include 'View/BackOffice/admin_reports.php';
        break;
    case 'admin_comments':
        include 'View/BackOffice/admin_comments.php';
        break;
    case 'delete_comment_admin':
        include 'View/BackOffice/delete_comment_admin.php';
        break;
    case 'search_comments':
        include 'View/BackOffice/searchComments.php';
        break;
    case 'list':
    default:
        include 'View/FrontOffice/forum.php';
        break;
}
?>
