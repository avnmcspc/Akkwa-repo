<?php
include('../config.php');
$esp32_ip = "http://192.168.1.9"; 


if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == "spin") {
        $description = isset($_GET['scheduled']) ? "Scheduled Spin" : "Manual Spin";
        file_get_contents("$esp32_ip/spin");
        saveSpin($description);
        echo "Servo spun!";
    } 
    
    else if ($action == "set_time" && isset($_GET['hour']) && isset($_GET['minute'])) {
        $hour = intval($_GET['hour']);
        $minute = intval($_GET['minute']);
        file_get_contents("$esp32_ip/set_time?hour=$hour&minute=$minute");
        
        
        saveScheduledTime($hour, $minute);
        
        echo "Time set to $hour:$minute!";
    }
    
    else if ($action == "get_time") {
        $result = $conn->query("SELECT hour, minute FROM schedule ORDER BY id DESC LIMIT 1");
        if ($row = $result->fetch_assoc()) {
            echo json_encode(["hour" => str_pad($row['hour'], 2, "0", STR_PAD_LEFT), "minute" => str_pad($row['minute'], 2, "0", STR_PAD_LEFT)]);
        } else {
            echo json_encode(["hour" => "00", "minute" => "00"]); 
        }
    }
    
}
function saveSpin($description) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO spin_logs (description) VALUES (?)");
    $stmt->bind_param("s", $description);
    $stmt->execute();
    $stmt->close();
}


function saveScheduledTime($hour, $minute) {
    global $conn;
    
    
    $conn->query("DELETE FROM schedule");
    
    $stmt = $conn->prepare("INSERT INTO schedule (hour, minute) VALUES (?, ?)");
    $stmt->bind_param("ii", $hour, $minute);
    $stmt->execute();
    $stmt->close();
}


function getScheduledTime() {
    global $conn;
    
    $result = $conn->query("SELECT hour, minute FROM schedule ORDER BY id DESC LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        return json_encode($row);
    } else {
        return json_encode(["hour" => -1, "minute" => -1]); 
    }
}

$conn->close();
?>
