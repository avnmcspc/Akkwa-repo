<?php
require('../../../config.php');

if (isset($_GET['ph_sensor'])) {
   
    $distance = floatval($_GET['ph_sensor']);

    $stmt = $conn->prepare("INSERT INTO ph_level (ph_sensor, recorded_at) VALUES (?, NOW())");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    
    $stmt->bind_param("d", $distance);

    
    if ($stmt->execute()) {
        echo "Ph level recorded successfully.";
    } else {
        echo "Error recording measurement: " . $stmt->error;
    }

    // Close the statement.
    $stmt->close();
} else {
    echo "No measurement provided.";
}

// Close the database connection.
$conn->close();
?>
