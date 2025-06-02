<?php
require('../../config.php'); // Ensure this file properly initializes $conn

if (!$conn) {
    die(json_encode(["error" => "Database connection failed"]));
}

$result = $conn->query("SELECT description, timestamp FROM spin_logs ORDER BY id DESC");

if (!$result) {
    die(json_encode(["error" => $conn->error]));
}

$history = [];

while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}

echo json_encode($history);
$conn->close();
