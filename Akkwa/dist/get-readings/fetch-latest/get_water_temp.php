<?php
require('../../../config.php');

// Debug: Check if parameters are received
if (isset($_GET['water_temp']) && isset($_GET['water_tempcel'])) {
    
    $water_temp = floatval($_GET['water_temp']);
    $water_tempcel = floatval($_GET['water_tempcel']);

    // Debugging: Print received values
    echo "Received Temp (F): " . $water_temp . " | Temp (C): " . $water_tempcel . "<br>";

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO temperature_level (temperature, temp_c, recorded_at) VALUES (?, ?, NOW())");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("dd", $water_temp, $water_tempcel);

    if ($stmt->execute()) {
        echo "✅ Measurement recorded successfully.";
    } else {
        echo "❌ Error recording measurement: " . $stmt->error;
    }

    // Close the statement.
    $stmt->close();
} else {
    echo "❌ No measurement provided.";
}

// Close the database connection.
$conn->close();
?>
