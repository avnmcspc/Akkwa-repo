<?php
include('../../config.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$response = [];

$sql = "SELECT recorded_at, ph_sensor FROM ph_level ORDER BY recorded_at";
$result = $conn->query($sql);

if ($result) {
    $temperature_data = [];

    while ($row = $result->fetch_assoc()) {
        $temperature_data[] = [
            "date" =>  date("Y-m-d H:i:s", strtotime($row["recorded_at"])),
            "ph_sensor" => floatval($row["ph_sensor"]),
            
        ];
    }

    $response = [
        "status" => "success",
        "temperature_data" => $temperature_data
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
