<?php
include('../config.php');

$sql = "SELECT percentage AS battery_percentage FROM battery_percentage ORDER BY timestamp DESC LIMIT 1";
$result = $conn->query($sql);
$data = $result->fetch_assoc();

if ($data && is_numeric($data['battery_percentage'])) {
    echo json_encode($data);
} else {
    echo json_encode(["battery_percentage" => 0]); // fallback value
}

$conn->close();
?>