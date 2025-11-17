<?php
header("Content-Type: application/json");
require "db.php";

$id = $_GET["id"];

$stmt = $conn->prepare("DELETE FROM reclamations WHERE id = ?");
$stmt->bind_param("s", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}
?>
