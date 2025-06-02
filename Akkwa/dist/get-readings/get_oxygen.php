<?php
include('../../config.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$response = [];

$sql = "SELECT state, time FROM oxygen_log ORDER BY time DESC;";
$result = $conn->query($sql);

if ($result) {
    $oxygen_data = [];

    while ($row = $result->fetch_assoc()) {
        $oxygen_data[] = [
            "state" => $row["state"],
            "time" => date("Y-m-d H:i:s", strtotime($row["time"]))  // Converts to readable format
        ];
        
    }

    $response = [
        "status" => "success",
        "oxygen_data" => $oxygen_data
    ];
} else {
    $response = [
        "status" => "error",
        "message" => "Database query failed",
        "error" => $conn->error  // Debugging error
    ];
}

$conn->close();

echo json_encode($response);
?>