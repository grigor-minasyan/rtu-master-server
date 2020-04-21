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
  $rtu_id = json_decode($body)->rtu_id;
  $rtu_ip = json_decode($body)->rtu_ip;
  $port = json_decode($body)->port;
  $type = json_decode($body)->type;
  $sql_displ_count = "SELECT rtu_type_name, rtu_type_display_count FROM rtu_types WHERE rtu_type_name = '{$type}'";
  $types = $mysqli->query($sql_displ_count);
  $type_row = $types->fetch_object();
  $disp_count = $type_row->rtu_type_display_count;

  $sql = "INSERT INTO rtu_list (rtu_id, rtu_ip, rtu_port, type, display_count)
          VALUES ({$rtu_id}, '{$rtu_ip}', {$port}, '{$type}', {$disp_count})";

  if ($mysqli->query($sql) === TRUE) {
    echo "Successfully submitted RTU Data to server";
  } else echo "Error: " . $mysqli->error;
} else echo "Your account does not have admin privileges";
