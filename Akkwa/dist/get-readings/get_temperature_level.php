<?php
include('../../config.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$response = [];

$sql = "SELECT recorded_at, temperature,temp_c FROM temperature_level ORDER BY recorded_at";
$result = $conn->query($sql);

if ($result) {
    $temperature_data = [];

    while ($row = $result->fetch_assoc()) {
        $temperature_data[] = [
            "date" => $row["recorded_at"],
            "temperature" => floatval($row["temperature"]),
            "celcius" => floatval($row["temp_c"]),
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
