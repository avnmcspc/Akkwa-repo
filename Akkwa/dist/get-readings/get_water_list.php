

<?php
require('../../config.php');

if (!$conn) {
    die(json_encode(["error" => "Database connection failed"]));
}

$result = $conn->query("SELECT level,recorded_at FROM waterlevel ORDER BY recorded_at DESC");

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

