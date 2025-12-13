<?php
if (session_status() === PHP_SESSION_NONE) {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../Model/FavoritesModel.php';

// Only handle request when executed directly
if (realpath($_SERVER['SCRIPT_FILENAME']) !== realpath(__FILE__)) {
    return;
}

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    $response['message'] = 'User not authenticated.';
    echo json_encode($response);
    exit();
}

try {
    $db = Config::getPDO();
    $favoritesModel = new FavoritesModel($db);

    $action = $_GET['action'] ?? $_POST['action'] ?? null;
    $eventId = filter_var($_GET['event_id'] ?? $_POST['event_id'] ?? null, FILTER_VALIDATE_INT);

    if (!$eventId) {
        $response['message'] = 'Event ID is required.';
        echo json_encode($response);
        exit();
    }

    switch ($action) {
        case 'add_favorite':
            if ($favoritesModel->addFavorite($userId, $eventId)) {
                $response['success'] = true;
                $response['message'] = 'Event added to favorites.';
            } else {
                $response['message'] = 'Failed to add event to favorites.';
            }
            break;

        case 'remove_favorite':
            if ($favoritesModel->removeFavorite($userId, $eventId)) {
                $response['success'] = true;
                $response['message'] = 'Event removed from favorites.';
            } else {
                $response['message'] = 'Failed to remove event from favorites.';
            }
            break;

        case 'is_favorite':
            $response['success'] = true;
            $response['is_favorite'] = $favoritesModel->isFavorite($userId, $eventId);
            $response['message'] = 'Favorite status retrieved.';
            break;

        default:
            $response['message'] = 'Invalid action.';
            break;
    }

} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
    error_log('FavoritesController PDO Error: ' . $e->getMessage());
} catch (Exception $e) {
    $response['message'] = 'General error: ' . $e->getMessage();
    error_log('FavoritesController General Error: ' . $e->getMessage());
}

echo json_encode($response);
