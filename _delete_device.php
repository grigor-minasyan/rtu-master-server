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
$device_id_to_delete = json_decode($body)->device_id_to_delete;

// Display the object
// print_r($object);

$sql = "DELETE FROM rtu_list WHERE rtu_id={$device_id_to_delete}";

if ($mysqli->query($sql) === TRUE) {
  if (mysqli_affected_rows($mysqli)) {
    $sql = "DELETE FROM event_history WHERE rtu_id={$device_id_to_delete}";
    if ($mysqli->query($sql) === TRUE) {
      echo ("Successfully removed device #{$device_id_to_delete} and ".mysqli_affected_rows($mysqli)." associated data.");
    } else echo "Error connecting: " . $mysqli->error;
  }
  else echo "Successfully submitted but no device was found";
} else echo "Error connecting: " . $mysqli->error;
