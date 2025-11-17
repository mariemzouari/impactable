<?php
header("Content-Type: application/json");
require "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id"];
$status = $data["status"];
$priorite = $data["priorite"];

$stmt = $conn->prepare("UPDATE reclamations SET status = ?, priorite = ? WHERE id = ?");
$stmt->bind_param("sss", $status, $priorite, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}
?>
