<?php
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

$result = [
    'session' => $_SESSION,
    'cookies' => $_COOKIE,
    'headers' => function_exists('getallheaders') ? getallheaders() : []
];

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>