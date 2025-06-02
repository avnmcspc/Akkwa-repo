<?php
include('../../config.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$response = [];

$sql = "SELECT level FROM waterlevel ORDER BY recorded_at DESC LIMIT 1";
$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response = [
            "status" => "success",
            "water_level" => floatval($row['level']) // Ensure numeric output
        ];
    } else {
        $response = [
            "status" => "success",
            "water_level" => 0 // No data found, return 0
        ];
    }
} else {
    $response = [
        "status" => "error",
        "message" => "Database query failed"
    ];
}

// Close the database connection
$conn->close();

// Return JSON response
echo json_encode($response);
