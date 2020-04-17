<?php
// Only allow POST requests
if (strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') {
  throw new Exception("\nOnly POST requests are allowed, not ".strtoupper($_SERVER['REQUEST_METHOD']));
}

// Make sure Content-Type is application/json
$content_type = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
if (stripos($content_type, 'application/json') === false) {
  throw new Exception("\nContent-Type must be application/json, not not not not not ".$_SERVER['CONTENT_TYPE']);
}

// Include config file
require_once "config.php";

// Read the input stream
$body = file_get_contents("php://input");
$rtu_id = json_decode($body)->rtu_id;
$rtu_ip = json_decode($body)->rtu_ip;
$port = json_decode($body)->port;

// Display the object
// print_r($object);

$sql = "INSERT INTO rtu_list (rtu_id, rtu_ip, rtu_port) VALUES ({$rtu_id}, '{$rtu_ip}', {$port})";

if ($mysqli->query($sql) === TRUE) {
  echo "Successfully submitted RTU Data to server";
} else echo "Error: " . $mysqli->error;
