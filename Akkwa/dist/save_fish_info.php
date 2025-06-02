<?php
// Ensure no previous output
ob_start();

// Set JSON header at the very beginning
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);  // Don't display errors to user
ini_set('log_errors', 1);

include('../config.php');

try {
    // Strict method check
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Capture and validate all required fields
    $required_fields = [
        'breedOfFish',
        'typeOfFish',
        'lifeSpan',
        'quantity',
        'behavior',
        'acquisitionDate'
    ];

    $data = [];
    foreach ($required_fields as $field) {
        $data[$field] = $_POST[$field] ?? null;
        if ($data[$field] === null || trim($data[$field]) === '') {
            throw new Exception("Missing or empty required field: $field");
        }
    }

    // Additional input validation
    if (!is_numeric($data['quantity']) || $data['quantity'] <= 0) {
        throw new Exception("Invalid quantity");
    }

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("INSERT INTO fish 
                            (breed, type, quantity, life_span, behaviour, added_date)  
                            VALUES (?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }

    $stmt->bind_param(
        "ssisss",
        $data['breedOfFish'],
        $data['typeOfFish'],
        $data['quantity'],
        $data['lifeSpan'],
        $data['behavior'],
        date('Y-m-d', strtotime($data['acquisitionDate']))
    );

    // Execute and check for specific errors
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    // Clear output buffer and send success response
    ob_clean();
    echo json_encode([
        'status' => 'success',
        'message' => 'Fish Information added successfully'
    ]);
} catch (Exception $e) {
    // Clear any previous output
    ob_clean();

    // Send error response
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} finally {
    // Clean up
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }

    // End output buffering
    ob_end_flush();
    exit;
}
