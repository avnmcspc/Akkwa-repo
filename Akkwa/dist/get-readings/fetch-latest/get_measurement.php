<?php
require('../../../config.php');

if (isset($_GET['distance'])) {
   
    $distance = floatval($_GET['distance']);

    $stmt = $conn->prepare("INSERT INTO waterlevel (level, recorded_at) VALUES (?, NOW())");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    
    $stmt->bind_param("d", $distance);

    
    if ($stmt->execute()) {
        echo "Measurement recorded successfully.";
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
