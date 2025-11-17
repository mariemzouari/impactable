<?php
header("Content-Type: application/json");
require "db.php";

$sql = "SELECT * FROM reclamations ORDER BY dateCreation DESC";
$result = $conn->query($sql);

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode($rows);
?>
