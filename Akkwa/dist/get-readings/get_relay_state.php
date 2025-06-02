<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");
$relayState = file_exists("relay_state.txt") ? file_get_contents("relay_state.txt") : "off";
echo json_encode(["relay" => trim($relayState)]);
?>