

<?php
require('../../config.php');

if (!$conn) {
    die(json_encode(["error" => "Database connection failed"]));
}

$result = $conn->query("SELECT temperature,recorded_at FROM temperature_level ORDER BY recorded_at");

if (!$result) {
    die(json_encode(["error" => $conn->error]));
}

$history = [];

while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}

echo json_encode($history);
$conn->close();
?>

