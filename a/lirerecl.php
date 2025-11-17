<?php
header("Content-Type: application/json");
require "db.php";

$id = $_GET["id"];

$stmt = $conn->prepare("SELECT * FROM reclamations WHERE id = ?");
stmt->bind_param("s", $id);
$stmt->execute();

$res = $stmt->get_result();
echo json_encode($res->fetch_assoc());
?>
