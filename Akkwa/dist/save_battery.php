<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_REQUEST['battery_percentage'])) {
        $battery_percentage = intval($_REQUEST['battery_percentage']);

        // Fetch the latest timestamp today (no calamity_id filtering)
        $latestTimeQuery = "SELECT timestamp FROM battery_percentage 
                            WHERE DATE(timestamp) = CURDATE() 
                            ORDER BY timestamp DESC 
                            LIMIT 1";

        $result = $conn->query($latestTimeQuery);
        $latestTime = $result->fetch_assoc()['timestamp'] ?? "No records today";

        // Insert new data (no calamity_id)
        $insertQuery = "INSERT INTO battery_percentage (percentage, timestamp) 
                        VALUES ('$battery_percentage', NOW())";

        if ($conn->query($insertQuery) === TRUE) {
            echo json_encode([
                "message" => "Data saved successfully",
                "latest_time" => $latestTime
            ]);
        } else {
            echo json_encode(["error" => $conn->error]);
        }
    } else {
        echo json_encode(["error" => "Missing required parameters!"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method!"]);
}

$conn->close();
?>
