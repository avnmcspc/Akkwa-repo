<?php
include('../../config.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Query to get the latest pH value
$sql = "SELECT ph_sensor, recorded_at FROM ph_level ORDER BY recorded_at DESC LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode([
        "status" => "success",
        "ph_level" => floatval($row["ph_sensor"]),
        "recorded_at" => $row["recorded_at"]
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "No data found"]);
}

// Close database connection
mysqli_close($conn);
?>