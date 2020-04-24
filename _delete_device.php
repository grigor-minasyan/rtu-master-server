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

session_start();
//check if they are admin to allow deleting
$sql = "SELECT is_admin FROM users WHERE id=".$_SESSION["id"];
$user_list = $mysqli->query($sql);
$test = $user_list->fetch_object();
if ($test->is_admin) {
  // Read the input stream
  $body = file_get_contents("php://input");
  $device_id_to_delete = json_decode($body)->device_id_to_delete;

  $sql = "DELETE FROM rtu_list WHERE rtu_id={$device_id_to_delete}";

  if ($mysqli->query($sql) === TRUE) {
    if (mysqli_affected_rows($mysqli)) {
      $sql = "DELETE FROM event_history WHERE rtu_id={$device_id_to_delete}";
      $sql2 = "DELETE FROM standing_alarms WHERE rtu_id={$device_id_to_delete}";
      if ($mysqli->query($sql) === TRUE) {
        $del_events = mysqli_affected_rows($mysqli);

        if ($mysqli->query($sql2) === TRUE) {
          $del_alms = mysqli_affected_rows($mysqli);
          echo ("Successfully removed device #{$device_id_to_delete} with {$del_events} events and {$del_alms} alarms");
        } else echo "Error connecting: " . $mysqli->error;
        
      } else echo "Error connecting: " . $mysqli->error;
    }
    else echo "Successfully submitted but no device was found";
  } else echo "Error connecting: " . $mysqli->error;
} else echo "Your account does not have admin privileges";
