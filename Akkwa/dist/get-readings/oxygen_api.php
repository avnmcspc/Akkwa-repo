<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

include('../../config.php');

// Check if relay state is sent
if (isset($_GET['oxygen'])) {
    $relayState = $_GET['oxygen'];  // "on" or "off"

    // Save the state to a file (optional, for quick access)
    file_put_contents("oxygen_state.txt", $relayState);

    // Insert the state and timestamp into the database
    $stmt = $conn->prepare("INSERT INTO oxygen_log (state, time) VALUES (?, NOW())");
    $stmt->bind_param("s", $relayState);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["status" => "success", "relay" => $relayState]);
} else {
    echo json_encode(["status" => "error", "message" => "Missing relay parameter"]);
}

$conn->close();
?>